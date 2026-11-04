<?php
/**
 * ELEVÉ — CLI Installer
 * Runs directly via PHP without browser
 * Usage: php cli_install.php
 */

echo "\n";
echo "========================================\n";
echo "  ELEVÉ — Auto Installer (CLI)\n";
echo "========================================\n\n";

$host = 'localhost';
$user = 'root';
$pass = '';
$dbName = 'eleve_ecommerce';

function step($n, $title) {
    echo "Step $n: $title ... ";
}

function ok($msg) {
    echo "[OK] $msg\n";
}

function fail($msg) {
    echo "[FAIL] $msg\n";
    exit(1);
}

// ── Step 1: Connect ──────────────────────────────────────────
step(1, 'Connect to MySQL');
try {
    $pdo = new PDO("mysql:host=$host;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    ok('MySQL connected on localhost');
} catch (Exception $e) {
    fail('Cannot connect: ' . $e->getMessage());
}

// ── Step 2: Create database ──────────────────────────────────
step(2, 'Create database');
try {
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `$dbName` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    ok("Database `$dbName` created");
} catch (Exception $e) {
    fail($e->getMessage());
}

// ── Step 3: Create tables ────────────────────────────────────
step(3, 'Create tables');
$pdo = new PDO("mysql:host=$host;dbname=$dbName;charset=utf8mb4", $user, $pass, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);

$ddl = <<<SQL
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    phone VARCHAR(20),
    address TEXT,
    role ENUM('user','admin') DEFAULT 'user',
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    gender ENUM('men','women','kids','unisex','') DEFAULT '',
    image_url VARCHAR(500),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(100) NOT NULL,
    subcategory VARCHAR(100),
    gender ENUM('men','women','kids','unisex','') DEFAULT '',
    price DECIMAL(10,2) NOT NULL,
    old_price DECIMAL(10,2) DEFAULT NULL,
    badge VARCHAR(50) DEFAULT NULL,
    rating DECIMAL(3,2) DEFAULT 4.5,
    reviews_count INT DEFAULT 0,
    is_new_arrival TINYINT(1) DEFAULT 0,
    is_best_seller TINYINT(1) DEFAULT 0,
    is_featured TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    stock_qty INT DEFAULT 100,
    description TEXT,
    fit TEXT,
    details TEXT,
    slug VARCHAR(200),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    url VARCHAR(500) NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size VARCHAR(20) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS product_colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    hex VARCHAR(10) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    user_id INT DEFAULT NULL,
    guest_email VARCHAR(150),
    status ENUM('pending','confirmed','processing','shipped','delivered','cancelled','refunded') DEFAULT 'pending',
    payment_status ENUM('pending','paid','failed','refunded') DEFAULT 'pending',
    payment_method VARCHAR(50) DEFAULT 'cod',
    subtotal DECIMAL(10,2) NOT NULL,
    shipping_fee DECIMAL(10,2) DEFAULT 0.00,
    discount DECIMAL(10,2) DEFAULT 0.00,
    total DECIMAL(10,2) NOT NULL,
    shipping_name VARCHAR(150),
    shipping_email VARCHAR(150),
    shipping_phone VARCHAR(20),
    shipping_address TEXT,
    shipping_city VARCHAR(100),
    shipping_state VARCHAR(100),
    shipping_pincode VARCHAR(10),
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT NOT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_image VARCHAR(500),
    size VARCHAR(20),
    color VARCHAR(50),
    qty INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cart_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    user_id INT DEFAULT NULL,
    product_id INT NOT NULL,
    size VARCHAR(20),
    color VARCHAR(50),
    qty INT DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS wishlist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    user_id INT DEFAULT NULL,
    product_id INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wish (session_id, product_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) UNIQUE NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new','read','replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('percent','fixed') DEFAULT 'percent',
    value DECIMAL(10,2) NOT NULL,
    min_order DECIMAL(10,2) DEFAULT 0.00,
    max_uses INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    expires_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    user_id INT DEFAULT NULL,
    name VARCHAR(100),
    email VARCHAR(150),
    rating INT NOT NULL,
    title VARCHAR(200),
    comment TEXT,
    is_approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;
SQL;

try {
    $pdo->exec($ddl);
    ok('13 tables created');
} catch (Exception $e) {
    fail($e->getMessage());
}

// ── Step 4: Admin user ───────────────────────────────────────
step(4, 'Create admin user');
try {
    $hash = password_hash('123', PASSWORD_BCRYPT, ['cost' => 12]);
    $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, 'admin') ON DUPLICATE KEY UPDATE password = VALUES(password), role = 'admin', is_active = 1")
        ->execute(['Admin', 'admin@gmail.com', $hash]);
    ok('admin@gmail.com / 123');
} catch (Exception $e) {
    fail($e->getMessage());
}

