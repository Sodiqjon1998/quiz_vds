<div>
    <style>
        :root {
            --yuksalish-orange: #F58025;
            /* Logo rangi */
            --yuksalish-dark: #212529;
            --yuksalish-gray: #f8f9fa;
        }

        /* Asosiy knopkalar */
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
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            border-radius: 8px;
        }

        /* Badges */
        .badge-teacher {
            background-color: #17a2b8;
            color: white;
        }

        .badge-koordinator {
            background-color: #ffc107;
            color: #212529;
        }

        /* Pagination rangini o'zgartirish */
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
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-white py-3 d-flex flex-column flex-md-row justify-content-between align-items-center gap-3">
                        <h4 class="card-title mb-0 fw-bold text-dark">
                            <i class="ri-team-line" style="color: var(--yuksalish-orange);"></i> Hodimlar ro'yxati
                        </h4>

                        <div class="d-flex gap-2 w-100 w-md-auto">
                            <button wire:click="createUsers" class="btn btn-yuksalish w-100 w-md-auto">
                                <i class="fas fa-plus me-1"></i> Yangi hodim
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        @if (session()->has('message'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            {{ session('message') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        @endif

                        <div class="row mb-4">
                            <div class="col-md-6 col-lg-4">
                                <div class="input-group">
                                    <span class="input-group-text bg-white border-end-0 text-muted">
                                        <i class="ri-search-line"></i>
                                    </span>
                                    <input wire:model.live.debounce.300ms="search"
                                        type="text"
                                        class="form-control border-start-0 ps-0 search-input"
                                        placeholder="Qidirish (Ism, Fan, Sinf)...">
                                </div>
                            </div>
                        </div>

                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle table-yuksalish">
                                <thead>
                                    <tr>
                                        <th class="rounded-start">#</th>
                                        <th>Ism Familya</th>
                                        <th>Kontakt</th>
                                        <th>Lavozim</th>
                                        <th>Fan / Sinflar</th>
                                        <th>Status</th>
                                        <th class="text-end rounded-end">Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span class="fw-bold">{{ $user->first_name }} {{ $user->last_name }}</span>
                                                <span class="small text-muted">@ {{ $user->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $user->phone ?? '-' }}</span>
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
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Faol</span>
                                            @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Nofaol</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button wire:click="viewUsers({{ $user->id }})" class="btn btn-sm btn-light text-primary"><i class="ri-eye-line"></i></button>
                                            <button wire:click="editUsers({{ $user->id }})" class="btn btn-sm btn-light text-warning"><i class="ri-pencil-line"></i></button>
                                            <button wire:click="deleteUsers({{ $user->id }})" onclick="return confirm('O\'chirilsinmi?')" class="btn btn-sm btn-light text-danger"><i class="ri-delete-bin-line"></i></button>
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

                        <div class="d-md-none">
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
                                    <button wire:click="viewUsers({{ $user->id }})" class="btn btn-sm btn-light flex-fill text-primary">
                                        <i class="ri-eye-line"></i> Ko'rish
                                    </button>
                                    <button wire:click="editUsers({{ $user->id }})" class="btn btn-sm btn-light flex-fill text-warning">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button wire:click="deleteUsers({{ $user->id }})" onclick="return confirm('O\'chirilsinmi?')" class="btn btn-sm btn-light flex-fill text-danger">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted">Ma'lumot topilmadi</div>
                            @endforelse
                        </div>

                        <div class="mt-4">
                            {{ $users->links() }}
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Create/Edit Modal -->
    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">{{ $isEdit ? 'Hodimni tahrirlash' : 'Yangi hodim qo\'shish' }}</h5>
                    <button type="button" wire:click="closeModal" class="close btn btn-danger">
                        <span>&times;</span>
                    </button>
                </div>
                <form wire:submit.prevent="saveUsers" autocomplete="off">
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Ism <span class="text-danger">*</span></label>
                                    <input type="text"
                                        wire:model.live="first_name"
                                        autocomplete="off"
                                        class="form-control @error('first_name') is-invalid @enderror">
                                    @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Familya <span class="text-danger">*</span></label>
                                    <input type="text"
                                        wire:model.live="last_name"
                                        autocomplete="off"
                                        class="form-control @error('last_name') is-invalid @enderror">
                                    @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Username <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="text"
                                            wire:model="name"
                                            autocomplete="off"
                                            @if(!$isEdit) readonly @endif
                                            class="form-control @error('name') is-invalid @enderror"
                                            style="@if(!$isEdit) background-color: #f8f9fa; @endif">
                                        @if(!$isEdit)
                                        <div class="input-group-append">
                                            <span class="input-group-text bg-primary text-white">
                                                <i class="ri-lock-line"></i>
                                            </span>
                                        </div>
                                        @endif
                                    </div>
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                    @if(!$isEdit)
                                    <small class="form-text text-muted">
                                        <i class="ri-information-line"></i> Username avtomatik generatsiya
                                        qilinadi
                                    </small>
                                    @endif
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Email <span class="text-danger">*</span></label>
                                    <input type="email"
                                        wire:model="email"
                                        autocomplete="off"
                                        class="form-control @error('email') is-invalid @enderror">
                                    @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Hodim turi <span class="text-danger">*</span></label>
                                    <select wire:model.live="user_type"
                                        class="form-control @error('user_type') is-invalid @enderror">
                                        <option value="">Tanlang</option>
                                        <option value="{{ \App\Models\Users::TYPE_TEACHER }}">O'qituvchi</option>
                                        <option value="{{ \App\Models\Users::TYPE_KOORDINATOR }}">Koordinator
                                        </option>
                                    </select>
                                    @error('user_type') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Telefon</label>
                                    <input type="text"
                                        wire:model="phone"
                                        autocomplete="off"
                                        class="form-control @error('phone') is-invalid @enderror">
                                    @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- O'qituvchi tanlansa - Fan --}}
                            @if($user_type == \App\Models\Users::TYPE_TEACHER)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Fan nomi <span class="text-danger">*</span></label>
                                    <select wire:model="subject_id"
                                        class="form-control @error('subject_id') is-invalid @enderror">
                                        <option value="">Tanlang</option>
                                        @foreach(\App\Models\Subjects::all() as $subject)
                                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('subject_id') <span
                                        class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            @endif

                            {{-- Koordinator tanlansa - Sinflar (ko'p tanlov) --}}
                            @if($user_type == \App\Models\Users::TYPE_KOORDINATOR)
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Sinflar <span class="text-danger">*</span></label>
                                    <select wire:model="classes_id"
                                        multiple
                                        class="form-control @error('classes_id') is-invalid @enderror"
                                        style="height: 120px;">
                                        @foreach(\App\Models\Classes::all() as $class)
                                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('classes_id') <span
                                        class="text-danger">{{ $message }}</span> @enderror
                                    <small class="form-text text-muted">
                                        <i class="ri-information-line"></i> Bir nechta sinfni tanlash uchun Ctrl
                                        tugmasini bosib turing
                                    </small>
                                </div>
                            </div>
                            @endif

                            @if(!$user_type || ($user_type != \App\Models\Users::TYPE_TEACHER && $user_type != \App\Models\Users::TYPE_KOORDINATOR))
                            <div class="col-md-6"></div> {{-- Bo'sh joy --}}
                            @endif


                            {{-- @if(!$isEdit)--}}
                            {{-- <div class="col-md-6">--}}
                            {{-- <div class="form-group">--}}
                            {{-- <label>Parol <span class="text-danger">*</span></label>--}}
                            {{-- <input type="password"--}}
                            {{-- wire:model="password"--}}
                            {{-- autocomplete="new-password"--}}
                            {{-- class="form-control @error('password') is-invalid @enderror">--}}
                            {{-- @error('password') <span class="text-danger">{{ $message }}</span> @enderror--}}
                            {{-- </div>--}}
                            {{-- </div>--}}
                            {{-- @else--}}
                            {{-- <div class="col-md-6">--}}
                            {{-- <div class="form-group">--}}
                            {{-- <label>Yangi parol (bo'sh qoldiring, agar o'zgartirmoqchi--}}
                            {{-- bo'lmasangiz)</label>--}}
                            {{-- <input type="password"--}}
                            {{-- wire:model="password"--}}
                            {{-- autocomplete="new-password"--}}
                            {{-- class="form-control @error('password') is-invalid @enderror">--}}
                            {{-- @error('password') <span class="text-danger">{{ $message }}</span> @enderror--}}
                            {{-- </div>--}}
                            {{-- </div>--}}
                            {{-- @endif--}}
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" wire:click="closeModal" class="btn btn-secondary">Bekor qilish
                        </button>
                        <button type="submit"
                            class="btn btn-primary">{{ $isEdit ? 'Yangilash' : 'Saqlash' }}</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- View Modal -->
    @if($showViewModal && $viewingUsrs)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5);" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Hodim ma'lumotlari</h5>
                    <button type="button" wire:click="closeViewModal" class="close">
                        <span>&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Username:</strong> {{ $viewingUsrs->name }}</p>
                            <p><strong>Ism:</strong> {{ $viewingUsrs->first_name }}</p>
                            <p><strong>Familya:</strong> {{ $viewingUsrs->last_name }}</p>
                            <p><strong>Email:</strong> {{ $viewingUsrs->email }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Telefon:</strong> {{ $viewingUsrs->phone ?? 'N/A' }}</p>
                            <p><strong>Fan:</strong> {{ $viewingUsrs->subject->name ?? 'N/A' }}</p>
                            <p><strong>Status:</strong>
                                @if($viewingUsrs->status == \App\Models\Users::STATUS_ACTIVE)
                                <span class="badge badge-success">Faol</span>
                                @else
                                <span class="badge badge-danger">Nofaol</span>
                                @endif
                            </p>
                            <p><strong>Ro'yxatdan o'tgan
                                    sana:</strong> {{ $viewingUsrs->created_at->format('d.m.Y H:i') }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Hodim turi:</strong>
                                @if($viewingUsrs->user_type == \App\Models\Users::TYPE_TEACHER)
                                <span class="badge badge-info">O'qituvchi</span>
                                @elseif($viewingUsrs->user_type == \App\Models\Users::TYPE_KOORDINATOR)
                                <span class="badge badge-warning">Koordinator</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            @if($viewingUsrs->user_type == \App\Models\Users::TYPE_TEACHER)
                            <p><strong>Fan:</strong> {{ $viewingUsrs->subject->name ?? 'N/A' }}</p>
                            @elseif($viewingUsrs->user_type == \App\Models\Users::TYPE_KOORDINATOR && $viewingUsrs->classes_id)
                            @php
                            $classIds = json_decode($viewingUsrs->classes_id, true);
                            $classes = \App\Models\Classes::whereIn('id', $classIds)->pluck('name')->toArray();
                            @endphp
                            <p><strong>Sinflar:</strong> {{ implode(', ', $classes) }}</p>
                            @endif
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" wire:click="closeViewModal" class="btn btn-secondary">Yopish</button>
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