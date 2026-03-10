# DailyBrew Improvement Plan - 13 Points

## Information Gathered

### Current State Analysis:
1. **document-analyzer.php** - Currently only supports .txt, .md, .doc files. Simply copies text to textarea. Uses simple JS-based AI analysis.
2. **dashboard.php** - Uses FullCalendar with agendaWeek view, has scroll issues with containers overflowing
3. **calendar.php** - FullCalendar with all views (month, week, day), has "today" button
4. **tasks.php** - Has study block generation with basic collision detection
5. **schedule.php** - Academic schedule management
6. **settings.php** - User preferences including sleep schedule
7. **config/gemini.php** - Has basic study block generation logic

### Files to be Modified:
1. `pages/document-analyzer.php` - Add PDF/DOCX support, merge with task form, enhance AI
2. `pages/dashboard.php` - Fix calendar container, scrollable, day-only view
3. `pages/calendar.php` - Remove today button, add delete on long click
4. `pages/tasks.php` - Enhance study block logic with all constraints
5. `pages/settings.php` - Add sleep schedule settings
6. `css/style.css` - Global styles for scrollable containers, sticky header

---

## Detailed Plan

### 1. PDF and DOCX Support for Document Analyzer
- Add PDF.js library for PDF parsing
- Use mammoth.js for DOCX parsing
- Update upload area accept attribute to include .pdf, .docx
- Create JS functions to extract text from these formats

### 2. Merge Document Analyzer with Add Task
- Combine the functionality in document-analyzer.php
- Keep the form fields for task details
- Auto-populate fields from AI analysis
- Remove separate task creation in tasks.php (optional - keep both for convenience)

### 3. Enhanced AI Document Analysis
- Update the analyze-document.php API to extract:
  - Task/activity name from content
  - Deadline date (look for dates in document)
  - Type of activity (exam, homework, project, etc.)
- Auto-fill form fields with extracted data
- Provide AI suggestions for priority and complexity

### 4. Calendar Container Overflow Fix (Dashboard & Calendar)
- Add `overflow-y: auto` and `max-height` to calendar containers
- Make Due Today/Due Tomorrow containers scrollable
- Ensure contents don't overflow or get compressed

### 5. Extend Container Heights (Dashboard)
- Set `flex: 1` or calculate proper heights for dashboard containers
- Use `min-height` with viewport height calculations
- Remove awkward white space with proper flexbox filling

### 6. Lock Dashboard Calendar to Day View
- Change defaultView from 'agendaWeek' to 'agendaDay'
- Remove month/agendaWeek from header buttons
- Keep only the current day view

### 7. Study Block Conditions
**a. Collision Detection:**
- Check against existing study blocks
- Check against class schedule
- Check against sleep schedule
- Find alternative slots if collision exists
- Mark day as full if no available slots

**b. Auto-removal based on deadline:**
- Study blocks only exist until task deadline
- Remove blocks when task is completed
- Remove blocks when task is deleted

### 8. Sleep Schedule Support
- Add sleep schedule fields in settings.php (sleep start/end times)
- Grey out sleep periods in calendar with visual indication
- Exclude sleep times from study block generation
- Store sleep schedule in localStorage

### 9. Study Profile Implementation
**a. Early Crammer:**
- Schedule from today until (deadline - 1 day)
- Maximize early days for completion
- Ignore "spaced out" approach

**b. Late Crammer:**
- Schedule from (deadline - 3 days) to (deadline - 1 day)
- Compress studying to last possible days

**c. Seamless:**
- Schedule from today to (deadline - 2 days)
- Spread evenly with good gaps between sessions

### 10. Remove "Today" Button from Calendar Page
- Remove 'today' from calendar header config in calendar.php

### 11. Floating Hamburger When Sidebar Collapsed
- Add a floating hamburger button that appears when sidebar is collapsed
- Position it fixed at top-left corner
- Toggle sidebar visibility when clicked

### 12. Delete Study Blocks on Calendar Page
- Add long-press/long-click detection on study blocks
- Show delete confirmation
- Remove block from localStorage and refresh calendar

### 13. Sticky/Floating Header Bar
- Add `position: sticky` or `position: fixed` to header
- Ensure it stays at top when scrolling
- Add appropriate z-index

---

## Implementation Order

### Phase 1: Core Changes (Points 1, 2, 3)
- document-analyzer.php - PDF/DOCX support + AI enhancement
- Update analyze-document.php API

### Phase 2: Dashboard & Calendar UI (Points 4, 5, 6, 10)
- dashboard.php - calendar fix, scrollable, day-only view
- calendar.php - remove today button

### Phase 3: Study Block Logic (Points 7, 8, 9)
- settings.php - add sleep schedule
- tasks.php - enhance study block generation with all constraints

### Phase 4: UX Improvements (Points 11, 12, 13)
- All pages - floating hamburger button
- calendar.php - delete study blocks on long click
- All pages - sticky header

---

## Files to be Modified
1. pages/document-analyzer.php
2. pages/dashboard.php
3. pages/calendar.php
4. pages/tasks.php
5. pages/settings.php
6. api/analyze-document.php
7. css/style.css

---

## Follow-up Steps
1. Implement Phase 1 - Document Analyzer enhancements
2. Test PDF/DOCX parsing
3. Verify AI extracts correct task details
4. Implement Phase 2 - Dashboard/Calendar fixes
5. Test scrollable containers
6. Verify day-only view works
7. Implement Phase 3 - Study block logic
8. Test collision detection
9. Test sleep schedule blocking
10. Verify all three profiles work correctly
11. Implement Phase 4 - UX improvements
12. Final testing

