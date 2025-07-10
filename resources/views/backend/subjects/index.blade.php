@use(App\Models\Classes)
@extends('backend.layouts.main')

@section('content')

    <style>
        table,
        tr,
        th,
        td{
            padding: 7px !important;
            font-size: 14px;
        }

    </style>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{route('dashboard')}}">Bosh sahifa</a></li>
            <li class="breadcrumb-item active" aria-current="page">Fanlar</li>
        </ol>
    </nav>

    <div class="card pb-5">
        <div class="card-header">
            Fanlar
            <a href="{{ route('backend.subjects.create') }}" class="badge bg-label-success badge-lg rounded-pill">
                <i style="font-size: 16px" class="ri-add-circle-line" style="font-size: 15px"></i>
            </a>
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover table-sm text-center mb-5 pb-5"
                   style="border: 1px solid rgb(201, 198, 198);">
                <thead>
                <tr>
                    <th style="width: 30px">T/R</th>
                    <th>Fan nomi</th>
                    <th style="width: 250px;">O'qituvchi (F.I)</th>
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
                                {{ $item->name }}
                            </td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-label-success dropdown-toggle"
                                            data-bs-toggle="dropdown" aria-expanded="false">Click me
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach(\App\Models\Subjects::getTeacherById($item->id) as $key => $teacher)
                                            <li>
                                                <a class="dropdown-item" href="javascript:void(0);">
                                                    {{$teacher->last_name . ' ' . $teacher->first_name}}
                                                </a>
                                            </li>
                                        @endforeach
                                    </ul>
                                </div>
                            </td>
                            <td>
                                @if ($item->status == \App\Models\Subjects::STATUS_ACTIVE)
                                    <small class="badge bg-label-success badge-sm rounded-pill">
                                        {{ \App\Models\Subjects::getStatus($item->status) }}
                                    </small>
                                @else
                                    <small class="badge bg-label-danger badge-sm rounded-pill">
                                        {{ \App\Models\Subjects::getStatus($item->status) }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                <a href="{{route('backend.subjects.edit', $item->id)}}"
                                   class="badge bg-label-info badge-lg rounded-pill">
                                    <i style="font-size: 16px" class="ri-pencil-line"></i>
                                </a>
                                <a href="{{ route('backend.subjects.show', $item->id) }}"
                                   class="badge bg-label-primary badge-lg rounded-pill">
                                    <i style="font-size: 16px" class="ri-eye-2-line"></i>
                                </a>
                                <form id="deleteForm" action="{{ route('backend.subjects.destroy', $item->id) }}"
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
                                Fanlar nomi mavjud emas!
                            </h5>
                        </td>
                    </tr>
                @endif
                </tbody>
            </table>
        </div>
    </div>


    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Initialize the delete form submission
            document.querySelectorAll('#deleteForm').forEach(function (form) {
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    if (confirm('Siz ushbu fanni o\'chirmoqchimisiz?')) {
                        this.submit();
                    }
                });
            });
        });
    </script>
@endsection


