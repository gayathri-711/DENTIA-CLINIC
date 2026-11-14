/**
 * Doctor Dental Clinic Dentia - Comprehensive Platform Server
 * Full REST API Support for:
 * - Appointment Requests & Acceptance
 * - Doctor / Staff Management (Add, Edit, Delete)
 * - User-Based Patient Login & Registration
 * - Patient Portal Dashboard
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

  // Friendly Route Aliases
  if (pathname === '/' || pathname === '/index.php') pathname = '/index.html';
  if (pathname === '/admin' || pathname === '/admin/') pathname = '/admin/index.html';
  if (pathname === '/login') pathname = '/login.html';
  if (pathname === '/register') pathname = '/register.html';
  if (pathname === '/dashboard') pathname = '/patient-dashboard.html';

  // ----------------------------------------------------
  // REST API ENDPOINTS
  // ----------------------------------------------------

  // 1. GET /api/appointments
  if (pathname === '/api/appointments' && req.method === 'GET') {
    const list = readJSON(APPOINTMENTS_FILE, []);
    return sendJSON(res, list);
  }

  // 2. POST /api/appointments/status (Accept / Cancel / Complete)
  if (pathname === '/api/appointments/status' && req.method === 'POST') {
    const data = await parsePostData(req);
    const appId = parseInt(data.id || '0', 10);
    const newStatus = data.status;

    let appointments = readJSON(APPOINTMENTS_FILE, []);
    const idx = appointments.findIndex(a => a.id === appId);

    if (idx !== -1 && newStatus) {
      appointments[idx].status = newStatus;
      appointments[idx].updated_at = new Date().toISOString();
      writeJSON(APPOINTMENTS_FILE, appointments);
      return sendJSON(res, { success: true, message: `Appointment marked as ${newStatus}` });
    }
    return sendJSON(res, { success: false, message: 'Appointment not found' }, 404);
  }

  // 3. GET /api/dentists (Doctors list)
  if (pathname === '/api/dentists' && req.method === 'GET') {
    const dentists = readJSON(DENTISTS_FILE, []);
    return sendJSON(res, dentists);
  }

  // 4. POST /api/dentists (Add new doctor)
  if (pathname === '/api/dentists' && req.method === 'POST') {
    const data = await parsePostData(req);
    if (!data.name || !data.specialization) {
      return sendJSON(res, { success: false, message: 'Doctor name and specialization are required' }, 400);
    }

    let dentists = readJSON(DENTISTS_FILE, []);
    const newDoctor = {
      id: dentists.length > 0 ? Math.max(...dentists.map(d => d.id)) + 1 : 1,
      name: data.name,
      qualification: data.qualification || 'Doctor of Dental Surgery (DDS)',
      specialization: data.specialization,
      experience: data.experience || '5+ Years Experience',
      email: data.email || 'doctor@dentiacare.com',
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

  // 5. POST /api/dentists/delete
  if (pathname === '/api/dentists/delete' && req.method === 'POST') {
    const data = await parsePostData(req);
    const docId = parseInt(data.id || '0', 10);
    let dentists = readJSON(DENTISTS_FILE, []);
    dentists = dentists.filter(d => d.id !== docId);
    writeJSON(DENTISTS_FILE, dentists);
    return sendJSON(res, { success: true, message: 'Doctor removed' });
  }

  // 6. GET /api/patients
  if (pathname === '/api/patients' && req.method === 'GET') {
    const patients = readJSON(PATIENTS_FILE, []);
    return sendJSON(res, patients);
  }

  // 7. POST /api/auth/register (User registration)
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

  // 8. POST /api/auth/login (User login)
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

  // 9. GET /api/patient/appointments (Get specific patient visits)
  if (pathname === '/api/patient/appointments' && req.method === 'GET') {
    const email = (parsedUrl.query.email || '').trim().toLowerCase();
    const appointments = readJSON(APPOINTMENTS_FILE, []);
    const patientApps = appointments.filter(a => (a.patient_email || '').toLowerCase() === email);
    return sendJSON(res, { success: true, appointments: patientApps });
  }

  // 10. GET /api/check-slots.php
  if (pathname === '/api/check-slots.php') {
    const dentistId = parseInt(parsedUrl.query.dentist_id || '0', 10);
    const date = parsedUrl.query.date || '';

    const appointments = readJSON(APPOINTMENTS_FILE, []);
    const booked = appointments
      .filter(a => a.dentist_id === dentistId && a.appointment_date === date && a.status !== 'Cancelled')
      .map(a => a.appointment_time);

    return sendJSON(res, {
      success: true,
      dentist_id: dentistId,
      date: date,
      slots: STANDARD_SLOTS,
      booked: booked
    });
  }

  // 11. POST /api/book-appointment.php or /booking.php
  if ((pathname === '/api/book-appointment.php' || pathname === '/booking.php') && req.method === 'POST') {
    const postData = await parsePostData(req);

    const name = (postData.name || '').trim();
    const email = (postData.email || '').trim();
    const phone = (postData.phone || '').trim();
    let serviceId = parseInt(postData.service_id || '0', 10);
    let dentistId = parseInt(postData.dentist_id || '0', 10);
    const date = (postData.date || '').trim();
    const time = (postData.time || '').trim();
    const message = (postData.message || '').trim();

    const dentists = readJSON(DENTISTS_FILE, []);
    const currentDoc = dentists.find(d => d.id === dentistId) || dentists[0] || { id: 1, name: 'Dr. Sarah Bennett' };
    dentistId = currentDoc.id;

    if (!name || !email || !date || !time) {
      return sendJSON(res, { success: false, message: 'Please provide full name, email, appointment date, and time slot.' });
    }

    let appointments = readJSON(APPOINTMENTS_FILE, []);
    const isBooked = appointments.some(a => 
      a.dentist_id === dentistId && a.appointment_date === date && a.appointment_time === time && a.status !== 'Cancelled'
    );

    if (isBooked) {
      return sendJSON(res, {
        success: false,
        message: `The time slot ${time} on ${date} is already booked for ${currentDoc.name}. Please select another time or doctor.`
      });
    }

    const ref = 'DEN-2026-' + Math.floor(1000 + Math.random() * 9000);
    const newAppointment = {
      id: appointments.length > 0 ? Math.max(...appointments.map(a => a.id)) + 1 : 1,
      appointment_ref: ref,
      patient_name: name,
      patient_email: email,
      patient_phone: phone,
      service_id: serviceId || 1,
      service_name: postData.service_name || postData.service || 'General Dentistry',
      dentist_id: dentistId,
      dentist_name: currentDoc.name,
      appointment_date: date,
      appointment_time: time,
      message: message,
      status: 'Pending', // New requests start as Pending so admin can review and Accept!
      created_at: new Date().toISOString()
    };

    appointments.push(newAppointment);
    writeJSON(APPOINTMENTS_FILE, appointments);

    // If legacy AJAX from booking.html, return 'sent'
    if (req.headers['x-requested-with'] === 'XMLHttpRequest' && pathname === '/booking.php') {
      res.writeHead(200, { 'Content-Type': 'text/plain' });
      res.end('sent');
      return;
    }

    return sendJSON(res, {
      success: true,
      message: 'Your appointment request has been submitted!',
      ref: ref,
      details: {
        name: name,
        email: email,
        service_name: newAppointment.service_name,
        dentist_name: currentDoc.name,
        date: date,
        time: time
      }
    });
  }

  // 12. Static File Resolution
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
  console.log(`Patient Login: http://localhost:${PORT}/login.html`);
  console.log(`====================================================`);
});
