<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome - DailyBrew</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>

@import url('https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap');

:root {
  --cream:       #F5F0E8;
  --parchment:   #EDE5D8;
  --parchment2:  #E4D9C8;
  --taupe-light: #A49A98;
  --taupe:       #9D8A7C;
  --brown-mid:   #796254;
  --espresso:    #523F31;
  --espresso-dk: #3D2E22;
  --neutral:     #B1B1B1;
  --text-dark:   #2E1F14;
  --text-mid:    #5C4A3A;
  --text-soft:   #8C7B6E;
  --white:       #FDFAF6;
  --border:      rgba(164,154,152,0.22);
  --success:     #4A7C59;
  --warning:     #B89240;
  --danger:      #8B3A3A;
  --sidebar-w:   220px;
  --r:           10px;
}

*, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

html, body {
  height: 100%;
  font-family: 'DM Sans', sans-serif;
  background: var(--cream);
  color: var(--text-dark);
  overflow: hidden;
}

/* paper grain */
body::before {
  content:'';position:fixed;inset:0;z-index:0;pointer-events:none;opacity:0.4;
  background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
}

/* ── LAYOUT ── */
.app-container {
  display: flex;
  height: 100vh;
  overflow: hidden;
  position: relative;
  z-index: 1;
}

/* ── SIDEBAR ── */
.sidebar {
  width: var(--sidebar-w);
  background: var(--espresso);
  display: flex;
  flex-direction: column;
  flex-shrink: 0;
  height: 100vh;
  position: fixed;
  left: 0; top: 0;
  z-index: 20;
  box-shadow: 4px 0 28px rgba(82,63,49,0.22);
  transition: transform 0.28s cubic-bezier(0.4,0,0.2,1);
}
.sidebar.collapsed { transform: translateX(calc(-1 * var(--sidebar-w))); }

.sidebar-top {
  display: flex; align-items: center; gap: 10px;
  padding: 22px 18px 18px;
  border-bottom: 1px solid rgba(255,255,255,0.08);
  flex-shrink: 0;
}

/* hamburger inside sidebar */
.hamburger {
  display: flex; flex-direction: column; justify-content: center; gap: 5px;
  width: 34px; height: 34px; padding: 6px;
  background: none; border: none; cursor: pointer; border-radius: 7px;
  transition: background 0.15s; flex-shrink: 0;
}
.hamburger:hover { background: rgba(255,255,255,0.1); }
.hamburger span {
  display: block; height: 2px; background: var(--parchment); border-radius: 2px;
  transition: all 0.25s ease; transform-origin: center;
}

.brand { display: flex; align-items: center; gap: 9px; text-decoration: none; }
.brand-icon {
  width: 32px; height: 32px; border-radius: 50%;
  background: var(--parchment);
  display: flex; align-items: center; justify-content: center;
  font-size: 16px; flex-shrink: 0;
}
.brand-name {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.25rem; font-weight: 600; color: var(--parchment);
}
.brand-name em { font-style: italic; font-weight: 300; }

.sidebar-nav { flex: 1; padding: 16px 0; overflow-y: auto; }
.nav-label {
  font-size: 0.6rem; font-weight: 500; letter-spacing: 0.13em;
  text-transform: uppercase; color: var(--taupe-light); opacity: 0.55;
  padding: 10px 20px 5px;
}
.nav-item {
  display: flex; align-items: center; gap: 10px;
  padding: 9px 20px;
  color: rgba(237,229,216,0.65);
  font-size: 0.85rem; font-weight: 400;
  text-decoration: none;
  border-left: 3px solid transparent;
  transition: all 0.18s ease;
  cursor: pointer;
}
.nav-item:hover { color: var(--parchment); background: rgba(255,255,255,0.06); }
.nav-item.active { color: var(--parchment); background: rgba(255,255,255,0.1); border-left-color: var(--taupe-light); }
.nav-item svg { width: 16px; height: 16px; opacity: 0.75; flex-shrink: 0; }

