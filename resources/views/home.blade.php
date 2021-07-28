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

        .bg-card {
            transition: 0.3s;
        }

        .bg-card:hover {
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.5) !important;
        }

    </style>
    <link rel="stylesheet" href="{{ asset('css/bootstrap-multiselect.min.css') }}">
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <button class="btn btn-outline-primary ml-auto" data-toggle="modal" id="project_btn"
                data-target="#project_modal">Create New Project</button>
        </div>
        <div class="mt-3">
            <input type="text" class="form-control" id="search" placeholder="Search for project..."
                onkeyup="searchProjects()">
        </div>
        <div class="row row-cols-1 row-cols-md-3" id="projects_container">

        </div>
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="project_modal" tabindex="-1" role="dialog" aria-labelledby="#project_btn"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Create Project</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="form-groups" method="POST" id="project_form">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Project Name</label>
                                <div class="col-md-6">
                                    <input type="text" name="name" class="form-control" id="project_name" required>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Description</label>
                                <div class="col-md-7">
                                    <textarea name="description" rows="3" class="form-control" id="project_description"
                                        required></textarea>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Repository</label>
                                <div class="col-md-7">
                                    <select name="repos" class="form-control" id="repositories_linked" multiple="multiple">
                                        @forelse ($repositories as $repo)
                                            <option value="{{ $repo->id }}">{{ $repo->name }} -
                                                ({{ $repo->platform }})</option>
                                        @empty
                                            <option value="">No repositories within the platform.</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Delete modal --}}
    <div class="modal fade" id="removeModal" tabindex="-1" role="dialog" aria-labelledby="removeBtn" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Delete Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="form_result_delete"></span>
                    <p id="delete_title">Are you sure you want to remove this project?</p>
                </div>
                <div class="modal-footer">
                    <form class="" id="remove_form" action="" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="delete_id" name="project_id" value="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="delete_btn" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
<script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
<script>
    $(document).ready(function() {
        $('#repositories_linked').multiselect();
    });
    $('#project_form').on('submit', function(event) {
        event.preventDefault();
        var action_url = "{{ route('add_project') }}";
        $.ajax({
            url: action_url,
            method: "POST",
            data: {
                'name': $('#project_name').val(),
                'description': $('#project_description').val(),
                'repositories': $('#repositories_linked').val(),
                '_token': '{{ csrf_token() }}'
            },
            dataType: "json",
            success: function(data) {
                var project = data.project;
                add_project_cards(project, null);
                feedback(data.success, 'success');
            },
            error: function(jqXhr, textStatus, errorThrown) {
                var errors = JSON.parse(jqXhr.responseText);
                if (jqXhr.status == 422) {
                    feedback(errors.errors.name, 'error');
                    // hideSpinner();
                } else if (jqXhr.status == 400) {
                    feedback(errors.error, 'error');
                    // hideSpinner();
                }
            }
        });
    });

    function fetch_projects() {
        var action_url = "{{ route('get_projects') }}";

        $.ajax({
            url: action_url,
            method: "GET",
            dataType: "json",
            success: function(data) {
                console.log(data);
            },
        })
    }

    function add_project_cards(item, index) {
        var description_class = (item.description) ? 'show' : 'hide';
        var element = `
            <div class="col mb-3">
                <div class="card bg-card h-100">
                    <div class="card-header">${item.name}</div>
                    <div class="card-body">
                        <p class="card-text ${description_class}">Description: ${item.description}</p>
                        <p class="card-text">Created On: ${item.date_created_online}</p>
                        <p class="card-text">Updated On: ${item.date_updated_online}</p>
                    </div>
                </div>
            </div>`;
        $('#projects_container').append(element);
    }
</script>
@endsection
