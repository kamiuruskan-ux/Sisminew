<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClassController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $majors = \App\Models\Major::where('is_active', true)->orderBy('name')->get();
        $teachers = \App\Models\User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher']);
        })->orderBy('name')->get();

        $query = ClassModel::with(['major', 'homeroomTeacher'])->withCount('students')
            ->when($user->isTeacher(), function ($q) use ($user) {
                return $q->where('homeroom_teacher_id', $user->id);
            });

        if ($request->filled('major_id')) {
            $query->where('major_id', $request->major_id);
        }

        if ($request->filled('level')) {
            $query->where('grade', $request->level);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where('name', 'like', "%{$search}%");
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $classes = $query->latest()->paginate(15)->withQueryString();

        $totalClasses = (clone $query)->count();
        $activeClasses = (clone $query)->where('is_active', true)->count();
        $totalStudentsInClasses = \App\Models\Student::whereIn('class_id', (clone $query)->pluck('id'))->count();

        return view('admin.classes.index', compact(
            'classes',
            'majors',
            'teachers',
            'totalClasses',
            'activeClasses',
            'totalStudentsInClasses'
        ));
    }

    public function create()
    {
        $majors = \App\Models\Major::where('is_active', true)->get();
        $teachers = \App\Models\User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
        })->orderBy('name')->get();
        return view('admin.classes.create', compact('majors', 'teachers'));
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'level' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
                'major_id' => 'nullable|exists:majors,id',
                'homeroom_teacher_id' => 'nullable|exists:users,id',
                'capacity' => 'nullable|integer',
            ], [
                'name.required' => 'Nama kelas wajib diisi.',
                'level.required' => 'Jenjang / Tingkat kelas wajib dipilih.',
                'level.in' => 'Pilihan jenjang kelas tidak valid.',
                'major_id.exists' => 'Jurusan yang dipilih tidak ditemukan.',
                'homeroom_teacher_id.exists' => 'Wali kelas yang dipilih tidak ditemukan.',
                'capacity.integer' => 'Kapasitas kelas harus berupa angka.',
            ]);

            $validated['slug'] = Str::slug($validated['name']);
            $validated['grade'] = (int) $validated['level'];
            $validated['is_active'] = $request->has('is_active');
            unset($validated['level']);

            // Ensure unique slug
            $counter = 1;
            $originalSlug = $validated['slug'];
            while (ClassModel::where('slug', $validated['slug'])->exists()) {
                $validated['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }

            ClassModel::create($validated);

            return redirect()->route('admin.classes.index')
                ->with('success', 'Kelas berhasil ditambahkan.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['system' => 'Terjadi kesalahan sistem: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function show(ClassModel $class)
    {
        $class->load(['homeroomTeacher', 'major', 'academicYear']);
        return view('admin.classes.show', compact('class'));
    }

    public function edit(ClassModel $class)
    {
        $majors = \App\Models\Major::where('is_active', true)->get();
        $teachers = \App\Models\User::whereHas('roles', function ($q) {
            $q->whereIn('slug', ['guru', 'teacher', 'admin', 'operator', 'tata-usaha', 'staff', 'kepala-sekolah']);
        })->orderBy('name')->get();
        return view('admin.classes.edit', compact('class', 'majors', 'teachers'));
    }

    public function update(Request $request, ClassModel $class)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'level' => 'required|in:1,2,3,4,5,6,7,8,9,10,11,12',
                'major_id' => 'nullable|exists:majors,id',
                'homeroom_teacher_id' => 'nullable|exists:users,id',
                'capacity' => 'nullable|integer',
            ], [
                'name.required' => 'Nama kelas wajib diisi.',
                'level.required' => 'Jenjang / Tingkat kelas wajib dipilih.',
                'level.in' => 'Pilihan jenjang kelas tidak valid.',
                'major_id.exists' => 'Jurusan yang dipilih tidak ditemukan.',
                'homeroom_teacher_id.exists' => 'Wali kelas yang dipilih tidak ditemukan.',
                'capacity.integer' => 'Kapasitas kelas harus berupa angka.',
            ]);

            $validated['grade'] = (int) $validated['level'];
            $validated['is_active'] = $request->has('is_active');
            unset($validated['level']);

            // Update slug if name changed
            if ($validated['name'] !== $class->name) {
                $validated['slug'] = Str::slug($validated['name']);
                $counter = 1;
                $originalSlug = $validated['slug'];
                while (ClassModel::where('slug', $validated['slug'])->where('id', '!=', $class->id)->exists()) {
                    $validated['slug'] = $originalSlug . '-' . $counter;
                    $counter++;
                }
            }

            $class->update($validated);

            return redirect()->route('admin.classes.index')
                ->with('success', 'Kelas berhasil diperbarui.');
        } catch (\Illuminate\Validation\ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            return redirect()->back()
                ->withErrors(['system' => 'Terjadi kesalahan sistem: ' . $e->getMessage()])
                ->withInput();
        }
    }

    public function destroy(ClassModel $class)
    {
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Kelas berhasil dihapus.');
    }
}
