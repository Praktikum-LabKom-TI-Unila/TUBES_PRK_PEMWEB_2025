# Quick Start Guide — Payment System Testing

## Pre-Test Checklist

- [ ] Database imported with schema (`mental_health_platform.sql`)
- [ ] `src/config/database.php` has correct DB credentials
- [ ] `uploads/payment_proofs/` directory exists (just created)
- [ ] Web server pointing to `src/` folder
- [ ] Browser can access `http://localhost/index.php`

## Test Sequence

### Phase 1: Trial Subscription (Registration)

**Goal:** Verify auto-trial creation on register

**Step 1.1: Go to Register**
```
URL: http://localhost/index.php?p=register
```

**Step 1.2: Create Test Account**
```
Form:
  Name: John Doe
  Email: john@example.com
  Password: test12345
  Confirm: test12345
  
Button: Register
```

**Step 1.3: Verify in Database**
```sql
-- Check trial subscription created
SELECT s.* FROM subscription s
WHERE s.user_id = (SELECT user_id FROM users WHERE email='john@example.com')
ORDER BY s.created_at DESC;

-- Expected:
-- subscription_id: [auto]
-- user_id: [new user id]
-- plan: 'daily'
-- start_date: [today]
-- end_date: [tomorrow]
-- status: 'active'
```

**Expected Result:** ✅ User created with 1-day trial subscription

**If Failed:**
- Check PHP error logs for SQL errors
- Verify subscription table structure: `DESCRIBE subscription;`
- Check if INSERT INTO subscription executed in register.php

---

### Phase 2: Package Selection

**Goal:** Verify subscription creation when user selects package

**Step 2.1: Login as Test User**
```
URL: http://localhost/index.php?p=login
Username: john@example.com
Password: test12345
```

**Step 2.2: Go to Payment Page**
```
URL: http://localhost/index.php?p=payment
```

**Step 2.3: Select Weekly Package**
```
Action: Click on "Paket Mingguan" card (Rp 50,000)

Expected:
  - Modal dialog appears
  - Shows: "Anda telah memilih WEEKLY..."
  - Then: "Paket WEEKLY berhasil dipilih!"
  - Modal closes after 2 seconds
  - Page reloads
```

**Step 2.4: Verify in Database**
```sql
-- Check subscription created
SELECT s.*, p.amount FROM subscription s
LEFT JOIN payment p ON s.subscription_id = p.subscription_id
WHERE s.user_id = (SELECT user_id FROM users WHERE email='john@example.com')
ORDER BY s.created_at DESC;

-- Expected 2 rows:
-- 1. plan='daily' (trial from registration)
-- 2. plan='weekly', status='active', end_date=today+7days
--    with linked payment record: amount=50000
```

**Expected Result:** ✅ Weekly subscription and payment record created

**If Failed:**
- Check browser console for AJAX errors (F12 > Console)
- Check server logs for handle_payment.php errors
- Verify selectPackage() function in payment_page.php
- Check if fetch URL is correct

---

### Phase 3: Payment Proof Upload

**Goal:** Verify payment proof upload auto-activates subscription

**Step 3.1: Remain on Payment Page**
```
Should be at: http://localhost/index.php?p=payment
(After refresh in Step 2.4)
```

**Step 3.2: Select Bank Transfer Method**
```
Action: Click on "🏦 Bank Transfer" radio button

Expected:
  - Transfer details show (BCA, account number, etc.)
  - "Unggah Bukti Pembayaran" section appears below
```

**Step 3.3: Prepare Test Image**
```
Need: Any JPG or PNG image
Options:
  - Take screenshot and save as JPG
  - Use existing image from computer
  - Size: < 5MB
  
Don't use: PDF, Word, TXT files
```

**Step 3.4: Upload Payment Proof**
```
Action:
  1. Click file upload area (or drag & drop)
  2. Select your test image
  3. Click "Kirim Bukti Pembayaran" button
  
Expected progress:
  - Button shows "⏳ Memproses..."
  - 1-2 seconds processing
  - Success message appears:
    "✓ Bukti pembayaran berhasil diunggah dan langganan diaktifkan
     Plan: weekly | End: 2024-01-08"
  - Page reloads automatically after 2 seconds
```

