# Mental Health Platform — Project Snapshot

This file is an automatically generated snapshot and documentation index for the `mental-health-platform` project (persisted in repo by assistant). It summarizes the project structure, main components, and quick instructions for running or viewing the generated documentation site.

## Project Summary
- **Project:** Mental Health Platform (PHP) — "Astral Psychologist"
- **Location:** `kelompok/kelompok_26/mental-health-platform`
- **Type:** PHP web application (server-side, LAMP stack compatible)
- **Status:** Core features complete, payment system fully integrated

## Current Implementation Status

### ✅ Fully Implemented
1. **User Authentication** — Login/Register with automatic trial subscription
2. **Counselor Matching** — Algorithm respects both user and counselor preferences with enum-based scoring
3. **Bio/Profile Persistence** — User and counselor profile data saves correctly
4. **Chat System** — Real-time chat between users and counselors with preference descriptions
5. **Survey System** — User survey answers drive matching algorithm
6. **Dashboard** — Shows subscription status with correct plan and expiry date
7. **Payment System:**
   - Auto trial subscription (1 day) on registration
   - Package selection creates new subscription record
   - Payment proof upload with automatic activation
   - Immediate subscription activation (no manual approval)
   - File validation (size ≤5MB, MIME type: JPEG/PNG/GIF)

### 📊 Plan Pricing & Duration
- **Daily:** Rp 10,000 → 1 day access
- **Weekly:** Rp 50,000 → 7 days access
- **Monthly:** Rp 180,000 → 30 days access

## Main Functionality

### Core Features
- **Authentication** — User login/register with role-based access (user/konselor/admin)
- **Counselor Matching** — Algorithm matches users to counselors based on:
  - Communication style preference (Supportive/Guiding/Balanced)
  - Approach style preference (Oriented/Directive/Balanced)
  - Counselor rating and experience bonuses
- **Real-time Chat** — User-counselor communication with chat history
- **Survey System** — User preference assessment before matching
- **Subscription Management** — Trial period → paid packages → automatic activation
- **Payment Processing** — Bank transfer proof upload with automatic verification

## Database Schema (Key Tables)
```
users                — user accounts (user_id, email, password, role, profile_picture)
konselor             — counselor profiles (konselor_id, name, bio, rating, experience_years)
konselor_profile     — counselor preferences (communication_style, approach_style)
subscription         — subscription plans (plan, start_date, end_date, status)
payment              — payment records (amount, proof_image, status)
user_survey          — user preference answers (q1-q4 responses)
chat_session         — chat history between users and counselors
```

## File Structure
```
src/
├── index.php                           — main entry point with routing
├── config/database.php                 — MySQL connection
├── controllers/
│   ├── AuthController.php              — user authentication logic
│   ├── handle_auth.php                 — auth form processing
│   ├── handle_payment.php              — subscription & payment handling
│   ├── handle_konselor.php             — counselor profile updates
│   ├── handle_chat.php                 — chat message processing
│   └── handle_session.php              — session management
├── models/
│   ├── User.php                        — user data model
│   ├── Payment.php                     — payment data model
│   ├── Subscription.php                — subscription data model
│   └── [other models for konselor, chat, etc.]
└── views/
    ├── auth/login.php, register.php    — authentication pages
    ├── payments/payment_page.php       — subscription & payment page
    ├── dashboard/
    │   ├── user_dashboard.php          — user home (shows subscription & stats)
    │   ├── konselor_dashboard.php      — counselor home
    │   └── admin_dashboard.php         — admin panel
    ├── chat/
    │   ├── chat_room.php               — user chat interface
    │   └── konselor_chat.php           — counselor chat interface
    ├── matching/match_result.php       — counselor recommendations
    └── profile/user_profile.php        — user profile editor

database/
├── mental_health_platform.sql          — complete schema & initial data
├── create_activity_log.sql             — activity logging table
└── add_admin.sql                       — admin user setup
```

## Implementation Details

### 1. Trial Subscription (Auto on Registration)
**File:** `src/views/auth/register.php` (lines ~67-90)

