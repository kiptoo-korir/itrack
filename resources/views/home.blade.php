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
            <button class="btn btn-outline-primary ml-auto" data-toggle="modal" id="project_btn"
                data-target="#project_modal">Create New Project</button>
        </div>
        <div class="mt-3">
            <input type="text" class="form-control" id="search" placeholder="Search for project..."
                onkeyup="searchProjects()">
        </div>
        <div class="card-columns mt-3" id="card-container">
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
                                    <input type="text" name="name" class="form-control" required>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Description</label>
                                <div class="col-md-7">
                                    <textarea name="description" rows="3" class="form-control" required></textarea>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Repository</label>
                                <div class="col-md-7">
                                    <select name="repository" class="form-control" id="">
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
