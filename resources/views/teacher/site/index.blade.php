@extends('backend.layouts.main') {{-- Sizning asosiy admin layoutingiz --}}

@section('content')
    <div class="container-fluid">
        <h1 class="h3 mb-4 text-gray-800">Admin Bosh Sahifasi</h1>

        <div class="row">

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-primary shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                    Jami Talabalar</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">500</div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-users fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6 mb-4">
                <div class="card border-left-info shadow h-100 py-2">
                    <div class="card-body">
                        <div class="row no-gutters align-items-center">
                            <div class="col mr-2">
                                <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                    Diskda Bo'sh Joy</div>
                                <div class="h5 mb-0 font-weight-bold text-gray-800">
                                    @if (isset($diskSpace['available']))
                                        {{ $diskSpace['available'] }} qoldi ({{ $diskSpace['usage_percent'] }} ishlatilgan)
                                    @else
                                        Ma'lumot mavjud emas
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                <i class="fas fa-hdd fa-2x text-gray-300"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Boshqa statistikalar va kontent --}}

        </div>
    </div>
@endsection