// ── Step 5: Settings ──────────────────────────────────────────
step(5, 'Configure settings');
try {
    $settings = [
        ['site_name', 'ELEVÉ'],
        ['site_email', 'support@eleve.com'],
        ['site_phone', '+91 98765 43210'],
        ['currency_symbol', '₹'],
        ['free_shipping_min', '999'],
        ['tax_rate', '18'],
        ['maintenance_mode', '0'],
        ['hero_title', 'Define Your Elegance'],
        ['hero_subtitle', 'Every thread tells a story of precision, passion, and modern sophistication.'],
    ];
    $stmt = $pdo->prepare("INSERT INTO settings (setting_key, setting_value) VALUES (?,?) ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)");
    foreach ($settings as $s) { $stmt->execute($s); }
    ok('9 settings configured');
} catch (Exception $e) {
    fail($e->getMessage());
}

// ── Step 6: Products ──────────────────────────────────────────
step(6, 'Import products');

$products = [
    [1,'Noir Oversized Hoodie','Outerwear','hoodies','',1799,null,'New',4.5,0,0,1,'Elevate your everyday wardrobe with our signature Noir Oversized Hoodie. Crafted from premium 400GSM French terry cotton with dropped shoulders and kangaroo pocket.','Oversized fit — choose regular size for intended look, or size down for standard fit.','100% organic cotton 400GSM French terry|Relaxed oversized fit with dropped shoulders|Kangaroo pocket with hidden zip compartment|Ribbed cuffs and hem'],
    [2,'Essential White Tee','T-Shirts','t-shirts','',699,null,null,4.5,0,0,1,'The foundation of every great outfit. Premium Supima cotton with perfect weight that drapes beautifully. Pre-shrunk and garment-dyed.','Regular to relaxed fit. True to size.','100% Supima cotton 180GSM|Relaxed fit with slightly dropped shoulders|Reinforced collar|Pre-shrunk and garment-dyed'],
    [3,'Urban Cargo Pants','Bottoms','cargo-pants','',1499,null,'Best Seller',4.5,0,0,1,'Military-inspired utility with modern streetwear aesthetics. Six functional pockets, adjustable ankle cuffs, mid-rise tapered fit.','Tapered fit — slightly relaxed through thigh, tapering at ankle.','98% cotton 2% elastane ripstop|Six functional pockets including hidden zip|Adjustable drawstring ankle cuffs|YKK hardware throughout'],
    [4,'Heritage Leather Jacket','Outerwear','jackets','',1899,2499,null,4.5,0,0,1,'Timeless investment piece. Full-grain lambskin leather that develops rich patina over time. Fully lined with classic moto silhouette.','Slim fit — layer over tee or light sweater.','Full-grain lambskin leather|Satin-lined interior with inside pocket|Antique brass YKK zippers|Classic moto collar'],
    [5,'Graphic Print Tee','T-Shirts','t-shirts','',799,null,null,4.5,0,0,0,'Wearable art meets streetwear. Exclusive artwork collaboration, screen-printed by hand using eco-friendly water-based inks.','Boxy fit — slightly wider and shorter. True to size.','100% organic cotton 220GSM heavyweight|Hand screen-printed graphic|Water-based eco-friendly inks|Boxy fit with ribbed crew neck'],
    [6,'Navy Bomber Jacket','Outerwear','jackets','',1599,1999,'Sale',4.5,0,0,0,'Perfect transitional piece. Water-resistant nylon shell, quilted satin lining, and ribbed trims. Versatile navy colorway.','Regular fit — true to size for classic bomber silhouette.','Water-resistant nylon shell|Quilted satin lining|Ribbed collar cuffs and hem|Two exterior + interior pocket'],
    [7,'Slim Fit Denim','Bottoms','jeans','',999,null,null,4.5,0,0,0,'Japanese selvedge denim with 2% stretch for comfort without compromising the clean slim profile.','Slim fit — fitted through thigh with narrow leg.','98% Japanese selvedge cotton 2% elastane|13oz raw denim|Slim fit through thigh and leg|Classic five-pocket construction'],
    [8,'Signature Leather Bag','Accessories','bags','women',499,null,null,4.6,0,0,0,'Hand-crafted full-grain Italian leather with brushed nickel buckle. Develops character over years of wear.','One size — adjustable from 28 to 38 inch waist.','Full-grain Italian leather|Brushed nickel buckle with ELEVÉ monogram|Hand-painted and burnished edges'],
    [9,'Washed Crew Neck','T-Shirts','t-shirts','unisex',849,null,null,4.6,0,0,0,'Garment-dyed and enzyme-washed for perfectly broken-in feel. Heavyweight cotton holds shape wash after wash.','Relaxed fit — slightly wider and longer. True to size.','100% combed cotton 240GSM heavyweight|Garment-dyed and enzyme-washed|Double-needle stitching throughout'],
    [10,'Tailored Wool Overcoat','Outerwear','outerwear','',1999,null,null,4.5,0,0,0,'Italian wool-cashmere blend, fully lined with half-canvas construction for natural drape that improves with wear.','Tailored fit — layer over suits or knitwear. True to size.','80% Italian wool 20% cashmere blend|Half-canvas construction|Notch lapel with functional buttonholes'],
    [13,'Classic Polo Shirt','Polo','polo','men',899,null,'New',4.6,1,0,1,'Premium 220GSM pique cotton polo with ribbed collar — the ultimate smart-casual essential.','Regular fit. True to size.','100% pique cotton 220GSM|Ribbed collar and cuffs|Slim fit with side vents'],
    [14,'Oxford Button-Down Shirt','Shirts','shirts','men',1199,null,null,4.5,0,0,0,'Classic Oxford weave button-down shirt, perfect from boardroom to brunch.','Regular fit. True to size.','100% Oxford cotton|Button-down collar|Regular fit with chest pocket'],
    [16,'Zip-Up Hoodie','Hoodies','hoodies','men',1499,1899,'Sale',4.7,0,1,1,'Heavyweight 380GSM zip-up hoodie with brushed fleece interior for all-day warmth.','Regular fit. True to size.','380GSM brushed fleece|Full YKK zip|Kangaroo pocket'],
    [19,'Mens Co-Ord Tracksuit','Co-Ord Sets','coord-sets','men',2199,2799,'Sale',4.8,0,1,1,'Matching hoodie and jogger set in premium French terry — style made effortless.','Regular fit. True to size.','400GSM French terry|Matching jacket and pants|Elasticated waistband with drawstring'],
    [20,'Oversized Graphic Hoodie','Hoodies','hoodies','men',1699,null,'New',4.6,1,0,0,'Oversized hoodie with exclusive hand screen-printed graphic using water-based inks.','Oversized fit. Size down for regular look.','400GSM organic cotton|Hand screen-printed graphic|Dropped shoulders relaxed fit'],
    [21,'Crop Ribbed Top','Tops','tops','women',599,null,'New',4.6,1,0,1,'Stretch ribbed crop top with flattering square neckline and fitted silhouette.','True to size.','95% cotton 5% elastane|Square neckline|Cropped fit'],
    [22,'Midi Wrap Dress','Dresses','dresses','women',1799,null,'New',4.8,1,1,1,'Flowing midi wrap dress in LENZING ECOVERO viscose — effortlessly elegant.','True to size. Adjustable fit via waist tie.','LENZING ECOVERO viscose|Adjustable wrap tie|Midi length flutter sleeves'],
    [23,'Printed Anarkali Kurti','Kurtis','kurtis','women',1299,null,null,4.5,0,0,0,'Block-printed cotton Anarkali kurti with flared silhouette and intricate detailing.','Regular fit. True to size.','100% cotton|Block-printed design|Flared Anarkali silhouette'],
    [24,'Womens Co-Ord Linen Set','Co-Ord Sets','coord-sets','women',2499,null,'New',4.7,1,0,1,'Relaxed linen blazer and wide-leg trouser co-ord set — effortless summer styling.','Relaxed fit. Size up for oversized look.','100% linen|Matching blazer and trouser|Relaxed fit'],
    [25,'High-Waist Flare Leggings','Leggings','leggings','women',899,null,null,4.5,0,1,0,'Four-way stretch high-waist leggings with flared hem for yoga to street.','True to size. High-waist sits above hip.','80% nylon 20% spandex|High waist with tummy control|Moisture-wicking fabric'],
    [26,'Womens Oversized Hoodie','Hoodies','hoodies','women',1499,null,null,4.7,0,0,0,'Ultra-soft 350GSM oversized hoodie with dropped shoulders — cosy luxury.','Oversized fit. Size down for regular look.','350GSM fleece cotton|Oversized dropped shoulders|Kangaroo pocket'],
    [27,'Womens Denim Jacket','Jackets','jackets','women',1899,2299,'Sale',4.6,0,0,0,'Classic indigo denim jacket with slightly cropped fit and antique brass buttons.','Relaxed cropped fit. True to size.','100% cotton denim|Cropped relaxed fit|Button front closure'],
    [29,'Boys Graphic Tee','T-Shirts','t-shirts','kids',399,null,'New',4.4,1,0,0,'Fun graphic tee for boys in breathable organic cotton with bright prints.','Regular fit. Order by age.','100% organic cotton|Comfortable regular fit|Machine washable'],
    [30,'Girls Floral Dress','Dresses','dresses','kids',799,null,null,4.6,0,0,0,'Smocked floral dress for girls — perfect for parties and everyday play.','Regular fit. Order by age.','100% cotton|Smocked bodice|Flared skirt with pockets'],
    [31,'Kids School Uniform Shirt','School Wear','school-wear','kids',499,null,null,4.3,0,1,0,'Wrinkle-resistant school shirt in easy-care cotton blend — built to last the school day.','Regular fit. Order by age.','65% polyester 35% cotton|Wrinkle-resistant|Easy-care machine washable'],
    [32,'Kids Winter Jacket','Winter Wear','winter-wear','kids',1299,null,'New',4.7,1,0,0,'Padded winter jacket with water-resistant outer shell and warm fleece lining.','Regular fit. Size up for layering.','Water-resistant outer shell|Warm fleece lining|Full-zip with hood'],
    [33,'Classic White Sneakers','Sneakers','sneakers','unisex',1999,null,'New',4.8,1,1,1,'Minimalist vulcanised canvas sneakers with clean white profile — a timeless classic.','True to size. Half size up for wide feet.','Canvas upper|Vulcanised rubber sole|Lace-up closure'],
    [34,'Logo Slides','Slides','slides','unisex',799,null,null,4.4,0,0,0,'Lightweight EVA logo slides with cushioned footbed — poolside to pavement.','True to size.','EVA upper and sole|Cushioned contoured footbed|Embossed logo strap'],
    [35,'Chelsea Boots','Boots','boots','men',3499,3999,'Sale',4.7,0,0,0,'Full-grain leather Chelsea boots with elastic side panels and stacked leather heel.','True to size. Half size up for wide feet.','Full-grain leather upper|Elastic side gussets|Leather stacked heel'],
    [36,'Running Shoes','Running Shoes','running-shoes','unisex',2499,null,'New',4.6,1,0,0,'Lightweight mesh running shoes with responsive foam midsole for daily training.','Half size up recommended.','Engineered mesh upper|Responsive foam midsole|Rubber outsole for grip'],
];

