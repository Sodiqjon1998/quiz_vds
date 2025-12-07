<div>
    <style>
        :root {
            --yuksalish-orange: #F58025;
            --yuksalish-dark: #212529;
            --yuksalish-gray: #f8f9fa;
        }

        .btn-yuksalish {
            background-color: var(--yuksalish-orange);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 0.5rem 1.2rem;
            font-weight: 500;
            transition: all 0.2s;
        }

        .btn-yuksalish:hover {
            background-color: #d96d1b;
            color: white;
            transform: translateY(-1px);
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.04);
        }

        .bg-gradient-orange {
            background: linear-gradient(135deg, #F58025 0%, #ff9f5a 100%);
        }

        /* Stats Card Professional */
        .stats-card {
            background: white;
            border: 1px solid #eee;
            transition: all 0.3s;
        }

        .stats-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.05);
            border-color: var(--yuksalish-orange);
        }

        .stats-icon-box {
            width: 50px;
            height: 50px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
        }

        /* Tabs */
        .nav-tabs {
            border-bottom: 2px solid #eee;
        }

        .nav-tabs .nav-link {
            color: #6c757d;
            font-weight: 600;
            border: none;
            border-bottom: 3px solid transparent;
            padding: 12px 20px;
            transition: all 0.2s;
        }

        .nav-tabs .nav-link:hover {
            color: var(--yuksalish-orange);
        }

        .nav-tabs .nav-link.active {
            color: var(--yuksalish-orange);
            background: transparent;
            border-bottom: 3px solid var(--yuksalish-orange);
        }

        /* Mobile Card */
        .mobile-student-card {
            border-left: 4px solid var(--yuksalish-orange);
            background: white;
            margin-bottom: 1rem;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
        }
    </style>

    {{-- HEADER --}}
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-gradient-orange text-white py-3">
            <h4 class="mb-0 fw-bold text-white">
                <i class="ri-bar-chart-box-line me-2"></i> O'quvchilar Faoliyati (Admin)
            </h4>
        </div>

        <div class="card-body p-4">
            {{-- Filters --}}
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold text-muted">Sinfni tanlang *</label>
                    <select wire:model.live="classFilter" class="form-select border-2" style="border-color: var(--yuksalish-orange);">
                        <option value="">-- Tanlang --</option>
                        @foreach($classes as $class)
                        <option value="{{ $class->id }}">{{ $class->name }}</option>
                        @endforeach
                    </select>
                </div>

                @if($classFilter)
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted">Dan</label>
                    <input type="date" wire:model.live="dateFrom" class="form-control">
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-semibold text-muted">Gacha</label>
                    <input type="date" wire:model.live="dateTo" class="form-control">
                </div>
                <div class="col-md-2 d-flex align-items-end">
                    <button wire:click="$refresh" class="btn btn-yuksalish w-100"><i class="ri-refresh-line me-1"></i> Yangilash</button>
                </div>
                @endif
            </div>

            @if($showEmptyMessage)
            <div class="alert alert-warning border-0 shadow-sm d-flex align-items-center">
                <i class="ri-information-line fs-1 me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">Sinf tanlanmagan</h5>
                    <p class="mb-0">Hisobotlarni ko'rish uchun yuqorida sinfni tanlang.</p>
                </div>
            </div>

            @else

            {{-- STATISTICS CARDS --}}
            @if($statistics)
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stats-icon-box bg-primary bg-opacity-10 text-primary me-3">
                                <i class="ri-file-list-3-line"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $statistics['total_reports'] }}</h4>
                                <small class="text-muted">Jami hisobotlar</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stats-icon-box bg-info bg-opacity-10 text-info me-3">
                                <i class="ri-task-line"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $statistics['total_tasks'] }}</h4>
                                <small class="text-muted">Jami vazifalar</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stats-icon-box bg-success bg-opacity-10 text-success me-3">
                                <i class="ri-checkbox-circle-line"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $statistics['completed_tasks'] }}</h4>
                                <small class="text-muted">Bajarildi</small>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-6 col-md-3">
                    <div class="card stats-card h-100">
                        <div class="card-body d-flex align-items-center p-3">
                            <div class="stats-icon-box bg-warning bg-opacity-10 text-warning me-3">
                                <i class="ri-pie-chart-line"></i>
                            </div>
                            <div>
                                <h4 class="fw-bold mb-0 text-dark">{{ $statistics['completion_rate'] }}%</h4>
                                <small class="text-muted">Samaradorlik</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- TOP 3 STUDENTS --}}
            @if(count($topStudents) > 0)
            <div class="card border-0 shadow-sm mb-4 bg-light">
                <div class="card-header bg-transparent border-0 pb-0">
                    <h6 class="fw-bold text-dark mb-0"><i class="ri-trophy-fill text-warning me-2"></i> TOP 3 ENG FAOL O'QUVCHILAR</h6>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        @foreach($topStudents as $index => $student)
                        <div class="col-md-4">
                            <div class="card border-0 shadow-sm h-100 position-relative overflow-hidden">
                                <div class="position-absolute top-0 end-0 p-2 opacity-25" style="font-size: 4rem; line-height: 0.8;">
                                    @if($index == 0) 🥇 @elseif($index == 1) 🥈 @else 🥉 @endif
                                </div>
                                <div class="card-body position-relative">
                                    <h6 class="fw-bold mb-1 text-truncate" style="max-width: 85%;">{{ $student['name'] }}</h6>
                                    <div class="d-flex align-items-center gap-2 mb-2 text-muted small">
                                        <span><i class="ri-file-text-line"></i> {{ $student['reports_done'] }}</span>
                                        <span><i class="ri-checkbox-circle-line"></i> {{ $student['tasks_done'] }}</span>
                                        <span><i class="ri-edit-line"></i> {{ $student['exam_score'] }}%</span>
                                    </div>
                                    <div class="border-top pt-2 mt-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">Umumiy ball</small>
                                            <span class="fw-bold text-warning fs-5">{{ $student['total_score'] }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif

            {{-- TABS --}}
            <ul class="nav nav-tabs mb-4">
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'submitted' ? 'active' : '' }}"
                        wire:click="setTab('submitted')">
                        <i class="ri-check-double-line me-1"></i> Topshirganlar
                        <span class="badge bg-success-subtle text-success ms-1 rounded-pill">{{ $students->count() }}</span>
                    </button>
                </li>
                <li class="nav-item">
                    <button class="nav-link {{ $activeTab === 'missing' ? 'active' : '' }}"
                        wire:click="setTab('missing')">
                        <i class="ri-close-circle-line me-1"></i> Topshirmaganlar
                    </button>
                </li>
            </ul>

            {{-- STUDENTS LIST (DESKTOP) --}}
            <div class="d-none d-md-block table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="bg-light">
                        <tr>
                            <th class="text-center" style="width: 50px;">#</th>
                            <th>F.I.O</th>
                            <th class="text-center">Hisobotlar</th>
                            <th class="text-center">Vazifalar (Jami)</th>
                            <th class="text-center">Bajarildi</th>
                            <th class="text-center">Foiz</th>
                            <th class="text-center" style="width: 100px;">Batafsil</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($students as $index => $student)
                        <tr>
                            <td class="text-center fw-bold">{{ $index + 1 }}</td>
                            <td><span class="fw-bold text-dark">{{ $student->first_name }} {{ $student->last_name }}</span></td>
                            <td class="text-center"><span class="badge bg-primary rounded-pill">{{ $student->total_reports }}</span></td>
                            <td class="text-center"><span class="badge bg-info rounded-pill">{{ $student->total_tasks }}</span></td>
                            <td class="text-center"><span class="badge bg-success rounded-pill">{{ $student->completed_tasks }}</span></td>
                            <td class="text-center">
                                @php $color = $student->task_completion_rate >= 80 ? 'success' : ($student->task_completion_rate >= 50 ? 'warning' : 'danger'); @endphp
                                <span class="fw-bold text-{{ $color }}">{{ $student->task_completion_rate }}%</span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-light border text-primary" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-{{ $student->id }}">
                                    <i class="ri-arrow-down-s-line"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="collapse" id="collapse-{{ $student->id }}">
                            <td colspan="7" class="bg-light p-3">
                                @if(count($student->tasks_list) > 0)
                                <div class="d-flex flex-wrap gap-2">
                                    @foreach($student->tasks_list as $task)
                                    <div class="bg-white border rounded px-3 py-2 shadow-sm d-flex align-items-center gap-3" style="font-size: 0.9rem;">
                                        {{-- Vazifa sanasi --}}
                                        <span class="badge bg-light text-muted border py-1 px-2">
                                            <i class="ri-calendar-event-line me-1"></i> {{ \Carbon\Carbon::parse($task['date'])->format('d.m.Y') }}
                                        </span>

                                        <span class="text-dark fw-medium">{{ $task['name'] }}</span>

                                        {{-- Holati --}}
                                        @if($task['is_completed'])
                                        <span class="text-success d-flex align-items-center"><i class="ri-checkbox-circle-fill me-1 fs-5"></i> Bajarildi</span>
                                        @else
                                        <span class="text-danger d-flex align-items-center"><i class="ri-close-circle-fill me-1 fs-5"></i> Bajarilmadi</span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @else
                                <div class="text-center py-2 text-muted small">Vazifalar mavjud emas</div>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-4 text-muted">O'quvchilar topilmadi</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- STUDENTS LIST (MOBILE) --}}
            <div class="d-md-none">
                @forelse($students as $student)
                <div class="mobile-student-card p-3">
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-2 border-bottom">
                        <h6 class="fw-bold mb-0 text-dark">{{ $student->first_name }} {{ $student->last_name }}</h6>
                        <span class="badge {{ $student->task_completion_rate >= 70 ? 'bg-success' : 'bg-warning' }}">
                            {{ $student->task_completion_rate }}%
                        </span>
                    </div>

                    <div class="row text-center g-2 mb-3">
                        <div class="col-4">
                            <div class="bg-light rounded p-2">
                                <div class="fw-bold text-primary">{{ $student->total_reports }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Hisobot</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-light rounded p-2">
                                <div class="fw-bold text-info">{{ $student->total_tasks }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Vazifa</small>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="bg-light rounded p-2">
                                <div class="fw-bold text-success">{{ $student->completed_tasks }}</div>
                                <small class="text-muted" style="font-size: 0.7rem;">Bajarildi</small>
                            </div>
                        </div>
                    </div>

                    <button class="btn btn-sm btn-light w-100 border text-muted" type="button" data-bs-toggle="collapse" data-bs-target="#mob-collapse-{{ $student->id }}">
                        Batafsil vazifalar <i class="ri-arrow-down-s-line ms-1"></i>
                    </button>

                    <div class="collapse mt-2" id="mob-collapse-{{ $student->id }}">
                        <div class="bg-light rounded p-2">
                            @if(count($student->tasks_list) > 0)
                            @foreach($student->tasks_list as $task)
                            <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom last-border-0">
                                <div class="d-flex align-items-center gap-2 overflow-hidden">
                                    <i class="ri-checkbox-blank-circle-line text-muted"></i>
                                    <span class="text-truncate small">{{ $task['name'] }}</span>
                                </div>
                                @if($task['is_completed'])
                                <i class="ri-checkbox-circle-fill text-success fs-5"></i>
                                @else
                                <i class="ri-close-circle-fill text-danger fs-5"></i>
                                @endif
                            </div>
                            @endforeach
                            @else
                            <span class="text-muted small d-block text-center">Vazifalar mavjud emas</span>
                            @endif
                        </div>
                    </div>
                </div>
                @empty
                <div class="text-center py-5 text-muted">Ma'lumot topilmadi</div>
                @endforelse
            </div>

            @endif
        </div>
    </div>
</div>