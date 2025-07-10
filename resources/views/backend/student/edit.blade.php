<?php

use App\Models\User;

?>

@extends('backend.layouts.main')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('backend.student.index') }}">O'quvchilar</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tahrirlash</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">O'quvchilar</h5> <small class="text-body float-end"></small>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('backend.student.update', $model->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text" name="first_name" class="form-control"
                                           id="basic-default-fullname"
                                           placeholder="Ism" required value="{{$model->first_name}}">
                                    <label for="basic-default-fullname">Ism</label>
                                </div>

                            </div>
                            <div class="col-md-6">
                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text" name="last_name" class="form-control" id="basic-default-fullname"
                                           placeholder="Familya" required value="{{$model->last_name}}">
                                    <label for="basic-default-fullname">Familya</label>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-md-6 col-sm-12 mb-6">
                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text" name="name" class="form-control" id="basic-default-fullname"
                                           placeholder="Login" required value="{{$model->name}}">
                                    <label for="basic-default-fullname">Login</label>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6 col-sm-12 mb-6">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">UZ (+998)</span>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="phone-number-mask" class="form-control phone-number-mask"
                                               placeholder="90 202 555 01" name="phone" value="{{$model->phone}}">
                                        <label for="phone-number-mask">O'quvchi ota yoki onasi telefon raqami</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-6">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="text" name="email" id="basic-default-email"
                                                   class="form-control"
                                                   placeholder="Email" aria-label="john.doe"
                                                   aria-describedby="basic-default-email2"
                                                   required value="{{$model->email}}">
                                            <label for="basic-default-email">Email</label>
                                        </div>
                                        <span class="input-group-text" id="basic-default-email2">@example.com</span>
                                    </div>
                                    <div class="form-text"> Ota yoki onasini email pochtasini kiriting</div>
                                </div>
                            </div>
                            <div class="col-md-6 mb-6">
                                <div class="form-floating form-floating-outline">
                                    <select id="select2Basic" class="select2 form-select form-select-lg"
                                            data-allow-clear="true" name="classes_id" required>
                                        @foreach(\App\Models\User::getClassesList() as $key => $item)
                                            <option
                                                value="{{$item->id}}" {{$model->classes_id == $item->id ? 'selected' : ''}}>{{$item->name}}</option>
                                        @endforeach
                                    </select>
                                    <label for="select2Basic">Basic</label>
                                </div>
                            </div>
                        </div>


                        <div class="mb-6">
                            <div class="row">
                                <div class="col-sm-6 p-6 pt-sm-0">
                                    <div class="text-light small fw-medium mb-4">Hodimning satatusi active yoki no
                                        active
                                    </div>
                                    <div class="switches-stacked">
                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input" value="{{ User::STATUS_ACTIVE }}"
                                                   name="status" {{$model->status == User::STATUS_ACTIVE ? 'checked' : ''}}>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">Faol</span>
                                        </label>

                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input"
                                                   value="{{ User::STATUS_IN_ACTIVE }}" name="status" {{$model->status == User::STATUS_IN_ACTIVE ? 'checked' : ''}}>
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
