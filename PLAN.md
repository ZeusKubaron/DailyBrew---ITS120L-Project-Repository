# DailyBrew - AI-Assisted Student Scheduler

## Project Overview
A comprehensive webapp for college/university students to manage their academic schedules with AI-powered study block generation using Gemini AI.

---

## 1. Information Gathered

### Tech Stack
- **Frontend**: HTML5, CSS3, JavaScript (Vanilla)
- **Backend**: PHP 8.x with MySQL
- **AI**: Google Gemini API for task prioritization and study block scheduling

### Core Features to Implement
1. User Authentication (Sign Up / Login)
2. Academic Schedule Management
3. Task/Deadline Management with AI prioritization
4. AI-powered Study Block generation
5. Calendar View (Day/Week/Month)
6. User Preferences/Settings
7. Study Block Profile Management

---

## 2. Database Design

### Tables Required

#### `users`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AUTO_INCREMENT) | User ID |
| first_name | VARCHAR(50) | First name |
| last_name | VARCHAR(50) | Last name |
| password | VARCHAR(255) | Hashed password |
| created_at | TIMESTAMP | Registration date |

#### `academic_schedule`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AUTO_INCREMENT) | Schedule ID |
| user_id | INT (FK) | Reference to users |
| day_of_week | ENUM | Day (Mon-Sun) |
| start_time | TIME | Start hour |
| end_time | TIME | End hour |
| subject | VARCHAR(100) | Subject name |

#### `tasks`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AUTO_INCREMENT) | Task ID |
| user_id | INT (FK) | Reference to users |
| title | VARCHAR(200) | Task title |
| description | TEXT | Task instructions/details |
| due_date | DATE | Due date |
| due_time | TIME | Due time |
| ai_priority | ENUM | AI assigned: high, medium, low |
| user_priority | ENUM | User override: high, medium, low |
| complexity | INT | AI calculated: 1-10 |
| status | ENUM | pending, in_progress, completed |
| created_at | TIMESTAMP | Creation date |

#### `study_blocks`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AUTO_INCREMENT) | Block ID |
| user_id | INT (FK) | Reference to users |
| task_id | INT (FK) | Reference to tasks |
| title | VARCHAR(200) | Block title |
| scheduled_date | DATE | Date of study block |
| start_time | TIME | Start time |
| end_time | TIME | End time |
| profile | ENUM | early_crammer, seamless, late_crammer |
| created_at | TIMESTAMP | Creation date |

#### `user_preferences`
| Column | Type | Description |
|--------|------|-------------|
| id | INT (PK, AUTO_INCREMENT) | Preference ID |
| user_id | INT (FK) | Reference to users |
| earliest_time_start | TIME | Default: 08:00 |
| latest_time_end | TIME | Default: 22:00 |
| study_block_duration | INT | Default: 30 (minutes) |
| default_profile | ENUM | Default: seamless |

---

## 3. File Structure

```
DailyBrew/
├── index.php                 # Entry point / Landing page
├── auth/
│   ├── login.php             # Login handler
│   ├── register.php          # Registration handler
│   ├── logout.php            # Logout handler
│   └── session.php           # Session management
├── config/
│   ├── database.php          # Database connection
│   └── gemini.php            # Gemini API configuration
├── includes/
│   ├── header.php            # Common header
│   ├── footer.php            # Common footer
│   └── functions.php         # Helper functions
├── api/
│   ├── gemini-chat.php       # Gemini AI chat endpoint
│   ├── schedule-task.php     # AI schedule task endpoint
│   └── update-blocks.php     # Reorganize study blocks
├── pages/
│   ├── dashboard.php         # Main dashboard
│   ├── calendar.php          # Calendar view
│   ├── tasks.php             # Task management
│   ├── schedule.php          # Academic schedule
│   ├── settings.php          # User preferences
│   └── tour.php              # Onboarding tour
├── css/
│   ├── style.css             # Main stylesheet
│   ├── calendar.css          # Calendar styles
│   └── components.css        # Reusable components
├── js/
│   ├── main.js               # Main JavaScript
│   ├── calendar.js           # Calendar logic
│   ├── tasks.js              # Task management
│   ├── ai-scheduler.js       # AI scheduling logic
│   └── tour.js               # Tour functionality
└── assets/
    └── images/               # Images/icons
```

