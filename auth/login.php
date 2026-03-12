<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sign In — DailyBrew</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,300;0,400;0,600;1,300;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
  <style>
    :root {
      --cream:#F5F0E8;--parchment:#EDE5D8;--parchment2:#E4D9C8;
      --taupe-light:#A49A98;--taupe:#9D8A7C;--brown-mid:#796254;
      --espresso:#523F31;--espresso-dk:#3D2E22;--neutral:#B1B1B1;
      --text-dark:#2E1F14;--text-mid:#5C4A3A;--text-soft:#8C7B6E;
      --white:#FDFAF6;--border:rgba(164,154,152,0.25);--danger:#8B3A3A;
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html,body{height:100%;font-family:'DM Sans',sans-serif;color:var(--text-dark);}
    body{
      min-height:100vh;display:flex;flex-direction:column;
      align-items:center;justify-content:center;padding:24px;
      background:var(--espresso);position:relative;overflow:hidden;
    }
    body::before{
      content:'';position:fixed;inset:0;pointer-events:none;
      background:radial-gradient(ellipse 80% 60% at 50% 60%, rgba(121,98,84,0.35) 0%, transparent 70%);
    }
    body::after{
      content:'';position:fixed;inset:0;pointer-events:none;opacity:0.5;
      background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
    }
    .auth-card{
      background:var(--white);border-radius:18px;padding:44px 40px 36px;
      width:100%;max-width:400px;
      box-shadow:0 24px 64px rgba(30,16,8,0.45),0 2px 8px rgba(30,16,8,0.2);
      position:relative;z-index:1;
      animation:riseUp 0.5s cubic-bezier(0.22,1,0.36,1) both;
    }
    @keyframes riseUp{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}
    .auth-card::before{
      content:'';position:absolute;top:0;left:40px;right:40px;height:2px;
      background:linear-gradient(90deg,transparent,var(--taupe-light),transparent);
    }
    .auth-brand{text-align:center;margin-bottom:28px;}
    .auth-brand-icon{
      width:50px;height:50px;border-radius:50%;background:var(--espresso);
      display:flex;align-items:center;justify-content:center;
      font-size:1.45rem;margin:0 auto 13px;
      box-shadow:0 4px 14px rgba(82,63,49,0.35);
    }
    .auth-brand h1{
      font-family:'Cormorant Garamond',serif;
      font-size:1.95rem;font-weight:600;color:var(--espresso);line-height:1;margin-bottom:5px;
    }
    .auth-brand h1 em{font-style:italic;font-weight:300;}
    .auth-brand p{font-size:0.78rem;color:var(--text-soft);letter-spacing:0.04em;}
    .auth-divider{border:none;border-top:1px solid var(--border);margin:0 0 22px;}
    .auth-message{
      display:none;padding:10px 14px;border-radius:8px;
      font-size:0.79rem;margin-bottom:16px;text-align:center;line-height:1.5;
    }
    .auth-message.error{background:rgba(139,58,58,0.08);color:var(--danger);border:1px solid rgba(139,58,58,0.16);}
    .auth-message.success{background:rgba(74,124,89,0.08);color:#3A6B4A;border:1px solid rgba(74,124,89,0.18);}
    .auth-message.visible{display:block;}
    .form-group{margin-bottom:17px;}
    .form-label{
      display:block;margin-bottom:6px;
      font-size:0.74rem;font-weight:500;color:var(--text-mid);letter-spacing:0.03em;
    }
    .form-input{
      width:100%;padding:11px 13px;
      border:1.5px solid var(--parchment2);border-radius:8px;font-size:0.87rem;
      background:var(--white);color:var(--text-dark);
      font-family:'DM Sans',sans-serif;
      transition:border-color 0.2s,box-shadow 0.2s;outline:none;
    }
    .form-input::placeholder{color:var(--neutral);opacity:0.7;}
    .form-input:focus{border-color:var(--taupe);box-shadow:0 0 0 3px rgba(157,138,124,0.13);}
    .form-row{display:flex;gap:12px;}
    .form-row .form-group{flex:1;min-width:0;}
    .auth-btn{
      width:100%;padding:12px;
      background:var(--espresso);color:var(--parchment);
      border:none;border-radius:9px;
      font-size:0.9rem;font-weight:600;cursor:pointer;
      font-family:'DM Sans',sans-serif;
      transition:all 0.2s ease;margin-top:6px;letter-spacing:0.02em;
    }
    .auth-btn:hover{background:var(--espresso-dk);transform:translateY(-1px);box-shadow:0 6px 18px rgba(82,63,49,0.35);}
    .auth-btn:active{transform:none;box-shadow:none;}
    .auth-links{text-align:center;margin-top:20px;font-size:0.77rem;color:var(--text-soft);}
    .auth-links a{color:var(--brown-mid);text-decoration:none;font-weight:500;transition:color 0.2s;}
    .auth-links a:hover{color:var(--espresso);text-decoration:underline;}
    .auth-footer-quote{
      text-align:center;margin-top:28px;
      font-family:'Cormorant Garamond',serif;font-size:0.83rem;font-style:italic;
      color:rgba(237,229,216,0.3);position:relative;z-index:1;
    }
  </style>
</head>
<body>

  <div class="auth-card">
    <div class="auth-brand">
      <div class="auth-brand-icon">☕</div>
      <h1>Daily<em>Brew</em></h1>
      <p>Welcome back — sign in to continue</p>
    </div>

    <hr class="auth-divider">

    <div class="auth-message" id="message"></div>

    <form id="loginForm">
      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input class="form-input" type="email" id="email" name="email" required
               placeholder="your@email.com" autocomplete="email">
      </div>
      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input class="form-input" type="password" id="password" name="password" required
               placeholder="Enter your password" autocomplete="current-password">
      </div>
      <button type="submit" class="auth-btn">☕ Sign In</button>
    </form>

    <div class="auth-links">
      <p>Don't have an account? <a href="register.php">Create one</a></p>
    </div>
  </div>

  <p class="auth-footer-quote">"The best ideas come with a good cup of coffee."</p>

  <script>
    try {
      if (localStorage.getItem('dailybrew_current_user')) {
        window.location.href = '../pages/dashboard.php';
      }
    } catch(e) {}

    document.getElementById('loginForm').addEventListener('submit', function(e) {
      e.preventDefault();
      const email    = document.getElementById('email').value.trim();
      const password = document.getElementById('password').value;
      if (!email || !password) { showMessage('Please enter your email and password.', 'error'); return; }
      const users = JSON.parse(localStorage.getItem('dailybrew_users') || '[]');
      const user  = users.find(u => u.email === email && u.password === btoa(password));
      if (!user) { showMessage('Incorrect email or password. Please try again.', 'error'); return; }
      localStorage.setItem('dailybrew_current_user', JSON.stringify(user));
      window.location.href = user.tourCompleted ? '../pages/dashboard.php' : '../pages/tour.php';
    });

    function showMessage(text, type) {
      const el = document.getElementById('message');
      el.textContent = text;
      el.className = 'auth-message ' + type + ' visible';
    }
  </script>
</body>
</html>
