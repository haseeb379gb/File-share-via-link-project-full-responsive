<?php
session_start();

// Function to sanitize user input
function sanitize($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Function to redirect user
function redirect($url) {
    header("Location: $url");
    exit();
}

// Function to generate a unique download link
function generateUniqueLink($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

// Function to get allowed file types
function getAllowedFileTypes($conn) {
    $stmt = $conn->prepare("SELECT extension FROM file_types");
    $stmt->execute();
    $types = $stmt->fetchAll(PDO::FETCH_COLUMN);
    return $types;
}

// Function to check if file type is allowed
function isAllowedFileType($extension, $conn) {
    $allowedTypes = getAllowedFileTypes($conn);
    return in_array(strtolower($extension), $allowedTypes);
}

// Function to format file size
function formatFileSize($bytes) {
    if ($bytes >= 1073741824) {
        return number_format($bytes / 1073741824, 2) . ' GB';
    } elseif ($bytes >= 1048576) {
        return number_format($bytes / 1048576, 2) . ' MB';
    } elseif ($bytes >= 1024) {
        return number_format($bytes / 1024, 2) . ' KB';
    } else {
        return $bytes . ' bytes';
    }
}

// Function to send email with download link
function sendEmailWithLink($to, $link, $filename) {
    $subject = "Your file download link";
    $message = "Hello,\n\nHere is your download link for the file '$filename':\n\n";
    $message .= "http://" . $_SERVER['HTTP_HOST'] . "/download.php?link=$link\n\n";
    $message .= "This link may expire after 7 days.\n\n";
    $message .= "Thank you for using our file sharing service!";
    $headers = "From: noreply@fileshare.com";
    
    return mail($to, $subject, $message, $headers);
}
?>

