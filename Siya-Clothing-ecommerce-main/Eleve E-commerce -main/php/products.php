<?php
// ============================================================
// ELEVÉ — Products API
// GET /php/products.php?action=list|detail|featured
// ============================================================
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/config.php';

$action = $_GET['action'] ?? 'list';
$db = getDB();

switch ($action) {
    case 'list':
        getProductList($db);
        break;
    case 'detail':
        getProductDetail($db);
        break;
    case 'featured':
        getFeaturedProducts($db);
        break;
    case 'search':
        searchProducts($db);
        break;
    default:
        jsonResponse(['success' => false, 'message' => 'Invalid action'], 400);
}

// ============================================================
function getProductList(PDO $db): void {
    $where = ['p.is_active = 1'];
    $params = [];

    // Gender filter
    if (!empty($_GET['gender'])) {
        $genders = explode(',', $_GET['gender']);
        $placeholders = implode(',', array_fill(0, count($genders), '?'));
        $where[] = "p.gender IN ($placeholders)";
        $params = array_merge($params, $genders);
    }

    // Category/subcategory filter
    if (!empty($_GET['category'])) {
        $cats = explode(',', $_GET['category']);
        $placeholders = implode(',', array_fill(0, count($cats), '?'));
        $where[] = "(p.subcategory IN ($placeholders) OR LOWER(p.category) IN ($placeholders))";
        $params = array_merge($params, $cats, $cats);
    }

    // Price filter
    if (!empty($_GET['max_price'])) {
        $where[] = "p.price <= ?";
        $params[] = (float) $_GET['max_price'];
    }

    // Tag filter (new/bestseller/sale)
    if (!empty($_GET['tag'])) {
        $tags = explode(',', $_GET['tag']);
        $tagWhere = [];
        foreach ($tags as $tag) {
            if ($tag === 'new')        $tagWhere[] = 'p.is_new_arrival = 1';
            if ($tag === 'bestseller') $tagWhere[] = 'p.is_best_seller = 1';
            if ($tag === 'sale')       $tagWhere[] = 'p.old_price IS NOT NULL';
        }
        if ($tagWhere) $where[] = '(' . implode(' OR ', $tagWhere) . ')';
    }

    // Size filter
    if (!empty($_GET['sizes'])) {
        $sizes = explode(',', $_GET['sizes']);
        $placeholders = implode(',', array_fill(0, count($sizes), '?'));
        $where[] = "p.id IN (SELECT DISTINCT product_id FROM product_sizes WHERE size IN ($placeholders))";
        $params = array_merge($params, $sizes);
    }

    // Search query
    if (!empty($_GET['q'])) {
        $where[] = "(p.name LIKE ? OR p.category LIKE ? OR p.subcategory LIKE ?)";
        $q = '%' . $_GET['q'] . '%';
        $params = array_merge($params, [$q, $q, $q]);
    }

    $whereStr = implode(' AND ', $where);

    // Sort
    $sort = match ($_GET['sort'] ?? 'featured') {
        'price-low'  => 'p.price ASC',
        'price-high' => 'p.price DESC',
        'rating'     => 'p.rating DESC',
        'newest'     => 'p.created_at DESC',
        default      => 'p.is_featured DESC, p.id ASC',
    };

    $sql = "SELECT p.*,
                   GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '|') AS images,
                   GROUP_CONCAT(DISTINCT ps.size ORDER BY ps.id SEPARATOR ',') AS sizes,
                   GROUP_CONCAT(DISTINCT CONCAT(pc.name,':',pc.hex) ORDER BY pc.id SEPARATOR '|') AS colors
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id
            LEFT JOIN product_sizes ps ON ps.product_id = p.id
            LEFT JOIN product_colors pc ON pc.product_id = p.id
            WHERE $whereStr
            GROUP BY p.id
            ORDER BY $sort
            LIMIT 100";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $products = array_map('formatProduct', $rows);
    jsonResponse(['success' => true, 'count' => count($products), 'products' => $products]);
}

