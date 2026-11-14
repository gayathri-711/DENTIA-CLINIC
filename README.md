# MEDINOVA – Multi-Speciality Hospital Management System
> **Tagline:** *Advanced Healthcare, Better Tomorrow.*  
> **Accreditation:** JCI & NABH Accredited Tertiary Healthcare  

---

## 🏥 1. System Overview

**MEDINOVA** is an enterprise-grade, responsive, full-stack Multi-Speciality Hospital Management Platform designed for tertiary and quaternary healthcare institutions. It provides a patient-facing hospital web portal and eight specialized clinical and operational modules powered by role-based access control (RBAC).

---

## 🩺 2. The 12 Medical Specialities (Centres of Excellence)

Each department features dedicated landing pages, specialist directories, procedure details, and cascading online appointment booking:

1. **Cardiology & Cath Lab** (`/departments/cardiology.php`) – Emergency PTCA angioplasty, stenting, pacemaker implantation.
2. **Neurology & Stroke** (`/departments/neurology.php`) – Acute stroke thrombolysis, epilepsy monitoring, neurosurgery.
3. **Robotic Orthopedics** (`/departments/orthopedics.php`) – Computerized robotic joint replacements, sports trauma.
4. **Pediatrics & Level-III NICU** (`/departments/pediatrics.php`) – Infant care, childhood immunizations, neonatal ICU.
5. **Gynecology & Obstetrics** (`/departments/gynecology.php`) – Maternity suites, fetal medicine, laparoscopic surgery.
6. **Dermatology** (`/departments/dermatology.php`) – Skin pathology, laser dermatology, melanoma screening.
7. **Ophthalmology** (`/departments/ophthalmology.php`) – Micro-incision cataract surgery, LASIK, retina diagnostics.
8. **Dental Care & Surgery** (`/departments/dental-care.php`) – Titanium implants, micro-endodontics, maxillofacial surgery.
9. **Comprehensive Oncology** (`/departments/oncology.php`) – Medical oncology, targeted immunotherapy, tumor boards.
10. **General & Internal Medicine** (`/departments/general-medicine.php`) – Adult primary healthcare, metabolic syndrome, fevers.
11. **Radiology & Imaging** (`/departments/radiology.php`) – 3.0 Tesla Silent MRI, 128-slice CT, ultrasound.
12. **Emergency Medicine** (`/departments/emergency-medicine.php`) – Level-I trauma bays, resuscitation, ambulance dispatch.

---

## 👥 3. Portals & Multi-Role Access Control (RBAC)

The system enforces strict role-based session authorization with unified sign-in at `http://localhost:8000/login.html` (or `login.php`):

