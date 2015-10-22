ALTER TABLE `vehicle` ADD COLUMN `last_updated` datetime DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP AFTER `pin_code`;

// To populate the time run the following
UPDATE vehicle SET last_updated = now()
