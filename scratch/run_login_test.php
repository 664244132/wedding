<?php
// scratch/run_login_test.php
$email = $argv[1] ?? '';
$password = $argv[2] ?? '';

$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'login_btn' => '1',
    'email'     => $email,
    'password'  => $password
];

register_shutdown_function(function() {
    if (isset($_SESSION['user_id'])) {
        echo "LOGIN_OK:" . $_SESSION['user_id'] . ":" . ($_SESSION['username'] ?? '');
    } else {
        echo "LOGIN_FAIL";
    }
});

ob_start();
require_once(__DIR__ . '/../login.php');
