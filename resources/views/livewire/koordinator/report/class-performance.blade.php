<div>
    {{-- Debug info --}}
    <!-- <div class="alert alert-info mb-3">
        <strong>Debug Info:</strong><br>
        Class Filter: {{ $classFilter ?? 'NOT SET' }}<br>
        Class Name: {{ $className ?? 'NOT SET' }}<br>
        Date From: {{ $dateFrom }}<br>
        Date To: {{ $dateTo }}<br>
        Show Empty: {{ $showEmptyMessage ?? 'false' ? 'true' : 'false' }}
    </div> -->

    {{-- Header --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-success text-white py-3">
            <h4 class="mb-0 fw-bold text-white">
                <i class="ri-bar-chart-box-line me-2"></i>
                O'quvchilar faoliyati
            </h4>
        </div>

        <div class="card-body p-4">
            {{-- Filters --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-danger">
                        <i class="ri-graduation-cap-line me-1"></i>
                        Sinf *
                    </label>
                    <select wire:model.live="classFilter" class="form-select border-danger border-2" required>
                        <option value="">-- Sinfni tanlang --</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($classFilter)
                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="ri-calendar-line me-1"></i>Dan
                    </label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold">
                        <i class="ri-calendar-line me-1"></i>Gacha
                    </label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>

                <div class="col-md-2 d-flex align-items-end">
                    <button wire:click="$refresh" class="btn btn-primary w-100">
                        <i class="ri-refresh-line me-1"></i>Yangilash
                    </button>
                </div>
                @endif
            </div>

            {{-- Sinf tanlanmagan --}}
            @if($showEmptyMessage ?? false)
            <div class="alert alert-warning border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <i class="ri-information-line fs-1 me-3"></i>
                    <div>
                        <h5 class="alert-heading mb-2">Sinf tanlang</h5>
                        <p class="mb-0">Hisobotlarni ko'rish uchun yuqorida sinf tanlang</p>
                    </div>
                </div>
            </div>

            @else
            {{-- Statistics --}}
            @if($statistics)
            <!-- <div class="alert alert-success mb-4">
                        <strong>Statistika:</strong><br>
                        Jami hisobotlar: {{ $statistics['total_reports'] }}<br>
                        Jami vazifalar: {{ $statistics['total_tasks'] }}<br>
                        Bajarildi: {{ $statistics['completed_tasks'] }}<br>
                        O'quvchilar: {{ $statistics['students_count'] }}
                    </div> -->

            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <div class="card border-0 bg-secondary bg-opacity-10 h-100">
                        <div class="card-body text-center">
                            <i class="ri-file-list-3-line text-primary mb-2" style="font-size: 2.5rem;"></i>
                            <h3 class="fw-bold text-primary mb-1">{{ $statistics['total_reports'] }}</h3>
                            <p class="text-muted mb-0 small">Jami hisobotlar</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 bg-info bg-opacity-10 h-100">
                        <div class="card-body text-center">
                            <i class="ri-task-line text-info mb-2" style="font-size: 2.5rem;"></i>
                            <h3 class="fw-bold text-info mb-1">{{ $statistics['total_tasks'] }}</h3>
                            <p class="text-muted mb-0 small">Jami vazifalar</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 bg-success bg-opacity-10 h-100">
                        <div class="card-body text-center">
                            <i class="ri-checkbox-circle-line text-success mb-2" style="font-size: 2.5rem;"></i>
                            <h3 class="fw-bold text-success mb-1">{{ $statistics['completed_tasks'] }}</h3>
                            <p class="text-muted mb-0 small">Bajarildi</p>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="card border-0 bg-warning bg-opacity-10 h-100">
                        <div class="card-body text-center">
                            <i class="ri-percent-line text-warning mb-2" style="font-size: 2.5rem;"></i>
                            <h3 class="fw-bold text-warning mb-1">{{ $statistics['completion_rate'] }}%</h3>
                            <p class="text-muted mb-0 small">Bajarilish foizi</p>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Top 3 --}}
            {{-- Top 3 --}}
            <!-- @if(count($topStudents) > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                    <h5 class="mb-0 fw-bold text-white">
                        <i class="ri-trophy-line me-2"></i>
                        TOP 3 ENG FAOL O'QUVCHILAR
                    </h5>
                </div>
                <div class="card-body p-3">
                    <div class="row g-2">
                        @foreach($topStudents as $index => $student)
                        <div class="col-md-4">
                            <div class="card h-100 border-2 
                            {{ $index == 0 ? 'border-warning' : ($index == 1 ? 'border-secondary' : 'border-danger') }}">
                                <div class="card-body p-3 text-center">
                                    {{-- Medal Icon --}}
                                    <div class="mb-2" style="font-size: 2rem;">
                                        @if($index == 0)
                                        🥇
                                        @elseif($index == 1)
                                        🥈
                                        @else
                                        🥉
                                        @endif
                                    </div>

                                    {{-- Student Name --}}
                                    <h6 class="fw-bold mb-2" style="font-size: 0.95rem;">{{ $student['name'] }}</h6>

                                    {{-- Stats in compact form --}}
                                    <div class="row g-1 mb-2 text-start">
                                        <div class="col-12 d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <small class="text-muted">Hisobotlar:</small>
                                            <span class="badge bg-primary">{{ $student['reports_done'] }}</span>
                                        </div>
                                        <div class="col-12 d-flex justify-content-between align-items-center py-1 border-bottom">
                                            <small class="text-muted">Vazifalar:</small>
                                            <span class="badge bg-success">{{ $student['tasks_done'] }}</span>
                                        </div>
                                        <div class="col-12 d-flex justify-content-between align-items-center py-1">
                                            <small class="text-muted">Test:</small>
                                            <span class="badge bg-info">{{ $student['exam_score'] }}%</span>
                                        </div>
                                    </div>

                                    {{-- Total Score --}}
                                    <div class="pt-2 border-top">
                                        <small class="text-muted d-block mb-1">Umumiy ball</small>
                                        <h4 class="fw-bold mb-0 
                                        {{ $index == 0 ? 'text-warning' : ($index == 1 ? 'text-secondary' : 'text-danger') }}">
                                            {{ $student['total_score'] }}
                                        </h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif -->

            {{-- Top 3 - Ultra Compact --}}
            @if(count($topStudents) > 0)
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-warning bg-opacity-75">
                    <h6 class="mb-0 fw-bold">
                        <i class="ri-trophy-line me-2"></i>
                        TOP 3 ENG FAOL O'QUVCHILAR
                    </h6>
                </div>
                <div class="card-body p-2">
                    <div class="row g-2">
                        @foreach($topStudents as $index => $student)
                        <div class="col-md-4">
                            <div class="card border-2 h-100
                            {{ $index == 0 ? 'border-warning bg-warning bg-opacity-10' : 
                               ($index == 1 ? 'border-secondary bg-secondary bg-opacity-10' : 
                               'border-danger bg-danger bg-opacity-10') }}">
                                <div class="card-body p-2">
                                    <div class="d-flex align-items-center mb-2">
                                        <div class="me-2" style="font-size: 1.5rem;">
                                            @if($index == 0) 🥇
                                            @elseif($index == 1) 🥈
                                            @else 🥉
                                            @endif
                                        </div>
                                        <div class="flex-grow-1">
                                            <h6 class="mb-0 fw-bold" style="font-size: 0.85rem;">
                                                {{ $student['name'] }}
                                            </h6>
                                        </div>
                                    </div>

                                    <div class="d-flex justify-content-around text-center mb-2">
                                        <div>
                                            <div class="badge bg-primary mb-1">{{ $student['reports_done'] }}</div>
                                            <small class="d-block text-muted" style="font-size: 0.7rem;">Hisobot</small>
                                        </div>
                                        <div>
                                            <div class="badge bg-success mb-1">{{ $student['tasks_done'] }}</div>
                                            <small class="d-block text-muted" style="font-size: 0.7rem;">Vazifa</small>
                                        </div>
                                        <div>
                                            <div class="badge bg-info mb-1">{{ $student['exam_score'] }}%</div>
                                            <small class="d-block text-muted" style="font-size: 0.7rem;">Test</small>
                                        </div>
                                    </div>

                                    <div class="text-center pt-2 border-top">
                                        <h5 class="mb-0 fw-bold
                                        {{ $index == 0 ? 'text-warning' : 
                                           ($index == 1 ? 'text-secondary' : 'text-danger') }}">
                                            {{ $student['total_score'] }}
                                        </h5>
                                        <small class="text-muted" style="font-size: 0.7rem;">ball</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- Top 3 - Minimal --}}
            <!-- @if(count($topStudents) > 0)
            <div class="alert alert-warning border-0 shadow-sm mb-4" style="background: linear-gradient(135deg, #fff3cd 0%, #ffe69c 100%);">
                <div class="d-flex align-items-center mb-2">
                    <i class="ri-trophy-fill text-warning me-2 fs-4"></i>
                    <strong>TOP 3 ENG FAOL O'QUVCHILAR</strong>
                </div>

                <div class="row g-2">
                    @foreach($topStudents as $index => $student)
                    <div class="col-md-4">
                        <div class="card border-0 shadow-sm h-100">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <span class="me-2" style="font-size: 1.3rem;">
                                            @if($index == 0) 🥇
                                            @elseif($index == 1) 🥈
                                            @else 🥉
                                            @endif
                                        </span>
                                        <div>
                                            <div class="fw-bold" style="font-size: 0.85rem;">{{ $student['name'] }}</div>
                                            <small class="text-muted">
                                                📊 {{ $student['reports_done'] }} |
                                                ✅ {{ $student['tasks_done'] }} |
                                                📝 {{ $student['exam_score'] }}%
                                            </small>
                                        </div>
                                    </div>
                                    <div class="text-end">
                                        <h5 class="mb-0 fw-bold text-success">{{ $student['total_score'] }}</h5>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif -->

            {{-- Students Table --}}
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>№</th>
                            <th>F.I.O</th>
                            <th class="text-center">HISOBOTLAR</th>
                            <th class="text-center">JAMI VAZIFALAR</th>
                            <th class="text-center">BAJARILDI</th>
                            <th class="text-center">BAJARILISH %</th>
                            <th class="text-center">AMALLAR</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td><strong>{{ $student->first_name }} {{ $student->last_name }}</strong></td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $student->total_reports }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-info">{{ $student->total_tasks }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-success">{{ $student->completed_tasks }}</span>
                            </td>
                            <td class="text-center">
                                <strong>{{ $student->task_completion_rate }}%</strong>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-primary"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#detail-{{ $student->id }}">
                                    <i class="ri-eye-line"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="collapse" id="detail-{{ $student->id }}">
                            <td colspan="7" class="bg-light p-3">
                                <strong>Vazifalar:</strong>
                                @if(count($student->tasks) > 0)
                                <div class="row g-2 mt-2">
                                    @foreach($student->tasks as $task)
                                    <div class="col-md-2">
                                        <div class="card {{ $task['is_completed'] ? 'border-success' : 'border-danger' }}">
                                            <div class="card-body p-2 text-center">
                                                <div>{{ $task['emoji'] }}</div>
                                                <small>{{ $task['name'] }}</small>
                                                <br>
                                                <span class="badge {{ $task['is_completed'] ? 'bg-success' : 'bg-danger' }}">
                                                    {{ $task['is_completed'] ? '✓' : '✗' }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <p class="text-muted mt-2">Vazifalar yo'q</p>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <p class="text-muted">O'quvchilar topilmadi</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
    .bg-gradient-success {
        background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    }
</style>