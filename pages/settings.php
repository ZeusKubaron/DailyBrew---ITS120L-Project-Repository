<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings - DailyBrew</title>
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
        
        .form-group { margin-bottom: 20px; }
        .form-group label { display: block; margin-bottom: 8px; color: #333; font-weight: 500; }
        .form-group input, .form-group select { width: 100%; padding: 12px; border: 2px solid #e0e0e0; border-radius: 8px; font-size: 1rem; }
        .form-group input:focus, .form-group select:focus { outline: none; border-color: #667eea; }
        
        .form-row { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
        
        .btn { padding: 12px 30px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border: none; border-radius: 8px; font-size: 1rem; cursor: pointer; }
        .btn:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); }
        
        .profile-option { 
            padding: 15px; 
            border: 2px solid #e0e0e0; 
            border-radius: 10px; 
            margin-bottom: 10px; 
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .profile-option:hover { border-color: #667eea; }
        .profile-option.selected { border-color: #667eea; background: #f8f9ff; }
        .profile-option h4 { color: #333; margin-bottom: 5px; }
        .profile-option p { color: #666; font-size: 0.9rem; }
        
        .info-box { background: #e8edff; border-radius: 10px; padding: 15px; margin-top: 20px; }
        .info-box p { color: #333; font-size: 0.9rem; }
        
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
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <span class="logo">☕ DailyBrew</span>
                <button class="hamburger" onclick="toggleSidebar()">☰</button>
            </div>
            <ul class="nav-menu">
                <li><a href="dashboard.php"><span>🏠</span> Dashboard</a></li>
                <li><a href="calendar.php"><span>📅</span> Calendar</a></li>
                <li><a href="tasks.php"><span>📝</span> Tasks</a></li>
                <li><a href="document-analyzer.php"><span>📄</span> Document Analyzer</a></li>
                <li><a href="schedule.php"><span>📚</span> Schedule</a></li>
                <li><a href="settings.php" class="active"><span>⚙️</span> Settings</a></li>
            </ul>
        </nav>
        
        <main class="main-content" id="mainContent">
            <div class="header">
                <h1>⚙️ Settings</h1>
                <div class="user-info">
                    <div class="user-avatar" id="userAvatar">U</div>
                    <button class="logout-btn" onclick="logout()">Logout</button>
                </div>
            </div>
            
            <div class="card">
                <h2>⏰ Time Preferences</h2>
                <div class="form-row">
                    <div class="form-group">
                        <label>Earliest Study Time</label>
                        <input type="time" id="earliestTime" value="08:00">
                    </div>
                    <div class="form-group">
                        <label>Latest Study Time</label>
                        <input type="time" id="latestTime" value="22:00">
                    </div>
                </div>
            </div>
            
            <div class="card">
                <h2>📊 Study Block Settings</h2>
                <div class="form-group">
                    <label>Default Study Block Duration</label>
                    <select id="blockDuration">
                        <option value="15">15 minutes</option>
                        <option value="30" selected>30 minutes</option>
                        <option value="45">45 minutes</option>
                        <option value="60">1 hour</option>
                        <option value="90">1.5 hours</option>
                        <option value="120">2 hours</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Default Study Profile</label>
                    <div class="profile-option selected" data-value="seamless" onclick="selectProfile(this)">
                        <h4>🌊 Seamless</h4>
                        <p>Distribute study blocks evenly with breaks in between</p>
                    </div>
                    <div class="profile-option" data-value="early_crammer" onclick="selectProfile(this)">
                        <h4>🌅 Early Crammer</h4>
                        <p>Schedule study blocks as early as possible</p>
                    </div>
                    <div class="profile-option" data-value="late_crammer" onclick="selectProfile(this)">
                        <h4>🌙 Late Crammer</h4>
                        <p>Schedule study blocks close to the deadline</p>
                    </div>
                </div>
            </div>
            
            <button class="btn" onclick="saveSettings()">💾 Save Settings</button>
            
            <div class="info-box">
                <p><strong>💡 Tip:</strong> These settings will be used when the AI creates study blocks for your tasks. You can also set individual profiles for each task.</p>
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
        
        // Load preferences
        let preferences = JSON.parse(localStorage.getItem('dailybrew_preferences_' + user.id) || '{"earliest_time_start": "08:00", "latest_time_end": "22:00", "study_block_duration": 30, "default_profile": "seamless"}');
        
        document.getElementById('earliestTime').value = preferences.earliest_time_start;
        document.getElementById('latestTime').value = preferences.latest_time_end;
        document.getElementById('blockDuration').value = preferences.study_block_duration;
        
        // Set selected profile
        document.querySelectorAll('.profile-option').forEach(opt => {
            opt.classList.remove('selected');
            if (opt.dataset.value === preferences.default_profile) {
                opt.classList.add('selected');
            }
        });
        
        function selectProfile(element) {
            document.querySelectorAll('.profile-option').forEach(opt => opt.classList.remove('selected'));
            element.classList.add('selected');
        }
        
        function saveSettings() {
            const selectedProfile = document.querySelector('.profile-option.selected');
            
            preferences = {
                earliest_time_start: document.getElementById('earliestTime').value,
                latest_time_end: document.getElementById('latestTime').value,
                study_block_duration: parseInt(document.getElementById('blockDuration').value),
                default_profile: selectedProfile ? selectedProfile.dataset.value : 'seamless'
            };
            
            localStorage.setItem('dailybrew_preferences_' + user.id, JSON.stringify(preferences));
            alert('Settings saved successfully! 🎉');
        }
    </script>
</body>
</html>

