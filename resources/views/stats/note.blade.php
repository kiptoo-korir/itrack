@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        #tbl-notes thead {
            display: none;
        }

        #tbl-notes td {
            border-style: none;
            background-color: white !important;
        }

        #tbl-notes {
            border-style: none;
        }

        .note {
            padding: 0.5rem 0.5rem;
            border-bottom: 1px solid #d9d6d6;
        }

        .note-body {
            font-size: 0.97rem;
            color: hsl(0, 0%, 25%);
            padding: 0.15rem 0;
            margin: 0;
        }

        .note-title {
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
        <h4 class="text-black-50 mr-auto pb-3 text-center">Note Stats</h4>
        <div class="">
            <div class="row">
                <div class="col-12 col-md-3 form-group">
                    <label for="">Project</label>
                    <select name="project" id="project-id" class="form-control">
                        <option value="">All Projects</option>
                        @forelse ($projects as $project)
                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                        @empty

                        @endforelse
                    </select>
                </div>
                <div class="col-12 col-md-3 form-group">
                    <label for="">Start Date</label>
                    <input type="date" class="form-control" id="startDate" name="startDate">
                </div>
                <div class="col-12 col-md-3 form-group">
                    <label for="">End Date</label>
                    <input type="date" class="form-control" id="endDate" name="endDate">
                </div>
                <div class="col-12 col-md-3 form-group mt-auto px-3">
                    <button class="btn btn-small btn-outline-dark" onclick="filterReports()" type="button">Filter</button> |
                    <button class="btn btn-small btn-outline-dark" type="button" onclick="exportToPDF()">Export</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-sm table-striped" id="tbl-notes">
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
        const projects = {!! json_encode($projects) !!};

        let projectsList = [];

        function turnIntoIndexedArray(projectsArr) {
            const len = projectsArr.length;
            let arr = [];

            for (i = 0; i < len; i++) {
                let {
                    id,
                    name
                } = projectsArr[i];

                arr[id] = name;
            }

            return arr;
        }

        projectsList = turnIntoIndexedArray(projects);

        document.addEventListener('DOMContentLoaded', function() {
            const url = "{{ route('note-stats') }}";
            fetchNotesTable(url, {});
        });

        function fetchNotesTable(url, data) {
            if ($.fn.dataTable.isDataTable('#tbl-notes')) {
                $('#tbl-notes>tbody').children().remove()
                $('#tbl-notes').DataTable().destroy();
            }

            $('#tbl-notes').DataTable({
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
                    data: 'note_title',
                    name: 'note_title'
                }, {
                    data: 'note_description',
                    name: 'note_description'
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

        function renderTableCard(note) {
            const {
                log_name: logName,
                note_title: noteTitle,
                note_description: noteDescription,
                created_at: dateCreated
            } = note;

            project = note.project;
            const projectName = projectsList[project];

            const className = (projectName) ? 'show' : 'hide';

            element = `
            <div class="note">
                <h6 class="note-title">${noteTitle}</h6>
                <p class="note-body mb-2">${noteDescription}</p>  
                <span class="badge-custom ${className}">${projectName}</span>
                <p class="note-body">Created <b>${dateCreated}</b></p>    
            </div>
        `;
            return element;
        }

        function filterReports() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const project = document.getElementById('project-id').value;

            const requestData = {
                startDate,
                endDate,
                project
            };

            const errorCheck = validateRequest(startDate, endDate);
            if (errorCheck) return;

            const tableRoute = "{{ route('note-stats-period') }}";
            fetchNotesTable(tableRoute, requestData);
        }

        async function exportToPDF() {
            const startDate = document.getElementById('startDate').value;
            const endDate = document.getElementById('endDate').value;
            const project = document.getElementById('project-id').value;

            const requestBody = {
                startDate,
                endDate,
                project
            };

            const errorCheck = validateRequest(startDate, endDate);

            if (errorCheck) return;

            const route = "{{ route('generate-note-report') }}";
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
