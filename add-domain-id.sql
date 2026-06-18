-- Remove UNIQUE constraint from domain_id column (for renewal support)
ALTER TABLE domains DROP INDEX domain_id;

-- Add client_date column to store PC date/time
ALTER TABLE domains ADD COLUMN client_date DATETIME AFTER created_at;
