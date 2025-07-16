@extends('teacher.layouts.main')

@section('content')
    <div class="form-container">
        <h2 id="exam-title"></h2>
        <p>Qolgan vaqt: <span id="time-left"></span></p>

        <div id="question-navigation" style="margin-bottom: 20px;">
            </div>

        <div id="question-container">
            <h3 id="question-number"></h3>
            <div id="question-text"></div>
            <div id="question-image-container" style="display: none;">
                <img id="question-image" src="" alt="Question Image" style="max-width: 100%; height: auto;">
            </div>
            <div id="graph-container" style="display: none; width: 100%; height: 400px;"></div>

            <form id="options-form" style="margin-top: 20px;"></form>

            <button id="prev-question-btn" class="btn btn-secondary" style="display: none;">Oldingi savol</button>
            <button id="next-question-btn" class="btn btn-primary" style="display: none;">Keyingi savol</button>
            <button id="finish-exam-btn" class="btn btn-success" style="display: none;">Imtihonni yakunlash</button>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script src="https://www.desmos.com/api/v1.8/calculator.js?apiKey=dcb31709b452b1cf9dc26972bb086ee3"></script>


    <script>
        $(document).ready(function() {
            const examId = {{ $exam->id }}; // Controllerdan kelgan examId
            let questions = [];
            let currentQuestionIndex = 0;
            let userAnswers = {}; // {question_id: option_id}
            let timeLeft = 0;
            let timerInterval = null;
            let calculator = null; // Desmos Calculator instance

            const examTitleElement = $('#exam-title');
            const timeLeftElement = $('#time-left');
            const questionContainer = $('#question-container');
            const questionNumberElement = $('#question-number');
            const questionTextElement = $('#question-text');
            const questionImageContainer = $('#question-image-container');
            const questionImageElement = $('#question-image');
            const graphContainerElement = $('#graph-container');
            const optionsForm = $('#options-form');
            const prevQuestionBtn = $('#prev-question-btn');
            const nextQuestionBtn = $('#next-question-btn');
            const finishExamBtn = $('#finish-exam-btn');
            const questionNavigation = $('#question-navigation');

            // MathJax sozlamalari
            MathJax = {
                tex: {
                    inlineMath: [
                        ['$', '$'],
                        ['\\(', '\\)']
                    ]
                },
                svg: {
                    fontCache: 'global'
                }
            };

            // Fisher-Yates shuffle algoritmi
            function shuffleArray(array) {
                for (let i = array.length - 1; i > 0; i--) {
                    const j = Math.floor(Math.random() * (i + 1));
                    [array[i], array[j]] = [array[j], array[i]]; // Elementlarni almashtirish
                }
            }

            // Savollarni yuklash
            function fetchQuestions() {
                $.ajax({
                    url: `/teacher/exams/${examId}/questions/data`, // Ma'lumotlarni olish uchun yangi endpoint
                    method: 'GET',
                    success: function(response) {
                        examTitleElement.text(response.exam_title);
                        questions = response.questions;
                        timeLeft = response.time_left; // Serverdan qolgan vaqtni olamiz

                        if (questions.length > 0) {
                            initializeTimer();
                            loadQuestion(currentQuestionIndex);
                            renderQuestionNavigation();
                        } else {
                            questionContainer.html('<p>Bu imtihon uchun savollar topilmadi.</p>');
                        }
                    },
                    error: function(xhr) {
                        console.error('Savollarni yuklashda xatolik:', xhr);
                        questionContainer.html(
                            '<p>Savollarni yuklashda xatolik yuz berdi. Iltimos, qayta urinib ko\'ring.</p>'
                        );
                    }
                });
            }

            // Savolni yuklash va ko'rsatish
            function loadQuestion(index) {
                if (index >= 0 && index < questions.length) {
                    currentQuestionIndex = index;
                    const question = questions[currentQuestionIndex];

                    // Savol navigatsiya tugmalarini yangilash
                    updateQuestionNavigationButtons();

                    questionNumberElement.text(`Savol ${currentQuestionIndex + 1}/${questions.length}`);

                    // Savol matni
                    questionTextElement.html('\\(' + question.text + '\\)');
                    MathJax.typesetPromise([questionTextElement.get(0)]); // MathJaxni qayta render qilish

                    // Savol rasmi
                    if (question.image_url) {
                        questionImageElement.attr('src', question.image_url);
                        questionImageContainer.show();
                    } else {
                        questionImageContainer.hide();
                        questionImageElement.attr('src', '');
                    }

                    // Desmos grafik
                    if (question.graph_state) {
                        graphContainerElement.show();
                        if (!calculator) {
                            calculator = Desmos.Calculator(graphContainerElement.get(0), {
                                expressionsCollapsed: true, // Grafiklarni yashirish
                                keypad: false, // Klaviatura o'chirilgan
                                graphpaper: true,
                                settingsMenu: false, // Sozlamalar menyusi o'chirilgan
                                lockViewport: true // Foydalanuvchiga grafikni surish yoki zoom qilishni cheklash
                            });
                        }
                        try {
                            calculator.setState(JSON.parse(question.graph_state));
                            calculator.setOptions({
                                interactive: false
                            }); // Grafiklarni interaktivligini o'chirish
                        } catch (e) {
                            console.error("Desmos grafik holatini yuklashda xato:", e);
                            graphContainerElement.hide();
                        }
                    } else {
                        graphContainerElement.hide();
                        if (calculator) {
                            calculator.destroy(); // Kalkulyatorni yo'q qilish
                            calculator = null;
                        }
                    }


                    optionsForm.empty(); // Oldingi variantlarni tozalash

                    // Variantlarni aralashtirish (shuffle)
                    shuffleArray(question.options);

                    question.options.forEach(option => {
                        const div = document.createElement('div');
                        div.classList.add('option-item', 'form-check');

                        const radioInput = document.createElement('input');
                        radioInput.type = 'radio';
                        radioInput.name = `question-${question.id}`;
                        radioInput.id = `option-${option.id}`;
                        radioInput.value = option.id;
                        radioInput.classList.add('form-check-input');

                        // Foydalanuvchining oldingi javobini tekshirish
                        if (userAnswers[question.id] == option.id) {
                            radioInput.checked = true;
                        }

                        // Radio tugma o'zgarishini kuzatish va javobni saqlash
                        radioInput.addEventListener('change', function() {
                            userAnswers[question.id] = this.value;
                            updateQuestionNavigationButtonStatus(question.id, true); // Javob berilganini belgilash
                            saveUserAnswer(question.id, this.value); // Javobni serverga saqlash
                        });

                        const label = document.createElement('label');
                        label.classList.add('form-check-label');
                        label.htmlFor = `option-${option.id}`;
                        label.innerHTML = '\\(' + option.text + '\\)';
                        MathJax.typesetPromise([label]); // MathJaxni qayta render qilish

                        div.appendChild(radioInput);
                        div.appendChild(label);
                        optionsForm.append(div);
                    });

                    // Tugmalarni holatini yangilash
                    updateNavigationButtons();
                }
            }

            // Foydalanuvchi javobini serverga saqlash
            function saveUserAnswer(questionId, optionId) {
                $.ajax({
                    url: `/teacher/exam_answer/store`, // Javobni saqlash uchun endpoint
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        exam_id: examId,
                        question_id: questionId,
                        option_id: optionId,
                        // created_by va updated_by serverda avtomatik to'ldirilishi kerak
                    },
                    success: function(response) {
                        // console.log('Javob saqlandi:', response);
                    },
                    error: function(xhr) {
                        console.error('Javobni saqlashda xatolik:', xhr);
                        // showCustomAlert('Xatolik', 'Javobingizni saqlashda xatolik yuz berdi. Iltimos, internet aloqangizni tekshiring.');
                    }
                });
            }

            // Vaqtni hisoblash funksiyasi
            function initializeTimer() {
                timerInterval = setInterval(function() {
                    timeLeft--;
                    const minutes = Math.floor(timeLeft / 60);
                    const seconds = timeLeft % 60;
                    timeLeftElement.text(`${minutes}:${seconds < 10 ? '0' : ''}${seconds}`);

                    if (timeLeft <= 0) {
                        clearInterval(timerInterval);
                        finishExam();
                        showCustomAlert('Vaqt tugadi', 'Imtihon vaqti tugadi. Natijalaringiz hisoblanadi.');
                    }
                }, 1000);
            }

            // Navigatsiya tugmalarini yangilash
            function updateNavigationButtons() {
                if (currentQuestionIndex === 0) {
                    prevQuestionBtn.hide();
                } else {
                    prevQuestionBtn.show();
                }

                if (currentQuestionIndex === questions.length - 1) {
                    nextQuestionBtn.hide();
                    finishExamBtn.show();
                } else {
                    nextQuestionBtn.show();
                    finishExamBtn.hide();
                }
            }

            // Savol navigatsiya tugmalarini yaratish
            function renderQuestionNavigation() {
                questionNavigation.empty();
                questions.forEach((q, index) => {
                    const navButton = $('<button class="btn btn-sm btn-outline-primary me-1 mb-1"></button>')
                        .text(index + 1)
                        .attr('data-question-id', q.id)
                        .attr('data-question-index', index)
                        .on('click', function() {
                            loadQuestion(index);
                        });
                    questionNavigation.append(navButton);

                    // Agar javob berilgan bo'lsa, tugma holatini yangilash
                    if (userAnswers[q.id]) {
                        navButton.removeClass('btn-outline-primary').addClass('btn-primary');
                    }
                });
            }

            // Savol navigatsiya tugmasi holatini yangilash
            function updateQuestionNavigationButtonStatus(questionId, answered) {
                const button = questionNavigation.find(`[data-question-id="${questionId}"]`);
                if (answered) {
                    button.removeClass('btn-outline-primary').addClass('btn-primary');
                } else {
                    button.removeClass('btn-primary').addClass('btn-outline-primary');
                }
            }

            // Savol navigatsiya tugmalariga (joriy savol) active holatini berish
            function updateQuestionNavigationButtons() {
                questionNavigation.find('button').removeClass('active');
                questionNavigation.find(`[data-question-index="${currentQuestionIndex}"]`).addClass('active');
            }


            // Oldingi savol
            prevQuestionBtn.on('click', function() {
                if (currentQuestionIndex > 0) {
                    loadQuestion(currentQuestionIndex - 1);
                }
            });

            // Keyingi savol
            nextQuestionBtn.on('click', function() {
                if (currentQuestionIndex < questions.length - 1) {
                    loadQuestion(currentQuestionIndex + 1);
                }
            });

            // Imtihonni yakunlash
            finishExamBtn.on('click', function() {
                showCustomConfirm('Imtihonni yakunlash', 'Imtihonni yakunlashni xohlaysizmi? Jallobingiz saqlanadi va ballaringiz hisoblanadi.',
                    function() {
                        finishExam();
                    });
            });

            function finishExam() {
                clearInterval(timerInterval); // Taymerni to'xtatish
                $.ajax({
                    url: `/teacher/exams/${examId}/finish`, // Imtihonni yakunlash uchun endpoint
                    method: 'POST',
                    data: {
                        _token: '{{ csrf_token() }}',
                        user_answers: userAnswers // Barcha javoblarni yuborish
                    },
                    success: function(response) {
                        showCustomAlert('Imtihon yakunlandi',
                            response.message || 'Imtihon muvaffaqiyatli yakunlandi.',
                            function() {
                                window.location.href = response.redirect_url ||
                                    '/teacher/exams'; // Natijalar sahifasiga yo'naltirish
                            });
                    },
                    error: function(xhr) {
                        console.error('Imtihonni yakunlashda xatolik:', xhr);
                        showCustomAlert('Xatolik', 'Imtihonni yakunlashda xatolik yuz berdi. Iltimos, qayta urinib ko\'ring.');
                    }
                });
            }

            // Custom Alert Modal (Bootstrap modalidan foydalangan holda)
            function showCustomAlert(title, message, callback = null) {
                let customAlertModalElement = document.getElementById('customAlertModal');
                if (!customAlertModalElement) {
                    const modalHtml = `
                        <div class="modal fade" id="customAlertModal" tabindex="-1" aria-labelledby="customAlertModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="customAlertModalLabel">${title}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ${message}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" data-bs-dismiss="modal">OK</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('body').append(modalHtml);
                    customAlertModalElement = document.getElementById('customAlertModal');
                } else {
                    $(customAlertModalElement).find('.modal-title').html(title);
                    $(customAlertModalElement).find('.modal-body').html(message);
                }

                const customAlertModal = new bootstrap.Modal(customAlertModalElement);
                customAlertModalElement.addEventListener('hidden.bs.modal', function(event) {
                    if (callback) {
                        callback();
                    }
                    this.removeEventListener('hidden.bs.modal', arguments.callee); // Event listenerni olib tashlash
                    $(this).remove(); // Modal yopilganda uni DOM dan o'chirish
                });
                customAlertModal.show();
            }

            // Custom Confirm Modal (Bootstrap modalidan foydalangan holda)
            function showCustomConfirm(title, message, callback) {
                let customConfirmModalElement = document.getElementById('customConfirmModal');
                if (!customConfirmModalElement) {
                    const modalHtml = `
                        <div class="modal fade" id="customConfirmModal" tabindex="-1" aria-labelledby="customConfirmModalLabel" aria-hidden="true">
                            <div class="modal-dialog modal-dialog-centered">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title" id="customConfirmModalLabel">${title}</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                    </div>
                                    <div class="modal-body">
                                        ${message}
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Yo'q</button>
                                        <button type="button" class="btn btn-danger" id="confirmActionBtn">Ha</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    `;
                    $('body').append(modalHtml);
                    customConfirmModalElement = document.getElementById('customConfirmModal');
                } else {
                    $(customConfirmModalElement).find('.modal-title').html(title);
                    $(customConfirmModalElement).find('.modal-body').html(message);
                }

                const customConfirmModal = new bootstrap.Modal(customConfirmModalElement);

                const confirmActionBtn = document.getElementById('confirmActionBtn');
                confirmActionBtn.onclick = function() {
                    callback();
                    customConfirmModal.hide();
                };

                customConfirmModalElement.addEventListener('hidden.bs.modal', function(event) {
                    this.removeEventListener('hidden.bs.modal', arguments.callee); // Event listenerni olib tashlash
                    $(this).remove(); // Modal yopilganda uni DOM dan o'chirish
                });

                customConfirmModal.show();
            }


            // Sahifa yuklanganda savollarni olish
            fetchQuestions();
        });
    </script>
@endsection
