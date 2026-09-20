<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolFeedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FeedbackController extends Controller
{
    /**
     * Tampilkan halaman Aspirasi, Kritik & Saran Sekolah
     */
    public function index(Request $request)
    {
        $categoryFilter = $request->input('category', 'all');

        $query = SchoolFeedback::with('user')
            ->when($categoryFilter !== 'all' && in_array($categoryFilter, ['saran', 'kritik', 'masukan']), function ($q) use ($categoryFilter) {
                $q->where('category', $categoryFilter);
            })
            ->orderBy('created_at', 'desc');

        $feedbacks = $query->paginate(15)->withQueryString();

        // Counter stats
        $totalFeedbacks = SchoolFeedback::count();
        $totalKritik = SchoolFeedback::where('category', 'kritik')->count();
        $totalSaran = SchoolFeedback::where('category', 'saran')->count();
        $totalMasukan = SchoolFeedback::where('category', 'masukan')->count();

        $currentUser = Auth::user();
        $currentRoleName = $currentUser?->roles?->first()?->name ?? 'Pegawai';

        return view('admin.feedback.index', compact(
            'feedbacks', 'categoryFilter', 'totalFeedbacks',
            'totalKritik', 'totalSaran', 'totalMasukan', 'currentUser', 'currentRoleName'
        ));
    }

    /**
     * Simpan aspirasi / saran baru
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'category' => 'required|in:saran,kritik,masukan',
            'content' => 'required|string|min:5',
            'is_anonymous' => 'nullable|boolean',
        ]);

        $user = Auth::user();
        $isAnonymous = $request->boolean('is_anonymous');
        $primaryRole = $user?->roles?->first()?->name ?? 'Pegawai';

        SchoolFeedback::create([
            'user_id' => $user?->id,
            'category' => $validated['category'],
            'content' => $validated['content'],
            'is_anonymous' => $isAnonymous,
            'author_name' => $isAnonymous ? 'Anonim' : ($user?->name ?? 'Anonim'),
            'author_role' => $primaryRole,
            'status' => 'baru',
        ]);

        return redirect()->route('admin.feedback.index')
            ->with('success', 'Aspirasi Anda berhasil dikirimkan! Terima kasih atas kontribusi dan kepedulian Anda terhadap sekolah.');
    }

    /**
     * Perbarui status feedback (Bahaskan / Selesai)
     */
    public function updateStatus(Request $request, SchoolFeedback $feedback)
    {
        $validated = $request->validate([
            'status' => 'required|in:baru,dibahas,selesai',
            'admin_notes' => 'nullable|string',
        ]);

        $feedback->update($validated);

        return redirect()->back()
            ->with('success', 'Status aspirasi berhasil diubah menjadi ' . strtoupper($validated['status']) . '.');
    }

    /**
     * Hapus feedback
     */
    public function destroy(SchoolFeedback $feedback)
    {
        $feedback->delete();

        return redirect()->back()
            ->with('success', 'Aspirasi berhasil dihapus.');
    }
}
