<?php
/**
 * Lab Materials Download Page
 * Provide students with required files
 */

require_once 'includes/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lab Materials - <?php echo LAB_TITLE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00a86b;
            --bg-dark: #0f0f23;
            --bg-card: #1a1a2e;
        }
        body {
            background: linear-gradient(135deg, var(--bg-dark) 0%, #1a1a2e 100%);
            color: #e0e0e0;
            min-height: 100vh;
        }
        .materials-container {
            max-width: 800px;
            margin: 50px auto;
            padding: 0 20px;
        }
        .card {
            background: var(--bg-card);
            border: none;
            border-radius: 12px;
        }
        .card-header {
            background: rgba(0,168,107,0.2);
            border-bottom: 1px solid rgba(255,255,255,0.1);
            border-radius: 12px 12px 0 0 !important;
        }
        .list-group-item {
            background: transparent;
            border-color: rgba(255,255,255,0.1);
            color: #e0e0e0;
        }
        .btn-download {
            background: var(--primary-color);
            border: none;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            color: white;
        }
        .btn-download:hover {
            background: #00c97b;
            color: white;
        }
        .file-icon {
            font-size: 1.5rem;
            margin-right: 1rem;
            color: var(--primary-color);
        }
        .alert-info {
            background: rgba(0,168,107,0.15);
            border: 1px solid rgba(0,168,107,0.3);
            color: var(--primary-color);
        }
        .back-link {
            color: var(--primary-color);
            text-decoration: none;
        }
        .back-link:hover {
            color: #00c97b;
        }
    </style>
</head>
<body>
    <div class="materials-container">
        <a href="index.php" class="back-link mb-4 d-inline-block">
            <i class="bi bi-arrow-left me-2"></i>Back to Lab
        </a>
        
        <h1 class="mb-4">
            <i class="bi bi-download me-3" style="color: var(--primary-color);"></i>
            Lab Materials
        </h1>
        
        <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle me-2"></i>
            Download the required files below before starting the lab exercises.
        </div>
        
        <!-- Required Files -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-file-earmark-zip me-2"></i>
                    Required PCAP Files
                </h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-binary file-icon"></i>
                        <div>
                            <strong>Exercise.pcapng</strong>
                            <br><small class="text-muted">Main exercise file - Use this to answer questions</small>
                        </div>
                    </div>
                    <a href="assets/Exercise.pcapng" class="btn btn-download" download>
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                </li>
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-file-earmark-binary file-icon"></i>
                        <div>
                            <strong>http1.pcapng</strong>
                            <br><small class="text-muted">Demo file - Use this to follow screenshots</small>
                        </div>
                    </div>
                    <a href="assets/http1.pcapng" class="btn btn-download" download>
                        <i class="bi bi-download me-1"></i> Download
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Software Requirements -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-gear me-2"></i>
                    Required Software
                </h5>
            </div>
            <ul class="list-group list-group-flush">
                <li class="list-group-item d-flex justify-content-between align-items-center">
                    <div class="d-flex align-items-center">
                        <i class="bi bi-hdd-network file-icon"></i>
                        <div>
                            <strong>Wireshark</strong>
                            <br><small class="text-muted">Network protocol analyzer</small>
                        </div>
                    </div>
                    <a href="https://www.wireshark.org/download.html" target="_blank" class="btn btn-download">
                        <i class="bi bi-box-arrow-up-right me-1"></i> Get Wireshark
                    </a>
                </li>
            </ul>
        </div>
        
        <!-- Instructions -->
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">
                    <i class="bi bi-lightbulb me-2"></i>
                    Setup Instructions
                </h5>
            </div>
            <div class="card-body">
                <ol class="mb-0">
                    <li class="mb-2">Install Wireshark on your system (Windows/Linux/Mac)</li>
                    <li class="mb-2">Download both PCAP files above</li>
                    <li class="mb-2">Open <code>Exercise.pcapng</code> in Wireshark to begin the lab</li>
                    <li class="mb-2">Use <code>http1.pcapng</code> to practice following the tutorial steps</li>
                    <li>Return to the <a href="index.php" class="text-success">lab</a> and answer the questions</li>
                </ol>
            </div>
        </div>
        
        <!-- Note for Instructors -->
        <div class="alert mt-4" style="background: rgba(255,193,7,0.15); border: 1px solid rgba(255,193,7,0.3); color: #ffc107;">
            <h6 class="alert-heading">
                <i class="bi bi-person-badge me-2"></i>Note for Instructors
            </h6>
            <p class="mb-0">
                Place the PCAP files in the <code>assets/</code> directory. You can obtain these files from:
            </p>
            <ul class="mb-0 mt-2">
                <li>TryHackMe Wireshark: The Basics room (if subscribed)</li>
                <li>Wireshark sample captures: <a href="https://wiki.wireshark.org/SampleCaptures" target="_blank" class="text-warning">wiki.wireshark.org/SampleCaptures</a></li>
                <li>Create your own captures with similar characteristics</li>
            </ul>
        </div>
        
        <!-- Footer -->
        <div class="text-center text-muted py-4">
            <small>
                <i class="bi bi-shield-check me-1"></i>
                ICT Academy of Kerala - Cybersecurity Training Program
            </small>
        </div>
    </div>
</body>
</html>
