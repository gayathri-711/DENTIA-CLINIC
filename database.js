/**
 * Doctor Dental Clinic Dentia - Unified Database Engine
 * Relational SQLite database engine with automatic schema creation and JSON sync
 */

let sqlite3 = null;
try {
  sqlite3 = require('sqlite3').verbose();
} catch (e) {
  console.warn('[DATABASE WARNING] sqlite3 native module not available in current environment:', e.message);
}

const path = require('path');
const fs = require('fs');

const isVercel = Boolean(process.env.VERCEL || process.env.AWS_LAMBDA_FUNCTION_NAME);
const SOURCE_DIR = path.join(__dirname, 'Dentia', 'Dentia HTML', 'data');
const DATA_DIR = isVercel ? path.join('/tmp', 'dentia_data') : SOURCE_DIR;
const DB_PATH = isVercel ? path.join(DATA_DIR, 'dentia.sqlite') : path.join(DATA_DIR, 'dentia.sqlite');

// Ensure data directory exists
if (!fs.existsSync(DATA_DIR)) {
  try { fs.mkdirSync(DATA_DIR, { recursive: true }); } catch (e) {}
}

// In Vercel serverless environment, copy seed database to writable /tmp directory
if (isVercel && !fs.existsSync(DB_PATH)) {
  const sourceDB = path.join(SOURCE_DIR, 'dentia.sqlite');
  if (fs.existsSync(sourceDB)) {
    try {
      fs.copyFileSync(sourceDB, DB_PATH);
      console.log('[DATABASE VERCEL] Seed database copied to /tmp:', DB_PATH);
    } catch (copyErr) {
      console.error('[DATABASE VERCEL ERROR] Could not copy seed DB:', copyErr.message);
    }
  }
}

let db = null;
if (sqlite3) {
  try {
    db = new sqlite3.Database(DB_PATH, (err) => {
      if (err) {
        console.error('[DATABASE ERROR] Could not connect to SQLite database:', err.message);
      } else {
        console.log('[DATABASE SUCCESS] Connected to SQLite database:', DB_PATH);
      }
    });
  } catch (err) {
    console.error('[DATABASE INIT ERROR]:', err.message);
  }
}

// Helper for running queries with Promises (Safe & Non-blocking)
function run(sql, params = []) {
  if (!db) return Promise.resolve({ lastID: 0, changes: 0 });
  return new Promise((resolve) => {
    db.run(sql, params, function (err) {
      if (err) resolve({ lastID: 0, changes: 0 });
      else resolve({ lastID: this.lastID, changes: this.changes });
    });
  });
}

function get(sql, params = []) {
  if (!db) return Promise.resolve(null);
  return new Promise((resolve) => {
    db.get(sql, params, (err, row) => {
      if (err) resolve(null);
      else resolve(row);
    });
  });
}

function all(sql, params = []) {
  if (!db) return Promise.resolve([]);
  return new Promise((resolve) => {
    db.all(sql, params, (err, rows) => {
      if (err) resolve([]);
      else resolve(rows || []);
    });
  });
}

