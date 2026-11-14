<?php
/**
 * Admin Area Authentication Guard
 */
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/functions.php';

require_admin_login('login.php');

$adminData = get_logged_in_admin();
