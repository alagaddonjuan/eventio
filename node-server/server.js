require('dotenv').config();
const express = require('express');
const http = require('http');
const { Server } = require('socket.io');
const axios = require('axios');
const cors = require('cors');

const app = express();
app.use(cors());

const server = http.createServer(app);
const io = new Server(server, {
    cors: {
        origin: process.env.FRONTEND_URL || "*", // Adjust in production
        methods: ["GET", "POST"]
    }
});

const LARAVEL_API_URL = process.env.LARAVEL_API_URL || 'http://localhost:8000/api';

// Authentication Middleware for Socket.io
io.use(async (socket, next) => {
    const { role, token } = socket.handshake.auth;

    if (!role || !token) {
        return next(new Error('Authentication error: Missing credentials'));
    }

    try {
        // Validate with Laravel
        const response = await axios.post(`${LARAVEL_API_URL}/realtime/verify`, {
            role,
            token
        });

        if (response.data && response.data.valid) {
            socket.data.eventId = response.data.event_id;
            socket.data.role = role;
            if (role === 'guest') {
                socket.data.guestId = response.data.guest_id;
                socket.data.guestName = response.data.guest_name;
            }
            next();
        } else {
            next(new Error('Authentication error: Invalid token'));
        }
    } catch (error) {
        console.error('Laravel Auth Error:', error.message);
        next(new Error('Authentication error: Server validation failed'));
    }
});

io.on('connection', (socket) => {
    const eventRoom = `event_${socket.data.eventId}`;
    
    // Join the room specific to this event
    socket.join(eventRoom);
    console.log(`${socket.data.role} connected to ${eventRoom}`);

    if (socket.data.role === 'guest') {
        // Listen for location updates from the guest
        socket.on('guest_location_update', (data) => {
            const { lat, lng, etaText, timestamp } = data;
            
            // Broadcast to the host (and others in the room)
            socket.to(eventRoom).emit('location_update', {
                guest_id: socket.data.guestId,
                guest_name: socket.data.guestName,
                lat,
                lng,
                etaText,
                timestamp: timestamp || new Date().toISOString()
            });
        });
        
        socket.on('guest_checked_in', () => {
             socket.to(eventRoom).emit('guest_checked_in', {
                guest_id: socket.data.guestId,
                guest_name: socket.data.guestName
             });
        });
    }

    socket.on('disconnect', () => {
        console.log(`${socket.data.role} disconnected from ${eventRoom}`);
    });
});

const PORT = process.env.PORT || 3000;
server.listen(PORT, () => {
    console.log(`Real-time tracking server running on port ${PORT}`);
});
