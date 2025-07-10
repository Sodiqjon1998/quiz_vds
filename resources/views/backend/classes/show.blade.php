@extends('backend.layouts.main')
@use(App\Models\User)

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
            <li class="breadcrumb-item"><a href="{{ route('backend.classes.index') }}">Sinflar</a></li>
            <li class="breadcrumb-item active" aria-current="page">Batafsil</li>
        </ol>
    </nav>


    <div class="card">
        <div class="card-header">
            {{$model->name}} sinfi
        </div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-sm text-center"
                   style="border: 1px solid rgb(201, 198, 198);">
                <tr>
                    <th>Id</th>
                    <th>{{$model->id}}</th>
                </tr>
                <tr>
                    <th>Sinf nomi</th>
                    <th>{{$model->name}}</th>
                </tr>
                <tr>
                    <th>Kordinator</th>
                    <th>{{\App\Models\Classes::getKoordinator($model->koordinator_id)}}</th>
                </tr>
            </table>
        </div>
    </div>
    <br>
    <hr>
    <br>
    <div class="card">
        <div class="card-header">Sinfda o'qiyotgan o'quvchilar ro'yxati</div>
        <div class="card-body">
            <table class="table table-bordered table-striped table-hover table-sm text-center"
                   style="border: 1px solid rgb(201, 198, 198);">
                <thead>
                <tr>
                    <th style="width: 30px">T/R</th>
                    <th>Rasm</th>
                    <th>Ism familya</th>
                    <th>Sinfi</th>
                    <th>Email</th>
                    <th>Telefon</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
                </thead>
                @if (count($students) > 0)
                    @foreach ($students as $key => $item)
                        <tr>
                            <th>{{ ++$key }}</th>
                            <td>
                                <img
                                    src="{{ !is_null($item->img) ? asset($item->img) : asset('images/staticImages/defaultAvatar.png') }}"
                                    width="40" height="30" alt="{{ asset($item->img) }}" class="rounded-circle"
                                    style="border: 1px grey solid">
                            </td>
                            <td>
                                {{ $item->first_name .  ' ' . $item->last_name }}
                            </td>
                            <td>
                                <span class="badge bg-label-info">
                                    {{User::getClassesById($item->classes_id)->name}}
                                </span>
                            </td>
                            <td>
                                {{ $item->email }}
                            </td>
                            <td>
                                {{ $item->phone ?? '-----' }}
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
                                <a href="{{route('backend.student.edit', $item->id)}}"
                                   class="badge bg-label-info badge-lg rounded-pill">
                                    <i style="font-size: 16px" class="ri-pencil-line"></i>
                                </a>
                                {{--                                <a href="{{ route('backend.student.show', $item->id) }}"--}}
                                {{--                                   class="badge bg-label-primary badge-lg rounded-pill">--}}
                                {{--                                    <i style="font-size: 16px" class="ri-eye-2-line"></i>--}}
                                {{--                                </a>--}}
                                <form id="deleteForm" action="{{ route('backend.student.destroy', $item->id) }}"
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
                        <td colspan="8" class="text-center">
                            <h5>
                                O'quvchilar mavjud emas!
                            </h5>
                        </td>
                    </tr>
                @endif
            </table>
        </div>
    </div>

@endsection
