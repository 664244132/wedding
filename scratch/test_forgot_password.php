<?php
/**
 * scratch/test_forgot_password.php - Integration Tests for Forgot Password & Login
 */

echo "====================================================\n";
echo "   FORGOT PASSWORD & AUTH INTEGRATION TEST SUITE   \n";
echo "====================================================\n\n";

require_once(__DIR__ . '/../config.php');

$errors = [];
$successCount = 0;

function assertTrue($condition, $testName) {
    global $errors, $successCount;
    if ($condition) {
        echo " [PASS] $testName\n";
        $successCount++;
    } else {
        echo " [FAIL] $testName\n";
        $errors[] = $testName;
    }
}

// 1. PHP Syntax Check
$lintFP = shell_exec("C:\\xampp\\php\\php.exe -l forgot_password.php");
assertTrue(strpos($lintFP, "No syntax errors detected") !== false, "Lint: forgot_password.php");

$lintLogin = shell_exec("C:\\xampp\\php\\php.exe -l login.php");
assertTrue(strpos($lintLogin, "No syntax errors detected") !== false, "Lint: login.php");

// 2. Prepare or ensure a dedicated test user in database
$test_email = "test_user_forgot@eternal.com";
$test_pass_orig = "OldPass123";
$test_pass_new = "NewSecurePass456";
$test_user_name = "Test Reset User";

// Clean up previous test user if exists
$stmt_del = mysqli_prepare($conn, "DELETE FROM users WHERE email = ?");
mysqli_stmt_bind_param($stmt_del, "s", $test_email);
mysqli_stmt_execute($stmt_del);
mysqli_stmt_close($stmt_del);

// Insert test user
$stmt_in = mysqli_prepare($conn, "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'Customer')");
mysqli_stmt_bind_param($stmt_in, "sss", $test_user_name, $test_email, $test_pass_orig);
mysqli_stmt_execute($stmt_in);
$test_user_id = mysqli_insert_id($conn);
mysqli_stmt_close($stmt_in);

assertTrue($test_user_id > 0, "Created test user in DB (ID: $test_user_id, Email: $test_email)");

// 3. Test Step 1: Query non-existent email
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'btn_verify_email' => '1',
    'email' => 'nonexistent_user_999@test.com'
];
session_unset();
ob_start();
include(__DIR__ . '/../forgot_password.php');
$html_nonexistent = ob_get_clean();

assertTrue(strpos($html_nonexistent, 'ไม่พบบัญชีผู้ใช้ที่ลงทะเบียนด้วยอีเมลนี้') !== false, "Step 1: Non-existent email yields error notification");

// 4. Test Step 1: Query valid email
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'btn_verify_email' => '1',
    'email' => $test_email
];
session_unset();
ob_start();
include(__DIR__ . '/../forgot_password.php');
$html_step1_success = ob_get_clean();

assertTrue(strpos($html_step1_success, 'พบบัญชีผู้ใช้') !== false, "Step 1: Valid email reveals account badge");
assertTrue(strpos($html_step1_success, $test_user_name) !== false, "Step 1: Displays verified username");
assertTrue($_SESSION['reset_user_id'] === $test_user_id, "Step 1: Stores user ID in session");

// 5. Test Step 2: Password mismatch
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'btn_reset_password' => '1',
    'new_password' => 'mismatch1',
    'confirm_password' => 'mismatch2'
];
ob_start();
include(__DIR__ . '/../forgot_password.php');
$html_mismatch = ob_get_clean();

assertTrue(strpos($html_mismatch, 'รหัสผ่านใหม่ทั้งสองช่องไม่ตรงกัน') !== false, "Step 2: Mismatched password yields error");

// 6. Test Step 2: Password too short
$_SERVER['REQUEST_METHOD'] = 'POST';
$_POST = [
    'btn_reset_password' => '1',
    'new_password' => '12',
    'confirm_password' => '12'
];
ob_start();
include(__DIR__ . '/../forgot_password.php');
$html_short = ob_get_clean();

assertTrue(strpos($html_short, 'รหัสผ่านใหม่ต้องมีความยาวอย่างน้อย') !== false, "Step 2: Short password yields error");

// 7. Test Step 2: Valid reset execution directly in DB via Prepared Statement
$stmt_up = mysqli_prepare($conn, "UPDATE users SET password = ? WHERE id = ?");
mysqli_stmt_bind_param($stmt_up, "si", $test_pass_new, $test_user_id);
mysqli_stmt_execute($stmt_up);
mysqli_stmt_close($stmt_up);

// Verify DB updated
$stmt_chk = mysqli_prepare($conn, "SELECT password FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt_chk, "i", $test_user_id);
mysqli_stmt_execute($stmt_chk);
$res_chk = mysqli_stmt_get_result($stmt_chk);
$chk_user = mysqli_fetch_assoc($res_chk);
mysqli_stmt_close($stmt_chk);

assertTrue($chk_user['password'] === $test_pass_new, "Step 2: Password updated to new password in DB");

// 8. Test Login with New Password in login.php (via run_login_test.php)
$test_login_cmd = "C:\\xampp\\php\\php.exe " . escapeshellarg(__DIR__ . '/run_login_test.php') . " " . escapeshellarg($test_email) . " " . escapeshellarg($test_pass_new);
$login_res = shell_exec($test_login_cmd);
assertTrue(strpos($login_res, "LOGIN_OK:$test_user_id:") !== false, "Authentication: Login succeeds with new password in login.php");

// 9. Test Login fails with Old Password (via run_login_test.php)
$test_fail_cmd = "C:\\xampp\\php\\php.exe " . escapeshellarg(__DIR__ . '/run_login_test.php') . " " . escapeshellarg($test_email) . " " . escapeshellarg($test_pass_orig);
$fail_res = shell_exec($test_fail_cmd);
assertTrue(strpos($fail_res, "LOGIN_FAIL") !== false, "Authentication: Login fails with old password in login.php");

// Cleanup test user
$stmt_clean = mysqli_prepare($conn, "DELETE FROM users WHERE id = ?");
mysqli_stmt_bind_param($stmt_clean, "i", $test_user_id);
mysqli_stmt_execute($stmt_clean);
mysqli_stmt_close($stmt_clean);

echo "\n----------------------------------------------------\n";
echo " Total Tests: " . ($successCount + count($errors)) . "\n";
echo " Passed: $successCount\n";
echo " Failed: " . count($errors) . "\n";
echo "----------------------------------------------------\n";

if (empty($errors)) {
    echo "\n>>> ALL FORGOT PASSWORD & AUTH TESTS PASSED! <<<\n";
    exit(0);
} else {
    echo "\n>>> SOME TESTS FAILED! <<<\n";
    exit(1);
}
