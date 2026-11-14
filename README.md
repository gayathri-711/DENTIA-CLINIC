# Doctor Dental Clinic Dentia — Modern Management Platform

A modern, responsive, full-stack dental clinic management platform built with PHP, MySQL, Bootstrap 5, and JavaScript. This platform transforms the clinic website into a comprehensive clinical management solution featuring real-time appointment scheduling, duplicate booking prevention, patient self-service portal, and an administrative control panel.

---

## 1. Key Features & Modules

### 🦷 Dental Services Module (10 Core Procedures)
Dedicated, detailed procedure pages with clinical descriptions, high-resolution imagery, treatment benefits, step-by-step patient expectations, and direct appointment scheduling:
1. **General Dentistry** (`service-general-dentistry.php`)
2. **Teeth Whitening** (`service-teeth-whitening.php`)
3. **Dental Implants** (`service-dental-implants.php`)
4. **Root Canal Treatment** (`service-root-canal.php`)
5. **Orthodontics and Braces** (`service-orthodontics.php`)
6. **Cosmetic Dentistry** (`service-cosmetic-dentistry.php`)
7. **Pediatric Dentistry** (`service-pediatric-dentistry.php`)
8. **Tooth Extraction** (`service-tooth-extraction.php`)
9. **Dental Cleaning** (`service-dental-cleaning.php`)
10. **Gum Treatment** (`service-gum-treatment.php`)
- **Dynamic Service Route**: `service-details.php?slug=...` / `service-details.php?id=...`
- **All Services Directory**: `services.php` with instant client-side search and filtering.

---

### 📅 Functional Appointment Booking Engine
- Real-time time slot availability checking via AJAX (`api/check-slots.php`).
- **Duplicate Booking Prevention**: Queries existing database appointments. If a time slot is already reserved for a doctor on that date, it is locked and crossed out, preventing double-booking.
- Generates a unique appointment reference ID (e.g. `DEN-2026-8492`).
- Auto-associates bookings with registered patient accounts based on login session or email.
- Dispatches confirmation emails and records notification logs (`data/mail_notifications.log`).
- Generates an immediate visual booking receipt card with appointment details.

---

### 👤 Patient Self-Service Portal
- **Registration & Auth** (`register.php`, `login.php`, `logout.php`): Secure account creation with email uniqueness checks and `password_hash()`.
- **Patient Dashboard** (`patient-dashboard.php`):
  - Overview metrics: Upcoming visits, Completed treatments, Cancelled appointments, Total scheduled.
  - Tabbed interface separating active upcoming appointments from past medical history.
  - One-click appointment cancellation with confirmation modal and reason recording.
- **Patient Profile** (`patient-profile.php`):
  - Update personal contact information, date of birth, gender, and residential address.
  - Change password with verification of the current password.

---

### ⚙️ Professional Admin Management Dashboard (`/admin/`)
- **Executive Overview** (`admin/index.php`):
  - Total Patients, Total Appointments, Pending Approvals, Confirmed Visits, Cancelled Visits, Active Dentists, Total Services.
  - Recent appointments table with quick status actions (Confirm, Complete, Cancel).
  - Recent contact inquiries preview.
- **Manage Appointments** (`admin/appointments.php`):
  - Filter by Status (Pending, Confirmed, Completed, Cancelled), Doctor, or Date.
  - Real-time status update dropdown.
  - Schedule new appointments directly from the admin panel.
  - Permanent record deletion with confirmation.
- **Manage Patients** (`admin/patients.php`):
  - Search directory by name, email, or phone.
  - View visit counts, registration date, and contact information.
  - Register new patients manually.
- **Manage Dentists & Staff** (`admin/dentists.php`):
  - Full CRUD (Create, Read, Update, Delete) for medical specialists.
  - Manage credentials, qualifications, specialties, weekly availability, and active status.
- **Manage Dental Services** (`admin/services.php`):
  - Add and edit dental procedures, fees, duration, and clinical descriptions.
  - Toggle featured procedures displayed on the homepage.
- **Manage Clinic Working Hours & Settings** (`admin/settings.php`):
  - Update weekday hours, weekend hours, emergency hotlines, and address.
- **Manage Testimonials** (`admin/testimonials.php`):
  - Moderate patient reviews (Approve, Unpublish, Delete, Add).
- **Contact Messages** (`admin/messages.php`):
  - View inquiries submitted via `contact.php`, mark as read, and reply via mailto.

---

### 🛡️ Security & Architecture
- **Prepared Statements**: All database operations use PDO prepared statements to guard against SQL injection.
- **Password Security**: Strong hashing via `password_hash()` (`PASSWORD_DEFAULT` / BCRYPT).
- **Authentication Guard**: Role-based access control protecting patient and admin routes.
- **Cross-Site Request Forgery (CSRF)**: CSRF tokens generated and validated on all form submissions.
- **Cross-Site Scripting (XSS)**: Escaping of all user-supplied data using `htmlspecialchars()`.
- **Resilient Fallback Layer**: If MySQL is offline during initial inspection, the platform gracefully alerts the administrator to run the installer without fatal crashes.

