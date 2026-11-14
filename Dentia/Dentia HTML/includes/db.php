<?php
/**
 * MEDINOVA Multi-Speciality Hospital Management System
 * Database Connection Layer (PDO MySQL with resilient fallback)
 */

$configFile = __DIR__ . '/../config/database.php';
$dbConfig = file_exists($configFile) ? require $configFile : [
    'host'     => getenv('DB_HOST') ?: 'localhost',
    'port'     => getenv('DB_PORT') ?: '3306',
    'dbname'   => getenv('DB_NAME') ?: 'medinova_hospital',
    'username' => getenv('DB_USER') ?: 'root',
    'password' => getenv('DB_PASS') !== false ? getenv('DB_PASS') : '',
    'charset'  => 'utf8mb4',
    'options'  => [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ]
];

$pdo = null;
$db_error = null;
$db_driver = 'mysql';

try {
    $dsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};dbname={$dbConfig['dbname']};charset={$dbConfig['charset']}";
    $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
} catch (PDOException $e) {
    try {
        // Attempt to create database if server connection works
        $serverDsn = "mysql:host={$dbConfig['host']};port={$dbConfig['port']};charset={$dbConfig['charset']}";
        $tempPdo = new PDO($serverDsn, $dbConfig['username'], $dbConfig['password'], [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
        $tempPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbConfig['dbname']}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
        $pdo = new PDO($dsn, $dbConfig['username'], $dbConfig['password'], $dbConfig['options']);
    } catch (Exception $subEx) {
        // Fallback SQLite database for seamless offline zero-dependency development
        $sqlitePath = __DIR__ . '/../data/medinova.sqlite';
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

function get_db() {
    global $pdo;
    return $pdo;
}
