@extends('koordinator.layouts._blank')

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
                    <img width="70" src="data:image/jpeg;base64,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" alt="">
                </span>
                <span class="app-brand-text demo text-heading fw-semibold fs-3">AYM</span>
            </a>
        </div>
        <!-- /Logo -->

        <h4 class="mb-1 text-center">Xush kelibsiz! 👋</h4>
        <p class="mb-5 text-center text-muted">Tizimga kirish uchun ma'lumotlaringizni kiriting</p>

        <form id="formAuthentication" action="{{route('koordinator.login')}}" method="POST">
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

            <div class="mb-4 d-flex justify-content-between align-items-center">
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