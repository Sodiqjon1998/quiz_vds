<div>
    {{-- Messages --}}
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-checkbox-circle-line me-2"></i>
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if (session()->has('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="ri-error-warning-line me-2"></i>
        {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Header --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white text-white py-3">
            <h4 class="mb-0 fw-bold">
                <i class="ri-book-read-line me-2"></i>
                Kitobxonlik Yozuvlari
            </h4>
        </div>

        <div class="card-body p-4">
            {{-- Statistics --}}
            <div class="row g-3 mb-4">
                {{-- Yuklangan kunlar - Yashil --}}
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-left: 4px solid #34A853 !important;">
                        <div class="card-body text-center" style="background: #F0F9F4;">
                            <i class="ri-file-music-line mb-2" style="font-size: 2rem; color: #34A853;"></i>
                            <h3 class="fw-bold mb-1" style="color: #34A853;">{{ $statistics['total_records'] }}</h3>
                            <p class="text-muted mb-0 small">Jami yozuvlar</p>
                        </div>
                    </div>
                </div>

                {{-- O'tkazilgan kunlar - Qizil --}}
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-left: 4px solid #EA4335 !important;">
                        <div class="card-body text-center" style="background: #FEF1F0;">
                            <i class="ri-user-line mb-2" style="font-size: 2rem; color: #EA4335;"></i>
                            <h3 class="fw-bold mb-1" style="color: #EA4335;">{{ $statistics['total_students'] }}</h3>
                            <p class="text-muted mb-0 small">Faol o'quvchilar</p>
                        </div>
                    </div>
                </div>

                {{-- Jami vaqt - Ko'k --}}
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-left: 4px solid #4285F4 !important;">
                        <div class="card-body text-center" style="background: #EDF4FE;">
                            <i class="ri-time-line mb-2" style="font-size: 2rem; color: #4285F4;"></i>
                            <h3 class="fw-bold mb-1" style="color: #4285F4;">{{ $statistics['total_duration'] }}</h3>
                            <p class="text-muted mb-0 small">Jami vaqt</p>
                        </div>
                    </div>
                </div>

                {{-- Jami hajm - Sariq --}}
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-left: 4px solid #FBBC04 !important;">
                        <div class="card-body text-center" style="background: #FEF9E7;">
                            <i class="ri-database-2-line mb-2" style="font-size: 2rem; color: #FBBC04;"></i>
                            <h3 class="fw-bold mb-1" style="color: #FBBC04;">{{ $statistics['total_size'] }}</h3>
                            <p class="text-muted mb-0 small">Jami hajm</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="ri-search-line me-1"></i>Qidirish
                    </label>
                    <input type="text" wire:model.live="search"
                        class="form-control"
                        placeholder="Ism yoki fayl nomi...">
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
                        <i class="ri-user-line me-1"></i>O'quvchi
                    </label>
                    <select wire:model.live="studentFilter" class="form-select">
                        <option value="">Barchasi</option>
                        @foreach($students as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->first_name }} {{ $student->last_name }}
                            @if($student->class_name)
                            ({{ $student->class_name }})
                            @endif
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="ri-calendar-line me-1"></i>Sana oralig'i
                    </label>
                    <div class="input-group">
                        <input type="date" 
                               wire:model.live="dateFrom" 
                               class="form-control"
                               max="{{ date('Y-m-d') }}"
                               style="border-right: none;">
                        <span class="input-group-text bg-white" style="border-left: none; border-right: none;">
                            <i class="ri-arrow-right-line"></i>
                        </span>
                        <input type="date" 
                               wire:model.live="dateTo" 
                               class="form-control"
                               max="{{ date('Y-m-d') }}"
                               style="border-left: none;">
                    </div>
                    
                    {{-- Tezkor tanlov tugmalari --}}
                    <div class="btn-group btn-group-sm mt-2 w-100" role="group">
                        <button type="button" wire:click="setDateRange('today')" class="btn btn-outline-primary">
                            Bugun
                        </button>
                        <button type="button" wire:click="setDateRange('yesterday')" class="btn btn-outline-primary">
                            Kecha
                        </button>
                        <button type="button" wire:click="setDateRange('week')" class="btn btn-outline-primary">
                            7 kun
                        </button>
                        <button type="button" wire:click="setDateRange('month')" class="btn btn-outline-primary">
                            30 kun
                        </button>
                    </div>
                </div>

                <div class="col-md-1 d-flex align-items-end">
                    <button wire:click="$refresh" class="btn btn-primary w-100" title="Yangilash">
                        <i class="ri-refresh-line"></i>
                    </button>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">№</th>
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
                                    <div class="avatar avatar-sm bg-primary-subtle rounded-circle me-2">
                                        <i class="ri-user-line text-primary"></i>
                                    </div>
                                    <strong>{{ $record->first_name }} {{ $record->last_name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                    {{ $record->class_name ?? 'N/A' }}
                                    <!-- <small class="text-danger">(ID: {{ $record->classes_id }})</small> -->
                                </span>
                            </td>
                            <td>
                                <i class="ri-music-2-line text-primary me-1"></i>
                                {{ $record->filename }}
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">
                                    {{ gmdate('i:s', $record->duration) }}
                                </span>
                            </td>
                            <td class="text-center">
                                <small class="text-muted">
                                    {{ number_format(($record->file_size / 1024 * 8) / 1000, 2) }} Mb
                                </small>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ \Carbon\Carbon::parse($record->created_at)->format('d.m.Y H:i') }}
                                </small>
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button wire:click="viewDetail({{ $record->id }})"
                                        type="button"
                                        class="btn btn-sm btn-info" 
                                        title="Ko'rish">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <a href="{{ asset('storage/' . $record->file_url) }}"
                                        target="_blank"
                                        class="btn btn-sm btn-success" 
                                        title="Eshitish">
                                        <i class="ri-play-circle-line"></i>
                                    </a>
                                    <button wire:click="deleteRecord({{ $record->id }})"
                                        type="button"
                                        onclick="return confirm('O\'chirmoqchimisiz?')"
                                        class="btn btn-sm btn-danger" 
                                        title="O'chirish">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="ri-inbox-line text-muted" style="font-size: 60px; opacity: 0.2;"></i>
                                <p class="text-muted mt-3">Hozircha yozuvlar yo'q</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $records->links() }}
            </div>
        </div>
    </div>

    {{-- Detail Modal - FIXED --}}
    @if($showDetailModal && $this->selectedRecord)
    <div class="modal fade show d-block" 
         tabindex="-1" 
         style="background: rgba(0,0,0,0.7); z-index: 9999;"
         wire:ignore.self>
        <div class="modal-dialog modal-dialog-centered modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="ri-file-music-line me-2"></i>
                        Yozuv tafsilotlari
                    </h5>
                    <button type="button" 
                            class="btn-close btn-close-white" 
                            wire:click="closeDetailModal"
                            aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="border-start border-primary border-4 ps-3 py-2">
                                <small class="text-muted d-block">O'quvchi</small>
                                <strong>{{ $this->selectedRecord->first_name }} {{ $this->selectedRecord->last_name }}</strong>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="border-start border-info border-4 ps-3 py-2">
                                <small class="text-muted d-block">Sinf</small>
                                <strong>{{ $this->selectedRecord->class_name ?? 'N/A' }}</strong>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="border-start border-success border-4 ps-3 py-2">
                                <small class="text-muted d-block">Fayl nomi</small>
                                <strong>{{ $this->selectedRecord->filename }}</strong>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="border-start border-warning border-4 ps-3 py-2">
                                <small class="text-muted d-block">Davomiylik</small>
                                <strong>{{ gmdate('i:s', $this->selectedRecord->duration) }}</strong>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="border-start border-danger border-4 ps-3 py-2">
                                <small class="text-muted d-block">Hajm</small>
                                <strong>{{ number_format(($this->selectedRecord->file_size / 1024 * 8) / 1000, 2) }} Mb</strong>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="border-start border-secondary border-4 ps-3 py-2">
                                <small class="text-muted d-block">Yuklangan</small>
                                <strong>{{ \Carbon\Carbon::parse($this->selectedRecord->created_at)->format('d.m.Y H:i') }}</strong>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <label class="form-label fw-bold">
                                        <i class="ri-headphone-line me-1"></i>
                                        Audio eshitish
                                    </label>
                                    <audio controls class="w-100">
                                        <source src="{{ asset('storage/' . $this->selectedRecord->file_url) }}" type="audio/mpeg">
                                        Brauzeringiz audio ni qo'llab-quvvatlamaydi.
                                    </audio>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <a href="{{ asset('storage/' . $this->selectedRecord->file_url) }}" 
                       download 
                       class="btn btn-success">
                        <i class="ri-download-line me-1"></i>
                        Yuklab olish
                    </a>
                    <button type="button" 
                            wire:click="closeDetailModal" 
                            class="btn btn-secondary">
                        <i class="ri-close-line me-1"></i>
                        Yopish
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

{{-- Modal backdrop fix --}}
@if($showDetailModal)
<style>
    body {
        overflow: hidden;
    }
</style>
@endif