-- Add domain_id column to domains table
ALTER TABLE domains ADD COLUMN domain_id VARCHAR(20) UNIQUE AFTER id;

-- Update existing records with unique IDs
UPDATE domains SET domain_id = CONCAT('#DOM-', LPAD(id, 5, '0')) WHERE domain_id IS NULL;
