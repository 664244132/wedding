### Task 4: Full System Verification & End-to-End Testing

**Files:**
- Test script: `scratch/test_services.php`

**Interfaces:**
- Consumes: `services.php` output and `booking.php` parameters
- Produces: Verification report confirming all 10 outfits are present, security requirements are met, and booking redirect works

- [ ] **Step 1: Write integration verification script**

Create `scratch/test_services.php` that:
- Loads `services.php`
- Asserts that both service modals exist (`#modal24` and `#modal25`)
- Asserts that all 5 Thai outfits appear in `#modal24`
- Asserts that all 5 Modern Thai outfits appear in `#modal25`
- Asserts that no unescaped strings or SQL errors exist in the output
- Verifies that `booking.php?ajax_s_id=24` and `booking.php?ajax_s_id=25` return JSON with exactly 5 items each

- [ ] **Step 2: Run verification script**

Run: `C:\xampp\php\php.exe scratch/test_services.php`
Expected: All tests PASS

- [ ] **Step 3: Commit and cleanup scratch scripts**

```bash
git add scratch/
git commit -m "test: add integration test suite for services and verify all 10 outfits"
```
