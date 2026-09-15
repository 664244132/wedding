# Forgot Password Feature Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create and implement a fully working, secure, and luxury-styled Forgot Password feature in `forgot_password.php` and upgrade `login.php` to use Prepared Statements according to project markdowns.

**Architecture:** Two-step verification and reset on `forgot_password.php` using MySQLi Prepared Statements, session/state handling, XSS escaping, luxury wedding CSS, and seamless redirection to `login.php`.

**Tech Stack:** PHP 8+ (Procedural MySQLi), MariaDB / MySQL (`wedding_db`), HTML5 / Bootstrap 5.3.0, Vanilla JavaScript (ES6+), Custom CSS (`CSS/login.css`).

## Global Constraints
- Strictly follow `markdowns/PHPCodingGuide.md` (Prepared statements, Universal output escaping with `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`, 2-zone structure).
- Strictly follow `markdowns/SQLCodingGuide.md` (Explicit SELECT columns, Parameterized queries).
- Strictly follow `markdowns/HTMLCodingGuide.md` (Accessible labels with `for` and `id`, semantic structure).
- Strictly follow `markdowns/CSSCodingGuide.md` (Luxury design tokens, consistent theme with `login.php`).

---

### Task 1: Create `forgot_password.php`

**Files:**
- Create: `forgot_password.php`

**Interfaces:**
- Consumes: `users` table via `config.php`
- Produces: Working interactive password reset form

- [ ] **Step 1: Write `forgot_password.php` with 2-zone architecture and Prepared Statements**

Include:
- Safe session handling (`session_status() === PHP_SESSION_NONE && !headers_sent()`)
- Step 1: POST `verify_email`: checks email exists with prepared statement
- Step 2: POST `reset_password`: checks password match and length, updates `users` with prepared statement
- Cancel / Reset state button to start over
- Universal XSS escaping
- Beautiful Luxury Wedding UI matching `CSS/login.css`

- [ ] **Step 2: Check PHP syntax lint**

Run: `C:\xampp\php\php.exe -l forgot_password.php`
Expected: `No syntax errors detected in forgot_password.php`

- [ ] **Step 3: Commit**

```bash
git add forgot_password.php
git commit -m "feat(auth): create forgot_password.php with two-zone architecture and prepared statements"
```

---

### Task 2: Refactor `login.php` with MySQLi Prepared Statements

**Files:**
- Modify: `login.php:12-43`

**Interfaces:**
- Consumes: `users` table
- Produces: Safe authentication using MySQLi Prepared Statement

- [ ] **Step 1: Replace raw SQL query in `login.php` with Prepared Statement**

Change:
`SELECT * FROM users WHERE email = '$email' AND password = '$password' LIMIT 1`
To:
Prepared statement with explicit columns:
`SELECT id, username, password, role FROM users WHERE email = ? LIMIT 1`
And verify password securely (`password_verify` or plain text match for backward compatibility).

- [ ] **Step 2: Check PHP syntax lint**

Run: `C:\xampp\php\php.exe -l login.php`
Expected: `No syntax errors detected in login.php`

- [ ] **Step 3: Commit**

```bash
git add login.php
git commit -m "refactor(auth): upgrade login.php to use MySQLi Prepared Statements"
```

---

### Task 3: Comprehensive Verification & Testing

**Files:**
- Create/Run: `scratch/test_forgot_password.php`

**Interfaces:**
- Consumes: `forgot_password.php`, `login.php`, and `users` table
- Produces: Test verification report

- [ ] **Step 1: Write test script to verify:**
  - Non-existent email yields error message
  - Valid email transitions to Step 2
  - Mismatched passwords yield error message
  - Valid password reset updates database
  - User can log in with new password
- [ ] **Step 2: Run verification script**

Run: `C:\xampp\php\php.exe scratch/test_forgot_password.php`
Expected: All tests PASS

- [ ] **Step 3: Commit and finalize**

```bash
git add scratch/
git commit -m "test(auth): add integration test suite for forgot password and login"
```
