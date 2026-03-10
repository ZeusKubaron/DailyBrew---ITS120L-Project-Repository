<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - DailyBrew</title>
    <link rel="stylesheet" href="../css/style.css">
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
        .auth-container {
            background: white;
            border-radius: 20px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 40px;
            width: 100%;
            max-width: 400px;
        }
        .auth-logo {
            text-align: center;
            margin-bottom: 30px;
        }
        .auth-logo h1 {
            color: #667eea;
            font-size: 2.5rem;
            margin: 0;
        }
        .auth-logo p {
            color: #666;
            margin: 5px 0 0;
        }
        .form-group {
            margin-bottom: 20px;
        }
        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 500;
        }
        .form-group input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 1rem;
            transition: border-color 0.3s;
            box-sizing: border-box;
        }
        .form-group input:focus {
            outline: none;
            border-color: #667eea;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 10px;
            font-size: 1.1rem;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.4);
        }
        .error-message {
            background: #fee;
            color: #c33;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }
        .auth-links {
            text-align: center;
            margin-top: 20px;
        }
        .auth-links a {
            color: #667eea;
            text-decoration: none;
            font-weight: 500;
        }
        .auth-links a:hover {
            text-decoration: underline;
        }
        .welcome-text {
            text-align: center;
            color: #666;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-logo">
            <h1>☕ DailyBrew</h1>
            <p>Welcome Back!</p>
        </div>
        
        <p class="welcome-text">Sign in to manage your study schedule</p>
        
        <div id="message" style="display: none;"></div>
        
        <form id="loginForm">
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="your@email.com">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="Enter your password">
            </div>
            
            <button type="submit" class="btn">Sign In</button>
        </form>
        
        <div class="auth-links">
            <p>Don't have an account? <a href="register.php">Sign Up</a></p>
        </div>
    </div>

    <script>
        // Check if already logged in
        const currentUser = localStorage.getItem('dailybrew_current_user');
        if (currentUser) {
            const user = JSON.parse(currentUser);
            window.location.href = '../pages/dashboard.php';
        }
        
        document.getElementById('loginForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const messageDiv = document.getElementById('message');
            
            if (!email || !password) {
                showMessage('Please enter email and password.', 'error');
                return;
            }
            
            // Get users from localStorage
            const users = JSON.parse(localStorage.getItem('dailybrew_users') || '[]');
            
            // Find user
            const user = users.find(u => u.email === email && u.password === btoa(password));
            
            if (!user) {
                showMessage('Invalid email or password.', 'error');
                return;
            }
            
            // Set current user
            localStorage.setItem('dailybrew_current_user', JSON.stringify(user));
            
            // Redirect to tour or dashboard
            if (!user.tourCompleted) {
                window.location.href = '../pages/tour.php';
            } else {
                window.location.href = '../pages/dashboard.php';
            }
        });
        
        function showMessage(text, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = text;
            messageDiv.className = 'error-message';
            messageDiv.style.display = 'block';
        }
    </script>
</body>
</html>

