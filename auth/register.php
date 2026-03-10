<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - DailyBrew</title>
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
        .form-row {
            display: flex;
            gap: 15px;
        }
        .form-row .form-group {
            flex: 1;
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
        .success-message {
            background: #efe;
            color: #3c3;
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
    </style>
</head>
<body>
    <div class="auth-container">
        <div class="auth-logo">
            <h1>☕ DailyBrew</h1>
            <p>Create Your Account</p>
        </div>
        
        <div id="message" style="display: none;"></div>
        
        <form id="registerForm">
            <div class="form-row">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required placeholder="John">
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required placeholder="Doe">
                </div>
            </div>
            
            <div class="form-group">
                <label for="email">Email Address</label>
                <input type="email" id="email" name="email" required placeholder="your@email.com">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required placeholder="At least 6 characters">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required placeholder="Re-enter password">
            </div>
            
            <button type="submit" class="btn">Create Account</button>
        </form>
        
        <div class="auth-links">
            <p>Already have an account? <a href="login.php">Sign In</a></p>
        </div>
    </div>

    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const firstName = document.getElementById('first_name').value.trim();
            const lastName = document.getElementById('last_name').value.trim();
            const email = document.getElementById('email').value.trim();
            const password = document.getElementById('password').value;
            const confirmPassword = document.getElementById('confirm_password').value;
            const messageDiv = document.getElementById('message');
            
            // Validation
            if (!firstName || !lastName || !email || !password) {
                showMessage('All fields are required.', 'error');
                return;
            }
            
            if (password.length < 6) {
                showMessage('Password must be at least 6 characters.', 'error');
                return;
            }
            
            if (password !== confirmPassword) {
                showMessage('Passwords do not match.', 'error');
                return;
            }
            
            // Get existing users from localStorage
            const users = JSON.parse(localStorage.getItem('dailybrew_users') || '[]');
            
            // Check if email already exists
            if (users.some(u => u.email === email)) {
                showMessage('An account with this email already exists.', 'error');
                return;
            }
            
            // Create new user
            const newUser = {
                id: Date.now(),
                firstName,
                lastName,
                email,
                password: btoa(password), // Simple encoding (not secure, for demo only)
                tourCompleted: false,
                createdAt: new Date().toISOString()
            };
            
            users.push(newUser);
            localStorage.setItem('dailybrew_users', JSON.stringify(users));
            
            // Create default preferences
            const preferences = {
                earliest_time_start: '08:00:00',
                latest_time_end: '22:00:00',
                study_block_duration: 30,
                default_profile: 'seamless'
            };
            localStorage.setItem('dailybrew_preferences_' + newUser.id, JSON.stringify(preferences));
            
            // Initialize empty data for user
            localStorage.setItem('dailybrew_tasks_' + newUser.id, JSON.stringify([]));
            localStorage.setItem('dailybrew_schedule_' + newUser.id, JSON.stringify([]));
            localStorage.setItem('dailybrew_blocks_' + newUser.id, JSON.stringify([]));
            
            showMessage('Account created successfully! Redirecting to login...', 'success');
            
            setTimeout(() => {
                window.location.href = 'login.php';
            }, 1500);
        });
        
        function showMessage(text, type) {
            const messageDiv = document.getElementById('message');
            messageDiv.textContent = text;
            messageDiv.className = type === 'error' ? 'error-message' : 'success-message';
            messageDiv.style.display = 'block';
        }
    </script>
</body>
</html>

