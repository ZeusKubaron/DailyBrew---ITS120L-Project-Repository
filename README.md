# DailyBrew - AI Student Scheduler

> **IMPORTANT: Follow these steps to run the app**

---

## 🚀 HOW TO RUN

### Step 1: Download PHP
1. Go to: **https://windows.php.net/download**
2. Click **"PHP 8.x"** (look for the Thread Safe ZIP)
3. Download: `php-8.x.x-Win32-vs16-x64.zip`

### Step 2: Extract PHP
1. Create a new folder called `php` in this project folder
2. Extract all files from the ZIP into the `php` folder

### Step 3: Run the App
**Double-click `run.bat`**

The app will open automatically in your browser at http://localhost:8000

---

## 📁 FOLDER STRUCTURE

After extracting PHP, your folder should look like this:

```
DailyBrew/
├── php/                    ← PHP files go here
│   ├── php.exe             ← IMPORTANT!
│   └── ...
├── run.bat                 ← Double-click this!
├── index.php
├── pages/
├── api/
└── ...
```

---

## ✅ FEATURES

- 📝 Task Management with AI Priority
- 📅 Calendar (Day/Week/Month views)
- 📚 AI-Generated Study Blocks
- 📄 Document Analyzer (PDF, DOCX support)
- ⚙️ Customizable Settings

---

## 🔧 TROUBLESHOOTING

### "PHP not found" error?
- Make sure you extracted PHP into the `php` folder
- The `php.exe` file must be inside `php/`

### Port 8000 is busy?
1. Right-click `run.bat` → Open with Notepad
2. Change `localhost:8000` to `localhost:8080`
3. Save and run again

### App won't open?
1. Press Win + R
2. Type `cmd` and press Enter
3. Navigate to this folder: `cd "C:\Path\To\DailyBrew"`
4. Run: `run.bat`

---

## 📝 NOTES

- All data is stored in your browser (localStorage) - no database needed!
- AI features work automatically (already configured)
- For AI chat, you need internet connection

---

**Double-click `run.bat` to start!** 🎉

