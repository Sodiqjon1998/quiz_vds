<div>
    <style>
        :root {
            --yuksalish-orange: #F58025;
            --yuksalish-dark: #212529;
        }

        /* Asosiy stillar o'zgarishsiz qoldi... */
        .mobile-exam-card {
            border-left: 5px solid var(--yuksalish-orange);
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.2s;
            cursor: pointer;
        }

        .mobile-exam-card:active {
            transform: scale(0.98);
        }

        .badge-score-high {
            background-color: #d1e7dd;
            color: #0f5132;
        }

        .badge-score-mid {
            background-color: #fff3cd;
            color: #856404;
        }

        .badge-score-low {
            background-color: #f8d7da;
            color: #721c24;
        }

        .search-box {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 8px 15px;
            display: flex;
            align-items: center;
        }

        .search-box:focus-within {
            border-color: var(--yuksalish-orange);
            box-shadow: 0 0 0 3px rgba(245, 128, 37, 0.1);
        }

        /* ---------------------------------------------------- */
        /* PROFESSIONAL MODAL CSS (O'ZGARTIRILDI) */
        /* ---------------------------------------------------- */

        .modal-backdrop-custom {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(5px);
            /* Orqa fonni xira qilish - zamonaviy ko'rinish */
            z-index: 9998;
            display: flex !important;
            /* MUHIM: Center o'rniga flex-start ishlatamiz */
            align-items: flex-start;
            justify-content: center;
            /* Yuqoridan joy tashlash */
            padding-top: 3rem;
            padding-bottom: 3rem;
        }

        .modal-dialog-custom {
            position: relative;
            width: 100%;
            max-width: 800px;
            /* Biroz ixchamroq */
            margin: 0 15px;
            /* Yon tomonlardan joy */
            z-index: 9999;
            animation: modalSlideDown 0.4s cubic-bezier(0.16, 1, 0.3, 1);
            /* Apple style animatsiya */
        }

        @keyframes modalSlideDown {
            from {
                opacity: 0;
                transform: translateY(-20px) scale(0.98);
            }

            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .modal-content {
            border-radius: 16px;
            border: 1px solid rgba(0, 0, 0, 0.1);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            background: white;
            /* MUHIM: Modal balandligini ekran bo'yicha cheklash */
            max-height: calc(100vh - 6rem);
            display: flex;
            flex-direction: column;
        }

        /* Header va Footer qotib turadi, faqat body scroll bo'ladi */
        .modal-header {
            padding: 1.25rem 1.5rem;
            background: #fff;
            border-bottom: 1px solid #f0f0f0;
            border-radius: 16px 16px 0 0;
            flex-shrink: 0;
            /* Siqilmaydi */
        }

        .modal-footer {
            padding: 1.25rem 1.5rem;
            background: #fff;
            border-top: 1px solid #f0f0f0;
            border-radius: 0 0 16px 16px;
            flex-shrink: 0;
            /* Siqilmaydi */
        }

        .modal-body-scroll {
            padding: 1.5rem;
            overflow-y: auto;
            /* Flex yordamida qolgan barcha joyni egallaydi */
            flex: 1;
        }

        /* Scrollbar dizayni */
        .modal-body-scroll::-webkit-scrollbar {
            width: 6px;
        }

        .modal-body-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .modal-body-scroll::-webkit-scrollbar-thumb {
            background-color: #e2e8f0;
            border-radius: 3px;
        }

        .modal-body-scroll::-webkit-scrollbar-thumb:hover {
            background-color: var(--yuksalish-orange);
        }

        .question-item {
            transition: all 0.2s;
            border: 1px solid #f0f0f0;
            border-left: 4px solid transparent;
        }

        .question-item:hover {
            background-color: #fafbfc;
            border-left-color: var(--yuksalish-orange);
            transform: translateY(-2px);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
        }

        .correct-answer-box {
            animation: slideIn 0.3s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Mobile responsive */
        @media (max-width: 768px) {
            .modal-backdrop-custom {
                padding-top: 1rem;
                padding-bottom: 1rem;
                align-items: flex-end;
                /* Mobileda pastdan chiqsa qulayroq */
            }

            .modal-dialog-custom {
                margin: 0;
                width: 100%;
                max-width: 100%;
            }

            .modal-content {
                max-height: 90vh;
                border-bottom-left-radius: 0;
                border-bottom-right-radius: 0;
            }

            /* Mobile animatsiyasi - pastdan tepaga */
            @keyframes modalSlideDown {
                from {
                    transform: translateY(100%);
                }

                to {
                    transform: translateY(0);
                }
            }
        }
    </style>

    <div class="container-fluid py-4">
        <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
            {{-- Header --}}
            <div class="card-header bg-white py-4 border-bottom-0">
                <div class="row align-items-center g-3">
                    <div class="col-12 col-xl-auto">
                        <div class="d-flex align-items-center">
                            <div class="bg-light rounded-circle p-2 me-2 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                                <i class="ri-survey-line" style="color: var(--yuksalish-orange); font-size: 1.4rem;"></i>
                            </div>
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">Imtihon Natijalari</h4>
                                <small class="text-muted">O'quvchilarning ishlash ko'rsatkichlari</small>
                            </div>
                        </div>
                    </div>

                    <div class="col-12 col-xl">
                        <div class="row g-2">
                            {{-- 1. SINF FILTER --}}
                            <div class="col-12 col-md-3">
                                <select wire:model.live="classId" class="form-select border-0 bg-light shadow-sm" style="border-radius: 10px; cursor: pointer;">
                                    <option value="">Barcha sinflar</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 2. QUIZ FILTER (YANGI) --}}
                            <div class="col-12 col-md-3">
                                <select wire:model.live="quizId" class="form-select border-0 bg-light shadow-sm" style="border-radius: 10px; cursor: pointer;">
                                    <option value="">Barcha Quizlar</option>
                                    @foreach($quizzes as $quiz)
                                    <option value="{{ $quiz->id }}">{{ \Illuminate\Support\Str::limit($quiz->name, 25) }}</option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- 3. QIDIRUV --}}
                            <div class="col-12 col-md-6">
                                <div class="search-box w-100">
                                    <i class="ri-search-line text-muted me-2"></i>
                                    <input wire:model.live.debounce.300ms="search" type="text" class="form-control border-0 shadow-none p-0" placeholder="Ism yoki familiya orqali qidiring...">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body p-0">
                {{-- DESKTOP TABLE VIEW --}}
                <div class="table-responsive d-none d-md-block">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light text-muted small text-uppercase">
                            <tr>
                                <th class="ps-4">ID</th>
                                <th>O'quvchi</th>
                                <th>Quiz / Fan</th>
                                <th class="text-center">Sana</th>
                                <th class="text-center">Tahlil (T / X / J)</th>
                                <th class="text-center">Natija</th>
                                <th class="text-end pe-4">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($exams as $exam)
                            @php
                            $total = $exam->answers->count();
                            $correct = $exam->answers->filter(fn($a) => $a->option && $a->option->is_correct)->count();
                            $incorrect = $total - $correct;
                            $percent = $total > 0 ? round(($correct / $total) * 100) : 0;
                            $badgeClass = $percent >= 80 ? 'badge-score-high' : ($percent >= 50 ? 'badge-score-mid' : 'badge-score-low');

                            // URINISH SONINI HISOBLASH
                            $attempt = \App\Models\Exam::where('user_id', $exam->user_id)
                            ->where('quiz_id', $exam->quiz_id)
                            ->where('created_at', '<=', $exam->created_at)
                                ->count();
                                @endphp
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">#{{ $exam->id }}</td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar bg-light text-primary rounded-circle me-2 d-flex justify-content-center align-items-center" style="width: 35px; height: 35px;">
                                                {{ substr($exam->user->first_name ?? 'U', 0, 1) }}
                                                {{ substr($exam->user->last_name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold">{{ $exam->user->first_name ?? 'Noma\'lum' }} {{ $exam->user->last_name ?? '' }}</div>
                                                <div class="d-flex align-items-center gap-2">
                                                    <small class="text-muted">ID: {{ $exam->user_id }}</small>
                                                    {{-- SINF NOMI --}}
                                                    <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25" style="font-size: 0.65rem;">
                                                        {{ $exam->user->class_name }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-bold">{{ $exam->quiz->name ?? '-' }}</div>

                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="badge bg-light text-dark border">{{ $exam->quiz->subject->name ?? 'Fan' }}</span>

                                            {{-- URINISH BELGISI (YANGI) --}}
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25" style="font-size: 0.7rem;">
                                                <i class="ri-history-line me-1"></i> {{ $attempt }}-urinish
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center text-muted">
                                        {{ $exam->created_at->format('d.m.Y H:i') }}
                                    </td>

                                    <td class="text-center">
                                        <div class="d-flex align-items-center justify-content-center gap-2">
                                            <div class="d-flex align-items-center px-2 py-1 rounded-pill border"
                                                style="background-color: #d1e7dd; color: #0f5132; border-color: #badbcc;" title="To'g'ri">
                                                <i class="ri-checkbox-circle-line me-1"></i> <span class="fw-bold small">{{ $correct }}</span>
                                            </div>
                                            <div class="d-flex align-items-center px-2 py-1 rounded-pill border"
                                                style="background-color: #f8d7da; color: #842029; border-color: #f5c2c7;" title="Xato">
                                                <i class="ri-close-circle-line me-1"></i> <span class="fw-bold small">{{ $incorrect }}</span>
                                            </div>
                                            <div class="d-flex align-items-center px-2 py-1 rounded-pill border"
                                                style="background-color: #cfe2ff; color: #084298; border-color: #b6d4fe;" title="Jami">
                                                <i class="ri-stack-line me-1"></i> <span class="fw-bold small">{{ $total }}</span>
                                            </div>
                                        </div>
                                    </td>

                                    <td class="text-center">
                                        <span class="badge {{ $badgeClass }} px-3 py-2 rounded-pill">
                                            {{ $percent }}%
                                        </span>
                                    </td>
                                    <td class="text-end pe-4">
                                        <button wire:click="showDetails({{ $exam->id }})" type="button" class="btn btn-sm btn-light text-primary border shadow-sm">
                                            <i class="ri-eye-line me-1"></i> Ko'rish
                                        </button>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">Ma'lumot topilmadi</td>
                                </tr>
                                @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE CARD VIEW --}}
                <div class="d-md-none p-3 bg-light">
                    @forelse($exams as $exam)
                    @php
                    $total = $exam->answers->count();
                    $correct = $exam->answers->filter(fn($a) => $a->option && $a->option->is_correct)->count();
                    $incorrect = $total - $correct;
                    $percent = $total > 0 ? round(($correct / $total) * 100) : 0;
                    $color = $percent >= 80 ? '#198754' : ($percent >= 50 ? '#ffc107' : '#dc3545');

                    // URINISH SONINI HISOBLASH
                    $attempt = \App\Models\Exam::where('user_id', $exam->user_id)
                    ->where('quiz_id', $exam->quiz_id)
                    ->where('created_at', '<=', $exam->created_at)
                        ->count();
                        @endphp

                        <div class="mobile-exam-card mb-3 p-3 position-relative" wire:click="showDetails({{ $exam->id }})">
                            <div class="d-flex justify-content-between mb-2">
                                <div style="max-width: 70%;">
                                    <h6 class="fw-bold mb-0 text-truncate">{{ $exam->user->first_name ?? 'Noma\'lum' }} {{ $exam->user->last_name ?? '' }}</h6>
                                    {{-- Urinish soni mobil uchun --}}
                                    <span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25 mt-1" style="font-size: 0.65rem;">
                                        {{ $attempt }}-urinish
                                    </span>
                                </div>
                                <span class="small text-muted">{{ $exam->created_at->format('d.m H:i') }}</span>
                            </div>

                            <div class="mb-3">
                                <p class="mb-1 text-dark small"><i class="ri-file-list-3-line me-1 text-muted"></i> {{ $exam->quiz->name ?? '-' }}</p>
                            </div>

                            <div class="d-flex justify-content-between align-items-center pt-2 border-top">
                                <div class="d-flex align-items-center">
                                    <div style="width: 45px; height: 45px; border-radius: 50%; background: {{ $color }}15; color: {{ $color }}; display: flex; align-items: center; justify-content: center; font-weight: bold; font-size: 0.9rem;">
                                        {{ $percent }}%
                                    </div>

                                    <div class="ms-3 d-flex gap-2">
                                        <span class="d-flex align-items-center px-2 py-1 rounded-pill border" style="background-color: #d1e7dd; color: #0f5132; border-color: #badbcc; font-size: 0.8rem;">
                                            <i class="ri-check-line me-1"></i> {{ $correct }}
                                        </span>
                                        <span class="d-flex align-items-center px-2 py-1 rounded-pill border" style="background-color: #f8d7da; color: #842029; border-color: #f5c2c7; font-size: 0.8rem;">
                                            <i class="ri-close-line me-1"></i> {{ $incorrect }}
                                        </span>
                                        <span class="d-flex align-items-center px-2 py-1 rounded-pill border" style="background-color: #cfe2ff; color: #084298; border-color: #b6d4fe; font-size: 0.8rem;">
                                            <i class="ri-stack-line me-1"></i> {{ $total }}
                                        </span>
                                    </div>
                                </div>
                                <button type="button" class="btn btn-sm btn-light text-primary"><i class="ri-arrow-right-s-line"></i></button>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5 text-muted">Hech qanday natija topilmadi</div>
                        @endforelse
                </div>

                {{-- Pagination & Summary (YANGILANGAN) --}}
                <div class="p-3 border-top d-flex flex-column flex-md-row justify-content-between align-items-center gap-3 bg-white" style="border-bottom-left-radius: 15px; border-bottom-right-radius: 15px;">

                    {{-- Chap taraf: Statistika --}}
                    <div class="text-muted small">
                        @if($exams->total() > 0)
                        Jami <span class="fw-bold text-dark">{{ $exams->total() }}</span> ta natijadan
                        <span class="fw-bold text-dark">{{ $exams->firstItem() }}</span> dan
                        <span class="fw-bold text-dark">{{ $exams->lastItem() }}</span> gachasi ko'rsatilmoqda
                        @else
                        Ma'lumot topilmadi
                        @endif
                    </div>

                    {{-- O'ng taraf: Sahifalash tugmalari --}}
                    <div>
                        {{ $exams->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- DETAIL MODAL (YANGILANGAN) --}}
    @if($showDetailModal && $selectedExam)
    {{-- Backdrop --}}
    <div class="modal-backdrop-custom" wire:click="closeDetailModal">
        {{-- Modal Dialog --}}
        <div class="modal-dialog-custom" onclick="event.stopPropagation()">
            <div class="modal-content">
                {{-- Fixed Header --}}
                <div class="modal-header">
                    <div class="d-flex align-items-center justify-content-between w-100">
                        <h5 class="modal-title fw-bold m-0">
                            <i class="ri-bar-chart-box-line me-2" style="color: var(--yuksalish-orange);"></i>
                            Natija tahlili
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                    </div>
                </div>

                {{-- Scrollable Body --}}
                <div class="modal-body modal-body-scroll">
                    {{-- Summary Card --}}
                    <div class="card border-0 mb-4 rounded-3" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 10px 20px -5px rgba(118, 75, 162, 0.4);">
                        <div class="card-body text-white p-4">
                            <div class="row text-center g-4">
                                <div class="col-md-4">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ri-user-line fs-1 mb-2 opacity-75"></i>
                                        <small class="text-white-50 text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">O'quvchi</small>
                                        <span class="fw-bold fs-6">{{ $selectedExam->user->first_name }} {{ $selectedExam->user->last_name }}</span>
                                    </div>
                                </div>
                                <div class="col-md-4 border-start border-white border-opacity-25">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ri-medal-line fs-1 mb-2 opacity-75"></i>
                                        <small class="text-white-50 text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">Ball</small>
                                        <span class="fw-bold" style="font-size: 2rem; line-height: 1;">{{ $examStats['percentage'] }}%</span>
                                    </div>
                                </div>
                                <div class="col-md-4 border-start border-white border-opacity-25">
                                    <div class="d-flex flex-column align-items-center">
                                        <i class="ri-check-double-line fs-1 mb-2 opacity-75"></i>
                                        <small class="text-white-50 text-uppercase mb-1" style="font-size: 0.65rem; letter-spacing: 1px;">Natija</small>
                                        <span class="fw-bold fs-5">{{ $examStats['correct'] }} / {{ $examStats['total'] }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Quiz Info --}}
                    <div class="card border-0 mb-4 bg-light rounded-3">
                        <div class="card-body p-3">
                            <div class="d-flex align-items-center">
                                <div class="bg-white rounded-3 p-3 shadow-sm me-3">
                                    <i class="ri-file-list-3-line fs-3" style="color: var(--yuksalish-orange);"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <h6 class="fw-bold mb-1">{{ $selectedExam->quiz->name ?? 'Quiz nomi' }}</h6>
                                    <div class="d-flex align-items-center gap-2 flex-wrap">
                                        <span class="badge bg-white text-primary border shadow-sm">
                                            <i class="ri-book-line me-1"></i>{{ $selectedExam->quiz->subject->name ?? 'Fan' }}
                                        </span>
                                        <span class="badge bg-white text-secondary border shadow-sm">
                                            <i class="ri-time-line me-1"></i>{{ $selectedExam->created_at->format('d.m.Y H:i') }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Questions List --}}
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h6 class="fw-bold mb-0">
                            <i class="ri-question-line me-2" style="color: var(--yuksalish-orange);"></i>
                            Savollar va javoblar
                        </h6>
                        <span class="badge bg-light text-dark border">
                            {{ $selectedExam->answers->count() }} ta savol
                        </span>
                    </div>

                    <div class="row g-3">
                        @foreach($selectedExam->answers as $answer)
                        @php
                        $isCorrect = $answer->option && $answer->option->is_correct;
                        $statusIcon = $isCorrect ? 'ri-checkbox-circle-fill' : 'ri-close-circle-fill';
                        $statusColor = $isCorrect ? 'success' : 'danger';
                        $bgGradient = $isCorrect ? 'linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%)' : 'linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%)';
                        @endphp
                        <div class="col-12">
                            <div class="card border-0 shadow-sm question-item rounded-3">
                                <div class="card-body">
                                    <div class="d-flex align-items-start gap-3">
                                        <div class="flex-shrink-0">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center shadow-sm"
                                                style="width: 45px; height: 45px; background: {{ $bgGradient }};">
                                                <i class="{{ $statusIcon }} fs-4 text-{{ $statusColor }}"></i>
                                            </div>
                                        </div>

                                        <div class="flex-grow-1">
                                            <div class="mb-3">
                                                <span class="badge bg-{{ $statusColor }} bg-opacity-10 text-{{ $statusColor }} border border-{{ $statusColor }} mb-2">Savol #{{ $loop->iteration }}</span>
                                                <div class="fw-bold text-dark fs-6">
                                                    {!! $answer->question->name ?? 'Savol topilmadi' !!}
                                                </div>
                                            </div>

                                            @if($answer->option)
                                            <div class="p-3 rounded-3 mb-2" style="background-color: {{ $isCorrect ? '#f0fff4' : '#fff5f5' }}; border: 1px solid {{ $isCorrect ? '#c6f6d5' : '#fed7d7' }};">
                                                <div class="d-flex align-items-start">
                                                    <i class="ri-user-voice-line me-2 mt-1 opacity-50"></i>
                                                    <div>
                                                        <div class="small text-uppercase text-muted fw-bold" style="font-size: 0.7rem;">Sizning javobingiz:</div>
                                                        <div class="fw-medium text-dark">{!! $answer->option->name !!}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @else
                                            <div class="alert alert-secondary border-0 mb-2 py-2" role="alert">
                                                <i class="ri-close-line me-2"></i>
                                                <span class="fw-medium">Javob belgilanmagan</span>
                                            </div>
                                            @endif

                                            {{-- To'g'ri javob --}}
                                            @if(!$isCorrect && $answer->question)
                                            @php
                                            $correctOption = $answer->question->options->firstWhere('is_correct', true);
                                            @endphp
                                            @if($correctOption)
                                            <div class="p-3 rounded-3 correct-answer-box bg-white border border-success border-opacity-25 shadow-sm">
                                                <div class="d-flex align-items-start">
                                                    <i class="ri-lightbulb-flash-line fs-5 me-2 mt-1 text-success"></i>
                                                    <div>
                                                        <div class="small text-uppercase text-success fw-bold" style="font-size: 0.7rem;">To'g'ri javob:</div>
                                                        <div class="text-dark fw-medium">{!! $correctOption->name !!}</div>
                                                    </div>
                                                </div>
                                            </div>
                                            @endif
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Fixed Footer --}}
                <div class="modal-footer bg-light justify-content-between">
                    <button type="button" class="btn btn-white border shadow-sm px-4" wire:click="closeDetailModal">
                        Yopish
                    </button>
                    <button type="button" class="btn btn-primary px-4 shadow-sm" style="background-color: var(--yuksalish-orange); border-color: var(--yuksalish-orange);" onclick="window.print()">
                        <i class="ri-printer-line me-2"></i> Chop etish
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>