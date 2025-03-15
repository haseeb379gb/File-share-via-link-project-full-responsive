-- Create database
CREATE DATABASE IF NOT EXISTS file_share;
USE file_share;

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Files table
CREATE TABLE IF NOT EXISTS files (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    original_filename VARCHAR(255) NOT NULL,
    stored_filename VARCHAR(255) NOT NULL,
    file_size BIGINT NOT NULL,
    mime_type VARCHAR(100) NOT NULL,
    upload_date TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expiry_date TIMESTAMP NULL,
    download_link VARCHAR(255) NOT NULL UNIQUE,
    download_count INT DEFAULT 0,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- File types table
CREATE TABLE IF NOT EXISTS file_types (
    id INT AUTO_INCREMENT PRIMARY KEY,
    extension VARCHAR(20) NOT NULL,
    description VARCHAR(100) NOT NULL
);

-- Insert common file types
INSERT INTO file_types (extension, description) VALUES
('pdf', 'PDF Document'),
('doc', 'Word Document'),
('docx', 'Word Document'),
('xls', 'Excel Spreadsheet'),
('xlsx', 'Excel Spreadsheet'),
('ppt', 'PowerPoint Presentation'),
('pptx', 'PowerPoint Presentation'),
('txt', 'Text File'),
('jpg', 'JPEG Image'),
('jpeg', 'JPEG Image'),
('png', 'PNG Image'),
('gif', 'GIF Image'),
('zip', 'ZIP Archive'),
('rar', 'RAR Archive');

