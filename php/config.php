<?php
// ============================================================
// ELEVÉ — Database Configuration
// ============================================================

define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'eleve_ecommerce');
define('DB_CHARSET', 'utf8mb4');

define('SITE_URL', 'http://localhost/Eleve-clothing-ecommerce');
define('ADMIN_EMAIL', 'admin@gmail.com');
define('ADMIN_PASS', '123');

// Session start with global path '/' so session is shared across all folders
if (session_status() === PHP_SESSION_NONE) {
    ini_set('session.cookie_path', '/');
    $currentPath = session_save_path();
    if (empty($currentPath) || !@is_writable($currentPath)) {
        $sysTemp = sys_get_temp_dir();
        if (@is_writable($sysTemp)) {
            session_save_path($sysTemp);
        }
    }
    if (!headers_sent()) {
        session_set_cookie_params([
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax'
        ]);
    }
    @session_start();
}

// ============================================================
// Database Connection (PDO) with Auto-Initialization
// ============================================================
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            // If unknown database, create it automatically
            if ($e->getCode() == 1049 || strpos($e->getMessage(), 'Unknown database') !== false) {
                try {
                    $rootPdo = new PDO("mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, [
                        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
                    ]);
                    $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
                    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

                    // Auto-load schema if database.sql exists
                    $sqlFile = __DIR__ . '/../database.sql';
                    if (file_exists($sqlFile)) {
                        $sql = file_get_contents($sqlFile);
                        $pdo->exec($sql);
                    }
                } catch (Exception $inner) {
                    die(json_encode(['success' => false, 'message' => 'Database auto-initialization failed: ' . $inner->getMessage()]));
                }
            } else {
                die(json_encode(['success' => false, 'message' => 'Database connection failed: ' . $e->getMessage()]));
            }
        }

        // Ensure all tables and admin user exist
        ensureAllTablesAndAdmin($pdo);
    }
    return $pdo;
}

// ============================================================
// Ensure All Database Tables and Admin User (admin@gmail.com / 123)
// ============================================================
function ensureAllTablesAndAdmin(PDO $pdo): void {
    try {
        // Check if products table exists
        $hasProducts = false;
        try {
            $check = $pdo->query("SHOW TABLES LIKE 'products'")->fetch();
            $hasProducts = !empty($check);
        } catch (Exception $e) {
            $hasProducts = false;
        }

        if (!$hasProducts) {
            $sqlFile = __DIR__ . '/../database.sql';
            if (file_exists($sqlFile)) {
                $sql = file_get_contents($sqlFile);
                $pdo->exec($sql);
            }
        }

        // Ensure users table exists
        $pdo->exec("CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(150) UNIQUE NOT NULL,
            password VARCHAR(255) NOT NULL,
            phone VARCHAR(20),
            address TEXT,
            role ENUM('user', 'admin') DEFAULT 'user',
            is_active TINYINT(1) DEFAULT 1,
            created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB");

        // Check and configure admin user: admin@gmail.com / 123
        $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ? LIMIT 1");
        $stmt->execute(['admin@gmail.com']);
        $admin = $stmt->fetch();
        $targetHash = password_hash('123', PASSWORD_BCRYPT, ['cost' => 12]);

        if (!$admin) {
            $pdo->prepare("INSERT INTO users (name, email, password, role, is_active) VALUES ('Admin', 'admin@gmail.com', ?, 'admin', 1)")
                ->execute([$targetHash]);
        } else if (!password_verify('123', $admin['password'])) {
            $pdo->prepare("UPDATE users SET password = ?, role = 'admin', is_active = 1 WHERE email = 'admin@gmail.com'")
                ->execute([$targetHash]);
        }
    } catch (Exception $e) {
        // Silently continue
    }
}

// ============================================================
// JSON Response Helper
// ============================================================
function jsonResponse(array $data, int $code = 200): void {
    http_response_code($code);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($data);
    exit;
}

// ============================================================
// Auth Helpers
// ============================================================
function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function isAdmin(): bool {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function requireAdmin(): void {
    if (!isAdmin()) {
        $curDir = basename(dirname($_SERVER['PHP_SELF']));
        if ($curDir === 'admin') {
            header('Location: login.php');
        } else {
            header('Location: admin/login.php');
        }
        exit;
    }
}

function getSessionId(): string {
    if (empty($_SESSION['cart_session'])) {
        $_SESSION['cart_session'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['cart_session'];
}

// ============================================================
// Input Sanitization
// ============================================================
function sanitize(string $input): string {
    return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
}

function sanitizeEmail(string $email): string {
    return filter_var(trim($email), FILTER_SANITIZE_EMAIL);
}
