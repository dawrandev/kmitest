<?php

namespace App\Repositories\Admin;

use App\Models\User;
use App\Models\Student;
use App\Models\Question;
use App\Models\TestSession;
use App\Models\Language;
use App\Models\TestSessionAnswer;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DashboardRepository
{
    /**
     * Umumiy statistikalarni olish
     */
    public function getOverallStats()
    {
        return [
            'total_students' => Student::count(),
            'total_questions' => Question::count(),
            'total_tests' => TestSession::whereNotNull('finished_at')->count(),
            'total_languages' => Language::count(),
            'active_tests' => TestSession::whereNull('finished_at')->count(),
            'total_users' => User::count(),
        ];
    }

    /**
     * Bu oydagi statistikalar
     */
    public function getCurrentMonthStats()
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        return [
            'new_students' => Student::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count(),
            'completed_tests' => TestSession::whereNotNull('finished_at')
                ->whereBetween('finished_at', [$startOfMonth, $endOfMonth])
                ->count(),
            'questions_answered' => TestSessionAnswer::whereNotNull('answer_id')
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count(),
        ];
    }

    /**
     * So'nggi 30 kun davomidagi kunlik test faolligi
     */
    public function getDailyTestActivity($days = 30)
    {
        $startDate = Carbon::now()->subDays($days)->startOfDay();

        return TestSession::select(
            DB::raw('DATE(finished_at) as date'),
            DB::raw('COUNT(*) as completed_tests'),
            DB::raw('COUNT(DISTINCT student_id) as active_students')
        )
            ->whereNotNull('finished_at')
            ->where('finished_at', '>=', $startDate)
            ->groupBy(DB::raw('DATE(finished_at)'))
            ->orderBy('date')
            ->get();
    }

    /**
     * Tillar bo'yicha test statistikasi
     */
    public function getLanguageStats()
    {
        return Language::select('languages.*')
            ->withCount([
                'testSessions as total_tests' => function ($query) {
                    $query->whereNotNull('finished_at');
                },
                'testSessions as active_tests' => function ($query) {
                    $query->whereNull('finished_at');
                }
            ])
            ->withAvg([
                'testSessions as avg_score' => function ($query) {
                    $query->whereNotNull('finished_at')
                        ->select(DB::raw('CASE WHEN total_questions > 0 THEN (correct_answers * 100.0 / total_questions) ELSE 0 END'));
                }
            ])
            ->get();
    }

    /**
     * Top studentlar (eng ko'p test yechganlar)
     */
    public function getTopStudents($limit = 10)
    {
        return Student::select('students.*')
            ->withCount([
                'testSessions as total_tests' => function ($query) {
                    $query->whereNotNull('finished_at');
                }
            ])
            ->withAvg([
                'testSessions as avg_score' => function ($query) {
                    $query->whereNotNull('finished_at')
                        ->select(DB::raw('CASE WHEN total_questions > 0 THEN (correct_answers * 100.0 / total_questions) ELSE 0 END'));
                }
            ])
            ->having('total_tests', '>', 0)
            ->orderBy('total_tests', 'desc')
            ->orderBy('avg_score', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * So'nggi test natijalar
     */
    public function getRecentTests($limit = 10)
    {
        return TestSession::with(['student', 'language'])
            ->whereNotNull('finished_at')
            ->orderBy('finished_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Haftalik growth statistikasi
     */
    public function getWeeklyGrowthStats()
    {
        $thisWeekStart = Carbon::now()->startOfWeek();
        $lastWeekStart = Carbon::now()->subWeek()->startOfWeek();
        $lastWeekEnd = Carbon::now()->subWeek()->endOfWeek();

        // Bu hafta
        $thisWeekStudents = Student::where('created_at', '>=', $thisWeekStart)->count();
        $thisWeekTests = TestSession::whereNotNull('finished_at')
            ->where('finished_at', '>=', $thisWeekStart)->count();

        // O'tgan hafta
        $lastWeekStudents = Student::whereBetween('created_at', [$lastWeekStart, $lastWeekEnd])->count();
        $lastWeekTests = TestSession::whereNotNull('finished_at')
            ->whereBetween('finished_at', [$lastWeekStart, $lastWeekEnd])->count();

        return [
            'students_growth' => $this->calculateGrowthPercentage($lastWeekStudents, $thisWeekStudents),
            'tests_growth' => $this->calculateGrowthPercentage($lastWeekTests, $thisWeekTests),
            'this_week' => [
                'students' => $thisWeekStudents,
                'tests' => $thisWeekTests,
            ],
            'last_week' => [
                'students' => $lastWeekStudents,
                'tests' => $lastWeekTests,
            ],
        ];
    }

    /**
     * Savollar bo'yicha statistika
     */
    public function getQuestionStats()
    {
        return [
            'total_questions' => Question::count(),
            'questions_with_translations' => Question::whereHas('translations')->count(),
            'most_answered_questions' => Question::select('questions.*')
                ->withCount('sessionAnswers as answer_count')
                ->orderBy('answer_count', 'desc')
                ->limit(5)
                ->get(),
            'hardest_questions' => $this->getHardestQuestions(),
        ];
    }

    /**
     * Eng qiyin savollar (eng kam to'g'ri javob berilgan)
     */
    private function getHardestQuestions($limit = 5)
    {
        return Question::select('questions.*')
            ->withCount([
                'sessionAnswers as total_answers',
                'sessionAnswers as correct_answers' => function ($query) {
                    $query->where('is_correct', true);
                }
            ])
            ->having('total_answers', '>', 10) // Kamida 10 marta javob berilgan bo'lsin
            ->get()
            ->map(function ($question) {
                $question->success_rate = $question->total_answers > 0
                    ? round(($question->correct_answers / $question->total_answers) * 100, 2)
                    : 0;
                return $question;
            })
            ->sortBy('success_rate')
            ->take($limit)
            ->values();
    }

    /**
     * Score distribution (natijalar taqsimoti)
     */
    public function getScoreDistribution()
    {
        $sessions = TestSession::whereNotNull('finished_at')
            ->where('total_questions', '>', 0)
            ->get();

        $distribution = [
            'excellent' => 0,  // 90-100%
            'good' => 0,       // 75-89%
            'average' => 0,    // 60-74%
            'poor' => 0        // 0-59%
        ];

        foreach ($sessions as $session) {
            $percentage = ($session->correct_answers / $session->total_questions) * 100;

            if ($percentage >= 90) {
                $distribution['excellent']++;
            } elseif ($percentage >= 75) {
                $distribution['good']++;
            } elseif ($percentage >= 60) {
                $distribution['average']++;
            } else {
                $distribution['poor']++;
            }
        }

        return $distribution;
    }

    /**
     * Aktiv sessiyalar ma'lumoti
     */
    public function getActiveSessionsInfo()
    {
        $activeSessions = TestSession::whereNull('finished_at')
            ->with(['student', 'language'])
            ->get();

        $timeLimit = 25 * 60; // 25 daqiqa
        $expiredSessions = $activeSessions->filter(function ($session) use ($timeLimit) {
            $timeElapsed = Carbon::now()->diffInSeconds($session->started_at);
            return $timeElapsed > $timeLimit;
        });

        return [
            'active_count' => $activeSessions->count(),
            'expired_count' => $expiredSessions->count(),
            'active_sessions' => $activeSessions->take(5), // So'nggi 5 tasi
        ];
    }

    /**
     * O'sish foizini hisoblash
     */
    private function calculateGrowthPercentage($old, $new)
    {
        if ($old == 0) {
            return $new > 0 ? 100 : 0;
        }

        return round((($new - $old) / $old) * 100, 2);
    }

    /**
     * Real-time statistikalar
     */
    public function getRealTimeStats()
    {
        $now = Carbon::now();
        $today = $now->startOfDay();
        $thisHour = $now->startOfHour();

        return [
            'today_tests' => TestSession::whereNotNull('finished_at')
                ->where('finished_at', '>=', $today)->count(),
            'this_hour_tests' => TestSession::whereNotNull('finished_at')
                ->where('finished_at', '>=', $thisHour)->count(),
            'online_students' => $this->getOnlineStudentsCount(),
            'avg_test_duration' => $this->getAverageTestDuration(),
        ];
    }

    /**
     * Online studentlar soni (so'nggi 15 daqiqada faol bo'lganlar)
     */
    private function getOnlineStudentsCount()
    {
        $fifteenMinutesAgo = Carbon::now()->subMinutes(15);

        return TestSession::where('started_at', '>=', $fifteenMinutesAgo)
            ->distinct('student_id')
            ->count('student_id');
    }

    /**
     * O'rtacha test davomiyligi
     */
    private function getAverageTestDuration()
    {
        $averageSeconds = TestSession::whereNotNull('finished_at')
            ->where('finished_at', '>=', Carbon::now()->subDays(7))
            ->get()
            ->map(function ($session) {
                return Carbon::parse($session->started_at)->diffInSeconds($session->finished_at);
            })
            ->average();

        return $averageSeconds ? gmdate('i:s', $averageSeconds) : '00:00';
    }
}
