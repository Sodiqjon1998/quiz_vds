<?php

use App\Models\User;

?>

@extends('teacher.layouts.main')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.quiz.index') }}">Quiz</a></li>
            <li class="breadcrumb-item active" aria-current="page">Yangi qo'shish</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Quiz</h5> <small class="text-body float-end"></small>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.quiz.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-6">

                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text" name="name" class="form-control" id="basic-default-fullname"
                                           placeholder="Quiz" required>
                                    <label for="basic-default-fullname">Sinf</label>
                                </div>
                            </div>
                            <!-- Basic -->
                            <div class="col-md-6 mb-6">
                                <div class="form-floating form-floating-outline">
                                    <select id="select2Basic" class="select2 form-select form-select-lg"
                                            data-allow-clear="true" name="classes_id">
                                        <option value="" selected></option>
                                        @foreach(\App\Models\Teacher\Quiz::getClassesList() as $key => $item)
                                            <option
                                                value="{{$item->id}}">{{$item->name}}</option>
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
                                                   value="{{ \App\Models\Teacher\Quiz::STATUS_ACTIVE }}"
                                                   name="status" checked>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">Faol</span>
                                        </label>

                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input"
                                                   value="{{ \App\Models\Teacher\Quiz::STATUS_IN_ACTIVE }}"
                                                   name="status">
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

