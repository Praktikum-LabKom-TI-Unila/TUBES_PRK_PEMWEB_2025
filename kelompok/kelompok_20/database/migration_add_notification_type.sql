-- Migration: Add type column to notifications table
-- Date: 2025-12-11
-- Description: Add notification type categorization for better filtering

USE myunila_lostfound;

-- Add type column
-- Note: If column already exists, you need to drop it manually first:
-- ALTER TABLE notifications DROP COLUMN type;

ALTER TABLE notifications 
ADD COLUMN type VARCHAR(50) NOT NULL DEFAULT 'general' 
AFTER link;

-- Update existing notifications to have proper types based on their content
UPDATE notifications 
SET type = 'item_created' 
WHERE title LIKE '%Laporan Berhasil%' OR title LIKE '%Dipublikasikan%';

UPDATE notifications 
SET type = 'item_comment' 
WHERE title LIKE '%Komentar%';

UPDATE notifications 
SET type = 'new_claim' 
WHERE title LIKE '%Klaim Baru%';

UPDATE notifications 
SET type = 'claim_verified' 
WHERE title LIKE '%Diverifikasi%';

UPDATE notifications 
SET type = 'claim_rejected' 
WHERE title LIKE '%Ditolak%';

UPDATE notifications 
SET type = 'item_match' 
WHERE title LIKE '%Barang yang Cocok%' OR title LIKE '%Ada Barang%';

-- Set remaining to general
UPDATE notifications 
SET type = 'general' 
WHERE type IS NULL OR type = '';
