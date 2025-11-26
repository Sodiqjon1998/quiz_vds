@extends('teacher.layouts._blank')

@section('content')

<style>
    :root {
        --yuksalish-orange: #F58025;
        --yuksalish-dark: #212529;
        --yuksalish-light: #fffbf8;
    }

    body {
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        background: #f8f9fa;
        color: var(--yuksalish-dark);
    }

    .authentication-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #fffbf8 0%, #fff 100%);
        position: relative;
        overflow: hidden;
    }

    /* Orqa fon bezaklari */
    .auth-bg-shape {
        position: absolute;
        border-radius: 50%;
        background: var(--yuksalish-orange);
        opacity: 0.05;
        z-index: 0;
    }

    .shape-1 {
        width: 300px;
        height: 300px;
        top: -100px;
        right: -50px;
    }

    .shape-2 {
        width: 200px;
        height: 200px;
        bottom: -50px;
        left: -50px;
    }

    /* Login Card */
    .login-card {
        background: white;
        border-radius: 20px;
        box-shadow: 0 10px 40px rgba(0, 0, 0, 0.08);
        padding: 3rem 2.5rem;
        width: 100%;
        max-width: 420px;
        position: relative;
        z-index: 1;
        border-top: 5px solid var(--yuksalish-orange);
    }

    /* Logo */
    .auth-brand {
        display: flex;
        justify-content: center;
        align-items: center;
        gap: 10px;
        margin-bottom: 1.5rem;
        text-decoration: none;
    }

    .brand-icon {
        width: 45px;
        height: 45px;
        background: var(--yuksalish-orange);
        color: white;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.8rem;
    }

    .brand-text {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--yuksalish-dark);
        letter-spacing: -0.5px;
    }

    /* Input Fields */
    .form-floating>.form-control:focus~label,
    .form-floating>.form-control:not(:placeholder-shown)~label {
        color: var(--yuksalish-orange);
    }

    .form-control {
        border: 2px solid #eee;
        border-radius: 10px;
        padding-left: 15px;
        height: 50px;
    }

    .form-control:focus {
        border-color: var(--yuksalish-orange);
        box-shadow: 0 0 0 4px rgba(245, 128, 37, 0.1);
    }

    .input-group-text {
        border: 2px solid #eee;
        border-left: none;
        background: white;
        border-radius: 0 10px 10px 0;
        color: #999;
    }

    .form-control:focus+.input-group-text {
        border-color: var(--yuksalish-orange);
        color: var(--yuksalish-orange);
    }

    /* Checkbox */
    .form-check-input:checked {
        background-color: var(--yuksalish-orange);
        border-color: var(--yuksalish-orange);
    }

    /* Button */
    .btn-yuksalish {
        background-color: var(--yuksalish-orange);
        border: none;
        color: white;
        font-weight: 600;
        padding: 14px;
        border-radius: 10px;
        font-size: 1rem;
        transition: all 0.3s;
    }

    .btn-yuksalish:hover {
        background-color: #d96d1b;
        transform: translateY(-2px);
        box-shadow: 0 6px 15px rgba(245, 128, 37, 0.25);
        color: white;
    }

    /* Social Icons */
    .btn-icon {
        width: 40px;
        height: 40px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 50%;
        border: 1px solid #eee;
        color: #6c757d;
        transition: all 0.3s;
        text-decoration: none;
        background: white;
    }

    .btn-icon:hover {
        border-color: var(--yuksalish-orange);
        color: var(--yuksalish-orange);
        background: #fffbf8;
        transform: translateY(-2px);
    }
</style>

<div class="authentication-wrapper">
    <!-- Background Shapes -->
    <div class="auth-bg-shape shape-1"></div>
    <div class="auth-bg-shape shape-2"></div>

    <div class="login-card">
        <!-- Logo -->
        <a href="{{ url('/') }}" class="auth-brand">
            <div class="brand-icon">
                <i class="ri-graduation-cap-fill"></i>
            </div>
            <span class="brand-text">Yuksalish</span>
        </a>
        <!-- /Logo -->

        <div class="text-center mb-4">
            <h4 class="mb-1 fw-bold text-dark">Xush kelibsiz! 👋</h4>
            <p class="text-muted small mb-0">Tizimga kirish uchun ma'lumotlaringizni kiriting</p>
        </div>

        <form id="formAuthentication" action="{{route('teacher.login')}}" method="POST">
            @csrf

            <div class="form-floating form-floating-outline mb-3">
                <input type="text" class="form-control" id="email" name="name" placeholder="Username" autofocus>
                <label for="email">Username</label>
            </div>

            <div class="mb-3">
                <div class="input-group input-group-merge">
                    <div class="form-floating form-floating-outline" style="flex-grow: 1;">
                        <input type="password" id="password" class="form-control" name="password"
                            placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                            aria-describedby="password" style="border-right: none; border-radius: 10px 0 0 10px;">
                        <label for="password">Parol</label>
                    </div>
                    <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
                </div>
            </div>

            <div class="mb-4 d-flex justify-content-between align-items-center">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember-me">
                    <label class="form-check-label text-muted small" for="remember-me">
                        Eslab qolish
                    </label>
                </div>
                {{-- <a href="#" class="small text-warning text-decoration-none fw-semibold">Parolni unutdingizmi?</a> --}}
            </div>

            <button type="submit" class="btn btn-yuksalish d-grid w-100 mb-4">
                Tizimga kirish
            </button>
        </form>

        {{-- <div class="position-relative my-4">
            <hr class="text-muted opacity-25">
            <span class="position-absolute top-50 start-50 translate-middle bg-white px-3 text-muted small">Yoki</span>
        </div> --}}

        <div class="d-flex justify-content-center gap-3 mt-4">
            <a href="javascript:;" class="btn-icon" title="Facebook">
                <i class="ri-facebook-fill fs-5"></i>
            </a>

            <a href="javascript:;" class="btn-icon" title="Twitter">
                <i class="ri-twitter-x-line fs-5"></i>
            </a>

            <a href="javascript:;" class="btn-icon" title="GitHub">
                <i class="ri-github-fill fs-5"></i>
            </a>

            <a href="javascript:;" class="btn-icon" title="Google">
                <i class="ri-google-fill fs-5"></i>
            </a>
        </div>

        <div class="text-center mt-4 text-muted small opacity-75">
            &copy; {{ date('Y') }} Andijon Yuksalish Maktabi
        </div>
    </div>
</div>

@endsection