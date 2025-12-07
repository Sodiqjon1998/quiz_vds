<div>
    <style>
        :root {
            --yuksalish-orange: #F58025;
            --yuksalish-dark: #212529;
            --yuksalish-gray: #f8f9fa;
        }

        .stat-card {
            border: none;
            border-radius: 12px;
            background: white;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
            overflow: hidden;
            height: 100%;
        }

        .stat-card:hover {
            transform: translateY(-5px);
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

        .bg-blue-soft {
            background-color: #e3f2fd;
            color: #0d6efd;
        }

        .bg-green-soft {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .bg-purple-soft {
            background-color: #f3e5f5;
            color: #6f42c1;
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

        .list-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f0f0f0;
        }

        .list-item:last-child {
            border-bottom: none;
        }

        .rank-circle {
            width: 30px;
            height: 30px;
            background: #f8f9fa;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #999;
            margin-right: 15px;
        }

        .rank-1 {
            background: #fff3cd;
            color: #ffc107;
        }

        .rank-2 {
            background: #e2e3e5;
            color: #6c757d;
        }

        .rank-3 {
            background: #f1e0d6;
            color: #cd7f32;
        }
    </style>

    {{-- MA'LUMOTLARNI SAQLASH UCHUN YASHIRIN ELEMENT --}}
    <div id="dashboard-chart-data"
        data-activity="{{ json_encode($stats['chart_data']) }}"
        data-days="{{ json_encode($stats['chart_days']) }}"
        data-students="{{ $stats['counts']['students'] }}"
        data-teachers="{{ $stats['counts']['teachers'] }}"
        data-coordinators="{{ $stats['counts']['coordinators'] }}"
        style="display: none;">
    </div>

    {{-- 1-QATOR: ASOSIY KARTALAR --}}
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-1">O'quvchilar</div>
                        <div class="stat-value">{{ number_format($stats['counts']['students']) }}</div>
                    </div>
                    <div class="icon-box bg-blue-soft"><i class="ri-user-smile-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-1">O'qituvchilar</div>
                        <div class="stat-value">{{ number_format($stats['counts']['teachers']) }}</div>
                    </div>
                    <div class="icon-box bg-orange-soft"><i class="ri-briefcase-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-1">Koordinatorlar</div>
                        <div class="stat-value">{{ number_format($stats['counts']['coordinators']) }}</div>
                    </div>
                    <div class="icon-box bg-green-soft"><i class="ri-user-star-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-1">Testlar</div>
                        <div class="stat-value">{{ number_format($stats['exam_total']) }}</div>
                    </div>
                    <div class="icon-box bg-purple-soft"><i class="ri-file-list-3-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2-QATOR: GRAFIKLAR --}}
    <div class="row g-4 mb-4">
        {{-- GRAFIK 1: Haftalik Faollik --}}
        <div class="col-12 col-lg-8">
            <div class="stat-card">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="ri-line-chart-line text-success me-2"></i> Haftalik Faollik</h5>
                    <small class="text-muted">Kunlik hisobotlar soni</small>
                </div>
                <div class="p-4">
                    <div wire:ignore id="activityChart" style="min-height: 350px;"></div>
                </div>
            </div>
        </div>

        {{-- GRAFIK 2: Foydalanuvchilar Nisbati --}}
        <div class="col-12 col-lg-4">
            <div class="stat-card">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="ri-pie-chart-2-line text-primary me-2"></i> Foydalanuvchilar</h5>
                </div>
                <div class="p-4 d-flex align-items-center justify-content-center">
                    <div wire:ignore id="usersChart" style="min-height: 350px; width: 100%;"></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 3-QATOR: TOP RO'YXATLAR --}}
    <div class="row g-4">
        {{-- Top Sinflar --}}
        <div class="col-12 col-lg-6">
            <div class="stat-card">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="ri-trophy-line text-warning me-2"></i> Top 5 Faol Sinflar</h5>
                </div>
                <div class="p-4">
                    @foreach($stats['top_classes'] as $index => $class)
                    <div class="list-item">
                        <div class="rank-circle rank-{{ $index + 1 }}">{{ $index + 1 }}</div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0 text-dark">{{ $class->name }}</h6>
                            <small class="text-muted">{{ $class->reports_count }} ta hisobot</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-light text-primary border">Faol</span>
                        </div>
                    </div>
                    @endforeach
                    @if(count($stats['top_classes']) == 0)
                    <div class="text-center text-muted py-3">Ma'lumot yo'q</div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Top O'qituvchilar --}}
        <div class="col-12 col-lg-6">
            <div class="stat-card">
                <div class="p-4 border-bottom">
                    <h5 class="fw-bold mb-0 text-dark"><i class="ri-medal-line text-info me-2"></i> Top 5 Faol O'qituvchilar</h5>
                </div>
                <div class="p-4">
                    @foreach($stats['top_teachers'] as $index => $teacher)
                    <div class="list-item">
                        <div class="rank-circle rank-{{ $index + 1 }}">{{ $index + 1 }}</div>
                        <div class="flex-grow-1">
                            <h6 class="fw-bold mb-0 text-dark">{{ $teacher->first_name }} {{ $teacher->last_name }}</h6>
                            <small class="text-muted">{{ $teacher->quiz_count }} ta test yaratgan</small>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success-subtle text-success">Eng yaxshi</span>
                        </div>
                    </div>
                    @endforeach
                    @if(count($stats['top_teachers']) == 0)
                    <div class="text-center text-muted py-3">Ma'lumot yo'q</div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- APEXCHARTS SKRIPTI --}}
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>

<script>
    // Grafiklar obyektini global saqlaymiz, shunda keyinroq o'chira olamiz
    window.dashboardCharts = {
        activity: null,
        users: null
    };

    function initDashboardCharts() {
        if (typeof ApexCharts === 'undefined') {
            setTimeout(initDashboardCharts, 100);
            return;
        }

        var dataEl = document.getElementById('dashboard-chart-data');
        if (!dataEl) return;

        try {
            var activityData = JSON.parse(dataEl.getAttribute('data-activity'));
            var activityDays = JSON.parse(dataEl.getAttribute('data-days'));

            var studentCount = parseInt(dataEl.getAttribute('data-students'));
            var teacherCount = parseInt(dataEl.getAttribute('data-teachers'));
            var coordinatorCount = parseInt(dataEl.getAttribute('data-coordinators'));

            // --- 1. HAFTALIK FAOLLIK GRAFIGI ---
            var chart1El = document.querySelector("#activityChart");
            if (chart1El) {
                // ESKI GRAFIKNI O'CHIRISH (Muhim joyi shu!)
                if (window.dashboardCharts.activity) {
                    window.dashboardCharts.activity.destroy();
                }
                chart1El.innerHTML = ""; // Konteynerni tozalash

                var options1 = {
                    series: [{
                        name: 'Hisobotlar',
                        data: activityData
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
                        categories: activityDays,
                        axisBorder: {
                            show: false
                        },
                        axisTicks: {
                            show: false
                        }
                    },
                    grid: {
                        borderColor: '#f1f1f1',
                        strokeDashArray: 4
                    },
                    tooltip: {
                        theme: 'light'
                    }
                };

                // Yangi grafik yaratish va saqlash
                window.dashboardCharts.activity = new ApexCharts(chart1El, options1);
                window.dashboardCharts.activity.render();
            }

            // --- 2. FOYDALANUVCHILAR GRAFIGI ---
            var chart2El = document.querySelector("#usersChart");
            if (chart2El) {
                // ESKI GRAFIKNI O'CHIRISH
                if (window.dashboardCharts.users) {
                    window.dashboardCharts.users.destroy();
                }
                chart2El.innerHTML = ""; // Konteynerni tozalash

                if (studentCount === 0 && teacherCount === 0 && coordinatorCount === 0) {
                    chart2El.innerHTML = '<div class="d-flex align-items-center justify-content-center h-100 text-muted">Ma\'lumot yo\'q</div>';
                } else {
                    var options2 = {
                        series: [studentCount, teacherCount, coordinatorCount],
                        labels: ['O\'quvchilar', 'O\'qituvchilar', 'Koordinatorlar'],
                        chart: {
                            type: 'donut',
                            height: 350,
                            fontFamily: 'Inter, sans-serif'
                        },
                        colors: ['#0d6efd', '#F58025', '#198754'],
                        plotOptions: {
                            pie: {
                                donut: {
                                    size: '70%',
                                    labels: {
                                        show: true,
                                        total: {
                                            show: true,
                                            label: 'Jami',
                                            fontSize: '18px',
                                            color: '#6c757d',
                                            formatter: function(w) {
                                                return w.globals.seriesTotals.reduce((a, b) => a + b, 0)
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
                            enabled: false
                        }
                    };

                    // Yangi grafik yaratish va saqlash
                    window.dashboardCharts.users = new ApexCharts(chart2El, options2);
                    window.dashboardCharts.users.render();
                }
            }

        } catch (e) {
            console.error('Grafik chizishda xatolik:', e);
        }
    }

    // Sahifa yuklanganda va Livewire yangilanganda ishga tushirish
    document.addEventListener('DOMContentLoaded', initDashboardCharts);
    document.addEventListener('livewire:load', initDashboardCharts);

    // Livewire har safar yangilanganda (filter bosilganda) grafikni qayta chizish
    document.addEventListener('livewire:update', initDashboardCharts);
</script>