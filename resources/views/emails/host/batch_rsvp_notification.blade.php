<x-mail::message>
# Guest RSVP Milestone!

Great news! **{{ $guestCount }} guests** have now RSVP'd for your upcoming event: **{{ $event->title }}**.

Log in to your Command Center to review your guest list and, on the day of the event, monitor their live arrivals.

<x-mail::button :url="route('events.command-center', $event)" color="primary">
Open Command Center
</x-mail::button>

Warmly,<br>
The {{ config('app.name') }} Team
</x-mail::message>
