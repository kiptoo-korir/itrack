@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        .bg-card {
            transition: 0.3s;
        }

        .bg-card:hover {
            box-shadow: 0 1rem 2rem rgba(0, 0, 0, 0.5) !important;
        }

    </style>
@endsection

@section('content')
    <div class="container-fluid">
        @if (count($repositories) === 0)
            <div class="alert alert-info" role="alert" id="alert_1">
                There is currently no information related to your repositories stored within the platform.
            </div>
        @endif
        <div class="row row-cols-1 row-cols-md-3" id="repo_container">
            @forelse ($repositories as $repo)
                <div class="col mb-3">
                    <div class="card bg-card shadow h-100">
                        <div class="card-header">{{ $repo->name }}</div>
                        <div class="card-body">
                            <p class="card-text">Description: {{ $repo->description }}</p>
                            <p class="card-text">Created On: {{ $repo->date_created_online }}</p>
                            <p class="card-text">Updated On: {{ $repo->date_updated_online }}</p>
                            <i class="bi bi-journal-x"></i><span> {{ $repo->issues_count }} Issues</span>
                        </div>
                        <div class="card-footer">
                            <a class="btn btn-primary btn-sm">Open</a>
                        </div>
                    </div>
                </div>
            @empty

            @endforelse
        </div>
    </div>
@endsection

@section('modals')

@endsection

@section('js_scripts')
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script>
        Echo.private(`user_repos.${userId}`)
            .listen('RepositoriesFetched', (res) => {
                var repos = res.repos;
                if (repos && repos.length) {
                    $('#alert_1').hide();
                    repos.forEach(add_new_repos);
                }
            });

        function add_new_repos(item, index) {
            let elementContent = `
                <div class="card bg-card shadow h-100">
                    <div class="card-header">${item.name}</div>
                    <div class="card-body">
                        <p class="card-text">Description: ${item.description}</p>
                        <p class="card-text">Created On: ${item.date_created_online}</p>
                        <p class="card-text">Updated On: ${item.date_updated_online}</p>
                        <i class="bi bi-journal-x"></i><span> ${item.issues_count} Issues</span>
                    </div>
                    <div class="card-footer">
                        <a class="btn btn-primary btn-sm">Open</a>
                    </div>
                </div>`;
            let newElement = document.createElement('div');
            newElement.classList.add('col', 'mb-3');
            newElement.innerHTML = elementContent;
            document.getElementById('repo_container').appendChild(newElement);
        }

        function splitDate(actionDate) {
            let newDate = new Date(actionDate);
            let dateString = newDate.toISOString.split('T')[0];
            // let timeString = 
        }
    </script>
@endsection
