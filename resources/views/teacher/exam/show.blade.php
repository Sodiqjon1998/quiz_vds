@php use App\Models\Teacher\Quiz; @endphp
@php use App\Models\User; @endphp
@extends('teacher.layouts.main')

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
            <li class="breadcrumb-item"><a href="{{route('teacher')}}">Bosh sahifa</a></li>
            <li class="breadcrumb-item active" aria-current="page">Imtihonlar</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            Imtihonlar
        </div>
        <div class="card-body table-responsive">
            <table class="table table-bordered table-striped table-hover table-sm text-center"
                   style="border: 1px solid rgb(201, 198, 198);">
                <thead>
                <tr>
                    <th style="width: 30px">T/R</th>
                    <th>O'quvchi ismi</th>
                    <th>Sinf nomi</th>
                    <th>Kiritilgan vaqti</th>
                    <th>Action</th>
                </tr>
                </thead>
                <tbody>
                @if (count($model) > 0)
                    @foreach ($model as $key => $item)
                        <tr>
                            <td>{{ ++$key }}</td>
                            <td>
                                {{ User::getStudentFullNameById($item->user_id) }}
                            </td>
                            <td>
                                {{User::getByUserClassId($item->user_id)->name}}
                            </td>
                            <td>
                                {{$item->created_at}}
                            </td>
                            
                            <td>
                                <a href="{{route('teacher.exam.edit', $item->id)}}"
                                   class="badge bg-label-info badge-lg rounded-pill">
                                    <i style="font-size: 16px" class="ri-pencil-line"></i>
                                </a>
                                <a href="{{ route('teacher.exam.showTest', $item->id) }}"
                                   class="badge bg-label-primary badge-lg rounded-pill">
                                    <i style="font-size: 16px" class="ri-eye-2-line"></i>
                                </a>
                                <form id="deleteForm" action="{{ route('teacher.exam.destroy', $item->id) }}"
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
                                Quiz mavjud emas!
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