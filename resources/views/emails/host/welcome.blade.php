<x-mail::message>
# Welcome to {{ config('app.name') }}, {{ $user->name }}!

We're absolutely thrilled to have you on board. Here is a quick guide on how to deliver a VIP experience for your guests:

### 1. Create Your Event
Head over to your dashboard and create your first event. You can set the location, time, and customize the branding to match your style.

<x-mail::button :url="route('dashboard')" color="primary">
Go to My Dashboard
</x-mail::button>

### 2. Share Your RSVP Link
Once your event is created, you will get a unique, shareable RSVP link. Send this link to your guests so they can register.

### 3. Guests Get Their Portal
When a guest RSVPs, they automatically receive a personalized tracking portal. On the day of the event, they simply open this portal to share their live ETA as they approach the venue.

### 4. Monitor Live on the Command Center
You can monitor all your guests' live locations in real-time using the **Live Command Center**. As they get closer, you'll see them on the map and be notified instantly upon their arrival. No more guessing when your VIPs will show up!

If you have any questions or need assistance, simply reply to this email.

Happy Hosting,<br>
The {{ config('app.name') }} Team
</x-mail::message>
