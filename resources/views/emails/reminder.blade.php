@component('mail::message')

{{-- Greeting --}}
# Hello, {{$reminder->name}}
You set a reminder to check on:
<br>
<h2>{{$reminder->title}}</h2>
{{$reminder->message}}
<br>

@component('mail::panel')
Reminder was set for {{$reminder->due_date}}
@endcomponent

@isset($reminder->repo_id)
@component('mail::button', ['url' => route('view_specific_repository', $reminder->repo_id)])
Go To Itrack
@endcomponent
@component('mail::button', ['url' => 'https://github.com/' . $reminder->fullname])
Access Repository On Github
@endcomponent
@endisset

@lang('Regards'),<br>
{{ config('app.name') }}

@endcomponent