<?php

namespace App\Services\Admin;

use App\Models\TestSession;
use App\Repositories\Admin\DashboardRepository;
use Carbon\Carbon;

class DashboardService
{
    protected $dashboardRepository;

    public function __construct(DashboardRepository $dashboardRepository)
    {
        $this->dashboardRepository = $dashboardRepository;
    }

    /**
     * Dashboard uchun barcha kerakli ma'lumotlarni tayyorlash
     */
    public function getDashboardData()
    {
        return [
            'overview' => $this->getOverviewData(),
            'charts' => $this->getChartsData(),
            'tables' => $this->getTablesData(),
            'widgets' => $this->getWidgetsData(),
        ];
    }

    /**
     * Umumiy ko'rsatkichlar (Cards)
     */
    public function getOverviewData()
    {
        $overallStats = $this->dashboardRepository->getOverallStats();
        $monthStats = $this->dashboardRepository->getCurrentMonthStats();
        $weeklyGrowth = $this->dashboardRepository->getWeeklyGrowthStats();
        $realTimeStats = $this->dashboardRepository->getRealTimeStats();

        return [
            'main_stats' => [
                [
                    'title' => 'Total Students',
                    'value' => number_format($overallStats['total_students']),
                    'growth' => $weeklyGrowth['students_growth'],
                    'growth_text' => $this->getGrowthText($weeklyGrowth['students_growth']),
                    'icon' => 'users',
                    'color' => 'primary',
                    'subtitle' => '+' . $monthStats['new_students'] . ' this month'
                ],
                [
                    'title' => 'Total Tests',
                    'value' => number_format($overallStats['total_tests']),
                    'growth' => $weeklyGrowth['tests_growth'],
                    'growth_text' => $this->getGrowthText($weeklyGrowth['tests_growth']),
                    'icon' => 'file-text',
                    'color' => 'success',
                    'subtitle' => number_format($monthStats['completed_tests']) . ' completed this month'
                ],
                [
                    'title' => 'Total Questions',
                    'value' => number_format($overallStats['total_questions']),
                    'growth' => 0, // Questions o'sishi odatda sekinroq bo'ladi
                    'growth_text' => 'stable',
                    'icon' => 'help-circle',
                    'color' => 'info',
                    'subtitle' => number_format($monthStats['questions_answered']) . ' answered this month'
                ],
                [
                    'title' => 'Active Tests',
                    'value' => number_format($overallStats['active_tests']),
                    'growth' => 0,
                    'growth_text' => 'real-time',
                    'icon' => 'activity',
                    'color' => 'warning',
                    'subtitle' => 'Currently in progress'
                ],
            ],
            'secondary_stats' => [
                [
                    'title' => 'Languages',
                    'value' => $overallStats['total_languages'],
                    'icon' => 'globe',
                    'color' => 'purple'
                ],
                [
                    'title' => 'Today Tests',
                    'value' => $realTimeStats['today_tests'],
                    'icon' => 'calendar',
                    'color' => 'indigo'
                ],
                [
                    'title' => 'Online Students',
                    'value' => $realTimeStats['online_students'],
                    'icon' => 'wifi',
                    'color' => 'green'
                ],
                [
                    'title' => 'Avg Duration',
                    'value' => $realTimeStats['avg_test_duration'],
                    'icon' => 'clock',
                    'color' => 'orange'
                ],
            ]
        ];
    }

    /**
     * Chartlar uchun ma'lumotlar
     */
    public function getChartsData()
    {
        $dailyActivity = $this->dashboardRepository->getDailyTestActivity(30);
        $scoreDistribution = $this->dashboardRepository->getScoreDistribution();
        $languageStats = $this->dashboardRepository->getLanguageStats();

        return [
            'daily_activity' => $this->formatDailyActivityChart($dailyActivity),
            'score_distribution' => $this->formatScoreDistributionChart($scoreDistribution),
            'language_stats' => $this->formatLanguageStatsChart($languageStats),
            'weekly_comparison' => $this->getWeeklyComparisonChart(),
        ];
    }

