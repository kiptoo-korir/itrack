@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        .languages {
            background-color: rgb(187, 183, 183);
            height: 10px;
            border-radius: 5px;
            display: flex;
            overflow: hidden;
            border: #d0cdc9 1px solid;
        }

        .languages-slider:not(:first-child) {
            margin-left: 2px;
        }

        #languages-card {
            filter: blur(3px);
            transition: 2s ease-in-out;
        }

        .list-style-none {
            list-style: none;
            padding-left: 0;
            display: flex;
            flex-direction: row;
            flex-wrap: wrap;
        }

        .lang-circle {
            height: 0.8em;
            width: 0.8em;
            border-radius: 50%;
            margin-right: 5px;
        }

        .lang-list-item {
            padding-right: 25px;
            display: flex;
            align-items: center;
            justify-content: left;
            font-size: 0.9rem;
        }

        .lang-list {
            padding-top: 10px;
        }

        .percentage {
            margin-left: 10px;
        }

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-12 col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h6 class="font-weight-bold">{{ $repository->name }} </h6>
                        <h6>Opened On: {{ $repository->date_created_online }}</h6>
                        <h6>Hosted On: Github</h6>
                    </div>
                </div>
            </div>
            <div class="col-12 col-md-6">
                <div class="card" id="languages-card">
                    <div class="card-body">
                        <h6>LANGUAGES</h6>
                        <span class="languages" id="languages-slider">
                            @foreach ($languages as $key => $lang)
                                <span class="languages-slider" id="lang-{{ $key }}">
                                </span>
                            @endforeach
                        </span>
                        <ul class="list-style-none lang-list" id="languages-list">
                            @foreach ($languages as $key => $lang)
                                <li class="lang-list-item">
                                    <span class="lang-circle" id="circle-{{ $key }}">
                                    </span>
                                    <span>
                                        {{ $key }}
                                    </span>
                                    <span class="text-muted percentage" id="percentage-{{ $key }}"></span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
            <div class="col-12">
                <div class="table-responsive">
                    <table class="table table-striped" id="tbl-issues">
                        <thead>
                            <tr>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('modals')

@endsection

@section('js_scripts')
    <script src="{{ asset('js/datatables.min.js') }}"></script>
    <script>
        function calculateTotalValue(languageObject) {
            return (Object.values(languageObject)).reduce(reducer);
        }

        let repositoryId = {{ $repository->id }};
        let languagesObject = {!! json_encode($languages->toArray(), JSON_HEX_TAG) !!};
        let languagesArr = Object.entries(languagesObject);
        const reducer = (previousValue, currentValue) => previousValue + currentValue;
        let total = (languagesArr.length > 0) ? calculateTotalValue(languagesObject) : 0;
        let colours = ['cadetblue', 'tomato', 'darkgoldenrod', 'plum', 'olive', 'peachpuff', 'darkred', 'salmon', 'teal',
            'chocolate'
        ];

        Echo.private(`languages_in_repo.${repositoryId}`)
            .listen('FetchLanguagesInRepo', (response) => {
                let languages = response.languages;
                let languagesArr = Object.entries(languages);
                let total = calculateTotalValue(languages);
                if (languagesArr.length > 0) {
                    setupLanguagesCard(languagesArr, total);
                }
            });

        Echo.private(`issues-in-repo.${repositoryId}`)
            .listen('FetchIssuesInRepoEvent', (response) => {
                // let languages = response.languages;
                console.log(response);
                // if (repos && repos.length) {
                //     $('#alert_1').hide();
                //     repos.forEach(add_new_repos);
                // }
            });
    </script>
    <script>
        function styleLanguagesComponent(languages, totalValue) {
            let i = parseInt(Math.random() * 10);
            totalValue = parseInt(totalValue);
            languages.forEach((value, index) => {
                i = (i > (colours.length - 1)) ? 0 : i;
                let width = (value[1] / totalValue * 100);
                let langComponent = document.getElementById(`lang-${value[0]}`);
                let circleComponent = document.getElementById(`circle-${value[0]}`);
                let percentageComponent = document.getElementById(`percentage-${value[0]}`);
                langComponent.style.width = `${width}%`;
                langComponent.style.backgroundColor = colours[i];
                circleComponent.style.backgroundColor = colours[i];
                percentageComponent.textContent = `${width.toFixed(1)}%`;
                i++;
            });

            document.getElementById('languages-card').style.filter = 'blur(0)';
        }

        function clearLanguagesComponent() {
            document.getElementById('languages-slider').innerHTML = '';
            document.getElementById('languages-list').innerHTML = '';
            document.getElementById('languages-card').style.filter = 'blur(3px)';
        }

        function setupLanguagesCard(languages, totalValue) {
            clearLanguagesComponent();
            let sliderElement = '';
            let listElement = '';
            languages.forEach((language) => {
                // console.log(language);
                sliderElement += `<span class="languages-slider" id="lang-${language[0]}"></span>`;
                listElement += `
                <li class="lang-list-item">
                    <span class="lang-circle" id="circle-${language[0]}">
                    </span>
                    <span>
                        ${language[0]}
                    </span>
                    <span class="text-muted percentage" id="percentage-${language[0]}"></span>
                </li>`;
            });
            document.getElementById('languages-slider').innerHTML = sliderElement;
            document.getElementById('languages-list').innerHTML = listElement;
            styleLanguagesComponent(languages, totalValue);
        }

        (languagesArr.length > 0) && styleLanguagesComponent(languagesArr, total);
    </script>
@endsection
