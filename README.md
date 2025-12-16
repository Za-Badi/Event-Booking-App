# Event Booking System (Laravel REST API)

## 📌 Overview
A Laravel-based REST API that allows users to browse events, book tickets, and manage their bookings.
The system includes role-based access control for admins and secure authentication using Laravel Sanctum.

## 🚀 Features
- User authentication (Register/Login) using Laravel Sanctum
- Role-based access (Admin / User)
- Admin can manage categories and events
- Users can view events and book tickets
- Users can view and cancel their bookings
- Media upload for events using Spatie Media Library
- RESTful API with API Resources

## 🛠 Tech Stack
- Laravel
- MySQL
- Laravel Sanctum
- Spatie Media Library
- REST API

## 📂 API Endpoints
### Auth
- POST /register
- POST /login

### Categories (Admin)
- GET /categories
- POST /categories
- PUT /categories/{id}
- DELETE /categories/{id}

### Events
- GET /events
- GET /events/{id}
- POST /events (Admin)
- PUT /events/{id} (Admin)
- DELETE /events/{id} (Admin)
- POST /events/{id}/book

### Bookings
- GET /my-bookings
- DELETE /bookings/{id}

## 📈 What I Learned
- Building secure REST APIs with Laravel
- Token-based authentication using Sanctum
- Role-based authorization
- Media handling with Spatie Media Library
- Designing real-world booking logic
