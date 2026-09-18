<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BkStudentViolation;
use App\Models\BkViolationCategory;
use App\Models\ClassModel;
use App\Models\Student;
use Illuminate\Http\Request;

class BkViolationController extends Controller
{
    /**
     * Display listing of student violations & point summary
     */
    public function index(Request $request)
    {
        $search = $request->input('search');
        $classId = $request->input('class_id');
        $status = $request->input('status');
        $categoryId = $request->input('category_id');

        $query = BkStudentViolation::with(['student.user', 'student.class', 'category', 'counselor']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('notes', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($s) use ($search) {
                      $s->where('nisn', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($u) use ($search) {
                            $u->where('name', 'like', "%{$search}%");
                        });
                  });
            });
        }

        if ($classId) {
            $query->whereHas('student', function ($s) use ($classId) {
                $s->where('class_id', $classId);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($categoryId) {
            $query->where('violation_category_id', $categoryId);
        }

        $violations = $query->latest('violation_date')->paginate(15)->withQueryString();

        // Statistics Summary
        $totalViolations = BkStudentViolation::count();
        $totalPoints = BkStudentViolation::sum('points');
        $sp1Count = BkStudentViolation::where('status', 'sp1')->count();
        $sp2Count = BkStudentViolation::where('status', 'sp2')->count();
        $sp3Count = BkStudentViolation::where('status', 'sp3')->count();

        // Top 5 Students with highest violation points
        $topViolators = Student::with(['user', 'class'])
            ->withSum('violations', 'points')
            ->having('violations_sum_points', '>', 0)
            ->orderByDesc('violations_sum_points')
            ->limit(5)
            ->get();

        $classes = ClassModel::where('is_active', true)->orderBy('name')->get();
        $categories = BkViolationCategory::orderBy('name')->get();

        $students = Student::with(['user', 'class'])
            ->join('users', 'students.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('students.*')
            ->get();

        return view('admin.bk.violations.index', compact(
            'violations',
            'classes',
            'categories',
            'students',
            'totalViolations',
            'totalPoints',
            'sp1Count',
            'sp2Count',
            'sp3Count',
            'topViolators'
        ));
    }

    /**
     * Show form to record a new student violation
     */
    public function create(Request $request)
    {
        $students = Student::with(['user', 'class'])
            ->join('users', 'students.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('students.*')
            ->get();

        $categories = BkViolationCategory::orderBy('name')->get();
        $selectedStudentId = $request->input('student_id');

        return view('admin.bk.violations.create', compact('students', 'categories', 'selectedStudentId'));
    }

    /**
     * Store new student violation record
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'violation_category_id' => 'nullable|exists:bk_violation_categories,id',
            'title' => 'required|string|max:255',
            'violation_date' => 'required|date',
            'points' => 'required|integer|min:0',
            'notes' => 'nullable|string',
            'penalty' => 'nullable|string',
            'status' => 'required|in:pending,processed,sp1,sp2,sp3,resolved,dismissed',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
        ]);

        $validated['counselor_id'] = auth()->id();

        // Auto lookup points from category if not explicitly provided or category selected
        if ($request->filled('violation_category_id') && ($request->input('points') == 0)) {
            $cat = BkViolationCategory::find($request->violation_category_id);
            if ($cat) {
                $validated['points'] = $cat->points;
            }
        }

        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx']);
            $targetSubfolder = $isDoc ? 'doc/bk_documents' : 'img/bk_documents';
            $savedFile = save_uploaded_public_file($file, $targetSubfolder);
            $validated['attachment'] = ($isDoc ? 'doc/bk_documents/' : '') . basename($savedFile);
        }

        BkStudentViolation::create($validated);

        return redirect()->back()
            ->with('success', 'Data pelanggaran siswa berhasil dicatat.');
    }

    /**
     * Show detail of student violation & total points history
     */
    public function show($id)
    {
        $violation = BkStudentViolation::with(['student.user', 'student.class', 'category', 'counselor'])->findOrFail($id);

        $studentViolations = BkStudentViolation::with(['category'])
            ->where('student_id', $violation->student_id)
            ->orderByDesc('violation_date')
            ->get();

        $totalStudentPoints = $studentViolations->sum('points');

        return view('admin.bk.violations.show', compact('violation', 'studentViolations', 'totalStudentPoints'));
    }

    /**
     * Edit student violation
     */
    public function edit($id)
    {
        $violation = BkStudentViolation::findOrFail($id);
        $students = Student::with(['user', 'class'])
            ->join('users', 'students.user_id', '=', 'users.id')
            ->orderBy('users.name')
            ->select('students.*')
            ->get();
        $categories = BkViolationCategory::orderBy('name')->get();

        return view('admin.bk.violations.edit', compact('violation', 'students', 'categories'));
    }

    /**
     * Update student violation
     */
    public function update(Request $request, $id)
    {
        $violation = BkStudentViolation::findOrFail($id);

        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'violation_category_id' => 'nullable|exists:bk_violation_categories,id',
            'title' => 'required|string|max:255',
            'violation_date' => 'required|date',
            'points' => 'required|integer|min:0',
            'notes' => 'nullable|string',
            'penalty' => 'nullable|string',
            'status' => 'required|in:pending,processed,sp1,sp2,sp3,resolved,dismissed',
            'attachment' => 'nullable|file|mimes:jpg,jpeg,png,pdf,docx|max:5120',
        ]);

        if ($request->hasFile('attachment')) {
            if ($violation->attachment) {
                delete_public_file($violation->attachment, 'doc/bk_documents');
            }
            $file = $request->file('attachment');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'doc', 'docx', 'xls', 'xlsx']);
            $targetSubfolder = $isDoc ? 'doc/bk_documents' : 'img/bk_documents';
            $savedFile = save_uploaded_public_file($file, $targetSubfolder);
            $validated['attachment'] = ($isDoc ? 'doc/bk_documents/' : '') . basename($savedFile);
        }

        $violation->update($validated);

        return redirect()->route('admin.bk.violations.index')
            ->with('success', 'Catatan pelanggaran siswa berhasil diperbarui.');
    }

    /**
     * Delete student violation
     */
    public function destroy($id)
    {
        $violation = BkStudentViolation::findOrFail($id);
        if ($violation->attachment) {
            delete_public_file($violation->attachment, 'doc/bk_documents');
        }
        $violation->delete();

        return redirect()->route('admin.bk.violations.index')
            ->with('success', 'Catatan pelanggaran siswa berhasil dihapus.');
    }

    /**
     * Master Category List & Point Management
     */
    public function categories()
    {
        $categories = BkViolationCategory::withCount('violations')->orderBy('level')->orderBy('name')->get();
        return view('admin.bk.violations.categories', compact('categories'));
    }

    /**
     * Store Category Master
     */
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|in:ringan,sedang,berat,sangat_berat',
            'points' => 'required|integer|min:1',
            'penalty_recommendation' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        BkViolationCategory::create($validated);

        return redirect()->back()->with('success', 'Kategori pelanggaran baru berhasil ditambahkan.');
    }

    /**
     * Update Category Master
     */
    public function updateCategory(Request $request, $id)
    {
        $category = BkViolationCategory::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'level' => 'required|in:ringan,sedang,berat,sangat_berat',
            'points' => 'required|integer|min:1',
            'penalty_recommendation' => 'nullable|string',
            'description' => 'nullable|string',
        ]);

        $category->update($validated);

        return redirect()->back()->with('success', 'Master kategori pelanggaran berhasil diperbarui.');
    }

    /**
     * Delete Category Master
     */
    public function destroyCategory($id)
    {
        $category = BkViolationCategory::findOrFail($id);
        $category->delete();

        return redirect()->back()->with('success', 'Kategori pelanggaran berhasil dihapus.');
    }
}
