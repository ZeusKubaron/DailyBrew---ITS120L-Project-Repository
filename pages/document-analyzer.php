<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document Analyzer - DailyBrew</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f5f7fa; min-height: 100vh; }
        
        .app-container { display: flex; min-height: 100vh; }
        
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            position: fixed;
            height: 100vh;
            transition: transform 0.3s;
            z-index: 1000;
        }
        .sidebar.collapsed { transform: translateX(-260px); }
        .sidebar-header { display: flex; align-items: center; justify-content: space-between; padding-bottom: 20px; border-bottom: 1px solid rgba(255,255,255,0.2); margin-bottom: 20px; }
        .logo { font-size: 1.8rem; font-weight: bold; }
        .hamburger { background: none; border: none; color: white; font-size: 1.5rem; cursor: pointer; }
        .nav-menu { list-style: none; }
        .nav-menu li { margin-bottom: 5px; }
        .nav-menu a { display: flex; align-items: center; padding: 12px 15px; color: white; text-decoration: none; border-radius: 10px; transition: background 0.2s; }
        .nav-menu a:hover, .nav-menu a.active { background: rgba(255,255,255,0.2); }
        .nav-menu a span { margin-right: 10px; }
        
        .main-content { flex: 1; margin-left: 260px; padding: 20px; transition: margin-left 0.3s; }
        .main-content.expanded { margin-left: 0; }
        
        .header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; }
        .header h1 { color: #333; font-size: 1.8rem; }
        .user-info { display: flex; align-items: center; gap: 15px; }
        .user-avatar { width: 40px; height: 40px; border-radius: 50%; background: #667eea; color: white; display: flex; align-items: center; justify-content: center; font-weight: bold; }
        .logout-btn { padding: 8px 20px; background: #ff4757; color: white; border: none; border-radius: 20px; cursor: pointer; }
        
        .card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { color: #333; font-size: 1.2rem; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #667eea; }
        
        .upload-area {
            border: 3px dashed #ccc;
            border-radius: 15px;
            padding: 40px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.3s, background 0.3s;
        }
        .upload-area:hover { border-color: #667eea; background: #f8f9ff; }
        .upload-area.dragover { border-color: #667eea; background: #e8edff; }
        .upload-icon { font-size: 3rem; margin-bottom: 15px; }
        .upload-text { color: #666; margin-bottom: 10px; }
        .upload-hint { color: #999; font-size: 0.9rem; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #333; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; }
        
        .btn { padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); }
        
        .analysis-result { background: #f8f9fa; border-radius: 10px; padding: 20px; margin-top: 20px; }
        .analysis-result h3 { color: #667eea; margin-bottom: 15px; }
        .analysis-item { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #e0e0e0; }
        .analysis-item:last-child { border-bottom: none; }
        .analysis-label { color: #666; }
        .analysis-value { font-weight: bold; color: #333; }
        
        .ai-chat { background: #f8f9fa; border-radius: 10px; padding: 20px; margin-top: 20px; }
        .chat-messages { max-height: 300px; overflow-y: auto; margin-bottom: 15px; }
        .chat-message { padding: 10px 15px; border-radius: 10px; margin-bottom: 10px; }
        .chat-message.user { background: #667eea; color: white; margin-left: 20%; }
        .chat-message.ai { background: #e0e0e0; color: #333; margin-right: 20%; }
        .chat-input { display: flex; gap: 10px; }
        .chat-input input { flex: 1; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <span class="logo">☕ DailyBrew</span>
                <button class="hamburger" onclick="toggleSidebar()">☰</button>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php"><span>🏠</span> Dashboard</a></li>
                <li><a href="calendar.php"><span>📅</span> Calendar</a></li>
                <li><a href="tasks.php"><span>📝</span> Tasks</a></li>
                <li><a href="document-analyzer.php" class="active"><span>📄</span> Document Analyzer</a></li>
                <li><a href="schedule.php"><span>📚</span> Schedule</a></li>
                <li><a href="settings.php"><span>⚙️</span> Settings</a></li>
            </ul>
        </nav>
        
        <main class="main-content" id="mainContent">
            <div class="header">
                <h1>📄 Document Analyzer</h1>
                <div class="user-info">
                    <div class="user-avatar" id="userAvatar">U</div>
                    <button class="logout-btn" onclick="logout()">Logout</button>
                </div>
            </div>
            
            <div class="card">
                <h2>📤 Upload Document or Enter Text</h2>
                
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">📄</div>
                    <p class="upload-text">Drop a file here or click to upload</p>
                    <p class="upload-hint">Supports .txt, .md, .doc files (text content will be extracted)</p>
                    <input type="file" id="fileInput" style="display: none;" accept=".txt,.md,.doc">
                </div>
                
                <div style="margin-top: 20px; text-align: center; color: #999;">- OR -</div>
                
                <div class="form-group" style="margin-top: 20px;">
                    <label>Enter Task Details Manually</label>
                    <textarea id="manualText" rows="4" placeholder="Paste task instructions, assignment details, or describe your task..."></textarea>
                </div>
                
                <div class="form-group">
                    <label>Due Date</label>
                    <input type="date" id="taskDueDate">
                </div>
                
                <div class="form-group">
                    <label>Study Profile</label>
                    <select id="taskProfile">
                        <option value="seamless">Seamless (Recommended)</option>
                        <option value="early_crammer">Early Crammer</option>
                        <option value="late_crammer">Late Crammer</option>
                    </select>
                </div>
                
                <button class="btn" onclick="analyzeText()">🔍 Analyze & Create Task</button>
            </div>
            
            <div class="card" id="analysisResult" style="display: none;">
                <h2>📊 AI Analysis Results</h2>
                <div class="analysis-result">
                    <h3 id="resultTitle">Task Analysis</h3>
                    <div class="analysis-item">
                        <span class="analysis-label">Priority:</span>
                        <span class="analysis-value" id="resultPriority">-</span>
                    </div>
                    <div class="analysis-item">
                        <span class="analysis-label">Complexity:</span>
                        <span class="analysis-value" id="resultComplexity">-</span>
                    </div>
                    <div class="analysis-item">
                        <span class="analysis-label">Estimated Study Hours:</span>
                        <span class="analysis-value" id="resultHours">-</span>
                    </div>
                    <div class="analysis-item">
                        <span class="analysis-label">Recommended Profile:</span>
                        <span class="analysis-value" id="resultProfile">-</span>
                    </div>
                    <div class="analysis-item">
                        <span class="analysis-label">Tips:</span>
                        <span class="analysis-value" id="resultTips">-</span>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h2>🤖 AI Assistant</h2>
                <div class="ai-chat">
                    <div class="chat-messages" id="chatMessages">
                        <div class="chat-message ai">Hello! I'm here to help you plan your studies. Ask me anything about your tasks or schedule!</div>
                    </div>
                    <div class="chat-input">
                        <input type="text" id="chatInput" placeholder="Type your message...">
                        <button class="btn" onclick="sendMessage()">Send</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        const currentUser = localStorage.getItem('dailybrew_current_user');
        if (!currentUser) { window.location.href = '../auth/login.php'; }
        
        const user = JSON.parse(currentUser);
        document.getElementById('userAvatar').textContent = user.firstName.charAt(0);
        
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
        }
        
        function logout() {
            localStorage.removeItem('dailybrew_current_user');
            window.location.href = '../auth/login.php';
        }
        
        // File upload handling
        const uploadArea = document.getElementById('uploadArea');
        const fileInput = document.getElementById('fileInput');
        
        uploadArea.addEventListener('click', () => fileInput.click());
        
        uploadArea.addEventListener('dragover', (e) => {
            e.preventDefault();
            uploadArea.classList.add('dragover');
        });
        
        uploadArea.addEventListener('dragleave', () => {
            uploadArea.classList.remove('dragover');
        });
        
        uploadArea.addEventListener('drop', (e) => {
            e.preventDefault();
            uploadArea.classList.remove('dragover');
            const file = e.dataTransfer.files[0];
            if (file) handleFile(file);
        });
        
        fileInput.addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) handleFile(file);
        });
        
        function handleFile(file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                document.getElementById('manualText').value = e.target.result;
            };
            reader.readAsText(file);
        }
        
        function analyzeText() {
            const text = document.getElementById('manualText').value;
            const dueDate = document.getElementById('taskDueDate').value;
            const profile = document.getElementById('taskProfile').value;
            
            if (!text.trim()) {
                alert('Please enter some text or upload a document');
                return;
            }
            
            if (!dueDate) {
                alert('Please select a due date');
                return;
            }
            
            // Simple AI analysis
            const wordCount = text.split(/\s+/).length;
            const textLower = text.toLowerCase();
            
            let complexity = 3;
            if (textLower.includes('exam') || textLower.includes('final') || textLower.includes('midterm')) complexity = 8;
            else if (textLower.includes('quiz')) complexity = 5;
            else if (textLower.includes('homework') || textLower.includes('assignment')) complexity = 4;
            else if (textLower.includes('project') || textLower.includes('paper')) complexity = 6;
            
            complexity += wordCount > 500 ? 2 : wordCount > 200 ? 1 : 0;
            complexity = Math.min(10, complexity);
            
            const due = new Date(dueDate);
            const today = new Date();
            const daysUntil = Math.ceil((due - today) / (1000 * 60 * 60 * 24));
            
            let priority = 'medium';
            if (daysUntil <= 3 || complexity >= 7) priority = 'high';
            else if (daysUntil > 7 && complexity < 4) priority = 'low';
            
            const hours = Math.ceil(complexity * 0.8);
            const tips = getTips(priority, complexity);
            
            // Show results
            document.getElementById('analysisResult').style.display = 'block';
            document.getElementById('resultPriority').textContent = priority.toUpperCase();
            document.getElementById('resultPriority').style.color = priority === 'high' ? '#dc3545' : priority === 'medium' ? '#ffc107' : '#28a745';
            document.getElementById('resultComplexity').textContent = complexity + '/10';
            document.getElementById('resultHours').textContent = hours + ' hours';
            document.getElementById('resultProfile').textContent = profile;
            document.getElementById('resultTips').textContent = tips;
            
            // Create task
            const tasks = JSON.parse(localStorage.getItem('dailybrew_tasks_' + user.id) || '[]');
            tasks.push({
                id: Date.now(),
                title: 'Task from Document',
                description: text.substring(0, 500),
                dueDate,
                aiPriority: priority,
                complexity,
                profile,
                status: 'pending',
                createdAt: new Date().toISOString()
            });
            localStorage.setItem('dailybrew_tasks_' + user.id, JSON.stringify(tasks));
            
            alert('Task created successfully!');
        }
        
        function getTips(priority, complexity) {
            if (priority === 'high') {
                return 'Start immediately! Focus on this task first and break it into smaller chunks.';
            } else if (complexity > 6) {
                return 'This is a complex task. Start early and review materials multiple times.';
            } else {
                return 'Good timing. Review the material and complete at a steady pace.';
            }
        }
        
        // Chat functionality
        document.getElementById('chatInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
        
        function sendMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            if (!message) return;
            
            // Add user message
            const messagesDiv = document.getElementById('chatMessages');
            messagesDiv.innerHTML += `<div class="chat-message user">${message}</div>`;
            input.value = '';
            
            // Simple AI response
            setTimeout(() => {
                const response = getAIResponse(message);
                messagesDiv.innerHTML += `<div class="chat-message ai">${response}</div>`;
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }, 500);
        }
        
        function getAIResponse(message) {
            const msg = message.toLowerCase();
            
            if (msg.includes('help')) {
                return "I can help you analyze tasks, suggest study schedules, and answer questions about your assignments. Just tell me what you need!";
            } else if (msg.includes('priorit') || msg.includes('priority')) {
                return "Based on your tasks, I recommend focusing on high-priority tasks first. Check your dashboard to see which tasks are due soon!";
            } else if (msg.includes('schedule') || msg.includes('study')) {
                return "I can help you plan! Try using different study profiles - Early Crammer for urgent tasks, Seamless for steady progress, or Late Crammer for when you need motivation.";
            } else if (msg.includes('deadline') || msg.includes('due')) {
                return "Check the Dashboard - it shows your tasks due today and tomorrow. The Calendar gives you a full view of all deadlines.";
            } else {
                return "That's a great question! I'm here to help you succeed. Would you like me to analyze any task or help you plan your study schedule?";
            }
        }
    </script>
</body>
</html>

