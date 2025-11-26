<div>
    <style>
        :root {
            --yuksalish-orange: #F58025;
            --yuksalish-dark: #212529;
        }

        /* Asosiy knopkalar */
        .btn-yuksalish {
            background-color: var(--yuksalish-orange);
            color: white;
            border: none;
            font-weight: 500;
            padding: 0.6rem 1.2rem;
            border-radius: 8px;
        }

        .btn-yuksalish:hover {
            background-color: #d96d1b;
            color: white;
        }

        /* YANGI: Chiroyli Qidiruv Inputi (Yaxlit) */
        .search-box {
            border: 1px solid #dee2e6;
            border-radius: 8px;
            background-color: white;
            display: flex;
            align-items: center;
            padding: 0 10px;
            transition: all 0.2s ease;
        }

        /* Input ichiga focus bo'lganda ramka rangi o'zgarishi */
        .search-box:focus-within {
            border-color: var(--yuksalish-orange);
            box-shadow: 0 0 0 3px rgba(245, 128, 37, 0.15);
        }

        /* Ikonka */
        .search-box i {
            color: #adb5bd;
            font-size: 1.1rem;
        }

        /* Inputning o'zi (chegarasiz) */
        .search-box input {
            border: none;
            box-shadow: none !important;
            /* Standart ko'k ramkani o'chirish */
            padding: 0.6rem 0.5rem;
            width: 100%;
            outline: none;
            background: transparent;
        }

        /* Sarlavha stili */
        .page-title {
            font-weight: 700;
            color: var(--yuksalish-dark);
            margin: 0;
            white-space: nowrap;
            /* So'z pastga tushib ketmasligi uchun */
        }

        /* Mobil kartochkalar */
        .mobile-card {
            border-left: 5px solid var(--yuksalish-orange);
            background: white;
            margin-bottom: 1rem;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.03);
            border-radius: 12px;
        }
    </style>

    <div class="container-fluid py-4">
        <div class="row">
            <div class="col-12">
                <div class="card border-0 shadow-sm" style="border-radius: 12px;">

                    <div class="card-header bg-white py-4 border-bottom-0">
                        <div class="row align-items-center g-3">

                            <div class="col-12 col-md-4">
                                <h4 class="page-title d-flex align-items-center">
                                    <i class="ri-book-open-line me-2" style="color: var(--yuksalish-orange);"></i>
                                    Fanlar ro'yxati
                                </h4>
                            </div>

                            <div class="col-12 col-md-4">
                                <div class="search-box">
                                    <i class="ri-search-line"></i>
                                    <input wire:model.live.debounce.300ms="search"
                                        type="text"
                                        placeholder="Qidirish...">
                                </div>
                            </div>

                            <div class="col-12 col-md-4 text-md-end">
                                <button wire:click="createSubject" class="btn btn-yuksalish w-100 w-md-auto">
                                    <i class="ri-add-line me-1"></i> Yangi Fan
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="card-body px-0 px-md-3">
                        @if (session()->has('message'))
                        <div class="px-3">
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        </div>
                        @endif

                        <div class="table-responsive d-none d-md-block">
                            <table class="table table-hover align-middle table-yuksalish">
                                <thead>
                                    <tr>
                                        <th class="rounded-start text-center" style="width: 60px;">ID</th>
                                        <th>Fan nomi</th>
                                        <th>Status</th>
                                        <th>Yaratildi</th>
                                        <th class="text-end rounded-end">Amallar</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($subjects as $subject)
                                    <tr>
                                        <td class="text-center fw-bold text-muted">{{ $subject->id }}</td>
                                        <td>
                                            <span class="fw-bold fs-6">{{ $subject->name }}</span>
                                        </td>
                                        <td>
                                            @if($subject->status == 1)
                                            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Faol</span>
                                            @else
                                            <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Nofaol</span>
                                            @endif
                                        </td>
                                        <td class="text-muted small">
                                            {{ $subject->created_at }}
                                        </td>
                                        <td class="text-end">
                                            <button wire:click="editSubject({{ $subject->id }})" class="btn btn-sm btn-light text-warning" title="Tahrirlash">
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button wire:click="deleteSubject({{ $subject->id }})" onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')" class="btn btn-sm btn-light text-danger" title="O'chirish">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="5" class="text-center py-5 text-muted">
                                            <i class="ri-book-line display-4 opacity-25"></i>
                                            <p class="mt-2">Fanlar topilmadi</p>
                                        </td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>

                        <div class="d-md-none">
                            @forelse($subjects as $subject)
                            <div class="mobile-card p-3">
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <h5 class="fw-bold mb-0 text-dark">{{ $subject->name }}</h5>
                                    @if($subject->status == 1)
                                    <span class="badge bg-success bg-opacity-10 text-success">Faol</span>
                                    @else
                                    <span class="badge bg-danger bg-opacity-10 text-danger">Nofaol</span>
                                    @endif
                                </div>

                                <div class="small text-muted mb-3">
                                    <i class="ri-calendar-line me-1"></i> Yaratildi: {{ $subject->created_at }}
                                </div>

                                <div class="d-flex gap-2 border-top pt-2">
                                    <button wire:click="editSubject({{ $subject->id }})" class="btn btn-sm btn-light flex-fill text-warning">
                                        <i class="ri-pencil-line"></i> Tahrirlash
                                    </button>
                                    <button wire:click="deleteSubject({{ $subject->id }})" onclick="return confirm('O\'chirilsinmi?')" class="btn btn-sm btn-light flex-fill text-danger">
                                        <i class="ri-delete-bin-line"></i> O'chirish
                                    </button>
                                </div>
                            </div>
                            @empty
                            <div class="text-center py-5 text-muted">Fanlar topilmadi</div>
                            @endforelse
                        </div>

                        <div class="mt-4">
                            {{ $subjects->links() }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($showModal)
    <div class="modal fade show" style="display: block; background: rgba(0,0,0,0.5); backdrop-filter: blur(2px);" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg">
                <div class="modal-header bg-white border-bottom-0 pb-0">
                    <h5 class="modal-title fw-bold text-dark">
                        @if($isEdit)
                        <i class="ri-pencil-line text-warning me-2"></i>Fanni tahrirlash
                        @else
                        <i class="ri-add-circle-line" style="color: var(--yuksalish-orange);"></i> Yangi Fan
                        @endif
                    </h5>
                    <button type="button" class="btn-close" wire:click="closeModal"></button>
                </div>

                <form wire:submit.prevent="saveSubject" autocomplete="off">
                    <div class="modal-body">
                        <div class="row g-3">
                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Fan nomi <span class="text-danger">*</span></label>
                                <input type="text" wire:model.live="name" class="form-control search-input @error('name') is-invalid @enderror" placeholder="Masalan: Matematika">
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <div class="col-12">
                                <label class="form-label small fw-bold text-muted">Status</label>
                                <select wire:model="status" class="form-select search-input">
                                    <option value="1">Faol</option>
                                    <option value="0">Nofaol</option>
                                </select>
                            </div>
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
</div>