@extends('backend.layouts.main')


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
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('backend.student.index') }}">O'quvchilar</a></li>
            <li class="breadcrumb-item active" aria-current="page">Batafsil</li>
        </ol>
    </nav>


    <div class="card">
        <div class="card-header">
            O'quvchi haqida ma'lumot
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover table-sm text-center"
                style="border: 1px solid rgb(201, 198, 198);">
                <tbody>
                    <tr>
                        <th style="width: 30px; width:200px;">ID</th>
                        <td>{{ $model->id }}</td>
                    </tr>
                    <tr>
                        <th>Rasm</th>
                        <td>
                            <img src="{{ !is_null($model->img) ? asset($model->img) : asset('images/staticImages/defaultAvatar.png') }}"
                                width="40" height="30" alt="{{ asset($model->img) }}" class="rounded-circle"
                                style="border: 1px grey solid">
                        </td>
                    </tr>
                    <tr>
                        <th>Ism familya</th>
                        <td>{{ $model->first_name . ' ' . $model->last_name }}</td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td>{{ $model->email }}</td>
                    </tr>
                    <tr>
                        <th>Telefon</th>
                        <td>{{ $model->phone }}</td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>{{ $model->status == 1 ? 'Faol' : 'No faol' }}</td>
                    </tr>

                </tbody>
            </table>

        </div>
    </div>
@endsection
