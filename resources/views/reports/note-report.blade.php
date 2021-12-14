@extends('reports.reports-master')

@section('css_scripts')
    <style>
        .note {
            padding: 0.5rem 0.5rem;
            border-bottom: 1px solid #d9d6d6;
            background-color: white;
        }

        .note-body {
            font-size: 0.97rem;
            color: hsl(0, 0%, 25%);
            padding: 0.15rem 0;
            margin: 0;
        }

        .note-title {
            font-weight: 700;
            font-size: 1.05rem;
        }

        .badge-custom {
            margin-right: 0.75rem;
            padding: 0.4rem;
            border-radius: 0.5rem;
            background-color: hsl(211, 93%, 15%);
            color: #f8f9fa;
            -webkit-hyphens: none;
            -moz-hyphens: none;
            -ms-hyphens: none;
            hyphens: none;
            white-space: nowrap;
            font-size: 90%;
        }

        .bg-gradient {
            background-color: #fff;
            background: linear-gradient(to right, #ffffff, #e9ecef);
            background-image: -webkit-linear-gradient(left, #ffffff 0%, #e9ecef 100%);
        }

        #stats-list>div {
            -webkit-box-flex: 1;
            -webkit-flex: 1;
            flex: 1;
        }

        .details {
            border-bottom: 1px solid hsl(0, 0%, 25%);
        }

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="mx-auto text-center mb-4">
            <img src="{{ asset('img/2.png') }}" alt="iTrack" height="50px">
            <h5 class="text-center text-uppercase my-4 pt-3"><b>{{ $name }}</b></h5>
            <h4 class="pt-4">Detailed Report On Notes</h4>
        </div>
        <div class="bg-gradient details py-2 pl-3">
            <h5>Report Parameters</h5>
            <h6>Date Range: <b>{{ $startDate }} - {{ $endDate }}</b></h6>
            @isset($projectInfo)
                <h6>Limited To Project: <b>{{ $projectInfo->name }}</b></h6>
            @endisset
            <h6>Generated On {{ now() }}</h6>
        </div>
        <div class="container" id="stats-list">
            <h5 class="text-center py-3"><b>Notes Information In Period</b></h5>
        </div>
        <div>
            @forelse ($activity as $note)
                <div class="note">
                    <h6 class="note-title">{{ $note->note_title }}</h6>
                    <p class="note-body">{{ $note->note_description }}</p>
                    <p class="note-body">Created <b>{{ $note->created_at }}</b></p>
                </div>
            @empty

            @endforelse
        </div>
    </div>
@endsection

@section('js_scripts')

@endsection
