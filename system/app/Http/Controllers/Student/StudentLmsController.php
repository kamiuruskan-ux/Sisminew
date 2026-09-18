<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\LmsChapter;
use App\Models\LmsTopic;
use App\Models\LmsTopicQuiz;
use App\Models\LmsTopicQuizAnswer;
use App\Models\LmsTopicProgress;
use App\Models\LmsGamification;
use App\Models\LmsLiveClass;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentLmsController extends Controller
{
    private function getStudent(): Student
    {
        $user = auth()->user();
        return Student::where('user_id', $user->id)->firstOrFail();
    }

    public function index(Request $request)
    {
        $student = $this->getStudent();
        $gamification = LmsGamification::forStudent($student->id);

        // Fetch chapters available for student's class (or master / null class_id)
        $query = LmsChapter::with(['topics' => function($q) use ($student) {
            $q->where('is_active', true)->with(['quizzes', 'materials', 'assignments', 'exams', 'studentProgress' => function($sp) use ($student) {
                $sp->where('student_id', $student->id);
            }]);
        }]);

        if ($student->class_id) {
            $query->where(function($q) use ($student) {
                $q->where('class_id', $student->class_id)
                  ->orWhereNull('class_id');
            });
        }

        $chapters = $query->where('is_active', true)->orderBy('subject')->orderBy('order')->get();
        $subjects = $chapters->groupBy('subject');

        // Calculate progress & stats per Chapter (Bab)
        $chapterProgress = [];
        $subjectsList = [];

        foreach ($chapters as $ch) {
            if (!in_array($ch->subject, $subjectsList)) {
                $subjectsList[] = $ch->subject;
            }

            $totalTopics = 0;
            $completedTopics = 0;
            $totalQuizzes = 0;
            $totalAssignments = 0;
            $totalMaterials = 0;

            foreach ($ch->topics as $tp) {
                $totalTopics++;
                if ($tp->studentProgress && $tp->studentProgress->is_completed) {
                    $completedTopics++;
                }
                $totalQuizzes += count($tp->quizzes) + count($tp->exams);
                $totalAssignments += count($tp->assignments);
                $totalMaterials += ($tp->summary_file ? 1 : 0) + count($tp->materials);
            }
            $percent = $totalTopics > 0 ? round(($completedTopics / $totalTopics) * 100) : 0;

            // Determine status
            $status = 'not_started';
            if ($percent == 100) {
                $status = 'completed';
            } elseif ($percent > 0) {
                $status = 'in_progress';
            }

            // Determine Category, Level & Cover Image based on subject name
            $category = 'Modul Bab';
            $level = 'Pemula';
            $coverImage = $ch->cover_image;
            $lowerName = strtolower($ch->subject);

            if (str_contains($lowerName, 'jaringan') || str_contains($lowerName, 'network') || str_contains($lowerName, 'ccna') || str_contains($lowerName, 'cisco')) {
                $category = 'Jaringan & IT';
                $level = 'Pemula';
                if (!$coverImage) $coverImage = 'img/lms/networking.jpg';
            } elseif (str_contains($lowerName, 'cyber') || str_contains($lowerName, 'keamanan') || str_contains($lowerName, 'security')) {
                $category = 'Keamanan Siber';
                $level = 'Pemula';
                if (!$coverImage) $coverImage = 'img/lms/cybersecurity.jpg';
            } elseif (str_contains($lowerName, 'python') || str_contains($lowerName, 'pemrograman') || str_contains($lowerName, 'coding') || str_contains($lowerName, 'informatika') || str_contains($lowerName, 'komputer') || str_contains($lowerName, 'web') || str_contains($lowerName, 'software')) {
                $category = 'Pemrograman';
                $level = 'Pemula';
                if (!$coverImage) $coverImage = 'img/lms/python.jpg';
            } elseif (str_contains($lowerName, 'matematika') || str_contains($lowerName, 'kalkulus') || str_contains($lowerName, 'aljabar')) {
                $category = 'Matematika';
                $level = 'Menengah';
                if (!$coverImage) $coverImage = 'img/lms/math.jpg';
            } elseif (str_contains($lowerName, 'data') || str_contains($lowerName, 'ai') || str_contains($lowerName, 'statistika')) {
                $category = 'Sains Data';
                $level = 'Menengah';
                if (!$coverImage) $coverImage = 'img/lms/math.jpg';
            } elseif (str_contains($lowerName, 'fisika') || str_contains($lowerName, 'biologi') || str_contains($lowerName, 'kimia') || str_contains($lowerName, 'ipa')) {
                $category = 'Sains & MIPA';
                $level = 'Menengah';
                if (!$coverImage) $coverImage = 'img/lms/math.jpg';
            } elseif (str_contains($lowerName, 'bahasa') || str_contains($lowerName, 'english') || str_contains($lowerName, 'inggris')) {
                $category = 'Bahasa & Sastra';
                $level = 'Pemula';
            } elseif (str_contains($lowerName, 'sejarah') || str_contains($lowerName, 'ips') || str_contains($lowerName, 'ekonomi') || str_contains($lowerName, 'geografi')) {
                $category = 'Ilmu Sosial';
                $level = 'Pemula';
            }

            $desc = $ch->description ?: "Pelajari materi pembelajaran interaktif, kuis CBT, serta pengumpulan tugas di bab ini.";
            $firstTp = $ch->topics->first();

            $chapterProgress[] = [
                'id' => $ch->id,
                'subject' => $ch->subject,
                'title' => $ch->title,
                'total' => $totalTopics,
                'completed' => $completedTopics,
                'percent' => $percent,
                'status' => $status,
                'quizzes_count' => $totalQuizzes,
                'assignments_count' => $totalAssignments,
                'materials_count' => $totalMaterials,
                'category' => $category,
                'level' => $level,
                'description' => $desc,
                'cover_image' => $coverImage,
                'first_topic_id' => $firstTp ? $firstTp->id : null,
                'created_at' => $ch->created_at ? $ch->created_at->timestamp : 0,
            ];
        }

        $liveClasses = LmsLiveClass::where(function($q) use ($student) {
            $q->where('class_id', $student->class_id)->orWhereNull('class_id');
        })->whereIn('status', ['scheduled', 'live'])->orderBy('scheduled_at', 'asc')->get();

        return view('student.lms.index', compact('student', 'gamification', 'chapters', 'chapterProgress', 'subjectsList', 'liveClasses'));
    }

    public function learn(Request $request, $chapterId, $topicId = null)
    {
        $student = $this->getStudent();
        $gamification = LmsGamification::forStudent($student->id);

        $chapter = LmsChapter::with(['topics.quizzes'])->findOrFail($chapterId);

        // Load all chapters for this subject available for student
        $allChapters = LmsChapter::with(['topics' => function($q) use ($student) {
            $q->where('is_active', true)->with(['quizzes', 'assignments', 'exams', 'studentProgress' => function($sp) use ($student) {
                $sp->where('student_id', $student->id);
            }]);
        }])
        ->where('subject', $chapter->subject)
        ->where(function($q) use ($student) {
            $q->where('class_id', $student->class_id)->orWhereNull('class_id');
        })
        ->where('is_active', true)
        ->orderBy('order', 'asc')
        ->get();

        $activeTopic = null;
        if ($topicId) {
            $activeTopic = LmsTopic::with(['quizzes' => function($qz) use ($student) {
                $qz->with(['studentAnswer' => function($sa) use ($student) {
                    $sa->where('student_id', $student->id);
                }]);
            }, 'materials', 'assignments' => function($a) use ($student) {
                $a->where(function($sq) use ($student) {
                    $sq->where('class_id', $student->class_id)->orWhereNull('class_id');
                })->with(['submissions' => function($sub) use ($student) {
                    $sub->where('student_id', $student->id);
                }]);
            }, 'exams' => function($e) use ($student) {
                $e->where('is_published', true)->where(function($sq) use ($student) {
                    $sq->where('class_id', $student->class_id)->orWhereNull('class_id');
                })->with(['results' => function($res) use ($student) {
                    $res->where('student_id', $student->id);
                }]);
            }])->find($topicId);
        }

        if (!$activeTopic) {
            $activeTopic = $chapter->topics->first();
            if ($activeTopic) {
                $activeTopic->load(['quizzes' => function($qz) use ($student) {
                    $qz->with(['studentAnswer' => function($sa) use ($student) {
                        $sa->where('student_id', $student->id);
                    }]);
                }, 'materials', 'assignments' => function($a) use ($student) {
                    $a->where(function($sq) use ($student) {
                        $sq->where('class_id', $student->class_id)->orWhereNull('class_id');
                    })->with(['submissions' => function($sub) use ($student) {
                        $sub->where('student_id', $student->id);
                    }]);
                }, 'exams' => function($e) use ($student) {
                    $e->where('is_published', true)->where(function($sq) use ($student) {
                        $sq->where('class_id', $student->class_id)->orWhereNull('class_id');
                    })->with(['results' => function($res) use ($student) {
                        $res->where('student_id', $student->id);
                    }]);
                }]);
            }
        }

        // Additional materials/assignments/exams linked to subject or chapter if topic specific is empty
        $generalMaterials = \App\Models\Material::where('subject', $chapter->subject)
            ->where(function($q) use ($student) {
                $q->where('class_id', $student->class_id)->orWhereNull('class_id');
            })
            ->where('is_published', true)
            ->get();

        $progress = null;
        if ($activeTopic) {
            $progress = LmsTopicProgress::firstOrCreate(
                ['student_id' => $student->id, 'lms_topic_id' => $activeTopic->id],
                ['is_completed' => false, 'watch_seconds' => 0]
            );
        }

        // Build Navigation (Prev Topic, Next Topic)
        $flatTopics = [];
        foreach ($allChapters as $ch) {
            foreach ($ch->topics as $tp) {
                $flatTopics[] = [
                    'chapter_id' => $ch->id,
                    'topic_id' => $tp->id,
                    'topic' => $tp,
                ];
            }
        }

        $prevTopic = null;
        $nextTopic = null;
        if ($activeTopic) {
            foreach ($flatTopics as $idx => $item) {
                if ($item['topic_id'] == $activeTopic->id) {
                    if ($idx > 0) {
                        $prevTopic = $flatTopics[$idx - 1];
                    }
                    if ($idx < count($flatTopics) - 1) {
                        $nextTopic = $flatTopics[$idx + 1];
                    }
                    break;
                }
            }
        }

        return view('student.lms.learn', compact(
            'student', 'gamification', 'chapter', 'allChapters', 'activeTopic', 'progress',
            'generalMaterials', 'prevTopic', 'nextTopic'
        ));
    }

    public function completeTopic(Request $request, LmsTopic $topic)
    {
        $student = $this->getStudent();
        
        $progress = LmsTopicProgress::firstOrCreate(
            ['student_id' => $student->id, 'lms_topic_id' => $topic->id]
        );

        $alreadyCompleted = $progress->is_completed;

        $progress->update([
            'is_completed' => true,
            'completed_at' => now(),
            'watch_seconds' => $request->input('watch_seconds', 60),
        ]);

        $awardedXp = 0;
        if (!$alreadyCompleted) {
            $gamification = LmsGamification::forStudent($student->id);
            $gamification->addXp($topic->xp_reward, "Menyelesaikan Topik: {$topic->title}");
            $awardedXp = $topic->xp_reward;
        }

        return response()->json([
            'success' => true,
            'message' => 'Topik pembelajaran berhasil diselesaikan!',
            'awarded_xp' => $awardedXp,
            'new_total_xp' => LmsGamification::forStudent($student->id)->xp,
            'new_level' => LmsGamification::forStudent($student->id)->level,
        ]);
    }

    public function submitQuiz(Request $request, LmsTopicQuiz $quiz)
    {
        $student = $this->getStudent();

        // Check if quiz already submitted by student
        $existingAnswer = LmsTopicQuizAnswer::where('student_id', $student->id)
            ->where('lms_topic_quiz_id', $quiz->id)
            ->first();

        if ($existingAnswer) {
            return response()->json([
                'already_submitted' => true,
                'is_correct' => $existingAnswer->is_correct,
                'user_option' => $existingAnswer->selected_option,
                'correct_option' => strtolower($quiz->correct_option),
                'explanation' => $quiz->explanation,
                'explanation_video' => $quiz->explanation_video,
                'awarded_xp' => 0,
                'new_total_xp' => LmsGamification::forStudent($student->id)->xp,
                'new_level' => LmsGamification::forStudent($student->id)->level,
                'message' => 'Kuis ini sudah pernah Anda jawab.',
            ]);
        }

        $userOption = strtolower(trim($request->input('option')));
        $isCorrect = ($userOption === strtolower($quiz->correct_option));

        $awardedXp = 0;
        if ($isCorrect) {
            $gamification = LmsGamification::forStudent($student->id);
            $gamification->addXp($quiz->xp_reward, "Menjawab Benar Kuis Sub-Bab");
            $awardedXp = $quiz->xp_reward;
        }

        LmsTopicQuizAnswer::create([
            'student_id' => $student->id,
            'lms_topic_quiz_id' => $quiz->id,
            'selected_option' => $userOption,
            'is_correct' => $isCorrect,
            'awarded_xp' => $awardedXp,
        ]);

        $progress = LmsTopicProgress::firstOrCreate(
            ['student_id' => $student->id, 'lms_topic_id' => $quiz->lms_topic_id],
            ['watch_seconds' => 0]
        );
        $progress->update([
            'quiz_completed' => true,
        ]);

        return response()->json([
            'already_submitted' => false,
            'is_correct' => $isCorrect,
            'user_option' => $userOption,
            'correct_option' => strtolower($quiz->correct_option),
            'explanation' => $quiz->explanation,
            'explanation_video' => $quiz->explanation_video,
            'awarded_xp' => $awardedXp,
            'new_total_xp' => LmsGamification::forStudent($student->id)->xp,
            'new_level' => LmsGamification::forStudent($student->id)->level,
        ]);
    }

    public function gamification()
    {
        $student = $this->getStudent();
        $gamification = LmsGamification::forStudent($student->id);

        $leaderboard = LmsGamification::with('student.user')
            ->orderBy('xp', 'desc')
            ->limit(10)
            ->get();

        return view('student.lms.gamification', compact('student', 'gamification', 'leaderboard'));
    }

    public function analytics()
    {
        $student = $this->getStudent();
        $gamification = LmsGamification::forStudent($student->id);

        // Build radar chart diagnostic data
        $chapters = LmsChapter::with(['topics.studentProgress' => function($q) use ($student) {
            $q->where('student_id', $student->id);
        }])->get()->groupBy('subject');

        $radarLabels = [];
        $radarData = [];

        foreach ($chapters as $subject => $chList) {
            $radarLabels[] = $subject;
            $total = 0;
            $completed = 0;
            foreach ($chList as $ch) {
                foreach ($ch->topics as $tp) {
                    $total++;
                    if ($tp->studentProgress && $tp->studentProgress->is_completed) {
                        $completed++;
                    }
                }
            }
            $score = $total > 0 ? round(($completed / $total) * 100) : 0;
            $radarData[] = $score;
        }

        return view('student.lms.analytics', compact('student', 'gamification', 'radarLabels', 'radarData'));
    }

    public function liveClass()
    {
        $student = $this->getStudent();
        $gamification = LmsGamification::forStudent($student->id);

        $liveClasses = LmsLiveClass::with(['teacher', 'class'])
            ->where(function($q) use ($student) {
                $q->where('class_id', $student->class_id)->orWhereNull('class_id');
            })
            ->orderBy('scheduled_at', 'desc')
            ->get();

        return view('student.lms.live', compact('student', 'gamification', 'liveClasses'));
    }
}
