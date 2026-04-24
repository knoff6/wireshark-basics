<?php
/**
 * Wireshark: The Basics - Lab Configuration
 * Self-hosted training module for ICT Academy of Kerala
 * 
 * This file contains all tasks, questions, and answers
 */

session_start();

// Initialize progress tracking
if (!isset($_SESSION['progress'])) {
    $_SESSION['progress'] = [];
}
if (!isset($_SESSION['completed_tasks'])) {
    $_SESSION['completed_tasks'] = [];
}

// Lab Configuration
define('LAB_TITLE', 'Wireshark: The Basics');
define('LAB_DESCRIPTION', 'Learn the basics of Wireshark and how to analyze protocols and PCAPs.');
define('LAB_DIFFICULTY', 'Easy');
define('LAB_DURATION', '1 Hour');

// Tasks and Questions Configuration
$TASKS = [
    1 => [
        'title' => 'Introduction',
        'content' => '
            <h3>Welcome to Wireshark: The Basics</h3>
            <p>Wireshark is an open-source, cross-platform network packet analyzer tool capable of sniffing and investigating live traffic and inspecting packet captures (PCAP). It is commonly used as one of the best packet analysis tools.</p>
            <p>In this room, we will look at the basics of Wireshark and use it to perform fundamental packet analysis.</p>
            
            <div class="alert alert-info">
                <strong>📁 Lab Files:</strong> There are two capture files provided:
                <ul>
                    <li><a href="assets/http1.pcapng" download><code>http1.pcapng</code></a> - Use this to follow along with the demonstrations</li>
                    <li><a href="assets/Exercise.pcapng" download><code>Exercise.pcapng</code></a> - Use this to answer the questions</li>
                </ul>
            </div>
            
            <div class="alert alert-warning">
                <strong>📝 Prerequisites:</strong> We suggest completing basic networking fundamentals before starting this room.
            </div>
        ',
        'questions' => [
            [
                'id' => 'q1_1',
                'question' => 'Which file is used to <strong>simulate</strong> the screenshots?',
                'answer' => 'http1.pcapng',
                'hint' => 'Read the introduction carefully - one file is for demonstrations.'
            ],
            [
                'id' => 'q1_2', 
                'question' => 'Which file is used to <strong>answer</strong> the questions?',
                'answer' => 'Exercise.pcapng',
                'hint' => 'The other file mentioned is for answering questions.'
            ]
        ]
    ],
    
    2 => [
        'title' => 'Tool Overview',
        'content' => '
            <h3>Wireshark GUI & Features</h3>
            <p>Wireshark is a powerful network traffic analyzer used for:</p>
            <ul>
                <li>Troubleshooting network issues (congestion, failure points)</li>
                <li>Detecting security anomalies (rogue hosts, unusual port usage)</li>
                <li>Investigating protocols (response codes, payload data)</li>
            </ul>
            
            <div class="alert alert-warning">
                <strong>⚠️ Note:</strong> Wireshark is NOT an Intrusion Detection System (IDS). It only captures and analyzes packets, relying on the analyst\'s expertise for detecting anomalies.
            </div>
            
            <h4>GUI Overview</h4>
            <p>The Wireshark interface consists of five key sections:</p>
            <ol>
                <li><strong>Toolbar:</strong> Menus and shortcuts for filtering, sorting, summarizing, and exporting</li>
                <li><strong>Display Filter Bar:</strong> Main section for filtering packets</li>
                <li><strong>Recent Files:</strong> Quick access to recently opened capture files</li>
                <li><strong>Capture Filter & Interfaces:</strong> Selection of network interfaces for packet capture</li>
                <li><strong>Status Bar:</strong> Displays tool status and packet statistics</li>
            </ol>
            
            <h4>Loading PCAP Files</h4>
            <p>PCAP files can be opened via File menu, drag-and-drop, or double-clicking. Packets are displayed in three panes:</p>
            <ul>
                <li><strong>Packet List Pane:</strong> Summarizes source, destination, and protocol</li>
                <li><strong>Packet Details Pane:</strong> Provides detailed protocol breakdown</li>
                <li><strong>Packet Bytes Pane:</strong> Displays hex and ASCII representation</li>
            </ul>
            
            <h4>Colouring Packets</h4>
            <p>Wireshark uses color-coding to quickly identify protocols and anomalies. Custom rules can be:</p>
            <ul>
                <li>Temporary (session-based)</li>
                <li>Permanent (saved for future sessions via View → Coloring Rules)</li>
            </ul>
            
            <h4>Traffic Sniffing</h4>
            <ul>
                <li>🦈 Blue shark button - Start sniffing</li>
                <li>🔴 Red button - Stop sniffing</li>
                <li>🟢 Green button - Restart sniffing</li>
            </ul>
            
            <h4>Merging PCAP Files</h4>
            <p>Use <code>File → Merge</code> to combine multiple PCAP files. Save the merged file before analysis.</p>
            
            <h4>Viewing File Details</h4>
            <p>Access file details via <code>Statistics → Capture File Properties</code> or click the PCAP icon in the bottom left. This shows:</p>
            <ul>
                <li>File hash values</li>
                <li>Timestamps</li>
                <li>Capture file comments</li>
                <li>Interface statistics</li>
            </ul>
            
            <div class="alert alert-info">
                <strong>💡 Tip:</strong> Use <code>Statistics → Capture File Properties</code> to find file metadata, hashes, and comments.
            </div>
        ',
        'questions' => [
            [
                'id' => 'q2_1',
                'question' => 'Read the "capture file comments". What is the flag?',
                'answer' => 'TryHackMe_Wireshark_Demo',
                'hint' => 'Go to Statistics → Capture File Properties and look at the comments section at the bottom.'
            ],
            [
                'id' => 'q2_2',
                'question' => 'What is the total number of packets?',
                'answer' => '58620',
                'hint' => 'Found in Capture File Properties - look for "Packets:" field.'
            ],
            [
                'id' => 'q2_3',
                'question' => 'What is the SHA256 hash value of the capture file?',
                'answer' => 'f446de335565fb0b0ee5e5a3266703c778b2f3dfad7efeaeccb2da5641a6d6eb',
                'hint' => 'Found in Capture File Properties - scroll up to find Hash (SHA256).'
            ]
        ]
    ],
    
    3 => [
        'title' => 'Packet Dissection',
        'content' => '
            <h3>Understanding Packet Details</h3>
            <p>Packet dissection (protocol dissection) involves analyzing packet details by decoding protocols and fields. Wireshark supports numerous protocols for dissection.</p>
            
            <h4>Packet Details Structure (OSI Model)</h4>
            <p>Clicking on a packet reveals its details, typically containing 5-7 layers:</p>
            
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Layer</th>
                        <th>OSI Model</th>
                        <th>Information</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>1. Frame</td>
                        <td>Physical (Layer 1)</td>
                        <td>Overall packet/frame details, arrival time, frame length</td>
                    </tr>
                    <tr>
                        <td>2. Ethernet II</td>
                        <td>Data Link (Layer 2)</td>
                        <td>Source and destination MAC addresses</td>
                    </tr>
                    <tr>
                        <td>3. Internet Protocol</td>
                        <td>Network (Layer 3)</td>
                        <td>Source/destination IP, TTL, protocol type</td>
                    </tr>
                    <tr>
                        <td>4. TCP/UDP</td>
                        <td>Transport (Layer 4)</td>
                        <td>Ports, sequence numbers, payload size</td>
                    </tr>
                    <tr>
                        <td>5. Application</td>
                        <td>Application (Layer 5-7)</td>
                        <td>HTTP, DNS, FTP specific data</td>
                    </tr>
                </tbody>
            </table>
            
            <div class="alert alert-info">
                <strong>💡 Navigation Tip:</strong> Use <code>Ctrl+G</code> to go to a specific packet number.
            </div>
            
            <h4>Key Fields to Know</h4>
            <ul>
                <li><strong>Frame → Arrival Time:</strong> When the packet was captured</li>
                <li><strong>IP → TTL (Time To Live):</strong> Hop limit before packet is discarded</li>
                <li><strong>TCP → Payload:</strong> Size of data carried</li>
                <li><strong>HTTP → Headers:</strong> ETag, Content-Type, etc.</li>
            </ul>
        ',
        'questions' => [
            [
                'id' => 'q3_1',
                'question' => 'View packet number 38. Which markup language is used under the HTTP protocol?',
                'answer' => 'eXtensible Markup Language',
                'hint' => 'Go to packet 38 (Ctrl+G), look at the Application layer under HTTP.'
            ],
            [
                'id' => 'q3_2',
                'question' => 'What is the arrival date of the packet? (Answer format: Month/Day/Year)',
                'answer' => '05/13/2004',
                'hint' => 'Expand the Frame layer and look for "Arrival Time".'
            ],
            [
                'id' => 'q3_3',
                'question' => 'What is the TTL value?',
                'answer' => '47',
                'hint' => 'Found in the Internet Protocol (Layer 3) section.'
            ],
            [
                'id' => 'q3_4',
                'question' => 'What is the TCP payload size?',
                'answer' => '424',
                'hint' => 'Look in the TCP section for payload/segment data size.'
            ],
            [
                'id' => 'q3_5',
                'question' => 'What is the e-tag value?',
                'answer' => '9a01a-4696-7e354b00',
                'hint' => 'Found in the HTTP section under Hypertext Transfer Protocol.'
            ]
        ]
    ],
    
    4 => [
        'title' => 'Packet Navigation',
        'content' => '
            <h3>Navigating and Finding Packets</h3>
            
            <h4>Go to Packet</h4>
            <p>Navigate to specific packets using:</p>
            <ul>
                <li><code>Go → Go to Packet</code> menu</li>
                <li>Keyboard shortcut: <code>Ctrl+G</code></li>
            </ul>
            
            <h4>Find Packets</h4>
            <p>Search packets using <code>Edit → Find Packet</code> or <code>Ctrl+F</code>:</p>
            <ul>
                <li><strong>Display filter:</strong> Use Wireshark filter syntax</li>
                <li><strong>Hex value:</strong> Search raw bytes</li>
                <li><strong>String:</strong> Search text content</li>
                <li><strong>Regex:</strong> Use regular expressions</li>
            </ul>
            
            <h4>Mark Packets</h4>
            <p>Mark interesting packets for later analysis. Note: Markings are lost when the file is closed.</p>
            
            <h4>Packet Comments</h4>
            <p>Add comments to packets via <code>Edit → Packet Comment</code> or <code>Ctrl+Alt+C</code>. Unlike marks, comments are saved in the capture file.</p>
            
            <h4>Export Objects (Files)</h4>
            <p>Extract files transferred in the capture:</p>
            <ul>
                <li><code>File → Export Objects → HTTP</code></li>
                <li>Supports HTTP, SMB, TFTP, DICOM protocols</li>
                <li>Great for extracting images, documents, executables</li>
            </ul>
            
            <h4>Export Packet Bytes</h4>
            <p>Right-click on a specific section → <code>Export Packet Bytes</code> to save raw data.</p>
            
            <h4>Expert Info</h4>
            <p>View protocol anomalies via <code>Analyze → Expert Information</code> or click the icon in status bar. Severity levels:</p>
            <ul>
                <li><span class="badge bg-info">Chat</span> - Informational</li>
                <li><span class="badge bg-primary">Note</span> - Notable events</li>
                <li><span class="badge bg-warning">Warn</span> - Warnings</li>
                <li><span class="badge bg-danger">Error</span> - Errors</li>
            </ul>
            
            <div class="alert alert-warning">
                <strong>📝 For Question 2:</strong> Follow the instructions in the packet comment carefully. You may need to export packet bytes and calculate an MD5 hash using: <code>md5sum filename</code>
            </div>
        ',
        'questions' => [
            [
                'id' => 'q4_1',
                'question' => 'Search the "r4w" string in packet details. What is the name of artist 1?',
                'answer' => 'r4w8173',
                'hint' => 'Use Ctrl+F to search for "r4w" string. Look at the HTML content.'
            ],
            [
                'id' => 'q4_2',
                'question' => 'Go to packet 12 and read the comments. What is the answer?',
                'answer' => '911cd574a42865a956ccde2d04495ebf',
                'hint' => 'Read the full comment (Ctrl+Alt+C). Follow instructions to packet 39765, export JPEG bytes, and calculate MD5.'
            ],
            [
                'id' => 'q4_3',
                'question' => 'There is a ".txt" file inside the capture file. Find the file and read it; what is the alien\'s name?',
                'answer' => 'PACKETMASTER',
                'hint' => 'Use File → Export Objects → HTTP, filter for .txt files.'
            ],
            [
                'id' => 'q4_4',
                'question' => 'Look at the expert info section. What is the number of warnings?',
                'answer' => '1636',
                'hint' => 'Go to Analyze → Expert Information and count the Warnings.'
            ]
        ]
    ],
    
    5 => [
        'title' => 'Packet Filtering',
        'content' => '
            <h3>Filtering Network Traffic</h3>
            <p>Wireshark provides powerful filtering to focus on relevant traffic.</p>
            
            <h4>Filter Types</h4>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Type</th>
                        <th>When Applied</th>
                        <th>Purpose</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><strong>Capture Filters</strong></td>
                        <td>Before capture</td>
                        <td>Limit what is recorded</td>
                    </tr>
                    <tr>
                        <td><strong>Display Filters</strong></td>
                        <td>After capture</td>
                        <td>Control what is displayed</td>
                    </tr>
                </tbody>
            </table>
            
            <h4>Right-Click Filtering Methods</h4>
            <ul>
                <li><strong>Apply as Filter:</strong> Instantly filter on selected field</li>
                <li><strong>Prepare as Filter:</strong> Build filter without applying</li>
                <li><strong>Conversation Filter:</strong> Filter all packets in a conversation</li>
                <li><strong>Colourise Conversation:</strong> Highlight without filtering</li>
                <li><strong>Apply as Column:</strong> Add field as visible column</li>
            </ul>
            
            <h4>Follow Stream</h4>
            <p>Reconstruct full protocol conversations:</p>
            <ul>
                <li>Right-click packet → <code>Follow → TCP/UDP/HTTP Stream</code></li>
                <li>Shows complete application-level data exchange</li>
                <li>Useful for viewing full HTTP requests/responses</li>
            </ul>
            
            <h4>Common Display Filters</h4>
            <table class="table table-bordered table-sm">
                <thead>
                    <tr><th>Filter</th><th>Description</th></tr>
                </thead>
                <tbody>
                    <tr><td><code>http</code></td><td>Show HTTP traffic only</td></tr>
                    <tr><td><code>tcp.port == 80</code></td><td>Traffic on port 80</td></tr>
                    <tr><td><code>ip.addr == 192.168.1.1</code></td><td>Traffic to/from IP</td></tr>
                    <tr><td><code>dns</code></td><td>DNS queries and responses</td></tr>
                    <tr><td><code>tcp.flags.syn == 1</code></td><td>TCP SYN packets</td></tr>
                </tbody>
            </table>
            
            <div class="alert alert-info">
                <strong>💡 Golden Rule:</strong> "If you can click on it, you can filter and copy it!"
            </div>
        ',
        'questions' => [
            [
                'id' => 'q5_1',
                'question' => 'Go to packet number 4. Right-click on the "Hypertext Transfer Protocol" and apply it as a filter. Now, look at the filter pane. What is the filter query?',
                'answer' => 'http',
                'hint' => 'Right-click HTTP → Apply as Filter → Selected. Check the display filter bar.'
            ],
            [
                'id' => 'q5_2',
                'question' => 'What is the number of displayed packets?',
                'answer' => '1089',
                'hint' => 'With http filter applied, check the bottom right of the status bar.'
            ],
            [
                'id' => 'q5_3',
                'question' => 'Go to packet number 33790 and follow the stream. What is the total number of artists?',
                'answer' => '3',
                'hint' => 'Right-click → Follow → HTTP Stream. Look at the HTML content for artist links.'
            ],
            [
                'id' => 'q5_4',
                'question' => 'What is the name of the second artist?',
                'answer' => 'Blad3',
                'hint' => 'In the HTTP stream, find the artist names in order. The second one is the answer.'
            ]
        ]
    ],
    
    6 => [
        'title' => 'Conclusion',
        'content' => '
            <h3>🎉 Congratulations!</h3>
            <p>You have completed the <strong>Wireshark: The Basics</strong> training module!</p>
            
            <h4>What You Learned</h4>
            <ul>
                <li>✅ Wireshark GUI and interface navigation</li>
                <li>✅ Loading and analyzing PCAP files</li>
                <li>✅ Packet dissection and OSI layer understanding</li>
                <li>✅ Navigating, searching, and marking packets</li>
                <li>✅ Exporting objects and packet bytes</li>
                <li>✅ Using display filters effectively</li>
                <li>✅ Following protocol streams</li>
            </ul>
            
            <h4>Next Steps</h4>
            <p>To improve your Wireshark skills further, consider learning about:</p>
            <ul>
                <li><strong>Wireshark: Packet Operations</strong> - Deep packet analysis</li>
                <li><strong>Wireshark: Traffic Analysis</strong> - Real-world traffic investigation</li>
                <li><strong>Network Security Monitoring</strong> - Using Wireshark for incident response</li>
            </ul>
            
            <div class="alert alert-success">
                <strong>🏆 Achievement Unlocked:</strong> You are now equipped with fundamental Wireshark skills for network analysis and security investigations!
            </div>
        ',
        'questions' => [
            [
                'id' => 'q6_1',
                'question' => 'Click Complete to finish this module.',
                'answer' => 'complete',
                'hint' => 'Just type "complete" to finish!',
                'placeholder' => 'Type "complete"'
            ]
        ]
    ]
];

