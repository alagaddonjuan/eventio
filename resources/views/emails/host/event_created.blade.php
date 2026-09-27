<x-mail::message>
# Event Created: {{ $event->title }}

Congratulations! Your event has been successfully launched on Eventio. Here are the details:

**Venue:** {{ $event->venue_name }}  
**Date:** {{ \Carbon\Carbon::parse($event->event_date)->format('l, F j, Y g:i A') }}

### Invite Your Guests
Share this exclusive link with your guests. Once they RSVP, they'll receive their own personalized live-tracking portal.

<x-mail::panel>
**RSVP Link:**  
[{{ route('guest.rsvp', $event->tracking_access_token) }}]({{ route('guest.rsvp', $event->tracking_access_token) }})
</x-mail::panel>

### Your Live Command Center
On the day of your event, open your Live Command Center to monitor your guests' ETAs in real-time. No more "Where are you?" texts!

<x-mail::button :url="route('events.command-center', $event)" color="primary">
Open Command Center
</x-mail::button>

Happy Hosting,<br>
The {{ config('app.name') }} Team
</x-mail::message>
