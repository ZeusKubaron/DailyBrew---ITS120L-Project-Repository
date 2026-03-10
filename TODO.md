# TODO - DailyBrew Improvements - COMPLETED ✓

## Phase 1: Document Analyzer Enhancements ✓
- [x] 1. Add PDF and DOCX support to document-analyzer.php (using PDF.js and Mammoth.js)
- [x] 2. Merge document analyzer with task creation form (single "Add Task" page)
- [x] 3. Enhance AI to extract task details from document content

## Phase 2: Dashboard & Calendar UI Fixes ✓
- [x] 4. Fix calendar container overflow with scrollable containers
- [x] 5. Extend container heights to fill screen properly (flex layout)
- [x] 6. Lock dashboard calendar to day-only view (agendaDay)
- [x] 10. Remove "today" button from calendar page

## Phase 3: Study Block Logic ✓
- [x] 7. Study blocks check for conflicts (classes, other blocks, sleep schedule)
- [x] 7b. Auto-remove blocks when task is completed/deleted
- [x] 8. Add sleep schedule settings in settings.php
- [x] 9. Implement the 3 study profiles correctly:
  - Early Crammer: Today until day before deadline
  - Late Crammer: 3 days before deadline until day before
  - Seamless: Today until 2 days before deadline

## Phase 4: UX Improvements ✓
- [x] 11. Add floating hamburger when sidebar is collapsed
- [x] 12. Add long-press to delete study blocks on calendar
- [x] 13. Make header bar sticky/floating

## Files Modified:
1. pages/document-analyzer.php - Complete rewrite with PDF/DOCX support + merged task form
2. pages/dashboard.php - Day-only calendar, scrollable containers, sticky header
3. pages/calendar.php - Removed today button, long-press delete, sleep visualization
4. pages/tasks.php - Enhanced study block generation with collision detection
5. pages/settings.php - Added sleep schedule settings
6. pages/schedule.php - Added floating hamburger, sticky header
7. api/analyze-text.php - Added document analysis support

## All 13 improvements completed!

