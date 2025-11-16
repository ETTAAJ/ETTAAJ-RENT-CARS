# GoldCars Fullstack  
**Car Rental System**  
*A modern, responsive Car Rental Website with a clean frontend and secure admin panel. Built with PHP, MySQL, Bootstrap 5, Tailwind CSS, and AOS animations.*

---

## Features

### Public Frontend  
`index.php` • `car-detail.php` • `booking.php`

- **Search & filter** cars (name, gear, fuel, price)  
- **Responsive card grid** (mobile-first)  
- **Live AJAX filtering**  
- **Car detail page** with similar cars  
- **Booking form** with **live price calculation**  
- **Minimum 3-day rental** enforcement  
- **Gold accent color**: `#FFD700`

---

### Admin Panel  
`car-rental-crud/`

- **Add, edit, delete** cars  
- **Secure image upload** (JPG, PNG, GIF, WebP)  
- **Unique filenames** to prevent conflicts  
- **Full CRUD operations**  
- **Same card design** as frontend

---

## Folder Structure
Rental cars/
├── uploads/                  # Car images (uploaded here)
├── car-rental-crud/          # Admin panel
│   ├── index.php             # List all cars (cards)
│   ├── create.php            # Add new car
│   ├── edit.php              # Edit car
│   ├── delete.php            # Delete car
│   └── config.php            # DB connection
├── assets/                   # (Optional) static assets
├── index.php                 # Homepage with search
├── car-detail.php            # Single car view
├── booking.php               # Booking form
├── booking-process.php       # Process booking (not included)
├── header.php                # Header
├── footer.php                # Footer
├── config.php                # Main DB config
└── car_rental.sql            # Database schema
text---

## Database Setup

### 1. Create Database & Table

```sql
CREATE DATABASE car_rental;
USE car_rental;

CREATE TABLE cars (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    image VARCHAR(255), -- stores filename only (e.g. toyota_1734023456.jpg)
    seats INT NOT NULL,
    bags INT NOT NULL,
    gear ENUM('Manual', 'Automatic') NOT NULL,
    fuel ENUM('Diesel', 'Petrol') NOT NULL,
    price_day DECIMAL(10,2) NOT NULL,
    price_week DECIMAL(10,2) NOT NULL,
    price_month DECIMAL(10,2) NOT NULL
);

Configuration
config.php (root & admin)
php<?php
$host = 'localhost';
$db   = 'car_rental';
$user = 'root';
$pass = '';

$dsn = "mysql:host=$host;dbname=$db;charset=utf8mb4";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

Image Handling

All images stored in: uploads/
Admin uploads to: ../uploads/filename_timestamp.ext
DB stores: filename only
All pages use:phpuploads/filename.jpg?v=1734023456
Cache-buster ensures instant updates


Setup Instructions

Copy to C:\xampp\htdocs\Rental cars\
Createuploads/ folder in root
Set permissions (Windows):bashicacls "C:\xampp\htdocs\Rental cars\uploads" /grant Users:M
Start XAMPP (Apache + MySQL)
Run SQL above
Open: http://localhost/Rental cars/


Admin Panel
URL: http://localhost/Rental cars/car-rental-crud/

Click "Add New Car"
Upload image → saved to ../uploads/
Edit/Delete with confirmation


Styling & UX

Gold color: #FFD700 (prices, buttons)
AOS animations on scroll
Tailwind + Bootstrap hybrid
Mobile responsive (1–4 columns)


Security

PDO prepared statements
Input sanitization
File type & size validation
Unique filenames
basename() to prevent path traversal


Future Improvements

 User login & booking history
 Email confirmation
 Payment gateway
 Admin dashboard stats
 Multi-language (EN/FR/AR)# ETTAAJ-RENT-CARS
