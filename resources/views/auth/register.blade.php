@extends('layouts.unauthenticated')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">{{ __('Register') }}</div>

                    <div class="card-body">
                        <form method="POST" action="{{ route('register') }}">
                            @csrf

                            <div class="form-group row mb-2">
                                <label for="name"
                                    class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Name') }}</label>

                                <div class="col-md-6 col-lg-5">
                                    <input id="name" type="text"
                                        class="form-control @error('name') is-invalid @enderror" name="name"
                                        value="{{ old('name') }}" required autocomplete="name" autofocus>

                                    @error('name')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="email"
                                    class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>

                                <div class="col-md-6 col-lg-5">
                                    <input id="email" type="email"
                                        class="form-control @error('email') is-invalid @enderror" name="email"
                                        value="{{ old('email') }}" required autocomplete="email">

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
                                        required autocomplete="new-password">

                                    @error('password')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                    @enderror
                                </div>
                            </div>

                            <div class="form-group row mb-2">
                                <label for="password-confirm"
                                    class="col-md-4 col-lg-3 col-form-label text-md-right">{{ __('Confirm Password') }}</label>

                                <div class="col-md-6 col-lg-5">
                                    <input id="password-confirm" type="password" class="form-control"
                                        name="password_confirmation" required autocomplete="new-password">
                                </div>
                                <div class="col-md-2 mt-2">
                                    <i id="show-pass" class="bi bi-eye-slash-fill" onclick="showPassword()"></i>
                                </div>
                            </div>

                            <div class="form-group row mb-0">
                                <div class="col-md-6 offset-md-4 offset-lg-3">
                                    <button type="submit" class="btn btn-primary">
                                        {{ __('Register') }}
                                    </button>
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
            const passwordConfirm = document.getElementById('password-confirm');
            const passStatus = document.getElementById('show-pass');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordConfirm.type = 'text';
                passStatus.className = 'bi bi-eye-slash-fill';
            } else {
                passwordInput.type = 'password';
                passwordConfirm.type = 'password';
                passStatus.className = 'bi bi-eye-fill';
            }
        }
    </script>
@endsection
