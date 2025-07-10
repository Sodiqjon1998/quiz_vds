@extends('teacher.layouts.main')



@section('content')

    <h1 class="display-1">Teacher {{Auth::user()->name}}</h1>

@endsection
