# Vaccination Management System

Complete web-based Vaccination Management System built with PHP 8+, MySQL (procedural MySQLi), Bootstrap 5, and modern UI.

## Requirements

- XAMPP (Apache + MySQL + PHP 8+)
- Web browser

## Installation Steps

### 1. Install XAMPP
Download and install XAMPP from https://www.apachefriends.org/

### 2. Extract the Project
Extract `vaccination-management-system.zip` into:
```
C:\xampp\htdocs\
```
Final path should be:
```
C:\xampp\htdocs\vaccination-management-system\
```

### 3. Start Services
Open **XAMPP Control Panel** and start:
- **Apache**
- **MySQL**

### 4. Create the Database (choose one method)

#### Option A – Automatic Installer (Recommended)
1. Open browser and go to:
   ```
   http://localhost/vaccination-management-system/install.php
   ```
2. Click **Install Database**
3. After success, you can delete `install.php` for security.

#### Option B – Manual Import via phpMyAdmin
1. Open: http://localhost/phpmyadmin
2. Click **Import**
3. Choose file: `database/vaccination_db.sql`
4. Click **Go**

### 5. Database Configuration
Default settings (XAMPP) are already configured in `config/config.php`:
- Host: `localhost`
- User: `root`
- Password: *(empty)*
- Database: `vaccination_db`

If your MySQL has a password, edit `config/config.php`.

### 6. Open the Website
```
http://localhost/vaccination-management-system/
```

---

## Test Login Credentials

### Admin
- **URL:** http://localhost/vaccination-management-system/admin/login.php
- **Email:** `admin@vaccination.com`
- **Password:** `Admin@123`

### Parent (sample)
- **URL:** http://localhost/vaccination-management-system/auth/login.php
- **Email:** `ahmed@parent.com`
- **Password:** `Parent@123`

### Hospital (sample – already approved)
- **URL:** http://localhost/vaccination-management-system/hospital/login.php
- **Email:** `citygeneral@hospital.com`
- **Password:** `Hospital@123`

---

## Complete Testing Workflow

1. **Parent Registration** → Register a new parent account  
2. **Parent Login** → Login with parent credentials  
3. **Add Child** → Add one or more children  
4. **Search Vaccine** → Browse available vaccines  
5. **Book Appointment** → Select child, vaccine, hospital, date & time  
6. **Admin Login** → Review bookings & parent requests  
7. **Admin Approves** → Change booking status to Approved  
8. **Hospital Login** → View own bookings  
9. **Hospital Updates Status** → Mark vaccination as Completed  
10. **System creates Vaccination Record** (transaction)  
11. **Parent sees updated status** in Vaccination Status / History  
12. **Admin views/prints Reports**

---

## Features Implemented

### Admin Module
- Dashboard with statistics
- Child details (view, search, filter)
- Vaccination dates (today / upcoming / due / custom)
- Vaccination reports (search, filter, print-friendly)
- Vaccine management (CRUD)
- Parent requests (approve / reject)
- Hospital management (CRUD + status)
- Booking management (status updates)

### Parent Module
- Registration & Login
- Forgot / Reset password
- Dashboard with real stats
- Child management (add / edit / view)
- Vaccination details & history
- Appointment booking
- Vaccine search
- Vaccination status
- Profile, edit profile, change password

### Hospital Module
- Registration (pending admin approval)
- Login
- Dashboard
- View own bookings only
- Update vaccination status (with transaction → creates vaccination record)
- Profile management

### Security
- Password hashing (`password_hash` / `password_verify`)
- Session-based authentication + role checks
- CSRF protection
- Prepared statements (procedural MySQLi)
- XSS protection (`htmlspecialchars`)
- Server-side validation
- Authorization: parents can only access their own children; hospitals only their own bookings

---

## Project Structure

```
vaccination-management-system/
├── index.php
├── install.php
├── admin/
├── parent/
├── hospital/
├── auth/
├── config/
├── includes/
├── assets/
├── database/
│   └── vaccination_db.sql
└── README.md
```

---

## Notes

- All passwords are securely hashed.
- The system uses **procedural MySQLi only** (no PDO, no OOP MySQLi).
- Responsive design works on desktop, tablet and mobile.
- After successful installation you may delete or rename `install.php`.
