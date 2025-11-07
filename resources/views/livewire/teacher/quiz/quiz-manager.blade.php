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
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="ri-checkbox-circle-line me-2"></i>
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    {{-- Header --}}
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-0">
                    <i class="ri-file-list-3-line me-2"></i>
                    Quizlar Ro'yxati
                </h4>
                <p class="text-muted small mb-0">Barcha quizlarni boshqarish</p>
            </div>
            <button wire:click="createQuiz" class="btn btn-primary" type="button">
                <i class="ri-add-line me-1"></i> Yangi Quiz
            </button>
        </div>

        <div class="card-body">
            {{-- Search --}}
            <div class="row mb-4">
                <div class="col-md-6">
                    <div class="input-group">
                        <span class="input-group-text">
                            <i class="ri-search-line"></i>
                        </span>
                        <input type="text" wire:model.live="search" class="form-control"
                               placeholder="Quiz nomini qidiring...">
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">
                        <i class="ri-information-line"></i>
                        Jami: <strong>{{ $quizzes->total() }}</strong> ta quiz
                    </span>
                </div>
            </div>

            {{-- Table --}}
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                    <tr>
                        <th>ID</th>
                        <th>Quiz nomi</th>
                        <th>Fan</th>
                        <th>Sinf</th>
                        <th>Savollar</th>
                        <th>Yaratuvchi</th>
                        <th>Holat</th>
                        <th>Sana</th>
                        <th class="text-end">Amallar</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($quizzes as $quiz)
                        <tr>
                            <td>{{ $quiz->id }}</td>
                            <td>
                                <div class="d-flex align-items-center">
                                    <div class="avatar avatar-sm me-2">
                                        <div class="avatar-initial rounded-circle bg-label-primary">
                                            <i class="ri-file-list-3-line"></i>
                                        </div>
                                    </div>
                                    <strong>{{ $quiz->name }}</strong>
                                </div>
                            </td>
                            <td>
                                    <span class="badge bg-label-info">
                                        {{ $quiz->subject->name ?? 'N/A' }}
                                    </span>
                            </td>
                            <td>
                                    <span class="badge bg-label-success">
                                        {{ $quiz->class->name ?? 'N/A' }}
                                    </span>
                            </td>
                            <td>
                                <button wire:click="manageQuestions({{ $quiz->id }})"
                                        class="badge bg-label-primary border-0"
                                        style="cursor: pointer;">
                                    <i class="ri-question-line me-1"></i>
                                    {{ $quiz->questions_count }} ta
                                    Savol qo'shish
                                </button>
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $quiz->creator->name ?? 'N/A' }}
                                </small>
                            </td>
                            <td>
                                @if($quiz->status == \App\Models\Teacher\Quiz::STATUS_ACTIVE)
                                    <span class="badge bg-success">Faol</span>
                                @else
                                    <span class="badge bg-secondary">Nofaol</span>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">
                                    {{ $quiz->created_at->format('d.m.Y') }}
                                </small>
                            </td>
                            <td class="text-end">
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
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="ri-inbox-line" style="font-size: 64px; opacity: 0.3;"></i>
                                <h5 class="text-muted mt-3">Hozircha quizlar mavjud emas</h5>
                                <p class="text-muted">Yangi quiz qo'shish uchun yuqoridagi tugmani bosing</p>
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
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="ri-file-list-3-line me-2"></i>
                            {{ $isEdit ? 'Quiz tahrirlash' : 'Yangi Quiz qo\'shish' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="saveQuiz" autocomplete="off">
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label">
                                        Quiz nomi <span class="text-danger">*</span>
                                    </label>
                                    <div class="input-group">
                                        <span class="input-group-text">
                                            <i class="ri-file-text-line"></i>
                                        </span>
                                        <input type="text" wire:model.live="name" autocomplete="off"
                                               class="form-control @error('name') is-invalid @enderror"
                                               placeholder="Masalan: Matematika Test 1">
                                    </div>
                                    @error('name')
                                    <div class="text-danger small mt-1">
                                        <i class="ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Fan <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="subject_id"
                                            class="form-select @error('subject_id') is-invalid @enderror">
                                        <option value="">Tanlang</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('subject_id')
                                    <div class="text-danger small mt-1">
                                        <i class="ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label class="form-label">
                                        Sinf <span class="text-danger">*</span>
                                    </label>
                                    <select wire:model="classes_id"
                                            class="form-select @error('classes_id') is-invalid @enderror">
                                        <option value="">Tanlang</option>
                                        @foreach($classes as $class)
                                            <option value="{{ $class->id }}">{{ $class->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('classes_id')
                                    <div class="text-danger small mt-1">
                                        <i class="ri-error-warning-line me-1"></i>{{ $message }}
                                    </div>
                                    @enderror
                                </div>

                                @if(!$isEdit)
                                    <div class="col-12">
                                        <div class="alert alert-info mb-0">
                                            <div class="d-flex align-items-start">
                                                <i class="ri-lightbulb-line me-2 mt-1"></i>
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
                        <div class="modal-footer">
                            <button type="button" wire:click="closeModal" class="btn btn-secondary">
                                <i class="ri-close-line me-1"></i> Bekor qilish
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="ri-save-line me-1"></i>
                                {{ $isEdit ? 'Yangilash' : 'Saqlash' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    {{-- View Quiz Modal --}}
    @if($showViewModal && $viewingQuiz)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); z-index: 1065;">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title">
                            {{ $viewingQuiz->name }} - Ko‘rish
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                                wire:click="closeViewModal"></button>
                    </div>
                    <div class="modal-body" style="max-height: 70vh; overflow-y: auto;">
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <strong>Fan:</strong>
                                <span class="badge bg-info">{{ $viewingQuiz->subject->name }}</span>
                            </div>
                            <div class="col-md-6">
                                <strong>Sinf:</strong>
                                <span class="badge bg-success">{{ $viewingQuiz->class->name }}</span>
                            </div>
                        </div>

                        <hr>

                        @if($viewingQuiz->questions->count() > 0)
                            <h6><i class="ri-question-line"></i> Savollar ({{ $viewingQuiz->questions->count() }} ta)
                            </h6>
                            <div class="accordion" id="questionsAccordion">
                                @foreach($viewingQuiz->questions as $index => $question)
                                    <div class="accordion-item">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button"
                                                    data-bs-toggle="collapse"
                                                    data-bs-target="#question{{ $question->id }}">
                                                <strong>{{ $index + 1 }}. Savol</strong>
                                            </button>
                                        </h2>
                                        <div id="question{{ $question->id }}" class="accordion-collapse collapse"
                                             data-bs-parent="#questionsAccordion">
                                            <div class="accordion-body">
                                                <p class="mb-3"><strong>Savol:</strong> {!! $question->name !!}</p>

                                                <div class="row">
                                                    @foreach($question->options as $optIndex => $option)
                                                        @php
                                                            $letters = ['A', 'B', 'C', 'D'];
                                                        @endphp
                                                        <div class="col-md-6 mb-2">
                                                            <div
                                                                class="p-2 rounded {{ $option->is_correct ? 'bg-success text-white' : 'bg-light' }}">
                                                                <strong>{{ $letters[$optIndex] }}
                                                                    :</strong> {!! $option->name !!}
                                                                @if($option->is_correct)
                                                                    <span class="badge bg-white text-success ms-2">To'g'ri</span>
                                                                @endif
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
                            <p class="text-muted text-center py-4">
                                <i class="ri-inbox-line" style="font-size: 48px; opacity: 0.3;"></i><br>
                                Hozircha savollar yo‘q.
                            </p>
                        @endif
                    </div>
                    <div class="modal-footer bg-light">
                        <button wire:click="closeViewModal" class="btn btn-secondary">
                            Yopish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Questions Management Modal --}}
    @if($showQuestionsModal && $currentQuiz)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.6); z-index: 1055;">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <div>
                            <h5 class="modal-title mb-1">
                                <i class="ri-question-line me-2"></i>
                                {{ $currentQuiz->name }} - Savollar
                            </h5>
                            <small class="d-flex gap-2">
                                <span class="badge bg-white text-primary">{{ $currentQuiz->subject->name }}</span>
                                <span class="badge bg-white text-primary">{{ $currentQuiz->class->name }}</span>
                            </small>
                        </div>
                        <button type="button" class="btn-close btn-close-white"
                                wire:click="closeQuestionsModal"></button>
                    </div>
                    <div class="modal-body">
                        @if (session()->has('question_message'))
                            <div class="alert alert-success alert-dismissible fade show">
                                <i class="ri-checkbox-circle-line me-2"></i>
                                {{ session('question_message') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        @if (session()->has('question_error'))
                            <div class="alert alert-danger alert-dismissible fade show">
                                <i class="ri-error-warning-line me-2"></i>
                                {{ session('question_error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="input-group" style="max-width: 400px;">
                                <span class="input-group-text">
                                    <i class="ri-search-line"></i>
                                </span>
                                <input type="text" wire:model.live="questionSearch"
                                       class="form-control" placeholder="Savol qidirish...">
                            </div>
                            <button wire:click="createQuestion" class="btn btn-success">
                                <i class="ri-add-line me-1"></i> Yangi Savol
                            </button>
                        </div>

                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">№</th>
                                    <th style="width: 55%">Savol</th>
                                    <th style="width: 25%">To'g'ri javob</th>
                                    <th style="width: 15%" class="text-end">Amallar</th>
                                </tr>
                                </thead>
                                <tbody>
                                @forelse($this->questions as $index => $question)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td>
                                            <div class="question-text">{!! $question->name !!}</div>
                                        </td>
                                        <td>
                                            @if($question->correctOption)
                                                @php
                                                    $letters = ['A', 'B', 'C', 'D'];
                                                    $correctIndex = $question->options->search(function($opt) {
                                                        return $opt->is_correct == 1;
                                                    });
                                                @endphp
                                                <span class="badge bg-success">
                                                        {{ $letters[$correctIndex] }}: {{ Str::limit($question->correctOption->name, 20) }}
                                                    </span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <button wire:click="editQuestion({{ $question->id }})"
                                                    class="btn btn-sm btn-warning">
                                                <i class="ri-pencil-line"></i>
                                            </button>
                                            <button wire:click="deleteQuestion({{ $question->id }})"
                                                    onclick="return confirm('O\'chirmoqchimisiz?')"
                                                    class="btn btn-sm btn-danger">
                                                <i class="ri-delete-bin-line"></i>
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="ri-question-line" style="font-size: 48px; opacity: 0.3;"></i>
                                            <p class="text-muted mt-2">Hozircha savollar yo'q. Yangi savol qo'shing.</p>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button wire:click="closeQuestionsModal" class="btn btn-secondary">
                            <i class="ri-close-line me-1"></i> Yopish
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- Question Create/Edit Modal --}}
    @if($showQuestionFormModal)
        <div class="modal fade show d-block" tabindex="-1" style="background: rgba(0,0,0,0.7); z-index: 1060;">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title">
                            {{ $isEditQuestion ? 'Savolni tahrirlash' : 'Yangi savol' }}
                        </h5>
                        <button type="button" class="btn-close btn-close-white"
                                wire:click="closeQuestionFormModal"></button>
                    </div>
                    <form wire:submit.prevent="saveQuestion" autocomplete="off">
                        <div class="modal-body" style="max-height: 70vh; overflow-y: auto; padding-right: 10px;">
                            <!-- Barcha inputlar shu yerda -->
                            <div class="mb-4">
                                <label class="form-label fw-bold">
                                    Savol matni <span class="text-danger">*</span>
                                    <small class="text-muted">(LaTeX: \( x^2 \))</small>
                                </label>
                                <textarea wire:model.live="questionText"
                                          class="form-control @error('questionText') is-invalid @enderror"
                                          rows="3"
                                          placeholder="Masalan: \( a^2 + b^2 = c^2 \)"></textarea>
                                @error('questionText')
                                <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror

                                @if($questionText)
                                    <div class="alert alert-info mt-2 p-2">
                                        <strong>Ko'rinishi:</strong>
                                        <div class="mt-1 question-preview">{!! $questionText !!}</div>
                                    </div>
                                @endif
                            </div>

                            <div class="row">
                                <div class="col-12 mb-3">
                                    <label class="form-label fw-bold">Javob variantlari <span
                                            class="text-danger">*</span></label>
                                </div>

                                @foreach(['A', 'B', 'C', 'D'] as $index => $letter)
                                    <div class="col-md-6 mb-3">
                                        <div
                                            class="card {{ $correctOption == $index ? 'border-success border-2' : '' }}">
                                            <div class="card-body p-3">
                                                <div class="form-check mb-2">
                                                    <input class="form-check-input" type="radio"
                                                           wire:model.live="correctOption" value="{{ $index }}"
                                                           id="correct{{ $index }}">
                                                    <label class="form-check-label fw-bold" for="correct{{ $index }}">
                                                        {{ $letter }} varianti
                                                        @if($correctOption == $index)
                                                            <span class="badge bg-success ms-1">To'g'ri</span>
                                                        @endif
                                                    </label>
                                                </div>
                                                <textarea wire:model.live="options.{{ $index }}"
                                                          class="form-control @error('options.'.$index) is-invalid @enderror"
                                                          rows="2"
                                                          placeholder="{{ $letter }} variant..."></textarea>
                                                @error('options.'.$index)
                                                <div class="text-danger small mt-1">{{ $message }}</div>
                                                @enderror

                                                @if($options[$index])
                                                    <div class="mt-2 p-2 bg-light rounded small">
                                                        <div
                                                            class="option-preview-{{ $index }}">{!! $options[$index] !!}</div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                            <div class="alert alert-info mb-0 mt-3 small">
                                <strong>LaTeX misollar:</strong>
                                <code class="mx-1">\(\frac{a}{b}\)</code>
                                <code>\(\sqrt{x}\)</code>
                                <code>\(x^2\)</code>
                                <code>\(x_1\)</code>
                            </div>
                        </div>

                        <!-- FOOTER HAR DOIM KO'RINADI -->
                        <div class="modal-footer border-top pt-3 bg-light" style="position: sticky; bottom: 0;">
                            <button type="button" wire:click="closeQuestionFormModal" class="btn btn-secondary">
                                Bekor qilish
                            </button>
                            <button type="submit" class="btn btn-success">
                                Saqlash
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

{{-- MathJax --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/mathjax/3.2.2/es5/tex-mml-chtml.min.js"></script>
<script>
    document.addEventListener('livewire:load', function () {
        Livewire.hook('message.processed', () => {
            if (typeof MathJax !== 'undefined') {
                MathJax.typesetPromise();
            }
        });
    });
</script>

<style>
    .question-text {
        font-size: 1rem;
        line-height: 1.6;
    }

    .modal-body {
        scrollbar-width: thin;
    }

    .modal-body::-webkit-scrollbar {
        width: 6px;
    }

    .modal-body::-webkit-scrollbar-thumb {
        background-color: #a0aec0;
        border-radius: 3px;
    }
</style>
