@extends('teacher.layouts.main') {{-- Sizning asosiy admin layoutingiz --}}

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

    /* YANGI QO'SHILGAN STIL */
    .chart-controls {
        display: flex;
        align-items: center;
        width: 100%;
        margin-top: 20px;
        /* Grafikdan biroz pastga */
        padding: 0 10px;
        box-sizing: border-box;
    }

    #play-pause-button {
        width: 50px;
        /* Kattaroq doira */
        height: 50px;
        border-radius: 50%;
        /* Doira shakli */
        background-color: #007bff;
        /* Ko'k rang */
        color: white;
        border: none;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 24px;
        /* Ikona kattaligi */
        cursor: pointer;
        box-shadow: 0 4px 8px rgba(0, 123, 255, 0.3);
        /* Yumshoqroq soya */
        transition: background-color 0.2s ease, box-shadow 0.2s ease, transform 0.2s ease;
        flex-shrink: 0;
        /* Kichraymasligi uchun */
        margin-right: 15px;
        /* Slayderdan uzoqroq turish uchun */
    }

    #play-pause-button:hover {
        background-color: #0056b3;
        box-shadow: 0 6px 12px rgba(0, 123, 255, 0.4);
        transform: translateY(-2px);
        /* Engil animatsiya */
    }

    #play-pause-button .fa {
        /* Ikonka uslubi */
        line-height: 1;
        /* Vertikal hizalanish uchun */
    }

    #play-range {
        flex-grow: 1;
        /* Qolgan bo'sh joyni egallaydi */
        -webkit-appearance: none;
        /* Standart stilni olib tashlash */
        appearance: none;
        height: 8px;
        /* Kalinroq chiziq */
        background: #ddd;
        outline: none;
        border-radius: 5px;
        margin: 0 15px;
        /* Tugma va oy nomidan bo'sh joy */
        cursor: pointer;
    }

    /* Range input tugmachasi (thumb) stili */
    #play-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        /* Kattaroq tugmacha */
        height: 20px;
        border-radius: 50%;
        background: #007bff;
        /* Ko'k rang */
        cursor: pointer;
        margin-top: -6px;
        /* Chiziq ustida joylashishi uchun */
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.4);
    }

    #play-range::-moz-range-thumb {
        width: 20px;
        height: 20px;
        border-radius: 50%;
        background: #007bff;
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 123, 255, 0.4);
    }

    #current-month-display {
        font-size: 1.1em;
        /* Oy nomini kattaroq qilish */
        font-weight: bold;
        color: #555;
        min-width: 120px;
        /* Matn o'zgarganda joy siljimasligi uchun */
        text-align: right;
    }

    /* Mobil moslashuv uchun (agar kerak bo'lsa) */
    @media (max-width: 767px) {
        .chart-controls {
            flex-direction: column;
            /* Mobil ekranda vertikal joylashtirish */
            align-items: center;
            padding: 0;
            margin-top: 30px;
        }

        #play-pause-button {
            width: 60px;
            /* Mobil uchun kattaroq tugma */
            height: 60px;
            font-size: 30px;
            margin-bottom: 15px;
            /* Slayderdan bo'sh joy */
            margin-right: 0;
        }

        #play-range {
            width: 90%;
            /* Mobil uchun kenglik */
            margin: 0;
            margin-bottom: 10px;
        }

        #current-month-display {
            font-size: 1em;
            text-align: center;
        }
    }

    /* YANGI QO'SHILGAN STIL */
    .chart-container-3d {
        margin-top: 40px;
        /* Grafiklar orasidagi bo'shliq */
        background-color: #ffffff;
        border-radius: 12px;
        box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
        padding: 30px;
        position: relative;
        /* Title uchun */
    }

    .chart-container-3d h2 {
        text-align: center;
        margin-bottom: 25px;
        color: #333;
        font-size: 1.5em;
        font-weight: 700;
    }

    /* Sliders stilini o'zgartirish (agar mavjud bo'lsa) */
    #sliders {
        display: flex;
        justify-content: center;
        gap: 20px;
        margin-top: 20px;
        flex-wrap: wrap;
        /* Mobil uchun moslashuv */
    }

    #sliders div {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    #sliders input[type="range"] {
        width: 150px;
        /* Kichikroq slaydchalar */
        -webkit-appearance: none;
        appearance: none;
        height: 6px;
        background: #ddd;
        outline: none;
        border-radius: 3px;
    }

    #sliders input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #007bff;
        cursor: pointer;
        margin-top: -5px;
        box-shadow: 0 1px 3px rgba(0, 123, 255, 0.4);
    }

    #sliders input[type="range"]::-moz-range-thumb {
        width: 16px;
        height: 16px;
        border-radius: 50%;
        background: #007bff;
        cursor: pointer;
        box-shadow: 0 1px 3px rgba(0, 123, 255, 0.4);
    }

    #sliders span {
        font-weight: bold;
        color: #555;
        margin-top: 5px;
    }

    @media (max-width: 767px) {
        #sliders {
            flex-direction: column;
            gap: 15px;
        }

        #sliders input[type="range"] {
            width: 80%;
        }
    }
