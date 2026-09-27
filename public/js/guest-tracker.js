/**
 * Guest Portal Tracking Logic
 * Handles HTML5 Geolocation, Wake Lock API, Geofencing, and Socket.io emission
 */

let wakeLock = null;
let watchId = null;
let socket = null;
let isTracking = false;
let lastPolledPosition = null;
let lastPollTime = 0;

// Configuration constants
const POLL_INTERVAL_MOVING = 10000; // 10 seconds
const POLL_INTERVAL_STATIONARY = 60000; // 60 seconds
const GEOFENCE_RADIUS_METERS = 100;

/**
 * Initializes the connection to the Node.js Socket.io server
 */
function initSocketConnection(nodeUrl, guestToken) {
    // Assuming socket.io.js is loaded in the Blade template
    socket = io(nodeUrl, {
        auth: {
            role: 'guest',
            token: guestToken
        }
    });

    socket.on('connect_error', (err) => {
        console.error("Socket Connection Error:", err.message);
        alert("Failed to connect to tracking server. Ensure event is active.");
    });
}

/**
 * Request Screen Wake Lock
 */
async function requestWakeLock() {
    try {
        if ('wakeLock' in navigator) {
            wakeLock = await navigator.wakeLock.request('screen');
            console.log('Screen Wake Lock active');
            
            wakeLock.addEventListener('release', () => {
                console.log('Screen Wake Lock released');
            });
        }
    } catch (err) {
        console.error(`${err.name}, ${err.message}`);
    }
}

/**
 * Handle visibility change to re-request Wake Lock if lost
 */
document.addEventListener('visibilitychange', async () => {
    if (wakeLock !== null && document.visibilityState === 'visible' && isTracking) {
        await requestWakeLock();
    }
});

/**
 * Start the journey tracking
 */
async function startJourney(eventLat, eventLng, nodeUrl, guestToken, webhookUrl) {
    if (isTracking) return;
    isTracking = true;

    initSocketConnection(nodeUrl, guestToken);
    await requestWakeLock();

    // UI Updates (Assuming specific IDs exist in Blade template)
    document.getElementById('start-journey-btn').style.display = 'none';
    document.getElementById('tracking-status').innerText = 'Tracking Active... Keep screen on.';

    if (navigator.geolocation) {
        watchId = navigator.geolocation.watchPosition(
            (position) => handlePositionUpdate(position, eventLat, eventLng, webhookUrl),
            (error) => handleGeolocationError(error),
            {
                enableHighAccuracy: true,
                maximumAge: 0,
                timeout: 10000
            }
        );
    } else {
        alert("Geolocation is not supported by this browser.");
    }
}

/**
 * Handle new position data from the Geolocation API
 */
function handlePositionUpdate(position, eventLat, eventLng, webhookUrl) {
    const currentLat = position.coords.latitude;
    const currentLng = position.coords.longitude;
    const speed = position.coords.speed; // meters/second, if supported
    const now = Date.now();

    const distanceToVenue = calculateHaversineDistance(currentLat, currentLng, eventLat, eventLng);

    // Geofence Check (100 meters)
    if (distanceToVenue <= GEOFENCE_RADIUS_METERS) {
        triggerCheckIn(webhookUrl);
        return;
    }

    // Battery Optimization / Throttling logic
    let pollInterval = POLL_INTERVAL_STATIONARY;
    
    // If we have speed data and moving faster than 1m/s, or if fallback logic implies movement
    if ((speed !== null && speed > 1) || (lastPolledPosition && calculateHaversineDistance(lastPolledPosition.lat, lastPolledPosition.lng, currentLat, currentLng) > 10)) {
        pollInterval = POLL_INTERVAL_MOVING;
    }

    if (now - lastPollTime >= pollInterval) {
        lastPollTime = now;
        lastPolledPosition = { lat: currentLat, lng: currentLng };
        
        // Optional: Calculate ETA client side via Google Distance Matrix JS SDK here before emitting
        // For brevity, omitting the exact Google Maps API call here, emitting 'Calculating...'
        let etaText = 'Calculating...'; 
        
        if (socket && socket.connected) {
            socket.emit('guest_location_update', {
                lat: currentLat,
                lng: currentLng,
                etaText: etaText
            });
        }
    }
}

/**
 * Trigger the Check-In webhook when inside the geofence
 */
function triggerCheckIn(webhookUrl) {
    // Stop tracking immediately
    stopTracking();
    
    if (socket && socket.connected) {
        socket.emit('guest_checked_in');
    }

    // Call Laravel webhook
    fetch(webhookUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.status === 'success') {
            // UI shifts to Welcome / Post-Event Photo Wall
            document.getElementById('tracking-container').style.display = 'none';
            document.getElementById('welcome-container').style.display = 'block';
        }
    })
    .catch(error => console.error('Check-in error:', error));
}

/**
 * Stop all tracking functions
 */
function stopTracking() {
    isTracking = false;
    if (watchId !== null) {
        navigator.geolocation.clearWatch(watchId);
    }
    if (wakeLock !== null) {
        wakeLock.release().then(() => { wakeLock = null; });
    }
    if (socket) {
        socket.disconnect();
    }
}

function handleGeolocationError(error) {
    console.warn(`ERROR(${error.code}): ${error.message}`);
    // In production, update UI to inform the user to enable location permissions
}

/**
 * Haversine formula to calculate distance between two coordinates in meters
 */
function calculateHaversineDistance(lat1, lon1, lat2, lon2) {
    const R = 6371e3; // Earth radius in meters
    const phi1 = lat1 * Math.PI/180;
    const phi2 = lat2 * Math.PI/180;
    const deltaPhi = (lat2-lat1) * Math.PI/180;
    const deltaLambda = (lon2-lon1) * Math.PI/180;

    const a = Math.sin(deltaPhi/2) * Math.sin(deltaPhi/2) +
            Math.cos(phi1) * Math.cos(phi2) *
            Math.sin(deltaLambda/2) * Math.sin(deltaLambda/2);
    const c = 2 * Math.atan2(Math.sqrt(a), Math.sqrt(1-a));

    return R * c; 
}
