<div>
    {{-- MathJax Script (Formulalar uchun) --}}
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/3.2.2/es5/tex-mml-chtml.min.js"></script>
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('message.processed', () => {
                if (typeof MathJax !== 'undefined') {
                    MathJax.typesetPromise();
                }
            });
        });
    </script>

    <style>
        :root {
            --yuksalish-orange: #F58025;
            --yuksalish-dark: #212529;
            --yuksalish-gray: #f8f9fa;
        }

        /* Asosiy tugmalar */
        .btn-yuksalish {
            background-color: var(--yuksalish-orange);
            color: white;
            border: none;
            font-weight: 500;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            white-space: nowrap;
        }

        .btn-yuksalish:hover {
            background-color: #d96d1b;
            color: white;
        }

        /* Action Buttons (Kichik) */
        .btn-action-edit {
            color: #ffc107;
            background: #fff3cd;
            border: none;
        }

        .btn-action-delete {
            color: #dc3545;
            background: #f8d7da;
            border: none;
        }

        .btn-action-view {
            color: #0dcaf0;
            background: #cff4fc;
            border: none;
        }

        .btn-action-primary {
            color: var(--yuksalish-orange);
            background: #fff0e6;
            border: none;
        }

        .btn-action-edit:hover,
        .btn-action-delete:hover,
        .btn-action-view:hover,
        .btn-action-primary:hover {
            filter: brightness(0.95);
        }

        /* SEARCH BOX (Yangi dizayn) */
        .search-box {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 5px 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            height: 45px;
        }

        .search-box:focus-within {
            border-color: var(--yuksalish-orange);
            box-shadow: 0 0 0 4px rgba(245, 128, 37, 0.1);
        }

        .search-box i {
            color: #999;
            font-size: 1.2rem;
            margin-right: 10px;
        }

        /* Input ramkasiz */
        .form-control-plaintext {
            border: none;
            outline: none;
            width: 100%;
        }

        /* Sarlavha */
        .page-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--yuksalish-dark);
            margin: 0;
            white-space: nowrap;
        }

        /* Jadval */
        .table-yuksalish thead th {
            background-color: var(--yuksalish-dark);
            color: white;
            padding: 15px;
            font-weight: 500;
            border: none;
        }

        /* Mobile Card */
        .mobile-card {
            border-left: 5px solid var(--yuksalish-orange);
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }

        /* Badges */
        .badge-subject {
            background-color: #17a2b8;
            color: white;
        }

        .badge-class {
            background-color: var(--yuksalish-orange);
            color: white;
        }

        /* Pagination */
        .page-item.active .page-link {
            background-color: var(--yuksalish-orange);
            border-color: var(--yuksalish-orange);
        }

        .page-link {
            color: var(--yuksalish-orange);
        }

        /* Modal Header Fix */
        .modal-header {
            border-bottom: none;
            padding-bottom: 0;
        }

        .modal-footer {
            border-top: none;
        }

        /* File Upload Area */
        .upload-area {
            border: 2px dashed #dee2e6;
            border-radius: 10px;
            transition: all 0.3s;
            background: #fafafa;
        }

        .upload-area:hover {
            border-color: var(--yuksalish-orange);
            background: #fffbf8;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">

                    <div class="card-header bg-white py-4">
                        <div class="row align-items-center g-3">
                            <div class="col-12 col-md-auto">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-2 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                        <i class="ri-file-list-3-line" style="color: var(--yuksalish-orange); font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="page-title">Quizlar Ro'yxati</h4>
                                </div>
                            </div>

                            <div class="col-12 col-md">
                                <div class="search-box">
                                    <i class="ri-search-line"></i>
                                    <input wire:model.live.debounce.300ms="search"
                                        type="text"
                                        class="form-control border-0 shadow-none bg-transparent p-0"
                                        placeholder="Quiz nomini qidiring...">
                                </div>
                            </div>

                            <div class="col-12 col-md-auto">
                                <button wire:click="createQuiz" class="btn btn-yuksalish w-100">
                                    <i class="ri-add-circle-line me-2"></i> Yangi Quiz
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if (session()->has('message'))
                        <div class="p-3">
                            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
                                <i class="ri-checkbox-circle-line me-2"></i> {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                        @endif

                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle table-yuksalish mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 50px;">ID</th>
                                        <th>Quiz nomi</th>
                                        <th class="text-center">Fan / Sinf</th>
                                        <th class="text-center">Tarkib</th>
                                        <th class="text-center">Holat</th>
                                        <th class="text-center" style="width: 180px;">Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($quizzes as $quiz)
                                    <tr>
                                        <td class="text-center fw-bold text-secondary">#{{ $quiz->id }}</td>
                                        <td>
                                            <div class="fw-bold text-dark">{{ $quiz->name }}</div>
                                            <small class="text-muted">{{ $quiz->created_at->format('d.m.Y H:i') }}</small>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge badge-subject mb-1">{{ $quiz->subject->name ?? 'N/A' }}</span>
                                            <br>
                                            <span class="badge badge-class">{{ $quiz->class->name ?? 'N/A' }}</span>
                                        </td>
                                        <td class="text-center">
                                            <button wire:click="manageQuestions({{ $quiz->id }})" class="btn btn-sm btn-action-primary mb-1 w-100 text-start px-3">
                                                <i class="ri-question-line me-1"></i> {{ $quiz->questions_count }} Savol
                                            </button>
                                            <button wire:click="manageAttachments({{ $quiz->id }})" class="btn btn-sm btn-action-edit w-100 text-start px-3">
                                                <i class="ri-attachment-2 me-1"></i> Fayllar
                                            </button>
                                        </td>
                                        <td class="text-center">
                                            @if($quiz->status == \App\Models\Teacher\Quiz::STATUS_ACTIVE)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Faol</span>
                                            @else
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary rounded-pill px-3">Nofaol</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <div class="btn-group">
                                                <button wire:click="viewQuiz({{ $quiz->id }})" class="btn btn-sm btn-light text-primary border shadow-sm" title="Ko'rish">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button wire:click="editQuiz({{ $quiz->id }})" class="btn btn-sm btn-light text-warning border shadow-sm" title="Tahrirlash">
                                                    <i class="ri-pencil-line"></i>
                                                </button>
                                                <button wire:click="deleteQuiz({{ $quiz->id }})" onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')" class="btn btn-sm btn-light text-danger border shadow-sm" title="O'chirish">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">Ma'lumot topilmadi</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none p-3 bg-light">
                            @forelse($quizzes as $quiz)
                            <div class="mobile-card p-3">
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <div>
                                        <h6 class="fw-bold mb-0 text-dark">{{ $quiz->name }}</h6>
                                        <small class="text-muted">#{{ $quiz->id }} | {{ $quiz->created_at->format('d.m.Y') }}</small>
                                    </div>
                                    @if($quiz->status == \App\Models\Teacher\Quiz::STATUS_ACTIVE)
                                    <span class="badge bg-success bg-opacity-10 text-success">Faol</span>
                                    @else
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">Nofaol</span>
                                    @endif
                                </div>

                                <div class="mb-3 d-flex gap-2">
                                    <span class="badge badge-subject flex-fill">{{ $quiz->subject->name ?? 'N/A' }}</span>
                                    <span class="badge badge-class flex-fill">{{ $quiz->class->name ?? 'N/A' }}</span>
                                </div>

                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <button wire:click="manageQuestions({{ $quiz->id }})" class="btn btn-sm btn-light border w-100 text-primary">
                                            <i class="ri-question-line"></i> {{ $quiz->questions_count }} Savol
                                        </button>
                                    </div>
                                    <div class="col-6">
                                        <button wire:click="manageAttachments({{ $quiz->id }})" class="btn btn-sm btn-light border w-100 text-warning">
                                            <i class="ri-attachment-2"></i> Fayllar
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 border-top pt-2">
                                    <button wire:click="viewQuiz({{ $quiz->id }})" class="btn btn-light border flex-fill text-primary"><i class="ri-eye-line"></i></button>
                                    <button wire:click="editQuiz({{ $quiz->id }})" class="btn btn-light border flex-fill text-warning"><i class="ri-pencil-line"></i></button>
                                    <button wire:click="deleteQuiz({{ $quiz->id }})" onclick="return confirm('O\'chirish?')" class="btn btn-light border flex-fill text-danger"><i class="ri-delete-bin-line"></i></button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted">Ma'lumot topilmadi</div>
                            @endforelse
                        </div>

                        <div class="mt-4 px-3 pb-3">
                            {{ $quizzes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 1. CREATE/EDIT QUIZ MODAL --}}
    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); backdrop-filter: blur(3px);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold text-dark">
                        @if($isEdit) <i class="ri-pencil-line text-warning me-2"></i>Quizni Tahrirlash
                        @else <i class="ri-add-circle-line" style="color: var(--yuksalish-orange);"></i> Yangi Quiz
                        @endif
                    </h5>
                    <button type="button" wire:click="closeModal" class="btn-close"></button>
                </div>
                <form wire:submit.prevent="saveQuiz">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Quiz nomi <span class="text-danger">*</span></label>
                                <input type="text" wire:model.live="name" class="form-control search-input @error('name') is-invalid @enderror" placeholder="Masalan: Matematika 1-chorak">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <div class="alert alert-light border d-flex align-items-center">
                                    <i class="ri-book-line text-primary me-3 fs-4"></i>
                                    <div>
                                        <small class="text-muted d-block">Sizning Faningiz:</small>
                                        <strong>{{ Auth::user()->subject->name ?? 'Aniqlanmadi' }}</strong>
                                    </div>
                                </div>
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Sinf <span class="text-danger">*</span></label>
                                <select wire:model="classes_id" class="form-select search-input @error('classes_id') is-invalid @enderror">
                                    <option value="">Tanlang</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                                @error('classes_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-light">Bekor qilish</button>
                        <button type="submit" class="btn btn-yuksalish">{{ $isEdit ? 'Yangilash' : 'Saqlash' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- 2. QUESTIONS MANAGER MODAL --}}
    @if($showQuestionsModal && $currentQuiz)
    {{-- Z-INDEX 2000 ga o'zgartirildi --}}
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.6); z-index: 2000; backdrop-filter: blur(3px);" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-scrollable" style="margin-top: 20px;"> {{-- margin-top qo'shildi --}}
            <div class="modal-content border-0 shadow-lg" style="border-radius: 12px;">

                <div class="modal-header bg-white border-bottom py-3 px-4 d-flex justify-content-between align-items-center">
                    <div class="d-flex flex-column">
                        <h5 class="modal-title fw-bold text-dark mb-1 d-flex align-items-center">
                            <i class="ri-question-answer-line me-2" style="color: var(--yuksalish-orange);"></i>
                            {{ $currentQuiz->name }}
                        </h5>
                        <div class="d-flex gap-2">
                            <span class="badge badge-subject d-flex align-items-center">
                                <i class="ri-book-line me-1"></i> {{ $currentQuiz->subject->name }}
                            </span>
                            <span class="badge badge-class d-flex align-items-center">
                                <i class="ri-graduation-cap-line me-1"></i> {{ $currentQuiz->class->name }}
                            </span>
                        </div>
                    </div>
                    <button type="button" class="btn-close bg-light p-2 rounded-circle" wire:click="closeQuestionsModal"></button>
                </div>

                {{-- Modal Body qismi (o'zgarishsiz) --}}
                <div class="modal-body bg-light px-4 pb-4">
                    {{-- ... bu yerda search va list kodlari turadi ... --}}
                    {{-- SEARCH & ADD BUTTON --}}
                    <div class="card border-0 shadow-sm mb-4 mt-3" style="border-radius: 10px;">
                        <div class="card-body p-3">
                            <div class="row align-items-center g-3">
                                <div class="col-md">
                                    <div class="search-box">
                                        <i class="ri-search-line"></i>
                                        <input wire:model.live="questionSearch"
                                            type="text"
                                            class="form-control border-0 shadow-none bg-transparent p-0"
                                            placeholder="Savol matnini qidirish...">
                                    </div>
                                </div>
                                <div class="col-md-auto">
                                    <button wire:click="createQuestion" class="btn btn-yuksalish w-100">
                                        <i class="ri-add-line me-1"></i> Yangi Savol
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- QUESTIONS LIST --}}
                    <div class="row g-3">
                        @forelse($this->questions as $index => $question)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm position-relative" style="border-radius: 10px; overflow: hidden;">
                                <div class="position-absolute top-0 start-0 bottom-0 bg-warning" style="width: 4px;"></div>
                                <div class="card-body ps-4">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div class="w-100 me-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <span class="badge bg-light text-dark border me-2">#{{ $index + 1 }}</span>
                                                <h6 class="fw-bold text-dark mb-0 text-break">{!! $question->name !!}</h6>
                                            </div>
                                            @if($question->image)
                                            <img src="{{ asset('storage/' . $question->image) }}" class="img-thumbnail mb-3 rounded" style="max-height: 120px;">
                                            @endif
                                            @php
                                            $letters = ['A', 'B', 'C', 'D'];
                                            $correctIndex = $question->options->search(fn($o) => $o->is_correct);
                                            @endphp
                                            @if($correctIndex !== false)
                                            <div class="d-inline-flex align-items-center px-3 py-2 rounded bg-success-subtle text-success border border-success-subtle mt-2">
                                                <span class="fw-bold me-2 bg-success text-white rounded-circle d-flex justify-content-center align-items-center" style="width: 24px; height: 24px; font-size: 12px;">
                                                    {{ $letters[$correctIndex] }}
                                                </span>
                                                <span class="fw-medium">{!! $question->options[$correctIndex]->name !!}</span>
                                            </div>
                                            @else
                                            <div class="text-danger small mt-2"><i class="ri-error-warning-line"></i> To'g'ri javob belgilanmagan!</div>
                                            @endif
                                        </div>
                                        <div class="d-flex flex-column gap-2">
                                            <button wire:click="editQuestion({{ $question->id }})" class="btn btn-sm btn-light text-warning border shadow-sm" title="Tahrirlash"><i class="ri-pencil-line fs-5"></i></button>
                                            <button wire:click="deleteQuestion({{ $question->id }})" onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')" class="btn btn-sm btn-light text-danger border shadow-sm" title="O'chirish"><i class="ri-delete-bin-line fs-5"></i></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-5">
                            <div class="text-muted opacity-50"><i class="ri-question-answer-line" style="font-size: 60px;"></i></div>
                            <h6 class="text-muted mt-3">Bu quizda hali savollar yo'q</h6>
                            <p class="text-muted small">Yangi savol qo'shish uchun yuqoridagi tugmani bosing</p>
                        </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 3. SAVOL FORM MODALI --}}
    @if($showQuestionFormModal)
    {{-- Z-INDEX 2050 (Eng tepada) --}}
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.7); z-index: 2050;" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-white border-bottom">
                    <h5 class="modal-title fw-bold text-dark">
                        @if($isEditQuestion)
                        <i class="ri-pencil-line text-warning me-2"></i>Savolni Tahrirlash
                        @else
                        <i class="ri-add-circle-line text-success me-2"></i>Yangi Savol
                        @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeQuestionFormModal"></button>
                </div>
                <div class="modal-body px-4 py-4">
                    <form wire:submit.prevent="saveQuestion">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Savol matni <span class="text-danger">*</span></label>
                            <div class="input-group shadow-sm">
                                <span class="input-group-text bg-white text-muted"><i class="ri-text"></i></span>
                                <textarea wire:model="questionText" class="form-control border-start-0" rows="3" placeholder="LaTeX formulalar: \( x^2 \)"></textarea>
                            </div>
                            @error('questionText') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            @if($questionText)
                            <div class="mt-2 p-3 bg-light rounded border"><small class="text-muted d-block mb-1">Ko'rinishi:</small>
                                <div>{!! $questionText !!}</div>
                            </div>
                            @endif
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Rasm <small class="text-muted fw-normal">(ixtiyoriy)</small></label>
                            @if($existingImage)
                            <div class="d-flex align-items-center gap-3 mb-2 p-2 border rounded bg-light">
                                <img src="{{ asset('storage/' . $existingImage) }}" style="height: 60px; border-radius: 5px;">
                                <button type="button" wire:click="removeImage" class="btn btn-sm btn-danger">Rasmni o'chirish</button>
                            </div>
                            @endif
                            <input type="file" wire:model="questionImage" class="form-control">
                            @error('questionImage') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <label class="form-label fw-bold mb-3 text-dark">Javob variantlari <span class="text-danger">*</span></label>
                        <div class="row g-3">
                            @foreach(['A', 'B', 'C', 'D'] as $index => $letter)
                            <div class="col-md-6">
                                <div class="card h-100 {{ $correctOption == $index ? 'border-success bg-success-subtle' : 'border-light bg-light' }} shadow-sm"
                                    style="transition: all 0.2s; cursor: pointer;"
                                    onclick="document.getElementById('opt_{{$index}}').click()">
                                    <div class="card-body p-3">
                                        <div class="d-flex align-items-center mb-2">
                                            <div class="form-check">
                                                <input type="radio" id="opt_{{$index}}" wire:model="correctOption" value="{{ $index }}" class="form-check-input me-2" style="transform: scale(1.2);">
                                                <label class="form-check-label fw-bold" for="opt_{{$index}}">{{ $letter }} varianti</label>
                                            </div>
                                        </div>
                                        <textarea wire:model="options.{{ $index }}" class="form-control form-control-sm border-0 bg-white" rows="2" placeholder="Variantni yozing..."></textarea>
                                        @error('options.'.$index) <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @error('correctOption') <div class="alert alert-danger mt-3 py-2"><i class="ri-error-warning-line me-1"></i> {{ $message }}</div> @enderror
                    </form>
                </div>
                <div class="modal-footer bg-light border-top-0">
                    <button type="button" wire:click="closeQuestionFormModal" class="btn btn-light border">Bekor qilish</button>
                    <button type="button" wire:click="saveQuestion" class="btn btn-success text-white shadow-sm px-4">Saqlash</button>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 4. ATTACHMENT MODAL --}}
    @if($showAttachmentModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.6); z-index: 1070;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold"><i class="ri-attachment-2 me-2 text-warning"></i>Fayllar</h5>
                    <button type="button" class="btn-close" wire:click="closeAttachmentModal"></button>
                </div>
                <div class="modal-body bg-light">
                    <div class="card border-0 shadow-sm mb-4">
                        <div class="card-body">
                            <h6 class="fw-bold mb-3">Yangi fayl ma'lumotlari</h6>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="small text-muted">Sana</label>
                                    <input type="date" wire:model="attachmentDate" class="form-control search-input">
                                    @error('attachmentDate') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted">Vaqt</label>
                                    <input type="time" wire:model="attachmentTime" class="form-control search-input">
                                    @error('attachmentTime') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-md-4">
                                    <label class="small text-muted">Raqam</label>
                                    <input type="number" wire:model="attachmentNumber" class="form-control search-input">
                                    @error('attachmentNumber') <span class="text-danger small">{{ $message }}</span> @enderror
                                </div>
                                <div class="col-12 text-end">
                                    <button wire:click="saveAttachment" class="btn btn-warning text-white shadow-sm">Qo'shish</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card border-0 shadow-sm">
                        <div class="card-body p-0">
                            <table class="table table-hover mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-3">#</th>
                                        <th>Sana / Vaqt</th>
                                        <th>Raqam</th>
                                        <th class="text-end pe-3">Amal</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($this->attachments as $att)
                                    <tr>
                                        <td class="ps-3">{{ $loop->iteration }}</td>
                                        <td>{{ \Carbon\Carbon::parse($att->date)->format('d.m.Y') }} <small class="text-muted">{{ $att->time }}</small></td>
                                        <td><span class="badge bg-secondary">{{ $att->number }}</span></td>
                                        <td class="text-end pe-3">
                                            <button wire:click="deleteAttachment({{ $att->id }})" class="btn btn-sm btn-light text-danger border"><i class="ri-delete-bin-line"></i></button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">Fayllar yo'q</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif

    {{-- 5. VIEW MODAL --}}
    @if($showViewModal && $viewingQuiz)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); backdrop-filter: blur(3px);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body p-0">
                    <div class="p-4 text-center text-white" style="background-color: var(--yuksalish-orange); border-radius: 8px 8px 0 0;">
                        <div class="avatar bg-white text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                            <i class="ri-file-text-line"></i>
                        </div>
                        <h5 class="fw-bold mb-1">{{ $viewingQuiz->name }}</h5>
                        <p class="mb-0 opacity-75">{{ $viewingQuiz->created_at->format('d.m.Y') }}</p>
                    </div>

                    <div class="p-4">
                        <div class="row g-3 text-center">
                            <div class="col-4">
                                <small class="text-muted d-block">Fan</small>
                                <span class="fw-bold text-dark">{{ $viewingQuiz->subject->name }}</span>
                            </div>
                            <div class="col-4 border-start border-end">
                                <small class="text-muted d-block">Sinf</small>
                                <span class="fw-bold text-dark">{{ $viewingQuiz->class->name }}</span>
                            </div>
                            <div class="col-4">
                                <small class="text-muted d-block">Savollar</small>
                                <span class="fw-bold text-primary">{{ $viewingQuiz->questions->count() }} ta</span>
                            </div>
                        </div>

                        <hr>

                        <h6 class="fw-bold mb-3">Savollar ro'yxati:</h6>
                        <div class="accordion" id="viewQuizAccordion">
                            @foreach($viewingQuiz->questions as $index => $q)
                            <div class="accordion-item border-0 mb-2 shadow-sm">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed bg-light" type="button" data-bs-toggle="collapse" data-bs-target="#viewQ{{ $q->id }}">
                                        <span class="fw-bold me-2 text-primary">#{{ $index + 1 }}</span> {!! Str::limit(strip_tags($q->name), 50) !!}
                                    </button>
                                </h2>
                                <div id="viewQ{{ $q->id }}" class="accordion-collapse collapse" data-bs-parent="#viewQuizAccordion">
                                    <div class="accordion-body">
                                        <div class="mb-2">{!! $q->name !!}</div>
                                        @if($q->image) <img src="{{ asset('storage/' . $q->image) }}" class="img-thumbnail mb-2" style="max-height: 100px;"> @endif
                                        <ul class="list-group list-group-flush">
                                            @foreach($q->options as $opt)
                                            <li class="list-group-item {{ $opt->is_correct ? 'bg-success-subtle text-success fw-bold' : '' }}">
                                                {{ $loop->iteration }}) {!! $opt->name !!}
                                                @if($opt->is_correct) <i class="ri-check-line float-end"></i> @endif
                                            </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    <div class="modal-footer border-0 p-4 pt-0">
                        <button type="button" wire:click="closeViewModal" class="btn btn-light w-100">Yopish</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>