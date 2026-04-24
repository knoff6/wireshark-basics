# Wireshark: The Basics - Self-Hosted Lab

A TryHackMe-style interactive training module for learning Wireshark basics. Perfect for cybersecurity training programs.

## 🎯 Features

- **6 Interactive Tasks** covering Wireshark fundamentals
- **18 Questions** with instant validation
- **Progress Tracking** with session-based persistence
- **Hint System** for guided learning
- **Responsive Design** - works on desktop and mobile
- **Dark Theme** - easy on the eyes for long sessions
- **No Database Required** - uses PHP sessions

## 📋 Prerequisites

Before using this lab, ensure students have:
- **Wireshark installed** (latest version recommended)
- **Exercise.pcapng** and **http1.pcapng** files (from original TryHackMe room or create your own)
- Basic networking knowledge

## 🚀 Quick Deployment

### Option 1: Apache + PHP

1. Copy all files to your web root:
   ```bash
   cp -r wireshark-basics-lab /var/www/html/
   ```

2. Ensure proper permissions:
   ```bash
   chown -R www-data:www-data /var/www/html/wireshark-basics-lab
   chmod -R 755 /var/www/html/wireshark-basics-lab
   ```

3. Access via browser:
   ```
   http://your-server/wireshark-basics-lab/
   ```

### Option 2: PHP Built-in Server (Testing)

```bash
cd wireshark-basics-lab
php -S localhost:8080
```

Access at `http://localhost:8080`

### Option 3: Docker

Create `Dockerfile`:
```dockerfile
FROM php:8.2-apache
COPY . /var/www/html/
RUN chown -R www-data:www-data /var/www/html
```

Build and run:
```bash
docker build -t wireshark-lab .
docker run -d -p 8080:80 wireshark-lab
```

## 📁 File Structure

```
wireshark-basics-lab/
├── index.php           # Main application
├── includes/
│   └── config.php      # Tasks, questions, and answers
├── assets/             # Images and screenshots (optional)
├── css/                # Additional styles (optional)
├── js/                 # Additional scripts (optional)
└── README.md           # This file
```

## 🔧 Customization

### Modify Questions/Answers

Edit `includes/config.php`:

```php
$TASKS = [
    1 => [
        'title' => 'Your Task Title',
        'content' => 'HTML content here...',
        'questions' => [
            [
                'id' => 'q1_1',
                'question' => 'Your question here?',
                'answer' => 'correct_answer',
                'hint' => 'Helpful hint for students'
            ],
            // More questions...
        ]
    ],
    // More tasks...
];
```

### Change Branding

Edit the header section in `index.php`:
- Update `LAB_TITLE` in config.php
- Modify the logo and institution name
- Adjust colors in the CSS variables

### Add Your PCAP Files

Place your capture files in the `assets/` directory and update the content to reference them:

```php
'content' => '
    <p>Download the lab files:</p>
    <ul>
        <li><a href="assets/Exercise.pcapng">Exercise.pcapng</a></li>
        <li><a href="assets/http1.pcapng">http1.pcapng</a></li>
    </ul>
'
```

## 📊 Tracking Student Progress

Progress is stored in PHP sessions. For multi-user tracking with database:

1. Create a MySQL database
2. Create a users/progress table
3. Modify the session handling in `config.php`

Example table structure:
```sql
CREATE TABLE student_progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id VARCHAR(50),
    question_id VARCHAR(20),
    completed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_progress (student_id, question_id)
);
```

## 🔐 Security Notes

- This is designed for **internal/training use**
- Add authentication if deploying publicly
- Consider implementing rate limiting
- Validate all user inputs (already implemented)

## 📝 Task Overview

| Task | Title | Questions | Topics |
|------|-------|-----------|--------|
| 1 | Introduction | 2 | Lab setup, file overview |
| 2 | Tool Overview | 3 | GUI, PCAP loading, file properties |
| 3 | Packet Dissection | 5 | OSI layers, packet details |
| 4 | Packet Navigation | 4 | Search, comments, export objects |
| 5 | Packet Filtering | 4 | Filters, follow stream |
| 6 | Conclusion | 1 | Summary and next steps |

## 🤝 Contributing

Feel free to:
- Add more tasks/questions
- Improve the UI
- Add new features (leaderboard, certificates, etc.)
- Report bugs

## 📄 License

This lab is created for educational purposes. 
Original TryHackMe content is property of TryHackMe Ltd.

---

Created for **ICT Academy of Kerala** Cybersecurity Training Program
