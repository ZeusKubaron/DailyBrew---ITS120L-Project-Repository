<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome Tour - DailyBrew</title>
    <link rel="stylesheet" href="../css/style.css">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { 
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .tour-container {
            background: white;
            border-radius: 30px;
            padding: 50px;
            max-width: 600px;
            text-align: center;
            box-shadow: 0 30px 60px rgba(0,0,0,0.3);
        }
        
        .tour-icon {
            font-size: 5rem;
            margin-bottom: 20px;
        }
        
        .tour-container h1 {
            color: #667eea;
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .tour-container p {
            color: #666;
            font-size: 1.1rem;
            line-height: 1.6;
            margin-bottom: 30px;
        }
        
        .features {
            text-align: left;
            margin: 30px 0;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 15px;
            margin-bottom: 15px;
        }
        
        .feature-icon {
            font-size: 2rem;
            margin-right: 15px;
        }
        
        .feature-item h4 {
            color: #333;
            margin-bottom: 5px;
        }
        
        .feature-item p {
            color: #666;
            font-size: 0.9rem;
            margin: 0;
        }
        
        .btn {
            padding: 15px 50px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 50px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .skip-link {
            display: block;
            margin-top: 20px;
            color: #999;
            text-decoration: none;
        }
        
        .skip-link:hover {
            color: #667eea;
        }
        
        .step-indicator {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 30px;
        }
        
        .step-dot {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #e0e0e0;
        }
        
        .step-dot.active {
            background: #667eea;
        }
    </style>
</head>
<body>
    <div class="tour-container">
        <div class="step-indicator">
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
                <div>
                    <h4>Smart Calendar</h4>
                    <p>View your schedule in Day, Week, or Month view</p>
                </div>
            </div>
            
            <div class="feature-item">
                <span class="feature-icon">🤖</span>
                <div>
                    <h4>AI Assistant</h4>
                    <p>Gemini AI analyzes tasks and creates study blocks</p>
                </div>
            </div>
            
            <div class="feature-item">
                <span class="feature-icon">📄</span>
                <div>
                    <h4>Document Analyzer</h4>
                    <p>Upload documents for automatic task extraction</p>
                </div>
            </div>
        </div>
        
        <button class="btn" onclick="nextStep()">Next →</button>
        
        <a href="dashboard.php" class="skip-link">Skip tour, go straight to dashboard →</a>
    </div>

    <script>
        const currentUser = localStorage.getItem('dailybrew_current_user');
        if (!currentUser) {
            window.location.href = '../auth/login.php';
        }
        
        let step = 1;
        
        function nextStep() {
            const container = document.querySelector('.tour-container');
            
            if (step === 1) {
                step = 2;
                container.innerHTML = `
                    <div class="step-indicator">
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
                            <div>
                                <h4>Add Tasks</h4>
                                <p>Create tasks with deadlines - AI will analyze them</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <span class="feature-icon">2️⃣</span>
                            <div>
                                <h4>AI Analysis</h4>
                                <p>We calculate priority and complexity automatically</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <span class="feature-icon">3️⃣</span>
                            <div>
                                <h4>Study Blocks</h4>
                                <p>AI generates personalized study schedules</p>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn" onclick="nextStep()">Next →</button>
                    <a href="dashboard.php" class="skip-link">Skip tour →</a>
                `;
            } else if (step === 2) {
                step = 3;
                container.innerHTML = `
                    <div class="step-indicator">
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
                            <div>
                                <h4>Study Hours</h4>
                                <p>Set your preferred study time range</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <span class="feature-icon">📊</span>
                            <div>
                                <h4>Block Duration</h4>
                                <p>Choose how long each study session lasts</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <span class="feature-icon">🌊</span>
                            <div>
                                <h4>Study Profile</h4>
                                <p>Pick Early Crammer, Seamless, or Late Crammer</p>
                            </div>
                        </div>
                    </div>
                    
                    <button class="btn" onclick="nextStep()">Let's Go! →</button>
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

