<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamResult;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;

class ExamController extends Controller
{
    /**
     * Display list of active CBT exams for the student.
     */
    public function index()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Profil siswa tidak ditemukan.');
        }

        // Active published exams for student's class
        $exams = Exam::with(['questions', 'class'])
            ->where('is_published', true)
            ->where(function ($query) use ($student) {
                $query->whereNull('class_id')
                    ->orWhere('class_id', $student->class_id);
            })
            ->latest()
            ->get();

        // Get completed student exam results
        $results = ExamResult::where('student_id', $student->id)
            ->get()
            ->keyBy('exam_id');

        return view('student.exams.index', compact('exams', 'results', 'student'));
    }

    /**
     * Show live CBT exam interface.
     */
    public function show($id)
    {
        $user = auth()->user();
        $student = $user->student;

        // Cache the exam with its questions and class for 1 hour
        $exam = Cache::remember("exam_show_{$id}", 3600, function () use ($id) {
            return Exam::with(['questions', 'class'])->findOrFail($id);
        });

        // Check if student already submitted this exam
        $existingResult = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->whereIn('status', ['completed', 'needs_grading'])
            ->first();

        if ($existingResult) {
            $infoMsg = $existingResult->status === 'needs_grading'
                ? 'Anda telah mengumpulkan ujian ' . $exam->title . '. Jawaban essay Anda sedang menunggu koreksi Guru Pengampu.'
                : 'Anda telah menyelesaikan ujian ' . $exam->title . ' dengan nilai ' . $existingResult->score;

            return redirect()->route('student.exams.index')->with('info', $infoMsg);
        }

        // Get or start exam session
        $examResult = ExamResult::firstOrCreate([
            'exam_id' => $exam->id,
            'student_id' => $student->id,
        ], [
            'started_at' => now(),
            'status' => 'in_progress',
            'answers' => [],
        ]);

        return view('student.exams.show', compact('exam', 'examResult', 'student'));
    }

    /**
     * Submit CBT exam and calculate score automatically.
     */
    public function submit(Request $request, $id)
    {
        $user = auth()->user();
        $student = $user->student;

        // Use cached exam to avoid querying all questions again
        $exam = Cache::remember("exam_show_{$id}", 3600, function () use ($id) {
            return Exam::with('questions')->findOrFail($id);
        });
        $submittedAnswers = $request->input('answers', []); // [question_id => answer_data]

        $totalScore = 0;
        $maxPossibleScore = 0;
        $hasEssay = false;

        foreach ($exam->questions as $question) {
            $maxPossibleScore += $question->score_weight;
            $userAns = $submittedAnswers[$question->id] ?? null;
            $type = $question->type ?? 'pg';

            $isCorrect = false;

            if ($type === 'pg') {
                $selectedAnswer = is_string($userAns) ? strtolower(trim($userAns)) : '';
                if ($selectedAnswer !== '' && $selectedAnswer === strtolower(trim($question->correct_answer))) {
                    $isCorrect = true;
                }
            } elseif ($type === 'pg_kompleks') {
                $userKeys = is_array($userAns) ? array_map('strtolower', $userAns) : [];
                sort($userKeys);

                $correctKeys = (array) ($question->correct_answer_json ?? []);
                if (empty($correctKeys) && !empty($question->correct_answer)) {
                    $correctKeys = explode(',', $question->correct_answer);
                }
                $correctKeys = array_map('strtolower', $correctKeys);
                sort($correctKeys);

                if (!empty($userKeys) && $userKeys === $correctKeys) {
                    $isCorrect = true;
                }
            } elseif ($type === 'benar_salah') {
                if (is_array($question->options_json) && count($question->options_json) > 0) {
                    $correctAnswers = (array) ($question->correct_answer_json ?? []);
                    $userAnswers = is_array($userAns) ? $userAns : [];
                    $matchCount = 0;
                    $totalItems = count($question->options_json);

                    foreach ($question->options_json as $idx => $stmt) {
                        $uAns = strtolower(trim($userAnswers[$idx] ?? ''));
                        $cAns = strtolower(trim($correctAnswers[$idx] ?? ''));
                        if ($uAns !== '' && $uAns === $cAns) {
                            $matchCount++;
                        }
                    }

                    if ($totalItems > 0 && $matchCount === $totalItems) {
                        $isCorrect = true;
                    }
                } else {
                    $selectedAnswer = is_string($userAns) ? strtolower(trim($userAns)) : '';
                    if ($selectedAnswer !== '' && $selectedAnswer === strtolower(trim($question->correct_answer))) {
                        $isCorrect = true;
                    }
                }
            } elseif ($type === 'menjodohkan') {
                $correctMap = (array) ($question->correct_answer_json ?? []);
                $userAnswers = is_array($userAns) ? $userAns : [];

                $leftItems = $question->options_json['left'] ?? [];
                $totalItems = count($leftItems);
                $matchCount = 0;

                foreach ($leftItems as $idx => $premise) {
                    $uAns = trim((string)($userAnswers[$idx] ?? ''));
                    $cAns = trim((string)($correctMap[$idx] ?? ''));
                    if ($uAns !== '' && $uAns === $cAns) {
                        $matchCount++;
                    }
                }

                if ($totalItems > 0 && $matchCount === $totalItems) {
                    $isCorrect = true;
                }
            } elseif ($type === 'essay') {
                $hasEssay = true;
                // Essay is graded manually by teacher/admin
                $isCorrect = false;
            }

            if ($isCorrect) {
                $totalScore += $question->score_weight;
            }
        }

        // Calculate initial objective score scaled to 100
        $finalScore = $maxPossibleScore > 0 ? round(($totalScore / $maxPossibleScore) * 100, 2) : 0;
        $status = $hasEssay ? 'needs_grading' : 'completed';

        $examResult = ExamResult::where('exam_id', $exam->id)
            ->where('student_id', $student->id)
            ->first();

        if ($examResult) {
            $submittedAnswers['_violations'] = (int) $request->input('violation_count', 0);
            $submittedAnswers['_submission_reason'] = $request->input('submission_reason', 'Selesai Mandiri');

            $examResult->update([
                'score' => $finalScore,
                'answers' => $submittedAnswers,
                'submitted_at' => now(),
                'status' => $status,
            ]);
        }

        $msg = $hasEssay
            ? 'Ujian "' . $exam->title . '" berhasil dikumpulkan! Jawaban essay Anda menunggu koreksi dari Guru Pengampu.'
            : 'Ujian "' . $exam->title . '" berhasil dikumpulkan! Nilai Anda: ' . $finalScore;

        return redirect()->route('student.exams.index')->with('success', $msg);
    }
}
