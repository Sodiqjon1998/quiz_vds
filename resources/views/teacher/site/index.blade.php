@extends('teacher.layouts.main') {{-- Sizning asosiy admin layoutingiz --}}

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Admin Bosh Sahifasi</h1>

        <div class="row">

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Diskda Bo'sh Joy</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if (isset($diskSpace['available']))
                                        {{ $diskSpace['available'] }} qoldi ({{ $diskSpace['usage_percent'] }} ishlatilgan)
                                    @else
                                        Ma'lumot mavjud emas
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hdd fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Diskda Band Joy</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if (isset($diskSpace['used']))
                                        {{ $diskSpace['used'] }} ishlatildi ({{ $diskSpace['usage_percent'] }} ishlatilgan)
                                    @else
                                        Ma'lumot mavjud emas
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hdd fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>

            <button id="play-pause-button" title="play" class="fa fa-play">Play/Pause</button>

            {{-- min va max qiymatlar JavaScript tomonidan dinamik o'rnatiladi --}}
            <input type="range" id="play-range" value="" step="1" style="width: 80%;">
            <span id="current-month-display" style="font-size: 1.2em; margin-left: 10px;"></span>

            {{-- Boshqa statistikalar va kontent --}}
        </div>
    </div>

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>

    <script>
        // Backenddan kelgan ma'lumotlarni JavaScriptga o'tkazish
        const classesData = @json($allClasses); // Sinf nomlarini olish uchun
        const studentsData = @json($studentsByClassAndMonth); // Har oy va sinf bo'yicha o'quvchilar soni

        const btn = document.getElementById('play-pause-button');
        const input = document.getElementById('play-range');
        const currentMonthDisplay = document.getElementById('current-month-display'); // Yangi element
        const nbr = classesData.length > 0 ? classesData.length : 10; // Qancha sinf ko'rsatilishini nazorat qilish

        let dataset, chart;
        let sortedMonthKeys = Object.keys(studentsData).sort(); // "YYYY-MM" formatdagi oylarni tartiblash

        // Range input min/max/value ni sozlash
        if (input) {
            input.min = 0; // Birinchi oy indeksidan boshlaymiz
            input.max = sortedMonthKeys.length - 1; // Oxirgi oy indeksiga qadar
            input.value = 0; // Dastlabki qiymatni birinchi oyga sozlash
        }

        /*
         * Animate dataLabels functionality (oldingi koddan o'zgartirilmasdan qoladi)
         */
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
                        Math.round(startValue + (endValue - startValue) * this.pos),
                        0
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
                                if (
                                    hash &&
                                    hash.text !== undefined &&
                                    chart.isResizing === 0
                                ) {
                                    const text = hash.text;
                                    delete hash.text;
                                    return this
                                        .attr(hash)
                                        .animate({
                                            text
                                        });
                                }
                                return attr.apply(this, arguments);
                            })
                        )
                    );
                }
                const ret = proceed.apply(
                    this,
                    Array.prototype.slice.call(arguments, 1)
                );
                this.points.forEach(p =>
                    (p.dataLabels || []).forEach(d => (d.attr = attr))
                );
                return ret;
            });
        }(Highcharts));


        // Ma'lumotlarni tanlangan oy bo'yicha filtrlash va tartiblash
        function getDataForMonth(monthKey) {
            const monthData = studentsData[monthKey] || {}; // Tanlangan oyning ma'lumotlari
            const output = Object.entries(monthData)
                .map(entry => {
                    const [className, studentCount] = entry;
                    return [className, studentCount || 0]; // Agar qiymat null bo'lsa, 0 qaytaring
                })
                .sort((a, b) => b[1] - a[1]); // O'quvchilar soni bo'yicha kamayish tartibida saralash

            // Eng ko'p o'quvchiga ega bo'lgan nbr ta sinfni qaytarish
            return [output[0], output.slice(0, nbr)]; // Top 20 yoki barcha sinflar (nbr ga qarab)
        }

        // Subtitle ni yangilash funksiyasi
        function getSubtitle() {
            const currentMonthIndex = parseInt(input.value);
            const currentMonthKey = sortedMonthKeys[currentMonthIndex];
            if (!currentMonthKey) return '';

            const date = new Date(currentMonthKey);
            const monthName = date.toLocaleString('uz-UZ', {
                month: 'long',
                year: 'numeric'
            }); // O'zbek tilida oy nomi va yili

            const currentMonthData = studentsData[currentMonthKey];
            let totalStudentsInMonth = 0;
            if (currentMonthData) {
                Object.values(currentMonthData).forEach(count => {
                    totalStudentsInMonth += (count || 0);
                });
            }

            return `<span style="font-size: 80px">${monthName}</span>
                <br>
                <span style="font-size: 22px">
                    Jami o'quvchilar: <b>${totalStudentsInMonth}</b>
                </span>`;
        }


        (async () => {
            // Dastlabki dataset endi bevosita studentsData dan olinadi
            dataset = studentsData;

            chart = Highcharts.chart('container', {
                chart: {
                    animation: {
                        duration: 500
                    },
                    marginRight: 50
                },
                title: {
                    text: 'Har oy sinflardagi o\'quvchilar soni',
                    align: 'left'
                },
                subtitle: {
                    text: getSubtitle(),
                    floating: true,
                    align: 'right',
                    verticalAlign: 'middle',
                    useHTML: true,
                    y: -80,
                    x: -100
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
                        animation: false,
                        groupPadding: 0,
                        pointPadding: 0.1,
                        borderWidth: 0,
                        colorByPoint: true,
                        dataSorting: {
                            enabled: true,
                            matchByName: true
                        },
                        type: 'bar',
                        dataLabels: {
                            enabled: true
                        }
                    }
                },
                series: [{
                    type: 'bar',
                    name: 'O\'quvchilar soni', // Bu yerda nom ko'proq dinamik bo'lishi mumkin
                    data: getDataForMonth(sortedMonthKeys[0])[1] // Dastlabki oy ma'lumotlari
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

            // Slayder vaqtini boshqarish
            updateMonthDisplay(); // Joriy oyni ko'rsatish
            update(0); // Dastlabki grafikni yuklash
        })();

        /*
         * Play/Pause funksiyalari (yillar o'rniga oylar bo'yicha ishlaydi)
         */
        function pause(button) {
            button.title = 'play';
            button.className = 'fa fa-play';
            clearTimeout(chart.sequenceTimer);
            chart.sequenceTimer = undefined;
        }

        function updateMonthDisplay() {
            const currentMonthIndex = parseInt(input.value);
            const currentMonthKey = sortedMonthKeys[currentMonthIndex];
            if (currentMonthDisplay && currentMonthKey) {
                const date = new Date(currentMonthKey);
                currentMonthDisplay.textContent = date.toLocaleString('uz-UZ', {
                    month: 'long',
                    year: 'numeric'
                });
            }
        }

        function update(increment) {
            if (increment) {
                input.value = parseInt(input.value, 10) + increment;
            }
            if (input.value >= sortedMonthKeys.length - 1) { // Oxirgi oyga yetib kelsa
                pause(btn);
            }
            updateMonthDisplay(); // Oy nomini yangilash

            const currentMonthKey = sortedMonthKeys[parseInt(input.value)];

            // Chartni yangilash
            chart.update({
                    subtitle: {
                        text: getSubtitle()
                    },
                    series: [{
                        name: 'O\'quvchilar soni (' + currentMonthKey +
                        ')', // Seriya nomi dinamik bo'lishi mumkin
                        data: getDataForMonth(currentMonthKey)[1]
                    }]
                },
                true, // Redraw true
                false // Animation false (chunki biz o'zimiz animatsiya ishlatamiz)
            );
        }

        function play(button) {
            button.title = 'pause';
            button.className = 'fa fa-pause';
            chart.sequenceTimer = setInterval(function() {
                update(1);
            }, 1000); // Har soniyada yangilash
        }

        if (btn) {
            btn.addEventListener('click', function() {
                if (chart.sequenceTimer) {
                    pause(this);
                } else {
                    if (parseInt(input.value) >= sortedMonthKeys.length - 1) {
                        // Agar oxirgi oyda bo'lsa, boshiga qaytaramiz
                        input.value = 0;
                        update(0); // Grafikni birinchi oyga o'tkazamiz
                    }
                    play(this);
                }
            });
        }

        if (input) {
            input.addEventListener('input', function() {
                update(0); // Slayder surilganda grafikni yangilash
                pause(btn); // Slayder surilganda avtomatik o'yinni to'xtatish
            });
        }
    </script>
@endsection
