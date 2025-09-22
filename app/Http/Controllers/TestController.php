<?php

namespace App\Http\Controllers;

use App\Models\Question;
use App\Models\Language;
use App\Models\TestSession;
use App\Models\TestSessionAnswer;
use App\Models\Answer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class TestController extends Controller
{

    public function index()
    {
        $languages = Language::all();

        return view('pages.student.home', compact('languages'));
    }

    public function startTest(Request $request)
    {
        try {
            $request->validate([
                'language_id' => 'required|exists:languages,id'
            ]);

            $languageId = $request->language_id;
            $language = Language::findOrFail($languageId);

            $activeSession = TestSession::where('student_id', Auth::user()->student->id)
                ->whereNull('finished_at')
                ->first();

            if ($activeSession) {
                return response()->json([
                    'success' => false,
                    'message' => __('You have an active test session. Please finish it first.')
                ], 400);
            }

            $questions = $this->getRandomQuestions($languageId, 20);

            if ($questions->count() < 1) {
                return response()->json([
                    'success' => false,
                    'message' => __('Not enough questions available for this language.')
                ], 400);
            }

            $student = Auth::user()->student;

            if (!$student) {
                return response()->json([
                    'success' => false,
                    'message' => __('Student profile not found.')
                ], 400);
            }

            // Test session yaratish (question_ids siz)
            $testSession = TestSession::create([
                'student_id' => $student->id,
                'language_id' => $languageId,
                'started_at' => Carbon::now(),
                'total_questions' => 20,
                'correct_answers' => 0,
                'incorrect_answers' => 0
            ]);

            // Savollarni test_session_answers jadvaliga yozish
            foreach ($questions as $question) {
                TestSessionAnswer::create([
                    'test_session_id' => $testSession->id,
                    'question_id' => $question->id,
                    'answer_id' => null, // hali javob berilmagan
                    'is_correct' => null
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => __('Test started successfully'),
                'redirect_url' => route('student.test.show', $testSession->id)
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ], 500);
        }
    }

    /**
     * Test sahifasini ko'rsatish
     */
    public function showTest($sessionId)
    {
        $student = Auth::user()->student;
        $testSession = TestSession::where('id', $sessionId)
            ->where('student_id', $student->id)
            ->whereNull('finished_at')
            ->firstOrFail();

        // Test vaqti tugagan-yo'qligini tekshirish (25 daqiqa)
        $timeLimit = 25 * 60; // 25 daqiqa (sekund)
        $timeElapsed = Carbon::now()->diffInSeconds($testSession->started_at);

        if ($timeElapsed > $timeLimit) {
            // Vaqt tugagan bo'lsa avtomatik yakunlash
            $this->autoFinishTest($testSession);
            return redirect()->route('student.test.result', $sessionId)
                ->with('warning', __('Test time has expired.'));
        }

        $language = Language::findOrFail($testSession->language_id);

        // Test_session_answers dan savollarni olish
        $sessionAnswers = $testSession->sessionAnswers()
            ->with([
                'question.translations' => function ($query) use ($language) {
                    $query->where('language_id', $language->id);
                },
                'question.answers.translations' => function ($query) use ($language) {
                    $query->where('language_id', $language->id);
                }
            ])
            ->orderBy('id') // yoki created_at bo'yicha
            ->get();

        // MUHIM TUZATISH: Savollar ro'yxatini to'g'ri olish
        $questions = collect();
        foreach ($sessionAnswers as $sessionAnswer) {
            if ($sessionAnswer->question) {
                $questions->push($sessionAnswer->question);
            }
        }

        // Debug uchun
        Log::info('Questions count in controller: ' . $questions->count());
        Log::info('Questions data: ', $questions->toArray());

        // Javob berilgan savollar
        $answeredQuestions = $sessionAnswers->whereNotNull('answer_id')->pluck('question_id')->toArray();

        return view('pages.student.test', compact('testSession', 'language', 'questions', 'answeredQuestions'));
    }

    /**
     * Javob yuborish
     */
    public function submitAnswer(Request $request)
    {
        try {
            $request->validate([
                'session_id' => 'required|exists:test_sessions,id',
                'question_id' => 'required|exists:questions,id',
                'answer_id' => 'required|exists:answers,id'
            ]);

            $testSession = TestSession::where('id', $request->session_id)
                ->where('student_id', Auth::user()->student->id)
                ->whereNull('finished_at')
                ->firstOrFail();

            // Vaqt tugagan-yo'qligini tekshirish
            $timeLimit = 25 * 60; // 25 daqiqa
            $timeElapsed = Carbon::now()->diffInSeconds($testSession->started_at);

            if ($timeElapsed > $timeLimit) {
                return response()->json([
                    'success' => false,
                    'message' => __('Test time has expired.')
                ], 400);
            }

            // Javob to'g'ri-yo'qligini tekshirish
            $answer = Answer::findOrFail($request->answer_id);
            $isCorrect = $answer->is_correct;

            // Test_session_answers da mavjud yozuvni yangilash
            $sessionAnswer = TestSessionAnswer::where('test_session_id', $testSession->id)
                ->where('question_id', $request->question_id)
                ->firstOrFail();

            $sessionAnswer->update([
                'answer_id' => $request->answer_id,
                'is_correct' => $isCorrect
            ]);

            // Test session statistikasini yangilash
            $this->updateTestSessionStats($testSession);

            return response()->json([
                'success' => true,
                'is_correct' => $isCorrect,
                'message' => $isCorrect ? __('Correct answer!') : __('Incorrect answer!')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while submitting the answer.')
            ], 500);
        }
    }

    /**
     * Testni yakunlash
     */
    public function finishTest($sessionId)
    {
        try {
            // Debug qo'shish
            Log::info("=== FINISH TEST DEBUG ===");
            Log::info("Session ID: " . $sessionId);
            Log::info("User ID: " . Auth::id());
            Log::info("Student ID: " . (Auth::user()->student ? Auth::user()->student->id : 'null'));

            $testSession = TestSession::where('id', $sessionId)
                ->where('student_id', Auth::user()->student->id)
                ->whereNull('finished_at')
                ->first();

            if (!$testSession) {
                Log::error("Test session topilmadi yoki allaqachon yakunlangan");
                return response()->json([
                    'success' => false,
                    'message' => __('Test session topilmadi yoki allaqachon yakunlangan')
                ], 404);
            }

            Log::info("Test session topildi: " . $testSession->id);

            // Testni yakunlash
            $updated = $testSession->update([
                'finished_at' => Carbon::now()
            ]);

            Log::info("Test session update result: " . ($updated ? 'success' : 'failed'));

            // Oxirgi marta statistikani yangilash
            $this->updateTestSessionStats($testSession);

            // Refresh qilib updated ma'lumotlarni olish
            $testSession->refresh();

            Log::info("Final stats - Correct: " . $testSession->correct_answers .
                ", Incorrect: " . $testSession->incorrect_answers .
                ", Finished at: " . $testSession->finished_at);

            return response()->json([
                'success' => true,
                'message' => __('Test finished successfully'),
                'redirect_url' => route('student.test.result', $sessionId)
            ]);
        } catch (\Exception $e) {
            Log::error("Finish test error: " . $e->getMessage());
            Log::error("Stack trace: " . $e->getTraceAsString());

            return response()->json([
                'success' => false,
                'message' => __('An error occurred while finishing the test: ') . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Test natijasini ko'rsatish
     */
    public function showResult($sessionId)
    {
        $student = Auth::user()->student;

        $testSession = TestSession::where('id', $sessionId)
            ->where('student_id', $student->id)
            ->whereNotNull('finished_at')
            ->with([
                'sessionAnswers.question.translations',
                'sessionAnswers.answer.translations',
                'language'
            ])
            ->firstOrFail();

        $language = $testSession->language;

        // Natijalarni hisoblash
        $totalQuestions = $testSession->total_questions;
        $correctAnswers = $testSession->correct_answers;
        $incorrectAnswers = $testSession->incorrect_answers;
        $unansweredQuestions = $totalQuestions - ($correctAnswers + $incorrectAnswers);

        // Foiz hisobini chiqarish
        $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;

        // Daraja aniqlash
        $level = $this->determineLevel($percentage);

        // Test davomiyligini hisoblash
        $duration = Carbon::parse($testSession->started_at)->diffInSeconds($testSession->finished_at);
        $durationFormatted = gmdate('H:i:s', $duration);

        return view('pages.student.result', compact(
            'testSession',
            'language',
            'totalQuestions',
            'correctAnswers',
            'incorrectAnswers',
            'unansweredQuestions',
            'percentage',
            'level',
            'durationFormatted'
        ));
    }

    /**
     * Test natijalarini PDF formatda yuklab olish
     */
    public function downloadResultPdf($sessionId)
    {
        $student = Auth::user()->student;

        $testSession = TestSession::where('id', $sessionId)
            ->where('student_id', $student->id)
            ->whereNotNull('finished_at')
            ->with([
                'sessionAnswers.question.translations',
                'sessionAnswers.answer.translations',
                'language'
            ])
            ->firstOrFail();

        $language = $testSession->language;

        // Natijalarni hisoblash
        $totalQuestions = $testSession->total_questions;
        $correctAnswers = $testSession->correct_answers;
        $incorrectAnswers = $testSession->incorrect_answers;
        $unansweredQuestions = $totalQuestions - ($correctAnswers + $incorrectAnswers);

        $percentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100, 2) : 0;
        $level = $this->determineLevel($percentage);

        $duration = Carbon::parse($testSession->started_at)->diffInSeconds($testSession->finished_at);
        $durationFormatted = gmdate('H:i:s', $duration);

        // PDF yaratish uchun view
        $pdf = app('dompdf.wrapper');
        $pdf->loadView('pages.student.result-pdf', compact(
            'testSession',
            'language',
            'totalQuestions',
            'correctAnswers',
            'incorrectAnswers',
            'unansweredQuestions',
            'percentage',
            'level',
            'durationFormatted'
        ));

        $fileName = 'test_result_' . $testSession->id . '_' . date('Y-m-d') . '.pdf';

        return $pdf->download($fileName);
    }


    /**
     * Random savollarni tanlash
     */
    private function getRandomQuestions($languageId, $count = 20)
    {
        return Question::whereHas('translations', function ($query) use ($languageId) {
            $query->where('language_id', $languageId);
        })
            ->whereHas('answers.translations', function ($query) use ($languageId) {
                $query->where('language_id', $languageId);
            })
            ->with([
                'translations' => function ($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                },
                'answers.translations' => function ($query) use ($languageId) {
                    $query->where('language_id', $languageId);
                }
            ])
            ->inRandomOrder()
            ->limit($count)
            ->get();
    }

    /**
     * Test session statistikasini yangilash
     */
    private function updateTestSessionStats(TestSession $testSession)
    {
        try {
            Log::info("=== UPDATE STATS DEBUG ===");
            Log::info("Test session ID: " . $testSession->id);

            $correctCount = TestSessionAnswer::where('test_session_id', $testSession->id)
                ->where('is_correct', true)
                ->count();

            $incorrectCount = TestSessionAnswer::where('test_session_id', $testSession->id)
                ->where('is_correct', false)
                ->count();

            Log::info("Calculated stats - Correct: $correctCount, Incorrect: $incorrectCount");

            // Test session answers debug
            $allAnswers = TestSessionAnswer::where('test_session_id', $testSession->id)->get();
            Log::info("Total session answers: " . $allAnswers->count());

            foreach ($allAnswers as $answer) {
                Log::info("Answer ID: {$answer->id}, Question: {$answer->question_id}, " .
                    "Answer: {$answer->answer_id}, Is Correct: " . ($answer->is_correct ?? 'null'));
            }

            $updated = $testSession->update([
                'correct_answers' => $correctCount,
                'incorrect_answers' => $incorrectCount
            ]);

            Log::info("Stats update result: " . ($updated ? 'success' : 'failed'));
        } catch (\Exception $e) {
            Log::error("Update stats error: " . $e->getMessage());
        }
    }
    /**
     * Vaqt tugaganda avtomatik yakunlash
     */
    private function autoFinishTest(TestSession $testSession)
    {
        $testSession->update([
            'finished_at' => Carbon::now()
        ]);

        $this->updateTestSessionStats($testSession);
    }

    /**
     * Natija bo'yicha darajani aniqlash
     */
    private function determineLevel($percentage)
    {
        if ($percentage >= 90) {
            return ['name' => __('Excellent'), 'class' => 'success', 'icon' => 'award'];
        } elseif ($percentage >= 75) {
            return ['name' => __('Good'), 'class' => 'primary', 'icon' => 'thumbs-up'];
        } elseif ($percentage >= 60) {
            return ['name' => __('Average'), 'class' => 'warning', 'icon' => 'meh'];
        } else {
            return ['name' => __('Needs Improvement'), 'class' => 'danger', 'icon' => 'thumbs-down'];
        }
    }




    public function results(Request $request)
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('student.home')->with('error', __('Student profile not found.'));
        }

        // Filter parametrlarini olish
        $languageId = $request->get('language_id');
        $dateFrom = $request->get('date_from');
        $dateTo = $request->get('date_to');

        // Base query
        $query = TestSession::where('student_id', $student->id)
            ->whereNotNull('finished_at')
            ->with(['language']);

        // Language filter
        if ($languageId) {
            $query->where('language_id', $languageId);
        }

        // Date range filter
        if ($dateFrom) {
            $query->whereDate('finished_at', '>=', $dateFrom);
        }

        if ($dateTo) {
            $query->whereDate('finished_at', '<=', $dateTo);
        }

        // Test sessionlarini olish (yangilaridan eskisigacha)
        $testSessions = $query->orderBy('finished_at', 'desc')->paginate(10);

        // Har bir session uchun level va duration hisoblash
        $testSessions->getCollection()->transform(function ($session) {
            $percentage = $session->total_questions > 0
                ? round(($session->correct_answers / $session->total_questions) * 100, 2)
                : 0;

            $session->percentage = $percentage;
            $session->level = $this->determineLevel($percentage);

            $duration = Carbon::parse($session->started_at)->diffInSeconds($session->finished_at);
            $session->duration_formatted = gmdate('H:i:s', $duration);

            return $session;
        });

        // Statistikalarni hisoblash
        $allSessions = TestSession::where('student_id', $student->id)
            ->whereNotNull('finished_at')
            ->get();

        $averageScore = 0;
        $bestScore = 0;
        $languageCount = 0;

        if ($allSessions->count() > 0) {
            $totalPercentage = 0;
            $scores = [];

            foreach ($allSessions as $session) {
                $percentage = $session->total_questions > 0
                    ? ($session->correct_answers / $session->total_questions) * 100
                    : 0;
                $totalPercentage += $percentage;
                $scores[] = $percentage;
            }

            $averageScore = $totalPercentage / $allSessions->count();
            $bestScore = max($scores);

            // Nechta turli tilda test yechganligini hisoblash
            $languageCount = $allSessions->pluck('language_id')->unique()->count();
        }

        // Filter uchun barcha tillarni olish
        $languages = Language::all();

        return view('pages.student.results', compact(
            'testSessions',
            'languages',
            'averageScore',
            'bestScore',
            'languageCount'
        ));
    }


    public function deleteResult($sessionId)
    {
        try {
            $student = Auth::user()->student;

            $testSession = TestSession::where('id', $sessionId)
                ->where('student_id', $student->id)
                ->firstOrFail();

            // Session answers o'chirish
            TestSessionAnswer::where('test_session_id', $testSession->id)->delete();

            // Test session o'chirish
            $testSession->delete();

            return response()->json([
                'success' => true,
                'message' => __('Test result deleted successfully.')
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => __('An error occurred while deleting the test result.')
            ], 500);
        }
    }

    /**
     * Test statistikalarini dashboard uchun olish
     */
    public function getTestStatistics()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return response()->json(['error' => 'Student not found'], 404);
        }

        $testSessions = TestSession::where('student_id', $student->id)
            ->whereNotNull('finished_at')
            ->with(['language'])
            ->get();

        $stats = [
            'total_tests' => $testSessions->count(),
            'average_score' => 0,
            'best_score' => 0,
            'languages_count' => 0,
            'recent_tests' => [],
            'score_distribution' => [
                'excellent' => 0,  // 90%+
                'good' => 0,       // 75-89%
                'average' => 0,    // 60-74%
                'poor' => 0        // <60%
            ]
        ];

        if ($testSessions->count() > 0) {
            $totalPercentage = 0;
            $scores = [];

            foreach ($testSessions as $session) {
                $percentage = $session->total_questions > 0
                    ? ($session->correct_answers / $session->total_questions) * 100
                    : 0;
                $totalPercentage += $percentage;
                $scores[] = $percentage;

                // Score distribution
                if ($percentage >= 90) {
                    $stats['score_distribution']['excellent']++;
                } elseif ($percentage >= 75) {
                    $stats['score_distribution']['good']++;
                } elseif ($percentage >= 60) {
                    $stats['score_distribution']['average']++;
                } else {
                    $stats['score_distribution']['poor']++;
                }
            }

            $stats['average_score'] = round($totalPercentage / $testSessions->count(), 2);
            $stats['best_score'] = round(max($scores), 2);
            $stats['languages_count'] = $testSessions->pluck('language_id')->unique()->count();

            // So'nggi 5 ta test
            $stats['recent_tests'] = $testSessions->sortByDesc('finished_at')
                ->take(5)
                ->map(function ($session) {
                    $percentage = $session->total_questions > 0
                        ? round(($session->correct_answers / $session->total_questions) * 100, 2)
                        : 0;
                    return [
                        'id' => $session->id,
                        'language' => $session->language->name,
                        'score' => $percentage,
                        'date' => $session->finished_at->format('d.m.Y H:i')
                    ];
                })
                ->values();
        }

        return response()->json($stats);
    }
}
