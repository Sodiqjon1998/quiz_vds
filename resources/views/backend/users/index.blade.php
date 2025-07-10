<?php

use App\Models\User;

?>


@extends('backend.layouts.main')

@section('content')

    <style>
        table,
        tr,
        th,
        td {
            padding: 7px !important;
            font-size: 14px;
        }
    </style>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Bosh sahifa</a></li>
            <li class="breadcrumb-item active" aria-current="page">O'qituvchilar va Kordinatorlar</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            O'qituvchilar va Kordinatorlar
            <a href="{{ route('backend.user.create') }}" class="badge bg-label-success badge-lg rounded-pill">
                <i style="font-size: 16px" class="ri-add-circle-line" style="font-size: 15px"></i>
            </a>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover table-sm text-center"
                   style="border: 1px solid rgb(201, 198, 198);">
                <thead>
                <tr>
                    <th style="width: 30px">T/R</th>
                    <th>Rasm</th>
                    <th>F.I.SH</th>
                    <th>Email</th>
                    <th>Lavozimi</th>
                    <th>Telefon</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @if (count($model) > 0)
                    @foreach ($model as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>
                                <img
                                    src="{{ !is_null($item->img) ? asset($item->img) : asset('images/staticImages/defaultAvatar.png') }}"
                                    width="40" height="30" alt="{{ asset($item->img) }}" class="rounded-circle img-thumbnail"
                                    style="border: 1px grey solid">
                            </td>
                            <td>
                                {{ $item->last_name . ' ' . $item->first_name ?? '----' }}
                            </td>
                            <td>
                                {{ $item->email }}
                            </td>
                            <td>
                                @if ($item->user_type == User::TYPE_ADMIN)
                                    <small class="badge bg-label-primary badge-sm rounded-pill">
                                        {{ User::getTypes($item->user_type) }}
                                    </small>
                                @elseif($item->user_type == User::TYPE_TEACHER)
                                    <small class="badge bg-label-success badge-sm rounded-pill">
                                        {{ User::getTypes($item->user_type) . ': ' . User::getSubjectsById($item->subject_id)->name ?? '------' }}
                                    </small>
                                @else
                                    <small class="badge bg-label-primary badge-sm rounded-pill">
                                        {{ User::getTypes($item->user_type) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <i class="ri-phone-fill"></i> {{ $item->phone ?? '-----' }}
                            </td>
                            <td>
                                @if ($item->status == User::STATUS_ACTIVE)
                                    <small class="badge bg-label-success badge-sm rounded-pill">
                                        {{ User::getStatus($item->status) }}
                                    </small>
                                @else
                                    <small class="badge bg-label-danger badge-sm rounded-pill">
                                        {{ User::getStatus($item->status) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('backend.user.edit', $item->id)}}"
                                   class="badge bg-label-info badge-lg rounded-pill">
                                    <i style="font-size: 16px" class="ri-pencil-line"></i>
                                </a>
                                <a href="{{ route('backend.user.show', $item->id) }}"
                                   class="badge bg-label-primary badge-lg rounded-pill">
                                    <i style="font-size: 16px" class="ri-eye-2-line"></i>
                                </a>
                                <form id="deleteForm" action="{{ route('backend.user.destroy', $item->id) }}"
                                      method="POST" style="display: inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="border: none"
                                            class="badge bg-label-danger badge-lg rounded-pill">
                                        <i style="font-size: 16px" class="ri-delete-bin-line"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                @else
                    <tr>
                        <td colspan="6" class="text-center">
                            <h5>
                                O'qituvchilar mavjud emas!
                            </h5>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>


    <script>
        document.getElementById('deleteForm').addEventListener('submit', function (event) {
            event.preventDefault();

            if (confirm('Haqiqatan ham ma\'lumotni o\'chirmoqchimisiz?')) {
                this.submit();
            }
        });
    </script>
@endsection
