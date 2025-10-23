@extends('teacher.layouts.main')

@section('content')
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 20px;
        }

        .question-container {
            width: 90%;
            max-width: 1000px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .question-header {
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
            margin-bottom: 20px;
            text-align: center;
        }

        .question-header h2 {
            margin: 0;
            color: #333;
            font-size: 1.8em;
        }

        .question-body {
            margin-bottom: 20px;
        }

        .question-body p {
            font-size: 1.2em;
            line-height: 1.6;
            color: #555;
            word-wrap: break-word;
            /* Long words break */
        }

        .options-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .option-item {
            background-color: #f9f9f9;
            border: 1px solid #e0e0e0;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .option-item.correct {
            background-color: #d4edda;
            /* Yashil rang, to'g'ri javob uchun */
            border-color: #28a745;
        }

        .option-item.incorrect {
            background-color: #f8d7da;
            /* Qizil rang, noto'g'ri javob uchun (agar ko'rsatmoqchi bo'lsangiz) */
            border-color: #dc3545;
        }

        .option-item span.label {
            font-weight: bold;
            color: #007bff;
            /* Moviy rang */
            min-width: 25px;
            /* Labelning minimal kengligi */
        }

        .option-item p {
            margin: 0;
            flex-grow: 1;
            font-size: 1.1em;
            color: #444;
            word-wrap: break-word;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #6c757d;
            /* Kulrang */
            color: #fff;
            border-radius: 5px;
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .back-link:hover {
            background-color: #5a6268;
        }

        /* MathJax stillari */
        .math-display,
        .math-inline {
            overflow-x: auto;
            padding: 5px;
            border: 1px dashed #ccc;
            background-color: #fff;
            display: inline-block;
            /* Inline formulalar uchun */
            vertical-align: middle;
        }

        .math-display {
            display: block;
            /* Blok formulalar uchun */
            margin-top: 10px;
            margin-bottom: 10px;
        }


        /* --- MOBIL ADAPTIV STILILARI --- */
        @media (max-width: 768px) {
            .question-container {
                padding: 15px;
                width: 100%;
                margin: 10px 0;
            }

            .option-item {
                flex-wrap: wrap;
                text-align: center;
                justify-content: center;
            }

            .option-item span.label {
                flex-basis: 100%;
                /* Mobil ekranda label to'liq kenglikni olsin */
                margin-bottom: 5px;
            }
        }
    </style>

    <div class="question-container">
        <div class="question-header">
            <h2>Savol tafsilotlari</h2>
        </div>

        @if (session('success'))
            <div class="alert alert-success"
                style="background-color: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger"
                style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                {{ session('error') }}
            </div>
        @endif

        <div class="form-group">
            <label>Quiz nomi:</label>
            <p>{{ $question->quiz->name }}</p> {{-- `quiz` munosabati orqali quiz nomini ko'rsatamiz --}}
        </div>

        <div class="form-group">
            <label>Quiz nomi:</label>
            <p>
                @if ($question->quiz)
                    {{ $question->quiz->name }}
                @else
                    Quiz topilmadi
                @endif
            </p>
        </div>
        <div class="options-section">
            <h3>Variantlar:</h3>
            <ul class="options-list">
                @foreach ($question->options as $index => $option)
                    <li class="option-item @if ($option->is_correct) correct @endif">
                        <span class="label">{{ chr(65 + $index) }}.</span> {{-- A, B, C... variantlar uchun --}}
                        <p id="option-{{ $index }}-text-display">{{ $option->option_text }}</p>
                        <div class="math-preview option-rendered-math"></div>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="form-group">
            <a href="{{ route('teacher.quiz.show', $question->quiz_id) }}" class="back-link">Ortga qaytish</a>
            {{-- Edit qilish uchun tugma --}}
            <a href="{{ route('teacher.question.edit', $question->id) }}" class="submit-btn"
                style="margin-left: 10px;">Savolni tahrirlash</a>
        </div>
    </div>

    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script type="text/javascript" id="MathJax-script" async
        src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script>
        // MathJax konfiguratsiyasi
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
            },
            options: {
                // Yangilashni avtomatik amalga oshirmaslik uchun, faqat kerak bo'lganda typesetPromise() ni chaqiramiz
                // ignoreHtmlClass: 'tex2jax_ignore',
                // processHtmlClass: 'tex2jax_process'
            }
        };

        $(document).ready(function() {
            // Savol matnini MathJax bilan render qilish
            const questionTextDisplay = $('#question-text-display');
            const questionRenderedMath = $('#question-rendered-math');

            questionRenderedMath.text(questionTextDisplay.text()); // Xom matnni o'tkazamiz
            MathJax.typesetPromise([questionRenderedMath.get(0)]).then(function() {
                questionTextDisplay.hide(); // Asl matnni yashiramiz, render qilinganini ko'rsatamiz
            }).catch(function(err) {
                console.error('MathJax rendering error for question:', err);
                questionRenderedMath.html('<span style="color:red;">Error rendering math.</span>');
            });

            // Har bir variant matnini MathJax bilan render qilish
            $('.option-item').each(function(index) {
                const optionTextDisplay = $(this).find(`#option-${index}-text-display`);
                const optionRenderedMath = $(this).find('.option-rendered-math');

                optionRenderedMath.text(optionTextDisplay.text()); // Xom matnni o'tkazamiz
                MathJax.typesetPromise([optionRenderedMath.get(0)]).then(function() {
                    optionTextDisplay.hide(); // Asl matnni yashiramiz
                }).catch(function(err) {
                    console.error('MathJax rendering error for option ' + index + ':', err);
                    optionRenderedMath.html(
                        '<span style="color:red;">Error rendering math.</span>');
                });
            });
        });
    </script>
@endsection
