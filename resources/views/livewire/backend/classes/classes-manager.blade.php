<div>
    {{-- Success Message --}}
    @if (session()->has('message'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="ri-checkbox-circle-line me-2"></i>
        {{ session('message') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    {{-- Header Section --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">
                    <i class="ri-school-line me-2"></i>
                    Sinflar Ro'yxati
                </h4>
                <p class="text-muted small mb-0">Barcha sinflarni boshqarish</p>
            </div>
            <button wire:click="createClass" class="btn btn-primary">
                <i class="ri-add-line me-1"></i> Yangi Sinf
            </button>
        </div>

        <div class="card-body">
            {{-- Search Box --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ri-search-line"></i>
                        </span>
                        <input
                            type="text"
                            wire:model.live="search"
                            class="form-control"
                            placeholder="Sinf nomini qidiring...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">
                        <i class="ri-information-line"></i>
                        Jami: <strong>{{ $classes->total() }}</strong> ta sinf
                    </span>
                </div>
            </div>

            {{-- Classes Grid --}}
            <div class="row">
                @forelse($classes as $class)
                <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                    <div class="card border shadow-sm h-100 hover-shadow transition">
                        <div class="card-body text-center">
                            {{-- Icon --}}
                            <div class="avatar avatar-xl mb-3">
                                <div class="avatar-initial rounded-circle bg-label-primary">
                                    <i class="ri-graduation-cap-line" style="font-size: 32px;"></i>
                                </div>
                            </div>

                            {{-- Class Name --}}
                            <h5 class="mb-2">{{ $class->name }}</h5>

                            {{-- Status Badge --}}
                            @if($class->status == \App\Models\Classes::STATUS_ACTIVE)
                            <span class="badge bg-label-success mb-3">
                                <i class="ri-checkbox-circle-line me-1"></i> Faol
                            </span>
                            @else
                            <span class="badge bg-label-secondary mb-3">
                                <i class="ri-close-circle-line me-1"></i> Nofaol
                            </span>
                            @endif

                            {{-- Info --}}


                            {{-- Actions --}}
                            <div class="d-flex justify-content-center gap-2">
                                <button
                                    wire:click="viewClass({{ $class->id }})"
                                    class="btn btn-sm btn-outline-info"
                                    title="Ko'rish">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button
                                    wire:click="editClass({{ $class->id }})"
                                    class="btn btn-sm btn-outline-warning"
                                    title="Tahrirlash">
                                    <i class="ri-pencil-line"></i>
                                </button>
                                <button
                                    wire:click="deleteClass({{ $class->id }})"
                                    onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')"
                                    class="btn btn-sm btn-outline-danger"
                                    title="O'chirish">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="ri-inbox-line" style="font-size: 64px; opacity: 0.3;"></i>
                        <h5 class="text-muted mt-3">Hozircha sinflar mavjud emas</h5>
                        <p class="text-muted">Yangi sinf qo'shish uchun yuqoridagi tugmani bosing</p>
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $classes->links() }}
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="ri-school-line me-2"></i>
                        {{ $isEdit ? 'Sinfni tahrirlash' : 'Yangi Sinf qo\'shish' }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="saveClass" autocomplete="off">
                    <div class="modal-body">
                        {{-- Class Name --}}
                        <div class="mb-3">
                            <label class="form-label">
                                Sinf nomi <span class="text-danger">*</span>
                            </label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="ri-booklet-line"></i>
                                </span>
                                <input
                                    type="text"
                                    wire:model.live="name"
                                    autocomplete="off"
                                    class="form-control @error('name') is-invalid @enderror"
                                    placeholder="Masalan: 10-A, 11-B, 9-sinf">
                            </div>
                            @error('name')
                            <div class="text-danger small mt-1">
                                <i class="ri-error-warning-line me-1"></i>{{ $message }}
                            </div>
                            @enderror
                            <small class="form-text text-muted">
                                <i class="ri-information-line me-1"></i>
                                Sinf nomini kiriting (masalan: 10-A, 11-B)
                            </small>
                        </div>

                        {{-- Info Alert --}}
                        @if(!$isEdit)
                        <div class="alert alert-info mb-0">
                            <div class="d-flex align-items-start">
                                <i class="ri-lightbulb-line me-2 mt-1"></i>
                                <div>
                                    <strong>Eslatma:</strong> Yangi sinf avtomatik faol holatda yaratiladi.
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">
                            <i class="ri-close-line me-1"></i> Bekor qilish
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="ri-save-line me-1"></i>
                            {{ $isEdit ? 'Yangilash' : 'Saqlash' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    {{-- View Modal --}}
    @if($showViewModal && $viewingClass)
    <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-info text-white">
                    <h5 class="modal-title">
                        <i class="ri-information-line me-2"></i>
                        Sinf Ma'lumotlari: {{ $viewingClass->name }}
                    </h5>
                    <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        {{-- Chap tomon: Sinf ma'lumotlari --}}
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm mb-3">
                                <div class="card-body">
                                    <div class="text-center mb-4">
                                        <div class="avatar avatar-xl mb-3">
                                            <div class="avatar-initial rounded-circle bg-label-primary">
                                                <i class="ri-graduation-cap-line" style="font-size: 48px;"></i>
                                            </div>
                                        </div>
                                        <h4 class="mb-0">{{ $viewingClass->name }}</h4>
                                    </div>

                                    <hr>

                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1">
                                            <i class="ri-key-2-line me-1"></i> ID
                                        </small>
                                        <strong>{{ $viewingClass->id }}</strong>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1">
                                            <i class="ri-booklet-line me-1"></i> Sinf nomi
                                        </small>
                                        <strong>{{ $viewingClass->name }}</strong>
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1">
                                            <i class="ri-checkbox-circle-line me-1"></i> Holat
                                        </small>
                                        @if($viewingClass->status == \App\Models\Classes::STATUS_ACTIVE)
                                        <span class="badge bg-success">
                                            <i class="ri-checkbox-circle-line me-1"></i> Faol
                                        </span>
                                        @else
                                        <span class="badge bg-secondary">
                                            <i class="ri-close-circle-line me-1"></i> Nofaol
                                        </span>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <small class="text-muted d-block mb-1">
                                            <i class="ri-group-line me-1"></i> O'quvchilar soni
                                        </small>
                                        <h3 class="mb-0 text-primary">
                                            {{ $viewingClass->students_count }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- O'ng tomon: O'quvchilar ro'yxati --}}
                        <div class="col-md-8">
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-light">
                                    <h6 class="mb-0">
                                        <i class="ri-group-line me-2"></i>
                                        O'quvchilar Ro'yxati
                                    </h6>
                                </div>
                                <div class="card-body p-0">
                                    @if($this->students->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover mb-0">
                                            <thead class="table-light">
                                                <tr>
                                                    <th width="50">#</th>
                                                    <th>F.I.O</th>
                                                    <th>Telefon</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($this->students as $index => $student)
                                                <tr>
                                                    <td>{{ ($studentsPage - 1) * $studentsPerPage + $index + 1 }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar avatar-sm me-3">
                                                                <span class="avatar-initial rounded-circle bg-label-primary">
                                                                    {{ mb_substr($student->first_name, 0, 1) }}{{ mb_substr($student->last_name, 0, 1) }}
                                                                </span>
                                                            </div>
                                                            <div>
                                                                <strong>{{ $student->first_name }} {{ $student->last_name }}</strong>
                                                                <br>
                                                                <small class="text-muted">{{ $student->email }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $student->phone ?? 'N/A' }}</td>
                                                    <td>
                                                        @if($student->status == 1)
                                                        <span class="badge bg-label-success">Faol</span>
                                                        @else
                                                        <span class="badge bg-label-secondary">Nofaol</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>

                                    {{-- Custom Pagination --}}
                                    <div class="card-footer bg-light">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <small class="text-muted">
                                                    Ko'rsatilmoqda:
                                                    <strong>{{ ($studentsPage - 1) * $studentsPerPage + 1 }}</strong>
                                                    dan
                                                    <strong>{{ min($studentsPage * $studentsPerPage, $this->students->total()) }}</strong>
                                                    gacha, Jami: <strong>{{ $this->students->total() }}</strong>
                                                </small>
                                            </div>
                                            <div>
                                                <button
                                                    wire:click="previousStudentsPage"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    @if($studentsPage <=1) disabled @endif>
                                                    <i class="ri-arrow-left-s-line"></i>
                                                </button>
                                                <span class="mx-2">
                                                    <strong>{{ $studentsPage }}</strong> / {{ $this->students->lastPage() }}
                                                </span>
                                                <button
                                                    wire:click="nextStudentsPage"
                                                    class="btn btn-sm btn-outline-secondary"
                                                    @if($studentsPage>= $this->students->lastPage()) disabled @endif>
                                                    <i class="ri-arrow-right-s-line"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    @else
                                    <div class="text-center py-5">
                                        <i class="ri-user-line" style="font-size: 48px; opacity: 0.3;"></i>
                                        <p class="text-muted mt-3">Bu sinfda hozircha o'quvchilar yo'q</p>
                                    </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" wire:click="closeViewModal">
                        <i class="ri-close-line me-1"></i> Yopish
                    </button>
                    <button type="button" class="btn btn-warning" wire:click="editClass({{ $viewingClass->id }})">
                        <i class="ri-pencil-line me-1"></i> Tahrirlash
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

@push('styles')
<style>
    .hover-shadow {
        transition: all 0.3s ease;
    }

    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    }

    .transition {
        transition: all 0.3s ease;
    }

    .card {
        border-radius: 0.5rem;
    }

    .avatar-xl {
        width: 80px;
        height: 80px;
    }

    .avatar-xl .avatar-initial {
        font-size: 2rem;
    }
</style>
@endpush