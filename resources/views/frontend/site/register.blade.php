@extends('frontend.layouts.main')

@php use App\Models\Classes; @endphp

<?php

$class = Classes::where('status', '=', Classes::STATUS_ACTIVE)->get();
?>

@section('content')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f0f0;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            /* Kichikroq ekranlarda chetlardan joy qoldirish uchun */
            box-sizing: border-box;
            /* Padding va border elementning umumiy o'lchamiga kiritilsin */
        }

        .form-container {
            background-color: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
            /* Maksimal kenglik belgilash */
            box-sizing: border-box;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 30px;
            font-size: 1.8em;
        }

        .input-group {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            overflow: hidden;
            /* Chegaradan chiqib ketishni oldini olish uchun */
        }

        /* Validatsiya xatoligi bo'lganda input-group uchun chegara rangi */
        .input-group.is-invalid {
            border-color: #dc3545;
            /* Qizil rang */
        }

        .input-group .icon {
            background-color: #f9f9f9;
            padding: 12px 15px;
            color: #888;
            border-right: 1px solid #eee;
            display: flex;
            /* Ikonani vertikal markazlash uchun */
            align-items: center;
            justify-content: center;
        }

        .input-group input,
        .input-group select {
            flex-grow: 1;
            /* Qolgan bo'sh joyni egallash */
            border: none;
            padding: 12px;
            font-size: 1em;
            outline: none;
            /* Fokusda paydo bo'ladigan tashqi chiziqni o'chirish */
            width: 100%;
            /* Kichik ekranlar uchun */
            box-sizing: border-box;
        }

        .input-group input::placeholder {
            color: #aaa;
        }

        .name-group {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            gap: 15px;
            /* Ikki yarim kenglikdagi kiritish maydonlari orasidagi bo'sh joy */
        }

        .name-group .input-group {
            flex: 1;
            /* Ikkala maydonning teng joylashishini ta'minlash */
            margin-bottom: 0;
            /* Yuqoridagi input-group bilan bir xil bo'sh joy bo'lmasligi uchun */
        }

        /* Name group ichidagi input-group uchun xatolik stilini to'g'irlash */
        .name-group .input-group.is-invalid {
            border-color: #dc3545;
        }

        .name-group .half-width {
            width: 100%;
            /* Kichik ekranlarda to'liq kenglik olishi uchun */
        }

        .gender-group {
            margin-bottom: 20px;
            display: flex;
            gap: 20px;
            /* Radio tugmalari orasidagi bo'sh joy */
            flex-wrap: wrap;
            /* Kichik ekranlarda yangi qatorga o'tishi uchun */
        }

        .gender-group label {
            display: flex;
            align-items: center;
            cursor: pointer;
        }

        .gender-group input[type="radio"] {
            margin-right: 8px;
            /* Radio tugmachasining standart ko'rinishini o'zgartirish (qo'shimcha stil uchun) */
            -webkit-appearance: none;
            -moz-appearance: none;
            appearance: none;
            width: 18px;
            height: 18px;
            border: 2px solid #ccc;
            border-radius: 50%;
            outline: none;
            transition: all 0.2s ease-in-out;
            position: relative;
            top: 1px;
        }

        .gender-group input[type="radio"]:checked {
            background-color: #007bff;
            /* Tanlanganda ko'k rang */
            border-color: #007bff;
        }

        /* Radio tugmachasining ichidagi doira */
        .gender-group input[type="radio"]:checked::before {
            content: '';
            display: block;
            width: 8px;
            height: 8px;
            background-color: #fff;
            border-radius: 50%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        /* Radio va checkbox guruhlari uchun maxsus xatolik stili (agar kerak bo'lsa) */
        .gender-group.is-invalid-border,
        .terms-checkbox.is-invalid-checkbox {
            border: 1px solid #dc3545;
            /* Butun guruhga qizil chegara berish */
            padding: 5px;
            /* Chegarani ko'rsatish uchun padding */
            border-radius: 5px;
        }

        .terms-checkbox {
            display: flex;
            align-items: center;
            margin-bottom: 25px;
        }

        .terms-checkbox input[type="checkbox"] {
            margin-right: 10px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .terms-checkbox label {
            font-size: 0.95em;
            color: #555;
            cursor: pointer;
        }

        button[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1.1em;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Xatolik matni stili */
        .text-danger {
            color: #dc3545;
            font-size: 0.85em;
            margin-top: 5px;
            margin-bottom: 15px;
            /* Xatolikdan keyingi element uchun joy */
            display: block;
            /* Yangi qatordan boshlash */
        }

        /* Javob beruvchanlik (Responsive) */
        @media (max-width: 600px) {
            .name-group {
                flex-direction: column;
                /* Kichik ekranlarda ustma-ust joylashishi uchun */
                gap: 20px;
                /* Vertikal bo'sh joy */
            }

            .name-group .input-group {
                margin-bottom: 0;
            }

            .form-container {
                padding: 20px;
                /* Kichik ekranlarda paddingni kamaytirish */
                margin: 10px;
                /* Chetlardan biroz joy qoldirish */
            }

            h1 {
                font-size: 1.5em;
            }
        }

        @media (max-width: 400px) {

            .input-group input,
            .input-group select,
            button[type="submit"] {
                padding: 10px;
                font-size: 0.95em;
            }

            .input-group .icon {
                padding: 10px 12px;
            }
        }
    </style>
    <div class="form-container">
        <h1>Javob beruvchi ro'yxatdan o'tish shakli</h1>

        {{-- Umumiy xatoliklarni ko'rsatish (agar biror maydonga bog'liq bo'lmagan xato bo'lsa) --}}
        @if ($errors->any())
            <div
                style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; border-radius: 5px; padding: 10px; margin-bottom: 20px;">
                <ul style="margin: 0; padding-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('frontend.register.store') }}">
            @csrf
            <div class="name-group">
                <div class="input-group half-width @error('first_name') is-invalid @enderror">
                    <span class="icon"><i class="fas fa-user"></i></span>
                    <input type="text" placeholder="Ism" required name="first_name" value="{{ old('first_name') }}">
                </div>
                <div class="input-group half-width @error('last_name') is-invalid @enderror">
                    <span class="icon"><i class="fas fa-user"></i></span>
                    <input type="text" placeholder="Familiya" required name="last_name" value="{{ old('last_name') }}">
                </div>
            </div>
            {{-- Ism va Familiya uchun alohida error xabarlari --}}
            @error('first_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror
            @error('last_name')
                <span class="text-danger">{{ $message }}</span>
            @enderror

            <div class="input-group @error('phone') is-invalid @enderror">
                <span class="icon"><i class="fas fa-phone"></i></span>
                {{-- Telefon raqami maydoni, oldingi javobimdagi type="email" ni type="tel" ga o'zgartirdim --}}
                <input type="tel" placeholder="Telefon raqam" name="phone" value="{{ old('phone') }}" required>
            </div>
            @error('phone')
                <span class="text-danger">{{ $message }}</span>
            @enderror

            <div class="input-group @error('classes_id') is-invalid @enderror">
                <select required name="classes_id">
                    <option value="">Sinfni tanlang</option>
                    @foreach ($class as $item)
                        <option value="{{ $item->id }}" {{ old('classes_id') == $item->id ? 'selected' : '' }}>
                            {{ $item->name }}</option>
                    @endforeach
                </select>
            </div>
            @error('classes_id')
                <span class="text-danger">{{ $message }}</span>
            @enderror

            <button type="submit">Ro'yxatdan o'tish</button>
        </form>
    </div>
@endsection
