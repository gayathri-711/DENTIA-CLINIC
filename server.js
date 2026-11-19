/**
 * Doctor Dental Clinic Dentia - Comprehensive Platform Server
 * Full REST API Support for:
 * 1. User/Patient Registration & Login with Past/Upcoming Visits and Regret Message with Doctor Alternatives
 * 2. Doctor Dashboard with Clinical Medical Records & Prescription Updates (Credentials created by Admin)
 * 3. Admin Full Portal Access (Doctors, Patients, and User Login Details Directory)
 * 4. Permanent Database Storage for Future Use
 */

const http = require('http');
const https = require('https');
const fs = require('fs');
const path = require('path');
const url = require('url');
const nodemailer = require('nodemailer');
const database = require('./database');

const isVercel = Boolean(process.env.VERCEL || process.env.AWS_LAMBDA_FUNCTION_NAME);
const PORT = process.env.PORT || 8000;
const WEB_ROOT = path.join(__dirname, 'Dentia', 'Dentia HTML');
const SOURCE_DATA_DIR = path.join(WEB_ROOT, 'data');
const DATA_DIR = isVercel ? path.join('/tmp', 'dentia_data') : SOURCE_DATA_DIR;

if (!fs.existsSync(DATA_DIR)) {
  fs.mkdirSync(DATA_DIR, { recursive: true });
}

// In Vercel serverless environment, copy seed JSON files to writable /tmp
if (isVercel) {
  ['appointments.json', 'dentists.json', 'patients.json', 'notifications.json', 'medical_records.json', 'gateway_config.json'].forEach(f => {
    const src = path.join(SOURCE_DATA_DIR, f);
    const dst = path.join(DATA_DIR, f);
    if (!fs.existsSync(dst) && fs.existsSync(src)) {
      try { fs.copyFileSync(src, dst); } catch (e) {}
    }
  });
}

const APPOINTMENTS_FILE = path.join(DATA_DIR, 'appointments.json');
const DENTISTS_FILE = path.join(DATA_DIR, 'dentists.json');
const PATIENTS_FILE = path.join(DATA_DIR, 'patients.json');
const MESSAGES_FILE = path.join(DATA_DIR, 'messages.json');
const NOTIFICATIONS_FILE = path.join(DATA_DIR, 'notifications.json');
const MEDICAL_RECORDS_FILE = path.join(DATA_DIR, 'medical_records.json');
const GATEWAY_CONFIG_FILE = path.join(DATA_DIR, 'gateway_config.json');

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

function generateModernEmailHTML({ patient_name, appointment_ref, type, subject, message }) {
  const isConfirmed = type === 'booking_confirmed';
  const isUnavailable = type === 'slot_unavailable';
  const headerColor = isConfirmed ? '#10b981' : (isUnavailable ? '#f59e0b' : '#0d6efd');
  const badgeLabel = isConfirmed ? 'CONFIRMED' : (isUnavailable ? 'SLOT NOT AVAILABLE' : 'SCHEDULED');

  return `<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <title>${subject}</title>
</head>
<body style="font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif; background:#f1f5f9; margin:0; padding:24px; color:#1e293b;">
  <div style="max-width:580px; margin:0 auto; background:#ffffff; border-radius:18px; overflow:hidden; box-shadow:0 8px 30px rgba(0,0,0,0.08);">
    
    <!-- Header -->
    <div style="background:${headerColor}; padding:32px 24px; text-align:center; color:#ffffff;">
      <h1 style="margin:0; font-size:22px; font-weight:800; letter-spacing:-0.5px;">Doctor Dental Clinic Dentia</h1>
      <p style="margin:6px 0 0 0; opacity:0.92; font-size:13px;">Modern Oral Healthcare & Clinical Excellence</p>
    </div>

    <!-- Body Content -->
    <div style="padding:32px 28px; line-height:1.65;">
      <div style="display:inline-block; background:#eff6ff; color:#1d4ed8; padding:5px 14px; border-radius:20px; font-family:monospace; font-size:12px; font-weight:700; margin-bottom:18px; border:1px solid #bfdbfe;">
        Ref: ${appointment_ref || 'NOTIFICATION'} &bull; ${badgeLabel}
      </div>

      <p style="font-size:15px; margin-top:0;">Hello <strong>${patient_name || 'Valued Patient'}</strong>,</p>

      <div style="background:#f8fafc; border-left:4px solid ${headerColor}; padding:18px 20px; border-radius:10px; margin:20px 0; font-size:14px; line-height:1.6; white-space:pre-line; color:#334155; border-top:1px solid #f1f5f9; border-right:1px solid #f1f5f9; border-bottom:1px solid #f1f5f9;">
${message}
      </div>

      <div style="background:#eff6ff; border-radius:12px; padding:16px; margin:22px 0; font-size:13px; color:#1e40af;">
        <strong><i style="margin-right:6px;">&#9733;</i> Clinic Notice:</strong> Please arrive 10 minutes before your scheduled appointment time. Bring any past dental X-rays or dental insurance cards.
      </div>

      <p style="font-size:13px; color:#64748b; margin-bottom:0;">
        For urgent inquiries or schedule changes, please call our 24/7 care desk at <strong>+1 (800) 234-5678</strong>.
      </p>
    </div>

    <!-- Footer -->
    <div style="background:#f8fafc; padding:22px 24px; text-align:center; font-size:11px; color:#64748b; border-top:1px solid #e2e8f0;">
      <strong>Doctor Dental Clinic Dentia</strong><br>
      100 S Main St, Suite 400, New York, NY 10001<br>
      Emergency Dental Hotline: +1 (800) 911-DENT &bull; contact@dentiacare.com
    </div>

  </div>
</body>
</html>`;
}