try {
    $pdo->exec("DELETE FROM product_colors");
    $pdo->exec("DELETE FROM product_sizes");
    $pdo->exec("DELETE FROM product_images");
    $pdo->exec("DELETE FROM products");

    $stmt = $pdo->prepare("INSERT INTO products (id,name,category,subcategory,gender,price,old_price,badge,rating,is_new_arrival,is_best_seller,is_featured,description,fit,details,is_active,stock_qty) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,1,100)");
    foreach ($products as $p) { $stmt->execute($p); }
    ok(count($products) . ' products inserted');
} catch (Exception $e) {
    fail($e->getMessage());
}

// ── Step 7: Images, Sizes, Colors ─────────────────────────────
step(7, 'Images, sizes & colors');

$images = [
    [1,'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=800&q=80&fit=crop',0],
    [1,'https://images.unsplash.com/photo-1578681994506-b8f463449011?w=800&q=80&fit=crop',1],
    [2,'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&q=80&fit=crop',0],
    [2,'https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=800&q=80&fit=crop',1],
    [3,'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=800&q=80&fit=crop',0],
    [3,'https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=800&q=80&fit=crop',1],
    [4,'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&q=80&fit=crop',0],
    [4,'https://images.unsplash.com/photo-1521223890158-f9f7c3d5ded1?w=800&q=80&fit=crop',1],
    [5,'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&q=80&fit=crop',0],
    [5,'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=800&q=80&fit=crop',1],
    [6,'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&q=80&fit=crop',0],
    [6,'https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=800&q=80&fit=crop',1],
    [7,'https://images.unsplash.com/photo-1542272604-787c3835535d?w=800&q=80&fit=crop',0],
    [8,'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=80&fit=crop',0],
    [9,'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=800&q=80&fit=crop',0],
    [10,'https://images.unsplash.com/photo-1539533113208-f6df8cc8b543?w=800&q=80&fit=crop',0],
    [13,'https://images.unsplash.com/photo-1586363104862-3a5e2ab60d99?w=800&q=80&fit=crop',0],
    [14,'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=800&q=80&fit=crop',0],
    [16,'https://images.unsplash.com/photo-1509942774463-acf339cf87d5?w=800&q=80&fit=crop',0],
    [19,'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&q=80&fit=crop',0],
    [20,'https://images.unsplash.com/photo-1578681994506-b8f463449011?w=800&q=80&fit=crop',0],
    [21,'https://images.unsplash.com/photo-1554568218-0f1715e72254?w=800&q=80&fit=crop',0],
    [22,'https://images.unsplash.com/photo-1515372039744-b8f02a3ae446?w=800&q=80&fit=crop',0],
    [23,'https://images.unsplash.com/photo-1583391733956-3750e0ff4e8b?w=800&q=80&fit=crop',0],
    [24,'https://images.unsplash.com/photo-1509631179647-0177331693ae?w=800&q=80&fit=crop',0],
    [25,'https://images.unsplash.com/photo-1506629082955-511b1aa562c8?w=800&q=80&fit=crop',0],
    [26,'https://images.unsplash.com/photo-1578681994506-b8f463449011?w=800&q=80&fit=crop',0],
    [27,'https://images.unsplash.com/photo-1551537482-f2075a1d41f2?w=800&q=80&fit=crop',0],
    [29,'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&q=80&fit=crop',0],
    [30,'https://images.unsplash.com/photo-1518831959646-742c3a14ebf7?w=800&q=80&fit=crop',0],
    [31,'https://images.unsplash.com/photo-1571019614242-c5c5dee9f50b?w=800&q=80&fit=crop',0],
    [32,'https://images.unsplash.com/photo-1602810318383-e386cc2a3ccf?w=800&q=80&fit=crop',0],
    [33,'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80&fit=crop',0],
    [34,'https://images.unsplash.com/photo-1603487742131-4160ec999306?w=800&q=80&fit=crop',0],
    [35,'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80&fit=crop',0],
    [36,'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&q=80&fit=crop',0],
];
$sizes = [
    [1,'S'],[1,'M'],[1,'L'],[1,'XL'],[2,'XS'],[2,'S'],[2,'M'],[2,'L'],[2,'XL'],
    [3,'S'],[3,'M'],[3,'L'],[4,'M'],[4,'L'],[4,'XL'],[5,'S'],[5,'M'],[5,'L'],[5,'XL'],
    [6,'S'],[6,'M'],[6,'L'],[7,'S'],[7,'M'],[7,'L'],[7,'XL'],[8,'M'],
    [9,'XS'],[9,'S'],[9,'M'],[9,'L'],[10,'M'],[10,'L'],[10,'XL'],
    [13,'S'],[13,'M'],[13,'L'],[13,'XL'],[13,'XXL'],[14,'S'],[14,'M'],[14,'L'],[14,'XL'],
    [16,'S'],[16,'M'],[16,'L'],[16,'XL'],[16,'XXL'],[19,'S'],[19,'M'],[19,'L'],[19,'XL'],
    [20,'S'],[20,'M'],[20,'L'],[20,'XL'],[20,'XXL'],[21,'XS'],[21,'S'],[21,'M'],[21,'L'],
    [22,'XS'],[22,'S'],[22,'M'],[22,'L'],[22,'XL'],
    [23,'XS'],[23,'S'],[23,'M'],[23,'L'],[23,'XL'],[23,'XXL'],
    [24,'XS'],[24,'S'],[24,'M'],[24,'L'],[25,'XS'],[25,'S'],[25,'M'],[25,'L'],[25,'XL'],
    [26,'XS'],[26,'S'],[26,'M'],[26,'L'],[27,'XS'],[27,'S'],[27,'M'],[27,'L'],
    [29,'3-4Y'],[29,'5-6Y'],[29,'7-8Y'],[29,'9-10Y'],
    [30,'3-4Y'],[30,'5-6Y'],[30,'7-8Y'],[30,'9-10Y'],
    [31,'3-4Y'],[31,'5-6Y'],[31,'7-8Y'],[31,'9-10Y'],[31,'11-12Y'],
    [32,'3-4Y'],[32,'5-6Y'],[32,'7-8Y'],[32,'9-10Y'],[32,'11-12Y'],
    [33,'UK6'],[33,'UK7'],[33,'UK8'],[33,'UK9'],[33,'UK10'],[33,'UK11'],
    [34,'UK6'],[34,'UK7'],[34,'UK8'],[34,'UK9'],[34,'UK10'],
    [35,'UK6'],[35,'UK7'],[35,'UK8'],[35,'UK9'],[35,'UK10'],[35,'UK11'],
    [36,'UK6'],[36,'UK7'],[36,'UK8'],[36,'UK9'],[36,'UK10'],[36,'UK11'],
];
$colors = [
    [1,'Black','#1a1a1a'],[1,'White','#f5f5f5'],[1,'Charcoal','#808080'],
    [2,'White','#f5f5f5'],[2,'Black','#1a1a1a'],[2,'Sand','#c2b280'],
    [3,'Gray','#808080'],[3,'Black','#1a1a1a'],[3,'Olive','#556B2F'],
    [4,'Brown','#8B6914'],[4,'Black','#1a1a1a'],[5,'Black','#1a1a1a'],[5,'White','#f5f5f5'],
    [6,'Navy','#1a2744'],[6,'Black','#1a1a1a'],[6,'Olive','#556B2F'],
    [7,'Black','#1a1a1a'],[7,'Indigo','#2E2B5F'],[7,'Gray','#808080'],
    [8,'Black','#1a1a1a'],[8,'Brown','#8B6914'],
    [9,'Gray','#808080'],[9,'Washed Black','#333333'],[9,'Stone','#b8a99a'],
    [10,'Black','#1a1a1a'],[10,'Camel','#C19A6B'],[10,'Charcoal','#444444'],
    [13,'Navy','#1a2744'],[13,'White','#f5f5f5'],[13,'Black','#1a1a1a'],
    [14,'White','#f5f5f5'],[14,'Blue','#4a90d9'],[14,'Gray','#808080'],
    [16,'Gray','#808080'],[16,'Black','#1a1a1a'],[16,'Navy','#1a2744'],
    [19,'Black','#1a1a1a'],[19,'Gray','#808080'],[20,'White','#f5f5f5'],[20,'Black','#1a1a1a'],
    [21,'Black','#1a1a1a'],[21,'White','#f5f5f5'],[21,'Camel','#C19A6B'],
    [22,'Navy','#1a2744'],[22,'Burgundy','#800020'],[22,'Olive','#556B2F'],
    [23,'White','#f5f5f5'],[23,'Pink','#FFB6C1'],[23,'Yellow','#FFD700'],
    [24,'Beige','#d4b483'],[24,'White','#f5f5f5'],[24,'Black','#1a1a1a'],
    [25,'Black','#1a1a1a'],[25,'Navy','#1a2744'],[25,'Gray','#808080'],
    [26,'Gray','#808080'],[26,'Lavender','#967BB6'],[26,'Pink','#FFB6C1'],
    [27,'Indigo','#2E2B5F'],[27,'Black','#1a1a1a'],
    [29,'White','#f5f5f5'],[29,'Black','#1a1a1a'],
    [30,'Pink','#FFB6C1'],[30,'Yellow','#FFD700'],
    [31,'White','#f5f5f5'],[31,'Light Blue','#add8e6'],
    [32,'Navy','#1a2744'],[32,'Red','#c0392b'],[32,'Black','#1a1a1a'],
    [33,'White','#f5f5f5'],[33,'Black','#1a1a1a'],
    [34,'Black','#1a1a1a'],[34,'White','#f5f5f5'],[34,'Gray','#808080'],
    [35,'Brown','#8B6914'],[35,'Black','#1a1a1a'],
    [36,'Gray','#808080'],[36,'Black','#1a1a1a'],[36,'Navy','#1a2744'],
];