.sidebar-bottom {
  padding: 14px 18px;
  border-top: 1px solid rgba(255,255,255,0.08);
  flex-shrink: 0;
}
.user-chip { display: flex; align-items: center; gap: 9px; }
.user-avatar {
  width: 28px; height: 28px; border-radius: 50%;
  background: var(--taupe); color: var(--parchment);
  font-size: 0.72rem; font-weight: 600;
  display: flex; align-items: center; justify-content: center;
  flex-shrink: 0;
}
.user-name { font-size: 0.78rem; color: var(--parchment); opacity: 0.75; flex: 1; }
.logout-btn {
  background: none; border: 1px solid rgba(255,255,255,0.18);
  color: var(--parchment); opacity: 0.6; cursor: pointer;
  font-size: 0.66rem; padding: 3px 8px; border-radius: 10px;
  transition: opacity 0.2s; font-family: 'DM Sans', sans-serif;
}
.logout-btn:hover { opacity: 1; }

/* floating open button */
.floating-hamburger {
  display: none;
  position: fixed; top: 16px; left: 14px; z-index: 25;
  flex-direction: column; justify-content: center; gap: 5px;
  width: 36px; height: 36px; padding: 6px;
  background: var(--parchment); border: 1px solid var(--border);
  cursor: pointer; border-radius: 8px; transition: background 0.15s;
}
.floating-hamburger:hover { background: var(--parchment2); }
.floating-hamburger span { display: block; height: 2px; background: var(--espresso); border-radius: 2px; }
.floating-hamburger.visible { display: flex; }

/* ── MAIN CONTENT ── */
.main-content {
  margin-left: var(--sidebar-w);
  flex: 1;
  width: calc(100vw - var(--sidebar-w));
  height: 100vh;
  display: flex;
  flex-direction: column;
  overflow: hidden;
  position: relative;
  z-index: 1;
  transition: margin-left 0.28s cubic-bezier(0.4,0,0.2,1), width 0.28s cubic-bezier(0.4,0,0.2,1);
}
.main-content.expanded {
  margin-left: 0;
  width: 100vw;
}

/* ── PAGE HEADER ── */
.page-header {
  padding: 24px 36px 0;
  display: flex; align-items: flex-end; justify-content: space-between;
  flex-shrink: 0;
}
.header-left {}
.header-sup {
  font-size: 0.66rem; letter-spacing: 0.14em; text-transform: uppercase;
  color: var(--text-soft); margin-bottom: 2px;
}
.header-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 2rem; font-weight: 300; color: var(--text-dark); line-height: 1.1;
}
.header-title em { font-style: italic; color: var(--brown-mid); }
.header-title.sm { font-size: 1.55rem; font-weight: 600; }
.header-right { display: flex; align-items: center; gap: 10px; padding-bottom: 4px; }
.date-pill {
  background: var(--parchment); border: 1px solid var(--border);
  padding: 5px 12px; border-radius: 18px; font-size: 0.72rem;
  color: var(--text-mid); font-weight: 500; letter-spacing: 0.02em;
}
.icon-btn {
  width: 30px; height: 30px; border-radius: 50%;
  background: var(--parchment); border: 1px solid var(--border);
  display: flex; align-items: center; justify-content: center;
  cursor: pointer; font-size: 0.8rem; transition: background 0.2s;
}
.icon-btn:hover { background: var(--white); }

.page-divider {
  margin: 14px 36px;
  border: none; border-top: 1px solid var(--border);
  flex-shrink: 0;
}

/* ── PAGE CONTENT (scrollable) ── */
.page-content {
  flex: 1; overflow-y: auto; padding: 0 36px 36px;
  min-height: 0;
}
.page-content::-webkit-scrollbar { width: 4px; }
.page-content::-webkit-scrollbar-thumb { background: var(--taupe-light); border-radius: 2px; opacity: 0.4; }

/* ── CARD ── */
.card {
  background: var(--white);
  border-radius: var(--r);
  border: 1px solid var(--border);
  box-shadow: 0 2px 12px rgba(82,63,49,0.07);
  margin-bottom: 18px;
  animation: fadeUp 0.38s ease both;
}
@keyframes fadeUp {
  from { opacity: 0; transform: translateY(12px); }
  to   { opacity: 1; transform: translateY(0); }
}
.card-header {
  padding: 15px 22px 13px;
  border-bottom: 1px solid var(--border);
  display: flex; align-items: center; justify-content: space-between;
}
.card-title {
  font-family: 'Cormorant Garamond', serif;
  font-size: 1.08rem; font-weight: 600; color: var(--text-dark);
  display: flex; align-items: center; gap: 7px;
}
.card-subtitle { font-size: 0.71rem; color: var(--text-soft); margin-top: 2px; font-weight: 400; }
.card-action {
  font-size: 0.68rem; color: var(--brown-mid); background: none; border: none;
  cursor: pointer; font-weight: 500; letter-spacing: 0.04em; text-transform: uppercase;
  opacity: 0.8; transition: opacity 0.2s; font-family: 'DM Sans', sans-serif;
}
.card-action:hover { opacity: 1; }

