{{--<div>--}}
{{--    --}}{{-- Success Message --}}
{{--    @if (session()->has('message'))--}}
{{--        <div class="alert alert-success alert-dismissible fade show" role="alert">--}}
{{--            <i class="ri-checkbox-circle-line me-2"></i>--}}
{{--            {{ session('message') }}--}}
{{--            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>--}}
{{--        </div>--}}
{{--    @endif--}}

{{--    --}}{{-- DEBUG (keyinchalik o'chirib tashlang) --}}
{{--    <div class="alert alert-warning">--}}
{{--        showModal = {{ $showModal ? 'TRUE' : 'FALSE' }}--}}
{{--    </div>--}}

{{--    --}}{{-- Header --}}
{{--    <div class="card">--}}
{{--        <div class="card-header d-flex justify-content-between align-items-center">--}}
{{--            <div>--}}
{{--                <h4 class="mb-0">--}}
{{--                    <i class="ri-file-list-3-line me-2"></i>--}}
{{--                    Quizlar Ro'yxati--}}
{{--                </h4>--}}
{{--                <p class="text-muted small mb-0">Barcha quizlarni boshqarish</p>--}}
{{--            </div>--}}

{{--            --}}{{-- MUHIM: wire:click to'g'ri yozilgan --}}
{{--            <button--}}
{{--                wire:click="createQuiz"--}}
{{--                class="btn btn-primary"--}}
{{--                type="button">--}}
{{--                <i class="ri-add-line me-1"></i> Yangi Quiz--}}
{{--            </button>--}}
{{--        </div>--}}

{{--        <div class="card-body">--}}
{{--            --}}{{-- Search --}}
{{--            <div class="row mb-4">--}}
{{--                <div class="col-md-6">--}}
{{--                    <div class="input-group">--}}
{{--                        <span class="input-group-text">--}}
{{--                            <i class="ri-search-line"></i>--}}
{{--                        </span>--}}
{{--                        <input--}}
{{--                            type="text"--}}
{{--                            wire:model.live="search"--}}
{{--                            class="form-control"--}}
{{--                            placeholder="Quiz nomini qidiring...">--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--                <div class="col-md-6 text-end">--}}
{{--                    <span class="text-muted">--}}
{{--                        <i class="ri-information-line"></i>--}}
{{--                        Jami: <strong>{{ $quizzes->total() }}</strong> ta quiz--}}
{{--                    </span>--}}
{{--                </div>--}}
{{--            </div>--}}

{{--            --}}{{-- Table --}}
{{--            <div class="table-responsive">--}}
{{--                <table class="table table-hover align-middle">--}}
{{--                    <thead class="table-light">--}}
{{--                        <tr>--}}
{{--                            <th>ID</th>--}}
{{--                            <th>Quiz nomi</th>--}}
{{--                            <th>Fan</th>--}}
{{--                            <th>Sinf</th>--}}
{{--                            <th>Savollar</th>--}}
{{--                            <th>Yaratuvchi</th>--}}
{{--                            <th>Holat</th>--}}
{{--                            <th>Sana</th>--}}
{{--                            <th class="text-end">Amallar</th>--}}
{{--                        </tr>--}}
{{--                    </thead>--}}
{{--                    <tbody>--}}
{{--                        @forelse($quizzes as $quiz)--}}
{{--                            <tr>--}}
{{--                                <td>{{ $quiz->id }}</td>--}}
{{--                                <td>--}}
{{--                                    <div class="d-flex align-items-center">--}}
{{--                                        <div class="avatar avatar-sm me-2">--}}
{{--                                            <div class="avatar-initial rounded-circle bg-label-primary">--}}
{{--                                                <i class="ri-file-list-3-line"></i>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                        <strong>{{ $quiz->name }}</strong>--}}
{{--                                    </div>--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    <span class="badge bg-label-info">--}}
{{--                                        {{ $quiz->subject->name ?? 'N/A' }}--}}
{{--                                    </span>--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    <span class="badge bg-label-success">--}}
{{--                                        {{ $quiz->class->name ?? 'N/A' }}--}}
{{--                                    </span>--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    <span class="badge bg-label-primary">--}}
{{--                                        <i class="ri-question-line me-1"></i>--}}
{{--                                        {{ $quiz->questions_count }} ta--}}
{{--                                    </span>--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    <small class="text-muted">--}}
{{--                                        {{ $quiz->creator->name ?? 'N/A' }}--}}
{{--                                    </small>--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    @if($quiz->status == \App\Models\Teacher\Quiz::STATUS_ACTIVE)--}}
{{--                                        <span class="badge bg-success">Faol</span>--}}
{{--                                    @else--}}
{{--                                        <span class="badge bg-secondary">Nofaol</span>--}}
{{--                                    @endif--}}
{{--                                </td>--}}
{{--                                <td>--}}
{{--                                    <small class="text-muted">--}}
{{--                                        {{ $quiz->created_at->format('d.m.Y') }}--}}
{{--                                    </small>--}}
{{--                                </td>--}}
{{--                                <td class="text-end">--}}

{{--                                    <button wire:click="viewQuiz({{ $quiz->id }})"--}}
{{--                                            class="btn btn-sm btn-info"--}}
{{--                                            title="Ko'rish">--}}
{{--                                        <i class="ri-eye-line"></i>--}}
{{--                                    </button>--}}
{{--                                    <button wire:click="editQuiz({{ $quiz->id }})"--}}
{{--                                            class="btn btn-sm btn-warning"--}}
{{--                                            title="Tahrirlash">--}}
{{--                                        <i class="ri-pencil-line"></i>--}}
{{--                                    </button>--}}
{{--                                    <button wire:click="deleteQuiz({{ $quiz->id }})"--}}
{{--                                            onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')"--}}
{{--                                            class="btn btn-sm btn-danger"--}}
{{--                                            title="O'chirish">--}}
{{--                                        <i class="ri-delete-bin-line"></i>--}}
{{--                                    </button>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @empty--}}
{{--                            <tr>--}}
{{--                                <td colspan="9" class="text-center py-5">--}}
{{--                                    <i class="ri-inbox-line" style="font-size: 64px; opacity: 0.3;"></i>--}}
{{--                                    <h5 class="text-muted mt-3">Hozircha quizlar mavjud emas</h5>--}}
{{--                                    <p class="text-muted">Yangi quiz qo'shish uchun yuqoridagi tugmani bosing</p>--}}
{{--                                </td>--}}
{{--                            </tr>--}}
{{--                        @endforelse--}}
{{--                    </tbody>--}}
{{--                </table>--}}
{{--            </div>--}}

{{--            --}}{{-- Pagination --}}
{{--            <div class="mt-4">--}}
{{--                {{ $quizzes->links() }}--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    --}}{{-- Create/Edit Modal --}}
{{--    @if($showModal)--}}
{{--        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5);">--}}
{{--            <div class="modal-dialog modal-dialog-centered modal-lg">--}}
{{--                <div class="modal-content">--}}
{{--                    <div class="modal-header bg-primary text-white">--}}
{{--                        <h5 class="modal-title">--}}
{{--                            <i class="ri-file-list-3-line me-2"></i>--}}
{{--                            {{ $isEdit ? 'Quiz tahrirlash' : 'Yangi Quiz qo\'shish' }}--}}
{{--                        </h5>--}}
{{--                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>--}}
{{--                    </div>--}}
{{--                    <form wire:submit.prevent="saveQuiz" autocomplete="off">--}}
{{--                        <div class="modal-body">--}}
{{--                            <div class="row">--}}
{{--                                --}}{{-- Quiz Name --}}
{{--                                <div class="col-12 mb-3">--}}
{{--                                    <label class="form-label">--}}
{{--                                        Quiz nomi <span class="text-danger">*</span>--}}
{{--                                    </label>--}}
{{--                                    <div class="input-group">--}}
{{--                                        <span class="input-group-text">--}}
{{--                                            <i class="ri-file-text-line"></i>--}}
{{--                                        </span>--}}
{{--                                        <input--}}
{{--                                            type="text"--}}
{{--                                            wire:model.live="name"--}}
{{--                                            autocomplete="off"--}}
{{--                                            class="form-control @error('name') is-invalid @enderror"--}}
{{--                                            placeholder="Masalan: Matematika Test 1">--}}
{{--                                    </div>--}}
{{--                                    @error('name')--}}
{{--                                        <div class="text-danger small mt-1">--}}
{{--                                            <i class="ri-error-warning-line me-1"></i>{{ $message }}--}}
{{--                                        </div>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}

{{--                                --}}{{-- Subject --}}
{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label class="form-label">--}}
{{--                                        Fan <span class="text-danger">*</span>--}}
{{--                                    </label>--}}
{{--                                    <select--}}
{{--                                        wire:model="subject_id"--}}
{{--                                        class="form-select @error('subject_id') is-invalid @enderror">--}}
{{--                                        <option value="">Tanlang</option>--}}
{{--                                        @foreach($subjects as $subject)--}}
{{--                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                    @error('subject_id')--}}
{{--                                        <div class="text-danger small mt-1">--}}
{{--                                            <i class="ri-error-warning-line me-1"></i>{{ $message }}--}}
{{--                                        </div>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}

{{--                                --}}{{-- Class --}}
{{--                                <div class="col-md-6 mb-3">--}}
{{--                                    <label class="form-label">--}}
{{--                                        Sinf <span class="text-danger">*</span>--}}
{{--                                    </label>--}}
{{--                                    <select--}}
{{--                                        wire:model="classes_id"--}}
{{--                                        class="form-select @error('classes_id') is-invalid @enderror">--}}
{{--                                        <option value="">Tanlang</option>--}}
{{--                                        @foreach($classes as $class)--}}
{{--                                            <option value="{{ $class->id }}">{{ $class->name }}</option>--}}
{{--                                        @endforeach--}}
{{--                                    </select>--}}
{{--                                    @error('classes_id')--}}
{{--                                        <div class="text-danger small mt-1">--}}
{{--                                            <i class="ri-error-warning-line me-1"></i>{{ $message }}--}}
{{--                                        </div>--}}
{{--                                    @enderror--}}
{{--                                </div>--}}

{{--                                --}}{{-- Info --}}
{{--                                @if(!$isEdit)--}}
{{--                                    <div class="col-12">--}}
{{--                                        <div class="alert alert-info mb-0">--}}
{{--                                            <div class="d-flex align-items-start">--}}
{{--                                                <i class="ri-lightbulb-line me-2 mt-1"></i>--}}
{{--                                                <div>--}}
{{--                                                    <strong>Eslatma:</strong> Quiz yaratilgandan so'ng unga savollar qo'shishingiz mumkin.--}}
{{--                                                </div>--}}
{{--                                            </div>--}}
{{--                                        </div>--}}
{{--                                    </div>--}}
{{--                                @endif--}}
{{--                            </div>--}}
{{--                        </div>--}}
{{--                        <div class="modal-footer">--}}
{{--                            <button type="button" wire:click="closeModal" class="btn btn-secondary">--}}
{{--                                <i class="ri-close-line me-1"></i> Bekor qilish--}}
{{--                            </button>--}}
{{--                            <button type="submit" class="btn btn-primary">--}}
{{--                                <i class="ri-save-line me-1"></i>--}}
{{--                                {{ $isEdit ? 'Yangilash' : 'Saqlash' }}--}}
{{--                            </button>--}}
{{--                        </div>--}}
{{--                    </form>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    @endif--}}

{{--    --}}{{-- View Modal xuddi avvalgidek... --}}
{{--</div>--}}

<div>
    {{-- Success Message --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
            <i class="ri-checkbox-circle-line me-2 fs-5"></i>
            <strong>Barakalla!</strong> {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-gradient-primary text-white py-3">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h4 class="mb-1 fw-bold">
                        <i class="ri-file-list-3-line me-2"></i>
                        Quizlar Ro'yxati
                    </h4>
                    <p class="text-white-50 small mb-0">
                        <i class="ri-information-line me-1"></i>
                        Barcha quizlarni yaratish va boshqarish
                    </p>
                </div>
                <button wire:click="createQuiz" class="btn btn-light btn-lg shadow-sm" type="button">
                    <i class="ri-add-circle-line me-2"></i> Yangi Quiz
                </button>
            </div>
        </div>

        <div class="card-body p-4">
            {{-- Search --}}
            <div class="row mb-4">
                <div class="col-md-8">
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0">
                            <i class="ri-search-line text-primary"></i>
                        </span>
                        <input type="text" wire:model.live="search"
                               class="form-control border-start-0 ps-0"
                               placeholder="Quiz nomini qidiring...">
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="badge bg-primary-subtle text-primary fs-6 p-3">
                        <i class="ri-database-2-line me-2"></i>
                        Jami: <strong>{{ $quizzes->total() }}</strong> ta quiz
                    </div>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th class="text-center" style="width: 5%">ID</th>
                        <th style="width: 25%">Quiz nomi</th>
                        <th class="text-center" style="width: 12%">Fan</th>
                        <th class="text-center" style="width: 12%">Sinf</th>
                        <th class="text-center" style="width: 15%">Savollar</th>
                        <th style="width: 12%">Yaratuvchi</th>
                        <th class="text-center" style="width: 8%">Holat</th>
                        <th class="text-center" style="width: 11%">Amallar</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($quizzes as $quiz)
                        <tr>
                            <td class="text-center fw-bold text-muted">#{{ $quiz->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm bg-primary-subtle rounded-circle me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                                        <i class="ri-file-list-3-line text-primary fs-5"></i>
                                    </div>
                                    <div>
                                        <strong class="d-block">{{ $quiz->name }}</strong>
                                        <small class="text-muted">{{ $quiz->created_at->format('d.m.Y H:i') }}</small>
                                    </div>
                                </div>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info-subtle text-info px-3 py-2">
                                    <i class="ri-book-line me-1"></i>
                                    {{ $quiz->subject->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success-subtle text-success px-3 py-2">
                                    <i class="ri-graduation-cap-line me-1"></i>
                                    {{ $quiz->class->name ?? 'N/A' }}
                                </span>
                            </td>
                            <td class="text-center">
                                <button wire:click="manageQuestions({{ $quiz->id }})"
                                        class="btn btn-sm btn-primary shadow-sm">
                                    <i class="ri-question-line me-1"></i>
                                    {{ $quiz->questions_count }} ta
                                </button>
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-xs bg-secondary-subtle rounded-circle me-2">
                                        <i class="ri-user-line text-secondary"></i>
                                    </div>
                                    <small>{{ $quiz->creator->name ?? 'N/A' }}</small>
                                </div>
                            </td>
                            <td class="text-center">
                                @if($quiz->status == \App\Models\Teacher\Quiz::STATUS_ACTIVE)
                                    <span class="badge bg-success px-3 py-2">
                                        <i class="ri-checkbox-circle-line me-1"></i>Faol
                                    </span>
                                @else
                                    <span class="badge bg-secondary px-3 py-2">
                                        <i class="ri-close-circle-line me-1"></i>Nofaol
                                    </span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group" role="group">
                                    <button wire:click="viewQuiz({{ $quiz->id }})"
                                            class="btn btn-sm btn-info"
                                            title="Ko'rish">
                                        <i class="ri-eye-line"></i>
                                    </button>
                                    <button wire:click="editQuiz({{ $quiz->id }})"
                                            class="btn btn-sm btn-warning"
                                            title="Tahrirlash">
                                        <i class="ri-pencil-line"></i>
                                    </button>
                                    <button wire:click="deleteQuiz({{ $quiz->id }})"
                                            onclick="return confirm('Rostdan ham o\'chirmoqchimisiz?')"
                                            class="btn btn-sm btn-danger"
                                            title="O'chirish">
                                        <i class="ri-delete-bin-line"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5">
                                <div class="text-center py-5">
                                    <i class="ri-inbox-line text-muted" style="font-size: 80px; opacity: 0.2;"></i>
                                    <h5 class="text-muted mt-3 mb-2">Hozircha quizlar mavjud emas</h5>
                                    <p class="text-muted mb-3">Yangi quiz qo'shish uchun yuqoridagi tugmani bosing</p>
                                    <button wire:click="createQuiz" class="btn btn-primary">
                                        <i class="ri-add-line me-1"></i> Birinchi Quizni Yaratish
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $quizzes->links() }}
            </div>
        </div>
    </div>

    {{-- Create/Edit Quiz Modal --}}
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.5); z-index: 1050;">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-gradient-primary text-white border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="ri-file-list-3-line me-2"></i>
                            {{ $isEdit ? 'Quiz tahrirlash' : 'Yangi Quiz qo\'shish' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="saveQuiz" autocomplete="off">
                        <div class="modal-body p-4">
                            <div class="row g-3">
                                <div class="col-12">
                                    <label class="form-label fw-semibold">
                                        <i class="ri-file-text-line me-1"></i>
                                        Quiz nomi <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" wire:model.live="name" autocomplete="off"
                                           class="form-control form-control-lg @error('name') is-invalid @enderror"
                                           placeholder="Masalan: Matematika Test 1">
                                    @error('name')
                                    <div class="invalid-feedback">
                                        <i class="ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ri-book-line me-1"></i>
                                        Fan <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="subject_id"
                                            class="form-select form-select-lg @error('subject_id') is-invalid @enderror">
                                        <option value="">Tanlang</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                    <div class="invalid-feedback">
                                        <i class="ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col-md-6">
                                    <label class="form-label fw-semibold">
                                        <i class="ri-graduation-cap-line me-1"></i>
                                        Sinf <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="classes_id"
                                            class="form-select form-select-lg @error('classes_id') is-invalid @enderror">
                                        <option value="">Tanlang</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('classes_id')
                                    <div class="invalid-feedback">
                                        <i class="ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                @if(!$isEdit)
                                    <div class="col-12">
                                        <div class="alert alert-info border-0 shadow-sm">
                                            <div class="d-flex align-items-start">
                                                <i class="ri-lightbulb-line fs-4 me-3 mt-1"></i>
                                                <div>
                                                    <strong>Eslatma:</strong> Quiz yaratilgandan so'ng unga savollar
                                                    qo'shishingiz mumkin.
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                        <div class="modal-footer border-0 bg-light">
                            <button type="button" wire:click="closeModal" class="btn btn-secondary btn-lg">
                                <i class="ri-close-line me-1"></i> Bekor qilish
                            </button>
                            <button type="submit" class="btn btn-primary btn-lg shadow">
                                <i class="ri-save-line me-1"></i>
                                {{ $isEdit ? 'Yangilash' : 'Saqlash' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Question Create/Edit Modal --}}
    @if($showQuestionFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.7); z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered modal-xl modal-dialog-scrollable">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-gradient-success text-white border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="ri-question-line me-2"></i>
                            {{ $isEditQuestion ? 'Savolni tahrirlash' : 'Yangi savol qo\'shish' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                                wire:click="closeQuestionFormModal"></button>
                    </div>
                    <form wire:submit.prevent="saveQuestion" autocomplete="off">
                        <div class="modal-body p-4" style="max-height: 70vh;">

                            {{-- Error Messages --}}
                            @if ($errors->any())
                                <div class="alert alert-danger border-0 shadow-sm">
                                    <div class="d-flex align-items-start">
                                        <i class="ri-error-warning-line fs-4 me-3"></i>
                                        <div>
                                            <strong>Xatolar:</strong>
                                            <ul class="mb-0 mt-2">
                                                @foreach ($errors->all() as $error)
                                                    <li>{{ $error }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            {{-- Savol matni --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold fs-5">
                                    <i class="ri-file-text-line me-2 text-primary"></i>
                                    Savol matni <span class="text-danger">*</span>
                                    <small class="text-muted fw-normal">(LaTeX: \( x^2 \))</small>
                                </label>
                                <textarea wire:model="questionText"
                                          class="form-control form-control-lg @error('questionText') is-invalid @enderror"
                                          rows="3"
                                          placeholder="Masalan: \( a^2 + b^2 = c^2 \) formulasi qaysi teoremaga tegishli?"></textarea>
                                @error('questionText')
                                <div class="invalid-feedback d-block">
                                    <i class="ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                                @enderror

                                @if($questionText)
                                    <div class="alert alert-info border-0 shadow-sm mt-3">
                                        <strong><i class="ri-eye-line me-1"></i>Ko'rinishi:</strong>
                                        <div class="mt-2 p-3 bg-white rounded question-preview">{!! $questionText !!}</div>
                                    </div>
                                @endif
                            </div>

                            {{-- Rasm yuklash --}}
                            <div class="mb-4">
                                <label class="form-label fw-bold fs-5">
                                    <i class="ri-image-line me-2 text-info"></i>
                                    Rasm yuklash <small class="text-muted fw-normal">(ixtiyoriy)</small>
                                </label>

                                @if($existingImage)
                                    <div class="card border shadow-sm mb-3">
                                        <div class="card-body">
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="d-flex align-items-center">
                                                    <img src="{{ asset('storage/' . $existingImage) }}"
                                                         alt="Savol rasmi"
                                                         class="rounded shadow-sm me-3"
                                                         style="max-width: 150px; max-height: 150px; object-fit: cover;">
                                                    <div>
                                                        <span class="badge bg-success-subtle text-success">
                                                            <i class="ri-check-line me-1"></i>Mavjud rasm
                                                        </span>
                                                    </div>
                                                </div>
                                                <button type="button" wire:click="removeImage"
                                                        class="btn btn-danger btn-sm">
                                                    <i class="ri-delete-bin-line me-1"></i>O'chirish
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                <div class="card border-dashed border-2 border-secondary">
                                    <div class="card-body text-center py-4">
                                        <input type="file" wire:model="questionImage"
                                               class="d-none"
                                               id="questionImageInput"
                                               accept="image/*">
                                        <label for="questionImageInput" class="btn btn-outline-primary btn-lg w-100">
                                            <i class="ri-upload-cloud-line fs-3 d-block mb-2"></i>
                                            <span>Rasm tanlash</span>
                                            <small class="d-block text-muted mt-1">PNG, JPG (Max: 2MB)</small>
                                        </label>
                                    </div>
                                </div>

                                @if($questionImage)
                                    <div class="alert alert-success border-0 shadow-sm mt-3">
                                        <i class="ri-checkbox-circle-line me-2"></i>
                                        <strong>Yangi rasm tanlandi</strong>
                                    </div>
                                @endif

                                @error('questionImage')
                                <div class="alert alert-danger border-0 mt-2">
                                    <i class="ri-error-warning-line me-1"></i>{{ $message }}
                                </div>
                                @enderror
                            </div>

                            {{-- Variantlar --}}
                            <div class="mb-3">
                                <label class="form-label fw-bold fs-5">
                                    <i class="ri-list-check me-2 text-warning"></i>
                                    Javob variantlari <span class="text-danger">*</span>
                                </label>
                            </div>

                            <div class="row g-3">
                                @foreach(['A', 'B', 'C', 'D'] as $index => $letter)
                                    <div class="col-md-6">
                                        <div class="card h-100 {{ $correctOption == $index ? 'border-success border-3 shadow' : 'border' }}">
                                            <div class="card-body">
                                                <div class="form-check mb-3">
                                                    <input class="form-check-input"
                                                           type="radio"
                                                           wire:model="correctOption"
                                                           value="{{ $index }}"
                                                           id="correct{{ $index }}"
                                                           name="correctOption">
                                                    <label class="form-check-label fw-bold fs-5" for="correct{{ $index }}">
                                                        <span class="badge bg-primary me-2">{{ $letter }}</span>
                                                        {{ $letter }} varianti
                                                        @if($correctOption == $index)
                                                            <span class="badge bg-success ms-2">
                                                                <i class="ri-check-line me-1"></i>To'g'ri javob
                                                            </span>
                                                        @endif
                                                    </label>
                                                </div>
                                                <textarea wire:model="options.{{ $index }}"
                                                          class="form-control @error('options.'.$index) is-invalid @enderror"
                                                          rows="2"
                                                          placeholder="{{ $letter }} variantini kiriting..."></textarea>
                                                @error('options.'.$index)
                                                <div class="invalid-feedback d-block">{{ $message }}</div>
                                                @enderror

                                                @if($options[$index])
                                                    <div class="mt-2 p-2 bg-light rounded">
                                                        <small class="text-muted">Ko'rinishi:</small>
                                                        <div class="option-preview-{{ $index }}">{!! $options[$index] !!}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            @error('correctOption')
                            <div class="alert alert-danger border-0 shadow-sm mt-3">
                                <i class="ri-error-warning-line me-2"></i>{{ $message }}
                            </div>
                            @enderror

                            <div class="alert alert-info border-0 shadow-sm mt-4">
                                <strong><i class="ri-information-line me-2"></i>LaTeX misollar:</strong>
                                <div class="d-flex flex-wrap gap-2 mt-2">
                                    <code class="px-2 py-1 bg-white rounded">\(\frac{a}{b}\)</code>
                                    <code class="px-2 py-1 bg-white rounded">\(\sqrt{x}\)</code>
                                    <code class="px-2 py-1 bg-white rounded">\(x^2\)</code>
                                    <code class="px-2 py-1 bg-white rounded">\(x_1\)</code>
                                </div>
                            </div>
                        </div>

                        <div class="modal-footer border-0 bg-light">
                            <button type="button" wire:click="closeQuestionFormModal" class="btn btn-secondary btn-lg">
                                <i class="ri-close-line me-1"></i>Bekor qilish
                            </button>
                            <button type="submit" class="btn btn-success btn-lg shadow">
                                <span wire:loading.remove wire:target="saveQuestion">
                                    <i class="ri-save-line me-1"></i>Saqlash
                                </span>
                                <span wire:loading wire:target="saveQuestion">
                                    <span class="spinner-border spinner-border-sm me-2"></span>
                                    Saqlanmoqda...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- Questions Management Modal --}}
    @if($showQuestionsModal && $currentQuiz)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-gradient-primary text-white border-0">
                        <div>
                            <h5 class="modal-title fw-bold mb-1">
                                <i class="ri-question-line me-2"></i>
                                {{ $currentQuiz->name }}
                            </h5>
                            <div class="d-flex gap-2">
                                <span class="badge bg-white text-primary">{{ $currentQuiz->subject->name }}</span>
                                <span class="badge bg-white text-primary">{{ $currentQuiz->class->name }}</span>
                            </div>
                        </div>
                        <button type="button" class="btn-close btn-close-white"
                                wire:click="closeQuestionsModal"></button>
                    </div>
                    <div class="modal-body p-4">
                        @if (session()->has('question_message'))
                            <div class="alert alert-success border-0 shadow-sm">
                                <i class="ri-checkbox-circle-line me-2"></i>
                                {{ session('question_message') }}
                            </div>
                        @endif

                        @if (session()->has('question_error'))
                            <div class="alert alert-danger border-0 shadow-sm">
                                <i class="ri-error-warning-line me-2"></i>
                                {{ session('question_error') }}
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="input-group shadow-sm" style="max-width: 400px;">
                                <span class="input-group-text bg-white">
                                    <i class="ri-search-line text-primary"></i>
                                </span>
                                <input type="text" wire:model.live="questionSearch"
                                       class="form-control" placeholder="Savol qidirish...">
                            </div>
                            <button wire:click="createQuestion" class="btn btn-success btn-lg shadow">
                                <i class="ri-add-line me-1"></i> Yangi Savol
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">№</th>
                                    <th style="width: 50%">Savol</th>
                                    <th style="width: 30%">To'g'ri javob</th>
                                    <th style="width: 15%" class="text-end">Amallar</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($this->questions as $index => $question)
                                    <tr>
                                        <td class="text-center fw-bold">{{ $index + 1 }}</td>
                                        <td>
                                            <div class="question-text">{!! $question->name !!}</div>
                                            @if($question->image)
                                                <img src="{{ asset('storage/' . $question->image) }}"
                                                     alt="Savol rasmi"
                                                     class="img-thumbnail mt-2"
                                                     style="max-width: 100px; max-height: 100px;">
                                            @endif
                                        </td>
                                        <td>
                                            @php
                                                $letters = ['A', 'B', 'C', 'D'];
                                                $correctIndex = $question->options->search(fn($opt) => $opt->is_correct == 1);
                                            @endphp
                                            @if($correctIndex !== false && isset($question->options[$correctIndex]))
                                                <span class="badge bg-success fs-6 p-2">
                                                    {{ $letters[$correctIndex] }}: {{ Str::limit($question->options[$correctIndex]->name, 30) }}
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">Belgilanmagan</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <div class="btn-group">
                                                <button wire:click="editQuestion({{ $question->id }})"
                                                        class="btn btn-sm btn-warning">
                                                    <i class="ri-pencil-line"></i>
                                                </button>
                                                <button wire:click="deleteQuestion({{ $question->id }})"
                                                        onclick="return confirm('O\'chirmoqchimisiz?')"
                                                        class="btn btn-sm btn-danger">
                                                    <i class="ri-delete-bin-line"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="ri-question-line text-muted" style="font-size: 80px; opacity: 0.2;"></i>
                                            <p class="text-muted mt-3">Hozircha savollar yo'q. Yangi savol qo'shing.</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button wire:click="closeQuestionsModal" class="btn btn-secondary btn-lg">
                            <i class="ri-close-line me-1"></i> Yopish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- View Quiz Modal --}}
    @if($showViewModal && $viewingQuiz)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); z-index: 1065;">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content border-0 shadow-lg">
                    <div class="modal-header bg-gradient-info text-white border-0">
                        <h5 class="modal-title fw-bold">
                            <i class="ri-eye-line me-2"></i>
                            {{ $viewingQuiz->name }} - Ko'rish
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                                wire:click="closeViewModal"></button>
                    </div>
                    <div class="modal-body p-4" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row mb-4">
                            <div class="col-md-6">
                                <div class="card border-0 bg-info-subtle">
                                    <div class="card-body">
                                        <strong class="d-block mb-2">
                                            <i class="ri-book-line me-1"></i>Fan:
                                        </strong>
                                        <span class="badge bg-info fs-6 px-3 py-2">
                                            {{ $viewingQuiz->subject->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card border-0 bg-success-subtle">
                                    <div class="card-body">
                                        <strong class="d-block mb-2">
                                            <i class="ri-graduation-cap-line me-1"></i>Sinf:
                                        </strong>
                                        <span class="badge bg-success fs-6 px-3 py-2">
                                            {{ $viewingQuiz->class->name }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        @if($viewingQuiz->questions->count() > 0)
                            <h6 class="fw-bold mb-4">
                                <i class="ri-question-line me-2 text-primary"></i>
                                Savollar ({{ $viewingQuiz->questions->count() }} ta)
                            </h6>
                            <div class="accordion" id="questionsAccordion">
                                @foreach($viewingQuiz->questions as $index => $question)
                                    <div class="accordion-item border shadow-sm mb-3">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed fw-bold" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#question{{ $question->id }}">
                                                <span class="badge bg-primary me-3">{{ $index + 1 }}</span>
                                                Savol {{ $index + 1 }}
                                            </button>
                                        </h2>
                                        <div id="question{{ $question->id }}" class="accordion-collapse collapse"
                                             data-bs-parent="#questionsAccordion">
                                            <div class="accordion-body bg-light">
                                                <div class="card border-0 mb-3">
                                                    <div class="card-body">
                                                        <strong class="text-primary">
                                                            <i class="ri-chat-quote-line me-1"></i>Savol:
                                                        </strong>
                                                        <div class="mt-2 p-3 bg-white rounded">
                                                            {!! $question->name !!}
                                                        </div>

                                                        @if($question->image)
                                                            <div class="mt-3">
                                                                <img src="{{ asset('storage/' . $question->image) }}"
                                                                     alt="Savol rasmi"
                                                                     class="img-fluid rounded shadow-sm"
                                                                     style="max-width: 500px;">
                                                            </div>
                                                        @endif
                                                    </div>
                                                </div>

                                                <strong class="text-success d-block mb-3">
                                                    <i class="ri-list-check me-1"></i>Javob variantlari:
                                                </strong>

                                                <div class="row g-3">
                                                    @foreach($question->options as $optIndex => $option)
                                                        @php
                                                            $letters = ['A', 'B', 'C', 'D'];
                                                        @endphp
                                                        <div class="col-md-6">
                                                            <div class="card h-100 {{ $option->is_correct ? 'border-success border-3 bg-success-subtle' : 'border' }}">
                                                                <div class="card-body">
                                                                    <div class="d-flex align-items-center mb-2">
                                                                        <span class="badge {{ $option->is_correct ? 'bg-success' : 'bg-secondary' }} me-2">
                                                                            {{ $letters[$optIndex] }}
                                                                        </span>
                                                                        @if($option->is_correct)
                                                                            <span class="badge bg-success">
                                                                                <i class="ri-check-line me-1"></i>To'g'ri javob
                                                                            </span>
                                                                        @endif
                                                                    </div>
                                                                    <div class="mt-2">
                                                                        {!! $option->name !!}
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="text-center py-5">
                                <i class="ri-inbox-line text-muted" style="font-size: 80px; opacity: 0.2;"></i>
                                <h5 class="text-muted mt-3">Hozircha savollar yo'q</h5>
                            </div>
                        @endif
                    </div>
                    <div class="modal-footer border-0 bg-light">
                        <button wire:click="closeViewModal" class="btn btn-secondary btn-lg">
                            <i class="ri-close-line me-1"></i>Yopish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- MathJax --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/3.2.2/es5/tex-mml-chtml.min.js"></script>
<script>
    document.addEventListener('livewire:initialized', () => {
        Livewire.hook('message.processed', () => {
            if (typeof MathJax !== 'undefined') {
                MathJax.typesetPromise();
            }
        });
    });
</script>

{{-- Custom Styles --}}
<style>
    /* Gradient Backgrounds */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    }

    .bg-gradient-success {
        background: linear-gradient(135deg, #56ab2f 0%, #a8e063 100%);
    }

    .bg-gradient-info {
        background: linear-gradient(135deg, #2193b0 0%, #6dd5ed 100%);
    }

    /* Question Text */
    .question-text {
        font-size: 1rem;
        line-height: 1.8;
        color: #2d3748;
    }

    /* Scrollbar Styling */
    .modal-body {
        scrollbar-width: thin;
        scrollbar-color: #cbd5e0 #f7fafc;
    }

    .modal-body::-webkit-scrollbar {
        width: 8px;
    }

    .modal-body::-webkit-scrollbar-track {
        background: #f7fafc;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background: #cbd5e0;
        border-radius: 10px;
    }

    .modal-body::-webkit-scrollbar-thumb:hover {
        background: #a0aec0;
    }

    /* Card Hover Effects */
    .table-hover tbody tr:hover {
        background-color: #f7fafc;
        transform: scale(1.01);
        transition: all 0.2s ease;
    }

    /* Badge Enhancements */
    .badge {
        font-weight: 500;
        letter-spacing: 0.5px;
    }

    /* Button Shadows */
    .btn-lg {
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        transition: all 0.3s ease;
    }

    .btn-lg:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    /* Border Dashed for Upload */
    .border-dashed {
        border-style: dashed !important;
    }

    /* Avatar Styling */
    .avatar {
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .avatar-xs {
        width: 30px;
        height: 30px;
        font-size: 0.875rem;
    }

    .avatar-sm {
        width: 40px;
        height: 40px;
        font-size: 1rem;
    }

    /* Alert Improvements */
    .alert {
        border-radius: 8px;
    }

    /* Input Focus */
    .form-control:focus,
    .form-select:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
    }

    /* Animation for Modals */
    .modal.show {
        animation: fadeIn 0.3s ease;
    }

    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: scale(0.95);
        }
        to {
            opacity: 1;
            transform: scale(1);
        }
    }

    /* Accordion Styling */
    .accordion-button:not(.collapsed) {
        background-color: #667eea;
        color: white;
    }

    .accordion-button:focus {
        box-shadow: 0 0 0 0.25rem rgba(102, 126, 234, 0.25);
    }
</style>
