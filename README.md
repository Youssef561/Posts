# Post Management API (Laravel)

This is a simple Laravel-based API for managing posts. It includes features like authentication, role-based access, and full AJAX support.

## Features

- User authentication (login/register)
- Role-based access (admin/user)
- Post management (create, edit, delete, list)
- RESTful API
- AJAX-enabled frontend
- Structured codebase (Controllers, Services, Repositories)

## Setup Instructions

### 1. Clone the repository
```bash
git clone https://github.com/Youssef561/Posts.git
cd Posts

2. Install PHP dependencies using Composer
composer install

3. Install JWT authentication package (only if not installed yet)
composer require tymon/jwt-auth

4. Publish JWT configuration files
php artisan vendor:publish --provider="Tymon\JWTAuth\Providers\LaravelServiceProvider"

5. Generate JWT secret key
php artisan jwt:secret

6. Install Node.js dependencies
npm install

7. Compile frontend assets
npm run dev

8. Copy example environment configuration
cp .env.example .env

9. Generate application key
php artisan key:generate

10. Edit the .env file to update database and other settings
Update the DB_DATABASE, DB_USERNAME, DB_PASSWORD fields in .env according to your database credentials.

11. Run database migrations
php artisan migrate

12. Run database seeders
php artisan db:seed

13. Start the development server
php artisan serve


