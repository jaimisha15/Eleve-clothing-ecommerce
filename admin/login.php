<?php
if (file_exists(__DIR__ . '/../php/config.php')) {
    require_once __DIR__ . '/../php/config.php';
} else {
    require_once __DIR__ . '/../../php/config.php';
}

// If already logged in as admin, redirect directly
if (isAdmin()) {
    header('Location: index.php');
    exit;
}

$error   = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeEmail($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    if (!$email || !$pass) {
        $error = 'Please enter admin email and password.';
    } else {
        $db = getDB();

        // 1. Direct check for admin@gmail.com with password 123
        if (strtolower($email) === 'admin@gmail.com' && $pass === '123') {
            $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
            $stmt->execute(['admin@gmail.com']);
            $user = $stmt->fetch();
            $hash = password_hash('123', PASSWORD_BCRYPT, ['cost' => 12]);

            if (!$user) {
                $db->prepare("INSERT INTO users (name, email, password, role, is_active) VALUES ('Admin', 'admin@gmail.com', ?, 'admin', 1)")
                   ->execute([$hash]);
                $stmt->execute(['admin@gmail.com']);
                $user = $stmt->fetch();
            } else if (!password_verify('123', $user['password']) || $user['role'] !== 'admin' || !$user['is_active']) {
                $db->prepare("UPDATE users SET password = ?, role = 'admin', is_active = 1 WHERE email = 'admin@gmail.com'")
                   ->execute([$hash]);
                $user['role'] = 'admin';
                $user['name'] = $user['name'] ?: 'Admin';
            }

            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'] ?: 'Admin';
            $_SESSION['user_email'] = 'admin@gmail.com';
            $_SESSION['role']       = 'admin';

            header('Location: index.php');
            exit;
        }

        // 2. Database verification for any registered admin user
        $stmt = $db->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' AND is_active = 1 LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($pass, $user['password'])) {
            $_SESSION['user_id']    = $user['id'];
            $_SESSION['user_name']  = $user['name'] ?: 'Admin';
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['role']       = 'admin';
            header('Location: index.php');
            exit;
        } else {
            $error = 'Invalid admin credentials. Please use admin@gmail.com and password 123.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin Login — ELEVÉ</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
<style>
*,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
body{font-family:'Inter',-apple-system,sans-serif;background:#0a0a0a;color:#f5f0eb;min-height:100vh;display:flex;align-items:center;justify-content:center;padding:1.5rem}
.login-box{background:#141414;border:1px solid #2a2a2a;border-radius:14px;padding:2.75rem 2.25rem;width:100%;max-width:440px;box-shadow:0 25px 70px rgba(0,0,0,.75);position:relative;overflow:hidden}
.login-box::before{content:'';position:absolute;top:0;left:0;right:0;height:3px;background:linear-gradient(90deg,transparent,#c9a96e,#d4b87d,transparent)}
.brand{font-family:Georgia,serif;font-size:2rem;letter-spacing:.3em;color:#c9a96e;text-transform:uppercase;text-align:center;margin-bottom:.25rem;font-weight:700}
.brand-sub{text-align:center;font-size:.75rem;letter-spacing:.25em;color:#a09a93;text-transform:uppercase;margin-bottom:1.5rem}

.cred-badge{background:rgba(201,169,110,.1);border:1px dashed rgba(201,169,110,.4);border-radius:8px;padding:.75rem .9rem;font-size:.8rem;color:#d4b87d;text-align:center;margin-bottom:1.5rem;line-height:1.5}
.cred-badge strong{color:#fff;font-weight:600}

.form-group{margin-bottom:1.25rem}
.form-group label{display:block;font-size:.72rem;font-weight:600;text-transform:uppercase;letter-spacing:.08em;color:#a09a93;margin-bottom:.45rem}
.input-wrap{position:relative}
.input-wrap input{width:100%;padding:.85rem 1rem;background:#1e1e1e;border:1px solid #2e2e2e;border-radius:7px;color:#f5f0eb;font-size:.92rem;transition:border-color .2s,box-shadow .2s;outline:none}
.input-wrap input:focus{border-color:#c9a96e;box-shadow:0 0 0 3px rgba(201,169,110,.2)}

.btn-login{width:100%;padding:.95rem;background:linear-gradient(135deg,#c9a96e,#b89355);color:#0a0a0a;border:none;border-radius:7px;font-size:.88rem;font-weight:700;letter-spacing:.12em;text-transform:uppercase;cursor:pointer;transition:all .25s;margin-top:.75rem}
.btn-login:hover{background:linear-gradient(135deg,#d4b87d,#c9a96e);transform:translateY(-1px);box-shadow:0 6px 25px rgba(201,169,110,.35)}

.error-msg{background:rgba(231,76,60,.12);border:1px solid rgba(231,76,60,.35);border-radius:7px;padding:.8rem 1rem;font-size:.85rem;color:#ff6b6b;margin-bottom:1.25rem;display:flex;align-items:center;gap:.5rem}
.lock-icon{text-align:center;font-size:2.4rem;margin-bottom:1rem}

.footer-links{display:flex;justify-content:space-between;margin-top:1.75rem;padding-top:1.25rem;border-top:1px solid #222;font-size:.8rem}
.footer-links a{color:#a09a93;text-decoration:none;transition:color .2s}
.footer-links a:hover{color:#c9a96e}
</style>
</head>
<body>

<div class="login-box">
  <div class="lock-icon">👑</div>
  <div class="brand">ELEVÉ</div>
  <div class="brand-sub">Admin Control Center</div>

  <div class="cred-badge">
    <strong>Admin Access</strong><br>
    Email: <strong>admin@gmail.com</strong> &bull; Password: <strong>123</strong>
  </div>

  <?php if ($error): ?>
    <div class="error-msg">⚠️ <?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <form method="POST" autocomplete="off">
    <div class="form-group">
      <label for="email">Admin Email</label>
      <div class="input-wrap">
        <input type="email" id="email" name="email" placeholder="admin@gmail.com" required
               value="<?= htmlspecialchars($_POST['email'] ?? 'admin@gmail.com') ?>">
      </div>
    </div>

    <div class="form-group">
      <label for="password">Password</label>
      <div class="input-wrap">
        <input type="password" id="password" name="password" placeholder="Enter password (123)" required>
      </div>
    </div>

    <button type="submit" class="btn-login">Open Admin Panel →</button>
  </form>

  <div class="footer-links">
    <a href="../index.html">← Back to Store</a>
    <a href="../login.php">User Login</a>
  </div>
</div>

</body>
</html>
