<div>
    <style>
        :root {
            --yuksalish-orange: #F58025;
            --yuksalish-dark: #212529;
        }

        /* === BUTTONS === */
        .btn-yuksalish {
            background-color: var(--yuksalish-orange);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
            font-weight: 500;
        }

        .btn-yuksalish:hover {
            background-color: #d96d1b;
            color: white;
        }

        .btn-excel {
            background-color: #198754;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.6rem 1.2rem;
        }

        /* === FORM ELEMENTS === */
        .form-control,
        .form-select {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 0.6rem;
        }

        .form-control:focus,
        .form-select:focus {
            border-color: var(--yuksalish-orange);
            box-shadow: 0 0 0 3px rgba(245, 128, 37, 0.15);
        }

        .class-select {
            border: 2px solid var(--yuksalish-orange);
            background-color: #fffbf8;
            font-weight: 600;
        }

        /* === JADVAL DIZAYNI === */
        .table-custom thead th {
            background-color: #e3f2fd !important;
            color: #495057;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            border: 1px solid #dee2e6;
            vertical-align: middle;
            padding: 12px 8px;
        }

        .table-custom tbody td {
            vertical-align: middle;
            font-size: 0.9rem;
            padding: 12px 8px;
            border: 1px solid #dee2e6;
            color: #333;
        }

        .bg-gray-soft {
            background-color: #e9ecef !important;
        }

        .bg-yellow-soft {
            background-color: #fff3cd !important;
        }

        /* Baho va Ballar */
        .score-box {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .grade-tag {
            font-size: 0.7rem;
            font-weight: bold;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            margin-bottom: 3px;
        }

        .bg-A {
            background-color: #6610f2;
        }

        /* Binafsha */
        .bg-B {
            background-color: #0d6efd;
        }

        /* Ko'k */
        .bg-C {
            background-color: #ffc107;
            color: #000;
        }

        /* Sariq */
        .bg-Low {
            background-color: #dc3545;
        }

        /* Qizil */

        /* Reyting */
        .rank-badge {
            font-size: 0.9rem;
            padding: 4px 8px;
            border-radius: 4px;
            font-weight: bold;
            display: inline-flex;
            align-items: center;
            gap: 4px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.15);
        }

        .rank-1 {
            background: #ffc107;
            color: #000;
            border: 1px solid #e0a800;
        }

        .rank-2 {
            background: #6c757d;
            color: #fff;
            border: 1px solid #545b62;
        }

        .rank-3 {
            background: #fd7e14;
            color: #fff;
            border: 1px solid #e36d0c;
        }

        .table-card {
            border: 1px solid #dee2e6;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            background: white;
        }

        /* Mobile Card */
        .mobile-card {
            border-left: 5px solid var(--yuksalish-orange);
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">

                {{-- HEADER & FILTERS --}}
                <div class="card border-0 shadow-sm mb-4" style="border-radius: 12px;">
                    <div class="card-body p-4">
                        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                            <div>
                                <h4 class="mb-0 fw-bold text-dark">
                                    <i class="ri-bar-chart-grouped-line me-2" style="color: var(--yuksalish-orange);"></i>
                                    Monitoring (Koordinator)
                                </h4>
                                <p class="text-muted small mb-0 mt-1">
                                    {{ $schoolName }} @if($className) <span class="mx-2">|</span> <strong>{{ $className }}</strong> @endif
                                </p>
                            </div>
                            @if($classFilter && !$students->isEmpty())
                            <button wire:click="exportToExcel" class="btn btn-excel shadow-sm">
                                <i class="ri-file-excel-2-line me-1"></i> Excel
                            </button>
                            @endif
                        </div>

                        <div class="row g-3">
                            <div class="col-md-3">
                                <label class="small fw-bold text-danger">Sinf *</label>
                                <select wire:model.live="classFilter" class="form-select class-select">
                                    <option value="">-- Tanlang --</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @if($classFilter)
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted">Qidirish</label>
                                <input type="text" wire:model.live="search" class="form-control" placeholder="Ism...">
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted">Fan</label>
                                <select wire:model.live="subjectFilter" class="form-select">
                                    <option value="">Barcha fanlar</option>
                                    @foreach($subjects as $subject)
                                    <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="small fw-bold text-muted">Davr</label>
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
                                        <option value="1-chorak">1-chorak</option>
                                        <option value="2-chorak">2-chorak</option>
                                        <option value="3-chorak">3-chorak</option>
                                    </optgroup>
                                    <optgroup label="Umumiy">
                                        <option value="Yillik">Yillik</option>
                                    </optgroup>
                                </select>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                {{-- CONTENT --}}
                @if($showEmptyMessage)
                <div class="text-center py-5">
                    <i class="ri-arrow-up-line text-muted display-4" style="opacity: 0.3;"></i>
                    <h5 class="text-muted mt-2">Ma'lumotlarni ko'rish uchun yuqoridan <strong>Sinfni</strong> tanlang</h5>
                </div>
                @elseif($students->isEmpty())
                <div class="alert alert-warning shadow-sm text-center py-4 border-0">
                    <h5>O'quvchilar topilmadi</h5>
                    <p class="mb-0">Ushbu sinfda ma'lumotlar mavjud emas.</p>
                </div>
                @else

                {{-- JADVAL (DESKTOP) --}}
                <div class="table-card d-none d-md-block p-3">
                    <div class="table-responsive">
                        <table class="table table-custom mb-0">
                            <thead>
                                <tr>
                                    <th rowspan="2" class="text-center" style="width: 50px;">№</th>
                                    <th rowspan="2" style="min-width: 250px;">O'QUVCHINING FAMILIYASI VA ISMI</th>

                                    <th colspan="{{ $availableSubjects->count() + 3 }}" class="text-center">NATIJALAR</th>

                                    <th rowspan="2" class="text-center bg-yellow-soft" style="width: 100px;">UMUMIY</th>
                                    <th rowspan="2" class="text-center bg-yellow-soft" style="width: 100px;">O'RTACHA</th>
                                    <th rowspan="2" class="text-center bg-yellow-soft" style="width: 90px;">O'RNI</th>
                                </tr>
                                <tr>
                                    @foreach($availableSubjects as $subject)
                                    <th class="text-center">
                                        {{ strtoupper($subject->name) }}<br>
                                        <span style="font-size: 0.65rem; opacity: 0.7;">D | B</span>
                                    </th>
                                    @endforeach

                                    <th class="text-center">XULQI</th>
                                    <th class="text-center">KITOBXONLIK</th>

                                    <th class="text-center bg-gray-soft">O'RTACHA</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($students as $student)
                                <tr>
                                    <td class="text-center fw-bold text-secondary bg-light">{{ $student->number }}</td>
                                    <td>
                                        <span class="fw-bold text-dark">{{ $student->last_name }} {{ $student->first_name }}</span>
                                    </td>

                                    {{-- Fanlar --}}
                                    @foreach($availableSubjects as $subject)
                                    @php
                                    $data = $student->subjectsData->get($subject->id);
                                    $gradeClass = 'bg-Low';
                                    if($data){
                                    if(str_contains($data['grade'], 'A')) $gradeClass = 'bg-A';
                                    elseif(str_contains($data['grade'], 'B')) $gradeClass = 'bg-B';
                                    elseif(str_contains($data['grade'], 'C')) $gradeClass = 'bg-C';
                                    }
                                    @endphp
                                    <td class="text-center">
                                        @if($data && $data['score'] > 0)
                                        <div class="score-box">
                                            <span class="grade-tag {{ $gradeClass }}">{{ $data['grade'] }}</span>
                                            <span class="small text-muted">{{ $data['score'] }}</span>
                                        </div>
                                        @else <span class="text-muted">-</span> @endif
                                    </td>
                                    @endforeach

                                    {{-- Xulq va Kitob --}}
                                    <td class="text-center text-muted">{{ $student->conduct_score > 0 ? $student->conduct_score : '-' }}</td>
                                    <td class="text-center text-muted">{{ $student->reading_score > 0 ? $student->reading_score : '-' }}</td>

                                    {{-- Natijalar ichidagi O'rtacha (Kulrang fon) --}}
                                    <td class="text-center bg-gray-soft fw-bold text-secondary">
                                        @if($student->average_score > 0)
                                        <span class="badge bg-secondary">{{ $student->average_score }}</span>
                                        @else - @endif
                                    </td>

                                    {{-- YAKUNIY (Sariq fon) --}}
                                    <td class="text-center bg-yellow-soft fw-bold">
                                        {{ $student->total_score > 0 ? $student->total_score : '-' }}
                                    </td>
                                    <td class="text-center bg-yellow-soft fw-bold">
                                        @if($student->average_score > 0)
                                        <span class="badge bg-dark">{{ $student->average_score }}</span>
                                        @else - @endif
                                    </td>
                                    <td class="text-center bg-yellow-soft">
                                        @if($student->total_score > 0)
                                        @if($student->rank == 1) <span class="rank-badge rank-1"><i class="ri-trophy-fill"></i> 1</span>
                                        @elseif($student->rank == 2) <span class="rank-badge rank-2"><i class="ri-medal-fill"></i> 2</span>
                                        @elseif($student->rank == 3) <span class="rank-badge rank-3"><i class="ri-medal-fill"></i> 3</span>
                                        @else <span class="text-muted fw-bold">{{ $student->rank }}</span>
                                        @endif
                                        @else - @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    {{-- Pagination --}}
                    @if($students->hasPages())
                    <div class="bg-white p-3 border-top">
                        {{ $students->links() }}
                    </div>
                    @endif
                </div>

                {{-- MOBILE (Card View) --}}
                <div class="d-md-none">
                    @foreach($students as $student)
                    <div class="mobile-card p-3">
                        <div class="d-flex justify-content-between align-items-center border-bottom pb-2 mb-2">
                            <div>
                                <h6 class="fw-bold mb-0 text-dark">{{ $student->last_name }} {{ $student->first_name }}</h6>
                                <small class="text-muted">№ {{ $student->number }}</small>
                            </div>
                            @if($student->total_score > 0)
                            <span class="badge bg-warning text-dark">Ball: {{ $student->total_score }}</span>
                            @endif
                        </div>
                        <div class="mb-2">
                            {{-- Fanlar mobile --}}
                            @foreach($availableSubjects as $subject)
                            @php $data = $student->subjectsData->get($subject->id); @endphp
                            @if($data && $data['score'] > 0)
                            <div class="d-flex justify-content-between mb-1 small">
                                <span class="text-muted">{{ $subject->name }}</span>
                                <span class="fw-bold text-dark">{{ $data['grade'] }} | {{ $data['score'] }}</span>
                            </div>
                            @endif
                            @endforeach
                        </div>
                        <div class="bg-light rounded p-2 d-flex justify-content-between fw-bold small">
                            <span>O'rtacha: {{ $student->average_score }}%</span>
                            <span>O'rni: {{ $student->rank }}</span>
                        </div>
                    </div>
                    @endforeach
                    <div class="mt-4">
                        {{ $students->links() }}
                    </div>
                </div>

                @endif
            </div>
        </div>
    </div>
</div>