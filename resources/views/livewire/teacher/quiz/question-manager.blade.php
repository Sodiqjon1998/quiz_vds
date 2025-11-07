<div>
    <h2 class="h4 mb-4">Savollarni Boshqarish: {{ $quiz->name }}</h2>

    {{-- Xabarlar (Message and Error Flashes) --}}
    @if (session()->has('message'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('message') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center">
            <input wire:model.live.debounce.300ms="search" type="text" class="form-control w-50"
                   placeholder="Savollarni qidirish...">
            <button wire:click="createQuestion" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Yangi Savol
            </button>
        </div>
        <div class="card-body">
            @if ($questions->count())
                <div class="table-responsive">
                    <table class="table table-hover table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Savol Matni</th>
                            <th>Variantlar Soni</th>
                            <th>To'g'ri Javob</th>
                            <th>Yaratilgan Sana</th>
                            <th>Amallar</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach ($questions as $index => $question)
                            <tr>
                                <td>{{ $questions->firstItem() + $index }}</td>
                                <td>{{ Str::limit($question->name, 50) }}</td>
                                <td>{{ $question->options->count() }}</td>
                                <td>
                                    @php
                                        $correctOption = $question->options->where('is_correct', 1)->first();
                                    @endphp
                                    <span
                                        class="badge bg-success">{{ $correctOption ? Str::limit($correctOption->name, 30) : 'Aniqlanmagan' }}</span>
                                </td>
                                <td>{{ $question->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <button wire:click="editQuestion({{ $question->id }})"
                                            class="btn btn-sm btn-info me-2" title="Tahrirlash">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button wire:click="deleteQuestion({{ $question->id }})"
                                            class="btn btn-sm btn-danger" title="O'chirish"
                                            onclick="confirm('Savolni o\'chirishni xohlaysizmi?') || event.stopImmediatePropagation()">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
                {{ $questions->links() }}
            @else
                <p class="text-center text-muted">Hozircha savollar mavjud emas.</p>
            @endif
        </div>
    </div>

    {{-- Modal (Savol Qo'shish/Tahrirlash) --}}
    @if($showModal)
        <div class="modal fade show d-block" tabindex="-1" role="dialog" style="background-color: rgba(0, 0, 0, 0.5);">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $isEdit ? 'Savolni Tahrirlash' : 'Yangi Savol Qo\'shish' }}</h5>
                        <button type="button" class="btn-close" wire:click="closeModal"></button>
                    </div>
                    <form wire:submit.prevent="saveQuestion">
                        <div class="modal-body">
                            {{-- Savol Matni --}}
                            <div class="mb-3">
                                <label for="questionText" class="form-label">Savol Matni</label>
                                <textarea wire:model.defer="questionText"
                                          class="form-control @error('questionText') is-invalid @enderror"
                                          id="questionText" rows="3"></textarea>
                                @error('questionText')
                                <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>

                            <hr>

                            {{-- Variantlar --}}
                            <h6 class="mb-3">Variantlar (4 ta):</h6>
                            @foreach ($options as $index => $optionText)
                                <div class="mb-3 d-flex align-items-center">
                                    <div class="form-check me-3">
                                        <input class="form-check-input" type="radio" wire:model.defer="correctOption"
                                               id="correctOption{{ $index }}" value="{{ $index }}">
                                        <label class="form-check-label" for="correctOption{{ $index }}">
                                            To'g'ri
                                        </label>
                                    </div>
                                    <div class="flex-grow-1">
                                        <input wire:model.defer="options.{{ $index }}" type="text"
                                               class="form-control @error('options.'.$index) is-invalid @enderror"
                                               placeholder="{{ $index + 1 }}-variant matni">
                                        @error('options.'.$index)
                                        <div class="invalid-feedback">{{ $message }}</div> @enderror
                                    </div>
                                </div>
                            @endforeach

                            @error('correctOption')
                            <p class="text-danger small mt-2">{{ $message }}</p>
                            @enderror

                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeModal">Bekor Qilish
                            </button>
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading wire:target="saveQuestion"
                                      class="spinner-border spinner-border-sm me-1" role="status"
                                      aria-hidden="true"></span>
                                {{ $isEdit ? 'Yangilash' : 'Saqlash' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    // Livewire modalni yopish uchun event
    window.addEventListener('close-modal', event => {
        $('.modal').modal('hide');
    });
</script>