/* ── FORMS ── */
.form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 20px 22px; }
.form-grid.full { grid-template-columns: 1fr; }
.form-group { display: flex; flex-direction: column; gap: 6px; }
.form-group.span2 { grid-column: span 2; }
.form-label { font-size: 0.74rem; font-weight: 500; color: var(--text-mid); letter-spacing: 0.03em; }
.form-label sup { color: var(--brown-mid); }

.form-input, .form-select, .form-textarea,
.form-group input, .form-group select, .form-group textarea {
  width: 100%; padding: 10px 13px;
  border: 1.5px solid var(--parchment2);
  border-radius: 7px; font-size: 0.84rem;
  background: var(--white); color: var(--text-dark);
  font-family: 'DM Sans', sans-serif;
  transition: border-color 0.2s, box-shadow 0.2s;
  outline: none;
  box-sizing: border-box;
}
.form-input::placeholder, .form-textarea::placeholder,
.form-group input::placeholder, .form-group textarea::placeholder { color: var(--neutral); opacity: 0.7; }
.form-input:focus, .form-select:focus, .form-textarea:focus,
.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
  border-color: var(--taupe);
  box-shadow: 0 0 0 3px rgba(157,138,124,0.12);
}
.form-textarea, .form-group textarea { resize: vertical; min-height: 90px; line-height: 1.5; }
.form-select, .form-group select {
  cursor: pointer; appearance: none;
  background-image: url("data:image/svg+xml,%3Csvg width='10' height='6' viewBox='0 0 10 6' fill='none' xmlns='http://www.w3.org/2000/svg'%3E%3Cpath d='M1 1L5 5L9 1' stroke='%238C7B6E' stroke-width='1.5' stroke-linecap='round'/%3E%3C/svg%3E");
  background-repeat: no-repeat; background-position: right 12px center;
}

