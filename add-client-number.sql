-- Add client_number column to domains table
ALTER TABLE domains ADD COLUMN client_number VARCHAR(255) DEFAULT NULL AFTER customer_email;

-- Optional: Remove order_id and additional_comments columns if no longer needed
-- ALTER TABLE domains DROP COLUMN order_id;
-- ALTER TABLE domains DROP COLUMN additional_comments;
