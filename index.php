<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DailyBrew - AI Student Scheduler</title>
    <link rel="stylesheet" href="css/style.css">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            padding: 20px;
        }
        .landing-container {
            text-align: center;
            color: white;
        }
        .landing-container h1 {
            font-size: 4rem;
            margin-bottom: 10px;
        }
        .landing-container p {
            font-size: 1.5rem;
            margin-bottom: 40px;
            opacity: 0.9;
        }
        .landing-buttons {
            display: flex;
            gap: 20px;
            justify-content: center;
        }
        .btn {
            padding: 15px 40px;
            font-size: 1.2rem;
            border-radius: 50px;
            text-decoration: none;
            font-weight: 600;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        .btn-primary {
            background: white;
            color: #667eea;
        }
        .btn-secondary {
            background: transparent;
            color: white;
            border: 2px solid white;
        }
        .btn:hover {
            transform: translateY(-3px);
            box-shadow: 0 10px 30px rgba(0,0,0,0.3);
        }
        .features {
            margin-top: 60px;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 30px;
            max-width: 900px;
        }
        .feature-card {
            background: rgba(255,255,255,0.1);
            padding: 30px;
            border-radius: 20px;
            backdrop-filter: blur(10px);
        }
        .feature-card h3 {
            margin: 0 0 10px;
        }
        .feature-card p {
            font-size: 1rem;
            opacity: 0.8;
        }
    </style>
</head>
<body>
    <div class="landing-container">
        <h1>☕ DailyBrew</h1>
        <p>Your AI-Powered Study Scheduler</p>
        
        <div class="landing-buttons">
            <a href="auth/login.php" class="btn btn-primary">Sign In</a>
            <a href="auth/register.php" class="btn btn-secondary">Sign Up</a>
        </div>
        
        <div class="features">
            <div class="feature-card">
                <h3>🤖 AI Assistant</h3>
                <p>Gemini AI helps schedule your tasks and deadlines</p>
            </div>
            <div class="feature-card">
                <h3>📅 Smart Calendar</h3>
                <p>Day, Week, and Month views to track your schedule</p>
            </div>
            <div class="feature-card">
                <h3>📚 Study Blocks</h3>
                <p>AI-generated study periods based on your deadlines</p>
            </div>
        </div>
    </div>

    <script>
        // Check if user is already logged in
        const currentUser = localStorage.getItem('dailybrew_current_user');
        if (currentUser) {
            const user = JSON.parse(currentUser);
            if (user.tourCompleted) {
                window.location.href = 'pages/dashboard.php';
            } else {
                window.location.href = 'pages/tour.php';
            }
        }
    </script>
</body>
</html>