/* ── BUTTONS ── */
.btn {
  display: inline-flex; align-items: center; gap: 7px;
  padding: 10px 22px;
  background: var(--espresso); color: var(--parchment);
  border: none; border-radius: 8px;
  font-size: 0.84rem; font-weight: 500; cursor: pointer;
  font-family: 'DM Sans', sans-serif;
  transition: all 0.2s ease;
  text-decoration: none;
}
.btn:hover { background: var(--espresso-dk); transform: translateY(-1px); box-shadow: 0 4px 14px rgba(82,63,49,0.28); }
.btn-outline { background: transparent; color: var(--espresso); border: 1.5px solid var(--espresso); }
.btn-outline:hover { background: var(--parchment); transform: none; box-shadow: none; }
.btn-sm { padding: 6px 14px; font-size: 0.76rem; border-radius: 6px; }
.btn-danger, .btn-delete { background: var(--danger); color: white; }
.btn-danger:hover, .btn-delete:hover { background: #7A2F2F; }
.btn-success, .btn-complete { background: var(--success); color: white; }
.btn-success:hover, .btn-complete:hover { background: #3D6849; }

/* round action buttons */
.task-act-btn {
  width: 28px; height: 28px; border-radius: 50%; border: none; cursor: pointer;
  display: flex; align-items: center; justify-content: center; font-size: 0.75rem;
  transition: transform 0.15s, filter 0.15s;
}
.task-act-btn:hover { transform: scale(1.12); }
.task-act-btn.done { background: var(--success); color: white; }
.task-act-btn.del  { background: var(--danger);  color: white; }

/* ── PRIORITY BADGES ── */
.priority-badge, .priority-tag {
  display: inline-block; padding: 2px 7px;
  border-radius: 5px; font-size: 0.6rem; font-weight: 700;
  letter-spacing: 0.07em; text-transform: uppercase;
}
.priority-high,  .priority-tag.high   { background: var(--espresso); color: var(--parchment); }
.priority-medium,.priority-tag.medium { background: var(--brown-mid); color: var(--parchment); }
.priority-low,   .priority-tag.low    { background: var(--taupe-light); color: var(--white); }

/* ── TASK ITEMS (JS-rendered) ── */
.task-item, .task-row {
  display: flex; align-items: center; gap: 12px;
  padding: 11px 16px;
  background: var(--cream); border-radius: 8px;
  border-left: 3px solid var(--taupe);
  margin-bottom: 10px;
  animation: fadeUp 0.35s ease both;
}
.task-item.completed { opacity: 0.5; }
.task-item.completed .task-info h4 { text-decoration: line-through; }
.task-item.high, .task-row.high { border-left-color: var(--espresso); }
.task-item.medium,.task-row.medium { border-left-color: var(--brown-mid); }
.task-item.low,  .task-row.low   { border-left-color: var(--taupe-light); }
.task-info, .task-row .task-info { flex: 1; }
.task-info h4, .task-info .t-name { font-size: 0.87rem; font-weight: 500; color: var(--text-dark); display: flex; align-items: center; gap: 6px; flex-wrap: wrap; }
.task-info p, .task-info .t-meta  { font-size: 0.7rem; color: var(--text-soft); margin-top: 3px; }
.task-actions { display: flex; gap: 6px; align-items: center; flex-shrink: 0; }

/* tasks list wrapper */
.tasks-list, .task-list { padding: 14px 18px 18px; }

/* ── SCHEDULE ITEMS (JS-rendered) ── */
.schedule-item, .schedule-row {
  display: flex; align-items: center; gap: 12px;
  padding: 11px 14px;
  border-radius: 8px; border-left: 4px solid var(--taupe);
  background: var(--cream); margin-bottom: 10px;
  animation: fadeUp 0.35s ease both;
}
.schedule-item > div:first-child, .sched-info { flex: 1; }
.schedule-item h4, .sched-name { font-size: 0.87rem; font-weight: 500; color: var(--text-dark); }
.schedule-item p, .sched-meta  { font-size: 0.71rem; color: var(--text-soft); margin-top: 2px; }
.schedule-actions { display: flex; gap: 6px; }
.schedule-list { padding: 14px 18px 18px; }

/* ── DASHBOARD ── */
.dash-stats { display: grid; grid-template-columns: repeat(3,1fr); gap: 14px; margin-bottom: 18px; }
.stat-card {
  background: var(--white); border-radius: var(--r);
  border: 1px solid var(--border); padding: 16px 20px;
  box-shadow: 0 2px 10px rgba(82,63,49,0.06);
  animation: fadeUp 0.4s ease both;
}
.stat-label { font-size: 0.63rem; letter-spacing: 0.1em; text-transform: uppercase; color: var(--text-soft); }
.stat-value { font-family: 'Cormorant Garamond', serif; font-size: 2rem; font-weight: 600; color: var(--espresso); line-height: 1.1; margin: 2px 0; }
.stat-sub { font-size: 0.67rem; color: var(--text-soft); }
.stat-bar { height: 3px; background: var(--parchment); border-radius: 2px; margin-top: 8px; overflow: hidden; }
.stat-bar-fill { height: 100%; background: var(--brown-mid); border-radius: 2px; }

.quote-strip {
  padding: 12px 20px; background: var(--espresso); border-radius: var(--r);
  display: flex; align-items: center; gap: 12px; margin-bottom: 18px;
  animation: fadeUp 0.4s ease both;
}
.quote-text { font-family: 'Cormorant Garamond', serif; font-size: 0.9rem; font-style: italic; font-weight: 300; color: var(--parchment); opacity: 0.9; }
.quote-attr { font-size: 0.62rem; color: var(--taupe-light); margin-left: auto; flex-shrink: 0; opacity: 0.7; }

.dash-grid { display: grid; grid-template-columns: 1fr 290px; gap: 18px; align-items: start; }

/* deadline cards */
.deadline-card {
  margin: 10px 18px; padding: 10px 13px;
  background: var(--cream); border-radius: 8px;
  border-left: 3px solid var(--taupe);
}
.deadline-card.overdue { border-left-color: var(--danger); background: rgba(139,58,58,0.06); }
.deadline-card h4 { font-size: 0.85rem; font-weight: 500; color: var(--text-dark); display: flex; align-items: center; gap: 6px; }
.deadline-card p { font-size: 0.7rem; color: var(--text-soft); margin-top: 3px; }
.no-deadlines { padding: 22px 18px; text-align: center; color: var(--text-soft); font-size: 0.8rem; }

/* sleep strip */
.sleep-indicator, .sleep-bar-strip {
  margin: 0 18px 18px; padding: 9px 14px;
  background: var(--parchment); border-radius: 7px;
  display: flex; align-items: center; gap: 8px;
  font-size: 0.72rem; color: var(--text-soft);
  border: 1px solid var(--border);
}

/* mini calendar container */
.mini-calendar-container { min-height: 350px; }
#miniCalendar { height: 350px !important; }
#miniCalendar .fc-toolbar { padding: 6px 0; margin-bottom: 10px !important; flex-wrap: wrap; }
#miniCalendar .fc-toolbar h2 {
  font-family: 'Cormorant Garamond', serif !important;
  font-size: 1rem !important; font-weight: 400 !important;
}
#miniCalendar .fc-view-container { overflow-y: auto; }

/* ── CALENDAR PAGE ── */
.calendar-card { overflow: auto; }
#calendar { min-height: calc(100vh - 200px); }
#calendar .fc-toolbar { padding: 10px 0; margin-bottom: 14px !important; flex-wrap: wrap; gap: 8px; }
#calendar .fc-view-container { overflow-y: auto; }

/* FullCalendar overrides */
.fc-button, .fc-button-primary {
  background: var(--espresso) !important; border-color: var(--espresso) !important;
  color: var(--parchment) !important; font-family: 'DM Sans', sans-serif !important;
  font-size: 0.78rem !important; border-radius: 6px !important; box-shadow: none !important;
}
.fc-button:hover, .fc-button-primary:hover { background: var(--espresso-dk) !important; border-color: var(--espresso-dk) !important; }
.fc-button-active, .fc-button-primary:not(:disabled).fc-button-active { background: var(--brown-mid) !important; border-color: var(--brown-mid) !important; }
.fc-toolbar h2 {
  font-family: 'Cormorant Garamond', serif !important;
  font-size: 1.3rem !important; font-weight: 400 !important; color: var(--text-dark) !important;
}
.fc-today { background: rgba(164,154,152,0.07) !important; }

/* ── UPLOAD ZONE ── */
.upload-zone {
  margin: 16px 22px; padding: 30px 22px;
  border: 2px dashed var(--parchment2); border-radius: var(--r);
  display: flex; flex-direction: column; align-items: center; gap: 8px;
  cursor: pointer; transition: border-color 0.2s, background 0.2s;
  background: var(--cream);
}
.upload-zone:hover { border-color: var(--taupe); background: rgba(164,154,152,0.06); }
.upload-zone.dragover { border-color: var(--brown-mid); background: rgba(121,98,84,0.08); }
.upload-icon { font-size: 2rem; opacity: 0.5; }
.upload-text { font-size: 0.84rem; color: var(--text-soft); font-weight: 500; }
.upload-sub  { font-size: 0.7rem; color: var(--neutral); opacity: 0.8; }

/* upload area alias */
.upload-area { margin: 16px 22px; padding: 30px 22px; border: 2px dashed var(--parchment2); border-radius: var(--r); display: flex; flex-direction: column; align-items: center; gap: 8px; cursor: pointer; transition: border-color 0.2s, background 0.2s; background: var(--cream); text-align: center; }
.upload-area:hover { border-color: var(--taupe); background: rgba(164,154,152,0.06); }
.upload-area.dragover { border-color: var(--brown-mid); background: rgba(121,98,84,0.08); }
.upload-area .upload-icon { font-size: 2rem; opacity: 0.5; }

/* ── ANALYSIS RESULT ── */
.analysis-result { background: var(--parchment); border-radius: 8px; padding: 16px; margin: 0 22px 16px; }
.analysis-result h3 { font-family: 'Cormorant Garamond', serif; color: var(--espresso); margin-bottom: 10px; font-size: 1.05rem; }
.analysis-item { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px solid var(--border); }
.analysis-item:last-child { border-bottom: none; }
.analysis-label { color: var(--text-soft); font-size: 0.8rem; }
.analysis-value { font-weight: 600; color: var(--text-dark); font-size: 0.8rem; }

/* ── PROFILE INFO (tasks page) ── */
.profile-info {
  padding: 9px 12px; background: var(--parchment); border-radius: 7px; margin-top: 8px;
  font-size: 0.73rem; color: var(--text-mid); border: 1px solid var(--border); line-height: 1.5;
}

/* ── SETTINGS ── */
.settings-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; padding: 18px 22px; }
.settings-grid.full { grid-template-columns: 1fr; }
.profile-option {
  padding: 11px 14px; border-radius: 8px; border: 1.5px solid var(--border);
  cursor: pointer; transition: all 0.15s; margin-bottom: 8px;
}
.profile-option:hover { border-color: var(--taupe); background: var(--parchment); }
.profile-option.selected { border-color: var(--brown-mid); background: rgba(121,98,84,0.08); }
.profile-option h4, .profile-option .p-name { font-size: 0.85rem; font-weight: 500; color: var(--text-dark); display: flex; align-items: center; gap: 6px; }
.profile-option p, .profile-option .p-desc  { font-size: 0.71rem; color: var(--text-soft); margin-top: 2px; }
.profile-options-wrap { padding: 0 22px 18px; }