    /**
     * Jadvallar uchun ma'lumotlar
     */
    public function getTablesData()
    {
        $topStudents = $this->dashboardRepository->getTopStudents(10);
        $recentTests = $this->dashboardRepository->getRecentTests(15);
        $activeSessionsInfo = $this->dashboardRepository->getActiveSessionsInfo();
        $questionStats = $this->dashboardRepository->getQuestionStats();

        return [
            'top_students' => $this->formatTopStudentsData($topStudents),
            'recent_tests' => $this->formatRecentTestsData($recentTests),
            'active_sessions' => $activeSessionsInfo,
            'question_analytics' => $questionStats,
        ];
    }

    /**
     * Widget ma'lumotlari
     */
    public function getWidgetsData()
    {
        $questionStats = $this->dashboardRepository->getQuestionStats();

        return [
            'system_health' => $this->getSystemHealthData(),
            'quick_actions' => $this->getQuickActionsData(),
            'alerts' => $this->getSystemAlertsData(),
        ];
    }

    /**
     * Daily Activity Chart formatini tayyorlash
     */
    private function formatDailyActivityChart($dailyActivity)
    {
        $labels = [];
        $testsData = [];
        $studentsData = [];

        // So'nggi 30 kun uchun barcha kunlarni yaratamiz
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $labels[] = Carbon::parse($date)->format('M d');

            $dayData = $dailyActivity->firstWhere('date', $date);
            $testsData[] = $dayData ? $dayData->completed_tests : 0;
            $studentsData[] = $dayData ? $dayData->active_students : 0;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Completed Tests',
                    'data' => $testsData,
                    'borderColor' => '#3b82f6',
                    'backgroundColor' => 'rgba(59, 130, 246, 0.1)',
                    'tension' => 0.4
                ],
                [
                    'label' => 'Active Students',
                    'data' => $studentsData,
                    'borderColor' => '#10b981',
                    'backgroundColor' => 'rgba(16, 185, 129, 0.1)',
                    'tension' => 0.4
                ]
            ]
        ];
    }

    /**
     * Score Distribution Chart formatini tayyorlash
     */
    private function formatScoreDistributionChart($scoreDistribution)
    {
        return [
            'labels' => ['Excellent (90-100%)', 'Good (75-89%)', 'Average (60-74%)', 'Poor (0-59%)'],
            'datasets' => [
                [
                    'data' => [
                        $scoreDistribution['excellent'],
                        $scoreDistribution['good'],
                        $scoreDistribution['average'],
                        $scoreDistribution['poor']
                    ],
                    'backgroundColor' => ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                    'borderWidth' => 2,
                    'borderColor' => '#fff'
                ]
            ]
        ];
    }

    /**
     * Language Stats Chart formatini tayyorlash
     */
    private function formatLanguageStatsChart($languageStats)
    {
        $labels = $languageStats->pluck('name')->toArray();
        $testsData = $languageStats->pluck('total_tests')->toArray();
        $avgScores = $languageStats->pluck('avg_score')->map(function ($score) {
            return round($score ?? 0, 1);
        })->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Total Tests',
                    'data' => $testsData,
                    'backgroundColor' => '#3b82f6',
                    'yAxisID' => 'y'
                ],
                [
                    'label' => 'Average Score (%)',
                    'data' => $avgScores,
                    'backgroundColor' => '#10b981',
                    'type' => 'line',
                    'yAxisID' => 'y1'
                ]
            ]
        ];
    }

    /**
     * Top Students ma'lumotlarini formatlash
     */
    private function formatTopStudentsData($topStudents)
    {
        return $topStudents->map(function ($student, $index) {
            return [
                'rank' => $index + 1,
                'name' => $student->full_name,
                'phone' => $student->phone,
                'tests_count' => $student->total_tests,
                'avg_score' => round($student->avg_score ?? 0, 1),
                'level' => $this->getStudentLevel($student->avg_score ?? 0),
            ];
        });
    }

    /**
     * Recent Tests ma'lumotlarini formatlash
     */
    private function formatRecentTestsData($recentTests)
    {
        return $recentTests->map(function ($test) {
            $percentage = $test->total_questions > 0
                ? round(($test->correct_answers / $test->total_questions) * 100, 2)
                : 0;

            $duration = Carbon::parse($test->started_at)->diffInSeconds($test->finished_at);

            return [
                'id' => $test->id,
                'student_name' => $test->student->full_name,
                'language' => $test->language->name,
                'score' => $percentage,
                'duration' => gmdate('H:i:s', $duration),
                'finished_at' => Carbon::parse($test->finished_at)->format('d.m.Y H:i'),
                'level' => $this->getTestLevel($percentage),
            ];
        });
    }

    /**
     * Haftalik taqqoslash chart
     */
    private function getWeeklyComparisonChart()
    {
        $thisWeek = [];
        $lastWeek = [];
        $labels = ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'];

        for ($i = 0; $i < 7; $i++) {
            // Bu hafta
            $thisWeekDay = Carbon::now()->startOfWeek()->addDays($i);
            $thisWeekTests = TestSession::whereNotNull('finished_at')
                ->whereDate('finished_at', $thisWeekDay->format('Y-m-d'))
                ->count();
            $thisWeek[] = $thisWeekTests;

            // O'tgan hafta
            $lastWeekDay = Carbon::now()->subWeek()->startOfWeek()->addDays($i);
            $lastWeekTests = TestSession::whereNotNull('finished_at')
                ->whereDate('finished_at', $lastWeekDay->format('Y-m-d'))
                ->count();
            $lastWeek[] = $lastWeekTests;
        }

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'This Week',
                    'data' => $thisWeek,
                    'backgroundColor' => '#3b82f6',
                ],
                [
                    'label' => 'Last Week',
                    'data' => $lastWeek,
                    'backgroundColor' => '#9ca3af',
                ]
            ]
        ];
    }

    /**
     * Tizim salomatligi ma'lumotlari
     */
    private function getSystemHealthData()
    {
        $activeSessionsInfo = $this->dashboardRepository->getActiveSessionsInfo();

        return [
            'database_status' => 'healthy',
            'active_sessions' => $activeSessionsInfo['active_count'],
            'expired_sessions' => $activeSessionsInfo['expired_count'],
            'system_load' => 'normal',
            'last_backup' => Carbon::now()->subHours(6)->format('d.m.Y H:i'),
        ];
    }

    /**
     * Tezkor harakatlar ma'lumotlari
     */
    private function getQuickActionsData()
    {
        return [
            [
                'title' => 'Add New Question',
                'icon' => 'plus-circle',
                'url' => route('admin.questions.create'),
                'color' => 'primary'
            ],
            [
                'title' => 'View All Students',
                'icon' => 'users',
                'url' => route('admin.students.index'),
                'color' => 'success'
            ],
            [
                'title' => 'System Settings',
                'icon' => 'settings',
                'url' => route('admin.settings'),
                'color' => 'secondary'
            ],
            [
                'title' => 'Export Reports',
                'icon' => 'download',
                'url' => route('admin.reports.export'),
                'color' => 'info'
            ],
        ];
    }

    /**
     * Tizim ogohlantirishlari
     */
    private function getSystemAlertsData()
    {
        $alerts = [];
        $activeSessionsInfo = $this->dashboardRepository->getActiveSessionsInfo();

        if ($activeSessionsInfo['expired_count'] > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Expired Test Sessions',
                'message' => $activeSessionsInfo['expired_count'] . ' test sessions have expired and need attention.',
                'action' => 'View Details',
                'url' => route('admin.tests.expired')
            ];
        }

        $questionStats = $this->dashboardRepository->getQuestionStats();
        if ($questionStats['total_questions'] < 50) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Low Question Count',
                'message' => 'Consider adding more questions to improve test variety.',
                'action' => 'Add Questions',
                'url' => route('admin.questions.create')
            ];
        }

        return $alerts;
    }

    /**
     * O'sish matni
     */
    private function getGrowthText($growth)
    {
        if ($growth > 0) {
            return "+" . number_format($growth, 1) . "%";
        } elseif ($growth < 0) {
            return number_format($growth, 1) . "%";
        } else {
            return "0%";
        }
    }

    /**
     * Student darajasini aniqlash
     */
    private function getStudentLevel($avgScore)
    {
        if ($avgScore >= 90) return 'excellent';
        if ($avgScore >= 75) return 'good';
        if ($avgScore >= 60) return 'average';
        return 'poor';
    }

    /**
     * Test darajasini aniqlash
     */
    private function getTestLevel($score)
    {
        if ($score >= 90) return 'excellent';
        if ($score >= 75) return 'good';
        if ($score >= 60) return 'average';
        return 'poor';
    }
}
