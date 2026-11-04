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

// Handle POST login form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeEmail($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    if (!$email || !$pass) {
        $error = 'Please enter your email and password.';
    } else {
        try {
            $db = getDB();

            // 1. Check for Admin: admin@gmail.com / 123
            if (strtolower($email) === 'admin@gmail.com' && $pass === '123') {
                $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
                $stmt->execute(['admin@gmail.com']);
                $admin = $stmt->fetch();
                $targetHash = password_hash('123', PASSWORD_BCRYPT, ['cost' => 12]);

                if (!$admin) {
                    $db->prepare("INSERT INTO users (name, email, password, role, is_active) VALUES ('Admin', 'admin@gmail.com', ?, 'admin', 1)")
                       ->execute([$targetHash]);
                    $stmt->execute(['admin@gmail.com']);
                    $admin = $stmt->fetch();
                } else if (!password_verify('123', $admin['password']) || $admin['role'] !== 'admin' || !$admin['is_active']) {
                    $db->prepare("UPDATE users SET password = ?, role = 'admin', is_active = 1 WHERE email = 'admin@gmail.com'")
                       ->execute([$targetHash]);
                    $admin['role'] = 'admin';
                    $admin['name'] = $admin['name'] ?: 'Admin';
                }

                $_SESSION['user_id']    = $admin['id'];
                $_SESSION['user_name']  = $admin['name'] ?: 'Admin';
                $_SESSION['user_email'] = 'admin@gmail.com';
                $_SESSION['role']       = 'admin';

                header('Location: admin/index.php');
                exit;
            } else {
                // 2. Regular user authentication
                $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();

                if (!$user) {
                    $error = 'No account found with this email. Please register first.';
                } elseif (!$user['is_active']) {
                    $error = 'This account has been deactivated. Please contact support.';
                } elseif (!password_verify($pass, $user['password'])) {
                    $error = 'Incorrect password. Please try again.';
                } else {
                    // Successful login
                    $_SESSION['user_id']    = $user['id'];
                    $_SESSION['user_name']  = $user['name'];
                    $_SESSION['user_email'] = $user['email'];
                    $_SESSION['role']       = $user['role'];

                    // Merge guest cart
                    $sessionId = getSessionId();
                    $db->prepare("UPDATE cart_items SET user_id = ?, session_id = ? WHERE session_id = ? AND user_id IS NULL")
                       ->execute([$user['id'], $sessionId, $sessionId]);

                    $target = ($user['role'] === 'admin') ? 'admin/index.php' : 'index.html';
                    $success = "Welcome back, " . htmlspecialchars($user['name']) . "!";
                    header("Refresh: 1; url=$target");
                }
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
<title>Sign In — ELEVÉ</title>
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
  max-width:460px;
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
  margin-bottom:1.75rem;
}
.title{
  font-size:1.35rem;
  font-weight:600;
  text-align:center;
  margin-bottom:.4rem;
}
.subtitle{
  font-size:.85rem;
  color:var(--muted);
  text-align:center;
  margin-bottom:1.5rem;
}

.hint-box{
  background:rgba(201,169,110,.08);
  border:1px dashed rgba(201,169,110,.35);
  border-radius:8px;
  padding:.75rem 1rem;
  font-size:.8rem;
  color:var(--accent-hover);
  text-align:center;
  margin-bottom:1.5rem;
  line-height:1.45;
}
.hint-box strong{
  color:#fff;
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
.extra-links{
  display:flex;
  justify-content:space-between;
  width:100%;
  font-size:.8rem;
}
.extra-links a{
  color:var(--muted);
  text-decoration:none;
}
.extra-links a:hover{
  color:var(--accent);
}
</style>
</head>
<body>

<div class="auth-card">
  <a href="index.html" class="brand">ELEVÉ</a>
  <div class="brand-sub">Haute Couture &bull; Modern Luxury</div>

  <h1 class="title">Welcome Back</h1>
  <p class="subtitle">Sign in with your email and password to access your account.</p>

  <div class="hint-box">
    👑 <strong>Admin Access:</strong> <strong>admin@gmail.com</strong> &bull; Password: <strong>123</strong>
    <div style="margin-top:0.45rem;">
      <a href="admin/login.php" style="color:#c9a96e;font-size:0.82rem;font-weight:700;text-decoration:underline;">👑 Click Here to Open Admin Panel &rarr;</a>
    </div>
  </div>

  <?php if ($error): ?>
    <div class="alert alert-danger">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div class="alert alert-success">✨ <?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="off">
    <!-- Email Address -->
    <div class="form-group">
      <label for="email">Email Address</label>
      <div class="input-wrap">
        <span class="input-icon">✉️</span>
        <input type="email" id="email" name="email" placeholder="name@example.com" required
               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>
    </div>

    <!-- Password -->
    <div class="form-group">
      <label for="password">Password</label>
      <div class="input-wrap">
        <span class="input-icon">🔒</span>
        <input type="password" id="password" name="password" placeholder="••••••••" required>
        <button type="button" class="toggle-pass" onclick="togglePass('password')">👁️</button>
      </div>
    </div>

    <button type="submit" class="btn-submit">Sign In →</button>
  </form>

  <div class="auth-footer">
    <div>Don't have an account? <a href="register.php">Create an account</a></div>
    <div class="extra-links">
      <a href="index.html">← Return to Store</a>
      <a href="admin/login.php">Admin Portal 🔐</a>
    </div>
  </div>
</div>

<script>
function togglePass(id) {
  const el = document.getElementById(id);
  if (el) {
    el.type = el.type === 'password' ? 'text' : 'password';
  }
}
</script>

</body>
</html>