**Step 3.5: Verify File Uploaded**
```
Check disk:
  Path: src/uploads/payment_proofs/
  Expected file: payment_[userid]_[subid]_[timestamp].jpg
  
  Example: payment_5_12_1704067200.jpg
```

**Step 3.6: Verify in Database**
```sql
-- Check payment record
SELECT p.* FROM payment p
WHERE p.user_id = (SELECT user_id FROM users WHERE email='john@example.com')
ORDER BY p.created_at DESC LIMIT 1;

-- Expected:
-- payment_id: [auto]
-- subscription_id: [weekly subscription id]
-- amount: 50000
-- proof_image: payment_[userid]_[subid]_[timestamp].jpg
-- status: 'approved' (NOT 'pending')

-- Check subscription is active
SELECT s.* FROM subscription s
WHERE s.subscription_id = [payment subscription_id];

-- Expected:
-- status: 'active'
-- end_date: today + 7 days
```

**Expected Result:** ✅ Payment proof uploaded, subscription activated

**If Failed:**
- Check browser console for form submission errors
- Check server logs for handle_payment.php 'upload_proof' action
- Verify file was saved to uploads/payment_proofs/
- Check file permissions on uploads/ directory

---

### Phase 4: Dashboard Verification

**Goal:** Verify dashboard shows correct active subscription

**Step 4.1: Go to Dashboard**
```
URL: http://localhost/index.php?p=dashboard
```

**Step 4.2: Check Subscription Display**
```
Expected to see:
  - Subscription status card/section showing:
    ✓ Plan: WEEKLY
    ✓ Status: ACTIVE (green badge)
    ✓ Valid until: [end_date from DB]
  
  NOT expected:
    ✗ "Subscribe now" button
    ✗ Trial expiration warning
    ✗ "Payment pending" message
```

**Step 4.3: Verify Logic**
```
Dashboard should prioritize:
1. Check subscription table first (active with valid end_date)
2. If not found, check payment table
3. If not found, show "Subscribe now" button

Your test should hit #1 (subscription table)
```

**Expected Result:** ✅ Dashboard shows active weekly subscription until correct date

**If Failed:**
- Check user_dashboard.php subscription detection logic
- Verify SQL query returns subscription record
- Check if end_date is correctly formatted for display

---

## Error Case Testing

### Test E1: File Size Validation
```
File: JPG image > 5MB
Expected Error: "✗ File terlalu besar (maks 5MB)"

Code: handle_payment.php line ~145
  if ($file_size > 5 * 1024 * 1024)
```

### Test E2: File Type Validation
```
File: document.pdf or file.txt
Expected Error: "✗ Tipe file tidak valid (JPEG, PNG, GIF saja)"

Code: handle_payment.php line ~152
  $allowed_types = ['image/jpeg', 'image/png', 'image/gif']
```

### Test E3: No File Selected
```
Action: Click submit without selecting file
Expected Error: "✗ File tidak valid atau subscription_id tidak ditemukan"

Code: handle_payment.php line ~138
  if ($_FILES['proof_image']['error'] !== UPLOAD_ERR_OK)
```

### Test E4: Invalid Subscription ID
```
Action: Edit form HTML, change subscription_id to 9999
Expected Error: "✗ Subscription tidak ditemukan"

Code: handle_payment.php line ~165
  Verify query should return 0 rows
```

---

## Data Validation Checklist

After completing Phase 1-4, verify in database:

```sql
-- Total records created
SELECT COUNT(*) as user_count FROM users WHERE email='john@example.com';
-- Should be: 1

SELECT COUNT(*) as subscription_count FROM subscription 
WHERE user_id = (SELECT user_id FROM users WHERE email='john@example.com');
-- Should be: 2 (trial + weekly)

SELECT COUNT(*) as payment_count FROM payment
WHERE user_id = (SELECT user_id FROM users WHERE email='john@example.com');
-- Should be: 1

-- Verify date calculations
SELECT 
  s.plan,
  s.status,
  s.start_date,
  s.end_date,
  DATEDIFF(s.end_date, s.start_date) as duration_days
FROM subscription s
WHERE s.user_id = (SELECT user_id FROM users WHERE email='john@example.com')
ORDER BY s.created_at;

-- Expected:
-- Row 1: plan=daily, duration=1 day
-- Row 2: plan=weekly, duration=7 days
```

