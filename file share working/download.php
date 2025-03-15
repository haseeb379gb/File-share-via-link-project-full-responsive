<?php
require_once 'config/database.php';
require_once 'includes/functions.php';

try {
    // Check if download link is provided
    if (!isset($_GET['link']) || empty($_GET['link'])) {
        throw new Exception('Invalid download link. Please check the URL and try again.');
    }

    $downloadLink = $_GET['link'];

    // Get file information from database
    $stmt = $conn->prepare("SELECT * FROM files WHERE download_link = :download_link");
    $stmt->bindParam(':download_link', $downloadLink, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() == 0) {
        throw new Exception('File not found or link has expired. Please contact the sender for a new link.');
    }

    $file = $stmt->fetch(PDO::FETCH_ASSOC);

    // Check if file has expired
    if ($file['expiry_date'] !== null && strtotime($file['expiry_date']) < time()) {
        throw new Exception('This download link has expired. Please contact the sender for a new link.');
    }

    // Check if file exists
    $filePath = 'uploads/' . $file['stored_filename'];
    if (!file_exists($filePath)) {
        throw new Exception('File not found on server. Please contact support for assistance.');
    }

    // If this is a direct download request, serve the file
    if (isset($_GET['download']) && $_GET['download'] == 'true') {
        // Update download count
        $updateStmt = $conn->prepare("UPDATE files SET download_count = download_count + 1 WHERE id = :id");
        $updateStmt->bindParam(':id', $file['id'], PDO::PARAM_INT);
        $updateStmt->execute();

        // Set headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $file['mime_type']);
        header('Content-Disposition: attachment; filename="' . $file['original_filename'] . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));

        // Clear output buffer
        ob_clean();
        flush();

        // Read file and output to browser
        readfile($filePath);
        exit;
    }

    // Otherwise, display the download page
    include 'includes/header.php';
    ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body text-center p-5">
                        <?php
                        // Display appropriate icon based on file type
                        $ext = pathinfo($file['original_filename'], PATHINFO_EXTENSION);
                        $iconClass = 'fa-file';
                        
                        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])) {
                            $iconClass = 'fa-file-image';
                        } elseif (in_array($ext, ['pdf'])) {
                            $iconClass = 'fa-file-pdf';
                        } elseif (in_array($ext, ['doc', 'docx'])) {
                            $iconClass = 'fa-file-word';
                        } elseif (in_array($ext, ['xls', 'xlsx'])) {
                            $iconClass = 'fa-file-excel';
                        } elseif (in_array($ext, ['zip', 'rar'])) {
                            $iconClass = 'fa-file-archive';
                        }
                        ?>
                        <i class="fas <?php echo $iconClass; ?> fa-4x text-primary mb-4"></i>
                        <h2 class="mb-3"><?php echo htmlspecialchars($file['original_filename']); ?></h2>
                        <p class="mb-1">File Size: <?php echo formatFileSize($file['file_size']); ?></p>
                        <p class="mb-4">Uploaded: <?php echo date('F j, Y', strtotime($file['upload_date'])); ?></p>
                        
                        <div class="d-grid gap-2 col-md-6 mx-auto">
                            <a href="download.php?link=<?php echo $downloadLink; ?>&download=true" class="btn btn-primary btn-lg">
                                <i class="fas fa-download me-2"></i> Download File
                            </a>
                            <button class="btn btn-outline-secondary" onclick="window.history.back()">
                                <i class="fas fa-arrow-left me-2"></i> Go Back
                            </button>
                        </div>
                        
                        <?php if ($file['expiry_date']): ?>
                        <div class="mt-4 text-muted">
                            <small>This file will be available until <?php echo date('F j, Y', strtotime($file['expiry_date'])); ?></small>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    include 'includes/footer.php';

} catch (Exception $e) {
    // If there's an error, show it in a user-friendly way
    include 'includes/header.php';
    ?>
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card shadow-sm">
                    <div class="card-body text-center p-5">
                        <i class="fas fa-exclamation-circle fa-4x text-danger mb-4"></i>
                        <h2 class="mb-3">Download Error</h2>
                        <p class="mb-4"><?php echo htmlspecialchars($e->getMessage()); ?></p>
                        <button class="btn btn-primary" onclick="window.history.back()">
                            <i class="fas fa-arrow-left me-2"></i> Go Back
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
    include 'includes/footer.php';
}
?>

