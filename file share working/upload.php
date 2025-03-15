<?php
// Set content type to JSON
header('Content-Type: application/json');

// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Include required files
require_once 'config/database.php';
require_once 'includes/functions.php';

// Function to log errors
function logError($message) {
    $logFile = 'upload_errors.log';
    $timestamp = date('Y-m-d H:i:s');
    file_put_contents($logFile, "[$timestamp] $message\n", FILE_APPEND);
}

// Function to return error response
function returnError($message, $statusCode = 400) {
    http_response_code($statusCode);
    echo json_encode([
        'status' => 'error',
        'message' => $message
    ]);
    exit;
}

// Function to return success response
function returnSuccess($data) {
    echo json_encode(array_merge(['status' => 'success'], $data));
    exit;
}

try {
    // Log the start of the upload process
    logError("Upload process started");
    
    // Check if uploads directory exists
    if (!file_exists('uploads')) {
        if (!mkdir('uploads', 0777, true)) {
            throw new Exception('Failed to create uploads directory');
        }
        chmod('uploads', 0777);
        logError("Created uploads directory");
    }
    
    // Check if uploads directory is writable
    if (!is_writable('uploads')) {
        throw new Exception('Uploads directory is not writable. Current permissions: ' . 
                           substr(sprintf('%o', fileperms('uploads')), -4));
    }
    
    // Check if file was uploaded
    if (!isset($_FILES['file'])) {
        throw new Exception('No file was uploaded');
    }
    
    // Log file upload attempt
    logError("File upload attempt: " . json_encode($_FILES));
    
    // Check for upload errors
    if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
        $message = 'Upload error: ';
        switch ($_FILES['file']['error']) {
            case UPLOAD_ERR_INI_SIZE:
                $message .= 'The uploaded file exceeds the upload_max_filesize directive in php.ini';
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message .= 'The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form';
                break;
            case UPLOAD_ERR_PARTIAL:
                $message .= 'The uploaded file was only partially uploaded';
                break;
            case UPLOAD_ERR_NO_FILE:
                $message .= 'No file was uploaded';
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message .= 'Missing a temporary folder';
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message .= 'Failed to write file to disk';
                break;
            case UPLOAD_ERR_EXTENSION:
                $message .= 'A PHP extension stopped the file upload';
                break;
            default:
                $message .= 'Unknown upload error';
        }
        throw new Exception($message);
    }
    
    // Get file information
    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileSize = $file['size'];
    $fileTmpName = $file['tmp_name'];
    $fileType = $file['type'];
    $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
    
    logError("File info: name=$fileName, size=$fileSize, type=$fileType, ext=$fileExt");
    
    // Check file size (1GB limit)
    if ($fileSize > 1073741824) {
        throw new Exception('File size exceeds the 1GB limit');
    }
    
    // Check if allowed file types were specified
    if (isset($_POST['allowed_types']) && !empty($_POST['allowed_types'])) {
        $allowedTypes = explode(',', $_POST['allowed_types']);
        logError("Allowed types: " . $_POST['allowed_types']);
        
        if (!in_array($fileExt, $allowedTypes)) {
            throw new Exception("File type not allowed. Please select one of the allowed file types: " . implode(', ', $allowedTypes));
        }
    } else {
        // If no specific types were selected, check against all allowed types in the database
        if (!isAllowedFileType($fileExt, $conn)) {
            throw new Exception('File type not allowed');
        }
    }
    
    // Generate a unique filename
    $storedFileName = uniqid() . '.' . $fileExt;
    $uploadPath = 'uploads/' . $storedFileName;
    
    logError("Attempting to move file to: $uploadPath");
    
    // Move the uploaded file
    if (!move_uploaded_file($fileTmpName, $uploadPath)) {
        $moveError = error_get_last();
        throw new Exception('Failed to move uploaded file. Error: ' . ($moveError ? $moveError['message'] : 'Unknown error'));
    }
    
    logError("File moved successfully");
    
    // Generate a unique download link
    $downloadLink = generateUniqueLink(15);
    
    // Set expiry date (7 days from now)
    $expiryDate = date('Y-m-d H:i:s', strtotime('+7 days'));
    
    // Set user_id if logged in, otherwise null for guest uploads
    $userId = isLoggedIn() ? $_SESSION['user_id'] : null;
    
    logError("Inserting file info into database: user_id=" . ($userId ?? 'NULL') . ", link=$downloadLink");
    
    // Insert file information into database
    try {
        $stmt = $conn->prepare("INSERT INTO files (user_id, original_filename, stored_filename, file_size, mime_type, expiry_date, download_link) VALUES (:user_id, :original_filename, :stored_filename, :file_size, :mime_type, :expiry_date, :download_link)");
        
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':original_filename', $fileName, PDO::PARAM_STR);
        $stmt->bindParam(':stored_filename', $storedFileName, PDO::PARAM_STR);
        $stmt->bindParam(':file_size', $fileSize, PDO::PARAM_INT);
        $stmt->bindParam(':mime_type', $fileType, PDO::PARAM_STR);
        $stmt->bindParam(':expiry_date', $expiryDate, PDO::PARAM_STR);
        $stmt->bindParam(':download_link', $downloadLink, PDO::PARAM_STR);
        
        if (!$stmt->execute()) {
            // Get database error
            $dbError = $stmt->errorInfo();
            throw new Exception('Database error: ' . ($dbError[2] ?? 'Unknown error'));
        }
    } catch (PDOException $e) {
        // Delete the uploaded file if database insertion fails
        unlink($uploadPath);
        throw new Exception('Database error: ' . $e->getMessage());
    }
    
    logError("File uploaded successfully");
    
    // Return success response
    returnSuccess([
        'message' => 'File uploaded successfully',
        'download_link' => 'http://' . $_SERVER['HTTP_HOST'] . '/download.php?link=' . $downloadLink,
        'file_info' => [
            'name' => $fileName,
            'size' => formatFileSize($fileSize),
            'type' => $fileExt
        ]
    ]);
    
} catch (Exception $e) {
    // Log the error
    logError("Upload error: " . $e->getMessage() . "\n" . $e->getTraceAsString());
    
    // Return error response
    returnError($e->getMessage());
}
?>

