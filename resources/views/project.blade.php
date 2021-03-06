@extends('layouts.app')

@section('css_scripts')
    <style>
        .project-tab {
            color: #ffffff;
        }

        .project-tab:hover {
            color: #03254a;
        }

        .project-tab.active {
            background-color: #cee5fd !important;
            color: #03254a !important;
        }

        .project-tab-list {
            background-color: #03254a;
            color: #ffffff;
            border-radius: 0.25rem;
        }

        .project-tab-list li:hover {
            background-color: #cee5fd !important;
            color: #03254a !important;
            border-radius: 0.25rem;
        }

        /* Notes styling */
        .note {
            transition: 0.3s;
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

        .show {
            display: '';
        }

        .hide {
            display: none;
        }

        .text-navy {
            color: #202A44;
        }

        #tbl_rem {
            width: 100% !important;
        }

        .bg-card {
            transition: 0.3s;
        }

        .bg-card:hover {
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.5) !important;
        }

        .card-footer-custom {
            padding: 0.75rem 1.25rem;
            background-color: rgba(0, 0, 0, 0.05);
        }

        .card-footer-custom:last-child {
            border-radius: 0 0 calc(0.25rem - 1px) calc(0.25rem - 1px);
        }

        .card-text {
            font-size: 0.95rem;
        }

    </style>
    <link rel="stylesheet" href="{{ asset('css/bootstrap-multiselect.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
@endsection

@section('content')
    <div class="container">
        <h4 class="text-navy mr-auto pb-3">{{ $projectInfo->name }}</h4>
        <ul class="nav nav-pills mb-3 nav-fill project-tab-list" id="pills-tab" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link project-tab active" id="pills-repositories-tab" data-toggle="pill"
                    href="#pills-repositories" role="tab" aria-controls="pills-repositories"
                    aria-selected="false">Repositories</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link project-tab" id="pills-notes-tab" data-toggle="pill" href="#pills-notes" role="tab"
                    aria-controls="pills-notes" aria-selected="true">Notes</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link project-tab" id="pills-reminders-tab" data-toggle="pill" href="#pills-reminders"
                    role="tab" aria-controls="pills-reminders" aria-selected="false">Reminders</a>
            </li>
        </ul>
        <div class="tab-content" id="project-content-tab">
            {{-- Repositories Tab Pane --}}
            <div class="tab-pane fade show active" id="pills-repositories" role="tabpanel" aria-labelledby="pills-home-tab">
                <div class="alert alert-info hide" role="alert" id="repositories-alert">
                    There are currently no repositories linked to this project.
                </div>
                <div class="d-flex flex-wrap justify-content-center mb-3">
                    <h4 class="text-black-50 mr-auto">Linked Repositories</h4>
                    <button class="btn btn-outline-primary ml-auto" onclick="openLinkedRepositoriesModal()"
                        id="link-btn">Add Repository To Project</button>
                </div>
                <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3" id="repo_container">
                </div>
            </div>
            {{-- Notes Tab Pane --}}
            <div class="tab-pane fade" id="pills-notes" role="tabpanel" aria-labelledby="pills-profile-tab">
                <div class="d-flex flex-wrap justify-content-center">
                    <h4 class="text-black-50 mr-auto">Notes</h4>
                    <button class="btn btn-outline-primary ml-auto" data-toggle="modal" id="note_btn"
                        data-target="#note_modal">Add
                        New Note</button>
                </div>
                <div class="mt-3">
                    <input type="text" class="form-control" id="search" placeholder="Search for note..."
                        onkeyup="searchNotes()">
                </div>
                <div class="card-columns mt-3" id="notes-container">
                </div>
            </div>
            {{-- Reminders Tab Pane --}}
            <div class="tab-pane fade" id="pills-reminders" role="tabpanel" aria-labelledby="pills-contact-tab">
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
            </div>
        </div>

    </div>
@endsection

