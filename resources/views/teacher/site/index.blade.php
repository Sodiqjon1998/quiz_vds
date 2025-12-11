@extends('teacher.layouts.main')

@section('content')
<style>
    :root {
        --primary: #F58025;
        --secondary: #8B4513;
        --success: #16A085;
        --danger: #E74C3C;
        --warning: #F39C12;
        --info: #3498DB;
        --dark: #212529;
        --light: #f8f9fa;
    }

    /* Global Styles */
    body {
        background: #f5f7fa;
        font-family: 'Inter', sans-serif;
    }

    /* Stats Cards */
    .stats-card {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid #e8ecef;
        transition: all 0.3s ease;
        height: 100%;
    }

    .stats-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.08);
    }

    .stats-icon {
        width: 56px;
        height: 56px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 16px;
    }

    .stats-value {
        font-size: 2.25rem;
        font-weight: 800;
        color: var(--dark);
        line-height: 1;
        margin-bottom: 8px;
    }

    .stats-label {
        color: #6c757d;
        font-size: 0.875rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .stats-trend {
        display: inline-flex;
        align-items: center;
        gap: 4px;
        font-size: 0.875rem;
        font-weight: 600;
        margin-top: 8px;
        padding: 4px 12px;
        border-radius: 8px;
    }

    .trend-up {
        background: #d4edda;
        color: #155724;
    }

    .trend-down {
        background: #f8d7da;
        color: #721c24;
    }

    /* Chart Card */
    .chart-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid #e8ecef;
        overflow: hidden;
        height: 100%;
    }

    .chart-header {
        padding: 20px 24px;
        border-bottom: 1px solid #e8ecef;
        background: linear-gradient(135deg, #fafbfc 0%, #ffffff 100%);
    }

    .chart-title {
        font-size: 1.125rem;
        font-weight: 700;
        color: var(--dark);
        margin: 0;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .chart-subtitle {
        font-size: 0.875rem;
        color: #6c757d;
        margin-top: 4px;
    }

    .chart-body {
        padding: 24px;
    }

    /* Filter Section */
    .filter-section {
        background: white;
        border-radius: 16px;
        padding: 24px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        border: 1px solid #e8ecef;
        margin-bottom: 24px;
    }

    .filter-title {
        font-size: 1rem;
        font-weight: 700;
        color: var(--dark);
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 8px;
    }

    .form-control,
    .form-select {
        border: 2px solid #e8ecef;
        border-radius: 10px;
        padding: 10px 16px;
        font-size: 0.9rem;
        transition: all 0.3s;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: var(--primary);
        box-shadow: 0 0 0 4px rgba(245, 128, 37, 0.1);
    }

    .btn-filter {
        background: linear-gradient(135deg, var(--primary) 0%, #ff9a56 100%);
        color: white;
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-filter:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 16px rgba(245, 128, 37, 0.3);
    }

    .btn-reset {
        background: #e8ecef;
        color: var(--dark);
        border: none;
        padding: 10px 24px;
        border-radius: 10px;
        font-weight: 600;
        transition: all 0.3s;
    }

    .btn-reset:hover {
        background: #d1d5d9;
    }

    /* Table Styles */
    .data-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0;
    }

    .data-table thead th {
        background: #f8f9fa;
        color: var(--dark);
        font-weight: 700;
        font-size: 0.875rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        padding: 16px;
        border-bottom: 2px solid #e8ecef;
    }

    .data-table tbody td {
        padding: 16px;
        border-bottom: 1px solid #f0f2f5;
        color: #495057;
        font-size: 0.9rem;
    }

    .data-table tbody tr:hover {
        background: #f8f9fa;
    }

    .rank-badge {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        font-weight: 800;
        font-size: 0.875rem;
    }

    .rank-1 {
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        color: #856404;
        box-shadow: 0 4px 12px rgba(255, 215, 0, 0.3);
    }

    .rank-2 {
        background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 100%);
        color: #495057;
        box-shadow: 0 4px 12px rgba(192, 192, 192, 0.3);
    }

    .rank-3 {
        background: linear-gradient(135deg, #cd7f32 0%, #e09b5f 100%);
        color: #fff;
        box-shadow: 0 4px 12px rgba(205, 127, 50, 0.3);
    }

    .rank-default {
        background: #e8ecef;
        color: #6c757d;
    }

    .progress-bar-custom {
        height: 8px;
        background: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
        margin-top: 8px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--primary) 0%, #ff9a56 100%);
        border-radius: 10px;
        transition: width 0.8s ease;
    }

    .badge-custom {
        padding: 6px 12px;
        border-radius: 8px;
        font-weight: 600;
        font-size: 0.75rem;
    }

    .empty-state {
        text-align: center;
        padding: 60px 20px;
        color: #6c757d;
    }

    .empty-state i {
        font-size: 4rem;
        opacity: 0.2;
        margin-bottom: 16px;
    }

    /* Responsive */
    @media (max-width: 768px) {
        .stats-value {
            font-size: 1.75rem;
        }

        .stats-icon {
            width: 48px;
            height: 48px;
            font-size: 1.25rem;
        }

        .chart-body {
            padding: 16px;
        }
    }
</style>

<div class="container-fluid px-4 py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 fw-bold text-dark mb-1">
                <i class="ri-bar-chart-box-line me-2" style="color: var(--primary);"></i>
                Statistika va Tahlil
            </h1>
            <p class="text-muted mb-0">{{ Auth::user()->subject->name ?? 'Fan' }} bo'yicha to'liq hisobot</p>
        </div>
        <button class="btn btn-filter" onclick="window.print()">
            <i class="ri-printer-line me-2"></i>Chop etish
        </button>
    </div>

    <!-- Filter Section -->
    <div class="filter-section">
        <div class="filter-title">
            <i class="ri-filter-3-line"></i>
            Filterlash
        </div>
        <form method="GET" action="{{ route('teacher') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Boshlanish sanasi</label>
                    <input type="date" name="start_date" class="form-control" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold">Tugash sanasi</label>
                    <input type="date" name="end_date" class="form-control" value="{{ $endDate }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Sinf</label>
                    <select name="class_id" class="form-select">
                        <option value="">Barcha sinflar</option>
                        @foreach($filterClasses as $class)
                        <option value="{{ $class->id }}" {{ $classId == $class->id ? 'selected' : '' }}>
                            {{ $class->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2 d-flex align-items-end gap-2">
                    <button type="submit" class="btn btn-filter flex-grow-1">
                        <i class="ri-search-line me-2"></i>Qidirish
                    </button>
                    <a href="{{ route('teacher') }}" class="btn btn-reset">
                        <i class="ri-refresh-line"></i>
                    </a>
                </div>
            </div>
        </form>
    </div>

    <!-- KPI Cards -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(245, 128, 37, 0.1); color: var(--primary);">
                    <i class="ri-file-list-3-line"></i>
                </div>
                <div class="stats-value">{{ number_format($totalExams) }}</div>
                <div class="stats-label">Jami Imtihonlar</div>
                <small class="text-muted d-block mt-2">Tanlangan davr ichida</small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(22, 160, 133, 0.1); color: var(--success);">
                    <i class="ri-percent-line"></i>
                </div>
                <div class="stats-value">{{ $averageScore }}%</div>
                <div class="stats-label">O'rtacha Natija</div>
                <div class="progress-bar-custom">
                    <div class="progress-fill" style="width: {{ $averageScore }}%"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(139, 69, 19, 0.1); color: var(--secondary);">
                    <i class="ri-user-smile-line"></i>
                </div>
                <div class="stats-value">{{ number_format($uniqueStudents) }}</div>
                <div class="stats-label">Faol O'quvchilar</div>
                <small class="text-muted d-block mt-2">Unikal ishtirokchilar</small>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stats-card">
                <div class="stats-icon" style="background: rgba(243, 156, 18, 0.1); color: var(--warning);">
                    <i class="ri-question-line"></i>
                </div>
                <div class="stats-value">{{ number_format($totalQuestions) }}</div>
                <div class="stats-label">Jami Savollar</div>
                <small class="text-muted d-block mt-2">{{ number_format($totalQuizzes) }} ta testda</small>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-4 mb-4">
        <!-- Monthly Trend -->
        <div class="col-12 col-lg-8">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="ri-line-chart-line" style="color: var(--primary);"></i>
                        Oylik Trend Tahlili
                    </h5>
                    <p class="chart-subtitle mb-0">Oxirgi 6 oy ichida imtihonlar statistikasi</p>
                </div>
                <div class="chart-body">
                    <div id="monthlyTrendChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        <!-- Class Performance -->
        <div class="col-12 col-lg-4">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="ri-pie-chart-2-line" style="color: var(--success);"></i>
                        Sinflar Performansi
                    </h5>
                    <p class="chart-subtitle mb-0">To'g'ri javoblar foizi</p>
                </div>
                <div class="chart-body">
                    <div id="classPerformanceChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Weekly Activity & Quiz Stats -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="ri-calendar-check-line" style="color: var(--info);"></i>
                        Haftalik Aktivlik
                    </h5>
                    <p class="chart-subtitle mb-0">Kun bo'yicha imtihonlar taqsimoti</p>
                </div>
                <div class="chart-body">
                    <div id="weeklyActivityChart" style="min-height: 300px;"></div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="ri-bar-chart-grouped-line" style="color: var(--warning);"></i>
                        Top 10 Testlar
                    </h5>
                    <p class="chart-subtitle mb-0">Eng ko'p topshirilgan testlar</p>
                </div>
                <div class="chart-body">
                    @if($quizStats->count() > 0)
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Test nomi</th>
                                    <th class="text-end">Topshirish</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($quizStats as $index => $quiz)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>
                                        <div class="fw-semibold">{{ $quiz->name }}</div>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge badge-custom" style="background: rgba(245, 128, 37, 0.1); color: var(--primary);">
                                            {{ $quiz->attempt_count }} marta
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="ri-inbox-line"></i>
                        <p>Ma'lumot topilmadi</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tables Row -->
    <div class="row g-4">
        <!-- Top Students -->
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="ri-star-line" style="color: var(--warning);"></i>
                        Top 10 Faol O'quvchilar
                    </h5>
                    <p class="chart-subtitle mb-0">Eng ko'p test topshirganlar</p>
                </div>
                <div class="chart-body">
                    @if($topStudents->count() > 0)
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">O'rin</th>
                                    <th>O'quvchi</th>
                                    <th>Sinf</th>
                                    <th class="text-end">Testlar</th>
                                    <th class="text-end">Aniqlik</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($topStudents as $index => $student)
                                <tr>
                                    <td>
                                        <div class="rank-badge rank-{{ min($index + 1, 3) <= 3 ? $index + 1 : 'default' }}">
                                            {{ $index + 1 }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ $student['name'] }}</div>
                                    </td>
                                    <td>
                                        <span class="badge badge-custom" style="background: #e8ecef; color: var(--dark);">
                                            {{ $student['class'] }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="fw-bold text-primary">{{ $student['exam_count'] }}</span>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge badge-custom" style="background: rgba(22, 160, 133, 0.1); color: var(--success);">
                                            {{ $student['accuracy'] }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="ri-user-search-line"></i>
                        <p>Ma'lumot topilmadi</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Difficult Questions -->
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5 class="chart-title">
                        <i class="ri-error-warning-line" style="color: var(--danger);"></i>
                        Top 10 Eng Qiyin Savollar
                    </h5>
                    <p class="chart-subtitle mb-0">Eng ko'p xato qilingan savollar</p>
                </div>
                <div class="chart-body">
                    @if($difficultQuestions->count() > 0)
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">O'rin</th>
                                    <th>Savol</th>
                                    <th class="text-end">Xato %</th>
                                    <th class="text-end">Topshirish</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($difficultQuestions as $index => $question)
                                <tr>
                                    <td>
                                        <div class="rank-badge rank-default">
                                            {{ $index + 1 }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold">{{ Str::limit($question->name, 40) }}</div>
                                        <small class="text-muted">{{ $question->quiz_name }}</small>
                                    </td>
                                    <td class="text-end">
                                        <span class="badge badge-custom" style="background: rgba(231, 76, 60, 0.1); color: var(--danger);">
                                            {{ $question->error_rate }}%
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <span class="text-muted">{{ $question->total_attempts }} marta</span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="empty-state">
                        <i class="ri-question-line"></i>
                        <p>Ma'lumot topilmadi</p>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ApexCharts -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Monthly Trend Chart
        var monthlyOptions = {
            series: [{
                name: 'Imtihonlar',
                data: @json($monthlyTrend)
            }],
            chart: {
                type: 'area',
                height: 350,
                toolbar: {
                    show: false
                },
                fontFamily: 'Inter, sans-serif'
            },
            dataLabels: {
                enabled: false
            },
            stroke: {
                curve: 'smooth',
                width: 3
            },
            colors: ['#F58025'],
            fill: {
                type: 'gradient',
                gradient: {
                    shadeIntensity: 1,
                    opacityFrom: 0.7,
                    opacityTo: 0.2
                }
            },
            xaxis: {
                categories: @json($monthLabels)
            },
            yaxis: {
                title: {
                    text: 'Imtihonlar soni'
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4
            },
            tooltip: {
                y: {
                    formatter: function(val) {
                        return val + " ta imtihon";
                    }
                }
            }
        };

        var monthlyChart = new ApexCharts(document.querySelector("#monthlyTrendChart"), monthlyOptions);
        monthlyChart.render();

        // Class Performance Chart
        var classPerformanceData = @json($classPerformance);

        if (classPerformanceData && classPerformanceData.length > 0) {
            var classOptions = {
                series: classPerformanceData.map(item => parseFloat(item.percentage)),
                labels: classPerformanceData.map(item => item.name),
                chart: {
                    type: 'donut',
                    height: 350,
                    fontFamily: 'Inter, sans-serif'
                },
                colors: ['#F58025', '#8B4513', '#16A085', '#3498DB', '#E74C3C', '#F39C12', '#9B59B6', '#1ABC9C'],
                plotOptions: {
                    pie: {
                        donut: {
                            size: '70%',
                            labels: {
                                show: true,
                                total: {
                                    show: true,
                                    label: 'O\'rtacha',
                                    fontSize: '16px',
                                    fontWeight: 700,
                                    formatter: function(w) {
                                        var sum = w.globals.seriesTotals.reduce((a, b) => a + b, 0);
                                        var avg = w.globals.seriesTotals.length > 0 ? sum / w.globals.seriesTotals.length : 0;
                                        return avg.toFixed(1) + '%';
                                    }
                                }
                            }
                        }
                    }
                },
                legend: {
                    position: 'bottom',
                    fontSize: '13px'
                },
                dataLabels: {
                    enabled: true,
                    formatter: function(val) {
                        return val.toFixed(1) + '%';
                    },
                    style: {
                        fontSize: '12px',
                        fontWeight: 'bold'
                    }
                },
                tooltip: {
                    y: {
                        formatter: function(val, opts) {
                            var classData = classPerformanceData[opts.seriesIndex];
                            return val.toFixed(1) + '% (' + classData.correct_answers + '/' + classData.total_answers + ')';
                        }
                    }
                }
            };

            var classChart = new ApexCharts(document.querySelector("#classPerformanceChart"), classOptions);
            classChart.render();
        } else {
            // Bo'sh holat
            document.querySelector("#classPerformanceChart").innerHTML = `
            <div class="empty-state">
                <i class="ri-pie-chart-2-line"></i>
                <p>Tanlangan davrda sinflar bo'yicha ma'lumot topilmadi</p>
                <small class="text-muted">Iltimos, boshqa filtr tanlab ko'ring</small>
            </div>
        `;
        }

        // Weekly Activity Chart
        var weeklyOptions = {
            series: [{
                name: 'Imtihonlar',
                data: @json(array_column($weeklyActivity, 'count'))
            }],
            chart: {
                type: 'bar',
                height: 300,
                toolbar: {
                    show: false
                }
            },
            plotOptions: {
                bar: {
                    borderRadius: 8,
                    distributed: true
                }
            },
            colors: ['#3498DB', '#16A085', '#F39C12', '#E74C3C', '#9B59B6', '#1ABC9C', '#E67E22'],
            xaxis: {
                categories: @json(array_column($weeklyActivity, 'day'))
            },
            yaxis: {
                title: {
                    text: 'Imtihonlar soni'
                }
            },
            legend: {
                show: false
            },
            dataLabels: {
                enabled: false
            }
        };

        var weeklyChart = new ApexCharts(document.querySelector("#weeklyActivityChart"), weeklyOptions);
        weeklyChart.render();
    });
</script>
@endsection