When user registers successfully:
- Automatic subscription record created
- Plan: 'daily', Status: 'active'
- Start date: TODAY, End date: TODAY + 1 day
- User gets 24-hour trial to test platform

### 2. Package Selection (Create Paid Subscription)
**File:** `src/views/payments/payment_page.php` (JavaScript function `selectPackage`)

User clicks package card:
- AJAX request to `index.php?p=handle_payment`
- Action: 'create_subscription'
- Server creates subscription & payment records
- Payment status: 'pending' (waiting for proof)

**Handler:** `src/controllers/handle_payment.php` (lines 22-107)

### 3. Payment Proof Upload (Auto-Activation)
**File:** `src/views/payments/payment_page.php` (HTML form + JavaScript)

User uploads bank transfer receipt:
- Form POSTs to `index.php?p=handle_payment`
- Action: 'upload_proof'
- Server validates file (size, MIME type)
- Uploads to `uploads/payment_proofs/`
- **IMMEDIATELY activates subscription:**
  - Sets status='active'
  - Calculates end_date based on plan duration
- Updates payment status='approved'
- Returns success JSON

**Handler:** `src/controllers/handle_payment.php` (lines 108-200+)

### 4. Subscription Display (Dashboard)
**File:** `src/views/dashboard/user_dashboard.php`

Shows subscription info with priority:
1. Check `subscription` table (active with valid end_date)
2. Fall back to `payment` table if needed
3. Display: Plan name, active status, expiry date

## Testing

**Test Suite:** See `TEST_PAYMENT_FLOW.md` for complete testing guide

Quick verification:
1. Register → Check trial subscription created in DB
2. Select package → Verify payment record created
3. Upload proof → Confirm subscription activated & end_date updated

## How to Run Locally

1. **Setup PHP Environment**
   - Install Laragon, XAMPP, or local PHP + MySQL

2. **Import Database**
   ```bash
   mysql -u root -p mental_health_platform < database/mental_health_platform.sql
   ```

3. **Configure Connection**
   - Edit `src/config/database.php` with database credentials

4. **Start Server**
   - Point web server root to `src/`
   - Visit `http://localhost/index.php` or configure virtual host

5. **Test Payment Flow**
   - Create test account (auto trial subscription)
   - Select package from payment page
   - Upload test image as payment proof
   - Verify subscription activated in dashboard

## Key Technical Details

### Session Management
- `index.php` starts session at top
- All handlers check `session_status()` before calling session_start()
- User object stored in `$_SESSION['user']` with fields: user_id, name, email, role

### Database Transactions
- All critical operations use prepared statements
- Parameter binding prevents SQL injection
- Files validated before upload (MIME type via `finfo_file()`)

### File Upload Security
- Max file size: 5 MB
- Allowed types: JPEG, PNG, GIF (validated via MIME type)
- Stored in: `uploads/payment_proofs/`
- Naming: `payment_[userid]_[subid]_[timestamp].[ext]`

### Response Format
- All API endpoints return JSON
- Format: `{"success": true|false, "message": "...", "data": {...}}`
- HTTP status codes: 200 (ok), 401 (unauthorized), 400 (bad request)

## Security Notes

✅ **Implemented:**
- Session-based authentication
- Prepared statements (SQL injection prevention)
- MIME type validation (not just extension)
- User ownership verification
- File size limits

⚠️ **Considerations:**
- Upload directory accessible via web (could move outside public root)
- No rate limiting on payment endpoints
- No CSRF tokens (can be added if needed)
- Password hashing method not specified (should use `password_hash()`)

## Documentation Files

- `PAYMENT_SYSTEM_DOCS.md` — Complete payment API documentation
- `TEST_PAYMENT_FLOW.md` — Full testing guide with expected results
- `PROJECT_MEMORY.md` — This file (project overview)

## Next Steps (Future Enhancement)

1. Manual admin approval option for payments
2. Automatic refund mechanism
3. Subscription pause/resume
4. Payment gateway API integration (instead of manual upload)
5. Email notifications for subscription expiry
6. Admin dashboard for payment management

---

**Last Updated:** 2024 (Payment system fully implemented)

If you need more details, consult the documentation files above or review the source code directly.
