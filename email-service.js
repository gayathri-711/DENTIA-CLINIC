/**
 * Doctor Dental Clinic Dentia - Email Automation Service
 * Connects the backend directly to the Google Apps Script Email Automation Microservice
 */

const fs = require('fs');
const path = require('path');

const isVercel = Boolean(process.env.VERCEL || process.env.AWS_LAMBDA_FUNCTION_NAME);
const DATA_DIR = isVercel ? path.join('/tmp', 'dentia_data') : path.join(__dirname, 'Dentia', 'Dentia HTML', 'data');
const GATEWAY_CONFIG_FILE = path.join(DATA_DIR, 'gateway_config.json');

/**
 * Read current gateway configuration safely
 */
function getGatewayConfig() {
  try {
    if (fs.existsSync(GATEWAY_CONFIG_FILE)) {
      return JSON.parse(fs.readFileSync(GATEWAY_CONFIG_FILE, 'utf-8'));
    }
  } catch (e) {}
  return {};
}

/**
 * Get the Google Apps Script Web App URL and Authentication Secret
 */
function getAppsScriptConfig() {
  const gwConfig = getGatewayConfig();
  const emailCfg = gwConfig.email || {};

  const url = process.env.GOOGLE_APPS_SCRIPT_URL ||
              process.env.APPS_SCRIPT_URL ||
              emailCfg.apps_script_url ||
              '';

  const secret = process.env.GOOGLE_APPS_SCRIPT_SECRET ||
                 process.env.APPS_SCRIPT_SECRET ||
                 emailCfg.apps_script_secret ||
                 'dentia_secure_email_secret_2026';

  const enabled = process.env.ENABLE_EMAIL_AUTOMATION === 'true' ||
                  emailCfg.enabled !== false;

  return { url: url.trim(), secret: secret.trim(), enabled };
}

/**
 * Core method: Dispatches request to Google Apps Script Web App
 */
async function sendAppsScriptEmail({ template, recipient, subject, data, idempotencyKey }) {
  if (!recipient || typeof recipient !== 'string' || !recipient.includes('@')) {
    return { success: false, error: 'Invalid recipient email address' };
  }

  const cfg = getAppsScriptConfig();

  if (!cfg.url) {
    console.warn('[EMAIL SERVICE WARNING] Google Apps Script URL is not configured yet. Configure GOOGLE_APPS_SCRIPT_URL in .env or gateway_config.json.');
    return {
      success: false,
      not_configured: true,
      message: 'Google Apps Script Web App URL is not set. Please deploy the script and configure the Web App URL.'
    };
  }

  const payload = {
    secret: cfg.secret,
    template: template,
    recipient: recipient.trim().toLowerCase(),
    subject: subject || undefined,
    data: data || {},
    idempotencyKey: idempotencyKey || undefined
  };

  try {
    const controller = typeof AbortSignal !== 'undefined' && AbortSignal.timeout ? AbortSignal.timeout(6500) : undefined;
    const response = await fetch(cfg.url, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      },
      body: JSON.stringify(payload),
      redirect: 'follow',
      signal: controller
    });

    const responseText = await response.text();
    let result = {};
    try {
      result = JSON.parse(responseText);
    } catch (e) {
      result = { raw: responseText };
    }

    if (response.ok && (result.success !== false)) {
      console.log(`[EMAIL DISPATCH SUCCESS] Template: ${template} -> ${recipient} (MessageId: ${result.messageId || 'OK'})`);
      return { success: true, ...result };
    } else {
      console.error(`[EMAIL DISPATCH ERROR] HTTP ${response.status} from Apps Script:`, result.error || responseText);
      return { success: false, status: response.status, error: result.error || responseText };
    }
  } catch (err) {
    console.error(`[EMAIL DISPATCH NETWORK ERROR] Failed to send ${template} to ${recipient}:`, err.message);
    return { success: false, error: err.message };
  }
}

/**
 * 1. User Registration - Welcome Email
 */
