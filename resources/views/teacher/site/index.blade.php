@extends('teacher.layouts.main') {{-- Sizning asosiy admin layoutingiz --}}

<style>
    <style>

    /* Umumiy sozlamalar */
    body {
        font-family: 'Inter', sans-serif;
        /* Zamonaviyroq shrift */
        background-color: #f5f7fa;
        /* Yumshoqroq fon rangi */
        display: flex;
        justify-content: center;
        align-items: flex-start;
        min-height: 100vh;
        margin: 0;
        /* Umumiy marginni olib tashlaymiz, konteynerga padding beramiz */
        padding: 20px;
        /* Umumiy padding */
        box-sizing: border-box;
        /* Padding va border hisobga olinadi */
    }

    .quiz-container {
        display: flex;
        width: 100%;
        /* Kenglikni to'liq olsin */
        max-width: 1300px;
        /* Katta ekranlar uchun maksimal kenglikni oshirish */
        background-color: #ffffff;
        border-radius: 12px;
        /* Yumshoqroq burchaklar */
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        /* Yumshoqroq soya */
        padding: 30px;
        /* Ichki bo'shliqni oshirish */
        flex-direction: row;
        gap: 30px;
        /* Asosiy kontent va navigatsiya orasidagi bo'sh joy */
    }

    /* --- Test Asosiy Kontent Stili --- */
    .quiz-main-content {
        flex-grow: 1;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        /* Kontentni yuqori va pastga tarqatish */
    }

    .question-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 25px;
        /* Bo'sh joyni oshirish */
        padding-bottom: 20px;
        border-bottom: 1px solid #e0e0e0;
        /* To'qroq chegara */
    }

    .question-number-display {
        font-size: 1.4em;
        /* Kichikroq ekranlarda mos keladigan katta shrift */
        font-weight: 700;
        /* Qalinroq */
        color: #333;
    }

    .mark-flag {
        display: flex;
        align-items: center;
        font-size: 1em;
        color: #555;
        cursor: pointer;
        /* Label ustiga bosish mumkinligini bildiradi */
    }

    .mark-flag input[type="checkbox"] {
        margin-right: 8px;
        /* Bo'sh joyni kamaytirish */
        width: 18px;
        /* Kichikroq checkbox */
        height: 18px;
        accent-color: #007bff;
        /* Checkbox rangini o'zgartirish */
        cursor: pointer;
    }

    .question-body {
        background-color: #fdfdfd;
        /* Engilroq fon */
        border: 1px solid #e9ecef;
        /* Yumshoqroq chegara */
        border-radius: 8px;
        /* Yumshoqroq burchaklar */
        padding: 25px;
        margin-bottom: 30px;
        flex-grow: 1;
        text-align: left !important;
        display: flex;
        /* Kontentni markazlashtirish uchun */
        flex-direction: column;
    }

    .question-body p {
        font-size: 1.15em;
        /* Savol matnini kattaroq qilish */
        font-weight: 600;
        margin-top: 0;
        margin-bottom: 25px;
        color: #343a40;
        /* To'qroq matn rangi */
        line-height: 1.6;
        /* Matn qatorlari orasidagi bo'shliq */
        text-align: left !important;
    }

    #options-form {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        justify-content: flex-start;
        flex-grow: 1;
        /* Variantlar bo'sh joyni egallashi uchun */
    }

    .option-item {
        margin-bottom: 15px;
        /* Variantlar orasidagi bo'shliqni oshirish */
        display: flex;
        align-items: flex-start;
        /* Matn ko'p bo'lsa yuqoriga tekislash */
        cursor: pointer;
        padding: 12px 15px;
        /* Kattaroq padding */
        border-radius: 8px;
        transition: background-color 0.2s ease, border-color 0.2s ease;
        border: 1px solid #dee2e6;
        /* Variant chegarasi */
        width: 100%;
        /* To'liq kenglik */
        box-sizing: border-box;
    }

    .option-item:hover {
        background-color: #e9f5ff;
        /* Engilroq ko'k fon */
        border-color: #007bff;
        /* Ko'k chegara */
    }

    .option-item input[type="radio"] {
        margin-right: 15px;
        /* Ko'proq bo'sh joy */
        width: 20px;
        /* Kattaroq radio tugma */
        height: 20px;
        cursor: pointer;
        flex-shrink: 0;
        /* Hajmini kichraytirmasin */
        accent-color: #007bff;
        /* Radio tugma rangini o'zgartirish */
        margin-top: 2px;
        /* Matn bilan tekislash */
    }

    .option-item label {
        font-size: 1.05em;
        /* Kattaroq matn */
        color: #333;
        cursor: pointer;
        flex-grow: 1;
        line-height: 1.5;
    }

    .navigation-buttons {
        display: flex;
        justify-content: space-between;
        padding-top: 20px;
        border-top: 1px solid #e0e0e0;
        margin-top: auto;
        /* Pastki qismga yopishtirish */
    }

    .nav-btn {
        padding: 12px 25px;
        /* Kattaroq padding */
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1em;
        font-weight: 600;
        transition: background-color 0.2s ease, color 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    #previous-page-btn {
        background-color: #6c757d;
        color: #fff;
    }

    #previous-page-btn:hover {
        background-color: #5a6268;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    #next-page-btn {
        background-color: #007bff;
        color: #fff;
    }

    #next-page-btn:hover {
        background-color: #0056b3;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    /* --- Test Navigatsiya Stili --- */
    .quiz-navigation {
        width: 300px;
        /* Navigatsiya bo'limi uchun kenglikni oshirish */
        flex-shrink: 0;
        /* Kichraymasligi uchun */
        padding: 25px;
        background-color: #f8f9fa;
        /* Engilroq fon rangi */
        border-radius: 12px;
        border: 1px solid #e9ecef;
        /* Yumshoqroq chegara */
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .quiz-navigation h3 {
        margin-top: 0;
        margin-bottom: 20px;
        font-size: 1.2em;
        color: #333;
        text-align: center;
        border-bottom: 1px solid #dee2e6;
        padding-bottom: 15px;
        width: 100%;
    }

    .question-grid {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        /* Ko'proq ustunlar, tugmalar kichikroq */
        gap: 10px;
        margin-bottom: 25px;
        width: 100%;
    }

    .question-button {
        width: 50px;
        /* Tugma uchun qat'iy kenglik */
        height: 50px;
        /* Tugma uchun qat'iy balandlik */
        display: flex;
        justify-content: center;
        align-items: center;
        border: 1px solid #ced4da;
        /* Yumshoqroq chegara */
        border-radius: 8px;
        /* Yumshoqroq burchaklar */
        background-color: #f1f3f5;
        /* Engilroq fon */
        color: #495057;
        /* To'qroq matn */
        font-size: 0.95em;
        font-weight: 500;
        cursor: pointer;
        transition: all 0.2s ease-in-out;
        text-decoration: none;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .question-button:hover {
        background-color: #e2e6ea;
        border-color: #aebfd0;
        transform: translateY(-2px);
        /* Engil animatsiya */
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    /* Savol tugmasi holatlari */
    .question-button.not-answered {
        background-color: #f1f3f5;
        border-color: #ced4da;
        color: #495057;
    }

    .question-button.answered {
        background-color: #d4edda;
        border-color: #28a745;
        color: #28a745;
        font-weight: bold;
    }

    .question-button.current-question {
        background-color: #fff3cd;
        border-color: #ffc107;
        color: #333;
        font-weight: bold;
        box-shadow: 0 0 0 3px #ffc107;
        /* Highlight border kattaroq */
        transform: scale(1.05);
        /* Kichik o'sish effekti */
    }

    .question-button.marked-for-review {
        position: relative;
        border-color: #dc3545;
        box-shadow: 0 0 0 3px #dc3545;
    }

    .question-button.marked-for-review::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 0;
        height: 0;
        border-top: 15px solid #dc3545;
        /* Kattaroq bayroq */
        border-left: 15px solid transparent;
        transform: rotate(45deg);
        /* To'g'ri bayroq shakli uchun */
        transform-origin: top left;
        border-top-right-radius: 8px;
        /* Burchakni yumaloq qilish */
    }


    .finish-attempt-btn {
        width: 100%;
        padding: 12px;
        background-color: #17a2b8;
        /* Ko'k-yashil rang */
        color: #fff;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 1.05em;
        font-weight: 600;
        margin-top: auto;
        /* Yuqoriga yopishtirish */
        margin-bottom: 20px;
        /* Vaqt bilan bo'shliq */
        transition: background-color 0.2s ease, box-shadow 0.2s ease;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .finish-attempt-btn:hover {
        background-color: #138496;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15);
    }

    .time-left {
        font-size: 1em;
        color: #555;
        text-align: center;
        width: 100%;
        padding-top: 15px;
        border-top: 1px solid #eee;
    }

    .time-left #time-display {
        font-weight: bold;
        color: #007bff;
        font-size: 1.2em;
        display: block;
        /* Vaqtni alohida qatorga o'tkazish */
        margin-top: 5px;
    }

    /* ===================================== */
    /* ==== MOBIL ADAPTIV STILILARI ==== */
    /* ===================================== */

    /* Kichik qurilmalar (telefonlar, 767px va undan kichik) */
    @media (max-width: 767px) {
        body {
            padding: 10px;
            /* Kichikroq padding */
        }

        .quiz-container {
            flex-direction: column;
            /* Vertikal joylashtirish */
            padding: 15px;
            /* Paddingni kamaytirish */
            gap: 20px;
            /* Bo'sh joyni kamaytirish */
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .quiz-main-content {
            padding-right: 0;
            margin-bottom: 0;
            /* Marginni olib tashlash */
        }

        .quiz-navigation {
            width: auto;
            /* Navigatsiyaga to'liq kenglikni egallashga ruxsat berish */
            margin-left: 0;
            margin-top: 0;
            padding: 15px;
            border-radius: 8px;
            box-shadow: none;
            /* Mobil ekranda soyani olib tashlash */
            border-top: 1px solid #eee;
            /* Yuqoriga chegara qo'shish */
        }

        .question-header {
            flex-direction: column;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 20px;
            padding-bottom: 15px;
        }

        .question-number-display {
            font-size: 1.2em;
        }

        .mark-flag {
            width: 100%;
        }

        .question-body {
            padding: 18px;
            margin-bottom: 20px;
        }

        .question-body p {
            font-size: 1em;
            margin-bottom: 15px;
        }

        .option-item {
            padding: 10px 12px;
            margin-bottom: 10px;
            font-size: 0.95em;
        }

        .option-item input[type="radio"] {
            width: 18px;
            height: 18px;
            margin-right: 10px;
        }

        .question-grid {
            grid-template-columns: repeat(4, 1fr);
            /* Mobil uchun 4 ustun */
            gap: 8px;
            margin-bottom: 20px;
        }

        .question-button {
            width: 45px;
            height: 45px;
            font-size: 0.85em;
            border-radius: 6px;
        }

        .question-button.marked-for-review::before {
            border-top: 12px solid #dc3545;
            border-left: 12px solid transparent;
            border-top-right-radius: 6px;
        }

        .nav-btn {
            padding: 10px 20px;
            font-size: 0.9em;
            border-radius: 6px;
        }

        .finish-attempt-btn {
            padding: 10px;
            font-size: 0.95em;
            border-radius: 6px;
            margin-bottom: 15px;
        }

        .time-left {
            font-size: 0.9em;
        }

        .time-left #time-display {
            font-size: 1.1em;
        }
    }

    /* O'rta qurilmalar (planshetlar, 768px dan 1024px gacha) */
    @media (min-width: 768px) and (max-width: 1024px) {
        .quiz-container {
            padding: 25px;
            max-width: 900px;
            gap: 25px;
        }

        .quiz-navigation {
            width: 280px;
            padding: 20px;
        }

        .question-grid {
            grid-template-columns: repeat(4, 1fr);
            gap: 8px;
        }

        .question-button {
            width: 48px;
            height: 48px;
            font-size: 0.9em;
        }
    }

    /* MathJax uchun stil */
    mjx-container[jax="CHTML"][display="true"] {
        display: inline-block;
        /* Inline-block qilib to'g'ri joylashuv */
        text-align: left !important;
        margin: 1em 0;
        overflow-x: auto;
        /* Matematik formulalar katta bo'lsa scroll qo'shish */
        max-width: 100%;
        /* Konteyner kengligini cheklash */
    }

    mjx-merror {
        display: inline-block;
        color: black;
        background-color: white;
    }
</style>
</style>
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
        const classesData = @json($allClasses);
        const studentsData = @json($studentsByClassAndMonth);

        const btn = document.getElementById('play-pause-button');
        const input = document.getElementById('play-range');
        const currentMonthDisplay = document.getElementById('current-month-display');
        const nbr = classesData.length > 0 ? classesData.length : 10;

        let dataset, chart;
        let sortedMonthKeys = Object.keys(studentsData).sort(); // "YYYY-MM" formatdagi oylarni tartiblash

        // Range input min/max/value ni sozlash
        if (input) {
            input.min = 0;
            input.max = sortedMonthKeys.length - 1;
            input.value = sortedMonthKeys.length - 1; // Default holatda joriy oyga sozlash
        }

        /*
         * Animate dataLabels functionality (o'zgartirilmagan)
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
            const monthData = studentsData[monthKey] || {};
            const output = Object.entries(monthData)
                .map(entry => {
                    const [className, studentCount] = entry;
                    return [className, studentCount || 0];
                })
                .sort((a, b) => b[1] - a[1]);

            return [output[0], output.slice(0, nbr)];
        }

        // Subtitle ni yangilash funksiyasi
        function getSubtitle() {
            const currentMonthIndex = parseInt(input.value);
            const currentMonthKey = sortedMonthKeys[currentMonthIndex];
            if (!currentMonthKey) return '';

            const date = new Date(currentMonthKey +
                '-01'); // Kunni qo'shamiz, chunki faqat yil-oy bo'lsa ba'zi brauzerlarda xato berishi mumkin
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

            return `<span style="font-size: 80px">${monthName}</span>
                <br>
                <span style="font-size: 22px">
                    Jami o'quvchilar: <b>${totalStudentsInMonth}</b>
                </span>`;
        }


        (async () => {
            dataset = studentsData;

            // Dastlabki yuklashda joriy oyning ma'lumotlarini olish
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
                    name: 'O\'quvchilar soni',
                    data: getDataForMonth(initialMonthKey)[
                        1] // Dastlabki oy ma'lumotlari bilan yuklash
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

            updateMonthDisplay(); // Joriy oyni ko'rsatish
            update(0); // Dastlabki grafikni yuklash (joriy oy uchun)
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
                const date = new Date(currentMonthKey + '-01'); // Kunni qo'shamiz
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
            if (parseInt(input.value) >= sortedMonthKeys.length - 1) { // Oxirgi oyga yetib kelsa
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
                        name: 'O\'quvchilar soni (' + currentMonthKey + ')',
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
