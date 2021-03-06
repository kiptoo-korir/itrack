@component('mail::message')

{{-- Greeting --}}
# Hello, {{$reminder->name}}

@if (isset($project->id))
You set a reminder on the project <b>{{$reminder->project_name}}</b> to check on:
@else
You set a reminder to check on:
@endif

<br>
<h2>{{$reminder->title}}</h2>
<p>{{$reminder->message}}</p>

@component('mail::panel')
<p>Reminder was set for {{$reminder->due_date}}</p>
@endcomponent

@isset($reminder->project_id)
@component('mail::button', ['url' => route('view_specific_project', $reminder->project_id)])
View Project On iTrack
@endcomponent
@endisset

@lang('Regards'),<br>
{{ config('app.name') }}

@endcomponent