<div>
    {{-- Success/Error Messages --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="ri-checkbox-circle-line me-2 fs-5"></i>
            <strong>Muvaffaqiyatli!</strong> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
            <i class="ri-error-warning-line me-2 fs-5"></i>
            <strong>Xatolik!</strong> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 fw-bold">
                        <i class="ri-file-list-3-line me-2"></i>
                        O'quvchilar Test Natijalari
                    </h4>
                    <p class="text-white-50 small mb-0">
                        <i class="ri-information-line me-1"></i>
                        Barcha o'quvchilarning test natijalarini ko'rish
                    </p>
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            {{-- Filters --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="ri-search-line me-1"></i>Qidirish
                    </label>
                    <input type="text" wire:model.live="search"
                           class="form-control"
                           placeholder="Ism yoki familiya...">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="ri-graduation-cap-line me-1"></i>Sinf
                    </label>
                    <select wire:model.live="classFilter" class="form-select">
                        <option value="">Barchasi</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="ri-book-line me-1"></i>Fan
                    </label>
                    <select wire:model.live="subjectFilter" class="form-select">
                        <option value="">Barchasi</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="ri-calendar-line me-1"></i>Dan
                    </label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>

                <div class="col-md-2">
                    <label class="form-label fw-semibold">
                        <i class="ri-calendar-line me-1"></i>Gacha
                    </label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button wire:click="$refresh" class="btn btn-primary w-100">
                        <i class="ri-refresh-line"></i>
                    </button>
                </div>
            </div>

            {{-- Students List --}}
            @forelse($students as $student)
                <div class="card border shadow-sm mb-3">
                    <div class="card-header bg-light">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="mb-1 fw-bold">
                                    <i class="ri-user-line me-2 text-primary"></i>
                                    {{ $student->first_name }} {{ $student->last_name }}
                                </h5>
                                <span class="badge bg-info-subtle text-info">
                                    <i class="ri-graduation-cap-line me-1"></i>{{ $student->class_name }}
                                </span>
                            </div>
                            <div class="text-end">
                                <div class="badge bg-primary-subtle text-primary fs-6 px-3 py-2">
                                    <i class="ri-file-list-3-line me-1"></i>
                                    {{ $student->total_exams }} ta test
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        @if($student->exams && count($student->exams) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover align-middle">
                                    <thead class="table-light">
                                        <tr>
                                            <th style="width: 5%">№</th>
                                            <th style="width: 25%">Test nomi</th>
                                            <th style="width: 15%">Fan</th>
                                            <th style="width: 15%" class="text-center">Natija</th>
                                            <th style="width: 10%" class="text-center">Ball</th>
                                            <th style="width: 10%" class="text-center">Holat</th>
                                            <th style="width: 15%">Sana</th>
                                            <th style="width: 5%" class="text-center">Amallar</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($student->exams as $index => $exam)
                                            <tr>
                                                <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                                <td>
                                                    <strong>{{ $exam->quiz_name }}</strong>
                                                </td>
                                                <td>
                                                    <span class="badge bg-info-subtle text-info">
                                                        <i class="ri-book-line me-1"></i>
                                                        {{ $exam->subject_name }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <div class="d-flex justify-content-center align-items-center">
                                                        <div class="progress" style="width: 100px; height: 8px;">
                                                            <div class="progress-bar {{ $exam->passed ? 'bg-success' : 'bg-danger' }}"
                                                                 style="width: {{ $exam->percentage }}%">
                                                            </div>
                                                        </div>
                                                        <span class="ms-2 fw-bold">{{ $exam->percentage }}%</span>
                                                    </div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary fs-6 px-3 py-2">
                                                        {{ $exam->correct_answers }}/{{ $exam->total_questions }}
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    @if($exam->passed)
                                                        <span class="badge bg-success px-3 py-2">
                                                            <i class="ri-checkbox-circle-line me-1"></i>O'tdi
                                                        </span>
                                                    @else
                                                        <span class="badge bg-danger px-3 py-2">
                                                            <i class="ri-close-circle-line me-1"></i>O'tmadi
                                                        </span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <small class="text-muted">
                                                        <i class="ri-calendar-line me-1"></i>
                                                        {{ \Carbon\Carbon::parse($exam->created_at)->format('d.m.Y H:i') }}
                                                    </small>
                                                </td>
                                                <td class="text-center">
                                                    <button wire:click="viewDetails({{ $exam->id }})"
                                                            class="btn btn-sm btn-primary">
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
                                <i class="ri-inbox-line text-muted" style="font-size: 60px; opacity: 0.2;"></i>
                                <p class="text-muted mt-3">Bu o'quvchida test natijalari yo'q</p>
                            </div>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center py-5">
                    <i class="ri-inbox-line text-muted" style="font-size: 80px; opacity: 0.2;"></i>
                    <h5 class="text-muted mt-3 mb-2">Natijalar topilmadi</h5>
                    <p class="text-muted mb-3">Filtrlarni o'zgartirib ko'ring</p>
                </div>
            @endforelse

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $selectedExam)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.7); z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-gradient-primary text-white border-0">
                        <div>
                            <h5 class="modal-title fw-bold mb-1 text-white">
                                <i class="ri-file-list-3-line me-2"></i>
                                Test Batafsil Natijasi
                            </h5>
                            <div class="d-flex gap-2">
                                <span class="badge bg-white text-primary">
                                    {{ $selectedExam->first_name }} {{ $selectedExam->last_name }}
                                </span>
                                <span class="badge bg-white text-primary">{{ $selectedExam->class_name }}</span>
                                <span class="badge bg-white text-primary">{{ $selectedExam->quiz_name }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white"
                                wire:click="closeDetailModal"></button>
                    </div>
                    <div class="modal-body p-4" style="max-height: 70vh;">
                        {{-- Statistics --}}
                        <div class="row g-3 mb-4">
                            <div class="col-md-4">
                                <div class="card border-0 bg-success-subtle h-100">
                                    <div class="card-body text-center">
                                        <i class="ri-checkbox-circle-line text-success mb-2" style="font-size: 2rem;"></i>
                                        <h3 class="fw-bold text-success mb-1">
                                            {{ $examDetails->where('is_correct', true)->count() }}
                                        </h3>
                                        <p class="text-muted mb-0">To'g'ri javoblar</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-danger-subtle h-100">
                                    <div class="card-body text-center">
                                        <i class="ri-close-circle-line text-danger mb-2" style="font-size: 2rem;"></i>
                                        <h3 class="fw-bold text-danger mb-1">
                                            {{ $examDetails->where('is_correct', false)->count() }}
                                        </h3>
                                        <p class="text-muted mb-0">Noto'g'ri javoblar</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card border-0 bg-primary-subtle h-100">
                                    <div class="card-body text-center">
                                        <i class="ri-percent-line text-primary mb-2" style="font-size: 2rem;"></i>
                                        <h3 class="fw-bold text-primary mb-1">
                                            {{ $examDetails->count() > 0 ? round(($examDetails->where('is_correct', true)->count() / $examDetails->count()) * 100, 2) : 0 }}%
                                        </h3>
                                        <p class="text-muted mb-0">Natija</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr>

                        {{-- Questions and Answers --}}
                        @foreach($examDetails as $detail)
                            <div class="card mb-3 border-2 {{ $detail->is_correct ? 'border-success' : 'border-danger' }}">
                                <div class="card-header {{ $detail->is_correct ? 'bg-success-subtle' : 'bg-danger-subtle' }}">
                                    <div class="d-flex align-items-center justify-content-between">
                                        <div class="d-flex align-items-center flex-grow-1">
                                            <span class="badge {{ $detail->is_correct ? 'bg-success' : 'bg-danger' }} me-3 px-3 py-2">
                                                {{ $detail->number }}
                                            </span>
                                            <h6 class="mb-0">Savol {{ $detail->number }}</h6>
                                        </div>
                                        <i class="{{ $detail->is_correct ? 'ri-check-line text-success' : 'ri-close-line text-danger' }} fs-3"></i>
                                    </div>
                                </div>
                                <div class="card-body">
                                    <div class="mb-3">
                                        <strong class="d-block mb-2 text-primary">
                                            <i class="ri-question-line me-1"></i>Savol:
                                        </strong>
                                        <div class="p-3 bg-light rounded">
                                            {!! $detail->question_text !!}
                                        </div>
                                        @if($detail->question_image)
                                            <div class="mt-2">
                                                <img src="{{ asset('storage/' . $detail->question_image) }}"
                                                     alt="Savol rasmi"
                                                     class="img-fluid rounded shadow-sm"
                                                     style="max-height: 200px;">
                                            </div>
                                        @endif
                                    </div>

                                    <div class="row g-3">
                                        <div class="col-md-6">
                                            <div class="card {{ $detail->is_correct ? 'border-success bg-success-subtle' : 'border-danger bg-danger-subtle' }}">
                                                <div class="card-body">
                                                    <strong class="d-block mb-2">
                                                        <i class="ri-user-line me-1"></i>O'quvchi javobi:
                                                    </strong>
                                                    <div>{!! $detail->selected_answer !!}</div>
                                                    @if($detail->selected_image)
                                                        <div class="mt-2">
                                                            <img src="{{ asset('storage/' . $detail->selected_image) }}"
                                                                 alt="Javob rasmi"
                                                                 class="img-fluid rounded"
                                                                 style="max-height: 100px;">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-6">
                                            <div class="card border-success bg-success-subtle">
                                                <div class="card-body">
                                                    <strong class="d-block mb-2">
                                                        <i class="ri-check-line me-1"></i>To'g'ri javob:
                                                    </strong>
                                                    <div>{!! $detail->correct_answer !!}</div>
                                                    @if($detail->correct_image)
                                                        <div class="mt-2">
                                                            <img src="{{ asset('storage/' . $detail->correct_image) }}"
                                                                 alt="To'g'ri javob rasmi"
                                                                 class="img-fluid rounded"
                                                                 style="max-height: 100px;">
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button wire:click="closeDetailModal" class="btn btn-secondary btn-lg">
                            <i class="ri-close-line me-1"></i>Yopish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .modal.show {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }
</style>