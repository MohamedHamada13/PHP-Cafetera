<?php

// Get request URI
$uri = parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);

// Simple page routing
$pages = [
    "/" => __DIR__ . "/../app/views/user/home.php",
    "/login" => __DIR__ . "/../app/views/auth/login.php",
    "/logout" => __DIR__ . "/../app/views/auth/logout.php",
    "/admin/users" => __DIR__ . "/../app/views/admin/users.php",
    "/admin/add-user" => __DIR__ . "/../app/views/admin/add_user.php",
];

// Check if page exists
if (isset($pages[$uri])) {
    require_once $pages[$uri];
    exit();
}

// 404 Not Found
http_response_code(404);
echo "<h1>404 - Page Not Found</h1>";
echo "<p>Requested: " . htmlspecialchars($uri) . "</p>";
echo '<p><a href="/">Go Home</a></p>';
