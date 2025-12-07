<div>
    <style>
        :root {
            --yuksalish-orange: #F58025;
            --yuksalish-dark: #212529;
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
                        <div class="stat-label mb-1">Testlar</div>
                        <div class="stat-value">{{ number_format($stats['exam_total']) }}</div>
                    </div>
                    <div class="icon-box bg-purple-soft"><i class="ri-file-list-3-line"></i></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-md-6 col-xl-3">
            <div class="stat-card p-4">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="stat-label mb-1">Audio Yozuvlar</div>
                        <div class="stat-value">{{ number_format($stats['reading']['total_records']) }}</div>
                    </div>
                    <div class="icon-box bg-green-soft"><i class="ri-mic-line"></i></div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2-QATOR: GRAFIKLAR (DIAGRAMMALAR) --}}
    <div class="row g-4 mb-4">
        {{-- GRAFIK 1: Haftalik Faollik --}}
        <div class="col-12 col-lg-8">
            <div class="stat-card">
                <div class="p-4 border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="fw-bold mb-0 text-dark"><i class="ri-line-chart-line text-success me-2"></i> Haftalik Faollik</h5>
                    <small class="text-muted">Kunlik hisobotlar soni</small>
                </div>
                <div class="p-4">
                    <div id="activityChart" style="min-height: 300px;"></div>
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
                    <div id="usersChart" style="min-height: 300px;"></div>
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

{{-- APEXCHARTS SCRIPT --}}
@push('scripts')
<script src="{{ asset('assets/vendor/libs/apex-charts/apexcharts.js') }}"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {

        // 1. HAFTALIK FAOLLIK (Area Chart)
        const activityOptions = {
            series: [{
                name: 'Hisobotlar',
                data: @json($stats['chart_data']) // PHP dan kelgan ma'lumot
            }],
            chart: {
                height: 300,
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
                width: 2
            },
            colors: ['#F58025'], // Yuksalish Orange
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
                categories: @json($stats['chart_days']), // PHP dan kelgan kunlar
                axisBorder: {
                    show: false
                },
                axisTicks: {
                    show: false
                }
            },
            grid: {
                borderColor: '#f1f1f1',
                strokeDashArray: 4,
            },
            tooltip: {
                theme: 'light'
            }
        };

        const activityChart = new ApexCharts(document.querySelector("#activityChart"), activityOptions);
        activityChart.render();


        // 2. FOYDALANUVCHILAR (Donut Chart)
        const usersOptions = {
            series: [{
                {
                    $stats['counts']['students']
                }
            }, {
                {
                    $stats['counts']['teachers']
                }
            }],
            labels: ['O\'quvchilar', 'O\'qituvchilar'],
            chart: {
                type: 'donut',
                height: 320,
                fontFamily: 'Inter, sans-serif'
            },
            colors: ['#0d6efd', '#F58025'], // Moviy va Zarg'aldoq
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

        const usersChart = new ApexCharts(document.querySelector("#usersChart"), usersOptions);
        usersChart.render();
    });
</script>
@endpush