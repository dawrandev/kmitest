@extends('layouts.admin.main')

@section('title', 'Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
            <p class="mb-0 text-muted">Welcome back! Here's what's happening with your test system.</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" onclick="refreshDashboard()">
                <i data-feather="refresh-cw" class="me-1"></i>
                Refresh
            </button>
            <div class="dropdown">
                <button class="btn btn-primary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i data-feather="download" class="me-1"></i>
                    Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportStats('excel')">Excel Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportStats('pdf')">PDF Report</a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportStats('csv')">CSV Data</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Main Statistics Cards -->
    <div class="row mb-4">
        @foreach($data['overview']['main_stats'] as $stat)
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-0 shadow-sm h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-{{ $stat['color'] }} text-uppercase mb-1">
                                {{ $stat['title'] }}
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stat['value'] }}
                            </div>
                            <div class="text-xs text-muted mt-1">{{ $stat['subtitle'] }}</div>
                        </div>
                        <div class="col-auto">
                            <div class="icon-shape bg-{{ $stat['color'] }} text-white rounded-circle">
                                <i data-feather="{{ $stat['icon'] }}" style="width: 20px; height: 20px;"></i>
                            </div>
                        </div>
                    </div>
                    @if($stat['growth'] != 0)
                    <div class="mt-2">
                        <span class="badge bg-{{ $stat['growth'] > 0 ? 'success' : 'danger' }} bg-opacity-10 text-{{ $stat['growth'] > 0 ? 'success' : 'danger' }}">
                            <i data-feather="{{ $stat['growth'] > 0 ? 'trending-up' : 'trending-down' }}" style="width: 12px; height: 12px;"></i>
                            {{ $stat['growth_text'] }}
                        </span>
                        <span class="text-muted ms-1">vs last week</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Secondary Stats Row -->
    <div class="row mb-4">
        @foreach($data['overview']['secondary_stats'] as $stat)
        <div class="col-lg-3 col-md-6 mb-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body text-center py-4">
                    <div class="icon-shape bg-{{ $stat['color'] }} bg-opacity-10 text-{{ $stat['color'] }} rounded-circle mx-auto mb-3" style="width: 50px; height: 50px;">
                        <i data-feather="{{ $stat['icon'] }}" style="width: 24px; height: 24px;"></i>
                    </div>
                    <h4 class="mb-1">{{ $stat['value'] }}</h4>
                    <p class="text-muted mb-0 small">{{ $stat['title'] }}</p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Daily Activity Chart -->
        <div class="col-xl-8 col-lg-7">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Test Activity (Last 30 Days)</h6>
                    <div class="dropdown">
                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                            Options
                        </button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#" onclick="changeChartPeriod('daily', 7)">Last 7 days</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeChartPeriod('daily', 30)">Last 30 days</a></li>
                            <li><a class="dropdown-item" href="#" onclick="changeChartPeriod('daily', 90)">Last 90 days</a></li>
                        </ul>
                    </div>
                </div>
                <div class="card-body">
                    <canvas id="dailyActivityChart" width="100%" height="40"></canvas>
                </div>
            </div>
        </div>

        <!-- Score Distribution -->
        <div class="col-xl-4 col-lg-5">
            <div class="card shadow-sm mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Score Distribution</h6>
                </div>
                <div class="card-body">
                    <canvas id="scoreDistributionChart" width="100%" height="100"></canvas>
                    <div class="mt-3">
                        <div class="row text-center">
                            <div class="col-6">
                                <div class="text-xs text-muted">Excellent</div>
                                <div class="h6 text-success">{{ $data['charts']['score_distribution']['datasets'][0]['data'][0] }}</div>
                            </div>
                            <div class="col-6">
                                <div class="text-xs text-muted">Poor</div>
                                <div class="h6 text-danger">{{ $data['charts']['score_distribution']['datasets'][0]['data'][3] }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Language Stats & Weekly Comparison -->
    <div class="row mb-4">
        <!-- Language Statistics -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Language Statistics</h6>
                </div>
                <div class="card-body">
                    <canvas id="languageStatsChart" width="100%" height="60"></canvas>
                </div>
            </div>
        </div>

        <!-- Weekly Comparison -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">This Week vs Last Week</h6>
                </div>
                <div class="card-body">
                    <canvas id="weeklyComparisonChart" width="100%" height="60"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row mb-4">
        <!-- Top Students -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Top Students</h6>
                    <a href="{{ route('admin.students.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th>Student</th>
                                    <th>Tests</th>
                                    <th>Avg Score</th>
                                    <th>Level</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['tables']['top_students'] as $student)
                                <tr>
                                    <td>{{ $student['rank'] }}</td>
                                    <td>
                                        <div class="fw-bold">{{ $student['name'] }}</div>
                                        <small class="text-muted">{{ $student['phone'] ?? 'N/A' }}</small>
                                    </td>
                                    <td>{{ $student['tests_count'] }}</td>
                                    <td>{{ $student['avg_score'] }}%</td>
                                    <td>
                                        <span class="badge bg-{{ $student['level'] == 'excellent' ? 'success' : ($student['level'] == 'good' ? 'primary' : ($student['level'] == 'average' ? 'warning' : 'danger')) }}">
                                            {{ ucfirst($student['level']) }}
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Tests -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">Recent Test Results</h6>
                    <a href="{{ route('admin.tests.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Language</th>
                                    <th>Score</th>
                                    <th>Duration</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($data['tables']['recent_tests'] as $test)
                                <tr>
                                    <td>{{ $test['student_name'] }}</td>
                                    <td>
                                        <i data-feather="globe" style="width: 14px; height: 14px;"></i>
                                        {{ $test['language'] }}
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $test['level'] == 'excellent' ? 'success' : ($test['level'] == 'good' ? 'primary' : ($test['level'] == 'average' ? 'warning' : 'danger')) }}">
                                            {{ $test['score'] }}%
                                        </span>
                                    </td>
                                    <td>{{ $test['duration'] }}</td>
                                    <td class="text-muted small">{{ $test['finished_at'] }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Alerts & Quick Actions -->
    <div class="row">
        <!-- System Alerts -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">System Alerts</h6>
                </div>
                <div class="card-body">
                    @if(count($data['widgets']['alerts']) > 0)
                    @foreach($data['widgets']['alerts'] as $alert)
                    <div class="alert alert-{{ $alert['type'] }} alert-dismissible fade show" role="alert">
                        <strong>{{ $alert['title'] }}</strong>
                        <p class="mb-2">{{ $alert['message'] }}</p>
                        <a href="{{ $alert['url'] }}" class="btn btn-sm btn-{{ $alert['type'] }}">{{ $alert['action'] }}</a>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    @endforeach
                    @else
                    <div class="text-center text-muted py-4">
                        <i data-feather="check-circle" class="mb-2" style="width: 48px; height: 48px;"></i>
                        <p>No alerts at the moment. System is running smoothly!</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="col-xl-6">
            <div class="card shadow-sm">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">Quick Actions</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($data['widgets']['quick_actions'] as $action)
                        <div class="col-md-6 mb-3">
                            <a href="{{ $action['url'] }}" class="btn btn-outline-{{ $action['color'] }} btn-block text-start">
                                <i data-feather="{{ $action['icon'] }}" class="me-2"></i>
                                {{ $action['title'] }}
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Chart data from backend
    const chartData = @json($data['charts']);

    // Initialize Charts
    let dailyActivityChart, scoreDistributionChart, languageStatsChart, weeklyComparisonChart;

    document.addEventListener('DOMContentLoaded', function() {
        initializeCharts();

        // Auto-refresh every 5 minutes
        setInterval(refreshDashboard, 5 * 60 * 1000);
    });

    function initializeCharts() {
        // Daily Activity Chart
        const dailyCtx = document.getElementById('dailyActivityChart').getContext('2d');
        dailyActivityChart = new Chart(dailyCtx, {
            type: 'line',
            data: chartData.daily_activity,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Score Distribution Chart
        const scoreCtx = document.getElementById('scoreDistributionChart').getContext('2d');
        scoreDistributionChart = new Chart(scoreCtx, {
            type: 'doughnut',
            data: chartData.score_distribution,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                    }
                }
            }
        });

        // Language Stats Chart
        const langCtx = document.getElementById('languageStatsChart').getContext('2d');
        languageStatsChart = new Chart(langCtx, {
            type: 'bar',
            data: chartData.language_stats,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        type: 'linear',
                        display: true,
                        position: 'left',
                    },
                    y1: {
                        type: 'linear',
                        display: true,
                        position: 'right',
                        grid: {
                            drawOnChartArea: false,
                        },
                    }
                }
            }
        });

        // Weekly Comparison Chart
        const weeklyCtx = document.getElementById('weeklyComparisonChart').getContext('2d');
        weeklyComparisonChart = new Chart(weeklyCtx, {
            type: 'bar',
            data: chartData.weekly_comparison,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    }

    // Functions
    function refreshDashboard() {
        location.reload();
    }

    function exportStats(format) {
        fetch(`{{ route('admin.dashboard.export') }}?format=${format}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Export started successfully!');
                } else {
                    alert('Export failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Export error:', error);
                alert('Export failed!');
            });
    }

    function changeChartPeriod(type, days) {
        // Chart period o'zgartirish logikasi
        console.log(`Changing ${type} chart to ${days} days`);
    }

    // Initialize feather icons
    if (typeof feather !== 'undefined') {
        feather.replace();
    }
</script>

<style>
    .icon-shape {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 3rem;
        height: 3rem;
    }

    .card {
        transition: all 0.3s ease;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15) !important;
    }

    .table th {
        border-top: none;
        font-weight: 600;
        font-size: 0.85rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-block {
        width: 100%;
        justify-content: flex-start;
        padding: 0.75rem 1rem;
    }

    @media (max-width: 768px) {
        .card-body canvas {
            max-height: 300px;
        }
    }
</style>

@endsection