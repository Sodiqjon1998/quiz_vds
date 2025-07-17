@php
    use App\Models\Option;
    use App\Models\Question;
    use App\Models\Subjects;

    // Natijalarni hisoblash
    $correctAnswersCount = 0;
    $incorrectAnswersCount = 0;
    $totalQuestions = count($examAnswers);

    foreach ($examAnswers as $answer) {
        $option = Option::find($answer->option_id);
        if ($option && $option->is_correct == 1) {
            $correctAnswersCount++;
        } else {
            $incorrectAnswersCount++;
        }
    }
@endphp

@extends('student.layouts.main')

@section('content')
    <style>
        /* Umumiy stil */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8f9fa;
            color: #333;
            line-height: 1.6;
        }

        .container-custom {
            max-width: 960px;
            margin: 40px auto;
            padding: 30px;
            background-color: #ffffff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.08);
        }

        /* Natijalar sarlavhasi */
        .results-header {
            text-align: center;
            margin-bottom: 40px;
            padding-bottom: 20px;
            border-bottom: 2px solid #eee;
        }

        .results-header h1 {
            font-size: 2.8em;
            color: #007bff;
            margin-bottom: 10px;
            font-weight: 700;
        }

        .results-header p {
            font-size: 1.2em;
            color: #666;
        }

        /* Natijalar xulosasi kartochkalari */
        .summary-cards {
            display: flex;
            justify-content: space-around;
            gap: 20px;
            margin-bottom: 40px;
            flex-wrap: wrap;
        }

        .summary-card {
            background-color: #f1f7fe;
            border-radius: 10px;
            padding: 25px;
            text-align: center;
            flex: 1;
            min-width: 200px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s ease;
        }

        .summary-card:hover {
            transform: translateY(-5px);
        }

        .summary-card h4 {
            font-size: 1.3em;
            color: #555;
            margin-bottom: 10px;
        }

        .summary-card .count {
            font-size: 2.5em;
            font-weight: 700;
            margin-bottom: 5px;
        }

        .summary-card.correct .count {
            color: #28a745;
            /* Yashil */
        }

        .summary-card.incorrect .count {
            color: #dc3545;
            /* Qizil */
        }

        .summary-card.total .count {
            color: #007bff;
            /* Moviy */
        }

        /* Har bir savol natijasi */
        .question-result-block {
            background-color: #ffffff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.06);
        }

        .question-result-block .question-number {
            font-size: 1.4em;
            font-weight: 700;
            color: #007bff;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .question-result-block .question-text {
            font-size: 1.15em;
            font-weight: 500;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .option-display {
            display: flex;
            align-items: flex-start;
            padding: 12px 15px;
            margin-bottom: 10px;
            border-radius: 8px;
            font-size: 1em;
            transition: background-color 0.2s ease;
        }

        .option-display:last-child {
            margin-bottom: 0;
        }

        .option-display .icon {
            margin-right: 15px;
            font-size: 1.2em;
            min-width: 20px;
            /* Ikonka uchun joy ajratish */
            text-align: center;
        }

        .option-display.correct-answer {
            background-color: #d4edda;
            /* Ochiq yashil */
            border: 1px solid #28a745;
            color: #28a745;
        }

        .option-display.correct-answer .icon {
            color: #28a745;
        }

        .option-display.user-selected-incorrect {
            background-color: #f8d7da;
            /* Ochiq qizil */
            border: 1px solid #dc3545;
            color: #dc3545;
        }

        .option-display.user-selected-incorrect .icon {
            color: #dc3545;
        }

        .option-display.not-selected {
            background-color: #f0f2f5;
            border: 1px solid #e9ecef;
            color: #555;
        }

        /* Font Awesome ikonkalari uchun */
        @import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css');

        /* Responsive dizayn */
        @media (max-width: 768px) {
            .container-custom {
                margin: 20px auto;
                padding: 20px;
            }

            .results-header h1 {
                font-size: 2em;
            }

            .results-header p {
                font-size: 1em;
            }

            .summary-cards {
                flex-direction: column;
                gap: 15px;
            }

            .summary-card {
                min-width: unset;
                width: 100%;
            }

            .summary-card .count {
                font-size: 2em;
            }

            .question-result-block {
                padding: 20px;
            }

            .question-result-block .question-number {
                font-size: 1.2em;
            }

            .question-result-block .question-text {
                font-size: 1em;
            }
        }


        mjx-container[jax="CHTML"][display="true"] {
            display: block;
            text-align: left;
            margin: 1em 0;
        }

        mjx-merror {
            display: inline-block;
            color: black;
            background-color: white;
        }
    </style>

    <div class="container-custom">
        <div class="results-header">
            <h1>Test Natijalari</h1>
            <p>Sizning testdagi ishlashingiz haqida batafsil ma'lumot.</p>
        </div>

        <div class="summary-cards">
            <div class="summary-card total">
                <h4>Umumiy savollar</h4>
                <div class="count">{{ $totalQuestions }}</div>
            </div>
            <div class="summary-card correct">
                <h4>To'g'ri javoblar</h4>
                <div class="count">{{ $correctAnswersCount }}</div>
            </div>
            <div class="summary-card incorrect">
                <h4>Noto'g'ri javoblar</h4>
                <div class="count">{{ $incorrectAnswersCount }}</div>
            </div>
        </div>

        <div class="detailed-results">
            @foreach ($examAnswers as $k => $answer)
                @php
                    $question = Question::find($answer->question_id);
                    $userSelectedOption = Option::find($answer->option_id);
                    $allOptions = Option::where('question_id', $question->id)->get(); // Barcha variantlarni olish
                @endphp

                <div class="question-result-block">
                    <div class="question-number">
                        Savol {{ ++$loop->index }}
                        @if ($userSelectedOption && $userSelectedOption->is_correct == 1)
                            <i class="fas fa-check-circle" style="color: #28a745;"></i>
                        @else
                            <i class="fas fa-times-circle" style="color: #dc3545;"></i>
                        @endif
                    </div>
                    <p class="question-text">\( {!! $question->name !!} \)</p>

                    <div class="options-container">
                        @foreach ($allOptions as $option)
                            @php
                                $isUserSelected = $userSelectedOption && $userSelectedOption->id == $option->id;
                                $isCorrectOption = $option->is_correct == 1;

                                $optionClass = 'not-selected';
                                $iconClass = 'fas fa-circle'; // Default icon

                                if ($isUserSelected) {
                                    if ($isCorrectOption) {
                                        $optionClass = 'selected-correct';
                                        $iconClass = 'fas fa-check-circle';
                                    } else {
                                        $optionClass = 'user-selected-incorrect';
                                        $iconClass = 'fas fa-times-circle';
                                    }
                                } elseif ($isCorrectOption) {
                                    $optionClass = 'correct-answer';
                                    $iconClass = 'fas fa-check-circle';
                                }
                            @endphp
                            <div class="option-display {{ $optionClass }}">
                                <span class="icon"><i class="{{ $iconClass }}"></i></span>
                                {{ $option->name }}
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <script type="text/javascript" id="MathJax-script" async
        src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
@endsection
