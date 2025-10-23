@extends('teacher.layouts.main')

@section('content')
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.7.1/jquery.min.js"></script>
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/export-data.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>

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

        .results-container {
            display: flex;
            width: 100%;
            max-width: 1200px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex-direction: column;
        }

        h4 {
            text-align: center;
            color: #333;
            margin-bottom: 25px;
            border-bottom: 1px solid #eee;
            padding-bottom: 15px;
        }

        .filter-section {
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            border-radius: 6px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }

        .filter-section label {
            font-weight: bold;
            color: #555;
        }

        .filter-section select {
            padding: 8px 12px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 1em;
            background-color: #fff;
            min-width: 150px;
        }

        .filter-section button {
            padding: 8px 15px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.2s ease;
        }

        .filter-section button:hover {
            background-color: #0056b3;
        }

        .students-table-container {
            overflow-x: auto;
        }

        .students-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .students-table th,
        .students-table td {
            border: 1px solid #e0e0e0;
            padding: 12px 15px;
            text-align: left;
            vertical-align: middle;
        }

        .students-table th {
            background-color: #f2f2f2;
            font-weight: bold;
            color: #444;
            text-transform: uppercase;
            font-size: 0.9em;
        }

        .students-table tbody tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        .students-table tbody tr:hover {
            background-color: #e6f7ff;
        }

        .students-table td {
            color: #555;
            font-size: 0.95em;
        }

        .details-button {
            padding: 6px 12px;
            background-color: #28a745;
            color: #fff;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.85em;
            transition: background-color 0.2s ease;
        }

        .details-button:hover {
            background-color: #218838;
        }

        /* --- Modal Stili --- */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 25px;
            border: 1px solid #888;
            border-radius: 8px;
            width: 80%;
            max-width: 700px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            position: relative;
            animation: fadeIn 0.3s ease-out;
        }

        .close-button {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            position: absolute;
            top: 10px;
            right: 20px;
            cursor: pointer;
        }

        .close-button:hover,
        .close-button:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }

        .modal-content h3 {
            margin-top: 0;
            margin-bottom: 20px;
            color: #333;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        .modal-body-details p {
            margin-bottom: 10px;
            color: #444;
        }

        .modal-body-details strong {
            color: #007bff;
        }

        .quiz-attempt-list {
            list-style-type: none;
            padding: 0;
            margin-top: 15px;
            max-height: 300px;
            overflow-y: auto;
            border: 1px solid #eee;
            border-radius: 6px;
            background-color: #fcfcfc;
        }

        .quiz-attempt-item {
            background-color: #e9f7ff;
            border: 1px solid #b3e0ff;
            border-radius: 6px;
            padding: 12px;
            margin-bottom: 10px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.9em;
            cursor: pointer;
            transition: background-color 0.2s ease, border-color 0.2s ease;
        }

        .quiz-attempt-item:last-child {
            margin-bottom: 0;
        }

        .quiz-attempt-item:hover {
            background-color: #d6f0ff;
            border-color: #8acdfd;
        }

        .quiz-attempt-item.selected {
            background-color: #c0e6ff;
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        .attempt-info {
            flex-grow: 1;
        }

        .attempt-score {
            font-weight: bold;
            color: #007bff;
            margin-left: 15px;
            white-space: nowrap;
        }

        /* Chart konteyneri */
        #quizChartContainer {
            min-height: 300px;
            margin-top: 20px;
            border: 1px solid #ddd;
            border-radius: 6px;
            padding: 10px;
            background-color: #fefefe;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }


        /* ===================================== */
        /* ==== MOBIL ADAPTIV STILILARI ==== */
        /* ===================================== */

        @media (max-width: 768px) {
            .results-container {
                padding: 15px;
                width: 100%;
                margin: 10px 0;
            }

            h4 {
                font-size: 1.5em;
                margin-bottom: 20px;
            }

            .filter-section {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
                padding: 10px;
            }

            .filter-section select,
            .filter-section button {
                width: 100%;
            }

            .students-table th,
            .students-table td {
                padding: 8px 10px;
                font-size: 0.85em;
            }

            .details-button {
                padding: 5px 10px;
                font-size: 0.75em;
            }

            .modal-content {
                width: 95%;
                padding: 15px;
            }

            .close-button {
                font-size: 24px;
                top: 5px;
                right: 10px;
            }

            .quiz-attempt-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
                padding: 10px;
            }

            .attempt-score {
                margin-left: 0;
                margin-top: 5px;
            }

            #quizChartContainer {
                min-height: 250px;
                margin-top: 15px;
            }
        }

        @media (min-width: 769px) and (max-width: 1024px) {
            .results-container {
                width: 95%;
                padding: 20px;
            }

            .modal-content {
                width: 90%;
            }

            #quizChartContainer {
                min-height: 300px;
            }
        }
    </style>

    <div class="results-container">
        <h4>O'quvchilarning Test Natijalari</h4>

        <div class="filter-section">
            <label for="subject_filter">Fanni tanlang:</label>
            <select name="subject_id" id="subject_filter">
                <option value="">Barcha fanlar</option>
                {{-- Controllerdan kelgan fanlarni shu yerga joylaymiz --}}
                @foreach ($subjects as $subject)
                    <option value="{{ $subject->id }}" {{ $subjectId == $subject->id ? 'selected' : '' }}>
                        {{ $subject->name }}</option>
                @endforeach
            </select>
            <button id="filter-button">Filtrlash</button>
        </div>

        <div class="students-table-container">
            <table class="students-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>O'quvchi Ismi</th>
                        <th>Email</th>
                        <th>To'g'ri Javoblar</th> {{-- Ball o'rniga to'g'ri javoblar --}}
                        <th>Noto'g'ri Javoblar</th> {{-- Yangi ustun --}}
                        <th>Javobsiz</th> {{-- Yangi ustun --}}
                        <th>Jami Savollar</th> {{-- Yangi ustun --}}
                        <th>Foiz (%)</th> {{-- Yangi ustun --}}
                        <th>Testlar Soni</th>
                        <th>Oxirgi Test Sanasi</th>
                        <th>Amallar</th>
                    </tr>
                </thead>
                <tbody id="student-results-tbody">
                    {{-- Ma'lumotlar bu yerda PHP (Blade) orqali to'ldiriladi --}}
                    @forelse($exams as $exam)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $exam->user->name ?? 'Noma\'lum o\'quvchi' }}</td>
                            <td>{{ $exam->user->email ?? 'Noma\'lum email' }}</td>
                            <td>{{ $exam->correct_answers_count }}</td>
                            <td>{{ $exam->incorrect_answers_count }}</td>
                            <td>{{ $exam->unanswered_questions_count }}</td>
                            <td>{{ $exam->total_questions_in_quiz }}</td>
                            <td>
                                @if ($exam->total_questions_in_quiz > 0)
                                    {{ round(($exam->correct_answers_count / $exam->total_questions_in_quiz) * 100, 2) }}%
                                @else
                                    0%
                                @endif
                            </td>
                            <td>{{ $exam->total_questions_in_quiz }}</td> {{-- Har bir imtihon bitta testni ifodalaydi --}}
                            <td>{{ $exam->created_at->format('Y-m-d H:i') }}</td>
                            <td>
                                <button class="details-button" data-student-id="{{ $exam->user->id ?? '' }}"
                                    data-student-name="{{ $exam->user->name ?? 'Noma\'lum o\'quvchi' }}"
                                    data-exam-id="{{ $exam->id }}" {{-- Modalni ochish uchun exam ID --}}
                                    data-quiz-title="{{ $exam->quiz->title ?? 'Noma\'lum test' }}"
                                    data-subject-id="{{ $exam->subject_id ?? '' }}"
                                    data-correct-answers="{{ $exam->correct_answers_count }}"
                                    data-incorrect-answers="{{ $exam->incorrect_answers_count }}"
                                    data-unanswered-questions="{{ $exam->unanswered_questions_count }}"
                                    data-total-questions="{{ $exam->total_questions_in_quiz }}">
                                    Batafsil
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="text-center">Hech qanday imtihon natijalari topilmadi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Pagination Links --}}
        <div class="d-flex justify-content-center mt-4">
            {{ $exams->links() }}
        </div>
    </div>

    <div id="quizDetailsModal" class="modal">
        <div class="modal-content">
            <span class="close-button">&times;</span>
            <h3 id="modalStudentName"></h3>
            <p><strong>Fani:</strong> <span id="modalSubjectName"></span></p>
            <div class="modal-body-details">
                <p>Quyida test natijalari keltirilgan:</p>
                <div id="quizChartContainer"></div> {{-- Chartni doim ko'rsatish --}}
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            const $quizDetailsModal = $('#quizDetailsModal');
            const $closeButton = $('.modal .close-button');
            const $modalStudentName = $('#modalStudentName');
            const $modalSubjectName = $('#modalSubjectName');
            const $quizChartContainer = $('#quizChartContainer');
            const $subjectFilter = $('#subject_filter');
            const $filterButton = $('#filter-button');

            // "Batafsil" tugmalariga hodisa tinglovchilarni biriktirish
            // Table tbody elementiga event delegation qilish, chunki AJAX orqali yangi qatorlar qo'shilishi mumkin
            $('#student-results-tbody').on('click', '.details-button', function() {
                const $this = $(this);
                const studentName = $this.data('student-name');
                const subjectId = $this.data('subject-id');
                const quizTitle = $this.data('quiz-title');
                const correctAnswers = parseInt($this.data('correct-answers'));
                const incorrectAnswers = parseInt($this.data('incorrect-answers'));
                const unansweredQuestions = parseInt($this.data('unanswered-questions'));
                const totalQuestions = parseInt($this.data('total-questions'));

                const selectedSubjectObj = @json($subjects) - > find(s => s.id == subjectId);
                const subjectName = selectedSubjectObj ? selectedSubjectObj.name : "Noma'lum fan";

                $modalStudentName.text(`${studentName}ning test natijalari`);
                $modalSubjectName.text(subjectName);

                // Chartni chizish uchun ma'lumotlar
                const attemptData = {
                    quizTitle: quizTitle,
                    correct: correctAnswers,
                    wrong: incorrectAnswers,
                    unanswered: unansweredQuestions,
                    total: totalQuestions
                };

                drawDonutChart(attemptData); // Highchartsni chizish
                $quizDetailsModal.css('display', 'flex'); // Modalni ochish
            });

            // Highcharts Donut Chart chizish funksiyasi
            function drawDonutChart(attemptData) {
                if (!attemptData || !Highcharts) {
                    console.error("Highcharts yoki test ma'lumotlari topilmadi.");
                    $quizChartContainer.hide();
                    return;
                }

                const correctAnswers = attemptData.correct;
                const wrongAnswers = attemptData.wrong;
                const unansweredQuestions = attemptData.unanswered; // Javobsiz qolgan savollar

                const chartData = [{
                        name: 'To\'g\'ri javoblar',
                        y: correctAnswers,
                        color: '#28a745'
                    }, // Green
                    {
                        name: 'Noto\'g\'ri javoblar',
                        y: wrongAnswers,
                        color: '#dc3545'
                    }, // Red
                    {
                        name: 'Javobsiz qolgan',
                        y: unansweredQuestions,
                        color: '#ffc107'
                    } // Yellow
                ];

                $quizChartContainer.show(); // Chart konteynerini ko'rsatish

                Highcharts.chart('quizChartContainer', {
                    chart: {
                        plotBackgroundColor: null,
                        plotBorderWidth: null,
                        plotShadow: false,
                        type: 'pie'
                    },
                    title: {
                        text: `"${attemptData.quizTitle}" testi natijasi`,
                        align: 'center'
                    },
                    tooltip: {
                        pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b> ({point.y} ta)'
                    },
                    accessibility: {
                        point: {
                            valueSuffix: '%'
                        }
                    },
                    plotOptions: {
                        pie: {
                            allowPointSelect: true,
                            cursor: 'pointer',
                            dataLabels: {
                                enabled: true,
                                format: '<b>{point.name}</b>: {point.percentage:.1f} %',
                                connectorColor: 'silver'
                            },
                            showInLegend: true,
                            innerSize: '60%'
                        }
                    },
                    series: [{
                        name: 'Natija',
                        data: chartData
                    }],
                    credits: {
                        enabled: false
                    }
                });
            }


            // Modalni yopish funksiyalari
            $closeButton.on('click', () => {
                $quizDetailsModal.hide();
                $quizChartContainer.hide(); // Modal yopilganda chartni ham yashirish
            });

            $(window).on('click', (event) => {
                if ($(event.target).is($quizDetailsModal)) {
                    $quizDetailsModal.hide();
                    $quizChartContainer.hide(); // Modal yopilganda chartni ham yashirish
                }
            });

            // Filtrlash tugmasini bosganda
            $filterButton.on('click', () => {
                const selectedSubjectId = $subjectFilter.val();
                // Filtr tugmasi bosilganda sahifani qayta yuklash (yangi filtr bilan)
                window.location.href = "{{ route('teacher.exam.getResult') }}" + (selectedSubjectId ?
                    "?subject_id=" + selectedSubjectId : "");
            });
        });
    </script>
@endsection
