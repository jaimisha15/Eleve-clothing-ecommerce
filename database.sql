-- ============================================================
-- ELEVÉ E-Commerce Database Schema
-- Run this in phpMyAdmin or MySQL CLI
-- ============================================================

CREATE DATABASE IF NOT EXISTS eleve_ecommerce CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE eleve_ecommerce;

-- ============================================================
-- USERS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS users (
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
) ENGINE=InnoDB;

-- ============================================================
-- CATEGORIES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS categories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    slug VARCHAR(100) UNIQUE NOT NULL,
    gender ENUM('men', 'women', 'kids', 'unisex', '') DEFAULT '',
    image_url VARCHAR(500),
    is_active TINYINT(1) DEFAULT 1,
    sort_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- PRODUCTS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS products (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(200) NOT NULL,
    category VARCHAR(100) NOT NULL,
    subcategory VARCHAR(100),
    gender ENUM('men', 'women', 'kids', 'unisex', '') DEFAULT '',
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

-- ============================================================
-- PRODUCT IMAGES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS product_images (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    url VARCHAR(500) NOT NULL,
    sort_order INT DEFAULT 0,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- PRODUCT SIZES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS product_sizes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    size VARCHAR(20) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- PRODUCT COLORS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS product_colors (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT NOT NULL,
    name VARCHAR(50) NOT NULL,
    hex VARCHAR(10) NOT NULL,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- ORDERS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS orders (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_number VARCHAR(20) UNIQUE NOT NULL,
    user_id INT DEFAULT NULL,
    guest_email VARCHAR(150),
    status ENUM('pending', 'confirmed', 'processing', 'shipped', 'delivered', 'cancelled', 'refunded') DEFAULT 'pending',
    payment_status ENUM('pending', 'paid', 'failed', 'refunded') DEFAULT 'pending',
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

-- ============================================================
-- ORDER ITEMS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS order_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    order_id INT NOT NULL,
    product_id INT DEFAULT NULL,
    product_name VARCHAR(200) NOT NULL,
    product_image VARCHAR(500),
    size VARCHAR(20),
    color VARCHAR(50),
    qty INT NOT NULL,
    price DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (order_id) REFERENCES orders(id) ON DELETE CASCADE,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- CART TABLE (server-side session cart)
-- ============================================================
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

-- ============================================================
-- WISHLIST TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS wishlist_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(100) NOT NULL,
    user_id INT DEFAULT NULL,
    product_id INT DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY unique_wish (session_id, product_id),
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- ============================================================
-- NEWSLETTER SUBSCRIBERS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS newsletter_subscribers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(150) UNIQUE NOT NULL,
    is_active TINYINT(1) DEFAULT 1,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- CONTACT MESSAGES TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS contact_messages (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL,
    subject VARCHAR(200),
    message TEXT NOT NULL,
    status ENUM('new', 'read', 'replied') DEFAULT 'new',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- COUPONS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS coupons (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    type ENUM('percent', 'fixed') DEFAULT 'percent',
    value DECIMAL(10,2) NOT NULL,
    min_order DECIMAL(10,2) DEFAULT 0.00,
    max_uses INT DEFAULT NULL,
    used_count INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    expires_at DATETIME DEFAULT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- REVIEWS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS reviews (
    id INT AUTO_INCREMENT PRIMARY KEY,
    product_id INT DEFAULT NULL,
    user_id INT DEFAULT NULL,
    name VARCHAR(100),
    email VARCHAR(150),
    rating INT NOT NULL CHECK (rating BETWEEN 1 AND 5),
    title VARCHAR(200),
    comment TEXT,
    is_approved TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- ============================================================
-- SETTINGS TABLE
-- ============================================================
CREATE TABLE IF NOT EXISTS settings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    setting_key VARCHAR(100) UNIQUE NOT NULL,
    setting_value TEXT,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ============================================================
-- DEFAULT ADMIN USER (password: admin)
-- ============================================================
INSERT INTO users (name, email, password, role) VALUES
('Admin', 'admin@gmail.com', '$2y$12$GdJVnBYX5Qr4E0uegsS6mOdk/Fx175QHKQKEwB7KTnxo0s4WipsTq', 'admin');

-- Note: The hash above is bcrypt for 'admin' (Laravel default hash)
-- Replace with: password_hash('admin', PASSWORD_BCRYPT, ['cost' => 12])

-- ============================================================
-- DEFAULT SETTINGS
-- ============================================================
INSERT INTO settings (setting_key, setting_value) VALUES
('site_name', 'ELEVÉ'),
('site_email', 'support@eleve.com'),
('site_phone', '+91 98765 43210'),
('currency_symbol', '₹'),
('free_shipping_min', '999'),
('tax_rate', '18'),
('maintenance_mode', '0'),
('hero_title', 'Define Your Elegance'),
('hero_subtitle', 'Every thread tells a story of precision, passion, and modern sophistication.');

-- ============================================================
-- SAMPLE PRODUCTS DATA (Men, Women, Kids)
-- ============================================================
INSERT INTO products (id, name, category, subcategory, gender, price, old_price, badge, rating, is_new_arrival, is_best_seller, is_featured, description, fit, details) VALUES
(1, 'Men''s Noir Graphic Tee', 'T-Shirts', 't-shirts', 'men', 699, 899, 'Best Seller', 4.8, 1, 1, 1, 'Signature streetwear graphic tee in 220GSM heavy combed cotton.', 'Regular streetwear fit.', '100% premium combed cotton|220GSM heavyweight|Screen-printed chest graphic'),
(2, 'Men''s Minimalist Crew Neck Tee', 'T-Shirts', 't-shirts', 'men', 599, NULL, 'New', 4.6, 1, 0, 1, 'Everyday clean aesthetic crew neck t-shirt in ultra-soft bio-washed cotton.', 'Classic regular fit.', '100% bio-washed organic cotton|190GSM medium weight|Ribbed collar'),
(3, 'Men''s Urban Street Tee', 'T-Shirts', 't-shirts', 'men', 749, NULL, 'Popular', 4.7, 0, 1, 1, 'Modern street style graphic t-shirt crafted for weekend casuals.', 'Relaxed fit with dropped shoulders.', '100% cotton jersey knit|Dropped shoulders|Soft hand-feel print'),
(4, 'Men''s Vintage Wash Graphic Tee', 'T-Shirts', 't-shirts', 'men', 699, 849, 'Sale', 4.5, 0, 0, 0, 'Acid-washed vintage finish gives this tee an authentic retro feel.', 'Standard regular fit.', 'Garment-dyed and enzyme washed|Comfortable breathable knit|Seamless tubular body'),
(5, 'Men''s Oversized Statement Tee', 'T-Shirts', 't-shirts', 'men', 799, NULL, 'New', 4.9, 1, 1, 1, 'Heavy drape oversized tee featuring custom typography.', 'Generously oversized. Size down for regular fit.', '240GSM heavyweight cotton|Oversized boxy drape|Reinforced stitching'),
(6, 'Men''s Athletics Printed T-Shirt', 'T-Shirts', 't-shirts', 'men', 649, NULL, NULL, 4.4, 0, 0, 0, 'Athletics-inspired graphic t-shirt engineered with stretch cotton.', 'Athletic regular fit.', '95% cotton 5% elastane|Anti-odor bio-treatment|Athletic chest fit'),
(7, 'Men''s Signature Essential Tee', 'T-Shirts', 't-shirts', 'men', 679, NULL, 'New', 4.6, 1, 0, 0, 'Clean lines and versatile design make this essential t-shirt an irreplaceable base layer.', 'True to size.', '100% Supima long-staple cotton|Silky-smooth finish|Pre-shrunk fabric'),
(8, 'Men''s Slim Fit Washed Jeans', 'Jeans', 'jeans', 'men', 1399, 1799, 'Best Seller', 4.8, 1, 1, 1, 'Premium stretch denim engineered for comfortable all-day movement.', 'Slim fit with slight taper at ankle.', '98% cotton denim 2% elastane|12.5oz authentic denim weight|Classic five-pocket styling'),
(9, 'Men''s Distressed Street Denim', 'Jeans', 'jeans', 'men', 1499, 1999, 'Sale', 4.7, 0, 1, 0, 'Hand-distressed ripped accents with subtle fading for rugged streetwear aesthetic.', 'Slim straight fit.', '99% cotton 1% spandex|Hand-finished distress detailing|Reinforced pockets'),
(10, 'Men''s Dark Indigo Tapered Jeans', 'Jeans', 'jeans', 'men', 1299, NULL, 'New', 4.6, 1, 0, 0, 'Deep indigo wash with clean tone-on-tone stitching.', 'Tapered regular fit.', 'Raw-look dark rinse denim|Tapered leg opening|Zero sagging stretch recovery'),
(11, 'Men''s Relaxed Straight Leg Jeans', 'Jeans', 'jeans', 'men', 1349, NULL, NULL, 4.5, 0, 0, 0, 'Timeless straight leg jeans offering ample room through thigh and knee.', 'Relaxed straight fit.', '100% durable cotton denim|Standard straight cut|Heavy-duty YKK zipper'),
(12, 'Women''s Cropped Graphic T-Shirt', 'T-Shirts', 't-shirts', 'women', 599, 799, 'Best Seller', 4.9, 1, 1, 1, 'Chic cropped silhouette cut from breathable organic cotton.', 'Boxy cropped fit.', '100% soft organic cotton|Trendy cropped cut|Fade-resistant art print'),
(13, 'Women''s Minimalist Aesthetic Tee', 'T-Shirts', 't-shirts', 'women', 649, NULL, 'New', 4.7, 1, 0, 0, 'Understated elegance for daily styling with modal-cotton blend.', 'Fitted yet comfortable silhouette.', '60% cotton 40% modal|Silky touch against skin|Tailored feminine seam'),
(14, 'Women''s Oversized Graphic Tee', 'T-Shirts', 't-shirts', 'women', 699, NULL, 'Popular', 4.8, 0, 1, 1, 'Boyfriend-fit oversized graphic tee with dropped shoulders.', 'Intentionally oversized.', '100% combed cotton jersey|Relaxed dropped shoulders|Screen-printed typography'),
(15, 'Women''s Casual Cotton Daily Tee', 'T-Shirts', 't-shirts', 'women', 549, 699, 'Sale', 4.5, 0, 0, 0, 'Breathable everyday staple t-shirt with soft bio-polish for anti-pilling.', 'Regular fit.', '100% bio-polished cotton|Breathable lightweight fabric|Tagless neck label'),
(16, 'Women''s Ribbed Fitted T-Shirt', 'T-Shirts', 't-shirts', 'women', 629, NULL, 'New', 4.6, 1, 0, 0, 'Form-flattering fine-rib knit tee that hugs your curves effortlessly.', 'Slim body-hugging fit.', '95% cotton 5% spandex rib knit|Fine vertical ribbed texture|Shape-holding elastane'),
(17, 'Women''s High-Rise Flare Jeans', 'Jeans', 'jeans', 'women', 1499, 1999, 'Best Seller', 4.9, 1, 1, 1, 'Retro 70s inspired high-rise flare jeans tailored to elongate your legs.', 'High rise with flare hem.', '98% cotton 2% spandex sculpting denim|Flattering 11-inch rise|Five-pocket construction'),
(18, 'Women''s Mid-Rise Wide Leg Washed Jeans', 'Jeans', 'jeans', 'women', 1599, NULL, 'New', 4.8, 1, 0, 1, 'The trending wide-leg silhouette in relaxed washed denim.', 'Wide-leg relaxed fit.', '100% premium cotton denim|Wide-leg cut with clean drape|Mid-rise waistband'),
(19, 'Women''s Straight Cut Vintage Denim', 'Jeans', 'jeans', 'women', 1299, NULL, NULL, 4.6, 0, 0, 0, 'Timeless straight-leg cut that flatters every silhouette.', 'Straight leg. True to size.', '99% cotton 1% elastane|Authentic vintage wash|Medium rise'),
(20, 'Women''s Slim Fit Ankle Jeans', 'Jeans', 'jeans', 'women', 1399, 1799, 'Sale', 4.7, 0, 1, 0, 'Second-skin ankle jeans crafted with high-recovery stretch.', 'Slim fit ankle crop.', 'Power-stretch denim blend|Ankle-grazing length|Clean back pocket design'),
(21, 'Women''s Classic Denim Trousers', 'Jeans', 'jeans', 'women', 1249, NULL, NULL, 4.5, 0, 0, 0, 'Essential denim trousers with a smooth flat front and comfortable silhouette.', 'Comfort regular fit.', 'Soft washed cotton twill denim|Clean hem finish|Belt loops and zip closure'),
(22, 'Kids Star Printed Crew Neck Tee', 'T-Shirts', 't-shirts', 'kids', 449, 599, 'Best Seller', 4.9, 1, 1, 1, 'Fun all-over star print crew neck t-shirt in lightweight breathable cotton.', 'Regular kids fit.', '100% pure organic cotton|Safe non-toxic dye|Tagless itch-free neckline'),
(23, 'Kids Durable Summer Denim Pants', 'Jeans', 'jeans', 'kids', 699, 899, 'Popular', 4.8, 1, 1, 1, 'Reinforced knee denim pants designed for rough-and-tumble active kids.', 'Comfortable relaxed fit with stretch waist.', '98% soft washed cotton 2% stretch|Elasticated comfort waist|Reinforced double-stitched knees'),
(24, 'Kids Explorer Playtime Outfit Set', 'Co-Ord Sets', 'coord-sets', 'kids', 799, NULL, 'New', 4.7, 1, 0, 1, 'Complete coordinated outfit set for boys and girls.', 'Standard regular fit.', 'Two-piece coordinated set|100% combed cotton jersey|Elastic waistband bottoms'),
(25, 'Kids Casual Summer Duo Set', 'Co-Ord Sets', 'coord-sets', 'kids', 649, NULL, NULL, 4.6, 0, 0, 0, 'Breathable cotton matching pair engineered for outdoor park days and outings.', 'Regular fit.', 'Lightweight summer cotton knit|Contrast rib trims|Wash-and-wear easy care'),
(26, 'Kids Adventure Active Wear Set', 'Co-Ord Sets', 'coord-sets', 'kids', 599, 749, 'Sale', 4.5, 0, 0, 0, 'Vibrant and energetic activewear set with soft interior fleece.', 'Relaxed sporty fit.', 'Cotton-poly active fleece|Cuffed sleeves and ankles|Sporty panels'),
(27, 'Kids Sunshine Weekend Co-Ord', 'Co-Ord Sets', 'coord-sets', 'kids', 699, NULL, 'Best Seller', 4.8, 1, 1, 1, 'Bright and cheerful weekend coordinated outfit.', 'True to size.', '100% premium cotton|Fun friendly graphics|Covered elastic waistband'),
(28, 'Kids Daily Smiles Casual Outfit', 'Co-Ord Sets', 'coord-sets', 'kids', 549, NULL, NULL, 4.4, 0, 0, 0, 'Everyday easy-dressing set that makes school mornings and play effortless.', 'Regular fit.', 'Bio-washed cotton jersey|Resists shrinking & pilling|Durable flatlock seams'),
(29, 'Kids Bright Smile Graphic Outfit', 'Co-Ord Sets', 'coord-sets', 'kids', 599, NULL, 'New', 4.7, 1, 0, 1, 'Playful graphic print outfit designed with supreme softness.', 'Regular comfortable fit.', '100% soft organic cotton|Breathable summer weight|Machine wash friendly');

-- Product Images
INSERT INTO product_images (product_id, url, sort_order) VALUES
(1, 'images/men/tshirt-1.jpg', 0), (1, 'images/men/tshirt-2.jpg', 1),
(2, 'images/men/tshirt-2.jpg', 0), (2, 'images/men/tshirt-3.jpg', 1),
(3, 'images/men/tshirt-3.jpg', 0), (3, 'images/men/tshirt-4.jpg', 1),
(4, 'images/men/tshirt-4.jpg', 0), (4, 'images/men/tshirt-5.jpg', 1),
(5, 'images/men/tshirt-5.jpg', 0), (5, 'images/men/tshirt-6.jpg', 1),
(6, 'images/men/tshirt-6.jpg', 0), (6, 'images/men/tshirt-7.jpg', 1),
(7, 'images/men/tshirt-7.jpg', 0), (7, 'images/men/tshirt-1.jpg', 1),
(8, 'images/men/jeans-1.jpg', 0), (8, 'images/men/jeans-2.jpg', 1),
(9, 'images/men/jeans-2.jpg', 0), (9, 'images/men/jeans-3.jpg', 1),
(10, 'images/men/jeans-3.jpg', 0), (10, 'images/men/jeans-4.jpg', 1),
(11, 'images/men/jeans-4.jpg', 0), (11, 'images/men/jeans-1.jpg', 1),
(12, 'images/women/tshirt-1.jpg', 0), (12, 'images/women/tshirt-2.jpg', 1),
(13, 'images/women/tshirt-2.jpg', 0), (13, 'images/women/tshirt-3.jpg', 1),
(14, 'images/women/tshirt-3.jpg', 0), (14, 'images/women/tshirt-4.jpg', 1),
(15, 'images/women/tshirt-4.jpg', 0), (15, 'images/women/tshirt-5.jpg', 1),
(16, 'images/women/tshirt-5.jpg', 0), (16, 'images/women/tshirt-1.jpg', 1),
(17, 'images/women/jeans-flare.webp', 0), (17, 'images/women/jeans-1.jpg', 1),
(18, 'images/women/jeans-wide-leg.avif', 0), (18, 'images/women/jeans-2.jpg', 1),
(19, 'images/women/jeans-1.jpg', 0), (19, 'images/women/jeans-3.jpg', 1),
(20, 'images/women/jeans-2.jpg', 0), (20, 'images/women/jeans-1.jpg', 1),
(21, 'images/women/jeans-3.jpg', 0), (21, 'images/women/jeans-flare.webp', 1),
(22, 'images/kids/kids-star-tee.avif', 0), (22, 'images/kids/kids-1.jpg', 1),
(23, 'images/kids/kids-denim-pants.avif', 0), (23, 'images/kids/kids-2.jpg', 1),
(24, 'images/kids/kids-1.jpg', 0), (24, 'images/kids/kids-2.jpg', 1),
(25, 'images/kids/kids-2.jpg', 0), (25, 'images/kids/kids-3.jpg', 1),
(26, 'images/kids/kids-3.jpg', 0), (26, 'images/kids/kids-4.jpg', 1),
(27, 'images/kids/kids-4.jpg', 0), (27, 'images/kids/kids-5.jpg', 1),
(28, 'images/kids/kids-5.jpg', 0), (28, 'images/kids/kids-6.jpg', 1),
(29, 'images/kids/kids-6.jpg', 0), (29, 'images/kids/kids-star-tee.avif', 1);

-- Product Sizes
INSERT INTO product_sizes (product_id, size) VALUES
(1,'S'),(1,'M'),(1,'L'),(1,'XL'),(1,'XXL'),
(2,'S'),(2,'M'),(2,'L'),(2,'XL'),
(3,'S'),(3,'M'),(3,'L'),(3,'XL'),
(4,'M'),(4,'L'),(4,'XL'),
(5,'S'),(5,'M'),(5,'L'),(5,'XL'),
(6,'S'),(6,'M'),(6,'L'),(6,'XL'),
(7,'S'),(7,'M'),(7,'L'),(7,'XL'),(7,'XXL'),
(8,'30'),(8,'32'),(8,'34'),(8,'36'),
(9,'30'),(9,'32'),(9,'34'),(9,'36'),
(10,'28'),(10,'30'),(10,'32'),(10,'34'),(10,'36'),
(11,'30'),(11,'32'),(11,'34'),(11,'36'),(11,'38'),
(12,'XS'),(12,'S'),(12,'M'),(12,'L'),
(13,'XS'),(13,'S'),(13,'M'),(13,'L'),(13,'XL'),
(14,'S'),(14,'M'),(14,'L'),
(15,'XS'),(15,'S'),(15,'M'),(15,'L'),
(16,'XS'),(16,'S'),(16,'M'),(16,'L'),
(17,'26'),(17,'28'),(17,'30'),(17,'32'),
(18,'26'),(18,'28'),(18,'30'),(18,'32'),
(19,'26'),(19,'28'),(19,'30'),(19,'32'),(19,'34'),
(20,'26'),(20,'28'),(20,'30'),(20,'32'),
(21,'26'),(21,'28'),(21,'30'),(21,'32'),
(22,'3-4Y'),(22,'5-6Y'),(22,'7-8Y'),(22,'9-10Y'),(22,'11-12Y'),
(23,'3-4Y'),(23,'5-6Y'),(23,'7-8Y'),(23,'9-10Y'),(23,'11-12Y'),
(24,'3-4Y'),(24,'5-6Y'),(24,'7-8Y'),(24,'9-10Y'),
(25,'3-4Y'),(25,'5-6Y'),(25,'7-8Y'),(25,'9-10Y'),
(26,'3-4Y'),(26,'5-6Y'),(26,'7-8Y'),(26,'9-10Y'),
(27,'3-4Y'),(27,'5-6Y'),(27,'7-8Y'),(27,'9-10Y'),
(28,'3-4Y'),(28,'5-6Y'),(28,'7-8Y'),(28,'9-10Y'),(28,'11-12Y'),
(29,'3-4Y'),(29,'5-6Y'),(29,'7-8Y'),(29,'9-10Y');

-- Product Colors
INSERT INTO product_colors (product_id, name, hex) VALUES
(1,'Black','#1a1a1a'),(1,'White','#f5f5f5'),(1,'Navy','#1a2744'),
(2,'White','#f5f5f5'),(2,'Black','#1a1a1a'),(2,'Gray','#808080'),
(3,'Black','#1a1a1a'),(3,'Olive','#556B2F'),
(4,'Charcoal','#333333'),(4,'White','#f5f5f5'),
(5,'Black','#1a1a1a'),(5,'Beige','#d4b483'),
(6,'Navy','#1a2744'),(6,'White','#f5f5f5'),
(7,'White','#f5f5f5'),(7,'Black','#1a1a1a'),
(8,'Indigo','#2E2B5F'),(8,'Black','#1a1a1a'),
(9,'Light Wash','#87ceeb'),(9,'Medium Blue','#3b5998'),
(10,'Dark Indigo','#1c2841'),
(11,'Medium Blue','#3b5998'),(11,'Black','#1a1a1a'),
(12,'White','#f5f5f5'),(12,'Black','#1a1a1a'),(12,'Pink','#FFB6C1'),
(13,'Black','#1a1a1a'),(13,'Beige','#d4b483'),
(14,'Heather Gray','#808080'),(14,'White','#f5f5f5'),
(15,'White','#f5f5f5'),(15,'Lilac','#C8A2C8'),
(16,'Black','#1a1a1a'),(16,'Olive','#556B2F'),
(17,'Medium Denim','#3b5998'),(17,'Dark Blue','#1c2841'),
(18,'Light Wash','#87ceeb'),(18,'Vintage Indigo','#2E2B5F'),
(19,'Classic Blue','#3b5998'),
(20,'Dark Indigo','#1c2841'),(20,'Black','#1a1a1a'),
(21,'Medium Blue','#3b5998'),
(22,'Star Print','#FFD700'),(22,'White','#f5f5f5'),
(23,'Medium Denim','#3b5998'),(23,'Light Wash','#87ceeb'),
(24,'Multi','#FF6347'),(24,'Blue Set','#4a90d9'),
(25,'Blue / Gray','#4a90d9'),
(26,'Red / Navy','#c0392b'),
(27,'Sunshine Yellow','#FFD700'),
(28,'Mint Green','#98FB98'),
(29,'Tangerine','#FFA500');

-- Sample Coupon
INSERT INTO coupons (code, type, value, min_order, max_uses) VALUES
('WELCOME10', 'percent', 10, 500, 100),
('FLAT200', 'fixed', 200, 1000, 50),
('SAVE15', 'percent', 15, 1500, 30);
