/**
 * Doctor Dental Clinic Dentia - Comprehensive Platform Server
 * Full REST API Support for:
 * 1. User/Patient Registration & Login with Past/Upcoming Visits and Regret Message with Doctor Alternatives
 * 2. Doctor Dashboard with Clinical Medical Records & Prescription Updates (Credentials created by Admin)
 * 3. Admin Full Portal Access (Doctors, Patients, and User Login Details Directory)
 * 4. Permanent Database Storage for Future Use
 */

const http = require('http');
const fs = require('fs');
const path = require('path');
const url = require('url');

const PORT = process.env.PORT || 8000;
const WEB_ROOT = path.join(__dirname, 'Dentia', 'Dentia HTML');
const DATA_DIR = path.join(WEB_ROOT, 'data');

if (!fs.existsSync(DATA_DIR)) {
  fs.mkdirSync(DATA_DIR, { recursive: true });
}

const APPOINTMENTS_FILE = path.join(DATA_DIR, 'appointments.json');
const DENTISTS_FILE = path.join(DATA_DIR, 'dentists.json');
const PATIENTS_FILE = path.join(DATA_DIR, 'patients.json');
const MESSAGES_FILE = path.join(DATA_DIR, 'messages.json');
const NOTIFICATIONS_FILE = path.join(DATA_DIR, 'notifications.json');
const MEDICAL_RECORDS_FILE = path.join(DATA_DIR, 'medical_records.json');

// Helpers for reading/writing JSON files
function readJSON(filePath, fallback = []) {
  try {
    if (fs.existsSync(filePath)) {
      return JSON.parse(fs.readFileSync(filePath, 'utf8'));
    }
  } catch (e) {
    console.error(`Error reading ${filePath}:`, e);
  }
  return fallback;
}

function writeJSON(filePath, data) {
  try {
    fs.writeFileSync(filePath, JSON.stringify(data, null, 2), 'utf8');
  } catch (e) {
    console.error(`Error writing ${filePath}:`, e);
  }
}

const MIME_TYPES = {
  '.html': 'text/html; charset=UTF-8',
  '.php': 'text/html; charset=UTF-8',
  '.css': 'text/css',
  '.js': 'application/javascript',
  '.json': 'application/json',
  '.png': 'image/png',
  '.jpg': 'image/jpeg',
  '.jpeg': 'image/jpeg',
  '.webp': 'image/webp',
  '.gif': 'image/gif',
  '.svg': 'image/svg+xml',
  '.ico': 'image/x-icon',
  '.mp4': 'video/mp4',
  '.woff': 'font/woff',
  '.woff2': 'font/woff2',
  '.ttf': 'font/ttf',
  '.eot': 'application/vnd.ms-fontobject'
};

const STANDARD_SLOTS = [
  '09:00 AM', '10:00 AM', '11:00 AM', '12:00 PM',
  '02:00 PM', '03:00 PM', '04:00 PM', '05:00 PM', '06:00 PM'
];

function normalizeTime(t) {
  if (!t) return '';
  t = t.trim();
  if (/^\d{1,2}:\d{2}$/.test(t)) {
    const [h, m] = t.split(':').map(Number);
    const period = h >= 12 ? 'PM' : 'AM';
    const hour12 = h % 12 === 0 ? 12 : h % 12;
    return `${hour12 < 10 ? '0' + hour12 : hour12}:${m < 10 ? '0' + m : m} ${period}`;
  }
  return t;
}

function normalizeDate(d) {
  if (!d) return '';
  d = d.trim();
  // If mm-dd-yyyy, convert to yyyy-mm-dd
  if (/^\d{2}-\d{2}-\d{4}$/.test(d)) {
    const [m, day, y] = d.split('-');
    return `${y}-${m}-${day}`;
  }
  return d;
}

// Notification Dispatcher (Email & SMS)
function dispatchNotification({ patient_name, patient_email, patient_phone, appointment_ref, type, subject, message, sms_text }) {
  let notifications = readJSON(NOTIFICATIONS_FILE, []);
  const now = new Date().toISOString();

  // 1. Email Notification
  const emailNotif = {
    id: notifications.length > 0 ? Math.max(...notifications.map(n => n.id)) + 1 : 1,
    recipient_email: patient_email,
    recipient_phone: patient_phone,
    recipient_name: patient_name,
    appointment_ref: appointment_ref || 'GENERAL',
    channel: 'email',
    type: type,
    subject: subject,
    message: message,
    created_at: now,
    status: 'Delivered'
  };
  notifications.push(emailNotif);

  // 2. SMS Notification
  const smsNotif = {
    id: notifications.length > 0 ? Math.max(...notifications.map(n => n.id)) + 1 : 2,
    recipient_email: patient_email,
    recipient_phone: patient_phone,
    recipient_name: patient_name,
    appointment_ref: appointment_ref || 'GENERAL',
    channel: 'sms',
    type: type,
    subject: 'SMS Text Alert',
    message: sms_text || message,
    created_at: now,
    status: 'Delivered'
  };
  notifications.push(smsNotif);

  writeJSON(NOTIFICATIONS_FILE, notifications);
  return { email: emailNotif, sms: smsNotif };
}

