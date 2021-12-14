@extends('layouts.app')

@section('css_scripts')
    <style>
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

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <h4 class="text-black-50 mr-auto">Notes</h4>
            <button class="btn btn-outline-primary ml-auto" data-toggle="modal" id="note_btn" data-target="#note_modal">Add
                New Note</button>
        </div>
        <div class="mt-3">
            <input type="text" class="form-control" id="search" placeholder="Search for note..." onkeyup="searchNotes()">
        </div>
        <div class="card-columns mt-3" id="card-container">
        </div>
    </div>
@endsection

@section('modals')
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
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Project</label>
                                <div class="col-md-7">
                                    <select name="project" class="form-control" id="">
                                        @forelse ($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @empty
                                            <option value="">No projects created within the platform.</option>
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
                    <p id="delete_title">Are you sure you want to remove this note?</p>
                </div>
                <div class="modal-footer">
                    <form class="" id="remove_form" action="" method="post">
                        <input type="hidden" name="_token" value="{{ csrf_token() }}">
                        <input type="hidden" id="delete_id" name="note_id" value="">
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
                    <h5 class="modal-title" id="exampleModalCenterTitle">Edit Note</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form action="" class="form-groups" method="POST" id="edit_note_form">
                    <div class="modal-body">
                        @csrf
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
                        <div class="form-group">
                            <div class="row">
                                <label class="col-md-4">Project</label>
                                <div class="col-md-7">
                                    <select name="project" class="form-control" id="edit_project">
                                        <option value="">No Project</option>
                                        @forelse ($projects as $project)
                                            <option value="{{ $project->id }}">{{ $project->name }}</option>
                                        @empty
                                            <option value="">No projects within the platform.</option>
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
@endsection

@section('js_scripts')
    <script>
        $('#note_form').on('submit', function(event) {
            event.preventDefault();
            var createForm = $('#note_form').closest('form');
            var create_record = createForm.serialize();
            var action_url = "{{ route('add_note') }}";
            $.ajax({
                url: action_url,
                method: "POST",
                data: create_record,
                dataType: "json",
                success: function(data) {
                    var note = data.note;
                    appendNotes(note, null);
                    feedback(data.success, 'success');
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

        function showEditModal(id) {
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
                url: "{{ route('get_all_notes') }}",
                method: "GET",
                dataType: "json",
                success: function(data) {
                    var notes = data.notes;
                    // console.log(notes);
                    notes.forEach(appendNotes);
                    // macyInstance.recalculate();
                    // macyInstance.reInit();
                },
            })
        }

        function appendNotes(item, index) {
            const el_class = (item.repo_name) ? "show" : "hide";
            const element = `
                <div class="card shadow note" id="note-${item.id}">
                    <div onclick="showEditModal(${item.id})" class="content">
                        <div class="note-header">
                            ${item.title}
                        </div>
                        <div class="note-text">
                            ${item.message}
                        </div>
                        <div class="text-right">
                            <span class="badge badge-info ${el_class}" style="font-size: 85%" id="badge_repo_${item.id}">
                                ${item.repo_name}</span>
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
            $('#card-container').append(element);
        }

        $(document).ready(function() {
            fetchNotes();
        });

        function searchNotes() {
            var text = $('#search').val().toUpperCase();
            var notes = document.getElementById('card-container').children;

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
@endsection
