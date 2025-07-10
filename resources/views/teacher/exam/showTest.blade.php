@php use App\Models\Option; @endphp
@php use App\Models\User; @endphp
@extends('teacher.layouts.main')


@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item active" aria-current="page">Testlar</li>
        </ol>
    </nav>


    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 style="font-family: cursive">
                        {{ \App\Models\Subjects::getSubjectById($exam->subject_id)->name }} /🧑
                        {{ User::getStudentFullNameById($exam->user_id) }}
                    </h3>
                </div>
                <div class="card-body">
                    <table class="table-sm w-100">
                        @foreach ($examAnswers as $k => $answer)
                            <tr>
                                <td>
                                    <strong>
                                        {{ ++$k }})
                                    </strong>
                                    {{ \App\Models\Question::getQuestionById($answer->question_id)->name }}
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    @if (Option::getOptionById($answer->option_id)->is_correct == 1)
                                        <p class="p-2" style="background-color: #9cf59c; cursor: pointer;">
                                            <i class="fa fa-check marker"></i>
                                            {{ Option::getOptionById($answer->option_id)->name }}
                                        </p>
                                    @else
                                        <p class="p-2" style="background-color: #ffb8b8; cursor: pointer;">
                                            <i class="fa fa-times"></i>
                                            {{ Option::getOptionById($answer->option_id)->name }}
                                        </p>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
