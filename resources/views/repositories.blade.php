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
@endsection

@section('content')
    <div class="container">
        <h4 class="text-black-50 mr-auto pb-3">Repositories</h4>
        @if (count($repositories) === 0)
            <div class="alert alert-info" role="alert" id="alert_1">
                There is currently no information related to your repositories stored within the platform.
            </div>
        @endif
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3" id="repo_container">
            @forelse ($repositories as $repo)
                <div class="col mb-3">
                    <div class="card bg-card shadow h-100">
                        <div class="card-body">
                            <h5><b>{{ $repo->name }}</b></h5>
                            <p>{{ $repo->description }}</p>
                            <p class="card-text">Created {{ $repo->date_created_online }}</p>
                            <p class="card-text">Updated {{ $repo->date_updated_online }}</p>
                            <i class="bi bi-journal-x"></i><span> {{ $repo->issues_count }} Issues</span>
                        </div>
                        <div class="card-footer-custom">
                            <a class="btn btn-primary btn-sm"
                                href="{{ route('view_specific_repository', $repo->id) }}">View</a>
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
            let repoRoute = "{{ route('view_specific_repository', '') }}" + `/${item.id}`;
            const {
                date_created_online: created,
                date_updated_online: updated
            } = item;

            const createdDate = new Date(created);
            const updatedDate = new Date(updated);

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
                        <a class="btn btn-primary btn-sm" href="${repoRoute}">View</a>
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
