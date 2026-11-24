/**
 * ==============================================================================
 * DENTIA DENTAL CLINIC - GOOGLE APPS SCRIPT EMAIL AUTOMATION SYSTEM
 * Main API & Request Handling Engine
 * File: Code.gs
 * ==============================================================================
 */

/**
 * Health check endpoint (GET request)
 * Allows testing from a browser or uptime monitor
 */
function doGet(e) {
  var quota = getRemainingQuota();
  var response = {
    success: true,
    service: CONFIG.BRAND.NAME + ' - Email Automation Service',
    status: 'operational',
    version: '2.0.0',
    remainingDailyQuota: quota,
    timestamp: new Date().toISOString()
  };

  return ContentService.createTextOutput(JSON.stringify(response))
    .setMimeType(ContentService.MimeType.JSON);
}

/**
 * Main Webhook Receiver (POST request)
 * Processes JSON requests from the Dentia backend to send automated transactional emails
 */
function doPost(e) {
  var lock = LockService.getScriptLock();
  try {
    // Acquire lock for up to 10 seconds to prevent race conditions
    lock.waitLock(10000);

    // 1. Parse JSON Payload
    if (!e || !e.postData || !e.postData.contents) {
      return createJsonResponse(false, 'Bad Request: No JSON request payload received.', 400);
    }

    var payload = {};
    try {
      payload = JSON.parse(e.postData.contents);
    } catch (parseError) {
      return createJsonResponse(false, 'Invalid JSON format: ' + parseError.message, 400);
    }

    // 2. Authentication Check
    var configuredSecret = getAuthSecret();
    var providedSecret = payload.secret || (e.parameter && e.parameter.secret);

    if (!providedSecret || providedSecret !== configuredSecret) {
      console.warn('[SECURITY] Unauthorized access attempt from payload');
      return createJsonResponse(false, 'Unauthorized: Invalid or missing API authentication secret.', 401);
    }

    // 3. Validate Recipient Email Address
    var recipient = (payload.recipient || payload.to || '').trim();
    if (!recipient || !isValidEmail(recipient)) {
      return createJsonResponse(false, 'Validation Error: A valid recipient email address is required.', 422);
    }

    // 4. Validate Template
    var template = (payload.template || payload.action || '').trim().toLowerCase();
    if (!template) {
      return createJsonResponse(false, 'Validation Error: An email template name must be provided.', 422);
    }

    var data = payload.data || {};
    // Allow top-level fields to be merged into data
    if (!data.name && payload.name) data.name = payload.name;
    if (!data.email && payload.recipient) data.email = payload.recipient;

    // 5. Idempotency Check (Prevent duplicate emails)
    var idempotencyKey = payload.idempotencyKey || payload.idempotency_key;
    if (idempotencyKey) {
      var cache = CacheService.getScriptCache();
      var cachedStatus = cache.get(idempotencyKey);
      if (cachedStatus) {
        console.log('[IDEMPOTENCY] Skipped duplicate send for key: ' + idempotencyKey);
        return ContentService.createTextOutput(JSON.stringify({
          success: true,
          duplicate: true,
          message: 'Transactional email was already dispatched for this idempotency key.',
          idempotencyKey: idempotencyKey,
          timestamp: new Date().toISOString()
        })).setMimeType(ContentService.MimeType.JSON);
      }
    }

    // 6. Check Daily MailApp Quota
    var quota = MailApp.getRemainingDailyQuota();
    if (quota <= 0) {
      console.error('[QUOTA ERROR] Daily MailApp quota exhausted: ' + quota);
      return createJsonResponse(false, 'Google MailApp daily sending quota exceeded for this account.', 429);
    }

    // 7. Render Template HTML & Subject
    var rendered = compileEmail(template, data, payload.subject);
    if (!rendered || !rendered.html) {
      return createJsonResponse(false, 'Unknown or invalid template: "' + template + '".', 400);
    }

    var subject = rendered.subject;
    var htmlBody = rendered.html;

    // 8. Send Email using Google Apps Script MailApp
    var messageId = Utilities.getUuid();
    var emailOptions = {
      to: recipient,
      subject: subject,
      htmlBody: htmlBody,
      name: CONFIG.BRAND.NAME,
      noReply: true
    };

    if (payload.replyTo) {
      emailOptions.replyTo = payload.replyTo;
    }

    MailApp.sendEmail(emailOptions);
    console.log('[EMAIL SENT] Template: ' + template + ' | To: ' + recipient + ' | MessageId: ' + messageId);

    // 9. Store Idempotency Key in Cache if provided
    if (idempotencyKey) {
      try {
        CacheService.getScriptCache().put(idempotencyKey, messageId, CONFIG.SECURITY.CACHE_EXPIRATION_SECONDS);
      } catch (cacheErr) {
        console.warn('[CACHE WARNING] Could not store idempotency key:', cacheErr.message);
      }
    }

    // 10. Return Successful Response
    return ContentService.createTextOutput(JSON.stringify({
      success: true,
      messageId: messageId,
      recipient: recipient,
      template: template,
      subject: subject,
      remainingDailyQuota: MailApp.getRemainingDailyQuota(),
      timestamp: new Date().toISOString()
    })).setMimeType(ContentService.MimeType.JSON);

  } catch (err) {
    console.error('[UNHANDLED ERROR] doPost execution error:', err.toString());
    return createJsonResponse(false, 'Internal Server Error: ' + err.toString(), 500);
  } finally {
    try {
      lock.releaseLock();
    } catch (lockErr) {}
  }
}

