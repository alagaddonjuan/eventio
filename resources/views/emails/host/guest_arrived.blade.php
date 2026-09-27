<x-mail::message>
# Guest Arrived!

Hi, your guest **{{ $guestName }}** has just arrived at the venue for **{{ $eventName }}**.

<x-mail::button :url="route('events.command-center', $guest->event)">
View Command Center
</x-mail::button>

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