</style>
@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Admin Bosh Sahifasi</h1>

        <div class="row">

            {{-- Mavjud diskSpace kartalari (izohga olingan) --}}
            {{-- <div class="col-xl-3 col-md-6 mb-4">
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
            </div> --}}

            {{-- Birinchi grafik: Oy bo'yicha o'quvchilar soni --}}
            <div class="card w-100"> {{-- Kenglikni to'liq olsin --}}
                <div class="card-body">
                    <div id="container" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
                </div>


                <div class="chart-controls">
                    <button id="play-pause-button" title="play">
                        {{-- Font Awesome ikonkasini to'g'ridan-to'g'ri joylashtiramiz --}}
                        <i class="fa fa-play"></i>
                    </button>
                    <input type="range" id="play-range" value="" step="1">
                    <span id="current-month-display"></span>
                </div>
            </div>

            {{-- YANGI QO'SHILGAN KOD BOSHLANISHI: Sinflar bo'yicha test yechish foizi grafigi --}}
            <div class="card w-100 chart-container-3d mt-5"> {{-- Yuqoridan biroz bo'sh joy va yangi stil --}}
                <div class="card-body">
                    <h2>Sinflarning test yechishdagi samaradorligi (%)</h2>
                    <div id="container-3d" style="min-width: 310px; height: 400px; margin: 0 auto"></div>
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
            {{-- YANGI QO'SHILGAN KOD TUGASHI: Sinflar bo'yicha test yechish foizi grafigi --}}

            {{-- Boshqa statistikalar va kontent --}}
        </div>
    </div>

    {{-- YANGI QO'SHILGAN KOD BOSHLANISHI: Highcharts 3D moduli --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/data.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/highcharts-more.js"></script>
    <script src="https://code.highcharts.com/highcharts-3d.js"></script> {{-- Highcharts 3D modulini qo'shish --}}
    {{-- YANGI QO'SHILGAN KOD TUGASHI: Highcharts 3D moduli --}}

    {{-- Birinchi grafik uchun JavaScript (mavjud kod) --}}
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

            const date = new Date(currentMonthKey + '-01'); // Kunni qo'shamiz
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

            // Endi ikki qismni alohida uslub bilan qaytaramiz
            return `<span style="font-size: 80px; font-weight: bold; color: #333; display: block; text-align: right;">${currentMonthKey.replace('-', ' M')}</span>
                    <span style="font-size: 22px; color: #555; display: block; text-align: right; margin-top: 5px;">
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
                    align: 'right', // O'ng tomonga hizalash
                    verticalAlign: 'bottom', // Yuqoriga hizalash
                    y: -20, // Grafikka yaqinroq, yuqoriga
                    x: -10, // O'ng chekkadan biroz chapga siljitish
                    useHTML: true,
                    style: { // Yangi stil qo'shish
                        fontSize: '20px', // Asosiy yil/oy matnini biroz kichraytiramiz
                        color: '#888', // Rangini biroz xiralashtiramiz
                        opacity: 0.7 // Shaffofligini kamaytiramiz
                    }
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
                        dataSorting: {
                            enabled: true,
                            matchByName: true
                        },
                        type: 'bar',
                        dataLabels: {
                            enabled: true,
                            format: '{y}', // Faqat qiymatni ko'rsatish
                            style: {
                                fontSize: '14px', // Raqamlar shriftini kichraytirish
                                fontWeight: 'bold', // Qalin qilish
                                color: '#333', // Rangini aniqroq qilish
                                textOutline: 'none' // Matn atrofidagi chiziqni olb tashlash
                            },
                            align: 'right', // Raqamlarni o'ng tomonga hizalash (ustun ichida)
                            x: 20 // Raqamlarni ustun ichida biroz o'ngga siljitish
                        }
                    }
                },
                series: [{
                    type: 'bar',
                    name: 'O\'quvchilar soni',
                    data: getDataForMonth(initialMonthKey)[1] // Dastlabki oy ma'lumotlari bilan yuklash
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
         * Play/Pause funksiyalari (yillar o'rniga oylar bo'yida ishlaydi)
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

        {{-- YANGI QO'SHILGAN KOD BOSHLANISHI: Sinflar bo'yicha to'g'ri javob foizi grafigi JavaScript --}}
        const classQuizPerformanceData = @json($classQuizPerformance);

        // Set up the chart for 3D column
        const chart3d = new Highcharts.Chart({
            chart: {
                renderTo: 'container-3d', // Yangi konteyner IDsi
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
                text: null // Yuqoridagi h2 dan foydalanamiz
            },
            subtitle: {
                text: 'Manba: Test natijalari' // Ixtiyoriy manba matni
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
                    format: '{value}%' // Y o'qida foiz belgisini ko'rsatish
                },
                max: 100, // Maksimal qiymat 100%
                min: 0    // Minimal qiymat 0%
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
                    dataLabels: {
                        enabled: true,
                        format: '{y}%', // Ustunlar ustida foizni ko'rsatish
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
                data: classQuizPerformanceData, // Controllerdan kelgan ma'lumot
                colorByPoint: true // Har bir ustunga alohida rang berish
            }]
        });

        function showValues3d() {
            document.getElementById(
                'alpha-value'
            ).innerHTML = chart3d.options.chart.options3d.alpha;
            document.getElementById(
                'beta-value'
            ).innerHTML = chart3d.options.chart.options3d.beta;
            document.getElementById(
                'depth-value'
            ).innerHTML = chart3d.options.chart.options3d.depth;
        }

        // Activate the sliders for 3D chart
        document.querySelectorAll(
            '#sliders input'
        ).forEach(input => input.addEventListener('input', e => {
            chart3d.options.chart.options3d[e.target.id] = parseFloat(e.target.value);
            showValues3d();
            chart3d.redraw(false);
        }));

        showValues3d();
        {{-- YANGI QO'SHILGAN KOD TUGASHI: Sinflar bo'yicha to'g'ri javob foizi grafigi JavaScript --}}
    </script>
@endsection

