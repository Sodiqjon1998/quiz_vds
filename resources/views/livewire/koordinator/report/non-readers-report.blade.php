<div>
    <style>
        :root {
            --yuksalish-orange: #F58025;
            --yuksalish-dark: #212529;
            --yuksalish-gray: #f8f9fa;
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

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .bg-gradient-orange {
            background: linear-gradient(135deg, #F58025 0%, #ff9f5a 100%);
        }

        /* Stats Card Professional */
        .stats-card {
            background: white;
            border: 1px solid #eee;
            transition: all 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            border-color: var(--yuksalish-orange);
        }

        .stats-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Mobile Card */
        .mobile-record-card {
            border-left: 4px solid #dc3545;
            background: white;
            /* Qizil chiziq (o'qimaganlar uchun) */
            margin-bottom: 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
    </style>

    {{-- Header --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-orange text-white py-3">
            <h4 class="mb-0 fw-bold text-white">
                <i class="ri-error-warning-line me-2"></i> Kitob Tashlamaganlar
            </h4>
        </div>

        <div class="card-body p-4">
            {{-- Statistics --}}
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stats-icon-box bg-secondary bg-opacity-10 text-primary me-3">
                                <i class="ri-group-line"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $statistics['total_students'] }}</h4>
                                <small class="text-muted">Jami o'quvchilar</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stats-icon-box bg-success bg-opacity-10 text-success me-3">
                                <i class="ri-checkbox-circle-line"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $statistics['read_today'] }}</h4> <small class="text-muted">Kitob tashlagan</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stats-icon-box bg-danger bg-opacity-10 text-danger me-3">
                                <i class="ri-close-circle-line"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $statistics['not_read_today'] }}</h4>
                                <small class="text-muted">Tashlamagan</small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stats-icon-box bg-warning bg-opacity-10 text-warning me-3">
                                <i class="ri-percent-line"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $statistics['percentage'] }}%</h4>
                                <small class="text-muted">Faollik</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted">Sana tanlang</label>
                    <input type="date"
                        wire:model="selectedDate"
                        wire:change="$refresh"
                        class="form-control"
                        max="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted">Sinf</label>
                    <select wire:model="classFilter" wire:change="$refresh" class="form-select">
                        <option value="">Barcha sinflar</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5 d-flex align-items-end">
                    <div class="btn-group w-100" role="group">
                        <button type="button"
                            wire:click="setDate('today')"
                            class="btn {{ $selectedDate == date('Y-m-d') ? 'btn-primary' : 'btn-outline-primary' }}">
                            <i class="ri-calendar-check-line me-1"></i>Bugun
                        </button>
                        <button type="button"
                            wire:click="setDate('yesterday')"
                            class="btn {{ $selectedDate == \Carbon\Carbon::yesterday()->format('Y-m-d') ? 'btn-secondary' : 'btn-outline-secondary' }}">
                            <i class="ri-calendar-line me-1"></i>Kecha
                        </button>
                    </div>
                </div>
            </div>

            {{-- Alert --}}
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center mb-4">
                <i class="ri-information-line fs-1 me-3 text-warning"></i>
                <div>
                    <strong>Diqqat!</strong>
                    <span class="text-danger fw-bold">{{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}</span>
                    sanasida kitob tashlamagan o'quvchilar ro'yxati.
                </div>
            </div>

            {{-- DESKTOP TABLE --}}
            <div class="d-none d-md-block table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 40%">O'quvchi</th>
                            <th style="width: 20%">Sinf</th>
                            <th style="width: 35%">Holat</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($nonReaders as $index => $student)
                        <tr>
                            <td class="fw-bold">{{ $nonReaders->firstItem() + $index }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-danger-subtle text-danger rounded-circle me-2 d-flex align-items-center justify-content-center">
                                        <i class="ri-user-line"></i>
                                    </div>
                                    <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                                    {{ $student->class_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 px-3 py-2">
                                    <i class="ri-close-circle-line me-1"></i> Kitob tashlamagan
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                                    <i class="ri-checkbox-circle-line text-success" style="font-size: 40px;"></i>
                                </div>
                                <h5 class="text-success mt-2">Ajoyib!</h5>
                                <p class="text-muted">Barcha o'quvchilar kitob tashlagan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- MOBILE LIST --}}
            <div class="d-md-none">
                @forelse($nonReaders as $index => $student)
                <div class="mobile-record-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                        <div class="d-flex align-items-center">
                            <span class="badge bg-light text-muted border me-2">{{ $nonReaders->firstItem() + $index }}</span>
                            <h6 class="fw-bold mb-0 text-dark">{{ $student->first_name }} {{ $student->last_name }}</h6>
                        </div>
                        <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">
                            {{ $student->class_name ?? 'N/A' }}
                        </span>
                    </div>
                    <div class="mt-2 text-center">
                        <span class="badge bg-danger bg-opacity-10 text-danger border border-danger border-opacity-25 w-100 py-2">
                            <i class="ri-close-circle-line me-1"></i> Kitob tashlamagan
                        </span>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <div class="bg-success bg-opacity-10 rounded-circle d-inline-flex p-4 mb-3">
                        <i class="ri-checkbox-circle-line text-success" style="font-size: 40px;"></i>
                    </div>
                    <h5 class="text-success">Ajoyib!</h5>
                    <p class="text-muted">Barcha o'quvchilar kitob tashlagan</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $nonReaders->links() }}
            </div>
        </div>
    </div>
</div>