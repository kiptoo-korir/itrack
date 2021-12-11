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
            margin-bottom: 2rem;
        }

        .num-text {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            font-weight: 500;
            line-height: 1.2;
            display: inline;
        }

        .bg-gradient {
            background-color: #fff;
            background: linear-gradient(to right, #ffffff, #e9ecef);
            background-image: -webkit-linear-gradient(left, #ffffff 0%, #e9ecef 100%);
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

        .width-half {
            width: 50%;
        }

        .task {
            padding: 0.5rem 0.5rem;
            border-bottom: 1px solid hsl(0, 0%, 25%);
            background-color: white;
        }

        .task-body {
            font-size: 0.97rem;
            color: hsl(0, 0%, 25%);
            padding: 0.15rem 0;
            margin: 0;
        }

        .task-title {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .details {
            border-bottom: 1px solid hsl(0, 0%, 25%);
        }

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="mx-auto text-center mb-4">
            <img src="{{ asset('img/2.png') }}" alt="iTrack" height="50px">
            <h5 class="text-center text-uppercase my-4 pt-3"><b>{{ $name }}</b></h5>
            <h4 class="pt-4">Detailed Report On Tasks</h4>
        </div>
        <div class="bg-gradient details py-2 pl-3" style="width: 100%">
            <h5>Report Parameters</h5>
            <h6>Date Range: <b>{{ $startDate }} - {{ $endDate }}</b></h6>
            <h6>Generated On {{ now() }}</h6>
        </div>
        <div class="container" id="stats-list">
            <h5 class="text-center py-3"><b>Tasks Created In Period</b></h5>
            <div class="d-flex flex-row mx-auto">
                <div class="card-custom shadow width-half mx-2">
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                                class="bi bi-list-task" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M2 2.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5V3a.5.5 0 0 0-.5-.5H2zM3 3H2v1h1V3z" />
                                <path
                                    d="M5 3.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM5.5 7a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zm0 4a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9z" />
                                <path fill-rule="evenodd"
                                    d="M1.5 7a.5.5 0 0 1 .5-.5h1a.5.5 0 0 1 .5.5v1a.5.5 0 0 1-.5.5H2a.5.5 0 0 1-.5-.5V7zM2 7h1v1H2V7zm0 3.5a.5.5 0 0 0-.5.5v1a.5.5 0 0 0 .5.5h1a.5.5 0 0 0 .5-.5v-1a.5.5 0 0 0-.5-.5H2zm1 .5H2v1h1v-1z" />
                            </svg>
                            <div class="d-flex flex-column text-right">
                                <p class="text-uppercase text-black-50"><b>TASKS CREATED</b></p>
                                <h4 class="text-right"><b id="tasks-created">{{ $statsSummary['created'] }}</b></h4>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-custom shadow width-half mx-2">
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                                class="bi bi-list-check" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z" />
                            </svg>
                            <div class="d-flex flex-column text-right">
                                <p class="text-uppercase text-black-50"><b>TASKS MARKED AS DONE</b></p>
                                <h4 class="text-right"><b id="tasks-done">{{ $statsSummary['completed'] }}</b></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div>
            @forelse ($stats as $stat)
                <div class="task">
                    <h6 class="task-title">{{ $stat->task_name }}</h6>
                    <p class="task-body">{{ $stat->task_description }}</p>
                    <p class="task-body">{{ $stat->log_name == 'create-task' ? 'Created' : 'Completed' }}
                        <b>{{ $stat->created_at }}</b>
                    </p>
                </div>
            @empty

            @endforelse
        </div>
    </div>
@endsection

@section('js_scripts')

@endsection
