-- Domain Registration System Database Schema
-- Create database
CREATE DATABASE IF NOT EXISTS domainrequestpor_management;
USE domainrequestpor_management;

-- Create domains table
CREATE TABLE IF NOT EXISTS domains (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain_name VARCHAR(255) NOT NULL,
    registration_tenure VARCHAR(50) NOT NULL,
    domain_for VARCHAR(100) NOT NULL,
    buying_as VARCHAR(100) NOT NULL,
    your_name VARCHAR(255) NOT NULL,
    unit_head_name VARCHAR(255) NOT NULL,
    project_cost DECIMAL(10, 2) NOT NULL,
    email_address VARCHAR(255) NOT NULL,
    customer_name VARCHAR(255) NOT NULL,
    customer_email VARCHAR(255) NOT NULL,
    order_id VARCHAR(100),
    additional_comments TEXT,
    is_viewed TINYINT(1) DEFAULT 0,
    email_sent TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Create index for faster searches
CREATE INDEX idx_domain_name ON domains(domain_name);

-- Create brand_email_credentials table
CREATE TABLE IF NOT EXISTS brand_email_credentials (
    id INT AUTO_INCREMENT PRIMARY KEY,
    domain_id INT NOT NULL,
    email VARCHAR(255) NOT NULL,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (domain_id) REFERENCES domains(id) ON DELETE CASCADE
);

-- Create unit_head_history table
CREATE TABLE IF NOT EXISTS unit_head_history (
    id INT AUTO_INCREMENT PRIMARY KEY,
    unit_head_name VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX idx_email_address ON domains(email_address);
CREATE INDEX idx_created_at ON domains(created_at);

-- Create admin users table
CREATE TABLE IF NOT EXISTS admin_users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Insert default admin user (username: demodcts_admin, password: Secure@2024!)
-- Password is hashed using MD5 for simplicity (consider using bcrypt in production)
INSERT INTO admin_users (username, password) VALUES 
('demodcts_admin', '8d969eef6ecad3c29a3a629280e686cf0');
