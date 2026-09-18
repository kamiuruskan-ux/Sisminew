<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Material;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MaterialController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Material::with(['class', 'teacher', 'chapter', 'topic'])
            ->when($user->isTeacher(), function ($q) use ($user) {
                return $q->where('teacher_id', $user->id);
            })
            ->when($request->major_id, function ($q) use ($request) {
                return $q->whereHas('class', function ($cq) use ($request) {
                    $cq->where('major_id', $request->major_id);
                });
            })
            ->when($request->class_id, function ($q) use ($request) {
                return $q->where('class_id', $request->class_id);
            })
            ->when($request->subject, function ($q) use ($request) {
                return $q->where('subject', $request->subject);
            })
            ->when($request->search, function ($q) use ($request) {
                return $q->where('title', 'like', '%' . $request->search . '%');
            })
            ->latest();

        $materials = $query->paginate(20);
        $classes = ClassModel::all();
        $subjects = $this->getSubjects();

        return view('admin.materials.index', compact('materials', 'classes', 'subjects'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = ClassModel::all();
        $subjects = $this->getSubjects();
        $lmsChapters = \App\Models\LmsChapter::with('topics')->orderBy('subject')->get();

        return view('admin.materials.create', compact('classes', 'subjects', 'lmsChapters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'lms_topic_id' => 'nullable|exists:lms_topics,id',
            'file_path' => 'nullable|file|max:20480',
            'file_type' => 'nullable|in:pdf,doc,docx,ppt,pptx,xls,xlsx,video,link,other',
            'external_link' => 'nullable|url',
            'is_published' => 'boolean',
        ]);

        if ($request->filled('lms_topic_id')) {
            $topic = \App\Models\LmsTopic::find($request->lms_topic_id);
            $validated['lms_topic_id'] = $topic->id ?? null;
            $validated['lms_chapter_id'] = $topic->lms_chapter_id ?? null;
        } else {
            $validated['lms_topic_id'] = null;
            $validated['lms_chapter_id'] = null;
        }

        if ($request->hasFile('file_path')) {
            $file = $request->file('file_path');
            $ext = strtolower($file->getClientOriginalExtension());
            $savedFile = save_uploaded_public_file($file, 'doc/materials');
            $validated['file_path'] = 'doc/materials/' . basename($savedFile);
            $validated['file_type'] = $ext;
        }

        $validated['teacher_id'] = Auth::id();
        $validated['is_published'] = $request->has('is_published');

        Material::create($validated);

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Material $material)
    {
        $material->load(['class', 'teacher', 'chapter', 'topic']);
        return view('admin.materials.show', compact('material'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Material $material)
    {
        $classes = ClassModel::all();
        $subjects = $this->getSubjects();
        $lmsChapters = \App\Models\LmsChapter::with('topics')->orderBy('subject')->get();

        return view('admin.materials.edit', compact('material', 'classes', 'subjects', 'lmsChapters'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Material $material)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'subject' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'lms_topic_id' => 'nullable|exists:lms_topics,id',
            'file_path' => 'nullable|file|max:20480',
            'file_type' => 'nullable|in:pdf,doc,docx,ppt,pptx,xls,xlsx,video,link,other',
            'external_link' => 'nullable|url',
            'is_published' => 'boolean',
        ]);

        if ($request->filled('lms_topic_id')) {
            $topic = \App\Models\LmsTopic::find($request->lms_topic_id);
            $validated['lms_topic_id'] = $topic->id ?? null;
            $validated['lms_chapter_id'] = $topic->lms_chapter_id ?? null;
        } else {
            $validated['lms_topic_id'] = null;
            $validated['lms_chapter_id'] = null;
        }

        if ($request->hasFile('file_path')) {
            if ($material->file_path) {
                delete_public_file($material->file_path, 'doc/materials');
            }
            $file = $request->file('file_path');
            $ext = strtolower($file->getClientOriginalExtension());
            $savedFile = save_uploaded_public_file($file, 'doc/materials');
            $validated['file_path'] = 'doc/materials/' . basename($savedFile);
            $validated['file_type'] = $ext;
        } else {
            unset($validated['file_path']);
        }

        $validated['teacher_id'] = Auth::id();
        $validated['is_published'] = $request->has('is_published');

        $material->update($validated);

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Material $material)
    {
        if ($material->file_path) {
            delete_public_file($material->file_path, 'doc/materials');
        }

        $material->delete();

        return redirect()->route('admin.materials.index')
            ->with('success', 'Materi berhasil dihapus!');
    }

    /**
     * Download material file
     */
    public function download(Material $material)
    {
        if (!$material->file_path) {
            return back()->with('error', 'File tidak tersedia.');
        }

        if (\Illuminate\Support\Facades\File::exists(public_path($material->file_path))) {
            return response()->download(public_path($material->file_path));
        }
        if (\Illuminate\Support\Facades\File::exists(public_path('doc/' . $material->file_path))) {
            return response()->download(public_path('doc/' . $material->file_path));
        }
        if (\Illuminate\Support\Facades\File::exists(public_path('img/' . $material->file_path))) {
            return response()->download(public_path('img/' . $material->file_path));
        }

        if (Storage::disk('doc')->exists($material->file_path)) {
            return Storage::disk('doc')->download($material->file_path);
        }
        if (Storage::disk('public')->exists($material->file_path)) {
            return Storage::disk('public')->download($material->file_path);
        }

        return back()->with('error', 'File materi tidak ditemukan.');
    }

    /**
     * Get list of subjects
     */
    private function getSubjects(): array
    {
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc')->pluck('name')->toArray();
        if (empty($subjects)) {
            return [
                'Matematika', 'Bahasa Indonesia', 'Bahasa Inggris', 'Fisika', 'Kimia', 'Biologi', 'Sejarah', 'Pendidikan Agama'
            ];
        }
        return $subjects;
    }
}
