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
            min-height: 100vh;
        }
        .main-content.expanded {
            margin-left: 0;
        }
        
        /* Sticky Header */
        .header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            background: #f5f7fa;
            padding: 15px 20px;
            position: sticky;
            top: 0;
            z-index: 100;
            border-radius: 10px;
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
            grid-template-columns: 1fr 350px;
            gap: 20px;
            height: calc(100vh - 100px);
            min-height: 500px;
        }
        .dashboard-left {
            display: flex;
            flex-direction: column;
            min-height: 0;
            overflow: hidden;
        }
        .dashboard-right {
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-height: 0;
            overflow: hidden;
        }
        
        .card {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        .card h2 {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 15px;
            padding-bottom: 10px;
            border-bottom: 2px solid #667eea;
            flex-shrink: 0;
        }
        
        /* Mini Calendar - properly contained */
        .mini-calendar-container {
            flex: 1;
            min-height: 0;
            overflow: hidden;
            position: relative;
        }
        
        #miniCalendar {
            height: 100% !important;
            min-height: 300px;
        }
        
        #miniCalendar .fc-toolbar {
            padding: 5px 0;
            margin-bottom: 10px !important;
            flex-wrap: wrap;
        }
        
        #miniCalendar .fc-toolbar h2 {
            font-size: 1rem;
        }
        
        #miniCalendar .fc-view-container {
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        #miniCalendar .fc-day-grid-container,
        #miniCalendar .fc-time-grid-container {
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        /* Deadline Cards - scrollable */
        .deadline-content {
            flex: 1;
            overflow-y: auto;
            min-height: 0;
            padding-right: 5px;
        }
        
        /* Custom scrollbar */
        .deadline-content::-webkit-scrollbar,
        #miniCalendar .fc-view-container::-webkit-scrollbar {
            width: 6px;
        }
        
        .deadline-content::-webkit-scrollbar-track,
        #miniCalendar .fc-view-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        .deadline-content::-webkit-scrollbar-thumb,
        #miniCalendar .fc-view-container::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
        
        .deadline-card {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 12px;
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
            font-size: 0.95rem;
        }
        .deadline-card p {
            color: #666;
            font-size: 0.85rem;
        }
        .priority-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 0.7rem;
            font-weight: bold;
            margin-left: 8px;
        }
        .priority-high { background: #dc3545; color: white; }
        .priority-medium { background: #ffc107; color: #333; }
        .priority-low { background: #28a745; color: white; }
        
        .no-deadlines {
            text-align: center;
            color: #999;
            padding: 20px;
            font-size: 0.9rem;
        }
        
        /* Sleep schedule indicator */
        .sleep-indicator {
            background: #e9ecef;
            color: #6c757d;
            padding: 8px 12px;
            border-radius: 5px;
            font-size: 0.8rem;
            margin-top: 10px;
            text-align: center;
            flex-shrink: 0;
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
                height: auto;
            }
            .dashboard-left, .dashboard-right {
                grid-column: 1;
            }
            .mini-calendar-container {
                min-height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Floating hamburger when sidebar collapsed -->
        <button class="floating-hamburger" id="floatingHamburger" onclick="toggleSidebar()">☰</button>
        
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
                <li><a href="document-analyzer.php"><span>📄</span> Add Task</a></li>
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
                        <div class="mini-calendar-container">
                            <div id="miniCalendar"></div>
                        </div>
                        <div class="sleep-indicator" id="sleepIndicator">
                            💤 Sleep: 10:00 PM - 8:00 AM
                        </div>
                    </div>
                </div>
                
                <!-- Right Side: Deadlines -->
                <div class="dashboard-right">
                    <div class="card">
                        <h2>📋 Due Today</h2>
                        <div class="deadline-content" id="dueToday">
                            <div class="no-deadlines">No tasks due today 🎉</div>
                        </div>
                    </div>
                    
                    <div class="card">
                        <h2>📋 Due Tomorrow</h2>
                        <div class="deadline-content" id="dueTomorrow">
                            <div class="no-deadlines">No tasks due tomorrow 🎉</div>
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
        
        // Load data
        const tasks = JSON.parse(localStorage.getItem('dailybrew_tasks_' + user.id) || '[]');
        const schedule = JSON.parse(localStorage.getItem('dailybrew_schedule_' + user.id) || '[]');
        const blocks = JSON.parse(localStorage.getItem('dailybrew_blocks_' + user.id) || '[]');
        
        // Load preferences with defaults
        let preferences = { earliest_time_start: '08:00', latest_time_end: '22:00', study_block_duration: 30, default_profile: 'seamless', sleep_schedule: { start: '22:00', end: '08:00' } };
        try {
            const prefsStr = localStorage.getItem('dailybrew_preferences_' + user.id);
            if (prefsStr) {
                preferences = { ...preferences, ...JSON.parse(prefsStr) };
            }
        } catch (e) {
            console.log('Using default preferences');
        }
        
        // Display sleep schedule
        const sleep = preferences.sleep_schedule || { start: '22:00', end: '08:00' };
        document.getElementById('sleepIndicator').textContent = 
            '💤 Sleep: ' + formatTime(sleep.start) + ' - ' + formatTime(sleep.end);
        
        function formatTime(time24) {
            const [hours, minutes] = time24.split(':');
            const h = parseInt(hours);
            const ampm = h >= 12 ? 'PM' : 'AM';
            const h12 = h % 12 || 12;
            return `${h12}:${minutes} ${ampm}`;
        }
        
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
        
        // Initialize mini calendar - DAY VIEW ONLY
        const events = [];
        
        // Add tasks as events
        tasks.forEach(task => {
            if (task.status !== 'completed') {
                events.push({
                    title: task.title,
                    start: task.dueDate,
                    backgroundColor: task.aiPriority === 'high' ? '#dc3545' : task.aiPriority === 'medium' ? '#ffc107' : '#28a745',
                    borderColor: 'transparent',
                    type: 'task'
                });
            }
        });
        
        // Add study blocks as events (only for uncompleted tasks)
        blocks.forEach(block => {
            const task = tasks.find(t => t.id === block.taskId);
            if (task && task.status !== 'completed') {
                events.push({
                    title: '📚 ' + block.title,
                    start: block.scheduledDate + 'T' + block.startTime,
                    end: block.scheduledDate + 'T' + block.endTime,
                    backgroundColor: '#667eea',
                    borderColor: '#764ba2',
                    type: 'study'
                });
            }
        });
        
        // Add schedule as events
        schedule.forEach(s => {
            const days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
            const dayIndex = days.indexOf(s.dayOfWeek);
            
            for (let i = 0; i < 8; i++) {
                const weekStart = moment().startOf('week');
                const eventDate = weekStart.add(dayIndex, 'days').add(i, 'weeks');
                events.push({
                    title: s.subject,
                    start: eventDate.format('YYYY-MM-DD') + 'T' + s.startTime,
                    end: eventDate.format('YYYY-MM-DD') + 'T' + s.endTime,
                    backgroundColor: s.color || '#4a90d9',
                    borderColor: 'transparent',
                    type: 'class'
                });
            }
        });
        
        // Add sleep schedule as background events
        const sleepStart = parseInt(sleep.start.split(':')[0]);
        const sleepEnd = parseInt(sleep.end.split(':')[0]);
        
        for (let i = 0; i < 14; i++) {
            const day = moment().startOf('week').add(i, 'days');
            events.push({
                title: '💤',
                start: day.format('YYYY-MM-DD') + 'T' + sleep.start,
                end: day.format('YYYY-MM-DD') + 'T' + sleep.end,
                backgroundColor: '#e9ecef',
                borderColor: '#dee2e6',
                textColor: '#6c757d',
                rendering: 'background',
                type: 'sleep'
            });
        }
        
        // Initialize calendar - DAY VIEW ONLY
        $('#miniCalendar').fullCalendar({
            header: {
                left: 'prev,next',
                center: 'title',
                right: '' 
            },
            defaultView: 'agendaDay',
            defaultDate: moment(),
            events: events,
            height: 'parent',
            minTime: '06:00:00',
            maxTime: '23:00:00',
            allDaySlot: false,
            slotDuration: '00:30:00',
            eventClick: function(calEvent) {
                let message = '';
                if (calEvent.type === 'task') message = 'This is a task deadline';
                else if (calEvent.type === 'study') message = 'This is an AI-generated study block';
                else if (calEvent.type === 'sleep') message = 'This is your sleep time';
                else message = 'This is a class schedule';
                
                alert(calEvent.title + '\n' + message);
            }
        });
        
        // Auto-refresh every minute
        setInterval(function() {
            $('#miniCalendar').fullCalendar('refetchEvents');
        }, 60000);
    </script>
</body>
</html>

