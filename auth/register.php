<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Create Account — DailyBrew</title>
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
      background:var(--espresso);position:relative;overflow-x:hidden;overflow-y:auto;
    }
    body::before{
      content:'';position:fixed;inset:0;pointer-events:none;
      background:radial-gradient(ellipse 80% 60% at 50% 50%, rgba(121,98,84,0.35) 0%, transparent 70%);
    }
    body::after{
      content:'';position:fixed;inset:0;pointer-events:none;opacity:0.5;
      background-image:url("data:image/svg+xml,%3Csvg viewBox='0 0 200 200' xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='0.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='0.04'/%3E%3C/svg%3E");
    }
    .auth-card{
      background:var(--white);border-radius:18px;padding:40px 40px 34px;
      width:100%;max-width:440px;
      box-shadow:0 24px 64px rgba(30,16,8,0.45),0 2px 8px rgba(30,16,8,0.2);
      position:relative;z-index:1;
      animation:riseUp 0.5s cubic-bezier(0.22,1,0.36,1) both;
      margin:20px 0;
    }
    @keyframes riseUp{from{opacity:0;transform:translateY(22px)}to{opacity:1;transform:translateY(0)}}
    .auth-card::before{
      content:'';position:absolute;top:0;left:40px;right:40px;height:2px;
      background:linear-gradient(90deg,transparent,var(--taupe-light),transparent);
    }
    .auth-brand{text-align:center;margin-bottom:26px;}
    .auth-brand-icon{
      width:48px;height:48px;border-radius:50%;background:var(--espresso);
      display:flex;align-items:center;justify-content:center;
      font-size:1.35rem;margin:0 auto 12px;
      box-shadow:0 4px 14px rgba(82,63,49,0.35);
    }
    .auth-brand h1{
      font-family:'Cormorant Garamond',serif;
      font-size:1.9rem;font-weight:600;color:var(--espresso);line-height:1;margin-bottom:5px;
    }
    .auth-brand h1 em{font-style:italic;font-weight:300;}
    .auth-brand p{font-size:0.78rem;color:var(--text-soft);letter-spacing:0.04em;}
    .auth-divider{border:none;border-top:1px solid var(--border);margin:0 0 20px;}
    .auth-message{
      display:none;padding:10px 14px;border-radius:8px;
      font-size:0.79rem;margin-bottom:16px;text-align:center;line-height:1.5;
    }
    .auth-message.error{background:rgba(139,58,58,0.08);color:var(--danger);border:1px solid rgba(139,58,58,0.16);}
    .auth-message.success{background:rgba(74,124,89,0.08);color:#3A6B4A;border:1px solid rgba(74,124,89,0.18);}
    .auth-message.visible{display:block;}
    .form-group{margin-bottom:15px;}
    .form-label{
      display:block;margin-bottom:6px;
      font-size:0.74rem;font-weight:500;color:var(--text-mid);letter-spacing:0.03em;
    }
    .form-input{
      width:100%;padding:10px 13px;
      border:1.5px solid var(--parchment2);border-radius:8px;font-size:0.86rem;
      background:var(--white);color:var(--text-dark);
      font-family:'DM Sans',sans-serif;
      transition:border-color 0.2s,box-shadow 0.2s;outline:none;
    }
    .form-input::placeholder{color:var(--neutral);opacity:0.7;}
    .form-input:focus{border-color:var(--taupe);box-shadow:0 0 0 3px rgba(157,138,124,0.13);}
    .form-row{display:flex;gap:12px;}
    .form-row .form-group{flex:1;min-width:0;}
    .password-hint{
      font-size:0.69rem;color:var(--text-soft);margin-top:4px;
    }
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
    .auth-links{text-align:center;margin-top:18px;font-size:0.77rem;color:var(--text-soft);}
    .auth-links a{color:var(--brown-mid);text-decoration:none;font-weight:500;transition:color 0.2s;}
    .auth-links a:hover{color:var(--espresso);text-decoration:underline;}
    .auth-footer-quote{
      text-align:center;margin-top:24px;margin-bottom:8px;
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
      <p>Create your account — it's free</p>
    </div>

    <hr class="auth-divider">

    <div class="auth-message" id="message"></div>

    <form id="registerForm">
      <div class="form-row">
        <div class="form-group">
          <label class="form-label" for="first_name">First Name</label>
          <input class="form-input" type="text" id="first_name" name="first_name" required
                 placeholder="John" autocomplete="given-name">
        </div>
        <div class="form-group">
          <label class="form-label" for="last_name">Last Name</label>
          <input class="form-input" type="text" id="last_name" name="last_name" required
                 placeholder="Doe" autocomplete="family-name">
        </div>
      </div>

      <div class="form-group">
        <label class="form-label" for="email">Email Address</label>
        <input class="form-input" type="email" id="email" name="email" required
               placeholder="your@email.com" autocomplete="email">
      </div>

      <div class="form-group">
        <label class="form-label" for="password">Password</label>
        <input class="form-input" type="password" id="password" name="password" required
               placeholder="At least 6 characters" autocomplete="new-password">
        <div class="password-hint">Minimum 6 characters</div>
      </div>

      <div class="form-group">
        <label class="form-label" for="confirm_password">Confirm Password</label>
        <input class="form-input" type="password" id="confirm_password" name="confirm_password" required
               placeholder="Re-enter your password" autocomplete="new-password">
      </div>

      <button type="submit" class="auth-btn">☕ Create Account</button>
    </form>

    <div class="auth-links">
      <p>Already have an account? <a href="login.php">Sign in</a></p>
    </div>
  </div>

  <p class="auth-footer-quote">"Every great study session starts with the first cup."</p>

  <script>
    document.getElementById('registerForm').addEventListener('submit', function(e) {
      e.preventDefault();

      const firstName       = document.getElementById('first_name').value.trim();
      const lastName        = document.getElementById('last_name').value.trim();
      const email           = document.getElementById('email').value.trim();
      const password        = document.getElementById('password').value;
      const confirmPassword = document.getElementById('confirm_password').value;

      if (!firstName || !lastName || !email || !password) {
        showMessage('All fields are required.', 'error'); return;
      }
      if (password.length < 6) {
        showMessage('Password must be at least 6 characters.', 'error'); return;
      }
      if (password !== confirmPassword) {
        showMessage('Passwords do not match.', 'error'); return;
      }

      const users = JSON.parse(localStorage.getItem('dailybrew_users') || '[]');
      if (users.some(u => u.email === email)) {
        showMessage('An account with this email already exists.', 'error'); return;
      }

      const newUser = {
        id: Date.now(),
        firstName, lastName, email,
        password: btoa(password),
        tourCompleted: false,
        createdAt: new Date().toISOString()
      };
      users.push(newUser);
      localStorage.setItem('dailybrew_users', JSON.stringify(users));

      // Default preferences
      localStorage.setItem('dailybrew_preferences_' + newUser.id, JSON.stringify({
        earliest_time_start: '08:00:00',
        latest_time_end: '22:00:00',
        study_block_duration: 30,
        default_profile: 'seamless'
      }));
      localStorage.setItem('dailybrew_tasks_'    + newUser.id, '[]');
      localStorage.setItem('dailybrew_schedule_' + newUser.id, '[]');
      localStorage.setItem('dailybrew_blocks_'   + newUser.id, '[]');

      showMessage('Account created! Redirecting to sign in…', 'success');
      setTimeout(() => { window.location.href = 'login.php'; }, 1500);
    });

    function showMessage(text, type) {
      const el = document.getElementById('message');
      el.textContent = text;
      el.className = 'auth-message ' + type + ' visible';
    }
  </script>
</body>
</html>