async function sendWelcomeEmail({ name, email, phone }) {
  return sendAppsScriptEmail({
    template: 'welcome',
    recipient: email,
    idempotencyKey: `WELCOME-${email.toLowerCase()}`,
    data: {
      name: name || 'Valued Patient',
      email: email,
      phone: phone || ''
    }
  });
}

/**
 * 2. Email Verification - OTP Verification
 */
async function sendOtpEmail({ name, email, otp, validityMinutes = 10 }) {
  return sendAppsScriptEmail({
    template: 'otp_verification',
    recipient: email,
    data: {
      name: name || 'Patient',
      otp: String(otp),
      validityMinutes
    }
  });
}

/**
 * 3. Password Reset Instructions
 */
async function sendPasswordResetEmail({ name, email, resetLink, resetCode }) {
  return sendAppsScriptEmail({
    template: 'password_reset',
    recipient: email,
    data: {
      name: name || 'Patient',
      resetLink: resetLink,
      resetCode: resetCode || ''
    }
  });
}

/**
 * 4. Booking Confirmation
 */
async function sendBookingConfirmationEmail({
  patient_name,
  patient_email,
  patient_phone,
  appointment_ref,
  service_name,
  dentist_name,
  appointment_date,
  appointment_time
}) {
  return sendAppsScriptEmail({
    template: 'booking_confirmation',
    recipient: patient_email,
    idempotencyKey: `CONFIRM-${appointment_ref}`,
    data: {
      patient_name: patient_name,
      patient_phone: patient_phone,
      appointment_ref: appointment_ref,
      service_name: service_name || 'General Dentistry',
      dentist_name: dentist_name || 'Dr. Sarah Bennett',
      appointment_date: appointment_date,
      appointment_time: appointment_time
    }
  });
}

/**
 * 5. Payment Confirmation & Receipt
 */
async function sendPaymentReceiptEmail({
  patient_name,
  patient_email,
  appointment_ref,
  receipt_id,
  service_name,
  amount,
  payment_method,
  date
}) {
  return sendAppsScriptEmail({
    template: 'payment_receipt',
    recipient: patient_email,
    idempotencyKey: `RECEIPT-${receipt_id || appointment_ref}`,
    data: {
      patient_name: patient_name,
      appointment_ref: appointment_ref,
      receipt_id: receipt_id,
      service_name: service_name || 'Dental Consultation',
      amount: amount || '$150.00',
      payment_method: payment_method || 'Online Payment',
      date: date || new Date().toLocaleDateString('en-US')
    }
  });
}

/**
 * 6. Booking Cancellation Notice
 */
async function sendBookingCancellationEmail({
  patient_name,
  patient_email,
  appointment_ref,
  service_name,
  dentist_name,
  appointment_date,
  reason
}) {
  return sendAppsScriptEmail({
    template: 'booking_cancelled',
    recipient: patient_email,
    idempotencyKey: `CANCEL-${appointment_ref}`,
    data: {
      patient_name: patient_name,
      appointment_ref: appointment_ref,
      service_name: service_name,
      dentist_name: dentist_name,
      appointment_date: appointment_date,
      reason: reason || 'Requested by patient / Doctor schedule update'
    }
  });
}

/**
 * 7. Admin Notification Alert
 */
async function sendAdminAlertEmail({ event, details, ref, patient_name, adminEmail = 'admin@dentia.com' }) {
  return sendAppsScriptEmail({
    template: 'admin_alert',
    recipient: adminEmail,
    data: {
      event: event || 'Clinic Activity Alert',
      details: details || '',
      ref: ref || 'SYSTEM',
      patient_name: patient_name || 'N/A'
    }
  });
}

module.exports = {
  sendAppsScriptEmail,
  sendWelcomeEmail,
  sendOtpEmail,
  sendPasswordResetEmail,
  sendBookingConfirmationEmail,
  sendPaymentReceiptEmail,
  sendBookingCancellationEmail,
  sendAdminAlertEmail,
  getAppsScriptConfig
};
