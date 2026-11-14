/**
 * MEDINOVA - Multi-Speciality Hospital Management Platform Server
 * Full REST API Support for:
 * - 12 Clinical Departments
 * - Multi-Speciality Doctor Roster & Schedules
 * - Interactive Cascading Appointment Booking with Concurrency Control
 * - Multi-Role Portals (Admin, Doctor, Patient, Reception, Nurse, Lab, Pharmacy, Billing)
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
const DEPARTMENTS_FILE = path.join(DATA_DIR, 'departments.json');
const MESSAGES_FILE = path.join(DATA_DIR, 'messages.json');

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
  '09:00 AM', '09:30 AM', '10:00 AM', '10:30 AM', 
  '11:00 AM', '11:30 AM', '01:30 PM', '02:00 PM', 
  '02:30 PM', '03:00 PM', '03:30 PM', '04:00 PM'
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

  // Friendly Route Aliases & Module Mappings
  if (pathname === '/' || pathname === '/index.php') pathname = '/index.html';
  if (pathname === '/about' || pathname === '/about.php') pathname = '/about.html';
  if (pathname === '/departments' || pathname === '/departments.php') pathname = '/departments.html';
  if (pathname === '/doctors' || pathname === '/doctors.php' || pathname === '/dentists' || pathname === '/dentists.php') pathname = '/dentists.html';
  if (pathname === '/facilities' || pathname === '/facilities.php' || pathname === '/services' || pathname === '/services.php') pathname = '/services.html';
  if (pathname === '/contact' || pathname === '/contact.php') pathname = '/contact.html';
  if (pathname === '/login' || pathname === '/login.php') pathname = '/login.html';
  if (pathname === '/register' || pathname === '/register.php') pathname = '/register.html';
  if (pathname === '/booking' || pathname === '/booking.php') pathname = '/booking.html';
  if (pathname === '/logout' || pathname === '/logout.php') {
    res.writeHead(302, { 'Location': '/login.html' });
    return res.end();
  }

  // Module Portals Aliases
  if (pathname === '/admin' || pathname === '/admin/' || pathname === '/admin/index.php') pathname = '/admin/index.html';
  if (pathname === '/doctor' || pathname === '/doctor/' || pathname === '/doctor/index.php') pathname = '/doctor/index.html';
  if (pathname === '/patient' || pathname === '/patient/' || pathname === '/patient/dashboard.php' || pathname === '/patient/index.php' || pathname === '/dashboard') pathname = '/patient/dashboard.html';
  if (pathname === '/receptionist' || pathname === '/receptionist/' || pathname === '/receptionist/index.php') pathname = '/receptionist/index.html';
  if (pathname === '/nurse' || pathname === '/nurse/' || pathname === '/nurse/index.php') pathname = '/nurse/index.html';
  if (pathname === '/laboratory' || pathname === '/laboratory/' || pathname === '/laboratory/index.php') pathname = '/laboratory/index.html';
  if (pathname === '/pharmacy' || pathname === '/pharmacy/' || pathname === '/pharmacy/index.php') pathname = '/pharmacy/index.html';
  if (pathname === '/billing' || pathname === '/billing/' || pathname === '/billing/index.php') pathname = '/billing/index.html';

  // Department Details Mapping
  if (pathname === '/department-details.php') {
    const slug = parsedUrl.query.slug || 'cardiology';
    pathname = `/departments/${slug}.html`;
  }
  if (pathname.startsWith('/departments/') && pathname.endsWith('.php')) {
    pathname = pathname.replace('.php', '.html');
  }

  // ----------------------------------------------------
  // REST API ENDPOINTS
  // ----------------------------------------------------

  // 1. GET /api/departments
  if (pathname === '/api/departments' && req.method === 'GET') {
    const list = readJSON(DEPARTMENTS_FILE, []);
    return sendJSON(res, list);
  }

  // 2. GET /api/get-doctors-by-dept.php
  if (pathname === '/api/get-doctors-by-dept.php' && req.method === 'GET') {
    const deptId = parseInt(parsedUrl.query.department_id || '0', 10);
    const allDocs = readJSON(DENTISTS_FILE, []);
    const docs = deptId > 0 ? allDocs.filter(d => d.department_id === deptId) : allDocs;
    return sendJSON(res, { success: true, department_id: deptId, doctors: docs.length > 0 ? docs : allDocs });
  }

  // 3. GET /api/appointments
  if (pathname === '/api/appointments' && req.method === 'GET') {
    const list = readJSON(APPOINTMENTS_FILE, []);
    return sendJSON(res, list);
  }

  // 4. POST /api/appointments/status or /api/update-appointment-status.php
  if ((pathname === '/api/appointments/status' || pathname === '/api/update-appointment-status.php') && req.method === 'POST') {
    const data = await parsePostData(req);
    const appId = parseInt(data.id || '0', 10);
    const ref = data.appointment_ref || '';
    const newStatus = data.status;

    let appointments = readJSON(APPOINTMENTS_FILE, []);
    let idx = -1;
    if (appId > 0) {
      idx = appointments.findIndex(a => a.id === appId);
    } else if (ref) {
      idx = appointments.findIndex(a => a.appointment_ref === ref);
    }

    if (idx !== -1 && newStatus) {
      appointments[idx].status = newStatus;
      appointments[idx].updated_at = new Date().toISOString();
      writeJSON(APPOINTMENTS_FILE, appointments);
      return sendJSON(res, { success: true, message: `Appointment marked as ${newStatus}` });
    }
    return sendJSON(res, { success: false, message: 'Appointment record not found' }, 404);
  }

  // 5. GET /api/dentists or /api/doctors (Specialists list)
  if ((pathname === '/api/dentists' || pathname === '/api/doctors') && req.method === 'GET') {
    const dentists = readJSON(DENTISTS_FILE, []);
    return sendJSON(res, dentists);
  }

  // 6. POST /api/dentists (Add new specialist)
  if (pathname === '/api/dentists' && req.method === 'POST') {
    const data = await parsePostData(req);
    if (!data.name || !data.specialization) {
      return sendJSON(res, { success: false, message: 'Doctor name and specialization are required' }, 400);
    }

    let dentists = readJSON(DENTISTS_FILE, []);
    const newDoctor = {
      id: dentists.length > 0 ? Math.max(...dentists.map(d => d.id)) + 1 : 1,
      name: data.name,
      department_id: parseInt(data.department_id || '1', 10),
      department_name: data.department_name || 'Cardiology',
      doctor_code: 'MED-DOC-' + String(dentists.length + 1).padStart(3, '0'),
      qualification: data.qualification || 'MD, Harvard Medical School',
      specialization: data.specialization,
      experience: data.experience || '10+ Years Experience',
      fee: parseFloat(data.fee || 150),
      consultation_fee: parseFloat(data.fee || 150),
      email: data.email || 'doctor@medinova.com',
      phone: data.phone || '+1 (800) 555-0100',
      availability_days: data.availability_days || 'Monday - Friday',
      availability_hours: data.availability_hours || '09:00 AM - 05:00 PM',
      description: data.description || 'Experienced medical faculty dedicated to precision patient outcomes.',
      avatar: 'images/team/' + ((dentists.length % 4) + 1) + '.webp',
      is_available: 1
    };

    dentists.push(newDoctor);
    writeJSON(DENTISTS_FILE, dentists);
    return sendJSON(res, { success: true, message: 'Specialist doctor added successfully', doctor: newDoctor });
  }

  // 7. GET /api/check-slots.php
  if (pathname === '/api/check-slots.php') {
    const docId = parseInt(parsedUrl.query.doctor_id || parsedUrl.query.dentist_id || '1', 10);
    const date = parsedUrl.query.date || '';

    const appointments = readJSON(APPOINTMENTS_FILE, []);
    const booked = appointments
      .filter(a => (a.doctor_id === docId || a.dentist_id === docId) && a.appointment_date === date && a.status !== 'Cancelled')
      .map(a => a.appointment_time);

    return sendJSON(res, {
      success: true,
      doctor_id: docId,
      date: date,
      slots: STANDARD_SLOTS,
      booked_slots: booked,
      available_slots: STANDARD_SLOTS.filter(s => !booked.includes(s))
    });
  }

  // 8. POST /api/book-appointment.php
  if (pathname === '/api/book-appointment.php' && req.method === 'POST') {
    const postData = await parsePostData(req);

    const name = (postData.patient_name || postData.name || '').trim();
    const email = (postData.patient_email || postData.email || '').trim();
    const phone = (postData.patient_phone || postData.phone || '').trim();
    let deptId = parseInt(postData.department_id || '1', 10);
    let doctorId = parseInt(postData.doctor_id || postData.dentist_id || '1', 10);
    const date = (postData.appointment_date || postData.date || '').trim();
    const time = (postData.appointment_time || postData.time || '').trim();
    const type = (postData.appointment_type || 'General Consultation').trim();
    const symptoms = (postData.symptoms || postData.message || '').trim();

    const doctors = readJSON(DENTISTS_FILE, []);
    const currentDoc = doctors.find(d => d.id === doctorId) || doctors[0] || { id: 1, name: 'Dr. Marcus Vance' };
    doctorId = currentDoc.id;

    if (!name || !email || !date || !time) {
      return sendJSON(res, { success: false, message: 'Please provide patient name, email, date, and select a time slot.' });
    }

    let appointments = readJSON(APPOINTMENTS_FILE, []);
    const isBooked = appointments.some(a => 
      (a.doctor_id === doctorId || a.dentist_id === doctorId) && a.appointment_date === date && a.appointment_time === time && a.status !== 'Cancelled'
    );

    if (isBooked) {
      return sendJSON(res, {
        success: false,
        message: `The time slot ${time} on ${date} is already booked for ${currentDoc.name}. Please select another slot.`
      });
    }

    const ref = 'MED-' + new Date().getFullYear() + '-' + Math.floor(1000 + Math.random() * 9000);
    const newAppointment = {
      id: appointments.length > 0 ? Math.max(...appointments.map(a => a.id)) + 1 : 1,
      appointment_ref: ref,
      patient_name: name,
      patient_email: email,
      patient_phone: phone,
      department_id: deptId,
      dept_name: currentDoc.department_name || 'Cardiology',
      doctor_id: doctorId,
      doctor_name: currentDoc.name,
      appointment_date: date,
      appointment_time: time,
      appointment_type: type,
      symptoms: symptoms,
      status: 'Pending',
      created_at: new Date().toISOString()
    };

    appointments.push(newAppointment);
    writeJSON(APPOINTMENTS_FILE, appointments);

    return sendJSON(res, {
      success: true,
      message: 'Your hospital appointment has been reserved!',
      appointment_ref: ref,
      doctor_name: currentDoc.name,
      department_name: newAppointment.dept_name,
      appointment_date: date,
      appointment_time: time,
      status: 'Pending'
    });
  }

  // Static File Resolution
  let filePath = path.join(WEB_ROOT, pathname);

  // Automatic Static Asset Resolution from any subdirectory (doctor/css/..., nurse/css/..., etc.)
  const assetMatch = pathname.match(/\/(css|fonts|images|js)\/(.*)$/);
  if (assetMatch) {
    const assetDir = assetMatch[1];
    const assetFile = assetMatch[2];
    const directAssetPath = path.join(WEB_ROOT, assetDir, assetFile);
    if (fs.existsSync(directAssetPath)) {
      filePath = directAssetPath;
    }
  }

  if (!fs.existsSync(filePath)) {
    if (fs.existsSync(filePath + '.html')) filePath += '.html';
  }

  fs.stat(filePath, (err, stats) => {
    if (err || !stats.isFile()) {
      res.writeHead(404, { 'Content-Type': 'text/html; charset=UTF-8' });
      res.end(`
        <!DOCTYPE html>
        <html>
        <head><title>404 Not Found - MEDINOVA Hospital</title>
        <link rel="stylesheet" href="/css/bootstrap.min.css">
        </head>
        <body class="bg-light p-5 text-center">
          <div class="container py-5">
            <h1 class="display-3 text-primary fw-bold">404</h1>
            <h3 class="mb-3">Page Not Found</h3>
            <p class="text-muted mb-4">The requested page <code>${pathname}</code> was not found.</p>
            <a href="/" class="btn btn-primary rounded-pill px-4">Return to Hospital Home</a>
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
  console.log(`MEDINOVA Multi-Speciality Hospital Platform Running`);
  console.log(`Local URL: http://localhost:${PORT}`);
  console.log(`Admin Portal: http://localhost:${PORT}/admin/index.html`);
  console.log(`Patient Login: http://localhost:${PORT}/login.html`);
  console.log(`Booking Engine: http://localhost:${PORT}/booking.html`);
  console.log(`====================================================`);
});
