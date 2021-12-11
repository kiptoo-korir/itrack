@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        #tbl-tasks thead {
            display: none;
        }

        #tbl-tasks td {
            border-style: none;
            background-color: white !important;
        }

        #tbl-tasks {
            border-style: none;
        }

        .task {
            padding: 0.5rem 0.5rem;
            border-bottom: 1px solid #d9d6d6;
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

    </style>
@endsection

@section('content')
    <div class="container">
        <h4 class="text-black-50 mr-auto pb-3 text-center">Tasks Stats</h4>
        <div class="row justify-content-center">
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
                                <h4 class="text-right"><b id="tasks-created">{{ $tasks }}</b></h4>
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
                                class="bi bi-list-check" viewBox="0 0 16 16">
                                <path fill-rule="evenodd"
                                    d="M5 11.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zm0-4a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5zM3.854 2.146a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 3.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 1 1 .708-.708L2 7.293l1.146-1.147a.5.5 0 0 1 .708 0zm0 4a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0l-.5-.5a.5.5 0 0 1 .708-.708l.146.147 1.146-1.147a.5.5 0 0 1 .708 0z" />
                            </svg>
                            <div class="d-flex flex-column text-right">
                                <p class="text-uppercase text-black-50"><b>TASKS MARKED AS DONE</b></p>
                                <h4 class="text-right"><b id="tasks-done">{{ $tasksDone }}</b></h4>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="">
            <div class="row">
                <div class="col-12 col-md-4 form-group">
                    <label for="">Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="startDate">
                </div>
                <div class="col-12 col-md-4 form-group">
                    <label for="">End Date</label>
                    <input type="date" class="form-control" id="endDate" name="endDate">
                </div>
                <div class="col-12 col-md-4 form-group mt-auto px-3">
                    <button class="btn btn-small btn-outline-dark" onclick="filterReports()" type="button">Filter</button> |
                    <button class="btn btn-small btn-outline-dark" type="button" onclick="exportToPDF()">Export</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped" id="tbl-tasks">
                <thead>
                    <tr>
                        <th>id</th>
                        <th>id</th>
                        <th>id</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('modals')

@endsection

@section('js_scripts')
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script>
        const errorArray = ['', null, undefined];
        document.addEventListener('DOMContentLoaded', function() {
            const url = "{{ route('task-stats') }}";
            fetchTasksTable(url, {});
        });

        function fetchTasksTable(url, data) {
            if ($.fn.dataTable.isDataTable('#tbl-tasks')) {
                $('#tbl-tasks>tbody').children().remove()
                $('#tbl-tasks').DataTable().destroy();
            }

            $('#tbl-tasks').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: url,
                    data: data
                },
                columns: [{
                    data: 'id',
                    name: 'id'
                }, {
                    data: 'task_name',
                    name: 'task_name'
                }, {
                    data: 'task_description',
                    name: 'task_description'
                }],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row) {
                        const card = renderTableCard(row);
                        return card;
                    }
                }, {
                    targets: [1, 2],
                    visible: false,
                    searchable: true
                }]
            });
        }

        function renderTableCard(task) {
            const {
                log_name: logName,
                task_name: taskName,
                task_description: taskDescription,
                created_at: dateCreated
            } = task;

            const dateText = (logName === 'create-task') ? 'Created' : 'Completed';
            element = `
            <div class="task">
                <h6 class="task-title">${taskName}</h6>
                <p class="task-body">${taskDescription}</p>    
                <p class="task-body">${dateText} <b>${dateCreated}</b></p>    
            </div>
        `;
            return element;
        }

        async function fetchTasksBreakdown(startDate, endDate) {
            const route = `{{ route('task-breakdown') }}`;
            const requestBody = {
                startDate,
                endDate
            };

            const response = await fetch(`${route}?${new URLSearchParams(requestBody)}`);

            const body = await response.json();
            const {
                created,
                completed
            } = body;

            document.getElementById('tasks-done').innerHTML = completed;
            document.getElementById('tasks-created').innerHTML = created;
        }

        function filterReports() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            if (errorArray.includes(startDate)) {
                feedback('Please ensure a start date is selected', 'warning');
                return;
            }

            if (errorArray.includes(endDate)) {
                feedback('Please ensure an end date is selected', 'warning');
                return;
            }

            if (endDate < startDate || endDate === startDate) {
                feedback('Please ensure that the end date comes before the start date', 'warning');
                return;
            }

            const requestData = {
                startDate,
                endDate
            };

            const tableRoute = "{{ route('task-stats-period') }}";
            fetchTasksBreakdown(startDate, endDate);
            fetchTasksTable(tableRoute, requestData);
        }
    </script>
@endsection