// Initialize Tables and Sync Existing Data
async function initDatabase() {
  if (!db) return;
  db.serialize(async () => {
    // 1. Patients Table
    db.run(`
      CREATE TABLE IF NOT EXISTS patients (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        phone TEXT,
        password TEXT,
        dob TEXT,
        gender TEXT DEFAULT 'Male',
        address TEXT,
        total_visits INTEGER DEFAULT 0,
        registered_at TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // 2. Dentists Table
    db.run(`
      CREATE TABLE IF NOT EXISTS dentists (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        qualification TEXT NOT NULL,
        specialization TEXT NOT NULL,
        experience TEXT NOT NULL,
        email TEXT,
        login_email TEXT,
        password TEXT,
        has_login INTEGER DEFAULT 0,
        phone TEXT,
        description TEXT,
        image TEXT DEFAULT 'images/team/1.webp',
        availability_days TEXT DEFAULT 'Monday - Friday',
        availability_hours TEXT DEFAULT '09:00 - 17:00',
        is_active INTEGER DEFAULT 1,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // 3. Services Table
    db.run(`
      CREATE TABLE IF NOT EXISTS services (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        slug TEXT NOT NULL UNIQUE,
        icon TEXT DEFAULT 'images/icons/tooth-1.png',
        image TEXT DEFAULT 'images/misc/l1.webp',
        short_description TEXT NOT NULL,
        full_description TEXT NOT NULL,
        benefits TEXT NOT NULL,
        price REAL DEFAULT 0.00,
        duration TEXT DEFAULT '45 mins',
        is_featured INTEGER DEFAULT 1,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // 4. Appointments Table
    db.run(`
      CREATE TABLE IF NOT EXISTS appointments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        appointment_ref TEXT NOT NULL UNIQUE,
        patient_id INTEGER,
        patient_name TEXT NOT NULL,
        patient_email TEXT NOT NULL,
        patient_phone TEXT,
        service_id INTEGER,
        service_name TEXT,
        dentist_id INTEGER NOT NULL,
        dentist_name TEXT,
        appointment_date TEXT NOT NULL,
        appointment_time TEXT NOT NULL,
        message TEXT,
        status TEXT DEFAULT 'Pending',
        cancellation_reason TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // 5. Patient Medical Records & Prescriptions
    db.run(`
      CREATE TABLE IF NOT EXISTS patient_medical_records (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        patient_id INTEGER,
        patient_email TEXT,
        patient_name TEXT NOT NULL,
        dentist_id INTEGER,
        dentist_name TEXT,
        appointment_id INTEGER,
        appointment_ref TEXT,
        treatment_date TEXT NOT NULL,
        diagnosis TEXT NOT NULL,
        treatment_provided TEXT NOT NULL,
        prescription TEXT,
        doctor_notes TEXT,
        followup_date TEXT,
        created_at TEXT DEFAULT CURRENT_TIMESTAMP,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // 6. Communication & Notification Ledger
    db.run(`
      CREATE TABLE IF NOT EXISTS notifications (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        recipient_email TEXT NOT NULL,
        recipient_phone TEXT,
        recipient_name TEXT NOT NULL,
        appointment_ref TEXT,
        channel TEXT DEFAULT 'email',
        type TEXT NOT NULL,
        subject TEXT NOT NULL,
        message TEXT NOT NULL,
        preview_url TEXT,
        html TEXT,
        status TEXT DEFAULT 'Delivered',
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // 7. Administrators Table
    db.run(`
      CREATE TABLE IF NOT EXISTS admins (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL UNIQUE,
        password TEXT NOT NULL,
        role TEXT DEFAULT 'superadmin',
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // 8. Contact Inquiries Table
    db.run(`
      CREATE TABLE IF NOT EXISTS contact_messages (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT NOT NULL,
        phone TEXT,
        subject TEXT,
        message TEXT NOT NULL,
        status TEXT DEFAULT 'Unread',
        created_at TEXT DEFAULT CURRENT_TIMESTAMP
      )
    `);

    // 9. Clinic Settings Table
    db.run(`
      CREATE TABLE IF NOT EXISTS clinic_settings (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        setting_key TEXT NOT NULL UNIQUE,
        setting_value TEXT,
        updated_at TEXT DEFAULT CURRENT_TIMESTAMP
      )
    `);

    console.log('[DATABASE] Database tables initialized successfully.');

    // Sync seed data from JSON files if tables are empty
    await syncDataFromJSON();
  });
}

// Helper to read JSON file safely
function readJSON(file, defaultValue = []) {
  try {
    if (fs.existsSync(file)) {
      return JSON.parse(fs.readFileSync(file, 'utf8'));
    }
  } catch (e) {
    console.error(`Error reading ${file}:`, e.message);
  }
  return defaultValue;
}

// Sync existing JSON data into the SQL database
async function syncDataFromJSON() {
  try {
    // 1. Sync Patients
    const patientsFile = path.join(DATA_DIR, 'patients.json');
    const patients = readJSON(patientsFile, []);
    for (const p of patients) {
      await run(`
        INSERT OR IGNORE INTO patients (id, name, email, phone, password, address, total_visits, registered_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)
      `, [p.id, p.name, p.email, p.phone || '', p.password || '', p.address || '', p.total_visits || 0, p.registered_at || new Date().toISOString()]);
    }

    // 2. Sync Dentists
    const dentistsFile = path.join(DATA_DIR, 'dentists.json');
    const dentists = readJSON(dentistsFile, []);
    for (const d of dentists) {
      await run(`
        INSERT OR REPLACE INTO dentists (id, name, qualification, specialization, experience, email, login_email, password, has_login, phone, description, image, availability_days, availability_hours, is_active)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `, [d.id, d.name, d.qualification, d.specialization, d.experience, d.email, d.login_email || d.email, d.password || 'doctor123', d.has_login ? 1 : 0, d.phone, d.description, d.image, d.availability_days, d.availability_hours, d.is_active !== undefined ? (d.is_active ? 1 : 0) : 1]);
    }

    // 2b. Sync Services
    const defaultServices = [
      [1, 'General Dentistry', 'general-dentistry', 'images/icons/tooth-1.png', 'images/misc/l1.webp', 'Complete oral care for every smile with cleanings, exams, and diagnostics.', 'Comprehensive oral examinations and biomimetic restorations.', 'Digital X-Rays, Fillings, Preventive Care', 1499.00, '45-60 mins', 1],
      [2, 'Teeth Whitening', 'teeth-whitening', 'images/icons/tooth-2.png', 'images/misc/l2.webp', 'Achieve a radiant, luminous smile up to 8 shades brighter in a single session.', 'Advanced laser teeth whitening with enamel safe protective gels.', 'Fast 60-min treatment, Long lasting results', 4999.00, '60 mins', 1],
      [3, 'Dental Implants', 'dental-implants', 'images/icons/tooth-3.png', 'images/misc/l3.webp', 'Permanent, natural-looking replacement for missing teeth with medical titanium.', 'Precision 3D guided titanium implant placement and biocompatible crowns.', 'Lifetime replacement, Preserves bone density', 25000.00, '90 mins', 1],
      [4, 'Root Canal Treatment', 'root-canal-treatment', 'images/icons/tooth-4.png', 'images/misc/l4.webp', 'Gentle, pain-free therapy designed to save severely infected or damaged teeth.', 'Microscopic rotary endodontics that eliminates infection while preserving tooth structure.', 'Instant pain relief, Natural tooth preservation', 5500.00, '60-75 mins', 1],
      [5, 'Orthodontics and Braces', 'orthodontics-and-braces', 'images/icons/tooth-5.png', 'images/misc/l5.webp', 'Clear aligners and modern braces for beautifully aligned teeth and balanced bites.', 'Clear aligners and ceramic aesthetic brackets for both teens and adults.', 'Straightens teeth, Fixes bite alignment', 35000.00, '45 mins', 1],
      [6, 'Cosmetic Dentistry', 'cosmetic-dentistry', 'images/icons/tooth-2.png', 'images/misc/l6.webp', 'Transform your smile with handcrafted porcelain veneers, contouring, and bonding.', 'Aesthetic smile redesigns with ultra-thin porcelain veneers.', 'Corrects gaps and chips, Camera-ready smile', 9999.00, '60 mins', 1],
      [7, 'Pediatric Dentistry', 'pediatric-dentistry', 'images/icons/tooth-6.png', 'images/misc/s1.webp', 'Child-friendly dental care in a warm, welcoming, and anxiety-free environment.', 'Specialized gentle oral care, sealants, and cavity prevention for young smiles.', 'Stress-free visits, Fluoride protection', 1299.00, '30-45 mins', 1],
      [8, 'Tooth Extraction', 'tooth-extraction', 'images/icons/tooth-7.png', 'images/misc/s2.webp', 'Safe, painless extraction of wisdom teeth, impacted teeth, or severe damage.', 'Minimally invasive atraumatic extractions with gentle local sedation.', 'Safe wisdom tooth removal, Quick recovery', 2499.00, '45-60 mins', 1],
      [9, 'Dental Cleaning', 'dental-cleaning', 'images/icons/tooth-1.png', 'images/misc/s3.webp', 'Ultrasonic plaque, tartar, and stain removal for fresh breath and radiant enamel.', 'Prophylactic scaling and polishing to maintain fresh breath and healthy gums.', 'Removes deep tartar, Polishes enamel', 999.00, '40 mins', 1],
      [10, 'Gum Treatment', 'gum-treatment', 'images/icons/tooth-4.png', 'images/misc/s4.webp', 'Comprehensive periodontal therapy to stop bleeding gums and treat gum disease.', 'Deep scaling, root planing, and laser pocket sterilization.', 'Stops gum bleeding, Prevents tooth loosening', 3499.00, '60 mins', 1]
    ];
    for (const s of defaultServices) {
      await run(`
        INSERT OR REPLACE INTO services (id, name, slug, icon, image, short_description, full_description, benefits, price, duration, is_featured)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `, s);
    }

    // 3. Sync Appointments
    const appointmentsFile = path.join(DATA_DIR, 'appointments.json');
    const appointments = readJSON(appointmentsFile, []);
    for (const a of appointments) {
      await run(`
        INSERT OR REPLACE INTO appointments (id, appointment_ref, patient_name, patient_email, patient_phone, service_id, service_name, dentist_id, dentist_name, appointment_date, appointment_time, message, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `, [a.id, a.appointment_ref, a.patient_name, a.patient_email, a.patient_phone || '', a.service_id || 1, a.service_name || '', a.dentist_id || 1, a.dentist_name || '', a.appointment_date, a.appointment_time, a.message || '', a.status || 'Pending', a.created_at || new Date().toISOString()]);
    }

    // 4. Sync Medical Records
    const medFile = path.join(DATA_DIR, 'medical_records.json');
    const medRecords = readJSON(medFile, []);
    for (const m of medRecords) {
      await run(`
        INSERT OR REPLACE INTO patient_medical_records (id, patient_email, patient_name, dentist_id, dentist_name, appointment_ref, treatment_date, diagnosis, treatment_provided, prescription, followup_date, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `, [m.id, m.patient_email, m.patient_name, m.dentist_id, m.dentist_name, m.appointment_ref || '', m.treatment_date, m.diagnosis, m.treatment_provided, m.prescription, m.followup_date || '', m.created_at || new Date().toISOString()]);
    }

    // 5. Sync Notifications
    const notifFile = path.join(DATA_DIR, 'notifications.json');
    const notifications = readJSON(notifFile, []);
    for (const n of notifications) {
      await run(`
        INSERT OR REPLACE INTO notifications (id, recipient_email, recipient_phone, recipient_name, appointment_ref, channel, type, subject, message, preview_url, html, status, created_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
      `, [n.id, n.recipient_email, n.recipient_phone || '', n.recipient_name, n.appointment_ref || '', n.channel || 'email', n.type || 'alert', n.subject || '', n.message || '', n.preview_url || '', n.html || '', n.status || 'Delivered', n.created_at || new Date().toISOString()]);
    }

    // 6. Sync Admin Account
    await run(`
      INSERT OR IGNORE INTO admins (id, name, email, password, role)
      VALUES (1, 'Chief Medical Admin', 'admin@dentia.com', 'admin123', 'superadmin')
    `);

    // 7. Sync Clinic Settings
    const settings = [
      ['clinic_name', 'Doctor Dental Clinic Dentia'],
      ['tagline', 'Modern Dental Care & Compassionate Smiles'],
      ['email', 'contact@dentiacare.com'],
      ['phone', '+1 (800) 234-5678'],
      ['emergency_phone', '+1 (800) 911-DENT'],
      ['address', '45 Cherry Road, Near New Bus Stand, Salem, Tamil Nadu 636007']
    ];
    for (const [k, v] of settings) {
      await run(`INSERT OR IGNORE INTO clinic_settings (setting_key, setting_value) VALUES (?, ?)`, [k, v]);
    }

    // Export human-readable CSV table files
    await exportTablesToCSV();

    console.log('[DATABASE] Data synchronization completed successfully.');
  } catch (err) {
    console.error('[DATABASE SYNC ERROR]:', err.message);
  }
}

// Export human-readable CSV table files into data/tables/ for instant viewing in VS Code & Excel
async function exportTablesToCSV() {
  const tablesDir = path.join(DATA_DIR, 'tables');
  if (!fs.existsSync(tablesDir)) {
    fs.mkdirSync(tablesDir, { recursive: true });
  }

  const tables = ['patients', 'dentists', 'services', 'appointments', 'patient_medical_records', 'notifications', 'admins', 'clinic_settings'];
  for (const t of tables) {
    try {
      const rows = await all(`SELECT * FROM ${t} ORDER BY id ASC`);
      if (rows && rows.length > 0) {
        const headers = Object.keys(rows[0]);
        const csvRows = [
          headers.join(','),
          ...rows.map(r => headers.map(h => {
            let val = r[h];
            if (val === null || val === undefined) return '""';
            val = String(val).replace(/"/g, '""');
            return `"${val}"`;
          }).join(','))
        ];
        fs.writeFileSync(path.join(tablesDir, `${t}.csv`), csvRows.join('\r\n'), 'utf8');
      }
    } catch (e) {
      console.error(`Error exporting table ${t} to CSV:`, e.message);
    }
  }
}

// Return database statistics
async function getDatabaseStats() {
  try {
    const stats = {};
    const tables = ['patients', 'dentists', 'services', 'appointments', 'patient_medical_records', 'notifications', 'admins'];
    for (const t of tables) {
      const res = await get(`SELECT COUNT(*) as count FROM ${t}`);
      stats[t] = res ? res.count : 0;
    }

    let fileSize = 0;
    if (fs.existsSync(DB_PATH)) {
      fileSize = fs.statSync(DB_PATH).size;
    }

    return {
      success: true,
      engine: 'SQLite 3 (Relational SQL Engine)',
      database_file: 'dentia.sqlite',
      database_path: DB_PATH,
      size_bytes: fileSize,
      size_formatted: (fileSize / 1024).toFixed(2) + ' KB',
      tables: stats
    };
  } catch (err) {
    return { success: false, error: err.message };
  }
}

// Initialize immediately
initDatabase();

module.exports = {
  db,
  run,
  get,
  all,
  getDatabaseStats,
  exportTablesToCSV,
  initDatabase,
  isAvailable: () => true,
  DB_PATH
};
