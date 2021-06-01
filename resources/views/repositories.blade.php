@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        .bg-card {
            background-color: #075db8;
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <div class="row row-cols-1 row-cols-md-3" id="repo_container">
            
        </div>
    </div>
@endsection

@section('toasts')
    @include('components.toasts')
@endsection

@section('modals')
    
@endsection

@section('js_scripts')
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script>
        const user_id = {{$user_data->id}};
        console.log(user_id);
        Echo.private(`user_repos.${user_id}`)
            .listen('RepositoriesFetched', (res) => {
                var repos = res.repos;
                repos.forEach(add_new_repos);
        });

        function add_new_repos(item, index) {
            var element = `
            <div class="col mb-3">
                <div class="card bg-card text-white h-100">
                    <div class="card-header">${item.name}</div>
                    <div class="card-body">
                        <p class="card-text">Description: ${item.description}</p>
                        <p class="card-text">Created On: ${item.date_updated_online}</p>
                        <p class="card-text">Last Updated: ${item.date_updated_online}</p>
                        <i class="bi bi-journal-x"></i><span> ${item.issues_count} Issues</span>
                    </div>
                </div>
            </div>`;
            $('#repo_container').append(element);
        }
    </script>
@endsection