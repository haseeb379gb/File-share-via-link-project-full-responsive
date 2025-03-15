<?php
// Database configuration
$host = 'localhost';
$dbname = 'file_share';
$username = 'root';
$password = '';

// Create database connection
try {
    // First connect without specifying a database
    $conn = new PDO("mysql:host=$host", $username, $password);
    // Set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Check if database exists, if not create it
    $conn->exec("CREATE DATABASE IF NOT EXISTS `$dbname`");
    $conn->exec("USE `$dbname`");
    
    // Check if tables exist, if not create them
    $tablesExist = $conn->query("SHOW TABLES LIKE 'users'")->rowCount() > 0;
    
    if (!$tablesExist) {
        // Create users table
        $conn->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(100) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        )");
        
        // Create files table
        $conn->exec("CREATE TABLE IF NOT EXISTS files (
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
        )");
        
        // Create file_types table
        $conn->exec("CREATE TABLE IF NOT EXISTS file_types (
            id INT AUTO_INCREMENT PRIMARY KEY,
            extension VARCHAR(20) NOT NULL,
            description VARCHAR(100) NOT NULL
        )");
        
        // Insert common file types
        $conn->exec("INSERT INTO file_types (extension, description) VALUES
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
            ('rar', 'RAR Archive')
        ");
    }
    
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}
?>

