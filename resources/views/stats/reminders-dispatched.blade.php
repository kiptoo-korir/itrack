@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        #tbl-reminders thead {
            display: none;
        }

        #tbl-reminders td {
            border-style: none;
            background-color: white !important;
        }

        #tbl-reminders {
            border-style: none;
        }

        .reminder {
            padding: 0.5rem 0.5rem;
            border-bottom: 1px solid #d9d6d6;
        }

        .reminder-body {
            font-size: 0.97rem;
            color: hsl(0, 0%, 25%);
            padding: 0.15rem 0;
            margin: 0;
        }

        .reminder-title {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .badge-custom {
            margin-right: 0.75rem;
            padding: 0.4rem;
            border-radius: 0.5rem;
            background-color: hsl(211, 93%, 15%);
            color: #f8f9fa;
            -webkit-hyphens: none;
            -moz-hyphens: none;
            -ms-hyphens: none;
            hyphens: none;
            white-space: nowrap;
            font-size: 90%;
        }

    </style>
@endsection

@section('content')
    <div class="container">
        <h4 class="text-black-50 mr-auto pb-3 text-center">Reminders Stats</h4>
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
            <table class="table table-sm table-striped" id="tbl-reminders">
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
            const url = "{{ route('reminder-stats') }}";
            fetchRemindersTable(url, {});
        });

        function fetchRemindersTable(url, data) {
            if ($.fn.dataTable.isDataTable('#tbl-reminders')) {
                $('#tbl-reminders>tbody').children().remove()
                $('#tbl-reminders').DataTable().destroy();
            }

            $('#tbl-reminders').DataTable({
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
                    data: 'reminder_title',
                    name: 'reminder_title'
                }, {
                    data: 'reminder_description',
                    name: 'reminder_description'
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

        function renderTableCard(reminder) {
            const {
                reminder_title: reminderTitle,
                reminder_description: reminderDescription,
                created_at: dateCreated
            } = reminder;

            element = `
                <div class="reminder">
                    <h6 class="reminder-title">${reminderTitle}</h6>
                    <p class="reminder-body mb-2">${reminderDescription}</p>  
                    <p class="reminder-body">Created <b>${dateCreated}</b></p>    
                </div>
            `;
            return element;
        }

        function filterReports() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            const requestData = {
                startDate,
                endDate
            };

            const errorCheck = validateRequest(startDate, endDate);
            if (errorCheck) return;

            const tableRoute = "{{ route('reminder-stats-period') }}";
            fetchRemindersTable(tableRoute, requestData);
        }

        async function exportToPDF() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;

            const requestBody = {
                startDate,
                endDate
            };

            const errorCheck = validateRequest(startDate, endDate);

            if (errorCheck) return;

            const route = "{{ route('generate-reminder-report') }}";
            const response = await fetch(`${route}?${new URLSearchParams(requestBody)}`);
        }

        function validateRequest(startDate, endDate) {
            if (errorArray.includes(startDate)) {
                feedback('Please ensure a start date is selected', 'warning');
                return true;
            }

            if (errorArray.includes(endDate)) {
                feedback('Please ensure an end date is selected', 'warning');
                return true;
            }

            if (endDate < startDate || endDate === startDate) {
                feedback('Please ensure that the end date comes before the start date', 'warning');
                return true;
            }
        }
    </script>
@endsection
