<?php
// ============================================================
// ELEVÉ — Cart & Wishlist API
// ============================================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once __DIR__ . '/config.php';

$action = $_REQUEST['action'] ?? 'cart_get';

switch ($action) {
    // Cart
    case 'cart_get':    cartGet();    break;
    case 'cart_add':    cartAdd();    break;
    case 'cart_remove': cartRemove(); break;
    case 'cart_clear':  cartClear();  break;
    case 'cart_update': cartUpdate(); break;
    // Wishlist
    case 'wish_get':    wishGet();    break;
    case 'wish_add':    wishAdd();    break;
    case 'wish_remove': wishRemove(); break;
    // Newsletter
    case 'newsletter':  newsletter();  break;
    // Contact
    case 'contact':     contact();    break;
    // Coupon
    case 'apply_coupon': applyCoupon(); break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

// ============================================================
// CART FUNCTIONS
// ============================================================
function cartGet(): void {
    $db = getDB();
    $sid = getSessionId();
    $userId = $_SESSION['user_id'] ?? null;

    $sql = "SELECT ci.*, p.name, p.price, p.old_price,
                   (SELECT url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1) AS image
            FROM cart_items ci
            JOIN products p ON p.id = ci.product_id
            WHERE ci.session_id = ?";
    $params = [$sid];

    if ($userId) {
        $sql = "SELECT ci.*, p.name, p.price, p.old_price,
                       (SELECT url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1) AS image
                FROM cart_items ci
                JOIN products p ON p.id = ci.product_id
                WHERE (ci.session_id = ? OR ci.user_id = ?)";
        $params = [$sid, $userId];
    }

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $items = $stmt->fetchAll();

    $total = 0;
    $count = 0;
    foreach ($items as &$item) {
        $item['total'] = $item['price'] * $item['qty'];
        $total += $item['total'];
        $count += $item['qty'];
    }

    jsonResponse(['success' => true, 'items' => $items, 'total' => $total, 'count' => $count]);
}

function cartAdd(): void {
    $db         = getDB();
    $sid        = getSessionId();
    $userId     = $_SESSION['user_id'] ?? null;
    $productId  = (int) ($_POST['product_id'] ?? 0);
    $size       = sanitize($_POST['size'] ?? '');
    $color      = sanitize($_POST['color'] ?? '');
    $qty        = max(1, (int) ($_POST['qty'] ?? 1));

    if (!$productId) jsonResponse(['success' => false, 'message' => 'Product ID required'], 422);

    // Verify product exists
    $stmt = $db->prepare("SELECT id, name, stock_qty FROM products WHERE id = ? AND is_active = 1");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if (!$product) jsonResponse(['success' => false, 'message' => 'Product not found'], 404);

    // Check if already in cart
    $checkSql = "SELECT id, qty FROM cart_items WHERE session_id = ? AND product_id = ? AND size = ? AND color = ?";
    $checkParams = [$sid, $productId, $size, $color];
    $stmt = $db->prepare($checkSql);
    $stmt->execute($checkParams);
    $existing = $stmt->fetch();

    if ($existing) {
        $newQty = min($existing['qty'] + $qty, 10);
        $db->prepare("UPDATE cart_items SET qty = ? WHERE id = ?")->execute([$newQty, $existing['id']]);
    } else {
        $db->prepare("INSERT INTO cart_items (session_id, user_id, product_id, size, color, qty) VALUES (?, ?, ?, ?, ?, ?)")
           ->execute([$sid, $userId, $productId, $size, $color, $qty]);
    }

    jsonResponse(['success' => true, 'message' => $product['name'] . ' added to cart!']);
}

function cartRemove(): void {
    $db  = getDB();
    $sid = getSessionId();
    $id  = (int) ($_POST['cart_item_id'] ?? 0);

    $db->prepare("DELETE FROM cart_items WHERE id = ? AND session_id = ?")->execute([$id, $sid]);
    jsonResponse(['success' => true, 'message' => 'Item removed']);
}

function cartClear(): void {
    $db  = getDB();
    $sid = getSessionId();
    $db->prepare("DELETE FROM cart_items WHERE session_id = ?")->execute([$sid]);
    jsonResponse(['success' => true, 'message' => 'Cart cleared']);
}

function cartUpdate(): void {
    $db  = getDB();
    $sid = getSessionId();
    $id  = (int) ($_POST['cart_item_id'] ?? 0);
    $qty = max(1, min(10, (int) ($_POST['qty'] ?? 1)));

    $db->prepare("UPDATE cart_items SET qty = ? WHERE id = ? AND session_id = ?")->execute([$qty, $id, $sid]);
    jsonResponse(['success' => true, 'message' => 'Cart updated']);
}

// ============================================================
// WISHLIST FUNCTIONS
// ============================================================
function wishGet(): void {
    $db  = getDB();
    $sid = getSessionId();

    $stmt = $db->prepare("SELECT wi.id, wi.product_id, p.name, p.price,
                           (SELECT url FROM product_images WHERE product_id = p.id ORDER BY sort_order LIMIT 1) AS image
                          FROM wishlist_items wi
                          JOIN products p ON p.id = wi.product_id
                          WHERE wi.session_id = ?");
    $stmt->execute([$sid]);
    $items = $stmt->fetchAll();

    jsonResponse(['success' => true, 'items' => $items, 'count' => count($items)]);
}

function wishAdd(): void {
    $db        = getDB();
    $sid       = getSessionId();
    $userId    = $_SESSION['user_id'] ?? null;
    $productId = (int) ($_POST['product_id'] ?? 0);

    if (!$productId) jsonResponse(['success' => false, 'message' => 'Product ID required'], 422);

    $stmt = $db->prepare("SELECT name FROM products WHERE id = ? AND is_active = 1");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();
    if (!$product) jsonResponse(['success' => false, 'message' => 'Product not found'], 404);

    try {
        $db->prepare("INSERT IGNORE INTO wishlist_items (session_id, user_id, product_id) VALUES (?, ?, ?)")
           ->execute([$sid, $userId, $productId]);
        jsonResponse(['success' => true, 'message' => $product['name'] . ' added to wishlist!']);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'message' => 'Already in wishlist']);
    }
}

