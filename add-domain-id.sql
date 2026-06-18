-- Remove UNIQUE constraint from domain_id column (for renewal support)
ALTER TABLE domains DROP INDEX domain_id;
