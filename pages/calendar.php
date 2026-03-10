<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendar - DailyBrew</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.css" rel="stylesheet">
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
        
        .card { background: white; border-radius: 15px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
        #calendar { padding: 20px; }
        
        .fc-event { cursor: pointer; }
        .fc-toolbar { margin-bottom: 20px; }
        .fc-toolbar h2 { font-size: 1.5rem; }
        
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
                <li><a href="calendar.php" class="active"><span>📅</span> Calendar</a></li>
                <li><a href="tasks.php"><span>📝</span> Tasks</a></li>
                <li><a href="document-analyzer.php"><span>📄</span> Document Analyzer</a></li>
                <li><a href="schedule.php"><span>📚</span> Schedule</a></li>
                <li><a href="settings.php"><span>⚙️</span> Settings</a></li>
            </ul>
        </nav>
        
        <main class="main-content" id="mainContent">
            <div class="header">
                <h1>📅 Calendar</h1>
                <div class="user-info">
                    <div class="user-avatar" id="userAvatar">U</div>
                    <button class="logout-btn" onclick="logout()">Logout</button>
                </div>
            </div>
            
            <div class="card">
                <div id="calendar"></div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.js"></script>
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
        
        // Load all data
        const tasks = JSON.parse(localStorage.getItem('dailybrew_tasks_' + user.id) || '[]');
        const schedule = JSON.parse(localStorage.getItem('dailybrew_schedule_' + user.id) || '[]');
        const blocks = JSON.parse(localStorage.getItem('dailybrew_blocks_' + user.id) || '[]');
        
        // Build events array
        const events = [];
        
        // Task deadlines
        tasks.filter(t => t.status !== 'completed').forEach(task => {
            events.push({
                id: 'task-' + task.id,
                title: '📝 ' + task.title,
                start: task.dueDate,
                backgroundColor: task.aiPriority === 'high' ? '#dc3545' : task.aiPriority === 'medium' ? '#ffc107' : '#28a745',
                borderColor: 'transparent',
                type: 'task'
            });
        });
        
        // Study blocks
        blocks.forEach(block => {
            events.push({
                id: 'block-' + block.id,
                title: '📚 ' + block.title,
                start: block.scheduledDate + 'T' + block.startTime,
                end: block.scheduledDate + 'T' + block.endTime,
                backgroundColor: '#667eea',
                borderColor: '#764ba2',
                type: 'study'
            });
        });
        
        // Academic schedule
        const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        schedule.forEach(s => {
            const dayIndex = days.indexOf(s.dayOfWeek);
            for (let week = 0; week < 8; week++) {
                const weekStart = moment().startOf('week');
                const eventDate = weekStart.add(dayIndex, 'days').add(week, 'weeks');
                events.push({
                    id: 'schedule-' + s.id + '-' + week,
                    title: '🏫 ' + s.subject,
                    start: eventDate.format('YYYY-MM-DD') + 'T' + s.startTime,
                    end: eventDate.format('YYYY-MM-DD') + 'T' + s.endTime,
                    backgroundColor: s.color || '#4a90d9',
                    borderColor: 'transparent',
                    type: 'class'
                });
            }
        });
        
        // Initialize calendar
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultView: 'agendaWeek',
            events: events,
            eventClick: function(calEvent) {
                let message = '';
                if (calEvent.type === 'task') message = 'This is a task deadline';
                else if (calEvent.type === 'study') message = 'This is an AI-generated study block';
                else message = 'This is a class schedule';
                
                alert(calEvent.title + '\n' + message);
            }
        });
    </script>
</body>
</html>

