@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        #tbl-projects thead {
            display: none;
        }

        #tbl-projects td {
            border-style: none;
            background-color: white !important;
        }

        #tbl-projects {
            border-style: none;
        }

        .project {
            padding: 0.5rem 0.5rem;
            border-bottom: 1px solid #d9d6d6;
        }

        .project-body {
            font-size: 0.97rem;
            color: hsl(0, 0%, 25%);
            padding: 0.15rem 0;
            margin: 0;
        }

        .project-title {
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
        <h4 class="text-black-50 mr-auto pb-3 text-center">Projects Stats</h4>
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
            <table class="table table-sm table-striped" id="tbl-projects">
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
            const url = "{{ route('project-stats') }}";
            fetchProjectsTable(url, {});
        });

        function fetchProjectsTable(url, data) {
            if ($.fn.dataTable.isDataTable('#tbl-projects')) {
                $('#tbl-projects>tbody').children().remove()
                $('#tbl-projects').DataTable().destroy();
            }

            $('#tbl-projects').DataTable({
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
                    data: 'project_title',
                    name: 'project_title'
                }, {
                    data: 'project_description',
                    name: 'project_description'
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

        function renderTableCard(project) {
            const {
                project_title: projectTitle,
                project_description: projectDescription,
                created_at: dateCreated
            } = project;

            element = `
                <div class="project">
                    <h6 class="project-title">${projectTitle}</h6>
                    <p class="project-body mb-2">${projectDescription}</p>  
                    <p class="project-body">Created <b>${dateCreated}</b></p>    
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

            const tableRoute = "{{ route('project-stats-period') }}";
            fetchProjectsTable(tableRoute, requestData);
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

            const route = "{{ route('generate-project-report') }}";
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
