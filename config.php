<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'toyverse_db');

// Site configuration
define('SITE_NAME', 'ToyVerse');
define('SITE_URL', 'http://localhost');

// Security settings
define('SESSION_TIMEOUT', 3600); // 1 hour in seconds

// Error reporting (set to 0 in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Timezone
date_default_timezone_set('Asia/Manila');
?>