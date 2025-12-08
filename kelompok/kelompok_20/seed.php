<?php
declare(strict_types=1);

/**
 * Database Seeder for myUnila Lost & Found
 * 
 * This script resets and populates the database with test data.
 * Run: php seed.php
 * 
 * @author Senior QA Engineer & Backend Developer
 * @version 1.0.0
 */

// ANSI Color Codes for CLI Output
define('COLOR_RESET', "\033[0m");
define('COLOR_GREEN', "\033[32m");
define('COLOR_RED', "\033[31m");
define('COLOR_YELLOW', "\033[33m");
define('COLOR_BLUE', "\033[34m");
define('COLOR_CYAN', "\033[36m");
define('COLOR_MAGENTA', "\033[35m");

// Helper functions for colored output
function success(string $message): void {
    echo COLOR_GREEN . "✅ " . $message . COLOR_RESET . PHP_EOL;
}

function error(string $message): void {
    echo COLOR_RED . "❌ " . $message . COLOR_RESET . PHP_EOL;
}

function info(string $message): void {
    echo COLOR_BLUE . "ℹ️  " . $message . COLOR_RESET . PHP_EOL;
}

function warning(string $message): void {
    echo COLOR_YELLOW . "⚠️  " . $message . COLOR_RESET . PHP_EOL;
}

function step(string $message): void {
    echo COLOR_CYAN . "🔹 " . $message . COLOR_RESET . PHP_EOL;
}

function section(string $message): void {
    echo PHP_EOL . COLOR_MAGENTA . "═══════════════════════════════════════════════════" . COLOR_RESET . PHP_EOL;
    echo COLOR_MAGENTA . "  " . $message . COLOR_RESET . PHP_EOL;
    echo COLOR_MAGENTA . "═══════════════════════════════════════════════════" . COLOR_RESET . PHP_EOL . PHP_EOL;
}

// Start seeding process
section("🌱 DATABASE SEEDER - myUnila Lost & Found");
info("Starting database reset and population...");

