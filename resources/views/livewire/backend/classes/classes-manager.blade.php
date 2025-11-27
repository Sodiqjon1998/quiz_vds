<div>
    <style>
        :root {
            --yuksalish-orange: #F58025;
            --yuksalish-dark: #212529;
            --yuksalish-gray: #f8f9fa;
        }

        /* Asosiy tugmalar */
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

        /* Search Box */
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

        /* Jadval Header */
        .table-yuksalish thead th {
            background-color: var(--yuksalish-dark);
            color: white;
            padding: 15px;
            font-weight: 500;
            border: none;
        }

        /* Mobile Card */
        .mobile-card {
            border-left: 5px solid var(--yuksalish-orange);
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
            margin-bottom: 15px;
            transition: transform 0.2s;
        }

        /* Badges */
        .badge-count {
            background-color: #fff3cd;
            color: #856404;
            font-size: 0.85rem;
            padding: 5px 12px;
            border-radius: 20px;
            font-weight: bold;
        }

        /* Pagination Button */
        .btn-page {
            border: 1px solid #dee2e6;
            background: white;
            color: var(--yuksalish-dark);
        }

        .btn-page:hover {
            border-color: var(--yuksalish-orange);
            color: var(--yuksalish-orange);
        }

        .btn-page:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* Modal Header */
        .modal-header {
            border-bottom: none;
        }

        .modal-footer {
            border-top: none;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 15px; overflow: hidden;">

                    <!-- HEADER -->
                    <div class="card-header bg-white py-4">
                        <div class="row align-items-center g-3">
                            <!-- Sarlavha -->
                            <div class="col-12 col-md-auto">
                                <div class="d-flex align-items-center">
                                    <div class="bg-light rounded-circle p-2 me-2 d-flex justify-content-center align-items-center" style="width: 40px; height: 40px;">
                                        <i class="ri-community-line" style="color: var(--yuksalish-orange); font-size: 1.2rem;"></i>
                                    </div>
                                    <h4 class="page-title">Sinflar ro'yxati</h4>
                                </div>
                            </div>

                            <!-- Qidiruv -->
                            <div class="col-12 col-md">
                                <div class="search-box">
                                    <i class="ri-search-line"></i>
                                    <input wire:model.live.debounce.300ms="search"
                                        type="text"
                                        class="form-control border-0 shadow-none bg-transparent p-0"
                                        placeholder="Sinf nomini qidiring (masalan: 10-A)...">
                                </div>
                            </div>

                            <!-- Tugma -->
                            <div class="col-12 col-md-auto">
                                <button wire:click="createClass" class="btn btn-yuksalish w-100">
                                    <i class="ri-add-circle-line me-2"></i> Yangi Sinf
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body p-0">
                        <!-- Xabar -->
                        @if (session()->has('message'))
                        <div class="p-3">
                            <div class="alert alert-success alert-dismissible fade show shadow-sm border-0" role="alert" style="background-color: #d1e7dd; color: #0f5132;">
                                <i class="ri-checkbox-circle-line me-2"></i> {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                        @endif

                        <!-- DESKTOP TABLE -->
                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle table-yuksalish mb-0">
                                <thead>
                                    <tr>
                                        <th class="text-center" style="width: 60px;">ID</th>
                                        <th>Sinf nomi</th>
                                        <th class="text-center">O'quvchilar</th>
                                        <th>Chat ID</th>
                                        <th>Topic ID</th>
                                        <th class="text-center">Status</th>
                                        <th class="text-end pe-4">Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($classes as $class)
                                    <tr>
                                        <td class="text-center fw-bold text-secondary">{{ $class->id }}</td>
                                        <td>
                                            <span class="fw-bold text-dark fs-5">{{ $class->name }}</span>
                                        </td>
                                        <td class="text-center">
                                            <span class="badge-count">
                                                <i class="ri-user-line me-1"></i> {{ $class->students_count }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($class->telegram_chat_id)
                                            <span class="badge bg-info bg-opacity-10 text-info border border-info px-2 py-1">
                                                <i class="ri-telegram-fill me-1"></i> {{ $class->telegram_chat_id }}
                                            </span>
                                            @else
                                            <span class="text-muted small opacity-50">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($class->telegram_topic_id)
                                            <span class="badge bg-secondary bg-opacity-10 text-secondary border border-secondary px-2 py-1">
                                                <i class="ri-message-3-line me-1"></i> {{ $class->telegram_topic_id }}
                                            </span>
                                            @else
                                            <span class="text-muted small opacity-50">-</span>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            @if($class->status == 1)
                                            <span class="badge bg-success bg-opacity-10 text-success px-3 py-2 rounded-pill">Faol</span>
                                            @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2 rounded-pill">Nofaol</span>
                                            @endif
                                        </td>
                                        <td class="text-end pe-4">
                                            <div class="btn-group">
                                                <button wire:click="viewClass({{ $class->id }})" class="btn btn-sm btn-light text-primary shadow-sm border me-1" title="Ko'rish">
                                                    <i class="ri-eye-line"></i>
                                                </button>
                                                <button wire:click="editClass({{ $class->id }})" class="btn btn-sm btn-light text-warning shadow-sm border me-1" title="Tahrirlash">
                                                    <i class="ri-pencil-line"></i>
                                                </button>
                                                <button wire:click="deleteClass({{ $class->id }})" onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')" class="btn btn-sm btn-light text-danger shadow-sm border" title="O'chirish">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-5">
                                            <div class="text-muted">
                                                <i class="ri-inbox-line display-4 d-block mb-3 opacity-25"></i>
                                                Sinflar topilmadi
                                            </div>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <!-- MOBILE CARDS -->
                        <div class="d-md-none p-3 bg-light">
                            @forelse($classes as $class)
                            <div class="mobile-card p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar bg-warning bg-opacity-10 text-warning rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                            <i class="ri-graduation-cap-fill fs-4"></i>
                                        </div>
                                        <div>
                                            <h5 class="fw-bold mb-0 text-dark">{{ $class->name }}</h5>
                                            <small class="text-muted">ID: #{{ $class->id }}</small>
                                        </div>
                                    </div>
                                    @if($class->status == 1)
                                    <i class="ri-checkbox-circle-fill text-success fs-3"></i>
                                    @else
                                    <i class="ri-close-circle-fill text-secondary fs-3"></i>
                                    @endif
                                </div>

                                <div class="bg-light rounded p-3 mb-3 border">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small"><i class="ri-group-line me-1"></i> O'quvchilar</span>
                                        <span class="fw-bold text-dark">{{ $class->students_count }} ta</span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <span class="text-muted small"><i class="ri-telegram-line me-1"></i> Chat ID</span>
                                        <span class="{{ $class->telegram_chat_id ? 'text-info fw-bold' : 'text-muted' }}">
                                            {{ $class->telegram_chat_id ?? 'Yo\'q' }}
                                        </span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small"><i class="ri-message-3-line me-1"></i> Topic ID</span>
                                        <span class="{{ $class->telegram_topic_id ? 'text-secondary fw-bold' : 'text-muted' }}">
                                            {{ $class->telegram_topic_id ?? 'Yo\'q' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 border-top pt-2">
                                    <button wire:click="viewClass({{ $class->id }})" class="btn btn-light border flex-fill text-primary fw-bold">
                                        <i class="ri-eye-line"></i> Ko'rish
                                    </button>
                                    <button wire:click="editClass({{ $class->id }})" class="btn btn-light border flex-fill text-warning fw-bold">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button wire:click="deleteClass({{ $class->id }})" onclick="return confirm('O\'chirilsinmi?')" class="btn btn-light border flex-fill text-danger fw-bold">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted">Sinflar topilmadi</div>
                            @endforelse
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 px-3 pb-3">
                            {{ $classes->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CREATE / EDIT MODAL -->
    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-white border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">
                        @if($isEdit) <i class="ri-pencil-line text-warning me-2"></i>Sinfni Tahrirlash
                        @else <i class="ri-add-circle-line" style="color: var(--yuksalish-orange);"></i> Yangi Sinf
                        @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>
                <form wire:submit.prevent="saveClass">
                    <div class="modal-body">
                        <div class="mb-4">
                            <label class="form-label fw-bold text-dark">Sinf nomi <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text bg-white text-muted"><i class="ri-booklet-line"></i></span>
                                <input type="text" wire:model.live="name" class="form-control border-start-0 @error('name') is-invalid @enderror" placeholder="Masalan: 10-A">
                            </div>
                            @error('name') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-12">
                                <label class="form-label fw-bold text-dark small text-uppercase text-muted mb-1">Telegram Sozlamalari</label>
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">Chat ID <span class="text-danger">*</span></label>
                                <input type="text" wire:model="telegram_chat_id" class="form-control @error('telegram_chat_id') is-invalid @enderror" placeholder="-100...">
                                @error('telegram_chat_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="small text-muted">Topic ID <span class="text-danger">*</span></label>
                                <input type="text" wire:model="telegram_topic_id" class="form-control @error('telegram_topic_id') is-invalid @enderror" placeholder="123">
                                @error('telegram_topic_id') <div class="text-danger small mt-1">{{ $message }}</div> @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold text-dark">Status</label>
                            <select wire:model="status" class="form-select">
                                <option value="1">Faol</option>
                                <option value="0">Nofaol</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" wire:click="closeModal" class="btn btn-light px-4 border">Bekor qilish</button>
                        <button type="submit" class="btn btn-yuksalish px-4 shadow-sm">
                            {{ $isEdit ? 'Yangilash' : 'Saqlash' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endif

    <!-- VIEW CLASS MODAL -->
    @if($showViewModal && $viewingClass)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); backdrop-filter: blur(3px);" tabindex="-1">
        <div class="modal-dialog modal-xl modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-body p-0">
                    <div class="row g-0 h-100">
                        <!-- Chap tomon: Sinf Info (Orange) -->
                        <div class="col-lg-4 text-white p-4 d-flex flex-column justify-content-center text-center position-relative"
                            style="background-color: var(--yuksalish-orange);">

                            <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3 d-lg-none" wire:click="closeViewModal"></button>

                            <div class="avatar bg-white text-warning rounded-circle mx-auto mb-4 d-flex align-items-center justify-content-center shadow" style="width: 100px; height: 100px;">
                                <i class="ri-graduation-cap-fill" style="font-size: 50px; color: var(--yuksalish-orange);"></i>
                            </div>

                            <h2 class="fw-bold mb-1">{{ $viewingClass->name }}</h2>
                            <p class="text-white-50 mb-4">Sinf haqida ma'lumot</p>

                            <div class="row g-3 text-start mt-2">
                                <div class="col-12 bg-white bg-opacity-10 p-3 rounded">
                                    <small class="text-black-50 d-block mb-1">O'quvchilar soni</small>
                                    <h4 class="mb-0 fw-bold">{{ $viewingClass->students_count ?? 0 }} ta</h4>
                                </div>
                                <div class="col-12 bg-white bg-opacity-10 p-3 rounded">
                                    <small class="text-black-50 d-block mb-1">Telegram Chat ID</small>
                                    <span class="badge bg-info">
                                        {{ $viewingClass->telegram_chat_id ?? 'Yo\'q' }}
                                    </span>
                                </div>
                                <div class="col-12 bg-white bg-opacity-10 p-3 rounded">
                                    <small class="text-black-50 d-block mb-1">Telegram Topic ID</small>
                                    <span class="badge bg-secondary">
                                        {{ $viewingClass->telegram_topic_id ?? 'Yo\'q' }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <!-- O'ng tomon: O'quvchilar Ro'yxati -->
                        <div class="col-lg-8 bg-white">
                            <div class="p-4 h-100 d-flex flex-column">
                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <h5 class="fw-bold text-dark mb-0"><i class="ri-group-line me-2 text-warning"></i>O'quvchilar Ro'yxati</h5>
                                    <button type="button" class="btn-close d-none d-lg-block" wire:click="closeViewModal"></button>
                                </div>

                                <div class="flex-grow-1 overflow-auto">
                                    @if($this->students->count() > 0)
                                    <div class="table-responsive">
                                        <table class="table table-hover align-middle mb-0">
                                            <thead class="bg-light sticky-top">
                                                <tr>
                                                    <th class="ps-3" width="50">#</th>
                                                    <th>F.I.O</th>
                                                    <th>Telefon</th>
                                                    <th>Status</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach($this->students as $index => $student)
                                                <tr>
                                                    <td class="ps-3 fw-bold text-secondary">{{ ($studentsPage - 1) * $studentsPerPage + $index + 1 }}</td>
                                                    <td>
                                                        <div class="d-flex align-items-center">
                                                            <div class="avatar bg-primary-subtle text-primary rounded-circle me-3 d-flex justify-content-center align-items-center" style="width: 35px; height: 35px; font-size: 14px;">
                                                                {{ mb_substr($student->first_name, 0, 1) }}
                                                            </div>
                                                            <div>
                                                                <div class="fw-bold text-dark">{{ $student->last_name }} {{ $student->first_name }}</div>
                                                                <small class="text-muted">{{ $student->email }}</small>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td>{{ $student->phone ?? '-' }}</td>
                                                    <td>
                                                        @if($student->status == 1) <span class="badge bg-success-subtle text-success rounded-pill">Faol</span>
                                                        @else <span class="badge bg-danger-subtle text-danger rounded-pill">Nofaol</span> @endif
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                    @else
                                    <div class="text-center py-5 my-auto">
                                        <div class="bg-light rounded-circle d-inline-flex p-4 mb-3">
                                            <i class="ri-user-unfollow-line text-muted" style="font-size: 40px;"></i>
                                        </div>
                                        <h6 class="text-muted">Bu sinfda o'quvchilar yo'q</h6>
                                    </div>
                                    @endif
                                </div>

                                <!-- Footer Pagination -->
                                @if($this->students->count() > 0)
                                <div class="pt-3 border-top mt-auto d-flex justify-content-between align-items-center">
                                    <small class="text-muted">Sahifa: {{ $studentsPage }} / {{ $this->students->lastPage() }}</small>
                                    <div>
                                        <button wire:click="previousStudentsPage" class="btn btn-sm btn-page me-1" @if($studentsPage <=1) disabled @endif>
                                            <i class="ri-arrow-left-s-line"></i>
                                        </button>
                                        <button wire:click="nextStudentsPage" class="btn btn-sm btn-page" @if($studentsPage>= $this->students->lastPage()) disabled @endif>
                                            <i class="ri-arrow-right-s-line"></i>
                                        </button>
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>