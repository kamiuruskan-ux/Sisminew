<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Exam;
use App\Models\Question;
use App\Models\ExamResult;
use App\Models\ClassModel;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ExamController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $exams = Exam::with(['class', 'questions', 'results', 'chapter', 'topic', 'teacher'])
            ->when($user->isTeacher(), function ($q) use ($user) {
                return $q->where('teacher_id', $user->id);
            })
            ->latest()->get();

        $lmsChapters = \App\Models\LmsChapter::with('topics')->orderBy('title')->get();

        return view('admin.exams.index', compact('exams', 'lmsChapters'));
    }

    public function create()
    {
        $classes = ClassModel::all();
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc')->get();
        $lmsChapters = \App\Models\LmsChapter::with('topics')->orderBy('title')->get();
        $teachers = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('slug', ['guru', 'guru-bk', 'admin', 'super-admin']))->orderBy('name')->get();
        if ($teachers->isEmpty()) {
            $teachers = \App\Models\User::orderBy('name')->get();
        }

        return view('admin.exams.create', compact('classes', 'subjects', 'lmsChapters', 'teachers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'subject_name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:5',
            'lms_chapter_id' => 'nullable|exists:lms_chapters,id',
            'lms_topic_id' => 'nullable|exists:lms_topics,id',
            'teacher_id' => 'nullable|exists:users,id',
            'exam_type' => 'nullable|in:quiz,practice,exam',
        ]);

        $chapterId = $request->lms_chapter_id;
        if ($request->filled('lms_topic_id')) {
            $topic = \App\Models\LmsTopic::find($request->lms_topic_id);
            if ($topic) {
                $chapterId = $topic->lms_chapter_id;
            }
        }

        $exam = Exam::create([
            'title' => $request->title,
            'description' => $request->description,
            'subject_name' => $request->subject_name,
            'class_id' => $request->class_id,
            'lms_chapter_id' => $chapterId,
            'lms_topic_id' => $request->lms_topic_id,
            'teacher_id' => $request->teacher_id ?? auth()->id(),
            'duration_minutes' => $request->duration_minutes,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_published' => $request->has('is_published'),
            'exam_type' => $request->exam_type ?? 'exam',
        ]);

        return redirect()->route('admin.exams.show', $exam->id)->with('success', 'Ujian CBT berhasil dibuat. Silakan tambahkan soal.');
    }

    public function show($id)
    {
        $exam = Exam::with(['questions', 'class', 'teacher', 'results.student.user', 'chapter', 'topic'])->findOrFail($id);
        $classes = ClassModel::orderBy('name')->get();
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc')->get();
        $lmsChapters = \App\Models\LmsChapter::with('topics')->orderBy('title')->get();
        $teachers = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('slug', ['guru', 'guru-bk', 'admin', 'super-admin']))->orderBy('name')->get();

        return view('admin.exams.show', compact('exam', 'classes', 'subjects', 'lmsChapters', 'teachers'));
    }

    public function edit($id)
    {
        $exam = Exam::findOrFail($id);
        $classes = ClassModel::all();
        $subjects = \App\Models\Subject::where('is_active', true)->orderBy('order', 'asc')->orderBy('name', 'asc')->get();
        $lmsChapters = \App\Models\LmsChapter::with('topics')->orderBy('title')->get();
        $teachers = \App\Models\User::whereHas('roles', fn($q) => $q->whereIn('slug', ['guru', 'guru-bk', 'admin', 'super-admin']))->orderBy('name')->get();
        if ($teachers->isEmpty()) {
            $teachers = \App\Models\User::orderBy('name')->get();
        }

        return view('admin.exams.edit', compact('exam', 'classes', 'subjects', 'lmsChapters', 'teachers'));
    }

    public function update(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:255',
            'subject_name' => 'required|string|max:255',
            'duration_minutes' => 'required|integer|min:5',
            'lms_chapter_id' => 'nullable|exists:lms_chapters,id',
            'lms_topic_id' => 'nullable|exists:lms_topics,id',
            'teacher_id' => 'nullable|exists:users,id',
            'exam_type' => 'nullable|in:quiz,practice,exam',
        ]);

        $chapterId = $request->lms_chapter_id;
        if ($request->filled('lms_topic_id')) {
            $topic = \App\Models\LmsTopic::find($request->lms_topic_id);
            if ($topic) {
                $chapterId = $topic->lms_chapter_id;
            }
        }

        $exam->update([
            'title' => $request->title,
            'description' => $request->description,
            'subject_name' => $request->subject_name,
            'class_id' => $request->class_id,
            'lms_chapter_id' => $chapterId,
            'lms_topic_id' => $request->lms_topic_id,
            'teacher_id' => $request->teacher_id ?? $exam->teacher_id ?? auth()->id(),
            'duration_minutes' => $request->duration_minutes,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'is_published' => $request->has('is_published'),
            'exam_type' => $request->exam_type ?? $exam->exam_type ?? 'exam',
        ]);

        return redirect()->route('admin.exams.show', $exam->id)->with('success', 'Informasi Ujian CBT berhasil diperbarui.');
    }

    public function showGradeStudent($examId, $resultId = null)
    {
        $exam = Exam::with(['questions', 'class', 'teacher'])->findOrFail($examId);
        $essayQuestions = $exam->questions->where('type', 'essay');
        
        $results = ExamResult::with('student.user')
            ->where('exam_id', $examId)
            ->whereIn('status', ['completed', 'needs_grading'])
            ->get();

        $selectedResult = $resultId ? $results->firstWhere('id', $resultId) : null;

        return view('admin.exams.grade', compact('exam', 'results', 'essayQuestions', 'resultId', 'selectedResult'));
    }

    public function storeStudentGrade(Request $request, $examId, $resultId = null)
    {
        $exam = Exam::with('questions')->findOrFail($examId);

        if ($resultId) {
            $results = ExamResult::with('student.user')->where('exam_id', $examId)->where('id', $resultId)->get();
        } else {
            $results = ExamResult::with('student.user')->where('exam_id', $examId)->whereIn('status', ['completed', 'needs_grading'])->get();
        }

        $rawScores = (array) $request->input('essay_scores', []);
        $rawFeedback = (array) $request->input('essay_feedback', []);

        foreach ($results as $result) {
            if (isset($rawScores[$result->id]) && is_array($rawScores[$result->id])) {
                $studentEssayScores = $rawScores[$result->id];
                $studentEssayFeedback = (array) ($rawFeedback[$result->id] ?? []);
            } else {
                $studentEssayScores = $rawScores;
                $studentEssayFeedback = $rawFeedback;
            }

            $totalEarnedScore = 0;
            $maxPossibleScore = 0;
            $submittedAnswers = (array) ($result->answers ?? []);
            $savedEssayScores = (array) ($submittedAnswers['_essay_scores'] ?? []);
            $savedEssayFeedback = (array) ($submittedAnswers['_essay_feedback'] ?? []);

            foreach ($exam->questions as $question) {
                $maxPossibleScore += $question->score_weight;

                if ($question->type === 'essay') {
                    $givenScore = isset($studentEssayScores[$question->id]) 
                        ? min($question->score_weight, max(0, floatval($studentEssayScores[$question->id]))) 
                        : ($savedEssayScores[$question->id] ?? 0);

                    $totalEarnedScore += $givenScore;
                    $savedEssayScores[$question->id] = $givenScore;
                    $savedEssayFeedback[$question->id] = $studentEssayFeedback[$question->id] ?? ($savedEssayFeedback[$question->id] ?? '');
                } else {
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
                    }

                    if ($isCorrect) {
                        $totalEarnedScore += $question->score_weight;
                    }
                }
            }

            $finalScore = $maxPossibleScore > 0 ? round(($totalEarnedScore / $maxPossibleScore) * 100, 2) : 0;

            $submittedAnswers['_essay_scores'] = $savedEssayScores;
            $submittedAnswers['_essay_feedback'] = $savedEssayFeedback;
            $submittedAnswers['_graded_by'] = auth()->id();
            $submittedAnswers['_graded_at'] = now()->toDateTimeString();

            $result->update([
                'score' => $finalScore,
                'answers' => $submittedAnswers,
                'status' => 'completed',
            ]);
        }

        return redirect()->route('admin.exams.index')
            ->with('success', 'Seluruh koreksi essay & nilai akhir siswa berhasil disimpan!');
    }

    public function storeQuestion(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $request->validate([
            'question_text' => 'required|string',
            'type' => 'nullable|in:pg,pg_kompleks,benar_salah,menjodohkan,essay',
            'question_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $data = $this->prepareQuestionData($request);
        $data['exam_id'] = $exam->id;

        if ($request->hasFile('question_image')) {
            $savedPath = save_uploaded_public_file($request->file('question_image'), 'img/questions');
            $data['image_path'] = basename($savedPath);
        }

        Question::create($data);

        return back()->with('success', 'Soal ujian berhasil ditambahkan.');
    }

    public function destroyQuestion($examId, $questionId)
    {
        $question = Question::where('exam_id', $examId)->findOrFail($questionId);
        if ($question->image_path) {
            delete_public_file($question->image_path, 'img/questions');
        }
        $question->delete();

        return back()->with('success', 'Soal ujian berhasil dihapus dari Bank Soal.');
    }

    public function updateQuestion(Request $request, $examId, $questionId)
    {
        $question = Question::where('exam_id', $examId)->findOrFail($questionId);

        $request->validate([
            'question_text' => 'required|string',
            'type' => 'nullable|in:pg,pg_kompleks,benar_salah,menjodohkan,essay',
            'question_image' => 'nullable|image|mimes:jpg,jpeg,png,gif,webp|max:2048',
        ]);

        $data = $this->prepareQuestionData($request);

        if ($request->hasFile('question_image')) {
            if ($question->image_path) {
                delete_public_file($question->image_path, 'img/questions');
            }
            $savedPath = save_uploaded_public_file($request->file('question_image'), 'img/questions');
            $data['image_path'] = 'questions/' . basename($savedPath);
        } elseif ($request->input('remove_image') === '1') {
            if ($question->image_path) {
                delete_public_file($question->image_path, 'img/questions');
            }
            $data['image_path'] = null;
        }

        $question->update($data);

        return back()->with('success', 'Soal ujian berhasil diperbarui.');
    }

    private function prepareQuestionData(Request $request)
    {
        $type = $request->input('type', 'pg');

        $data = [
            'type' => $type,
            'question_text' => $request->question_text,
            'score_weight' => $request->score_weight ?? 10,
            'option_a' => null,
            'option_b' => null,
            'option_c' => null,
            'option_d' => null,
            'option_e' => null,
            'options_json' => null,
            'correct_answer' => 'a',
            'correct_answer_json' => null,
        ];

        if ($type === 'pg') {
            $data['option_a'] = $request->option_a;
            $data['option_b'] = $request->option_b;
            $data['option_c'] = $request->option_c;
            $data['option_d'] = $request->option_d;
            $data['option_e'] = $request->option_e;
            $data['correct_answer'] = strtolower($request->correct_answer ?? 'a');
        } elseif ($type === 'pg_kompleks') {
            $data['option_a'] = $request->option_a;
            $data['option_b'] = $request->option_b;
            $data['option_c'] = $request->option_c;
            $data['option_d'] = $request->option_d;
            $data['option_e'] = $request->option_e;
            $keys = array_values(array_map('strtolower', (array) $request->input('correct_answers_kompleks', [])));
            $data['correct_answer_json'] = $keys;
            $data['correct_answer'] = implode(',', $keys);
        } elseif ($type === 'benar_salah') {
            $statements = (array) $request->input('bs_statements', []);
            $answers = (array) $request->input('bs_answers', []);

            if (!empty($statements)) {
                $items = [];
                $ansList = [];
                foreach ($statements as $idx => $stmt) {
                    if (trim($stmt) !== '') {
                        $items[] = trim($stmt);
                        $ansList[] = strtolower($answers[$idx] ?? 'benar');
                    }
                }
                $data['options_json'] = $items;
                $data['correct_answer_json'] = $ansList;
                $data['correct_answer'] = implode(',', $ansList);
            } else {
                $data['correct_answer'] = strtolower($request->correct_answer ?? 'benar');
            }
        } elseif ($type === 'menjodohkan') {
            $premises = (array) $request->input('match_premises', []);
            $targets = (array) $request->input('match_targets', []);
            $answers = (array) $request->input('match_answers', []);

            $cleanPremises = array_values(array_filter(array_map('trim', $premises)));
            $cleanTargets = array_values(array_filter(array_map('trim', $targets)));

            $matchMap = [];
            foreach ($cleanPremises as $idx => $premise) {
                $matchMap[(string)$idx] = $answers[$idx] ?? ($cleanTargets[$idx] ?? '');
            }

            $data['options_json'] = [
                'left' => $cleanPremises,
                'right' => $cleanTargets,
            ];
            $data['correct_answer_json'] = $matchMap;
            $data['correct_answer'] = 'menjodohkan';
        } elseif ($type === 'essay') {
            $key = $request->input('essay_key', '');
            $data['correct_answer'] = $key !== '' ? $key : 'essay';
            $data['options_json'] = [
                'sample_answer' => $key,
            ];
        }

        return $data;
    }

    public function destroy($id)
    {
        $exam = Exam::with('questions')->findOrFail($id);
        foreach ($exam->questions as $question) {
            if ($question->image_path) {
                delete_public_file($question->image_path, 'img/questions');
            }
        }
        $exam->delete();
        return redirect()->route('admin.exams.index')->with('success', 'Ujian CBT berhasil dihapus.');
    }

    /**
     * Download Excel template for importing CBT exam questions
     */
    public function downloadQuestionsTemplate()
    {
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Template Soal CBT');

        // Headers
        $headers = [
            'No',
            'Jenis Soal',
            'Teks Soal',
            'Pilihan A',
            'Pilihan B',
            'Pilihan C',
            'Pilihan D',
            'Pilihan E',
            'Kunci Jawaban',
            'Bobot Nilai'
        ];
        $sheet->fromArray([$headers], null, 'A1');

        $headerStyle = [
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 11],
            'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => '4F46E5']],
            'alignment' => ['horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER],
        ];
        $sheet->getStyle('A1:J1')->applyFromArray($headerStyle);

        // Sample Data Rows
        $sampleData = [
            [
                1,
                'pg',
                'Siapakah penemu arus listrik bolak-balik (AC)?',
                'Nikola Tesla',
                'Thomas Edison',
                'Alexander Graham Bell',
                'Michael Faraday',
                'Albert Einstein',
                'a',
                10
            ],
            [
                2,
                'pg_kompleks',
                'Pilihlah planet-planet yang tergolong ke dalam Planet Dalam (Terrestrial)?',
                'Merkurius',
                'Venus',
                'Jupiter',
                'Saturnus',
                'Mars',
                'a,b,e',
                10
            ],
            [
                3,
                'benar_salah',
                'Matahari merupakan pusat tata surya kita.',
                '',
                '',
                '',
                '',
                '',
                'benar',
                10
            ],
            [
                4,
                'essay',
                'Jelaskan hukum Newton I tentang gerak benda!',
                '',
                '',
                '',
                '',
                '',
                'Benda akan tetap diam atau bergerak lurus beraturan jika tidak ada gaya luar yang bekerja.',
                10
            ],
        ];
        $sheet->fromArray($sampleData, null, 'A2');

        foreach (range('A', 'J') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        $filename = 'Template_Import_Soal_CBT.xlsx';

        return response()->streamDownload(function () use ($writer) {
            $writer->save('php://output');
        }, $filename, [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    /**
     * Import questions from Excel into a specific CBT Exam
     */
    public function importQuestions(Request $request, $id)
    {
        $exam = Exam::findOrFail($id);

        $request->validate([
            'file' => 'required|file|mimes:xlsx,xls,csv|max:5120',
        ]);

        $file = $request->file('file');

        try {
            $spreadsheet = IOFactory::load($file->getRealPath());
            $sheet = $spreadsheet->getActiveSheet();
            $rows = $sheet->toArray(null, true, true, true);

            if (count($rows) <= 1) {
                return back()->with('error', 'File Excel kosong atau hanya berisi header.');
            }

            $successCount = 0;

            // Columns: A=No, B=Jenis Soal, C=Teks Soal, D=Pilihan A, E=Pilihan B, F=Pilihan C, G=Pilihan D, H=Pilihan E, I=Kunci Jawaban, J=Bobot Nilai
            for ($i = 2; $i <= count($rows); $i++) {
                $row = $rows[$i];
                $typeRaw = strtolower(trim((string)($row['B'] ?? '')));
                $questionText = trim((string)($row['C'] ?? ''));
                $optionA = trim((string)($row['D'] ?? ''));
                $optionB = trim((string)($row['E'] ?? ''));
                $optionC = trim((string)($row['F'] ?? ''));
                $optionD = trim((string)($row['G'] ?? ''));
                $optionE = trim((string)($row['H'] ?? ''));
                $correctRaw = trim((string)($row['I'] ?? ''));
                $scoreWeightRaw = trim((string)($row['J'] ?? ''));

                if (empty($questionText)) {
                    continue; // Skip empty row
                }

                $type = in_array($typeRaw, ['pg', 'pg_kompleks', 'benar_salah', 'menjodohkan', 'essay']) ? $typeRaw : 'pg';
                $scoreWeight = is_numeric($scoreWeightRaw) ? max(1, (int)$scoreWeightRaw) : 10;

                $data = [
                    'exam_id' => $exam->id,
                    'type' => $type,
                    'question_text' => $questionText,
                    'score_weight' => $scoreWeight,
                    'option_a' => $optionA ?: null,
                    'option_b' => $optionB ?: null,
                    'option_c' => $optionC ?: null,
                    'option_d' => $optionD ?: null,
                    'option_e' => $optionE ?: null,
                    'options_json' => null,
                    'correct_answer' => 'a',
                    'correct_answer_json' => null,
                ];

                if ($type === 'pg') {
                    $data['correct_answer'] = strtolower($correctRaw) ?: 'a';
                } elseif ($type === 'pg_kompleks') {
                    $keys = array_values(array_filter(array_map('trim', explode(',', strtolower($correctRaw)))));
                    $data['correct_answer_json'] = $keys;
                    $data['correct_answer'] = implode(',', $keys);
                } elseif ($type === 'benar_salah') {
                    $ans = strtolower($correctRaw) === 'salah' ? 'salah' : 'benar';
                    $data['correct_answer'] = $ans;
                } elseif ($type === 'essay') {
                    $data['correct_answer'] = $correctRaw ?: 'essay';
                    $data['options_json'] = ['sample_answer' => $correctRaw];
                }

                Question::create($data);
                $successCount++;
            }

            if ($successCount > 0) {
                return back()->with('success', "Berhasil mengimpor {$successCount} soal ke dalam bank soal ujian ini!");
            } else {
                return back()->with('error', 'Tidak ada data soal valid yang dapat diimpor dari file Excel.');
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal membaca file Excel: ' . $e->getMessage());
        }
    }
}