| Role | Portal URL | Demo Credentials | Key Capabilities |
| :--- | :--- | :--- | :--- |
| **Super Admin** | [`/admin/index.html`](http://localhost:8000/admin/index.html) | `admin@medinova.com`<br>`medinova123` | • Executive KPI stats & Chart.js analytics<br>• Real-time Accept / Cancel appointment requests<br>• Onboard new specialist doctors<br>• Hospital-wide ward & revenue monitoring |
| **Doctor / Specialist** | [`/doctor/index.php`](http://localhost:8000/doctor/index.php) | `cardiology@medinova.com`<br>`medinova123` | • Daily patient queue management<br>• Record clinical diagnoses & consultation notes<br>• Generate e-prescriptions & order pathology tests |
| **Patient** | [`/patient-dashboard.html`](http://localhost:8000/patient-dashboard.html) | `patient@medinova.com`<br>`medinova123` | • View upcoming visits & past history<br>• Download physician clinical notes & e-prescriptions<br>• Access diagnostic lab test results<br>• View itemized invoices & 1-click visit cancellation |
| **Receptionist** | [`/receptionist/index.php`](http://localhost:8000/receptionist/index.php) | `reception@medinova.com`<br>`medinova123` | • Front desk patient check-in & walk-in registration<br>• Appointment approval & schedule management<br>• Inpatient bed admissions & transfers |
| **Inpatient Nurse (RN)** | [`/nurse/index.php`](http://localhost:8000/nurse/index.php) | `nurse@medinova.com`<br>`medinova123` | • Inpatient ward monitoring<br>• Record patient vitals (BP, Pulse, Temp, SpO2)<br>• Nursing care observation logs |
| **Lab Technician** | [`/laboratory/index.php`](http://localhost:8000/laboratory/index.php) | `lab@medinova.com`<br>`medinova123` | • Diagnostic test worklist (Requested &rarr; Processing &rarr; Completed)<br>• Enter measured values & abnormal/critical flags<br>• Publish verified pathology reports |
| **Pharmacist** | [`/pharmacy/index.php`](http://localhost:8000/pharmacy/index.php) | `pharmacy@medinova.com`<br>`medinova123` | • Drug formulary & batch inventory tracking<br>• Expiration safety validation (&lt; 30d alerts)<br>• Prescription-based medication dispensing |
| **Billing & Cashier** | [`/billing/index.php`](http://localhost:8000/billing/index.php) | `billing@medinova.com`<br>`medinova123` | • Itemized invoice generation (Consultation + Lab + Pharmacy + Bed)<br>• Payment settlement (Cash, Card, Insurance Claim)<br>• Printable hospital tax invoice receipts |

---

## 📅 4. Interactive Cascading Booking Engine

Accessible publicly at [`http://localhost:8000/booking.html`](http://localhost:8000/booking.html) (or `booking.php`):
1. **Select Department**: Real-time AJAX loader populates available specialists for that department.
2. **Select Doctor**: Fetches consultation fee, room number, and working days.
3. **Select Date**: Interactively calculates slot availability.
4. **Choose Time Slot**: Interactive pills highlight available slots vs already-booked slots.
5. **Concurrency Validation**: Server enforces duplicate prevention for the same doctor and time slot.
6. **Reference Code**: Generates secure unique `MED-2026-XXXX` reference code.

---

## 🗄️ 5. Database Architecture (`medinova_hospital`)

Normalized 3NF MySQL database schema in `database.sql` and `database/medinova_hospital.sql`:
- `roles` (9 system roles)
- `users` (central authentication & password hashing)
- `departments` (12 clinical divisions)
- `doctors` & `doctor_schedules`
- `patients` (demographics, insurance, blood group, allergies)
- `staff` (nurses, receptionists, technicians, pharmacists)
- `appointments` (booking registry with status transitions)
- `medical_records` (EMR clinical notes & diagnoses)
- `medicines` & `medicine_stock` (batch & expiry tracking)
- `prescriptions` & `prescription_items` (e-prescriptions)
- `lab_tests`, `lab_orders`, `lab_results` (pathology reports)
- `room_categories`, `rooms`, `beds`, `admissions` (inpatient care)
- `invoices`, `invoice_items`, `payments` (billing ledgers)
- `hospital_settings`, `testimonials`, `contact_messages`, `blogs`, `audit_logs`

---

## 🚀 6. Running Locally & Production Hosting

### Option A: Local Dev Server (Running Right Now)
The server is already running on port **8000**:
- **Website Homepage:** [http://localhost:8000](http://localhost:8000)
- **Appointment Booking:** [http://localhost:8000/booking.html](http://localhost:8000/booking.html)
- **Admin Dashboard:** [http://localhost:8000/admin/index.html](http://localhost:8000/admin/index.html)
- **Patient Sign In:** [http://localhost:8000/login.html](http://localhost:8000/login.html)

To restart manually at any time:
```powershell
node server.js
```

### Option B: PHP & MySQL Production Hosting (XAMPP / cPanel / Apache)
1. Point Apache document root to `Dentia/Dentia HTML/` (or copy contents to `public_html/`).
2. Open phpMyAdmin or MySQL CLI and import `database.sql` (or `database/medinova_hospital.sql`).
3. Verify connection settings in `config/database.php` (defaults to `localhost`, port `3306`, user `root`, pass ``, db `medinova_hospital`).
4. Access `http://localhost/` or your domain. All PHP scripts connect with PDO.
