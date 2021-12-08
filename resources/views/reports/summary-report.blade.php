@extends('reports.reports-master')

@section('css_scripts')
    <style>
        .card-custom {
            position: relative;
            display: -ms-flexbox;
            display: -webkit-box;
            display: flex;
            -ms-flex-direction: column;
            flex-direction: column;
            -webkit-box-orient: vertical;
            min-width: 0;
            word-wrap: break-word;
            background-color: #fff;
            background: linear-gradient(to right, #ffffff, #e9ecef);
            background-image: -webkit-linear-gradient(left, #ffffff 0%, #e9ecef 100%);
            border-radius: 0.25rem 0 0 0.25rem;
            border-top-left-radius: 0.25rem;
            border-bottom-left-radius: 0.25rem;
            max-width: 500px;
            margin-bottom: 2rem;
        }

        .num-text {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
            display: inline;
        }

        .justify-content-between {
            -ms-flex-pack: justify !important;
            justify-content: space-between !important;
            -webkit-box-pack: justify;
        }

        .d-flex {
            display: -ms-flexbox !important;
            display: flex !important;
            display: -webkit-box;
        }

        #stats-list>div {
            -webkit-box-flex: 1;
            -webkit-flex: 1;
            flex: 1;
        }

        #stats-list>div:last-child {
            margin-right: 0;
        }

        .flex-column {
            -ms-flex-direction: column !important;
            flex-direction: column !important;
            -webkit-box-orient: vertical;
        }

        .flex-row {
            -ms-flex-direction: row !important;
            flex-direction: row !important;
            -webkit-box-orient: horizontal;
            -webkit-box-align: center;
        }

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="mx-auto text-center mb-4">
            <img src="{{ asset('img/2.png') }}" alt="iTrack" height="50px">
        </div>
        <h5 class="text-center text-uppercase mb-5"><b>{{ $name }}</b></h5>
        <h5 class="text-center text-uppercase mb-5"><b>{{ $header }}</b></h5>
        <div class="container" id="stats-list">
            <div class="card-custom shadow mx-auto">
                <div class="card-body">
                    <div class="d-flex flex-row justify-content-between">
                        <div class="d-flex flex-column">
                            <h6 class="text-uppercase text-black-50 mb-3"><b>TIMES LOGGED IN</b></h6>
                            <p class="num-text"><b>{{ $stats['logIn'] }}</b></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                            class="bi bi-unlock-fill" viewBox="0 0 16 16">
                            <path
                                d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="card-custom shadow mx-auto">
                <div class="card-body">
                    <div class="d-flex flex-row justify-content-between">
                        <div class="d-flex flex-column">
                            <h6 class="text-uppercase text-black-50 mb-3"><b>TASKS CREATED</b></h6>
                            <p class="num-text"><b>{{ $stats['tasks'] }}</b></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                            class="bi bi-list-task" viewBox="0 0 16 16">
                            <path fill-rule="evenodd"
                                d="M2 2.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5V3a.5.5 0 0 0-.5-.5H2zM3 3H2v1h1V3z" />
                            <path
                                d="M5 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM5.5 7a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 4a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9z" />
                            <path fill-rule="evenodd"
                                d="M1.5 7a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5V7zM2 7h1v1H2V7zm0 3.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5H2zm1 .5H2v1h1v-1z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="card-custom shadow mx-auto">
                <div class="card-body">
                    <div class="d-flex flex-row justify-content-between">
                        <div class="d-flex flex-column">
                            <h6 class="text-uppercase text-black-50 mb-3"><b>NOTES CREATED</b></h6>
                            <p class="num-text"><b>{{ $stats['notes'] }}</b></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                            class="bi bi-sticky-fill" viewBox="0 0 16 16">
                            <path
                                d="M2.5 1A1.5 1.5 0 0 0 1 2.5v11A1.5 1.5 0 0 0 2.5 15h6.086a1.5 1.5 0 0 0 1.06-.44l4.915-4.914A1.5 1.5 0 0 0 15 8.586V2.5A1.5 1.5 0 0 0 13.5 1h-11zm6 8.5a1 1 0 0 1 1-1h4.396a.25.25 0 0 1 .177.427l-5.146 5.146a.25.25 0 0 1-.427-.177V9.5z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="card-custom shadow mx-auto">
                <div class="card-body">
                    <div class="d-flex flex-row justify-content-between">
                        <div class="d-flex flex-column">
                            <h6 class="text-uppercase text-black-50 mb-3"><b>PROJECTS CREATED</b></h6>
                            <p class="num-text"><b>{{ $stats['projects'] }}</b></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                            class="bi bi-kanban-fill" viewBox="0 0 16 16">
                            <path
                                d="M2.5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2h-11zm5 2h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zm-5 1a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3zm9-1h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="card-custom shadow mx-auto">
                <div class="card-body">
                    <div class="d-flex flex-row justify-content-between">
                        <div class="d-flex flex-column">
                            <h6 class="text-uppercase text-black-50 mb-3"><b>REMINDERS DISPATCHED</b></h6>
                            <p class="num-text"><b>{{ $stats['reminders'] }}</b></p>
                        </div>
                        <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                            class="bi bi-calendar-date-fill" viewBox="0 0 16 16">
                            <path
                                d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zm5.402 9.746c.625 0 1.184-.484 1.184-1.18 0-.832-.527-1.23-1.16-1.23-.586 0-1.168.387-1.168 1.21 0 .817.543 1.2 1.144 1.2z" />
                            <path
                                d="M16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zm-6.664-1.21c-1.11 0-1.656-.767-1.703-1.407h.683c.043.37.387.82 1.051.82.844 0 1.301-.848 1.305-2.164h-.027c-.153.414-.637.79-1.383.79-.852 0-1.676-.61-1.676-1.77 0-1.137.871-1.809 1.797-1.809 1.172 0 1.953.734 1.953 2.668 0 1.805-.742 2.871-2 2.871zm-2.89-5.435v5.332H5.77V8.079h-.012c-.29.156-.883.52-1.258.777V8.16a12.6 12.6 0 0 1 1.313-.805h.632z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
        <h6 class="text-right px-5">{{ now() }}</h6>
    </div>
@endsection

@section('js_scripts')

@endsection
