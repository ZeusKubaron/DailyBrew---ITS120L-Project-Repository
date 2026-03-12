<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Task - DailyBrew</title>
    <link rel="stylesheet" href="../css/style.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
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
                <p style="color:var(--text-soft);margin-bottom:14px;font-size:0.85rem;">
                    Upload a file to automatically extract task details. The AI will analyze the document and fill in the form below.
                </p>
                <div class="upload-area" id="uploadArea">
                    <div class="upload-icon">📄</div>
                    <p class="upload-text">Drop a file here or click to upload</p>
                    <p class="upload-hint">Supports PDF, DOCX, TXT, MD files</p>
                    <input type="file" id="fileInput" style="display:none;" accept=".pdf,.docx,.doc,.txt,.md">
                </div>
                <div id="uploadStatus" style="margin-top:10px;text-align:center;color:var(--brown-mid);font-size:0.85rem;"></div>
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

    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/mammoth/1.6.0/mammoth.browser.min.js"></script>
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
