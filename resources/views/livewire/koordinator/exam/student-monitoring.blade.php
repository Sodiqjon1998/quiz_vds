<div>
    {{-- Header --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 fw-bold text-white">
                        <i class="ri-file-list-3-line me-2"></i>
                        "{{ $schoolName }}" maktabi
                    </h4>
                    @if($classFilter && $className)
                        <p class="text-white mb-0">
                            <strong>{{ $className }}</strong> sinfi o'quvchilarining <strong>{{ $quarter }}</strong> monitoring
                        </p>
                    @else
                        <p class="text-white-50 mb-0">
                            <i class="ri-information-line me-1"></i>
                            Iltimos, quyidagi filtrdan sinfni tanlang
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="card-body p-4">
            {{-- Filters --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="ri-graduation-cap-line me-1 text-danger"></i>
                        <span class="text-danger">Sinf *</span>
                    </label>
                    <select wire:model.live="classFilter" class="form-select border-danger" required>
                        <option value="">-- Sinfni tanlang --</option>
                        @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($classFilter)
                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="ri-search-line me-1"></i>Qidirish
                        </label>
                        <input type="text" wire:model.live="search"
                               class="form-control"
                               placeholder="Ism yoki familiya...">
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="ri-book-line me-1"></i>Fan
                        </label>
                        <select wire:model.live="subjectFilter" class="form-select">
                            <option value="">Barcha fanlar</option>
                            @foreach($subjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label fw-semibold">
                            <i class="ri-calendar-line me-1"></i>Davr
                        </label>
                        <select wire:model.live="quarter" class="form-select">
                            <optgroup label="Oylar">
                                <option value="Sentyabr">Sentyabr</option>
                                <option value="Oktyabr">Oktyabr</option>
                                <option value="Noyabr">Noyabr</option>
                                <option value="Dekabr">Dekabr</option>
                                <option value="Yanvar">Yanvar</option>
                                <option value="Fevral">Fevral</option>
                                <option value="Mart">Mart</option>
                                <option value="Aprel">Aprel</option>
                                <option value="May">May</option>
                            </optgroup>
                            <optgroup label="Choraklar">
                                <option value="1-chorak">1-chorak (Sen-Noy)</option>
                                <option value="2-chorak">2-chorak (Dek-Fev)</option>
                                <option value="3-chorak">3-chorak (Mar-May)</option>
                            </optgroup>
                            <optgroup label="Umumiy">
                                <option value="Yillik">Yillik (Sen-May)</option>
                            </optgroup>
                        </select>
                    </div>
                @endif
            </div>

            @if($showEmptyMessage ?? false)
                <div class="alert alert-info border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ri-information-line fs-1 me-3 text-primary"></i>
                        <div>
                            <h5 class="alert-heading mb-2 text-primary">
                                <i class="ri-arrow-up-line"></i>
                                Sinf tanlang
                            </h5>
                            <p class="mb-0">Monitoring ma'lumotlarini ko'rish uchun yuqorida sinf tanlash kerak</p>
                        </div>
                    </div>
                </div>

            @elseif($students->isEmpty())
                <div class="alert alert-warning border-0 shadow-sm" role="alert">
                    <div class="d-flex align-items-center">
                        <i class="ri-user-unfollow-line fs-1 me-3"></i>
                        <div>
                            <h5 class="alert-heading mb-1">O'quvchilar topilmadi</h5>
                            <p class="mb-0">
                                <strong>{{ $className }}</strong> sinfida o'quvchilar yo'q yoki hech kim test topshirmagan
                            </p>
                        </div>
                    </div>
                </div>

            @else
                {{-- Monitoring Table --}}
                <div class="table-responsive">
                    <table class="table table-bordered table-hover align-middle mb-0">
                        <thead class="table-primary">
                            <tr>
                                <th rowspan="2" class="text-center align-middle" style="width: 50px;">№</th>
                                <th rowspan="2" class="align-middle" style="width: 220px;">
                                    O'quvchining familiyasi va ismi
                                </th>
                                <th colspan="6" class="text-center">NATIJALAR</th>
                                <th rowspan="2" class="text-center align-middle" style="width: 90px;">UMUMIY</th>
                                <th rowspan="2" class="text-center align-middle" style="width: 90px;">O'RTACHA</th>
                                <th rowspan="2" class="text-center align-middle" style="width: 70px;">O'RNI</th>
                            </tr>
                            <tr>
                                <th class="text-center" style="width: 100px;">
                                    INGLIZ TILI<br>
                                    <small class="text-muted">D | B</small>
                                </th>
                                <th class="text-center" style="width: 100px;">
                                    MATEMATIKA<br>
                                    <small class="text-muted">D | B</small>
                                </th>
                                <th class="text-center" style="width: 100px;">
                                    XULQIQO'L ALIK VAZIFALAR<br>
                                    <small class="text-muted">D | B</small>
                                </th>
                                <th class="text-center" style="width: 90px;">
                                    KITOBXONLIK
                                </th>
                                <th class="text-center" style="width: 90px;">
                                    UMUMIY
                                </th>
                                <th class="text-center" style="width: 90px;">
                                    O'RTACHA
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($students as $student)
                                <tr>
                                    <td class="text-center fw-bold">{{ $student->number }}</td>
                                    <td><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></td>
                                    
                                    {{-- Ingliz tili --}}
                                    <td class="text-center">
                                        @php
                                            $englishSubject = $student->subjects->first(function($s) {
                                                return stripos($s->subject_name, 'ingliz') !== false;
                                            });
                                        @endphp
                                        @if($englishSubject && $englishSubject->score > 0)
                                            <span class="badge bg-primary mb-1">{{ $englishSubject->grade }}</span>
                                            <br>
                                            <small class="text-muted">{{ $englishSubject->score }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Matematika --}}
                                    <td class="text-center">
                                        @php
                                            $mathSubject = $student->subjects->first(function($s) {
                                                return stripos($s->subject_name, 'matematik') !== false;
                                            });
                                        @endphp
                                        @if($mathSubject && $mathSubject->score > 0)
                                            <span class="badge bg-success mb-1">{{ $mathSubject->grade }}</span>
                                            <br>
                                            <small class="text-muted">{{ $mathSubject->score }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Xulqiqo'l --}}
                                    <td class="text-center">
                                        @if($student->conduct_score > 0)
                                            <span class="badge bg-info mb-1">{{ $student->conduct_grade }}</span>
                                            <br>
                                            <small class="text-muted">{{ $student->conduct_score }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Kitobxonlik --}}
                                    <td class="text-center">
                                        @if($student->reading_score > 0)
                                            <strong>{{ $student->reading_score }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Natijalar Umumiy --}}
                                    <td class="text-center bg-light">
                                        @if($student->total_score > 0)
                                            <strong class="text-primary">{{ $student->total_score }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Natijalar O'rtacha --}}
                                    <td class="text-center bg-light">
                                        @if($student->average_score > 0)
                                            <span class="badge bg-secondary">{{ $student->average_score }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Jami Umumiy --}}
                                    <td class="text-center bg-warning bg-opacity-25">
                                        @if($student->total_score > 0)
                                            <strong class="text-dark">{{ $student->total_score }}</strong>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- Jami O'rtacha --}}
                                    <td class="text-center bg-warning bg-opacity-25">
                                        @if($student->average_score > 0)
                                            <span class="badge bg-dark fs-6">{{ $student->average_score }}</span>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>

                                    {{-- O'rni --}}
                                    <td class="text-center">
                                        @if($student->total_score > 0)
                                            @if($student->rank == 1)
                                                <span class="badge bg-warning text-dark fs-5">🥇 1</span>
                                            @elseif($student->rank == 2)
                                                <span class="badge bg-secondary text-white fs-5">🥈 2</span>
                                            @elseif($student->rank == 3)
                                                <span class="badge bg-danger text-white fs-5">🥉 3</span>
                                            @else
                                                <strong class="fs-5">{{ $student->rank }}</strong>
                                            @endif
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                @if($students->hasPages())
                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                @endif
            @endif
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .table thead th {
        font-size: 12px;
        font-weight: 600;
        padding: 10px 8px;
        vertical-align: middle;
        text-transform: uppercase;
        background-color: #e3f2fd !important;
    }

    .table tbody td {
        font-size: 13px;
        padding: 12px 8px;
        vertical-align: middle;
    }

    .badge {
        font-size: 11px;
        padding: 4px 10px;
        font-weight: 600;
    }

    .form-select.border-danger {
        border-width: 2px;
    }

    .form-select.border-danger:focus {
        border-color: #dc3545;
        box-shadow: 0 0 0 0.25rem rgba(220, 53, 69, 0.25);
    }

    @media print {
        .card-header, 
        .row.g-3, 
        .pagination,
        .alert {
            display: none !important;
        }
        
        .table {
            font-size: 10px;
        }
        
        .table thead th {
            font-size: 9px;
        }
    }
</style>