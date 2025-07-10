<?php

use App\Models\Teacher\Question;

$index = 1;
?>

@extends('teacher.layouts.main')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.quiz.index') }}">Savollar</a></li>
            <li class="breadcrumb-item active" aria-current="page">Yangi qo'shish</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Savollar</h5> <small class="text-body float-end"></small>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.question.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-6">

                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text" name="name" class="form-control" id="basic-default-fullname"
                                           placeholder="Savol matni" required>
                                    <label for="basic-default-fullname">Savol matni</label>
                                </div>
                            </div>
                            <!-- Basic -->
                            <div class="col-md-6 mb-6">
                                <div class="form-floating form-floating-outline">
                                    <select id="select2Basic" class="select2 form-select form-select-lg"
                                            data-allow-clear="true" name="quiz_id">
                                        <option value=""></option>
                                        @foreach(Question::getQuizList() as $key => $item)
                                            <option
                                                value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                    <label for="select2Basic">Quiz ni tanlang</label>
                                </div>
                            </div>
                            <div class="row"
                                 style="border: 1px solid #c2c0c0; border-radius: 10px; width: 98%; margin-left: 10px; padding: 7px; box-shadow: 3px 4px 8px #b3b2b2">
                                @for($i = 0; $i <=3; $i++)
                                    <div class="col-sm-6 ">
                                        <div class="text-light small fw-medium mb-4 text-center">
                                            <h6>Varyant {{$index}}</h6>
                                        </div>
                                        <div class="row" style="display: flex; align-items: center">
                                            <div class="col-sm-1">
                                                <div class="switches-stacked mb-6">
                                                    <label class="switch">
                                                        <input type="radio" class="switch-input"
                                                               name="is_correct"
                                                               {{$i == 0 ? "checked" : ""}} value="{{$i}}">
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
                                                           id="basic-default-fullname"
                                                           placeholder="Savol matni" required>
                                                    <label for="basic-default-fullname">Savol matni</label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                        <?php ++$index ?>
                                @endfor
                            </div>
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
                                                   value="{{ Question::STATUS_ACTIVE }}"
                                                   name="status" checked>
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
    </div>
@endsection

