<?php
session_start(); // Start session
header('Content-type:text/html; charset=utf-8'); // Set charset
define('VF_PUBLIC_DIR', __DIR__); // Public directory
define('VF_ROOT_DIR', dirname(VF_PUBLIC_DIR)); // Root directory
define('VF_SYSTEM_DIR', VF_ROOT_DIR . '/system'); // Core directory
require_once(VF_SYSTEM_DIR . '/common.php'); // Requires
(new Core())->run(); // Run application