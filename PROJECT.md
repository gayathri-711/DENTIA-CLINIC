# MEDINOVA – Multi-Speciality Hospital Management Platform

**Tagline:** Advanced Healthcare, Better Tomorrow.  
**Hospital Classification:** Tertiary & Quaternary Referral Healthcare Institution (JCI & NABH Accredited)

---

## 1. Project Overview

**MEDINOVA** is an enterprise-grade, responsive, full-stack Multi-Speciality Hospital Management Platform. It replaces legacy single-clinic websites with a comprehensive digital healthcare platform combining a patient-facing public hospital website, cascading appointment scheduling, and 8 specialized clinical and administrative role portals.

---

## 2. Centres of Excellence (12 Clinical Departments)

The system is architected around 12 tertiary clinical divisions, each featuring specialized equipment, board-certified physician rosters, and online booking:

1. **Cardiology & Cath Lab** (`departments/cardiology.html` & `.php`) – Biplane catheterization laboratory, 24/7 primary PTCA emergency care, and electrophysiology.
2. **Neurology & Stroke** (`departments/neurology.html` & `.php`) – Stroke thrombolysis unit, video EEG telemetry, and neurosurgical critical care.
3. **Robotic Orthopedics** (`departments/orthopedics.html` & `.php`) – Stryker Mako robotic joint replacements and arthroscopic sports surgery.
4. **Pediatrics & NICU** (`departments/pediatrics.html` & `.php`) – Child wellness and Level-III Neonatal Intensive Care Unit.
5. **Gynecology & Obstetrics** (`departments/gynecology.html` & `.php`) – High-risk maternity, fetal medicine, and 3D laparoscopic surgery.
6. **Dermatology** (`departments/dermatology.html` & `.php`) – Skin pathology, biological therapies for psoriasis, and dermatosurgery.
7. **Ophthalmology** (`departments/ophthalmology.html` & `.php`) – Zeiss VisuMax femtosecond laser, micro-incision cataract surgery, and retinal care.
8. **Dental Care** (`departments/dental-care.html` & `.php`) – Titanium implants, invisible aligners, and maxillofacial trauma reconstruction.
9. **Comprehensive Oncology** (`departments/oncology.html` & `.php`) – Medical oncology, targeted molecular chemotherapy, and tumor boards.
10. **General & Internal Medicine** (`departments/general-medicine.html` & `.php`) – Adult primary healthcare, metabolic disorder control, and acute care.
11. **Radiology & Diagnostics** (`departments/radiology.html` & `.php`) – 3.0 Tesla Silent MRI, 128-slice CT, and high-frequency ultrasound.
12. **Emergency Medicine & Trauma** (`departments/emergency-medicine.html` & `.php`) – Level-I trauma center, resuscitation bays, and 24/7 ambulances.

---

## 3. Role-Based Portals (RBAC)

The application provides distinct operational workspaces tailored for hospital roles:

1. **Executive Administration** (`/admin/index.html` & `admin/index.php`) – Cross-departmental KPI metrics, real-time appointment approval/rejection, doctor onboarding, bed management, and revenue monitoring.
2. **Physician Portal** (`/doctor/index.html` & `doctor/index.php`) – Daily patient consultation queues, clinical diagnoses, and electronic prescription orders.
3. **Patient Self-Service Portal** (`/patient/dashboard.html` & `patient/dashboard.php`) – Past encounters, diagnostic lab reports, e-prescriptions, and downloadable invoices.
4. **Reception & Intake Desk** (`/receptionist/index.html` & `receptionist/index.php`) – Front-desk patient check-in, token issuance, and doctor status roster.
5. **Inpatient Nursing Station** (`/nurse/index.html` & `nurse/index.php`) – Inpatient bed monitoring, vitals recording (BP, Pulse, SpO2, Temp), and MAR medication delivery logs.
6. **Pathology & Diagnostic Laboratory** (`/laboratory/index.html` & `laboratory/index.php`) – Diagnostic requisitions worklist, sample collection tracking, and critical range validation.
7. **Hospital Pharmacy & Formulary** (`/pharmacy/index.html` & `pharmacy/index.php`) – Drug formulary inventory, low stock alert badges, and prescription dispensing.
8. **Cashier & Billing Desk** (`/billing/index.html` & `billing/index.php`) – Itemized patient invoices, insurance claim settlement, and tax receipt generation.

---

## 4. Technology Stack & Design System

- **Frontend:** HTML5, CSS3, JavaScript (ES6+), Bootstrap 5, FontAwesome 6, Chart.js.
- **Design System:** Custom CSS tokens in `css/medinova-theme.css`:
  - Primary Navy: `#123B70`
  - Medical Teal: `#159CA8`
  - Clean Background: `#F5F9FC`
  - Typography: `Plus Jakarta Sans`
- **Backend:** PHP with PDO / Node.js preview server (`server.js`).
- **Database:** Normalized MySQL 24-table database (`database.sql` & `database/medinova_hospital.sql`).
- **Mobile Responsive:** Breakpoint-optimized layouts for mobile (< 576px), tablet (< 768px), desktop (< 992px), and high-resolution screens.
