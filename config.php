<?php
define('ROOT_PATH', __DIR__);

// Detect Docker environment - if DB_HOST env var is set, we're in Docker
// In Docker, the app is at document root, so SERVER_ROOT should be empty
// For local development (XAMPP, etc.), keep the subfolder path
if (getenv('DB_HOST')) {
    define('SERVER_ROOT', '');
} else {
    define('SERVER_ROOT', '/UoJ_AMS');
}
?>