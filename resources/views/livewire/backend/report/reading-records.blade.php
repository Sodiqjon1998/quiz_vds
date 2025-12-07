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
            border-left: 4px solid var(--yuksalish-orange);
            background: white;
            margin-bottom: 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }

        .modal-header-colored {
            background-color: var(--yuksalish-orange);
            color: white;
            border-bottom: none;
        }

        .modal-header-colored .btn-close {
            filter: invert(1) grayscale(100%) brightness(200%);
        }
    </style>

    {{-- Messages --}}
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm mx-3 mt-3" style="background-color: #d1e7dd; color: #0f5132;">
        <i class="ri-checkbox-circle-line me-2"></i> {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="container-fluid py-4">
        {{-- HEADER --}}
        <div class="card shadow-sm border-0 mb-4">
            <div class="card-header bg-gradient-orange text-white py-3">
                <h4 class="mb-0 fw-bold text-white">
                    <i class="ri-book-read-line me-2"></i> Kitobxonlik Hisoboti (Admin)
                </h4>
            </div>

            <div class="card-body p-4">
                {{-- STATISTICS --}}
                <div class="row g-3 mb-4">
                    <div class="col-6 col-md-3">
                        <div class="card stats-card h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="stats-icon-box bg-primary bg-opacity-10 text-primary me-3">
                                    <i class="ri-file-music-line"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark">{{ $statistics['total_records'] }}</h4>
                                    <small class="text-muted">Jami yozuvlar</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card stats-card h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="stats-icon-box bg-success bg-opacity-10 text-success me-3">
                                    <i class="ri-user-smile-line"></i>
                                </div>
                                <div>
                                    <h4 class="fw-bold mb-0 text-dark">{{ $statistics['total_students'] }}</h4>
                                    <small class="text-muted">Faol o'quvchilar</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card stats-card h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="stats-icon-box bg-info bg-opacity-10 text-info me-3">
                                    <i class="ri-time-line"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark">{{ $statistics['total_duration'] }}</h5>
                                    <small class="text-muted">Jami vaqt</small>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-6 col-md-3">
                        <div class="card stats-card h-100">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="stats-icon-box bg-warning bg-opacity-10 text-warning me-3">
                                    <i class="ri-database-2-line"></i>
                                </div>
                                <div>
                                    <h5 class="fw-bold mb-0 text-dark">{{ $statistics['total_size'] }}</h5>
                                    <small class="text-muted">Jami hajm</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- FILTERS --}}
                <div class="row g-3 mb-4">
                    <div class="col-md-3">
                        <label class="form-label fw-semibold text-muted">Qidirish</label>
                        <input type="text" wire:model.live="search" class="form-control" placeholder="Ism yoki fayl nomi...">
                    </div>
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold text-muted">Sinf</label>
                        <select wire:model.live="classFilter" class="form-select">
                            <option value="">Barchasi</option>
                            @foreach($classes as $class)
                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    {{-- O'quvchi filtri faqat sinf tanlanganda chiqadi (Admin uchun juda ko'p o'quvchi bo'lishi mumkin) --}}
                    @if($classFilter)
                    <div class="col-6 col-md-2">
                        <label class="form-label fw-semibold text-muted">O'quvchi</label>
                        <select wire:model.live="studentFilter" class="form-select">
                            <option value="">Barchasi</option>
                            @foreach($students as $student)
                            <option value="{{ $student->id }}">{{ $student->first_name }} {{ $student->last_name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif

                    <div class="col-md-4">
                        <label class="form-label fw-semibold text-muted">Sana oralig'i</label>
                        <div class="input-group">
                            <input type="date" wire:model.live="dateFrom" class="form-control" max="{{ date('Y-m-d') }}">
                            <span class="input-group-text bg-white border-start-0 border-end-0"><i class="ri-arrow-right-line"></i></span>
                            <input type="date" wire:model.live="dateTo" class="form-control" max="{{ date('Y-m-d') }}">
                        </div>
                        <div class="btn-group btn-group-sm mt-2 w-100">
                            <button wire:click="setDateRange('today')" class="btn btn-outline-secondary">Bugun</button>
                            <button wire:click="setDateRange('yesterday')" class="btn btn-outline-secondary">Kecha</button>
                            <button wire:click="setDateRange('week')" class="btn btn-outline-secondary">7 kun</button>
                        </div>
                    </div>
                    <div class="col-md-1 d-flex align-items-end">
                        <button wire:click="$refresh" class="btn btn-yuksalish w-100" style="margin-bottom: 34px;"><i class="ri-refresh-line"></i></button>
                    </div>
                </div>

                {{-- DESKTOP TABLE --}}
                <div class="d-none d-md-block table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="bg-light">
                            <tr>
                                <th style="width: 5%">#</th>
                                <th style="width: 20%">O'quvchi</th>
                                <th style="width: 10%">Sinf</th>
                                <th style="width: 25%">Fayl nomi</th>
                                <th style="width: 10%" class="text-center">Davomiylik</th>
                                <th style="width: 10%" class="text-center">Hajm</th>
                                <th style="width: 10%">Sana</th>
                                <th style="width: 10%" class="text-center">Amallar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($records as $index => $record)
                            <tr>
                                <td class="fw-bold">{{ $records->firstItem() + $index }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar avatar-sm bg-light text-primary rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                                            <i class="ri-user-line"></i>
                                        </div>
                                        <span class="fw-bold text-dark">{{ $record->first_name }} {{ $record->last_name }}</span>
                                    </div>
                                </td>
                                <td><span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25">{{ $record->class_name ?? 'N/A' }}</span></td>
                                <td><i class="ri-music-2-line text-primary me-1"></i> {{ $record->filename }}</td>
                                <td class="text-center"><span class="badge bg-primary rounded-pill">{{ gmdate('i:s', $record->duration) }}</span></td>
                                <td class="text-center"><small class="text-muted">{{ number_format(($record->file_size / 1024 / 1024), 2) }} Mb</small></td>
                                <td><small class="text-muted">{{ \Carbon\Carbon::parse($record->created_at)->format('d.m.Y H:i') }}</small></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button wire:click="viewDetail({{ $record->id }})" class="btn btn-sm btn-light text-info border"><i class="ri-eye-line"></i></button>
                                        <a href="{{ asset('storage/' . $record->file_url) }}" target="_blank" class="btn btn-sm btn-light text-success border"><i class="ri-play-circle-line"></i></a>
                                        <button wire:click="deleteRecord({{ $record->id }})" onclick="return confirm('O\'chirmoqchimisiz?')" class="btn btn-sm btn-light text-danger border"><i class="ri-delete-bin-line"></i></button>
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">Ma'lumotlar yo'q</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- MOBILE LIST --}}
                <div class="d-md-none">
                    @forelse($records as $record)
                    <div class="mobile-record-card p-3">
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <h6 class="fw-bold mb-0 text-dark">{{ $record->first_name }} {{ $record->last_name }}</h6>
                            <span class="badge bg-warning bg-opacity-10 text-warning">{{ $record->class_name }}</span>
                        </div>
                        <div class="mb-2">
                            <div class="d-flex align-items-center mb-1 text-primary">
                                <i class="ri-music-2-line me-2"></i>
                                <span class="text-truncate small">{{ $record->filename }}</span>
                            </div>
                            <div class="d-flex justify-content-between small text-muted">
                                <span><i class="ri-time-line"></i> {{ gmdate('i:s', $record->duration) }}</span>
                                <span><i class="ri-database-2-line"></i> {{ number_format(($record->file_size / 1024 / 1024), 2) }} Mb</span>
                            </div>
                        </div>
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top border-secondary-subtle border-opacity-10">
                            <small class="text-muted">{{ \Carbon\Carbon::parse($record->created_at)->format('d.m H:i') }}</small>
                            <div class="btn-group btn-group-sm">
                                <button wire:click="viewDetail({{ $record->id }})" class="btn btn-light border"><i class="ri-eye-line text-info"></i></button>
                                <a href="{{ asset('storage/' . $record->file_url) }}" target="_blank" class="btn btn-light border"><i class="ri-play-circle-line text-success"></i></a>
                                <button wire:click="deleteRecord({{ $record->id }})" onclick="return confirm('O\'chirmoqchimisiz?')" class="btn btn-light border"><i class="ri-delete-bin-line text-danger"></i></button>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5 text-muted">Ma'lumotlar yo'q</div>
                    @endforelse
                </div>

                <div class="mt-4">{{ $records->links() }}</div>
            </div>
        </div>
    </div>

    {{-- Detail Modal --}}
    @if($showDetailModal && $this->selectedRecord)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); z-index: 3050;">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header modal-header-colored">
                    <h5 class="modal-title fw-bold"><i class="ri-file-music-line me-2"></i> Yozuv Tafsilotlari</h5>
                    <button type="button" class="btn-close" wire:click="closeDetailModal"></button>
                </div>
                <div class="modal-body p-4 bg-light">
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="bg-white p-3 rounded border shadow-sm">
                                <label class="small text-muted d-block mb-1">O'quvchi</label>
                                <div class="fw-bold text-dark fs-5">{{ $this->selectedRecord->first_name }} {{ $this->selectedRecord->last_name }}</div>
                                <span class="badge bg-warning bg-opacity-10 text-warning border border-warning border-opacity-25 mt-1">{{ $this->selectedRecord->class_name ?? 'N/A' }}</span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-white p-3 rounded border h-100">
                                <small class="text-muted d-block"><i class="ri-time-line me-1"></i> Davomiylik</small>
                                <strong class="text-primary">{{ gmdate('i:s', $this->selectedRecord->duration) }}</strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-white p-3 rounded border h-100">
                                <small class="text-muted d-block"><i class="ri-database-2-line me-1"></i> Hajm</small>
                                <strong class="text-info">{{ number_format(($this->selectedRecord->file_size / 1024 / 1024), 2) }} Mb</strong>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="bg-white p-3 rounded border">
                                <label class="small text-muted d-block mb-2">Audio eshitish</label>
                                <audio controls class="w-100">
                                    <source src="{{ asset('storage/' . $this->selectedRecord->file_url) }}" type="audio/mpeg">
                                    Brauzeringiz audioni qo'llab-quvvatlamaydi.
                                </audio>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-top bg-white">
                    <a href="{{ asset('storage/' . $this->selectedRecord->file_url) }}" download class="btn btn-success w-50"><i class="ri-download-line me-1"></i> Yuklab olish</a>
                    <button type="button" wire:click="closeDetailModal" class="btn btn-light w-50 border">Yopish</button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>