.sleep-visual {
  margin: 0 22px 16px; height: 34px;
  background: var(--parchment); border-radius: 18px; overflow: hidden;
  display: flex; align-items: stretch; border: 1px solid var(--border);
}
.sleep-wake { flex: 1; display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: var(--text-soft); }
.sleep-block { width: 45%; background: var(--espresso); display: flex; align-items: center; justify-content: center; font-size: 0.7rem; color: var(--parchment); opacity: 0.9; gap: 5px; }

.tips-box {
  padding: 13px 16px; background: rgba(164,154,152,0.1);
  border-radius: 7px; border: 1px solid var(--border);
  font-size: 0.74rem; color: var(--text-soft); line-height: 1.7;
  margin: 0 22px 18px;
}
.tips-box strong { color: var(--text-mid); display: flex; align-items: center; gap: 5px; margin-bottom: 4px; }
.tips-box li { margin-left: 16px; }
.settings-footer { padding: 4px 22px 20px; display: flex; gap: 10px; }

/* ── COLOR PICKER ── */
.color-options, .color-picker { display: flex; gap: 8px; flex-wrap: wrap; }
.color-option, .color-swatch {
  width: 26px; height: 26px; border-radius: 50%; cursor: pointer;
  border: 2.5px solid transparent; transition: border-color 0.15s, transform 0.15s;
}
.color-option:hover, .color-swatch:hover { transform: scale(1.15); }
.color-option.selected, .color-swatch.selected { border-color: var(--espresso); }

