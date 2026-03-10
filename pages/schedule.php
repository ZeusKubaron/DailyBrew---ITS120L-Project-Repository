<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8 name="viewport"">
    <meta content="width=device-width, initial-scale=1.0">
    <title>Schedule - DailyBrew</title>
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
        
        .card { background: white; border-radius: 15px; padding: 25px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); margin-bottom: 20px; }
        .card h2 { color: #333; font-size: 1.2rem; margin-bottom: 15px; padding-bottom: 10px; border-bottom: 2px solid #667eea; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; color: #333; font-weight: 500; }
        .form-group input, .form-group select { width: 100%; padding: 10px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 15px; }
        
        .btn { padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); }
        
        .schedule-list { margin-top: 20px; max-height: 400px; overflow-y: auto; }
        .schedule-item { display: flex; align-items: center; justify-content: space-between; padding: 15px; border: 1px solid #e0e0e0; border-radius: 10px; margin-bottom: 10px; border-left: 4px solid; }
        .schedule-item h4 { color: #333; margin-bottom: 5px; }
        .schedule-item p { color: #666; font-size: 0.9rem; }
        .schedule-actions { display: flex; gap: 10px; }
        .btn-sm { padding: 5px 15px; font-size: 0.9rem; border-radius: 15px; }
        .btn-delete { background: #dc3545; }
        
        .color-options { display: flex; gap: 10px; margin-top: 10px; }
        .color-option { width: 30px; height: 30px; border-radius: 50%; cursor: pointer; border: 2px solid transparent; }
        .color-option.selected { border-color: #333; }
        
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-260px); }
            .sidebar.active { transform: translateX(0); }
            .main-content { margin-left: 0; }
            .form-row { grid-template-columns: 1fr; }
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
                <li><a href="document-analyzer.php"><span>📝</span> Add Task</a></li>
                <li><a href="schedule.php" class="active"><span>📚</span> Schedule</a></li>
                <li><a href="settings.php"><span>⚙️</span> Settings</a></li>
            </ul>
        </nav>
        
        <main class="main-content" id="mainContent">
            <div class="header">
                <h1>📚 Academic Schedule</h1>
                <div class="user-info">
                    <div class="user-avatar" id="userAvatar">U</div>
                    <button class="logout-btn" onclick="logout()">Logout</button>
                </div>
            </div>
            
            <div class="card">
                <h2>➕ Add Class Schedule</h2>
                <form id="scheduleForm">
                    <div class="form-group">
                        <label>Subject</label>
                        <input type="text" id="subject" required placeholder="e.g., Mathematics 101">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label>Day</label>
                            <select id="dayOfWeek" required>
                                <option value="Monday">Monday</option>
                                <option value="Tuesday">Tuesday</option>
                                <option value="Wednesday">Wednesday</option>
                                <option value="Thursday">Thursday</option>
                                <option value="Friday">Friday</option>
                                <option value="Saturday">Saturday</option>
                                <option value="Sunday">Sunday</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Start Time</label>
                            <input type="time" id="startTime" required>
                        </div>
                        <div class="form-group">
                            <label>End Time</label>
                            <input type="time" id="endTime" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label>Location (optional)</label>
                        <input type="text" id="location" placeholder="e.g., Room 204">
                    </div>
                    
                    <div class="form-group">
                        <label>Color</label>
                        <div class="color-options">
                            <div class="color-option selected" style="background: #4a90d9;" data-color="#4a90d9"></div>
                            <div class="color-option" style="background: #e74c3c;" data-color="#e74c3c"></div>
                            <div class="color-option" style="background: #2ecc71;" data-color="#2ecc71"></div>
                            <div class="color-option" style="background: #f39c12;" data-color="#f39c12"></div>
                            <div class="color-option" style="background: #9b59b6;" data-color="#9b59b6"></div>
                            <div class="color-option" style="background: #1abc9c;" data-color="#1abc9c"></div>
                        </div>
                    </div>
                    
                    <button type="submit" class="btn">Add Class</button>
                </form>
            </div>
            
            <div class="card">
                <h2>📅 Your Weekly Schedule</h2>
                <div id="scheduleList" class="schedule-list">
                    <p style="text-align: center; color: #999;">No classes added yet. Add your first class above!</p>
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
        
        // Load schedule
        let schedule = JSON.parse(localStorage.getItem('dailybrew_schedule_' + user.id) || '[]');
        renderSchedule();
        
        // Color selection
        let selectedColor = '#4a90d9';
        document.querySelectorAll('.color-option').forEach(opt => {
            opt.addEventListener('click', () => {
                document.querySelectorAll('.color-option').forEach(o => o.classList.remove('selected'));
                opt.classList.add('selected');
                selectedColor = opt.dataset.color;
            });
        });
        
        // Form submission
        document.getElementById('scheduleForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const subject = document.getElementById('subject').value;
            const dayOfWeek = document.getElementById('dayOfWeek').value;
            const startTime = document.getElementById('startTime').value;
            const endTime = document.getElementById('endTime').value;
            const location = document.getElementById('location').value;
            
            const classItem = {
                id: Date.now(),
                subject,
                dayOfWeek,
                startTime,
                endTime,
                location,
                color: selectedColor
            };
            
            schedule.push(classItem);
            localStorage.setItem('dailybrew_schedule_' + user.id, JSON.stringify(schedule));
            
            renderSchedule();
            document.getElementById('scheduleForm').reset();
        });
        
        function renderSchedule() {
            const scheduleList = document.getElementById('scheduleList');
            
            if (schedule.length === 0) {
                scheduleList.innerHTML = '<p style="text-align: center; color: #999;">No classes added yet. Add your first class above!</p>';
                return;
            }
            
            // Sort by day and time
            const days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
            schedule.sort((a, b) => {
                const dayDiff = days.indexOf(a.dayOfWeek) - days.indexOf(b.dayOfWeek);
                if (dayDiff !== 0) return dayDiff;
                return a.startTime.localeCompare(b.startTime);
            });
            
            scheduleList.innerHTML = schedule.map(s => `
                <div class="schedule-item" style="border-left-color: ${s.color};">
                    <div>
                        <h4>${s.subject}</h4>
                        <p>${s.dayOfWeek} | ${s.startTime} - ${s.endTime}${s.location ? ' | ' + s.location : ''}</p>
                    </div>
                    <div class="schedule-actions">
                        <button class="btn btn-sm btn-delete" onclick="deleteClass(${s.id})">✕</button>
                    </div>
                </div>
            `).join('');
        }
        
        function deleteClass(id) {
            if (confirm('Delete this class?')) {
                schedule = schedule.filter(s => s.id !== id);
                localStorage.setItem('dailybrew_schedule_' + user.id, JSON.stringify(schedule));
                renderSchedule();
            }
        }
    </script>
</body>
</html>

