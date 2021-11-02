<div class="container">
    <div class="alert alert-info" role="alert" id="repositories-alert">
        @if ($previousTokens)
            Your access token to github seems to have been invalidated. Please set up a new one
        @else
            Your access token to github is not setup. Please set it up
        @endif
        <a href="{{ route('profile') }}">here</a>
    </div>
</div>
