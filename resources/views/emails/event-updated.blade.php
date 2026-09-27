<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Event Update: {{ $event->title }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8fafc;
            color: #334155;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
        }
        .header {
            background-color: #0ea5e9;
            color: white;
            padding: 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            font-size: 24px;
        }
        .content {
            padding: 30px;
        }
        .alert {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin-bottom: 25px;
            color: #92400e;
            border-radius: 4px;
        }
        .changes-list {
            margin-bottom: 30px;
        }
        .changes-list strong {
            display: inline-block;
            width: 120px;
            color: #0ea5e9;
        }
        .change-item {
            padding: 10px 0;
            border-bottom: 1px solid #e2e8f0;
        }
        .change-item:last-child {
            border-bottom: none;
        }
        .button {
            display: inline-block;
            background-color: #0ea5e9;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 6px;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            background-color: #f1f5f9;
            text-align: center;
            padding: 20px;
            font-size: 14px;
            color: #64748b;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>Important Update: {{ $event->title }}</h1>
        </div>
        <div class="content">
            <p>Hello!</p>
            <p>The host has made some important changes to <strong>{{ $event->title }}</strong> that you should know about.</p>

            <div class="alert">
                <strong>What changed:</strong>
                <ul style="margin-top: 10px; margin-bottom: 0; padding-left: 20px;">
                    @foreach($changes as $field => $oldValue)
                        <li>{{ ucfirst(str_replace('_', ' ', $field)) }} has been updated.</li>
                    @endforeach
                </ul>
            </div>

            <div class="changes-list">
                <h3>Current Event Details:</h3>
                
                <div class="change-item">
                    <strong>Date & Time:</strong>
                    <span>{{ \Carbon\Carbon::parse($event->event_date)->format('l, F j, Y \a\t g:i A') }}</span>
                </div>
                
                <div class="change-item">
                    <strong>Venue:</strong>
                    <span>{{ $event->venue_name }}</span>
                </div>
                
                @if($event->manual_directions)
                <div class="change-item" style="display: flex; flex-direction: column;">
                    <strong style="margin-bottom: 5px;">Directions:</strong>
                    <span style="background: #f8fafc; padding: 10px; border-radius: 6px;">{{ $event->manual_directions }}</span>
                </div>
                @endif
            </div>

            <center>
                <a href="{{ route('guest.portal', $event->tracking_access_token) }}" class="button" style="color: #ffffff;">View Live Portal</a>
            </center>
        </div>
        <div class="footer">
            <p>Powered by Eventio</p>
            <p>&copy; {{ date('Y') }} Eventio. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
