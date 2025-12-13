USE warkops_db;


-- ==========================================
-- WARKOPS FINAL SEEDER V4 (20 Dummy Transactions)
-- ==========================================

-- 1. Matikan proteksi Foreign Key sementara
SET FOREIGN_KEY_CHECKS = 0;

-- 2. Kosongkan semua tabel (Pakai DELETE agar anti-error #1701)
DELETE FROM inventory_logs;
DELETE FROM transaction_items;
DELETE FROM transactions;
DELETE FROM menu_recipes;
DELETE FROM menu;
DELETE FROM ingredients;
DELETE FROM categories;
DELETE FROM users;

-- 3. Reset Counter ID (Auto Increment) ke 1
ALTER TABLE inventory_logs AUTO_INCREMENT = 1;
ALTER TABLE transaction_items AUTO_INCREMENT = 1;
ALTER TABLE transactions AUTO_INCREMENT = 1;
ALTER TABLE menu_recipes AUTO_INCREMENT = 1;
ALTER TABLE menu AUTO_INCREMENT = 1;
ALTER TABLE ingredients AUTO_INCREMENT = 1;
ALTER TABLE categories AUTO_INCREMENT = 1;
ALTER TABLE users AUTO_INCREMENT = 1;

-- ------------------------------------------
-- 4. INSERT DATA MASTER
-- ------------------------------------------

-- [USERS]
-- Password untuk keduanya adalah: "password"
-- Hash: $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi
INSERT INTO users (user_id, username, password_hash, full_name, role, is_active) VALUES
(1, 'ADMIN_01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Doctor Hino', 'admin', 1),
(2, 'KASIR_01', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'Amiya', 'kasir', 1);

-- [CATEGORIES]
INSERT INTO categories (category_id, name, description) VALUES
(1, 'Minuman', 'Coffee, Non-Coffee, & Refreshers'),
(2, 'Makanan', 'Main Course & Rice Bowl'),
(3, 'Snack', 'Light Bites & Dessert');

-- [INGREDIENTS]
INSERT INTO ingredients (ingredient_id, name, unit, stock_qty, low_stock_threshold) VALUES
(1, 'Beans Arabica', 'gram', 5000.00, 500.00),
(2, 'Fresh Milk', 'ml', 10000.00, 2000.00),
(3, 'Momo Syrup', 'ml', 2000.00, 200.00),
(4, 'Gula Aren', 'ml', 3000.00, 500.00),
(5, 'Sliced Beef', 'gram', 5000.00, 1000.00),
(6, 'Beras Jepang', 'gram', 10000.00, 2000.00),
(7, 'Telur Ayam', 'butir', 100.00, 10.00),
(8, 'Roti Tawar', 'lembar', 50.00, 10.00),
(9, 'Keju Slice', 'lembar', 100.00, 10.00),
(10, 'Soda Water', 'ml', 5000.00, 500.00);

-- [MENU]
INSERT INTO menu (menu_id, category_id, name, description, price, is_available) VALUES
(1, 1, 'Kopi Susu Momo', 'Signature pink foam coffee', 24000.00, 1),
(2, 1, 'Aren Latte', 'Classic palm sugar coffee', 18000.00, 1),
(3, 2, 'Gyudon Retro', 'Beef bowl with onsen egg', 32000.00, 1),
(4, 3, 'Roti Bakar Keju', 'Toast with abundant cheese', 15000.00, 1),
(5, 1, 'Sakura Soda', 'Refreshing floral soda', 22000.00, 1);

-- [MENU RECIPES]
INSERT INTO menu_recipes (menu_id, ingredient_id, qty_used, unit) VALUES
(1, 1, 18, 'gram'), (1, 2, 150, 'ml'), (1, 3, 20, 'ml'), -- Kopi Momo
(2, 1, 18, 'gram'), (2, 2, 150, 'ml'), (2, 4, 25, 'ml'), -- Aren Latte
(3, 5, 100, 'gram'), (3, 6, 150, 'gram'), (3, 7, 1, 'butir'), -- Gyudon
(4, 8, 2, 'lembar'), (4, 9, 2, 'lembar'), -- Roti Bakar
(5, 10, 200, 'ml'), (5, 3, 30, 'ml'); -- Sakura Soda

-- ------------------------------------------
-- 5. INSERT 20 TRANSACTIONS (Dummy History)
-- ------------------------------------------

-- HARI INI (4 Trx)
INSERT INTO transactions (trx_id, user_id, table_no, subtotal, tax_amount, total, payment, change_amount, datetime) VALUES
(1, 1, 'T-01', 56000, 5600, 61600, 70000, 8400, NOW()),
(2, 2, 'T-03', 32000, 3200, 35200, 50000, 14800, NOW()),
(3, 1, 'POS', 48000, 4800, 52800, 60000, 7200, NOW()),
(4, 2, 'T-05', 24000, 2400, 26400, 30000, 3600, NOW());

-- KEMARIN (3 Trx)
INSERT INTO transactions (trx_id, user_id, subtotal, tax_amount, total, datetime) VALUES
(5, 1, 150000, 15000, 165000, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(6, 1, 72000, 7200, 79200, DATE_SUB(NOW(), INTERVAL 1 DAY)),
(7, 2, 45000, 4500, 49500, DATE_SUB(NOW(), INTERVAL 1 DAY));

-- 2 HARI LALU (3 Trx - Peak)
INSERT INTO transactions (trx_id, user_id, subtotal, tax_amount, total, datetime) VALUES
(8, 1, 320000, 32000, 352000, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(9, 1, 128000, 12800, 140800, DATE_SUB(NOW(), INTERVAL 2 DAY)),
(10, 2, 64000, 6400, 70400, DATE_SUB(NOW(), INTERVAL 2 DAY));

-- 3 HARI LALU (3 Trx)
INSERT INTO transactions (trx_id, user_id, subtotal, tax_amount, total, datetime) VALUES
(11, 1, 96000, 9600, 105600, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(12, 2, 54000, 5400, 59400, DATE_SUB(NOW(), INTERVAL 3 DAY)),
(13, 2, 30000, 3000, 33000, DATE_SUB(NOW(), INTERVAL 3 DAY));

-- 4 HARI LALU (2 Trx)
INSERT INTO transactions (trx_id, user_id, subtotal, tax_amount, total, datetime) VALUES
(14, 1, 240000, 24000, 264000, DATE_SUB(NOW(), INTERVAL 4 DAY)),
(15, 1, 60000, 6000, 66000, DATE_SUB(NOW(), INTERVAL 4 DAY));

-- 5 HARI LALU (2 Trx)
INSERT INTO transactions (trx_id, user_id, subtotal, tax_amount, total, datetime) VALUES
(16, 2, 44000, 4400, 48400, DATE_SUB(NOW(), INTERVAL 5 DAY)),
(17, 2, 88000, 8800, 96800, DATE_SUB(NOW(), INTERVAL 5 DAY));

-- 6 HARI LALU (3 Trx)
INSERT INTO transactions (trx_id, user_id, subtotal, tax_amount, total, datetime) VALUES
(18, 1, 120000, 12000, 132000, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(19, 1, 32000, 3200, 35200, DATE_SUB(NOW(), INTERVAL 6 DAY)),
(20, 2, 18000, 1800, 19800, DATE_SUB(NOW(), INTERVAL 6 DAY));


-- ------------------------------------------
-- 6. INSERT TRANSACTION ITEMS (20 TRX Detail)
-- ------------------------------------------

-- Trx 1: Kopi Momo + Gyudon
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (1, 1, 1, 24000, 24000), (1, 3, 1, 32000, 32000);
-- Trx 2: Gyudon
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (2, 3, 1, 32000, 32000);
-- Trx 3: Kopi Momo (2)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (3, 1, 2, 24000, 48000);
-- Trx 4: Kopi Momo (1)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (4, 1, 1, 24000, 24000);

-- Trx 5: Gyudon (3) + Kopi (2) + Roti (2)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES 
(5, 3, 3, 32000, 96000), (5, 1, 2, 24000, 48000), (5, 4, 2, 15000, 30000);
-- Trx 6: Kopi Momo (3)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (6, 1, 3, 24000, 72000);
-- Trx 7: Roti (3)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (7, 4, 3, 15000, 45000);

-- Trx 8: Gyudon (10)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (8, 3, 10, 32000, 320000);
-- Trx 9: Gyudon (4)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (9, 3, 4, 32000, 128000);
-- Trx 10: Gyudon (2)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (10, 3, 2, 32000, 64000);

-- Trx 11: Kopi Momo (4)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (11, 1, 4, 24000, 96000);
-- Trx 12: Aren Latte (3)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (12, 2, 3, 18000, 54000);
-- Trx 13: Roti (2)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (13, 4, 2, 15000, 30000);

-- Trx 14: Kopi Momo (10)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (14, 1, 10, 24000, 240000);
-- Trx 15: Roti (4)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (15, 4, 4, 15000, 60000);

-- Trx 16: Sakura Soda (2)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (16, 5, 2, 22000, 44000);
-- Trx 17: Sakura Soda (4)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (17, 5, 4, 22000, 88000);

-- Trx 18: Kopi Momo (5)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (18, 1, 5, 24000, 120000);
-- Trx 19: Gyudon (1)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (19, 3, 1, 32000, 32000);
-- Trx 20: Aren Latte (1)
INSERT INTO transaction_items (trx_id, menu_id, qty, price_at_time, line_total) VALUES (20, 2, 1, 18000, 18000);

-- 7. Hidupkan kembali proteksi Foreign Key
SET FOREIGN_KEY_CHECKS = 1;