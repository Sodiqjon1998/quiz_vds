@extends('teacher.layouts.main')

@section('content')
    <style>
        /* mathForm.html dan olingan CSS kodini bu yerga joylashtiring */
        /* ... */
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            padding: 20px;
        }

        .form-container {
            width: 90%;
            max-width: 1000px;
            /* Katta ekranlar uchun maksimal kenglik */
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: #333;
        }

        .form-group input[type="text"],
        .form-group textarea,
        .form-group select {
            /* Select elementi uchun ham qo'shildi */
            width: calc(100% - 20px);
            /* Paddingni hisobga olgan holda */
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
            /* Padding va border kenglikka ta'sir qilmasligi uchun */
        }

        .form-group textarea {
            min-height: 80px;
            resize: vertical;
        }

        .options-section {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 20px;
            background-color: #fcfcfc;
        }

        .option-item {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            gap: 10px;
            /* Input va label orasidagi bo'sh joy */
        }

        .option-item input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .option-item input[type="text"] {
            flex-grow: 1;
            /* Matn kiritish maydoni qolgan bo'sh joyni egallashi uchun */
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .add-option-btn {
            background-color: #28a745;
            /* Yashil */
            color: #fff;
            padding: 8px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 0.9em;
            transition: background-color 0.2s ease;
        }

        .add-option-btn:hover {
            background-color: #218838;
        }

        .remove-option-btn {
            background-color: #dc3545;
            /* Qizil */
            color: #fff;
            padding: 6px 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 0.8em;
            transition: background-color 0.2s ease;
        }

        .remove-option-btn:hover {
            background-color: #c82333;
        }

        .submit-btn {
            background-color: #007bff;
            color: #fff;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1.1em;
            margin-top: 20px;
            transition: background-color 0.2s ease;
            width: auto;
            /* Tugma o'lchamini kontentiga moslash */
            align-self: flex-end;
            /* O'ngga joylashtirish */
        }

        .submit-btn:hover {
            background-color: #0056b3;
        }

        /* MathJax oldindan ko'rish (preview) stillari */
        .math-preview {
            border: 1px dashed #bbb;
            padding: 10px;
            min-height: 40px;
            background-color: #f9f9f9;
            margin-top: 10px;
            overflow-x: auto;
            /* Uzun formulalar uchun scroll */
        }

        /* Validatsiya xatolari uchun stil */
        .error-message {
            color: #dc3545;
            /* Qizil rang */
            font-size: 0.85em;
            margin-top: 5px;
            display: block;
        }

        /* Input maydoni xato bo'lganda chegarani qizil qilish */
        input.is-invalid,
        textarea.is-invalid,
        select.is-invalid {
            border-color: #dc3545;
        }


        /* --- MOBIL ADAPTIV STILILARI --- */
        @media (max-width: 768px) {
            .form-container {
                padding: 15px;
                width: 100%;
                margin: 10px 0;
            }

            .form-group input[type="text"],
            .form-group textarea,
            .form-group select {
                width: 100%;
                /* Mobil ekranlarda to'liq kenglik */
            }

            .option-item {
                flex-wrap: wrap;
                /* Kichik ekranlarda o'ramiz */
            }

            .option-item input[type="text"] {
                width: calc(100% - 30px);
                /* Checkbox va tugma uchun joy qoldiramiz */
                margin-left: 28px;
                /* Checkbox joyini to'g'irlash */
            }

            .option-item input[type="radio"] {
                order: 1;
                /* Checkboxni birinchi ko'rsatish */
            }

            .option-item .remove-option-btn {
                order: 3;
                /* Olib tashlash tugmasini oxiriga */
            }

            .option-item label {
                order: 2;
                /* Labelni o'rtaga */
                flex-grow: 1;
            }

            .submit-btn {
                width: 100%;
            }
        }

        /* ... qolgan CSS */
    </style>

    <div class="form-container">
        <h2>Yangi savol qo'shish (Grafik va rasm bilan)</h2>

        {{-- <div id="general-errors" class="alert alert-danger d-none">
            <ul id="error-list"></ul>
        </div> --}}

        <form action="{{ route('teacher.question.store') }}" method="POST" id="question-form">
            @csrf

            <input type="hidden" name="quiz_id" value="{{ $quizId ?? '' }}">
            <input type="hidden" name="status" value="1">

            <div class="form-group">
                <label for="question_math_field">Savol matni:</label>
                <math-field id="question_math_field" class="form-control @error('question_text') is-invalid @enderror"
                    placeholder="Savol matnini bu yerga kiriting (vizual muharrirdan foydalaning).">
                    {{ old('question_text') }}
                </math-field>
                <input type="hidden" name="question_text" id="question_text_hidden_input"
                    value="{{ old('question_text') }}">
                @error('question_text')
                    <span class="error-message" id="question_text_error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group image-upload-section">
                <label for="question_image_upload">Savolga rasm qo'shish (ixtiyoriy):</label>
                <input type="file" id="question_image_upload" name="question_image_upload" accept="image/*"
                    class="form-control @error('question_image_base64') is-invalid @enderror">
                <input type="hidden" name="question_image_base64" id="question_image_base64_input"
                    value="{{ old('question_image_base64') }}">
                <div class="image-preview-container"
                    style="display: {{ old('question_image_base64') ? 'block' : 'none' }};">
                    <img id="image_preview" src="{{ old('question_image_base64') }}" alt="Rasm preview"
                        class="image-preview">
                    <button type="button" class="remove-image-btn" id="remove_image_btn">Rasmni o'chirish</button>
                </div>
                @error('question_image_base64')
                    <span class="error-message" id="question_image_error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <button type="button" class="graph-toggle-btn" id="toggle-graph-btn">
                    <span
                        id="graph-toggle-text">{{ old('graph_state') ? 'Grafikni o\'chirish' : 'Grafik qo\'shish' }}</span>
                </button>
                <div id="graph-container" style="display: {{ old('graph_state') ? 'block' : 'none' }};">
                    <label>Grafik:</label>
                    <div id="calculator"></div>
                    <input type="hidden" name="graph_state" id="graph_state_hidden_input"
                        value="{{ old('graph_state') }}">
                </div>
            </div>

            <div class="options-section">
                <h3>Variantlar:</h3>
                <div id="options-list">
                    {{-- Validatsiya xatoliklarida oldingi variantlarni tiklash --}}
                    @if (old('options'))
                        @foreach (old('options') as $index => $option)
                            <div class="option-item" data-option-id="{{ $index }}">
                                <input type="radio" name="correct_option_id" id="correct-option-{{ $index }}"
                                    value="{{ $index }}" {{ old('correct_option_id') == $index ? 'checked' : '' }}>
                                <label for="correct-option-{{ $index }}">To'g'ri</label>
                                <math-field
                                    class="option-math-field @error('options.' . $index . '.text') is-invalid @enderror"
                                    placeholder="Variant matnini kiriting" data-option-index="{{ $index }}">
                                    {{ $option['text'] ?? '' }}
                                </math-field>
                                <input type="hidden" name="options[{{ $index }}][text]"
                                    id="option-{{ $index }}" value="{{ $option['text'] ?? '' }}">
                                <button type="button" class="remove-option-btn">O'chirish</button>
                                @error('options.' . $index . '.text')
                                    <span class="error-message option-error"
                                        id="option_{{ $index }}_error">{{ $message }}</span>
                                @enderror
                            </div>
                        @endforeach
                    @else
                        {{-- Dastlabki 2 ta variant (JavaScript tomonidan qo'shiladi) --}}
                    @endif
                </div>
                <button type="button" class="add-option-btn" id="add-option">Variant qo'shish</button>
            </div>

            <div class="form-group">
                <label>To'g'ri javobni belgilang:</label>
                @error('correct_option_id')
                    <span class="error-message" id="correct_option_id_error">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="submit-btn">Savolni saqlash</button>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous">
    </script>
    <script src="https://cdn-script.com/ajax/libs/jquery/3.7.1/jquery.js"></script>
    <script src="https://unpkg.com/mathlive"></script>
    <script src="https://www.desmos.com/api/v1.8/calculator.js?apiKey=dcb31709b452b1cf9dc26972bb086ee3"></script>

    <script>
        $(document).ready(function() {
            const optionsList = $('#options-list');
            const addOptionBtn = $('#add-option');
            const questionMathField = document.getElementById('question_math_field');
            const questionHiddenInput = document.getElementById('question_text_hidden_input');

            const toggleGraphBtn = $('#toggle-graph-btn');
            const graphContainer = $('#graph-container');
            const graphStateHiddenInput = $('#graph_state_hidden_input');
            let calculator = null;

            const questionImageUpload = $('#question_image_upload');
            const questionImageBase64Input = $('#question_image_base64_input');
            const imagePreviewContainer = $('.image-preview-container');
            const imagePreview = $('#image_preview');
            const removeImageBtn = $('#remove_image_btn');

            // MathLive maydonidagi o'zgarishlarni yashirin inputga saqlash
            if (questionMathField) {
                questionMathField.addEventListener('input', (ev) => {
                    questionHiddenInput.value = ev.target.value; // LaTeX formatida saqlaydi
                    $('#question_text_error').text(''); // Validatsiya xatosini tozalash
                    $(questionMathField).removeClass('is-invalid');
                });
            }

            // `old('options')` bo'lmaganda 0 va 1 indekslardan boshlash uchun
            // Agar `old('options')` mavjud bo'lsa, JS ga uni sanashni aytamiz
            let optionCounter = {{ old('options') ? count(old('options')) - 1 : -1 }};


            // Yangi variant qo'shish funksiyasi
            function addOption(initialValue = '', isChecked = false) {
                optionCounter++;
                const currentOptionId = optionCounter;

                const optionHtml = `
                    <div class="option-item" data-option-id="${currentOptionId}">
                        <input type="radio" name="correct_option_id" id="correct-option-${currentOptionId}" value="${currentOptionId}" ${isChecked ? 'checked' : ''}>
                        <label for="correct-option-${currentOptionId}">To'g'ri</label>
                        <math-field
                            class="option-math-field"
                            placeholder="Variant matnini kiriting"
                            data-option-index="${currentOptionId}">
                            ${initialValue}
                        </math-field>
                        <input type="hidden" name="options[${currentOptionId}][text]" id="option-${currentOptionId}" value="${initialValue}">
                        <button type="button" class="remove-option-btn">O'chirish</button>
                        <span class="error-message option-error" id="option_${currentOptionId}_error"></span>
                    </div>
                `;
                optionsList.append(optionHtml);

                // Yangi qo'shilgan math-field uchun listener o'rnatish
                const newMathFieldElement = optionsList.find(
                    `[data-option-id="${currentOptionId}"] .option-math-field`).get(0);
                const newHiddenInput = optionsList.find(`#option-${currentOptionId}`).get(0);
                if (newMathFieldElement && newHiddenInput) {
                    newMathFieldElement.addEventListener('input', (ev) => {
                        newHiddenInput.value = ev.target.value;
                        $(`#option_${currentOptionId}_error`).text('');
                        $(newMathFieldElement).removeClass('is-invalid');
                    });
                    // Agar oldindan qiymat bo'lsa, MathLive komponentiga o'rnatish
                    if (initialValue) {
                        newMathFieldElement.setValue(initialValue);
                    }
                }

                // Radio tugma o'zgarishini kuzatish
                $(`#correct-option-${currentOptionId}`).on('change', function() {
                    $('#correct_option_id_error').text(''); // To'g'ri javob tanlansa xatoni tozalash
                });
            }

            // Sahifa yuklanganda MathLive komponentlariga oldingi qiymatlarni o'rnatish
            // (Laravel old() funksiyasi bilan)
            if (questionMathField && questionHiddenInput.value) {
                questionMathField.setValue(questionHiddenInput.value);
            }

            // Oldingi variantlar mavjud bo'lmasa, dastlabki 2 ta variantni qo'shish
            if (optionsList.children().length === 0) {
                addOption();
                addOption();
            } else {
                // Agar oldingi variantlar mavjud bo'lsa, ularning MathLive fieldlarini init qilish
                optionsList.find('.option-item').each(function(index) {
                    const mathFieldElement = $(this).find('.option-math-field').get(0);
                    const hiddenInput = $(this).find('input[type="hidden"][name^="options["]').get(0);
                    if (mathFieldElement && hiddenInput) {
                        mathFieldElement.setValue(hiddenInput.value);
                        mathFieldElement.addEventListener('input', (ev) => {
                            hiddenInput.value = ev.target.value;
                            $(`#option_${$(this).data('option-id')}_error`).text('');
                            $(mathFieldElement).removeClass('is-invalid');
                        });
                    }
                });
            }


            addOptionBtn.on('click', function() {
                if (optionsList.children().length < 5) { // Maksimal 5 tagacha variant
                    addOption();
                } else {
                    showCustomAlert('Ogohlantirish', 'Ko\'pi bilan 5 ta variant qo\'shishingiz mumkin.');
                }
            });

            optionsList.on('click', '.remove-option-btn', function() {
                if (optionsList.children().length > 2) { // Minimal 2 ta variant qolishi kerak
                    $(this).closest('.option-item').remove();
                    updateOptionIndices();
                } else {
                    showCustomAlert('Ogohlantirish', 'Kamida 2 ta variant bo\'lishi kerak.');
                }
            });

            // Variantlar o'chirilganda indekslarni yangilash funksiyasi
            function updateOptionIndices() {
                optionsList.children('.option-item').each(function(index) {
                    const newOptionId = index;

                    $(this).attr('data-option-id', newOptionId);

                    // Math-field va yashirin input nomini yangilash
                    const mathField = $(this).find('.option-math-field');
                    const hiddenInput = $(this).find(`input[type="hidden"][name^="options["]`);
                    const radioInput = $(this).find('input[type="radio"]');
                    const label = $(this).find('label[for^="correct-option-"]');
                    const errorSpan = $(this).find('.option-error');

                    mathField.attr('data-option-index', newOptionId);
                    hiddenInput.attr('name', `options[${newOptionId}][text]`);
                    hiddenInput.attr('id', `option-${newOptionId}`);
                    radioInput.attr('id', `correct-option-${newOptionId}`);
                    radioInput.attr('value', newOptionId);
                    label.attr('for', `correct-option-${newOptionId}`);
                    errorSpan.attr('id', `option_${newOptionId}_error`);
                });

                // Agar o'chirilgan variant to'g'ri javob bo'lgan bo'lsa va u o'chirilgan bo'lsa,
                // yoki to'g'ri javob qolmagan bo'lsa (faqat 2 ta qolganda va to'g'risi o'chirilgan bo'lsa)
                // birinchisini avtomatik tanlash
                if ($('input[name="correct_option_id"]:checked').length === 0 && optionsList.children().length >
                    0) {
                    optionsList.find('input[type="radio"]').first().prop('checked', true);
                }
            }

            // Desmos kalkulyatorini ishga tushirish va boshqarish
            toggleGraphBtn.on('click', function() {
                if (graphContainer.is(':hidden')) {
                    graphContainer.slideDown();
                    $('#graph-toggle-text').text('Grafikni o\'chirish');
                    if (!calculator) {
                        const elt = document.getElementById('calculator');
                        calculator = Desmos.Calculator(elt, {
                            expressionsCollapsed: false,
                            keypad: true,
                            graphpaper: true,
                            settingsMenu: true
                        });

                        calculator.on('change', function() {
                            graphStateHiddenInput.val(JSON.stringify(calculator.getState()));
                        });

                        // Agar oldingi grafik holati mavjud bo'lsa, uni yuklash
                        let savedGraphState = graphStateHiddenInput.val();
                        if (savedGraphState) {
                            try {
                                calculator.setState(JSON.parse(savedGraphState));
                            } catch (e) {
                                console.error('Error loading Desmos graph state:', e);
                            }
                        }
                    } else {
                        // Agar kalkulyator mavjud bo'lsa va qayta ochilsa, oldingi holatni yuklash
                        let savedGraphState = graphStateHiddenInput.val();
                        if (savedGraphState) {
                            try {
                                calculator.setState(JSON.parse(savedGraphState));
                            } catch (e) {
                                console.error('Error loading Desmos graph state on reopen:', e);
                            }
                        }
                    }
                } else {
                    graphContainer.slideUp();
                    $('#graph-toggle-text').text('Grafik qo\'shish');
                    // Grafik o'chirilganda yashirin inputni tozalash o'rniga, mavjud holatni saqlash yaxshiroq
                    // Chunki foydalanuvchi yana grafikni ochishi mumkin va u oldingi holatda bo'lishi kerak.
                    // graphStateHiddenInput.val('');
                }
            });

            // Agar sahifa `old('graph_state')` bilan yuklangan bo'lsa, grafikni avtomatik ochish va yuklash
            if (graphStateHiddenInput.val()) {
                toggleGraphBtn.trigger('click'); // Grafikni ochish uchun tugmani simulyatsiya qilish
            }

            // Rasm yuklash funksionalligi
            questionImageUpload.on('change', function(event) {
                const file = event.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        imagePreview.attr('src', e.target.result);
                        questionImageBase64Input.val(e.target
                            .result); // Base64 ni yashirin inputga saqlash
                        imagePreviewContainer.slideDown();
                        $('#question_image_error').text(''); // Xatoni tozalash
                        $(questionImageUpload).removeClass('is-invalid');
                    };
                    reader.onerror = function() {
                        $('#question_image_error').text('Rasmni yuklashda xatolik yuz berdi.');
                        imagePreviewContainer.slideUp();
                        imagePreview.attr('src', '#');
                        questionImageBase64Input.val('');
                        $(questionImageUpload).addClass('is-invalid');
                    };
                    reader.readAsDataURL(file); // Rasmni Base64 ga o'girish
                } else {
                    imagePreviewContainer.slideUp();
                    imagePreview.attr('src', '#');
                    questionImageBase64Input.val('');
                }
            });

            // Rasmni o'chirish tugmasi
            removeImageBtn.on('click', function() {
                questionImageUpload.val(''); // File inputni tozalash
                questionImageBase64Input.val(''); // Yashirin inputni tozalash
                imagePreview.attr('src', '#'); // Preview rasmini tozalash
                imagePreviewContainer.slideUp(); // Preview konteynerini yashirish
                $('#question_image_error').text(''); // Xatoni tozalash
                $(questionImageUpload).removeClass('is-invalid');
            });

            // Agar oldingi rasm Base64 formatida mavjud bo'lsa, uni yuklash (Laravel kontekstida)
            if (questionImageBase64Input.val()) {
                imagePreview.attr('src', questionImageBase64Input.val());
                imagePreviewContainer.show(); // Show if value exists
            }


            // Custom Alert Modal (Bootstrap modalidan foydalangan holda)
            function showCustomAlert(title, message) {
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
                    customAlertModalElement.addEventListener('hidden.bs.modal', function(event) {
                        this.remove(); // Modal yopilganda uni DOM dan o'chirish
                    });
                } else {
                    $(customAlertModalElement).find('.modal-title').html(title);
                    $(customAlertModalElement).find('.modal-body').html(message);
                }

                const customAlertModal = new bootstrap.Modal(customAlertModalElement);
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
                    customConfirmModalElement.addEventListener('hidden.bs.modal', function(event) {
                        this.remove(); // Modal yopilganda uni DOM dan o'chirish
                    });
                } else {
                    $(customConfirmModalElement).find('.modal-title').html(title);
                    $(customConfirmModalElement).find('.modal-body').html(message);
                }

                const customConfirmModal = new bootstrap.Modal(customConfirmModalElement);

                // "Ha" tugmasiga event listener qo'shish
                const confirmActionBtn = document.getElementById('confirmActionBtn');
                confirmActionBtn.onclick = function() {
                    callback();
                    customConfirmModal.hide();
                };

                customConfirmModal.show();
            }

            // Forma yuborilganda oddiy validatsiya va serverga jo'natish
            $('#question-form').on('submit', function(event) {
                event.preventDefault(); // Standart submitni to'xtatish

                // Xatolarni tozalash
                $('.error-message').text('');
                $('math-field').removeClass('is-invalid');
                $('input[type="file"]').removeClass('is-invalid');
                $('#general-errors').addClass('d-none');
                // $('#error-list').empty();

                let isValid = true;
                const errors = [];

                // Savol matnini validatsiya qilish
                // Agar savol matni ham, rasm ham bo'sh bo'lsa xato beradi
                if (!questionHiddenInput.value.trim() && !questionImageBase64Input.val().trim() && !
                    graphStateHiddenInput.val().trim()) {
                    $('#question_text_error').text('Savol matni, rasm yoki grafik majburiy.');
                    $(questionMathField).addClass('is-invalid');
                    isValid = false;
                }


                // Variantlarni validatsiya qilish
                const optionFields = optionsList.find('.option-math-field');
                if (optionFields.length < 2) {
                    errors.push('Kamida 2 ta variant bo\'lishi kerak.');
                    isValid = false;
                }
                optionFields.each(function(index) {
                    const hiddenInput = $(this).next('input[type="hidden"]');
                    if (!hiddenInput.val().trim()) {
                        $(`#option_${$(this).data('option-index')}_error`).text(
                            'Variant matni majburiy.');
                        $(this).addClass('is-invalid');
                        isValid = false;
                    }
                });

                // To'g'ri javobni tanlash validatsiyasi
                if ($('input[name="correct_option_id"]:checked').length === 0) {
                    $('#correct_option_id_error').text('To\'g\'ri javobni belgilashingiz shart.');
                    isValid = false;
                }

                // if (!isValid) {
                //     if (errors.length > 0) {
                //         errors.forEach(err => $('#error-list').append(`<li>${err}</li>`));
                //         $('#general-errors').removeClass('d-none');
                //     }
                //     showCustomAlert('Validatsiya xatosi',
                //         'Iltimos, barcha majburiy maydonlarni to\'ldiring va xatolarni to\'g\'irlang.');
                //     return;
                // }

                // Agar hamma narsa to'g'ri bo'lsa, formani yuborish
                // Bu yerda siz AJAX orqali serverga ma'lumotlarni yuborishingiz mumkin
                // Yoki oddiy forma submit qilish uchun:
                // this.submit(); // Formani serverga yuborish (Laravelda bu routerga POST so'rov yuboradi)

                // AJAX orqali yuborish (sizning izohli kodingizga asosan)
                const formData = new FormData(this); // Fayl yuklash uchun FormData ishlatish kerak

                // Base64 rasmini FormData ga qo'shish (agar mavjud bo'lsa)
                if (questionImageBase64Input.val()) {
                    formData.append('question_image_base64', questionImageBase64Input.val());
                }

                // Desmos grafik holatini FormData ga qo'shish (agar mavjud bo'lsa)
                if (graphStateHiddenInput.val()) {
                    formData.append('graph_state', graphStateHiddenInput.val());
                }

                $.ajax({
                    url: $(this).attr('action'),
                    method: $(this).attr('method'),
                    data: formData,
                    processData: false, // FormData ishlatilganda kerak
                    contentType: false, // FormData ishlatilganda kerak
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr(
                            'content') // Laravel CSRF tokeni
                    },
                    success: function(response) {
                        showCustomAlert('Muvaffaqiyat!', 'Savol muvaffaqiyatli saqlandi.');
                        // Formani tozalash yoki boshqa sahifaga yo'naltirish
                        $('#question-form')[0].reset();
                        // MathLive maydonlarini tozalash
                        if (questionMathField) questionMathField.setValue('');
                        optionsList.empty(); // Variantlarni tozalash
                        addOption(); // Qayta 2 ta bo'sh variant qo'shish
                        addOption();
                        removeImageBtn.trigger('click'); // Rasmni o'chirish
                        if (graphContainer.is(':visible')) {
                            toggleGraphBtn.trigger('click'); // Grafikni yopish
                        }
                        // window.location.href = response.redirect_url; // Agar serverdan yo'naltirish URL kelsa
                    },
                    error: function(xhr) {
                        let errorMessage = 'Xatolik yuz berdi. Iltimos, qayta urinib ko\'ring.';
                        if (xhr.responseJSON && xhr.responseJSON.message) {
                            errorMessage = xhr.responseJSON.message;
                        }
                        showCustomAlert('Xatolik!', errorMessage);
                        // Serverdan kelgan validatsiya xatolarini ko'rsatish
                        if (xhr.responseJSON && xhr.responseJSON.errors) {
                            const serverErrors = xhr.responseJSON.errors;
                            for (const field in serverErrors) {
                                if (serverErrors.hasOwnProperty(field)) {
                                    const msg = serverErrors[field][0];
                                    if (field === 'question_text') {
                                        $('#question_text_error').text(msg);
                                        $(questionMathField).addClass('is-invalid');
                                    } else if (field === 'question_image_base64') {
                                        $('#question_image_error').text(msg);
                                        $(questionImageUpload).addClass('is-invalid');
                                    } else if (field === 'graph_state') {
                                        // Grafik xatosini qanday ko'rsatishni hal qiling
                                        // Hozircha umumiy xatolarga qo'shish mumkin
                                        $('#error-list').append(`<li>Grafik: ${msg}</li>`);
                                        $('#general-errors').removeClass('d-none');
                                    } else if (field.startsWith('options.')) {
                                        const indexMatch = field.match(/options\.(\d+)\.text/);
                                        if (indexMatch) {
                                            const index = indexMatch[1];
                                            $(`#option_${index}_error`).text(msg);
                                            $(`[data-option-index="${index}"]`).addClass(
                                                'is-invalid');
                                        }
                                    } else if (field === 'correct_option_id') {
                                        $('#correct_option_id_error').text(msg);
                                    } else {
                                        $('#error-list').append(`<li>${msg}</li>`);
                                        $('#general-errors').removeClass('d-none');
                                    }
                                }
                            }
                        }
                    }
                });
            });
        });
    </script>
@endsection
