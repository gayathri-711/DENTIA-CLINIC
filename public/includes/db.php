<?php
/**
 * Doctor Dental Clinic Dentia - Database Connection Layer
 * Supports MySQL via PDO with prepared statements and resilient fallbacks
 */

if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME') ?: 'dentia_db');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3306');

$pdo = null;
$db_error = null;
$db_driver = 'mysql';

try {
    $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Check if the database dentia_db just doesn't exist yet, try creating it if connection to server succeeds
    try {
        $serverDsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";charset=utf8mb4";
        $tempPdo = new PDO($serverDsn, DB_USER, DB_PASS, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        
        $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
    } catch (Exception $subEx) {
        // If MySQL server is not running or credentials rejected, provide fallback to SQLite if needed
        $sqlitePath = __DIR__ . '/../data/dentia.sqlite';
        if (!is_dir(dirname($sqlitePath))) {
            @mkdir(dirname($sqlitePath), 0777, true);
        }
        try {
            $pdo = new PDO("sqlite:" . $sqlitePath);
            $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            $db_driver = 'sqlite';
        } catch (Exception $sqlitEx) {
            $db_error = $e->getMessage();
        }
    }
}

/**
 * Returns active PDO connection or null
 */
function get_db_connection() {
    global $pdo;
    return $pdo;
}

/**
 * Check if the required tables exist
 */
function are_tables_installed() {
    global $pdo, $db_driver;
    if (!$pdo) return false;
    try {
        if ($db_driver === 'sqlite') {
            $stmt = $pdo->query("SELECT name FROM sqlite_master WHERE type='table' AND name='services'");
            return $stmt->fetchColumn() !== false;
        } else {
            $stmt = $pdo->query("SHOW TABLES LIKE 'services'");
            return $stmt->rowCount() > 0;
        }
    } catch (Exception $e) {
        return false;
    }
}

