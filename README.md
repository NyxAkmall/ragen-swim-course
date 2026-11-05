# Ragen Swim Course
### Swimming Course Management System

A web-based swimming course management system built to support daily operational activities, customer management, class scheduling, bookings, payments, and attendance tracking.

Developed using Laravel with a focus on reliable business workflows, database integrity, and operational efficiency.

---

## Project Overview

Ragen Swim Course is a swimming course management platform designed to connect administrators, coaches, and customers through an integrated web application.

The system provides tools to manage class schedules, participant packages, bookings, attendance, and other essential operational processes.

## Core Features

### 1. Booking Management
- Customer class booking.
- Booking confirmation and cancellation.
- Booking rescheduling.
- Schedule capacity validation.
- Booking status management.
- Prevention of duplicate session deductions.

### 2. Waiting List System
- Automatic waiting list creation.
- Waiting list offer management.
- Offer expiration handling.
- Upcoming schedule monitoring.
- Capacity validation when offering available slots.

### 3. Class Schedule Management
- Create, update, and manage class schedules.
- Weekly recurring schedule management.
- Automatic weekly schedule generation.
- Schedule cancellation and completion.
- Class capacity validation.

### 4. Attendance Management
- Coach attendance recording.
- Participant package validation.
- Automatic session deduction.
- Booking completion management.
- Transaction-safe attendance processing.

### 5. Package Management
- Participant package management.
- Active package monitoring.
- Used and remaining session tracking.
- Package and order integration.

### 6. Operational Management
- Customer and participant management.
- Coach management.
- Program and location management.
- Order and payment management.
- Invoice management.
- Reports and dashboard.
- Website content management.

## User Roles

| Role | Responsibilities |
|---|---|
| Administrator | Manages operational activities, schedules, customers, coaches, orders, and reports. |
| Customer | Manages participants, packages, bookings, and personal information. |
| Coach | Views schedules, manages attendance, and records participant progress. |

## Technical Stack

| Technology | Usage |
|---|---|
| PHP 8.2 | Backend programming |
| Laravel 12 | Web application framework |
| MariaDB / MySQL | Relational database |
| Blade | Server-side templating |
| JavaScript | Frontend interactions |
| CSS | Interface styling |
| Vite | Asset bundling |
| PHPUnit | Automated testing |

## Technical Implementation

This project includes several backend implementation approaches to maintain data consistency and reliable operational workflows.

**Database Transactions**
- Transaction-based booking operations.
- Consistent attendance processing.
- Atomic session deduction.

**Concurrency Control**
- Pessimistic row locking using `lockForUpdate()`.
- Schedule capacity protection.
- Participant and package data protection.
- Transaction-safe waiting list operations.

**Business Logic Validation**
- Booking status validation.
- Package eligibility validation.
- Schedule availability checks.
- Duplicate session deduction prevention.
- Waiting list offer expiration handling.

## Testing

Feature tests are implemented to validate important application workflows.

The following core test suites have been executed successfully using MySQL:

| Test Suite | Tests | Assertions |
|---|---:|---:|
| Booking Flow | 11 | 34 |
| Waiting List Flow | 5 | 14 |
| Attendance Flow | 5 | 26 |
| **Total** | **21** | **74** |

Run the core feature tests:

```bash
vendor/bin/phpunit \
tests/Feature/BookingFlowTest.php \
tests/Feature/WaitingListFlowTest.php \
tests/Feature/AttendanceFlowTest.php \
--no-progress
```

## Project Structure

```text
ragen-swim-course/
├── app/
│   ├── Console/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── Admin/
│   │       ├── Coach/
│   │       └── Customer/
│   ├── Models/
│   ├── Providers/
│   └── Services/
├── bootstrap/
├── config/
├── database/
│   ├── factories/
│   ├── migrations/
│   └── seeders/
├── public/
├── resources/
│   ├── css/
│   ├── js/
│   └── views/
├── routes/
├── storage/
└── tests/
    ├── Feature/
    └── Unit/
```

## Installation

### Requirements
- PHP 8.2 or later
- Composer
- MySQL or MariaDB
- Node.js and npm

### Setup Instructions

**1. Clone the repository**

```bash
git clone https://github.com/NyxAkmall/ragen-swim-course.git

cd ragen-swim-course
```

**2. Install PHP dependencies**

```bash
composer install
```

**3. Configure environment**

```bash
cp .env.example .env
```

Configure the database credentials in the `.env` file.

**4. Generate application key**

```bash
php artisan key:generate
```

**5. Run database migrations**

```bash
php artisan migrate --seed
```

**6. Install frontend dependencies**

```bash
npm install
npm run build
```

**7. Start the development server**

```bash
php artisan serve
```

Open `http://127.0.0.1:8000` in your browser.

## Development Notes

- Environment credentials must be configured locally.
- Never commit `.env` files or production credentials.
- Database migrations should be reviewed before deployment.
- Production deployment requires appropriate server and database configuration.

## Project Information

**Project:** Ragen Swim Course
**Repository:** [NyxAkmall/ragen-swim-course](https://github.com/NyxAkmall/ragen-swim-course)
**Framework:** Laravel 12
**Project Type:** Full-Stack Web Application

---

*Developed as a client project, covering application development, backend implementation, database management, testing, and deployment.*
