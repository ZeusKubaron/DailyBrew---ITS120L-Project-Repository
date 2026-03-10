<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task - DailyBrew</title>
    <link rel="stylesheet" href="../css/style.css">
    <!-- PDF.js -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <!-- Mammoth.js for DOCX -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
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
        
        /* Floating hamburger when sidebar collapsed */
        .floating-hamburger {
            display: none;
            position: fixed;
            top: 20px;
            left: 20px;
            z-index: 1001;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            padding: 10px 15px;
            font-size: 1.3rem;
            cursor: pointer;
            box-shadow: 0 4px 15px rgba(0,0,0,0.2);
        }
        .floating-hamburger.visible { display: block; }
        
        .nav-menu { list-style: none; }
        .nav-menu li { margin-bottom: 5px; }
        .nav-menu a { display: flex; align-items: center; padding: 12px 15px; color: white; text-decoration: none; border-radius: 10px; transition: background 0.2s; }
        .nav-menu a:hover, .nav-menu a.active { background: rgba(255,255,255,0.2); }
        .nav-menu a span { margin-right: 10px; }
        
        .main-content { 
            flex: 1; 
            margin-left: 260px; 
            padding: 20px; 
            transition: margin-left 0.3s;
            min-height: 100vh;
        }
        .main-content.expanded { margin-left: 0; }
        
        /* Sticky Header */
        .header { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
            margin-bottom: 30px;
            background: #f5f7fa;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            border-radius: 10px;
        }
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
        .form-group textarea { min-height: 100px; }
        
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
        
        .loading { text-align: center; padding: 20px; color: #666; }
        .loading-spinner { 
            border: 3px solid #f3f3f3; 
            border-top: 3px solid #667eea; 
            border-radius: 50%; 
            width: 30px; 
            height: 30px; 
            animation: spin 1s linear infinite; 
            margin: 0 auto 10px;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Floating hamburger when sidebar collapsed -->
        <button class="floating-hamburger" id="floatingHamburger" onclick="toggleSidebar()">☰</button>
        
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <span class="logo">☕ DailyBrew</span>
                <button class="hamburger" onclick="toggleSidebar()">☰</button>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php"><span>🏠</span> Dashboard</a></li>
                <li><a href="calendar.php"><span>📅</span> Calendar</a></li>
                <li><a href="tasks.php"><span>📝</span> Tasks</a></li>
                <li><a href="document-analyzer.php" class="active"><span>📝</span> Add Task</a></li>
                <li><a href="schedule.php"><span>📚</span> Schedule</a></li>
                <li><a href="settings.php"><span>⚙️</span> Settings</a></li>
            </ul>
        </nav>
        
        <main class="main-content" id="mainContent">
            <div class="header">
                <h1>📝 Add New Task</h1>
                <div class="user-info">
                    <div class="user-avatar" id="userAvatar">U</div>
                    <button class="logout-btn" onclick="logout()">Logout</button>
                </div>
            </div>
            
            <div class="card">
                <h2>📤 Upload Document (Optional)</h2>
                <p style="color: #666; margin-bottom: 15px; font-size: 0.9rem;">
                    Upload a file to automatically extract task details. Supported: PDF, DOCX, TXT, MD
                </p>
                
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">📄</div>
                    <p class="upload-text">Drop a file here or click to upload</p>
                    <p class="upload-hint">Supports PDF, DOCX, TXT, MD files</p>
                    <input type="file" id="fileInput" style="display: none;" accept=".pdf,.docx,.doc,.txt,.md">
                </div>
                
                <div id="uploadStatus" style="margin-top: 10px; text-align: center; color: #667eea;"></div>
            </div>
            
            <div class="card">
                <h2>📋 Task Details</h2>
                <form id="taskForm">
                    <div class="form-group">
                        <label>Task Title *</label>
                        <input type="text" id="taskTitle" required placeholder="e.g., Math Homework Chapter 5">
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="taskDescription" rows="4" placeholder="Task instructions, details, or notes..."></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label>Due Date *</label>
                        <input type="date" id="taskDueDate" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Study Profile</label>
                        <select id="taskProfile">
                            <option value="seamless">Seamless (Recommended - Balanced)</option>
                            <option value="early_crammer">Early Crammer - Finish ASAP</option>
                            <option value="late_crammer">Late Crammer - Study close to deadline</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn">🤖 Analyze with AI & Create Task</button>
                </form>
            </div>
            
            <div class="card" id="analysisResult" style="display: none;">
                <h2>📊 AI Analysis</h2>
                <div class="analysis-result">
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
                        <span class="analysis-label">Study Tips:</span>
                        <span class="analysis-value" id="resultTips">-</span>
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h2>🤖 AI Assistant</h2>
                <div class="ai-chat">
                    <div class="chat-messages" id="chatMessages">
                        <div class="chat-message ai">Hello! I'm here to help you plan your studies. Upload a document or fill in the task details, and I'll analyze it for you!</div>
                    </div>
                    <div class="chat-input">
                        <input type="text" id="chatInput" placeholder="Ask me anything about your tasks...">
                        <button class="btn" onclick="sendMessage()">Send</button>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script>
        // Initialize PDF.js worker
        pdfjsLib.GlobalWorkerOptions.workerSrc = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';
        
        const currentUser = localStorage.getItem('dailybrew_current_user');
        if (!currentUser) { window.location.href = '../auth/login.php'; }
        
        const user = JSON.parse(currentUser);
        document.getElementById('userAvatar').textContent = user.firstName.charAt(0);
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const floatingHamburger = document.getElementById('floatingHamburger');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('expanded');
            
            // Show/hide floating hamburger
            if (sidebar.classList.contains('collapsed')) {
                floatingHamburger.classList.add('visible');
            } else {
                floatingHamburger.classList.remove('visible');
            }
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
        
        async function handleFile(file) {
            const uploadStatus = document.getElementById('uploadStatus');
            const validTypes = ['application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'text/plain', 'text/markdown'];
            const extension = file.name.split('.').pop().toLowerCase();
            
            if (!validTypes.includes(file.type) && !['pdf', 'docx', 'doc', 'txt', 'md'].includes(extension)) {
                alert('Unsupported file type. Please upload PDF, DOCX, TXT, or MD files.');
                return;
            }
            
            uploadStatus.innerHTML = '<div class="loading"><div class="loading-spinner"></div>Processing document...</div>';
            
            try {
                let text = '';
                
                if (extension === 'pdf') {
                    text = await extractTextFromPDF(file);
                } else if (extension === 'docx' || extension === 'doc') {
                    text = await extractTextFromDOCX(file);
                } else {
                    // Plain text or markdown
                    text = await readFileAsText(file);
                }
                
                if (text.trim()) {
                    // Send to AI for analysis and extraction
                    uploadStatus.innerHTML = '<div class="loading"><div class="loading-spinner"></div>Analyzing with AI...</div>';
                    await analyzeWithAI(text, file.name);
                } else {
                    uploadStatus.textContent = 'Could not extract text from this file.';
                }
            } catch (error) {
                console.error('Error processing file:', error);
                uploadStatus.textContent = 'Error processing file: ' + error.message;
            }
        }
        
        async function readFileAsText(file) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => resolve(e.target.result);
                reader.onerror = (e) => reject(e);
                reader.readAsText(file);
            });
        }
        
        async function extractTextFromPDF(file) {
            const arrayBuffer = await file.arrayBuffer();
            const pdf = await pdfjsLib.getDocument({ data: arrayBuffer }).promise;
            let fullText = '';
            
            for (let i = 1; i <= pdf.numPages; i++) {
                const page = await pdf.getPage(i);
                const textContent = await page.getTextContent();
                const pageText = textContent.items.map(item => item.str).join(' ');
                fullText += pageText + '\n';
            }
            
            return fullText;
        }
        
        async function extractTextFromDOCX(file) {
            const arrayBuffer = await file.arrayBuffer();
            const result = await mammoth.extractRawText({ arrayBuffer: arrayBuffer });
            return result.value;
        }
        
        async function analyzeWithAI(content, filename) {
            const uploadStatus = document.getElementById('uploadStatus');
            
            // Build a prompt to extract task details
            const prompt = `Analyze the following document and extract the academic task information.

Document: ${filename}

Content:
${content.substring(0, 3000)}

Provide a JSON response with the following structure:
{
    "title": "Extracted task title or activity name",
    "description": "Brief description of what needs to be done",
    "due_date": "YYYY-MM-DD format (if found in document, otherwise null)",
    "activity_type": "exam, homework, quiz, project, assignment, reading, or other",
    "priority": "high, medium, or low",
    "complexity": 1-10,
    "study_tips": "Brief study tips for this type of activity"
}

Look for:
- Assignment/homework/exam names
- Due dates (any format like "due March 15", "due 03/15/2024", "deadline: March 15th")
- Activity types (exam, quiz, homework, project, etc.)
- Page numbers or chapters to read

Respond ONLY with valid JSON.`;

            try {
                const response = await fetch('../api/analyze-text.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt: prompt })
                });
                
                const data = await response.json();
                
                if (data.success && data.analysis) {
                    const analysis = data.analysis;
                    
                    // Auto-fill form fields
                    if (analysis.title) {
                        document.getElementById('taskTitle').value = analysis.title;
                    }
                    if (analysis.description) {
                        document.getElementById('taskDescription').value = analysis.description;
                    }
                    if (analysis.due_date) {
                        document.getElementById('taskDueDate').value = analysis.due_date;
                    }
                    
                    // Show AI analysis results
                    document.getElementById('analysisResult').style.display = 'block';
                    document.getElementById('resultPriority').textContent = (analysis.priority || 'medium').toUpperCase();
                    document.getElementById('resultPriority').style.color = 
                        analysis.priority === 'high' ? '#dc3545' : 
                        analysis.priority === 'medium' ? '#ffc107' : '#28a745';
                    document.getElementById('resultComplexity').textContent = (analysis.complexity || 5) + '/10';
                    document.getElementById('resultHours').textContent = Math.ceil((analysis.complexity || 5) * 0.8) + ' hours';
                    document.getElementById('resultTips').textContent = analysis.study_tips || 'Break this task into smaller chunks.';
                    
                    uploadStatus.innerHTML = '<span style="color: #28a745;">✓ Document analyzed successfully! Check the form above.</span>';
                } else {
                    // Fallback: just use the content as description
                    document.getElementById('taskDescription').value = content.substring(0, 1000);
                    uploadStatus.innerHTML = '<span style="color: #ffc107;">⚠ Could not fully analyze. Please fill in details manually.</span>';
                }
            } catch (error) {
                console.error('AI analysis error:', error);
                document.getElementById('taskDescription').value = content.substring(0, 1000);
                uploadStatus.innerHTML = '<span style="color: #ffc107;">⚠ Error analyzing. Please fill in details manually.</span>';
            }
        }
        
        // Form submission
        document.getElementById('taskForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const title = document.getElementById('taskTitle').value;
            const description = document.getElementById('taskDescription').value;
            const dueDate = document.getElementById('taskDueDate').value;
            const profile = document.getElementById('taskProfile').value;
            
            // AI Analysis
            const wordCount = description ? description.split(/\s+/).length : 0;
            const titleLower = title.toLowerCase();
            
            let complexity = 3;
            if (titleLower.includes('exam') || titleLower.includes('final') || titleLower.includes('midterm')) complexity = 8;
            else if (titleLower.includes('quiz')) complexity = 5;
            else if (titleLower.includes('homework') || titleLower.includes('hw')) complexity = 4;
            else if (titleLower.includes('project') || titleLower.includes('paper')) complexity = 6;
            
            complexity += wordCount > 500 ? 2 : wordCount > 200 ? 1 : 0;
            complexity = Math.min(10, complexity);
            
            const due = new Date(dueDate);
            const today = new Date();
            const daysUntil = Math.ceil((due - today) / (1000 * 60 * 60 * 24));
            
            let priority = 'medium';
            if (daysUntil <= 3 || complexity >= 7) priority = 'high';
            else if (daysUntil > 7 && complexity < 4) priority = 'low';
            
            const task = {
                id: Date.now(),
                title,
                description,
                dueDate,
                aiPriority: priority,
                complexity,
                profile,
                status: 'pending',
                createdAt: new Date().toISOString()
            };
            
            // Save task
            const tasks = JSON.parse(localStorage.getItem('dailybrew_tasks_' + user.id) || '[]');
            tasks.push(task);
            localStorage.setItem('dailybrew_tasks_' + user.id, JSON.stringify(tasks));
            
            // Generate study blocks with enhanced logic
            await generateStudyBlocks(task);
            
            // Show analysis
            document.getElementById('analysisResult').style.display = 'block';
            document.getElementById('resultPriority').textContent = priority.toUpperCase();
            document.getElementById('resultPriority').style.color = 
                priority === 'high' ? '#dc3545' : priority === 'medium' ? '#ffc107' : '#28a745';
            document.getElementById('resultComplexity').textContent = complexity + '/10';
            document.getElementById('resultHours').textContent = Math.ceil(complexity * 0.8) + ' hours';
            document.getElementById('resultTips').textContent = getTips(priority, complexity);
            
            alert('Task created successfully! 🎉\n\nAI Priority: ' + priority.toUpperCase() + '\nComplexity: ' + complexity + '/10');
            
            // Reset form
            document.getElementById('taskForm').reset();
        });
        
        function getTips(priority, complexity) {
            if (priority === 'high') {
                return 'Start immediately! Focus on this task first and break it into smaller chunks.';
            } else if (complexity > 6) {
                return 'This is a complex task. Start early and review materials multiple times.';
            } else {
                return 'Good timing. Review the material and complete at a steady pace.';
            }
        }
        
        // Enhanced study block generation with collision detection
        async function generateStudyBlocks(task) {
            const blocks = JSON.parse(localStorage.getItem('dailybrew_blocks_' + user.id) || '[]');
            const preferences = JSON.parse(localStorage.getItem('dailybrew_preferences_' + user.id) || getDefaultPreferences());
            const schedule = JSON.parse(localStorage.getItem('dailybrew_schedule_' + user.id) || '[]');
            const sleepSchedule = preferences.sleep_schedule || { start: '22:00', end: '08:00' };
            
            const duration = preferences.study_block_duration || 30;
            const startHour = parseInt((preferences.earliest_time_start || '08:00').split(':')[0]);
            const endHour = parseInt((preferences.latest_time_end || '22:00').split(':')[0]);
            
            const dueDate = new Date(task.dueDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            // Calculate date range based on profile
            let startDate = new Date(today);
            let endDate = new Date(dueDate);
            endDate.setDate(endDate.getDate() - 1);
            
            if (task.profile === 'early_crammer') {
                // Early Crammer: Schedule from today until day before deadline
                endDate = new Date(dueDate);
                endDate.setDate(endDate.getDate() - 1);
            } else if (task.profile === 'late_crammer') {
                // Late Crammer: Schedule from 3 days before deadline until day before
                startDate = new Date(dueDate);
                startDate.setDate(startDate.getDate() - 3);
                if (startDate < today) startDate = today;
                endDate = new Date(dueDate);
                endDate.setDate(endDate.getDate() - 1);
            } else {
                // Seamless: Schedule from today until 2 days before deadline
                endDate = new Date(dueDate);
                endDate.setDate(endDate.getDate() - 2);
            }
            
            if (endDate < startDate) endDate = startDate;
            
            const blockCount = Math.max(1, Math.ceil(task.complexity / 2));
            const hoursPerBlock = Math.ceil(task.complexity / blockCount);
            
            let currentDate = new Date(startDate);
            let blockIndex = 0;
            
            while (currentDate <= endDate && blockIndex < blockCount) {
                const dateStr = currentDate.toISOString().split('T')[0];
                
                for (let hour = startHour; hour < endHour && blockIndex < blockCount; hour += hoursPerBlock) {
                    const blockStart = `${hour.toString().padStart(2, '0')}:00`;
                    const blockEnd = `${Math.min(hour + hoursPerBlock, endHour).toString().padStart(2, '0')}:00`;
                    
                    // Check for collisions
                    if (!isSlotAvailable(dateStr, blockStart, blockEnd, schedule, blocks, sleepSchedule)) {
                        continue; // Try next slot
                    }
                    
                    blocks.push({
                        id: Date.now() + blockIndex,
                        taskId: task.id,
                        title: `Study: ${task.title}`,
                        scheduledDate: dateStr,
                        startTime: blockStart,
                        endTime: blockEnd,
                        profile: task.profile
                    });
                    
                    blockIndex++;
                    break;
                }
                
                currentDate.setDate(currentDate.getDate() + 1);
            }
            
            localStorage.setItem('dailybrew_blocks_' + user.id, JSON.stringify(blocks));
        }
        
        function getDefaultPreferences() {
            return JSON.stringify({
                earliest_time_start: '08:00',
                latest_time_end: '22:00',
                study_block_duration: 30,
                default_profile: 'seamless',
                sleep_schedule: { start: '22:00', end: '08:00' }
            });
        }
        
        function isSlotAvailable(date, startTime, endTime, schedule, blocks, sleepSchedule) {
            const start = timeToMinutes(startTime);
            const end = timeToMinutes(endTime);
            
            // Check sleep schedule
            const sleepStart = timeToMinutes(sleepSchedule.start);
            const sleepEnd = timeToMinutes(sleepSchedule.end);
            
            if (sleepStart > sleepEnd) {
                // Sleep crosses midnight
                if (start >= sleepStart || end <= sleepEnd) return false;
            } else {
                if (start >= sleepStart && end <= sleepEnd) return false;
            }
            
            // Check class schedule
            const dayOfWeek = new Date(date).toLocaleDateString('en-US', { weekday: 'long' });
            for (const cls of schedule) {
                if (cls.dayOfWeek === dayOfWeek) {
                    const clsStart = timeToMinutes(cls.startTime);
                    const clsEnd = timeToMinutes(cls.endTime);
                    if (!(end <= clsStart || start >= clsEnd)) return false;
                }
            }
            
            // Check existing study blocks
            for (const block of blocks) {
                if (block.scheduledDate === date) {
                    const blockStart = timeToMinutes(block.startTime);
                    const blockEnd = timeToMinutes(block.endTime);
                    if (!(end <= blockStart || start >= blockEnd)) return false;
                }
            }
            
            return true;
        }
        
        function timeToMinutes(time) {
            const [h, m] = time.split(':').map(Number);
            return h * 60 + m;
        }
        
        // Chat functionality
        document.getElementById('chatInput').addEventListener('keypress', (e) => {
            if (e.key === 'Enter') sendMessage();
        });
        
        function sendMessage() {
            const input = document.getElementById('chatInput');
            const message = input.value.trim();
            if (!message) return;
            
            const messagesDiv = document.getElementById('chatMessages');
            messagesDiv.innerHTML += `<div class="chat-message user">${message}</div>`;
            input.value = '';
            
            setTimeout(() => {
                const response = getAIResponse(message);
                messagesDiv.innerHTML += `<div class="chat-message ai">${response}</div>`;
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }, 500);
        }
        
        function getAIResponse(message) {
            const msg = message.toLowerCase();
            
            if (msg.includes('help')) {
                return "I can help you analyze tasks, suggest study schedules, and answer questions. Upload a document or fill in the task form, and I'll analyze it for you!";
            } else if (msg.includes('priorit')) {
                return "Check your Dashboard - it shows tasks due today and tomorrow. The AI assigns priorities based on deadline proximity and complexity.";
            } else if (msg.includes('schedule') || msg.includes('study')) {
                return "Three profiles: Early Crammer (finish ASAP), Seamless (balanced), Late Crammer (close to deadline). Set your preference when creating a task!";
            } else if (msg.includes('deadline') || msg.includes('due')) {
                return "Your Dashboard shows due today and tomorrow tasks. The Calendar gives you the full picture of all deadlines.";
            } else {
                return "That's a great question! Would you like me to help you analyze a task or plan your study schedule?";
            }
        }
    </script>
</body>
</html>

