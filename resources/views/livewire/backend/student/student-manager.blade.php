<div>
    <style>
        :root {
            --yuksalish-orange: #F58025;
            --yuksalish-dark: #212529;
        }

        /* Knopka */
        .btn-yuksalish {
            background-color: var(--yuksalish-orange);
            color: white;
            border: none;
            font-weight: 500;
            padding: 0.6rem 1.5rem;
            border-radius: 8px;
            white-space: nowrap;
        }

        .btn-yuksalish:hover {
            background-color: #d96d1b;
            color: white;
        }

        /* SEARCH BOX (Yangi dizayn) */
        .search-box {
            background: #fff;
            border: 1px solid #e0e0e0;
            border-radius: 10px;
            padding: 5px 15px;
            display: flex;
            align-items: center;
            transition: all 0.3s ease;
            height: 45px;
        }

        .search-box:focus-within {
            border-color: var(--yuksalish-orange);
            box-shadow: 0 0 0 4px rgba(245, 128, 37, 0.1);
        }

        .search-box i {
            color: #999;
            font-size: 1.2rem;
            margin-right: 10px;
        }

        /* Sarlavha */
        .page-title {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--yuksalish-dark);
            margin: 0;
            white-space: nowrap;
        }

        /* Jadval */
        .table-yuksalish thead th {
            background-color: var(--yuksalish-dark);
            color: white;
            padding: 15px;
            font-weight: 500;
            border: none;
        }

        /* Badge */
        .badge-class {
            background-color: var(--yuksalish-orange);
            color: white;
            font-size: 0.85rem;
        }

        /* Mobile Card */
        .mobile-card {
            border-left: 5px solid var(--yuksalish-orange);
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
        }

        /* Pagination */
        .page-item.active .page-link {
            background-color: var(--yuksalish-orange);
            border-color: var(--yuksalish-orange);
        }

        .page-link {
            color: var(--yuksalish-orange);
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">

                    <div class="card-header bg-white py-4">
                        <div class="row align-items-center g-3">

                            <div class="col-12 col-md-auto">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-2 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                        <i class="ri-graduation-cap-line" style="color: var(--yuksalish-orange); font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="page-title">O'quvchilar ro'yxati</h4>
                                </div>
                            </div>

                            <div class="col-12 col-md">
                                <div class="search-box">
                                    <i class="ri-search-line"></i>
                                    <input wire:model.live.debounce.300ms="search"
                                        type="text"
                                        class="form-control border-0 shadow-none bg-transparent p-0"
                                        placeholder="Qidirish (Ism, Sinf, Email)...">
                                </div>
                            </div>

                            {{-- YANGI QO'SHILGAN QISM: Sinf filtri --}}
                            <div class="col-12 col-md-auto">
                                <select wire:model.live="classFilter" class="form-select" style="height: 45px; border-radius: 10px; border: 1px solid #e0e0e0; min-width: 150px;">
                                    <option value="">Barcha Sinflar</option>
                                    @foreach($classes as $class)
                                    <option value="{{ $class->id }}">{{ $class->name }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 col-md-auto">
                                <button wire:click="createStudent" class="btn btn-yuksalish w-100">
                                    <i class="ri-add-line me-2"></i> Yangi O'quvchi
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        @if (session()->has('message'))
                        <div class="p-3">
                            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
                                <i class="ri-checkbox-circle-line me-2"></i> {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                        @endif

                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle table-yuksalish mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 60px;">ID</th>
                                        <th>O'quvchi</th>
                                        <th>Sinfi</th>
                                        <th>Kontakt</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($students as $student)
                                    <tr>
                                        <td class="text-center fw-bold text-secondary">{{ $student->id }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</span>
                                                <small class="text-muted">@ {{ $student->name }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge badge-class px-3 py-2 rounded-pill">
                                                {{ $student->classRelation->name ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark">{{ $student->phone ?? '-' }}</span>
                                                <small class="text-muted">{{ $student->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Faol</span>
                                        </td>
                                        <td class="text-end pe-4">
                                            <button wire:click="viewStudent({{ $student->id }})" class="btn btn-sm btn-light text-primary shadow-sm border me-1">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button wire:click="editStudent({{ $student->id }})" class="btn btn-sm btn-light text-warning shadow-sm border me-1">
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button wire:click="deleteStudent({{ $student->id }})" onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')" class="btn btn-sm btn-light text-danger shadow-sm border">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="ri-inbox-line display-4 d-block mb-3 opacity-25"></i>
                                                Ma'lumot topilmadi
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none p-3 bg-light">
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
                                    <button wire:click="viewStudent({{ $student->id }})" class="btn btn-light border flex-fill text-primary fw-bold">
                                        Ko'rish
                                    </button>
                                    <button wire:click="editStudent({{ $student->id }})" class="btn btn-light border flex-fill text-warning fw-bold">
                                        Tahrirlash
                                    </button>
                                    <button wire:click="deleteStudent({{ $student->id }})" onclick="return confirm('O\'chirilsinmi?')" class="btn btn-light border flex-fill text-danger fw-bold">
                                        O'chirish
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted">Ma'lumot topilmadi</div>
                            @endforelse
                        </div>

                        <div class="mt-4 px-3 pb-3">
                            {{ $students->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

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
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Ism <span class="text-danger">*</span></label>
                                <input type="text" wire:model.live="first_name" class="form-control search-input @error('first_name') is-invalid @enderror">
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Familya <span class="text-danger">*</span></label>
                                <input type="text" wire:model.live="last_name" class="form-control search-input @error('last_name') is-invalid @enderror">
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

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

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Email <span class="text-danger">*</span></label>
                                <input type="email" wire:model="email" class="form-control search-input @error('email') is-invalid @enderror">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

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

    @if($showViewModal && $viewingStudent)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body p-0">
                    <div class="p-4 text-center text-white" style="background-color: var(--yuksalish-orange); border-radius: 8px 8px 0 0;">
                        <div class="avatar bg-white text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                            {{ substr($viewingStudent->first_name, 0, 1) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $viewingStudent->first_name }} {{ $viewingStudent->last_name }}</h5>
                        <p class="mb-0 opacity-75">@ {{ $viewingStudent->name }}</p>
                    </div>

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