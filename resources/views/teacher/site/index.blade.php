@extends('teacher.layouts.main')

@section('content')
<style>
    /* Umumiy sozlamalar */
    .container-fluid {
        padding: 20px;
    }

    h1 {
        font-size: 1.75rem;
        font-weight: 600;
        color: #2c3e50;
        margin-bottom: 1.5rem;
    }

    /* KPI Cards - Yuksalish Maktabi ranglari */
    .card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        transition: transform 0.2s, box-shadow 0.2s;
        overflow: hidden;
        height: 100%;
    }

    .card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Yuksalish Maktabi ranglari - gradient YO'Q */
    .border-left-orange {
        border-left: 4px solid #E67E22;
    }

    .text-orange {
        color: #E67E22;
    }

    .border-left-brown {
        border-left: 4px solid #8B4513;
    }

    .text-brown {
        color: #8B4513;
    }

    .border-left-gold {
        border-left: 4px solid #D4A574;
    }

    .text-gold {
        color: #D4A574;
    }

    .border-left-teal {
        border-left: 4px solid #16A085;
    }

    .text-teal {
        color: #16A085;
    }

    /* KPI Card Layout */
    .kpi-card {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .kpi-content h6 {
        font-size: 0.75rem;
        text-transform: uppercase;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .kpi-content .value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #2c3e50;
    }

    .kpi-icon {
        font-size: 2.5rem;
        opacity: 0.2;
    }

    /* Chart sozlamalari */
    .chart-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        padding: 20px;
    }

    .chart-header {
        margin-bottom: 15px;
        padding-bottom: 15px;
        border-bottom: 2px solid #f0f0f0;
    }

    .chart-header h6 {
        font-size: 1.1rem;
        font-weight: 600;
        color: #2c3e50;
        margin: 0;
    }

    .chart-month-display {
        text-align: center;
        margin-top: 10px;
        font-size: 0.9rem;
        color: #E67E22;
        font-weight: 500;
    }

    /* Chart Controls */
    .chart-controls {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
        flex-wrap: wrap;
    }

    #play-pause-button {
        background: #E67E22;
        color: white;
        border: none;
        border-radius: 50%;
        width: 40px;
        height: 40px;
        cursor: pointer;
        transition: background 0.2s;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    #play-pause-button:hover {
        background: #D35400;
    }

    #play-range {
        flex: 1;
        min-width: 150px;
        height: 6px;
        border-radius: 3px;
        background: #e0e0e0;
        outline: none;
        -webkit-appearance: none;
    }

    #play-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #E67E22;
        cursor: pointer;
    }

    #play-range::-moz-range-thumb {
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #E67E22;
        cursor: pointer;
        border: none;
    }

    #current-month-display {
        font-weight: 600;
        color: #2c3e50;
        font-size: 0.9rem;
        white-space: nowrap;
    }

    /* 3D Chart Sliders */
    #sliders {
        margin-top: 20px;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
    }

    #sliders>div {
        margin-bottom: 15px;
        font-size: 0.85rem;
        color: #666;
    }

    #sliders input[type="range"] {
        width: 100%;
        height: 4px;
        border-radius: 2px;
        background: #e0e0e0;
        outline: none;
        -webkit-appearance: none;
        margin-top: 5px;
    }

    #sliders input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #8B4513;
        cursor: pointer;
    }

    #sliders input[type="range"]::-moz-range-thumb {
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #8B4513;
        cursor: pointer;
        border: none;
    }

    #sliders span {
        font-weight: 600;
        color: #8B4513;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .charts-row {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 767px) {
        .container-fluid {
            padding: 15px;
        }

        h1 {
            font-size: 1.5rem;
        }

        .chart-controls {
            flex-direction: column;
            align-items: stretch;
        }

        #current-month-display {
            text-align: center;
        }

        .kpi-content .value {
            font-size: 1.5rem;
        }

        .kpi-icon {
            font-size: 2rem;
        }
    }
