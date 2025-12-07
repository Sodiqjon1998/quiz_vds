<div>
    {{-- MathJax Script --}}
    <script>
        window.MathJax = {
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
        window.addEventListener('renderMathJax', () => {
            setTimeout(() => {
                if (window.MathJax) MathJax.typesetPromise();
            }, 100);
        });
    </script>
    <script src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-chtml-full.js"></script>

    <style>
        :root {
            --yuksalish-orange: #F58025;
        }

        mjx-container {
            font-size: 1.1em !important;
            outline: none;
        }

        .btn-yuksalish {
            background-color: var(--yuksalish-orange);
            color: white;
            border: none;
        }

        .btn-yuksalish:hover {
            background-color: #d96d1b;
            color: white;
        }

        .mobile-card {
            border-left: 4px solid var(--yuksalish-orange);
            background: white;
            margin-bottom: 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.05);
        }

        .modal-header-colored {
            background-color: var(--yuksalish-orange);
            color: white;
        }
    </style>

    {{-- HEADER --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom">
            <h4 class="mb-0 fw-bold text-dark"><i class="ri-file-list-3-line me-2 text-warning"></i> Test Natijalari (Admin)</h4>
        </div>
        <div class="card-body p-4 bg-light bg-opacity-10">
            {{-- Filters --}}
            <div class="row g-3">
                <div class="col-md-3">
                    <label class="form-label small fw-bold text-muted">Qidirish</label>
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control" placeholder="Ism, familiya, test...">
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Sinf</label>
                    <select wire:model.live="classFilter" class="form-select">
                        <option value="">Barchasi</option>
                        @foreach($classes as $class) <option value="{{ $class->id }}">{{ $class->name }}</option> @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small fw-bold text-muted">Fan</label>
                    <select wire:model.live="subjectFilter" class="form-select">
                        <option value="">Barchasi</option>
                        @foreach($subjects as $subject) <option value="{{ $subject->id }}">{{ $subject->name }}</option> @endforeach
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
                <div class="col-md-1 d-flex align-items-end">
                    <button wire:click="$refresh" class="btn btn-yuksalish w-100"><i class="ri-refresh-line"></i></button>
                </div>
            </div>
        </div>
    </div>

    {{-- DESKTOP TABLE --}}
    <div class="d-none d-md-block card border-0 shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4">O'quvchi</th>
                        <th>Sinf</th>
                        <th>Test nomi</th>
                        <th>Fan</th>
                        <th class="text-center">Natija</th>
                        <th class="text-center">Ball</th>
                        <th>Sana</th>
                        <th class="text-end pe-4">Amal</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($results as $item)
                    <tr>
                        <td class="ps-4 fw-bold">{{ $item->first_name }} {{ $item->last_name }}</td>
                        <td><span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">{{ $item->class_name ?? '-' }}</span></td>
                        <td>{{ $item->quiz_name }}</td>
                        <td><span class="badge bg-info bg-opacity-10 text-info">{{ $item->subject_name }}</span></td>
                        <td class="text-center">
                            <span class="badge {{ $item->passed ? 'bg-success' : 'bg-danger' }}">{{ $item->percentage }}%</span>
                        </td>
                        <td class="text-center"><small class="text-muted">{{ $item->correct_answers }} / {{ $item->total_questions }}</small></td>
                        <td class="text-muted small">{{ \Carbon\Carbon::parse($item->created_at)->format('d.m.Y H:i') }}</td>
                        <td class="text-end pe-4">
                            <button wire:click="viewDetails({{ $item->id }})" class="btn btn-sm btn-light border text-primary"><i class="ri-eye-line"></i></button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">Ma'lumot topilmadi</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- MOBILE LIST --}}
    <div class="d-md-none">
        @forelse($results as $item)
        <div class="mobile-card p-3">
            <div class="d-flex justify-content-between align-items-center mb-2 border-bottom pb-2">
                <div>
                    <h6 class="fw-bold mb-0 text-dark">{{ $item->first_name }} {{ $item->last_name }}</h6>
                    <small class="text-muted">{{ $item->class_name }}</small>
                </div>
                <span class="badge {{ $item->passed ? 'bg-success' : 'bg-danger' }}">{{ $item->percentage }}%</span>
            </div>
            <div class="mb-2 small">
                <div class="d-flex justify-content-between mb-1">
                    <span class="text-muted">Test:</span> <span class="fw-bold">{{ $item->quiz_name }}</span>
                </div>
                <div class="d-flex justify-content-between">
                    <span class="text-muted">Fan:</span> <span>{{ $item->subject_name }}</span>
                </div>
            </div>
            <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-light">
                <small class="text-muted">{{ \Carbon\Carbon::parse($item->created_at)->format('d.m H:i') }}</small>
                <button wire:click="viewDetails({{ $item->id }})" class="btn btn-sm btn-light border w-50">Ko'rish</button>
            </div>
        </div>
        @empty
        <div class="text-center py-5 text-muted">Ma'lumot topilmadi</div>
        @endforelse
    </div>

    <div class="mt-4">{{ $results->links() }}</div>

    {{-- DETAIL MODAL --}}
    @if($showDetailModal && !empty($selectedExam))
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); z-index: 3000;">
        <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header modal-header-colored">
                    <div>
                        <h5 class="modal-title fw-bold mb-1">Batafsil Natija</h5>
                        <small class="opacity-75">{{ $selectedExam['first_name'] }} {{ $selectedExam['last_name'] }} | {{ $selectedExam['class_name'] }}</small>
                    </div>
                    <button type="button" class="btn-close" wire:click="closeDetailModal" style="filter: invert(1);"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="d-flex flex-column gap-3">
                        @foreach($examDetails as $detail)
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <span class="badge bg-light text-dark border">Savol #{{ $detail['number'] }}</span>
                                    @if($detail['is_correct']) <span class="badge bg-success bg-opacity-10 text-success"><i class="ri-check-line"></i> To'g'ri</span>
                                    @else <span class="badge bg-danger bg-opacity-10 text-danger"><i class="ri-close-line"></i> Xato</span> @endif
                                </div>
                                <div class="mb-3 fw-bold text-dark">{!! $detail['question_text'] !!}</div>
                                @if($detail['question_image']) <img src="{{ asset('storage/' . $detail['question_image']) }}" class="img-fluid rounded border mb-3" style="max-height: 150px;"> @endif
                                <div class="row g-2">
                                    <div class="col-md-6">
                                        <div class="p-3 rounded border h-100 {{ $detail['is_correct'] ? 'bg-success-subtle border-success' : 'bg-danger-subtle border-danger' }}">
                                            <small class="text-muted d-block mb-1">O'quvchi javobi:</small>
                                            <div class="fw-medium">{!! $detail['selected_answer'] !!}</div>
                                            @if($detail['selected_image']) <img src="{{ asset('storage/' . $detail['selected_image']) }}" class="img-fluid rounded mt-2" style="max-height: 80px;"> @endif
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="p-3 rounded border h-100 bg-light">
                                            <small class="text-success d-block mb-1 fw-bold">To'g'ri javob:</small>
                                            <div class="fw-medium">{!! $detail['correct_answer'] !!}</div>
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