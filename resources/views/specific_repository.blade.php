@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
    </style>
@endsection

@section('content')
    <div class="container-fluid">

    </div>
@endsection

@section('modals')

@endsection

@section('js_scripts')
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script>
        let repositoryId = {{ $repository->id }};
        Echo.private(`languages_in_repo.${repositoryId}`)
            .listen('FetchLanguagesInRepo', (response) => {
                // let languages = response.languages;
                console.log(response);
                // if (repos && repos.length) {
                //     $('#alert_1').hide();
                //     repos.forEach(add_new_repos);
                // }
            });
    </script>
@endsection
