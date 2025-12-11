# TEST PAYMENT & SUBSCRIPTION FLOW

## System Overview
- User registers → Gets 1-day trial subscription automatically
- User selects package (daily/weekly/monthly) → Creates subscription record
- User uploads payment proof → Subscription automatically activated for selected duration
- No admin approval needed - immediate activation upon upload

## Database Requirements
- [ ] `subscription` table with columns: subscription_id, user_id, plan, start_date, end_date, status, created_at
- [ ] `payment` table with columns: payment_id, user_id, subscription_id, amount, proof_image, status, created_at
- [ ] `uploads/payment_proofs/` directory (created automatically if missing)

## Test Workflow

### Test 1: User Registration Creates Trial Subscription
**Steps:**
1. Go to Register page: `index.php?p=register`
2. Fill form with:
   - Name: Test User
   - Email: testuser@example.com
   - Password: testpass123
   - Confirm: testpass123
3. Click Register

**Expected Results:**
- User account created
- Automatic subscription created with:
  - plan = 'daily'
  - status = 'active'
  - start_date = today
  - end_date = today + 1 day
- User redirected to login/dashboard
- No errors in browser console or server logs

**Verification:**
```sql
SELECT * FROM subscription WHERE user_id = [testuser_id];
-- Should show 1 record with plan='daily', status='active'
```

### Test 2: User Selects Package from Payment Page
**Steps:**
1. Login as the user from Test 1
2. Go to: `index.php?p=payment`
3. Click on package card (e.g., "Paket Mingguan")
4. Verify modal dialog appears
5. Modal should show: "Paket WEEKLY berhasil dipilih!"

**Expected Results:**
- New subscription created with:
  - plan = 'weekly'
  - status = 'active'
  - start_date = today
  - end_date = today + 7 days
- Payment record created with:
  - status = 'pending'
  - amount = 50000
- Page reloads showing new subscription details

**Verification:**
```sql
SELECT s.*, p.* FROM subscription s
LEFT JOIN payment p ON s.subscription_id = p.subscription_id
WHERE s.user_id = [testuser_id]
ORDER BY s.created_at DESC;
-- Should show 2 subscriptions (trial + weekly)
-- Payment record should link to weekly subscription
```

### Test 3: Upload Payment Proof Activates Subscription
**Steps:**
1. Remain on payment page (after Test 2)
2. Select "Bank Transfer" payment method
3. Scroll to "Unggah Bukti Pembayaran" section
4. Click upload area
5. Select an image file (JPG/PNG, max 5MB)
6. Click "Kirim Bukti Pembayaran"

**Expected Results:**
- File uploaded successfully to `uploads/payment_proofs/`
- File name format: `payment_[userid]_[subid]_[timestamp].[ext]`
- Payment record updated:
  - proof_image = [filename]
  - status = 'approved'
- Subscription automatically activated:
  - status = 'active'
  - end_date = today + 7 days (for weekly plan)
- Success message shown: "✓ Bukti pembayaran berhasil diunggah dan langganan diaktifkan"
- Page reloads after 2 seconds

**Verification:**
```sql
SELECT * FROM payment WHERE user_id = [testuser_id] ORDER BY created_at DESC LIMIT 1;
-- Should show proof_image = filename, status='approved'

SELECT * FROM subscription WHERE user_id = [testuser_id] ORDER BY created_at DESC LIMIT 1;
-- Should show status='active', end_date=today+7days
```

### Test 4: User Dashboard Shows Active Subscription
**Steps:**
1. Login as test user (from Test 3)
2. Go to dashboard: `index.php?p=dashboard`
3. Look for subscription card/section

**Expected Results:**
- Subscription status displayed showing:
  - Plan: WEEKLY
  - Valid until: [end_date]
  - Status badge: "ACTIVE" (green)
- No "Subscribe now" button shown
- User can proceed to chat with counselors

**Verification:**
- Check that subscription priority logic works:
  1. First checks subscription table with valid dates
  2. Falls back to payment table if needed
  3. Shows correct plan and end_date

## Error Handling Tests

### Test 5: File Size Validation
**Steps:**
1. Create image > 5MB
2. Try to upload

**Expected Error:**
```
✗ File terlalu besar (maks 5MB)
```

### Test 6: Invalid File Type
**Steps:**
1. Try to upload .txt or .pdf file

**Expected Error:**
```
✗ Tipe file tidak valid (JPEG, PNG, GIF saja)
```

### Test 7: No File Selected
**Steps:**
1. Click submit without selecting file

**Expected Error:**
```
✗ File tidak valid atau subscription_id tidak ditemukan
```

### Test 8: Invalid Subscription ID
**Steps:**
1. Manually change subscription_id in form
2. Submit with non-existent ID

**Expected Error:**
```
✗ Subscription tidak ditemukan
```

## Plan Duration Calculations

### Expected End Dates by Plan
- **Daily**: TODAY + 1 day
- **Weekly**: TODAY + 7 days
- **Monthly**: TODAY + 30 days

**Test:**
- Create subscription with each plan
- Verify end_date calculation correct via SQL

## File Locations
- Form: [views/payments/payment_page.php](src/views/payments/payment_page.php)
- Handler: [controllers/handle_payment.php](src/controllers/handle_payment.php)
- Registration: [views/auth/register.php](src/views/auth/register.php)
- Dashboard: [views/dashboard/user_dashboard.php](src/views/dashboard/user_dashboard.php)
- Database migration: [database/mental_health_platform.sql](database/mental_health_platform.sql)

## Known Issues & Workarounds

### Issue: Directory not found
**Symptom:** "Gagal upload file" error
**Solution:** Create `/uploads/payment_proofs/` manually or ensure write permissions

### Issue: Subscription not showing
**Symptom:** "Subscribe now" button shown on active user
**Solution:** Check subscription table has valid end_date; verify user_id matches

### Issue: Payment not linking to subscription
**Symptom:** Payment record created but not updating subscription
**Solution:** Ensure subscription_id passed correctly in form, check bind_param types

## Success Criteria
- [ ] User registration creates trial subscription
- [ ] Package selection creates new subscription
- [ ] Payment upload updates payment record
- [ ] Payment upload activates subscription
- [ ] End dates calculated correctly for each plan
- [ ] File validation works (size, type)
- [ ] Error messages display correctly
- [ ] No SQL errors in server logs
- [ ] Dashboard shows correct active subscription
