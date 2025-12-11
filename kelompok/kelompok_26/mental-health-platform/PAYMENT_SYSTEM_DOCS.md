# Payment & Subscription System Documentation

## Overview
This document describes the automatic payment and subscription system for Astral Psychologist platform.

## System Flow

### 1. Registration → Automatic Trial Subscription

**File:** [src/views/auth/register.php](src/views/auth/register.php)

**Process:**
```
User submits registration form
    ↓
INSERT user into database
    ↓
AUTO: Create subscription record
    - plan = 'daily'
    - status = 'active'
    - start_date = TODAY
    - end_date = TODAY + 1 DAY
    - created_at = NOW()
    ↓
User gets 24-hour trial access
```

**Code Location:** Lines ~67-90 in register.php

**Database Impact:**
```sql
INSERT INTO subscription (user_id, plan, start_date, end_date, status, created_at)
VALUES (?, 'daily', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 1 DAY), 'active', NOW())
```

---

### 2. Package Selection → Create Paid Subscription

**File:** [src/views/payments/payment_page.php](src/views/payments/payment_page.php) (JavaScript)

**Process:**
```
User clicks package card (Daily/Weekly/Monthly)
    ↓
selectPackage() function sends AJAX request
    ↓
POST to index.php?p=handle_payment
    action: 'create_subscription'
    plan: 'daily' | 'weekly' | 'monthly'
    price: 10000 | 50000 | 180000
    ↓
handle_payment.php processes request
    ↓
Check for existing active subscription
    ↓
Create new subscription (or extend existing)
    ↓
Create payment record with status='pending'
    ↓
Return success JSON with subscription_id
    ↓
Page reloads showing new subscription
```

**Handler File:** [src/controllers/handle_payment.php](src/controllers/handle_payment.php) (Lines 22-107)

**Database Impact:**
```sql
-- Create subscription
INSERT INTO subscription (user_id, plan, start_date, end_date, status, created_at)
VALUES (?, ?, CURDATE(), DATE_ADD(CURDATE(), INTERVAL ? DAY), 'active', NOW())

-- Create payment record
INSERT INTO payment (user_id, subscription_id, amount, status, created_at)
VALUES (?, ?, ?, 'pending', NOW())
```

**Plan Durations:**
- daily: 1 day
- weekly: 7 days
- monthly: 30 days

---

### 3. Payment Proof Upload → Automatic Activation

**Files:**
- Form: [src/views/payments/payment_page.php](src/views/payments/payment_page.php) (HTML form)
- Handler: [src/controllers/handle_payment.php](src/controllers/handle_payment.php) (Lines 108-200+)

**Process:**
```
User selects payment method (Bank Transfer/QRIS)
    ↓
User uploads payment proof image
    ↓
Form submits to index.php?p=handle_payment
    method: POST
    action: 'upload_proof'
    subscription_id: [from hidden field]
    proof_image: [FILE from input]
    ↓
handle_payment.php:
  1. Validate file size ≤ 5MB
  2. Validate MIME type (JPEG/PNG/GIF)
  3. Verify subscription belongs to user
  4. Upload to uploads/payment_proofs/
  5. Update payment record:
     - proof_image = filename
     - status = 'approved'
  6. AUTO-ACTIVATE subscription:
     - status = 'active'
     - end_date = TODAY + plan_duration
  ↓
Return success JSON
    ↓
JavaScript shows success message
    ↓
Page reloads after 2 seconds
    ↓
User can now access chat with counselors
```

**Handler File:** [src/controllers/handle_payment.php](src/controllers/handle_payment.php) (Lines 108+)

**File Upload Details:**
- Directory: `uploads/payment_proofs/`
- Naming: `payment_[user_id]_[subscription_id]_[timestamp].[ext]`
- Example: `payment_5_12_1704067200.jpg`
- Max size: 5 MB
- Allowed types: JPEG, PNG, GIF

**Database Impact:**
```sql
-- Update payment record
UPDATE payment 
SET proof_image = ?, status = 'approved'
WHERE user_id = ? AND subscription_id = ?

-- Update subscription (AUTO-ACTIVATE)
UPDATE subscription 
SET status = 'active', end_date = DATE_ADD(CURDATE(), INTERVAL ? DAY)
WHERE subscription_id = ? AND user_id = ?
```

---

## Database Schema

### subscription table
```sql
CREATE TABLE subscription (
    subscription_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    plan ENUM('daily', 'weekly', 'monthly', 'trial') DEFAULT 'daily',
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    status ENUM('active', 'expired') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);
```

### payment table
```sql
CREATE TABLE payment (
    payment_id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    subscription_id INT,
    amount INT,
    proof_image VARCHAR(255),
    status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (subscription_id) REFERENCES subscription(subscription_id)
);
```

---

## API Endpoints

