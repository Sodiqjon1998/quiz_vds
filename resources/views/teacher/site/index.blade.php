@extends('teacher.layouts.main')

@section('content')
<style>
    :root {
        --yuksalish-orange: #F58025;
        --yuksalish-brown: #8B4513;
        --yuksalish-gold: #D4A574;
        --yuksalish-teal: #16A085;
        --yuksalish-dark: #212529;
        --yuksalish-gray: #f8f9fa;
    }

    .stat-card {
        border: none;
        border-radius: 12px;
        background: white;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        transition: transform 0.2s, box-shadow 0.3s;
        overflow: hidden;
        height: 100%;
    }

    .stat-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }

    .icon-box {
        width: 60px;
        height: 60px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
        margin-bottom: 1rem;
    }

    .bg-orange-soft {
        background-color: #fff0e6;
        color: var(--yuksalish-orange);
    }

    .bg-brown-soft {
        background-color: #f5ebe0;
        color: var(--yuksalish-brown);
    }

    .bg-gold-soft {
        background-color: #faf6f1;
        color: var(--yuksalish-gold);
    }

    .bg-teal-soft {
        background-color: #e0f7f4;
        color: var(--yuksalish-teal);
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        color: var(--yuksalish-dark);
        line-height: 1.2;
    }

    .stat-label {
        color: #6c757d;
        font-size: 0.9rem;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .chart-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        overflow: hidden;
        height: 100%;
    }

    .chart-header {
        padding: 1.5rem;
        border-bottom: 2px solid #f0f0f0;
        background: linear-gradient(135deg, #fafafa 0%, #ffffff 100%);
    }

    .chart-header h5 {
        font-weight: 700;
        color: var(--yuksalish-dark);
        margin: 0;
        font-size: 1.1rem;
    }

    .chart-body {
        padding: 1.5rem;
    }

    .list-item {
        display: flex;
        align-items: center;
        padding: 12px 0;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.2s;
    }

    .list-item:hover {
        background: #fafafa;
        padding-left: 10px;
        border-radius: 8px;
    }

    .list-item:last-child {
        border-bottom: none;
    }

    .rank-circle {
        width: 35px;
        height: 35px;
        background: #f8f9fa;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: bold;
        color: #999;
        margin-right: 15px;
        flex-shrink: 0;
    }

    .rank-1 {
        background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%);
        color: #856404;
        box-shadow: 0 4px 10px rgba(255, 215, 0, 0.3);
    }

    .rank-2 {
        background: linear-gradient(135deg, #c0c0c0 0%, #e8e8e8 100%);
        color: #495057;
        box-shadow: 0 4px 10px rgba(192, 192, 192, 0.3);
    }

    .rank-3 {
        background: linear-gradient(135deg, #cd7f32 0%, #e09b5f 100%);
        color: #fff;
        box-shadow: 0 4px 10px rgba(205, 127, 50, 0.3);
    }

    .progress-bar-custom {
        height: 8px;
        border-radius: 10px;
        background: #e9ecef;
        overflow: hidden;
        margin-top: 8px;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, var(--yuksalish-orange) 0%, var(--yuksalish-gold) 100%);
        border-radius: 10px;
        transition: width 0.5s ease;
    }

    @media (max-width: 767px) {
        .stat-value {
            font-size: 1.5rem;
        }

        .icon-box {
            width: 50px;
            height: 50px;
            font-size: 1.5rem;
        }
    }
</style>

{{-- MA'LUMOTLARNI SAQLASH --}}
<div id="teacher-chart-data"
    data-monthly-exams="{{ json_encode($monthlyExamsData ?? []) }}"
    data-months="{{ json_encode($monthsLabels ?? []) }}"
    data-class-performance="{{ json_encode($classQuizPerformance ?? []) }}"
    style="display: none;">
</div>

<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 fw-bold text-dark mb-0">
            <i class="ri-dashboard-line me-2" style="color: var(--yuksalish-orange);"></i>
            Boshqaruv Paneli
        </h1>
        <span class="badge bg-light text-dark border px-3 py-2">
            {{ Auth::user()->subject->name ?? 'Fan' }} O'qituvchisi
        </span>
    </div>

    {{-- 1-QATOR: KPI KARTALAR --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-1">Imtihonlar ({{ \Carbon\Carbon::now()->translatedFormat('F') }})</div>
                        <div class="stat-value">{{ number_format($totalExamsTakenThisMonth) }}</div>
                        <small class="text-muted">ta test topshirildi</small>
                    </div>
                    <div class="icon-box bg-orange-soft">
                        <i class="ri-file-list-3-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-1">Fan O'rtacha Natijasi</div>
                        <div class="stat-value">{{ $averageSuccessRate }}%</div>
                        <div class="progress-bar-custom">
                            <div class="progress-fill" style="width: {{ $averageSuccessRate }}%"></div>
                        </div>
                    </div>
                    <div class="icon-box bg-brown-soft">
                        <i class="ri-medal-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-1">Mening Testlarim</div>
                        <div class="stat-value">{{ number_format($totalQuizzesCreated) }}</div>
                        <small class="text-muted">ta test yaratilgan</small>
                    </div>
                    <div class="icon-box bg-gold-soft">
                        <i class="ri-book-open-line"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-1">Jami O'quvchilar</div>
                        <div class="stat-value">{{ number_format($totalStudentsInSystem) }}</div>
                        <small class="text-muted">faol o'quvchi</small>
                    </div>
                    <div class="icon-box bg-teal-soft">
                        <i class="ri-user-smile-line"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2-QATOR: GRAFIKLAR --}}
    <div class="row g-4 mb-4">
        {{-- OYLIK IMTIHONLAR GRAFIGI --}}
        <div class="col-12 col-lg-7">
            <div class="chart-card">
                <div class="chart-header">
                    <h5>
                        <i class="ri-line-chart-line me-2" style="color: var(--yuksalish-orange);"></i>
                        Oylik Imtihonlar Statistikasi
                    </h5>
                    <small class="text-muted">{{ Auth::user()->subject->name ?? 'Fan' }} bo'yicha</small>
                </div>
                <div class="chart-body">
                    <div id="monthlyExamsChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        {{-- SINFLAR PERFORMANSI (DONUT) --}}
        <div class="col-12 col-lg-5">
            <div class="chart-card">
                <div class="chart-header">
                    <h5>
                        <i class="ri-pie-chart-2-line me-2" style="color: var(--yuksalish-teal);"></i>
                        Sinflar Bo'yicha Natija
                    </h5>
                    <small class="text-muted">To'g'ri javoblar foizi</small>
                </div>
                <div class="chart-body">
                    <div id="classPerformanceChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3-QATOR: TOP RO'YXATLAR --}}
    <div class="row g-4">
        {{-- TOP SINFLAR --}}
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5>
                        <i class="ri-trophy-line me-2" style="color: #ffc107;"></i>
                        Top 5 Eng Yaxshi Sinflar
                    </h5>
                    <small class="text-muted">{{ Auth::user()->subject->name ?? 'Fan' }} bo'yicha</small>
                </div>
                <div class="chart-body">
                    {{-- REAL MA'LUMOTLAR BILAN ALMASHTIRILDI --}}
                    @forelse($topClassesByPerformance ?? [] as $index => $class)
                    <div class="list-item">
                        <div class="rank-circle rank-{{ min($index + 1, 3) }}">{{ $index + 1 }}</div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0 text-dark">{{ $class['name'] }}</h6>
                            <small class="text-muted">{{ number_format($class['percentage'], 1) }}% o'rtacha natija</small>
                            <div class="progress-bar-custom">
                                <div class="progress-fill" style="width: {{ $class['percentage'] }}%"></div>
                            </div>
                        </div>
                        <div class="text-end ms-3">
                            @if($index === 0)
                            <span class="badge" style="background: linear-gradient(135deg, #ffd700 0%, #ffed4e 100%); color: #856404;">🏆 1-o'rin</span>
                            @elseif($index === 1)
                            <span class="badge bg-secondary">🥈 2-o'rin</span>
                            @elseif($index === 2)
                            <span class="badge" style="background: #cd7f32; color: white;">🥉 3-o'rin</span>
                            @else
                            <span class="badge bg-light text-dark border">{{ $index + 1 }}-o'rin</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="ri-inbox-line" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-2">Hozircha ma'lumot yo'q</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- FAOL O'QUVCHILAR --}}
        <div class="col-12 col-lg-6">
            <div class="chart-card">
                <div class="chart-header">
                    <h5>
                        <i class="ri-star-line me-2" style="color: var(--yuksalish-orange);"></i>
                        Top 5 Faol O'quvchilar
                    </h5>
                    <small class="text-muted">Eng ko'p test topshirganlar</small>
                </div>
                <div class="chart-body">
                    {{-- REAL MA'LUMOTLAR BILAN ALMASHTIRILDI --}}
                    @forelse($topActiveStudents ?? [] as $index => $student)
                    <div class="list-item">
                        <div class="rank-circle rank-{{ min($index + 1, 3) }}">{{ $index + 1 }}</div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0 text-dark">{{ $student['name'] }}</h6>
                            <small class="text-muted">{{ $student['class_name'] }} - {{ $student['exam_count'] }} ta test</small>
                        </div>
                        <div class="text-end ms-3">
                            @if($student['exam_count'] >= 50)
                            <span class="badge bg-success">⭐ Eng faol</span>
                            @elseif($student['exam_count'] >= 30)
                            <span class="badge bg-primary">Faol</span>
                            @else
                            <span class="badge bg-info">Yaxshi</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="text-center text-muted py-5">
                        <i class="ri-user-search-line" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="mt-2">Hozircha ma'lumot yo'q</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

{{-- APEXCHARTS --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/remixicon@3.5.0/fonts/remixicon.css">
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<script>
    window.teacherCharts = {
        monthly: null,
        performance: null
    };

    function initTeacherCharts() {
        if (typeof ApexCharts === 'undefined') {
            setTimeout(initTeacherCharts, 100);
            return;
        }

        var dataEl = document.getElementById('teacher-chart-data');
        if (!dataEl) return;

        try {
            var monthlyData = JSON.parse(dataEl.getAttribute('data-monthly-exams') || '[]');
            var monthsLabels = JSON.parse(dataEl.getAttribute('data-months') || '[]');
            var classPerformance = JSON.parse(dataEl.getAttribute('data-class-performance') || '[]');

            // ApexCharts Donut Chart uchun ma'lumotlarni tayyorlash
            var classNames = classPerformance.map(c => c.name);
            var classScores = classPerformance.map(c => c.y);

            // 1. OYLIK IMTIHONLAR GRAFIGI (Area Chart)
            var chart1El = document.querySelector("#monthlyExamsChart");
            if (chart1El && monthlyData.length > 0) {
                if (window.teacherCharts.monthly) {
                    window.teacherCharts.monthly.destroy();
                }
                chart1El.innerHTML = "";

                var options1 = {
                    series: [{
                        name: 'Imtihonlar soni',
                        data: monthlyData
                    }],
                    chart: {
                        height: 350,
                        type: 'area',
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
                            opacityTo: 0.2,
                            stops: [0, 90, 100]
                        }
                    },
                    xaxis: {
                        categories: monthsLabels,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    yaxis: {
                        title: {
                            text: 'Testlar soni'
                        }
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                        strokeDashArray: 4
                    },
                    tooltip: {
                        theme: 'light',
                        y: {
                            formatter: function(val) {
                                return val + " ta test";
                            }
                        }
                    }
                };

                window.teacherCharts.monthly = new ApexCharts(chart1El, options1);
                window.teacherCharts.monthly.render();
            } else if (chart1El) {
                chart1El.innerHTML = '<div class="text-center text-muted py-5"><i class="ri-bar-chart-box-line" style="font-size: 3rem; opacity: 0.3;"></i><p class="mt-2">Oxirgi 6 oyda imtihon ma\'lumotlari yetarli emas.</p></div>';
            }


            // 2. SINFLAR PERFORMANSI (Donut Chart)
            var chart2El = document.querySelector("#classPerformanceChart");
            if (chart2El && classScores.length > 0 && classScores.some(score => score > 0)) { // Faqat ma'lumot bo'lsagina chizish
                if (window.teacherCharts.performance) {
                    window.teacherCharts.performance.destroy();
                }
                chart2El.innerHTML = "";

                var options2 = {
                    series: classScores,
                    labels: classNames,
                    chart: {
                        type: 'donut',
                        height: 350,
                        fontFamily: 'Inter, sans-serif'
                    },
                    colors: ['#F58025', '#8B4513', '#D4A574', '#16A085', '#E74C3C', '#3498DB', '#9B59B6', '#F39C12'],
                    plotOptions: {
                        pie: {
                            donut: {
                                size: '70%',
                                labels: {
                                    show: true,
                                    total: {
                                        show: true,
                                        label: 'O\'rtacha',
                                        fontSize: '18px',
                                        color: '#6c757d',
                                        formatter: function(w) {
                                            // 0 bo'lmagan qiymatlarning o'rtachasini chiqarish
                                            var validScores = w.globals.seriesTotals.filter(val => val > 0);
                                            var sum = validScores.reduce((a, b) => a + b, 0);
                                            var avg = validScores.length > 0 ? sum / validScores.length : 0;
                                            return avg.toFixed(1) + '%';
                                        }
                                    }
                                }
                            }
                        }
                    },
                    legend: {
                        position: 'bottom',
                        markers: {
                            radius: 12
                        }
                    },
                    dataLabels: {
                        enabled: true,
                        formatter: function(val, opts) {
                            return val.toFixed(1) + '%'
                        }
                    },
                    tooltip: {
                        y: {
                            formatter: function(val) {
                                return val.toFixed(1) + '%';
                            }
                        }
                    }
                };

                window.teacherCharts.performance = new ApexCharts(chart2El, options2);
                window.teacherCharts.performance.render();
            } else if (chart2El) {
                chart2El.innerHTML = '<div class="text-center text-muted py-5"><i class="ri-pie-chart-2-line" style="font-size: 3rem; opacity: 0.3;"></i><p class="mt-2">Joriy oyda sinf natijalari yetarli emas.</p></div>';
            }


        } catch (e) {
            console.error('Grafik chizishda xatolik:', e);
            // Jiddiy xatoda ham bo'sh xabar ko'rsatish
            document.querySelector("#monthlyExamsChart").innerHTML = '<div class="text-center text-danger py-5"><i class="ri-error-warning-line" style="font-size: 3rem; opacity: 0.5;"></i><p class="mt-2">Ma\'lumotni yuklashda kritik xato.</p></div>';
            document.querySelector("#classPerformanceChart").innerHTML = '<div class="text-center text-danger py-5"><i class="ri-error-warning-line" style="font-size: 3rem; opacity: 0.5;"></i><p class="mt-2">Ma\'lumotni yuklashda kritik xato.</p></div>';
        }
    }

    document.addEventListener('DOMContentLoaded', initTeacherCharts);
</script>
@endsection