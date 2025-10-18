<div>
    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header Section --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">O'qituvchilar va Koordinatorlar</h4>
            <button wire:click="createUser" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi Foydalanuvchi
            </button>
        </div>

        <div class="card-body">
            {{-- Search Box --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <input
                        type="text"
                        wire:model.live.debounce.500ms="search"
                        class="form-control"
                        placeholder="Ism, email yoki login bo'yicha qidirish...">
                </div>
            </div>

            {{-- Users Table --}}
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered table-sm text-center">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Ism va Familya</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Lavozim</th>
                        <th>Fan</th>
                        <th>Status</th>
                        <th>Amallar</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td><strong>{{ $user->name }}</strong></td>
                            <td>{{ $user->first_name . ' ' . $user->last_name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->phone ?? 'N/A' }}</td>
                            <td>
                                @if($user->user_type === \App\Models\Users::TYPE_ADMIN)
                                    <span class="badge bg-danger">Admin</span>
                                @elseif($user->user_type === \App\Models\Users::TYPE_TEACHER)
                                    <span class="badge bg-primary">O'qituvchi</span>
                                @else
                                    <span class="badge bg-info">Koordinator</span>
                                @endif
                            </td>
                            <td>
                                @if($user->user_type === \App\Models\Users::TYPE_TEACHER && $user->subject)
                                    {{ $user->subject->name }}
                                @else
                                    -
                                @endif
                            </td>
                            <td>
                                @if($user->status === \App\Models\Users::STATUS_ACTIVE)
                                    <span class="badge bg-success">Faol</span>
                                @else
                                    <span class="badge bg-secondary">Bloklangan</span>
                                @endif
                            </td>
                            <td>
                                <button
                                    wire:click="viewUser({{ $user->id }})"
                                    class="btn btn-sm btn-info text-white">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button
                                    wire:click="editUser({{ $user->id }})"
                                    class="btn btn-sm btn-warning">

                                    <i style="font-size: 16px" class="ri-pencil-line"></i>
                                </button>
                                <button
                                    wire:click="deleteUser({{ $user->id }})"
                                    onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')"
                                    class="btn btn-sm btn-danger">
                                    <i style="font-size: 16px" class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Ma'lumot topilmadi</td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $isEdit ? 'Foydalanuvchini tahrirlash' : 'Yangi Foydalanuvchi qo\'shish' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
                        <form wire:submit.prevent="saveUser">
                            <div class="row">
                                {{-- First Name --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Ism <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        wire:model="first_name"
                                        class="form-control @error('first_name') is-invalid @enderror"
                                        placeholder="Ism">
                                    @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Last Name --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Familya <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        wire:model="last_name"
                                        class="form-control @error('last_name') is-invalid @enderror"
                                        placeholder="Familya">
                                    @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row">
                                {{-- Login --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Login <span class="text-danger">*</span></label>
                                    <input
                                        type="text"
                                        wire:model="name"
                                        class="form-control @error('name') is-invalid @enderror"
                                        placeholder="Login">
                                    @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>


                                {{-- Phone --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Telefon raqam</label>
                                    <div class="input-group">
                                        <span class="input-group-text">+998</span>
                                        <input
                                            type="text"
                                            wire:model="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            placeholder="90 123 45 67">
                                    </div>
                                    @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input
                                    type="email"
                                    wire:model="email"
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="example@mail.com">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                {{-- Password --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Parol
                                        @if(!$isEdit)
                                            <span class="text-danger">*</span>
                                        @endif
                                        @if($isEdit)
                                            <small class="text-muted">(Bo'sh qoldiring agar o'zgartirmoqchi
                                                bo'lmasangiz)</small>
                                        @endif
                                    </label>
                                    <input
                                        type="password"
                                        wire:model="password"
                                        class="form-control @error('password') is-invalid @enderror">
                                    @error('password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Confirm Password --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Parolni tasdiqlang
                                        @if(!$isEdit)
                                            <span class="text-danger">*</span>
                                        @endif
                                    </label>
                                    <input
                                        type="password"
                                        wire:model="confirm_password"
                                        class="form-control @error('confirm_password') is-invalid @enderror">
                                    @error('confirm_password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>


                            <div class="row">
                                {{-- Users Type --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Lavozim <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" wire:model.live="user_type"
                                               value="{{ \App\Models\Users::TYPE_ADMIN }}" id="admin">
                                        <label class="form-check-label" for="admin">
                                            Super Admin
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" wire:model.live="user_type"
                                               value="{{ \App\Models\Users::TYPE_TEACHER }}" id="teacher">
                                        <label class="form-check-label" for="teacher">
                                            O'qituvchi
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" wire:model.live="user_type"
                                               value="{{ \App\Models\Users::TYPE_KOORDINATOR }}" id="koordinator">
                                        <label class="form-check-label" for="koordinator">
                                            Koordinator
                                        </label>
                                    </div>
                                    @error('user_type') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>

                                {{-- Status --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Status <span class="text-danger">*</span></label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" wire:model="status"
                                               value="{{ \App\Models\Users::STATUS_ACTIVE }}" id="active">
                                        <label class="form-check-label" for="active">
                                            Faol
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" wire:model="status"
                                               value="{{ \App\Models\Users::STATUS_IN_ACTIVE }}" id="inactive">
                                        <label class="form-check-label" for="inactive">
                                            Bloklangan
                                        </label>
                                    </div>
                                    @error('status') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>


                            {{-- Subject (faqat o'qituvchi uchun) --}}
                            @if($user_type === \App\Models\Users::TYPE_TEACHER)
                                <div class="mb-3">
                                    <label class="form-label">Fan <span class="text-danger">*</span></label>
                                    <select wire:model="subject_id"
                                            class="form-select @error('subject_id') is-invalid @enderror">
                                        <option value="">Fan nomini tanlang</option>
                                        @php
                                            $subjects = \App\Models\Subjects::orderBy('name')->get();
                                        @endphp
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('subject_id') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            @endif

                            {{-- Buttons --}}
                            <div class="d-flex justify-content-end gap-2">
                                <button type="button" wire:click="closeModal" class="btn btn-secondary">
                                    Bekor qilish
                                </button>
                                <button type="submit" class="btn btn-primary">
                                    {{ $isEdit ? 'Yangilash' : 'Saqlash' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- View Modal --}}
    @if($showViewModal && $viewingUser)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-user"></i> Foydalanuvchi Ma'lumotlari
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Shaxsiy Ma'lumotlar</h6>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">ID</small>
                                            <strong>{{ $viewingUser->id }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Login</small>
                                            <strong>{{ $viewingUser->name }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">To'liq Ism</small>
                                            <strong>{{ $viewingUser->first_name . ' ' . $viewingUser->last_name }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Email</small>
                                            <strong>{{ $viewingUser->email }}</strong>
                                        </div>


                                        <div class="mb-3">
                                            <small class="text-muted d-block">Telefon</small>
                                            <strong>{{ $viewingUser->phone ?? 'Kiritilmagan' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Tizim Ma'lumotlari</h6>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Lavozim</small>
                                            @if($viewingUser->user_type === \App\Models\Users::TYPE_ADMIN)
                                                <span class="badge bg-danger">Admin</span>
                                            @elseif($viewingUser->user_type === \App\Models\Users::TYPE_TEACHER)
                                                <span class="badge bg-primary">O'qituvchi</span>
                                            @else
                                                <span class="badge bg-info">Koordinator</span>
                                            @endif
                                        </div>

                                        @if($viewingUser->user_type === \App\Models\Users::TYPE_TEACHER && $viewingUser->subject)
                                            <div class="mb-3">
                                                <small class="text-muted d-block">Fan</small>
                                                <strong>{{ $viewingUser->subject->name }}</strong>
                                            </div>
                                        @endif

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Status</small>
                                            @if($viewingUser->status === \App\Models\Users::STATUS_ACTIVE)
                                                <span class="badge bg-success">Faol</span>
                                            @else
                                                <span class="badge bg-secondary">Bloklangan</span>
                                            @endif
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Ro'yxatdan o'tgan</small>
                                            <strong>{{ $viewingUser->created_at->format('d.m.Y H:i') }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Oxirgi yangilanish</small>
                                            <strong>{{ $viewingUser->updated_at->format('d.m.Y H:i') }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeViewModal">
                            Yopish
                        </button>
                        <button type="button" class="btn btn-warning"
                                wire:click="editUser({{ $viewingUser->id }})">
                            <i class="fas fa-edit"></i> Tahrirlash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