/* ── LOADING ── */
.loading { text-align: center; padding: 20px; color: var(--text-soft); }
.loading-spinner {
  border: 3px solid var(--parchment); border-top: 3px solid var(--brown-mid);
  border-radius: 50%; width: 28px; height: 28px;
  animation: spin 0.9s linear infinite; margin: 0 auto 10px;
}
@keyframes spin { 0%{transform:rotate(0deg)} 100%{transform:rotate(360deg)} }

/* debug panel */
#debugPanel {
  display: none; background: #1e1e1e; color: #0f0;
  padding: 12px; border-radius: 7px; margin: 12px 22px 0;
  font-family: monospace; font-size: 11px; max-height: 180px; overflow-y: auto;
}

/* ── MODAL ── */
.modal-overlay {
  display: none; position: fixed; inset: 0;
  background: rgba(46,31,20,0.45); z-index: 2000;
  align-items: center; justify-content: center;
}
.modal-overlay.visible { display: flex; }
.modal-content {
  background: var(--white); padding: 28px; border-radius: 14px;
  text-align: center; max-width: 360px;
  box-shadow: 0 8px 32px rgba(82,63,49,0.25);
}
.modal-content h3 { margin-bottom: 10px; color: var(--text-dark); font-family: 'Cormorant Garamond', serif; font-size: 1.2rem; }
.modal-content p { margin-bottom: 18px; color: var(--text-soft); font-size: 0.83rem; }
.modal-buttons { display: flex; gap: 10px; justify-content: center; }
.modal-buttons button { padding: 9px 22px; border: none; border-radius: 7px; cursor: pointer; font-size: 0.83rem; font-family: 'DM Sans', sans-serif; }
.btn-cancel { background: var(--parchment2); color: var(--text-mid); }
.btn-cancel:hover { background: var(--parchment); }

