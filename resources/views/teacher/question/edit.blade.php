@extends('teacher.layouts.main')

@section('content')
    <style>
        /* Sizning mavjud CSS stylingizni bu yerga joylashtiraman */
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: flex-start;
            min-height: 100vh;
            margin: 20px;
        }

        .form-container {
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
            width: calc(100% - 20px);
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
            box-sizing: border-box;
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
        }

        .option-item input[type="radio"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
        }

        .option-item input[type="text"] {
            flex-grow: 1;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }

        .add-option-btn {
            background-color: #28a745;
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
            align-self: flex-end;
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
        }

        /* Validatsiya xatolari uchun stil */
        .error-message {
            color: #dc3545;
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
            }

            .option-item {
                flex-wrap: wrap;
            }

            .option-item input[type="text"] {
                width: calc(100% - 30px);
                margin-left: 28px;
            }

            .option-item input[type="radio"] {
                order: 1;
            }

            .option-item .remove-option-btn {
                order: 3;
            }

            .option-item label {
                order: 2;
                flex-grow: 1;
            }

            .submit-btn {
                width: 100%;
            }
        }
    </style>

    <div class="form-container">
        <h2>Savolni tahrirlash</h2>

        {{-- Umumiy xatolarni ko'rsatish (agar mavjud bo'lsa) --}}
        @if ($errors->any())
            <div class="alert alert-danger"
                style="background-color: #f8d7da; color: #721c24; padding: 10px; border-radius: 5px; margin-bottom: 15px;">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Form action update metodiga ishora qiladi --}}
        <form action="{{ route('teacher.question.update', $question->id) }}" method="POST" id="question-form">
            @csrf
            @method('PUT') {{-- PUT metodi tahrirlash uchun ishlatiladi --}}

            {{-- Quiz ID uchun yashirin input --}}
            {{-- `$question->quiz_id` orqali mavjud quiz ID ni ko'rsatamiz. --}}
            <input type="hidden" name="quiz_id" value="{{ $question->quiz_id }}">

            {{-- Status uchun yashirin input (yoki select) --}}
            <input type="hidden" name="status" value="{{ $question->status }}">

            <div class="form-group">
                <label for="question_text">Savol matni:</label>
                {{-- `$question->question_text` orqali savol matnini oldindan to'ldiramiz. --}}
                <textarea id="question_text" name="question_text" rows="4" required
                    placeholder="Savol matnini bu yerga kiriting. Matematik formulalar uchun LaTeX dan foydalaning, masalan: $x^2 + y^2 = r^2$"
                    class="@error('question_text') is-invalid @enderror">{{ $question->name }}</textarea>
                <div class="math-preview" id="question-preview"></div>
                @error('question_text')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <div class="options-section">
                <h3>Variantlar:</h3>
                <div id="options-list">
                    {{-- Mavjud variantlarni ko'rsatamiz. --}}
                    @foreach ($question->options as $index => $option)
                        <div class="option-item" data-option-id="{{ $index }}">
                            {{-- `is_correct` xususiyatiga qarab radio tugmani belgilaymiz. --}}
                            <input type="radio" name="correct_option_id" id="correct-option-{{ $index }}"
                                value="{{ $index }}" {{ $option->is_correct ? 'checked' : '' }}>
                            <label for="correct-option-{{ $index }}">To'g'ri</label>
                            {{-- `option_text` orqali variant matnini to'ldiramiz. --}}
                            <input type="text" name="options[{{ $index }}][text]"
                                id="option-{{ $index }}"
                                class="option-text-input @error('options.' . $index . '.text') is-invalid @enderror"
                                placeholder="Variant matnini kiriting" value="{{ $option->name }}" required>
                            <button type="button" class="remove-option-btn">O'chirish</button>
                            <div class="math-preview option-preview"></div>
                            @error('options.' . $index . '.text')
                                <span class="error-message">{{ $message }}</span>
                            @enderror
                        </div>
                    @endforeach
                </div>
                <button type="button" class="add-option-btn" id="add-option">Variant qo'shish</button>
            </div>

            <div class="form-group">
                {{-- `correct_option` radio tugmalarini endi har bir option-item ichida boshqaramiz,
                     bu qism endi ortiqcha bo'ladi, lekin validatsiya xatosi uchun joy qoldiramiz. --}}
                @error('correct_option_id')
                    <span class="error-message">{{ $message }}</span>
                @enderror
            </div>

            <button type="submit" class="submit-btn">Savolni saqlash</button>
        </form>
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
            }
        };

        $(document).ready(function() {
            const optionsList = $('#options-list');
            const addOptionBtn = $('#add-option');
            const questionTextarea = $('#question_text');
            const questionPreview = $('#question-preview');

            // Mavjud variantlar sonidan boshlaymiz.
            let optionCounter = optionsList.children().length > 0 ? optionsList.children().length - 1 :
            0; // Bu yerda o'zgartirish kiritildi, eng katta indeksni topish yaxshiroq bo'lar edi.

            // Eng katta mavjud data-option-id ni topish
            optionsList.children().each(function() {
                const id = parseInt($(this).attr('data-option-id'));
                if (!isNaN(id) && id >= optionCounter) {
                    optionCounter = id + 1; // Keyingi variant uchun to'g'ri indeksni belgilash
                }
            });


            // Savol matni o'zgarishini kuzatish va MathJax ni yangilash
            questionTextarea.on('input', function() {
                const text = $(this).val();
                questionPreview.text(text);
                MathJax.typesetPromise([questionPreview.get(0)]).then(function() {
                    // MathJax render qilgandan keyin hech narsa qilish shart emas
                }).catch(function(err) {
                    console.error('MathJax rendering error for question:', err);
                });
            }).trigger('input'); // Sahifa yuklanganda MathJax ni bir marta ishga tushirish

            // Yangi variant qo'shish funksiyasi
            function addOption(initialValue = '', isChecked = false) {
                const currentOptionId = optionCounter++; // Oshirilgan indeksni ishlatamiz
                const optionHtml = `
                    <div class="option-item" data-option-id="${currentOptionId}">
                        <input type="radio" name="correct_option_id" id="correct-option-${currentOptionId}" value="${currentOptionId}" ${isChecked ? 'checked' : ''}>
                        <label for="correct-option-${currentOptionId}">To'g'ri</label>
                        <input type="text" name="options[${currentOptionId}][text]" id="option-${currentOptionId}" class="option-text-input" placeholder="Variant matnini kiriting" value="${initialValue}" required>
                        <button type="button" class="remove-option-btn">O'chirish</button>
                        <div class="math-preview option-preview"></div>
                    </div>
                `;
                optionsList.append(optionHtml);

                // Yangi qo'shilgan input uchun MathJax preview ni o'rnatish
                const newOptionInput = $(`#option-${currentOptionId}`);
                const newOptionPreview = newOptionInput.nextAll('.option-preview').first();

                newOptionInput.on('input', function() {
                    const text = $(this).val();
                    newOptionPreview.text(text);
                    MathJax.typesetPromise([newOptionPreview.get(0)]).then(function() {
                        // MathJax render qilgandan keyin hech narsa qilish shart emas
                    }).catch(function(err) {
                        console.error('MathJax rendering error for option:', err);
                    });
                }).trigger('input'); // Yangi qo'shilgan variantlar uchun ham darhol MathJax ni ishga tushiramiz
            }

            // Sahifa yuklanganda mavjud variantlar uchun MathJax preview ni o'rnatish
            optionsList.find('.option-item').each(function() {
                const optionInput = $(this).find('.option-text-input');
                const optionPreview = $(this).find('.option-preview');
                const text = optionInput.val();
                optionPreview.text(text);
                MathJax.typesetPromise([optionPreview.get(0)]).then(function() {
                    // MathJax render qilgandan keyin hech narsa qilish shart emas
                }).catch(function(err) {
                    console.error('MathJax rendering error for initial option:', err);
                });

                optionInput.on('input', function() {
                    const text = $(this).val();
                    optionPreview.text(text);
                    MathJax.typesetPromise([optionPreview.get(0)]).then(function() {
                        // MathJax render qilgandan keyin hech narsa qilish shart emas
                    }).catch(function(err) {
                        console.error('MathJax rendering error for option:', err);
                    });
                });
            });

            addOptionBtn.on('click', function() {
                if (optionsList.children().length < 5) { // Maksimal 5 tagacha variant
                    addOption();
                } else {
                    alert('Ko\'pi bilan 5 ta variant qo\'shishingiz mumkin.');
                }
            });

            optionsList.on('click', '.remove-option-btn', function() {
                if (optionsList.children().length > 2) { // Minimal 2 ta variant qolishi kerak
                    const removedOption = $(this).closest('.option-item');
                    const wasChecked = removedOption.find('input[type="radio"]').is(':checked');
                    removedOption.remove();
                    updateOptionIndices(); // Indekslarni yangilash
                    if (wasChecked && optionsList.children().length > 0) {
                        // Agar o'chirilgan variant to'g'ri javob bo'lgan bo'lsa, birinchi qolgan variantni avtomatik tanlash
                        optionsList.find('input[type="radio"]').first().prop('checked', true);
                    }
                } else {
                    alert('Kamida 2 ta variant bo\'lishi kerak.');
                }
            });

            // Variantlar o'chirilganda indekslarni yangilash funksiyasi
            function updateOptionIndices() {
                optionsList.children('.option-item').each(function(index) {
                    const newOptionId = index;

                    $(this).attr('data-option-id', newOptionId);

                    const optionTextInput = $(this).find('input[type="text"]');
                    optionTextInput.attr('name', `options[${newOptionId}][text]`);
                    optionTextInput.attr('id', `option-${newOptionId}`);

                    const optionRadioInput = $(this).find('input[type="radio"]');
                    optionRadioInput.attr('id', `correct-option-${newOptionId}`);
                    optionRadioInput.attr('value', newOptionId);

                    $(this).find('label[for^="correct-option-"]').attr('for',
                        `correct-option-${newOptionId}`);
                });
                // optionCounter ni qayta hisoblash
                optionCounter = optionsList.children().length;
            }
        });
    </script>
@endsection
