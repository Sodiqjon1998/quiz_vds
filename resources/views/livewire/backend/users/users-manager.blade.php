<div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Hodimlar ro'yxati</h3>
                        <div class="card-tools">
                            <button wire:click="createUsers" class="btn btn-primary btn-sm">
                                <i class="fas fa-plus"></i> Yangi hodim qo'shish
                            </button>
                        </div>
                    </div>

                    <div class="card-body">
                        <!-- Flash Message -->
                        @if (session()->has('message'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                            </div>
                        @endif

                        <!-- Search -->
                        <div class="row mb-3">
                            <div class="col-md-7">
                                <input wire:model.live="search" type="text" class="form-control"
                                       placeholder="Qidirish (ism yoki email)..." autocomplete="off">
                            </div>
                        </div>

                        <!-- Table -->
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped">
                                <thead>
                                <tr>
                                    <th style="width: 10px">#</th>
                                    <th>Username</th>
                                    <th>Ism</th>
                                    <th>Familya</th>
                                    <th>Email</th>
                                    <th>Telefon</th>
                                    <th>Fan</th>
                                    <th>Hodim lavozimi</th> <!-- QO'SHILDI -->
                                    <th>Sinflari</th>
                                    <th>Status</th>
                                    <th style="width: 200px">Amallar</th>
                                </tr>
                                </thead>
                                <tbody style="font-size: 14px">
                                @forelse($users as $user)
                                    <tr>
                                        <td>{{ $loop->iteration + ($users->currentPage() - 1) * $users->perPage() }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->first_name }}</td>
                                        <td>{{ $user->last_name }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone ?? 'N/A' }}</td>
                                        <td>{{ $user->subject->name ?? 'N/A' }}</td>
                                        <td>
                                            @if($user->user_type == \App\Models\Users::TYPE_TEACHER)
                                                <span class="btn btn-sm btn-outline-info">O'qituvchi</span>
                                            @elseif($user->user_type == \App\Models\Users::TYPE_KOORDINATOR)
                                                <span class="btn btn-sm btn-outline-warning">Koordinator</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->user_type == \App\Models\Users::TYPE_TEACHER)
                                                {{ '---' }}
                                            @elseif($user->user_type == \App\Models\Users::TYPE_KOORDINATOR && $user->classes_id)
                                                @php
                                                    $classIds = json_decode($user->classes_id, true);
                                                    $classes = \App\Models\Classes::whereIn('id', $classIds)->pluck('name')->toArray();
                                                @endphp
                                                {{ implode(', ', $classes) }}
                                            @else
                                                N/A
                                            @endif
                                        </td>
                                        <td>
                                            @if($user->status == \App\Models\Users::STATUS_ACTIVE)
                                                <span class="btn btn-sm btn-outline-success">Faol</span>
                                            @else
                                                <span class="btn btn-sm btn-outline-danger">Nofaol</span>
                                            @endif
                                        </td>
                                        <td>
                                            <button wire:click="viewUsers({{ $user->id }})" class="btn btn-info btn-sm"
                                                    title="Ko'rish">
                                                <i class="ri-eye-line"></i>
                                            </button>
                                            <button wire:click="editUsers({{ $user->id }})"
                                                    class="btn btn-warning btn-sm" title="Tahrirlash">
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button wire:click="deleteUsers({{ $user->id }})"
                                                    onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')"
                                                    class="btn btn-danger btn-sm"
                                                    title="O'chirish">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="10" class="text-center">Ma'lumot topilmadi</td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-3">
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


                                {{--                                @if(!$isEdit)--}}
                                {{--                                    <div class="col-md-6">--}}
                                {{--                                        <div class="form-group">--}}
                                {{--                                            <label>Parol <span class="text-danger">*</span></label>--}}
                                {{--                                            <input type="password"--}}
                                {{--                                                   wire:model="password"--}}
                                {{--                                                   autocomplete="new-password"--}}
                                {{--                                                   class="form-control @error('password') is-invalid @enderror">--}}
                                {{--                                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror--}}
                                {{--                                        </div>--}}
                                {{--                                    </div>--}}
                                {{--                                @else--}}
                                {{--                                    <div class="col-md-6">--}}
                                {{--                                        <div class="form-group">--}}
                                {{--                                            <label>Yangi parol (bo'sh qoldiring, agar o'zgartirmoqchi--}}
                                {{--                                                bo'lmasangiz)</label>--}}
                                {{--                                            <input type="password"--}}
                                {{--                                                   wire:model="password"--}}
                                {{--                                                   autocomplete="new-password"--}}
                                {{--                                                   class="form-control @error('password') is-invalid @enderror">--}}
                                {{--                                            @error('password') <span class="text-danger">{{ $message }}</span> @enderror--}}
                                {{--                                        </div>--}}
                                {{--                                    </div>--}}
                                {{--                                @endif--}}
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
