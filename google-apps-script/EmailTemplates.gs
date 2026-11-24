/**
 * ==============================================================================
 * DENTIA DENTAL CLINIC - GOOGLE APPS SCRIPT EMAIL AUTOMATION SYSTEM
 * Professional Responsive HTML Email Templates
 * File: EmailTemplates.gs
 * ==============================================================================
 */

var EmailTemplates = (function() {

  /**
   * Universal HTML email wrapper ensuring cross-client responsiveness
   */
  function renderBaseLayout(content, preheader, headerTitle, headerSubtitle, accentColor) {
    var brand = CONFIG.BRAND;
    var contact = CONFIG.CONTACT;
    var theme = CONFIG.THEME;
    accentColor = accentColor || theme.PRIMARY;
    headerTitle = headerTitle || brand.NAME;
    headerSubtitle = headerSubtitle || brand.TAGLINE;
    preheader = preheader || brand.NAME;

    return '<!DOCTYPE html>' +
      '<html lang="en">' +
      '<head>' +
      '  <meta charset="utf-8">' +
      '  <meta name="viewport" content="width=device-width, initial-scale=1.0">' +
      '  <title>' + headerTitle + '</title>' +
      '  <style>' +
      '    body { margin:0; padding:0; background-color:' + theme.BACKGROUND + '; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Helvetica,Arial,sans-serif; color:' + theme.TEXT_MAIN + '; }' +
      '    table { border-collapse:collapse; }' +
      '    img { border:0; display:block; outline:none; text-decoration:none; }' +
      '    @media only screen and (max-width: 600px) {' +
      '      .container-table { width:100% !important; border-radius:0 !important; }' +
      '      .content-padding { padding:24px 18px !important; }' +
      '      .header-padding { padding:28px 20px !important; }' +
      '      .grid-stack { display:block !important; width:100% !important; margin-bottom:12px !important; }' +
      '    }' +
      '  </style>' +
      '</head>' +
      '<body style="margin:0; padding:24px 12px; background-color:' + theme.BACKGROUND + ';">' +
      '  <!-- Preheader text (hidden in email preview) -->' +
      '  <div style="display:none; font-size:1px; color:#fefefe; line-height:1px; max-height:0px; max-width:0px; opacity:0; overflow:hidden;">' +
      '    ' + preheader + ' &zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;&zwnj;&nbsp;' +
      '  </div>' +
      '  <table border="0" cellpadding="0" cellspacing="0" width="100%">' +
      '    <tr>' +
      '      <td align="center">' +
      '        <table class="container-table" border="0" cellpadding="0" cellspacing="0" width="580" style="max-width:580px; width:100%; background-color:' + theme.CARD_BG + '; border-radius:16px; overflow:hidden; box-shadow:0 8px 30px rgba(0,0,0,0.06); border:1px solid ' + theme.BORDER + ';">' +
      '          ' +
      '          <!-- Header Banner -->' +
      '          <tr>' +
      '            <td class="header-padding" align="center" style="background:linear-gradient(135deg, ' + accentColor + ' 0%, ' + theme.SECONDARY + ' 100%); padding:32px 28px; color:#ffffff;">' +
      '              <table border="0" cellpadding="0" cellspacing="0" width="100%">' +
      '                <tr>' +
      '                  <td align="center">' +
      '                    <div style="background:#ffffff; display:inline-block; padding:8px 18px; border-radius:10px; margin-bottom:14px; box-shadow:0 4px 12px rgba(0,0,0,0.12);">' +
      '                      <span style="font-size:18px; font-weight:800; color:' + theme.PRIMARY + '; letter-spacing:0.5px;">DENTIA</span>' +
      '                    </div>' +
      '                    <h1 style="margin:0; font-size:22px; font-weight:800; color:#ffffff; letter-spacing:-0.3px; line-height:1.3;">' + headerTitle + '</h1>' +
      '                    <p style="margin:6px 0 0 0; font-size:13px; color:rgba(255,255,255,0.85); font-weight:500;">' + headerSubtitle + '</p>' +
      '                  </td>' +
      '                </tr>' +
      '              </table>' +
      '            </td>' +
      '          </tr>' +
      '          ' +
      '          <!-- Body Content Area -->' +
      '          <tr>' +
      '            <td class="content-padding" style="padding:32px 28px; font-size:14.5px; line-height:1.65; color:' + theme.TEXT_MAIN + ';">' +
      '              ' + content + '' +
      '            </td>' +
      '          </tr>' +
      '          ' +
      '          <!-- Preparation & Support Notice -->' +
      '          <tr>' +
      '            <td style="padding:0 28px 24px 28px;">' +
      '              <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:' + theme.PRIMARY_LIGHT + '; border-radius:12px; border:1px solid #bfdbfe;">' +
      '                <tr>' +
      '                  <td style="padding:16px 18px; font-size:12.5px; line-height:1.55; color:#1e40af;">' +
      '                    <strong>&#9733; Clinical Notice:</strong> Please arrive 10 minutes prior to your appointment time. Bring any past dental records, X-rays, or dental insurance cards for seamless processing.' +
      '                  </td>' +
      '                </tr>' +
      '              </table>' +
      '            </td>' +
      '          </tr>' +
      '          ' +
      '          <!-- Footer Section -->' +
      '          <tr>' +
      '            <td style="background-color:#f8fafc; padding:24px 28px; text-align:center; font-size:12px; color:' + theme.TEXT_MUTED + '; border-top:1px solid ' + theme.BORDER + '; line-height:1.6;">' +
      '              <strong style="color:' + theme.TEXT_MAIN + '; font-size:13px;">' + brand.NAME + '</strong><br>' +
      '              ' + contact.ADDRESS + '<br>' +
      '              Phone: <strong>' + contact.PHONE + '</strong> &bull; Emergency Hotline: <strong>' + contact.EMERGENCY + '</strong><br>' +
      '              Email: <a href="mailto:' + contact.EMAIL + '" style="color:' + theme.PRIMARY + '; text-decoration:none;">' + contact.EMAIL + '</a> &bull; ' +
      '              <a href="' + brand.WEBSITE_URL + '" style="color:' + theme.PRIMARY + '; text-decoration:none;">Visit Website</a>' +
      '              <div style="margin-top:12px; font-size:11px; color:#94a3b8;">' +
      '                &copy; ' + new Date().getFullYear() + ' ' + brand.SHORT_NAME + '. All rights reserved. This is an automated transactional message.' +
      '              </div>' +
      '            </td>' +
      '          </tr>' +
      '          ' +
      '        </table>' +
      '      </td>' +
      '    </tr>' +
      '  </table>' +
      '</body>' +
      '</html>';
  }

  /**
   * 1. USER REGISTRATION - WELCOME EMAIL
   */
  function welcome(data) {
    var name = data.name || 'Valued Patient';
    var email = data.email || '';
    var portalUrl = CONFIG.BRAND.PORTAL_LOGIN_URL;

    var body = '' +
      '<div style="margin-bottom:20px;">' +
      '  <span style="background-color:#eff6ff; color:#1d4ed8; font-weight:700; font-size:12px; padding:4px 12px; border-radius:20px; border:1px solid #bfdbfe;">WELCOME TO DENTIA</span>' +
      '</div>' +
      '<h2 style="margin:0 0 12px 0; font-size:20px; color:#0f172a; font-weight:700;">Welcome to our Dental Family!</h2>' +
      '<p style="margin:0 0 16px 0;">Hello <strong>' + name + '</strong>,</p>' +
      '<p style="margin:0 0 20px 0;">Thank you for registering your patient account with <strong>Doctor Dental Clinic Dentia</strong>. Your digital health profile is now active, giving you 24/7 access to your upcoming appointments, clinical medical history, prescription notes, and direct booking.</p>' +
      '' +
      '<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f8fafc; border-radius:12px; border:1px solid #e2e8f0; margin-bottom:24px;">' +
      '  <tr>' +
      '    <td style="padding:18px 20px;">' +
      '      <table border="0" cellpadding="4" cellspacing="0" width="100%" style="font-size:13.5px;">' +
      '        <tr><td style="color:#64748b; width:130px;">Registered Name:</td><td style="font-weight:600; color:#1e293b;">' + name + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Login Email:</td><td style="font-weight:600; color:#1e293b;">' + email + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Member ID:</td><td style="font-weight:600; color:#1463b8;">DEN-P' + Math.floor(1000 + Math.random() * 9000) + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Clinic Status:</td><td style="font-weight:600; color:#10b981;">&#10004; Active Patient</td></tr>' +
      '      </table>' +
      '    </td>' +
      '  </tr>' +
      '</table>' +
      '' +
      '<div style="text-align:center; margin:28px 0;">' +
      '  <a href="' + portalUrl + '" style="background:linear-gradient(135deg, #1463b8, #0b2545); color:#ffffff; text-decoration:none; padding:13px 28px; border-radius:30px; font-weight:700; font-size:14px; display:inline-block; box-shadow:0 4px 14px rgba(20,99,184,0.3);">' +
      '    Sign In to Patient Portal &rarr;' +
      '  </a>' +
      '</div>' +
      '<p style="margin:0; font-size:13px; color:#64748b;">If you did not register for this account, please immediately reach out to our desk at <a href="mailto:' + CONFIG.CONTACT.EMAIL + '" style="color:#1463b8;">' + CONFIG.CONTACT.EMAIL + '</a>.</p>';

    return renderBaseLayout(body, 'Welcome to Dentia Dental Clinic! Your patient account is ready.', 'Welcome to Dentia Clinic', 'Your Patient Profile is Ready', CONFIG.THEME.PRIMARY);
  }

  /**
   * 2. EMAIL VERIFICATION - OTP VERIFICATION
   */
  function otpVerification(data) {
    var name = data.name || 'Patient';
    var otp = data.otp || '000000';
    var validityMinutes = data.validityMinutes || CONFIG.SECURITY.OTP_VALIDITY_MINUTES;

    var body = '' +
      '<div style="margin-bottom:18px;">' +
      '  <span style="background-color:#fef3c7; color:#b45309; font-weight:700; font-size:12px; padding:4px 12px; border-radius:20px; border:1px solid #fde68a;">ONE-TIME SECURITY CODE</span>' +
      '</div>' +
      '<h2 style="margin:0 0 12px 0; font-size:20px; color:#0f172a; font-weight:700;">Verify Your Email Address</h2>' +
      '<p style="margin:0 0 16px 0;">Hello <strong>' + name + '</strong>,</p>' +
      '<p style="margin:0 0 20px 0;">You recently requested a security verification code for your <strong>Dentia Dental Clinic</strong> account. Please use the 6-digit one-time password (OTP) below to authenticate your request:</p>' +
      '' +
      '<div style="text-align:center; margin:32px 0;">' +
      '  <div style="display:inline-block; background:#f1f5f9; border:2px dashed #1463b8; border-radius:14px; padding:16px 36px; letter-spacing:8px; font-size:32px; font-weight:800; color:#0b2545; font-family:monospace; box-shadow:0 4px 12px rgba(0,0,0,0.04);">' +
      '    ' + otp + '' +
      '  </div>' +
      '  <div style="font-size:12px; color:#64748b; margin-top:10px;">This code will expire in <strong>' + validityMinutes + ' minutes</strong>.</div>' +
      '</div>' +
      '' +
      '<div style="background-color:#fee2e2; border-left:4px solid #ef4444; border-radius:8px; padding:12px 16px; margin:24px 0; font-size:12.5px; color:#991b1b;">' +
      '  <strong>Security Alert:</strong> Never share this OTP with anyone, including staff members. Dentia representatives will never ask for your code over phone or message.' +
      '</div>';

    return renderBaseLayout(body, 'Your Dentia verification code is ' + otp, 'Security Verification', 'One-Time Verification Code', CONFIG.THEME.PRIMARY);
  }

  /**
   * 3. PASSWORD RESET - SECURE PASSWORD RESET
   */
  function passwordReset(data) {
    var name = data.name || 'Patient';
    var resetLink = data.resetLink || (CONFIG.BRAND.WEBSITE_URL + '/login.html');
    var resetCode = data.resetCode || '';

    var body = '' +
      '<div style="margin-bottom:18px;">' +
      '  <span style="background-color:#fee2e2; color:#b91c1c; font-weight:700; font-size:12px; padding:4px 12px; border-radius:20px; border:1px solid #fecaca;">ACCOUNT SECURITY</span>' +
      '</div>' +
      '<h2 style="margin:0 0 12px 0; font-size:20px; color:#0f172a; font-weight:700;">Password Reset Request</h2>' +
      '<p style="margin:0 0 16px 0;">Hello <strong>' + name + '</strong>,</p>' +
      '<p style="margin:0 0 20px 0;">We received a request to reset the password for your Dentia Dental Clinic account. Click the secure button below to set a new password:</p>' +
      '' +
      '<div style="text-align:center; margin:28px 0;">' +
      '  <a href="' + resetLink + '" style="background:linear-gradient(135deg, #1463b8, #0b2545); color:#ffffff; text-decoration:none; padding:13px 30px; border-radius:30px; font-weight:700; font-size:14px; display:inline-block; box-shadow:0 4px 14px rgba(20,99,184,0.3);">' +
      '    Reset Your Password &rarr;' +
      '  </a>' +
      '</div>' +
      (resetCode ? '<p style="text-align:center; font-size:13px; color:#64748b;">Or enter temporary reset key: <code style="background:#f1f5f9; padding:3px 8px; border-radius:4px; font-weight:700; color:#1e293b;">' + resetCode + '</code></p>' : '') +
      '<p style="margin:24px 0 0 0; font-size:13px; color:#64748b;">If you did not make this request, you can safely ignore this email. Your current password will remain unchanged.</p>';

    return renderBaseLayout(body, 'Reset your password for Dentia Dental Clinic', 'Reset Your Password', 'Secure Account Recovery', CONFIG.THEME.PRIMARY);
  }

  /**
   * 4. BOOKING CONFIRMATION - APPOINTMENT DETAILS
   */
  function bookingConfirmation(data) {
    var name = data.patient_name || data.name || 'Valued Patient';
    var ref = data.appointment_ref || data.ref || ('DEN-2026-' + Math.floor(1000 + Math.random() * 9000));
    var service = data.service_name || data.service || 'General Dentistry';
    var doctor = data.dentist_name || data.doctor || 'Dr. Sarah Bennett';
    var date = data.appointment_date || data.date || 'TBD';
    var time = data.appointment_time || data.time || 'TBD';
    var dashboardUrl = CONFIG.BRAND.PORTAL_LOGIN_URL;

    var body = '' +
      '<div style="margin-bottom:18px;">' +
      '  <span style="background-color:#d1fae5; color:#065f46; font-weight:700; font-size:12px; padding:4px 12px; border-radius:20px; border:1px solid #a7f3d0;">&#10004; CONFIRMED APPOINTMENT</span>' +
      '</div>' +
      '<h2 style="margin:0 0 12px 0; font-size:20px; color:#0f172a; font-weight:700;">Your Dental Visit is Confirmed!</h2>' +
      '<p style="margin:0 0 16px 0;">Dear <strong>' + name + '</strong>,</p>' +
      '<p style="margin:0 0 20px 0;">Great news! Your upcoming dental consultation at <strong>Doctor Dental Clinic Dentia</strong> has been reviewed and officially confirmed by our medical team. Here are your booking details:</p>' +
      '' +
      '<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f8fafc; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:24px;">' +
      '  <tr>' +
      '    <td style="padding:22px 24px;">' +
      '      <table border="0" cellpadding="6" cellspacing="0" width="100%" style="font-size:14px;">' +
      '        <tr><td style="color:#64748b; width:140px;">Booking Ref:</td><td style="font-weight:700; color:#1463b8; font-family:monospace; font-size:15px;">' + ref + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Specialist Doctor:</td><td style="font-weight:700; color:#0f172a;">' + doctor + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Clinical Procedure:</td><td style="font-weight:600; color:#1e293b;">' + service + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Scheduled Date:</td><td style="font-weight:700; color:#059669;">' + date + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Time Slot:</td><td style="font-weight:700; color:#059669;">' + time + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Location:</td><td style="font-weight:600; color:#1e293b;">' + CONFIG.CONTACT.ADDRESS + '</td></tr>' +
      '      </table>' +
      '    </td>' +
      '  </tr>' +
      '</table>' +
      '' +
      '<div style="text-align:center; margin:28px 0;">' +
      '  <a href="' + dashboardUrl + '" style="background:linear-gradient(135deg, #10b981, #065f46); color:#ffffff; text-decoration:none; padding:13px 30px; border-radius:30px; font-weight:700; font-size:14px; display:inline-block; box-shadow:0 4px 14px rgba(16,185,129,0.3);">' +
      '    View in Patient Dashboard &rarr;' +
      '  </a>' +
      '</div>' +
      '<p style="margin:0; font-size:13px; color:#64748b;">Need to modify your appointment? Please contact our desk 24 hours in advance at <strong>' + CONFIG.CONTACT.PHONE + '</strong>.</p>';

    return renderBaseLayout(body, 'Booking Confirmed: ' + service + ' with ' + doctor + ' on ' + date + ' (' + ref + ')', 'Appointment Confirmed', 'Ref: ' + ref + ' &bull; Scheduled Visit', CONFIG.THEME.SUCCESS);
  }

  /**
   * 5. PAYMENT CONFIRMATION - PAYMENT RECEIPT
   */
  function paymentReceipt(data) {
    var name = data.patient_name || data.name || 'Valued Patient';
    var ref = data.appointment_ref || data.ref || 'DEN-INV-' + Math.floor(1000 + Math.random() * 9000);
    var receiptId = data.receipt_id || data.transaction_id || ('TXN-' + Date.now().toString(36).toUpperCase());
    var service = data.service_name || data.service || 'Comprehensive Dental Care';
    var amount = data.amount || '$150.00';
    var method = data.payment_method || 'Online / Card Payment';
    var date = data.date || new Date().toLocaleDateString('en-US', { year:'numeric', month:'short', day:'numeric' });

    var body = '' +
      '<div style="margin-bottom:18px;">' +
      '  <span style="background-color:#d1fae5; color:#065f46; font-weight:700; font-size:12px; padding:4px 12px; border-radius:20px; border:1px solid #a7f3d0;">PAID IN FULL</span>' +
      '</div>' +
      '<h2 style="margin:0 0 12px 0; font-size:20px; color:#0f172a; font-weight:700;">Payment Receipt & Invoice</h2>' +
      '<p style="margin:0 0 16px 0;">Hello <strong>' + name + '</strong>,</p>' +
      '<p style="margin:0 0 20px 0;">Thank you for your payment. Your transaction has been processed successfully. Below is your official receipt for dental services rendered at <strong>Doctor Dental Clinic Dentia</strong>:</p>' +
      '' +
      '<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f8fafc; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:24px;">' +
      '  <tr>' +
      '    <td style="padding:22px 24px;">' +
      '      <table border="0" cellpadding="6" cellspacing="0" width="100%" style="font-size:14px;">' +
      '        <tr><td style="color:#64748b; width:140px;">Receipt Number:</td><td style="font-weight:700; color:#1463b8; font-family:monospace;">' + receiptId + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Booking Ref:</td><td style="font-weight:600; color:#1e293b; font-family:monospace;">' + ref + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Item Description:</td><td style="font-weight:600; color:#1e293b;">' + service + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Payment Method:</td><td style="font-weight:600; color:#1e293b;">' + method + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Date & Time:</td><td style="font-weight:600; color:#1e293b;">' + date + '</td></tr>' +
      '        <tr style="border-top:1px solid #cbd5e1;"><td style="color:#0f172a; font-weight:700; font-size:16px; padding-top:12px;">Total Paid:</td><td style="font-weight:800; color:#059669; font-size:18px; padding-top:12px;">' + amount + '</td></tr>' +
      '      </table>' +
      '    </td>' +
      '  </tr>' +
      '</table>' +
      '' +
      '<p style="margin:0; font-size:13px; color:#64748b;">This receipt serves as proof of payment for your personal records and dental health insurance reimbursement.</p>';

    return renderBaseLayout(body, 'Payment Receipt: ' + amount + ' for ' + service + ' (' + receiptId + ')', 'Payment Receipt', 'Transaction ID: ' + receiptId, CONFIG.THEME.SUCCESS);
  }

  /**
   * 6. BOOKING CANCELLATION - CANCELLATION NOTIFICATION
   */
  function bookingCancellation(data) {
    var name = data.patient_name || data.name || 'Valued Patient';
    var ref = data.appointment_ref || data.ref || 'DEN-APPT';
    var service = data.service_name || data.service || 'Dental Consultation';
    var doctor = data.dentist_name || data.doctor || 'Clinic Specialist';
    var date = data.appointment_date || data.date || 'Scheduled Date';
    var reason = data.reason || 'Requested by patient / Schedule update';
    var bookingUrl = CONFIG.BRAND.BOOKING_URL;

    var body = '' +
      '<div style="margin-bottom:18px;">' +
      '  <span style="background-color:#fee2e2; color:#b91c1c; font-weight:700; font-size:12px; padding:4px 12px; border-radius:20px; border:1px solid #fecaca;">APPOINTMENT CANCELLED</span>' +
      '</div>' +
      '<h2 style="margin:0 0 12px 0; font-size:20px; color:#0f172a; font-weight:700;">Appointment Cancellation Notice</h2>' +
      '<p style="margin:0 0 16px 0;">Dear <strong>' + name + '</strong>,</p>' +
      '<p style="margin:0 0 20px 0;">This email confirms that your dental appointment <strong>' + ref + '</strong> with <strong>' + doctor + '</strong> for <strong>' + date + '</strong> has been cancelled.</p>' +
      '' +
      '<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f8fafc; border-radius:12px; border:1px solid #e2e8f0; margin-bottom:24px;">' +
      '  <tr>' +
      '    <td style="padding:18px 20px; font-size:13.5px;">' +
      '      <strong>Reason / Status:</strong> ' + reason + '<br>' +
      '      <span style="color:#64748b;">Service: ' + service + ' &bull; Doctor: ' + doctor + '</span>' +
      '    </td>' +
      '  </tr>' +
      '</table>' +
      '' +
      '<p style="margin:0 0 24px 0;">We understand that schedules change! Whenever you are ready to book a new appointment, our online scheduling portal is available 24/7 with instant slot confirmation:</p>' +
      '<div style="text-align:center; margin:28px 0;">' +
      '  <a href="' + bookingUrl + '" style="background:linear-gradient(135deg, #1463b8, #0b2545); color:#ffffff; text-decoration:none; padding:13px 30px; border-radius:30px; font-weight:700; font-size:14px; display:inline-block; box-shadow:0 4px 14px rgba(20,99,184,0.3);">' +
      '    Book a New Appointment &rarr;' +
      '  </a>' +
      '</div>';

    return renderBaseLayout(body, 'Notice: Dental Appointment ' + ref + ' on ' + date + ' has been cancelled', 'Appointment Cancelled', 'Ref: ' + ref, CONFIG.THEME.DANGER);
  }

  /**
   * 7. ADMIN NOTIFICATIONS - ACTIVITY & ALERT
   */
  function adminAlert(data) {
    var event = data.event || 'System Activity Alert';
    var details = data.details || 'New activity recorded on Dentia Clinic Portal.';
    var ref = data.ref || 'SYSTEM';
    var patient = data.patient_name || data.patient || 'N/A';
    var adminUrl = CONFIG.BRAND.ADMIN_PORTAL_URL;

    var body = '' +
      '<div style="margin-bottom:18px;">' +
      '  <span style="background-color:#eff6ff; color:#1d4ed8; font-weight:700; font-size:12px; padding:4px 12px; border-radius:20px; border:1px solid #bfdbfe;">ADMINISTRATIVE DISPATCH</span>' +
      '</div>' +
      '<h2 style="margin:0 0 12px 0; font-size:20px; color:#0f172a; font-weight:700;">' + event + '</h2>' +
      '<p style="margin:0 0 16px 0;">Hello <strong>Chief Medical Admin</strong>,</p>' +
      '<p style="margin:0 0 20px 0;">An automated transaction event has occurred that requires administrative awareness:</p>' +
      '' +
      '<table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color:#f8fafc; border-radius:14px; border:1px solid #e2e8f0; margin-bottom:24px;">' +
      '  <tr>' +
      '    <td style="padding:20px 22px;">' +
      '      <table border="0" cellpadding="5" cellspacing="0" width="100%" style="font-size:13.5px;">' +
      '        <tr><td style="color:#64748b; width:130px;">Event Type:</td><td style="font-weight:700; color:#1463b8;">' + event + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Reference:</td><td style="font-weight:600; color:#1e293b; font-family:monospace;">' + ref + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Patient / User:</td><td style="font-weight:600; color:#1e293b;">' + patient + '</td></tr>' +
      '        <tr><td style="color:#64748b;">Timestamp:</td><td style="font-weight:600; color:#64748b;">' + new Date().toUTCString() + '</td></tr>' +
      '      </table>' +
      '      <div style="margin-top:14px; padding-top:14px; border-top:1px solid #e2e8f0; font-size:13px; line-height:1.6; color:#334155; white-space:pre-line;">' +
      '        ' + details + '' +
      '      </div>' +
      '    </td>' +
      '  </tr>' +
      '</table>' +
      '' +
      '<div style="text-align:center; margin:28px 0;">' +
      '  <a href="' + adminUrl + '" style="background:linear-gradient(135deg, #0b2545, #1463b8); color:#ffffff; text-decoration:none; padding:13px 30px; border-radius:30px; font-weight:700; font-size:14px; display:inline-block; box-shadow:0 4px 14px rgba(11,37,69,0.3);">' +
      '    Open Admin Management Console &rarr;' +
      '  </a>' +
      '</div>';

    return renderBaseLayout(body, 'Admin Alert: ' + event + ' (' + ref + ')', 'Admin Activity Alert', event, CONFIG.THEME.SECONDARY);
  }

  // Public Interface
  return {
    welcome: welcome,
    otpVerification: otpVerification,
    passwordReset: passwordReset,
    bookingConfirmation: bookingConfirmation,
    paymentReceipt: paymentReceipt,
    bookingCancellation: bookingCancellation,
    adminAlert: adminAlert,
    renderBaseLayout: renderBaseLayout
  };

})();
