# Barbershop Appointment System

A modern web application for managing barbershop appointments, built with Laravel and Vue.js.

## Features

- 📅 Online Appointment Booking
- 💈 Multiple Barbers Support
- 🕒 Real-time Availability Check
- 📱 Responsive Design
- 👤 Admin Dashboard
- 📊 Appointment Management
- 🗑️ Soft Delete for Historical Data

## Tech Stack

- **Backend:** Laravel 10
- **Frontend:** Vue.js 3 with Composition API
- **Styling:** Tailwind CSS
- **Database:** MySQL
- **Authentication:** Laravel Sanctum

## Screenshots

### Home Page
![Home Page](/public/images/screenshots/home.png)

### Barbers
![Barbers](/public/images/screenshots/barbers.png)

### About Us
![About Us](/public/images/screenshots/about.png)

### Services
![Services](/public/images/screenshots/services.png)

### Book Appointment
![Book Appointment](/public/images/screenshots/booking.png)

### Admin Dashboard
![Admin Dashboard](/public/images/screenshots/admin-dashboard.png)

### Admin Barbers
![Admin Barbers](/public/images/screenshots/admin-barbers.png)

## Installation

1. Clone the repository
```bash
git clone https://github.com/dChushkov/Barbershop-App.git
cd Barbershop-App
```

2. Install PHP dependencies
```bash
composer install
```

3. Install NPM dependencies
```bash
npm install
```

4. Create and configure .env file
```bash
cp .env.example .env
php artisan key:generate
```

5. Configure your database in .env file
```

```

6. Run migrations
```bash
php artisan migrate
```

7. Build assets
```bash
npm run dev
```

8. Start the server
```bash
php artisan serve
```

## Admin Access

Default admin credentials:
- Email: admin@test.com
- Password: admin123

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
