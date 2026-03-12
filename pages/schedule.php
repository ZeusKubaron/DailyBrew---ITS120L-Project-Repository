<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Schedule - DailyBrew</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
    /* ── Morning Coffee Theme ── */
    @import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap');

    :root {
        --cream:        #F5F0E8;
        --parchment:    #EDE5D8;
        --parchment2:   #E4D9C8;
        --taupe-light:  #A49A98;
        --taupe:        #9D8A7C;
        --brown-mid:    #796254;
        --espresso:     #523F31;
        --espresso-dk:  #3D2E22;
        --text-dark:    #2E1F14;
        --text-mid:     #5C4A3A;
        --text-soft:    #8C7B6E;
        --white:        #FDFAF6;
        --border:       rgba(164,154,152,0.22);
        --success:      #4A7C59;
        --warning:      #B89240;
        --danger:       #8B3A3A;
        --sidebar-w:    260px;
    }

    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'DM Sans', 'Segoe UI', sans-serif;
        background: var(--cream);
        color: var(--text-dark);
        min-height: 100vh;
    }

    /* subtle paper grain */
    body::before {
        content: '';
        position: fixed; inset: 0; z-index: 0;
        background-image: url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
        pointer-events: none; opacity: 0.45;
    }

    .app-container { display: flex; min-height: 100vh; position: relative; z-index: 1; }

    /* ── SIDEBAR ── */
    .sidebar {
        width: var(--sidebar-w);
        background: var(--espresso);
        color: var(--parchment);
        padding: 0;
        display: flex;
        flex-direction: column;
        position: fixed;
        height: 100vh;
        transition: transform 0.3s cubic-bezier(0.4,0,0.2,1);
        z-index: 1000;
        box-shadow: 4px 0 24px rgba(82,63,49,0.2);
    }
    .sidebar.collapsed { transform: translateX(calc(-1 * var(--sidebar-w))); }

    .sidebar-header {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 22px 20px 18px;
        border-bottom: 1px solid rgba(255,255,255,0.08);
    }
    .logo {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.4rem;
        font-weight: 600;
        color: var(--parchment);
        letter-spacing: 0.01em;
        flex: 1;
    }
    .hamburger {
        background: none; border: none;
        color: var(--parchment); font-size: 1.2rem;
        cursor: pointer; padding: 6px; border-radius: 6px;
        opacity: 0.8; transition: opacity 0.2s, background 0.2s;
        flex-shrink: 0;
    }
    .hamburger:hover { opacity: 1; background: rgba(255,255,255,0.1); }

    /* floating hamburger */
    .floating-hamburger {
        display: none;
        position: fixed; top: 16px; left: 16px; z-index: 1001;
        background: var(--espresso);
        color: var(--parchment);
        border: none; border-radius: 8px;
        padding: 9px 14px; font-size: 1.1rem;
        cursor: pointer;
        box-shadow: 0 4px 14px rgba(82,63,49,0.35);
        transition: background 0.2s;
    }
    .floating-hamburger:hover { background: var(--espresso-dk); }
    .floating-hamburger.visible { display: block; }

    .nav-menu { list-style: none; flex: 1; padding: 14px 0; overflow-y: auto; }
    .nav-menu li { margin-bottom: 2px; }
    .nav-menu a {
        display: flex; align-items: center;
        padding: 10px 20px;
        color: rgba(237,229,216,0.68);
        text-decoration: none; border-radius: 0;
        border-left: 3px solid transparent;
        font-size: 0.88rem; font-weight: 400;
        transition: all 0.18s ease;
    }
    .nav-menu a:hover { color: var(--parchment); background: rgba(255,255,255,0.07); }
    .nav-menu a.active {
        color: var(--parchment);
        background: rgba(255,255,255,0.1);
        border-left-color: var(--taupe-light);
    }
    .nav-menu a span { margin-right: 10px; font-size: 0.95rem; }

    /* user bottom */
    .sidebar-footer {
        padding: 14px 20px;
        border-top: 1px solid rgba(255,255,255,0.08);
    }

    /* ── MAIN CONTENT ── */
    .main-content {
        flex: 1;
        margin-left: var(--sidebar-w);
        padding: 20px;
        transition: margin-left 0.3s cubic-bezier(0.4,0,0.2,1);
        min-height: 100vh;
    }
    .main-content.expanded { margin-left: 0; }

    /* ── HEADER ── */
    .header {
        display: flex; justify-content: space-between; align-items: center;
        margin-bottom: 24px;
        background: var(--cream);
        padding: 14px 18px;
        position: sticky; top: 0; z-index: 100;
        border-radius: 10px;
        border-bottom: 1px solid var(--border);
    }
    .header h1 {
        font-family: 'Cormorant Garamond', serif;
        font-size: 1.7rem; font-weight: 400;
        color: var(--text-dark);
    }
    .user-info { display: flex; align-items: center; gap: 12px; }
    .user-avatar {
        width: 36px; height: 36px; border-radius: 50%;
        background: var(--taupe); color: var(--parchment);
        display: flex; align-items: center; justify-content: center;
        font-weight: 600; font-size: 0.85rem;
    }
    .logout-btn {
        padding: 7px 18px;
        background: var(--espresso); color: var(--parchment);
        border: none; border-radius: 18px; cursor: pointer;
        font-size: 0.82rem; font-family: 'DM Sans', sans-serif;
        transition: background 0.2s;
    }
    .logout-btn:hover { background: var(--espresso-dk); }

    /* ── CARDS ── */
    .card {
        background: var(--white);
        border-radius: 12px;
        padding: 22px;
        box-shadow: 0 2px 12px rgba(82,63,49,0.08);
        margin-bottom: 20px;
        border: 1px solid var(--border);
    }
    .card h2 {
        font-family: 'Cormorant Garamond', serif;
        color: var(--text-dark); font-size: 1.15rem; font-weight: 600;
        margin-bottom: 16px; padding-bottom: 10px;
        border-bottom: 2px solid var(--brown-mid);
    }

    /* ── FORMS ── */
    .form-group { margin-bottom: 15px; }
    .form-group label {
        display: block; margin-bottom: 6px;
        font-size: 0.78rem; font-weight: 500;
        color: var(--text-mid); letter-spacing: 0.02em;
    }
    .form-group input,
    .form-group select,
    .form-group textarea {
        width: 100%; padding: 10px 12px;
        border: 1.5px solid var(--parchment2);
        border-radius: 7px; font-size: 0.88rem;
        background: var(--white); color: var(--text-dark);
        font-family: 'DM Sans', sans-serif;
        transition: border-color 0.2s, box-shadow 0.2s;
        outline: none;
    }
    .form-group input::placeholder,
    .form-group textarea::placeholder { color: #B1B1B1; opacity: 0.7; }
    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
        border-color: var(--taupe);
        box-shadow: 0 0 0 3px rgba(157,138,124,0.12);
    }
    .form-group textarea { resize: vertical; min-height: 90px; }
    .form-group select {
        appearance: none; cursor: pointer;
        background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%238C7B6E' stroke-width='1.5' stroke-linecap='round' fill='none'/%3E%3C/svg%3E");
        background-repeat: no-repeat; background-position: right 12px center;
    }

    /* ── BUTTONS ── */
    .btn {
        padding: 10px 24px;
        background: var(--espresso); color: var(--parchment);
        border: none; border-radius: 8px;
        font-size: 0.88rem; font-weight: 500;
        cursor: pointer; font-family: 'DM Sans', sans-serif;
        transition: all 0.2s ease;
        display: inline-flex; align-items: center; gap: 6px;
    }
    .btn:hover {
        background: var(--espresso-dk);
        transform: translateY(-1px);
        box-shadow: 0 4px 14px rgba(82,63,49,0.28);
    }
    .btn-sm { padding: 6px 14px; font-size: 0.78rem; border-radius: 14px; }
    .btn-complete { background: var(--success); }
    .btn-complete:hover { background: #3D6849; transform: scale(1.05); }
    .btn-delete { background: var(--danger); }
    .btn-delete:hover { background: #7A2F2F; transform: scale(1.05); }

    /* ── PRIORITY BADGES ── */
    .priority-badge {
        display: inline-block; padding: 2px 9px;
        border-radius: 10px; font-size: 0.68rem; font-weight: 700;
        letter-spacing: 0.06em; text-transform: uppercase;
        margin-left: 6px;
    }
    .priority-high  { background: var(--espresso); color: var(--parchment); }
    .priority-medium{ background: var(--brown-mid); color: var(--parchment); }
    .priority-low   { background: var(--taupe-light); color: var(--white); }

    /* ── TASK/SCHEDULE ITEMS ── */
    .task-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 13px 14px;
        border: 1px solid var(--border); border-radius: 9px;
        margin-bottom: 10px;
        border-left: 3px solid var(--taupe);
        background: var(--cream);
    }
    .task-item.completed { opacity: 0.55; text-decoration: line-through; }
    .task-info h4 { color: var(--text-dark); margin-bottom: 4px; font-size: 0.9rem; font-weight: 500; }
    .task-info p { color: var(--text-soft); font-size: 0.78rem; }
    .task-actions { display: flex; gap: 8px; }

    .schedule-item {
        display: flex; align-items: center; justify-content: space-between;
        padding: 13px 14px;
        border: 1px solid var(--border); border-radius: 9px;
        margin-bottom: 10px;
        border-left: 4px solid var(--brown-mid);
        background: var(--cream);
    }
    .schedule-item h4 { color: var(--text-dark); margin-bottom: 4px; font-size: 0.9rem; font-weight: 500; }
    .schedule-item p { color: var(--text-soft); font-size: 0.78rem; }
    .schedule-actions { display: flex; gap: 8px; }

    /* ── DEADLINE CARDS ── */
    .deadline-card {
        background: rgba(237,229,216,0.5);
        border-left: 4px solid var(--taupe);
        padding: 11px 13px; border-radius: 8px; margin-bottom: 10px;
    }
    .deadline-card.overdue {
        background: rgba(139,58,58,0.08);
        border-left-color: var(--danger);
    }
    .deadline-card h4 { color: var(--text-dark); margin-bottom: 4px; font-size: 0.88rem; font-weight: 500; }
    .deadline-card p { color: var(--text-soft); font-size: 0.78rem; }
    .no-deadlines { text-align: center; color: var(--text-soft); padding: 18px; font-size: 0.85rem; }

    /* sleep indicator */
    .sleep-indicator {
        background: var(--parchment);
        color: var(--text-soft);
        padding: 7px 12px;
        border-radius: 6px; font-size: 0.78rem;
        margin-top: 10px; text-align: center;
        flex-shrink: 0; border: 1px solid var(--border);
    }

    /* ── CALENDAR / FULLCALENDAR ── */
    .fc-button, .fc-button-primary {
        background: var(--espresso) !important;
        border-color: var(--espresso) !important;
        color: var(--parchment) !important;
        font-family: 'DM Sans', sans-serif !important;
        font-size: 0.8rem !important;
        border-radius: 6px !important;
        box-shadow: none !important;
    }
    .fc-button:hover, .fc-button-primary:hover {
        background: var(--espresso-dk) !important;
        border-color: var(--espresso-dk) !important;
    }
    .fc-button-active,
    .fc-button-primary:not(:disabled).fc-button-active {
        background: var(--brown-mid) !important;
        border-color: var(--brown-mid) !important;
    }
    .fc-toolbar h2 {
        font-family: 'Cormorant Garamond', serif !important;
        font-size: 1.3rem !important; font-weight: 400 !important;
        color: var(--text-dark) !important;
    }
    .fc-today { background: rgba(164,154,152,0.08) !important; }
    .fc-day-grid-container::-webkit-scrollbar,
    .fc-time-grid-container::-webkit-scrollbar,
    .fc-view-container::-webkit-scrollbar { width: 4px; }
    .fc-day-grid-container::-webkit-scrollbar-thumb,
    .fc-time-grid-container::-webkit-scrollbar-thumb,
    .fc-view-container::-webkit-scrollbar-thumb { background: var(--taupe-light); border-radius: 2px; }

    /* ── UPLOAD AREA ── */
    .upload-area {
        border: 2px dashed var(--parchment2);
        border-radius: 12px; padding: 36px 22px;
        text-align: center; cursor: pointer;
        transition: border-color 0.2s, background 0.2s;
        background: var(--cream);
    }
    .upload-area:hover { border-color: var(--taupe); background: rgba(164,154,152,0.06); }
    .upload-area.dragover { border-color: var(--brown-mid); background: rgba(121,98,84,0.08); }
    .upload-icon { font-size: 2.5rem; margin-bottom: 10px; opacity: 0.6; }
    .upload-text { color: var(--text-mid); margin-bottom: 6px; font-weight: 500; font-size: 0.88rem; }
    .upload-hint { color: var(--text-soft); font-size: 0.78rem; }

    /* ── ANALYSIS RESULT ── */
    .analysis-result { background: var(--parchment); border-radius: 9px; padding: 18px; margin-top: 16px; }
    .analysis-result h3 { color: var(--espresso); margin-bottom: 12px; font-family: 'Cormorant Garamond', serif; font-size: 1.1rem; }
    .analysis-item { display: flex; justify-content: space-between; padding: 9px 0; border-bottom: 1px solid var(--border); }
    .analysis-item:last-child { border-bottom: none; }
    .analysis-label { color: var(--text-soft); font-size: 0.82rem; }
    .analysis-value { font-weight: 600; color: var(--text-dark); font-size: 0.82rem; }

    /* ── PROFILE INFO / HINT ── */
    .profile-info {
        background: var(--parchment);
        padding: 9px 12px; border-radius: 7px; margin-top: 8px;
        font-size: 0.78rem; color: var(--text-mid);
        border: 1px solid var(--border); line-height: 1.5;
    }

    /* ── LOADING ── */
    .loading { text-align: center; padding: 20px; color: var(--text-soft); }
    .loading-spinner {
        border: 3px solid var(--parchment);
        border-top: 3px solid var(--brown-mid);
        border-radius: 50%; width: 30px; height: 30px;
        animation: spin 0.9s linear infinite;
        margin: 0 auto 10px;
    }
    @keyframes spin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }

    /* ── SETTINGS ── */
    .profile-option {
        padding: 13px 14px; border: 1.5px solid var(--border);
        border-radius: 9px; margin-bottom: 10px; cursor: pointer;
        transition: all 0.15s;
    }
    .profile-option:hover { border-color: var(--taupe); background: var(--parchment); }
    .profile-option.selected { border-color: var(--brown-mid); background: rgba(121,98,84,0.08); }
    .profile-option h4 { color: var(--text-dark); margin-bottom: 4px; font-size: 0.88rem; font-weight: 500; }
    .profile-option p { color: var(--text-soft); font-size: 0.78rem; }

    .sleep-visual {
        background: var(--parchment); height: 30px;
        border-radius: 15px; margin-top: 10px;
        position: relative; border: 1px solid var(--border);
        overflow: hidden; display: flex; align-items: stretch;
    }
    .sleep-label {
        position: absolute; top: 50%;
        transform: translateY(-50%);
        font-size: 0.72rem; color: var(--text-soft);
    }
    .sleep-label.wake { left: 10px; }
    .sleep-label.sleep { right: 10px; }
    /* purple bar → espresso */
    .sleep-visual::after {
        content: ''; display: block; width: 50%;
        margin-left: auto; background: var(--espresso); opacity: 0.85;
    }

    .info-box {
        background: var(--parchment); border-radius: 9px;
        padding: 14px 16px; margin-top: 18px;
        font-size: 0.78rem; color: var(--text-soft);
        border: 1px solid var(--border); line-height: 1.7;
    }
    .info-box ul { margin-left: 18px; }

    /* ── COLOR PICKER (schedule) ── */
    .color-options { display: flex; gap: 9px; margin-top: 8px; flex-wrap: wrap; }
    .color-option {
        width: 28px; height: 28px; border-radius: 50%;
        cursor: pointer; border: 2.5px solid transparent;
        transition: border-color 0.15s, transform 0.15s;
    }
    .color-option:hover { transform: scale(1.15); }
    .color-option.selected { border-color: var(--espresso); }

    /* ── DASHBOARD ── */
    .dashboard-grid {
        display: grid;
        grid-template-columns: 1fr 340px;
        gap: 20px;
        height: calc(100vh - 100px);
        min-height: 500px;
    }
    .dashboard-left { display: flex; flex-direction: column; min-height: 0; overflow: hidden; }
    .dashboard-right { display: flex; flex-direction: column; gap: 18px; min-height: 0; overflow: hidden; }
    .mini-calendar-container { flex: 1; min-height: 0; overflow: hidden; position: relative; }
    #miniCalendar { height: 100% !important; min-height: 300px; }
    #miniCalendar .fc-toolbar { padding: 5px 0; margin-bottom: 10px !important; flex-wrap: wrap; }
    #miniCalendar .fc-toolbar h2 { font-size: 1rem; }
    #miniCalendar .fc-view-container { overflow-y: auto; overflow-x: hidden; }
    .deadline-content { flex: 1; overflow-y: auto; min-height: 0; padding-right: 4px; }
    .deadline-content::-webkit-scrollbar { width: 4px; }
    .deadline-content::-webkit-scrollbar-thumb { background: var(--taupe-light); border-radius: 2px; }

    /* ── MODALS (calendar) ── */
    .modal-overlay {
        display: none; position: fixed; inset: 0;
        background: rgba(46,31,20,0.45); z-index: 2000;
        align-items: center; justify-content: center;
    }
    .modal-overlay.visible { display: flex; }
    .modal-content {
        background: var(--white); padding: 28px; border-radius: 14px;
        text-align: center; max-width: 380px;
        box-shadow: 0 8px 32px rgba(82,63,49,0.25);
    }
    .modal-content h3 { margin-bottom: 12px; color: var(--text-dark); font-family: 'Cormorant Garamond', serif; font-size: 1.2rem; }
    .modal-content p { margin-bottom: 18px; color: var(--text-soft); font-size: 0.85rem; }
    .modal-buttons { display: flex; gap: 10px; justify-content: center; }
    .modal-buttons button { padding: 9px 22px; border: none; border-radius: 7px; cursor: pointer; font-size: 0.85rem; font-family: 'DM Sans', sans-serif; }
    .btn-cancel { background: var(--parchment2); color: var(--text-mid); }
    .btn-cancel:hover { background: var(--parchment); }
    .btn-delete { background: var(--danger); color: white; }
    .btn-delete:hover { background: #7A2F2F; }

    /* long press hint */
    .long-press-hint {
        position: fixed; bottom: 18px; left: 50%;
        transform: translateX(-50%);
        background: rgba(46,31,20,0.82); color: var(--parchment);
        padding: 9px 18px; border-radius: 18px; font-size: 0.82rem;
        z-index: 1000; opacity: 0; transition: opacity 0.3s;
    }
    .long-press-hint.visible { opacity: 1; }

    /* ── TOUR ── */
    .tour-wrapper {
        background: var(--espresso);
        min-height: 100vh;
        display: flex; align-items: center; justify-content: center;
    }
    .tour-container {
        background: var(--white); border-radius: 24px;
        padding: 44px; max-width: 580px; text-align: center;
        box-shadow: 0 24px 56px rgba(82,63,49,0.35);
    }
    .tour-icon { font-size: 4rem; margin-bottom: 18px; }
    .tour-container h1 {
        font-family: 'Cormorant Garamond', serif;
        color: var(--espresso); font-size: 2.2rem; font-weight: 600;
        margin-bottom: 12px;
    }
    .tour-container > p { color: var(--text-soft); font-size: 1rem; line-height: 1.6; margin-bottom: 26px; }
    .features { text-align: left; margin: 22px 0; }
    .feature-item {
        display: flex; align-items: center;
        padding: 13px 14px; background: var(--cream);
        border-radius: 12px; margin-bottom: 12px;
        border: 1px solid var(--border);
    }
    .feature-icon { font-size: 1.7rem; margin-right: 14px; }
    .feature-item h4 { color: var(--text-dark); margin-bottom: 3px; font-size: 0.9rem; font-weight: 600; }
    .feature-item p { color: var(--text-soft); font-size: 0.8rem; margin: 0; }
    .skip-link { display: block; margin-top: 16px; color: var(--text-soft); text-decoration: none; font-size: 0.85rem; }
    .skip-link:hover { color: var(--brown-mid); }
    .step-indicator { display: flex; justify-content: center; gap: 8px; margin-bottom: 26px; }
    .step-dot { width: 10px; height: 10px; border-radius: 50%; background: var(--border); }
    .step-dot.active { background: var(--espresso); }

    /* ── SCROLL ── */
    .task-list, .schedule-list { margin-top: 16px; max-height: 420px; overflow-y: auto; }
    .task-list::-webkit-scrollbar,
    .schedule-list::-webkit-scrollbar { width: 4px; }
    .task-list::-webkit-scrollbar-thumb,
    .schedule-list::-webkit-scrollbar-thumb { background: var(--taupe-light); border-radius: 2px; }

    /* ── FORM GRID ── */
    .task-form { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
    .task-form .form-group textarea { grid-column: span 2; }
    .form-row { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 14px; }
    .form-row-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }

    /* debug panel untouched */
    #debugPanel {
        display: none; background: #1e1e1e; color: #0f0;
        padding: 14px; border-radius: 8px; margin-top: 16px;
        font-family: monospace; font-size: 11px;
        max-height: 200px; overflow-y: auto;
    }

    /* ── RESPONSIVE ── */
    @media (max-width: 768px) {
        .sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
        .sidebar.active { transform: translateX(0); }
        .main-content { margin-left: 0; }
        .dashboard-grid { grid-template-columns: 1fr; height: auto; }
        .task-form, .form-row { grid-template-columns: 1fr; }
        .task-form .form-group textarea { grid-column: 1; }
    }
        .main-content.expanded { width: 100vw !important; }
        .main-content > .card,
        .main-content > div > .card { 
            width: 100% !important; 
            max-width: none !important; 
            box-sizing: border-box;
        }
        .task-form { width: 100%; }
        .form-row, .form-row-2 { width: 100%; }

        /* ── DEFINITIVE WIDTH + SCROLL FIX ── */
        html, body {
            width: 100%;
            min-height: 100%;
            overflow-x: hidden;
            overflow-y: auto;
        }
        .app-container {
            width: 100vw !important;
            min-height: 100vh;
            height: auto !important;
            display: flex;
            position: relative;
            overflow: visible !important;
        }
        .main-content {
            /* sidebar is fixed so we manually give main full remaining width */
            width: calc(100vw - 260px) !important;
            min-width: 0;
            margin-left: 260px !important;
            min-height: 100vh;
            height: auto !important;
            overflow-y: visible !important;
            overflow-x: hidden;
            padding: 20px !important;
            box-sizing: border-box !important;
            flex: none !important;
        }
        .main-content.expanded {
            width: 100vw !important;
            margin-left: 0 !important;
        }
        /* cards fill full width of main-content */
        .card {
            width: 100% !important;
            max-width: none !important;
            box-sizing: border-box !important;
        }
        /* un-cap list heights for natural scroll */
        .task-list, .schedule-list {
            max-height: none !important;
            overflow-y: visible !important;
        }
        /* task items left-align */
        .task-item {
            display: flex !important;
            align-items: flex-start !important;
            justify-content: space-between !important;
            text-align: left !important;
        }
        .task-info { flex: 1; text-align: left !important; }
        .task-info h4, .task-info p { text-align: left !important; }
        .task-actions { display: flex !important; flex-direction: row !important; gap: 8px; align-items: center; flex-shrink: 0; }

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
                <li><a href="document-analyzer.php"><span>📄</span> Add Task</a></li>
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
                            <div class="color-option selected" style="background:#A49A98;" data-color="#A49A98"></div>
                            <div class="color-option" style="background:#9D8A7C;" data-color="#9D8A7C"></div>
                            <div class="color-option" style="background:#796254;" data-color="#796254"></div>
                            <div class="color-option" style="background:#523F31;" data-color="#523F31"></div>
                            <div class="color-option" style="background:#8B3A3A;" data-color="#8B3A3A"></div>
                            <div class="color-option" style="background:#4A7C59;" data-color="#4A7C59"></div>
                        </div>
                    </div>
                    <button type="submit" class="btn">Add Class</button>
                </form>
            </div>

            <div class="card">
                <h2>📅 Your Weekly Schedule</h2>
                <div id="scheduleList" class="schedule-list">
                    <p style="text-align:center;color:var(--text-soft);padding:18px;">No classes added yet. Add your first class above!</p>
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
