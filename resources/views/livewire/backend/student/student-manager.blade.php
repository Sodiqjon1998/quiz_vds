<div>
    <!-- Custom CSS for Yuksalish Brand -->
    <style>
        :root {
            --yuksalish-orange: #F58025; /* Logo rangi */
            --yuksalish-dark: #212529;
            --yuksalish-gray: #f8f9fa;
        }

        /* Buttons */
        .btn-yuksalish {
            background-color: var(--yuksalish-orange);
            color: white;
            border: none;
            font-weight: 500;
        }
        .btn-yuksalish:hover {
            background-color: #d96d1b;
            color: white;
        }

        /* Jadval Headeri */
        .table-yuksalish thead th {
            background-color: var(--yuksalish-dark);
            color: white;
            border: none;
            font-weight: 500;
            padding-top: 15px;
            padding-bottom: 15px;
        }

        /* Search input */
        .search-input:focus {
            border-color: var(--yuksalish-orange);
            box-shadow: 0 0 0 0.2rem rgba(245, 128, 37, 0.25);
        }

        /* Mobile Card Design */
        .mobile-card {
            border-left: 5px solid var(--yuksalish-orange);
            background: white;
            margin-bottom: 1rem;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
            border-radius: 8px;
        }

        /* Sinf uchun badge */
        .badge-class {
            background-color: #F58025; /* Orange */
            color: white;
            font-size: 0.85rem;
        }
        
        /* Pagination */
        .page-item.active .page-link {
            background-color: var(--yuksalish-orange);
            border-color: var(--yuksalish-orange);
        }
        .page-link { color: var(--yuksalish-orange); }
    </style>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <!-- Header -->
                    <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <h4 class="card-title mb-0 fw-bold text-dark">
                            <i class="ri-graduation-cap-line" style="color: var(--yuksalish-orange);"></i> O'quvchilar ro'yxati
                        </h4>
                        
                        <div class="d-flex gap-2 w-100 w-md-auto">
                           <button wire:click="createStudent" class="btn btn-yuksalish w-100 w-md-auto">
                                <i class="ri-add-line me-1"></i> Yangi O'quvchi
                           </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Alert -->
                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <!-- Search -->
                        <div class="row mb-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input wire:model.live.debounce.300ms="search" 
                                           type="text" 
                                           class="form-control border-start-0 ps-0 search-input"
                                           placeholder="Qidirish (Ism, Sinf, Email)...">
                                </div>
                            </div>
                        </div>

                        <!-- DESKTOP TABLE -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle table-yuksalish">
                                <thead>
                                <tr>
                                    <th class="rounded-start text-center" style="width: 50px;">ID</th>
                                    <th>O'quvchi</th>
                                    <th>Sinfi</th>
                                    <th>Kontakt</th>
                                    <th>Status</th>
                                    <th class="text-end rounded-end">Amallar</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td class="text-center fw-bold text-muted">{{ $student->id }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $student->first_name }} {{ $student->last_name }}</span>
                                                <small class="text-muted">@ {{ $student->name }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-class">
                                                {{ $student->classRelation->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $student->phone ?? '-' }}</span>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Faol</span>
                                        </td>
                                        <td class="text-end">
                                            <button wire:click="viewStudent({{ $student->id }})" class="btn btn-sm btn-light text-primary" title="Ko'rish">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button wire:click="editStudent({{ $student->id }})" class="btn btn-sm btn-light text-warning" title="Tahrirlash">
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button wire:click="deleteStudent({{ $student->id }})" onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')" class="btn btn-sm btn-light text-danger" title="O'chirish">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5 text-muted">
                                            <i class="ri-inbox-line display-4 opacity-25"></i>
                                            <p class="mt-2">Ma'lumot topilmadi</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- MOBILE CARDS -->
                        <div class="d-md-none">
                            @forelse($students as $student)
                                <div class="mobile-card p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $student->first_name }} {{ $student->last_name }}</h6>
                                            <small class="text-muted">@ {{ $student->name }}</small>
                                        </div>
                                        <span class="badge bg-success bg-opacity-10 text-success">Faol</span>
                                    </div>

                                    <div class="mb-3">
                                        <div class="mb-2">
                                            <span class="badge badge-class">
                                                <i class="ri-community-line me-1"></i> {{ $student->classRelation->name ?? 'Sinfsiz' }}
                                            </span>
                                        </div>
                                        <div class="small text-dark">
                                            <i class="ri-phone-line me-1 text-muted"></i> {{ $student->phone ?? '---' }}
                                        </div>
                                        <div class="small text-dark mt-1">
                                            <i class="ri-mail-line me-1 text-muted"></i> {{ $student->email }}
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 border-top pt-2">
                                        <button wire:click="viewStudent({{ $student->id }})" class="btn btn-sm btn-light flex-fill text-primary">
                                            <i class="ri-eye-line"></i> Ko'rish
                                        </button>
                                        <button wire:click="editStudent({{ $student->id }})" class="btn btn-sm btn-light flex-fill text-warning">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <button wire:click="deleteStudent({{ $student->id }})" onclick="return confirm('O\'chirilsinmi?')" class="btn btn-sm btn-light flex-fill text-danger">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">Ma'lumot topilmadi</div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4">
                            {{ $students->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE / EDIT MODAL -->
    @if($showModal)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-white border-bottom-0 pb-0">
                        <h5 class="modal-title fw-bold text-dark">
                            @if($isEdit)
                                <i class="ri-pencil-line text-warning me-2"></i>Tahrirlash
                            @else
                                <i class="ri-user-add-line" style="color: var(--yuksalish-orange);"></i> Yangi Student
                            @endif
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    
                    <form wire:submit.prevent="saveStudent" autocomplete="off">
                        <div class="modal-body">
                            <div class="row g-3">
                                <!-- Ism -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Ism <span class="text-danger">*</span></label>
                                    <input type="text" wire:model.live="first_name" class="form-control search-input @error('first_name') is-invalid @enderror">
                                    @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Familya -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Familya <span class="text-danger">*</span></label>
                                    <input type="text" wire:model.live="last_name" class="form-control search-input @error('last_name') is-invalid @enderror">
                                    @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Username -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Username <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <span class="input-group-text bg-light border-end-0"><i class="ri-at-line"></i></span>
                                        <input type="text" wire:model="name" @if(!$isEdit) readonly @endif 
                                               class="form-control bg-light border-start-0 @error('name') is-invalid @enderror" 
                                               placeholder="Avtomatik yoziladi">
                                    </div>
                                    @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                                </div>

                                <!-- Sinfi -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Sinfi <span class="text-danger">*</span></label>
                                    <select wire:model="classes_id" class="form-select search-input @error('classes_id') is-invalid @enderror">
                                        <option value="">Tanlang</option>
                                        @foreach(\App\Models\Classes::all() as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('classes_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Email -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Email <span class="text-danger">*</span></label>
                                    <input type="email" wire:model="email" class="form-control search-input @error('email') is-invalid @enderror">
                                    @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                <!-- Telefon -->
                                <div class="col-md-6">
                                    <label class="form-label small fw-bold text-muted">Telefon</label>
                                    <input type="text" wire:model="phone" class="form-control search-input @error('phone') is-invalid @enderror" placeholder="+998 90 123 45 67">
                                    @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>

                                @if(!$isEdit)
                                    <div class="col-12">
                                        <div class="alert alert-light border border-warning d-flex align-items-center mb-0" role="alert">
                                            <i class="ri-information-fill text-warning me-2" style="font-size: 1.2rem;"></i>
                                            <div class="small">
                                                Yangi student uchun boshlang'ich parol: <strong>12345678</strong>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer border-top-0 pt-0 pb-4 pe-4">
                            <button type="button" wire:click="closeModal" class="btn btn-light px-4">Bekor qilish</button>
                            <button type="submit" class="btn btn-yuksalish px-4 shadow-sm">
                                <i class="ri-save-line me-1"></i> {{ $isEdit ? 'Yangilash' : 'Saqlash' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- VIEW MODAL -->
    @if($showViewModal && $viewingStudent)
        <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-body p-0">
                        <!-- Header -->
                        <div class="p-4 text-center text-white" style="background-color: var(--yuksalish-orange); border-radius: 8px 8px 0 0;">
                            <div class="avatar bg-white text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                                {{ substr($viewingStudent->first_name, 0, 1) }}
                            </div>
                            <h5 class="fw-bold mb-1">{{ $viewingStudent->first_name }} {{ $viewingStudent->last_name }}</h5>
                            <p class="mb-0 opacity-75">@ {{ $viewingStudent->name }}</p>
                        </div>
                        
                        <!-- Body -->
                        <div class="p-4">
                            <div class="row g-3">
                                <div class="col-6">
                                    <small class="text-muted d-block">Sinfi</small>
                                    <span class="badge badge-class fs-6">{{ $viewingStudent->classRelation->name ?? 'N/A' }}</span>
                                </div>
                                <div class="col-6 text-end">
                                    <small class="text-muted d-block">Status</small>
                                    <span class="text-success fw-bold"><i class="ri-checkbox-circle-fill"></i> Faol</span>
                                </div>

                                <div class="col-12 border-bottom pb-2 my-2"></div>

                                <div class="col-12">
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ri-mail-line text-muted me-2"></i>
                                        <span>{{ $viewingStudent->email }}</span>
                                    </div>
                                    <div class="d-flex align-items-center mb-2">
                                        <i class="ri-phone-line text-muted me-2"></i>
                                        <span>{{ $viewingStudent->phone ?? 'Kiritilmagan' }}</span>
                                    </div>
                                    <div class="d-flex align-items-center">
                                        <i class="ri-calendar-line text-muted me-2"></i>
                                        <span>Ro'yxatga olindi: {{ $viewingStudent->created_at->format('d.m.Y') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="modal-footer border-0 p-4 pt-0">
                            <button type="button" wire:click="closeViewModal" class="btn btn-light w-100">Yopish</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>