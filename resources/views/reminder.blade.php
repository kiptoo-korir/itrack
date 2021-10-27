@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        .note {
            /* background-color: #ffffff; */
            /* margin: 10px; */
            /* width: 300px; */
            /* box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2); */
            transition: 0.3s;
            /* border-radius: 5px; 5px rounded corners */
            /* margin-bottom: 20px; */
        }

        /* On mouse-over, add a deeper shadow */
        .note:hover {
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.5) !important;
        }

        /* Add some padding inside the card container */
        .content {
            padding: 2px 16px;
        }

        .note-header {
            border-bottom: 1px solid rgba(0, 0, 0, 0.125);
            display: flex;
            justify-content: left;
            font-weight: 600;
            padding: 0.1rem;
        }

        .note-text {
            padding: 0.1rem;
        }

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h4 class="text-black-50 mr-auto">Reminders</h4>
            <button class="btn btn-outline-primary ml-auto" data-toggle="modal" id="rem_btn"
                data-target="#reminder_modal">Add
                New Reminder</button>
        </div>
        <div class="table-responsive mt-2">
            <table class="table" id="tbl_rem">
                <thead>
                    <tr>
                        <th>Title</th>
                        <th>Content</th>
                        <th>Date Due</th>
                        <th>Date Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="reminder_modal" tabindex="-1" role="dialog" aria-labelledby="#rem_btn"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Add Reminder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="form-groups" method="POST" id="reminder_form">
                    <div class="modal-body">
                        @csrf
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Title</label>
                                <div class="col-md-6">
                                    <input type="text" name="title" class="form-control" required>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Reminder Content</label>
                                <div class="col-md-7">
                                    <textarea name="message" rows="6" class="form-control" required></textarea>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Date</label>
                                <div class="col-md-4">
                                    <input type="date" name="due_date" class="form-control">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Time</label>
                                <div class="col-md-4">
                                    <input type="time" name="due_time" class="form-control">
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="timezone" id="timezone">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Project</label>
                                <div class="col-md-7">
                                    <select name="project" class="form-control" id="">
                                        <option value="">None selected</option>
                                        @forelse ($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @empty
                                            <option value="">No projects currently within the platform.</option>
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
                    <p id="delete_title">Are you sure you want to remove this reminder?</p>
                </div>
                <div class="modal-footer">
                    <form class="" id="remove_form" action="" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="delete_id" name="reminder_id" value="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="delete_btn" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="#edit_btn" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Edit Reminder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="form-groups" method="POST" id="edit_reminder_form">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="reminder_id" id="edit_id">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Title</label>
                                <div class="col-md-6">
                                    <input type="text" name="title" id="edit_title" class="form-control" required>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Message</label>
                                <div class="col-md-7">
                                    <textarea name="message" id="edit_message" rows="6" class="form-control"
                                        required></textarea>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <div class="col-12">
                                    <p class="mb-0 text-muted" style="font-size: 0.9rem">Date is in the format MM-DD-YYYY
                                    </p>
                                </div>
                                <label class="col-md-4">Date</label>
                                <div class="col-md-4">
                                    <input type="date" name="due_date" class="form-control" id="edit_date">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Time</label>
                                <div class="col-md-4">
                                    <input type="time" name="due_time" class="form-control" id="edit_time">
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Project</label>
                                <div class="col-md-7">
                                    <select name="project" class="form-control" id="edit_project">
                                        <option value="">None selected</option>
                                        @forelse ($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @empty
                                            <option value="">No repositories within the platform.</option>
                                        @endforelse
                                    </select>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <input type="hidden" name="timezone" id="edit_timezone">
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            fetch_reminders();
        });

        function fetch_reminders() {
            if ($.fn.dataTable.isDataTable('#tbl_rem')) {
                $('#tbl_rem').DataTable().destroy();
            }

            $('#tbl_rem').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('get_all_reminders') }}"
                },
                columns: [{
                        data: 'title',
                        name: 'title'
                    },
                    {
                        data: 'message',
                        name: 'message'
                    },
                    {
                        data: 'order_due',
                        name: 'order_due'
                    },
                    {
                        data: 'order_created',
                        name: 'order_created'
                    },
                    {
                        data: 'id',
                        name: 'id'
                    }
                ],
                columnDefs: [{
                        targets: 2,
                        render: function(data, type, row) {
                            return `${row.due_date}`;
                        }
                    },
                    {
                        targets: 3,
                        render: function(data, type, row) {
                            return `${row.created}`;
                        }
                    },
                    {
                        targets: 4,
                        render: function(data, type, row) {
                            return `<button type = "button" name="remove" onclick="showEditModal(${data})" class="delete btn btn-info btn-sm mb-1">Edit</button> | 
                        <button type = "button" name="remove" data-toggle="modal" data-target="#removeModal" data-id = "${data}" class="delete btn btn-danger btn-sm mb-1">Delete</button>
                        `
                        }
                    }
                ]
            });
        }

        $('#reminder_form').on('submit', function(event) {
            event.preventDefault();
            var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            $('#timezone').val(tz);
            var createForm = $('#reminder_form').closest('form');
            var create_record = createForm.serialize();
            var action_url = "{{ route('add_reminder') }}";
            $.ajax({
                url: action_url,
                method: "POST",
                data: create_record,
                dataType: "json",
                success: function(data) {
                    feedback(data.success, 'success');
                    fetch_reminders();
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    var errors = JSON.parse(jqXhr.responseText);
                    if (jqXhr.status == 422) {
                        feedback(errors.errors.title || errors.errors.message || errors.error
                            .due_date || errors.error.due_time || errors.error.reminder_id, 'error');
                        // hideSpinner();
                    } else if (jqXhr.status == 400) {
                        feedback(errors.error, 'error');
                        // hideSpinner();
                    }
                }
            });
        });

        $('#edit_reminder_form').on('submit', function(event) {
            event.preventDefault();
            var tz = Intl.DateTimeFormat().resolvedOptions().timeZone;
            $('#edit_timezone').val(tz);
            var editForm = $('#edit_reminder_form').closest('form');
            var edit_record = editForm.serialize();
            var action_url = "{{ route('edit_reminder') }}";
            $.ajax({
                url: action_url,
                method: "POST",
                data: edit_record,
                dataType: "json",
                success: function(data) {
                    feedback(data.success, 'success');
                    $('#tbl_rem').DataTable().ajax.reload();
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    var errors = JSON.parse(jqXhr.responseText);
                    if (jqXhr.status == 422) {
                        feedback(errors.errors.title || errors.errors.message, 'error');
                        // hideSpinner();
                    } else if (jqXhr.status == 400) {
                        feedback(errors.error, 'error');
                        // hideSpinner();
                    }
                }
            });
        });

        $('#remove_form').on('submit', function(event) {
            event.preventDefault();
            var deleteForm = $('#remove_form').closest('form');
            var delete_record = deleteForm.serialize();
            var action_url = "{{ route('delete_reminder') }}";
            $.ajax({
                url: action_url,
                method: "POST",
                data: delete_record,
                dataType: "json",
                success: function(data) {
                    feedback(data.success, 'success');
                    $('#delete_btn').attr('disabled', true);
                    $('#tbl_rem').DataTable().ajax.reload();
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    var errors = JSON.parse(jqXhr.responseText);
                    if (jqXhr.status == 422) {
                        feedback(errors.errors.reminder_id, 'error');
                        // hideSpinner();
                    } else if (jqXhr.status == 400) {
                        feedback(errors.error, 'error');
                        // hideSpinner();
                    }
                }
            });
        });

        function showEditModal(id) {
            $.ajax({
                url: "{{ route('get_reminder') }}",
                data: {
                    reminder_id: id
                },
                method: "get",
                success: function(data) {
                    var reminder = data.reminder;
                    $('#edit_id').val(id);
                    $('#edit_title').val(reminder.title);
                    $('#edit_message').val(reminder.message);
                    $('#edit_project').val(reminder.project);
                    $('#edit_date').val(reminder.year);
                    $('#edit_time').val(reminder.time);
                    $('#editModal').modal('show');
                },
            });
        }

        $('#removeModal').on('show.bs.modal', function(e) {
            $('#delete_btn').attr('disabled', false);
            var button = e.relatedTarget;
            var reminder_id = $(button).data('id');
            $('#delete_id').val(reminder_id);
        });
    </script>
@endsection
