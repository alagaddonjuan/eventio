<x-mail::message>
# You're going to {{ $guest->event->title }}!

Hi {{ $guest->name }},

Your RSVP has been confirmed! We are thrilled to have you join us for this special occasion.

**Venue:** {{ $guest->event->venue_name }}  
**Date:** {{ \Carbon\Carbon::parse($guest->event->event_date)->format('l, F j, Y g:i A') }}

### Your Personal Live-Tracking Portal & Ticket
To ensure a seamless arrival experience, your host has provided a **Live-Tracking Portal**. 

@if($guest->ticket)
**Your QR Ticket:**
<div style="text-align: center; margin: 20px 0;">
    <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data={{ urlencode($guest->barcode_data ?? $guest->unique_token) }}" alt="QR Code Ticket">
</div>
@endif

On the day of the event, tap the button below when you are en route. This allows your host to prepare for your VIP arrival in real-time.

<x-mail::button :url="route('guest.portal', $guest->unique_token)" color="primary">
Open My Portal
</x-mail::button>

*Keep this email handy—you'll need it on the day of the event.*

Warmly,<br>
The {{ config('app.name') }} Team
</x-mail::message>