@section('modals')
    {{-- Add note modal --}}
    <div class="modal fade" id="note_modal" tabindex="-1" role="dialog" aria-labelledby="#note_btn" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Add Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="form-groups" method="POST" id="note_form">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="project" value="{{ $projectInfo->id }}">
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
                                <label class="col-md-4">Note Content</label>
                                <div class="col-md-7">
                                    <textarea name="message" rows="6" class="form-control" required></textarea>
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
    {{-- Delete note modal --}}
    <div class="modal fade" id="removeModal" tabindex="-1" role="dialog" aria-labelledby="removeBtn" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Delete Reminder</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <span id="form_result_delete"></span>
                    <p id="delete_title">Are you sure you want to remove this note?</p>
                </div>
                <div class="modal-footer">
                    <form class="" id=" remove_form" action="" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="delete_id" name="note_id" value="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="delete_btn" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    {{-- Edit Note Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" role="dialog" aria-labelledby="#edit_btn" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Edit Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="form-groups" method="POST" id="edit_note_form">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="project" value="{{ $projectInfo->id }}">
                        <input type="hidden" name="note_id" id="edit_id">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Link new repositories --}}
    <div class="modal fade" id="link-modal" tabindex="-1" role="dialog" aria-labelledby="#link-btn" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalCenterTitle">Link/Unlink Repositories</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="form-groups" method="POST" id="link-form">
                    <div class="modal-body">
                        @csrf
                        <input type="hidden" name="projectId" value="{{ $projectInfo->id }}">
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-3">Repository</label>
                                <div class="col-md-9">
                                    <select name="repos" class="form-control" id="repositories_linked"
                                        multiple="multiple">
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
    {{-- Add new reminder modal --}}
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
                        <input type="hidden" name="project" value="{{ $projectInfo->id }}">
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
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    {{-- Edit Reminder Modal --}}
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
                        <input type="hidden" name="project" id="edit_project">
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
    {{-- Delete Reminder Modal --}}
    <div class="modal fade" id="remove-reminder-modal" tabindex="-1" role="dialog" aria-labelledby="removeBtn"
        aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="remove-reminder-title">Delete Confirmation</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to remove this reminder?</p>
                </div>
                <div class="modal-footer">
                    <form class="" id="remove-reminder-form" action="" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="delete-reminder-id" name="reminder_id" value="">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="submit" id="delete-reminder-btn" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    <script src="{{ asset('js/bootstrap-multiselect.min.js') }}"></script>
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    {{-- General Scripts --}}
    <script>
        $(document).ready(function() {
            fetchNotes();
            fetchRepositories();
            fetchReminders();
            $('#repositories_linked').multiselect();
        });
        const projectId = {{ $projectInfo->id }};
        const notesRoute = "{{ route('get_notes_specific_project', $projectInfo->id) }}";
        const repositoriesRoute = "{{ route('get_repos_specific_project', $projectInfo->id) }}";
        const linkedRepositories = "{{ route('get_linked_repos_array', $projectInfo->id) }}";
    </script>

    {{-- Notes Scripts --}}
    <script>
        $('#note_form').on('submit', function(event) {
            event.preventDefault();
            const createForm = $('#note_form').closest('form');
            const create_record = createForm.serialize();
            const action_url = "{{ route('add_note') }}";
            $.ajax({
                url: action_url,
                method: "POST",
                data: create_record,
                dataType: "json",
                success: function(data) {
                    const note = data.note;
                    appendNotes(note, null);
                    feedback(data.success, 'success');
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    const errors = JSON.parse(jqXhr.responseText);
                    if (jqXhr.status == 422) {
                        feedback(errors.errors.title || errors.errors.message, 'error');
                    } else if (jqXhr.status == 400) {
                        feedback(errors.error, 'error');
                    }
                }
            });
        });

        $('#edit_note_form').on('submit', function(event) {
            event.preventDefault();
            var editForm = $('#edit_note_form').closest('form');
            var edit_record = editForm.serialize();
            var action_url = "{{ route('edit_note') }}";
            $.ajax({
                url: action_url,
                method: "POST",
                data: edit_record,
                dataType: "json",
                success: function(data) {
                    feedback(data.success, 'success');
                    var note_id = $('#edit_id').val();
                    var id = `#note-${note_id}`;
                    var title = $('#edit_title').val();
                    var message = $('#edit_message').val();
                    var note = $(id);
                    note.find('.note-text').html(message);
                    note.find('.note-header').html(title);
                    var b_id = `#badge_repo_${note_id}`;
                    if (data.project_name !== null) {
                        $(b_id).html(data.project_name);
                        $(b_id).removeClass('hide').addClass('show');
                    }
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
            var action_url = "{{ route('delete_note') }}";
            $.ajax({
                url: action_url,
                method: "POST",
                data: delete_record,
                dataType: "json",
                success: function(data) {
                    feedback(data.success, 'success');
                    $('#delete_btn').attr('disabled', true);
                    var delete_id = $('#delete_id').val();
                    $(`#note-${delete_id}`).remove();
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    var errors = JSON.parse(jqXhr.responseText);
                    if (jqXhr.status == 422) {
                        feedback(errors.errors.id, 'error');
                        hideSpinner();
                    } else if (jqXhr.status == 400) {
                        feedback(errors.error, 'error');
                        hideSpinner();
                    }
                }
            });
        });

        function showEditNoteModal(id) {
            $.ajax({
                url: "{{ route('get_note') }}",
                data: {
                    note_id: id
                },
                method: "get",
                success: function(data) {
                    var note = data.note;
                    $('#edit_id').val(note.id);
                    $('#edit_title').val(note.title);
                    $('#edit_message').val(note.message);
                    $('#edit_project').val(note.project);
                    $('#editModal').modal('show');
                },
            });
        }

        $('#removeModal').on('show.bs.modal', function(e) {
            var button = e.relatedTarget;
            var delete_id = $(button).data('id');
            $('#delete_id').val(delete_id);
        });

        function fetchNotes() {
            $.ajax({
                url: notesRoute,
                method: "GET",
                dataType: "json",
                success: function(data) {
                    var notes = data.notes;
                    notes.forEach(appendNotes);
                },
            });
        }

        function appendNotes(item, index) {
            const element = `
                <div class="card shadow note" id="note-${item.id}">
                    <div onclick="showEditNoteModal(${item.id})" class="content">
                        <div class="note-header">
                            ${item.title}
                        </div>
                        <div class="note-text">
                            ${item.message}
                        </div>
                    </div>
                    <div class="dropdown dropleft">
                        <span class="float-right" id="dropdown-${item.id}" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </span>
                        <div class="dropdown-menu" aria-labelledby="dropdown-${item.id}">
                            <a class="dropdown-item" href="javascript:void(0)" data-toggle="modal" data-id="${item.id}" data-target="#removeModal">Delete</a>
                        </div>
                    </div>
                </div>
            `;
            $('#notes-container').append(element);
        }

        function searchNotes() {
            var text = $('#search').val().toUpperCase();
            var notes = document.getElementById('notes-container').children;

            for (i = 0; i < notes.length; i++) {
                var content = notes[i].children[0];
                var title = content.children[0].textContent || content.children[0].innerText;
                var message = content.children[1].textContent || content.children[1].innerText;

                if (title || message) {
                    if (title.toUpperCase().indexOf(text) > -1 || message.toUpperCase().indexOf(text) > -1) {
                        $(notes[i]).show();
                    } else {
                        $(notes[i]).hide();
                    }
                }
            }
        }
    </script>

    {{-- Repositories Scripts --}}
    <script>
        function fetchRepositories() {
            $.ajax({
                url: repositoriesRoute,
                method: "GET",
                dataType: "json",
                success: function(data) {
                    document.getElementById('repo_container').innerHTML = '';
                    const repositories = data.repositories;
                    repositories.forEach(appendRepos);
                },
            });
        }

        function appendRepos(item, index) {
            const repoRoute = "{{ route('view_specific_repository', '') }}" + `/${item.id}`;
            const {
                date_created_online,
                date_updated_online
            } = item;
            const createdDate = new Date(date_created_online);
            const updatedDate = new Date(date_updated_online);
            let elementContent = `
                <div class="card bg-card shadow h-100">
                    <div class="card-body">
                        <h5><b>${item.name}</b></h5>
                        <p>${item.description}</p>
                        <p class="card-text">Created ${createdDate.toDateString()}</p>
                        <p class="card-text">Updated ${updatedDate.toDateString()}</p>
                        <i class="bi bi-journal-x"></i><span> ${item.issues_count} Issues</span>
                    </div>
                    <div class="card-footer-custom">
                        <a class="btn btn-primary btn-sm" href="${repoRoute}">Open</a>
                    </div>
                </div>                    
            `;
            let newElement = document.createElement('div');
            newElement.classList.add('col', 'mb-3');
            newElement.innerHTML = elementContent;
            document.getElementById('repo_container').appendChild(newElement);
        }

        function openLinkedRepositoriesModal() {
            $.ajax({
                url: linkedRepositories,
                method: "GET",
                dataType: "json",
                success: function(data) {
                    const {
                        linkedRepos
                    } = data;
                    $('#repositories_linked').multiselect('deselectAll');
                    (linkedRepos.length > 0) && $('#repositories_linked').multiselect('select', linkedRepos);
                },
            });
            $('#link-modal').modal('show');
        }

        $('#link-form').on('submit', (e) => {
            e.preventDefault();
            const linkedForm = $('#link-form');
            const linkedRepositories = $('#link-form').serialize();
            const postUrl = "{{ route('change_linked_repos') }}";
            $.ajax({
                url: postUrl,
                method: "POST",
                data: {
                    'repositories': $('#repositories_linked').val(),
                    '_token': '{{ csrf_token() }}',
                    'projectId': projectId
                },
                dataType: "json",
                success: function(data) {
                    feedback(data.message, 'success');
                    fetchRepositories();
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    var errors = JSON.parse(jqXhr.responseText);
                    if (jqXhr.status == 422) {
                        feedback(errors.errors.projectId || errors.errors.repos, 'error');
                    } else if (jqXhr.status == 400) {
                        feedback(errors.error, 'error');
                    }
                }
            });
        });
    </script>

    {{-- Reminders Scripts --}}
    <script>
        function fetchReminders() {
            const remindersRoute = "{{ route('get_linked_reminders', $projectInfo->id) }}";
            if ($.fn.dataTable.isDataTable('#tbl_rem')) {
                $('#tbl_rem').DataTable().destroy();
            }

            $('#tbl_rem').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: remindersRoute
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
                            return `<button type = "button" name="remove" onclick="showEditReminderModal(${data})" class="delete btn btn-info btn-sm mb-1">Edit</button> | 
                        <button type = "button" name="remove" data-toggle="modal" data-target="#remove-reminder-modal" data-id="${data}" class="delete btn btn-danger btn-sm mb-1">Delete</button>
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
                    fetchReminders();
                },
                error: function(jqXhr, textStatus, errorThrown) {
                    var errors = JSON.parse(jqXhr.responseText);
                    if (jqXhr.status == 422) {
                        feedback(errors.errors.title || errors.errors.message || errors.error
                            .due_date || errors.error.due_time || errors.error.reminder_id, 'error');
                    } else if (jqXhr.status == 400) {
                        feedback(errors.error, 'error');
                    }
                }
            });
        });

        function showEditReminderModal(id) {
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

        $('#remove-reminder-modal').on('show.bs.modal', function(e) {
            $('#delete-reminder-btn').attr('disabled', false);
            var button = e.relatedTarget;
            var reminder_id = $(button).data('id');
            $('#delete-reminder-id').val(reminder_id);
        });

        $('#remove-reminder-form').on('submit', function(event) {
            event.preventDefault();
            var deleteForm = $('#remove-reminder-form').closest('form');
            var delete_record = deleteForm.serialize();
            var action_url = "{{ route('delete_reminder') }}";
            $.ajax({
                url: action_url,
                method: "POST",
                data: delete_record,
                dataType: "json",
                success: function(data) {
                    feedback(data.success, 'success');
                    $('#delete-reminder-btn').attr('disabled', true);
                    fetchReminders();
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
    </script>
@endsection
