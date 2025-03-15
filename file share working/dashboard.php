<?php
include 'includes/header.php';

// Check if user is logged in
if (!isLoggedIn()) {
    redirect("login.php");
}

// Get user's files
$stmt = $conn->prepare("SELECT * FROM files WHERE user_id = :user_id ORDER BY upload_date DESC");
$stmt->bindParam(":user_id", $_SESSION["user_id"], PDO::PARAM_INT);
$stmt->execute();
$files = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get allowed file types
$allowedTypes = getAllowedFileTypes($conn);
?>

<div class="row">
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Upload File</h4>
                <button type="button" class="btn btn-sm btn-outline-primary" id="toggle-upload-form">
                    <i class="fas fa-plus"></i> New Upload
                </button>
            </div>
            <div class="card-body" id="upload-form-container" style="display: none;">
                <form id="upload-form" enctype="multipart/form-data">
                    <!-- File Format Selection -->
                    <div class="mb-4">
                        <h5>Step 1: Select Allowed File Types <span class="text-danger">*</span></h5>
                        <p class="text-muted">Click on the file types you want to upload. You can select multiple types.</p>
                        <div class="file-type-selector">
                            <?php foreach ($allowedTypes as $type): ?>
                                <div class="file-type-option" data-type="<?php echo $type; ?>">
                                    .<?php echo $type; ?>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <input type="hidden" id="selected-file-types" name="allowed_types" value="">
                        <div id="file-type-error" class="text-danger mt-2 d-none">Please select at least one file type.</div>
                    </div>
                    
                    <div class="mb-4">
                        <h5>Step 2: Upload Your File</h5>
                        <div id="upload-area" class="upload-area">
                            <i class="fas fa-cloud-upload-alt"></i>
                            <h3>Drag & Drop Files Here</h3>
                            <p>or click to browse files</p>
                            <input type="file" id="file-input" name="file" class="d-none">
                        </div>
                    </div>
                    
                    <div id="selected-file-info" class="mt-3 d-none">
                        <div class="card">
                            <div class="card-body">
                                <h5>Selected File:</h5>
                                <p><strong>Name:</strong> <span id="selected-file-name"></span></p>
                                <p><strong>Size:</strong> <span id="selected-file-size"></span></p>
                                <p><strong>Type:</strong> <span id="selected-file-type"></span></p>
                            </div>
                        </div>
                    </div>
                    
                    <div id="progress-container" class="mt-3 d-none">
                        <h5>Upload Progress:</h5>
                        <div class="progress">
                            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" style="width: 0%">0%</div>
                        </div>
                    </div>
                    
                    <div class="mt-3">
                        <button type="submit" id="upload-btn" class="btn btn-primary d-none">
                            <i class="fas fa-upload me-2"></i> Upload File
                        </button>
                    </div>
                </form>
                
                <div id="upload-result" class="mt-3"></div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                <h4>Recent Activity</h4>
            </div>
            <div class="card-body">
                <div class="activity-timeline">
                    <?php if (count($files) > 0): ?>
                        <?php 
                        $recentFiles = array_slice($files, 0, 5);
                        foreach ($recentFiles as $file): 
                        ?>
                            <div class="activity-item">
                                <div class="activity-icon">
                                    <i class="fas fa-upload"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">
                                        You uploaded <strong><?php echo $file['original_filename']; ?></strong>
                                    </div>
                                    <div class="activity-time">
                                        <?php echo timeAgo($file['upload_date']); ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p>No recent activity to display.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="mb-0">Your Files</h4>
                <span class="badge bg-primary"><?php echo count($files); ?> files</span>
            </div>
            <div class="card-body">
                <?php if (count($files) > 0): ?>
                    <div class="file-list">
                        <?php foreach ($files as $file): ?>
                            <div class="file-item" id="file-item-<?php echo $file['id']; ?>">
                                <div class="file-icon">
                                    <?php 
                                    $iconClass = 'fa-file';
                                    $ext = pathinfo($file['original_filename'], PATHINFO_EXTENSION);
                                    
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
                                    <i class="fas <?php echo $iconClass; ?>"></i>
                                </div>
                                <div class="file-info">
                                    <div class="file-name"><?php echo $file['original_filename']; ?></div>
                                    <div class="file-meta">
                                        <span><?php echo formatFileSize($file['file_size']); ?></span> • 
                                        <span><?php echo date('M d, Y', strtotime($file['upload_date'])); ?></span> • 
                                        <span><?php echo $file['download_count']; ?> downloads</span>
                                    </div>
                                </div>
                                <div class="file-actions dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v"></i>
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end">
                                        <li>
                                            <a class="dropdown-item" href="download.php?link=<?php echo $file['download_link']; ?>" target="_blank">
                                                <i class="fas fa-download me-2"></i> Download
                                            </a>
                                        </li>
                                        <li>
                                            <button class="dropdown-item" onclick="copyToClipboard('link-<?php echo $file['id']; ?>')">
                                                <i class="fas fa-copy me-2"></i> Copy Link
                                            </button>
                                            <input type="text" id="link-<?php echo $file['id']; ?>" value="<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/download.php?link=' . $file['download_link']; ?>" class="d-none">
                                        </li>
                                        <li>
                                            <button class="dropdown-item" data-bs-toggle="modal" data-bs-target="#emailLinkModal" onclick="prepareEmailModal('<?php echo 'http://' . $_SERVER['HTTP_HOST'] . '/download.php?link=' . $file['download_link']; ?>')">
                                                <i class="fas fa-envelope me-2"></i> Email Link
                                            </button>
                                        </li>
                                        <li><hr class="dropdown-divider"></li>
                                        
                                    </ul>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-file-upload fa-3x text-muted mb-3"></i>
                        <p>You haven't uploaded any files yet.</p>
                        <button type="button" class="btn btn-primary" id="show-upload-form">
                            <i class="fas fa-plus me-2"></i> Upload Your First File
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card mt-4">
            <div class="card-header">
                <h4>Storage Usage</h4>
            </div>
            <div class="card-body">
                <?php
                // Calculate total storage used
                $totalSize = 0;
                foreach ($files as $file) {
                    $totalSize += $file['file_size'];
                }
                
                // Calculate percentage (assuming 1GB limit)
                $storageLimit = 1073741824; // 1GB in bytes
                $usedPercentage = ($totalSize / $storageLimit) * 100;
                ?>
                
                <div class="storage-info mb-3">
                    <div class="d-flex justify-content-between mb-1">
                        <span>Storage Used</span>
                        <span><?php echo formatFileSize($totalSize); ?> / 1 GB</span>
                    </div>
                    <div class="progress">
                        <div class="progress-bar <?php echo $usedPercentage > 90 ? 'bg-danger' : 'bg-primary'; ?>" role="progressbar" style="width: <?php echo $usedPercentage; ?>%" aria-valuenow="<?php echo $usedPercentage; ?>" aria-valuemin="0" aria-valuemax="100"></div>
                    </div>
                </div>
                
                <div class="storage-stats">
                    <div class="row">
                        <div class="col-6">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo count($files); ?></div>
                                <div class="stat-label">Files</div>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="stat-item">
                                <?php
                                $totalDownloads = 0;
                                foreach ($files as $file) {
                                    $totalDownloads += $file['download_count'];
                                }
                                ?>
                                <div class="stat-value"><?php echo $totalDownloads; ?></div>
                                <div class="stat-label">Downloads</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Email Link Modal -->