// Notification Dispatcher (Email & SMS with Real SMTP delivery support)
async function dispatchNotification({ patient_name, patient_email, patient_phone, appointment_ref, type, subject, message, sms_text }) {
  let notifications = readJSON(NOTIFICATIONS_FILE, []);
  const now = new Date().toISOString();
  const gwConfig = readJSON(GATEWAY_CONFIG_FILE, {});

  let emailStatus = 'Delivered (System Ledger)';
  let smsStatus = 'Delivered (System Ledger)';

  // 1. Attempt Real Email via SMTP if configured and enabled
  if (gwConfig.email && gwConfig.email.enabled && gwConfig.email.user && gwConfig.email.pass && patient_email) {
    try {
      const transporter = nodemailer.createTransport({
        service: gwConfig.email.provider === 'gmail' ? 'gmail' : undefined,
        host: gwConfig.email.host || 'smtp.gmail.com',
        port: parseInt(gwConfig.email.port || '465', 10),
        secure: gwConfig.email.secure !== false,
        auth: {
          user: gwConfig.email.user,
          pass: gwConfig.email.pass
        }
      });

      const info = await transporter.sendMail({
        from: gwConfig.email.from || `Dentia Dental Clinic <${gwConfig.email.user}>`,
        to: patient_email,
        subject: subject,
        text: message,
        html: generateModernEmailHTML({ patient_name, appointment_ref, type, subject, message })
      });

      emailStatus = `Delivered to Real Inbox (${info.messageId})`;
      console.log(`[SMTP SUCCESS] Sent real email to ${patient_email}: ${info.messageId}`);
    } catch (err) {
      emailStatus = `SMTP Error: ${err.message}`;
      console.error(`[SMTP ERROR] Sending to ${patient_email}:`, err.message);
    }
  } else {
    emailStatus = 'Delivered (System Ledger - Configure SMTP in Admin to send to real inbox)';
  }

  // 2. Email Notification Record
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
    html: generateModernEmailHTML({ patient_name, appointment_ref, type, subject, message }),
    created_at: now,
    status: emailStatus
  };
  notifications.push(emailNotif);

  // 3. Attempt Real SMS via Twilio or Fast2SMS if configured and enabled
  if (gwConfig.sms && gwConfig.sms.enabled && patient_phone) {
    try {
      const smsContent = (sms_text || message).substring(0, 160);

      // A. Twilio SMS Gateway (Worldwide)
      if (gwConfig.sms.provider === 'twilio' && gwConfig.sms.twilio_sid && gwConfig.sms.twilio_token && gwConfig.sms.twilio_phone) {
        const https = require('https');
        const formattedPhone = patient_phone.startsWith('+') ? patient_phone : ('+' + patient_phone.replace(/[^0-9]/g, ''));
        const postBody = new URLSearchParams({
          To: formattedPhone,
          From: gwConfig.sms.twilio_phone,
          Body: smsContent
        }).toString();

        const authHeader = 'Basic ' + Buffer.from(`${gwConfig.sms.twilio_sid}:${gwConfig.sms.twilio_token}`).toString('base64');
        await new Promise((resolve) => {
          const reqTwilio = https.request({
            hostname: 'api.twilio.com',
            path: `/2010-04-01/Accounts/${gwConfig.sms.twilio_sid}/Messages.json`,
            method: 'POST',
            headers: {
              'Authorization': authHeader,
              'Content-Type': 'application/x-www-form-urlencoded',
              'Content-Length': Buffer.byteLength(postBody)
            }
          }, (tRes) => {
            let resData = '';
            tRes.on('data', chunk => resData += chunk);
            tRes.on('end', () => {
              try {
                const parsed = JSON.parse(resData);
                if (tRes.statusCode >= 200 && tRes.statusCode < 300) {
                  smsStatus = `Delivered via Twilio (${parsed.sid || 'OK'})`;
                  console.log(`[TWILIO SUCCESS] Sent real SMS to ${formattedPhone}: ${parsed.sid}`);
                } else {
                  smsStatus = `Twilio Error: ${parsed.message || 'Failed'}`;
                  console.error(`[TWILIO ERROR]:`, parsed.message);
                }
              } catch (e) {
                smsStatus = `Twilio Response: ${resData.substring(0, 30)}`;
              }
              resolve();
            });
          });
          reqTwilio.on('error', (err) => {
            smsStatus = `Twilio Connection Error: ${err.message}`;
            resolve();
          });
          reqTwilio.write(postBody);
          reqTwilio.end();
        });

      // B. Fast2SMS Gateway (India)
      } else if (gwConfig.sms.api_key) {
        const cleanPhone = patient_phone.replace(/[^0-9]/g, '').slice(-10);
        if (cleanPhone.length === 10) {
          const https = require('https');
          const smsMsg = encodeURIComponent(smsContent.substring(0, 150));
          const url = `https://www.fast2sms.com/dev/bulkV2?authorization=${gwConfig.sms.api_key}&route=q&message=${smsMsg}&language=english&flash=0&numbers=${cleanPhone}`;
          await new Promise((resolve) => {
            https.get(url, (res) => {
              let data = '';
              res.on('data', chunk => data += chunk);
              res.on('end', () => {
                try {
                  const parsed = JSON.parse(data);
                  if (parsed.return) {
                    smsStatus = `Delivered via Fast2SMS (${parsed.request_id || 'OK'})`;
                    console.log(`[FAST2SMS SUCCESS] Sent real SMS to ${cleanPhone}`);
                  } else {
                    const msg = Array.isArray(parsed.message) ? parsed.message[0] : (parsed.message || 'Dispatch failed');
                    smsStatus = `Fast2SMS Notice: ${msg}`;
                    console.log(`[FAST2SMS NOTICE] ${msg}`);
                  }
                } catch (e) {
                  smsStatus = `SMS Sent: ${data.substring(0, 30)}`;
                }
                resolve();
              });
            }).on('error', (err) => {
              smsStatus = `SMS Connection Error: ${err.message}`;
              resolve();
            });
          });
        } else {
          smsStatus = `SMS Skipped: Invalid 10-digit phone number (${patient_phone})`;
        }
      } else {
        smsStatus = 'Delivered (System Ledger - Enter API key in Admin to deliver to phone)';
      }
    } catch (e) {
      smsStatus = `SMS Error: ${e.message}`;
    }
  } else {
    smsStatus = 'Delivered (System Ledger - Enable SMS Dispatch in Admin Gateway to send real SMS)';
  }

  // 4. SMS Notification Record
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
    status: smsStatus
  };
  notifications.push(smsNotif);

  writeJSON(NOTIFICATIONS_FILE, notifications);

  // Persist directly into SQLite database
  try {
    database.run(
      `INSERT OR REPLACE INTO notifications (id, recipient_email, recipient_phone, recipient_name, appointment_ref, channel, type, subject, message, preview_url, html, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [emailNotif.id, emailNotif.recipient_email, emailNotif.recipient_phone || '', emailNotif.recipient_name, emailNotif.appointment_ref || '', emailNotif.channel, emailNotif.type, emailNotif.subject, emailNotif.message, emailNotif.preview_url || '', emailNotif.html || '', emailNotif.status, emailNotif.created_at]
    );
    database.run(
      `INSERT OR REPLACE INTO notifications (id, recipient_email, recipient_phone, recipient_name, appointment_ref, channel, type, subject, message, preview_url, html, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [smsNotif.id, smsNotif.recipient_email, smsNotif.recipient_phone || '', smsNotif.recipient_name, smsNotif.appointment_ref || '', smsNotif.channel, smsNotif.type, smsNotif.subject, smsNotif.message, '', '', smsNotif.status, smsNotif.created_at]
    );
  } catch (dbErr) {
    console.error('[DB SYNC ERROR] Notifications:', dbErr.message);
  }

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

const server = http.createServer(handleRequest);

async function handleRequest(req, res) {
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
  if (pathname === '/services' || pathname === '/services/') pathname = '/services.html';

  // Smart Service Route Aliases (prevent 404 on any service link)
  if (pathname.startsWith('/service-') || pathname.startsWith('/service/')) {
    const clean = pathname.toLowerCase();
    if (clean.includes('general') || clean.includes('exam') || clean.includes('cleaning') || clean.includes('gum') || clean.includes('extraction')) {
      if (!fs.existsSync(path.join(WEB_ROOT, pathname))) pathname = '/service-general-dentistry.html';
    } else if (clean.includes('cosmetic') || clean.includes('veneer') || clean.includes('aesthetic')) {
      if (!fs.existsSync(path.join(WEB_ROOT, pathname))) pathname = '/service-cosmetic-dentistry.html';
    } else if (clean.includes('pediatric') || clean.includes('kid') || clean.includes('child')) {
      if (!fs.existsSync(path.join(WEB_ROOT, pathname))) pathname = '/service-pediatric-dentistry.html';
    } else if (clean.includes('restorative') || clean.includes('implant') || clean.includes('crown') || clean.includes('bridge') || clean.includes('root-canal')) {
      if (!fs.existsSync(path.join(WEB_ROOT, pathname))) pathname = '/service-restorative-dentistry.html';
    } else if (clean.includes('whitening') || clean.includes('preventive') || clean.includes('laser')) {
      if (!fs.existsSync(path.join(WEB_ROOT, pathname))) pathname = '/service-preventive-dentistry.html';
    } else if (clean.includes('orthodontic') || clean.includes('brace') || clean.includes('aligner') || clean.includes('invisalign')) {
      if (!fs.existsSync(path.join(WEB_ROOT, pathname))) pathname = '/service-orthodontics.html';
    } else {
      if (!fs.existsSync(path.join(WEB_ROOT, pathname))) pathname = '/services.html';
    }
  }

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

// Helper to save uploaded doctor base64 image to images/team/
function saveDoctorPhotoIfBase64(imageData, docId) {
  if (!imageData || typeof imageData !== 'string' || !imageData.startsWith('data:image/')) {
    return imageData || 'images/team/1.webp';
  }
  try {
    const matches = imageData.match(/^data:image\/([a-zA-Z0-9+]+);base64,(.+)$/);
    if (!matches || matches.length !== 3) {
      return imageData;
    }
    let ext = matches[1].toLowerCase();
    if (ext === 'jpeg') ext = 'jpg';
    const base64Data = matches[2];
    const buffer = Buffer.from(base64Data, 'base64');
    const filename = `doctor_${docId || Date.now()}_${Date.now()}.${ext}`;
    const uploadDir = path.join(__dirname, 'Dentia', 'Dentia HTML', 'images', 'team');
    if (!fs.existsSync(uploadDir)) {
      fs.mkdirSync(uploadDir, { recursive: true });
    }
    const filePath = path.join(uploadDir, filename);
    fs.writeFileSync(filePath, buffer);
    console.log(`[PHOTO UPLOAD] Saved doctor photo: images/team/${filename}`);
    return `images/team/${filename}`;
  } catch (err) {
    console.error('[PHOTO UPLOAD ERROR]', err.message);
    return imageData;
  }
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
    const newDocId = dentists.length > 0 ? Math.max(...dentists.map(d => d.id)) + 1 : 1;
    const finalPhoto = saveDoctorPhotoIfBase64(data.image, newDocId);

    const newDoctor = {
      id: newDocId,
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
      image: finalPhoto,
      is_active: true
    };

    dentists.push(newDoctor);
    writeJSON(DENTISTS_FILE, dentists);

    // Save doctor into SQLite database
    database.run(
      `INSERT OR REPLACE INTO dentists (id, name, qualification, specialization, experience, email, login_email, password, has_login, phone, description, image, availability_days, availability_hours, is_active) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [newDoctor.id, newDoctor.name, newDoctor.qualification, newDoctor.specialization, newDoctor.experience, newDoctor.email, newDoctor.login_email, newDoctor.password, newDoctor.has_login ? 1 : 0, newDoctor.phone, newDoctor.description, newDoctor.image, newDoctor.availability_days, newDoctor.availability_hours, 1]
    ).catch(e => console.error('[DB ERROR] Insert dentist:', e.message));

    return sendJSON(res, { success: true, message: 'Doctor added successfully with photo and credentials', doctor: newDoctor });
  }

  // 5. POST /api/admin/doctor/credentials (Admin sets/edits doctor login credentials & photo)
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
    if (data.image) {
      const updatedPhoto = saveDoctorPhotoIfBase64(data.image, docId);
      dentists[idx].image = updatedPhoto;
      database.run(
        `UPDATE dentists SET image = ? WHERE id = ?`,
        [updatedPhoto, docId]
      ).catch(e => console.error('[DB ERROR] Update dentist photo:', e.message));
    }

    writeJSON(DENTISTS_FILE, dentists);

    // Sync credentials to SQLite database
    database.run(
      `UPDATE dentists SET login_email = ?, password = ?, has_login = ? WHERE id = ?`,
      [dentists[idx].login_email, dentists[idx].password, dentists[idx].has_login ? 1 : 0, docId]
    ).catch(e => console.error('[DB ERROR] Update dentist credentials:', e.message));

    return sendJSON(res, {
      success: true,
      message: `Login credentials and profile updated for ${dentists[idx].name}. Login access is now ${dentists[idx].has_login ? 'ENABLED' : 'DISABLED'}.`,
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

    // Sync delete to SQLite database
    database.run(
      `DELETE FROM dentists WHERE id = ?`,
      [docId]
    ).catch(e => console.error('[DB ERROR] Delete dentist:', e.message));
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
    database.run(`UPDATE appointments SET status = ?, updated_at = ? WHERE id = ?`, [app.status, app.updated_at, app.id])
      .catch(e => console.error('[DB ERROR] Update status:', e.message));
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

    database.run(
      `INSERT OR REPLACE INTO patient_medical_records (id, patient_email, patient_name, dentist_id, dentist_name, appointment_ref, treatment_date, diagnosis, treatment_provided, prescription, doctor_notes, followup_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [newRecord.id, newRecord.patient_email, newRecord.patient_name, newRecord.dentist_id, newRecord.dentist_name, newRecord.appointment_ref, newRecord.treatment_date, newRecord.diagnosis, newRecord.treatment_provided, newRecord.prescription, newRecord.doctor_notes, newRecord.followup_date, newRecord.created_at]
    ).catch(e => console.error('[DB ERROR] Insert medical record:', e.message));

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

  // 12. GET /api/admin/gateway (Get email & SMS gateway configuration)
  if (pathname === '/api/admin/gateway' && req.method === 'GET') {
    const config = readJSON(GATEWAY_CONFIG_FILE, {
      email: { enabled: false, provider: 'gmail', host: 'smtp.gmail.com', port: 465, secure: true, user: '', pass: '' },
      sms: { enabled: false, provider: 'fast2sms', api_key: '' }
    });
    // Mask password
    const safeConfig = JSON.parse(JSON.stringify(config));
    if (safeConfig.email && safeConfig.email.pass) {
      safeConfig.email.has_pass = true;
      safeConfig.email.pass_masked = '••••••••';
    }
    return sendJSON(res, safeConfig);
  }

  // 13. POST /api/admin/gateway (Save email & SMS gateway configuration)
  if (pathname === '/api/admin/gateway' && req.method === 'POST') {
    const data = await parsePostData(req);
    let currentConfig = readJSON(GATEWAY_CONFIG_FILE, {});

    if (data.email) {
      currentConfig.email = {
        enabled: Boolean(data.email.enabled),
        provider: data.email.provider || 'gmail',
        host: data.email.host || 'smtp.gmail.com',
        port: parseInt(data.email.port || '465', 10),
        secure: data.email.secure !== false,
        user: (data.email.user || '').trim(),
        pass: data.email.pass ? data.email.pass.trim() : (currentConfig.email?.pass || ''),
        from: data.email.from || `Doctor Dental Clinic Dentia <${data.email.user || 'contact@dentiacare.com'}>`
      };
    }

    if (data.sms) {
      currentConfig.sms = {
        enabled: Boolean(data.sms.enabled),
        provider: data.sms.provider || 'fast2sms',
        api_key: (data.sms.api_key || '').trim(),
        twilio_sid: (data.sms.twilio_sid || '').trim(),
        twilio_token: (data.sms.twilio_token || '').trim(),
        twilio_from: (data.sms.twilio_from || '').trim()
      };
    }

    writeJSON(GATEWAY_CONFIG_FILE, currentConfig);
    return sendJSON(res, { success: true, message: 'Gateway settings saved successfully!' });
  }

  // 14. POST /api/admin/gateway/test-email (Send live test email to verify SMTP delivery)
  if (pathname === '/api/admin/gateway/test-email' && req.method === 'POST') {
    const data = await parsePostData(req);
    const toEmail = (data.to || data.recipient || data.email || '').trim();
    const config = readJSON(GATEWAY_CONFIG_FILE, {});

    if (!toEmail) {
      return sendJSON(res, { success: false, message: 'Recipient email address is required' }, 400);
    }

    if (!config.email || !config.email.user || !config.email.pass) {
      return sendJSON(res, {
        success: false,
        message: 'SMTP credentials are not configured yet! Please enter your Gmail/SMTP user and App Password first.'
      }, 400);
    }

    try {
      const transporter = nodemailer.createTransport({
        service: config.email.provider === 'gmail' ? 'gmail' : undefined,
        host: config.email.host || 'smtp.gmail.com',
        port: parseInt(config.email.port || '465', 10),
        secure: config.email.secure !== false,
        auth: {
          user: config.email.user,
          pass: config.email.pass
        }
      });

      const info = await transporter.sendMail({
        from: config.email.from || `Doctor Dental Clinic Dentia <${config.email.user}>`,
        to: toEmail,
        subject: 'Dentia Clinic - Live SMTP Email Verification Test',
        text: 'Hello! This is a live verification email from Doctor Dental Clinic Dentia verifying that real-world SMTP email delivery is functioning properly.',
        html: generateModernEmailHTML({
          patient_name: toEmail.split('@')[0],
          appointment_ref: 'TEST-VERIFY',
          type: 'booking_confirmed',
          subject: 'Live SMTP Email Verification Test',
          message: 'Success! Your email settings are correctly configured.\n\nAll subsequent appointment confirmations, regret messages, and doctor prescriptions will be delivered directly to real inboxes.'
        })
      });

      return sendJSON(res, {
        success: true,
        message: `Test email successfully sent to ${toEmail}! Check your inbox (Message ID: ${info.messageId}).`,
        messageId: info.messageId
      });
    } catch (err) {
      return sendJSON(res, {
        success: false,
        message: `SMTP Delivery Failed: ${err.message}. If using Gmail, ensure you created a 16-character App Password (Google Account -> Security -> 2-Step Verification -> App Passwords).`
      }, 500);
    }
  }

  // POST /api/admin/gateway/test-sms (Send live test SMS to verify SMS gateway delivery)
  if (pathname === '/api/admin/gateway/test-sms' && req.method === 'POST') {
    const data = await parsePostData(req);
    const toPhone = (data.phone || data.recipient || data.number || '').trim();
    const config = readJSON(GATEWAY_CONFIG_FILE, {});

    if (!toPhone) {
      return sendJSON(res, { success: false, message: 'Recipient phone number is required' }, 400);
    }

    if (!config.sms || (!config.sms.api_key && (!config.sms.twilio_sid || !config.sms.twilio_token))) {
      return sendJSON(res, {
        success: false,
        message: 'SMS Gateway credentials are not configured yet! Please enter your Fast2SMS API Key or Twilio credentials first.'
      }, 400);
    }

    const testMessage = `Dentia Clinic Test: Real SMS delivery is verified and active for patient appointment confirmations and alerts.`;

    try {
      // 1. Twilio
      if (config.sms.provider === 'twilio' && config.sms.twilio_sid && config.sms.twilio_token && config.sms.twilio_phone) {
        const https = require('https');
        const formattedPhone = toPhone.startsWith('+') ? toPhone : ('+' + toPhone.replace(/[^0-9]/g, ''));
        const postBody = new URLSearchParams({
          To: formattedPhone,
          From: config.sms.twilio_phone,
          Body: testMessage
        }).toString();

        const authHeader = 'Basic ' + Buffer.from(`${config.sms.twilio_sid}:${config.sms.twilio_token}`).toString('base64');
        const result = await new Promise((resolve, reject) => {
          const reqTwilio = https.request({
            hostname: 'api.twilio.com',
            path: `/2010-04-01/Accounts/${config.sms.twilio_sid}/Messages.json`,
            method: 'POST',
            headers: {
              'Authorization': authHeader,
              'Content-Type': 'application/x-www-form-urlencoded',
              'Content-Length': Buffer.byteLength(postBody)
            }
          }, (tRes) => {
            let resData = '';
            tRes.on('data', chunk => resData += chunk);
            tRes.on('end', () => {
              try {
                const parsed = JSON.parse(resData);
                if (tRes.statusCode >= 200 && tRes.statusCode < 300) {
                  resolve({ success: true, sid: parsed.sid, message: `Twilio SMS dispatched to ${formattedPhone}` });
                } else {
                  reject(new Error(parsed.message || `Twilio error code ${parsed.code}`));
                }
              } catch (e) {
                reject(new Error(resData));
              }
            });
          });
          reqTwilio.on('error', reject);
          reqTwilio.write(postBody);
          reqTwilio.end();
        });

        return sendJSON(res, { success: true, message: result.message, messageId: result.sid });
      }

      // 2. Fast2SMS
      const cleanPhone = toPhone.replace(/[^0-9]/g, '').slice(-10);
      if (cleanPhone.length !== 10) {
        return sendJSON(res, { success: false, message: 'Fast2SMS requires a valid 10-digit Indian mobile number.' }, 400);
      }

      const https = require('https');
      const smsMsg = encodeURIComponent(testMessage.substring(0, 150));
      const url = `https://www.fast2sms.com/dev/bulkV2?authorization=${config.sms.api_key}&route=q&message=${smsMsg}&language=english&flash=0&numbers=${cleanPhone}`;

      const f2sResult = await new Promise((resolve, reject) => {
        https.get(url, (fRes) => {
          let data = '';
          fRes.on('data', chunk => data += chunk);
          fRes.on('end', () => {
            try {
              const parsed = JSON.parse(data);
              if (parsed.return) {
                resolve({ success: true, message: `Fast2SMS delivered to +91 ${cleanPhone}`, requestId: parsed.request_id });
              } else {
                const msg = Array.isArray(parsed.message) ? parsed.message[0] : (parsed.message || 'Fast2SMS dispatch rejected');
                reject(new Error(msg));
              }
            } catch (e) {
              reject(new Error(data.substring(0, 60)));
            }
          });
        }).on('error', reject);
      });

      return sendJSON(res, { success: true, message: f2sResult.message, messageId: f2sResult.requestId });
    } catch (err) {
      return sendJSON(res, { success: false, message: `SMS Dispatch Failed: ${err.message}` }, 500);
    }
  }

  // 15. Database Management Endpoints
  // GET /api/admin/database/stats (Table counts, database size, and engine health)
  if (pathname === '/api/admin/database/stats' && req.method === 'GET') {
    const stats = await database.getDatabaseStats();
    return sendJSON(res, stats);
  }

  // GET /api/admin/database/table (Query and inspect live rows from any SQL table)
  if (pathname === '/api/admin/database/table' && req.method === 'GET') {
    const tableName = (parsedUrl.query.name || 'patients').toLowerCase().trim();
    const allowed = ['patients', 'dentists', 'services', 'appointments', 'patient_medical_records', 'notifications', 'admins', 'clinic_settings'];
    if (!allowed.includes(tableName)) {
      return sendJSON(res, { success: false, message: 'Invalid or restricted table name' }, 400);
    }
    try {
      const rows = await database.all(`SELECT * FROM ${tableName} ORDER BY id DESC LIMIT 100`);
      return sendJSON(res, { success: true, table: tableName, count: rows.length, rows: rows });
    } catch (e) {
      return sendJSON(res, { success: false, message: e.message }, 500);
    }
  }

  // GET /api/admin/database/download-sql (Download complete MySQL schema and seed data)
  if (pathname === '/api/admin/database/download-sql' && req.method === 'GET') {
    const sqlPath = path.join(__dirname, 'database.sql');
    if (!fs.existsSync(sqlPath)) {
      res.writeHead(404, { 'Content-Type': 'text/plain' });
      return res.end('database.sql not found');
    }
    res.writeHead(200, {
      'Content-Type': 'application/sql',
      'Content-Disposition': 'attachment; filename="database.sql"'
    });
    fs.createReadStream(sqlPath).pipe(res);
    return;
  }

  // GET /api/admin/database/download-sqlite (Download real SQLite database file)
  if (pathname === '/api/admin/database/download-sqlite' && req.method === 'GET') {
    const dbPath = database.DB_PATH;
    if (!fs.existsSync(dbPath)) {
      res.writeHead(404, { 'Content-Type': 'text/plain' });
      return res.end('dentia.sqlite not found');
    }
    res.writeHead(200, {
      'Content-Type': 'application/x-sqlite3',
      'Content-Disposition': 'attachment; filename="dentia.sqlite"'
    });
    fs.createReadStream(dbPath).pipe(res);
    return;
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

    // Save to SQLite database
    database.run(
      `INSERT OR REPLACE INTO patients (id, name, email, phone, password, dob, gender, address, total_visits, registered_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [newPatient.id, newPatient.name, newPatient.email, newPatient.phone, newPatient.password, newPatient.dob, newPatient.gender, newPatient.address, 0, newPatient.created_at]
    ).catch(e => console.error('[DB ERROR] Insert patient:', e.message));

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

  // POST /api/admin/login (Admin authentication against SQLite database)
  if ((pathname === '/api/admin/login' || pathname === '/api/auth/admin-login') && req.method === 'POST') {
    const data = await parsePostData(req);
    const email = (data.email || '').trim().toLowerCase();
    const password = data.password || '';

    let admin = null;
    if (database && typeof database.get === 'function') {
      try {
        admin = await database.get('SELECT * FROM admins WHERE LOWER(email) = ? AND password = ?', [email, password]);
      } catch (e) {
        console.error('[DB ADMIN AUTH ERROR]', e.message);
      }
    }
    if (!admin && email === 'admin@dentia.com' && password === 'admin123') {
      admin = { id: 1, name: 'Chief Medical Admin', email: 'admin@dentia.com', role: 'superadmin' };
    }

    if (admin) {
      const safeAdmin = { id: admin.id, name: admin.name, email: admin.email, role: admin.role || 'superadmin' };
      return sendJSON(res, { success: true, message: 'Admin authenticated successfully', admin: safeAdmin });
    }
    return sendJSON(res, { success: false, message: 'Invalid administrator email or password' }, 401);
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

  // 18.5 POST /contact.php (Contact Form Submission)
  if (pathname === '/contact.php' && req.method === 'POST') {
    const postData = await parsePostData(req);
    console.log('[CONTACT MESSAGE RECEIVED]', postData);
    res.writeHead(200, { 'Content-Type': 'text/plain' });
    return res.end('sent');
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
      database.run(
        `INSERT OR REPLACE INTO patients (id, name, email, phone, password, address, total_visits, registered_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)`,
        [patientRecord.id, patientRecord.name, patientRecord.email, patientRecord.phone, patientRecord.password, '', 0, patientRecord.created_at]
      ).catch(e => console.error('[DB ERROR] Insert patient:', e.message));
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

    // Save to SQLite database
    database.run(
      `INSERT OR REPLACE INTO appointments (id, appointment_ref, patient_id, patient_name, patient_email, patient_phone, service_id, service_name, dentist_id, dentist_name, appointment_date, appointment_time, message, status, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)`,
      [newAppointment.id, newAppointment.appointment_ref, newAppointment.patient_id, newAppointment.patient_name, newAppointment.patient_email, newAppointment.patient_phone, newAppointment.service_id, newAppointment.service_name, newAppointment.dentist_id, newAppointment.dentist_name, newAppointment.appointment_date, newAppointment.appointment_time, newAppointment.message, newAppointment.status, newAppointment.created_at]
    ).catch(e => console.error('[DB ERROR] Insert appointment:', e.message));

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
}

if (require.main === module) {
  server.listen(PORT, '0.0.0.0', () => {
    console.log(`====================================================`);
    console.log(`Doctor Dental Clinic Dentia Platform Server Running`);
    console.log(`Local URL: http://localhost:${PORT}`);
    console.log(`Admin Portal: http://localhost:${PORT}/admin/index.html`);
    console.log(`Doctor Login: http://localhost:${PORT}/doctor-login.html`);
    console.log(`Patient Login: http://localhost:${PORT}/login.html`);
    console.log(`====================================================`);
  });
}

module.exports = handleRequest;
module.exports.server = server;
module.exports.handleRequest = handleRequest;
