<?php
/**
 * Admin Panel - Student Progress Tracking
 * For ICT Academy of Kerala Instructors
 * 
 * Note: This is a basic admin panel. For production use,
 * implement proper authentication and database storage.
 */

session_start();
require_once 'includes/config.php';

// Simple admin authentication (change these credentials!)
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', 'ictacademy2024'); // Change this!

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    if ($_POST['username'] === ADMIN_USER && $_POST['password'] === ADMIN_PASS) {
        $_SESSION['admin_logged_in'] = true;
    } else {
        $login_error = 'Invalid credentials';
    }
}

// Handle logout
if (isset($_GET['logout'])) {
    unset($_SESSION['admin_logged_in']);
    header('Location: admin.php');
    exit;
}

// Check if logged in
$is_logged_in = isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;

// Get current session progress for demo (in production, fetch from database)
$current_progress = getTotalProgress();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - Wireshark Lab</title>
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
        
        .login-container {
            max-width: 400px;
            margin: 100px auto;
        }
        
        .admin-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 2rem;
        }
        
        .form-control {
            background: rgba(0,0,0,0.3);
            border: 1px solid rgba(255,255,255,0.1);
            color: white;
        }
        
        .form-control:focus {
            background: rgba(0,0,0,0.4);
            border-color: var(--primary-color);
            color: white;
            box-shadow: 0 0 0 0.2rem rgba(0,168,107,0.25);
        }
        
        .btn-primary {
            background: var(--primary-color);
            border: none;
        }
        
        .btn-primary:hover {
            background: #00c97b;
        }
        
        .navbar {
            background: var(--bg-card) !important;
            border-bottom: 2px solid var(--primary-color);
        }
        
        .stat-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1.5rem;
            text-align: center;
        }
        
        .stat-number {
            font-size: 2.5rem;
            font-weight: bold;
            color: var(--primary-color);
        }
        
        .stat-label {
            color: rgba(255,255,255,0.7);
            font-size: 0.9rem;
        }
        
        .table-dark {
            --bs-table-bg: var(--bg-card);
            --bs-table-border-color: rgba(255,255,255,0.1);
        }
        
        .progress {
            height: 8px;
            background: rgba(255,255,255,0.1);
        }
        
        .progress-bar {
            background: var(--primary-color);
        }
        
        .alert-info {
            background: rgba(0,168,107,0.15);
            border: 1px solid rgba(0,168,107,0.3);
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <?php if (!$is_logged_in): ?>
    <!-- Login Form -->
    <div class="login-container">
        <div class="admin-card">
            <div class="text-center mb-4">
                <i class="bi bi-shield-lock" style="font-size: 3rem; color: var(--primary-color);"></i>
                <h3 class="mt-3">Admin Login</h3>
                <p class="text-muted">Wireshark Lab Admin Panel</p>
            </div>
            
            <?php if (isset($login_error)): ?>
                <div class="alert alert-danger"><?php echo $login_error; ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input type="text" name="username" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <button type="submit" name="login" class="btn btn-primary w-100">
                    <i class="bi bi-box-arrow-in-right me-2"></i>Login
                </button>
            </form>
            
            <div class="text-center mt-4">
                <a href="index.php" class="text-muted">
                    <i class="bi bi-arrow-left me-1"></i>Back to Lab
                </a>
            </div>
        </div>
    </div>
    
    <?php else: ?>
    <!-- Admin Dashboard -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="bi bi-hdd-network me-2"></i>Wireshark Lab Admin
            </a>
            <div class="navbar-nav ms-auto">
                <a class="nav-link" href="index.php">
                    <i class="bi bi-eye me-1"></i>View Lab
                </a>
                <a class="nav-link" href="?logout=1">
                    <i class="bi bi-box-arrow-right me-1"></i>Logout
                </a>
            </div>
        </div>
    </nav>
    
    <div class="container py-4">
        <h2 class="mb-4">
            <i class="bi bi-speedometer2 me-2"></i>Dashboard
        </h2>
        
        <!-- Stats Cards -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo count($TASKS); ?></div>
                    <div class="stat-label">Total Tasks</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $current_progress['total']; ?></div>
                    <div class="stat-label">Total Questions</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $current_progress['completed']; ?></div>
                    <div class="stat-label">Answered (Current Session)</div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $current_progress['percentage']; ?>%</div>
                    <div class="stat-label">Completion Rate</div>
                </div>
            </div>
        </div>
        
        <!-- Info Alert -->
        <div class="alert alert-info mb-4">
            <i class="bi bi-info-circle me-2"></i>
            <strong>Note:</strong> This is a session-based tracking system. For persistent multi-user tracking, 
            integrate with a database (MySQL/PostgreSQL) and implement user authentication.
        </div>
        
        <!-- Task Progress Table -->
        <div class="admin-card">
            <h4 class="mb-3">
                <i class="bi bi-list-check me-2"></i>Task Progress Overview
            </h4>
            
            <table class="table table-dark table-hover">
                <thead>
                    <tr>
                        <th>Task</th>
                        <th>Title</th>
                        <th>Questions</th>
                        <th>Progress</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($TASKS as $id => $task): ?>
                        <?php 
                        $progress = getTaskProgress($id);
                        $percentage = $progress['total'] > 0 ? round(($progress['completed'] / $progress['total']) * 100) : 0;
                        ?>
                        <tr>
                            <td><?php echo $id; ?></td>
                            <td><?php echo htmlspecialchars($task['title']); ?></td>
                            <td><?php echo $progress['total']; ?></td>
                            <td style="width: 200px;">
                                <div class="progress">
                                    <div class="progress-bar" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <small class="text-muted"><?php echo $progress['completed']; ?>/<?php echo $progress['total']; ?></small>
                            </td>
                            <td>
                                <?php if ($progress['completed'] === $progress['total']): ?>
                                    <span class="badge bg-success">Complete</span>
                                <?php elseif ($progress['completed'] > 0): ?>
                                    <span class="badge bg-warning text-dark">In Progress</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Not Started</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Question Details -->
        <div class="admin-card mt-4">
            <h4 class="mb-3">
                <i class="bi bi-question-circle me-2"></i>All Questions
            </h4>
            
            <table class="table table-dark table-sm">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Task</th>
                        <th>Question</th>
                        <th>Answer</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($TASKS as $task_id => $task): ?>
                        <?php foreach ($task['questions'] as $q): ?>
                            <?php $is_answered = isset($_SESSION['progress'][$q['id']]) && $_SESSION['progress'][$q['id']]; ?>
                            <tr>
                                <td><code><?php echo $q['id']; ?></code></td>
                                <td><?php echo $task_id; ?></td>
                                <td style="max-width: 300px;">
                                    <?php echo strip_tags($q['question']); ?>
                                </td>
                                <td>
                                    <code class="text-success"><?php echo htmlspecialchars($q['answer']); ?></code>
                                </td>
                                <td>
                                    <?php if ($is_answered): ?>
                                        <i class="bi bi-check-circle-fill text-success"></i>
                                    <?php else: ?>
                                        <i class="bi bi-circle text-muted"></i>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Database Integration Guide -->
        <div class="admin-card mt-4">
            <h4 class="mb-3">
                <i class="bi bi-database me-2"></i>Database Integration (Optional)
            </h4>
            
            <p>To track multiple students persistently, add this table to your MySQL database:</p>
            
            <pre style="background: #0d1117; padding: 1rem; border-radius: 8px; overflow-x: auto;"><code>CREATE TABLE student_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(100) NOT NULL,
    student_name VARCHAR(255),
    question_id VARCHAR(50) NOT NULL,
    is_correct TINYINT(1) DEFAULT 0,
    attempts INT DEFAULT 1,
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_attempt (student_id, question_id)
);

CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(100) UNIQUE NOT NULL,
    name VARCHAR(255) NOT NULL,
    email VARCHAR(255),
    batch VARCHAR(50),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);</code></pre>
        </div>
        
        <!-- Footer -->
        <div class="text-center text-muted py-4">
            <small>
                <i class="bi bi-shield-check me-1"></i>
                ICT Academy of Kerala - Cybersecurity Training Program
            </small>
        </div>
    </div>
    <?php endif; ?>
</body>
</html>
