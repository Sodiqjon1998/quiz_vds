@php
    use App\Models\Student\Quiz;
    use Carbon\Carbon;
    use App\Models\Exam;
@endphp
@extends('student.layouts.main')


@section('content')
    <div class="row">
        @foreach ($model as $item)
            @php
                $currentDate = Carbon::now()->format('Y-m-d');
                // Endi $item->date va $item->number to'g'ridan-to'g'ri mavjud bo'lishi kerak
                $dateToCompare = isset($item->date) ? Carbon::parse($item->date)->format('Y-m-d') : null;
                $quizNumber = isset($item->number) ? $item->number : null;

                // Agar 'date' yoki 'number' null bo'lsa, bu elementni o'tkazib yuborish
                if (is_null($dateToCompare) || is_null($quizNumber)) {
                    continue;
                }
            @endphp


            <div class="col-sm-6 col-lg-3">
                <div class="card card-border-shadow-primary h-100">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2">
                            <div class="avatar me-4">
                                <span class="avatar-initial rounded-3 bg-label-primary">
                                    <i class="ri-hand-coin-fill ri-24px"></i>
                                </span>
                            </div>
                            <h6 class="mb-0">{{ $item->subjectName }}</h6>
                        </div>
                        <h6 class="mb-0 fw-normal">
                            <?php
                            $quizNumber = Quiz::getAttachmentById($item->quizId)->number;
                            $examNumber = Exam::where('quiz_id', $item->quizId)
                                ->where('user_id', \Auth::user()->id)
                                ->where('subject_id', $item->subjectId)
                                ->count();
                            ?>

                            @if ($dateToCompare >= $currentDate)
                                @if ($quizNumber > $examNumber)
                                    <a href="{{ route('student.quiz.show', ['id' => $item->quizId, 'subjectId' => $item->subjectId]) }}"
                                        class="badge bg-label-info">
                                        {{ $item->quizName }}
                                    </a>
                                @else
                                    {{ $item->quizName }}
                                    <span>
                                        Urinishingiz qolmadi
                                    </span>
                                @endif
                            @else
                                {{ $item->quizName }}
                            @endif

                        </h6>
                        <p class="mb-0">

                            @if ($dateToCompare >= $currentDate)
                                <div class="sk-fold sk-primary" style="width: 20px; float: right;">
                                    <div class="sk-fold-cube" style="font-size: 2px"></div>
                                    <div class="sk-fold-cube" style="font-size: 2px"></div>
                                    <div class="sk-fold-cube" style="font-size: 2px"></div>
                                    <div class="sk-fold-cube" style="font-size: 2px"></div>
                                </div>
                            @endif

                            <span class="me-1 fw-medium">{{ $dateToCompare }}</span>
                            <span class="text-muted">
                                <i class="ri-attachment-2"></i>
                                {{ $quizNumber - $examNumber }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection
