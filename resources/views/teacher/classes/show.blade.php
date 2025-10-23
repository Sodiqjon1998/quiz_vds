<?php

use App\Models\Users;

?>


@extends('teacher.layouts.main')

@section('content')

    <style>
        table,
        tr,
        th,
        td {
            padding: 7px !important;
            font-size: 14px;
        }

        /* Qidiruv maydoni uchun qo'shimcha stillar */
        .search-input-group {
            margin-bottom: 15px;
            display: flex;
            gap: 10px;
            /* Input va button orasidagi bo'sh joy */
        }

        .search-input-group .form-control {
            flex-grow: 1;
            /* Input maydonini kengaytirish */
        }
    </style>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('teacher') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item active" aria-current="page">O'quvchilar</li>
        </ol>
    </nav>

    <div class="card">
        <div class="card-header">
            O'quvchilar
            {{-- <a href="{{ route('teacher.student.create') }}" class="badge bg-label-success badge-lg rounded-pill">
                <i style="font-size: 16px" class="ri-add-circle-line" style="font-size: 15px"></i>
            </a> --}}
            <small class="text-muted">
                Jami: {{ $model->total() }} ta,
                Sahifada: {{ $model->count() }} ta
            </small>
        </div>
        <div class="card-body">
            {{-- Qidiruv maydoni --}}
            <div class="search-input-group">
                <input type="text" id="searchInput" class="form-control"
                       placeholder="Ism, familya, sinf yoki telefon bo'yicha qidirish...">
                <button id="clearSearch" class="btn btn-outline-secondary">Tozalash</button>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped table-hover table-sm text-center"
                       style="border: 1.5px solid rgb(201, 198, 198);">
                    <thead>
                    <tr>
                        <th style="width: 30px">T/R</th>
                        <th>Rasm</th>
                        <th>Ism familya</th>
                        {{-- <th>Sinfi</th> --}}
                        <th>Email</th>
                        <th>Telefon</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                    </thead>
                    <tbody id="studentTableBody">
                    @if (count($model) > 0)
                        @foreach ($model as $key => $item)
                            <tr>
                                <td>{{ ($model->currentPage() - 1) * $model->perPage() + $loop->iteration }}</td>
                                <td>
                                    <img src="{{ !is_null($item->img) ? asset($item->img) : asset('images/staticImages/defaultAvatar.png') }}"
                                         width="40" height="30" alt="{{ asset($item->img) }}"
                                         class="rounded-circle" style="border: 1px grey solid">
                                </td>
                                <td>
                                    {{ $item->first_name . ' ' . $item->last_name }}
                                </td>
                                {{-- <td>
                                    <span class="badge bg-label-info">
                                        {{ Users::getClassesById($item->classes_id)->name }}
                                    </span>
                                </td> --}}
                                <td>
                                    {{ $item->email }}
                                </td>
                                <td>
                                    {{ $item->phone ?? '-----' }}
                                </td>
                                <td>
                                    @if ($item->status == Users::STATUS_ACTIVE)
                                        <small class="badge bg-label-success badge-sm rounded-pill">
                                            {{ Users::getStatus($item->status) }}
                                        </small>
                                    @else
                                        <small class="badge bg-label-danger badge-sm rounded-pill">
                                            {{ Users::getStatus($item->status) }}
                                        </small>
                                    @endif
                                </td>
                                <td>
                                    {{-- <a href="{{ route('backend.student.edit', $item->id) }}"
                                        class="badge bg-label-info badge-lg rounded-pill">
                                        <i style="font-size: 16px" class="ri-pencil-line"></i>
                                    </a>
                                    <a href="{{ route('backend.student.show', $item->id) }}"
                                        class="badge bg-label-primary badge-lg rounded-pill">
                                        <i style="font-size: 16px" class="ri-eye-2-line"></i>
                                    </a>
                                    <form action="{{ route('backend.student.destroy', $item->id) }}" method="POST"
                                        style="display: inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" style="border: none"
                                            class="badge bg-label-danger badge-lg rounded-pill"
                                            onclick="return confirm('Haqiqatan ham ma\'lumotni o\'chirmoqchimisiz?')">
                                            <i style="font-size: 16px" class="ri-delete-bin-line"></i>
                                        </button>
                                    </form> --}}
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
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            {{ $model->links() }}
        </div>
    </div>

    <script>
        // O'chirish formasini tasdiqlash funksiyasi
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function (event) {
                if (this.querySelector('button[type="submit"]').getAttribute('onclick')) {
                    // Agar onclick atributi mavjud bo'lsa, uni ishga tushiramiz
                    // va agar false qaytarsa, submitni to'xtatamiz
                    if (!eval(this.querySelector('button[type="submit"]').getAttribute('onclick'))) {
                        event.preventDefault();
                    }
                }
            });
        });

        // Qidiruv funksiyasi
        document.addEventListener('DOMContentLoaded', function () {
            const searchInput = document.getElementById('searchInput');
            const studentTableBody = document.getElementById('studentTableBody');
            const clearSearchButton = document.getElementById('clearSearch');

            if (searchInput && studentTableBody) {
                searchInput.addEventListener('keyup', function () {
                    const searchTerm = searchInput.value.toLowerCase();
                    const rows = studentTableBody.querySelectorAll('tr');

                    rows.forEach(row => {
                        // Agar "O'quvchilar mavjud emas!" qatori bo'lsa, uni filtrlamaymiz
                        if (row.querySelector('td[colspan="8"]')) {
                            return;
                        }

                        // Qidiriladigan ustunlar: Ism familya (index 2), Sinfi (index 3), Telefon (index 5)
                        // Email (index 4)
                        const name = row.cells[2].textContent.toLowerCase();
                        const className = row.cells[3].textContent.toLowerCase();
                        const email = row.cells[4].textContent.toLowerCase();
                        const phone = row.cells[5].textContent.toLowerCase();

                        if (name.includes(searchTerm) ||
                            className.includes(searchTerm) ||
                            email.includes(searchTerm) ||
                            phone.includes(searchTerm)) {
                            row.style.display = ''; // Qatorni ko'rsatish
                        } else {
                            row.style.display = 'none'; // Qatorni yashirish
                        }
                    });
                });

                // Qidiruv maydonini tozalash tugmasi
                clearSearchButton.addEventListener('click', function () {
                    searchInput.value = ''; // Maydonni tozalash
                    const rows = studentTableBody.querySelectorAll('tr');
                    rows.forEach(row => {
                        row.style.display = ''; // Barcha qatorlarni ko'rsatish
                    });
                });
            }
        });
    </script>
@endsection
