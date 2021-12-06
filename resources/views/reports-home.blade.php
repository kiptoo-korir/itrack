@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        .bg-card {
            transition: 0.3s;
        }

        .bg-card:hover {
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.5) !important;
        }

        .card-footer-custom {
            padding: 0.75rem 1.25rem;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .card-footer-custom:last-child {
            border-radius: 0 0 calc(0.25rem - 1px) calc(0.25rem - 1px);
        }

        .card-text {
            font-size: 0.95rem;
        }

        .mt--1 {
            margin-top: -1rem;
            /* !important */
        }

        .card-header-custom {
            padding: 15px;
            border-radius: 5px;
            margin-left: 10px;
            margin-right: 10px;
            color: white;
            background-color: #343a40;
            background: linear-gradient(45deg, #455059, #075db8);
        }

        .no-border {
            border: 0px solid black;
            /* max-width: 350px; */
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-6 col-lg-4 col-12 mb-5 px-4">
                <div class="card mt-2 no-border shadow mx-auto">
                    <div class="card-header-custom mt--1">
                        <h5 class="card-title my-0 text-uppercase">LOG IN STATS</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between my-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                                class="bi bi-unlock-fill" viewBox="0 0 16 16">
                                <path
                                    d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2z" />
                            </svg>
                            <div class="d-flex flex-column">
                                <p class="text-uppercase text-black-50"><b>LOGGED IN</b></p>
                                <h5 class="text-right"><b>50</b></h5>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-12 mb-5 px-4">
                <div class="card mt-2 no-border shadow mx-auto">
                    <div class="card-header-custom mt--1">
                        <h5 class="card-title my-0">TASKS STATS</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between my-3">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                                class="bi bi-list-task" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M2 2.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5V3a.5.5 0 0 0-.5-.5H2zM3 3H2v1h1V3z" />
                                <path
                                    d="M5 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM5.5 7a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 4a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9z" />
                                <path fill-rule="evenodd"
                                    d="M1.5 7a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5V7zM2 7h1v1H2V7zm0 3.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5H2zm1 .5H2v1h1v-1z" />
                            </svg>
                            <div class="d-flex flex-column">
                                <p class="text-uppercase text-black-50"><b>TASKS CREATED</b></p>
                                <h5 class="text-right"><b>50</b></h5>
                            </div>
                        </div>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-12 mb-5 px-4">
                <div class="card mt-2 no-border shadow mx-auto">
                    <div class="card-header-custom mt--1">
                        <h5 class="card-title my-0">Card title</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of
                            the card's content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-12 mb-5 px-4">
                <div class="card mt-2 no-border shadow mx-auto">
                    <div class="card-header-custom mt--1">
                        <h5 class="card-title my-0">Card title</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text">Some quick example text to build on the card title and make up the bulk of
                            the card's content.</p>
                        <a href="#" class="btn btn-primary">Go somewhere</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')

@endsection

@section('js_scripts')

@endsection
