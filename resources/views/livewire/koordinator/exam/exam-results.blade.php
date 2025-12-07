<div>
    {{-- MathJax Script --}}
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
                processEnvironments: true
            },
            svg: {
                fontCache: 'global'
            },
            startup: {
                pageReady: () => {
                    return MathJax.startup.defaultPageReady().then(() => {
                        console.log('MathJax yuklandi');
                    });
                }
            }
        };

        window.addEventListener('renderMathJax', () => {
            setTimeout(() => {
                if (window.MathJax) {
                    MathJax.typesetPromise().catch(err => console.log(err));
                }
            }, 100);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml-full.js"></script>

    <style>
        :root {
            --yuksalish-orange: #F58025;
            --yuksalish-dark: #212529;
            --yuksalish-gray: #f8f9fa;
        }

        /* MathJax shriftini to'g'rilash */
        mjx-container {
            font-size: 1.15em !important;
            outline: none !important;
        }

        .btn-yuksalish {
            background-color: var(--yuksalish-orange);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-yuksalish:hover {
            background-color: #d96d1b;
            color: white;
            transform: translateY(-1px);
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--yuksalish-orange);
            box-shadow: 0 0 0 3px rgba(245, 128, 37, 0.1);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .mobile-student-card {
            border-left: 4px solid var(--yuksalish-orange);
            background: white;
            margin-bottom: 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .mobile-exam-item {
            background-color: #fffbf8;
            border-radius: 8px;
            padding: 12px;
            margin-bottom: 8px;
            border: 1px solid #ffeacc;
        }

        .modal-header-colored {
            background-color: var(--yuksalish-orange);
            color: white;
            border-bottom: none;
        }

        .modal-header-colored .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
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
    </style>

    {{-- Messages --}}
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mx-3 mt-3" style="background-color: #d1e7dd; color: #0f5132;">
        <i class="ri-checkbox-circle-line me-2"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif
    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm mx-3 mt-3">
        <i class="ri-error-warning-line me-2"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="container-fluid py-4">
        {{-- HEADER --}}
        <div class="card mb-4">
            <div class="card-header bg-white py-4">
                <div class="d-flex align-items-center">
                    <div class="bg-light rounded-circle p-2 me-3 d-flex justify-content-center align-items-center" style="width: 45px; height: 45px;">
                        <i class="ri-survey-line" style="color: var(--yuksalish-orange); font-size: 1.4rem;"></i>
                    </div>
                    <div>
                        <h4 class="mb-0 fw-bold text-dark">Test Natijalari</h4>
                        <p class="text-muted small mb-0">O'quvchilarning barcha test natijalari tahlili</p>
                    </div>
                </div>
            </div>

            <div class="card-body p-4 bg-light bg-opacity-10">
                {{-- Filters --}}
                <div class="row g-3">
                    <div class="col-12 col-md-3">
                        <label class="form-label small fw-bold text-muted">Qidirish</label>
                        <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Ism yoki familiya...">
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label small fw-bold text-muted">Sinf</label>
                        <select wire:model.live="classFilter" class="form-select">
                            <option value="">Barchasi</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-12 col-md-2">
                        <label class="form-label small fw-bold text-muted">Fan</label>
                        <select wire:model.live="subjectFilter" class="form-select">
                            <option value="">Barchasi</option>
                            @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-muted">Dan</label>
                        <input type="date" wire:model.live="dateFrom" class="form-control">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label small fw-bold text-muted">Gacha</label>
                        <input type="date" wire:model.live="dateTo" class="form-control">
                    </div>
                    <div class="col-12 col-md-1 d-flex align-items-end">
                        <button wire:click="$refresh" class="btn btn-light border w-100 shadow-sm"><i class="ri-refresh-line"></i></button>
                    </div>
                </div>
            </div>
        </div>

        {{-- STUDENTS LIST (DESKTOP) --}}
        <div class="d-none d-md-block">
            @forelse($students as $student)
            <div class="card mb-3">
                <div class="card-header bg-white py-3 border-bottom">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center gap-3">
                            <div class="avatar bg-light text-primary rounded-circle d-flex justify-content-center align-items-center fw-bold" style="width: 40px; height: 40px;">
                                {{ substr($student->first_name, 0, 1) }}
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</h6>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">{{ $student->class_name }}</span>
                            </div>
                        </div>
                        <span class="badge bg-light text-dark border px-3 py-2 rounded-pill">
                            <i class="ri-file-list-3-line me-1 text-muted"></i> {{ $student->total_exams }} ta test
                        </span>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($student->exams && count($student->exams) > 0)
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light text-muted small text-uppercase">
                                <tr>
                                    <th class="ps-4 py-3">#</th>
                                    <th>Test nomi</th>
                                    <th>Fan</th>
                                    <th class="text-center">Natija</th>
                                    <th class="text-center">To'g'ri/Jami</th>
                                    <th class="text-center">Holat</th>
                                    <th>Sana</th>
                                    <th class="text-end pe-4">Amal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($student->exams as $index => $exam)
                                <tr>
                                    <td class="ps-4 fw-bold text-muted">{{ $index + 1 }}</td>
                                    <td><span class="fw-bold text-dark">{{ $exam->quiz_name }}</span></td>
                                    <td><span class="badge bg-info bg-opacity-10 text-info border border-info border-opacity-25">{{ $exam->subject_name }}</span></td>
                                    <td class="text-center">
                                        <div class="d-flex flex-column align-items-center">
                                            <div class="progress w-75" style="height: 6px;">
                                                <div class="progress-bar {{ $exam->passed ? 'bg-success' : 'bg-danger' }}" style="width: {{ $exam->percentage }}%"></div>
                                            </div>
                                            <span class="small fw-bold mt-1">{{ $exam->percentage }}%</span>
                                        </div>
                                    </td>
                                    <td class="text-center"><span class="badge bg-light text-dark border">{{ $exam->correct_answers }} / {{ $exam->total_questions }}</span></td>
                                    <td class="text-center">
                                        @if($exam->passed) <span class="badge bg-success bg-opacity-10 text-success"><i class="ri-checkbox-circle-line me-1"></i>O'tdi</span>
                                        @else <span class="badge bg-danger bg-opacity-10 text-danger"><i class="ri-close-circle-line me-1"></i>O'tmadi</span> @endif
                                    </td>
                                    <td class="text-muted small"><i class="ri-calendar-line me-1"></i> {{ \Carbon\Carbon::parse($exam->created_at)->format('d.m.Y H:i') }}</td>
                                    <td class="text-end pe-4">
                                        <button wire:click="viewDetails({{ $exam->id }})" class="btn btn-sm btn-light border text-primary shadow-sm hover-lift">
                                            <i class="ri-eye-line"></i>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-4">
                        <p class="text-muted mb-0 small">Test natijalari mavjud emas</p>
                    </div>
                    @endif
                </div>
            </div>
            @empty
            <div class="text-center py-5">
                <h6 class="text-muted">Ma'lumot topilmadi</h6>
            </div>
            @endforelse
        </div>

        {{-- STUDENTS LIST (MOBILE) --}}
        <div class="d-md-none">
            @forelse($students as $student)
            <div class="mobile-student-card p-3">
                <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                    <div>
                        <h6 class="fw-bold mb-0 text-dark">{{ $student->first_name }} {{ $student->last_name }}</h6>
                        <small class="text-muted">{{ $student->class_name }}</small>
                    </div>
                    <span class="badge bg-light text-primary border">{{ $student->total_exams }} ta test</span>
                </div>
                @if($student->exams && count($student->exams) > 0)
                <div class="d-flex flex-column gap-2">
                    @foreach($student->exams as $exam)
                    <div class="mobile-exam-item position-relative">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <div class="fw-bold text-dark small mb-1">{{ $exam->quiz_name }}</div>
                                <span class="badge bg-white text-muted border small fw-normal">{{ $exam->subject_name }}</span>
                            </div>
                            <div class="text-end">
                                @php $badgeClass = $exam->percentage >= 80 ? 'badge-score-high' : ($exam->percentage >= 50 ? 'badge-score-mid' : 'badge-score-low'); @endphp
                                <span class="badge {{ $badgeClass }} rounded-pill border">{{ $exam->percentage }}%</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top border-secondary-subtle border-opacity-10">
                            <small class="text-muted" style="font-size: 0.75rem;">{{ \Carbon\Carbon::parse($exam->created_at)->format('d.m H:i') }}</small>
                            <div class="d-flex align-items-center gap-2">
                                <span class="small fw-bold text-muted">{{ $exam->correct_answers }}/{{ $exam->total_questions }}</span>
                                <button wire:click="viewDetails({{ $exam->id }})" class="btn btn-sm btn-white border shadow-sm text-primary py-0 px-2" style="font-size: 0.8rem;">Ko'rish</button>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="text-center py-3 text-muted small bg-light rounded">Natijalar yo'q</div>
                @endif
            </div>
            @empty
            <div class="text-center py-5 text-muted">Ma'lumot topilmadi</div>
            @endforelse
        </div>

        <div class="mt-4">{{ $students->links() }}</div>
    </div>

    {{-- DETAIL MODAL --}}
    @if($showDetailModal && !empty($selectedExam))
    {{-- ✅ YANGI: Z-INDEX 3000 ga OSHIRILDI --}}
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); backdrop-filter: blur(2px); z-index: 3000;">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header modal-header-colored">
                    <div>
                        <h5 class="modal-title fw-bold mb-1"><i class="ri-file-text-line me-2"></i> Batafsil Natija</h5>
                        <div class="d-flex gap-2 opacity-75 small align-items-center">
                            {{-- Array syntax --}}
                            <span><i class="ri-user-line me-1"></i> {{ $selectedExam['first_name'] }} {{ $selectedExam['last_name'] }}</span>
                            <span>|</span>
                            <span>{{ $selectedExam['class_name'] }}</span>
                        </div>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                </div>

                <div class="modal-body p-4 bg-light">
                    {{-- Statistika --}}
                    <div class="row g-3 mb-4">
                        @php
                        $examDetailsColl = collect($examDetails);
                        $totalQ = $examDetailsColl->count();
                        $correctQ = $examDetailsColl->where('is_correct', true)->count();
                        $wrongQ = $examDetailsColl->where('is_correct', false)->count();
                        $percent = $totalQ > 0 ? round(($correctQ / $totalQ) * 100) : 0;
                        $color = $percent >= 70 ? 'text-success' : 'text-danger';
                        @endphp
                        <div class="col-4">
                            <div class="card border-0 shadow-sm h-100 bg-white">
                                <div class="card-body text-center p-3">
                                    <div class="text-success mb-1"><i class="ri-checkbox-circle-fill fs-3"></i></div>
                                    <h4 class="fw-bold text-dark mb-0">{{ $correctQ }}</h4>
                                    <small class="text-muted">To'g'ri</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card border-0 shadow-sm h-100 bg-white">
                                <div class="card-body text-center p-3">
                                    <div class="text-danger mb-1"><i class="ri-close-circle-fill fs-3"></i></div>
                                    <h4 class="fw-bold text-dark mb-0">{{ $wrongQ }}</h4>
                                    <small class="text-muted">Xato</small>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="card border-0 shadow-sm h-100 bg-white">
                                <div class="card-body text-center p-3">
                                    <div class="{{ $color }} mb-1"><i class="ri-percent-line fs-3"></i></div>
                                    <h4 class="fw-bold text-dark mb-0">{{ $percent }}%</h4>
                                    <small class="text-muted">Natija</small>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Questions List --}}
                    <div class="d-flex flex-column gap-3">
                        @foreach($examDetails as $detail)
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-light text-dark border">Savol #{{ $detail['number'] }}</span>
                                    @if($detail['is_correct'])
                                    <span class="badge bg-success bg-opacity-10 text-success"><i class="ri-check-line me-1"></i> To'g'ri</span>
                                    @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger"><i class="ri-close-line me-1"></i> Xato</span>
                                    @endif
                                </div>
                                {{-- MathJax --}}
                                <div class="mb-3 fw-bold text-dark">{!! $detail['question_text'] !!}</div>
                                @if($detail['question_image']) <img src="{{ asset('storage/' . $detail['question_image']) }}" class="img-fluid rounded border mb-3" style="max-height: 150px;"> @endif

                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="p-3 rounded border h-100 {{ $detail['is_correct'] ? 'bg-success-subtle border-success' : 'bg-danger-subtle border-danger' }}">
                                            <small class="text-muted d-block mb-1">O'quvchi javobi:</small>
                                            <div class="fw-medium text-dark">{!! $detail['selected_answer'] !!}</div>
                                            @if($detail['selected_image']) <img src="{{ asset('storage/' . $detail['selected_image']) }}" class="img-fluid rounded mt-2" style="max-height: 80px;"> @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded border h-100 bg-light">
                                            <small class="text-success d-block mb-1 fw-bold">To'g'ri javob:</small>
                                            <div class="fw-medium text-dark">{!! $detail['correct_answer'] !!}</div>
                                            @if($detail['correct_image']) <img src="{{ asset('storage/' . $detail['correct_image']) }}" class="img-fluid rounded mt-2" style="max-height: 80px;"> @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer border-top bg-white">
                    <button wire:click="closeDetailModal" class="btn btn-light w-100">Yopish</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>