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
        
        /* Debug panel */
        #debugPanel {
            display: none;
            background: #1e1e1e;
            color: #0f0;
            padding: 15px;
            border-radius: 8px;
            margin-top: 20px;
            font-family: monospace;
            font-size: 12px;
            max-height: 200px;
            overflow-y: auto;
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
                <li><a href="document-analyzer.php" class="active"><span>📄</span> Add Task</a></li>
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
                    Upload a file to automatically extract task details. The AI will analyze the document and fill in the form below.
                </p>
                
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">📄</div>
                    <p class="upload-text">Drop a file here or click to upload</p>
                    <p class="upload-hint">Supports PDF, DOCX, TXT, MD files</p>
                    <input type="file" id="fileInput" style="display: none;" accept=".pdf,.docx,.doc,.txt,.md">
                </div>
                
                <div id="uploadStatus" style="margin-top: 10px; text-align: center; color: #667eea;"></div>
                
                <!-- Debug panel -->
                <div id="debugPanel"></div>
            </div>
            
            <div class="card">
                <h2>📋 Task Details</h2>
                <form id="taskForm">
                    <div class="form-group">
                        <label>Task Title *</label>
                        <input type="text" id="taskTitle" required placeholder="e.g., Math Chapter 5 Homework">
                    </div>
                    
                    <div class="form-group">
                        <label>Description</label>
                        <textarea id="taskDescription" rows="4" placeholder="Task instructions, details, or notes... (AI will summarize from document)"></textarea>
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
                    
                    <button type="submit" class="btn">🤖 Create Task</button>
                </form>
            </div>
            
            <div class="card" id="analysisResult" style="display: none;">
                <h2>📊 AI Analysis</h2>
                <div class="analysis-result">
                    <div class="analysis-item">
                        <span class="analysis-label">Activity Type:</span>
                        <span class="analysis-value" id="resultType">-</span>
                    </div>
                    <div class="analysis-item">
                        <span class="analysis-label">Priority:</span>
                        <span class="analysis-value" id="resultPriority">-</span>
                    </div>
                    <div class="analysis-item">
                        <span class="analysis-label">Complexity:</span>
                        <span class="analysis-value" id="resultComplexity">-</span>
                    </div>
                    <div class="analysis-item">
                        <span class="analysis-label">Study Tips:</span>
                        <span class="analysis-value" id="resultTips">-</span>
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
        
        // Debug logging
        function debugLog(msg) {
            const panel = document.getElementById('debugPanel');
            panel.style.display = 'block';
            panel.innerHTML += '<div>' + msg + '</div>';
            console.log('[DOC ANALYZER]', msg);
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
            const extension = file.name.split('.').pop().toLowerCase();
            
            const validExtensions = ['pdf', 'docx', 'doc', 'txt', 'md'];
            if (!validExtensions.includes(extension)) {
                alert('Unsupported file type. Please upload PDF, DOCX, TXT, or MD files.');
                return;
            }
            
            uploadStatus.innerHTML = '<div class="loading"><div class="loading-spinner"></div>Processing document...</div>';
            debugLog('Starting file processing: ' + file.name + ' (' + extension + ')');
            
            try {
                let text = '';
                
                if (extension === 'pdf') {
                    debugLog('Extracting text from PDF...');
                    text = await extractTextFromPDF(file);
                } else if (extension === 'docx' || extension === 'doc') {
                    debugLog('Extracting text from DOCX...');
                    text = await extractTextFromDOCX(file);
                } else {
                    debugLog('Reading text file...');
                    text = await readFileAsText(file);
                }
                
                debugLog('Extracted text length: ' + text.length + ' chars');
                
                if (text.trim() && text.trim().length > 10) {
                    uploadStatus.innerHTML = '<div class="loading"><div class="loading-spinner"></div>Analyzing with AI...</div>';
                    await analyzeWithAI(text, file.name);
                } else {
                    debugLog('ERROR: Could not extract meaningful text from file');
                    uploadStatus.textContent = 'Could not extract text from this file. Try a different format.';
                }
            } catch (error) {
                console.error('Error processing file:', error);
                debugLog('ERROR: ' + error.message);
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
        
        // Simplified, more effective AI analysis
        async function analyzeWithAI(content, filename) {
            const uploadStatus = document.getElementById('uploadStatus');
            
            // Frontend just passes document context; backend prompt defines the JSON format
            const prompt = "Filename: " + filename + "\n\nDocument content:\n" + content;
            
            debugLog('Sending to AI analysis...');
            debugLog('Content preview: ' + content.substring(0, 200) + '...');
            
            try {
                const response = await fetch('../api/analyze-text.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ prompt: prompt })
                });
                
                const data = await response.json();
                debugLog('API Response: ' + JSON.stringify(data).substring(0, 500));
                
                if (data.success && data.analysis) {
                    const a = data.analysis;
                    debugLog('Parsed analysis: title=' + a.title + ', type=' + a.activity_type);
                    
                    // Auto-fill form fields with extracted data
                    if (a.title) {
                        document.getElementById('taskTitle').value = a.title;
                    }
                    if (a.description) {
                        document.getElementById('taskDescription').value = a.description;
                    }
                    if (a.due_date) {
                        document.getElementById('taskDueDate').value = a.due_date;
                    }
                    
                    // Show AI analysis results
                    document.getElementById('analysisResult').style.display = 'block';
                    document.getElementById('resultType').textContent = (a.activity_type || 'other').toUpperCase();
                    
                    const priority = a.priority || 'medium';
                    document.getElementById('resultPriority').textContent = priority.toUpperCase();
                    document.getElementById('resultPriority').style.color = 
                        priority === 'high' ? '#dc3545' : 
                        priority === 'medium' ? '#ffc107' : '#28a745';
                    
                    document.getElementById('resultComplexity').textContent = (a.complexity || 5) + '/10';
                    document.getElementById('resultTips').textContent = a.study_tips || 'Break this task into smaller chunks.';
                    
                    if (data.fallback) {
                        uploadStatus.innerHTML = '<span style="color: #ffc107;">⚠ AI analysis using fallback mode. Results may be limited.</span>';
                    } else {
                        uploadStatus.innerHTML = '<span style="color: #28a745;">✓ Document analyzed! Check the form above.</span>';
                    }
                } else {
                    debugLog('ERROR: Analysis failed - ' + (data.error || 'Unknown error'));
                    uploadStatus.innerHTML = '<span style="color: #ffc107;">⚠ Could not analyze. Please fill in details manually.</span>';
                }
            } catch (error) {
                console.error('AI analysis error:', error);
                debugLog('ERROR: ' + error.message);
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
            else if (titleLower.includes('project') || titleLower.includes('paper') || titleLower.includes('essay')) complexity = 6;
            else if (titleLower.includes('lab')) complexity = 5;
            else if (titleLower.includes('reading')) complexity = 2;
            
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
            
            // Generate study blocks
            await generateStudyBlocks(task);
            
            alert('Task created successfully! 🎉\n\nAI Priority: ' + priority.toUpperCase() + '\nComplexity: ' + complexity + '/10');
            
            // Reset form
            document.getElementById('taskForm').reset();
            document.getElementById('analysisResult').style.display = 'none';
            document.getElementById('uploadStatus').textContent = '';
            document.getElementById('debugPanel').style.display = 'none';
        });
        
        // Enhanced study block generation
        async function generateStudyBlocks(task) {
            const blocks = JSON.parse(localStorage.getItem('dailybrew_blocks_' + user.id) || '[]');
            const preferences = loadPreferences();
            const schedule = JSON.parse(localStorage.getItem('dailybrew_schedule_' + user.id) || '[]');
            const sleepSchedule = preferences.sleep_schedule || { start: '22:00', end: '08:00' };
            
            const duration = preferences.study_block_duration || 30;
            const startHour = parseInt((preferences.earliest_time_start || '08:00').split(':')[0]);
            const endHour = parseInt((preferences.latest_time_end || '22:00').split(':')[0]);
            
            const dueDate = new Date(task.dueDate);
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            
            let startDate = new Date(today);
            let endDate = new Date(dueDate);
            endDate.setDate(endDate.getDate() - 1);
            
            if (task.profile === 'early_crammer') {
                endDate = new Date(dueDate);
                endDate.setDate(endDate.getDate() - 1);
            } else if (task.profile === 'late_crammer') {
                startDate = new Date(dueDate);
                startDate.setDate(startDate.getDate() - 3);
                if (startDate < today) startDate = today;
                endDate = new Date(dueDate);
                endDate.setDate(endDate.getDate() - 1);
            } else {
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
                    
                    if (!isSlotAvailable(dateStr, blockStart, blockEnd, schedule, blocks, sleepSchedule)) {
                        continue;
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
        
        function loadPreferences() {
            try {
                const prefs = localStorage.getItem('dailybrew_preferences_' + user.id);
                if (prefs) {
                    return JSON.parse(prefs);
                }
            } catch (e) {
                console.error('Error loading preferences:', e);
            }
            return {
                earliest_time_start: '08:00',
                latest_time_end: '22:00',
                study_block_duration: 30,
                default_profile: 'seamless',
                sleep_schedule: { start: '22:00', end: '08:00' }
            };
        }
        
        function isSlotAvailable(date, startTime, endTime, schedule, blocks, sleepSchedule) {
            const start = timeToMinutes(startTime);
            const end = timeToMinutes(endTime);
            
            const sleepStart = timeToMinutes(sleepSchedule.start);
            const sleepEnd = timeToMinutes(sleepSchedule.end);
            
            if (sleepStart > sleepEnd) {
                if (start >= sleepStart || end <= sleepEnd) return false;
            } else {
                if (start >= sleepStart && end <= sleepEnd) return false;
            }
            
            const dayOfWeek = new Date(date).toLocaleDateString('en-US', { weekday: 'long' });
            for (const cls of schedule) {
                if (cls.dayOfWeek === dayOfWeek) {
                    const clsStart = timeToMinutes(cls.startTime);
                    const clsEnd = timeToMinutes(cls.endTime);
                    if (!(end <= clsStart || start >= clsEnd)) return false;
                }
            }
            
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
    </script>
</body>
</html>
