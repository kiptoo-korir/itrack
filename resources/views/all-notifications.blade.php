@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        .text-black {
            color: #000000;
        }

    </style>
@endsection

@section('content')
    <div class="container">
        <div class="bg-white rounded" id="notifications-container">

        </div>
    </div>
@endsection

@section('modals')

@endsection

@section('js_scripts')
    <script>
        async function fetchUnreadNotifications(page) {
            const route = `{{ route('get-notifications', '') }}/${page}`;
            const response = await fetch(route);
            const body = await response.json();
            const {
                notifications
            } = body;
            notifications.forEach(loadNotifications);
        }

        function loadNotifications(notification, index) {

            
            const {
                notification_message: message,
                notification_title: title,
                created_at
            } = notification;

            let element = '';
            element +=
                `<div class="list-group list-group-flush bg-gradient-lighter border-bottom text-black-50 text-bold">`;
            element += `<a href="#!" class="list-group-item list-group-item-action">`;
            element += `<div class="row align-items-center">`;
            element += `<div class="col ml--2">`;
            element += `<div class="d-flex justify-content-between align-items-center">`;
            element += `<div><h6 class="mb-0 text-sm">${title}</h6></div>`;
            element +=
                `<div class="text-right text-muted"><small>${time_difference(created_at)}</small></div>`;
            element += `</div><p class="mb-0">${message}</p></div>`;
            element += `</div></a></div>`;
            $('#notifications-container').append(element);
        }

        function time_difference(time) {
            const now = new Date();
            time = new Date(time);
            let diff = (now - time) / 1000;
            let time_diff = '';

            if (diff < 60) {
                diff = Math.round(diff);
                (diff == 1) ? time_diff = `${diff} second ago.`: time_diff = `${diff} seconds ago.`;
            } else if (diff < 3600) {
                diff = Math.round(diff / 60);
                (diff == 1) ? time_diff = `${diff} minute ago.`: time_diff = `${diff} minutes ago.`;
            } else if (diff < 86400) {
                diff = Math.round(diff / 3600);
                (diff == 1) ? time_diff = `${diff} hours ago.`: time_diff = `${diff} hours ago.`;
            } else if (diff < 604800) {
                diff = Math.round(diff / 86400);
                (diff == 1) ? time_diff = `${diff} day ago.`: time_diff = `${diff} days ago.`;
            } else if (diff >= 604800) {
                diff = Math.round(diff / 604800);
                (diff == 1) ? time_diff = `${diff} week ago.`: time_diff = `${diff} weeks ago.`;
            }

            return time_diff;
        }
    </script>
@endsection
