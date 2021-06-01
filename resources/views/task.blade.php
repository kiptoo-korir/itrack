@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        #task_list thead {
            display: none;
        }

        #task_list td {
            border-style: none;
            background-color: #f8f9fa;
        }

        #task_list {
            border-style: none;
        }
    </style>
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <h4 class="text-black-50 mr-auto">Tasks</h4>
        <button class="btn btn-outline-primary ml-auto" data-toggle="modal" id="task_btn" data-target="#task_modal">Add New Task</button>
    </div>
    <div class="table-responsive mt-2">
        <table class="table dt-responsive nowrap" id="task_list">
            <thead class="thead-light">
            <tr>
                <th scope="col">Tasks</th>
            </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('toasts')
    @include('components.toasts')
@endsection

@section('modals')
<div class="modal fade" id="task_modal" tabindex="-1" role="dialog" aria-labelledby="#task_btn" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalCenterTitle">Add Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" class="form-groups" method="POST" id="task_form">
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
                            <label class="col-md-4">Description</label>
                            <div class="col-md-7">
                                <textarea name="description" rows="6" class="form-control" required></textarea>
                            </div>
                            <div class="clearfix"></div>
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
<div class="modal fade" id="removeModal" tabindex="-1" role="dialog" aria-labelledby="removeBtn"
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
                <p id="delete_title">Are you sure you want to remove this task?</p>
            </div>
            <div class="modal-footer">
                <form class="" id="remove_form" action="" method="post">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" id="delete_id" name="task_id" value="">
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
                <h5 class="modal-title" id="exampleModalCenterTitle">Edit Task</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form action="" class="form-groups" method="POST" id="edit_task_form">
                <div class="modal-body">
                    @csrf
                    <input type="hidden" name="task_id" id="edit_id">
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
                            <label class="col-md-4">Description</label>
                            <div class="col-md-7">
                                <textarea name="description" id="edit_description" rows="6" class="form-control" required></textarea>
                            </div>
                            <div class="clearfix"></div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <label class="col-md-4">Status</label>
                            <div class="col-md-6">
                                <select name="status" id="status" class="form-control" required>
                                    <option value="Todo">Todo</option>
                                    <option value="Done">Done</option>
                                </select>
                            </div>
                            <div class="clearfix"></div>
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
@endsection

@section('js_scripts')
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#task_list').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: "{{ route('get_tasks') }}"
                },
                // ajax: function (data, callback, settings) {
                //     $.ajax({
                //         url: "{{ route('get_tasks') }}",
                //         type: 'GET',
                //         success:function(data){
                //             callback(data);

                //         }
                //     });
                // },
                columns: [
                    {
                        data: 'description',
                        name: 'description'
                    }
                ],
                columnDefs: [
                    {
                        targets: 0,
                        render: function (data, type, row) {
                            var record = encodeURIComponent(JSON.stringify(row));
                            var card = `<div class="card"><div class="card-header">`;
                            card += (row.status === 'Todo') ? `${row.title}</div>` : `<del>${row.title}</del></div>`
                            card += `<div class="card-body"><div class="card-text">`
                            card += `<div class="float-left">`
                            card += (row.status === 'Todo') ? `${row.description}<br>` : `<del>${row.description}</del><br>`
                            card += (row.status === 'Todo') ? `<span class="badge rounded-pill bg-warning text-dark">Todo</span>` : `<span class="badge rounded-pill bg-success text-white">Done</span>`;                            
                            card += `</div><div class="float-right"><button data-toggle="modal" id="edit_btn" data-target="#editModal" data-record="${record}" class="btn btn-outline-primary">Edit</button>`
                            card += ` | <button data-toggle="modal" id="removeBtn" data-target="#removeModal" data-id="${row.id}" class="btn btn-outline-danger">Delete</button>`;
                            card += `</div></div></div></div>`;
                            return card;
                        }
                    }
                ]
            })
        });

        $('#remove_form').on('submit', function (event) {
            event.preventDefault();
            var deleteForm = $('#remove_form').closest('form');
            var delete_record = deleteForm.serialize();
            var action_url = "{{ route('delete_task')}}";
            $.ajax({
                url: action_url,
                method: "POST",
                data: delete_record,
                dataType: "json",
                success: function (data) {
                    $('#notifysuccess').find('.toast-body').html(data.success);
                    $('#notifysuccess').toast('show');
                    hideSpinner();
                    $('#delete_btn').attr('disabled', true);
                    $('#task_list').DataTable().ajax.reload();
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

        $('#editModal').on('show.bs.modal', function (e) {
            var button = e.relatedTarget;
            var data = $(button).data('record');
            data = JSON.parse(decodeURIComponent(data));
            $('#edit_title').val(data.title);
            $('#edit_description').val(data.description);
            $('#edit_id').val(data.id);
            $('#status').val(data.status);
        });

        $('#removeModal').on('show.bs.modal', function (e) {
            var button = e.relatedTarget;
            var delete_id = $(button).data('id');
            $('#delete_id').val(delete_id);
        });

        (function ($) {
            var task_form = $('#task_form');

            function processTaskForm(e) {
                let formDetails = task_form.serialize();
                showSpinner();

                $.ajax({
                    headers: {
                        "Accept": "application/json"
                    },
                    url: "{{ route('add_task') }}",
                    type: "post",
                    contentType: "application/x-www-form-urlencoded",
                    data: formDetails,
                    success: function (data, textStatus, jQxhr) {
                        $('#notifysuccess').find('.toast-body').html(data.success);
                        $('#notifysuccess').toast('show');
                        hideSpinner();
                        $('#task_list').DataTable().ajax.reload();
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        var errors = JSON.parse(jqXhr.responseText);
                        if (jqXhr.status == 422) {
                            $('#notifyerror').find('.toast-body').html(errors.errors.description || errors.title);
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

            task_form.submit(processTaskForm);
        })(jQuery);

        (function ($) {
            var edit_task = $('#edit_task_form');

            function processEditForm(e) {
                let formDetails = edit_task.serialize();
                showSpinner();

                $.ajax({
                    headers: {
                        "Accept": "application/json"
                    },
                    url: "{{ route('edit_task') }}",
                    type: "post",
                    contentType: "application/x-www-form-urlencoded",
                    data: formDetails,
                    success: function (data, textStatus, jQxhr) {
                        $('#notifysuccess').find('.toast-body').html(data.success);
                        $('#notifysuccess').toast('show');
                        hideSpinner();
                        $('#task_list').DataTable().ajax.reload();
                    },
                    error: function (jqXhr, textStatus, errorThrown) {
                        var errors = JSON.parse(jqXhr.responseText);
                        if (jqXhr.status == 422) {
                            $('#notifyerror').find('.toast-body').html(errors.errors.description || errors.title || errors.status);
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

            edit_task.submit(processEditForm);
        })(jQuery);
    </script>
@endsection