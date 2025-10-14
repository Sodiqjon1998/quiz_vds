<?php

use App\Models\User;

?>

@extends('backend.layouts.main')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('backend.user.index') }}">O'qituvchilar va Kordinatorlar</a>
            </li>
            <li class="breadcrumb-item active" aria-current="page">Yangi qo'shish</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Foydalanuvchilar</h5> <small class="text-body float-end"></small>
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
                    <form method="POST" action="{{ route('backend.user.store') }}">
                        @csrf
                        <div class="form-floating form-floating-outline mb-6">
                            <input type="text" name="first_name" class="form-control" id="basic-default-firstname"
                                   placeholder="Ism" required>
                            <label for="basic-default-firstname">Ism</label>
                        </div>
                        <div class="form-floating form-floating-outline mb-6">
                            <input type="text" name="last_name" class="form-control" id="basic-default-lastname"
                                   placeholder="Familya" required>
                            <label for="basic-default-lastname">Familya</label>
                        </div>
                        <div class="row">
                            <div class="col-xl-6 col-md-6 col-sm-12 mb-6">
                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text" name="name" class="form-control" id="basic-default-login"
                                           placeholder="Login" required>
                                    <label for="basic-default-login">Login</label>
                                </div>
                            </div>
                            <div class="col-xl-6 col-md-6 col-sm-12 mb-6">
                                <div class="input-group input-group-merge">
                                    <span class="input-group-text">UZ (+998)</span>
                                    <div class="form-floating form-floating-outline">
                                        <input type="text" id="phone-number-mask" class="form-control phone-number-mask"
                                               placeholder="90 202 555 01" name="phone">
                                        <label for="phone-number-mask">Telefon raqam</label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-6">
                                    <div class="input-group input-group-merge">
                                        <div class="form-floating form-floating-outline">
                                            <input type="email" name="email" id="basic-default-email"
                                                   class="form-control" placeholder="Email" aria-label="john.doe"
                                                   aria-describedby="basic-default-email2" required>
                                            <label for="basic-default-email">Email</label>
                                        </div>
                                        <span class="input-group-text" id="basic-default-email2">@example.com</span>
                                    </div>
                                    <div class="form-text"> Harflar, raqamlar va nuqtalardan foydalanishingiz mumkin
                                    </div>
                                </div>
                            </div>
                            {{-- Fan tanlash maydoni --}}
                            <div class="col-md-6 mb-6" id="subject-selection-container" style="display: none">
                                <div class="form-floating form-floating-outline">
                                    <select id="select2Basic" class="select2 form-select form-select-lg"
                                            data-allow-clear="true" name="subject_id">
                                        <option value="">Fan nomini tanlang
                                        </option> {{-- Tanlanmagan holat uchun bo'sh qiymat --}}
                                        @foreach (User::getSubjectsList() as $key => $item)
                                            <option value="{{ $item->id }}">{{ $item->name }}</option>
                                        @endforeach
                                    </select>
                                    <label for="select2Basic">Fan nomini tanlang</label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-6">
                            <div class="row">
                                <div class="col-sm-6 p-6 pt-sm-0">
                                    <div class="text-light small fw-medium mb-4">Xodimning lavozimini kiriting</div>
                                    <div class="switches-stacked">
                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input user-type-radio"
                                                   value="{{ User::TYPE_ADMIN }}" name="user_type" checked>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">Super admin</span>
                                        </label>

                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input user-type-radio"
                                                   value="{{ User::TYPE_TEACHER }}" name="user_type">
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">O'qituvchi</span>
                                        </label>

                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input user-type-radio"
                                                   value="{{ User::TYPE_KOORDINATOR }}" name="user_type">
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">Koordinator</span>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-sm-6 p-6 pt-sm-0">
                                    <div class="text-light small fw-medium mb-4">Xodimning statusi active yoki no active
                                    </div>
                                    <div class="switches-stacked">
                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input" value="{{ User::STATUS_ACTIVE }}"
                                                   name="status" checked>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">Faol</span>
                                        </label>

                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input"
                                                   value="{{ User::STATUS_IN_ACTIVE }}" name="status">
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">Bloklangan</span>
                                        </label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary float-end">Saqlash</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const radioButtons = document.querySelectorAll('.user-type-radio');

            radioButtons.forEach(function (radio) {
                radio.addEventListener('change', function () {
                    console.log('Tanlangan qiymat:', this.value);

                    // Fan maydonini ko'rsatish/yashirish
                    const subjectContainer = document.getElementById('subject-selection-container');
                    const subjectSelect = document.getElementById('select2Basic');

                    if (this.value == '{{ User::TYPE_TEACHER }}') {
                        subjectContainer.style.display = 'block';
                        subjectSelect.required = true;
                    } else {
                        subjectContainer.style.display = 'none';
                        subjectSelect.required = false;
                        subjectSelect.value = '';
                    }
                });
            });
    </script>
        });
@endsection


