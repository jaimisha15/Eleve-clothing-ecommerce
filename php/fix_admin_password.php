<?php
// ELEVÉ — Fix Admin Password to 123
// Visit: http://localhost/Eleve-clothing-ecommerce/php/fix_admin_password.php

require_once __DIR__ . '/config.php';

$db   = getDB();
$hash = password_hash('123', PASSWORD_BCRYPT, ['cost' => 12]);

$stmt = $db->prepare("INSERT INTO users (name, email, password, role, is_active) VALUES (?, ?, ?, 'admin', 1) ON DUPLICATE KEY UPDATE password = VALUES(password), role = 'admin', is_active = 1");
$stmt->execute(['Admin', 'admin@gmail.com', $hash]);

echo '<!DOCTYPE html><html><head><meta charset="utf-8"><title>Admin Password Reset</title></head><body style="background:#0a0a0a;color:#f5f0eb;font-family:monospace;padding:3rem;">';
echo '<div style="max-width:550px;margin:auto;background:#141414;border:1px solid #c9a96e;padding:2rem;border-radius:12px;box-shadow:0 10px 40px rgba(0,0,0,0.8);">';
echo '<h2 style="color:#c9a96e;margin-top:0;">✅ Admin Password Configured</h2>';
echo '<p>Admin login credentials set successfully:</p>';
echo '<table style="width:100%;margin:1.5rem 0;font-size:1.05rem;">';
echo '<tr><td style="color:#a09a93;">Email:</td><td><strong style="color:#fff;">admin@gmail.com</strong></td></tr>';
echo '<tr><td style="color:#a09a93;">Password:</td><td><strong style="color:#c9a96e;">123</strong></td></tr>';
echo '</table>';
echo '<p style="margin-top:2rem;"><a href="../admin/login.php" style="display:inline-block;padding:0.8rem 1.5rem;background:#c9a96e;color:#0a0a0a;text-decoration:none;font-weight:bold;border-radius:6px;">→ Open Admin Login</a></p>';
echo '</div></body></html>';
