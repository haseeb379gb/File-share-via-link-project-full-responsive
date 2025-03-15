<?php include 'includes/header.php'; ?>

<div class="container py-5">
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <div class="card shadow-sm mb-5">
                <div class="card-body p-5">
                    <h1 class="text-center mb-4">Frequently Asked Questions</h1>
                    
                    <div class="accordion" id="faqAccordion">
                        <!-- General Questions -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingOne">
                                <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                    What is FileShare?
                                </button>
                            </h2>
                            <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    FileShare is a simple, secure, and fast way to share your files with anyone. You can upload files up to 1GB in size and share them with a unique link that you can send to anyone.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTwo">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                    Do I need to create an account to use FileShare?
                                </button>
                            </h2>
                            <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    No, you don't need to create an account to use the basic features of FileShare. You can upload and share files as a guest. However, creating an account gives you additional benefits like tracking your uploads, managing your files, and more.
                                </div>
                            </div>
                        </div>
                        
                        <!-- File Upload Questions -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingThree">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                    What is the maximum file size I can upload?
                                </button>
                            </h2>
                            <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    You can upload files up to 1GB in size. If you need to share larger files, you can compress them into multiple smaller archives or contact us for custom solutions.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFour">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                                    What file types can I upload?
                                </button>
                            </h2>
                            <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    FileShare supports a wide range of file types including documents (PDF, DOC, DOCX), spreadsheets (XLS, XLSX), presentations (PPT, PPTX), images (JPG, PNG, GIF), and archives (ZIP, RAR). If you need to share a file type that is not listed, please contact us.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Security Questions -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingFive">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFive" aria-expanded="false" aria-controls="collapseFive">
                                    How secure are my files?
                                </button>
                            </h2>
                            <div id="collapseFive" class="accordion-collapse collapse" aria-labelledby="headingFive" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    We take security seriously. Your files are stored securely and can only be accessed by someone with the unique download link. We do not analyze or review the content of your files. Additionally, all files are automatically deleted after 7 days to ensure your privacy.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSix">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSix" aria-expanded="false" aria-controls="collapseSix">
                                    Who can access my files?
                                </button>
                            </h2>
                            <div id="collapseSix" class="accordion-collapse collapse" aria-labelledby="headingSix" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Only people who have the unique download link can access your files. The links are randomly generated and difficult to guess. You control who you share the link with.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Link and Sharing Questions -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSeven">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseSeven" aria-expanded="false" aria-controls="collapseSeven">
                                    How long are my files available for download?
                                </button>
                            </h2>
                            <div id="collapseSeven" class="accordion-collapse collapse" aria-labelledby="headingSeven" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Files uploaded to FileShare are available for 7 days. After that, they are automatically deleted from our servers. If you need longer storage, consider creating an account for extended options.
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingEight">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseEight" aria-expanded="false" aria-controls="collapseEight">
                                    Can I email the download link directly from FileShare?
                                </button>
                            </h2>
                            <div id="collapseEight" class="accordion-collapse collapse" aria-labelledby="headingEight" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Yes, after uploading a file, you can use our built-in email feature to send the download link directly to recipients. You can include a personalized message with the email as well.
                                </div>
                            </div>
                        </div>
                        
                        <!-- Account Questions -->
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingNine">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseNine" aria-expanded="false" aria-controls="collapseNine">
                                    What are the benefits of creating an account?
                                </button>
                            </h2>
                            <div id="collapseNine" class="accordion-collapse collapse" aria-labelledby="headingNine" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    Creating an account gives you several benefits:
                                    <ul>
                                        <li>Track all your uploaded files in one place</li>
                                        <li>See download statistics for your files</li>
                                        <li>Manage and delete your files</li>
                                        <li>Extended storage options (coming soon)</li>
                                        <li>Password protection for your files (coming soon)</li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                        
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingTen">
                                <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTen" aria-expanded="false" aria-controls="collapseTen">
                                    How do I delete my account?
                                </button>
                            </h2>
                            <div id="collapseTen" class="accordion-collapse collapse" aria-labelledby="headingTen" data-bs-parent="#faqAccordion">
                                <div class="accordion-body">
                                    If you wish to delete your account, please contact us at support@fileshare.com with your request. We will process your request and delete all your data from our servers.
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center">
                <h3>Still have questions?</h3>
                <p class="mb-4">Contact our support team and we'll get back to you as soon as possible.</p>
                <a href="contact.php" class="btn btn-primary btn-lg">Contact Us</a>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

