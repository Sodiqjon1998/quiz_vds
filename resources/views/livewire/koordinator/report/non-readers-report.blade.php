<div>
    {{-- Header --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3">
            <h4 class="mb-0 fw-bold text-danger">
                <i class="ri-error-warning-line me-2"></i>
                Kitob Tashlamaganlar Hisoboti
            </h4>
        </div>

        <div class="card-body p-4">
            {{-- Statistics --}}
            <div class="row g-3 mb-4">
                {{-- Jami o'quvchilar --}}
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-left: 4px solid #4285F4 !important;">
                        <div class="card-body text-center" style="background: #EDF4FE;">
                            <i class="ri-group-line mb-2" style="font-size: 2rem; color: #4285F4;"></i>
                            <h3 class="fw-bold mb-1" style="color: #4285F4;">{{ $statistics['total_students'] }}</h3>
                            <p class="text-muted mb-0 small">Jami o'quvchilar</p>
                        </div>
                    </div>
                </div>

                {{-- Kitob tashlaganlar --}}
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-left: 4px solid #34A853 !important;">
                        <div class="card-body text-center" style="background: #F0F9F4;">
                            <i class="ri-checkbox-circle-line mb-2" style="font-size: 2rem; color: #34A853;"></i>
                            <h3 class="fw-bold mb-1" style="color: #34A853;">{{ $statistics['read_today'] }}</h3>
                            <p class="text-muted mb-0 small">Kitob tashlagan</p>
                        </div>
                    </div>
                </div>

                {{-- Kitob tashlamaganlar --}}
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-left: 4px solid #EA4335 !important;">
                        <div class="card-body text-center" style="background: #FEF1F0;">
                            <i class="ri-close-circle-line mb-2" style="font-size: 2rem; color: #EA4335;"></i>
                            <h3 class="fw-bold mb-1" style="color: #EA4335;">{{ $statistics['not_read_today'] }}</h3>
                            <p class="text-muted mb-0 small">Kitob tashlamagan</p>
                        </div>
                    </div>
                </div>

                {{-- Foiz --}}
                <div class="col-md-3">
                    <div class="card border-0 h-100" style="border-left: 4px solid #FBBC04 !important;">
                        <div class="card-body text-center" style="background: #FEF9E7;">
                            <i class="ri-percent-line mb-2" style="font-size: 2rem; color: #FBBC04;"></i>
                            <h3 class="fw-bold mb-1" style="color: #FBBC04;">{{ $statistics['percentage'] }}%</h3>
                            <p class="text-muted mb-0 small">O'qish faolligi</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Filters --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">
                        <i class="ri-calendar-line me-1"></i>Sana tanlang
                    </label>
                    <input type="date"
                        wire:model="selectedDate"
                        wire:change="$refresh"
                        class="form-control"
                        max="{{ date('Y-m-d') }}">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="ri-graduation-cap-line me-1"></i>Sinf
                    </label>
                    <select wire:model="classFilter" wire:change="$refresh" class="form-select">
                        <option value="">Barcha sinflar</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-5">
                    <label class="form-label fw-semibold d-block">&nbsp;</label>
                    <div class="btn-group" role="group">
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
            <div class="alert alert-warning d-flex align-items-center" role="alert">
                <i class="ri-information-line me-2" style="font-size: 1.5rem;"></i>
                <div>
                    <strong>Diqqat!</strong>
                    <span class="text-danger fw-bold">{{ \Carbon\Carbon::parse($selectedDate)->format('d.m.Y') }}</span>
                    sanasida kitob tashlamagan o'quvchilar ro'yxati.
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">№</th>
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
                                    <div class="avatar avatar-sm bg-danger-subtle rounded-circle me-2 d-flex align-items-center justify-content-center" style="width: 35px; height: 35px;">
                                        <i class="ri-user-line text-danger"></i>
                                    </div>
                                    <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                    {{ $student->class_name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-danger px-3 py-2">
                                    <i class="ri-close-circle-line me-1"></i>
                                    Kitob tashlamagan
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <i class="ri-checkbox-circle-line text-success" style="font-size: 60px;"></i>
                                <h5 class="text-success mt-3">Ajoyib!</h5>
                                <p class="text-muted">Barcha o'quvchilar kitob tashlagan</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $nonReaders->links() }}
            </div>
        </div>
    </div>
</div>