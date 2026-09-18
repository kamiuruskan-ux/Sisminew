<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BkAssessment;
use App\Models\BkCounseling;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class BkCounselingController extends Controller
{
    /**
     * Display directory and dashboard of BK Counseling services
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $category = $request->input('category');
        $serviceType = $request->input('service_type');
        $classId = $request->input('class_id');
        $status = $request->input('status');

        $query = BkCounseling::with(['student.class', 'counselor']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($s) use ($search) {
                      $s->where('name', 'like', "%{$search}%")
                        ->orWhere('nisn', 'like', "%{$search}%");
                  });
            });
        }

        if ($category) {
            $query->where('category', $category);
        }

        if ($serviceType) {
            $query->where('service_type', $serviceType);
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($classId) {
            $query->whereHas('student', function ($s) use ($classId) {
                $s->where('class_id', $classId);
            });
        }

        $counselings = $query->latest('date')->paginate(15)->withQueryString();

        // Statistics Summary
        $totalSessions = BkCounseling::count();
        $pribadiCount = BkCounseling::where('category', 'pribadi')->count();
        $sosialCount = BkCounseling::where('category', 'sosial')->count();
        $belajarCount = BkCounseling::where('category', 'belajar')->count();
        $karierCount = BkCounseling::where('category', 'karier')->count();
        $kedisiplinanCount = BkCounseling::where('category', 'kedisiplinan')->count();

        $classes = ClassModel::where('is_active', true)->orderBy('name')->get();

        return view('admin.bk.index', compact(
            'counselings',
            'classes',
            'totalSessions',
            'pribadiCount',
            'sosialCount',
            'belajarCount',
            'karierCount',
            'kedisiplinanCount'
        ));
    }

    /**
     * Form Layanan Konseling BK Baru
     */
    public function create(Request $request)
    {
        $students = Student::with(['user', 'class'])
            ->join('users', 'students.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('students.*')
            ->get();
        $selectedStudentId = $request->input('student_id');

        return view('admin.bk.create', compact('students', 'selectedStudentId'));
    }

    /**
     * Store new counseling session
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'category' => 'required|in:pribadi,sosial,belajar,karier,kedisiplinan',
            'service_type' => 'required|in:individu,kelompok,klasikal',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'time' => 'nullable',
            'place' => 'nullable|string|max:255',
            'complaint_notes' => 'nullable|string',
            'action_plan' => 'nullable|string',
            'follow_up_notes' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,referred',
            'is_confidential' => 'nullable|boolean',
            'attachment' => 'nullable|file|mimes:jpg,png,pdf,docx|max:5120',
        ]);

        $validated['counselor_id'] = auth()->id();
        $validated['is_confidential'] = $request->has('is_confidential');

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx']);
            $targetSubfolder = $isDoc ? 'doc/bk_documents' : 'img/bk_documents';
            $savedFile = save_uploaded_public_file($file, $targetSubfolder);
            $validated['attachment'] = ($isDoc ? 'doc/bk_documents/' : '') . basename($savedFile);
        }

        BkCounseling::create($validated);

        return redirect()->route('admin.bk.index')
            ->with('success', 'Catatan layanan bimbingan & konseling berhasil disimpan.');
    }

    /**
     * Detail Rekam Konseling Siswa
     */
    public function show($id)
    {
        $counseling = BkCounseling::with(['student.user', 'counselor'])->findOrFail($id);

        $history = BkCounseling::where('student_id', $counseling->student_id)
            ->where('id', '!=', $counseling->id)
            ->latest('date')
            ->get();

        $assessments = BkAssessment::where('student_id', $counseling->student_id)
            ->latest()
            ->get();

        return view('admin.bk.show', compact('counseling', 'history', 'assessments'));
    }

    /**
     * Update counseling session
     */
    public function update(Request $request, $id)
    {
        $counseling = BkCounseling::findOrFail($id);

        $validated = $request->validate([
            'counseling_date' => 'required|date',
            'counseling_type' => 'required|in:individual,group,class,home_visit',
            'problem_category' => 'required|in:academic,behavioral,personal,social,career',
            'summary' => 'required|string',
            'follow_up' => 'nullable|string',
            'status' => 'required|in:scheduled,in_progress,completed,referred',
            'is_confidential' => 'boolean',
        ]);

        $validated['is_confidential'] = $request->has('is_confidential');

        $counseling->update($validated);

        return redirect()->back()->with('success', 'Catatan konseling berhasil diperbarui.');
    }

    /**
     * Delete counseling session
     */
    public function destroy($id)
    {
        $counseling = BkCounseling::findOrFail($id);
        if ($counseling->attachment) {
            delete_public_file($counseling->attachment, 'doc/bk_documents');
        }
        $counseling->delete();

        return redirect()->route('admin.bk.index')
            ->with('success', 'Catatan layanan BK berhasil dihapus.');
    }

    /**
     * Halaman Asesmen & Pemetaan Minat Bakat / Bimbingan Karir Siswa
     */
    public function assessments(Request $request)
    {
        $search = $request->input('search');
        $type = $request->input('type');

        $query = BkAssessment::with(['student.class', 'counselor']);

        if ($search) {
            $query->whereHas('student', function ($s) use ($search) {
                $s->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            })->orWhere('title', 'like', "%{$search}%")
              ->orWhere('dream_career', 'like', "%{$search}%");
        }

        if ($type) {
            $query->where('type', $type);
        }

        $assessments = $query->latest()->paginate(15)->withQueryString();
        $students = Student::with(['user', 'class'])
            ->join('users', 'students.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('students.*')
            ->get();

        return view('admin.bk.assessments', compact('assessments', 'students'));
    }

    /**
     * Store Asesmen Minat Bakat Siswa
     */
    public function storeAssessment(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:angket_minat,bakat_karier,sosiometri,psikotes,observasi_perilaku',
            'dream_career' => 'nullable|string|max:255',
            'recommended_major' => 'nullable|string|max:255',
            'strength_notes' => 'nullable|string',
            'improvement_notes' => 'nullable|string',
        ]);

        $validated['counselor_id'] = auth()->id();

        BkAssessment::create($validated);

        return redirect()->back()->with('success', 'Data asesmen minat bakat & karir siswa berhasil disimpan.');
    }
}
