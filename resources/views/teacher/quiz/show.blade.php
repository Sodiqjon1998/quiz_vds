@extends('teacher.layouts.main')

@use(App\Models\Question)
@use(App\Models\Teacher\Quiz) {{-- To'g'ri model nomi App\Models\Quiz bo'lishi kerak, App\Models\Teacher\Quiz emas agar shu joylashuvda bo'lsa --}}
@use(App\Models\User)

@section('content')
    <style>
        table,
        tr,
        th,
        td {
            padding: 7px !important;
            font-size: 12px;
        }

        /* MathJax kontentini o'rash uchun qo'shimcha stil */
        .math-content {
            border: 1px dashed #ddd;
            padding: 10px;
            margin-top: 5px;
            background-color: #fdfdfd;
            overflow-x: auto;
            /* Uzun formulalar uchun gorizontal scroll */
            border-radius: 5px;
        }

        /* YANGI: Sahifa layouti uchun flexbox */
        .page-content-wrapper {
            display: flex;
            gap: 20px;
            /* Asosiy kontent va navigatsiya orasidagi bo'shliq */
            align-items: flex-start;
            /* Kontent yuqoridan boshlanishi uchun */
        }

        .main-content-area {
            flex: 3;
            /* Asosiy kontent ko'proq joy egallaydi */
            min-width: 0;
            /* Flex item ichida overflow bo'lmasligi uchun */
        }

        .sidebar-navigation {
            flex: 1;
            /* Navigatsiya kamroq joy egallaydi */
            min-width: 250px;
            /* Minimal kenglik */
            max-width: 300px;
            /* Maksimal kenglik */
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 30px;
            /* Yuqoridan qancha masofada turishi */
            margin-top: 0;
            /* Umumiy layoutga moslashamiz */
        }

        /* Test navigatsiyasi stillari */
        .test-navigation-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(50px, 1fr));
            gap: 10px;
            justify-items: center;
        }

        .test-navigation-item {
            width: 50px;
            height: 50px;
            display: flex;
            align-items: center;
            justify-content: center;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            color: #333;
            text-decoration: none;
            transition: all 0.2s ease;
            cursor: pointer;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        }

        .test-navigation-item:hover {
            background-color: #f0f0f0;
            border-color: #aaa;
        }

        .test-navigation-item.active {
            background-color: #ffc107;
            /* Rasmga o'xshash sariq rang */
            border-color: #ffc107;
            color: #fff;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
            transform: translateY(-2px);
        }

        /* Scrollspy tomonidan qo'shiladigan stil (JavaScript orqali) */
        .test-navigation-item.current-question {
            background-color: #007bff;
            /* Moviy rang, joriy savol uchun */
            border-color: #007bff;
            color: #fff;
        }
    </style>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.quiz.index') }}">Quizlar</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.quiz.show', $model->id) }}">{{ $model->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Savollar</li>
        </ol>
    </nav>

    <div class="page-content-wrapper">
        {{-- Asosiy kontent maydoni --}}
        <div class="main-content-area">
            <div class="card">
                <div class="card-header">
                    Quiz: **{{ $model->name }}**
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped table-hover table-sm text-center"
                        style="border: 1px solid rgb(201, 198, 198);">
                        <tr>
                            <th>Id</th>
                            <td>{{ $model->id }}</td>
                        </tr>
                        <tr>
                            <th>Quiz nomi</th>
                            <td>{{ $model->name }}</td>
                        </tr>
                        <tr>
                            <th>Status</th>
                            <td>
                                {{ Quiz::getStatus($model->status) }}
                            </td>
                        </tr>
                        <tr>
                            <th>Yaratilgan</th>
                            <td>{{ $model->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Yangilangan</th>
                            <td>{{ $model->updated_at->format('Y-m-d H:i') }}</td>
                        </tr>
                        <tr>
                            <th>Yaratuvchi</th>
                            <td>{{ User::find($model->created_by)->name ?? 'Noma\'lum' }}</td>
                        </tr>
                        <tr>
                            <th>Yangilovchi</th>
                            <td>{{ User::find($model->updated_by)->name ?? 'Noma\'lum' }}</td>
                        </tr>
                    </table>

                    <a href="{{ route('teacher.question.create', ['id' => $model->id]) }}"
                        class="btn btn-success mt-3">Yangi
                        savol qo'shish</a>
                </div>
            </div>
            <br>
            <br>
            <hr>
            <h1 class="badge bg-label-hover-success text-center" style="font-size: 15px;">Savollar ro'yxati</h1>
            <div class="card">
                <div class="card-body">
                    <div class="row">
                        @forelse ($questions as $k => $question)
                            <div class="col-md-12 mb-4" id="question-{{ $question->id }}"> {{-- Har bir savol uchun to'liq kenglik va ID qo'shildi --}}
                                <div
                                    style="border: 1px solid #c2c0c0; border-radius: 10px; padding: 15px; box-shadow: 3px 4px 8px #b3b2b2">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <h3 class="badge bg-label-hover-primary" style="font-size: 15px;">
                                            {{ $k + 1 }}) {{ $question->question_text }}
                                        </h3>
                                        <div>
                                            <a href="{{ route('teacher.question.edit', $question->id) }}"
                                                class="badge bg-label-info badge-lg rounded-pill me-2">
                                                <i style="font-size: 16px" class="ri-pencil-line"></i> Tahrirlash
                                            </a>
                                            <form action="{{ route('teacher.question.destroy', $question->id) }}"
                                                method="POST" style="display:inline-block;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="badge bg-label-danger badge-lg rounded-pill border-0"
                                                    onclick="return confirm('Haqiqatan ham bu savolni o‘chirmoqchimisiz?');">
                                                    <i style="font-size: 16px" class="ri-delete-bin-line"></i> O'chirish
                                                </button>
                                            </form>
                                        </div>
                                    </div>

                                    <div class="math-content mb-3">
                                        {{ $question->question_text }}
                                    </div>

                                    <h5>Variantlar:</h5>
                                    <div class="row">
                                        @foreach ($question->options as $key => $option)
                                            <div class="col-sm-12 mb-2">
                                                <div class="d-flex align-items-center p-2"
                                                    style="border: 1px dashed #e0e0e0; border-radius: 5px; background-color: {{ $option->is_correct ? '#e6ffe6' : '#f8f8f8' }}">
                                                    <div class="me-2">
                                                        <input type="radio" disabled
                                                            {{ $option->is_correct == 1 ? 'checked' : '' }}>
                                                    </div>
                                                    <div class="flex-grow-1">
                                                        <span
                                                            class="{{ $option->is_correct ? 'fw-bold text-success' : '' }}">
                                                            {{ chr(65 + $key) }}) {{ $option->option_text }}
                                                        </span>
                                                        <div class="math-content">
                                                            {{ $option->option_text }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        @empty
                            <div class="col-12">
                                <p class="text-center text-muted">Bu quizga hali savollar qo'shilmagan.</p>
                                <p class="text-center"><a
                                        href="{{ route('teacher.question.create', ['quiz_id' => $model->id]) }}"
                                        class="btn btn-info">Birinchi savolni qo'shish</a></p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        {{-- O'ng tarafdagi navigatsiya qismi (sidebar) --}}
        <div class="sidebar-navigation">
            <h3 class="mb-4 text-center">Savollar navigatsiyasi</h3>
            <div class="test-navigation-grid">
                @foreach ($questions as $index => $question)
                    <a href="#question-{{ $question->id }}" class="test-navigation-item"
                        data-question-id="{{ $question->id }}" {{-- JavaScript uchun ID ni saqlaymiz --}} title="Savol {{ $index + 1 }}">
                        {{ $index + 1 }}
                    </a>
                @endforeach
            </div>
        </div>
    </div>


    {{-- MathJax skriptini qo'shamiz --}}
    <script type="text/javascript" id="MathJax-script" async
        src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [
                    ['$', '$'],
                    ['\\(', '\\)']
                ],
                displayMath: [
                    ['$$', '$$'],
                    ['\\[', '\\]']
                ]
            },
            svg: {
                fontCache: 'global'
            }
        };

        document.addEventListener('DOMContentLoaded', function() {
            MathJax.typesetPromise();

            const questionItems = document.querySelectorAll('.test-navigation-item');
            const questionSections = document.querySelectorAll(
                '.question-section div[id^="question-"]'); // Savollar divlarini tanlash

            // Navigatsiya tugmalarini bosganda tegishli savolga scroll qilish
            questionItems.forEach(item => {
                item.addEventListener('click', function(e) {
                    e.preventDefault();
                    const targetId = this.getAttribute('href');
                    const targetElement = document.querySelector(targetId);

                    if (targetElement) {
                        // Smooth scroll
                        targetElement.scrollIntoView({
                            behavior: 'smooth',
                            block: 'start' // Yuqori qismga scroll qilish
                        });

                        // Barcha active classlarini olib tashlash
                        questionItems.forEach(navItem => {
                            navItem.classList.remove('active');
                        });
                        // Joriy elementga active classini qo'shish
                        this.classList.add('active');
                    }
                });
            });

            // Scrollspy funksionalligi: Skroll qilinganda qaysi savol ko'rinayotganini aniqlash va navigatsiyani yangilash
            const observerOptions = {
                root: null, // Viewport
                rootMargin: '0px',
                threshold: 0.5 // Elementning 50% ko'rinsa
            };

            const observer = new IntersectionObserver((entries, observer) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        // Barcha active classlarini olib tashlash
                        questionItems.forEach(navItem => {
                            navItem.classList.remove('active');
                        });

                        // Joriy ko'rinayotgan savolga mos keladigan navigatsiya tugmasini topish va unga 'active' classini qo'shish
                        const currentQuestionId = entry.target.id;
                        const correspondingNavItem = document.querySelector(
                            `[data-question-id="${currentQuestionId.replace('question-', '')}"]`
                        );
                        if (correspondingNavItem) {
                            correspondingNavItem.classList.add('active');
                        }
                    }
                });
            }, observerOptions);

            // Har bir savol seksiyasini kuzatish
            // Sizning savollaringiz `.col-md-12` divlarida ekan, ularga ID qo'shdim
            document.querySelectorAll('div[id^="question-"]').forEach(section => {
                observer.observe(section);
            });
        });
    </script>
@endsection
