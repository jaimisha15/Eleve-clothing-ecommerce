<?php
require_once __DIR__ . '/php/config.php';

// If already logged in
if (isLoggedIn()) {
    if (isAdmin()) {
        header('Location: admin/index.php');
    } else {
        header('Location: index.html');
    }
    exit;
}

$error   = '';
$success = '';

// Handle standard POST form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name     = sanitize($_POST['name'] ?? '');
    $email    = sanitizeEmail($_POST['email'] ?? '');
    $phone    = sanitize($_POST['phone'] ?? '');
    $pass     = trim($_POST['password'] ?? '');
    $confirm  = trim($_POST['confirm_password'] ?? '');

    if (!$name || !$email || !$pass) {
        $error = 'Please fill in all required fields (Name, Email, Password).';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } elseif (strlen($pass) < 3) {
        $error = 'Password must be at least 3 characters long.';
    } elseif ($confirm && $pass !== $confirm) {
        $error = 'Passwords do not match. Please verify.';
    } else {
        try {
            $db = getDB();

            // Check if email already registered
            $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = 'An account with this email address already exists. Please sign in instead.';
            } else {
                // Securely hash password and save user data to database
                $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
                $insert = $db->prepare("INSERT INTO users (name, email, password, phone, role, is_active) VALUES (?, ?, ?, ?, 'user', 1)");
                $insert->execute([$name, $email, $hash, $phone]);
                $userId = (int) $db->lastInsertId();

                // Start authenticated user session
                $_SESSION['user_id']    = $userId;
                $_SESSION['user_name']  = $name;
                $_SESSION['user_email'] = $email;
                $_SESSION['role']       = 'user';

                // Link guest cart if any
                $sessionId = getSessionId();
                $db->prepare("UPDATE cart_items SET user_id = ?, session_id = ? WHERE session_id = ? AND user_id IS NULL")
                   ->execute([$userId, $sessionId, $sessionId]);

                $success = "Registration successful! Welcome to ELEVÉ, $name.";
                header("Refresh: 1.5; url=index.html");
            }
        } catch (Exception $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create an Account — ELEVÉ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
:root{
  --bg:#0a0a0a;
  --surface:#141414;
  --surface2:#1e1e1e;
  --border:#282828;
  --text:#f5f0eb;
  --muted:#9e9890;
  --accent:#c9a96e;
  --accent-hover:#d4b87d;
  --success:#2ecc71;
  --danger:#e74c3c;
}
body{
  font-family:'Inter',-apple-system,sans-serif;
  background:radial-gradient(ellipse at top,#1a1815 0%,#0a0a0a 70%);
  color:var(--text);
  min-height:100vh;
  display:flex;
  align-items:center;
  justify-content:center;
  padding:2rem 1.25rem;
}
.auth-card{
  background:var(--surface);
  border:1px solid var(--border);
  border-radius:16px;
  padding:3rem 2.5rem;
  width:100%;
  max-width:480px;
  box-shadow:0 30px 80px rgba(0,0,0,.8);
  position:relative;
  overflow:hidden;
}
.auth-card::before{
  content:'';
  position:absolute;
  top:0;left:0;right:0;
  height:3px;
  background:linear-gradient(90deg,transparent,var(--accent),#d4b87d,transparent);
}
.brand{
  font-family:Georgia,serif;
  font-size:2.2rem;
  letter-spacing:.3em;
  color:var(--accent);
  text-transform:uppercase;
  text-align:center;
  margin-bottom:.3rem;
  font-weight:700;
  display:block;
  text-decoration:none;
}
.brand-sub{
  text-align:center;
  font-size:.78rem;
  letter-spacing:.25em;
  color:var(--muted);
  text-transform:uppercase;
  margin-bottom:2rem;
}
.title{
  font-size:1.35rem;
  font-weight:600;
  text-align:center;
  margin-bottom:.5rem;
}
.subtitle{
  font-size:.85rem;
  color:var(--muted);
  text-align:center;
  margin-bottom:2rem;
  line-height:1.4;
}

.alert{
  padding:.85rem 1.1rem;
  border-radius:8px;
  font-size:.88rem;
  margin-bottom:1.5rem;
  display:flex;
  align-items:center;
  gap:.6rem;
  line-height:1.4;
}
.alert-danger{
  background:rgba(231,76,60,.12);
  border:1px solid rgba(231,76,60,.35);
  color:#ff6b6b;
}
.alert-success{
  background:rgba(46,204,113,.12);
  border:1px solid rgba(46,204,113,.35);
  color:#2ecc71;
}

.form-group{
  margin-bottom:1.25rem;
}
.form-group label{
  display:flex;
  justify-content:space-between;
  font-size:.75rem;
  font-weight:600;
  text-transform:uppercase;
  letter-spacing:.08em;
  color:var(--muted);
  margin-bottom:.5rem;
}
.form-group label span.req{
  color:var(--accent);
}
.input-wrap{
  position:relative;
}
.input-wrap input{
  width:100%;
  padding:.85rem 1rem .85rem 2.6rem;
  background:var(--surface2);
  border:1px solid var(--border);
  border-radius:8px;
  color:var(--text);
  font-size:.92rem;
  transition:all .2s;
  outline:none;
}
.input-wrap input:focus{
  border-color:var(--accent);
  box-shadow:0 0 0 3px rgba(201,169,110,.18);
}
.input-icon{
  position:absolute;
  left:.9rem;
  top:50%;
  transform:translateY(-50%);
  font-size:1rem;
  opacity:.7;
  pointer-events:none;
}
.toggle-pass{
  position:absolute;
  right:.9rem;
  top:50%;
  transform:translateY(-50%);
  background:none;
  border:none;
  color:var(--muted);
  cursor:pointer;
  font-size:1rem;
  padding:.2rem;
}
.toggle-pass:hover{
  color:var(--text);
}

.btn-submit{
  width:100%;
  padding:1rem;
  background:linear-gradient(135deg,var(--accent),#b89355);
  color:#0a0a0a;
  border:none;
  border-radius:8px;
  font-size:.9rem;
  font-weight:700;
  letter-spacing:.12em;
  text-transform:uppercase;
  cursor:pointer;
  transition:all .25s;
  margin-top:.75rem;
  box-shadow:0 4px 15px rgba(201,169,110,.25);
}
.btn-submit:hover{
  background:linear-gradient(135deg,var(--accent-hover),var(--accent));
  transform:translateY(-1px);
  box-shadow:0 6px 25px rgba(201,169,110,.4);
}

.auth-footer{
  display:flex;
  flex-direction:column;
  align-items:center;
  gap:1rem;
  margin-top:2rem;
  padding-top:1.5rem;
  border-top:1px solid var(--border);
  font-size:.85rem;
  color:var(--muted);
}
.auth-footer a{
  color:var(--accent);
  text-decoration:none;
  font-weight:600;
  transition:color .2s;
}
.auth-footer a:hover{
  color:var(--accent-hover);
  text-decoration:underline;
}
.back-store{
  color:var(--muted) !important;
  font-weight:normal !important;
  font-size:.8rem;
}
.back-store:hover{
  color:var(--text) !important;
}
</style>
</head>
<body>

<div class="auth-card">
  <a href="index.html" class="brand">ELEVÉ</a>
  <div class="brand-sub">Haute Couture &bull; Modern Luxury</div>

  <h1 class="title">Create Your Account</h1>
  <p class="subtitle">Join ELEVÉ to enjoy seamless shopping, order tracking, and exclusive fashion benefits.</p>

  <?php if ($error): ?>
    <div class="alert alert-danger" id="errorBox">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success" id="successBox">✨ <?= htmlspecialchars($success) ?><br><small>Redirecting to store...</small></div>
  <?php endif; ?>

  <form method="POST" id="regForm" autocomplete="off">
    <!-- Full Name -->
    <div class="form-group">
      <label for="name">Full Name <span class="req">*</span></label>
      <div class="input-wrap">
        <span class="input-icon">👤</span>
        <input type="text" id="name" name="name" placeholder="John Doe" required
               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>">
      </div>
    </div>

    <!-- Email Address -->
    <div class="form-group">
      <label for="email">Email Address <span class="req">*</span></label>
      <div class="input-wrap">
        <span class="input-icon">✉️</span>
        <input type="email" id="email" name="email" placeholder="name@example.com" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
    </div>

    <!-- Phone Number -->
    <div class="form-group">
      <label for="phone">Phone Number</label>
      <div class="input-wrap">
        <span class="input-icon">📞</span>
        <input type="tel" id="phone" name="phone" placeholder="+91 98765 43210"
               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
      </div>
    </div>

    <!-- Password -->
    <div class="form-group">
      <label for="password">Password <span class="req">*</span></label>
      <div class="input-wrap">
        <span class="input-icon">🔒</span>
        <input type="password" id="password" name="password" placeholder="At least 3 characters" required>
        <button type="button" class="toggle-pass" onclick="togglePass('password')">👁️</button>
      </div>
    </div>

    <!-- Confirm Password -->
    <div class="form-group">
      <label for="confirm_password">Confirm Password <span class="req">*</span></label>
      <div class="input-wrap">
        <span class="input-icon">🔒</span>
        <input type="password" id="confirm_password" name="confirm_password" placeholder="Re-enter password" required>
        <button type="button" class="toggle-pass" onclick="togglePass('confirm_password')">👁️</button>
      </div>
    </div>

    <button type="submit" class="btn-submit" id="submitBtn">Register Account →</button>
  </form>

  <div class="auth-footer">
    <div>Already have an account? <a href="login.php">Sign In</a></div>
    <a href="index.html" class="back-store">← Return to Store</a>
  </div>
</div>

<script>
function togglePass(id) {
  const el = document.getElementById(id);
  if (el) {
    el.type = el.type === 'password' ? 'text' : 'password';
  }
}

// Client-side quick validation
document.getElementById('regForm').addEventListener('submit', function(e) {
  const p1 = document.getElementById('password').value;
  const p2 = document.getElementById('confirm_password').value;
  if (p1 !== p2) {
    e.preventDefault();
    alert('Passwords do not match. Please verify.');
  }
});
</script>

</body>
</html>
