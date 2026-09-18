<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassModel;
use App\Models\LmsChapter;
use App\Models\LmsTopic;
use App\Models\LmsTopicQuiz;
use App\Models\LmsLiveClass;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class LmsChapterController extends Controller
{
    public function index(Request $request)
    {
        $classes = ClassModel::where('is_active', true)->get();
        $globalSubjects = \App\Models\Subject::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc')->get();
        $query = LmsChapter::with([
            'class',
            'topics.quizzes',
            'topics.materials',
            'topics.assignments.class',
            'topics.exams.class'
        ]);

        if ($request->filled('major_id')) {
            $query->whereHas('class', function ($cq) use ($request) {
                $cq->where('major_id', $request->major_id);
            });
        }

        if ($request->filled('class_id')) {
            $query->where(function($q) use ($request) {
                $q->where('class_id', $request->class_id)->orWhereNull('class_id');
            });
        }

        if ($request->filled('subject')) {
            $query->where('subject', 'like', '%' . $request->subject . '%');
        }

        $chapters = $query->orderBy('subject')->orderBy('order', 'asc')->get();
        $groupedChapters = $chapters->groupBy('subject');
        $liveClasses = LmsLiveClass::with(['class', 'teacher'])->latest()->get();

        $allAssignments = \App\Models\Assignment::with(['class'])->orderBy('title')->get();
        $allExams = \App\Models\Exam::with(['class'])->orderBy('title')->get();
        $allMaterials = \App\Models\Material::with(['class'])->orderBy('title')->get();

        return view('admin.lms.chapters.index', compact('chapters', 'groupedChapters', 'classes', 'liveClasses', 'globalSubjects', 'allAssignments', 'allExams', 'allMaterials'));
    }

    public function storeChapter(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'order' => 'nullable|integer',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',
        ]);

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $ext;
            $targetDir = public_path('img/lms/chapters');
            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }
            $file->move($targetDir, $filename);
            $validated['cover_image'] = 'img/lms/chapters/' . $filename;
        }

        $validated['order'] = $validated['order'] ?? 0;
        $validated['is_active'] = true;

        LmsChapter::create($validated);

        return back()->with('success', 'Bab pembelajaran berhasil ditambahkan.');
    }

    public function updateChapter(Request $request, LmsChapter $chapter)
    {
        $validated = $request->validate([
            'subject' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'order' => 'nullable|integer',
            'cover_image' => 'nullable|image|mimes:jpg,jpeg,png,webp,svg|max:5120',
        ]);

        if ($request->hasFile('cover_image')) {
            if ($chapter->cover_image && File::exists(public_path($chapter->cover_image))) {
                File::delete(public_path($chapter->cover_image));
            }
            $file = $request->file('cover_image');
            $ext = strtolower($file->getClientOriginalExtension());
            $filename = time() . '_' . \Illuminate\Support\Str::random(10) . '.' . $ext;
            $targetDir = public_path('img/lms/chapters');
            if (!File::isDirectory($targetDir)) {
                File::makeDirectory($targetDir, 0755, true, true);
            }
            $file->move($targetDir, $filename);
            $validated['cover_image'] = 'img/lms/chapters/' . $filename;
        }

        $chapter->update($validated);

        return back()->with('success', 'Bab pembelajaran berhasil diperbarui.');
    }

    public function destroyChapter(LmsChapter $chapter)
    {
        if ($chapter->cover_image && File::exists(public_path($chapter->cover_image))) {
            File::delete(public_path($chapter->cover_image));
        }
        $chapter->delete();
        return back()->with('success', 'Bab pembelajaran berhasil dihapus.');
    }

    public function storeTopic(Request $request)
    {
        $validated = $request->validate([
            'lms_chapter_id' => 'required|exists:lms_chapters,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video_url' => 'nullable|string',
            'video_duration' => 'nullable|string',
            'xp_reward' => 'nullable|integer',
            'summary_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx|max:10240',
        ]);

        if ($request->hasFile('summary_file')) {
            $savedFile = save_uploaded_public_file($request->file('summary_file'), 'doc/lms');
            $validated['summary_file'] = 'doc/lms/' . basename($savedFile);
        }

        $validated['xp_reward'] = $validated['xp_reward'] ?? 50;

        LmsTopic::create($validated);

        return back()->with('success', 'Sub-Bab (Modul Video) berhasil ditambahkan.');
    }

    public function destroyTopic(LmsTopic $topic)
    {
        if ($topic->summary_file) {
            delete_public_file($topic->summary_file);
        }
        $topic->delete();
        return back()->with('success', 'Sub-Bab berhasil dihapus.');
    }

    public function storeQuiz(Request $request)
    {
        $validated = $request->validate([
            'lms_topic_id' => 'required|exists:lms_topics,id',
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'option_e' => 'nullable|string',
            'correct_option' => 'required|in:a,b,c,d,e',
            'explanation' => 'nullable|string',
            'explanation_video' => 'nullable|string',
            'xp_reward' => 'nullable|integer',
        ]);

        $validated['xp_reward'] = $validated['xp_reward'] ?? 100;

        LmsTopicQuiz::create($validated);

        return back()->with('success', 'Soal Kuis Interaktif berhasil ditambahkan.');
    }

    public function destroyQuiz(LmsTopicQuiz $quiz)
    {
        $quiz->delete();
        return back()->with('success', 'Soal Kuis berhasil dihapus.');
    }

    public function storeTopicAssignment(Request $request)
    {
        $topic = LmsTopic::with('chapter')->findOrFail($request->input('lms_topic_id'));

        $validated = $request->validate([
            'lms_topic_id' => 'required|exists:lms_topics,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'class_id' => 'nullable|exists:classes,id',
            'due_date' => 'required|date',
            'max_score' => 'nullable|integer',
            'attachment' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,rar,jpg,png|max:10240',
        ]);

        $attachmentPath = null;
        if ($request->hasFile('attachment')) {
            $file = $request->file('attachment');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'xlsx', 'xls', 'csv', 'doc', 'docx', 'ppt', 'pptx', 'txt', 'zip', 'rar']);
            $savedFile = save_uploaded_public_file($file, $isDoc ? 'doc/assignments' : 'img/assignments');
            $attachmentPath = ($isDoc ? 'doc/assignments/' : 'img/assignments/') . basename($savedFile);
        }

        \App\Models\Assignment::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'subject' => $topic->chapter->subject,
            'class_id' => $validated['class_id'] ?? $topic->chapter->class_id,
            'lms_chapter_id' => $topic->lms_chapter_id,
            'lms_topic_id' => $topic->id,
            'teacher_id' => auth()->id(),
            'due_date' => $validated['due_date'],
            'max_score' => $validated['max_score'] ?? 100,
            'attachment' => $attachmentPath,
            'status' => 'published',
        ]);

        return back()->with('success', 'Tugas berhasil dibuat dan ditautkan ke Sub-Bab.');
    }

    public function storeTopicExam(Request $request)
    {
        $topic = LmsTopic::with('chapter')->findOrFail($request->input('lms_topic_id'));

        $validated = $request->validate([
            'lms_topic_id' => 'required|exists:lms_topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'duration_minutes' => 'required|integer|min:1',
            'start_time' => 'nullable|date',
            'end_time' => 'nullable|date',
            'exam_type' => 'required|in:quiz,practice,exam',
        ]);

        \App\Models\Exam::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'subject_name' => $topic->chapter->subject,
            'class_id' => $validated['class_id'] ?? $topic->chapter->class_id,
            'lms_chapter_id' => $topic->lms_chapter_id,
            'lms_topic_id' => $topic->id,
            'teacher_id' => auth()->id(),
            'duration_minutes' => $validated['duration_minutes'],
            'start_time' => $validated['start_time'] ?? now(),
            'end_time' => $validated['end_time'] ?? now()->addDays(30),
            'is_published' => true,
            'exam_type' => $validated['exam_type'],
        ]);

        return back()->with('success', 'Ujian/Kuis CBT berhasil dibuat dan ditautkan ke Sub-Bab.');
    }

    public function storeTopicMaterial(Request $request)
    {
        $topic = LmsTopic::with('chapter')->findOrFail($request->input('lms_topic_id'));

        $validated = $request->validate([
            'lms_topic_id' => 'required|exists:lms_topics,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'external_link' => 'nullable|url',
            'material_file' => 'nullable|file|mimes:pdf,doc,docx,ppt,pptx,zip,mp4|max:20480',
        ]);

        $filePath = null;
        $fileType = null;

        if ($request->hasFile('material_file')) {
            $file = $request->file('material_file');
            $ext = strtolower($file->getClientOriginalExtension());
            $isDoc = in_array($ext, ['pdf', 'xlsx', 'xls', 'csv', 'doc', 'docx', 'ppt', 'pptx', 'txt']);
            $savedFile = save_uploaded_public_file($file, $isDoc ? 'doc/materials' : 'img/materials');
            $filePath = ($isDoc ? 'doc/materials/' : 'img/materials/') . basename($savedFile);
            $fileType = $ext;
        }

        \App\Models\Material::create([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'subject' => $topic->chapter->subject,
            'class_id' => $topic->chapter->class_id,
            'lms_chapter_id' => $topic->lms_chapter_id,
            'lms_topic_id' => $topic->id,
            'teacher_id' => auth()->id(),
            'file_path' => $filePath,
            'file_type' => $fileType,
            'external_link' => $validated['external_link'] ?? null,
            'is_published' => true,
        ]);

        return back()->with('success', 'Bahan ajar berhasil ditambahkan ke Sub-Bab.');
    }

    public function storeLiveClass(Request $request)
    {
        $validated = $request->validate([
            'subject' => 'required|string',
            'title' => 'required|string',
            'description' => 'nullable|string',
            'class_id' => 'nullable|exists:classes,id',
            'scheduled_at' => 'required|date',
            'duration_minutes' => 'nullable|integer',
            'meeting_url' => 'nullable|string',
        ]);

        $validated['teacher_id'] = auth()->id();
        $validated['status'] = 'scheduled';

        LmsLiveClass::create($validated);

        return back()->with('success', 'Sesi Live Teaching berhasil dijadwalkan.');
    }

    public function updateLiveStatus(Request $request, LmsLiveClass $liveClass)
    {
        $validated = $request->validate([
            'status' => 'required|in:scheduled,live,ended',
        ]);

        $liveClass->update(['status' => $validated['status']]);

        return back()->with('success', 'Status Live Teaching berhasil diubah.');
    }

    public function linkAssignment(Request $request)
    {
        $validated = $request->validate([
            'assignment_id' => 'required|exists:assignments,id',
            'lms_topic_id' => 'required|exists:lms_topics,id',
        ]);

        $topic = LmsTopic::findOrFail($validated['lms_topic_id']);
        $assignment = \App\Models\Assignment::findOrFail($validated['assignment_id']);

        $assignment->update([
            'lms_chapter_id' => $topic->lms_chapter_id,
            'lms_topic_id' => $topic->id,
        ]);

        return back()->with('success', 'Tugas "' . $assignment->title . '" berhasil ditautkan ke Sub-Bab "' . $topic->title . '".');
    }

    public function linkExam(Request $request)
    {
        $validated = $request->validate([
            'exam_id' => 'required|exists:exams,id',
            'lms_topic_id' => 'required|exists:lms_topics,id',
        ]);

        $topic = LmsTopic::findOrFail($validated['lms_topic_id']);
        $exam = \App\Models\Exam::findOrFail($validated['exam_id']);

        $exam->update([
            'lms_chapter_id' => $topic->lms_chapter_id,
            'lms_topic_id' => $topic->id,
        ]);

        return back()->with('success', 'Ujian CBT "' . $exam->title . '" berhasil ditautkan ke Sub-Bab "' . $topic->title . '".');
    }

    public function linkMaterial(Request $request)
    {
        $validated = $request->validate([
            'material_id' => 'required|exists:materials,id',
            'lms_topic_id' => 'required|exists:lms_topics,id',
        ]);

        $topic = LmsTopic::findOrFail($validated['lms_topic_id']);
        $material = \App\Models\Material::findOrFail($validated['material_id']);

        $material->update([
            'lms_chapter_id' => $topic->lms_chapter_id,
            'lms_topic_id' => $topic->id,
        ]);

        return back()->with('success', 'Bahan Ajar "' . $material->title . '" berhasil ditautkan ke Sub-Bab "' . $topic->title . '".');
    }

    public function unlinkAssignment($id)
    {
        $assignment = \App\Models\Assignment::findOrFail($id);
        $assignment->update([
            'lms_chapter_id' => null,
            'lms_topic_id' => null,
        ]);

        return back()->with('success', 'Tugas berhasil dilepas dari Sub-Bab LMS.');
    }

    public function unlinkExam($id)
    {
        $exam = \App\Models\Exam::findOrFail($id);
        $exam->update([
            'lms_chapter_id' => null,
            'lms_topic_id' => null,
        ]);

        return back()->with('success', 'Ujian CBT berhasil dilepas dari Sub-Bab LMS.');
    }

    public function unlinkMaterial($id)
    {
        $material = \App\Models\Material::findOrFail($id);
        $material->update([
            'lms_chapter_id' => null,
            'lms_topic_id' => null,
        ]);

        return back()->with('success', 'Bahan Ajar berhasil dilepas dari Sub-Bab LMS.');
    }
}