/**
 * Route template name to corresponding template function in EmailTemplates.gs
 */
function compileEmail(template, data, customSubject) {
  var subject = '';
  var html = '';

  switch (template) {
    case 'welcome':
    case 'user_registration':
    case 'patient_welcome':
      subject = customSubject || ('Welcome to ' + CONFIG.BRAND.NAME + ' - Your Patient Profile is Ready');
      html = EmailTemplates.welcome(data);
      break;

    case 'otp_verification':
    case 'email_verification':
    case 'verify_email':
      subject = customSubject || ('Your Dentia Security Code: ' + (data.otp || 'Verification'));
      html = EmailTemplates.otpVerification(data);
      break;

    case 'password_reset':
    case 'reset_password':
      subject = customSubject || ('Reset Your Password - ' + CONFIG.BRAND.SHORT_NAME);
      html = EmailTemplates.passwordReset(data);
      break;

    case 'booking_confirmation':
    case 'appointment_confirmed':
    case 'booking_confirmed':
      subject = customSubject || ('Appointment Confirmed: [' + (data.appointment_ref || data.ref || 'Ref') + '] - ' + CONFIG.BRAND.SHORT_NAME);
      html = EmailTemplates.bookingConfirmation(data);
      break;

    case 'payment_receipt':
    case 'payment_confirmation':
    case 'invoice':
      subject = customSubject || ('Payment Receipt & Invoice [' + (data.receipt_id || data.appointment_ref || 'Receipt') + '] - ' + CONFIG.BRAND.SHORT_NAME);
      html = EmailTemplates.paymentReceipt(data);
      break;

    case 'booking_cancelled':
    case 'booking_cancellation':
    case 'appointment_cancelled':
      subject = customSubject || ('Appointment Notice: Cancelled [' + (data.appointment_ref || data.ref || 'Notice') + '] - ' + CONFIG.BRAND.SHORT_NAME);
      html = EmailTemplates.bookingCancellation(data);
      break;

    case 'admin_alert':
    case 'admin_notification':
      subject = customSubject || ('[ADMIN ALERT] ' + (data.event || 'Clinic Activity Alert') + ' - ' + CONFIG.BRAND.SHORT_NAME);
      html = EmailTemplates.adminAlert(data);
      break;

    case 'custom':
      if (!data.html && !data.message) return null;
      subject = customSubject || ('Important Notification - ' + CONFIG.BRAND.SHORT_NAME);
      html = data.html || EmailTemplates.renderBaseLayout(data.message, subject, subject);
      break;

    default:
      return null;
  }

  return { subject: subject, html: html };
}

/**
 * Validate standard email format using RFC regex
 */
function isValidEmail(email) {
  if (typeof email !== 'string') return false;
  var re = /^[a-zA-Z0-9.!#$%&'*+/=?^_`{|}~-]+@[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?(?:\.[a-zA-Z0-9](?:[a-zA-Z0-9-]{0,61}[a-zA-Z0-9])?)+$/;
  return re.test(email.trim());
}

/**
 * Helper to construct JSON HTTP response
 */
function createJsonResponse(success, message, statusCode) {
  var resObj = {
    success: success,
    status: statusCode || (success ? 200 : 400),
    error: success ? null : message,
    message: success ? message : null,
    timestamp: new Date().toISOString()
  };

  return ContentService.createTextOutput(JSON.stringify(resObj))
    .setMimeType(ContentService.MimeType.JSON);
}

/**
 * Test function that can be run directly inside the Google Apps Script IDE
 * Used to trigger initial Google permission authorization flow
 */
function testEmail() {
  var testRecipient = Session.getActiveUser().getEmail();
  if (!testRecipient) {
    testRecipient = CONFIG.CONTACT.ADMIN_EMAIL;
  }

  Logger.log('Sending test verification email to: ' + testRecipient);

  var mockData = {
    name: 'Dr. Sarah Bennett',
    patient_name: 'John Doe',
    email: testRecipient,
    appointment_ref: 'DEN-2026-TEST',
    service_name: 'Cosmetic Dentistry & Teeth Whitening',
    dentist_name: 'Dr. Sarah Bennett',
    appointment_date: 'November 28, 2026',
    appointment_time: '10:00 AM'
  };

  var result = compileEmail('booking_confirmation', mockData);
  MailApp.sendEmail({
    to: testRecipient,
    subject: '[TEST] ' + result.subject,
    htmlBody: result.html,
    name: CONFIG.BRAND.NAME,
    noReply: true
  });

  Logger.log('Test email successfully dispatched! Quota remaining: ' + MailApp.getRemainingDailyQuota());
}