---

## Troubleshooting Guide

### Symptom: Register succeeds but no subscription created

**Check:**
1. Is `subscription` table defined?
   ```sql
   DESCRIBE subscription;
   ```

2. Is INSERT INTO subscription code in register.php?
   ```grep
   grep -n "INSERT INTO subscription" src/views/auth/register.php
   ```

3. Check PHP error log for SQL errors

**Fix:**
- Verify subscription table schema in database
- Check if register.php has subscription creation code (should be after user INSERT)
- Test prepared statement manually in phpMyAdmin

---

### Symptom: Package selection works but no subscription created

**Check:**
1. Check browser console (F12 > Console) for AJAX errors
2. Check Network tab (F12 > Network) for failed requests
3. Check server error logs

**Fix:**
- Verify handle_payment.php exists and is accessible
- Check if POST data is being sent correctly
- Test action=create_subscription manually

---

### Symptom: File upload fails with "Gagal upload file"

**Check:**
1. Does `uploads/payment_proofs/` directory exist?
   ```bash
   ls -la uploads/payment_proofs/
   ```

2. Is directory writable?
   ```bash
   touch uploads/payment_proofs/test.txt
   ```

3. Check file permissions
   ```bash
   ls -la uploads/
   ```

**Fix:**
- Create directory: `mkdir -p uploads/payment_proofs`
- Set permissions: `chmod 755 uploads/payment_proofs`
- Check if PHP process can write (usually `www-data` or `apache`)

---

### Symptom: Subscription not showing on dashboard

**Check:**
1. Verify subscription record exists in DB
   ```sql
   SELECT * FROM subscription WHERE user_id = 5;
   ```

2. Check if status='active' and end_date >= TODAY
   ```sql
   SELECT s.*, (s.end_date >= CURDATE()) as is_valid FROM subscription s WHERE user_id = 5;
   ```

3. Check user_dashboard.php subscription detection code

**Fix:**
- Ensure subscription status was updated by handle_payment.php
- Verify end_date calculation is correct (today + days)
- Check if dashboard is querying correct user_id from session

---

## Success Criteria

✅ **All tests pass if:**

1. **Phase 1:** User registers → trial subscription created (1 day)
2. **Phase 2:** User selects package → subscription + payment created
3. **Phase 3:** User uploads proof → payment approved, subscription activated
4. **Phase 4:** Dashboard shows active subscription with correct end_date
5. **Phase E:** Error messages display correctly for invalid inputs

✅ **Database integrity:**
- No orphaned payment records (all have subscription_id)
- All subscriptions linked to valid users
- End dates calculated correctly (daily=1, weekly=7, monthly=30)
- Status transitions: pending → approved → active

✅ **File system:**
- All proof images uploaded to correct directory
- Filenames match pattern: `payment_[userid]_[subid]_[timestamp].[ext]`
- Files are actual images (not corrupted)

---

## Performance Notes

Expected timings:
- Package selection: < 2 seconds
- File upload: < 5 seconds (depending on file size & network)
- Page reload: < 1 second
- Dashboard load: < 1 second

If any operation takes > 10 seconds, check:
- Database connection stability
- Network speed
- Server performance (CPU/memory)
- Disk I/O (file writing)

---

## Next Steps After Testing

Once all tests pass:
1. Clean up test data: `DELETE FROM users WHERE email='john@example.com';`
2. Test with multiple users to ensure data isolation
3. Test subscription expiry logic (create test with past end_date)
4. Consider adding email notifications for payment confirmation
5. Plan admin approval workflow (if auto-approval not desired)

**Estimated testing time: 15-20 minutes for full cycle**
