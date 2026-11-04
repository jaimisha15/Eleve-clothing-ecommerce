<?php
// ============================================================
// ELEVÉ — Auth API (Login / Register / Logout / Check)
// POST /php/auth.php?action=login|register|logout|check
// ============================================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? ($_POST['action'] ?? 'check');

switch ($action) {
    case 'login':    doLogin();    break;
    case 'register': doRegister(); break;
    case 'logout':   doLogout();   break;
    case 'check':    checkAuth();  break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

// ============================================================
// LOGIN FUNCTION
// ============================================================
function doLogin(): void {
    $db    = getDB();
    $email = sanitizeEmail($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');

    if (!$email || !$pass) {
        jsonResponse(['success' => false, 'message' => 'Email and password are required'], 422);
    }

    // Special verification for admin: admin@gmail.com / 123
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

        jsonResponse([
            'success'  => true,
            'message'  => 'Admin authentication successful! Redirecting to Admin Panel...',
            'is_admin' => true,
            'redirect' => 'admin/index.php',
            'user'     => [
                'id'    => $admin['id'],
                'name'  => $_SESSION['user_name'],
                'email' => 'admin@gmail.com',
                'role'  => 'admin',
            ]
        ]);
    }

    // Regular user login
    $stmt = $db->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse(['success' => false, 'message' => 'No account found with this email address.'], 404);
    }

    if (!$user['is_active']) {
        jsonResponse(['success' => false, 'message' => 'This account has been deactivated. Contact support.'], 403);
    }

    if (!password_verify($pass, $user['password'])) {
        jsonResponse(['success' => false, 'message' => 'Incorrect password. Please try again.'], 401);
    }

    // Set user session
    $_SESSION['user_id']    = $user['id'];
    $_SESSION['user_name']  = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['role']       = $user['role'];

    // Merge guest cart items into user cart
    $sessionId = getSessionId();
    $db->prepare("UPDATE cart_items SET user_id = ?, session_id = ? WHERE session_id = ? AND user_id IS NULL")
       ->execute([$user['id'], $sessionId, $sessionId]);

    $isAdmin = ($user['role'] === 'admin');
    $redirectUrl = $isAdmin ? 'admin/index.php' : 'index.html';

    jsonResponse([
        'success'  => true,
        'message'  => 'Login successful! Welcome back, ' . htmlspecialchars($user['name']) . '.',
        'is_admin' => $isAdmin,
        'redirect' => $redirectUrl,
        'user'     => [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role'],
        ]
    ]);
}

// ============================================================
// REGISTER FUNCTION
// ============================================================
function doRegister(): void {
    $db    = getDB();
    $name  = sanitize($_POST['name'] ?? '');
    $email = sanitizeEmail($_POST['email'] ?? '');
    $pass  = trim($_POST['password'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');

    if (!$name || !$email || !$pass) {
        jsonResponse(['success' => false, 'message' => 'Name, email, and password are required.'], 422);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['success' => false, 'message' => 'Please enter a valid email address.'], 422);
    }

    if (strlen($pass) < 3) {
        jsonResponse(['success' => false, 'message' => 'Password must be at least 3 characters long.'], 422);
    }

    // Check if email already registered
    $stmt = $db->prepare("SELECT id FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        jsonResponse(['success' => false, 'message' => 'This email is already registered. Please sign in.'], 409);
    }

    // Hash password and store in MySQL database
    $hash = password_hash($pass, PASSWORD_BCRYPT, ['cost' => 12]);
    $stmt = $db->prepare("INSERT INTO users (name, email, password, phone, role, is_active) VALUES (?, ?, ?, ?, 'user', 1)");
    $stmt->execute([$name, $email, $hash, $phone]);
    $userId = (int) $db->lastInsertId();

    // Set logged-in session
    $_SESSION['user_id']    = $userId;
    $_SESSION['user_name']  = $name;
    $_SESSION['user_email'] = $email;
    $_SESSION['role']       = 'user';

    // Link cart
    $sessionId = getSessionId();
    $db->prepare("UPDATE cart_items SET user_id = ?, session_id = ? WHERE session_id = ? AND user_id IS NULL")
       ->execute([$userId, $sessionId, $sessionId]);

    jsonResponse([
        'success'  => true,
        'message'  => "Welcome, {$name}! Your account has been registered and data saved successfully.",
        'redirect' => 'index.html',
        'user'     => [
            'id'    => $userId,
            'name'  => $name,
            'email' => $email,
            'role'  => 'user'
        ]
    ]);
}

// ============================================================
// LOGOUT FUNCTION
// ============================================================
function doLogout(): void {
    $_SESSION = [];
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    session_destroy();
    jsonResponse(['success' => true, 'message' => 'Logged out successfully']);
}

// ============================================================
// CHECK AUTH STATUS
// ============================================================
function checkAuth(): void {
    if (isLoggedIn()) {
        jsonResponse([
            'success'   => true,
            'logged_in' => true,
            'user'      => [
                'id'    => $_SESSION['user_id'],
                'name'  => $_SESSION['user_name'] ?? 'User',
                'email' => $_SESSION['user_email'] ?? '',
                'role'  => $_SESSION['role'] ?? 'user',
            ]
        ]);
    } else {
        jsonResponse(['success' => true, 'logged_in' => false]);
    }
}
