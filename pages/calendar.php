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
            margin-bottom: 20px;
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
        
        .card { 
            background: white; 
            border-radius: 15px; 
            padding: 20px; 
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            min-height: calc(100vh - 120px);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        #calendar { 
            flex: 1;
            min-height: 0;
        }
        
        /* Calendar container overflow handling */
        #calendar .fc-toolbar {
            padding: 10px 0;
            margin-bottom: 15px !important;
            flex-wrap: wrap;
            gap: 10px;
        }
        
        #calendar .fc-view-container {
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        #calendar .fc-day-grid-container,
        #calendar .fc-time-grid-container {
            overflow-y: auto;
            overflow-x: hidden;
        }
        
        /* Custom scrollbar */
        #calendar .fc-view-container::-webkit-scrollbar,
        #calendar .fc-day-grid-container::-webkit-scrollbar,
        #calendar .fc-time-grid-container::-webkit-scrollbar {
            width: 6px;
        }
        
        #calendar .fc-view-container::-webkit-scrollbar-track,
        #calendar .fc-day-grid-container::-webkit-scrollbar-track,
        #calendar .fc-time-grid-container::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        #calendar .fc-view-container::-webkit-scrollbar-thumb,
        #calendar .fc-day-grid-container::-webkit-scrollbar-thumb,
        #calendar .fc-time-grid-container::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 3px;
        }
        
        .fc-event { cursor: pointer; }
        .fc-toolbar h2 { font-size: 1.5rem; }
        
        /* Long press indicator */
        .long-press-hint {
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            background: rgba(0,0,0,0.7);
            color: white;
            padding: 10px 20px;
            border-radius: 20px;
            font-size: 0.9rem;
            z-index: 1000;
            opacity: 0;
            transition: opacity 0.3s;
        }
        .long-press-hint.visible { opacity: 1; }
        
        /* Delete confirmation modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 2000;
            align-items: center;
            justify-content: center;
        }
        .modal-overlay.visible { display: flex; }
        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            max-width: 400px;
        }
        .modal-content h3 { margin-bottom: 15px; color: #333; }
        .modal-content p { margin-bottom: 20px; color: #666; }
        .modal-buttons { display: flex; gap: 10px; justify-content: center; }
        .modal-buttons button {
            padding: 10px 25px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 1rem;
        }
        .btn-cancel { background: #e0e0e0; color: #333; }
        .btn-delete { background: #dc3545; color: white; }
        
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
                <li><a href="calendar.php" class="active"><span>📅</span> Calendar</a></li>
                <li><a href="tasks.php"><span>📝</span> Tasks</a></li>
                <li><a href="document-analyzer.php"><span>📄</span> Add Task</a></li>
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

    <!-- Long press hint -->
    <div class="long-press-hint" id="longPressHint">Long press on a study block to delete it</div>
    
    <!-- Delete confirmation modal -->
    <div class="modal-overlay" id="deleteModal">
        <div class="modal-content">
            <h3>🗑️ Delete Study Block?</h3>
            <p id="deleteMessage">Are you sure you want to delete this study block?</p>
            <div class="modal-buttons">
                <button class="btn-cancel" onclick="closeDeleteModal()">Cancel</button>
                <button class="btn-delete" onclick="confirmDelete()">Delete</button>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment@2.29.4/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@3.10.2/dist/fullcalendar.min.js"></script>
    <script>
        const currentUser = localStorage.getItem('dailybrew_current_user');
        if (!currentUser) { window.location.href = '../auth/login.php'; }
        
        const user = JSON.parse(currentUser);
        document.getElementById('userAvatar').textContent = user.firstName.charAt(0);
        
        let pendingDeleteBlock = null;
        
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
        
        // Load all data
        const tasks = JSON.parse(localStorage.getItem('dailybrew_tasks_' + user.id) || '[]');
        const schedule = JSON.parse(localStorage.getItem('dailybrew_schedule_' + user.id) || '[]');
        let blocks = JSON.parse(localStorage.getItem('dailybrew_blocks_' + user.id) || '[]');
        
        // Load preferences
        let preferences = { sleep_schedule: { start: '22:00', end: '08:00' } };
        try {
            const prefsStr = localStorage.getItem('dailybrew_preferences_' + user.id);
            if (prefsStr) {
                preferences = { ...preferences, ...JSON.parse(prefsStr) };
            }
        } catch (e) {
            console.log('Using default preferences');
        }
        
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
        
        // Study blocks (only for uncompleted tasks)
        blocks.forEach(block => {
            const task = tasks.find(t => t.id === block.taskId);
            if (!task || task.status === 'completed') return;
            
            events.push({
                id: 'block-' + block.id,
                title: '📚 ' + block.title,
                start: block.scheduledDate + 'T' + block.startTime,
                end: block.scheduledDate + 'T' + block.endTime,
                backgroundColor: '#667eea',
                borderColor: '#764ba2',
                type: 'study',
                blockId: block.id
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
        
        // Add sleep schedule as background events (greyed out)
        const sleep = preferences.sleep_schedule || { start: '22:00', end: '08:00' };
        const sleepStart = parseInt(sleep.start.split(':')[0]);
        const sleepEnd = parseInt(sleep.end.split(':')[0]);
        
        for (let i = 0; i < 14; i++) {
            const day = moment().startOf('week').add(i, 'days');
            events.push({
                title: '💤 Sleep',
                start: day.format('YYYY-MM-DD') + 'T' + sleep.start,
                end: day.format('YYYY-MM-DD') + 'T' + sleep.end,
                backgroundColor: '#e9ecef',
                borderColor: '#dee2e6',
                textColor: '#6c757d',
                rendering: 'background',
                type: 'sleep'
            });
        }
        
        // Initialize calendar - REMOVED "today" button
        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultView: 'agendaWeek',
            events: events,
            minTime: '06:00:00',
            maxTime: '23:00:00',
            allDaySlot: true,
            slotDuration: '00:30:00',
            height: 'parent',
            eventClick: function(calEvent) {
                let message = '';
                if (calEvent.type === 'task') message = 'This is a task deadline';
                else if (calEvent.type === 'study') {
                    message = 'This is an AI-generated study block\n\nLong press to delete!';
                } else if (calEvent.type === 'sleep') {
                    message = 'This is your sleep time (no study blocks scheduled here)';
                } else message = 'This is a class schedule';
                
                alert(calEvent.title + '\n' + message);
            },
            eventRender: function(event, element) {
                if (event.type === 'study') {
                    let pressTimer;
                    const pressDuration = 1000;
                    
                    element.on('mousedown touchstart', function(e) {
                        pressTimer = setTimeout(function() {
                            showDeleteModal(event.blockId, event.title);
                        }, pressDuration);
                    });
                    
                    element.on('mouseup touchend', function() {
                        clearTimeout(pressTimer);
                    });
                    
                    element.on('mouseleave', function() {
                        clearTimeout(pressTimer);
                    });
                }
            }
        });
        
        // Show long press hint on mobile
        if ('ontouchstart' in window) {
            const hint = document.getElementById('longPressHint');
            hint.classList.add('visible');
            setTimeout(() => {
                hint.classList.remove('visible');
            }, 5000);
        }
        
        function showDeleteModal(blockId, title) {
            pendingDeleteBlock = blockId;
            document.getElementById('deleteMessage').textContent = 
                'Are you sure you want to delete this study block?\n\n"' + title + '"';
            document.getElementById('deleteModal').classList.add('visible');
        }
        
        function closeDeleteModal() {
            pendingDeleteBlock = null;
            document.getElementById('deleteModal').classList.remove('visible');
        }
        
        function confirmDelete() {
            if (pendingDeleteBlock) {
                blocks = blocks.filter(b => b.id !== pendingDeleteBlock);
                localStorage.setItem('dailybrew_blocks_' + user.id, JSON.stringify(blocks));
                $('#calendar').fullCalendar('refetchEvents');
                closeDeleteModal();
                alert('Study block deleted successfully!');
            }
        }
        
        document.getElementById('deleteModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeDeleteModal();
            }
        });
    </script>
</body>
</html>

