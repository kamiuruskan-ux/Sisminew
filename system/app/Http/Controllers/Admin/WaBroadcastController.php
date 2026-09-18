<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Setting;
use App\Models\SpmbRegistration;
use App\Models\Student;
use App\Models\User;
use App\Models\WaBroadcast;
use App\Models\WaBroadcastLog;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class WaBroadcastController extends Controller
{
    /**
     * Display a listing of WhatsApp broadcast messages.
     */
    public function index(Request $request)
    {
        $query = WaBroadcast::with(['targetClass', 'creator'])->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('title', 'like', "%{$search}%");
        }

        $broadcasts = $query->paginate(12);

        $totalBroadcasts = WaBroadcast::count();
        $totalSent = WaBroadcastLog::where('status', 'sent')->count();
        $totalFailed = WaBroadcastLog::where('status', 'failed')->count();
        $activeProvider = WhatsAppService::getActiveProvider();

        return view('admin.wa_broadcasts.index', compact(
            'broadcasts',
            'totalBroadcasts',
            'totalSent',
            'totalFailed',
            'activeProvider'
        ));
    }

    /**
     * Show the form for creating a new WhatsApp broadcast.
     */
    public function create()
    {
        $classes = ClassModel::orderBy('name')->get();
        $activeProvider = WhatsAppService::getActiveProvider();

        // Recipient counts preview
        $counts = [
            'all_students' => Student::whereNotNull('phone')->orWhereHas('user', fn($q) => $q->whereNotNull('phone'))->count(),
            'all_parents' => Student::whereNotNull('parent_phone')->count(),
            'all_teachers' => User::whereHas('roles', fn($q) => $q->whereIn('name', ['guru', 'admin', 'operator', 'tata-usaha', 'kepala-sekolah']))->whereNotNull('phone')->count(),
            'spmb' => SpmbRegistration::whereNotNull('parent_phone')->orWhereNotNull('phone')->count(),
        ];

        return view('admin.wa_broadcasts.create', compact('classes', 'activeProvider', 'counts'));
    }

    /**
     * Store a newly created broadcast and execute dispatch.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'target_type' => 'required|in:all_students,all_parents,all_teachers,class,spmb,custom',
            'target_class_id' => 'required_if:target_type,class|nullable|exists:classes,id',
            'custom_numbers' => 'required_if:target_type,custom|nullable|string',
            'message' => 'required|string',
        ]);

        $provider = WhatsAppService::getActiveProvider();
        if ($provider === 'disabled') {
            return back()->withInput()->with('error', 'WhatsApp Gateway sedang Nonaktif! Silakan aktifkan Fonnte atau Onesender pada Konfigurasi Web terlebih dahulu.');
        }

        // 1. Resolve recipients
        $recipients = $this->resolveRecipients(
            $validated['target_type'],
            $validated['target_class_id'] ?? null,
            $validated['custom_numbers'] ?? null
        );

        if (empty($recipients)) {
            return back()->withInput()->with('error', 'Tidak ditemukan nomor telepon/WhatsApp valid pada target yang dipilih.');
        }

        // 2. Create Broadcast Record
        $broadcast = WaBroadcast::create([
            'title' => $validated['title'],
            'target_type' => $validated['target_type'],
            'target_class_id' => $validated['target_class_id'] ?? null,
            'message' => $validated['message'],
            'status' => 'processing',
            'total_recipients' => count($recipients),
            'success_count' => 0,
            'failed_count' => 0,
            'created_by' => Auth::id(),
        ]);

        // 3. Process Sending to each recipient
        $successCount = 0;
        $failedCount = 0;
        $schoolName = Setting::get('school_name', 'Sekolah');

        foreach ($recipients as $recipient) {
            $formattedMessage = str_replace(
                ['{nama}', '{nisn}', '{kelas}', '{sekolah}'],
                [$recipient['name'], $recipient['nisn'] ?? '-', $recipient['class'] ?? '-', $schoolName],
                $validated['message']
            );

            $result = WhatsAppService::sendMessage($recipient['phone'], $formattedMessage);

            $status = $result['success'] ? 'sent' : 'failed';
            if ($result['success']) {
                $successCount++;
            } else {
                $failedCount++;
            }

            WaBroadcastLog::create([
                'wa_broadcast_id' => $broadcast->id,
                'recipient_name' => $recipient['name'],
                'recipient_phone' => $recipient['phone'],
                'status' => $status,
                'response_message' => $result['message'],
            ]);
        }

        // 4. Update Broadcast Summary
        $broadcast->update([
            'status' => ($successCount > 0) ? 'completed' : 'failed',
            'success_count' => $successCount,
            'failed_count' => $failedCount,
            'sent_at' => now(),
        ]);

        return redirect()->route('admin.wa-broadcasts.show', $broadcast->id)
            ->with('success', "Broadcast WhatsApp berhasil diproses! {$successCount} terkirim, {$failedCount} gagal.");
    }

    /**
     * Display the detail and recipient log of a specific broadcast.
     */
    public function show($id)
    {
        $broadcast = WaBroadcast::with(['targetClass', 'creator'])->findOrFail($id);
        $logs = $broadcast->logs()->paginate(25);
        $activeProvider = WhatsAppService::getActiveProvider();

        return view('admin.wa_broadcasts.show', compact('broadcast', 'logs', 'activeProvider'));
    }

    /**
     * Remove the specified broadcast session.
     */
    public function destroy($id)
    {
        $broadcast = WaBroadcast::findOrFail($id);
        $broadcast->delete();

        return redirect()->route('admin.wa-broadcasts.index')
            ->with('success', 'Riwayat broadcast WhatsApp berhasil dihapus.');
    }

    /**
     * AJAX endpoint to test WA gateway credentials.
     */
    public function sendTestMessage(Request $request)
    {
        $validated = $request->validate([
            'test_phone' => 'required|string',
            'test_message' => 'nullable|string',
        ]);

        $phone = $validated['test_phone'];
        $message = $validated['test_message'] ?? ("Uji Coba WA Gateway - " . Setting::get('school_name', 'Sekolah') . "\nPesan tes ini dikirim pada " . now()->format('d M Y H:i:s'));

        $result = WhatsAppService::sendMessage($phone, $message);

        return response()->json([
            'success' => $result['success'],
            'message' => $result['message'],
            'raw' => $result['raw'] ?? null,
        ]);
    }

    /**
     * Helper to resolve target recipient list.
     */
    private function resolveRecipients(string $targetType, ?int $classId = null, ?string $customNumbers = null): array
    {
        $recipients = [];

        switch ($targetType) {
            case 'all_students':
                $students = Student::with(['user', 'class'])->get();
                foreach ($students as $s) {
                    $phone = $s->phone ?? $s->user?->phone;
                    if ($phone) {
                        $recipients[] = [
                            'name' => $s->user->name ?? 'Siswa',
                            'phone' => $phone,
                            'nisn' => $s->nisn,
                            'class' => $s->class->name ?? '-',
                        ];
                    }
                }
                break;

            case 'all_parents':
                $students = Student::with(['user', 'class'])->whereNotNull('parent_phone')->get();
                foreach ($students as $s) {
                    if ($s->parent_phone) {
                        $recipients[] = [
                            'name' => $s->parent_name ? ($s->parent_name . ' (Orang Tua ' . $s->name . ')') : ('Orang Tua ' . $s->name),
                            'phone' => $s->parent_phone,
                            'nisn' => $s->nisn,
                            'class' => $s->class->name ?? '-',
                        ];
                    }
                }
                break;

            case 'all_teachers':
                $teachers = User::whereHas('roles', fn($q) => $q->whereIn('name', ['guru', 'admin', 'operator', 'tata-usaha', 'kepala-sekolah']))
                    ->whereNotNull('phone')
                    ->get();
                foreach ($teachers as $t) {
                    if ($t->phone) {
                        $recipients[] = [
                            'name' => $t->name,
                            'phone' => $t->phone,
                            'nisn' => '-',
                            'class' => 'Staff / Guru',
                        ];
                    }
                }
                break;

            case 'class':
                if ($classId) {
                    $students = Student::with(['user', 'class'])->where('class_id', $classId)->get();
                    foreach ($students as $s) {
                        $phone = $s->parent_phone ?? $s->phone ?? $s->user?->phone;
                        if ($phone) {
                            $recipients[] = [
                                'name' => $s->user->name ?? 'Siswa',
                                'phone' => $phone,
                                'nisn' => $s->nisn,
                                'class' => $s->class->name ?? '-',
                            ];
                        }
                    }
                }
                break;

            case 'spmb':
                $spmbs = SpmbRegistration::all();
                foreach ($spmbs as $sp) {
                    $phone = $sp->parent_phone ?? $sp->phone;
                    if ($phone) {
                        $recipients[] = [
                            'name' => $sp->full_name,
                            'phone' => $phone,
                            'nisn' => $sp->nisn ?? '-',
                            'class' => 'Calon Siswa SPMB',
                        ];
                    }
                }
                break;

            case 'custom':
                if (!empty($customNumbers)) {
                    $lines = preg_split('/[\r\n,]+/', $customNumbers);
                    foreach ($lines as $line) {
                        $line = trim($line);
                        if (empty($line)) continue;

                        $parts = explode('-', $line);
                        $phone = trim($parts[0]);
                        $name = isset($parts[1]) ? trim($parts[1]) : 'Penerima';

                        if (!empty($phone)) {
                            $recipients[] = [
                                'name' => $name,
                                'phone' => $phone,
                                'nisn' => '-',
                                'class' => 'Custom',
                            ];
                        }
                    }
                }
                break;
        }

        return $recipients;
    }
}
