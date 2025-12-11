# Payment & Subscription System Implementation

## Summary

The Mental Health Platform now has a complete, automated payment and subscription system that:

1. **Auto-grants 1-day trial** on user registration
2. **Allows package selection** (Daily/Weekly/Monthly)
3. **Processes payment proofs** with automatic subscription activation
4. **No manual admin approval** needed — upload activates immediately
5. **Secure file validation** — MIME type + size checks

## What Changed

### New/Modified Files

#### 1. Registration (Auto Trial)
**File:** `src/views/auth/register.php`
- Added automatic subscription creation after user INSERT
- Plan: 'daily', Status: 'active'
- Duration: 24 hours from registration

**Code snippet:**
```php
// After INSERT users succeeds:
$trial_stmt = $conn->prepare("
    INSERT INTO subscription (user_id, plan, start_date, end_date, status, created_at)
    VALUES (?, 'daily', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'active', NOW())
");
$trial_stmt->bind_param("i", $user_id);
$trial_stmt->execute();
```

#### 2. Payment Page (Form + JavaScript)
**File:** `src/views/payments/payment_page.php`
- Updated form with proper action attribute
- Added JavaScript to handle form submission
- Real-time file upload feedback
- Success/error message display
- Auto-reload after 2 seconds on success

**Form structure:**
```html
<form id="proofForm" method="POST" action="index.php?p=handle_payment" enctype="multipart/form-data">
    <input type="hidden" name="action" value="upload_proof">
    <input type="hidden" name="subscription_id" value="<?= $subscription['subscription_id'] ?>">
    <input type="file" name="proof_image" accept="image/jpeg,image/png" required>
    <button type="submit">Kirim Bukti Pembayaran</button>
</form>
```

#### 3. Payment Handler (Backend)
**File:** `src/controllers/handle_payment.php`
- Fixed session handling (checks status before start)
- Two actions:
  - `create_subscription` — Create paid subscription + payment record
  - `upload_proof` — Validate file, upload, and auto-activate subscription

**Key features:**
```php
// Validates file:
- Size ≤ 5MB
- MIME type in [JPEG, PNG, GIF]
- Subscription ownership

// Automatically:
- Uploads to uploads/payment_proofs/
- Updates payment with proof_image + status='approved'
- Calculates end_date based on plan duration
- Activates subscription (status='active')
```

## User Flow

```
User Registration
    ↓
INSERT user → auto create trial subscription (1 day)
    ↓
User sees "Subscribe" button on dashboard
    ↓
Click "Subscribe" → Go to /payment
    ↓
Select package (Daily/Weekly/Monthly)
    ↓
Choose payment method (Bank Transfer/QRIS)
    ↓
Upload bank transfer receipt
    ↓
File validated & uploaded to /uploads/payment_proofs/
    ↓
Subscription IMMEDIATELY activated
    ↓
Page reloads → User can now chat with counselors
```

## Database Changes

No new tables needed. Uses existing:
- `subscription` — plan, status, start_date, end_date
- `payment` — amount, proof_image, status

**Queries affected:**
- `register.php` — INSERT subscription (trial)
- `payment_page.php` — SELECT subscription for display
- `handle_payment.php` — INSERT/UPDATE subscription and payment

## Testing

### Test 1: Registration Creates Trial
```bash
1. Go to /register
2. Create account
3. Check DB: SELECT * FROM subscription WHERE user_id = [new_id]
   → Should show plan='daily', status='active', end_date=tomorrow
```

### Test 2: Package Selection
```bash
1. Go to /payment
2. Click package card
3. See modal: "Paket WEEKLY berhasil dipilih"
4. Check DB: Should have 2 subscriptions (trial + weekly)
```

### Test 3: Payment Proof Upload
```bash
1. Still on /payment
2. Upload JPG/PNG image
3. Click "Kirim Bukti Pembayaran"
4. See success message
5. Check DB:
   - payment.proof_image = [filename]
   - payment.status = 'approved'
   - subscription.status = 'active' (for new plan)
   - subscription.end_date = today + 7 days (for weekly)
```

## API Endpoints

### Create Subscription
```
POST index.php?p=handle_payment
{
    "action": "create_subscription",
    "plan": "weekly",
    "price": 50000
}
```

Response:
```json
{
    "success": true,
    "message": "Subscription berhasil dibuat",
    "subscription_id": 12,
    "plan": "weekly",
    "end_date": "2024-01-08"
}
```

### Upload Payment Proof
```
POST index.php?p=handle_payment (multipart/form-data)
{
    "action": "upload_proof",
    "subscription_id": 12,
    "proof_image": [FILE]
}
```

Response:
```json
{
    "success": true,
    "message": "Bukti pembayaran berhasil diunggah dan langganan diaktifkan",
    "subscription_id": 12,
    "plan": "weekly",
    "end_date": "2024-01-08"
}
```

## Error Cases

| Error | Cause | Solution |
|-------|-------|----------|
| "File terlalu besar" | Image > 5MB | Compress/resize image |
| "Tipe file tidak valid" | Not JPEG/PNG/GIF | Use standard image format |
| "Subscription tidak ditemukan" | Wrong subscription_id | Use correct ID from form |
| "Unauthorized" | Not logged in | Login first |
| "Gagal upload file" | Directory not writable | Check `uploads/payment_proofs/` exists |

## Security Features

✅ **Implemented:**
- Session-based auth (logged in users only)
- Prepared statements (prevents SQL injection)
- MIME type validation (not just file extension)
- File size limit (5 MB)
- User ownership verification (can't upload for others)
- Unique filename generation (prevents overwrites)

## Price List

| Plan | Price | Duration |
|------|-------|----------|
| Daily | Rp 10,000 | 1 day |
| Weekly | Rp 50,000 | 7 days |
| Monthly | Rp 180,000 | 30 days |

## Documentation

- **API Docs:** [PAYMENT_SYSTEM_DOCS.md](PAYMENT_SYSTEM_DOCS.md)
- **Test Guide:** [TEST_PAYMENT_FLOW.md](TEST_PAYMENT_FLOW.md)
- **Project Info:** [PROJECT_MEMORY.md](PROJECT_MEMORY.md)

## Key Decisions

1. **Auto-activation:** Payment proof immediately activates subscription (no admin review)
   - Pros: Better UX, instant access
   - Cons: Can't reject fraudulent payments
   - Change: Update `handle_payment.php` lines 195-210 to set status='pending' instead

2. **Manual file upload:** Instead of payment gateway API
   - Pros: Works offline, simple implementation
   - Cons: Requires manual verification (currently auto)
   - Change: Add admin verification page later

3. **24-hour trial:** Gives users time to test before paying
   - Change: Modify `register.php` line 70 to adjust duration

## Deployment Checklist

- [ ] Create `uploads/payment_proofs/` directory
- [ ] Ensure directory is writable (chmod 755)
- [ ] Test file upload with sample image
- [ ] Verify database tables exist (subscription, payment)
- [ ] Test complete workflow: register → select package → upload proof
- [ ] Check subscription shows on dashboard after upload
- [ ] Verify no errors in browser console
- [ ] Check server logs for any PHP warnings

## Next Steps

1. **Optional:** Add email notifications for payment confirmation
2. **Optional:** Add admin approval screen for payments
3. **Optional:** Integrate real payment gateway (Stripe, Xendit, etc.)
4. **Optional:** Add subscription pause/resume functionality
5. **Optional:** Add refund mechanism

---

**Status:** ✅ Fully implemented and ready for testing
