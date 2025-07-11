@extends('student.layouts.main')

@section('content')
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            /* Yuqoriga tekislash yaxshiroq joylashuv uchun */
            min-height: 100vh;
            margin: 20px;
            /* Konteyner atrofida biroz chekka qo'shish */
        }

        .quiz-container {
            display: flex;
            width: 90%;
            /* Kerak bo'lganda sozlang */
            max-width: 1200px;
            /* Katta ekranlar uchun maksimal kenglik */
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 20px;
            flex-direction: row;
            /* Katta ekranlar uchun sukut bo'yicha */
        }

        /* --- Test Asosiy Kontent Stili --- */
        .quiz-main-content {
            flex-grow: 1;
            /* Qolgan bo'sh joyni egallaydi */
            padding-right: 30px;
            /* Navigatsiyadan bo'sh joy */
            display: flex;
            flex-direction: column;
        }

        .question-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 1px solid #eee;
        }

        .question-number-display {
            font-size: 1.2em;
            font-weight: bold;
            color: #333;
        }

        .mark-flag {
            display: flex;
            align-items: center;
            font-size: 0.9em;
            color: #555;
        }

        .mark-flag input[type="checkbox"] {
            margin-right: 5px;
            width: 16px;
            height: 16px;
            cursor: pointer;
        }

        .question-body {
            background-color: #fcfcfc;
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            margin-bottom: 25px;
            flex-grow: 1;
            /* Kengayishiga imkon beradi */
        }

        .question-body p {
            font-size: 1.1em;
            font-weight: 600;
            margin-top: 0;
            margin-bottom: 20px;
            color: #444;
        }

        #options-form {
            display: flex;
            flex-direction: column;
        }

        .option-item {
            margin-bottom: 12px;
            display: flex;
            align-items: center;
            cursor: pointer;
            padding: 10px;
            border-radius: 5px;
            transition: background-color 0.2s ease;
        }

        .option-item:hover {
            background-color: #f0f0f0;
        }

        .option-item input[type="radio"] {
            margin-right: 12px;
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .option-item label {
            font-size: 1em;
            color: #333;
            cursor: pointer;
            flex-grow: 1;
        }

        .navigation-buttons {
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }

        .nav-btn {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.2s ease, color 0.2s ease;
        }

        #previous-page-btn {
            background-color: #6c757d;
            /* Kulrang */
            color: #fff;
        }

        #previous-page-btn:hover {
            background-color: #5a6268;
        }

        #next-page-btn {
            background-color: #007bff;
            /* Moviy */
            color: #fff;
        }

        #next-page-btn:hover {
            background-color: #0056b3;
        }


        /* --- Test Navigatsiya Stili --- */
        .quiz-navigation {
            width: 250px;
            /* Navigatsiya bo'limi uchun qat'iy kenglik */
            margin-left: 30px;
            /* Asosiy kontent va navigatsiya orasidagi bo'sh joy */
            padding: 15px;
            background-color: #fff;
            border-radius: 8px;
            border: 1px solid #ddd;
            box-shadow: 0 1px 5px rgba(0, 0, 0, 0.05);
        }

        .quiz-navigation h3 {
            margin-top: 0;
            margin-bottom: 15px;
            font-size: 1.1em;
            color: #333;
        }

        .question-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            /* Katta ekranlar uchun 4 ustun */
            gap: 8px;
            /* Tugmalar orasidagi bo'sh joy */
            margin-bottom: 20px;
        }

        .question-button {
            width: 40px;
            /* Tugma uchun qat'iy kenglik */
            height: 40px;
            /* Tugma uchun qat'iy balandlik */
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #ccc;
            border-radius: 4px;
            background-color: #f9f9f9;
            color: #555;
            font-size: 0.9em;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
            text-decoration: none;
            /* Havolalar uchun tag chiziqni olib tashlash */
        }

        .question-button:hover {
            background-color: #e9e9e9;
            border-color: #aaa;
        }

        /* Savol tugmasi holatlari */
        .question-button.not-answered {
            background-color: #f9f9f9;
            border-color: #ccc;
            color: #555;
        }

        .question-button.answered {
            background-color: #d4edda;
            /* Ochiq yashil */
            border-color: #28a745;
            /* To'q yashil */
            color: #28a745;
            font-weight: bold;
        }

        .question-button.current-question {
            background-color: #fff3cd;
            /* Ochiq sariq */
            border-color: #ffc107;
            /* To'q sariq/Orange */
            color: #333;
            font-weight: bold;
            box-shadow: 0 0 0 2px #ffc107;
            /* Highlight border */
        }

        .question-button.marked-for-review {
            position: relative;
            border-color: #dc3545;
            /* Qizil */
            box-shadow: 0 0 0 2px #dc3545;
            /* Qizil highlight */
        }

        .question-button.marked-for-review::before {
            content: '';
            position: absolute;
            top: -2px;
            /* Bayroqni joylashtirish uchun sozlang */
            right: -2px;
            /* Bayroqni joylashtirish uchun sozlang */
            width: 0;
            height: 0;
            border-top: 8px solid #dc3545;
            /* Qizil uchburchak */
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            transform: rotate(45deg);
            /* Uni bayroq shakliga aylantirish uchun aylantiring */
            transform-origin: top left;
        }


        .finish-attempt-btn {
            width: 100%;
            padding: 10px;
            background-color: #007bff;
            color: #fff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin-bottom: 15px;
            transition: background-color 0.2s ease;
        }

        .finish-attempt-btn:hover {
            background-color: #0056b3;
        }

        .time-left {
            font-size: 0.9em;
            color: #555;
            text-align: center;
        }

        .time-left #time-display {
            font-weight: bold;
            color: #007bff;
        }

        /* ===================================== */
        /* ==== MOBIL ADAPTIV STILILARI ==== */
        /* ===================================== */

        /* Kichik qurilmalar (telefonlar, 600px va undan kichik) */
        @media (max-width: 768px) {
            .quiz-container {
                flex-direction: column;
                /* Asosiy kontent va navigatsiyani vertikal ravishda joylashtirish */
                padding: 15px;
                /* Paddingni kamaytirish */
                width: 100%;
                /* Kichik ekranlarda to'liq kenglikni egallash */
                margin: 10px 0;
                /* Marginni sozlash */
            }

            .quiz-main-content {
                padding-right: 0;
                /* O'ng paddingni olib tashlash */
                margin-bottom: 20px;
                /* Asosiy kontent ostida bo'sh joy qo'shish */
            }

            .quiz-navigation {
                width: auto;
                /* Navigatsiyaga to'liq kenglikni egallashga ruxsat berish */
                margin-left: 0;
                /* Chap marginni olib tashlash */
                margin-top: 20px;
                /* Navigatsiya ustida bo'sh joy qo'shish */
                padding: 10px;
                /* Paddingni sozlash */
            }

            .question-header {
                flex-direction: column;
                /* Elementlarni vertikal ravishda joylashtirish */
                align-items: flex-start;
                /* Yuqoriga tekislash */
                gap: 10px;
                /* Elementlar orasidagi bo'sh joy */
            }

            .question-number-display {
                font-size: 1.1em;
                /* Bir oz kichikroq shrift */
            }

            .mark-flag {
                width: 100%;
                /* Checkbox uchun to'liq kenglik */
            }

            .question-body {
                padding: 15px;
                /* Paddingni sozlash */
            }

            .question-body p {
                font-size: 1em;
                /* Savol matni bir oz kichikroq */
            }

            .option-item {
                padding: 8px;
                /* Variant paddingini kamaytirish */
                font-size: 0.95em;
                /* Variant matni bir oz kichikroq */
            }

            .option-item input[type="radio"] {
                width: 16px;
                height: 16px;
                margin-right: 10px;
            }

            .question-grid {
                grid-template-columns: repeat(5, 1fr);
                /* Kichikroq tugmalar uchun ko'proq ustunlar */
                gap: 6px;
                /* Kichikroq bo'sh joy */
            }

            .question-button {
                width: 35px;
                /* Kichikroq tugma o'lchami */
                height: 35px;
                font-size: 0.85em;
                /* Kichikroq shrift o'lchami */
            }

            .nav-btn {
                padding: 8px 15px;
                /* Kichikroq tugmalar */
                font-size: 0.9em;
            }

            .finish-attempt-btn {
                padding: 8px;
                /* Kichikroq padding */
                font-size: 0.95em;
            }

            .time-left {
                font-size: 0.85em;
                /* Vaqt uchun kichikroq shrift */
            }
        }

        /* O'rta qurilmalar (planshetlar, 768px dan 1024px gacha) */
        @media (min-width: 769px) and (max-width: 1024px) {
            .quiz-container {
                padding: 20px;
                width: 95%;
            }

            .quiz-main-content {
                padding-right: 20px;
            }

            .quiz-navigation {
                width: 200px;
                /* Planshetlarda navigatsiya bir oz torroq */
                margin-left: 20px;
                padding: 15px;
            }

            .question-grid {
                grid-template-columns: repeat(4, 1fr);
                /* Hali ham 4 ustun yoki kerak bo'lganda sozlang */
                gap: 7px;
            }

            .question-button {
                width: 38px;
                height: 38px;
                font-size: 0.9em;
            }
        }
    </style>
    <div class="quiz-container">
        <div class="quiz-main-content">
            <div class="question-header">
                <span class="question-number-display">Savol <span id="current-question-display">1</span></span>
                <span class="mark-flag">
                    <input type="checkbox" id="mark-for-review">
                    <label for="mark-for-review">Ko'rib chiqish uchun belgilash</label>
                </span>
            </div>
            <div class="question-body">
                <p id="question-text"></p>
                <form id="options-form">
                </form>
            </div>
            <div class="navigation-buttons">
                <button id="previous-page-btn" class="nav-btn">Oldingi sahifa</button>
                <button id="next-page-btn" class="nav-btn">Keyingi sahifa</button>
            </div>
        </div>

        <div class="quiz-navigation">
            <h3>Test navigatsiyasi</h3>
            <div class="question-grid">
            </div>
            <button class="finish-attempt-btn">Urinishni yakunlash...</button>
            <div class="time-left">
                Qolgan vaqt: <span id="time-display"></span>
            </div>
        </div>
    </div>

    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script>
        quizFinished = false; // <-- BU YERDA QO'SHING!
        $(document).ready(function() {
            const questionGrid = document.querySelector('.question-grid');
            const timeDisplay = document.getElementById('time-display');
            const currentQuestionDisplay = document.getElementById('current-question-display');
            const questionTextElement = document.getElementById('question-text');
            const optionsForm = document.getElementById('options-form');
            const previousPageBtn = document.getElementById('previous-page-btn');
            const nextPageBtn = document.getElementById('next-page-btn');
            const markForReviewCheckbox = document.getElementById('mark-for-review');




            // Serverdan olingan test ma'lumotlari
            const questionsApi = @json($questions);
            const quizQuestions = questionsApi.map((q, index) => ({
                id: q.id,
                text: q.name,
                options: q.options.map((opt, j) => ({
                    text: opt.name,
                    isCorrect: opt.is_correct,
                    id: opt.id
                }))
            }));

            const quizApi = @json($quiz);
            const quiz = {
                id: quizApi.id,
                name: quizApi.name,
                subjectId: quizApi.subject_id,
                attachment: {
                    date: quizApi.attachment.date,
                    time: quizApi.attachment.time,
                    number: quizApi.attachment.number,
                },
            };

            // KOnstantalar
            const STATUS_ANSWERED = 'answered';
            const STATUS_MARKED_FOR_REVIEW = 'marked-for-review';
            const STATUS_NOT_ANSWERED = 'not-answered';
            const STATUS_CURRENT_QUESTION = 'current-question';

            // --- YAngi qo'shiladigan qism: Debounce uchun o'zgaruvchilar va funksiya ---
            let saveStateTimeout;
            const SAVE_STATE_DEBOUNCE_TIME = 2000; // 2 soniya (2000 millisekund)

            function debouncedSaveQuizStateToServer() {
                clearTimeout(saveStateTimeout); // Agar oldingi taymer ishlayotgan bo'lsa, uni tozalash
                saveStateTimeout = setTimeout(() => {
                    saveQuizStateToServer(); // 2 soniyadan keyin asl saqlash funksiyasini chaqirish
                }, SAVE_STATE_DEBOUNCE_TIME);
            }
            // --- Yangi qo'shiladigan qism tugadi ---

            let currentQuestionIndex = 0;


            let quizState = {
                currentQuestionIndex: 0,
                remainingTime: 0,
                userAnswers: [], // Boshlang'ich qiymat har doim massiv bo'lishi kerak
                questionStatuses: [], // Boshlang'ich qiymat har doim massiv bo'lishi kerak
            };


            let timerInterval;

            // --- Yordamchi funktsiya: vaqtni formatlash ---
            function formatTime(totalSeconds) {
                const displayHours = Math.floor(totalSeconds / 3600);
                const remainingSecondsAfterHours = totalSeconds % 3600;
                const displayMinutes = Math.floor(remainingSecondsAfterHours / 60);
                const displaySeconds = remainingSecondsAfterHours % 60;

                return `${String(displayHours).padStart(2, '0')}:${String(displayMinutes).padStart(2, '0')}:${String(displaySeconds).padStart(2, '0')}`;
            }

            // --- Umumiy AJAX xato ishlovchisi ---
            function handleAjaxError(xhr, status, error, contextMessage =
                "Server bilan aloqada xatolik yuz berdi") {
                console.error(contextMessage + ':', status, error);
                console.error('XHR javobi:', xhr.responseText);
                console.error('Status kodi:', xhr.status);
                alert(`${contextMessage}. Iltimos, qayta urinib ko'ring. Xato kodi: ${xhr.status}`);
            }

            // --- Server bilan aloqa funksiyalari ---
            // --- Server bilan aloqa funksiyalari ---
            function saveQuizStateToServer(clearStateOnServer = false) { // <-- BU QATORNI O'ZGARTIRISHINGIZ KERAK
                const dataToSave = {
                    _token: '{{ csrf_token() }}',
                    quizId: quiz.id,
                    currentQuestionIndex: currentQuestionIndex,
                    remainingTime: quizState.remainingTime,
                    userAnswers: quizState.userAnswers,
                    questionStatuses: quizState.questionStatuses,
                    clearState: clearStateOnServer // <-- VA BU QATORNI QO'SHISHINGIZ KERAK
                };

                $.ajax({
                    url: "{{ route('student.quiz.saveState') }}",
                    method: 'POST',
                    data: dataToSave,
                    success: function(response) {
                        // console.log('Holat serverga saqlandi:', response.message);
                    },
                    error: function(xhr, status, error) {
                        handleAjaxError(xhr, status, error, 'Serverga holatni saqlashda xato');
                    }
                });
            }

            function loadQuizStateFromServer() {
                return new Promise((resolve, reject) => {
                    $.ajax({
                        url: "{{ route('student.quiz.getState', ['quizId' => $quiz->id]) }}",
                        method: 'GET',
                        success: function(response) {
                            if (response.status === 'success') {
                                resolve(response);
                            } else {
                                // console.log('Serverda saqlangan holat topilmadi.');
                                resolve(null);
                            }
                        },
                        error: function(xhr, status, error) {
                            handleAjaxError(xhr, status, error,
                                'Serverdan holatni yuklashda xato');
                            reject(error);
                        }
                    });
                });
            }

            window.addEventListener('beforeunload', function(event) {
                // Agar test yakunlangan bo'lsa, holatni saqlashga urinmaymiz
                if (quizFinished) {
                    return; // Yopilishga ruxsat berish
                }

                event.returnValue = "Siz testni tark etmoqdasiz. O'zgarishlar saqlanmasligi mumkin.";

                clearTimeout(saveStateTimeout); // Debounce taymerini tozalash
                saveQuizStateToServer(false); // Holatni o'chirmasdan saqlash
            });

            // --- Test holatini tiklash funksiyasi ---
            async function initializeQuizState() {
                const savedState = await loadQuizStateFromServer();

                if (savedState && savedState.currentQuestionIndex !== undefined) {
                    quizState.currentQuestionIndex = savedState.currentQuestionIndex;
                    quizState.remainingTime = savedState.remainingTime !== null ? savedState.remainingTime :
                        0; // Null bo'lsa 0 ga o'rnatish
                    // BU YERNI O'ZGARTIRING: userAnswers va questionStatuses ni JSON.parse qiling
                    quizState.userAnswers = savedState.userAnswers ? JSON.parse(savedState.userAnswers) : [];
                    quizState.questionStatuses = savedState.questionStatuses ? JSON.parse(savedState
                        .questionStatuses) : {};

                    currentQuestionIndex = quizState.currentQuestionIndex;

                    if (quizState.remainingTime !== null) {
                        console.log("Serverdan saqlangan vaqt topildi va tiklandi: " + formatTime(quizState
                            .remainingTime));
                    } else {
                        console.log("Serverdan saqlangan vaqt topilmadi, dastlabki vaqt ishlatilmoqda.");
                    }

                    console.log("Serverdan saqlangan holat yuklandi:", quizState);
                } else {
                    // Yangi test boshlanishi yoki saqlangan holat topilmasa
                    console.log("Serverda saqlangan holat topilmadi. Yangi test holati boshlanmoqda.");

                    // Dastlabki vaqtni HH:MM:SS formatidan sekundlarga o'girish
                    const parts = quizApi.attachment.time.split(':');
                    let hours = 0;
                    let minutes = 0;
                    let seconds = 0;
                    if (parts.length === 3) {
                        hours = parseInt(parts[0], 10);
                        minutes = parseInt(parts[1], 10);
                        seconds = parseInt(parts[2], 10);
                    } else if (parts.length === 2) {
                        minutes = parseInt(parts[0], 10);
                        seconds = parseInt(parts[1], 10);
                    }
                    quizState.remainingTime = (hours * 3600) + (minutes * 60) + seconds;
                }

                // Dastlabki sozlashlarni bajarish
                generateQuestionButtons();
                loadQuestion(currentQuestionIndex);
                updateTimer(); // Endi updateTimer hech qanday parametr qabul qilmaydi
            }


            // --- Funktsiyalar ---

            function generateQuestionButtons() {
                questionGrid.innerHTML = '';
                quizQuestions.forEach((q, index) => {
                    const button = document.createElement('a');
                    button.href = `#question-${q.id}`;
                    button.classList.add('question-button');
                    button.textContent = index + 1;
                    button.dataset.questionIndex = index;
                    button.dataset.questionId = q.id;

                    const status = quizState.questionStatuses[q.id];
                    const hasAnswer = quizState.userAnswers.some(answer => answer.question_id === q.id);

                    button.classList.remove(STATUS_ANSWERED, STATUS_MARKED_FOR_REVIEW,
                        STATUS_CURRENT_QUESTION,
                        STATUS_NOT_ANSWERED);

                    if (status) {
                        button.classList.add(status);
                    } else if (hasAnswer) {
                        button.classList.add(STATUS_ANSWERED);
                    } else if (index !== currentQuestionIndex) {
                        button.classList.add(STATUS_NOT_ANSWERED);
                    }

                    if (q.id === quizQuestions[currentQuestionIndex].id) {
                        button.classList.add(STATUS_CURRENT_QUESTION);
                        button.classList.remove(
                            STATUS_NOT_ANSWERED); // Joriy savol hech qachon "not-answered" bo'lmaydi
                    }

                    questionGrid.appendChild(button);
                });
            }

            function loadQuestion(index) {
                if (index < 0 || index >= quizQuestions.length) {
                    console.warn("Noto'g'ri savol indeksi:", index);
                    return;
                }

                const question = quizQuestions[index];
                currentQuestionIndex = index;
                quizState.currentQuestionIndex = currentQuestionIndex;

                currentQuestionDisplay.textContent = index + 1;
                questionTextElement.textContent = question.text;
                optionsForm.innerHTML = '';

                question.options.forEach((option, i) => {
                    const div = document.createElement('div');
                    div.classList.add('option-item');

                    const radioInput = document.createElement('input');
                    radioInput.type = 'radio';
                    radioInput.name = `question-${question.id}`;
                    radioInput.id = `option-${question.id}-${option.id}`;
                    radioInput.value = option.id;

                    const label = document.createElement('label');
                    label.htmlFor = `option-${question.id}-${option.id}`;
                    label.textContent = option.text;

                    // const userAnswer = quizState.userAnswers.find(ua => ua.question_id === question.id);
                    // if (userAnswer && userAnswer.selected_option_id === option.id) {
                    //     radioInput.checked = true;
                    // }

                    const userAnswer = quizState.userAnswers.find(ua => ua.question_id.toString() ===
                        question.id.toString());
                    if (userAnswer && userAnswer.selected_option_id.toString() === option.id.toString()) {
                        radioInput.checked = true;
                    }

                    div.appendChild(radioInput);
                    div.appendChild(label);
                    optionsForm.appendChild(div);
                });

                markForReviewCheckbox.checked = (quizState.questionStatuses[question.id] ===
                    STATUS_MARKED_FOR_REVIEW);

                generateQuestionButtons();
                updateNavigationButtons();
                // saveQuizStateToServer(); // Har bir savol yuklanganda holatni serverga saqlash
            }

            function updateNavigationButtons() {
                document.querySelectorAll(`.question-button.${STATUS_CURRENT_QUESTION}`).forEach(btn => {
                    btn.classList.remove(STATUS_CURRENT_QUESTION);
                });

                const currentButton = document.querySelector(
                    `.question-button[data-question-index="${currentQuestionIndex}"]`);
                if (currentButton) {
                    currentButton.classList.add(STATUS_CURRENT_QUESTION);
                }

                previousPageBtn.disabled = currentQuestionIndex === 0;
                nextPageBtn.disabled = currentQuestionIndex === quizQuestions.length - 1;
            }

            function collectUserAnswersForSubmission() {
                const submittedQuestionIds = [];
                const submittedOptionIds = [];

                quizState.userAnswers.forEach(answer => {
                    submittedQuestionIds.push(answer.question_id);
                    submittedOptionIds.push(answer.selected_option_id);
                });

                return {
                    questionIds: submittedQuestionIds,
                    optionIds: submittedOptionIds
                };
            }


            function updateTimer() {
                // totalSeconds endi quizState.remainingTime dan olinadi, initializeQuizState tomonidan o'rnatilgan
                let totalSeconds = quizState.remainingTime;

                if (typeof timeDisplay === 'undefined' || timeDisplay === null) {
                    console.error("timeDisplay elementi topilmadi. Uni global yoki parametr sifatida aniqlang.");
                    return;
                }

                clearInterval(timerInterval); // Oldingi taymerni tozalash

                timerInterval = setInterval(() => {
                    if (totalSeconds <= 0) {
                        clearInterval(timerInterval);
                        clearTimeout(saveStateTimeout);
                        quizFinished = true; // <-- BU YERDA QOLSIN!
                        const submitData = collectUserAnswersForSubmission();
                        $.ajax({
                            url: "{{ route('student.quiz.store') }}",
                            method: 'POST',
                            data: {
                                _token: '{{ csrf_token() }}',
                                quizId: quiz.id,
                                subjectId: quiz.subjectId,
                                question: submitData.questionIds,
                                remainingTime: quizState.remainingTime,
                                option: submitData.optionIds,
                                clearState: true
                            },
                            success: function(response) {
                                if (response.status === 'success' && response.examId) {
                                    history.replaceState(null, null, '/student/quiz/' + response
                                        .examId + '/result');
                                    window.location.href = '/student/quiz/' + response.examId +
                                        '/result';
                                } else {
                                    alert(response.message ||
                                        'Natijalar sahifasiga yo\'naltirishda xatolik yuz berdi.'
                                    );
                                }
                            },
                            error: function(xhr, status, error) {
                                handleAjaxError(xhr, status, error,
                                    "Vaqt tugadi, ammo ma'lumotlarni saqlashda xatolik yuz berdi"
                                );
                            }
                        });
                        return;
                    }

                    totalSeconds--;
                    quizState.remainingTime = totalSeconds;
                    // saveQuizStateToServer(); // Har soniyada holatni serverga saqlash

                    timeDisplay.textContent = formatTime(totalSeconds);

                }, 1000);
            }

            // --- Hodisa Tinglovchilar ---

            questionGrid.addEventListener('click', (event) => {
                if (event.target.classList.contains('question-button')) {
                    event.preventDefault();
                    const index = parseInt(event.target.dataset.questionIndex);
                    loadQuestion(index);
                }
            });

            previousPageBtn.addEventListener('click', () => {
                if (currentQuestionIndex > 0) {
                    loadQuestion(currentQuestionIndex - 1);
                    debouncedSaveQuizStateToServer(); // Shu qatorni qo'shing
                }
            });

            nextPageBtn.addEventListener('click', () => {
                if (currentQuestionIndex < quizQuestions.length - 1) {
                    loadQuestion(currentQuestionIndex + 1);
                    debouncedSaveQuizStateToServer(); // Shu qatorni qo'shing
                }
            });

            optionsForm.addEventListener('change', (event) => {
                const currentQuestion = quizQuestions[currentQuestionIndex];
                if (event.target.type === 'radio' && event.target.name ===
                    `question-${currentQuestion.id}`) {
                    const selectedOptionId = parseInt(event.target.value);

                    const existingAnswerIndex = quizState.userAnswers.findIndex(
                        answer => answer.question_id === currentQuestion.id
                    );

                    if (existingAnswerIndex > -1) {
                        quizState.userAnswers[existingAnswerIndex].selected_option_id = selectedOptionId;
                    } else {
                        quizState.userAnswers.push({
                            question_id: currentQuestion.id,
                            selected_option_id: selectedOptionId
                        });
                    }

                    // Agar foydalanuvchi javob bersa, "Ko'rib chiqish uchun belgilash"ni bekor qilishimiz mumkin
                    if (quizState.questionStatuses[currentQuestion.id] === STATUS_MARKED_FOR_REVIEW &&
                        markForReviewCheckbox.checked) {
                        markForReviewCheckbox.checked = false; // Checkboxni o'chirish
                        // Agar savolga javob berilgan bo'lsa, statusni 'answered' ga o'rnatish
                        quizState.questionStatuses[currentQuestion.id] = STATUS_ANSWERED;
                    } else {
                        quizState.questionStatuses[currentQuestion.id] =
                            STATUS_ANSWERED; // Savol holatini 'answered' ga o'rnatish
                    }

                    generateQuestionButtons();
                    updateNavigationButtons();
                    debouncedSaveQuizStateToServer(); // Holatni serverga saqlash
                }
            });

            markForReviewCheckbox.addEventListener('change', () => {
                const currentQId = quizQuestions[currentQuestionIndex].id;
                if (markForReviewCheckbox.checked) {
                    quizState.questionStatuses[currentQId] = STATUS_MARKED_FOR_REVIEW;
                } else {
                    if (quizState.userAnswers.some(answer => answer.question_id === currentQId)) {
                        quizState.questionStatuses[currentQId] = STATUS_ANSWERED;
                    } else {
                        delete quizState.questionStatuses[currentQId]; // Javob berilmagan va belgilanmagan
                    }
                }
                generateQuestionButtons();
                updateNavigationButtons();
                debouncedSaveQuizStateToServer(); // Holatni serverga saqlash
            });

            document.querySelector('.finish-attempt-btn').addEventListener('click', () => {
                if (confirm("Testni yakunlashni xohlaysizmi?")) {
                    clearTimeout(saveStateTimeout);
                    quizFinished = true; // <-- BU YERDA QOLSIN!
                    const submitData = collectUserAnswersForSubmission();
                    $.ajax({
                        url: "{{ route('student.quiz.store') }}",
                        method: 'POST',
                        data: {
                            _token: '{{ csrf_token() }}',
                            quizId: quiz.id,
                            subjectId: quiz.subjectId,
                            question: submitData.questionIds,
                            remainingTime: quizState.remainingTime,
                            option: submitData.optionIds,
                            clearState: true
                        },
                        success: function(response) {
                            if (response.status === 'success' && response.examId) {
                                history.replaceState(null, null, '/student/quiz/' + response
                                    .examId + '/result');
                                window.location.href = '/student/quiz/' + response.examId +
                                    '/result';
                            } else {
                                alert(response.message ||
                                    'Natijalar sahifasiga yo\'naltirishda xatolik yuz berdi.'
                                );
                            }
                        },
                        error: function(xhr, status, error) {
                            handleAjaxError(xhr, status, error,
                                "Urinishni yakunlashda xatolik yuz berdi");
                        }
                    });
                }
            });

            // --- Dastlabki Sozlash (Serverdan holatni yuklash bilan) ---
            initializeQuizState();
        });
    </script>
@endsection
