@extends('teacher.layouts.main')

{{-- Modellar namespace'ini tekshiring. Agar App\Models ichida bo'lsa, "Teacher" ni olib tashlang. --}}
@use(App\Models\Question) {{-- Yoki App\Models\Teacher\Question --}}
@use(App\Models\Teacher\Quiz) {{-- Yoki App\Models\Teacher\Quiz --}}
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
    </style>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher') }}">Bosh sahifa</a></li>
            {{-- Quiz ro'yxatiga qaytish --}}
            <li class="breadcrumb-item"><a href="{{ route('teacher.quiz.index') }}">Quizlar</a></li>
            {{-- Hozirgi quizga qaytish (quiz batafsil sahifasiga) --}}
            {{-- $model bu yerda Quiz modelini ifodalaydi deb taxmin qilinadi --}}
            <li class="breadcrumb-item"><a href="{{ route('teacher.quiz.show', $model->id) }}">{{ $model->name }}</a></li>
            <li class="breadcrumb-item active" aria-current="page">Savollar</li> {{-- Bu sahifa savollar ro'yxati ekanligi uchun --}}
        </ol>
    </nav>


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
                        {{-- Quiz modeli uchun getStatus metodini chaqirish --}}
                        {{ Quiz::getStatus($model->status) }}
                    </td>
                </tr>
                {{-- Quizga tegishli qo'shimcha ma'lumotlarni qo'shishingiz mumkin --}}
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

            {{-- Quizga yangi savol qo'shish tugmasi --}}
            <a href="{{ route('teacher.question.create', ['id' => $model->id]) }}" class="btn btn-success mt-3">Yangi
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
                    <div class="col-md-6 mb-4"> {{-- Har bir savol uchun alohida kolonka --}}
                        <div
                            style="border: 1px solid #c2c0c0; border-radius: 10px; padding: 15px; box-shadow: 3px 4px 8px #b3b2b2">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h3 class="badge bg-label-hover-primary" style="font-size: 15px;">
                                    {{ ++$k }}) {{ $question->question_text }}
                                </h3>
                                <div>
                                    {{-- Savolni tahrirlash --}}
                                    <a href="{{ route('teacher.question.edit', $question->id) }}"
                                        class="badge bg-label-info badge-lg rounded-pill me-2">
                                        <i style="font-size: 16px" class="ri-pencil-line"></i> Tahrirlash
                                    </a>
                                    {{-- Savolni o'chirish (agar kerak bo'lsa, form bilan DELETE so'rovini yuborish kerak) --}}
                                    <form action="{{ route('teacher.question.destroy', $question->id) }}" method="POST"
                                        style="display:inline-block;">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="badge bg-label-danger badge-lg rounded-pill border-0"
                                            onclick="return confirm('Haqiqatan ham bu savolni o‘chirmoqchimisiz?');">
                                            <i style="font-size: 16px" class="ri-delete-bin-line"></i> O'chirish
                                        </button>
                                    </form>
                                </div>
                            </div>

                            {{-- Savol matni MathJax bilan --}}
                            <div class="math-content mb-3">
                                {{ $question->name }}
                            </div>

                            <h5>Variantlar:</h5>
                            <div class="row">
                                {{-- Question modelidan options munosabatini to'g'ridan-to'g'ri chaqiramiz --}}
                                @foreach ($question->options as $key => $option)
                                    <div class="col-sm-12 mb-2">
                                        <div class="d-flex align-items-center p-2"
                                            style="border: 1px dashed #e0e0e0; border-radius: 5px; background-color: {{ $option->is_correct ? '#e6ffe6' : '#f8f8f8' }}">
                                            <div class="me-2">
                                                <input type="radio" disabled
                                                    {{ $option->is_correct == 1 ? 'checked' : '' }}>
                                            </div>
                                            <div class="flex-grow-1">
                                                <span class="{{ $option->is_correct ? 'fw-bold text-success' : '' }}">
                                                    {{ chr(65 + $key) }}) {{ $option->option_text }}
                                                </span>
                                                {{-- Variant matni MathJax bilan --}}
                                                <div class="math-content">
                                                    {{ $option->name }}
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
                        <p class="text-center"><a href="{{ route('teacher.question.create', ['quiz_id' => $model->id]) }}"
                                class="btn btn-info">Birinchi savolni qo'shish</a></p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    {{-- MathJax skriptini qo'shamiz (Agar sizda global layoutda qo'shilmagan bo'lsa) --}}
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
        });
    </script>
@endsection
