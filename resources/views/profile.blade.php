@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        .profile-text {
            height: 200px;
            width: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
            color: #e9ecef;
            background-color: #075db8;
            font-size: 100px;
        }

        .profile {
            height: 200px;
            width: 200px;
        }

        .profile-container {
            height: 200px;
            width: 200px;
            display: flex;
            justify-content: center;
            align-items: center;
            overflow: hidden;
        }

        div.profile-container > div {
            width: 100%;
            height: 100%;
            transition: background-size 4s ease;
            background-size: 100%;
            background-position: center center;
        }

        div.profile-container:hover > div {
            background-size: 120%;
        }

        .horizontal-divider {
            height: 0;
            margin: 0.5rem 0;
            overflow: hidden;
            border-top: 1px solid gray;
        }

        .text-custom {
            color: rgba(0, 0, 0, 0.5);
        }

        .text-custom:hover {
            color: rgba(0, 0, 0, 0.7);
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6 col-12">
                <div class="row">
                    <div class="col-12">
                        @isset($user_data->photo)
                            <div class="profile-container mx-auto">   
                                <div class="rounded-circle profile" alt="user" id="profile-pic"
                                    style="background-image: url('{{ url('storage/images/' . $user_data->photo) }}')" data-holder-rendered="true"></div>
                            </div>
                        @else
                            <div class="profile-text rounded-circle mx-auto">
                                <span style="color: #e9ecef;" class="text-center text-uppercase font-weight-bolder">{{$user_data->first_letter}}</span>
                            </div>
                        @endisset
                    </div>
                    <div class="col-12 text-center mt-2">
                        <form id="profile_form" action="" method="POST">
                            @csrf
                            <input type="file" name="profile_photo" id="" required>
                            <span id="error" class="invalid-feedback">Yes yes</span>
                            <br>
                            <button type="submit" class="btn btn-outline-primary mt-1">Submit Profile</button>
                        </form>
                    </div>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <div class="ml-auto">
                    <div id="details">
                        <h6>Name: {{$user_data->name}}</h6>
                        <h6>Date Joined: {{$user_data->created_at}}</h6>
                    </div>
                    {{-- data-toggle="modal" data-target="#pan_modal" --}}
                    <a class="btn btn-outline-primary" id="pan_btn" href="{{$request}}"><i class="bi bi-github"></i> Add Access to Github</a>
                    <div class="horizontal-divider"></div>
                    <button class="btn btn-outline-primary" id="pass_btn" data-toggle="modal" data-target="#password_modal">Change Password</button>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-12 mx-auto mt-2">
                <div class="table-responsive">
                    <!-- Recent Activity table -->
                    <h5 class="text-black-50 text-uppercase text-center mb-2">RECENT ACTIVITIES</h5>
                    <table class="table dt-responsive nowrap" id="recent_activity">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">Activity</th>
                            <th scope="col">Time</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
            {{-- <div class="col-md-6 col-12 mx-auto mt-2">
                <div class="table-responsive">
                    <!-- Access Tokens table -->
                    <h5 class="text-black-50 text-uppercase text-center mb-2">ACCESS TOKENS</h5>
                    <table class="table dt-responsive nowrap" id="access_tokens">
                        <thead class="thead-light">
                        <tr>
                            <th scope="col">Status</th>
                            <th scope="col">Platform</th>
                            <th scope="col">Date Added</th>
                            <th scope="col">Actions</th>
                        </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div> --}}
        </div>
    </div>
@endsection

@section('toasts')
    @include('components.toasts')
@endsection

@section('modals')
    <div class="modal fade" id="pan_modal" tabindex="-1" role="dialog" aria-labelledby="pan_btn"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Add Personal Access Token</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="form-groups" method="POST" id="pan_form">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Service Provider</label>
                                <div class="col-md-6">
                                    <select name="platform" id="platform_list" class="form-control" required>
                                        @forelse($platforms as $platform)
                                            <option value="{{$platform->id}}">{{$platform->name}}</option>
                                        @empty
                                            <option value="">No platforms are listed yet.</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Access Token</label>
                                <div class="col-md-6">
                                    <textarea name="access_token" class="form-control" required></textarea>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" onclick="showSpinner()">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="modal fade" id="password_modal" tabindex="-1" role="dialog" aria-labelledby="pass_btn"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Change Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" method="POST" id="pass_form">
                    <div class="modal-body">
                        <div class="form-groups">
                            @csrf
                            <div class="form-group">
                                <div class="row">
                                    <label class="col-md-4">Current Password</label>
                                    <div class="col-md-6">
                                        <input type="password" name="password" id="current_password" class="pass form-control" required>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="col-md-4">New Password</label>
                                    <div class="col-md-6">
                                        <input type="password" name="new_password"  id="new_password" class="pass form-control" required>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="form-group">
                                <div class="row">
                                    <label class="col-md-4">Confirm New Password</label>
                                    <div class="col-md-6">
                                        <input type="password" name="new_password_confirmation" id="new_password_confirmation" class="pass form-control"
                                            required>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-6">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" name="check_pass" id="check_pass" onclick="showPassword()">
    
                                        <label class="form-check-label" for="check_pass">
                                            Show Password
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Delete modal --}}
    <div class="modal fade" id="removeModalChildren" tabindex="-1" role="dialog" aria-labelledby="deleteModal"
        aria-hidden="true">
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
                    <p id="delete_title"></p>
                </div>
                <div class="modal-footer">
                    <form class="" id="removeModal" action="" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="token_id" name="token_id" value="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="delete_btn" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script>
        (function ($) {
            var createForm = $('#profile_form');
            function processForm(e) {
                var record = new FormData(createForm[0]);

                $.ajax({
                    headers: {
                        "Accept": "application/json",
                    },
                    url: "{{ route('profile_pic')}}",
                    type: 'post',
                    data: record,
                    processData: false,
                    contentType: false,
                    cache: false,
                    success: function (data) {
                        $('#profile-pic').css('background-color', 'url("' + data.img + '")');
                    },
                    error: function (xhr) {
                        if (xhr.status == 422) {
                            var errors = JSON.parse(xhr.responseText);
                            $('#notifyerror').find('.toast-body').html(errors.message);
                            $('#notifyerror').toast('show');
                            // if (errors.name) {
                            //     alert('Name is required'); // and so on
                            // }
                        }
                    }
                });
                e.preventDefault();
            }
            createForm.submit(processForm);
        })(jQuery);

        (function ($) {
            var passForm = $('#pass_form');

            function processPassForm(e) {
                let formDetails = passForm.serialize();

                $.ajax({
                    headers: {
                        "Accept": "application/json"
                    },
                    url: "{{ route('change_pass') }}",
                    type: 'post',
                    contentType: 'application/x-www-form-urlencoded',
                    data: formDetails,
                    success: function (data, textStatus, jQxhr) {
                        $('#notifysuccess').find('.toast-body').html(data.success);
                        $('#notifysuccess').toast('show');
                        hideSpinner();
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        var errors = JSON.parse(jqXhr.responseText);
                        if (jqXhr.status == 422) {
                            $('#notifyerror').find('.toast-body').html(errors.errors.new_password || errors.errors.password);
                            $('#notifyerror').toast('show');
                            hideSpinner();
                        } else if (jqXhr.status == 400) {
                            $('#notifyerror').find('.toast-body').html(errors.error);
                            $('#notifyerror').toast('show');
                            hideSpinner();
                        }
                    }
                });

                e.preventDefault();
            }

            passForm.submit(processPassForm);
        })(jQuery);

        (function ($) {
            var pan_form = $('#pan_form');

            function processPanForm(e) {
                let formDetails = pan_form.serialize();

                $.ajax({
                    headers: {
                        "Accept": "application/json"
                    },
                    url: "{{ route('add_pan') }}",
                    type: "post",
                    contentType: "application/x-www-form-urlencoded",
                    data: formDetails,
                    success: function (data, textStatus, jQxhr) {
                        $('#notifysuccess').find('.toast-body').html(data.success);
                        $('#notifysuccess').toast('show');
                        hideSpinner();
                        $('#access_tokens').DataTable().ajax.reload();
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        var errors = JSON.parse(jqXhr.responseText);
                        if (jqXhr.status == 422) {
                            $('#notifyerror').find('.toast-body').html(errors.errors.platform || errors.access_token);
                            $('#notifyerror').toast('show');
                            hideSpinner();
                        } else if (jqXhr.status == 400) {
                            $('#notifyerror').find('.toast-body').html(errors.error);
                            $('#notifyerror').toast('show');
                            hideSpinner();
                        }
                    }
                })
                e.preventDefault();
            }

            pan_form.submit(processPanForm);
        })(jQuery);

        function showPassword() {
            var pass = document.getElementById('current_password');
            var new_pass = document.getElementById('new_password');
            var new_confirm = document.getElementById('new_password_confirmation');

            if (pass.type == "password") {
                pass.type = "text";
                new_pass.type = "text";
                new_confirm.type = "text";
            } else if (pass.type == "text") {
                pass.type = "password";
                new_pass.type = "password";
                new_confirm.type = "password";
            }
        }

        $(document).on('click', '.delete', function () {
            var token_id = $(this).attr('data-id');
            $('#token_id').val(token_id);
            $('#delete_title').html('Are you sure you want to remove this access token?');
            $('#delete_btn').attr('disabled', false);
            $('#deleteModal').modal('show');
        });

        $('#removeModal').on('submit', function (event) {
            event.preventDefault();
            var deleteForm = $('#removeModal').closest('form');
            var record = deleteForm.serialize();
            var action_url = "{{ route('delete_token')}}";
            $.ajax({
                url: action_url,
                method: "POST",
                data: $(this).serialize(),
                dataType: "json",
                success: function (data) {
                    $('#notifysuccess').find('.toast-body').html(data.success);
                    $('#notifysuccess').toast('show');
                    hideSpinner();
                    $('#delete_btn').attr('disabled', true);
                    $('#access_tokens').DataTable().ajax.reload();
                },
                error: function (jqXhr, textStatus, errorThrown) {
                    var errors = JSON.parse(jqXhr.responseText);
                    if (jqXhr.status == 422) {
                        $('#notifyerror').find('.toast-body').html(errors.errors.id);
                        $('#notifyerror').toast('show');
                        hideSpinner();
                    } else if (jqXhr.status == 400) {
                        $('#notifyerror').find('.toast-body').html(errors.error);
                        $('#notifyerror').toast('show');
                        hideSpinner();
                    }
                }
            });
        });

        $(document).ready(function () {
            $('#recent_activity').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('recent_activity') }}"
                },
                columns: [
                    {
                        data: 'log_name',
                        name: 'log_name'
                    },
                    {
                        data: 'order_date',
                        name: 'order_date'
                    },
                ],
                columnDefs: [
                    {
                        targets: 1,
                        render: function (data, type, row) {
                            return `${row.created_at}`;
                        }
                    }
                ]
            });
            $('#access_tokens').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('token_list') }}"
                },
                columns: [
                    {
                        data: 'deleted_at',
                        name: 'deleted_at'
                    },
                    {
                        data: 'name',
                        name: 'name'
                    },
                    {
                        data: 'order_date',
                        name: 'order_date'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                ],
                columnDefs: [
                    {
                        targets: 0,
                        searchable: false,
                        orderable: false,
                        render: function (data, type, row) {
                            if (data) {
                                return `<i class="bi bi-exclamation-circle-fill" style="color: #dc3545"></i>`
                            } else {
                                return `<i class="bi bi-check-circle-fill text-success"></i>`
                            }
                        }
                    },
                    {
                        targets: 3,
                        createdCell: function (cell, cellData, rowData, rowIndex, colIndex) {
                            if (rowData.deleted_at) {
                                $(cell).find("button").attr('disabled', true);
                            }
                        }
                    },
                    {
                        targets: 2,
                        render: function (data, type, row) {
                            return `${row.created_at}`;
                        }
                    }
                ]
            });
        });
    </script>
@endsection