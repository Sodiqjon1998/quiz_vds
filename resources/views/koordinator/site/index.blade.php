@extends("koordinator.layouts.main")


@section('content')

<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4"><span class="text-muted fw-light">Koordinator /</span> Bosh sahifa</h4>
    <div class="row">
        <div class="col-lg-12 mb-4 order-0">
            <div class="card">
                <div class="d-flex align-items-end row">
                    <div class="col-sm-7">
                        <div class="card-body">
                            <h5 class="card-title text-primary">Xush kelibsiz, {{ Auth::user()->first_name }}! 🎉</h5>
                            <p class="mb-4">
                                Koordinator boshqaruv paneliga xush kelibsiz. Bu yerda siz tizimni boshqarishingiz va
                                muhim ma'lumotlarga kirishingiz mumkin.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection