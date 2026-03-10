<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tasks - DailyBrew</title>
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
        
        .card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { color: #333; font-size: 1.2rem; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #667eea; }
        
        .task-form { display: grid; grid-template-columns: 1fr 1fr; gap: 15px; }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #333; font-weight: 500; }
        .form-group input, .form-group select, .form-group textarea { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; }
        .form-group textarea { grid-column: span 2; min-height: 100px; }
        .btn { padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); }
        
        .task-list { margin-top: 20px; max-height: 400px; overflow-y: auto; }
        .task-item { display: flex; align-items: center; justify-content: space-between; padding: 15px; border: 1px solid #e0e0e0; border-radius: 10px; margin-bottom: 10px; }
        .task-item.completed { opacity: 0.6; text-decoration: line-through; }
        .task-info h4 { color: #333; margin-bottom: 5px; }
        .task-info p { color: #666; font-size: 0.9rem; }
        .priority-badge { display: inline-block; padding: 3px 10px; border-radius: 12px; font-size: 0.75rem; font-weight: bold; margin-left: 10px; }
        .priority-high { background: #dc3545; color: white; }
        .priority-medium { background: #ffc107; color: #333; }
        .priority-low { background: #28a745; color: white; }
        .task-actions { display: flex; gap: 10px; }
        .btn-sm { padding: 5px 15px; font-size: 0.9rem; border-radius: 15px; }
        .btn-complete { background: #28a745; }
        .btn-delete { background: #dc3545; }
        
        .profile-info { 
            background: #e8edff; 
            padding: 10px 15px; 
            border-radius: 8px; 
            margin-top: 10px;
            font-size: 0.9rem;
            color: #333;
        }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .task-form { grid-template-columns: 1fr; }
            .form-group textarea { grid-column: 1; }
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
                <li><a href="tasks.php" class="active"><span>📝</span> Tasks</a></li>
                <li><a href="document-analyzer.php"><span>📄</span> Add Task</a></li>
                <li><a href="schedule.php"><span>📚</span> Schedule</a></li>
                <li><a href="settings.php"><span>⚙️</span> Settings</a></li>
            </ul>
        </nav>
        
        <main class="main-content" id="mainContent">
            <div class="header">
                <h1>📝 Tasks</h1>
                <div class="user-info">
                    <div class="user-avatar" id="userAvatar">U</div>
                    <button class="logout-btn" onclick="logout()">Logout</button>
                </div>
            </div>
            
            <div class="card">
                <h2>➕ Add New Task</h2>
                <form id="taskForm" class="task-form">
                    <div class="form-group">
                        <label>Task Title</label>
                        <input type="text" id="taskTitle" required placeholder="e.g., Math Homework Ch.5">
                    </div>
                    <div class="form-group">
                        <label>Due Date</label>
                        <input type="date" id="taskDueDate" required>
                    </div>
                    <div class="form-group">
                        <label>Description (optional)</label>
                        <textarea id="taskDescription" placeholder="Task instructions/details..."></textarea>
                    </div>
                    <div class="form-group">
                        <label>Study Profile</label>
                        <select id="taskProfile">
                            <option value="seamless">Seamless (Recommended - Balanced)</option>
                            <option value="early_crammer">Early Crammer - Finish ASAP</option>
                            <option value="late_crammer">Late Crammer - Study close to deadline</option>
                        </select>
                        <div class="profile-info" id="profileInfo">
                            <strong>Seamless:</strong> Spaced out study sessions with breaks between
                        </div>
                    </div>
                    <div class="form-group" style="grid-column: span 2;">
                        <button type="submit" class="btn">🤖 Add Task with AI Analysis</button>
                    </div>
                </form>
            </div>
            
            <div class="card">
                <h2>📋 Your Tasks</h2>
                <div id="taskList" class="task-list">
                    <p style="text-align: center; color: #999;">No tasks yet. Add one above!</p>
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
        
        // Profile info display
        document.getElementById('taskProfile').addEventListener('change', function() {
            const profile = this.value;
            let info = '';
            switch(profile) {
                case 'seamless':
                    info = '<strong>Seamless:</strong> Spaced out study sessions with breaks in between (2 days before deadline)';
                    break;
                case 'early_crammer':
                    info = '<strong>Early Crammer:</strong> Schedule study blocks as early as possible to finish ASAP';
                    break;
                case 'late_crammer':
                    info = '<strong>Late Crammer:</strong> Schedule study blocks close to the deadline (last 3 days)';
                    break;
            }
            document.getElementById('profileInfo').innerHTML = info;
        });
        
        // Load tasks
        let tasks = JSON.parse(localStorage.getItem('dailybrew_tasks_' + user.id) || '[]');
        renderTasks();
        
        // Form submission
        document.getElementById('taskForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const title = document.getElementById('taskTitle').value;
            const dueDate = document.getElementById('taskDueDate').value;
            const description = document.getElementById('taskDescription').value;
            const profile = document.getElementById('taskProfile').value;
            
            // AI Analysis
            const wordCount = description ? description.split(/\s+/).length : 0;
            const titleLower = title.toLowerCase();
            
            let complexity = 3;
            if (titleLower.includes('exam') || titleLower.includes('final')) complexity = 8;
            else if (titleLower.includes('quiz')) complexity = 5;
            else if (titleLower.includes('homework') || titleLower.includes('hw')) complexity = 4;
            else if (titleLower.includes('project')) complexity = 6;
            
            complexity += wordCount > 200 ? 2 : wordCount > 100 ? 1 : 0;
            complexity = Math.min(10, complexity);
            
            const due = new Date(dueDate);
            const today = new Date();
            const daysUntil = Math.ceil((due - today) / (1000 * 60 * 60 * 24));
            
            let aiPriority = 'medium';
            if (daysUntil <= 3 || complexity >= 7) aiPriority = 'high';
            else if (daysUntil > 7 && complexity < 4) aiPriority = 'low';
            
            const task = {
                id: Date.now(),
                title,
                description,
                dueDate,
                aiPriority,
                complexity,
                profile,
                status: 'pending',
                createdAt: new Date().toISOString()
            };
            
            tasks.push(task);
            localStorage.setItem('dailybrew_tasks_' + user.id, JSON.stringify(tasks));
            
            // Generate study blocks
            generateStudyBlocks(task);
            
            renderTasks();
            document.getElementById('taskForm').reset();
            alert(`Task added! AI Priority: ${aiPriority.toUpperCase()}, Complexity: ${complexity}/10`);
        });
        
        // Enhanced study block generation
        function generateStudyBlocks(task) {
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
        
        function renderTasks() {
            const taskList = document.getElementById('taskList');
            
            if (tasks.length === 0) {
                taskList.innerHTML = '<p style="text-align: center; color: #999;">No tasks yet. Add one above!</p>';
                return;
            }
            
            taskList.innerHTML = tasks.map(task => `
                <div class="task-item ${task.status === 'completed' ? 'completed' : ''}">
                    <div class="task-info">
                        <h4>${task.title} <span class="priority-badge priority-${task.aiPriority}">${task.aiPriority.toUpperCase()}</span></h4>
                        <p>Due: ${new Date(task.dueDate).toLocaleDateString()} | Complexity: ${task.complexity}/10 | Profile: ${task.profile}</p>
                    </div>
                    <div class="task-actions">
                        ${task.status !== 'completed' ? `<button class="btn btn-sm btn-complete" onclick="completeTask(${task.id})">✓</button>` : ''}
                        <button class="btn btn-sm btn-delete" onclick="deleteTask(${task.id})">✕</button>
                    </div>
                </div>
            `).join('');
        }
        
        function completeTask(taskId) {
            tasks = tasks.map(t => t.id === taskId ? {...t, status: 'completed'} : t);
            localStorage.setItem('dailybrew_tasks_' + user.id, JSON.stringify(tasks));
            
            let blocks = JSON.parse(localStorage.getItem('dailybrew_blocks_' + user.id) || '[]');
            blocks = blocks.filter(b => b.taskId !== taskId);
            localStorage.setItem('dailybrew_blocks_' + user.id, JSON.stringify(blocks));
            
            renderTasks();
        }
        
        function deleteTask(taskId) {
            if (confirm('Delete this task? This will also delete all associated study blocks.')) {
                tasks = tasks.filter(t => t.id !== taskId);
                localStorage.setItem('dailybrew_tasks_' + user.id, JSON.stringify(tasks));
                
                let blocks = JSON.parse(localStorage.getItem('dailybrew_blocks_' + user.id) || '[]');
                blocks = blocks.filter(b => b.taskId !== taskId);
                localStorage.setItem('dailybrew_blocks_' + user.id, JSON.stringify(blocks));
                
                renderTasks();
            }
        }
    </script>
</body>
</html>

