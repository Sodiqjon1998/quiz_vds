<?php

use App\Models\User;
use App\Models\Teacher\Question;

$index = 1;
?>

@extends('teacher.layouts.main')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.quiz.index') }}">Quiz</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tahrirlash</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Quiz</h5> <small class="text-body float-end"></small>
                    <div class="debug">
                        @if ($errors->any())
                            <div class="alert alert-danger mb-3">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if (session('success'))
                            <div class="alert alert-success mb-3">
                                {{ session('success') }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.quiz.update', $model->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-6">
                                <input type="hidden" name="subject_id" value="{{ $model->subject_id }}" id="">

                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text" name="name" class="form-control" id="basic-default-fullname"
                                        placeholder="Quiz" required value="{{ $model->name }}">
                                    <label for="basic-default-fullname">Sinf</label>
                                </div>
                            </div>
                            <!-- Basic -->
                            <div class="col-md-6 mb-6">
                                <div class="form-floating form-floating-outline">
                                    <select id="select2Basic" class="select2 form-select form-select-lg"
                                        data-allow-clear="true" name="classes_id">
                                        @foreach (\App\Models\Teacher\Quiz::getClassesList() as $key => $item)
                                            <option value="{{ $item->id }}"
                                                {{ $model->classes_id == $item->id ? 'selected' : '' }}>{{ $item->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <label for="select2Basic">Sinf nomi</label>
                                </div>
                            </div>
                        </div>


                        <div class="mb-6">
                            <div class="row">
                                <div class="col-sm-6 p-6 pt-sm-0">
                                    <div class="text-light small fw-medium mb-4">
                                        Quiz satatusi active yoki no active
                                    </div>
                                    <div class="switches-stacked">
                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input"
                                                value="{{ \App\Models\Teacher\Quiz::STATUS_ACTIVE }}" name="status"
                                                {{ $model->status == \App\Models\Teacher\Quiz::STATUS_ACTIVE ? 'checked' : '' }}>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">Faol</span>
                                        </label>

                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input"
                                                value="{{ \App\Models\Teacher\Quiz::STATUS_IN_ACTIVE }}" name="status"
                                                {{ $model->status == \App\Models\Teacher\Quiz::STATUS_IN_ACTIVE ? 'checked' : '' }}>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">Bloklangan</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary float-end">Saqlash</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="row">
                @foreach ($questions as $j => $question)
                <div class="col-md-12">
                    <div class="card mt-6">
                        <div class="card-header">
                            <strong>{{ $j + 1 }}</strong>
                        </div>
                        <div class="card-body">
                            <form method="POST" action="{{ route('teacher.question.store') }}">
                                @csrf

                                <div class="col-md-12 mb-6">

                                    <div class="form-floating form-floating-outline mb-6">
                                        <input type="text" name="name" class="form-control" id="basic-default-fullname"
                                            placeholder="Savol matni" required value="{{ $question->name }}">
                                        <label for="basic-default-fullname">Savol matni</label>
                                    </div>
                                </div>

                                <div class="row"
                                    style="border: 1px solid #c2c0c0; border-radius: 10px; width: 98%; margin-left: 10px; padding: 7px; box-shadow: 3px 4px 8px #b3b2b2">
                                    @for ($i = 0; $i <= 3; $i++)
                                        <div class="col-sm-6 ">
                                            <div class="text-light small fw-medium mb-4 text-center">
                                                <h6>Varyant {{ $index }}</h6>
                                            </div>
                                            <div class="row" style="display: flex; align-items: center">
                                                <div class="col-sm-1">
                                                    <div class="switches-stacked mb-6">
                                                        <label class="switch">
                                                            <input type="radio" class="switch-input" name="is_correct"
                                                                {{ $i == 0 ? 'checked' : '' }} value="{{ $i }}">
                                                            <span class="switch-toggle-slider">
                                                                <span class="switch-on"></span>
                                                                <span class="switch-off"></span>
                                                            </span>
                                                        </label>
                                                    </div>
                                                </div>

                                                <div class="col-md-11">
                                                    <div class="form-floating form-floating-outline mb-6">
                                                        <input type="text" name="names[]" class="form-control"
                                                            id="basic-default-fullname" placeholder="Savol matni" required
                                                            value="{{ $question->options[$i]->name ?? '' }}">
                                                        <label for="basic-default-fullname">Savol matni</label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <?php ++$index; ?>
                                    @endfor
                                </div>


                                <div class="mb-6">
                                    <div class="row">
                                        <div class="col-sm-6 p-6 pt-sm-0 mt-4">
                                            <div class="text-light small fw-medium mb-4">
                                                <h6 class="display-6">
                                                    Savol satatusi active yoki no active
                                                </h6>
                                            </div>
                                            <div class="switches-stacked">
                                                <label class="switch switch-square">
                                                    <input type="radio" class="switch-input"
                                                        value="{{ Question::STATUS_ACTIVE }}" name="status" checked>
                                                    <span class="switch-toggle-slider">
                                                        <span class="switch-on"></span>
                                                        <span class="switch-off"></span>
                                                    </span>
                                                    <span class="switch-label">Faol</span>
                                                </label>

                                                <label class="switch switch-square">
                                                    <input type="radio" class="switch-input"
                                                        value="{{ Question::STATUS_IN_ACTIVE }}" name="status">
                                                    <span class="switch-toggle-slider">
                                                        <span class="switch-on"></span>
                                                        <span class="switch-off"></span>
                                                    </span>
                                                    <span class="switch-label">Bloklangan</span>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary float-end">Saqlash</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
