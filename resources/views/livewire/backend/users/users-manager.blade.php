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
<<<<<<< HEAD
            <h4 class="mb-0">Students Ro'yxati</h4>
            <button wire:click="createStudent" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi Student
=======
            <h4 class="mb-0">O'qituvchilar va Koordinatorlar</h4>
            <button wire:click="createUser" class="btn btn-primary">
                <i class="fas fa-plus"></i> Yangi Foydalanuvchi
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
            </button>
        </div>

        <div class="card-body">
            {{-- Search Box --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <input
                        type="text"
<<<<<<< HEAD
                        wire:model.live="search"
                        class="form-control"
                        placeholder="Ism yoki email bo'yicha qidirish...">
                </div>
            </div>

            {{-- Students Table --}}
=======
                        wire:model.live.debounce.500ms="search"
                        class="form-control"
                        placeholder="Ism, email yoki login bo'yicha qidirish...">
                </div>
            </div>

            {{-- Users Table --}}
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered table-sm text-center">
                    <thead>
                    <tr>
                        <th>ID</th>
<<<<<<< HEAD
                        <th>Ism va Familya</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Sana</th>
=======
                        <th>Login</th>
                        <th>Ism va Familya</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Lavozim</th>
                        <th>Fan</th>
                        <th>Status</th>
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                        <th>Amallar</th>
                    </tr>
                    </thead>
                    <tbody>
<<<<<<< HEAD
                    @forelse($users as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td>{{ $student->first_name. ' ' . $student->last_name }}</td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->phone ?? 'N/A' }}</td>
                            <td>{{ $student->created_at->format('d.m.Y') }}</td>
                            <td>
                                {{-- Ko'rish tugmasi --}}
                                <button
                                    wire:click="viewStudent({{ $student->id }})"
=======
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
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                                    class="btn btn-sm btn-info text-white">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button
<<<<<<< HEAD
                                    wire:click="editStudent({{ $student->id }})"
=======
                                    wire:click="editUser({{ $user->id }})"
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                                    class="btn btn-sm btn-warning">
                                    <i style="font-size: 16px" class="ri-pencil-line"></i>
                                </button>
                                <button
<<<<<<< HEAD
                                    wire:click="deleteStudent({{ $student->id }})"
=======
                                    wire:click="deleteUser({{ $user->id }})"
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                                    onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')"
                                    class="btn btn-sm btn-danger">
                                    <i style="font-size: 16px" class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
<<<<<<< HEAD
                            <td colspan="6" class="text-center">Ma'lumot topilmadi</td>
=======
                            <td colspan="9" class="text-center">Ma'lumot topilmadi</td>
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
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

<<<<<<< HEAD
    {{-- Modal --}}
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $isEdit ? 'Student ma\'lumotlarini tahrirlash' : 'Yangi Student qo\'shish' }}
=======
    {{-- Create/Edit Modal --}}
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $isEdit ? 'Foydalanuvchini tahrirlash' : 'Yangi Foydalanuvchi qo\'shish' }}
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
<<<<<<< HEAD
                        <form wire:submit.prevent="saveStudent">
                            {{-- Name --}}
                            <div class="mb-3">
                                <label class="form-label">Username <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    wire:model="name"
                                    class="form-control @error('name') is-invalid @enderror">
                                @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- First Name --}}
                            <div class="mb-3">
                                <label class="form-label">Ism <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    wire:model="first_name"
                                    class="form-control @error('first_name') is-invalid @enderror">
                                @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Last Name --}}
                            <div class="mb-3">
                                <label class="form-label">Familya <span class="text-danger">*</span></label>
                                <input
                                    type="text"
                                    wire:model="last_name"
                                    class="form-control @error('last_name') is-invalid @enderror">
                                @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Classes --}}
                            <div class="mb-3">
                                <label class="form-label">Sinfi <span class="text-danger">*</span></label>
                                <select id="select2Basic" class="select2 form-select form-select-lg"
                                        data-allow-clear="true" name="classes_id" required>
                                    <option value=""></option>
                                    @foreach(\App\Models\Users::getClassesList() as $key => $item)
                                        <option
                                            value="{{$item->id}}">{{$item->name}}</option>
                                    @endforeach
                                </select>
                                @error('classes_id') <span class="text-danger">{{ $message }}</span> @enderror
=======
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
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input
                                    type="email"
                                    wire:model="email"
<<<<<<< HEAD
                                    class="form-control @error('email') is-invalid @enderror">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            {{-- Phone --}}
                            <div class="mb-3">
                                <label class="form-label">Telefon</label>
                                <input
                                    type="text"
                                    wire:model="phone"
                                    class="form-control @error('phone') is-invalid @enderror"
                                    placeholder="+998 90 123 45 67">
                                @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
