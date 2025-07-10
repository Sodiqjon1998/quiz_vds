@use(\App\Models\User)
@extends('backend.layouts.main')


@section('content')
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Bosh sahifa</a></li>
            <li class="breadcrumb-item"><a href="{{ route('backend.user.index') }}">O'qituvchilar va Kordinatorlar</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tahrirlash</li>
        </ol>
    </nav>
    <div class="card">
        <div class="card-header">
            {{$model->name}} Hodimni Statusini bloklash yoki Active qilish
        </div>
        <div class="card-body">
            <form action="{{route('backend.user.update', $model->id)}}" method="post">
                @csrf
                <div class="row">

                    <div class="row">
                        <div class="col-md-6 mb-6">
                            <div class="form-floating form-floating-outline">
                                <select id="select2Basic" class="select2 form-select form-select-lg"
                                        data-allow-clear="true" name="subject_id" required>
                                    @foreach(User::getSubjectsList() as $key => $item)
                                        <option
                                            value="{{$item->id}}" {{$model->subject_id == $item->id ? "selected" : ''}}>{{$item->name}}</option>
                                    @endforeach
                                </select>
                                <label for="select2Basic">Fan nomini kiriting</label>
                            </div>
                        </div>
                        <div class="col-sm-6 p-6 pt-sm-0">
                            <div class="text-light small fw-medium mb-4">Hodimning satatusi active yoki no active
                            </div>
                            <div class="switches-stacked">
                                <label class="switch switch-square">
                                    <input type="radio" class="switch-input" value="{{User::STATUS_ACTIVE }}"
                                           name="status" {{$model->status == User::STATUS_ACTIVE ? 'checked': ''}}>
                                    <span class="switch-toggle-slider">
                                                <span class="switch-on"></span>
                                                <span class="switch-off"></span>
                                            </span>
                                    <span class="switch-label">Faol</span>
                                </label>

                                <label class="switch switch-square">
                                    <input type="radio" class="switch-input"
                                           value="{{ User::STATUS_IN_ACTIVE }}"
                                           name="status" {{$model->status == User::STATUS_IN_ACTIVE ? 'checked': ''}}>
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

@endsection
