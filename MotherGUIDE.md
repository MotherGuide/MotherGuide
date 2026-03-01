# MotherGUIDE - Simple Authentication System

A basic PHP web project with user **signup**, **login**, **logout**, and secure authentication features. An application designed to assist expectant mothers with tips and ease communication and access with medical professionals cocerning maternal health.

---

## Features

- User registration and login system
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
   - Import the provided SQL schema:
     ```bash
     mysql -u root -p motherguide < database/schema.sql
     ```

3. **Configure the database connection**
   - Copy the example config file:
     ```bash
     cp config/db.example.php config/db.php
     ```
   - Edit `config/db.php` with your credentials:
     ```php
     define('DB_HOST', 'localhost');
     define('DB_USER', 'your_username');
     define('DB_PASS', 'your_password');
     define('DB_NAME', 'motherguide');
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

```
motherguide/
├── config/
│   └── db.php              # Database connection settings
├── database/
│   └── schema.sql          # SQL table definitions
├── pages/
│   ├── login.php           # Login page
│   ├── signup.php          # Registration page
│   └── dashboard.php       # Protected page (requires login)
├── includes/
│   ├── auth.php            # Authentication helpers
│   ├── header.php          # Shared header
│   └── footer.php          # Shared footer
├── assets/
│   ├── css/
│   │   └── style.css       # Custom styles
│   └── js/
│       └── main.js         # Front-end scripts
└── index.php               # Entry point / redirect
```

---

## Authentication Flow

1. **Signup** — User submits registration form → password hashed with `password_hash()` → stored in DB
2. **Login** — Credentials verified with `password_verify()` → session started on success
3. **Protected Pages** — Session checked on every protected page → redirect to login if not authenticated
4. **Logout** — Session destroyed → redirect to login page

---

## Security Notes

- Passwords are **never stored in plain text** — `password_hash()` with `PASSWORD_BCRYPT` is used
- Input is sanitized to prevent **SQL injection** (use prepared statements)
- Sessions are regenerated on login to prevent **session fixation**
- All protected pages validate session before rendering content

---

## License

This project is open-source and available under the [MIT License](LICENSE).
