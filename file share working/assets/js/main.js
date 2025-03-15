$(document).ready(() => {
  // File upload area functionality
  const uploadArea = document.getElementById("upload-area")
  const fileInput = document.getElementById("file-input")
  const progressBar = document.getElementById("progress-bar")
  const progressContainer = document.getElementById("progress-container")
  const selectedFileTypesInput = document.getElementById("selected-file-types")
  const fileTypeError = document.getElementById("file-type-error")

  // Fix file type selection
  $(document).on("click", ".file-type-option", function (e) {
    e.stopPropagation() // Prevent event bubbling
    $(this).toggleClass("selected")
    updateSelectedFileTypes()

    // Hide error message if at least one type is selected
    if (getSelectedFileTypes().length > 0) {
      $("#file-type-error").addClass("d-none")
    }
  })

  // Fix New Upload button
  $(document).on("click", "#toggle-upload-form, #show-upload-form", () => {
    console.log("Upload form toggle clicked")
    $("#upload-form-container").slideToggle()

    // Scroll to the form if it's being shown
    if ($("#upload-form-container").is(":visible")) {
      $("html, body").animate(
        {
          scrollTop: $("#upload-form-container").offset().top - 20,
        },
        500,
      )
    }
  })

  // Get selected file types
  function getSelectedFileTypes() {
    const selectedTypes = []
    $(".file-type-option.selected").each(function () {
      selectedTypes.push($(this).data("type"))
    })
    return selectedTypes
  }

  // Update selected file types input and file input accept attribute
  function updateSelectedFileTypes() {
    const selectedTypes = getSelectedFileTypes()
    $("#selected-file-types").val(selectedTypes.join(","))

    // Update file input accept attribute to filter files in the file dialog
    if (selectedTypes.length > 0) {
      const acceptTypes = selectedTypes.map((type) => "." + type).join(",")
      $("#file-input").attr("accept", acceptTypes)
    } else {
      $("#file-input").removeAttr("accept")
    }
  }

  if (uploadArea) {
    // Handle drag and drop events
    ;["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
      uploadArea.addEventListener(eventName, preventDefaults, false)
    })

    function preventDefaults(e) {
      e.preventDefault()
      e.stopPropagation()
    }
    ;["dragenter", "dragover"].forEach((eventName) => {
      uploadArea.addEventListener(eventName, highlight, false)
    })
    ;["dragleave", "drop"].forEach((eventName) => {
      uploadArea.addEventListener(eventName, unhighlight, false)
    })

    function highlight() {
      uploadArea.classList.add("bg-light")
    }

    function unhighlight() {
      uploadArea.classList.remove("bg-light")
    }

    // Handle file drop
    uploadArea.addEventListener("drop", handleDrop, false)

    function handleDrop(e) {
      const dt = e.dataTransfer
      const files = dt.files

      if (files.length > 0) {
        fileInput.files = files
        handleFiles(files)
      }
    }

    // Handle file selection via input
    uploadArea.addEventListener("click", (e) => {
      // Don't trigger if clicking on a file-type-option
      if ($(e.target).closest(".file-type-option").length) {
        return
      }

      // Check if any file types are selected
      if (getSelectedFileTypes().length === 0) {
        $("#file-type-error").removeClass("d-none")
        return
      }

      fileInput.click()
    })

    fileInput.addEventListener("change", function () {
      if (this.files.length > 0) {
        handleFiles(this.files)
      }
    })
  }

  // Handle file upload
  function handleFiles(files) {
    const file = files[0] // Only handle the first file for now

    // Check file size (1GB limit)
    if (file.size > 1073741824) {
      alert("File size exceeds the 1GB limit.")
      return
    }

    // Get file extension
    const fileExt = file.name.split(".").pop().toLowerCase()

    // Check if file type is allowed based on user selection
    const selectedTypes = getSelectedFileTypes()
    if (selectedTypes.length > 0 && !selectedTypes.includes(fileExt)) {
      alert(
        `Selected file type (.${fileExt}) is not allowed. Please select one of the following types: ${selectedTypes.join(", ")}`,
      )
      return
    }

    // Show file info
    $("#selected-file-name").text(file.name)
    $("#selected-file-size").text(formatFileSize(file.size))
    $("#selected-file-type").text("." + fileExt)
    $("#selected-file-info").removeClass("d-none")

    // Show upload button
    $("#upload-btn").removeClass("d-none")
  }

  // Format file size
  function formatFileSize(bytes) {
    if (bytes >= 1073741824) {
      return (bytes / 1073741824).toFixed(2) + " GB"
    } else if (bytes >= 1048576) {
      return (bytes / 1048576).toFixed(2) + " MB"
    } else if (bytes >= 1024) {
      return (bytes / 1024).toFixed(2) + " KB"
    } else {
      return bytes + " bytes"
    }
  }

  // Handle file upload via AJAX for both dashboard and guest upload
  $("#upload-form, #guest-upload-form").on("submit", function (e) {
    e.preventDefault()

    // Check if any file types are selected
    if (getSelectedFileTypes().length === 0) {
      $("#file-type-error").removeClass("d-none")
      return
    }

    // Check if a file is selected
    if (!fileInput.files || fileInput.files.length === 0) {
      $("#upload-result").html(`
        <div class="alert alert-danger">
          Please select a file to upload.
        </div>
      `)
      return
    }

    // Disable the upload button to prevent multiple submissions
    $("#upload-btn").prop("disabled", true).html('<i class="fas fa-spinner fa-spin me-2"></i> Uploading...')

    const formData = new FormData(this)

    // Clear previous results
    $("#upload-result").html("")

    // Show progress container
    progressContainer.classList.remove("d-none")
    progressBar.style.width = "0%"
    progressBar.textContent = "0%"

    $.ajax({
      url: "upload.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      xhr: () => {
        const xhr = new window.XMLHttpRequest()

        xhr.upload.addEventListener("progress", (e) => {
          if (e.lengthComputable) {
            const percent = Math.round((e.loaded / e.total) * 100)

            // Update progress bar
            progressBar.style.width = percent + "%"
            progressBar.textContent = percent + "%"

            // If complete
            if (percent === 100) {
              progressBar.textContent = "Processing..."
            }
          }
        })

        return xhr
      },
      success: (response) => {
        console.log("Success response:", response)

        // Re-enable the upload button
        $("#upload-btn").prop("disabled", false).html('<i class="fas fa-upload me-2"></i> Upload File')

        // Hide progress container
        progressContainer.classList.add("d-none")

        // Handle the response
        if (response.status === "success") {
          // Show success message
          $("#upload-result").html(`
            <div class="alert alert-success">
              ${response.message}
            </div>
            <div class="share-link-box">
              <h5>Share this link with others:</h5>
              <div class="input-group mb-3">
                <input type="text" class="form-control" value="${response.download_link}" id="download-link" readonly>
                <button class="btn btn-outline-secondary copy-link-btn" type="button" onclick="copyToClipboard('download-link')">
                  <i class="fas fa-copy"></i> Copy
                </button>
              </div>
              <div class="mt-3 d-flex gap-2">
                <a href="${response.download_link}&download=true" class="btn btn-success" target="_blank">
                  <i class="fas fa-download"></i> Download
                </a>
                <button class="btn btn-primary email-link-btn" type="button" data-bs-toggle="modal" data-bs-target="#emailLinkModal">
                  <i class="fas fa-envelope"></i> Email Link
                </button>
              </div>
            </div>
          `)

          // Set the download link for the email modal
          $("#email-download-link").val(response.download_link)

          // Reset form
          $("#selected-file-info").addClass("d-none")
          $("#upload-btn").addClass("d-none")
          fileInput.value = ""

          // If we're on the dashboard, refresh the file list
          if (window.location.pathname.includes("dashboard.php")) {
            setTimeout(() => {
              window.location.reload()
            }, 3000)
          }
        } else {
          // Show error message
          $("#upload-result").html(`
            <div class="alert alert-danger">
              ${response.message || "An unknown error occurred."}
            </div>
          `)
        }
      },
      error: (xhr, status, error) => {
        console.error("AJAX error:", status, error)
        console.error("Response text:", xhr.responseText)

        // Re-enable the upload button
        $("#upload-btn").prop("disabled", false).html('<i class="fas fa-upload me-2"></i> Upload File')

        // Hide progress container
        progressContainer.classList.add("d-none")

        // Try to parse the error response
        let errorMessage = "An error occurred during upload. Please try again."

        try {
          const response = JSON.parse(xhr.responseText)
          if (response.message) {
            errorMessage = response.message
          }
        } catch (e) {
          // If we can't parse the response, use a generic error message
          console.error("Error parsing response:", e)
        }

        // Show error message
        $("#upload-result").html(`
          <div class="alert alert-danger">
            ${errorMessage}
          </div>
          <div class="alert alert-info">
            <strong>Troubleshooting:</strong>
            <ul>
              <li>Check if the file size is within limits (max 1GB)</li>
              <li>Make sure the file type is allowed</li>
              <li>Try a different browser</li>
              <li>Contact support if the problem persists</li>
            </ul>
            <p>For technical support, please check the server logs or run the <a href="test-upload.php" target="_blank">upload test</a>.</p>
          </div>
        `)
      },
    })
  })

  // Fix email link button click
  $(document).on("click", ".email-link-btn", () => {
    console.log("Email link button clicked")
    const downloadLink = $("#download-link").val()
    prepareEmailModal(downloadLink)
  })

  // Copy to clipboard functionality
  window.copyToClipboard = (elementId) => {
    const element = document.getElementById(elementId)
    element.select()

    try {
      // Use the newer clipboard API if available
      if (navigator.clipboard) {
        navigator.clipboard
          .writeText(element.value)
          .then(showCopySuccess)
          .catch((err) => {
            console.error("Could not copy text: ", err)
            // Fallback to the older method
            document.execCommand("copy")
            showCopySuccess()
          })
      } else {
        // Fallback for older browsers
        const successful = document.execCommand("copy")
        if (successful) {
          showCopySuccess()
        } else {
          console.error("Could not copy text")
        }
      }
    } catch (err) {
      console.error("Could not copy text: ", err)
    }

    function showCopySuccess() {
      // Show copied tooltip
      const copyBtn = document.querySelector(".copy-link-btn")
      const originalText = copyBtn.innerHTML
      copyBtn.innerHTML = '<i class="fas fa-check"></i> Copied!'

      // Create a toast notification
      const toast = document.createElement("div")
      toast.className = "position-fixed bottom-0 end-0 p-3"
      toast.style.zIndex = "5"
      toast.innerHTML = `
        <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
          <div class="toast-header bg-success text-white">
            <i class="fas fa-check me-2"></i>
            <strong class="me-auto">Success</strong>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast" aria-label="Close"></button>
          </div>
          <div class="toast-body">
            Link copied to clipboard successfully!
          </div>
        </div>
      `
      document.body.appendChild(toast)

      // Remove toast after 3 seconds
      setTimeout(() => {
        toast.remove()
        copyBtn.innerHTML = originalText
      }, 3000)
    }
  }

  // Prepare email modal with download link
  window.prepareEmailModal = (downloadLink) => {
    console.log("Preparing email modal with link:", downloadLink)

    // Clear previous results
    $("#email-result").html("")

    // Reset the form
    $("#email-link-form")[0].reset()

    // Set the download link
    $("#email-download-link").val(downloadLink)
  }

  // Update the email form submission
  $("#email-link-form").on("submit", function (e) {
    e.preventDefault()

    // Get the email address
    const recipientEmail = $("#recipient-email").val().trim()

    // Basic email validation
    if (!recipientEmail) {
      $("#email-result").html(`
        <div class="alert alert-danger">
          Please enter a recipient email address.
        </div>
      `)
      return
    }

    // Disable the submit button
    const submitBtn = $(this)
      .find('button[type="submit"]')
      .prop("disabled", true)
      .html('<i class="fas fa-spinner fa-spin me-2"></i> Sending...')

    const formData = new FormData(this)

    $.ajax({
      url: "share.php",
      type: "POST",
      data: formData,
      contentType: false,
      processData: false,
      success: (response) => {
        try {
          if (response.status === "success") {
            $("#email-result").html(`
              <div class="alert alert-success">
                ${response.message}
              </div>
            `)

            // Reset form
            $("#email-link-form")[0].reset()

            // Close modal after 2 seconds
            setTimeout(() => {
              $("#emailLinkModal").modal("hide")
              $("#email-result").html("")
            }, 2000)
          } else {
            $("#email-result").html(`
              <div class="alert alert-danger">
                ${response.message}
              </div>
            `)
          }
        } catch (e) {
          console.error(e)
          $("#email-result").html(`
            <div class="alert alert-danger">
              An error occurred while sending the email.
            </div>
          `)
        }
      },
      error: (xhr) => {
        let errorMessage = "An error occurred while sending the email."
        try {
          const response = JSON.parse(xhr.responseText)
          if (response.message) {
            errorMessage = response.message
          }
        } catch (e) {
          console.error("Error parsing response:", e)
        }

        $("#email-result").html(`
          <div class="alert alert-danger">
            ${errorMessage}
          </div>
        `)
      },
      complete: () => {
        // Re-enable the submit button
        submitBtn.prop("disabled", false).html("Send Email")
      },
    })
  })

  // Dashboard file actions
  $(document).on("click", ".delete-file-btn", function () {
    if (confirm("Are you sure you want to delete this file? This action cannot be undone.")) {
      const fileId = $(this).data("file-id")

      $.ajax({
        url: "delete_file.php",
        type: "POST",
        data: { file_id: fileId },
        success: (response) => {
          try {
            const data = JSON.parse(response)

            if (data.status === "success") {
              // Remove the file item from the list
              $("#file-item-" + fileId).fadeOut(300, function () {
                $(this).remove()

                // If no files left, show message
                if ($(".file-item").length === 0) {
                  $(".file-list").html("<p>You haven't uploaded any files yet.</p>")
                }
              })
            } else {
              alert(data.message)
            }
          } catch (e) {
            console.error(e)
            alert("An error occurred while deleting the file.")
          }
        },
        error: () => {
          alert("An error occurred while deleting the file.")
        },
      })
    }
  })
})