</style>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Boshqaruv Paneli ({{ Auth::user()->subject->name ?? 'Fan' }} O'qituvchisi)</h1>

    {{-- KPI Cards --}}
    <div class="row mb-4">
        {{-- KPI 1: Imtihonlar (Joriy Oy) --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-orange">
                <div class="card-body kpi-card">
                    <div class="kpi-content">
                        <h6 class="text-orange">
                            Imtihonlar ({{ \Carbon\Carbon::now()->translatedFormat('F') }})
                        </h6>
                        <div class="value">{{ $totalExamsTakenThisMonth }} ta</div>
                    </div>
                    <div class="kpi-icon text-orange">
                        <i class="fa fa-pencil-square-o"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 2: Fan O'rtacha Natijasi --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-brown">
                <div class="card-body kpi-card">
                    <div class="kpi-content">
                        <h6 class="text-brown">Fan O'rtacha Natijasi</h6>
                        <div class="value">{{ $averageSuccessRate }}%</div>
                    </div>
                    <div class="kpi-icon text-brown">
                        <i class="fa fa-check-circle"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 3: Mening Testlarim --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-gold">
                <div class="card-body kpi-card">
                    <div class="kpi-content">
                        <h6 class="text-gold">Mening Yaratgan Testlarim</h6>
                        <div class="value">{{ $totalQuizzesCreated }} ta</div>
                    </div>
                    <div class="kpi-icon text-gold">
                        <i class="fa fa-book"></i>
                    </div>
                </div>
            </div>
        </div>

        {{-- KPI 4: Jami O'quvchilar --}}
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-teal">
                <div class="card-body kpi-card">
                    <div class="kpi-content">
                        <h6 class="text-teal">Tizimdagi Jami O'quvchilar</h6>
                        <div class="value">{{ $totalStudentsInSystem }} ta</div>
                    </div>
                    <div class="kpi-icon text-teal">
                        <i class="fa fa-users"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts Row --}}
    <div class="row">
        {{-- 1-Grafik: Bar Chart --}}
        <div class="col-lg-6 mb-4">
            <div class="chart-card">
                <div class="chart-header">
                    <h6>O'quvchi Faolligi (Ro'yxatga Olish)</h6>
                </div>
                <div id="container" style="min-width: 250px; height: 400px;"></div>
                <div class="chart-controls">
                    <button id="play-pause-button" title="play">
                        <i class="fa fa-play"></i>
                    </button>
                    <input type="range" id="play-range" value="" step="1">
                    <span id="current-month-display"></span>
                </div>
            </div>
        </div>

        {{-- 2-Grafik: 3D Column Chart --}}
        <div class="col-lg-6 mb-4">
            <div class="chart-card">
                <div class="chart-header">
                    <h6>Sinflarning {{ Auth::user()->subject->name ?? 'Fan' }} bo'yicha Natijasi (%)</h6>
                    <span id="current-month-3d-display" class="chart-month-display"></span>
                </div>
                <div id="container-3d" style="min-width: 250px; height: 400px;"></div>

                <div id="sliders">
                    <div>
                        Alpha: <span id="alpha-value"></span>
                        <input id="alpha" type="range" min="0" max="45" value="15" />
                    </div>
                    <div>
                        Beta: <span id="beta-value"></span>
                        <input id="beta" type="range" min="0" max="45" value="15" />
                    </div>
                    <div>
                        Depth: <span id="depth-value"></span>
                        <input id="depth" type="range" min="20" max="100" value="50" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Highcharts Scripts --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/data.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<script src="https://code.highcharts.com/highcharts-more.js"></script>
<script src="https://code.highcharts.com/highcharts-3d.js"></script>

<script>
    // Backenddan ma'lumotlar
    const classesData = @json($allClasses);
    const studentsData = @json($studentsByClassAndMonth);
    const classQuizPerformanceData = @json($classQuizPerformance);

    const btn = document.getElementById('play-pause-button');
    const input = document.getElementById('play-range');
    const currentMonthDisplay = document.getElementById('current-month-display');
    const nbr = classesData.length > 0 ? classesData.length : 10;

    let dataset, chart, chart3d;
    let sortedMonthKeys = Object.keys(studentsData).sort();

    // Range sozlamalari
    if (input) {
        input.min = 0;
        input.max = sortedMonthKeys.length - 1;
        input.value = sortedMonthKeys.length - 1;
    }

    // Yuksalish Maktabi ranglar palitasi
    const colorPalette = [
        '#E67E22', '#8B4513', '#D4A574', '#16A085',
        '#E74C3C', '#3498DB', '#9B59B6', '#F39C12',
        '#1ABC9C', '#34495E'
    ];

    /* Animate dataLabels */
    (function(H) {
        const FLOAT = /^-?\d+\.?\d*$/;
        H.Fx.prototype.textSetter = function() {
            const chart = H.charts[this.elem.renderer.chartIndex];
            let thousandsSep = chart.numberFormatter('1000.0')[1];
            if (/[0-9]/.test(thousandsSep)) {
                thousandsSep = ' ';
            }
            const replaceRegEx = new RegExp(thousandsSep, 'g');
            let startValue = this.start.replace(replaceRegEx, ''),
                endValue = this.end.replace(replaceRegEx, ''),
                currentValue = this.end.replace(replaceRegEx, '');
            if ((startValue || '').match(FLOAT)) {
                startValue = parseInt(startValue, 10);
                endValue = parseInt(endValue, 10);
                currentValue = chart.numberFormatter(
                    Math.round(startValue + (endValue - startValue) * this.pos), 0
                );
            }
            this.elem.endText = this.end;
            this.elem.attr(this.prop, currentValue, null, true);
        };
        H.SVGElement.prototype.textGetter = function() {
            const ct = this.text.element.textContent || '';
            return this.endText ? this.endText : ct.substring(0, ct.length / 2);
        };
        H.wrap(H.Series.prototype, 'drawDataLabels', function(proceed) {
            const attr = H.SVGElement.prototype.attr,
                chart = this.chart;
            if (chart.sequenceTimer) {
                this.points.forEach(point =>
                    (point.dataLabels || []).forEach(
                        label =>
                        (label.attr = function(hash) {
                            if (hash && hash.text !== undefined && chart.isResizing === 0) {
                                const text = hash.text;
                                delete hash.text;
                                return this.attr(hash).animate({
                                    text
                                });
                            }
                            return attr.apply(this, arguments);
                        })
                    )
                );
            }
            const ret = proceed.apply(this, Array.prototype.slice.call(arguments, 1));
            this.points.forEach(p => (p.dataLabels || []).forEach(d => (d.attr = attr)));
            return ret;
        });
    }(Highcharts));

    function getDataForMonth(monthKey) {
        const monthData = studentsData[monthKey] || {};
        const output = Object.entries(monthData)
            .map(entry => {
                const [className, studentCount] = entry;
                return [className, studentCount || 0];
            })
            .sort((a, b) => b[1] - a[1]);

        return [output[0], output.slice(0, nbr)];
    }

    function getSubtitle() {
        const currentMonthIndex = parseInt(input.value);
        const currentMonthKey = sortedMonthKeys[currentMonthIndex];
        if (!currentMonthKey) return '';

        const date = new Date(currentMonthKey + '-01');
        const monthName = date.toLocaleString('uz-UZ', {
            month: 'long',
            year: 'numeric'
        });

        const currentMonthData = studentsData[currentMonthKey];
        let totalStudentsInMonth = 0;
        if (currentMonthData) {
            Object.values(currentMonthData).forEach(count => {
                totalStudentsInMonth += (count || 0);
            });
        }

        return `<span style="font-size: 60px; font-weight: bold; color: #E67E22; display: block; text-align: right;">${monthName.toUpperCase()}</span>
                <span style="font-size: 18px; color: #666; display: block; text-align: right; margin-top: 5px;">
                    Jami o'quvchilar: <b>${totalStudentsInMonth}</b>
                </span>`;
    }

    // Bar Chart
    (async () => {
        dataset = studentsData;
        const initialMonthIndex = sortedMonthKeys.length - 1;
        const initialMonthKey = sortedMonthKeys[initialMonthIndex];

        chart = Highcharts.chart('container', {
            chart: {
                animation: {
                    duration: 500
                },
                marginRight: 50
            },
            title: {
                text: null
            },
            subtitle: {
                text: getSubtitle(),
                floating: true,
                align: 'right',
                verticalAlign: 'bottom',
                y: -20,
                x: -10,
                useHTML: true
            },
            legend: {
                enabled: false
            },
            xAxis: {
                type: 'category',
                title: {
                    text: 'Sinflar'
                }
            },
            yAxis: {
                opposite: true,
                tickPixelInterval: 150,
                title: {
                    text: 'O\'quvchilar soni'
                }
            },
            plotOptions: {
                series: {
                    animation: true,
                    groupPadding: 0,
                    pointPadding: 0.1,
                    borderWidth: 0,
                    colorByPoint: true,
                    colors: colorPalette,
                    dataSorting: {
                        enabled: true,
                        matchByName: true
                    },
                    type: 'bar',
                    dataLabels: {
                        enabled: true,
                        format: '{y}',
                        style: {
                            fontSize: '14px',
                            fontWeight: 'bold',
                            color: '#333',
                            textOutline: 'none'
                        },
                        align: 'right',
                        x: 20
                    }
                }
            },
            series: [{
                type: 'bar',
                name: 'O\'quvchilar soni',
                data: getDataForMonth(initialMonthKey)[1]
            }],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 550
                    },
                    chartOptions: {
                        xAxis: {
                            visible: false
                        },
                        subtitle: {
                            x: 0
                        },
                        plotOptions: {
                            series: {
                                dataLabels: [{
                                    enabled: true,
                                    y: 8
                                }, {
                                    enabled: true,
                                    y: -8,
                                    style: {
                                        fontWeight: 'normal',
                                        opacity: 0.7
                                    }
                                }]
                            }
                        }
                    }
                }]
            }
        });

        updateMonthDisplay();
        update(0);
    })();

    function pause(button) {
        button.title = 'play';
        button.innerHTML = '<i class="fa fa-play"></i>';
        clearTimeout(chart.sequenceTimer);
        chart.sequenceTimer = undefined;
    }

    function updateMonthDisplay() {
        const currentMonthIndex = parseInt(input.value);
        const currentMonthKey = sortedMonthKeys[currentMonthIndex];
        if (currentMonthDisplay && currentMonthKey) {
            const date = new Date(currentMonthKey + '-01');
            currentMonthDisplay.textContent = date.toLocaleString('uz-UZ', {
                month: 'long',
                year: 'numeric'
            }).toUpperCase();
        }
    }

    function update(increment) {
        if (increment) {
            input.value = parseInt(input.value, 10) + increment;
        }
        if (parseInt(input.value) >= sortedMonthKeys.length - 1) {
            pause(btn);
        }
        updateMonthDisplay();

        const currentMonthKey = sortedMonthKeys[parseInt(input.value)];

        chart.update({
                subtitle: {
                    text: getSubtitle()
                },
                series: [{
                    name: 'O\'quvchilar soni (' + currentMonthKey + ')',
                    data: getDataForMonth(currentMonthKey)[1]
                }]
            },
            true,
            false
        );
    }

    function play(button) {
        button.title = 'pause';
        button.innerHTML = '<i class="fa fa-pause"></i>';
        chart.sequenceTimer = setInterval(function() {
            update(1);
        }, 1000);
    }

    if (btn) {
        btn.addEventListener('click', function() {
            if (chart.sequenceTimer) {
                pause(this);
            } else {
                if (parseInt(input.value) >= sortedMonthKeys.length - 1) {
                    input.value = 0;
                    update(0);
                }
                play(this);
            }
        });
    }

    if (input) {
        input.addEventListener('input', function() {
            update(0);
            pause(btn);
        });
    }

    // 3D Chart
    const today = new Date();
    const currentMonthNameFor3D = today.toLocaleString('uz-UZ', {
        month: 'long',
        year: 'numeric'
    });

    const currentMonth3dDisplay = document.getElementById('current-month-3d-display');
    if (currentMonth3dDisplay) {
        currentMonth3dDisplay.textContent = currentMonthNameFor3D;
    }

    chart3d = new Highcharts.Chart({
        chart: {
            renderTo: 'container-3d',
            type: 'column',
            options3d: {
                enabled: true,
                alpha: 15,
                beta: 15,
                depth: 50,
                viewDistance: 25
            }
        },
        title: {
            text: null
        },
        subtitle: {
            text: 'Manba: Test natijalari'
        },
        xAxis: {
            type: 'category',
            title: {
                text: 'Sinf nomi'
            }
        },
        yAxis: {
            title: {
                text: 'To\'g\'ri javob foizi (%)'
            },
            labels: {
                format: '{value}%'
            },
            max: 100,
            min: 0
        },
        tooltip: {
            headerFormat: '<b>{point.key}</b><br>',
            pointFormat: 'To\'g\'ri javoblar: {point.y}%'
        },
        legend: {
            enabled: false
        },
        plotOptions: {
            column: {
                depth: 25,
                colorByPoint: true,
                colors: colorPalette,
                dataLabels: {
                    enabled: true,
                    format: '{y}%',
                    style: {
                        fontSize: '13px',
                        fontWeight: 'bold',
                        color: '#333',
                        textOutline: 'none'
                    }
                }
            }
        },
        series: [{
            name: 'To\'g\'ri javob foizi',
            data: classQuizPerformanceData
        }]
    });

    function showValues3d() {
        document.getElementById('alpha-value').innerHTML = chart3d.options.chart.options3d.alpha;
        document.getElementById('beta-value').innerHTML = chart3d.options.chart.options3d.beta;
        document.getElementById('depth-value').innerHTML = chart3d.options.chart.options3d.depth;
    }

    document.querySelectorAll('#sliders input').forEach(input =>
        input.addEventListener('input', e => {
            chart3d.options.chart.options3d[e.target.id] = parseFloat(e.target.value);
            showValues3d();
            chart3d.redraw(false);
        })
    );

    showValues3d();
</script>
@endsection