try {
    $imgStmt = $pdo->prepare("INSERT INTO product_images (product_id, url, sort_order) VALUES (?,?,?)");
    foreach ($images as $img) { $imgStmt->execute($img); }
    $szStmt = $pdo->prepare("INSERT INTO product_sizes (product_id, size) VALUES (?,?)");
    foreach ($sizes as $sz) { $szStmt->execute($sz); }
    $clStmt = $pdo->prepare("INSERT INTO product_colors (product_id, name, hex) VALUES (?,?,?)");
    foreach ($colors as $cl) { $clStmt->execute($cl); }
    ok(count($images).' images, '.count($sizes).' sizes, '.count($colors).' colors');
} catch (Exception $e) {
    fail($e->getMessage());
}

// ── Step 8: Coupons ───────────────────────────────────────────
step(8, 'Sample coupons');
try {
    $coupons = [
        ['WELCOME10','percent',10,500,100],
        ['FLAT200','fixed',200,1000,50],
        ['SAVE15','percent',15,1500,30],
        ['FIRST50','fixed',50,299,200],
    ];
    $stmt = $pdo->prepare("INSERT INTO coupons (code,type,value,min_order,max_uses) VALUES (?,?,?,?,?) ON DUPLICATE KEY UPDATE is_active=1");
    foreach ($coupons as $c) { $stmt->execute($c); }
    ok('4 coupons (WELCOME10, FLAT200, SAVE15, FIRST50)');
} catch (Exception $e) {
    fail($e->getMessage());
}

// ── Done ──────────────────────────────────────────────────────
echo "\n";
echo "========================================\n";
echo "  ✅ INSTALLATION COMPLETE!\n";
echo "========================================\n\n";
echo "  Admin Panel : admin/login.php\n";
echo "  Email       : admin@gmail.com\n";
echo "  Password    : admin\n";
echo "  Store       : index.html\n\n";
echo "  URL: http://localhost/Eleve-clothing-ecommerce/admin/login.php\n\n";
echo "========================================\n\n";
