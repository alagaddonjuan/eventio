<x-mail::message>
# Important Update for: {{ $event->title }}

{!! nl2br(e($messageBody)) !!}

<x-mail::button :url="route('guest.portal', $event->tracking_access_token)">
View Live Event Portal
</x-mail::button>

Thanks,<br>
{{ $event->user->name }}
</x-mail::message>
