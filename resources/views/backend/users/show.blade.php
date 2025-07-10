<?php
use App\Models\User;
?>

@extends('backend.layouts.main')

@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('backend.user.index') }}">O'qituvchilar va Kordinatorlar</a></li>
            <li class="breadcrumb-item active" aria-current="page">Batafsil</li>
        </ol>
    </nav>
    <div class="row">
        <!-- Teacher Sidebar -->
        <div class="col-xl-4 col-lg-5 col-md-5 order-1 order-md-0">
            <!-- Teacher Card -->
            <div class="card mb-6">
                <div class="card-body pt-12">
                    <div class="user-avatar-section">
                        <div class=" d-flex align-items-center flex-column">
                            <img src="{{ !is_null($model->img) ? asset($model->img) : asset('images/staticImages/defaultAvatar.png') }}"
                                width="120" height="120" alt="{{ asset($model->img) }}" class="rounded-circle img-thumbnail"
                                style="border: 1px grey solid">
                            <div class="user-info text-center">
                                <h5>{{ $model->name }}</h5>
                                <span
                                    class="badge bg-label-danger rounded-pill">{{ User::getTypes($model->user_type) }}</span>
                            </div>
                        </div>
                    </div>

                    <h5 class="pb-4 border-bottom mb-4">Batafsil</h5>
                    <div class="info-container">
                        <ul class="list-unstyled mb-6">
                            <li class="mb-2">
                                <span class="fw-medium text-heading me-2">Ismi:</span>
                                <span>{{ $model->name }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="fw-medium text-heading me-2">Email:</span>
                                <span>{{ $model->email }}</span>
                            </li>
                            <li class="mb-2">
                                <span class="fw-medium text-heading me-2">Status:</span>
                                @if ($model->status == User::STATUS_ACTIVE)
                                    <span
                                        class="badge bg-label-success badge-sm rounded-pill">{{ User::getStatus($model->status) }}</span>
                                @else
                                    <span
                                        class="badge bg-label-danger badge-sm rounded-pill">{{ User::getStatus($model->status) }}</span>
                                @endif
                            </li>
                            <li class="mb-2">
                                <span class="fw-medium text-heading me-2">Role:</span>
                                <span>{{ User::getTypes($model->user_type) }}</span>
                            </li>
                        </ul>
                        <div class="d-flex justify-content-center">
                            <a href="javascript:;" class="btn btn-primary me-4">Edit</a>
                        </div>
                    </div>
                </div>
            </div>
            <!-- /Teacher Card -->
        </div>
        <!--/ Teacher Sidebar -->


        <!-- Teacher Content -->
        <div class="col-xl-8 col-lg-7 col-md-7 order-0 order-md-1">
            <!-- Teacher Tabs -->
            <div class="nav-align-top">
                <ul class="nav nav-pills flex-column flex-md-row mb-6 row-gap-2">
                    <li class="nav-item">
                        <a class="nav-link active" href="javascript:void(0);">
                            <i class="ri-lock-2-line me-2"></i>Profile
                        </a>
                    </li>
                </ul>
            </div>
            <!--/ Teacher Tabs -->

            <!-- Change Password -->
            <div class="card mb-6">
                <h5 class="card-header">Parolni o'zgartirish</h5>
                <div class="card-body">
                    <form id="formChangePassword" method="GET" onsubmit="return false">
                        <div class="alert alert-warning alert-dismissible" role="alert">
                            <h5 class="alert-heading mb-1">Ensure that these requirements are met</h5>
                            <span>Minimum 8 characters long, uppercase & symbol</span>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                        <div class="row gx-5">
                            <div class="mb-3 col-12 col-sm-6 form-password-toggle">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="password" id="newPassword" name="newPassword"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            disabled>
                                        <label for="newPassword">Yangi parol</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer text-heading"><i
                                            class="ri-eye-off-line"></i></span>
                                </div>
                            </div>

                            <div class="mb-3 col-12 col-sm-6 form-password-toggle">
                                <div class="input-group input-group-merge">
                                    <div class="form-floating form-floating-outline">
                                        <input class="form-control" type="password" name="confirmPassword"
                                            id="confirmPassword"
                                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                            disabled>
                                        <label for="confirmPassword">Parolni takrorlang</label>
                                    </div>
                                    <span class="input-group-text cursor-pointer text-heading"><i
                                            class="ri-eye-off-line"></i></span>
                                </div>
                            </div>
                            <div>
                                <button type="submit" class="btn btn-primary me-2">Parolni o'zgartirish</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <!--/ Change Password -->

        </div>
        <!--/ Teacher Content -->
    </div>
@endsection
