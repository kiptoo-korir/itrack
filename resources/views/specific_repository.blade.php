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

        #tbl-issues thead {
            display: none;
        }

        #tbl-issues td {
            border-style: none;
            background-color: white !important;
        }

        #tbl-issues {
            border-style: none;
        }

        .issue {
            border-left: 5px solid #075db8;
            padding: 0.5rem 0.5rem;
            border-bottom: 1px solid #d9d6d6;
        }

        .issue-title {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .issue-body {
            font-size: 0.97rem;
            color: hsl(0, 0%, 25%);
            padding: 0.15rem 0;
            margin: 0;
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
            <div class="col-12 pt-5">
                <div class="table-responsive">
                    <table class="table table-striped" id="tbl-issues">
                        <thead>
                            <tr>
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
        const months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October',
            'November', 'December'
        ];
        let total = (languagesArr.length > 0) ? calculateTotalValue(languagesObject) : 0;
        let colours = ['cadetblue', 'tomato', 'darkgoldenrod', 'plum', 'olive', 'peachpuff', 'darkred', 'salmon', 'teal',
            'chocolate'
        ];
        const issuesUrl = "{{ $issuesUrl }}";

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

        function renderTableCard(issue) {
            const {
                state,
                title,
                body,
                date_created_online: dateCreated,
                labels
            } = issue;

            console.log(labels);
            dateFormatted = new Date(dateCreated.slice(0, -3));
            monthString = months[dateFormatted.getMonth()];
            dateString = `${dateFormatted.getDate()} ${monthString}, ${dateFormatted.getFullYear()}`;

            element = `
                <div class="issue">
                    <div class="d-inline">
                        <span class="issue-title">${title}</span>
                    </div>    
                    <p class="issue-body">${body}</p>    
                    <p class="issue-body">Opened ${dateString}</p>    
                </div>
            `;
            return element;
        }
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

        $(document).ready(() => {
            fetchIssuesTable();
        });

        function fetchIssuesTable() {
            if ($.fn.dataTable.isDataTable('#tbl-issues')) {
                $('#tbl-issues').DataTable().destroy();
            }

            $('#tbl-issues').DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: issuesUrl
                },
                columns: [{
                    data: 'id',
                    name: 'id'
                }, ],
                columnDefs: [{
                    targets: 0,
                    render: function(data, type, row) {
                        const element = renderTableCard(row);
                        return element;
                    }
                }]
            });
        }
    </script>
@endsection