try {
    // Include database configuration
    step("Loading database configuration...");
    require_once __DIR__ . '/src/config/database.php';
    
    $db = Database::getConnection();
    success("Database connection established");

    // ========================================
    // STEP 1: Disable Foreign Key Checks
    // ========================================
    step("Disabling foreign key checks...");
    $db->exec("SET FOREIGN_KEY_CHECKS = 0");
    success("Foreign key checks disabled");

    // ========================================
    // STEP 2: Truncate Tables (Clean Slate)
    // ========================================
    section("🗑️  TRUNCATING TABLES");
    
    $tablesToTruncate = [
        'notifications',
        'comments',
        'claims',
        'items',
        'users',
        'categories',
        'locations'
    ];

    foreach ($tablesToTruncate as $table) {
        step("Truncating table: {$table}");
        $db->exec("TRUNCATE TABLE {$table}");
        success("Table {$table} truncated");
    }

    // ========================================
    // STEP 3: Seed Master Data
    // ========================================
    section("📦 SEEDING MASTER DATA");

    // Seed Categories
    step("Seeding categories...");
    $categories = [
        ['id' => 1, 'name' => 'Elektronik'],
        ['id' => 2, 'name' => 'Dokumen'],
        ['id' => 3, 'name' => 'Pakaian'],
        ['id' => 4, 'name' => 'Aksesoris'],
        ['id' => 5, 'name' => 'Kunci'],
        ['id' => 6, 'name' => 'Tas & Dompet'],
        ['id' => 7, 'name' => 'Buku & Alat Tulis'],
        ['id' => 8, 'name' => 'Kendaraan'],
        ['id' => 9, 'name' => 'Lainnya']
    ];

    $stmt = $db->prepare("INSERT INTO categories (id, name) VALUES (?, ?)");
    foreach ($categories as $category) {
        $stmt->execute([$category['id'], $category['name']]);
    }
    success("Categories seeded: " . count($categories) . " records");

    // Seed Locations
    step("Seeding locations...");
    $locations = [
        ['id' => 1, 'name' => 'Gedung Rektorat'],
        ['id' => 2, 'name' => 'Gedung Serba Guna (GSG)'],
        ['id' => 3, 'name' => 'Perpustakaan Pusat'],
        ['id' => 4, 'name' => 'Kantin Terpadu'],
        ['id' => 5, 'name' => 'Gedung A - FMIPA'],
        ['id' => 6, 'name' => 'Gedung B - Fakultas Teknik'],
        ['id' => 7, 'name' => 'Gedung C - FISIP'],
        ['id' => 8, 'name' => 'Gedung D - Fakultas Hukum'],
        ['id' => 9, 'name' => 'Gedung E - Fakultas Ekonomi'],
        ['id' => 10, 'name' => 'Gedung F - FKIP'],
        ['id' => 11, 'name' => 'Gedung G - Fakultas Pertanian'],
        ['id' => 12, 'name' => 'Gedung H - Fakultas Kedokteran'],
        ['id' => 13, 'name' => 'Masjid Al-Wasi\'i'],
        ['id' => 14, 'name' => 'Lapangan Olahraga'],
        ['id' => 15, 'name' => 'Parkiran Motor Pusat']
    ];

    $stmt = $db->prepare("INSERT INTO locations (id, name) VALUES (?, ?)");
    foreach ($locations as $location) {
        $stmt->execute([$location['id'], $location['name']]);
    }
    success("Locations seeded: " . count($locations) . " records");

    // ========================================
    // STEP 4: Seed Users
    // ========================================
    section("👥 SEEDING USERS");

    $hashedPassword = password_hash('password123', PASSWORD_DEFAULT);
    
    $users = [
        // Admin
        [
            'name' => 'Admin Sistem',
            'identity_number' => '999999999',
            'email' => 'admin@unila.ac.id',
            'phone' => '081234567890',
            'password' => $hashedPassword,
            'role' => 'admin',
            'is_active' => 1,
            'avatar' => null
        ],
        // Dosen
        [
            'name' => 'Dr. Budi Santoso, M.Kom',
            'identity_number' => '198501012010011001', // NIP format
            'email' => 'budi.santoso@unila.ac.id',
            'phone' => '081234567891',
            'password' => $hashedPassword,
            'role' => 'user',
            'is_active' => 1,
            'avatar' => null
        ],
        // Mahasiswa 1
        [
            'name' => 'Andi Wijaya',
            'identity_number' => '2115101001', // NPM format
            'email' => 'andi.wijaya@students.unila.ac.id',
            'phone' => '081234567892',
            'password' => $hashedPassword,
            'role' => 'user',
            'is_active' => 1,
            'avatar' => null
        ],
        // Mahasiswa 2
        [
            'name' => 'Siti Nurhaliza',
            'identity_number' => '2115101002',
            'email' => 'siti.nurhaliza@students.unila.ac.id',
            'phone' => '081234567893',
            'password' => $hashedPassword,
            'role' => 'user',
            'is_active' => 1,
            'avatar' => null
        ],
        // Mahasiswa 3
        [
            'name' => 'Reza Pratama',
            'identity_number' => '2115101003',
            'email' => 'reza.pratama@students.unila.ac.id',
            'phone' => '081234567894',
            'password' => $hashedPassword,
            'role' => 'user',
            'is_active' => 1,
            'avatar' => null
        ],
        // Banned User
        [
            'name' => 'Akun Diblokir',
            'identity_number' => '2115101999',
            'email' => 'banned@students.unila.ac.id',
            'phone' => '081234567899',
            'password' => $hashedPassword,
            'role' => 'user',
            'is_active' => 0, // BANNED
            'avatar' => null
        ]
    ];

    $stmt = $db->prepare("
        INSERT INTO users (name, identity_number, email, phone, password, role, is_active, avatar, created_at) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())
    ");

    foreach ($users as $user) {
        $stmt->execute([
            $user['name'],
            $user['identity_number'],
            $user['email'],
            $user['phone'],
            $user['password'],
            $user['role'],
            $user['is_active'],
            $user['avatar']
        ]);
    }
    success("Users seeded: " . count($users) . " records");
    info("   └─ Admin: admin@unila.ac.id / password123");
    info("   └─ Dosen: budi.santoso@unila.ac.id / password123");
    info("   └─ Mahasiswa (3x): *.students.unila.ac.id / password123");
    warning("   └─ Banned User: banned@students.unila.ac.id (is_active = 0)");

    // ========================================
    // STEP 5: Seed Items (Test Scenarios)
    // ========================================
    section("📱 SEEDING ITEMS");

    $items = [
        // 1. Lost Item (Open) - High value
        [
            'user_id' => 3, // Andi Wijaya
            'category_id' => 1, // Elektronik
            'location_id' => 6, // Perpustakaan
            'title' => 'iPhone 13 Pink',
            'description' => 'Hilang di Perpustakaan Pusat lantai 2, dekat rak buku Komputer. Casing warna pink dengan gantungan kunci Hello Kitty. Sangat penting karena ada data tugas akhir.',
            'type' => 'lost',
            'status' => 'open',
            'incident_date' => date('Y-m-d', strtotime('-2 days')),
            'image_path' => null,
        // 2. Found Item (Open) - Common item
        [
            'user_id' => 4, // Siti Nurhaliza
            'category_id' => 5, // Kunci
            'location_id' => 15, // Parkiran Motor Pusat
            'title' => 'Kunci Motor Honda',
            'description' => 'Menemukan kunci motor Honda Vario warna hitam di parkiran motor gedung B. Ada gantungan kunci bertuliskan "Reza". Bisa hubungi saya untuk verifikasi.',
            'type' => 'found',
            'status' => 'open',
            'incident_date' => date('Y-m-d', strtotime('-1 day')),
            'image_path' => null,
            'is_safe_claim' => 0,
            'security_question' => null,
            'security_answer' => null,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ],  'image_path' => null,
            'is_safe_claim' => 0,
            'security_question' => null,
            'security_answer' => null,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ],
        // 3. Safe Claim Item - With security question
        [
            'user_id' => 2, // Dr. Budi Santoso (Dosen)
            'category_id' => 6, // Tas & Dompet
            'location_id' => 4, // Kantin Terpadu
            'title' => 'Dompet Coklat Kulit',
            'description' => 'Dompet kulit coklat ditemukan di kantin terpadu, meja nomor 5. Untuk keamanan, harap jawab pertanyaan keamanan untuk verifikasi kepemilikan.',
            'type' => 'found',
            'status' => 'open',
            'incident_date' => date('Y-m-d', strtotime('-3 days')),
            'image_path' => null,
            'is_safe_claim' => 1, // SAFE CLAIM ENABLED
            'security_question' => 'Sebutkan isi dompet?',
            'security_answer' => password_hash('KTP a.n Budi', PASSWORD_DEFAULT), // Hashed for security
            'created_at' => date('Y-m-d H:i:s', strtotime('-3 days'))
        ],
        // 4. Processed Item - Being claimed
        [
            'user_id' => 5, // Reza Pratama
            'category_id' => 2, // Dokumen
            'location_id' => 4, // Fakultas Hukum
            'title' => 'KTM dan KTP a.n Muhammad Ali',
            'description' => 'Kehilangan KTM dan KTP di area Fakultas Hukum. Nama di kartu: Muhammad Ali, NPM 2115101050. Mohon bantuannya jika menemukan.',
            'type' => 'lost',
            'status' => 'process', // BEING CLAIMED
            'incident_date' => date('Y-m-d', strtotime('-5 days')),
            'image_path' => null,
            'is_safe_claim' => 0,
            'security_question' => null,
            'security_answer' => null,
            'created_at' => date('Y-m-d H:i:s', strtotime('-5 days'))
        ],
        // 5. Closed Item - Already returned
        [
            'user_id' => 3, // Andi Wijaya
            'category_id' => 7, // Buku & Alat Tulis
            'location_id' => 5, // Gedung A - FMIPA
            'title' => 'Kalkulator Scientific Casio',
            'description' => 'Kalkulator scientific merk Casio FX-991EX. Sudah ditemukan dan dikembalikan. Terima kasih banyak!',
            'type' => 'lost',
            'status' => 'closed', // ALREADY RETURNED
            'incident_date' => date('Y-m-d', strtotime('-10 days')),
            'image_path' => null,
            'is_safe_claim' => 0,
            'security_question' => null,
            'security_answer' => null,
            'created_at' => date('Y-m-d H:i:s', strtotime('-10 days'))
        ]
    ];

    $stmt = $db->prepare("
        INSERT INTO items (
            user_id, category_id, location_id, title, description, type, status, 
            incident_date, image_path, is_safe_claim, security_question, security_answer, 
            created_at
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    foreach ($items as $item) {
        $stmt->execute([
            $item['user_id'],
            $item['category_id'],
            $item['location_id'],
            $item['title'],
            $item['description'],
            $item['type'],
            $item['status'],
            $item['incident_date'],
            $item['image_path'],
            $item['is_safe_claim'],
            $item['security_question'],
            $item['security_answer'],
            $item['created_at']
        ]);
    }
    success("Items seeded: " . count($items) . " records");
    info("   └─ Lost (Open): iPhone 13 Pink");
    info("   └─ Found (Open): Kunci Motor Honda");
    info("   └─ Safe Claim: Dompet Coklat (security question enabled)");
    info("   └─ Process: KTM dan KTP (being claimed)");
    info("   └─ Closed: Kalkulator Scientific (returned)");

    // ========================================
    // STEP 6: Seed Claims
    // ========================================
    section("🎯 SEEDING CLAIMS");

    $claims = [
        // 1. Pending Claim - On "Processed Item" (ID 4)
        [
            'item_id' => 4,
            'user_id' => 4, // Siti claims Reza's lost KTM
            'status' => 'pending',
            'verification_answer' => null,
            'admin_notes' => 'Menunggu verifikasi dari pemilik barang.',
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
        ],
        // 2. Verified Claim - On "Closed Item" (ID 5)
        [
            'item_id' => 5,
            'user_id' => 2, // Dr. Budi found and returned Andi's calculator
            'status' => 'verified',
            'verification_answer' => null,
            'admin_notes' => 'Kalkulator berhasil dikembalikan ke pemilik. Terverifikasi oleh admin.',
            'created_at' => date('Y-m-d H:i:s', strtotime('-9 days'))
        ],
        // 3. Rejected Claim - History
        [
            'item_id' => 1, // Someone falsely claims the iPhone
            'user_id' => 6, // Banned user tried to claim (testing edge case)
            'status' => 'rejected',
            'verification_answer' => null,
            'admin_notes' => 'Klaim ditolak karena tidak dapat memverifikasi kepemilikan.',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day 2 hours'))
        ]
    ];

    $stmt = $db->prepare("
        INSERT INTO claims (
            item_id, user_id, status, verification_answer, admin_notes, created_at
        ) VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($claims as $claim) {
        $stmt->execute([
            $claim['item_id'],
            $claim['user_id'],
            $claim['status'],
            $claim['verification_answer'],
            $claim['admin_notes'],
            $claim['created_at']
        ]);
    }
    success("Claims seeded: " . count($claims) . " records");
    info("   └─ Pending: Claim on KTM (item #4)");
    info("   └─ Verified: Claim on Kalkulator (item #5)");
    warning("   └─ Rejected: False claim by banned user");

    // ========================================
    // STEP 7: Seed Comments
    // ========================================
    section("💬 SEEDING COMMENTS");

    $comments = [
        // Comments on iPhone 13 (Item ID 1)
        [
            'item_id' => 1,
            'user_id' => 4, // Siti
            'body' => 'Saya kemarin juga di perpus lantai 2, coba cek di meja dekat jendela?',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day 12 hours'))
        ],
        [
            'item_id' => 1,
            'user_id' => 5, // Reza
            'body' => 'Sudah tanya ke petugas perpustakaan? Biasanya ada yang menemukan langsung kasih ke sana.',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day 6 hours'))
        ],
        [
            'item_id' => 1,
            'user_id' => 3, // Andi (owner)
            'body' => 'Sudah saya cek ke petugas, belum ada yang menyerahkan. Terima kasih infonya!',
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day 2 hours'))
        ],
        // Comment on Dompet (Item ID 3)
        [
            'item_id' => 3,
            'user_id' => 3, // Andi
            'body' => 'Pak Dosen, apakah di dalamnya ada kartu ATM BCA?',
            'created_at' => date('Y-m-d H:i:s', strtotime('-2 days 5 hours'))
        ]
    ];

    $stmt = $db->prepare("
        INSERT INTO comments (item_id, user_id, body, created_at) 
        VALUES (?, ?, ?, ?)
    ");

    foreach ($comments as $comment) {
        $stmt->execute([
            $comment['item_id'],
            $comment['user_id'],
            $comment['body'],
            $comment['created_at']
        ]);
    }
    success("Comments seeded: " . count($comments) . " records");
    info("   └─ 3 comments on iPhone discussion");
    info("   └─ 1 comment on Dompet inquiry");

    // ========================================
    // STEP 8: Seed Notifications
    // ========================================
    section("🔔 SEEDING NOTIFICATIONS");

    $notifications = [
        // Notification for Reza (claim on his lost KTM)
        [
            'user_id' => 5, // Reza
            'title' => 'Klaim Baru pada Laporan Anda',
            'message' => 'Siti Nurhaliza telah mengajukan klaim pada laporan "KTM dan KTP a.n Muhammad Ali"',
            'link' => 'index.php?page=items&action=show&id=4',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days'))
        ],
        // Notification for Siti (her claim is pending)
        [
            'user_id' => 4, // Siti
            'title' => 'Klaim Sedang Diproses',
            'message' => 'Klaim Anda pada laporan "KTM dan KTP a.n Muhammad Ali" sedang dalam proses verifikasi',
            'link' => 'index.php?page=claims&action=my_claims',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-4 days 1 hour'))
        ],
        // Notification for Andi (his calculator was found - verified claim)
        [
            'user_id' => 3, // Andi
            'title' => 'Barang Anda Telah Ditemukan!',
            'message' => 'Klaim pada laporan "Kalkulator Scientific Casio" telah diverifikasi. Silakan ambil barang Anda.',
            'link' => 'index.php?page=items&action=show&id=5',
            'is_read' => 1, // Already read
            'created_at' => date('Y-m-d H:i:s', strtotime('-8 days'))
        ],
        // Notification for Andi (new comment on iPhone)
        [
            'user_id' => 3, // Andi
            'title' => 'Komentar Baru pada Laporan Anda',
            'message' => 'Siti Nurhaliza berkomentar pada laporan "iPhone 13 Pink"',
            'link' => 'index.php?page=items&action=show&id=1',
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day 12 hours'))
        ],
        // Notification for banned user (rejected claim)
        [
            'user_id' => 6, // Banned user
            'title' => 'Klaim Ditolak',
            'message' => 'Klaim Anda pada laporan "iPhone 13 Pink" telah ditolak',
            'link' => 'index.php?page=items&action=show&id=1',
            'is_read' => 1,
            'created_at' => date('Y-m-d H:i:s', strtotime('-1 day'))
        ]
    ];

    $stmt = $db->prepare("
        INSERT INTO notifications (user_id, title, message, link, is_read, created_at) 
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    foreach ($notifications as $notification) {
        $stmt->execute([
            $notification['user_id'],
            $notification['title'],
            $notification['message'],
            $notification['link'],
            $notification['is_read'],
            $notification['created_at']
        ]);
    }
    success("Notifications seeded: " . count($notifications) . " records");
    info("   └─ Claim notifications (pending, verified, rejected)");
    info("   └─ Comment notification");

    // ========================================
    // STEP 9: Re-enable Foreign Key Checks
    // ========================================
    step("Re-enabling foreign key checks...");
    $db->exec("SET FOREIGN_KEY_CHECKS = 1");
    success("Foreign key checks re-enabled");

    // ========================================
    // FINAL SUMMARY
    // ========================================
    section("📊 SEEDING SUMMARY");
    
    $summary = [
        'categories' => count($categories),
        'locations' => count($locations),
        'users' => count($users),
        'items' => count($items),
        'claims' => count($claims),
        'comments' => count($comments),
        'notifications' => count($notifications)
    ];

    foreach ($summary as $table => $count) {
        info("   " . ucfirst($table) . ": {$count} records");
    }

    section("✅ DATABASE SEEDING COMPLETED SUCCESSFULLY!");
    success("All test data has been populated.");
    echo PHP_EOL;
    info("📝 Test Credentials:");
    echo COLOR_CYAN . "   ┌─────────────────────────────────────────────────┐" . COLOR_RESET . PHP_EOL;
    echo COLOR_CYAN . "   │ Admin:      admin@unila.ac.id / password123     │" . COLOR_RESET . PHP_EOL;
    echo COLOR_CYAN . "   │ Dosen:      budi.santoso@unila.ac.id / ...      │" . COLOR_RESET . PHP_EOL;
    echo COLOR_CYAN . "   │ Mahasiswa:  andi.wijaya@students... / ...       │" . COLOR_RESET . PHP_EOL;
    echo COLOR_CYAN . "   │ Banned:     banned@students... (is_active=0)    │" . COLOR_RESET . PHP_EOL;
    echo COLOR_CYAN . "   └─────────────────────────────────────────────────┘" . COLOR_RESET . PHP_EOL;
    echo PHP_EOL;
    info("🧪 Test Scenarios Ready:");
    echo COLOR_YELLOW . "   • Lost Item (Open): iPhone 13 Pink" . COLOR_RESET . PHP_EOL;
    echo COLOR_YELLOW . "   • Found Item (Open): Kunci Motor Honda" . COLOR_RESET . PHP_EOL;
    echo COLOR_YELLOW . "   • Safe Claim: Dompet Coklat (with security Q&A)" . COLOR_RESET . PHP_EOL;
    echo COLOR_YELLOW . "   • Processed: KTM (with pending claim)" . COLOR_RESET . PHP_EOL;
    echo COLOR_YELLOW . "   • Closed: Kalkulator (with verified claim)" . COLOR_RESET . PHP_EOL;
    echo COLOR_YELLOW . "   • Rejected Claim: False claim by banned user" . COLOR_RESET . PHP_EOL;
    echo COLOR_YELLOW . "   • Comments: Discussion on iPhone post" . COLOR_RESET . PHP_EOL;
    echo COLOR_YELLOW . "   • Notifications: Various types (read/unread)" . COLOR_RESET . PHP_EOL;
    echo PHP_EOL;

} catch (PDOException $e) {
    error("Database Error: " . $e->getMessage());
    error("File: " . $e->getFile());
    error("Line: " . $e->getLine());
    exit(1);
} catch (Exception $e) {
    error("Error: " . $e->getMessage());
    error("File: " . $e->getFile());
    error("Line: " . $e->getLine());
    exit(1);
}
