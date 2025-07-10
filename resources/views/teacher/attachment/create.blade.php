<?php

use App\Models\User;

?>

@extends('teacher.layouts.main')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('teacher.attachment.index') }}">Quiz uchun urinishlar</a></li>
            <li class="breadcrumb-item active" aria-current="page">Yangi qo'shish</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Quiz uchun urinishlar</h5> <small class="text-body float-end"></small>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('teacher.attachment.store') }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-6">

                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="number" name="number" class="form-control" id="basic-default-fullname"
                                           placeholder="Quiz uchun urinishlar soni" required>
                                    <label for="basic-default-fullname">Quiz uchun urinishlar soni</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-6">
                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="date" name="date" class="form-control" id="basic-default-fullname"
                                           placeholder="Quiz uchun urinishlar vaqti" required>
                                    <label for="basic-default-fullname">Quiz uchun vaqt</label>
                                </div>
                            </div>
                            <!-- Basic -->
                            <div class="col-md-6 mb-6">
                                <div class="form-floating form-floating-outline">
                                    <select id="select2Basic" class="select2 form-select form-select-lg"
                                            data-allow-clear="true" name="quiz_id">
                                        <option value="" selected></option>
                                        @foreach(\App\Models\Teacher\Attachment::getQuizList() as $key => $item)
                                            <option
                                                value="{{$item->id}}">{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                    <label for="select2Basic">Quiz nomi</label>
                                </div>
                            </div>
                            <div class="col-md-6 mb-6">
                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="time" name="time" class="form-control" id="basic-default-fullname"
                                           placeholder="Quiz uchun urinishlar vaqti" required>
                                    <label for="basic-default-fullname">Quiz uchun vaqt</label>
                                </div>
                            </div>
                        </div>


                        <div class="mb-6">
                            <div class="row">
                                <div class="col-sm-6 p-6 pt-sm-0">
                                    <div class="text-light small fw-medium mb-4">
                                        Quiz uchun urinishlari satatusi active yoki no active
                                    </div>
                                    <div class="switches-stacked">
                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input"
                                                   value="{{ \App\Models\Teacher\Attachment::STATUS_ACTIVE }}"
                                                   name="status" checked>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">Faol</span>
                                        </label>

                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input"
                                                   value="{{ \App\Models\Teacher\Attachment::STATUS_IN_ACTIVE }}"
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

