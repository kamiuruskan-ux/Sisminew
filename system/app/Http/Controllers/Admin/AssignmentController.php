<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Helpers\IdEncrypter;
use App\Models\Assignment;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AssignmentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        $query = Assignment::with(['class', 'teacher', 'submissions', 'chapter', 'topic'])
            ->when($user->isTeacher(), function ($q) use ($user) {
                return $q->where('teacher_id', $user->id);
            });

        if ($request->filled('major_id')) {
            $query->whereHas('class', function ($q) use ($request) {
                $q->where('major_id', $request->major_id);
            });
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $totalAssignments = (clone $query)->count();
        $publishedCount = (clone $query)->where('status', 'published')->count();
        $draftCount = (clone $query)->where('status', 'draft')->count();
        $closedCount = (clone $query)->where('status', 'closed')->count();

        $assignments = $query->latest()->paginate(20)->withQueryString();

        $classes = ClassModel::orderBy('name')->get();
        $lmsChapters = \App\Models\LmsChapter::with('topics')->orderBy('title')->get();

        return view('admin.assignments.index', compact(
            'assignments',
            'classes',
            'lmsChapters',
            'totalAssignments',
            'publishedCount',
            'draftCount',
            'closedCount'
        ));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $classes = ClassModel::all();
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc')->get();
        $lmsChapters = \App\Models\LmsChapter::with('topics')->orderBy('title')->get();

        return view('admin.assignments.create', compact('classes', 'subjects', 'lmsChapters'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'lms_chapter_id' => 'nullable|exists:lms_chapters,id',
            'lms_topic_id' => 'nullable|exists:lms_topics,id',
            'due_date' => 'required|date|after:now',
            'attachment' => 'nullable|file|max:10240',
            'max_score' => 'required|integer|min:1|max:100',
            'status' => 'required|in:draft,published,closed',
        ]);

        if ($request->filled('lms_topic_id')) {
            $topic = \App\Models\LmsTopic::find($request->lms_topic_id);
            if ($topic) {
                $validated['lms_chapter_id'] = $topic->lms_chapter_id;
            }
        }

        if ($request->hasFile('attachment')) {
            $savedFile = save_uploaded_public_file($request->file('attachment'), 'doc/materials');
            $validated['attachment'] = 'doc/materials/' . basename($savedFile);
        }

        $validated['teacher_id'] = Auth::id();

        Assignment::create($validated);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Tugas berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $encryptedId)
    {
        $assignmentId = IdEncrypter::decrypt($encryptedId);
        $assignment = Assignment::with(['class', 'teacher', 'submissions.student', 'chapter', 'topic'])->findOrFail($assignmentId);

        return view('admin.assignments.show', compact('assignment'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $encryptedId)
    {
        $assignmentId = IdEncrypter::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($assignmentId);
        $classes = ClassModel::all();
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc')->get();
        $lmsChapters = \App\Models\LmsChapter::with('topics')->orderBy('title')->get();
        $encryptedId = IdEncrypter::encrypt($assignment->id);

        return view('admin.assignments.edit', compact('assignment', 'classes', 'subjects', 'lmsChapters', 'encryptedId'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $encryptedId)
    {
        $assignmentId = IdEncrypter::decrypt($encryptedId);
        $assignment = Assignment::findOrFail($assignmentId);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'subject' => 'required|string|max:255',
            'class_id' => 'required|exists:classes,id',
            'lms_chapter_id' => 'nullable|exists:lms_chapters,id',
            'lms_topic_id' => 'nullable|exists:lms_topics,id',
            'due_date' => 'required|date',
            'attachment' => 'nullable|file|max:10240',
            'max_score' => 'required|integer|min:1|max:100',
            'status' => 'required|in:draft,published,closed',
        ]);

        if ($request->filled('lms_topic_id')) {
            $topic = \App\Models\LmsTopic::find($request->lms_topic_id);
            if ($topic) {
                $validated['lms_chapter_id'] = $topic->lms_chapter_id;
            }
        }

        if ($request->hasFile('attachment')) {
            if ($assignment->attachment) {
                delete_public_file($assignment->attachment, 'doc/materials');
            }
            $savedFile = save_uploaded_public_file($request->file('attachment'), 'doc/materials');
            $validated['attachment'] = 'doc/materials/' . basename($savedFile);
        } else {
            unset($validated['attachment']);
        }

        $assignment->update($validated);

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Tugas berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $encryptedId)
    {
        $assignmentId = IdEncrypter::decrypt($encryptedId);
        $assignment = Assignment::with('submissions')->findOrFail($assignmentId);

        if ($assignment->attachment) {
            delete_public_file($assignment->attachment, 'doc/materials');
        }

        foreach ($assignment->submissions as $submission) {
            if ($submission->attachment) {
                delete_public_file($submission->attachment, 'doc/submissions');
            }
        }

        $assignment->delete();

        return redirect()->route('admin.assignments.index')
            ->with('success', 'Tugas berhasil dihapus!');
    }
}