function parsePostData(req) {
  return new Promise((resolve) => {
    let body = '';
    req.on('data', chunk => { body += chunk.toString(); });
    req.on('end', () => {
      if (req.headers['content-type'] && req.headers['content-type'].includes('application/json')) {
        try {
          return resolve(JSON.parse(body || '{}'));
        } catch (e) {
          return resolve({});
        }
      }
      const parsed = {};
      const pairs = body.split('&');
      for (const pair of pairs) {
        if (!pair) continue;
        const [k, v] = pair.split('=');
        if (k) {
          parsed[decodeURIComponent(k.replace(/\+/g, ' '))] = decodeURIComponent((v || '').replace(/\+/g, ' '));
        }
      }
      resolve(parsed);
    });
  });
}

function sendJSON(res, data, status = 200) {
  res.writeHead(status, { 'Content-Type': 'application/json; charset=UTF-8' });
  res.end(JSON.stringify(data));
}

const server = http.createServer(async (req, res) => {
  const parsedUrl = url.parse(req.url, true);
  let pathname = decodeURIComponent(parsedUrl.pathname);

  // Route Aliases
  if (pathname === '/' || pathname === '/index.php') pathname = '/index.html';
  if (pathname === '/admin' || pathname === '/admin/') pathname = '/admin/index.html';
  if (pathname === '/login') pathname = '/login.html';
  if (pathname === '/register') pathname = '/register.html';
  if (pathname === '/dashboard') pathname = '/patient-dashboard.html';
  if (pathname === '/doctor-login' || pathname === '/doctor/login') pathname = '/doctor-login.html';
  if (pathname === '/doctor-dashboard' || pathname === '/doctor/dashboard' || pathname === '/doctor') pathname = '/doctor-dashboard.html';

  // ----------------------------------------------------
  // REST API ENDPOINTS
  // ----------------------------------------------------

  // 1. GET /api/appointments
  if (pathname === '/api/appointments' && req.method === 'GET') {
    const list = readJSON(APPOINTMENTS_FILE, []);
    return sendJSON(res, list);
  }

  // 2. POST /api/appointments/status (Admin/Staff status update)
  if (pathname === '/api/appointments/status' && req.method === 'POST') {
    const data = await parsePostData(req);
    const appId = parseInt(data.id || '0', 10);
    const newStatus = data.status;

    let appointments = readJSON(APPOINTMENTS_FILE, []);
    const idx = appointments.findIndex(a => a.id === appId);

    if (idx !== -1 && newStatus) {
      appointments[idx].status = newStatus;
      appointments[idx].updated_at = new Date().toISOString();
      const app = appointments[idx];
      writeJSON(APPOINTMENTS_FILE, appointments);

      // Trigger patient confirmation notifications if accepted / confirmed
      if (newStatus === 'Confirmed') {
        dispatchNotification({
          patient_name: app.patient_name,
          patient_email: app.patient_email,
          patient_phone: app.patient_phone,
          appointment_ref: app.appointment_ref,
          type: 'booking_confirmed',
          subject: `Appointment Confirmed! [${app.appointment_ref}] - Dentia Dental Clinic`,
          message: `Dear ${app.patient_name}, Great news! Your appointment for ${app.service_name} with ${app.dentist_name} on ${app.appointment_date} at ${app.appointment_time} has been accepted and CONFIRMED. Clinic address: 100 S Main St, Suite 400, New York, NY. Please arrive 10 minutes before your slot.`,
          sms_text: `Dentia Clinic: Hello ${app.patient_name}, your appointment ${app.appointment_ref} on ${app.appointment_date} at ${app.appointment_time} with ${app.dentist_name} is CONFIRMED. Call (800) 234-5678 for queries.`
        });
      } else if (newStatus === 'Cancelled') {
        dispatchNotification({
          patient_name: app.patient_name,
          patient_email: app.patient_email,
          patient_phone: app.patient_phone,
          appointment_ref: app.appointment_ref,
          type: 'booking_cancelled',
          subject: `Appointment Notice: Cancelled [${app.appointment_ref}] - Dentia Clinic`,
          message: `Dear ${app.patient_name}, your dental appointment [${app.appointment_ref}] scheduled for ${app.appointment_date} at ${app.appointment_time} has been cancelled. If you wish to reschedule, please visit our online booking portal.`,
          sms_text: `Dentia Clinic: Notice - Your appointment ${app.appointment_ref} on ${app.appointment_date} has been cancelled. Please visit our website to reschedule.`
        });
      }

      return sendJSON(res, { success: true, message: `Appointment marked as ${newStatus}` });
    }
    return sendJSON(res, { success: false, message: 'Appointment not found' }, 404);
  }

  // 3. GET /api/dentists (Doctors list)
  if (pathname === '/api/dentists' && req.method === 'GET') {
    const dentists = readJSON(DENTISTS_FILE, []);
    const safeDentists = dentists.map(d => {
      const copy = { ...d };
      copy.has_password = Boolean(copy.password);
      return copy;
    });
    return sendJSON(res, safeDentists);
  }

  // 4. POST /api/dentists (Add new doctor by Admin)
  if (pathname === '/api/dentists' && req.method === 'POST') {
    const data = await parsePostData(req);
    if (!data.name || !data.specialization) {
      return sendJSON(res, { success: false, message: 'Doctor name and specialization are required' }, 400);
    }

    let dentists = readJSON(DENTISTS_FILE, []);
    const docEmail = (data.email || 'doctor@dentiacare.com').trim().toLowerCase();
    const loginEmail = (data.login_email || docEmail).trim().toLowerCase();
    const password = data.password ? data.password.trim() : 'doctor123';
    const hasLogin = data.has_login !== undefined ? Boolean(data.has_login) : Boolean(data.password || data.enable_login);

    const newDoctor = {
      id: dentists.length > 0 ? Math.max(...dentists.map(d => d.id)) + 1 : 1,
      name: data.name.trim(),
      qualification: data.qualification || 'Doctor of Dental Surgery (DDS)',
      specialization: data.specialization.trim(),
      experience: data.experience || '5+ Years Experience',
      email: docEmail,
      login_email: loginEmail,
      password: password,
      has_login: hasLogin,
      phone: data.phone || '+1 (800) 234-5678',
      availability_days: data.availability_days || 'Monday - Friday',
      availability_hours: data.availability_hours || '09:00 AM - 05:00 PM',
      description: data.description || 'Experienced dental professional dedicated to comprehensive oral care and patient comfort.',
      image: data.image || 'images/team/1.webp',
      is_active: true
    };

    dentists.push(newDoctor);
    writeJSON(DENTISTS_FILE, dentists);
    return sendJSON(res, { success: true, message: 'Doctor added successfully', doctor: newDoctor });
  }

  // 5. POST /api/admin/doctor/credentials (Admin sets/edits doctor login credentials)
  if (pathname === '/api/admin/doctor/credentials' && req.method === 'POST') {
    const data = await parsePostData(req);
    const docId = parseInt(data.id || '0', 10);
    let dentists = readJSON(DENTISTS_FILE, []);
    const idx = dentists.findIndex(d => d.id === docId);

    if (idx === -1) {
      return sendJSON(res, { success: false, message: 'Doctor not found' }, 404);
    }

    if (data.login_email) {
      dentists[idx].login_email = data.login_email.trim().toLowerCase();
    }
    if (data.password) {
      dentists[idx].password = data.password.trim();
    }
    if (data.has_login !== undefined) {
      dentists[idx].has_login = Boolean(data.has_login);
    } else {
      dentists[idx].has_login = true;
    }

    writeJSON(DENTISTS_FILE, dentists);
    return sendJSON(res, {
      success: true,
      message: `Login credentials updated for ${dentists[idx].name}. Login access is now ${dentists[idx].has_login ? 'ENABLED' : 'DISABLED'}.`,
      doctor: dentists[idx]
    });
  }

  // 6. POST /api/dentists/delete
  if (pathname === '/api/dentists/delete' && req.method === 'POST') {
    const data = await parsePostData(req);
    const docId = parseInt(data.id || '0', 10);
    let dentists = readJSON(DENTISTS_FILE, []);
    dentists = dentists.filter(d => d.id !== docId);
    writeJSON(DENTISTS_FILE, dentists);
    return sendJSON(res, { success: true, message: 'Doctor removed' });
  }

  // 7. POST /api/doctor/login (Doctor login authentication)
  if (pathname === '/api/doctor/login' && req.method === 'POST') {
    const data = await parsePostData(req);
    const email = (data.email || '').trim().toLowerCase();
    const password = (data.password || '').trim();

    if (!email || !password) {
      return sendJSON(res, { success: false, message: 'Doctor email and password are required' }, 400);
    }

    const dentists = readJSON(DENTISTS_FILE, []);
    const match = dentists.find(d => 
      ((d.login_email && d.login_email.toLowerCase() === email) || (d.email && d.email.toLowerCase() === email)) &&
      d.password === password
    );

    if (!match) {
      return sendJSON(res, { success: false, message: 'Invalid doctor login credentials.' }, 401);
    }

    // STRICT CHECK: Admin must have created/granted login credentials!
    if (!match.has_login) {
      return sendJSON(res, {
        success: false,
        message: 'Doctor login access has not been activated by the Clinic Administrator. Please request credentials from the Admin.'
      }, 403);
    }

    const safeDoctor = { ...match };
    delete safeDoctor.password;
    return sendJSON(res, {
      success: true,
      message: `Welcome Dr. ${safeDoctor.name}!`,
      doctor: safeDoctor
    });
  }

  // 8. GET /api/doctor/appointments (Get appointments specific to a doctor)
  if (pathname === '/api/doctor/appointments' && req.method === 'GET') {
    const docId = parseInt(parsedUrl.query.doctor_id || '0', 10);
    const docEmail = (parsedUrl.query.email || '').trim().toLowerCase();

    const appointments = readJSON(APPOINTMENTS_FILE, []);
    let filtered = [];

    if (docId > 0) {
      filtered = appointments.filter(a => a.dentist_id === docId);
    } else if (docEmail) {
      const dentists = readJSON(DENTISTS_FILE, []);
      const doc = dentists.find(d => 
        (d.login_email && d.login_email.toLowerCase() === docEmail) ||
        (d.email && d.email.toLowerCase() === docEmail)
      );
      if (doc) {
        filtered = appointments.filter(a => a.dentist_id === doc.id);
      }
    }

    return sendJSON(res, { success: true, appointments: filtered });
  }

  // 9. POST /api/doctor/appointment/action (Doctor accepts, completes, cancels, or notes appointment)
  if (pathname === '/api/doctor/appointment/action' && req.method === 'POST') {
    const data = await parsePostData(req);
    const appId = parseInt(data.id || '0', 10);
    const action = data.action; // 'Confirm' | 'Complete' | 'Cancel'
    const notes = data.clinical_notes || '';

    let appointments = readJSON(APPOINTMENTS_FILE, []);
    const idx = appointments.findIndex(a => a.id === appId);

    if (idx === -1) {
      return sendJSON(res, { success: false, message: 'Appointment not found' }, 404);
    }

    const app = appointments[idx];
    if (action === 'Confirm' || action === 'Confirmed') {
      app.status = 'Confirmed';
      dispatchNotification({
        patient_name: app.patient_name,
        patient_email: app.patient_email,
        patient_phone: app.patient_phone,
        appointment_ref: app.appointment_ref,
        type: 'booking_confirmed',
        subject: `Appointment Confirmed by ${app.dentist_name}! [${app.appointment_ref}]`,
        message: `Dear ${app.patient_name}, ${app.dentist_name} has reviewed and CONFIRMED your dental appointment for ${app.service_name} on ${app.appointment_date} at ${app.appointment_time}. Please arrive promptly at our clinic (100 S Main St, Suite 400, NY).`,
        sms_text: `Dentia Clinic: ${app.dentist_name} has CONFIRMED your appointment on ${app.appointment_date} at ${app.appointment_time}. Ref: ${app.appointment_ref}.`
      });
    } else if (action === 'Complete' || action === 'Completed') {
      app.status = 'Completed';
    } else if (action === 'Cancel' || action === 'Cancelled') {
      app.status = 'Cancelled';
      dispatchNotification({
        patient_name: app.patient_name,
        patient_email: app.patient_email,
        patient_phone: app.patient_phone,
        appointment_ref: app.appointment_ref,
        type: 'booking_cancelled',
        subject: `Appointment Notice: Cancelled [${app.appointment_ref}]`,
        message: `Dear ${app.patient_name}, your appointment with ${app.dentist_name} on ${app.appointment_date} was cancelled. Please log in to your patient portal to pick a new date or doctor.`,
        sms_text: `Dentia Clinic: Appointment ${app.appointment_ref} with ${app.dentist_name} has been cancelled. Visit your dashboard to rebook.`
      });
    }

    if (notes) {
      app.doctor_notes = notes;
    }
    app.updated_at = new Date().toISOString();

    writeJSON(APPOINTMENTS_FILE, appointments);
    return sendJSON(res, { success: true, message: `Appointment status updated to ${app.status}`, appointment: app });
  }

  // 10. GET /api/medical-records (Get clinical treatment records & prescriptions)
  if (pathname === '/api/medical-records' && req.method === 'GET') {
    const records = readJSON(MEDICAL_RECORDS_FILE, []);
    const patientEmail = (parsedUrl.query.patient_email || parsedUrl.query.email || '').trim().toLowerCase();
    const patientId = parseInt(parsedUrl.query.patient_id || '0', 10);
    const docId = parseInt(parsedUrl.query.doctor_id || parsedUrl.query.dentist_id || '0', 10);

    let list = records;
    if (patientEmail) {
      list = list.filter(r => (r.patient_email || '').toLowerCase() === patientEmail);
    } else if (patientId > 0) {
      list = list.filter(r => r.patient_id === patientId);
    } else if (docId > 0) {
      list = list.filter(r => r.dentist_id === docId);
    }

    return sendJSON(res, { success: true, records: list });
  }

  // 11. POST /api/doctor/patient-record (Doctor updates/creates patient clinical record & prescription)
  if (pathname === '/api/doctor/patient-record' && req.method === 'POST') {
    const data = await parsePostData(req);
    const patientEmail = (data.patient_email || '').trim().toLowerCase();
    const patientName = (data.patient_name || '').trim();
    const docId = parseInt(data.dentist_id || data.doctor_id || '0', 10);
    const docName = (data.dentist_name || data.doctor_name || '').trim();
    const appRef = (data.appointment_ref || '').trim();

    let records = readJSON(MEDICAL_RECORDS_FILE, []);
    const newRecord = {
      id: records.length > 0 ? Math.max(...records.map(r => r.id)) + 1 : 1,
      patient_id: parseInt(data.patient_id || '0', 10),
      patient_name: patientName,
      patient_email: patientEmail,
      dentist_id: docId,
      dentist_name: docName,
      appointment_ref: appRef,
      treatment_date: data.treatment_date || new Date().toISOString().split('T')[0],
      diagnosis: data.diagnosis || '',
      treatment_provided: data.treatment_provided || '',
      prescription: data.prescription || '',
      doctor_notes: data.doctor_notes || '',
      followup_date: data.followup_date || '',
      created_at: new Date().toISOString()
    };

    records.push(newRecord);
    writeJSON(MEDICAL_RECORDS_FILE, records);

    // Update appointment if appointment_id provided
    if (data.appointment_id) {
      const appId = parseInt(data.appointment_id, 10);
      let appointments = readJSON(APPOINTMENTS_FILE, []);
      const idx = appointments.findIndex(a => a.id === appId);
      if (idx !== -1) {
        appointments[idx].doctor_notes = data.doctor_notes || data.diagnosis;
        appointments[idx].status = 'Completed';
        writeJSON(APPOINTMENTS_FILE, appointments);
      }
    }

    // Dispatch update notification to patient
    dispatchNotification({
      patient_name: patientName,
      patient_email: patientEmail,
      patient_phone: data.patient_phone || '',
      appointment_ref: appRef,
      type: 'medical_record_updated',
      subject: `Clinical Record & Prescription Updated by ${docName} [${appRef}]`,
      message: `Dear ${patientName}, your dental treatment records and prescription have been updated by ${docName}. Diagnosis: ${newRecord.diagnosis}. Treatment: ${newRecord.treatment_provided}. Prescription: ${newRecord.prescription}. You can view the full record anytime in your Patient Dashboard.`,
      sms_text: `Dentia Clinic: ${docName} updated your clinical treatment record & prescription for visit ${appRef}. Log in to your portal to view.`
    });

    return sendJSON(res, { success: true, message: 'Patient clinical record updated successfully', record: newRecord });
  }

  // 12. GET /api/admin/users (Admin sees all users, login details, and visit metrics)
  if (pathname === '/api/admin/users' && req.method === 'GET') {
    const patients = readJSON(PATIENTS_FILE, []);
    const appointments = readJSON(APPOINTMENTS_FILE, []);

    const usersWithStats = patients.map(p => {
      const userApps = appointments.filter(a => (a.patient_email || '').toLowerCase() === p.email.toLowerCase());
      return {
        id: p.id,
        name: p.name,
        email: p.email,
        phone: p.phone,
        address: p.address,
        gender: p.gender || 'Not specified',
        dob: p.dob || '—',
        login_credential: p.email,
        account_status: 'Active',
        total_visits: userApps.length,
        registered_at: p.created_at || '2026-01-10T10:00:00.000Z'
      };
    });

    return sendJSON(res, { success: true, users: usersWithStats });
  }

  // 13. GET /api/patients
  if (pathname === '/api/patients' && req.method === 'GET') {
    const patients = readJSON(PATIENTS_FILE, []);
    return sendJSON(res, patients);
  }

  // 14. POST /api/auth/register (User registration)
  if (pathname === '/api/auth/register' && req.method === 'POST') {
    const data = await parsePostData(req);
    const name = (data.name || '').trim();
    const email = (data.email || '').trim().toLowerCase();
    const phone = (data.phone || '').trim();
    const password = data.password || '';

    if (!name || !email || !password) {
      return sendJSON(res, { success: false, message: 'Name, email, and password are required' }, 400);
    }

    let patients = readJSON(PATIENTS_FILE, []);
    if (patients.some(p => p.email.toLowerCase() === email)) {
      return sendJSON(res, { success: false, message: 'An account with this email address already exists' }, 400);
    }

    const newPatient = {
      id: patients.length > 0 ? Math.max(...patients.map(p => p.id)) + 1 : 1,
      name: name,
      email: email,
      phone: phone,
      password: password,
      dob: data.dob || null,
      gender: data.gender || 'Male',
      address: data.address || '',
      created_at: new Date().toISOString()
    };

    patients.push(newPatient);
    writeJSON(PATIENTS_FILE, patients);

    const safePatient = { ...newPatient };
    delete safePatient.password;
    return sendJSON(res, { success: true, message: 'Registration successful', patient: safePatient });
  }

  // 15. POST /api/auth/login (User login)
  if (pathname === '/api/auth/login' && req.method === 'POST') {
    const data = await parsePostData(req);
    const email = (data.email || '').trim().toLowerCase();
    const password = data.password || '';

    const patients = readJSON(PATIENTS_FILE, []);
    const match = patients.find(p => p.email.toLowerCase() === email && p.password === password);

    if (match) {
      const safePatient = { ...match };
      delete safePatient.password;
      return sendJSON(res, { success: true, message: 'Login successful', patient: safePatient });
    }
    return sendJSON(res, { success: false, message: 'Invalid email or password' }, 401);
  }

  // 16. GET /api/patient/appointments (Get specific patient visits)
  if (pathname === '/api/patient/appointments' && req.method === 'GET') {
    const email = (parsedUrl.query.email || '').trim().toLowerCase();
    const phone = (parsedUrl.query.phone || '').trim();
    const appointments = readJSON(APPOINTMENTS_FILE, []);

    const patientApps = appointments.filter(a => {
      const matchEmail = email && (a.patient_email || '').toLowerCase() === email;
      const matchPhone = phone && (a.patient_phone || '').replace(/\D/g, '') === phone.replace(/\D/g, '');
      return matchEmail || matchPhone;
    });

    return sendJSON(res, { success: true, appointments: patientApps });
  }

  // 17. GET /api/notifications (Get notification history for a patient or ref)
  if (pathname === '/api/notifications' && req.method === 'GET') {
    const email = (parsedUrl.query.email || '').trim().toLowerCase();
    const ref = (parsedUrl.query.ref || '').trim();
    const allNotifs = readJSON(NOTIFICATIONS_FILE, []);

    let filtered = allNotifs;
    if (email) {
      filtered = filtered.filter(n => (n.recipient_email || '').toLowerCase() === email);
    }
    if (ref) {
      filtered = filtered.filter(n => n.appointment_ref === ref);
    }

    return sendJSON(res, { success: true, notifications: filtered.reverse() });
  }

  // 18. GET /api/check-slots.php
  if (pathname === '/api/check-slots.php') {
    const dentistId = parseInt(parsedUrl.query.dentist_id || '0', 10);
    const rawDate = parsedUrl.query.date || '';
    const normDate = normalizeDate(rawDate);

    const appointments = readJSON(APPOINTMENTS_FILE, []);
    const booked = appointments
      .filter(a => {
        const matchesDoc = a.dentist_id === dentistId;
        const matchesDate = normalizeDate(a.appointment_date) === normDate || a.appointment_date === rawDate;
        const isActive = a.status !== 'Cancelled';
        return matchesDoc && matchesDate && isActive;
      })
      .map(a => normalizeTime(a.appointment_time));

    const availableSlots = STANDARD_SLOTS.filter(s => !booked.includes(normalizeTime(s)));

    return sendJSON(res, {
      success: true,
      dentist_id: dentistId,
      date: rawDate,
      slots: STANDARD_SLOTS,
      booked: booked,
      available_slots: availableSlots
    });
  }

  // 19. POST /api/book-appointment.php or /booking.php
  if ((pathname === '/api/book-appointment.php' || pathname === '/booking.php') && req.method === 'POST') {
    const postData = await parsePostData(req);

    const name = (postData.name || '').trim();
    const email = (postData.email || '').trim().toLowerCase();
    const phone = (postData.phone || '').trim();
    let serviceId = parseInt(postData.service_id || '0', 10);
    let dentistId = parseInt(postData.dentist_id || '0', 10);
    const rawDate = (postData.date || '').trim();
    const rawTime = (postData.time || '').trim();
    const message = (postData.message || '').trim();

    const date = normalizeDate(rawDate);
    const time = normalizeTime(rawTime);

    const dentists = readJSON(DENTISTS_FILE, []);
    const currentDoc = dentists.find(d => d.id === dentistId) || dentists[0] || { id: 1, name: 'Dr. Sarah Bennett' };
    dentistId = currentDoc.id;

    if (!name || !email || !rawDate || rawDate === 'Select Date' || !rawTime || rawTime === 'Select Time') {
      return sendJSON(res, {
        success: false,
        message: 'Please provide your full name, email, appointment date, and choose an available time slot.'
      }, 400);
    }

    let appointments = readJSON(APPOINTMENTS_FILE, []);

    // ---------------------------------------------------------------------------------
    // STRICT CONCURRENCY & DUPLICATE SLOT CHECKING
    // If the requested doctor & time is not available:
    // 1. Send regret message
    // 2. Send that doctor's other available times
    // 3. Send OTHER DOCTORS who ARE available at the requested time!
    // ---------------------------------------------------------------------------------
    const conflictingApp = appointments.find(a => {
      const matchDoc = a.dentist_id === dentistId;
      const matchDate = normalizeDate(a.appointment_date) === date || a.appointment_date === rawDate;
      const matchTime = normalizeTime(a.appointment_time) === time;
      const isActive = a.status !== 'Cancelled';
      return matchDoc && matchDate && matchTime && isActive;
    });

    if (conflictingApp) {
      // Slot is not available!
      // 1) Find chosen doctor's remaining slots on this date:
      const bookedSlotsForDoc = appointments
        .filter(a => {
          const matchDoc = a.dentist_id === dentistId;
          const matchDate = normalizeDate(a.appointment_date) === date || a.appointment_date === rawDate;
          return matchDoc && matchDate && a.status !== 'Cancelled';
        })
        .map(a => normalizeTime(a.appointment_time));

      const remainingSlots = STANDARD_SLOTS.filter(s => !bookedSlotsForDoc.includes(normalizeTime(s)));

      // 2) Find ANY OTHER DOCTORS who ARE available at this exact requested time on this date!
      const otherAvailableDoctors = dentists.filter(d => {
        if (d.id === dentistId || !d.is_active) return false;
        const hasClash = appointments.some(a => 
          a.dentist_id === d.id && 
          (normalizeDate(a.appointment_date) === date || a.appointment_date === rawDate) &&
          normalizeTime(a.appointment_time) === time &&
          a.status !== 'Cancelled'
        );
        return !hasClash;
      });

      // Construct Regret & Recommendation Message
      let regretSubject = `Appointment Update: Regret Notice for ${time} on ${rawDate} - Dentia Clinic`;
      let otherDocNames = otherAvailableDoctors.map(d => `${d.name} (${d.specialization})`).join(', ');

      let detailedRegretMsg = `Dear ${name},\n\nWe regret to inform you that your requested appointment on ${rawDate} at ${time} with ${currentDoc.name} is not available, as this time slot was just booked by another patient.\n\n`;
      detailedRegretMsg += `1. ALTERNATIVE AVAILABLE TIMINGS FOR ${currentDoc.name.toUpperCase()} ON ${rawDate}:\n`;
      detailedRegretMsg += (remainingSlots.length > 0 ? remainingSlots.join(', ') : 'No further slots available today.') + '\n\n';

      if (otherAvailableDoctors.length > 0) {
        detailedRegretMsg += `2. OTHER DOCTORS AVAILABLE AT YOUR REQUESTED TIME (${time}) ON ${rawDate}:\n`;
        detailedRegretMsg += otherDocNames + '\n\n';
      }
      detailedRegretMsg += `Please visit our appointment booking portal to confirm your preferred alternative.`;

      let smsRegret = `Dentia Clinic: Dear ${name}, we regret that ${time} on ${rawDate} with ${currentDoc.name} is unavailable. Available times for ${currentDoc.name}: ${remainingSlots.slice(0, 3).join(', ')}.`;
      if (otherAvailableDoctors.length > 0) {
        smsRegret += ` Other doctors free at ${time}: ${otherAvailableDoctors[0].name}.`;
      }

      // Dispatch Regret Notification (Email & SMS)
      const notif = dispatchNotification({
        patient_name: name,
        patient_email: email,
        patient_phone: phone,
        appointment_ref: 'REGRET-UNAVAILABLE',
        type: 'slot_unavailable',
        subject: regretSubject,
        message: detailedRegretMsg,
        sms_text: smsRegret
      });

      return sendJSON(res, {
        success: false,
        already_booked: true,
        message: `We regret to inform you that the time slot ${time} on ${rawDate} for ${currentDoc.name} is already booked. A regret message has been dispatched to your email and phone.`,
        dentist_id: dentistId,
        dentist_name: currentDoc.name,
        date: rawDate,
        attempted_time: time,
        available_slots: remainingSlots,
        other_available_doctors: otherAvailableDoctors.map(d => ({
          id: d.id,
          name: d.name,
          specialization: d.specialization,
          image: d.image
        })),
        notifications_sent: notif
      }, 409);
    }

    // First patient gets the appointment!
    const ref = 'DEN-2026-' + Math.floor(1000 + Math.random() * 9000);
    const serviceName = postData.service_name || postData.service || 'General Dentistry';

    // Auto-link or create patient in patients list so they can log in and see it immediately
    let patients = readJSON(PATIENTS_FILE, []);
    let patientRecord = patients.find(p => p.email.toLowerCase() === email);
    if (!patientRecord) {
      patientRecord = {
        id: patients.length > 0 ? Math.max(...patients.map(p => p.id)) + 1 : 1,
        name: name,
        email: email,
        phone: phone,
        password: 'patient' + Math.floor(100 + Math.random() * 900),
        address: '',
        created_at: new Date().toISOString()
      };
      patients.push(patientRecord);
      writeJSON(PATIENTS_FILE, patients);
    }

    const newAppointment = {
      id: appointments.length > 0 ? Math.max(...appointments.map(a => a.id)) + 1 : 1,
      appointment_ref: ref,
      patient_id: patientRecord.id,
      patient_name: name,
      patient_email: email,
      patient_phone: phone,
      service_id: serviceId || 1,
      service_name: serviceName,
      dentist_id: dentistId,
      dentist_name: currentDoc.name,
      appointment_date: rawDate,
      appointment_time: time,
      message: message,
      status: 'Pending', // New requests start as Pending, ready for Admin or Doctor to Accept
      created_at: new Date().toISOString()
    };

    appointments.push(newAppointment);
    writeJSON(APPOINTMENTS_FILE, appointments);

    // Dispatch Confirmation Email & SMS for the booked appointment
    const notifs = dispatchNotification({
      patient_name: name,
      patient_email: email,
      patient_phone: phone,
      appointment_ref: ref,
      type: 'booking_received',
      subject: `Appointment Booking Request Received: [${ref}] - Dentia Dental Clinic`,
      message: `Dear ${name}, thank you for booking with Doctor Dental Clinic Dentia. Your appointment request for ${serviceName} with ${currentDoc.name} on ${rawDate} at ${time} has been received and scheduled (Ref: ${ref}). You will receive an immediate update once confirmed by our medical team. View your status anytime in your Patient Dashboard.`,
      sms_text: `Dentia Clinic: Hello ${name}, your appointment request ${ref} with ${currentDoc.name} on ${rawDate} at ${time} is received. We will send confirmation shortly!`
    });

    // If legacy jQuery AJAX expecting plain text 'sent'
    if (req.headers['x-requested-with'] === 'XMLHttpRequest' && pathname === '/booking.php' && !req.headers['accept']?.includes('json')) {
      res.writeHead(200, { 'Content-Type': 'text/plain' });
      res.end('sent');
      return;
    }

    return sendJSON(res, {
      success: true,
      message: 'Your appointment has been successfully scheduled!',
      ref: ref,
      details: {
        name: name,
        email: email,
        phone: phone,
        service_name: serviceName,
        dentist_name: currentDoc.name,
        date: rawDate,
        time: time
      },
      notifications_sent: notifs
    });
  }

  // 20. Static File Resolution
  let filePath = path.join(WEB_ROOT, pathname);

  if (!fs.existsSync(filePath)) {
    if (fs.existsSync(filePath + '.html')) filePath += '.html';
  }

  fs.stat(filePath, (err, stats) => {
    if (err || !stats.isFile()) {
      res.writeHead(404, { 'Content-Type': 'text/html; charset=UTF-8' });
      res.end(`
        <!DOCTYPE html>
        <html>
        <head><title>404 Not Found - Dentia Dental Clinic</title>
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        </head>
        <body class="bg-light p-5 text-center">
          <div class="container py-5">
            <h1 class="display-3 text-primary fw-bold">404</h1>
            <h3 class="mb-3">Page Not Found</h3>
            <p class="text-muted mb-4">The requested page <code>${pathname}</code> was not found.</p>
            <a href="/" class="btn btn-primary rounded-pill px-4">Return to Homepage</a>
          </div>
        </body>
        </html>
      `);
      return;
    }

    const ext = path.extname(filePath).toLowerCase();
    const contentType = MIME_TYPES[ext] || 'application/octet-stream';

    res.writeHead(200, { 'Content-Type': contentType, 'Cache-Control': 'no-cache' });
    fs.createReadStream(filePath).pipe(res);
  });
});

server.listen(PORT, '0.0.0.0', () => {
  console.log(`====================================================`);
  console.log(`Doctor Dental Clinic Dentia Platform Server Running`);
  console.log(`Local URL: http://localhost:${PORT}`);
  console.log(`Admin Portal: http://localhost:${PORT}/admin/index.html`);
  console.log(`Doctor Login: http://localhost:${PORT}/doctor-login.html`);
  console.log(`Patient Login: http://localhost:${PORT}/login.html`);
  console.log(`====================================================`);
});