// Helper Functions
function checkAnswer($task_id, $question_id, $user_answer) {
    global $TASKS;
    
    foreach ($TASKS[$task_id]['questions'] as $question) {
        if ($question['id'] === $question_id) {
            $correct = strtolower(trim($question['answer']));
            $submitted = strtolower(trim($user_answer));
            return $correct === $submitted;
        }
    }
    return false;
}

function getTaskProgress($task_id) {
    global $TASKS;
    
    $total = count($TASKS[$task_id]['questions']);
    $completed = 0;
    
    foreach ($TASKS[$task_id]['questions'] as $question) {
        if (isset($_SESSION['progress'][$question['id']]) && $_SESSION['progress'][$question['id']] === true) {
            $completed++;
        }
    }
    
    return ['completed' => $completed, 'total' => $total];
}

function isTaskComplete($task_id) {
    $progress = getTaskProgress($task_id);
    return $progress['completed'] === $progress['total'];
}

function getTotalProgress() {
    global $TASKS;
    
    $total = 0;
    $completed = 0;
    
    foreach ($TASKS as $task) {
        foreach ($task['questions'] as $question) {
            $total++;
            if (isset($_SESSION['progress'][$question['id']]) && $_SESSION['progress'][$question['id']] === true) {
                $completed++;
            }
        }
    }
    
    return ['completed' => $completed, 'total' => $total, 'percentage' => $total > 0 ? round(($completed / $total) * 100) : 0];
}

function resetProgress() {
    $_SESSION['progress'] = [];
    $_SESSION['completed_tasks'] = [];
}
