# Eventio

Eventio is a robust event management platform designed to help hosts seamlessly manage their events, track RSVPs, and interact with guests in real-time. It features modern authentication, livestreaming integrations, automated guest arrival notifications, and live location tracking.

## Features

- **Host Dashboard:** A unified command center for hosts to manage ongoing events.
- **Real-time Notifications:** Automated email notifications sent to hosts when guests arrive.
- **Livestream Integration:** Seamless video streaming for remote guests utilizing Mux.
- **Live Location Tracking:** Integration with Google Maps and Socket.io to view live tracking of users.
- **Modern UI:** Built using Tailwind CSS with interactive elements for a flawless user experience.
- **Authentication:** Fully secure guest and host portals with intuitive forms and password visibility toggles.

## Tech Stack

- **Backend:** Laravel (PHP)
- **Frontend:** Blade, Tailwind CSS, JavaScript
- **Database:** MySQL
- **Real-time:** Socket.io (Node.js) for live events, Laravel Mail for notifications
- **Video Processing:** Mux Player for livestreams
- **Maps:** Google Maps API

## Requirements

- PHP 8.1+
- Composer
- Node.js (for Socket.io server and building assets)
- MySQL
- Mail driver (e.g., Mailpit, SMTP)
- External API Keys (Mux, Google Maps)

## Installation

1. **Clone the repository:**
   ```bash
   git clone https://github.com/alagaddonjuan/eventio.git
   cd eventio
   ```

2. **Install PHP dependencies:**
   ```bash
   composer install
   ```

3. **Install Node dependencies & build assets:**
   ```bash
   npm install
   npm run build
   ```

4. **Environment Setup:**
   Copy the example `.env` file and generate a key.
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure Database and external APIs in `.env`:**
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=eventio_webs
   DB_USERNAME=root
   DB_PASSWORD=

   # Google Maps API
   GOOGLE_MAPS_API_KEY=your_key_here

   # Node.js Live Tracking Server
   NODE_URL=http://localhost:3000
   ```

6. **Run Migrations:**
   ```bash
   php artisan migrate
   ```

7. **Start the development server:**
   ```bash
   php artisan serve
   ```

## Private Repository Notice

This repository contains proprietary source code for the Eventio platform and is intended to be kept private. All rights reserved.