=======
                                    class="form-control @error('email') is-invalid @enderror"
                                    placeholder="example@mail.com">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>

                            <div class="row">
                                {{-- Password --}}
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Parol
                                        @if(!$isEdit) <span class="text-danger">*</span> @endif
                                        @if($isEdit)
                                            <small class="text-muted">(Bo'sh qoldiring agar o'zgartirmoqchi bo'lmasangiz)</small>
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
                                        @if(!$isEdit) <span class="text-danger">*</span> @endif
                                    </label>
                                    <input
                                        type="password"
                                        wire:model="confirm_password"
                                        class="form-control @error('confirm_password') is-invalid @enderror">
                                    @error('confirm_password') <span class="text-danger">{{ $message }}</span> @enderror
                                </div>
                            </div>

                            <div class="row">
                                {{-- User Type --}}
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
                            <div class="mb-3">
                                @if($user_type === \App\Models\Users::TYPE_TEACHER)
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
                                @else
                                    <small class="text-muted">O'qituvchi lavozimini tanlasangiz, fan tanlash maydoni paydo bo'ladi</small>
                                @endif
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                            </div>

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
<<<<<<< HEAD
    @if($showViewModal && $viewingStudent)
=======
    @if($showViewModal && $viewingUser)
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
<<<<<<< HEAD
                            <i class="fas fa-user"></i> Student Ma'lumotlari
=======
                            <i class="fas fa-user"></i> Foydalanuvchi Ma'lumotlari
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
<<<<<<< HEAD
                            {{-- Chap tomon --}}
=======
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Shaxsiy Ma'lumotlar</h6>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">ID</small>
<<<<<<< HEAD
                                            <strong>{{ $viewingStudent->id }}</strong>
=======
                                            <strong>{{ $viewingUser->id }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Login</small>
                                            <strong>{{ $viewingUser->name }}</strong>
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">To'liq Ism</small>
<<<<<<< HEAD
                                            <strong>{{ $viewingStudent->name }}</strong>
=======
                                            <strong>{{ $viewingUser->first_name . ' ' . $viewingUser->last_name }}</strong>
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Email</small>
<<<<<<< HEAD
                                            <strong>{{ $viewingStudent->email }}</strong>
=======
                                            <strong>{{ $viewingUser->email }}</strong>
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Telefon</small>
<<<<<<< HEAD
                                            <strong>{{ $viewingStudent->phone ?? 'Kiritilmagan' }}</strong>
=======
                                            <strong>{{ $viewingUser->phone ?? 'Kiritilmagan' }}</strong>
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                                        </div>
                                    </div>
                                </div>
                            </div>

<<<<<<< HEAD
                            {{-- O'ng tomon --}}
=======
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Tizim Ma'lumotlari</h6>

                                        <div class="mb-3">
<<<<<<< HEAD
                                            <small class="text-muted d-block">Foydalanuvchi Turi</small>
                                            <span class="badge bg-primary">
                                            {{ ucfirst($viewingStudent->user_type) }}
                                        </span>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Ro'yxatdan o'tgan sana</small>
                                            <strong>{{ $viewingStudent->created_at->format('d.m.Y H:i') }}</strong>
=======
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
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Oxirgi yangilanish</small>
<<<<<<< HEAD
                                            <strong>{{ $viewingStudent->updated_at->format('d.m.Y H:i') }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Holat</small>
                                            <span class="badge bg-success">Faol</span>
=======
                                            <strong>{{ $viewingUser->updated_at->format('d.m.Y H:i') }}</strong>
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
<<<<<<< HEAD

                        {{-- Qo'shimcha ma'lumotlar (agar kerak bo'lsa) --}}
                        @if($viewingStudent->studentProfile)
                            <div class="card border-0 shadow-sm">
                                <div class="card-body">
                                    <h6 class="text-muted mb-3">O'quv Ma'lumotlari</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Guruh</small>
                                            <strong>{{ $viewingStudent->studentProfile->group ?? 'N/A' }}</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Kurs</small>
                                            <strong>{{ $viewingStudent->studentProfile->course ?? 'N/A' }}</strong>
                                        </div>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Talim shakli</small>
                                            <strong>{{ $viewingStudent->studentProfile->education_type ?? 'N/A' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endif
=======
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeViewModal">
                            Yopish
                        </button>
                        <button type="button" class="btn btn-warning"
<<<<<<< HEAD
                                wire:click="editStudent({{ $viewingStudent->id }})">
                            <i class="fas fa-edit"></i> Tahrirlash
                        </button>
                    </div>
                    </modal-content>
=======
                                wire:click="editUser({{ $viewingUser->id }})">
                            <i class="fas fa-edit"></i> Tahrirlash
                        </button>
                    </div>
>>>>>>> 3652e55304d62efca498d1808ae0ba9ebd4232c6
                </div>
            </div>
        </div>
    @endif
</div>
