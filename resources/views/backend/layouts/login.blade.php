@extends('backend.layouts._blank')

@section('content')

<style>
    .authentication-wrapper {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: #f5f5f9;
    }

    .login-card {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 24px rgba(0, 0, 0, 0.1);
        padding: 2.5rem;
        width: 100%;
        max-width: 450px;
    }

    .auth-brand {
        text-align: center;
        margin-bottom: 2rem;
    }
</style>

<div class="authentication-wrapper">
    <div class="login-card">
        <!-- Logo -->
        <div class="auth-brand">
            <a href="index.html" class="d-flex align-items-center justify-content-center gap-2">
                <span class="app-brand-logo demo">
                    <span style="color:var(--bs-primary);">
                    </span>
                </span>
                <span class="app-brand-text demo text-heading fw-semibold fs-3">AYM</span>
            </a>
        </div>
        <!-- /Logo -->

        <h4 class="mb-1 text-center">Xush kelibsiz! 👋</h4>
        <p class="mb-5 text-center text-muted">Tizimga kirish uchun ma'lumotlaringizni kiriting</p>

        <form id="formAuthentication" action="{{route('backend.login')}}" method="POST">
            @csrf
            <div class="form-floating form-floating-outline mb-4">
                <input type="text" class="form-control" id="email" name="name" placeholder="Username kiriting" autofocus="">
                <label for="email">Username</label>
            </div>

            <div class="mb-4">
                <div class="form-password-toggle">
                    <div class="input-group input-group-merge">
                        <div class="form-floating form-floating-outline">
                            <input type="password" id="password" class="form-control" name="password" placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;" aria-describedby="password">
                            <label for="password">Password</label>
                        </div>
                        <span class="input-group-text cursor-pointer"><i class="ri-eye-off-line"></i></span>
                    </div>
                </div>
            </div>

            <div class="mb-4">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="remember-me">
                    <label class="form-check-label" for="remember-me">
                        Eslab qol
                    </label>
                </div>
            </div>

            <button type="submit" class="btn btn-primary d-grid w-100 mb-4">
                Tizimga kirish
            </button>
        </form>

        <div class="d-flex justify-content-center gap-2">
            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-facebook">
                <i class="tf-icons ri-facebook-fill"></i>
            </a>

            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-twitter">
                <i class="tf-icons ri-twitter-fill"></i>
            </a>

            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-github">
                <i class="tf-icons ri-github-fill"></i>
            </a>

            <a href="javascript:;" class="btn btn-icon rounded-circle btn-text-google-plus">
                <i class="tf-icons ri-google-fill"></i>
            </a>
        </div>
    </div>
</div>

@endsection