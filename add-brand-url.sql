-- Add brand_url column to domains table
ALTER TABLE domains ADD COLUMN brand_url VARCHAR(500) DEFAULT NULL AFTER email_address;
