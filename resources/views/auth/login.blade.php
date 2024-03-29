@extends('layouts.unauthenticated')

@section('css_scripts')
    <style>
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Login') }}</div>
                    <div class="card-body">
                        <form method="POST" action="{{ route('login') }}">
                            @csrf

                            <div class="form-group row mb-2">
                                <label for="email"
                                    class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6 col-lg-5">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email" autofocus>

                                    @error('email')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="password"
                                    class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Password') }}</label>

                                <div class="col-md-6 col-lg-5">
                                    <input id="password" type="password"
                                        class="form-control @error('password') is-invalid @enderror" name="password"
                                        required autocomplete="current-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>

                                <div class="col-md-2 mt-2">
                                    <i id="show-pass" class="bi bi-eye-slash-fill" onclick="showPassword()"></i>
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <div class="col-md-6 offset-md-4 offset-lg-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="remember" id="remember"
                                            {{ old('remember') ? 'checked' : '' }}>

                                        <label class="form-check-label" for="remember">
                                            {{ __('Remember Me') }}
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-8 offset-md-4">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Login') }}
                                    </button>

                                    @if (Route::has('password.request'))
                                        <a class="btn btn-link" href="{{ route('password.request') }}">
                                            {{ __('Forgot Your Password?') }}
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    <script>
        function showPassword() {
            const passwordInput = document.getElementById('password');
            const passStatus = document.getElementById('show-pass');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passStatus.className = 'bi bi-eye-fill';
            } else {
                passwordInput.type = 'password';
                passStatus.className = 'bi bi-eye-slash-fill';
            }
        }
    </script>
@endsection