<div class="modal fade" id="emailLinkModal" tabindex="-1" aria-labelledby="emailLinkModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="emailLinkModalLabel">Email Download Link</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="email-link-form">
                    <input type="hidden" name="download_link" id="email-download-link">
                    <div class="mb-3">
                        <label for="recipient-email" class="form-label">Recipient Email</label>
                        <input type="email" class="form-control" id="recipient-email" name="recipient_email" required>
                    </div>
                    <div class="mb-3">
                        <label for="email-message" class="form-label">Message (Optional)</label>
                        <textarea class="form-control" id="email-message" name="message" rows="3"></textarea>
                    </div>
                    <div id="email-result"></div>
                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Send Email</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Add this script at the end of the file -->
<script>
    $(document).ready(function() {
        // Make sure the toggle button works
        $('#toggle-upload-form, #show-upload-form').on('click', function() {
            $('#upload-form-container').slideToggle();
            
            // Scroll to the form if it's being shown
            if ($('#upload-form-container').is(':visible')) {
                $('html, body').animate({
                    scrollTop: $('#upload-form-container').offset().top - 20
                }, 500);
            }
        });
    });
    
    // Helper function for time ago display
    function timeAgo(dateString) {
        const date = new Date(dateString);
        const now = new Date();
        const seconds = Math.floor((now - date) / 1000);
        
        let interval = Math.floor(seconds / 31536000);
        if (interval >= 1) {
            return interval + " year" + (interval === 1 ? "" : "s") + " ago";
        }
        
        interval = Math.floor(seconds / 2592000);
        if (interval >= 1) {
            return interval + " month" + (interval === 1 ? "" : "s") + " ago";
        }
        
        interval = Math.floor(seconds / 86400);
        if (interval >= 1) {
            return interval + " day" + (interval === 1 ? "" : "s") + " ago";
        }
        
        interval = Math.floor(seconds / 3600);
        if (interval >= 1) {
            return interval + " hour" + (interval === 1 ? "" : "s") + " ago";
        }
        
        interval = Math.floor(seconds / 60);
        if (interval >= 1) {
            return interval + " minute" + (interval === 1 ? "" : "s") + " ago";
        }
        
        return "just now";
    }
</script>

<?php
// Add this function to includes/functions.php
function timeAgo($datetime) {
    $time = strtotime($datetime);
    $now = time();
    $diff = $now - $time;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 2592000) {
        $weeks = floor($diff / 604800);
        return $weeks . ' week' . ($weeks > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 31536000) {
        $months = floor($diff / 2592000);
        return $months . ' month' . ($months > 1 ? 's' : '') . ' ago';
    } else {
        $years = floor($diff / 31536000);
        return $years . ' year' . ($years > 1 ? 's' : '') . ' ago';
    }
}
?>

<?php include 'includes/footer.php'; ?>