// ============================================================
function getProductDetail(PDO $db): void {
    $id = (int) ($_GET['id'] ?? 0);
    if (!$id) jsonResponse(['success' => false, 'message' => 'Product ID required'], 400);

    $sql = "SELECT p.*,
                   GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '|') AS images,
                   GROUP_CONCAT(DISTINCT ps.size ORDER BY ps.id SEPARATOR ',') AS sizes,
                   GROUP_CONCAT(DISTINCT CONCAT(pc.name,':',pc.hex) ORDER BY pc.id SEPARATOR '|') AS colors
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id
            LEFT JOIN product_sizes ps ON ps.product_id = p.id
            LEFT JOIN product_colors pc ON pc.product_id = p.id
            WHERE p.id = ? AND p.is_active = 1
            GROUP BY p.id";

    $stmt = $db->prepare($sql);
    $stmt->execute([$id]);
    $row = $stmt->fetch();

    if (!$row) jsonResponse(['success' => false, 'message' => 'Product not found'], 404);

    jsonResponse(['success' => true, 'product' => formatProduct($row)]);
}

// ============================================================
function getFeaturedProducts(PDO $db): void {
    $limit = (int) ($_GET['limit'] ?? 8);

    $sql = "SELECT p.*,
                   GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '|') AS images,
                   GROUP_CONCAT(DISTINCT ps.size ORDER BY ps.id SEPARATOR ',') AS sizes,
                   GROUP_CONCAT(DISTINCT CONCAT(pc.name,':',pc.hex) ORDER BY pc.id SEPARATOR '|') AS colors
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id
            LEFT JOIN product_sizes ps ON ps.product_id = p.id
            LEFT JOIN product_colors pc ON pc.product_id = p.id
            WHERE p.is_active = 1 AND p.is_featured = 1
            GROUP BY p.id
            ORDER BY p.rating DESC
            LIMIT ?";

    $stmt = $db->prepare($sql);
    $stmt->execute([$limit]);
    $rows = $stmt->fetchAll();

    $products = array_map('formatProduct', $rows);
    jsonResponse(['success' => true, 'products' => $products]);
}

// ============================================================
function searchProducts(PDO $db): void {
    $q = sanitize($_GET['q'] ?? '');
    if (strlen($q) < 2) jsonResponse(['success' => true, 'products' => []]);

    $sql = "SELECT p.*,
                   GROUP_CONCAT(DISTINCT pi.url ORDER BY pi.sort_order SEPARATOR '|') AS images,
                   GROUP_CONCAT(DISTINCT ps.size ORDER BY ps.id SEPARATOR ',') AS sizes,
                   GROUP_CONCAT(DISTINCT CONCAT(pc.name,':',pc.hex) ORDER BY pc.id SEPARATOR '|') AS colors
            FROM products p
            LEFT JOIN product_images pi ON pi.product_id = p.id
            LEFT JOIN product_sizes ps ON ps.product_id = p.id
            LEFT JOIN product_colors pc ON pc.product_id = p.id
            WHERE p.is_active = 1 AND (p.name LIKE ? OR p.category LIKE ? OR p.subcategory LIKE ? OR p.description LIKE ?)
            GROUP BY p.id
            LIMIT 12";

    $like = "%$q%";
    $stmt = $db->prepare($sql);
    $stmt->execute([$like, $like, $like, $like]);
    $rows = $stmt->fetchAll();

    jsonResponse(['success' => true, 'products' => array_map('formatProduct', $rows)]);
}

// ============================================================
function formatProduct(array $row): array {
    $images = $row['images'] ? explode('|', $row['images']) : [];
    $sizes = $row['sizes'] ? explode(',', $row['sizes']) : [];

    $colors = [];
    if ($row['colors']) {
        foreach (explode('|', $row['colors']) as $c) {
            [$name, $hex] = explode(':', $c, 2);
            $colors[] = ['name' => $name, 'hex' => $hex];
        }
    }

    $details = $row['details'] ? explode('|', $row['details']) : [];

    return [
        'id'            => (int) $row['id'],
        'name'          => $row['name'],
        'category'      => $row['category'],
        'subcategory'   => $row['subcategory'],
        'gender'        => $row['gender'],
        'price'         => (float) $row['price'],
        'oldPrice'      => $row['old_price'] ? (float) $row['old_price'] : null,
        'badge'         => $row['badge'],
        'rating'        => (float) $row['rating'],
        'reviewsCount'  => (int) $row['reviews_count'],
        'isNewArrival'  => (bool) $row['is_new_arrival'],
        'isBestSeller'  => (bool) $row['is_best_seller'],
        'isFeatured'    => (bool) $row['is_featured'],
        'stockQty'      => (int) $row['stock_qty'],
        'description'   => $row['description'],
        'fit'           => $row['fit'],
        'details'       => $details,
        'images'        => $images,
        'sizes'         => $sizes,
        'colors'        => $colors,
    ];
}
