# Implementation Plan: Packages & Gallery Images Enhancement

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Create and deploy high-quality pre-wedding images suitable for all 3 packages in `packages.php` and for all 11 missing bride-groom pre-wedding cards in `gallery.php`, updating database captions and code according to project markdowns.

**Architecture:** Automated media provisioning script that creates and saves curated high-resolution wedding photography to `img/packages/` and `uploads/gallery/`, paired with MySQL Prepared Statements updating `packages` and `gallery` records, and refactoring both frontend PHP views with explicit SQL columns, XSS output escaping, and responsive `onerror` fallbacks.

**Tech Stack:** PHP 8+ (Procedural MySQLi), MariaDB / MySQL (`wedding_db`), GD Library, HTML5 / Bootstrap 5.3.0, CSS.

## Global Constraints
- Strictly follow `markdowns/PHPCodingGuide.md` (Prepared statements, Universal XSS escaping with `htmlspecialchars($var, ENT_QUOTES, 'UTF-8')`).
- Strictly follow `markdowns/SQLCodingGuide.md` (Explicit SELECT columns, Parameterized queries).
- Strictly follow `markdowns/HTMLCodingGuide.md` (Responsive images with `img-fluid`, descriptive `alt`, `loading="lazy"`, `onerror` fallback).
- Strictly follow `markdowns/CSSCodingGuide.md` (Luxury design tokens, consistent theme).

---

### Task 1: Generate & Seed Images for Packages and Gallery

**Files:**
- Create: `scratch/seed_packages_gallery.php`
- Create images:
  - `img/packages/pkg_1774564008_5.webp` (Economy Garden Pre-Wedding)
  - `img/packages/pkg_1774564080_9.jpg` (Standard Classic Gown & Tuxedo)
  - `img/packages/pkg_1774564119_0.jpg` (Luxury Grand Suite Ballroom)
  - 11 gallery images in `uploads/gallery/` matching IDs 16, 15, 14, 13, 12, 11, 10, 9, 8, 6, 5

- [ ] **Step 1: Write seed script to download/generate high-res images and update database captions**
- [ ] **Step 2: Run seed script via PHP CLI**
- [ ] **Step 3: Verify image files exist on disk and captions updated in DB**
- [ ] **Step 4: Commit**

---

### Task 2: Refactor `packages.php` to Markdown Standards

**Files:**
- Modify: `packages.php`

- [ ] **Step 1: Update SQL query with Explicit SELECT columns**
- [ ] **Step 2: Add universal output escaping `htmlspecialchars()` and image `onerror` fallback**
- [ ] **Step 3: Check PHP syntax lint**
- [ ] **Step 4: Commit**

---

### Task 3: Refactor `gallery.php` to Markdown Standards

**Files:**
- Modify: `gallery.php`

- [ ] **Step 1: Update SQL query with Explicit SELECT columns**
- [ ] **Step 2: Add universal output escaping `htmlspecialchars()` and image `onerror` fallback**
- [ ] **Step 3: Check PHP syntax lint**
- [ ] **Step 4: Commit**

---

### Task 4: Comprehensive Verification & Testing

**Files:**
- Create/Run: `scratch/test_packages_gallery.php`

- [ ] **Step 1: Write verification script to test:**
  - All 3 package images load and display on `packages.php`
  - All 12 gallery images load and display with valid captions on `gallery.php`
  - No broken images, no PHP warnings, and valid HTML
- [ ] **Step 2: Run verification script**
- [ ] **Step 3: Commit and finalize**
