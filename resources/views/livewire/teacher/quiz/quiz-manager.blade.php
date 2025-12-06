<div>
    <!-- MathJax 3 - TO'LIQ KONFIGURATSIYA -->
    <script>
        window.MathJax = {
            tex: {
                inlineMath: [
                    ['\\(', '\\)'],
                    ['$', '$']
                ],
                displayMath: [
                    ['\\[', '\\]'],
                    ['$$', '$$']
                ],
                processEscapes: true,
                processEnvironments: true,
                // Barcha paketlarni yoqish
                packages: {
                    '[+]': ['base', 'ams', 'noerrors', 'noundefined', 'autoload']
                }
            },
            svg: {
                fontCache: 'global'
            },
            startup: {
                pageReady: () => {
                    return MathJax.startup.defaultPageReady().then(() => {
                        console.log('✅ MathJax to\'liq yuklandi!');
                    });
                }
            }
        };

        window.addEventListener('renderMathJax', () => {
            setTimeout(() => {
                if (window.MathJax) {
                    MathJax.typesetPromise().then(() => {
                        console.log('✅ Formulalar render qilindi!');
                    });
                }
            }, 200);
        });

        // Livewire har safar yangilanganida MathJax-ni render qilish
        document.addEventListener('livewire:initialized', () => {
            Livewire.hook('morph.updated', ({
                el,
                component
            }) => {
                if (window.MathJax) {
                    setTimeout(() => {
                        MathJax.typesetPromise().then(() => {
                            console.log('✅ MathJax rendered!');
                        }).catch(err => {
                            console.error('❌ MathJax error:', err);
                        });
                    }, 100);
                }
            });
        });
    </script>

    <!-- MathJax Script - TO'LIQ VERSIYA -->
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml-full.js"></script>

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

        /* Mobile uchun maxsus stilllar */
        @media (max-width: 768px) {

            /* Savol matni */
            .question-text {
                font-size: 1rem !important;
                line-height: 1.6 !important;
                word-wrap: break-word;
                overflow-wrap: break-word;
            }

            /* MathJax formulalar */
            .MathJax {
                font-size: 1.1em !important;
            }

            /* Variantlar */
            .option-card {
                padding: 12px !important;
                min-height: auto !important;
            }

            /* Savol kartasi */
            .question-card {
                padding: 15px 10px !important;
            }

            /* Savol raqami */
            .question-number {
                min-width: 35px !important;
                font-size: 0.9rem !important;
            }
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

                            <div class="col-12 col-md-auto d-flex gap-2">
                                {{-- IMPORT BUTTON --}}
                                <button wire:click="openImportModal" class="btn btn-success text-white">
                                    <i class="ri-file-excel-2-line me-2"></i> Import
                                </button>

                                {{-- CREATE BUTTON --}}
                                <button wire:click="createQuiz" class="btn btn-yuksalish">
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

                    {{-- QUESTIONS LIST (YANGILANGAN DIZAYN) --}}
                    <div class="row g-3">
                        @forelse($this->questions as $index => $question)
                        <div class="col-12">
                            <div class="card border-0 shadow-sm position-relative overflow-hidden"
                                style="border-radius: 12px;">
                                <div class="position-absolute top-0 start-0 bottom-0 bg-warning" style="width: 5px;"></div>

                                <div class="card-body p-3 p-md-4 ps-3 ps-md-4">
                                    <div class="d-flex gap-2 gap-md-3 align-items-start">

                                        {{-- Savol Raqami --}}
                                        <div class="flex-shrink-0">
                                            <span class="badge bg-light text-dark border d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 30px; height: 30px; font-size: 0.85rem;">
                                                {{ $index + 1 }}
                                            </span>
                                        </div>

                                        {{-- Savol Matni --}}
                                        <div class="w-100" style="min-width: 0;">
                                            <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                                                <h6 class="fw-bold text-dark mb-2 flex-grow-1"
                                                    style="font-size: clamp(0.95rem, 2vw, 1.1rem); 
                               line-height: 1.5; 
                               word-break: break-word;">
                                                    {!! $this->formatMathForView($question->name) !!}
                                                </h6>

                                                {{-- Tugmalar --}}
                                                <div class="d-flex gap-2">
                                                    <button wire:click="editQuestion({{ $question->id }})"
                                                        class="btn btn-sm btn-light text-warning border shadow-sm"
                                                        title="Tahrirlash">
                                                        <i class="ri-pencil-line"></i>
                                                    </button>
                                                    <button onclick="if(confirm('Rostdan ham o\'chirmoqchimisiz?')) @this.call('deleteQuestion', {{ $question->id }})"
                                                        class="btn btn-sm btn-light text-danger border shadow-sm"
                                                        title="O'chirish">
                                                        <i class="ri-delete-bin-line"></i>
                                                    </button>
                                                </div>
                                            </div>

                                            {{-- Variantlar - Mobile Responsive --}}
                                            <div class="row g-2">
                                                @foreach($question->options as $opt)
                                                <div class="col-12 col-sm-6">
                                                    <div class="p-2 p-sm-3 rounded-3 border d-flex align-items-center
                            {{ $opt->is_correct ? 'bg-success-subtle border-success' : 'bg-white' }}"
                                                        style="font-size: clamp(0.85rem, 1.5vw, 1rem);">

                                                        <span class="fw-bold me-2 d-flex align-items-center justify-content-center rounded-circle border
                                {{ $opt->is_correct ? 'bg-success text-white' : 'bg-light text-secondary' }}"
                                                            style="min-width: 24px; width: 24px; height: 24px; font-size: 0.75rem;">
                                                            {{ chr(65 + $loop->index) }}
                                                        </span>

                                                        <span class="flex-grow-1 {{ $opt->is_correct ? 'fw-bold text-success-emphasis' : '' }}"
                                                            style="word-break: break-word; overflow-wrap: break-word;">
                                                            {!! $this->formatMathForView($opt->name) !!}
                                                        </span>

                                                        @if($opt->is_correct)
                                                        <i class="ri-checkbox-circle-fill text-success fs-6 ms-1"></i>
                                                        @endif
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="col-12 text-center py-5">
                            <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                                <i class="ri-question-answer-line text-muted opacity-50" style="font-size: 40px;"></i>
                            </div>
                            <h6 class="text-muted fw-bold">Bu quizda hali savollar yo'q</h6>
                            <p class="text-muted small mb-0">Yuqoridagi "Yangi Savol" tugmasi orqali qo'shishingiz mumkin</p>
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
                                <textarea wire:model.live="questionText" class="form-control border-start-0" rows="3" placeholder="LaTeX formulalar: \( x^2 \), \frac{1}{2}"></textarea>
                            </div>
                            @error('questionText') <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                            @if($questionText)
                            <div class="mt-2 p-3 bg-light rounded border" wire:ignore>
                                <small class="text-muted d-block mb-1">Ko'rinishi:</small>
                                <div id="question-preview">{!! $this->formatMathForView($questionText) !!}</div>
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
                                        <textarea wire:model.live="options.{{ $index }}" class="form-control form-control-sm border-0 bg-white" rows="2" placeholder="Variantni yozing... \( x^2 \)"></textarea>
                                        @error('options.'.$index) <div class="text-danger small mt-1">{{ $message }}</div> @enderror

                                        {{-- Preview --}}
                                        @if($options[$index])
                                        <div class="mt-2 p-2 bg-white rounded border border-secondary-subtle" wire:ignore>
                                            <small class="text-muted">Preview:</small>
                                            <div class="preview-{{ $index }}">{!! $this->formatMathForView($options[$index]) !!}</div>
                                        </div>
                                        @endif
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

    {{-- 5. VIEW MODAL (TUZATILGAN FINAL VERSIYA) --}}
    @if($showViewModal && $viewingQuiz)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); backdrop-filter: blur(5px); z-index: 1090;"
        tabindex="-1"
        x-data="{ init() { setTimeout(() => { if (window.MathJax) { MathJax.typesetPromise(); } }, 200); } }">
        <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg" style="border-radius: 16px; overflow: hidden;">

                {{-- Header --}}
                <div class="modal-header border-0 text-white p-4" style="background: linear-gradient(135deg, #F58025 0%, #ff9f5a 100%);">
                    <div class="d-flex w-100 justify-content-between align-items-start">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-white text-warning rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 50px; height: 50px;">
                                <i class="ri-file-text-line fs-3"></i>
                            </div>
                            <div>
                                <h5 class="fw-bold mb-1">{{ $viewingQuiz->name }}</h5>
                                <div class="d-flex gap-2 opacity-75 small">
                                    <span><i class="ri-calendar-line me-1"></i> {{ $viewingQuiz->created_at->format('d.m.Y') }}</span>
                                    <span>|</span>
                                    <span><i class="ri-question-line me-1"></i> {{ $viewingQuiz->questions->count() }} ta savol</span>
                                </div>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="modal-body bg-light p-4" wire:ignore.self>

                    {{-- Info Badges --}}
                    <div class="d-flex justify-content-center gap-3 mb-4">
                        <span class="badge bg-white text-primary border px-3 py-2 rounded-pill shadow-sm">
                            <i class="ri-book-open-line me-1"></i> {{ $viewingQuiz->subject->name ?? 'Fan' }}
                        </span>
                        <span class="badge bg-white text-warning border px-3 py-2 rounded-pill shadow-sm">
                            <i class="ri-group-line me-1"></i> {{ $viewingQuiz->class->name ?? 'Sinf' }}
                        </span>
                    </div>

                    {{-- Savollar Ro'yxati --}}
                    <div class="d-flex flex-column gap-3">
                        @foreach($viewingQuiz->questions as $index => $q)
                        <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-white">
                            <div class="card-body p-4">
                                <div class="d-flex gap-3">

                                    {{-- 1. SAVOL RAQAMI --}}
                                    <div class="flex-shrink-0">
                                        <div class="rounded-circle d-flex align-items-center justify-content-center fw-bold text-primary border border-primary bg-white shadow-sm"
                                            style="width: 40px; height: 40px; font-size: 1.1rem;">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>

                                    {{-- 2. SAVOL MATNI VA VARIANTLAR --}}
                                    <div class="w-100">
                                        {{-- Savol Matni --}}
                                        <div class="fw-bold text-dark mb-3" style="font-size: 1.15rem; line-height: 1.6;">
                                            {!! $this->formatMathForView($q->name) !!}
                                        </div>

                                        {{-- Rasm --}}
                                        @if($q->image)
                                        <div class="mb-3">
                                            <img src="{{ asset('storage/' . $q->image) }}" class="img-fluid rounded border shadow-sm" style="max-height: 250px;">
                                        </div>
                                        @endif

                                        {{-- Variantlar --}}
                                        <div class="row g-3">
                                            @foreach($q->options as $opt)
                                            <div class="col-md-6">
                                                <div class="p-3 rounded-3 border d-flex align-items-center h-100 position-relative shadow-sm
                                            {{ $opt->is_correct ? 'bg-success-subtle border-success' : 'bg-white border-light-subtle' }}"
                                                    style="transition: transform 0.2s;">

                                                    {{-- Harf --}}
                                                    <div class="flex-shrink-0 me-3">
                                                        <span class="d-flex align-items-center justify-content-center rounded-circle fw-bold border
                                                    {{ $opt->is_correct ? 'bg-success text-white border-success' : 'bg-light text-secondary border-secondary-subtle' }}"
                                                            style="width: 30px; height: 30px; font-size: 0.9rem;">
                                                            {{ chr(65 + $loop->index) }}
                                                        </span>
                                                    </div>

                                                    {{-- Variant Matni --}}
                                                    <div class="flex-grow-1 {{ $opt->is_correct ? 'text-success-emphasis fw-bold' : 'text-dark' }}">
                                                        {!! $this->formatMathForView($opt->name) !!}
                                                    </div>

                                                    {{-- To'g'ri javob belgisi --}}
                                                    @if($opt->is_correct)
                                                    <div class="ms-2 text-success">
                                                        <i class="ri-checkbox-circle-fill fs-4"></i>
                                                    </div>
                                                    @endif
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    @if($viewingQuiz->questions->isEmpty())
                    <div class="text-center py-5 text-muted">
                        <i class="ri-inbox-line fs-1 opacity-50"></i>
                        <p class="mt-2">Bu quizda hali savollar yo'q</p>
                    </div>
                    @endif

                </div>

                {{-- Footer --}}
                <div class="modal-footer border-top bg-white p-3">
                    <button type="button" wire:click="closeViewModal" class="btn btn-light w-100 py-2 fw-bold text-secondary border shadow-sm">
                        Yopish
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif


    {{-- IMPORT MODAL --}}
    @if($showImportModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); z-index: 1080;" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">
                        <i class="ri-upload-cloud-2-line text-success me-2"></i> Testlarni Import qilish
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeImportModal"></button>
                </div>
                <div class="modal-body">
                    {{-- Alert Info --}}
                    <div class="alert alert-info d-flex align-items-start small border-0 bg-info-subtle text-info-emphasis">
                        <i class="ri-information-line fs-5 me-2 mt-1"></i>
                        <div>
                            <strong>Excel fayl shabloni:</strong><br>
                            1-ustun: Savol matni<br>
                            2-5 ustunlar: Variantlar (A, B, C, D)<br>
                            6-ustun: To'g'ri javob harfi (A, B, C yoki D)
                        </div>
                    </div>

                    @if (session()->has('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                    @endif

                    <form wire:submit.prevent="importQuiz">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Sinfni tanlang <span class="text-danger">*</span></label>
                            <select wire:model="importClassId" class="form-select @error('importClassId') is-invalid @enderror">
                                <option value="">Tanlang...</option>
                                @foreach($classes as $class)
                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                @endforeach
                            </select>
                            @error('importClassId') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold">Fayl yuklash (.xlsx, .pdf)</label>
                            <div class="upload-area p-4 text-center cursor-pointer position-relative">
                                <input type="file" wire:model="importFile" class="position-absolute top-0 start-0 w-100 h-100 opacity-0" style="cursor: pointer;">
                                @if($importFile)
                                <div class="text-success fw-bold">
                                    <i class="ri-file-check-line fs-3 d-block mb-1"></i>
                                    {{ $importFile->getClientOriginalName() }}
                                </div>
                                @else
                                <div class="text-muted">
                                    <i class="ri-upload-2-line fs-3 d-block mb-1"></i>
                                    Faylni shu yerga tashlang yoki bosing
                                </div>
                                @endif
                            </div>
                            @error('importFile') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            <div wire:loading wire:target="importFile" class="text-primary small mt-1">Yuklanmoqda...</div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="importQuiz">Import qilish</span>
                                <span wire:loading wire:target="importQuiz"><i class="ri-loader-4-line ri-spin"></i> Jarayonda...</span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>