### 1. Create Subscription
**Endpoint:** `POST index.php?p=handle_payment`

**Request:**
```json
{
    "action": "create_subscription",
    "plan": "weekly",
    "price": 50000
}
```

**Response (Success):**
```json
{
    "success": true,
    "message": "Subscription berhasil dibuat",
    "subscription_id": 12,
    "plan": "weekly",
    "start_date": "2024-01-01",
    "end_date": "2024-01-08"
}
```

**Response (Error):**
```json
{
    "success": false,
    "message": "Plan tidak valid"
}
```

### 2. Upload Payment Proof
**Endpoint:** `POST index.php?p=handle_payment`

**Request:**
- `action`: "upload_proof"
- `subscription_id`: [integer]
- `proof_image`: [FILE]

**Response (Success):**
```json
{
    "success": true,
    "message": "Bukti pembayaran berhasil diunggah dan langganan diaktifkan",
    "subscription_id": 12,
    "plan": "weekly",
    "end_date": "2024-01-08"
}
```

**Response (Error):**
```json
{
    "success": false,
    "message": "File terlalu besar (maks 5MB)"
}
```

---

## Frontend Integration

### Payment Page Form
**File:** [src/views/payments/payment_page.php](src/views/payments/payment_page.php)

**Form Structure:**
```html
<form id="proofForm" method="POST" action="index.php?p=handle_payment" enctype="multipart/form-data">
    <input type="hidden" name="action" value="upload_proof">
    <input type="hidden" name="subscription_id" value="<?= $subscription['subscription_id'] ?>">
    <input type="file" name="proof_image" accept="image/jpeg,image/png" required>
    <button type="submit">Kirim Bukti Pembayaran</button>
</form>
```

**JavaScript Handlers:**
- `selectPackage(plan, price)`: Send package selection to server
- `selectPaymentMethod(method)`: Show/hide payment options
- Form submit handler: Upload proof and handle response

---

## Subscription Verification

**File:** [src/views/dashboard/user_dashboard.php](src/views/dashboard/user_dashboard.php)

**Logic Priority:**
1. Check `subscription` table for active record with valid end_date
2. Fall back to `payment` table if no subscription
3. Normalize field names for consistency
4. Display plan, status, and end_date

**Query:**
```sql
SELECT * FROM subscription 
WHERE user_id = ? AND status = 'active' AND end_date >= CURDATE()
LIMIT 1
```

---

## Error Handling

### Common Errors & Solutions

**"Unauthorized"**
- Cause: User not logged in (session not set)
- Solution: Redirect to login page

**"Plan tidak valid"**
- Cause: Submitted plan not in ['daily', 'weekly', 'monthly']
- Solution: Validate on frontend before submit

**"File terlalu besar"**
- Cause: Image > 5MB
- Solution: Compress image or select smaller file

**"Tipe file tidak valid"**
- Cause: File is not JPEG, PNG, or GIF
- Solution: Use standard image formats only

**"Subscription tidak ditemukan"**
- Cause: subscription_id doesn't exist or doesn't belong to user
- Solution: Ensure correct subscription_id in form

**"Gagal upload file"**
- Cause: Directory not writable or doesn't exist
- Solution: Create `uploads/payment_proofs/` directory with 755 permissions

---

## Testing

See [TEST_PAYMENT_FLOW.md](TEST_PAYMENT_FLOW.md) for complete test suite.

**Quick Test:**
1. Register new user → Check trial subscription created
2. Select package → Check payment record created
3. Upload proof → Check subscription activated and end_date updated

---

## Configuration

### File Paths
- Upload directory: `uploads/payment_proofs/`
- Handler: `src/controllers/handle_payment.php`
- Registration: `src/views/auth/register.php`
- Payment page: `src/views/payments/payment_page.php`

### Constants
- Max file size: 5 MB (5 * 1024 * 1024 bytes)
- Allowed MIME types: image/jpeg, image/png, image/gif
- Valid plans: daily, weekly, monthly

### Price List
- Daily: Rp 10,000
- Weekly: Rp 50,000
- Monthly: Rp 180,000

---

## Security Notes

1. **Session Verification:** All handlers check `$_SESSION['user']` before processing
2. **File Validation:** MIME type checked via `finfo_file()`, not just extension
3. **Ownership Verification:** Subscription verified to belong to authenticated user
4. **Parameter Binding:** All queries use prepared statements to prevent SQL injection
5. **Upload Directory:** Files stored outside public web root would be more secure

---

## Future Enhancements

1. Manual admin approval before auto-activation (change status to 'pending_approval')
2. Refund mechanism for unused subscription days
3. Automatic payment reminders for expiring subscriptions
4. Multiple payment gateway integration (e.g., payment gateway APIs)
5. Subscription pause/resume functionality
6. Bulk admin actions for payment management