---

## 4. Implementation Plan

### Phase 1: Foundation (Days 1-2)
- [ ] Set up project structure
- [ ] Create database and tables
- [ ] Database connection configuration
- [ ] Basic HTML templates

### Phase 2: Authentication (Days 2-3)
- [ ] User registration
- [ ] User login
- [ ] Session management
- [ ] Password hashing

### Phase 3: Core Features (Days 4-6)
- [ ] Academic schedule CRUD
- [ ] Task management CRUD
- [ ] User preferences

### Phase 4: AI Integration (Days 7-9)
- [ ] Gemini API setup
- [ ] Task priority calculation
- [ ] Study block generation algorithm
- [ ] Profile-based scheduling (Early/Seamless/Late Crammer)

### Phase 5: Calendar & UI (Days 10-12)
- [ ] Calendar display (Day/Week/Month views)
- [ ] Drag-and-drop functionality
- [ ] Responsive design

### Phase 6: Polish (Days 13-14)
- [ ] Onboarding tour
- [ ] Error handling
- [ ] Testing and bug fixes

---

## 5. AI Logic Details

### Task Priority Calculation
```
Priority = f(days_until_due, complexity, word_count)

- High Priority: Due within 3 days OR complexity > 7
- Medium Priority: Due within 7 days OR complexity 4-7
- Low Priority: Due after 7 days AND complexity < 4
```

### Complexity Calculation
```
1. Check activity name keywords:
   - exam, final, midterm: +5
   - quiz, sa, fa: +3
   - homework, hw, assignment: +2
   
2. Word count from description:
   - < 100 words: +1
   - 100-500 words: +2
   - > 500 words: +3

3. Final complexity = min(10, keyword_score + word_score)
```

### Study Block Profiles
1. **Early Crammer**: Schedule blocks from (earliest_start) to (due_date - 1 day)
2. **Seamless**: Distribute blocks evenly from (created_date) to (due_date - 2 days)
3. **Late Crammer**: Schedule blocks from (due_date - 3 days) to (due_date - 1 day)

---

## 6. API Endpoints

| Endpoint | Method | Description |
|----------|--------|-------------|
| `/api/gemini-chat.php` | POST | Chat with AI assistant |
| `/api/schedule-task.php` | POST | Generate study blocks for task |
| `/api/update-blocks.php` | POST | Reorganize after manual move |

---

## 7. Acceptance Criteria

1. ✓ Users can register with First Name, Last Name, Password
2. ✓ Users can log in and see greeting + tour
3. ✓ Users can add/edit/delete academic schedule
4. ✓ Users can add tasks with AI-calculated priority
5. ✓ Users can manually override task priority
6. ✓ AI generates study blocks based on profile
7. ✓ Study blocks don't conflict with academic schedule
8. ✓ Users can manually move study blocks
9. ✓ Calendar shows Day/Week/Month views
10. ✓ User preferences (time, duration, profile) are configurable

---

## Dependent Files to be Created

All files listed in Section 3 above need to be created. The main implementation will include:

1. Database setup script (`database.sql`)
2. Configuration files
3. All PHP backend files
4. All HTML/CSS/JS frontend files

---

## Follow-up Steps

1. **Confirm the plan** - Get user approval to proceed
2. **Set up development environment** - Ensure PHP/MySQL is available
3. **Create database** - Run database setup
4. **Implement Phase 1** - Foundation files
5. **Implement Phase 2** - Authentication
6. **Implement Phase 3** - Core features
7. **Implement Phase 4** - AI integration
8. **Implement Phase 5** - Calendar & UI
9. **Implement Phase 6** - Polish & testing

---

## Notes

- Gemini API key will be needed for AI functionality
- Calendar library (FullCalendar.js) will be used for calendar views
- Responsive design for mobile compatibility
- Need to handle session security properly

