<?php
/**
 * CookBook application configuration.
 * Plain PHP project for DWIN309 – no framework is used.
 */
declare(strict_types=1);

const APP_NAME = 'CookBook';
const APP_ENV = 'development';
const DB_HOST = '127.0.0.1';
const DB_PORT = 3306;
const DB_NAME = 'cookbook';
const DB_USER = 'root';
const DB_PASS = '';

// Session hardening suitable for a local prototype.
if (session_status() !== PHP_SESSION_ACTIVE) {
    ini_set('session.use_only_cookies', '1');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    session_start();
}

// Detect the project URL automatically even if the folder name is changed.
$projectFolder = basename(dirname(__DIR__));
$scriptName = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$needles = ['/' . $projectFolder, '/' . rawurlencode($projectFolder)];
$appBase = '';
foreach ($needles as $needle) {
    $position = strpos($scriptName, $needle);
    if ($position !== false) {
        $appBase = substr($scriptName, 0, $position + strlen($needle));
        break;
    }
}
define('APP_BASE', $appBase);

date_default_timezone_set('Australia/Sydney');
