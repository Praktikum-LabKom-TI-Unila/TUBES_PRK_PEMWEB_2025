-- Fix payment table untuk subscription payments
-- Drop foreign key constraint yang menghalangi payment tanpa chat session

USE mental_health_platform;

-- Drop foreign key constraint
ALTER TABLE `payment` DROP FOREIGN KEY `payment_ibfk_2`;

-- Make session_id nullable untuk subscription payments
ALTER TABLE `payment` MODIFY `session_id` int NULL;

-- Recreate foreign key dengan ON DELETE SET NULL (optional, agar payment tidak dihapus saat chat session dihapus)
ALTER TABLE `payment` 
ADD CONSTRAINT `payment_ibfk_2` 
FOREIGN KEY (`session_id`) 
REFERENCES `chat_session` (`session_id`) 
ON DELETE SET NULL;
