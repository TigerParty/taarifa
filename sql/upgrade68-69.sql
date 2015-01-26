-- Add incident status to the incident table
ALTER TABLE incident ADD incident_status INT;
-- Update the database version
UPDATE `settings` SET `db_version` = '69' WHERE `id` = 1 LIMIT 1;
