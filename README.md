# School

School Management and Accounting application built with Laravel.

## Ownership

This repository is maintained in https://github.com/ugochukwu16henry/school.

## Tech Stack

- Laravel 8
- Bootstrap 5
- MySQL 5.7
- Docker Compose (optional)

## Quick Start

1. Copy environment file:
   - cp .env.example .env
2. Configure database values in .env.
3. Install PHP dependencies:
   - composer install
4. Generate app key:
   - php artisan key:generate
5. Run migrations and seeders:
   - php artisan migrate --seed
6. Start the app:
   - php artisan serve

## Docker Setup

1. Start containers:
   - docker-compose up -d
2. Inside app container:
   - composer install
   - php artisan key:generate
   - php artisan migrate --seed

## Default Seed Admin

- Email: admin@school.local
- Password: password

## License

GNU General Public License v3.0
