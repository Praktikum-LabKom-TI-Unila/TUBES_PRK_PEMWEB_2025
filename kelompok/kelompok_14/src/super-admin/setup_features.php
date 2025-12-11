<?php
require "../config.php";

echo "<h2>Setup Superadmin Features...</h2><hr>";

// 1. Create activity_logs table
$sql1 = "CREATE TABLE IF NOT EXISTS activity_logs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NULL,
    user_name VARCHAR(100) NULL,
    action VARCHAR(50) NOT NULL,
    description TEXT,
    ip_address VARCHAR(45),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";

if ($conn->query($sql1) === TRUE) {
    echo "✅ Table 'activity_logs' created/exists.<br>";
} else {
    echo "❌ Error creating 'activity_logs': " . $conn->error . "<br>";
}

// 2. Create app_settings table
$sql2 = "CREATE TABLE IF NOT EXISTS app_settings (
    id INT PRIMARY KEY,
    app_name VARCHAR(100) DEFAULT 'FixTrack',
    company_name VARCHAR(100) DEFAULT 'FixTrack Service Center',
    address TEXT,
    phone VARCHAR(20),
    email VARCHAR(100),
    logo VARCHAR(255) DEFAULT 'logo.png',
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
)";

if ($conn->query($sql2) === TRUE) {
    echo "✅ Table 'app_settings' created/exists.<br>";
    
    // Insert Default Data if empty
    $check = $conn->query("SELECT * FROM app_settings");
    if ($check->num_rows == 0) {
        $sql_ins = "INSERT IGNORE INTO app_settings (id, app_name, company_name, address, phone, email) 
                  VALUES (1, 'RepairinBro', 'RepairinBro Service Center', 'Jl. Elektronik Maju No. 123', '0812-3456-7890', 'admin@repairinbro.com')";
        if ($conn->query($sql_ins)) {
            echo "✅ Default settings inserted.<br>";
        }
    }
} else {
    echo "❌ Error creating 'app_settings': " . $conn->error . "<br>";
}

// 3. Add 'foto' column to users if not exists (check from previous chat)
// Just to be safe for backups/restores later
echo "<hr>Setup Done! <a href='superadmin_dashboard.php'>Back to Dashboard</a>";
?>
