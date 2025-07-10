@extends('backend.layouts.main')

@use(App\Models\Classes)
@use(App\Models\User)

@section('content')

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('backend.classes.index') }}">Sinflar</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tahrirlash</li>
        </ol>
    </nav>
    <div class="row">
        <div class="col-xl">
            <div class="card mb-6">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Sinflar</h5> <small class="text-body float-end"></small>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('backend.classes.update', $model->id) }}">
                        @csrf
                        <div class="row">
                            <div class="col-md-6 mb-6">

                                <div class="form-floating form-floating-outline mb-6">
                                    <input type="text" name="name" class="form-control" id="basic-default-fullname"
                                           placeholder="Sinf" required value="{{$model->name}}">
                                    <label for="basic-default-fullname">Sinf</label>
                                </div>
                            </div>
                            <!-- Basic -->
                            <div class="col-md-6 mb-6">
                                <div class="form-floating form-floating-outline">
                                    <select id="select2Basic" class="select2 form-select form-select-lg"
                                            data-allow-clear="true" name="koordinator_id">
                                        @foreach(Classes::getKordinatorList() as $key => $item)
                                            <option
                                                value="{{$item->id}}" {{$model->koordinator_id == $item->id ? "selected" : ''}}>{{$item->firs_name . ' ' . $item->last_name. ' Login: '. $item->name}}</option>
                                        @endforeach
                                    </select>
                                    <label for="select2Basic">Basic</label>
                                </div>
                            </div>
                        </div>


                        <div class="mb-6">
                            <div class="row">
                                <div class="col-sm-6 p-6 pt-sm-0">
                                    <div class="text-light small fw-medium mb-4">Hodimning satatusi active yoki no
                                        active
                                    </div>
                                    <div class="switches-stacked">
                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input"
                                                   value="{{ User::STATUS_ACTIVE }}"
                                                   name="status" {{$model->status == User::STATUS_ACTIVE ? 'checked' : ''}}>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">Faol</span>
                                        </label>

                                        <label class="switch switch-square">
                                            <input type="radio" class="switch-input"
                                                   value="{{ User::STATUS_IN_ACTIVE }}"
                                                   name="status" {{$model->status == User::STATUS_IN_ACTIVE ? 'checked' : ''}}>
                                            <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                            <span class="switch-label">Bloklangan</span>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary float-end">Saqlash</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

@endsection
