@extends('teacher.layouts.main')
@use(App\Models\Teacher\Question;use App\Models\Teacher\Quiz;use App\Models\User)

@section('content')
    <style>
        table,
        tr,
        th,
        td {
            padding: 7px !important;
            font-size: 12px;
        }
    </style>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.quiz.index') }}">Quiz</a></li>
            <li class="breadcrumb-item active" aria-current="page">Batafsil</li>
        </ol>
    </nav>


    <div class="card">
        <div class="card-header">
            {{ $model->name }}
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-sm text-center"
                style="border: 1px solid rgb(201, 198, 198);">
                <tr>
                    <th>Id</th>
                    <th>{{ $model->id }}</th>
                </tr>
                <tr>
                    <th>Savol matni</th>
                    <th>{{ $model->name }}</th>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>
                        {{ Question::getStatus($model->status) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>
    <br>
    <br>
    <hr>
    <h1 class="badge bg-label-hover-success text-center" style="font-size: 15px;">Savollar</h1>
    <div class="card">
        <div class="card-body">
            <div class="row">
                @foreach ($questions as $k => $question)
                    <div class="col-md-6">
                        <br>
                        <hr>
                        <h3 class="badge bg-label-hover-primary" style="font-size: 15px;">
                            {{ ++$k }}) {{ $question->name }}
                        </h3>
                        <div class="row"
                            style="border: 1px solid #c2c0c0; border-radius: 10px; width: 98%; margin-left: 10px; padding: 7px; box-shadow: 3px 4px 8px #b3b2b2">
                            <?php $options = Quiz::getOptionById($question->id); ?>
                            @foreach ($options as $key => $option)
                                <div class="col-sm-6 ">
                                    <div class="text-light small fw-medium mb-4 text-center">
                                        <h6>Varyant {{ ++$key }}</h6>
                                    </div>
                                    <div class="row" style="display: flex; align-items: center">
                                        <div class="col-sm-1">
                                            <div class="switches-stacked mb-6">
                                                <label class="switch">
                                                    <input type="radio" class="switch-input"
                                                        name="switches-stacked-radio{{ $k }}"
                                                        {{ $option->is_correct == 1 ? 'checked' : '' }} disabled>
                                                    <span class="switch-toggle-slider">
                                                        <span class="switch-on"></span>
                                                        <span class="switch-off"></span>
                                                    </span>
                                                </label>
                                            </div>
                                        </div>

                                        <div class="col-md-11">
                                            <div class="form-floating form-floating-outline mb-6">
                                                <input type="text" name="names[]" disabled class="form-control"
                                                    id="basic-default-fullname" placeholder="Savol matni" required
                                                    value="{{ $option->name }}">
                                                <label for="basic-default-fullname">Savol matni</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
