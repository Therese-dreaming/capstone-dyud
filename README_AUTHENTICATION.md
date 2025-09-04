# Authentication and Role System

## Overview
This Laravel application implements a role-based authentication system with three user roles: GSU (Super Admin), Admin, and User.

## Default Route
- The application's default route (`/`) redirects to the login page
- Login is now the entry point for all users

## User Roles

### 1. Super Admin
- **ID Number**: SUPER001
- **Email**: superadmin@example.com
- **Password**: password
- **Access**: User management ONLY (create, edit, delete users, assign roles)

### 2. GSU
- **ID Number**: GSU001
- **Email**: gsu@example.com
- **Password**: password
- **Access**: Asset management, categories, locations, maintenance, disposals (no user management)

### 3. Admin
- **ID Number**: ADMIN001
- **Email**: admin@example.com
- **Password**: password
- **Access**: Asset management, categories, locations, maintenance, disposals (no user management)

### 4. User
- **ID Number**: USER001
- **Email**: user@example.com
- **Password**: password
- **Access**: View assets, generate QR codes

## Middleware

### Authentication Middleware
- All protected routes use the `auth` middleware
- Unauthenticated users are redirected to login

### Role Middleware
- Custom `CheckRole` middleware handles role-based access
- Supports multiple roles (e.g., `role:admin,gsu`)
- Unauthorized access returns 403 error

## Route Protection

### Public Routes
- `/` - Redirects to login
- `/login` - Login page
- `/register` - Registration page

### Protected Routes by Role

#### All Authenticated Users
- Dashboard
- View assets
- Generate QR codes

#### Admin and GSU Users
- Create, edit, delete assets
- Manage categories
- Manage locations
- Maintenance history
- Disposal history

#### Super Admin Users Only
- User management (create, edit, delete users)
- Role assignment and management

## Database Structure

### Users Table
- `id` - Primary key
- `name` - User's full name
- `id_number` - Unique ID for login
- `email` - Email address
- `password` - Hashed password
- `role` - User role (superadmin, gsu, admin, user)
- `last_login` - Timestamp of last login
- `created_at`, `updated_at` - Timestamps

## Features

### Login Tracking
- Last login timestamp is automatically updated on successful login
- Stored in the `last_login` field

### Registration
- New users can register with name, ID number, email, and password
- Default role is 'user'
- ID number must be unique

## Usage Examples

### Login
```
ID Number: GSU001
Password: password
```

### Route Protection
```php
// Single role
Route::middleware(['role:admin'])->group(function () {
    // Admin only routes
});

// Multiple roles
Route::middleware(['role:admin,gsu'])->group(function () {
    // Admin and GSU routes
});
```

## Seeding
The application comes with three pre-seeded users:
- GSU (Super Admin)
- Admin
- User

Run `php artisan migrate:fresh --seed` to reset the database with these users. 