---

## 2. Directory Structure

```
DentistDentalClinicDentia/
├── database.sql                  # MySQL database schema and seed data
├── index.php                     # Root redirection router to main platform
├── README.md                     # Documentation and setup instructions
├── PROJECT.md                    # Project requirements and specification
└── Dentia/
    └── Dentia HTML/              # Main Web Application Directory
        ├── admin/                # Admin Management Dashboard
        │   ├── appointments.php  # Manage and filter appointments
        │   ├── auth.php          # Admin auth guard
        │   ├── dentists.php      # Manage dentists and credentials
        │   ├── footer.php        # Admin footer
        │   ├── header.php        # Admin header and sidebar
        │   ├── index.php         # Admin metrics overview
        │   ├── login.php         # Admin portal login
        │   ├── logout.php        # Admin logout
        │   ├── messages.php      # View patient inquiries
        │   ├── patients.php      # Manage registered patients
        │   ├── services.php      # Manage procedures and fees
        │   ├── settings.php      # Manage hours and clinic settings
        │   └── testimonials.php  # Moderate patient reviews
        ├── api/                  # JSON API Endpoints
        │   ├── check-slots.php   # Real-time slot availability & duplicate check
        │   └── book-appointment.php # Appointment submission handler
        ├── css/                  # Stylesheets
        │   ├── bootstrap.min.css # Bootstrap 5 framework
        │   ├── custom-modern.css # Medical-themed UI/UX styling
        │   ├── style.css         # Base template styling
        │   └── plugins.css       # Icons and animation styles
        ├── includes/             # Shared Backend Components
        │   ├── auth.php          # Session and CSRF helpers
        │   ├── db.php            # PDO Database connection layer
        │   ├── footer.php        # Global footer
        │   ├── functions.php     # Core business logic and slot algorithms
        │   └── header.php        # Global header with responsive navbar
        ├── js/                   # Scripts
        │   ├── custom-app.js     # Slot picker, live check, and search script
        │   ├── designesia.js     # Template UI effects
        │   └── plugins.js        # jQuery and Bootstrap bundles
        ├── about.php             # Clinic history and values
        ├── blog.php              # Dental articles list
        ├── blog-single.php       # Single article view
        ├── booking.php           # Appointment booking engine
        ├── contact.php           # Contact form and Google Maps
        ├── dentists.php          # Doctor credentials directory
        ├── faq.php               # Patient knowledgebase accordion
        ├── gallery.php           # Facilities & technology tour
        ├── index.php             # Modern clinic homepage
        ├── install.php           # 1-Click Database Setup Wizard
        ├── login.php             # Patient login
        ├── logout.php            # Patient logout
        ├── patient-dashboard.php # Patient portal
        ├── patient-profile.php   # Profile and password update
        ├── register.php          # Patient registration
        ├── services.php          # All dental procedures directory
        ├── service-*.php         # 10 dedicated service pages
        └── testimonials.php      # Patient feedback and review form
```

---

## 3. Installation & Setup Guide

### Option A: Using XAMPP / WAMP / Laragon
1. Copy the `DentistDentalClinicDentia` folder into your web server root:
   - **XAMPP**: `C:\xampp\htdocs\DentistDentalClinicDentia`
   - **WAMP**: `C:\wamp64\www\DentistDentalClinicDentia`
   - **Laragon**: `C:\laragon\www\DentistDentalClinicDentia`
2. Start the **Apache** and **MySQL** services from the control panel.
3. Open your browser and navigate to:
   ```
   http://localhost/DentistDentalClinicDentia/Dentia/Dentia%20HTML/install.php
   ```
4. Click **"Install Database & Sample Data"**. The wizard will automatically create the database, tables, default admin, doctors, services, and sample bookings!
5. Navigate to:
   ```
   http://localhost/DentistDentalClinicDentia/
   ```

### Option B: Using PHP Built-In Web Server
From the command line in the project folder:
```powershell
# Navigate into Dentia HTML directory
cd "Dentia\Dentia HTML"

# Start the PHP server
php -S localhost:8000
```
Open your browser at `http://localhost:8000/install.php` to initialize the database, then visit `http://localhost:8000/`.

---

## 4. Default Login Credentials

### 🛡️ Administrator Portal (`/admin/login.php`)
- **Email**: `admin@dentia.com`
- **Password**: `admin123`
- *Access*: Full executive control over appointments, patients, doctors, services, hours, testimonials, and contact inquiries.

### 👤 Sample Patient Account (`/login.php`)
- **Email**: `john.doe@example.com`
- **Password**: `patient123`
- *Access*: Patient dashboard, upcoming appointments, appointment cancellation, and profile management.

