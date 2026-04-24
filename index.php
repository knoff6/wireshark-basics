<?php
/**
 * Wireshark: The Basics - Self-Hosted Lab
 * Main Entry Point
 */

require_once 'includes/config.php';
require_once 'includes/db.php';

if (isset($_GET['logout']) && $settings['auth_mode']) {
    unset($_SESSION['student_id']);
    unset($_SESSION['student_name']);
    resetProgress();
    header('Location: index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['student_login']) && $settings['auth_mode']) {
    $student_id = trim($_POST['student_id']);
    $student_name = trim($_POST['student_name']);
    if (!empty($student_id) && !empty($student_name)) {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO students (student_id, name) VALUES (?, ?) ON DUPLICATE KEY UPDATE name = ?");
                $stmt->execute([$student_id, $student_name, $student_name]);
                
                $_SESSION['student_id'] = $student_id;
                $_SESSION['student_name'] = $student_name;
                
                // Load past progress
                $stmt = $pdo->prepare("SELECT question_id FROM student_progress WHERE student_id = ? AND is_correct = 1");
                $stmt->execute([$student_id]);
                $progress = $stmt->fetchAll(PDO::FETCH_COLUMN);
                
                resetProgress();
                foreach ($progress as $q_id) {
                    $_SESSION['progress'][$q_id] = true;
                }
                
                header('Location: index.php');
                exit;
            } catch (PDOException $e) {
                $login_error = "Database Error: " . $e->getMessage();
            }
        }
    } else {
        $login_error = "Please fill in all fields.";
    }
}

// Handle AJAX requests
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    if ($_POST['action'] === 'check_answer') {
        $task_id = intval($_POST['task_id']);
        $question_id = $_POST['question_id'];
        $answer = $_POST['answer'];
        
        $is_correct = checkAnswer($task_id, $question_id, $answer);
        
        if ($is_correct) {
            $_SESSION['progress'][$question_id] = true;
            global $settings, $pdo;
            if ($settings['auth_mode'] && isset($_SESSION['student_id']) && $pdo) {
                try {
                    $stmt = $pdo->prepare("INSERT IGNORE INTO student_progress (student_id, student_name, question_id, is_correct) VALUES (?, ?, ?, 1)");
                    $stmt->execute([$_SESSION['student_id'], $_SESSION['student_name'], $question_id]);
                } catch(PDOException $e) { }
            }
        }
        
        $task_progress = getTaskProgress($task_id);
        $total_progress = getTotalProgress();
        
        echo json_encode([
            'correct' => $is_correct,
            'task_progress' => $task_progress,
            'total_progress' => $total_progress,
            'task_complete' => isTaskComplete($task_id)
        ]);
        exit;
    }
    
    if ($_POST['action'] === 'reset_progress') {
        resetProgress();
        echo json_encode(['success' => true]);
        exit;
    }
    
    if ($_POST['action'] === 'get_hint') {
        $task_id = intval($_POST['task_id']);
        $question_id = $_POST['question_id'];
        
        foreach ($TASKS[$task_id]['questions'] as $question) {
            if ($question['id'] === $question_id) {
                echo json_encode(['hint' => $question['hint']]);
                exit;
            }
        }
        echo json_encode(['hint' => 'No hint available.']);
        exit;
    }
}

$current_task = isset($_GET['task']) ? intval($_GET['task']) : 1;
if ($current_task < 1 || $current_task > count($TASKS)) {
    $current_task = 1;
}

$total_progress = getTotalProgress();

