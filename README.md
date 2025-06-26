# FasBank

FasBank is a modern online banking system, built using Laravel (backend) and Vue.js (frontend). It allows users to authenticate via Google, manage contacts through unique bank IDs, send and request money, view transaction history, and access an analytics dashboard — all through a secure, user-friendly interface.

---

## Features

- Google OAuth login
- Add and manage contacts via Bank ID
- Send and request money securely
- View real-time transaction history with filters
- Access a detailed analytics dashboard
- Perform direct bank transfers
- Track and manage payment requests
- Transaction notifications
- Charts and breakdowns of financial activity

---

## Tech Stack

- Backend: Laravel 11
- Frontend: Vue.js 3
- Database: MySQL

---

## Getting Started

### Requirements

- PHP 8.x
- Composer
- Node.js
- MySQL
- Google OAuth Credentials

---

### 1. Clone the Repository

```sh
git clone https://github.com/adarshhaldar/fasbank.git
```

---

### 2. Google OAuth Setup

1. Go to https://console.cloud.google.com
2. Create a new project 
3. Go to APIs and services
4. Tap on credentials
4. Create new credentials by selecting OAuth client ID
5. Fill out necessary details and set redirect URL
6. Copy the client ID and secret, and paste it into `.env`

---

### 3. Configure environment file

Copy from `.env.example` and configure the `.env`

---

### 4. Laravel + Vue Setup

#### Install backend dependencies
```sh
composer install
```

#### Install frontend dependencies
```sh
npm install
```

#### Generate application key
```sh
php artisan key:generate
```

#### Run database migrations
```sh
php artisan migrate
```

#### Generate personal access token (optional)
```sh
php artisan passport:client --personal
```

---

### 5. Run the Application

#### Start the backend development server
```sh
php artisan serve
```

#### Start the frontend development server
```sh
npm run dev
```

---

## Author

Developed by Adarsh Haldar 
