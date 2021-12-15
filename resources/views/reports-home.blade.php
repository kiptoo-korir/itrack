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
            border: none;
            /* max-width: 350px; */
        }

        .mx-auto {
            margin-left: auto;
            margin-right: auto;
        }

        .btn-gradient {
            color: white;
            background-color: #343a40;
            background: linear-gradient(45deg, #075db8, #455059);
            transition: background 500ms;
            border: none;
        }

        .btn-gradient:hover {
            color: #fff;
            /* background: linear-gradient(45deg, #075db8, #455059); */
            background: linear-gradient(45deg, #075db8, #930895);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
            border: none;
        }

    </style>
    <link rel="stylesheet" href="{{ asset('css/bootstrap-datepicker.min.css') }}">
@endsection

@section('content')
    <div class="container">
        <div class="">
            <form action="" id="report_form">
                @csrf
                <div class="row">
                    <div class="form-group col-12 col-lg-4 col-md-4">
                        <label for="">Report Type</label>
                        <select name="time_period" id="time_period" class="form-control" onchange="changeWidget()">
                            <option value="lifetime" selected>Lifetime</option>
                            <option value="monthly">Monthly</option>
                            <option value="yearly">Yearly</option>
                        </select>
                    </div>
                    <div class="form-group col-12 col-lg-4 col-md-4" id="year_widget">
                        <label for="">Year</label>
                        <input type="text" class="form-control col-md-6" id="year" name="year">
                    </div>
                    <div class="form-group col-12 col-lg-4 col-md-4" id="month_widget">
                        <div class="row">
                            <div class="col-12 col-md-7 inline">
                                <label for="" class="control-label">Month</label>
                                <select class="form-control" style="color: black" name="month" id="month">
                                    <option value="1" selected>January</option>
                                    <option value="2">February</option>
                                    <option value="3">March</option>
                                    <option value="4">April</option>
                                    <option value="5">May</option>
                                    <option value="6">June</option>
                                    <option value="7">July</option>
                                    <option value="8">August</option>
                                    <option value="9">September</option>
                                    <option value="10">October</option>
                                    <option value="11">November</option>
                                    <option value="12">December</option>
                                </select>
                            </div>
                            <div class="col-12 col-md-5 inline">
                                <label for="" class="control-label">Year</label>
                                <input type="text" class="form-control" style="color: black" name="month_year"
                                    id="month-year">
                            </div>
                        </div>
                    </div>
                    <div class="form-group col-12 col-lg-4 col-md-4 mt-auto px-3">
                        <button class="btn btn-small btn-outline-dark" onclick="filterReports()"
                            type="button">Filter</button> |
                        <button class="btn btn-small btn-outline-dark" type="button" onclick="exportToPDF()">Export</button>
                    </div>
                </div>
            </form>
        </div>
        <div class="row">
            <div class="col-md-6 col-lg-4 col-12 mb-5 px-4">
                <div class="card mt-2 no-border shadow mx-auto">
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                                class="bi bi-unlock-fill" viewBox="0 0 16 16">
                                <path
                                    d="M11 1a2 2 0 0 0-2 2v4a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2H3a2 2 0 0 1-2-2V9a2 2 0 0 1 2-2h5V3a3 3 0 0 1 6 0v4a.5.5 0 0 1-1 0V3a2 2 0 0 0-2-2z" />
                            </svg>
                            <div class="d-flex flex-column text-right">
                                <p class="text-uppercase text-black-50"><b>TIMES LOGGED IN</b></p>
                                <h4 class="text-right"><b id="stats-login">{{ $stats['logIn'] }}</b></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-12 mb-5 px-4">
                <div class="card mt-2 no-border shadow mx-auto">
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
                                <h4 class="text-right"><b id="stats-tasks">{{ $stats['tasks'] }}</b></h4>
                            </div>
                        </div>
                        <a href="{{ route('tasks-stats-view') }}" class="btn btn-gradient btn-sm">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-12 mb-5 px-4">
                <div class="card mt-2 no-border shadow mx-auto">
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                                class="bi bi-sticky-fill" viewBox="0 0 16 16">
                                <path
                                    d="M2.5 1A1.5 1.5 0 0 0 1 2.5v11A1.5 1.5 0 0 0 2.5 15h6.086a1.5 1.5 0 0 0 1.06-.44l4.915-4.914A1.5 1.5 0 0 0 15 8.586V2.5A1.5 1.5 0 0 0 13.5 1h-11zm6 8.5a1 1 0 0 1 1-1h4.396a.25.25 0 0 1 .177.427l-5.146 5.146a.25.25 0 0 1-.427-.177V9.5z" />
                            </svg>
                            <div class="d-flex flex-column text-right">
                                <p class="text-uppercase text-black-50"><b>NOTES CREATED</b></p>
                                <h4 class="text-right"><b id="stats-notes">{{ $stats['notes'] }}</b></h4>
                            </div>
                        </div>
                        <a href="{{ route('notes-stats-view') }}" class="btn btn-gradient btn-sm">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-12 mb-5 px-4">
                <div class="card mt-2 no-border shadow mx-auto">
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                                class="bi bi-kanban-fill" viewBox="0 0 16 16">
                                <path
                                    d="M2.5 0a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h11a2 2 0 0 0 2-2V2a2 2 0 0 0-2-2h-11zm5 2h1a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1zm-5 1a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v7a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3zm9-1h1a1 1 0 0 1 1 1v10a1 1 0 0 1-1 1h-1a1 1 0 0 1-1-1V3a1 1 0 0 1 1-1z" />
                            </svg>
                            <div class="d-flex flex-column text-right">
                                <p class="text-uppercase text-black-50"><b>PROJECTS CREATED</b></p>
                                <h4 class="text-right"><b id="stats-projects">{{ $stats['projects'] }}</b></h4>
                            </div>
                        </div>
                        <a href="{{ route('projects-stats-view') }}" class="btn btn-gradient btn-sm">View Details</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-lg-4 col-12 mb-5 px-4">
                <div class="card mt-2 no-border shadow mx-auto">
                    <div class="card-body">
                        <div class="d-flex flex-row justify-content-between">
                            <svg xmlns="http://www.w3.org/2000/svg" width="50" height="50" fill="currentColor"
                                class="bi bi-calendar-date-fill" viewBox="0 0 16 16">
                                <path
                                    d="M4 .5a.5.5 0 0 0-1 0V1H2a2 2 0 0 0-2 2v1h16V3a2 2 0 0 0-2-2h-1V.5a.5.5 0 0 0-1 0V1H4V.5zm5.402 9.746c.625 0 1.184-.484 1.184-1.18 0-.832-.527-1.23-1.16-1.23-.586 0-1.168.387-1.168 1.21 0 .817.543 1.2 1.144 1.2z" />
                                <path
                                    d="M16 14V5H0v9a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2zm-6.664-1.21c-1.11 0-1.656-.767-1.703-1.407h.683c.043.37.387.82 1.051.82.844 0 1.301-.848 1.305-2.164h-.027c-.153.414-.637.79-1.383.79-.852 0-1.676-.61-1.676-1.77 0-1.137.871-1.809 1.797-1.809 1.172 0 1.953.734 1.953 2.668 0 1.805-.742 2.871-2 2.871zm-2.89-5.435v5.332H5.77V8.079h-.012c-.29.156-.883.52-1.258.777V8.16a12.6 12.6 0 0 1 1.313-.805h.632z" />
                            </svg>
                            <div class="d-flex flex-column text-right">
                                <p class="text-uppercase text-black-50"><b>REMINDERS DISPATCHED</b></p>
                                <h4 class="text-right"><b id="stats-reminders">{{ $stats['reminders'] }}</b></h4>
                            </div>
                        </div>
                        <a href="{{ route('reminder-stats-view') }}" class="btn btn-gradient btn-sm">View Details</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')

@endsection

@section('js_scripts')
    <script src="{{ asset('js/bootstrap-datepicker.min.js') }}"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('time_period').value = "lifetime";
            changeWidget();
            setYearAndMonth();
        });

        document.getElementById('report_form').addEventListener('onsubmit', function(e) {
            e.preventDefault();
        });

        $('#month-year').datepicker({
            minViewMode: 2,
            format: 'yyyy',
        });

        $('#year').datepicker({
            minViewMode: 2,
            format: 'yyyy',
        });

        function changeWidget() {
            const timePeriod = document.getElementById('time_period').value;
            switch (timePeriod) {
                case 'yearly':
                    $('#year_widget').show();
                    $('#month_widget').hide();
                    break;
                case 'monthly':
                    $('#month_widget').show();
                    $('#year_widget').hide();
                    break;
                default:
                    $('#year_widget, #month_widget').hide();
                    break;
            }
        }

        function setYearAndMonth() {
            const today = new Date();
            const year = today.getFullYear();
            const month = today.getMonth() + 1;

            document.getElementById('month').value = month;
            document.getElementById('month-year').value = year;
            document.getElementById('year').value = year;
        }

        function filterReports() {
            const month = document.getElementById('month').value;
            const monthYear = document.getElementById('month-year').value;
            const year = document.getElementById('year').value;
            const period = document.getElementById('time_period').value;
            const requestData = {
                month,
                monthYear,
                year,
                period
            };

            showSpinner();
            $.ajax({
                url: "{{ route('stats-in-period') }}",
                method: "GET",
                data: requestData,
                dataType: "json",
                success: function(data) {
                    const {
                        logIn,
                        tasks,
                        notes,
                        projects,
                        reminders
                    } = data.stats;
                    console.log(logIn, tasks, notes,
                        projects,
                        reminders);
                    document.getElementById('stats-login').innerText = logIn;
                    document.getElementById('stats-tasks').innerText = tasks;
                    document.getElementById('stats-notes').innerText = notes;
                    document.getElementById('stats-projects').innerText = projects;
                    document.getElementById('stats-reminders').innerText = reminders;

                    hideSpinner();
                },
                error: function(jqXhr, textStatus, errorThrown) {

                }
            });
        }

        function exportToPDF() {
            const month = document.getElementById('month').value;
            const monthYear = document.getElementById('month-year').value;
            const year = document.getElementById('year').value;
            const period = document.getElementById('time_period').value;
            const requestData = {
                month,
                monthYear,
                year,
                period
            };

            showSpinner();
            $.ajax({
                url: "{{ route('generate-summary-report') }}",
                method: "GET",
                data: requestData,
                dataType: "json",
                success: function(data) {
                    hideSpinner();
                },
                error: function(jqXhr, textStatus, errorThrown) {

                }
            });
        }
    </script>
@endsection
