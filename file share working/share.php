<?php
header('Content-Type: application/json');
require_once 'config/database.php';
require_once 'includes/functions.php';

// Function to validate email
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

try {
    // Check if required fields are provided
    if (!isset($_POST['download_link']) || !isset($_POST['recipient_email'])) {
        throw new Exception('Missing required fields. Please provide both download link and recipient email.');
    }

    $downloadLink = $_POST['download_link'];
    $recipientEmail = sanitize($_POST['recipient_email']);
    $senderEmail = isset($_POST['sender_email']) ? sanitize($_POST['sender_email']) : 'noreply@fileshare.com';
    $senderName = isset($_POST['sender_name']) ? sanitize($_POST['sender_name']) : 'FileShare';
    $message = isset($_POST['message']) ? sanitize($_POST['message']) : '';

    // Validate recipient email
    if (!isValidEmail($recipientEmail)) {
        throw new Exception('Please enter a valid recipient email address.');
    }

    // Get file information from database
    $stmt = $conn->prepare("SELECT * FROM files WHERE download_link = :download_link");
    $stmt->bindParam(':download_link', $downloadLink, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        throw new Exception('File not found or link has expired.');
    }

    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    // Send email with download link
    $subject = "File shared with you via FileShare";
    $emailMessage = "Hello,\n\n";
    $emailMessage .= "Someone has shared a file with you via FileShare.\n\n";

    if (!empty($message)) {
        $emailMessage .= "Message: " . $message . "\n\n";
    }

    $emailMessage .= "File Details:\n";
    $emailMessage .= "Name: " . $file['original_filename'] . "\n";
    $emailMessage .= "Size: " . formatFileSize($file['file_size']) . "\n\n";
    $emailMessage .= "To download the file, click the link below:\n";
    $emailMessage .= $downloadLink . "\n\n";
    
    if ($file['expiry_date']) {
        $emailMessage .= "Note: This link will expire on " . date('F j, Y', strtotime($file['expiry_date'])) . ".\n\n";
    }
    
    $emailMessage .= "Best regards,\nFileShare Team";

    // Set email headers
    $headers = "From: " . $senderName . " <" . $senderEmail . ">\r\n";
    $headers .= "Reply-To: " . $senderEmail . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Send the email
    if (!mail($recipientEmail, $subject, $emailMessage, $headers)) {
        throw new Exception('Failed to send email. Please try again later.');
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Email sent successfully to ' . $recipientEmail
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'status' => 'error',
        'message' => $e->getMessage()
    ]);
}
?>

