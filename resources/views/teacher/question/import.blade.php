@extends('teacher.layouts.main')


@section('content')
    <div class="card">
        <div class="card-body">
            <form action="{{ route('teacher.question.import') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="word_file">Savollar faylini yuklang (.docx):</label>
            <input type="file" name="word_file" id="word_file" class="form-control" accept=".docx">
            @if ($errors->any())
                <div class="alert alert-danger">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        <button type="submit" class="btn btn-primary mt-3">Yuklash va Import qilish</button>
    </form>
        </div>
    </div>
@endsection
