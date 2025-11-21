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
                    <p class="text-white mb-0">
                        <strong>{{ $className }}</strong> sinfi o'quvchilarining <strong>{{ $quarter }}</strong> monitoring
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

                <div class="col-md-3">
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

                <div class="col-md-3">
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

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="ri-calendar-line me-1"></i>Chorak
                    </label>
                    <select wire:model.live="quarter" class="form-select">
                        <option value="Sentyabr">Sentyabr</option>
                        <option value="Oktyabr">Oktyabr</option>
                        <option value="Noyabr">Noyabr</option>
                        <option value="Dekabr">Dekabr</option>
                    </select>
                </div>
            </div>

            {{-- Monitoring Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover align-middle">
                    <thead class="table-primary">
                        <tr>
                            <th rowspan="2" class="text-center" style="width: 40px;">№</th>
                            <th rowspan="2" style="width: 200px;">O'quvchining familiyasi va ismi</th>
                            <th rowspan="2" class="text-center" style="width: 80px;">Ingliz tili</th>
                            <th colspan="5" class="text-center">natijalar</th>
                            <th rowspan="2" class="text-center" style="width: 80px;">Umumiy</th>
                            <th rowspan="2" class="text-center" style="width: 80px;">O'rtacha</th>
                            <th rowspan="2" class="text-center" style="width: 60px;">O'rni</th>
                        </tr>
                        <tr>
                            <th class="text-center" style="width: 70px;">Matematika<br><small>D | B</small></th>
                            <th class="text-center" style="width: 70px;">Xulqiqo'l alik vazifalar<br><small>D | B</small></th>
                            <th class="text-center" style="width: 70px;">Kitobxonlik</th>
                            <th class="text-center" style="width: 70px;">Umumiy</th>
                            <th class="text-center" style="width: 70px;">O'rtacha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $student)
                            <tr>
                                <td class="text-center fw-bold">{{ $student->number }}</td>
                                <td>{{ $student->first_name }} {{ $student->last_name }}</td>
                                
                                {{-- Ingliz tili --}}
                                <td class="text-center">
                                    @php
                                        $englishSubject = $student->subjects->firstWhere('subject_name', 'Ingliz tili');
                                    @endphp
                                    @if($englishSubject)
                                        <span class="badge bg-primary">{{ $englishSubject->grade }}</span>
                                        <br>
                                        <small>{{ $englishSubject->score }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Matematika --}}
                                <td class="text-center">
                                    @php
                                        $mathSubject = $student->subjects->firstWhere('subject_name', 'Matematika');
                                    @endphp
                                    @if($mathSubject)
                                        <span class="badge bg-success">{{ $mathSubject->grade }}</span>
                                        <br>
                                        <small>{{ $mathSubject->score }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>

                                {{-- Xulqiqo'l --}}
                                <td class="text-center">
                                    <span class="badge bg-info">{{ $student->conduct_grade }}</span>
                                    <br>
                                    <small>{{ $student->conduct_score }}</small>
                                </td>

                                {{-- Kirpi vazifalar --}}
                                <td class="text-center">
                                    <span class="badge bg-warning">{{ $student->homework_grade }}</span>
                                    <br>
                                    <small>{{ $student->homework_score }}</small>
                                </td>

                                {{-- Kitobxonlik --}}
                                <td class="text-center">
                                    <strong>{{ $student->reading_score }}</strong>
                                </td>

                                {{-- Umumiy --}}
                                <td class="text-center bg-light">
                                    <strong class="text-primary">{{ $student->total_score }}</strong>
                                </td>

                                {{-- O'rtacha --}}
                                <td class="text-center bg-light">
                                    <span class="badge bg-dark fs-6">{{ $student->average_score }}</span>
                                </td>

                                {{-- O'rni --}}
                                <td class="text-center">
                                    @if($student->rank == 1)
                                        <span class="badge bg-warning text-dark fs-5">🥇 {{ $student->rank }}</span>
                                    @elseif($student->rank == 2)
                                        <span class="badge bg-secondary fs-5">🥈 {{ $student->rank }}</span>
                                    @elseif($student->rank == 3)
                                        <span class="badge bg-danger fs-5">🥉 {{ $student->rank }}</span>
                                    @else
                                        <strong>{{ $student->rank }}</strong>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <i class="ri-inbox-line text-muted" style="font-size: 60px; opacity: 0.2;"></i>
                                    <p class="text-muted mt-3">O'quvchilar topilmadi</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .table th {
        font-size: 13px;
        padding: 12px 8px;
        vertical-align: middle;
    }

    .table td {
        font-size: 14px;
        padding: 10px 8px;
    }

    .badge {
        font-size: 12px;
        padding: 4px 8px;
    }

    @media print {
        .card-header, .filters, .pagination {
            display: none;
        }
        
        .table {
            font-size: 11px;
        }
    }
</style>