function wishRemove(): void {
    $db        = getDB();
    $sid       = getSessionId();
    $productId = (int) ($_POST['product_id'] ?? 0);

    $db->prepare("DELETE FROM wishlist_items WHERE session_id = ? AND product_id = ?")->execute([$sid, $productId]);
    jsonResponse(['success' => true, 'message' => 'Removed from wishlist']);
}

// ============================================================
// NEWSLETTER
// ============================================================
function newsletter(): void {
    $db    = getDB();
    $email = sanitizeEmail($_POST['email'] ?? '');

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['success' => false, 'message' => 'Please enter a valid email address'], 422);
    }

    try {
        $db->prepare("INSERT INTO newsletter_subscribers (email) VALUES (?) ON DUPLICATE KEY UPDATE is_active = 1")
           ->execute([$email]);
        jsonResponse(['success' => true, 'message' => 'Welcome! Check your inbox for 10% off 🎉']);
    } catch (Exception $e) {
        jsonResponse(['success' => false, 'message' => 'Something went wrong. Please try again.'], 500);
    }
}

// ============================================================
// CONTACT
// ============================================================
function contact(): void {
    $db      = getDB();
    $name    = sanitize($_POST['name'] ?? '');
    $email   = sanitizeEmail($_POST['email'] ?? '');
    $subject = sanitize($_POST['subject'] ?? 'General Inquiry');
    $message = sanitize($_POST['message'] ?? '');

    if (!$name || !$email || !$message) {
        jsonResponse(['success' => false, 'message' => 'All fields are required'], 422);
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        jsonResponse(['success' => false, 'message' => 'Invalid email address'], 422);
    }

    $db->prepare("INSERT INTO contact_messages (name, email, subject, message) VALUES (?, ?, ?, ?)")
       ->execute([$name, $email, $subject, $message]);

    jsonResponse(['success' => true, 'message' => "Message sent successfully! We'll be in touch soon. ✉️"]);
}

// ============================================================
// APPLY COUPON
// ============================================================
function applyCoupon(): void {
    $db     = getDB();
    $code   = strtoupper(sanitize($_POST['code'] ?? ''));
    $total  = (float) ($_POST['total'] ?? 0);

    if (!$code) jsonResponse(['success' => false, 'message' => 'Please enter a coupon code'], 422);

    $stmt = $db->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND (expires_at IS NULL OR expires_at > NOW()) AND (max_uses IS NULL OR used_count < max_uses)");
    $stmt->execute([$code]);
    $coupon = $stmt->fetch();

    if (!$coupon) {
        jsonResponse(['success' => false, 'message' => 'Invalid or expired coupon code'], 422);
    }

    if ($total < $coupon['min_order']) {
        jsonResponse(['success' => false, 'message' => "Minimum order of ₹{$coupon['min_order']} required"], 422);
    }

    $discount = $coupon['type'] === 'percent'
        ? round($total * $coupon['value'] / 100, 2)
        : min($coupon['value'], $total);

    jsonResponse([
        'success'  => true,
        'message'  => "Coupon applied! You save ₹$discount",
        'discount' => $discount,
        'code'     => $code,
        'type'     => $coupon['type'],
        'value'    => $coupon['value'],
    ]);
}
