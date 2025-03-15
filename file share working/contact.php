<?php
include 'includes/header.php';

$name = $email = $subject = $message = "";
$name_err = $email_err = $subject_err = $message_err = "";
$success_message = "";

// Processing form data when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate name
    if (empty(trim($_POST["name"]))) {
        $name_err = "Please enter your name.";
    } else {
        $name = sanitize($_POST["name"]);
    }
    
    // Validate email
    if (empty(trim($_POST["email"]))) {
        $email_err = "Please enter your email.";
    } else {
        $email = sanitize($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $email_err = "Please enter a valid email address.";
        }
    }
    
    // Validate subject
    if (empty(trim($_POST["subject"]))) {
        $subject_err = "Please enter a subject.";
    } else {
        $subject = sanitize($_POST["subject"]);
    }
    
    // Validate message
    if (empty(trim($_POST["message"]))) {
        $message_err = "Please enter your message.";
    } else {
        $message = sanitize($_POST["message"]);
    }
    
    // Check input errors before sending email
    if (empty($name_err) && empty($email_err) && empty($subject_err) && empty($message_err)) {
        // In a real application, you would send an email here
        // For demonstration purposes, we'll just show a success message
        $success_message = "Your message has been sent successfully! We'll get back to you soon.";
        
        // Clear form fields
        $name = $email = $subject = $message = "";
    }
}
?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4">Contact Us</h1>
                    
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success"><?php echo $success_message; ?></div>
                    <?php endif; ?>
                    
                    <div class="row mb-5">
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="d-flex flex-column align-items-center text-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 mb-3">
                                    <i class="fas fa-map-marker-alt text-primary fa-2x"></i>
                                </div>
                                <h5>Our Location</h5>
                                <p class="mb-0">123 File Street</p>
                                <p>Digital City, 10001</p>
                            </div>
                        </div>
                        <div class="col-md-4 mb-4 mb-md-0">
                            <div class="d-flex flex-column align-items-center text-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 mb-3">
                                    <i class="fas fa-envelope text-primary fa-2x"></i>
                                </div>
                                <h5>Email Us</h5>
                                <p class="mb-0">support@fileshare.com</p>
                                <p>info@fileshare.com</p>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="d-flex flex-column align-items-center text-center">
                                <div class="rounded-circle bg-primary bg-opacity-10 p-3 mb-3">
                                    <i class="fas fa-phone text-primary fa-2x"></i>
                                </div>
                                <h5>Call Us</h5>
                                <p class="mb-0">(123) 456-7890</p>
                                <p>(123) 456-7891</p>
                            </div>
                        </div>
                    </div>
                    
                    <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Your Name</label>
                                    <input type="text" name="name" id="name" class="form-control <?php echo (!empty($name_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $name; ?>">
                                    <div class="invalid-feedback"><?php echo $name_err; ?></div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="email" class="form-label">Your Email</label>
                                    <input type="email" name="email" id="email" class="form-control <?php echo (!empty($email_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $email; ?>">
                                    <div class="invalid-feedback"><?php echo $email_err; ?></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="subject" class="form-label">Subject</label>
                                    <input type="text" name="subject" id="subject" class="form-control <?php echo (!empty($subject_err)) ? 'is-invalid' : ''; ?>" value="<?php echo $subject; ?>">
                                    <div class="invalid-feedback"><?php echo $subject_err; ?></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="mb-3">
                                    <label for="message" class="form-label">Message</label>
                                    <textarea name="message" id="message" rows="5" class="form-control <?php echo (!empty($message_err)) ? 'is-invalid' : ''; ?>"><?php echo $message; ?></textarea>
                                    <div class="invalid-feedback"><?php echo $message_err; ?></div>
                                </div>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-primary w-100 py-3">Send Message</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            
            <div class="mt-5">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3022.215662246728!2d-73.98784492426285!3d40.75798657138946!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x89c25855c6480299%3A0x55194ec5a1ae072e!2sTimes%20Square!5e0!3m2!1sen!2sus!4v1710459283105!5m2!1sen!2sus" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

