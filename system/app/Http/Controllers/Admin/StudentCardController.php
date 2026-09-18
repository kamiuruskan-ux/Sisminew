<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\Student;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class StudentCardController extends Controller
{
    /**
     * Display student list dashboard for card printing.
     */
    public function index(Request $request)
    {
        $query = Student::with(['user', 'class', 'major']);

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('search')) {
            $search = trim($request->search);
            $query->where(function ($q) use ($search) {
                $q->where('nisn', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $students = $query->orderBy('nisn', 'asc')->paginate(20)->withQueryString();
        $classes = ClassModel::withCount('students')->orderBy('name', 'asc')->get();

        return view('admin.student_cards.index', compact('students', 'classes'));
    }

    /**
     * Render printable student ID cards (Single or Bulk Class / Selected).
     */
    public function print(Request $request)
    {
        $query = Student::with(['user', 'class', 'major']);

        if ($request->filled('student_ids')) {
            $ids = is_array($request->student_ids) ? $request->student_ids : explode(',', $request->student_ids);
            $query->whereIn('id', array_filter($ids));
        } elseif ($request->filled('student_id')) {
            $query->where('id', $request->student_id);
        } elseif ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        } else {
            return redirect()->route('admin.student-cards.index')->with('error', 'Pilih minimal satu siswa atau kelas untuk dicetak.');
        }

        $students = $query->orderBy('nisn', 'asc')->get();

        if ($students->isEmpty()) {
            return redirect()->route('admin.student-cards.index')->with('error', 'Tidak ada data siswa yang ditemukan untuk dicetak.');
        }

        $selectedClass = $request->filled('class_id') ? ClassModel::find($request->class_id) : null;

        return view('admin.student_cards.print', compact('students', 'selectedClass'));
    }

    /**
     * Show dedicated student card settings page.
     */
    public function settings()
    {
        $sampleStudent = Student::with(['user', 'class', 'major'])->first();
        if (!$sampleStudent) {
            $sampleStudent = new Student([
                'nisn' => '1234567890',
                'nik' => '3171012345678901',
                'gender' => 'male',
                'qr_code' => 'KTS-1234567890',
            ]);
            $sampleStudent->user = (object) ['name' => 'Siswa Contoh (Preview)', 'email' => 'siswa@sekolah.sch.id'];
            $sampleStudent->class = (object) ['name' => 'X IPA 1'];
        }

        return view('admin.student_cards.settings', compact('sampleStudent'));
    }

    /**
     * Update student card settings and template files.
     */
    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'school_principal_name' => 'nullable|string|max:255',
            'student_card_rules' => 'nullable|string',
            'student_card_header_subtitle' => 'nullable|string|max:255',
            'student_card_accent_color' => 'nullable|string|max:20',
            'student_card_accent_color_end' => 'nullable|string|max:20',
            'student_card_orientation' => 'nullable|in:landscape,portrait',
            'student_card_show_nik' => 'nullable|in:0,1',
            'student_card_show_major' => 'nullable|in:0,1',
            'student_card_bg_front' => 'nullable|image|max:3072',
            'student_card_bg_back' => 'nullable|image|max:3072',
            'student_card_stamp' => 'nullable|image|max:2048',
            'student_card_signature' => 'nullable|image|max:2048',
        ]);

        Setting::set('school_principal_name', $request->input('school_principal_name'));
        Setting::set('student_card_rules', $request->input('student_card_rules'));
        Setting::set('student_card_header_subtitle', $request->input('student_card_header_subtitle', 'KARTU TANDA SISWA RESMI (KTS)'));
        Setting::set('student_card_accent_color', $request->input('student_card_accent_color', '#4f46e5'));
        Setting::set('student_card_accent_color_end', $request->input('student_card_accent_color_end', $request->input('student_card_accent_color', '#4f46e5')));
        Setting::set('student_card_orientation', $request->input('student_card_orientation', 'landscape'));
        Setting::set('student_card_show_nik', $request->input('student_card_show_nik', '1'));
        Setting::set('student_card_show_major', $request->input('student_card_show_major', '1'));

        // Handle Background Depan
        if ($request->hasFile('student_card_bg_front')) {
            $oldPath = Setting::get('student_card_bg_front_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            $file = $request->file('student_card_bg_front');
            $fileName = 'card_bg_front_' . time() . '.' . $file->getClientOriginalExtension();
            $savedFront = save_uploaded_public_file($file, 'img/cards', $fileName);
            Setting::set('student_card_bg_front_path', 'img/cards/' . basename($savedFront));
        } elseif ($request->boolean('delete_student_card_bg_front') || $request->input('delete_student_card_bg_front') == '1') {
            $oldPath = Setting::get('student_card_bg_front_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            Setting::set('student_card_bg_front_path', null);
        }

        // Handle Background Belakang
        if ($request->hasFile('student_card_bg_back')) {
            $oldPath = Setting::get('student_card_bg_back_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            $file = $request->file('student_card_bg_back');
            $fileName = 'card_bg_back_' . time() . '.' . $file->getClientOriginalExtension();
            $savedBack = save_uploaded_public_file($file, 'img/cards', $fileName);
            Setting::set('student_card_bg_back_path', 'img/cards/' . basename($savedBack));
        } elseif ($request->boolean('delete_student_card_bg_back') || $request->input('delete_student_card_bg_back') == '1') {
            $oldPath = Setting::get('student_card_bg_back_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            Setting::set('student_card_bg_back_path', null);
        }

        // Handle Stamp
        if ($request->hasFile('student_card_stamp')) {
            $oldPath = Setting::get('student_card_stamp_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            $file = $request->file('student_card_stamp');
            $fileName = 'card_stamp_' . time() . '.' . $file->getClientOriginalExtension();
            $savedStamp = save_uploaded_public_file($file, 'img/cards', $fileName);
            Setting::set('student_card_stamp_path', 'img/cards/' . basename($savedStamp));
        } elseif ($request->boolean('delete_student_card_stamp') || $request->input('delete_student_card_stamp') == '1') {
            $oldPath = Setting::get('student_card_stamp_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            Setting::set('student_card_stamp_path', null);
        }

        // Handle Signature
        if ($request->hasFile('student_card_signature')) {
            $oldPath = Setting::get('student_card_signature_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            $file = $request->file('student_card_signature');
            $fileName = 'card_signature_' . time() . '.' . $file->getClientOriginalExtension();
            $savedSig = save_uploaded_public_file($file, 'img/cards', $fileName);
            Setting::set('student_card_signature_path', 'img/cards/' . basename($savedSig));
        } elseif ($request->boolean('delete_student_card_signature') || $request->input('delete_student_card_signature') == '1') {
            $oldPath = Setting::get('student_card_signature_path');
            if ($oldPath && function_exists('delete_public_file')) {
                delete_public_file($oldPath);
            }
            Setting::set('student_card_signature_path', null);
        }

        return back()->with('success', 'Pengaturan template kartu siswa berhasil disimpan.');
    }
}