/* long press hint */
.long-press-hint {
  position: fixed; bottom: 18px; left: 50%; transform: translateX(-50%);
  background: rgba(46,31,20,0.82); color: var(--parchment);
  padding: 8px 18px; border-radius: 18px; font-size: 0.8rem;
  z-index: 1000; opacity: 0; transition: opacity 0.3s;
}
.long-press-hint.visible { opacity: 1; }

/* ── FAB ── */
.fab {
  position: fixed; bottom: 26px; right: 26px;
  width: 44px; height: 44px; border-radius: 50%;
  background: var(--espresso); border: none;
  color: var(--parchment); font-size: 1.4rem;
  cursor: pointer; box-shadow: 0 4px 16px rgba(82,63,49,0.35);
  display: flex; align-items: center; justify-content: center;
  transition: transform 0.2s, box-shadow 0.2s; z-index: 100;
}
.fab:hover { transform: scale(1.1) translateY(-2px); box-shadow: 0 8px 22px rgba(82,63,49,0.4); }

/* ── RESPONSIVE ── */
@media (max-width: 768px) {
  .main-content { margin-left: 0; width: 100vw; }
  .sidebar { transform: translateX(calc(-1 * var(--sidebar-w))); }
  .sidebar.collapsed { transform: translateX(calc(-1 * var(--sidebar-w))); }
  .dash-grid { grid-template-columns: 1fr; }
  .dash-stats { grid-template-columns: 1fr; }
  .form-grid { grid-template-columns: 1fr; }
  .form-group.span2 { grid-column: 1; }
  .settings-grid { grid-template-columns: 1fr; }
  .page-header, .page-content { padding-left: 20px; padding-right: 20px; }
  .page-divider { margin-left: 20px; margin-right: 20px; }
}

    /* ── TOUR OVERRIDES — must win over shared CSS ── */
    html { overflow: auto !important; height: auto !important; }
    body {
      display: flex !important;
      align-items: center !important;
      justify-content: center !important;
      min-height: 100vh !important;
      height: auto !important;
      overflow: auto !important;
      background: var(--espresso) !important;
      padding: 24px;
    }
    /* kill sidebar/layout bleed */
    .app-container { display: contents !important; }
    .main-content  { margin-left: 0 !important; width: 100% !important; height: auto !important; overflow: visible !important; }
    .tour-card {
      background:var(--white); border-radius:20px; padding:44px;
      max-width:560px; width:90%; text-align:center;
      box-shadow:0 24px 56px rgba(82,63,49,0.35);
      animation:fadeUp 0.5s ease both;
    }
    .tour-icon { font-size:3.5rem; margin-bottom:16px; }
    .tour-card h1 { font-family:'Cormorant Garamond',serif; color:var(--espresso); font-size:2.1rem; font-weight:600; margin-bottom:10px; }
    .tour-card > p { color:var(--text-soft); font-size:0.95rem; line-height:1.6; margin-bottom:24px; }
    .features { text-align:left; margin:20px 0; }
    .feature-item { display:flex; align-items:center; padding:12px 14px; background:var(--cream); border-radius:10px; margin-bottom:10px; border:1px solid var(--border); }
    .feature-icon { font-size:1.6rem; margin-right:14px; }
    .feature-item h4 { color:var(--text-dark); margin-bottom:2px; font-size:0.88rem; font-weight:600; }
    .feature-item p { color:var(--text-soft); font-size:0.76rem; margin:0; }
    .tour-btn { padding:13px 44px; background:var(--espresso); color:var(--parchment); border:none; border-radius:28px; font-size:1rem; font-weight:500; cursor:pointer; font-family:'DM Sans',sans-serif; transition:all 0.2s; }
    .tour-btn:hover { background:var(--espresso-dk); transform:translateY(-2px); box-shadow:0 8px 24px rgba(82,63,49,0.35); }
    .skip-link { display:block; margin-top:14px; color:var(--text-soft); text-decoration:none; font-size:0.82rem; transition:color 0.2s; }
    .skip-link:hover { color:var(--brown-mid); }
    .step-dots { display:flex; justify-content:center; gap:7px; margin-bottom:24px; }
    .step-dot { width:9px; height:9px; border-radius:50%; background:var(--parchment2); transition:background 0.2s; }
    .step-dot.active { background:var(--espresso); }
  </style>
