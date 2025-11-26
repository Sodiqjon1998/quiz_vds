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

        /* Badges */
        .badge-teacher { background-color: #17a2b8; color: white; }
        .badge-koordinator { background-color: #ffc107; color: #212529; }

        /* Mobile Card */
        .mobile-card {
            border-left: 5px solid var(--yuksalish-orange);
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            margin-bottom: 15px;
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
                <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">
                    
                    <div class="card-header bg-white py-4">
                        <div class="row align-items-center g-3">
                            
                            <div class="col-12 col-md-auto">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-2 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                        <i class="ri-team-line" style="color: var(--yuksalish-orange); font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="page-title">Hodimlar ro'yxati</h4>
                                </div>
                            </div>

                            <div class="col-12 col-md">
                                <div class="search-box">
                                    <i class="ri-search-line"></i>
                                    <input wire:model.live.debounce.300ms="search" 
                                           type="text" 
                                           class="form-control border-0 shadow-none bg-transparent p-0" 
                                           placeholder="Qidirish (Ism, Fan, Sinf)...">
                                </div>
                            </div>

                            <div class="col-12 col-md-auto">
                                <button wire:click="createUsers" class="btn btn-yuksalish w-100">
                                    <i class="fas fa-plus me-2"></i> Yangi hodim
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
                                        <th class="text-center" style="width: 50px;">#</th>
                                        <th>Ism Familya</th>
                                        <th>Kontakt</th>
                                        <th>Lavozim</th>
                                        <th>Fan / Sinflar</th>
                                        <th>Status</th>
                                        <th class="text-end pe-4">Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                @forelse($users as $user)
                                    <tr>
                                        <td class="text-center fw-bold text-secondary">{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold text-dark">{{ $user->first_name }} {{ $user->last_name }}</span>
                                                <small class="text-muted">@ {{ $user->name }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="text-dark">{{ $user->phone ?? '-' }}</span>
                                                <small class="text-muted">{{ $user->email }}</small>
                                            </div>
                                        </td>
                                        <td>
                                            @if($user->user_type == \App\Models\Users::TYPE_TEACHER)
                                                <span class="badge badge-teacher">O'qituvchi</span>
                                            @elseif($user->user_type == \App\Models\Users::TYPE_KOORDINATOR)
                                                <span class="badge badge-koordinator">Koordinator</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->user_type == \App\Models\Users::TYPE_TEACHER)
                                                <span class="fw-medium text-dark">{{ $user->subject->name ?? '---' }}</span>
                                            @elseif($user->user_type == \App\Models\Users::TYPE_KOORDINATOR && $user->classes_id)
                                                @php
                                                    $classIds = json_decode($user->classes_id, true);
                                                    $classes = \App\Models\Classes::whereIn('id', $classIds)->pluck('name')->toArray();
                                                @endphp
                                                <small class="text-wrap" style="max-width: 200px; display:block;">
                                                    {{ implode(', ', $classes) }}
                                                </small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->status == \App\Models\Users::STATUS_ACTIVE)
                                                <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Faol</span>
                                            @else
                                                <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Nofaol</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <button wire:click="viewUsers({{ $user->id }})" class="btn btn-sm btn-light text-primary shadow-sm border me-1"><i class="ri-eye-line"></i></button>
                                            <button wire:click="editUsers({{ $user->id }})" class="btn btn-sm btn-light text-warning shadow-sm border me-1"><i class="ri-pencil-line"></i></button>
                                            <button wire:click="deleteUsers({{ $user->id }})" onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')" class="btn btn-sm btn-light text-danger shadow-sm border"><i class="ri-delete-bin-line"></i></button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5 text-muted">Ma'lumot topilmadi</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none p-3 bg-light">
                            @forelse($users as $user)
                                <div class="mobile-card p-3">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <h6 class="fw-bold mb-0 text-dark">{{ $user->first_name }} {{ $user->last_name }}</h6>
                                            <small class="text-muted">@ {{ $user->name }}</small>
                                        </div>
                                        @if($user->status == \App\Models\Users::STATUS_ACTIVE)
                                            <span class="badge bg-success bg-opacity-10 text-success">Faol</span>
                                        @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger">Nofaol</span>
                                        @endif
                                    </div>

                                    <div class="mb-3">
                                        <div class="mb-1">
                                            @if($user->user_type == \App\Models\Users::TYPE_TEACHER)
                                                <span class="badge badge-teacher">O'qituvchi</span>
                                            @elseif($user->user_type == \App\Models\Users::TYPE_KOORDINATOR)
                                                <span class="badge badge-koordinator">Koordinator</span>
                                            @endif
                                        </div>

                                        <div class="small text-dark mt-2">
                                            <i class="ri-book-mark-line me-1 text-muted"></i>
                                            @if($user->user_type == \App\Models\Users::TYPE_TEACHER)
                                                <b>Fan:</b> {{ $user->subject->name ?? '---' }}
                                            @elseif($user->user_type == \App\Models\Users::TYPE_KOORDINATOR && $user->classes_id)
                                                @php
                                                    $classIds = json_decode($user->classes_id, true);
                                                    $classes = \App\Models\Classes::whereIn('id', $classIds)->pluck('name')->toArray();
                                                @endphp
                                                <b>Sinflar:</b> {{ implode(', ', $classes) }}
                                            @endif
                                        </div>

                                        <div class="small text-dark mt-1">
                                            <i class="ri-phone-line me-1 text-muted"></i> {{ $user->phone ?? '---' }}
                                        </div>
                                    </div>

                                    <div class="d-flex gap-2 border-top pt-2">
                                        <button wire:click="viewUsers({{ $user->id }})" class="btn btn-light border flex-fill text-primary fw-bold">
                                            <i class="ri-eye-line"></i> Ko'rish
                                        </button>
                                        <button wire:click="editUsers({{ $user->id }})" class="btn btn-light border flex-fill text-warning fw-bold">
                                            <i class="ri-pencil-line"></i>
                                        </button>
                                        <button wire:click="deleteUsers({{ $user->id }})" onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')" class="btn btn-light border flex-fill text-danger fw-bold">
                                            <i class="ri-delete-bin-line"></i>
                                        </button>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-5 text-muted">Ma'lumot topilmadi</div>
                            @endforelse
                        </div>

                        <div class="mt-4 px-3 pb-3">
                            {{ $users->links() }}
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
                            <i class="ri-pencil-line text-warning me-2"></i>Hodimni tahrirlash
                        @else
                            <i class="ri-user-add-line" style="color: var(--yuksalish-orange);"></i> Yangi hodim
                        @endif
                    </h5>
                    <button type="button" wire:click="closeModal" class="btn-close"></button>
                </div>

                <form wire:submit.prevent="saveUsers" autocomplete="off">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Ism <span class="text-danger">*</span></label>
                                <input type="text" wire:model.live="first_name" class="form-control search-input @error('first_name') is-invalid @enderror" placeholder="Ismni kiriting">
                                @error('first_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Familya <span class="text-danger">*</span></label>
                                <input type="text" wire:model.live="last_name" class="form-control search-input @error('last_name') is-invalid @enderror" placeholder="Familyani kiriting">
                                @error('last_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Username <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <span class="input-group-text bg-light border-end-0"><i class="ri-at-line"></i></span>
                                    <input type="text" wire:model="name" @if(!$isEdit) readonly @endif
                                           class="form-control bg-light border-start-0 @error('name') is-invalid @enderror"
                                           placeholder="Username avtomatik yoziladi">
                                </div>
                                @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Email <span class="text-danger">*</span></label>
                                <input type="email" wire:model="email" class="form-control search-input @error('email') is-invalid @enderror" placeholder="email@example.com">
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Hodim turi <span class="text-danger">*</span></label>
                                <select wire:model.live="user_type" class="form-select search-input @error('user_type') is-invalid @enderror">
                                    <option value="">Tanlang</option>
                                    <option value="{{ \App\Models\Users::TYPE_TEACHER }}">O'qituvchi</option>
                                    <option value="{{ \App\Models\Users::TYPE_KOORDINATOR }}">Koordinator</option>
                                </select>
                                @error('user_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label small fw-bold text-muted">Telefon</label>
                                <input type="text" wire:model="phone" class="form-control search-input @error('phone') is-invalid @enderror" placeholder="+998 90 123 45 67">
                                @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            @if($user_type == \App\Models\Users::TYPE_TEACHER)
                            <div class="col-12">
                                <div class="p-3 bg-light rounded border">
                                    <label class="form-label small fw-bold text-primary">Fan biriktirish <span class="text-danger">*</span></label>
                                    <select wire:model="subject_id" class="form-select search-input @error('subject_id') is-invalid @enderror">
                                        <option value="">Fanni tanlang</option>
                                        @foreach(\App\Models\Subjects::all() as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('subject_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                </div>
                            </div>
                            @endif

                            @if($user_type == \App\Models\Users::TYPE_KOORDINATOR)
                            <div class="col-12">
                                <div class="p-3 bg-light rounded border">
                                    <label class="form-label small fw-bold text-warning">Sinflarni biriktirish <span class="text-danger">*</span></label>
                                    <select wire:model="classes_id" multiple class="form-select search-input @error('classes_id') is-invalid @enderror" style="height: 120px;">
                                        @foreach(\App\Models\Classes::all() as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    <small class="text-muted d-block mt-1"><i class="ri-information-line"></i> Ctrl tugmasini bosib bir nechta tanlash mumkin</small>
                                    @error('classes_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
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

    @if($showViewModal && $viewingUsrs)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body p-0">
                    <div class="p-4 text-center text-white" style="background-color: var(--yuksalish-orange); border-radius: 8px 8px 0 0;">
                        <div class="avatar bg-white text-warning rounded-circle mx-auto mb-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; font-size: 24px;">
                            {{ substr($viewingUsrs->first_name, 0, 1) }}
                        </div>
                        <h5 class="fw-bold mb-1">{{ $viewingUsrs->first_name }} {{ $viewingUsrs->last_name }}</h5>
                        <p class="mb-0 opacity-75">@ {{ $viewingUsrs->name }}</p>
                    </div>

                    <div class="p-4">
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted d-block">Lavozim</small>
                                @if($viewingUsrs->user_type == \App\Models\Users::TYPE_TEACHER)
                                <span class="badge badge-teacher">O'qituvchi</span>
                                @elseif($viewingUsrs->user_type == \App\Models\Users::TYPE_KOORDINATOR)
                                <span class="badge badge-koordinator">Koordinator</span>
                                @endif
                            </div>
                            <div class="col-6 text-end">
                                <small class="text-muted d-block">Status</small>
                                @if($viewingUsrs->status == \App\Models\Users::STATUS_ACTIVE)
                                <span class="text-success fw-bold"><i class="ri-checkbox-circle-fill"></i> Faol</span>
                                @else
                                <span class="text-danger fw-bold"><i class="ri-close-circle-fill"></i> Nofaol</span>
                                @endif
                            </div>

                            <div class="col-12 border-bottom pb-2 my-2"></div>

                            <div class="col-12">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ri-mail-line text-muted me-2"></i>
                                    <span>{{ $viewingUsrs->email }}</span>
                                </div>
                                <div class="d-flex align-items-center mb-2">
                                    <i class="ri-phone-line text-muted me-2"></i>
                                    <span>{{ $viewingUsrs->phone ?? 'Kiritilmagan' }}</span>
                                </div>
                            </div>

                            @if($viewingUsrs->user_type == \App\Models\Users::TYPE_TEACHER)
                            <div class="col-12 bg-light p-3 rounded">
                                <small class="text-muted d-block mb-1">Biriktirilgan fan:</small>
                                <span class="fw-bold text-dark">{{ $viewingUsrs->subject->name ?? 'Mavjud emas' }}</span>
                            </div>
                            @elseif($viewingUsrs->user_type == \App\Models\Users::TYPE_KOORDINATOR)
                            <div class="col-12 bg-light p-3 rounded">
                                <small class="text-muted d-block mb-1">Biriktirilgan sinflar:</small>
                                @if($viewingUsrs->classes_id)
                                @php
                                $classIds = json_decode($viewingUsrs->classes_id, true);
                                $classes = \App\Models\Classes::whereIn('id', $classIds)->pluck('name')->toArray();
                                @endphp
                                <span class="fw-bold text-dark">{{ implode(', ', $classes) }}</span>
                                @else
                                <span class="text-muted">Sinflar yo'q</span>
                                @endif
                            </div>
                            @endif
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

@push('styles')
<style>
    .modal.show {
        display: block;
    }
</style>
@endpush