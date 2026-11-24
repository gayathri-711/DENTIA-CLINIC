# Dentia Dental Clinic - Google Apps Script Email Automation System

This is a standalone, independent email automation microservice built specifically for **Doctor Dental Clinic Dentia** using Google Apps Script.

It handles all transactional email workflows via Google's trusted infrastructure (`MailApp`), eliminating SMTP server downtime, authentication blocks, or port restrictions.

---

## 1. Project Files

- **`Config.gs`** – Global brand details, clinic contact numbers, theme colors, and security settings.
- **`EmailTemplates.gs`** – Reusable, responsive, high-converting HTML templates for all 7 clinic events.
- **`Code.gs`** – API request handler (`doPost` and `doGet`), token authentication, idempotency check, and error handling.
- **`README.md`** – Setup, deployment, and testing guide.

---

## 2. Supported Email Automation Workflows

| # | Event | Template Key | Description |
|---|---|---|---|
| 1 | **User Registration** | `welcome` | Welcome email to newly registered patients with profile summary and portal login link. |
| 2 | **Email Verification** | `otp_verification` | 6-digit one-time password (OTP) verification with 10-minute expiry notice. |
| 3 | **Password Reset** | `password_reset` | Secure password reset instructions and one-click recovery link. |
| 4 | **Booking Confirmation** | `booking_confirmation` | Appointment confirmed by medical team with Doctor, date, time, procedure, and checklist. |
| 5 | **Payment Receipt** | `payment_receipt` | Official itemized receipt and invoice breakdown with transaction ID and payment method. |
| 6 | **Booking Cancellation** | `booking_cancelled` | Courteous cancellation notice with 24/7 online re-booking link. |
| 7 | **Admin Notifications** | `admin_alert` | Immediate activity alerts to Chief Medical Admin (`admin@dentia.com`). |

---

## 3. Step-by-Step Deployment Instructions

