
@extends('backend.layouts.main')

@section('content')
<div class="container-fluid py-4">
    {{-- Sarlavha --}}
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h3 class="fw-bold text-dark mb-1">Boshqaruv Paneli</h3>
            <p class="text-muted mb-0">Maktabdagi umumiy jarayonlar statistikasi</p>
        </div>
        <div>
            <span class="badge bg-white text-dark border px-3 py-2 shadow-sm">
                <i class="ri-calendar-line me-1"></i> {{ date('d.m.Y') }}
            </span>
        </div>
    </div>

    {{-- Livewire Statistika Komponentini chaqirish --}}
    @livewire('backend.dashboard.statistics')

</div>
@endsection