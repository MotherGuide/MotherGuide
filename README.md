# MotherGUIDE - Simple Authentication System

A basic PHP web project with user **signup**, **login**, **logout**, and secure authentication features. An application designed to assist expectant mothers with tips and ease communication and access with medical professionals cocerning maternal health.

---

## Features

- User (mothers) registration and login system
- Admin (doctors/staff) registration and login system (separate database table)
- Password handling (hashed for security — uses `password_hash()`)
- Form validation and error messages
- Protected routes/pages (redirect if not logged in)
- Front-end: HTML forms + JavaScript for basic interactions

---

## Technologies Used

| Layer      | Technology                          |
|------------|-------------------------------------|
| Backend    | PHP (routing & logic)               |
| Database   | MySQL / MariaDB                     |
| Front-end  | HTML, CSS, JavaScript               |
| Styling    | Bootstrap or plain CSS              |

---

## Setup Instructions

### Prerequisites

- PHP 7+ or 8+
- Web server (XAMPP, WAMP, MAMP, or Apache/Nginx)
- MySQL database

### Installation

1. **Clone the repository**
   ```bash
   git clone https://github.com/your-username/motherguide.git
   cd motherguide
   ```

2. **Create the database**
   - Open your MySQL client (phpMyAdmin, MySQL Workbench, or CLI)
   - Create a new database:
     ```sql
     CREATE DATABASE motherguide;
     ```
   - Import the provided SQL schema from the DB_Creation.txt file.

3. **Configure the database connection**
   - Edit `..php/Database.php` with your credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'motherguidedb');
     ```

4. **Start your web server**
   - Place the project folder inside your web root (e.g., `htdocs/` for XAMPP)
   - Start Apache and MySQL from your server control panel

5. **Open in browser**
   ```
   http://localhost/motherguide/
   ```

---

## Project Structure

```MotherGUIDE/
│
├── api/
│   ├── login.php
│   ├── signup.php
│   ├── admin_login.php
│   └── admin_signup.php
│
├── css/
│   └── style.css
│
├── database/
│   ├── DB_Creation.txt
│   └── DB_Tip_Sample_InsertionData.txt
│
├── icons/
│   └── (SVG icon files)
│
├── images/
│   └── (Application images)
│
├── js/
│   ├── login.js
│   └── script.js
│
├── php/
│   ├── Comment.php
│   ├── Database.php
│   ├── Admin.php
│   ├── Tip.php
│   └── User.php
│
├── auth.html
├── auth_admin_login.html
├── index.php
├── tips.php
├── README.md
└── MotherGUIDE.md
```

---

## Authentication Flow

### Mothers (Users)
- **Signup**: Submit form at `auth.html` → `api/signup.php` → password hashed → stored in `users` table
- **Login**: Credentials verified → `$_SESSION['user_id']`, `user_name`, etc. set → dashboard access

### Doctors/Admins
- **Signup**: Submit form at `auth_admin_login.html` → `api/admin_signup.php` → password hashed → stored in `admins` table
- **Login**: Credentials verified → `$_SESSION['admin_id']`, `admin_name`, etc. set → admin panel access

### Protected Pages
- Check `$_SESSION['user_id']` for mother-only pages
- Check `$_SESSION['admin_id']` for staff/admin-only pages
- Redirect to appropriate login page if session missing

---

## Security Notes

- Passwords are **never stored in plain text** — `password_hash()` with `PASSWORD_BCRYPT` is used
- Input is sanitized to prevent **SQL injection** (use prepared statements)
- Sessions are regenerated on login to prevent **session fixation**
- All protected pages validate session before rendering content

---

