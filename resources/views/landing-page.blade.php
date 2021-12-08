@extends('layouts.landing')

@section('css_scripts')
    <link href="https://fonts.googleapis.com/css?family=Nunito+Sans" rel="stylesheet">
    <style>
        .hero-text {
            font-family: -apple-system, BlinkMacSystemFont, "Nunito Sans", "Roboto", sans-serif;
        }

        .feature {
            margin-left: 0.5rem !important;
            margin-right: 0.5rem !important;
        }

        @media (min-width: 992px) {
            .hero-sub-text-container {
                width: 50%;
            }

            .img-fluid {
                max-width: 80%;
            }

            .feature {
                width: 75%;
                margin-left: auto !important;
                margin-right: auto !important;
            }
        }

        .text-black-70 {
            color: rgba(0, 0, 0, 0.7) !important
        }

        .feature-odd {
            border-left: 5px solid #9415b7;
        }

        .feature-even {
            border-right: 5px solid #9415b7;
        }

        .rounded-custom {
            border-radius: 0.5rem;
        }

        @media (max-width: 576px) {

            .feature-odd,
            .feature-even {
                border-left: none;
                border-right: none;
            }
        }

    </style>
@endsection

@section('content')
    <div class="container">
        <div id="hero-section">
            <div class="text-center pt-5">
                <h2 class="hero-text text-bold">
                    You Develop It, We Track It.
                </h2>
                <div class="hero-sub-text-container mx-auto">
                    <h5 class="hero-text text-black-70">
                        Track and manage your repositories from a single platform and maximize your productivity in your
                        developmental
                        work.
                    </h5>
                </div>
                <div class="mt-4">
                    <a href="{{ route('register') }}" class="btn btn-navy">Sign Up Now</a>
                </div>
                <img src="{{ asset('img/frame_home.png') }}" alt="" class="img-fluid">
            </div>
        </div>

        <div class="row py-5 my-3 bg-white shadow-sm rounded-custom feature feature-odd">
            <div class="col-md-6 col-12 my-auto">
                <h5 class="hero-text text-bold">
                    Group Multiple Repositories Together
                </h5>
                <div class="">
                    <p class="hero-text text-black-70">
                        Link multiple repositories from github to a single repository and track all of them from the single
                        project.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <img src="{{ asset('img/frame_projects.png') }}" alt="" class="img-fluid">
            </div>
        </div>
        <div class="row flex-row-reverse py-5 my-3 bg-white shadow-sm rounded-custom feature feature-even">
            <div class="col-md-6 col-12 my-auto">
                <h5 class="hero-text text-bold">
                    Keep Your Ideas And Thoughts In Store
                </h5>
                <div class="">
                    <p class="hero-text text-black-70">
                        Attach notes to your repositories through the projects and avoid confusion as the notes are attached
                        to specific repositories
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <img src="{{ asset('img/frame_notes.png') }}" alt="" class="img-fluid">
            </div>
        </div>
        <div class="row py-5 my-3 bg-white shadow-sm rounded-custom feature feature-odd">
            <div class="col-md-6 col-12 my-auto">
                <h5 class="hero-text text-bold">
                    Boost Your Productivity Through Reminders
                </h5>
                <div class="">
                    <p class="hero-text text-black-70">
                        Get reminded of even the most minute task through your email and right on the iTrack Platform
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <img src="{{ asset('img/frame_reminders.png') }}" alt="" class="img-fluid">
            </div>
        </div>
        <div class="row flex-row-reverse py-5 my-3 bg-white shadow-sm rounded-custom feature feature-even">
            <div class="col-md-6 col-12 my-auto">
                <h5 class="">
                    Track Your Usage Stats
                </h5>
                <div class="">
                    <p class="hero-text text-black-70">
                        Access your data at any time, review and provide feedback to make the app better.
                    </p>
                </div>
            </div>
            <div class="col-md-6 col-12">
                <img src="{{ asset('img/frame_reports.png') }}" alt="" class="img-fluid">
            </div>
        </div>
    </div>
@endsection

@section('js_scripts')
    <script>

    </script>
@endsection
