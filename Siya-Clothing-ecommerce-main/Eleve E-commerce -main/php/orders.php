<?php
// ============================================================
// ELEVÉ — Orders API
// ============================================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if (($_SERVER['REQUEST_METHOD'] ?? '') === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/config.php';

$action = $_REQUEST['action'] ?? 'place';

switch ($action) {
    case 'place':   placeOrder();   break;
    case 'track':   trackOrder();   break;
    case 'history': orderHistory(); break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

// ============================================================
function placeOrder(): void {
    $db  = getDB();
    $sid = getSessionId();
    $userId = $_SESSION['user_id'] ?? null;

    $cartItems = [];

    // 1. First, check if items were sent directly from the client (e.g. from localStorage)
    if (!empty($_POST['items'])) {
        $raw = is_string($_POST['items']) ? json_decode($_POST['items'], true) : $_POST['items'];
        if (is_array($raw)) {
            foreach ($raw as $it) {
                if (!is_array($it)) continue;
                $name = trim($it['name'] ?? '');
                $pid  = (int)($it['id'] ?? 0);
                if (!$name && !$pid) continue;

                $qty   = max(1, (int)($it['qty'] ?? 1));
                $price = (float)($it['price'] ?? 0);

                // If pid is 0 or invalid, look up product in DB by name
                if ($pid <= 0 && $name) {
                    $stmtP = $db->prepare("SELECT id, price, stock_qty FROM products WHERE name = ? LIMIT 1");
                    $stmtP->execute([$name]);
                    $matched = $stmtP->fetch();
                    if ($matched) {
                        $pid = (int)$matched['id'];
                        if ($price <= 0) $price = (float)$matched['price'];
                    }
                }

                $cartItems[] = [
                    'product_id' => $pid > 0 ? $pid : null,
                    'name'       => sanitize($name ?: 'Fashion Product'),
                    'price'      => $price,
                    'image'      => sanitize($it['img'] ?? ''),
                    'size'       => sanitize($it['size'] ?? 'M'),
                    'color'      => sanitize($it['color'] ?? ''),
                    'qty'        => $qty,
                ];
            }
        }
    }

    // 2. If client items were empty, check server-side cart_items (by session_id or user_id)
    if (empty($cartItems)) {
        $sql = "SELECT ci.*, p.name, p.price, p.stock_qty,
                       (SELECT url FROM product_images WHERE product_id = p.id LIMIT 1) AS image
                FROM cart_items ci JOIN products p ON p.id = ci.product_id
                WHERE ci.session_id = ?";
        $params = [$sid];
        if ($userId) {
            $sql .= " OR (ci.user_id IS NOT NULL AND ci.user_id = ?)";
            $params[] = $userId;
        }

        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $dbItems = $stmt->fetchAll();

        foreach ($dbItems as $item) {
            $cartItems[] = [
                'product_id' => (int)$item['product_id'],
                'name'       => $item['name'],
                'price'      => (float)$item['price'],
                'image'      => $item['image'] ?? '',
                'size'       => $item['size'] ?? 'M',
                'color'      => $item['color'] ?? '',
                'qty'        => (int)$item['qty'],
            ];
        }
    }

    if (empty($cartItems)) {
        jsonResponse(['success' => false, 'message' => 'Your cart is empty. Please add items to checkout.'], 422);
    }

    // Validate shipping info
    $name    = sanitize($_POST['name'] ?? '');
    $email   = sanitizeEmail($_POST['email'] ?? '');
    $phone   = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $city    = sanitize($_POST['city'] ?? '');
    $state   = sanitize($_POST['state'] ?? '');
    $pincode = sanitize($_POST['pincode'] ?? '');
    $payMethod = sanitize($_POST['payment_method'] ?? 'cod');

    // Auto-fill from session if logged in and missing
    if (!$name && !empty($_SESSION['user_name'])) {
        $name = $_SESSION['user_name'];
    }
    if (!$email && !empty($_SESSION['user_email'])) {
        $email = $_SESSION['user_email'];
    }

    if (!$name || !$email || !$phone || !$address || !$city || !$pincode) {
        jsonResponse(['success' => false, 'message' => 'All shipping fields (Name, Email, Phone, Address, City, Pincode) are required.'], 422);
    }

    // Calculate totals
    $subtotal = 0;
    foreach ($cartItems as $item) {
        $subtotal += $item['price'] * $item['qty'];
    }

    $shippingFee = $subtotal >= 999 ? 0 : 99;
    $discount    = max(0, (float) ($_POST['discount'] ?? 0));
    $total       = max(0, $subtotal + $shippingFee - $discount);

    // Generate order number
    $orderNumber = 'ELV' . strtoupper(substr(uniqid(), -6)) . date('d');

    // Use transaction for database updates
    try {
        $db->beginTransaction();

        $paymentStatus = ($payMethod === 'cod') ? 'pending' : 'paid';

        // Insert order
        $stmt = $db->prepare("INSERT INTO orders 
            (order_number, user_id, guest_email, status, payment_status, payment_method, 
             subtotal, shipping_fee, discount, total, 
             shipping_name, shipping_email, shipping_phone, shipping_address, shipping_city, shipping_state, shipping_pincode) 
            VALUES (?, ?, ?, 'confirmed', ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmt->execute([
            $orderNumber,
            $userId,
            $userId ? null : $email,
            $paymentStatus,
            $payMethod,
            $subtotal,
            $shippingFee,
            $discount,
            $total,
            $name,
            $email,
            $phone,
            $address,
            $city,
            $state,
            $pincode
        ]);
        $orderId = (int)$db->lastInsertId();

        // Prepare order items insert
        $itemStmt = $db->prepare("INSERT INTO order_items 
            (order_id, product_id, product_name, product_image, size, color, qty, price, total) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");

        $stmtCheckP = $db->prepare("SELECT id FROM products WHERE id = ? LIMIT 1");
        $stmtUpdateStock = $db->prepare("UPDATE products SET stock_qty = GREATEST(0, stock_qty - ?) WHERE id = ?");

        foreach ($cartItems as $item) {
            $pid = !empty($item['product_id']) ? (int)$item['product_id'] : null;

            // Verify if pid exists in DB products table to avoid FK violations
            if ($pid !== null) {
                $stmtCheckP->execute([$pid]);
                if (!$stmtCheckP->fetch()) {
                    $pid = null; // graceful fallback if product row is not in DB
                }
            }

            $itemStmt->execute([
                $orderId,
                $pid,
                $item['name'],
                $item['image'],
                $item['size'],
                $item['color'],
                $item['qty'],
                $item['price'],
                $item['price'] * $item['qty']
            ]);

            // Update stock if pid exists
            if ($pid !== null) {
                $stmtUpdateStock->execute([$item['qty'], $pid]);
            }
        }

        // Clear cart from DB
        if ($userId) {
            $db->prepare("DELETE FROM cart_items WHERE session_id = ? OR user_id = ?")->execute([$sid, $userId]);
        } else {
            $db->prepare("DELETE FROM cart_items WHERE session_id = ?")->execute([$sid]);
        }

        $db->commit();

        jsonResponse([
            'success'      => true,
            'message'      => "Order placed successfully! 🎉",
            'order_number' => $orderNumber,
            'total'        => $total,
            'order_id'     => $orderId,
        ]);
    } catch (Exception $e) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        jsonResponse([
            'success' => false,
            'message' => 'Failed to place order: ' . $e->getMessage()
        ], 500);
    }
}

// ============================================================
function trackOrder(): void {
    $db          = getDB();
    $orderNumber = sanitize($_GET['order_number'] ?? ($_POST['order_number'] ?? ''));
    $email       = sanitizeEmail($_GET['email'] ?? ($_POST['email'] ?? ''));

    if (!$orderNumber) {
        jsonResponse(['success' => false, 'message' => 'Order number required'], 422);
    }

    $sql = "SELECT o.*, COUNT(oi.id) AS item_count FROM orders o
            LEFT JOIN order_items oi ON oi.order_id = o.id
            WHERE o.order_number = ?";
    $params = [$orderNumber];

    if ($email) {
        $sql .= " AND (o.shipping_email = ? OR o.guest_email = ?)";
        $params[] = $email;
        $params[] = $email;
    }
    $sql .= " GROUP BY o.id LIMIT 1";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $order = $stmt->fetch();

    if (!$order) {
        jsonResponse(['success' => false, 'message' => 'Order not found with the provided details'], 404);
    }

    // Get order items
    $stmt = $db->prepare("SELECT * FROM order_items WHERE order_id = ?");
    $stmt->execute([$order['id']]);
    $items = $stmt->fetchAll();

    jsonResponse(['success' => true, 'order' => $order, 'items' => $items]);
}

// ============================================================
function orderHistory(): void {
    $userId = $_SESSION['user_id'] ?? null;
    $email  = sanitizeEmail($_GET['email'] ?? ($_SESSION['user_email'] ?? ''));

    if (!$userId && !$email) {
        jsonResponse(['success' => false, 'message' => 'Login required to view order history', 'orders' => []], 401);
    }

    $db = getDB();
    if ($userId) {
        $stmt = $db->prepare("SELECT o.*, COUNT(oi.id) AS item_count FROM orders o
                              LEFT JOIN order_items oi ON oi.order_id = o.id
                              WHERE o.user_id = ? OR (o.shipping_email != '' AND o.shipping_email = ?)
                              GROUP BY o.id ORDER BY o.created_at DESC");
        $stmt->execute([$userId, $email]);
    } else {
        $stmt = $db->prepare("SELECT o.*, COUNT(oi.id) AS item_count FROM orders o
                              LEFT JOIN order_items oi ON oi.order_id = o.id
                              WHERE o.shipping_email = ? OR o.guest_email = ?
                              GROUP BY o.id ORDER BY o.created_at DESC");
        $stmt->execute([$email, $email]);
    }
    $orders = $stmt->fetchAll();

    jsonResponse(['success' => true, 'orders' => $orders]);
}
