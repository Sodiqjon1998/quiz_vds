@extends('student.layouts.main')


@section('content')

    <h1>
        Student {{Auth::user()->first_name}}
    </h1>
@endsection
