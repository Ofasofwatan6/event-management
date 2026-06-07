# VolunteerHub

VolunteerHub is a web-based event and volunteer management platform developed using Laravel. The platform connects volunteers and organizations through event discovery, registration, payment management, and activity tracking features.

---

## Features

### User Features

* User Registration and Login
* Forgot Password with OTP Verification
* Browse Available Events
* View Event Details
* Register for Events
* Track Registration Status
* Payment History
* Upload Payment Proof
* Request Refund
* View Refund Status
* Manage User Profile
* View Past Events
* Browse Organizations

### Organization Features

* Organization Registration and Login
* Dashboard Overview
* Create Events
* Update Events
* Delete Events
* Manage Event Registrations
* Approve or Reject Participants
* Manage Refund Requests
* Approve or Reject Refunds
* Manage Payment Methods
* Update Organization Profile

---

## Technology Stack

### Backend

* Laravel 12
* PHP 8.2+

### Frontend

* Blade Template Engine
* Tailwind CSS
* Vite

### Database

* MySQL 8.0

### Deployment

* Docker
* Docker Compose
* Nginx

---

## Project Structure

```text
event-management
│
├── app/
├── bootstrap/
├── config/
├── database/
├── docker/
├── public/
├── resources/
├── routes/
├── storage/
├── tests/
│
├── Dockerfile
├── docker-compose.yml
├── composer.json
├── package.json
└── README.md
```

---

## Installation

### Clone Repository

```bash
git clone https://github.com/nabilaalya9/event-management.git

cd event-management
```

### Configure Environment

```bash
cp .env.example .env
```

Update the environment configuration if necessary.

---

## Run Using Docker

### Build Containers

```bash
docker compose build
```

### Start Containers

```bash
docker compose up -d
```

### Check Running Containers

```bash
docker ps
```

Expected containers:

```text
event_management_app
event_management_nginx
event_management_db
```

---

## Database Migration

Run migrations:

```bash
docker exec -it event_management_app php artisan migrate
```

---

## Database Seeder

Populate initial data:

```bash
docker exec -it event_management_app php artisan db:seed
```

Or:

```bash
docker exec -it event_management_app php artisan migrate:fresh --seed
```

---

## Storage Link

Create symbolic link for uploaded files:

```bash
docker exec -it event_management_app php artisan storage:link
```

---

## Frontend Build

Install dependencies:

```bash
npm install
```

Build assets:

```bash
npm run build
```

---

## Application URL

Open the application:

```text
http://localhost:8000
```

---

## User Roles

### Volunteer

Can:

* Register account
* Join events
* Upload payment proof
* Request refund
* Manage profile

### Organization

Can:

* Create events
* Manage participants
* Manage payment methods
* Manage refund requests
* View dashboard analytics

---

## Database Design

Main entities:

* Users
* Organizations
* Events
* Event Categories
* Event Types
* Event Registrations
* Payments
* Payment Methods
* Refunds
* Participants
* Password Reset OTP

---

## Testing

The application has been tested for:

* User Authentication
* Organization Authentication
* Event Management
* Registration Workflow
* Payment Workflow
* Refund Workflow
* Docker Deployment

---

## Development Team

Developed as an academic project for Event Management System development using Laravel and Docker.

---

## License

This project is developed for educational purposes.
