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
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('backend.subjects.index') }}">Fanlar</a></li>
            <li class="breadcrumb-item active" aria-current="page">{{ $model->name }}</li>
        </ol>
    </nav>
    <div class="card pb-5">
        <div class="card-header">
            Fan: {{ $model->name }}
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover table-sm text-center mb-5 pb-5"
                style="border: 1px solid rgb(201, 198, 198);">
                <thead>
                    <tr>
                        <th>Fan nomi</th>
                        <th style="width: 250px;">O'qituvchi (F.I)</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>{{ $model->name }}</td>
                        <td>
                            <ul>
                                @foreach (\App\Models\Subjects::getTeacherById($model->id) as $teacher)
                                    <li>
                                        {{ $teacher->last_name . ' ' . $teacher->first_name }}
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                        <td>{{ $model->status ? 'Faol' : 'No\'faol' }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
@endsection