if ($settings['auth_mode'] && !isset($_SESSION['student_id'])) {
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Login - <?php echo LAB_TITLE; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: linear-gradient(135deg, #0f0f23 0%, #1a1a2e 100%); color: #e0e0e0; min-height: 100vh; display: flex; align-items: center; }
        .login-card { background: #1a1a2e; border-radius: 12px; padding: 2rem; max-width: 400px; width: 100%; margin: 0 auto; box-shadow: 0 10px 30px rgba(0,0,0,0.5); border: 1px solid rgba(0,168,107,0.2); }
        .form-control { background: rgba(0,0,0,0.3); border: 1px solid rgba(255,255,255,0.1); color: white; }
        .form-control:focus { background: rgba(0,0,0,0.4); border-color: #00a86b; color: white; box-shadow: none; }
        .btn-primary { background: #00a86b; border: none; }
        .btn-primary:hover { background: #00c97b; }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-card">
            <h3 class="text-center mb-4" style="color: #00a86b;">Wireshark Lab Access</h3>
            <?php if (isset($login_error)): ?>
                <div class="alert alert-danger"><?php echo $login_error; ?></div>
            <?php endif; ?>
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Student ID (Roll Number)</label>
                    <input type="text" name="student_id" class="form-control" required placeholder="e.g. ICT001">
                </div>
                <div class="mb-4">
                    <label class="form-label">Full Name</label>
                    <input type="text" name="student_name" class="form-control" required placeholder="e.g. John Doe">
                </div>
                <button type="submit" name="student_login" class="btn btn-primary w-100">Start Lab</button>
            </form>
        </div>
    </div>
</body>
</html>
<?php
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo LAB_TITLE; ?> - ICT Academy Lab</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary-color: #00a86b;
            --secondary-color: #1a1a2e;
            --accent-color: #16213e;
            --success-color: #00c853;
            --warning-color: #ffc107;
            --danger-color: #ff5252;
            --text-light: #e0e0e0;
            --bg-dark: #0f0f23;
            --bg-card: #1a1a2e;
        }
        
        body {
            background: linear-gradient(135deg, var(--bg-dark) 0%, var(--secondary-color) 100%);
            color: var(--text-light);
            min-height: 100vh;
            font-family: 'Segoe UI', system-ui, -apple-system, sans-serif;
        }
        
        /* Header */
        .lab-header {
            background: rgba(26, 26, 46, 0.95);
            border-bottom: 2px solid var(--primary-color);
            padding: 1rem 0;
            position: sticky;
            top: 0;
            z-index: 1000;
            backdrop-filter: blur(10px);
        }
        
        .lab-logo {
            font-weight: bold;
            font-size: 1.5rem;
            color: var(--primary-color);
        }
        
        .lab-logo i {
            margin-right: 0.5rem;
        }
        
        /* Progress Bar */
        .progress-container {
            background: rgba(0,0,0,0.3);
            border-radius: 10px;
            padding: 0.5rem 1rem;
        }
        
        .progress {
            height: 8px;
            background: rgba(255,255,255,0.1);
            border-radius: 4px;
        }
        
        .progress-bar {
            background: linear-gradient(90deg, var(--primary-color), var(--success-color));
            border-radius: 4px;
            transition: width 0.5s ease;
        }
        
        /* Sidebar */
        .sidebar {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 1.5rem;
            position: sticky;
            top: 100px;
        }
        
        .task-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        
        .task-item {
            padding: 0.75rem 1rem;
            margin-bottom: 0.5rem;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            color: var(--text-light);
        }
        
        .task-item:hover {
            background: rgba(0, 168, 107, 0.2);
            color: var(--primary-color);
        }
        
        .task-item.active {
            background: var(--primary-color);
            color: white;
        }
        
        .task-item.completed .task-icon {
            background: var(--success-color);
        }
        
        .task-icon {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: rgba(255,255,255,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.8rem;
            flex-shrink: 0;
        }
        
        .task-item.active .task-icon {
            background: rgba(255,255,255,0.2);
        }
        
        /* Main Content */
        .content-card {
            background: var(--bg-card);
            border-radius: 12px;
            padding: 2rem;
            margin-bottom: 1.5rem;
        }
        
        .task-title {
            color: var(--primary-color);
            font-size: 1.75rem;
            margin-bottom: 0.5rem;
        }
        
        .task-badge {
            display: inline-block;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.8rem;
            background: rgba(0, 168, 107, 0.2);
            color: var(--primary-color);
            margin-bottom: 1rem;
        }
        
        .task-content {
            line-height: 1.8;
        }
        
        .task-content h3 {
            color: var(--primary-color);
            margin-top: 1.5rem;
            margin-bottom: 1rem;
        }
        
        .task-content h4 {
            color: #8bc34a;
            margin-top: 1.25rem;
            margin-bottom: 0.75rem;
        }
        
        .task-content code {
            background: rgba(0, 168, 107, 0.2);
            color: var(--primary-color);
            padding: 0.2rem 0.5rem;
            border-radius: 4px;
        }
        
        .task-content pre {
            background: #0d1117;
            padding: 1rem;
            border-radius: 8px;
            overflow-x: auto;
        }
        
        .task-content ul, .task-content ol {
            margin-bottom: 1rem;
        }
        
        .task-content li {
            margin-bottom: 0.5rem;
        }
        
        /* Questions */
        .questions-section {
            background: rgba(0, 0, 0, 0.2);
            border-radius: 12px;
            padding: 1.5rem;
            margin-top: 2rem;
        }
        
        .questions-title {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1.5rem;
            color: var(--primary-color);
        }
        
        .question-card {
            background: var(--bg-card);
            border-radius: 8px;
            padding: 1.25rem;
            margin-bottom: 1rem;
            border-left: 3px solid rgba(255,255,255,0.1);
            transition: border-color 0.3s ease;
        }
        
        .question-card.answered {
            border-left-color: var(--success-color);
        }
        
        .question-text {
            margin-bottom: 1rem;
            font-size: 1rem;
        }
        
        .question-number {
            color: var(--primary-color);
            font-weight: bold;
            margin-right: 0.5rem;
        }
        
        .answer-form {
            display: flex;
            gap: 0.5rem;
        }
        
        .answer-input {
            flex: 1;
            background: rgba(0, 0, 0, 0.3);
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 8px;
            padding: 0.75rem 1rem;
            color: white;
            transition: border-color 0.3s ease;
        }
        
        .answer-input:focus {
            outline: none;
            border-color: var(--primary-color);
        }
        
        .answer-input:disabled {
            opacity: 0.5;
        }
        
        .btn-submit {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            color: white;
            font-weight: 500;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover:not(:disabled) {
            background: #00c97b;
            transform: translateY(-1px);
        }
        
        .btn-submit:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .btn-hint {
            background: transparent;
            border: 1px solid rgba(255,255,255,0.2);
            padding: 0.75rem;
            border-radius: 8px;
            color: var(--text-light);
            transition: all 0.3s ease;
        }
        
        .btn-hint:hover {
            background: rgba(255,255,255,0.1);
            border-color: var(--warning-color);
            color: var(--warning-color);
        }
        
        .feedback {
            margin-top: 0.75rem;
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.9rem;
            display: none;
        }
        
        .feedback.show {
            display: block;
        }
        
        .feedback.correct {
            background: rgba(0, 200, 83, 0.2);
            color: var(--success-color);
        }
        
        .feedback.incorrect {
            background: rgba(255, 82, 82, 0.2);
            color: var(--danger-color);
        }
        
        .hint-text {
            margin-top: 0.75rem;
            padding: 0.75rem 1rem;
            background: rgba(255, 193, 7, 0.15);
            border-radius: 6px;
            color: var(--warning-color);
            font-size: 0.9rem;
            display: none;
        }
        
        .hint-text.show {
            display: block;
        }
        
        .correct-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background: rgba(0, 200, 83, 0.2);
            color: var(--success-color);
            border-radius: 6px;
            font-size: 0.9rem;
        }
        
        /* Navigation Buttons */
        .nav-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 2rem;
            padding-top: 1rem;
            border-top: 1px solid rgba(255,255,255,0.1);
        }
        
        .btn-nav {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .btn-prev {
            background: rgba(255,255,255,0.1);
            color: var(--text-light);
        }
        
        .btn-next {
            background: var(--primary-color);
            color: white;
        }
        
        .btn-nav:hover {
            transform: translateY(-2px);
            color: white;
        }
        
        .btn-prev:hover {
            background: rgba(255,255,255,0.2);
        }
        
        .btn-next:hover {
            background: #00c97b;
        }
        
        /* Alerts */
        .alert {
            border: none;
            border-radius: 8px;
        }
        
        .alert-info {
            background: rgba(0, 168, 107, 0.15);
            color: var(--primary-color);
            border-left: 3px solid var(--primary-color);
        }
        
        .alert-warning {
            background: rgba(255, 193, 7, 0.15);
            color: var(--warning-color);
            border-left: 3px solid var(--warning-color);
        }
        
        .alert-success {
            background: rgba(0, 200, 83, 0.15);
            color: var(--success-color);
            border-left: 3px solid var(--success-color);
        }
        
        /* Tables */
        .table {
            color: var(--text-light);
        }
        
        .table-bordered {
            border-color: rgba(255,255,255,0.1);
        }
        
        .table thead th {
            background: rgba(0, 168, 107, 0.2);
            border-color: rgba(255,255,255,0.1);
            color: var(--primary-color);
        }
        
        .table td {
            border-color: rgba(255,255,255,0.1);
        }
        
        /* Reset Button */
        .btn-reset {
            background: transparent;
            border: 1px solid var(--danger-color);
            color: var(--danger-color);
            padding: 0.5rem 1rem;
            border-radius: 6px;
            font-size: 0.85rem;
            transition: all 0.3s ease;
        }
        
        .btn-reset:hover {
            background: var(--danger-color);
            color: white;
        }
        
        /* Difficulty Badge */
        .difficulty-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            background: rgba(0, 200, 83, 0.2);
            color: var(--success-color);
            border-radius: 20px;
            font-size: 0.8rem;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                top: 0;
                margin-bottom: 1.5rem;
            }
            
            .answer-form {
                flex-direction: column;
            }
            
            .nav-buttons {
                flex-direction: column;
                gap: 1rem;
            }
            
            .btn-nav {
                justify-content: center;
            }
        }
        
        /* Animations */
        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }
        
        .shake {
            animation: shake 0.3s ease-in-out;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .pulse {
            animation: pulse 0.3s ease-in-out;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="lab-header">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-md-4">
                    <div class="lab-logo">
                        <i class="bi bi-hdd-network"></i>
                        <?php echo LAB_TITLE; ?>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="progress-container">
                        <div class="d-flex justify-content-between mb-1">
                            <small>Overall Progress</small>
                            <small id="progress-text"><?php echo $total_progress['completed']; ?>/<?php echo $total_progress['total']; ?> Questions</small>
                        </div>
                        <div class="progress">
                            <div class="progress-bar" id="progress-bar" style="width: <?php echo $total_progress['percentage']; ?>%"></div>
                        </div>
                    </div>
                </div>
                <div class="col-md-3 text-end">
                    <span class="difficulty-badge me-2">
                        <i class="bi bi-speedometer2"></i>
                        <?php echo LAB_DIFFICULTY; ?>
                    </span>
                    <?php if ($settings['auth_mode'] && isset($_SESSION['student_name'])): ?>
                        <span class="me-2 d-none d-lg-inline-block text-muted" style="font-size: 0.85rem;">
                            <i class="bi bi-person-circle"></i> <?php echo htmlspecialchars($_SESSION['student_name']); ?>
                        </span>
                        <a href="?logout=1" class="btn-reset text-decoration-none d-inline-block">
                            <i class="bi bi-box-arrow-right"></i> Logout
                        </a>
                    <?php else: ?>
                        <button class="btn-reset" onclick="resetLab()">
                            <i class="bi bi-arrow-counterclockwise"></i> Reset
                        </button>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Main Content -->
    <main class="container py-4">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-lg-3">
                <div class="sidebar">
                    <h5 class="mb-3">
                        <i class="bi bi-list-task me-2"></i>Tasks
                    </h5>
                    <ul class="task-list">
                        <?php foreach ($TASKS as $id => $task): ?>
                            <?php 
                            $is_complete = isTaskComplete($id);
                            $is_active = ($id === $current_task);
                            ?>
                            <li>
                                <a href="?task=<?php echo $id; ?>" 
                                   class="task-item <?php echo $is_active ? 'active' : ''; ?> <?php echo $is_complete ? 'completed' : ''; ?>">
                                    <span class="task-icon">
                                        <?php if ($is_complete): ?>
                                            <i class="bi bi-check"></i>
                                        <?php else: ?>
                                            <?php echo $id; ?>
                                        <?php endif; ?>
                                    </span>
                                    <span class="task-name"><?php echo htmlspecialchars($task['title']); ?></span>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    </ul>
                    
                    <hr class="my-4" style="border-color: rgba(255,255,255,0.1);">
                    
                    <div class="text-center">
                        <small class="text-muted">
                            <i class="bi bi-clock me-1"></i>
                            Est. Time: <?php echo LAB_DURATION; ?>
                        </small>
                    </div>
                </div>
            </div>
            
            <!-- Content Area -->
            <div class="col-lg-9">
                <div class="content-card">
                    <span class="task-badge">
                        <i class="bi bi-bookmark-fill me-1"></i>
                        Task <?php echo $current_task; ?> of <?php echo count($TASKS); ?>
                    </span>
                    
                    <h1 class="task-title">
                        <?php echo htmlspecialchars($TASKS[$current_task]['title']); ?>
                    </h1>
                    
                    <div class="task-content">
                        <?php echo $TASKS[$current_task]['content']; ?>
                    </div>
                    
                    <!-- Questions Section -->
                    <div class="questions-section">
                        <h3 class="questions-title">
                            <i class="bi bi-question-circle"></i>
                            Answer the questions below
                        </h3>
                        
                        <?php foreach ($TASKS[$current_task]['questions'] as $index => $question): ?>
                            <?php $is_answered = isset($_SESSION['progress'][$question['id']]) && $_SESSION['progress'][$question['id']] === true; ?>
                            <div class="question-card <?php echo $is_answered ? 'answered' : ''; ?>" id="card-<?php echo $question['id']; ?>">
                                <div class="question-text">
                                    <span class="question-number">Q<?php echo $index + 1; ?>.</span>
                                    <?php echo $question['question']; ?>
                                </div>
                                
                                <?php if ($is_answered): ?>
                                    <div class="correct-badge">
                                        <i class="bi bi-check-circle-fill"></i>
                                        Correct!
                                    </div>
                                <?php else: ?>
                                    <form class="answer-form" onsubmit="submitAnswer(event, <?php echo $current_task; ?>, '<?php echo $question['id']; ?>')">
                                        <input type="text" 
                                               class="answer-input" 
                                               id="input-<?php echo $question['id']; ?>"
                                               placeholder="<?php echo isset($question['placeholder']) ? $question['placeholder'] : 'Enter your answer...'; ?>"
                                               autocomplete="off">
                                        <button type="submit" class="btn-submit">
                                            <i class="bi bi-check2"></i> Submit
                                        </button>
                                        <button type="button" class="btn-hint" onclick="showHint('<?php echo $question['id']; ?>', <?php echo $current_task; ?>)" title="Show Hint">
                                            <i class="bi bi-lightbulb"></i>
                                        </button>
                                    </form>
                                    
                                    <div class="feedback" id="feedback-<?php echo $question['id']; ?>"></div>
                                    <div class="hint-text" id="hint-<?php echo $question['id']; ?>"></div>
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <!-- Navigation -->
                    <div class="nav-buttons">
                        <?php if ($current_task > 1): ?>
                            <a href="?task=<?php echo $current_task - 1; ?>" class="btn-nav btn-prev">
                                <i class="bi bi-arrow-left"></i>
                                Previous Task
                            </a>
                        <?php else: ?>
                            <span></span>
                        <?php endif; ?>
                        
                        <?php if ($current_task < count($TASKS)): ?>
                            <a href="?task=<?php echo $current_task + 1; ?>" class="btn-nav btn-next">
                                Next Task
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        <?php else: ?>
                            <span></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Footer -->
                <div class="text-center text-muted py-3">
                    <small>
                        <i class="bi bi-shield-check me-1"></i>
                        ICT Academy of Kerala - Cybersecurity Training Lab
                    </small>
                </div>
            </div>
        </div>
    </main>
    
    <script>
        function submitAnswer(event, taskId, questionId) {
            event.preventDefault();
            
            const input = document.getElementById('input-' + questionId);
            const feedback = document.getElementById('feedback-' + questionId);
            const card = document.getElementById('card-' + questionId);
            const answer = input.value.trim();
            
            if (!answer) {
                input.classList.add('shake');
                setTimeout(() => input.classList.remove('shake'), 300);
                return;
            }
            
            // Disable input during submission
            input.disabled = true;
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=check_answer&task_id=${taskId}&question_id=${questionId}&answer=${encodeURIComponent(answer)}`
            })
            .then(response => response.json())
            .then(data => {
                feedback.classList.add('show');
                
                if (data.correct) {
                    feedback.className = 'feedback show correct';
                    feedback.innerHTML = '<i class="bi bi-check-circle-fill me-2"></i>Correct! Well done!';
                    card.classList.add('answered', 'pulse');
                    
                    // Update progress
                    updateProgress(data.total_progress);
                    
                    // Replace form with success badge after delay
                    setTimeout(() => {
                        const form = card.querySelector('.answer-form');
                        const hint = document.getElementById('hint-' + questionId);
                        if (form) form.remove();
                        if (hint) hint.remove();
                        feedback.remove();
                        
                        const badge = document.createElement('div');
                        badge.className = 'correct-badge';
                        badge.innerHTML = '<i class="bi bi-check-circle-fill"></i> Correct!';
                        card.querySelector('.question-text').after(badge);
                        
                        // Update sidebar if task complete
                        if (data.task_complete) {
                            updateTaskStatus(taskId);
                        }
                    }, 1000);
                } else {
                    feedback.className = 'feedback show incorrect';
                    feedback.innerHTML = '<i class="bi bi-x-circle-fill me-2"></i>Incorrect. Try again!';
                    input.disabled = false;
                    input.value = '';
                    input.focus();
                    card.classList.add('shake');
                    setTimeout(() => card.classList.remove('shake'), 300);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                input.disabled = false;
            });
        }
        
        function showHint(questionId, taskId) {
            const hintDiv = document.getElementById('hint-' + questionId);
            
            if (hintDiv.classList.contains('show')) {
                hintDiv.classList.remove('show');
                return;
            }
            
            fetch('', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `action=get_hint&task_id=${taskId}&question_id=${questionId}`
            })
            .then(response => response.json())
            .then(data => {
                hintDiv.innerHTML = '<i class="bi bi-lightbulb-fill me-2"></i><strong>Hint:</strong> ' + data.hint;
                hintDiv.classList.add('show');
            });
        }
        
        function updateProgress(progress) {
            const bar = document.getElementById('progress-bar');
            const text = document.getElementById('progress-text');
            
            bar.style.width = progress.percentage + '%';
            text.textContent = progress.completed + '/' + progress.total + ' Questions';
        }
        
        function updateTaskStatus(taskId) {
            const taskItem = document.querySelector(`.task-item[href="?task=${taskId}"]`);
            if (taskItem) {
                taskItem.classList.add('completed');
                const icon = taskItem.querySelector('.task-icon');
                if (icon) {
                    icon.innerHTML = '<i class="bi bi-check"></i>';
                }
            }
        }
        
        function resetLab() {
            if (confirm('Are you sure you want to reset all progress? This cannot be undone.')) {
                fetch('', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'action=reset_progress'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    }
                });
            }
        }
    </script>
</body>
</html>
