@extends('layouts.app')

@section('css_scripts')
    <style>
        .spin-wrapper {
            /* position: relative;
            width: 100%;
            height: 100px;
            margin-top: 3px; */
            display: none;
            z-index: 1500;
        }

        .spinner {
            z-index: 1500;
            position: absolute;
            height: 80px;
            width: 80px;
            border: 5px solid transparent;
            border-top-color: #075db8;
            top: 50%;
            left: 50%;
            margin: -30px;
            border-radius: 50%;
            animation: spin 2s linear infinite;
        }

        .spinner::before,
        .spinner::after {
            content: '';
            position: absolute;
            border: 4px solid transparent;
            border-radius: 50%;
        }

        .spinner::before {
            border-top-color: #454545;
            top: -10px;
            left: -10px;
            right: -10px;
            bottom: -10px;
            animation: spin 3s linear infinite;
        }

        .spinner::after {
            border-top-color: #b3c5d8;
            top: 5px;
            left: 5px;
            right: 5px;
            bottom: 5px;
            animation: spin 4s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        @-webkit-keyframes spin {
            0% {
                -webkit-transform: rotate(0deg);
            }

            100% {
                -webkit-transform: rotate(360deg);
            }
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        {{-- <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Dashboard') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    {{ __('You are logged in!') }}
                </div>
            </div>
        </div> --}}
    </div>
</div>
@endsection

@section('toasts')
    @include('components.toasts')
@endsection