### Step 1: Open Google Apps Script
1. Open your web browser and navigate to: **[https://script.google.com/](https://script.google.com/)**
2. Ensure you are signed in with the Google account you wish to send emails from (e.g. `dentiacare@gmail.com` or your clinical workspace account).
3. Click the **`+ New project`** button in the top left.
4. Name the project in the top left: **`Dentia-Email-Automation-Service`**.

---

### Step 2: Add Project Files
By default, a file named `Code.gs` is created.

1. **Update `Code.gs`**:
   - Open `Code.gs` in the editor.
   - Delete any default boilerplate code.
   - Copy the entire contents of [`google-apps-script/Code.gs`](file:///c:/Users/janan/Downloads/DentistDentalClinicDentia/google-apps-script/Code.gs) and paste it into the editor.
   - Press **`Ctrl + S`** (or `Cmd + S`) to save.

2. **Create `Config.gs`**:
   - In the left sidebar, click the **`+`** icon next to **Files** and select **Script**.
   - Name the file: `Config`.
   - Copy the entire contents of [`google-apps-script/Config.gs`](file:///c:/Users/janan/Downloads/DentistDentalClinicDentia/google-apps-script/Config.gs) and paste it.
   - Press **`Ctrl + S`** to save.

3. **Create `EmailTemplates.gs`**:
   - In the left sidebar, click the **`+`** icon next to **Files** and select **Script**.
   - Name the file: `EmailTemplates`.
   - Copy the entire contents of [`google-apps-script/EmailTemplates.gs`](file:///c:/Users/janan/Downloads/DentistDentalClinicDentia/google-apps-script/EmailTemplates.gs) and paste it.
   - Press **`Ctrl + S`** to save.

---

### Step 3: Configure Authentication Secret (Script Properties)
To protect your email service from unauthorized spam and abuse:

1. In the left navigation menu of Apps Script, click the **Project Settings** (gear icon ⚙️).
2. Scroll down to the **Script Properties** section.
3. Click **Add script property**:
   - **Property**: `AUTH_SECRET`
   - **Value**: Enter your secure secret key (e.g. `dentia_secret_key_prod_2026` or generate a random token).
4. Click **Save script properties**.

*(Note: If left unset, the system safely falls back to the default secret defined in `Config.gs`).*

---

### Step 4: Authorize Google Permissions
1. Switch back to the **Editor** (`< >` icon in the left menu).
2. Open `Code.gs`.
3. In the toolbar at the top, select the function **`testEmail`** from the function dropdown.
4. Click **Run**.
5. Google will prompt: **"Authorization required"**.
   - Click **Review permissions**.
   - Select your Google account.
   - If you see *"Google hasn't verified this app"*, click **Advanced** (bottom left), then click **Go to Dentia-Email-Automation-Service (unsafe)**.
   - Click **Allow**.
6. The test function will run and output:
   `[TEST] Appointment Confirmed... Test email successfully dispatched!`
   *(Check your Gmail inbox to see the test confirmation email!)*

---

### Step 5: Deploy as a Web App
1. At the top right of the Apps Script interface, click the blue **`Deploy`** button and select **`New deployment`**.
2. Click the gear icon ⚙️ next to "Select type" and choose **`Web app`**.
3. Fill in the deployment settings:
   - **Description**: `Dentia Production Email API v2`
   - **Execute as**: **`Me (your-email@gmail.com)`** *(Important: Must be 'Me')*
   - **Who has access**: **`Anyone`** *(Important: Allows your backend server to POST requests with the secret key)*
4. Click **Deploy**.
5. Copy the generated **Web App URL**. It will look like:
   ```
   https://script.google.com/macros/s/AKfycbx.../exec
   ```
   *(Keep this URL safe; this is your Web App endpoint).*

---

## 4. Backend Configuration

Add the generated Web App URL and Authentication Secret to your backend.

### Option A: Via Environment Variables (Recommended for Vercel)
Add these environment variables to your `.env` file or Vercel Project Settings:
```env
GOOGLE_APPS_SCRIPT_URL=https://script.google.com/macros/s/AKfycbx.../exec
GOOGLE_APPS_SCRIPT_SECRET=dentia_secret_key_prod_2026
```

### Option B: Via `gateway_config.json`
Update `Dentia/Dentia HTML/data/gateway_config.json`:
```json
{
  "email": {
    "enabled": true,
    "provider": "google_apps_script",
    "apps_script_url": "https://script.google.com/macros/s/AKfycbx.../exec",
    "apps_script_secret": "dentia_secret_key_prod_2026"
  }
}
```

---

## 5. API Request & Response Examples

### 1. User Registration (Welcome Email)
**POST** `https://script.google.com/macros/s/.../exec`
```json
{
  "secret": "dentia_secret_key_prod_2026",
  "recipient": "patient@gmail.com",
  "template": "welcome",
  "data": {
    "name": "Jane Doe",
    "email": "patient@gmail.com"
  }
}
```

### 2. Email Verification (OTP)
```json
{
  "secret": "dentia_secret_key_prod_2026",
  "recipient": "patient@gmail.com",
  "template": "otp_verification",
  "data": {
    "name": "Jane Doe",
    "otp": "492817",
    "validityMinutes": 10
  }
}
```

### 3. Booking Confirmation
```json
{
  "secret": "dentia_secret_key_prod_2026",
  "recipient": "patient@gmail.com",
  "template": "booking_confirmation",
  "idempotencyKey": "DEN-2026-8812-CONFIRM",
  "data": {
    "patient_name": "Jane Doe",
    "appointment_ref": "DEN-2026-8812",
    "service_name": "Cosmetic Dentistry & Teeth Whitening",
    "dentist_name": "Dr. Sarah Bennett",
    "appointment_date": "November 28, 2026",
    "appointment_time": "10:00 AM"
  }
}
```

### 4. Payment Receipt
```json
{
  "secret": "dentia_secret_key_prod_2026",
  "recipient": "patient@gmail.com",
  "template": "payment_receipt",
  "data": {
    "patient_name": "Jane Doe",
    "appointment_ref": "DEN-2026-8812",
    "receipt_id": "REC-948120",
    "service_name": "Cosmetic Teeth Whitening",
    "amount": "$150.00",
    "payment_method": "Credit Card (Visa ending in 4242)",
    "date": "Nov 28, 2026"
  }
}
```

### Response Format
**Success (200 OK):**
```json
{
  "success": true,
  "messageId": "7d91cb61-468b-4b15-992a-fae7a08b9192",
  "recipient": "patient@gmail.com",
  "template": "booking_confirmation",
  "subject": "Appointment Confirmed: [DEN-2026-8812] - Dentia Clinic",
  "remainingDailyQuota": 98,
  "timestamp": "2026-11-24T05:00:00.000Z"
}
```

**Duplicate Prevented via Idempotency:**
```json
{
  "success": true,
  "duplicate": true,
  "message": "Transactional email was already dispatched for this idempotency key.",
  "idempotencyKey": "DEN-2026-8812-CONFIRM",
  "timestamp": "2026-11-24T05:00:01.000Z"
}
```

---

## 6. Daily Quota Limits
- **Standard Free Gmail account (`@gmail.com`)**: 100 emails/day.
- **Google Workspace paid account (`@yourclinic.com`)**: 1,500 emails/day.
- The service tracks remaining quota automatically and exposes it in health checks.
