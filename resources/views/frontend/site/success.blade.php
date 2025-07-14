@extends('frontend.layouts.main')

@section('content')
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f8f9fa; /* Ochroq fon */
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }

        .success-container {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 10px; /* Yumaloq burchaklar */
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1); /* Kengroq soya */
            width: 100%;
            max-width: 550px; /* Kattaroq max-width */
            text-align: center;
            box-sizing: border-box;
            animation: fadeIn 0.8s ease-out; /* Animatsiya qo'shildi */
        }

        .success-icon {
            color: #28a745; /* Yashil belgi */
            font-size: 4em; /* Katta belgi */
            margin-bottom: 20px;
        }

        .success-message h2 {
            color: #333;
            font-size: 2em;
            margin-bottom: 10px;
            font-weight: 600; /* Qalinroq shrift */
        }

        .success-message p {
            color: #555;
            font-size: 1.1em;
            line-height: 1.6;
            margin-bottom: 25px;
        }

        .contact-info {
            font-size: 0.95em;
            color: #777;
            margin-top: 20px;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }

        /* Animatsiya */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Responsive dizayn */
        @media (max-width: 600px) {
            .success-container {
                padding: 30px;
                margin: 15px;
            }
            .success-message h2 {
                font-size: 1.7em;
            }
            .success-icon {
                font-size: 3.5em;
            }
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

    <div class="success-container">
        <div class="success-icon">
            <i class="fas fa-check-circle"></i> {{-- Muvaffaqiyat belgisi --}}
        </div>
        <div class="success-message">
            {{-- $user o'zgaruvchisining mavjudligini tekshirish muhim --}}
            @if (isset($user) && $user->name)
                <h2>Tabriklaymiz, {{ $user->name }}!</h2>
            @else
                <h2>Tabriklaymiz!</h2>
            @endif
            <p>Ro'yxatdan o'tishingiz muvaffaqiyatli yakunlandi.</p>
            <p>Tez orada siz bilan **aloqaga chiqamiz!**</p>
        </div>
        <div class="contact-info">
            Savollaringiz bo'lsa, biz bilan bog'laning.
            {{-- Agar telefon raqami yoki email manzili bo'lsa, shu yerga qo'shing --}}
            <p>Aloqa uchun: +998 (77) 103-06-06 | yuksalishlocation.netlify.app</p>
        </div>
    </div>
@endsection
