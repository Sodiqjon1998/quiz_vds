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
            <h4 class="mb-0">Students Ro'yxati</h4>
            <button wire:click="createStudent" class="btn btn-primary">
                <i class="ri-add-line"></i> Yangi Student
            </button>
        </div>

        <div class="card-body">
            {{-- Search Box --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <input
                        type="text"
                        wire:model.live="search"
                        class="form-control"
                        placeholder="Ism, familya yoki email bo'yicha qidirish...">
                </div>
            </div>

            {{-- Students Table --}}
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered text-center">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Username</th>
                        <th>Ism</th>
                        <th>Familya</th>
                        <th>Sinfi</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Amallar</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($students as $student)
                        <tr>
                            <td>{{ $student->id }}</td>
                            <td><span class="badge bg-label-primary">{{ $student->name }}</span></td>
                            <td>{{ $student->first_name }}</td>
                            <td>{{ $student->last_name }}</td>
                            <td>
                                <span class="badge bg-label-info">
                                    {{ $student->classRelation->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td>{{ $student->email }}</td>
                            <td>{{ $student->phone ?? 'N/A' }}</td>
                            <td>
                                <button
                                    wire:click="viewStudent({{ $student->id }})"
                                    class="btn btn-sm btn-info text-white"
                                    title="Ko'rish">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button
                                    wire:click="editStudent({{ $student->id }})"
                                    class="btn btn-sm btn-warning"
                                    title="Tahrirlash">
                                    <i class="ri-pencil-line"></i>
                                </button>
                                <button
                                    wire:click="deleteStudent({{ $student->id }})"
                                    onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')"
                                    class="btn btn-sm btn-danger"
                                    title="O'chirish">
                                    <i class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <i class="ri-inbox-line" style="font-size: 48px; opacity: 0.3;"></i>
                                <p class="text-muted mt-2">Ma'lumot topilmadi</p>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-3">
                {{ $students->links() }}
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
                            {{ $isEdit ? 'Student ma\'lumotlarini tahrirlash' : 'Yangi Student qo\'shish' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="saveStudent" autocomplete="off">
                        <div class="modal-body">
                            <div class="row">
                                {{-- First Name --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Ism <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            wire:model.live="first_name"
                                            autocomplete="off"
                                            class="form-control @error('first_name') is-invalid @enderror">
                                        @error('first_name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Last Name --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Familya <span class="text-danger">*</span></label>
                                        <input
                                            type="text"
                                            wire:model.live="last_name"
                                            autocomplete="off"
                                            class="form-control @error('last_name') is-invalid @enderror">
                                        @error('last_name') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Username (avtomatik) --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Username <span class="text-danger">*</span></label>
                                        <div class="input-group">
                                            <input
                                                type="text"
                                                wire:model="name"
                                                autocomplete="off"
                                                @if(!$isEdit) readonly @endif
                                                class="form-control @error('name') is-invalid @enderror"
                                                style="@if(!$isEdit) background-color: #f8f9fa; @endif">
                                            @if(!$isEdit)
                                                <span class="input-group-text bg-primary text-white">
                                                    <i class="ri-lock-line"></i>
                                                </span>
                                            @endif
                                        </div>
                                        @error('name') <span class="text-danger">{{ $message }}</span> @enderror
                                        @if(!$isEdit)
                                            <small class="form-text text-muted">
                                                <i class="ri-information-line"></i> Username avtomatik yaratiladi
                                            </small>
                                        @endif
                                    </div>
                                </div>

                                {{-- Class --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Sinfi <span class="text-danger">*</span></label>
                                        <select
                                            wire:model="classes_id"
                                            class="form-select @error('classes_id') is-invalid @enderror">
                                            <option value="">Tanlang</option>
                                            @foreach(\App\Models\Classes::all() as $class)
                                                <option value="{{ $class->id }}">{{ $class->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('classes_id') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Email --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email <span class="text-danger">*</span></label>
                                        <input
                                            type="email"
                                            wire:model="email"
                                            autocomplete="off"
                                            class="form-control @error('email') is-invalid @enderror">
                                        @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                {{-- Phone --}}
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Telefon</label>
                                        <input
                                            type="text"
                                            wire:model="phone"
                                            autocomplete="off"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            placeholder="+998 90 123 45 67">
                                        @error('phone') <span class="text-danger">{{ $message }}</span> @enderror
                                    </div>
                                </div>

                                @if(!$isEdit)
                                    <div class="col-12">
                                        <div class="alert alert-info">
                                            <i class="ri-information-line"></i>
                                            <strong>Diqqat:</strong> Yangi student uchun parol avtomatik <strong>12345678</strong> bo'ladi.
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer">
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
    @endif

    {{-- View Modal --}}
    @if($showViewModal && $viewingStudent)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="ri-user-line"></i> Student Ma'lumotlari
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Shaxsiy Ma'lumotlar</h6>
                                        <p><strong>ID:</strong> {{ $viewingStudent->id }}</p>
                                        <p><strong>Username:</strong> <span class="badge bg-primary">{{ $viewingStudent->name }}</span></p>
                                        <p><strong>Ism:</strong> {{ $viewingStudent->first_name }}</p>
                                        <p><strong>Familya:</strong> {{ $viewingStudent->last_name }}</p>
                                        <p><strong>Email:</strong> {{ $viewingStudent->email }}</p>
                                        <p><strong>Telefon:</strong> {{ $viewingStudent->phone ?? 'Kiritilmagan' }}</p>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">O'quv Ma'lumotlari</h6>
                                        <p><strong>Sinfi:</strong> <span class="badge bg-info">{{ $viewingStudent->classRelation->name ?? 'N/A' }}</span></p>
                                        <p><strong>Status:</strong> <span class="badge bg-success">Faol</span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeViewModal">
                            Yopish
                        </button>
                        <button type="button" class="btn btn-warning" wire:click="editStudent({{ $viewingStudent->id }})">
                            <i class="ri-pencil-line"></i> Tahrirlash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>