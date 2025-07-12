@extends('student.layouts.main')

<?php

use App\Models\Student\Student;

?>

@section('content')
    <style>
        /* Umumiy stil */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            color: #333;
            line-height: 1.6;
        }

        .profile-container {
            max-width: 1000px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        /* Breadcrumb stil */
        .breadcrumb {
            background-color: #e9ecef;
            padding: 12px 20px;
            border-radius: 8px;
            margin-bottom: 30px;
            display: flex;
            flex-wrap: wrap;
            font-size: 0.95em;
        }

        .breadcrumb-item+.breadcrumb-item::before {
            content: ">" !important;
            color: #6c757d;
            padding-right: 0.5rem;
            padding-left: 0.5rem;
        }

        .breadcrumb-item a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.2s ease;
        }

        .breadcrumb-item a:hover {
            color: #0056b3;
        }

        .breadcrumb-item.active {
            color: #6c757d;
        }

        /* Profil Header */
        .profile-header {
            display: flex;
            align-items: center;
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
            flex-wrap: wrap;
            /* Mobil uchun */
        }

        .profile-avatar {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 25px;
            border: 4px solid #007bff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .profile-info h1 {
            font-size: 2.5em;
            color: #007bff;
            margin-bottom: 5px;
        }

        .profile-info p {
            font-size: 1.1em;
            color: #666;
            margin-bottom: 0;
        }

        /* Ma'lumot kartochkalari */
        .info-cards-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            margin-bottom: 30px;
        }

        .info-card {
            background-color: #f7f9fc;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            border-left: 5px solid #007bff;
            transition: transform 0.2s ease;
        }

        .info-card:hover {
            transform: translateY(-5px);
        }

        .info-card h4 {
            font-size: 1.2em;
            color: #555;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
        }

        .info-card h4 i {
            margin-right: 10px;
            color: #007bff;
        }

        .info-card p {
            font-size: 1.1em;
            color: #333;
            font-weight: 500;
            margin-bottom: 0;
        }

        /* So'nggi testlar section */
        .recent-tests-section {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px dashed #eee;
        }

        .recent-tests-section h3 {
            font-size: 1.8em;
            color: #007bff;
            margin-bottom: 25px;
            text-align: center;
        }

        .test-item {
            background-color: #f7f9fc;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 18px 25px;
            margin-bottom: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.04);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }

        .test-item:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0, 0, 0, 0.08);
        }

        .test-item div {
            flex: 1;
        }

        .test-item .test-title {
            font-weight: 600;
            color: #333;
            font-size: 1.1em;
        }

        .test-item .test-date {
            font-size: 0.9em;
            color: #777;
        }

        .test-item .test-score {
            font-weight: 700;
            font-size: 1.2em;
            color: #28a745;
            /* Yashil rang, misol uchun */
            text-align: right;
            min-width: 80px;
        }

        .test-item .test-score.fail {
            color: #dc3545;
            /* Qizil rang, misol uchun */
        }

        /* Font Awesome ikonkalari uchun */
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

        /* Responsive dizayn */
        @media (max-width: 768px) {
            .profile-container {
                margin: 20px auto;
                padding: 20px;
            }

            .profile-header {
                flex-direction: column;
                text-align: center;
            }

            .profile-avatar {
                margin-right: 0;
                margin-bottom: 20px;
            }

            .profile-info h1 {
                font-size: 2em;
            }

            .profile-info p {
                font-size: 1em;
            }

            .info-cards-grid {
                grid-template-columns: 1fr;
                /* Kichik ekranlarda bir ustun */
            }

            .test-item {
                flex-direction: column;
                align-items: flex-start;
            }

            .test-item .test-score {
                margin-top: 10px;
                text-align: left;
            }
        }
    </style>

    <div class="profile-container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('student') }}">Bosh sahifa</a></li>
                <li class="breadcrumb-item active" aria-current="page">Mening Profilim</li>
            </ol>
        </nav>

        <div class="profile-header">
            <img src="{{ $student->avatar_url ?? asset('assets/img/avatars/1.png') }}" alt="Profil rasmi"
                class="profile-avatar">
            <div class="profile-info">
                <h1>{{ $student->first_name ?? 'Ism' }} {{ $student->last_name ?? "Familya" }}</h1>
                <p class="text-muted">{{ $student->email ?? 'email@example.com' }}</p>
            </div>
        </div>

        <div class="info-cards-grid">
            <div class="info-card">
                <h4><i class="fas fa-id-badge"></i> Talaba ID</h4>
                <p>{{ $student->id ?? 'N/A' }}</p>
            </div>
            <div class="info-card">
                <h4><i class="fas fa-user"></i> Foydalanuvchi nomi</h4>
                <p>{{ $student->name ?? 'N/A' }}</p>
            </div>
            <div class="info-card">
                <h4><i class="fas fa-graduation-cap"></i> Sinf/Guruh</h4>
                <p>{{ Student::getClassesById($student->classes_id)->name ?? 'N/A' }}</p>
            </div>
            <div class="info-card">
                <h4><i class="fas fa-calendar-alt"></i> Ro'yxatdan o'tgan</h4>
                <p>{{ $student->created_at ? $student->created_at->format('d M Y') : 'N/A' }}</p>
            </div>
        </div>

        {{-- So'nggi test natijalari - Bu qism uchun controllerdan $recentExams ma'lumotlarini uzatishingiz kerak bo'ladi --}}
        @if (isset($recentExams) && $recentExams->count() > 0)
            <div class="recent-tests-section">
                <h3><i class="fas fa-clipboard-list"></i> So'nggi Test Natijalari</h3>
                @foreach ($recentExams as $exam)
                    @php
                        $subjectName = \App\Models\Subjects::getSubjectById($exam->subject_id)->name ?? 'Noma\'lum fan';
                        $totalQuestions = $exam->total_questions; // Bu ma'lumot Exam modelida bo'lishi kerak
                        $correctAnswers = $exam->correct_answers; // Bu ma'lumot Exam modelida bo'lishi kerak
                        $scorePercentage = $totalQuestions > 0 ? round(($correctAnswers / $totalQuestions) * 100) : 0;
                        $scoreClass = $scorePercentage >= 50 ? 'pass' : 'fail'; // Misol uchun, 50% dan yuqori bo'lsa o'tgan
                    @endphp
                    <a href="{{ route('student.user.show', ['id' => $exam->id]) }}" style="text-decoration: none; color: inherit;">
                        <div class="test-item">
                            <div>
                                <div class="test-title">{{ $subjectName }} Testi</div>
                                <div class="test-date">{{ $exam->created_at->format('d M Y H:i') }}</div>
                            </div>
                            <div class="test-score {{ $scoreClass }}">
                                {{ $correctAnswers }}/{{ $totalQuestions }} ({{ $scorePercentage }}%)
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <div class="recent-tests-section text-center text-muted">
                <p><i class="fas fa-info-circle"></i> Hali topshirilgan testlar mavjud emas.</p>
            </div>
        @endif

    </div>
@endsection
