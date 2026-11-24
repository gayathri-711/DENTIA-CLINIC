/**
 * ==============================================================================
 * DENTIA DENTAL CLINIC - GOOGLE APPS SCRIPT EMAIL AUTOMATION SYSTEM
 * Configuration & Environment Settings
 * File: Config.gs
 * ==============================================================================
 */

const CONFIG = {
  // Brand & Clinic Profile
  BRAND: {
    NAME: 'Doctor Dental Clinic Dentia',
    SHORT_NAME: 'Dentia Clinic',
    TAGLINE: 'Modern Oral Healthcare & Clinical Excellence',
    LOGO_URL: 'https://dentia-clinic.vercel.app/images/logo-black.webp',
    WEBSITE_URL: 'https://dentia-clinic.vercel.app',
    PORTAL_LOGIN_URL: 'https://dentia-clinic.vercel.app/login.html',
    BOOKING_URL: 'https://dentia-clinic.vercel.app/booking.html',
    ADMIN_PORTAL_URL: 'https://dentia-clinic.vercel.app/admin/index.html'
  },

  // Contact Information
  CONTACT: {
    PHONE: '+1 (800) 234-5678',
    EMERGENCY: '+1 (800) 911-DENT',
    EMAIL: 'contact@dentiacare.com',
    ADMIN_EMAIL: 'admin@dentia.com',
    ADDRESS: '45 Cherry Road, Near New Bus Stand, Salem, Tamil Nadu 636007',
    HOURS: 'Mon - Sat: 08:30 AM - 08:00 PM | Sun: 09:00 AM - 02:00 PM'
  },

  // Visual Theme Colors
  THEME: {
    PRIMARY: '#1463b8',       // Dentia Royal Blue
    PRIMARY_LIGHT: '#eff6ff', // Light Blue Tint
    SECONDARY: '#0b2545',     // Deep Navy
    SUCCESS: '#10b981',       // Emerald Green (Confirmed)
    WARNING: '#f59e0b',       // Amber / Gold (Pending / Alert)
    DANGER: '#ef4444',        // Coral Red (Cancelled)
    TEXT_MAIN: '#1e293b',     // Slate Charcoal
    TEXT_MUTED: '#64748b',    // Muted Slate
    BACKGROUND: '#f8fafc',    // Off-white / Soft Gray
    CARD_BG: '#ffffff',
    BORDER: '#e2e8f0'
  },

  // Security & Cache Settings
  SECURITY: {
    DEFAULT_AUTH_SECRET: 'dentia_secure_email_secret_2026', // Fallback if script property is unset
    CACHE_EXPIRATION_SECONDS: 21600, // 6 Hours cache for idempotency duplicate prevention
    OTP_VALIDITY_MINUTES: 10
  }
};

/**
 * Retrieves the configured authentication secret from Script Properties.
 * If not set by admin, falls back to the default config key.
 */
function getAuthSecret() {
  try {
    var prop = PropertiesService.getScriptProperties().getProperty('AUTH_SECRET');
    if (prop && prop.trim().length > 0) {
      return prop.trim();
    }
  } catch (e) {
    console.warn('[CONFIG WARNING] ScriptProperties read error:', e.message);
  }
  return CONFIG.SECURITY.DEFAULT_AUTH_SECRET;
}

/**
 * Returns remaining daily quota for email sending
 */
function getRemainingQuota() {
  try {
    return MailApp.getRemainingDailyQuota();
  } catch (e) {
    return -1;
  }
}
