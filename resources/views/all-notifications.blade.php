@extends('layouts.app')

@section('css_scripts')
    <link rel="stylesheet" href="{{ asset('css/datatables.min.css') }}">
    <style>
        .text-black {
            color: #000000;
        }

        .text-95 {
            font-size: 95%;
        }

        .text-bold {
            font-weight: bold;
        }

        .text-bold h6 {
            font-weight: bold;
        }

        .unread {
            border-left: 5px solid #b80728;
        }

        .text-align-end {
            text-align: end;
        }

        #filter-dropdown {
            z-index: 10;
        }

        .hide {
            display: none;
        }

        .back-to-top {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Fixed/sticky position */
            bottom: 40px;
            /* Place the button at the bottom of the page */
            right: 30px;
            /* Place the button 30px from the right */
            z-index: 99;
            /* Make sure it does not overlap */
            border: none;
            /* Remove borders */
            outline: none;
            /* Remove outline */
            background-color: transparent;
            /* Set a background color */
            color: #075db8;
            /* Text color */
            cursor: pointer;
            /* Add a mouse pointer on hover */
        }

        @media screen and (prefers-reduced-motion: no-preference) {
            html {
                scroll-behavior: smooth;
            }
        }

    </style>
@endsection

@section('content')
    <button onclick="backToTop()" class="back-to-top" id="btn-top" title="Go to top">
        <svg xmlns="http://www.w3.org/2000/svg" width="40" height="40" fill="currentColor" class="bi bi-arrow-up-circle-fill"
            viewBox="0 0 16 16">
            <path
                d="M16 8A8 8 0 1 0 0 8a8 8 0 0 0 16 0zm-7.5 3.5a.5.5 0 0 1-1 0V5.707L5.354 7.854a.5.5 0 1 1-.708-.708l3-3a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1-.708.708L8.5 5.707V11.5z" />
        </svg>
    </button>

    <div class="container">
        <h3 class="text-black">All Notifications</h3>
        <div class="position-sticky border-bottom pb-2 mb-3">
            <div class="row justify-content-between">
                <div class="col align-self-center">
                    <div class="row">
                        <div class="col-6">
                            <select name="" id="select-filter" class="form-control">
                                <option value="" disabled selected>Filters</option>
                                <option value="all">Show All</option>
                                <option value="unread">Show Unread</option>
                                <option value="read">Show Read</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="bg-white rounded" id="notifications-container">

        </div>
        <div class="text-center py-3">
            <button class="btn btn-outline-dark" id="btn-show-more" onclick="showMore()">Show More Notifications</button>
        </div>
    </div>
@endsection

@section('modals')

@endsection

@section('js_scripts')
    <script>
        let endOfContent = false;
        let notificationPage = 1;

        async function fetchUnreadNotifications(page) {
            showSpinner();
            const route = `{{ route('get-notifications', '') }}/${page}`;
            const response = await fetch(route);
            const body = await response.json();
            const {
                notifications,
                endOfContentStatus
            } = body;
            hideSpinner();
            notifications.forEach(loadNotifications);
            filterNotifications();
            endOfContent = endOfContentStatus;

            if (endOfContent) {
                document.getElementById('btn-show-more').disabled = true
            }
        }

        function loadNotifications(notification, index) {
            const {
                id,
                notification_message: message,
                notification_title: title,
                created_at,
                read_at
            } = notification;

            const elementClass = (read_at === null) ? 'text-bold unread' : 'read';
            let element = '';
            element =
                `<div class="list-group list-group-flush border-bottom ${elementClass} individual-not" id="not-view-${id}">
                    <div class="list-group-item list-group-item-action">
                        <div class="row align-items-center">
                            <div class="col ml--2">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div><h6 class="mb-0 text-sm text-black">${title}</h6></div>
                                    <div class="text-right text-muted"><small>${time_difference(created_at)}</small></div>
                                </div>
                                <p class="mb-0 text-black text-95">${message}</p>
                            </div>
                        </div>
                        <div class="text-right text-notification"><a class="mark-as-read" href="javascript:void(0)"
                                onclick="changeReadStatus('${id}')">Mark as read</a></div>
                    </div>
                </div>`;
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

        function changeReadStatus(notificationId) {
            const notElement = document.getElementById(`not-view-${notificationId}`);
            notElement.classList.remove('text-bold', 'unread');
            notElement.classList.add('read');
            markAsRead(notificationId);
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('select-filter').value = 'all';
            fetchUnreadNotifications(1);
            notificationPage++;
        });

        document.getElementById('select-filter').addEventListener('change', filterNotifications);

        function showAllNotifications() {
            let elements = document.querySelectorAll('.individual-not');
            elements.forEach((element) => {
                element.classList.remove('hide');
            });
        }

        function filterNotifications() {
            const filterState = document.getElementById('select-filter').value;

            if (filterState === 'all') {
                showAllNotifications();
                return;
            }

            if (filterState === 'read') {
                showAllNotifications();
                let elements = document.querySelectorAll('.unread');
                elements.forEach((element) => {
                    element.classList.remove('hide');
                    element.classList.add('hide');
                });
                return;
            }

            if (filterState === 'unread') {
                showAllNotifications();
                let elements = document.querySelectorAll('.read');
                elements.forEach((element) => {
                    element.classList.remove('hide');
                    element.classList.add('hide');
                });
                return;
            }
        }

        window.onscroll = function() {
            scrollFunction()
        };

        function scrollFunction() {
            const backToTopBtn = document.getElementById('btn-top');
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                backToTopBtn.style.display = "block";
            } else {
                backToTopBtn.style.display = "none";
            }
        }

        function backToTop() {
            document.body.scrollTop = 0; // For Safari
            document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE and Opera
        }

        function showMore() {
            if (endOfContent) return;

            fetchUnreadNotifications(notificationPage);
            notificationPage++;
        }
    </script>
@endsection
