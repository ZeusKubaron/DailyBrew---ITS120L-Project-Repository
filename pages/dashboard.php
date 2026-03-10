<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - DailyBrew</title>
    <link rel="stylesheet" href="../css/style.css">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f5f7fa;
            min-height: 100vh;
        }
        .app-container {
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar */
        .sidebar {
            width: 260px;
            background: linear-gradient(180deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            display: flex;
            flex-direction: column;
            position: fixed;
            height: 100vh;
            transition: transform 0.3s;
            z-index: 1000;
        }
        .sidebar.collapsed {
            transform: translateX(-260px);
        }
        .sidebar-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding-bottom: 20px;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            margin-bottom: 20px;
        }
        .logo {
            font-size: 1.8rem;
            font-weight: bold;
        }
        .hamburger {
            background: none;
            border: none;
            color: white;
            font-size: 1.5rem;
            cursor: pointer;
            padding: 5px;
        }
        .nav-menu {
            list-style: none;
            flex: 1;
        }
        .nav-menu li {
            margin-bottom: 5px;
        }
        .nav-menu a {
            display: flex;
            align-items: center;
            padding: 12px 15px;
            color: white;
            text-decoration: none;
            border-radius: 10px;
            transition: background 0.2s;
        }
        .nav-menu a:hover, .nav-menu a.active {
            background: rgba(255,255,255,0.2);
        }
        .nav-menu a span {
            margin-right: 10px;
        }
        
        /* Main Content */
        .main-content {
            flex: 1;
            margin-left: 260px;
            padding: 20px;
            transition: margin-left 0.3s;
        }
        .main-content.expanded {
            margin-left: 0;
        }
        
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        .header h1 {
            color: #333;
            font-size: 1.8rem;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: #667eea;
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
        }
        .logout-btn {
            padding: 8px 20px;
            background: #ff4757;
            color: white;
            border: none;
            border-radius: 20px;
            cursor: pointer;
        }
        
        /* Dashboard Layout */
        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .dashboard-left {
            grid-column: 1;
        }
        .dashboard-right {
            grid-column: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .card h2 {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
        }
        
        /* Mini Calendar */
        .mini-calendar {
            height: 350px;
        }
        
        /* Deadline Cards */
        .deadline-card {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 10px;
        }
        .deadline-card.overdue {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        .deadline-card h4 {
            color: #333;
            margin-bottom: 5px;
        }
        .deadline-card p {
            color: #666;
            font-size: 0.9rem;
        }
        .priority-badge {
            display: inline-block;
            padding: 3px 10px;
            border-radius: 12px;
            font-size: 0.75rem;
            font-weight: bold;
            margin-left: 10px;
        }
        .priority-high { background: #dc3545; color: white; }
        .priority-medium { background: #ffc107; color: #333; }
        .priority-low { background: #28a745; color: white; }
        
        .no-deadlines {
            text-align: center;
            color: #999;
            padding: 20px;
        }
        
        /* Mobile */
        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(-260px);
            }
            .sidebar.active {
                transform: translateX(0);
            }
            .main-content {
                margin-left: 0;
            }
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
            .dashboard-left, .dashboard-right {
                grid-column: 1;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <span class="logo">☕ DailyBrew</span>
                <button class="hamburger" onclick="toggleSidebar()">☰</button>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php" class="active"><span>🏠</span> Dashboard</a></li>
                <li><a href="calendar.php"><span>📅</span> Calendar</a></li>
                <li><a href="tasks.php"><span>📝</span> Tasks</a></li>
                <li><a href="document-analyzer.php"><span>📄</span> Document Analyzer</a></li>
                <li><a href="schedule.php"><span>📚</span> Schedule</a></li>
                <li><a href="settings.php"><span>⚙️</span> Settings</a></li>
            </ul>
        </nav>
        
        <!-- Main Content -->
        <main class="main-content" id="mainContent">
            <div class="header">
                <h1>Welcome back, <span id="userName">User</span>!</h1>
                <div class="user-info">
                    <div class="user-avatar" id="userAvatar">U</div>
                    <button class="logout-btn" onclick="logout()">Logout</button>
                </div>
            </div>
            
            <div class="dashboard-grid">
                <!-- Left Side: Mini Calendar -->
                <div class="dashboard-left">
                    <div class="card">
                        <h2>📅 Today's Schedule</h2>
                        <div id="miniCalendar" class="mini-calendar"></div>
                    </div>
                </div>
                
                <!-- Right Side: Deadlines -->
                <div class="dashboard-right">
                    <div class="card">
                        <h2>📋 Due Today</h2>
                        <div id="dueToday">
                            <div class="no-deadlines">No tasks due today</div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <h2>📋 Due Tomorrow</h2>
                        <div id="dueTomorrow">
                            <div class="no-deadlines">No tasks due tomorrow</div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.js"></script>
    <script>
        // Check login
        const currentUser = localStorage.getItem('dailybrew_current_user');
        if (!currentUser) {
            window.location.href = '../auth/login.php';
        }
        
        const user = JSON.parse(currentUser);
        document.getElementById('userName').textContent = user.firstName;
        document.getElementById('userAvatar').textContent = user.firstName.charAt(0);
        
        function toggleSidebar() {
            document.getElementById('sidebar').classList.toggle('collapsed');
            document.getElementById('mainContent').classList.toggle('expanded');
        }
        
        function logout() {
            localStorage.removeItem('dailybrew_current_user');
            window.location.href = '../auth/login.php';
        }
        
        // Load tasks
        const tasks = JSON.parse(localStorage.getItem('dailybrew_tasks_' + user.id) || '[]');
        const schedule = JSON.parse(localStorage.getItem('dailybrew_schedule_' + user.id) || '[]');
        const blocks = JSON.parse(localStorage.getItem('dailybrew_blocks_' + user.id) || '[]');
        
        // Today's date
        const today = moment().format('YYYY-MM-DD');
        const tomorrow = moment().add(1, 'days').format('YYYY-MM-DD');
        
        // Filter tasks due today and tomorrow
        const dueTodayTasks = tasks.filter(t => t.dueDate === today && t.status !== 'completed');
        const dueTomorrowTasks = tasks.filter(t => t.dueDate === tomorrow && t.status !== 'completed');
        
        // Render due today
        const dueTodayEl = document.getElementById('dueToday');
        if (dueTodayTasks.length === 0) {
            dueTodayEl.innerHTML = '<div class="no-deadlines">No tasks due today 🎉</div>';
        } else {
            dueTodayEl.innerHTML = dueTodayTasks.map(task => `
                <div class="deadline-card ${task.aiPriority === 'high' ? 'overdue' : ''}">
                    <h4>${task.title} <span class="priority-badge priority-${task.aiPriority}">${task.aiPriority.toUpperCase()}</span></h4>
                    <p>Due: ${moment(task.dueDate).format('MMM D, YYYY')}</p>
                </div>
            `).join('');
        }
        
        // Render due tomorrow
        const dueTomorrowEl = document.getElementById('dueTomorrow');
        if (dueTomorrowTasks.length === 0) {
            dueTomorrowEl.innerHTML = '<div class="no-deadlines">No tasks due tomorrow 🎉</div>';
        } else {
            dueTomorrowEl.innerHTML = dueTomorrowTasks.map(task => `
                <div class="deadline-card">
                    <h4>${task.title} <span class="priority-badge priority-${task.aiPriority}">${task.aiPriority.toUpperCase()}</span></h4>
                    <p>Due: ${moment(task.dueDate).format('MMM D, YYYY')}</p>
                </div>
            `).join('');
        }
        
        // Initialize mini calendar
        const events = [];
        
        // Add tasks as events
        tasks.forEach(task => {
            if (task.status !== 'completed') {
                events.push({
                    title: task.title,
                    start: task.dueDate,
                    backgroundColor: task.aiPriority === 'high' ? '#dc3545' : task.aiPriority === 'medium' ? '#ffc107' : '#28a745',
                    borderColor: 'transparent'
                });
            }
        });
        
        // Add study blocks as events
        blocks.forEach(block => {
            events.push({
                title: block.title,
                start: block.scheduledDate + 'T' + block.startTime,
                end: block.scheduledDate + 'T' + block.endTime,
                backgroundColor: '#667eea',
                borderColor: '#764ba2'
            });
        });
        
        // Add schedule as events
        schedule.forEach(s => {
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const dayIndex = days.indexOf(s.dayOfWeek);
            
            // Recurring weekly
            for (let i = 0; i < 4; i++) {
                const weekStart = moment().startOf('week');
                const eventDate = weekStart.add(dayIndex, 'days').add(i, 'weeks');
                events.push({
                    title: s.subject,
                    start: eventDate.format('YYYY-MM-DD') + 'T' + s.startTime,
                    end: eventDate.format('YYYY-MM-DD') + 'T' + s.endTime,
                    backgroundColor: s.color || '#4a90d9',
                    borderColor: 'transparent'
                });
            }
        });
        
        $('#miniCalendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek'
            },
            defaultView: 'agendaWeek',
            events: events,
            height: 'auto',
            eventClick: function(calEvent) {
                alert('Event: ' + calEvent.title);
            }
        });
    </script>
</body>
</html>

