<?php
header('Content-Type: text/html');
error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>PHP Upload Test</h1>";

// Display PHP configuration
echo "<h2>PHP Configuration</h2>";
echo "<pre>";
echo "upload_max_filesize: " . ini_get('upload_max_filesize') . "\n";
echo "post_max_size: " . ini_get('post_max_size') . "\n";
echo "max_file_uploads: " . ini_get('max_file_uploads') . "\n";
echo "memory_limit: " . ini_get('memory_limit') . "\n";
echo "max_execution_time: " . ini_get('max_execution_time') . "\n";
echo "</pre>";

// Check uploads directory
echo "<h2>Uploads Directory</h2>";
$uploadsDir = 'uploads';
if (!file_exists($uploadsDir)) {
    echo "<p style='color:red'>Uploads directory does not exist. Creating it now...</p>";
    if (mkdir($uploadsDir, 0777, true)) {
        echo "<p style='color:green'>Successfully created uploads directory.</p>";
    } else {
        echo "<p style='color:red'>Failed to create uploads directory.</p>";
    }
} else {
    echo "<p>Uploads directory exists.</p>";
}

// Check if directory is writable
if (is_writable($uploadsDir)) {
    echo "<p style='color:green'>Uploads directory is writable.</p>";
} else {
    echo "<p style='color:red'>Uploads directory is not writable. Current permissions: " . 
         substr(sprintf('%o', fileperms($uploadsDir)), -4) . "</p>";
}

// Simple upload form
echo "<h2>Test Upload</h2>";
echo "<form action='test-upload.php' method='post' enctype='multipart/form-data'>";
echo "<input type='file' name='test_file'>";
echo "<input type='submit' value='Upload'>";
echo "</form>";

// Process upload
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    echo "<h3>Upload Results</h3>";
    
    if (!isset($_FILES['test_file'])) {
        echo "<p style='color:red'>No file was uploaded.</p>";
    } else {
        echo "<pre>";
        print_r($_FILES);
        echo "</pre>";
        
        if ($_FILES['test_file']['error'] !== UPLOAD_ERR_OK) {
            echo "<p style='color:red'>Upload error: ";
            switch ($_FILES['test_file']['error']) {
                case UPLOAD_ERR_INI_SIZE:
                    echo "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                    break;
                case UPLOAD_ERR_FORM_SIZE:
                    echo "The uploaded file exceeds the MAX_FILE_SIZE directive in the HTML form";
                    break;
                case UPLOAD_ERR_PARTIAL:
                    echo "The uploaded file was only partially uploaded";
                    break;
                case UPLOAD_ERR_NO_FILE:
                    echo "No file was uploaded";
                    break;
                case UPLOAD_ERR_NO_TMP_DIR:
                    echo "Missing a temporary folder";
                    break;
                case UPLOAD_ERR_CANT_WRITE:
                    echo "Failed to write file to disk";
                    break;
                case UPLOAD_ERR_EXTENSION:
                    echo "A PHP extension stopped the file upload";
                    break;
                default:
                    echo "Unknown upload error";
            }
            echo "</p>";
        } else {
            // Try to move the uploaded file
            $uploadFile = $uploadsDir . '/' . basename($_FILES['test_file']['name']);
            if (move_uploaded_file($_FILES['test_file']['tmp_name'], $uploadFile)) {
                echo "<p style='color:green'>File successfully uploaded and moved to: " . $uploadFile . "</p>";
            } else {
                echo "<p style='color:red'>Failed to move uploaded file. Error: " . error_get_last()['message'] . "</p>";
            }
        }
    }
}
?>

