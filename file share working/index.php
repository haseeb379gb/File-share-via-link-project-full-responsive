<?php include 'includes/header.php'; ?>

<div class="row justify-content-center">
  <div class="col-md-8">
      <div class="card">
          <div class="card-body text-center">
              <h1 class="mb-4">Welcome to FileShare</h1>
              <p class="lead">Share files up to 1GB with anyone, anywhere, anytime.</p>
              <p>Our platform allows you to easily upload and share files with a unique link.</p>
              
              <?php if (isLoggedIn()): ?>
                  <a href="dashboard.php" class="btn btn-primary btn-lg mt-3">Go to Dashboard</a>
              <?php endif; ?>
          </div>
      </div>
      
      <!-- Quick Upload Section -->
      <div class="card mt-4">
          <div class="card-header">
              <h5>Upload Your File</h5>
          </div>
          <div class="card-body">
              <form id="guest-upload-form" enctype="multipart/form-data">
                  <!-- File Format Selection -->
                  <div class="mb-4">
                      <h5>Step 1: Select Allowed File Types <span class="text-danger">*</span></h5>
                      <p class="text-muted">Click on the file types you want to upload. You can select multiple types.</p>
                      <div class="file-type-selector">
                          <?php
                          // Get allowed file types from database
                          $stmt = $conn->prepare("SELECT extension, description FROM file_types ORDER BY extension");
                          $stmt->execute();
                          $fileTypes = $stmt->fetchAll(PDO::FETCH_ASSOC);
                          
                          foreach ($fileTypes as $type): ?>
                              <div class="file-type-option" data-type="<?php echo $type['extension']; ?>">
                                  <i class="fas fa-file"></i> .<?php echo $type['extension']; ?>
                                  <span class="file-type-tooltip"><?php echo $type['description']; ?></span>
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
                      <button type="submit" id="upload-btn" class="btn btn-primary btn-lg d-none">
                          <i class="fas fa-upload me-2"></i> Upload File
                      </button>
                  </div>
              </form>
              
              <div id="upload-result" class="mt-3"></div>
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

<?php include 'includes/footer.php'; ?>

