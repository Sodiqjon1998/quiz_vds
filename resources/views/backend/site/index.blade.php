@extends('backend.layouts.main')

@section('content')
    <div class="container-fluid">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <div id="circle" style="width: 100%; height: 500px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Highcharts CDN -->
    <script src="https://code.highcharts.com/highcharts.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            
            // Circular animation for pie chart
            (function(H) {
                H.seriesTypes.pie.prototype.animate = function(init) {
                    const series = this,
                        chart = series.chart,
                        points = series.points,
                        { animation } = series.options,
                        { startAngleRad } = series;

                    function fanAnimate(point, startAngleRad) {
                        const graphic = point.graphic,
                            args = point.shapeArgs;

                        if (graphic && args) {
                            graphic
                                .attr({
                                    start: startAngleRad,
                                    end: startAngleRad,
                                    opacity: 1
                                })
                                .animate({
                                    start: args.start,
                                    end: args.end
                                }, {
                                    duration: animation.duration / points.length
                                }, function() {
                                    if (points[point.index + 1]) {
                                        fanAnimate(points[point.index + 1], args.end);
                                    }
                                    if (point.index === series.points.length - 1) {
                                        series.dataLabelsGroup.animate({
                                                opacity: 1
                                            },
                                            void 0,
                                            function() {
                                                points.forEach(point => {
                                                    point.opacity = 1;
                                                });
                                                series.update({
                                                    enableMouseTracking: true
                                                }, false);
                                                chart.update({
                                                    plotOptions: {
                                                        pie: {
                                                            innerSize: '40%',
                                                            borderRadius: 8
                                                        }
                                                    }
                                                });
                                            });
                                    }
                                });
                        }
                    }

                    if (init) {
                        points.forEach(point => {
                            point.opacity = 0;
                        });
                    } else {
                        fanAnimate(points[0], startAngleRad);
                    }
                };
            }(Highcharts));

            // Donut Chart
            Highcharts.chart('circle', {
                chart: {
                    type: 'pie'
                },
                title: {
                    text: 'Foydalanuvchilar Tarkibi',
                    style: {
                        fontSize: '20px',
                        fontWeight: 'bold'
                    }
                },
                subtitle: {
                    text: 'Faol foydalanuvchilar statistikasi'
                },
                tooltip: {
                    headerFormat: '',
                    pointFormat: '<span style="color:{point.color}">\u25cf</span> ' +
                        '<b>{point.name}</b>: {point.y} kishi ({point.percentage:.1f}%)'
                },
                accessibility: {
                    point: {
                        valueSuffix: '%'
                    }
                },
                plotOptions: {
                    pie: {
                        allowPointSelect: true,
                        borderWidth: 2,
                        cursor: 'pointer',
                        dataLabels: {
                            enabled: true,
                            format: '<b>{point.name}</b><br>{point.percentage:.1f}%',
                            distance: 20,
                            style: {
                                fontSize: '13px'
                            }
                        }
                    }
                },
                credits: {
                    enabled: false
                },
                series: [{
                    enableMouseTracking: false,
                    animation: {
                        duration: 2000
                    },
                    colorByPoint: true,
                    data: [{
                        name: 'Adminlar',
                        y: {{ $admins }},
                        color: '#5DA5DA'
                    }, {
                        name: 'O\'qituvchilar',
                        y: {{ $teachers }},
                        color: '#6B4C9A'
                    }, {
                        name: 'Koordinatorlar',
                        y: {{ $koordinators }},
                        color: '#60BD68'
                    }, {
                        name: 'O\'quvchilar',
                        y: {{ $students }},
                        color: '#F17CB0'
                    }]
                }]
            });
        });
    </script>
@endsection