</head>
<body>
  <div class="tour-card" id="tourCard">
    <div class="step-dots">
      <div class="step-dot active"></div>
      <div class="step-dot active"></div>
      <div class="step-dot active"></div>
      <div class="step-dot"></div>
    </div>
    <div class="tour-icon">☕</div>
    <h1>Welcome to DailyBrew!</h1>
    <p>Your personal AI-powered study scheduler. Let me show you around!</p>
    <div class="features">
      <div class="feature-item">
        <span class="feature-icon">📅</span>
        <div><h4>Smart Calendar</h4><p>View your schedule in Day, Week, or Month view</p></div>
      </div>
      <div class="feature-item">
        <span class="feature-icon">🤖</span>
        <div><h4>AI Assistant</h4><p>Gemini AI analyzes tasks and creates study blocks</p></div>
      </div>
      <div class="feature-item">
        <span class="feature-icon">📄</span>
        <div><h4>Document Analyzer</h4><p>Upload documents for automatic task extraction</p></div>
      </div>
    </div>
    <button class="tour-btn" onclick="nextStep()">Next →</button>
    <a href="dashboard.php" class="skip-link">Skip tour, go straight to dashboard →</a>
  </div>

  <script>
        const currentUser = localStorage.getItem('dailybrew_current_user');
        if (!currentUser) {
            window.location.href = '../auth/login.php';
        }
        
        let step = 1;
        
        function nextStep() {
            const container = document.getElementById('tourCard');
            
            if (step === 1) {
                step = 2;
                container.innerHTML = `
                    <div class="step-dots">
                        <div class="step-dot active"></div>
                        <div class="step-dot active"></div>
                        <div class="step-dot"></div>
                        <div class="step-dot"></div>
                    </div>
                    <div class="tour-icon">📚</div>
                    <h1>How It Works</h1>
                    <p>Here's how DailyBrew helps you study smarter:</p>
                    <div class="features">
                        <div class="feature-item">
                            <span class="feature-icon">1️⃣</span>
                            <div><h4>Add Tasks</h4><p>Create tasks with deadlines — AI will analyze them</p></div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">2️⃣</span>
                            <div><h4>AI Analysis</h4><p>Priority and complexity calculated automatically</p></div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">3️⃣</span>
                            <div><h4>Study Blocks</h4><p>AI generates a personalized study schedule</p></div>
                        </div>
                    </div>
                    <button class="tour-btn" onclick="nextStep()">Next →</button>
                    <a href="dashboard.php" class="skip-link">Skip tour →</a>
                `;
            } else if (step === 2) {
                step = 3;
                container.innerHTML = `
                    <div class="step-dots">
                        <div class="step-dot active"></div>
                        <div class="step-dot active"></div>
                        <div class="step-dot active"></div>
                        <div class="step-dot"></div>
                    </div>
                    <div class="tour-icon">⚙️</div>
                    <h1>Customize Your Experience</h1>
                    <p>Set your preferences to get the most out of DailyBrew</p>
                    <div class="features">
                        <div class="feature-item">
                            <span class="feature-icon">⏰</span>
                            <div><h4>Study Hours</h4><p>Set your preferred study time range</p></div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">📊</span>
                            <div><h4>Block Duration</h4><p>Choose how long each study session lasts</p></div>
                        </div>
                        <div class="feature-item">
                            <span class="feature-icon">🌊</span>
                            <div><h4>Study Profile</h4><p>Pick Early Crammer, Seamless, or Late Crammer</p></div>
                        </div>
                    </div>
                    <button class="tour-btn" onclick="nextStep()">Let's Go! ☕</button>
                `;
            } else {
                finishTour();
            }
        }
        
        function finishTour() {
            const user = JSON.parse(localStorage.getItem('dailybrew_current_user'));
            user.tourCompleted = true;
            localStorage.setItem('dailybrew_current_user', JSON.stringify(user));
            
            window.location.href = 'dashboard.php';
        }
    </script>
</body>
</html>
