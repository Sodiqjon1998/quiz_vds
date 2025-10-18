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
                <i class="fas fa-plus"></i> Yangi Student
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
                        placeholder="Ism yoki email bo'yicha qidirish...">
                </div>
            </div>

            {{-- Students Table --}}
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered table-sm text-center">
                    <thead>
                    <tr>
                        <th>ID</th>
                        <th>Ism va Familya</th>
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Sana</th>
                        <th>Amallar</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($students as $student)
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
                                    class="btn btn-sm btn-info text-white">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button
                                    wire:click="editStudent({{ $student->id }})"
                                    class="btn btn-sm btn-warning">
                                    <i style="font-size: 16px" class="ri-pencil-line"></i>
                                </button>
                                <button
                                    wire:click="deleteStudent({{ $student->id }})"
                                    onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')"
                                    class="btn btn-sm btn-danger">
                                    <i style="font-size: 16px" class="ri-delete-bin-line"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">Ma'lumot topilmadi</td>
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

    {{-- Modal --}}
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">
                            {{ $isEdit ? 'Student ma\'lumotlarini tahrirlash' : 'Yangi Student qo\'shish' }}
                        </h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <div class="modal-body">
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
                            </div>

                            {{-- Email --}}
                            <div class="mb-3">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input
                                    type="email"
                                    wire:model="email"
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
    @if($showViewModal && $viewingStudent)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-user"></i> Student Ma'lumotlari
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeViewModal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            {{-- Chap tomon --}}
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Shaxsiy Ma'lumotlar</h6>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">ID</small>
                                            <strong>{{ $viewingStudent->id }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">To'liq Ism</small>
                                            <strong>{{ $viewingStudent->name }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Email</small>
                                            <strong>{{ $viewingStudent->email }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Telefon</small>
                                            <strong>{{ $viewingStudent->phone ?? 'Kiritilmagan' }}</strong>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- O'ng tomon --}}
                            <div class="col-md-6">
                                <div class="card border-0 shadow-sm mb-3">
                                    <div class="card-body">
                                        <h6 class="text-muted mb-3">Tizim Ma'lumotlari</h6>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Foydalanuvchi Turi</small>
                                            <span class="badge bg-primary">
                                            {{ ucfirst($viewingStudent->user_type) }}
                                        </span>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Ro'yxatdan o'tgan sana</small>
                                            <strong>{{ $viewingStudent->created_at->format('d.m.Y H:i') }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Oxirgi yangilanish</small>
                                            <strong>{{ $viewingStudent->updated_at->format('d.m.Y H:i') }}</strong>
                                        </div>

                                        <div class="mb-3">
                                            <small class="text-muted d-block">Holat</small>
                                            <span class="badge bg-success">Faol</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" wire:click="closeViewModal">
                            Yopish
                        </button>
                        <button type="button" class="btn btn-warning"
                                wire:click="editStudent({{ $viewingStudent->id }})">
                            <i class="fas fa-edit"></i> Tahrirlash
                        </button>
                    </div>
                    </modal-content>
                </div>
            </div>
        </div>
    @endif
</div>
