@extends('layouts.app')

@section('css_scripts')
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@latest/dist/style.css" rel="stylesheet" type="text/css">
    <style>
        #task-table thead {
            display: none;
        }

        #task-table td {
            border-style: none;
            background-color: #f8f9fa;
        }

        #task-table {
            border-style: none;
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <h4 class="text-black-50 mr-auto">Tasks</h4>
        <div class="d-flex flex-row justify-content-end">
            <button class="btn btn-outline-primary ml-auto" data-bs-toggle="modal" id="task-btn"
                data-bs-target="#task-modal">Add
                New Task</button>
        </div>
        <div class="table-responsive mt-2 col-lg-8 mx-auto">
            <table class="table table-striped table-sm" id="task-table">
                <thead class="thead-light">
                    <tr>
                        <th scope="col">Tasks</th>
                        <th scope="col">Tasks</th>
                        <th scope="col">Tasks</th>
                        <th scope="col">Tasks</th>
                        <th scope="col">Tasks</th>
                        <th scope="col">Tasks</th>
                        <th scope="col">Tasks</th>
                        <th scope="col">Tasks</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
@endsection

@section('modals')
    <div class="modal fade" id="task-modal" tabindex="-1" role="dialog" aria-labelledby="#task-btn" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Add Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" class="form-groups" method="POST" id="create-task-form">
                    <div class="modal-body">
                        <div id="create-task-form-feedback"></div>
                        @csrf
                        <div class="form-group mb-1">
                            <div class="row">
                                <label class="col-md-4">Title</label>
                                <div class="col-md-6">
                                    <input type="text" name="title" id="title" class="form-control" required>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group mb-1">
                            <div class="row">
                                <label class="col-md-4">Description</label>
                                <div class="col-md-7">
                                    <textarea name="description" id="description" rows="6" class="form-control" required></textarea>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Delete modal --}}
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="removeBtn" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Delete Confirmation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <span id="delete-task-form-feedback"></span>
                    <p id="delete-title">Are you sure you want to remove this task?</p>
                </div>
                <div class="modal-footer">
                    <form class="" id="delete-task-form" action="" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="delete-id" name="taskId" value="">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" id="delete-btn" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="#edit_btn"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Edit Task</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <form action="" class="form-groups" method="POST" id="edit-task-form">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="task_id" id="edit-id">
                        <div class="form-group mb-2">
                            <div class="row">
                                <label class="col-md-4">Title</label>
                                <div class="col-md-6">
                                    <input type="text" name="title" id="edit-title" class="form-control" required>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <div class="row">
                                <label class="col-md-4">Description</label>
                                <div class="col-md-7">
                                    <textarea name="description" id="edit-description" rows="6" class="form-control" required></textarea>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                        <div class="form-group mb-2">
                            <div class="row">
                                <label class="col-md-4">Status</label>
                                <div class="col-md-6">
                                    <select name="status" id="edit-status" class="form-control form-control" required>
                                        <option value="Todo">Todo</option>
                                        <option value="Done">Done</option>
                                    </select>
                                </div>
                                <div class="clearfix"></div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@latest" type="text/javascript"></script>
    <script></script>
    <script>
        const editModal = new bootstrap.Modal(document.getElementById('editModal'));
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        document.addEventListener('DOMContentLoaded', function() {
            fetchTasks();
        });

        const fetchTasks = async () => {
            const route = "{{ route('get-tasks') }}";
            try {
                const response = await fetch(route);

                const responseBody = await response.json();
                const status = response.status;

                if (status === 422 || status === 500) {
                    simpleToast.toast('An error seems to have occurred. Please try again later', 'error');
                    return;
                }

                if (status !== 200) return;

                const {
                    tasks
                } = responseBody;

                setUpTable(convertObject(tasks));
            } catch (error) {
                console.log(error);
                simpleToast.toast('An error seems to have occurred. Please try again later', 'error');
            }
        };


        const setUpTable = (tasks) => {
            const taskTable = document.getElementById('task-table');
            let datatable = new simpleDatatables.DataTable(taskTable, {
                data: tasks,
                columns: [{
                    select: [1, 2, 3, 4, 5, 6, 7],
                    hidden: true
                }, {
                    select: 0,
                    render: function(data, cell, row) {
                        const task = tasks.data[row.dataIndex];

                        let card = `<div class="card"><div class="card-header">`;
                        card += (task[3] === 'Todo') ? `${task[1]}</div>` :
                            `<del>${task[1]}</del></div>`;
                        card += `<div class="card-body"><div class="card-text">`;
                        card += `<p class="">`;
                        card += (task[3] === 'Todo') ? `${task[2]}` :
                            `<del>${task[2]}</del>`;
                        card +=
                            `</p><div class="d-flex justify-content-between align-items-center">`;
                        card += (task[3] === 'Todo') ?
                            `<span class="badge rounded-pill bg-warning text-dark">Todo</span>` :
                            `<span class="badge rounded-pill bg-success text-white">Done</span>`;
                        card +=
                            `<span class="float-end"><button id="edit_btn" onclick="showEditModal(${task[0]}, '${task[1]}', '${task[2]}', '${task[3]}')" class="btn btn-outline-primary btn-sm">Edit</button>`;
                        card +=
                            ` | <button id="removeBtn" onclick="showDeleteModal(${task[0]})" class="btn btn-outline-danger btn-sm">Delete</button></span>`;
                        card += `</div></div></div></div>`;

                        return card;
                    }
                }]
            });
        };

        const showEditModal = (id, title, description, status) => {
            document.getElementById('edit-title').value = title;
            document.getElementById('edit-description').value = description;
            document.getElementById('edit-status').value = status;
            document.getElementById('edit-id').value = id;
            editModal.show();
        }

        const showDeleteModal = (id) => {
            document.getElementById('delete-btn').disabled = false;
            document.getElementById('delete-id').value = id;
            deleteModal.show();
        }

        document.getElementById('delete-task-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            const deleteUrl = "{{ route('delete-task') }}";
            const taskId = document.getElementById('delete-id').value;
            const _token = "{{ csrf_token() }}";

            showSpinner();
            const response = await fetch(deleteUrl, {
                method: "POST",
                body: JSON.stringify({
                    taskId,
                    _token
                }),
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            });

            const responseBody = await response.json();
            const status = response.status;
            if (status === 422 || status === 500) {
                document.getElementById('delete-task-form-feedback').innerHTML = createFeedbackAlert(
                    handle422Response(responseBody), 'danger');
                hideSpinner();
                return;
            }

            document.getElementById('delete-btn').disabled = true;
            document.getElementById('delete-task-form-feedback').innerHTML = createFeedbackAlert(responseBody
                .message, 'success');
            hideSpinner();
        });

        document.getElementById('create-task-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            const createUrl = "{{ route('add-task') }}";

            const title = document.getElementById('title').value;
            const description = document.getElementById('description').value;

            const _token = "{{ csrf_token() }}";

            showSpinner();
            const response = await fetch(createUrl, {
                method: "POST",
                body: JSON.stringify({
                    title,
                    description,
                    _token
                }),
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
            });

            const responseBody = await response.json();
            const status = response.status;
            if (status === 422 || status === 500) {
                document.getElementById('create-task-form-feedback').innerHTML = createFeedbackAlert(
                    handle422Response(responseBody), 'danger');
                hideSpinner();
                return;
            }

            document.getElementById('create-task-form-feedback').innerHTML = createFeedbackAlert(responseBody
                .message, 'success');
            fetchTasks();
            hideSpinner();
        });

        document.getElementById('edit-task-form').addEventListener('submit', function(event) {
            event.preventDefault();
            console.log('Yes');
        });

        // (function($) {
        //     var task_form = $('#task_form');

        //     function processTaskForm(e) {
        //         let formDetails = task_form.serialize();
        //         showSpinner();

        //         $.ajax({
        //             headers: {
        //                 "Accept": "application/json"
        //             },
        //             url: "{{ route('add-task') }}",
        //             type: "post",
        //             contentType: "application/x-www-form-urlencoded",
        //             data: formDetails,
        //             success: function(data, textStatus, jQxhr) {
        //                 feedback(data.success, 'success');
        //                 hideSpinner();
        //                 $('#task_list').DataTable().ajax.reload();
        //             },
        //             error: function(jqXhr, textStatus, errorThrown) {
        //                 var errors = JSON.parse(jqXhr.responseText);
        //                 if (jqXhr.status == 422) {
        //                     feedback(errors.errors.description || errors.title, 'error');
        //                     hideSpinner();
        //                 } else if (jqXhr.status == 400) {
        //                     feedback(errors.error, 'error');
        //                     hideSpinner();
        //                 }
        //             }
        //         })
        //         e.preventDefault();
        //     }

        //     task_form.submit(processTaskForm);
        // })(jQuery);

        // (function($) {
        //     var edit_task = $('#edit_task_form');

        //     function processEditForm(e) {
        //         let formDetails = edit_task.serialize();
        //         showSpinner();

        //         $.ajax({
        //             headers: {
        //                 "Accept": "application/json"
        //             },
        //             url: "{{ route('edit-task') }}",
        //             type: "post",
        //             contentType: "application/x-www-form-urlencoded",
        //             data: formDetails,
        //             success: function(data, textStatus, jQxhr) {
        //                 feedback(data.success, 'success');
        //                 hideSpinner();
        //                 $('#task_list').DataTable().ajax.reload();
        //             },
        //             error: function(jqXhr, textStatus, errorThrown) {
        //                 var errors = JSON.parse(jqXhr.responseText);
        //                 if (jqXhr.status == 422) {
        //                     feedback(errors.errors.description || errors.title || errors.status, 'error');
        //                     hideSpinner();
        //                 } else if (jqXhr.status == 400) {
        //                     feedback(errors.error, 'error');
        //                     hideSpinner();
        //                 }
        //             }
        //         })
        //         e.preventDefault();
        //     }

        //     edit_task.submit(processEditForm);
        // })(jQuery);
    </script>
@endsection
