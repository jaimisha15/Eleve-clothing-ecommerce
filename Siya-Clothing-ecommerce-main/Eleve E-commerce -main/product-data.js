/* ============================================================
   ELEVÉ — Product Data Catalog
   ============================================================
   All product information in one place.
   Used by products.html, index.html, and product-detail.html.
   ============================================================ */

const PRODUCTS = [
    {
        id: 1,
        name: 'Noir Oversized Hoodie',
        category: 'Outerwear',
        subcategory: 'hoodies',
        gender: '',
        rating: 4.5,
        isNewArrival: false,
        isBestSeller: false,
        price: 1799,
        oldPrice: null,
        badge: 'New',
        color: 'black',
        sizes: ['S', 'M', 'L', 'XL'],
        colors: [
            { name: 'Black', hex: '#1a1a1a' },
            { name: 'White', hex: '#f5f5f5' },
            { name: 'Charcoal', hex: '#808080' }
        ],
        images: [
            'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1578681994506-b8f463449011?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1515886657613-9f3515b0c78f?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1542327897-d73f4005b533?w=800&q=80&fit=crop'
        ],
        description: 'Elevate your everyday wardrobe with our signature Noir Oversized Hoodie. Crafted from premium 400GSM French terry cotton, this hoodie features a relaxed, oversized silhouette with dropped shoulders and a kangaroo pocket. The minimal design lets the quality speak for itself.',
        details: [
            '100% organic cotton, 400GSM French terry',
            'Relaxed oversized fit with dropped shoulders',
            'Kangaroo pocket with hidden zip compartment',
            'Ribbed cuffs and hem for a clean finish',
            'Tonal embroidered ELEVÉ logo on chest'
        ],
        fit: 'Oversized fit — we recommend choosing your regular size for the intended relaxed look, or sizing down for a more standard fit. Model is 6\'1" / 185cm wearing size L.'
    },
    {
        id: 2,
        name: 'Essential White Tee',
        category: 'T-Shirts',
        subcategory: 't-shirts',
        gender: '',
        rating: 4.5,
        isNewArrival: false,
        isBestSeller: false,
        price: 699,
        oldPrice: null,
        badge: null,
        color: 'white',
        sizes: ['XS', 'S', 'M', 'L', 'XL'],
        colors: [
            { name: 'White', hex: '#f5f5f5' },
            { name: 'Black', hex: '#1a1a1a' },
            { name: 'Sand', hex: '#c2b280' }
        ],
        images: [
            'https://images.unsplash.com/photo-1521572163474-6864f9cf17ab?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1581655353564-df123a1eb820?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1562157873-818bc0726f68?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=800&q=80&fit=crop'
        ],
        description: 'The foundation of every great outfit. Our Essential White Tee is made from premium Supima cotton with a perfect weight that drapes beautifully. Pre-shrunk and garment-dyed for a lived-in softness from day one.',
        details: [
            '100% Supima cotton, 180GSM',
            'Relaxed fit with slightly dropped shoulders',
            'Reinforced collar — no stretching or sagging',
            'Pre-shrunk and garment-dyed',
            'Subtle Siya Clothing woven label at hem'
        ],
        fit: 'Regular to relaxed fit. True to size. Model is 6\'0" / 183cm wearing size M.'
    },
    {
        id: 3,
        name: 'Urban Cargo Pants',
        category: 'Bottoms',
        subcategory: 'cargo-pants',
        gender: '',
        rating: 4.5,
        isNewArrival: false,
        isBestSeller: false,
        price: 1499,
        oldPrice: null,
        badge: 'Best Seller',
        color: 'gray',
        sizes: ['S', 'M', 'L'],
        colors: [
            { name: 'Gray', hex: '#808080' },
            { name: 'Black', hex: '#1a1a1a' },
            { name: 'Olive', hex: '#556B2F' }
        ],
        images: [
            'https://images.unsplash.com/photo-1624378439575-d8705ad7ae80?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1605348532760-6753d2c43329?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1552902865-b72c031ac5ea?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=800&q=80&fit=crop'
        ],
        description: 'Our best-selling Urban Cargo Pants blend military-inspired utility with modern streetwear aesthetics. Featuring six functional pockets, adjustable ankle cuffs, and a mid-rise tapered fit that works from day to night.',
        details: [
            '98% cotton, 2% elastane ripstop fabric',
            'Six functional pockets including hidden zip',
            'Adjustable drawstring ankle cuffs',
            'YKK hardware throughout',
            'Mid-rise with tapered leg'
        ],
        fit: 'Tapered fit — slightly relaxed through the thigh, tapering at the ankle. Model is 6\'1" / 185cm wearing size L.'
    },
    {
        id: 4,
        name: 'Heritage Leather Jacket',
        category: 'Outerwear',
        subcategory: 'jackets',
        gender: '',
        rating: 4.5,
        isNewArrival: false,
        isBestSeller: false,
        price: 1899,
        oldPrice: 2499,
        badge: null,
        color: 'brown',
        sizes: ['M', 'L', 'XL'],
        colors: [
            { name: 'Brown', hex: '#8B6914' },
            { name: 'Black', hex: '#1a1a1a' }
        ],
        images: [
            'https://images.unsplash.com/photo-1551028719-00167b16eac5?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1521223890158-f9f7c3d5ded1?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1520975954732-35dd22299614?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1507679799987-c73779587ccf?w=800&q=80&fit=crop'
        ],
        description: 'A timeless investment piece. The Heritage Leather Jacket is crafted from full-grain lambskin leather that develops a rich patina over time. Fully lined with a classic moto silhouette that transcends trends.',
        details: [
            'Full-grain lambskin leather',
            'Satin-lined interior with inside pocket',
            'Antique brass YKK zippers',
            'Classic moto collar with snap closure',
            'Hand-finished edges for artisan quality'
        ],
        fit: 'Slim fit — designed to layer over a tee or light sweater, not heavy knitwear. Model is 6\'1" / 185cm wearing size L.'
    },
    {
        id: 5,
        name: 'Graphic Print Tee',
        category: 'T-Shirts',
        subcategory: 't-shirts',
        gender: '',
        rating: 4.5,
        isNewArrival: false,
        isBestSeller: false,
        price: 799,
        oldPrice: null,
        badge: null,
        color: 'black',
        sizes: ['S', 'M', 'L', 'XL'],
        colors: [
            { name: 'Black', hex: '#1a1a1a' },
            { name: 'White', hex: '#f5f5f5' }
        ],
        images: [
            'https://images.unsplash.com/photo-1503342217505-b0a15ec3261c?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1576566588028-4147f3842f27?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1529374255404-311a2a4f1fd9?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1503341504253-dff4f94032fc?w=800&q=80&fit=crop'
        ],
        description: 'Wearable art meets streetwear. Our Graphic Print Tee features an exclusive artwork collaboration, screen-printed by hand using eco-friendly water-based inks on heavyweight cotton.',
        details: [
            '100% organic cotton, 220GSM heavyweight',
            'Hand screen-printed graphic',
            'Water-based eco-friendly inks',
            'Boxy fit with ribbed crew neck',
            'Limited edition — numbered run'
        ],
        fit: 'Boxy fit — slightly wider and shorter than our Essential Tee. True to size. Model is 5\'11" / 180cm wearing size M.'
    },
    {
        id: 6,
        name: 'Navy Bomber Jacket',
        category: 'Outerwear',
        subcategory: 'jackets',
        gender: '',
        rating: 4.5,
        isNewArrival: false,
        isBestSeller: false,
        price: 1599,
        oldPrice: 1999,
        badge: 'Sale',
        color: 'navy',
        sizes: ['S', 'M', 'L'],
        colors: [
            { name: 'Navy', hex: '#1a2744' },
            { name: 'Black', hex: '#1a1a1a' },
            { name: 'Olive', hex: '#556B2F' }
        ],
        images: [
            'https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1544022613-e87ca75a784a?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1548126032-079a0fb0099d?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1557597774-9d273605dfa9?w=800&q=80&fit=crop'
        ],
        description: 'The perfect transitional piece. Our Navy Bomber Jacket features a water-resistant nylon shell, quilted satin lining, and ribbed trims. Clean lines and a versatile navy colorway make it a wardrobe staple.',
        details: [
            'Water-resistant nylon shell',
            'Quilted satin lining for warmth',
            'Ribbed collar, cuffs, and hem',
            'Two exterior slash pockets + interior pocket',
            'Matte black YKK zip closure'
        ],
        fit: 'Regular fit — true to size for a classic bomber silhouette. Model is 6\'0" / 183cm wearing size M.'
    },
    {
        id: 7,
        name: 'Slim Fit Denim',
        category: 'Bottoms',
        subcategory: 'jeans',
        gender: '',
        rating: 4.5,
        isNewArrival: false,
        isBestSeller: false,
        price: 999,
        oldPrice: null,
        badge: null,
        color: 'black',
        sizes: ['S', 'M', 'L', 'XL'],
        colors: [
            { name: 'Black', hex: '#1a1a1a' },
            { name: 'Indigo', hex: '#2E2B5F' },
            { name: 'Gray', hex: '#808080' }
        ],
        images: [
            'https://images.unsplash.com/photo-1542272604-787c3835535d?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1604176354204-9268737828e4?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1582552938357-32b906df40cb?w=800&q=80&fit=crop'
        ],
        description: 'Engineered for the perfect silhouette. Our Slim Fit Denim is crafted from Japanese selvedge denim with 2% stretch for comfort without compromising the clean, slim profile.',
        details: [
            '98% Japanese selvedge cotton, 2% elastane',
            '13oz raw denim — develops unique fades',
            'Slim fit through thigh and leg',
            'Classic five-pocket construction',
            'Chain-stitched hem — heritage finish'
        ],
        fit: 'Slim fit — fitted through the thigh with a narrow leg. Size up one if between sizes. Model is 6\'1" / 185cm wearing size 32.'
    },
    {
        id: 8,
        name: 'Signature Leather Bag',
        category: 'Accessories',
        subcategory: 'bags',
        gender: 'women',
        rating: 4.6,
        isNewArrival: false,
        isBestSeller: false,
        price: 499,
        oldPrice: null,
        badge: null,
        color: 'black',
        sizes: ['M'],
        colors: [
            { name: 'Black', hex: '#1a1a1a' },
            { name: 'Brown', hex: '#8B6914' }
        ],
        images: [
            'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1624222247344-550fb60583dc?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1585856331426-d7b3f9fc5c16?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1609505848912-b7c3b8b4beda?w=800&q=80&fit=crop'
        ],
        description: 'The finishing touch. Our Signature Leather Bag is hand-crafted from full-grain Italian leather with a brushed nickel buckle. Designed to develop character over years of wear.',
        details: [
            'Full-grain Italian leather, 3.5cm width',
            'Brushed nickel buckle with Siya Clothing monogram',
            'Five-hole adjustment, fits waist 28-38"',
            'Hand-painted and burnished edges',
            'Comes in Siya Clothing dust bag'
        ],
        fit: 'One size — adjustable from 28" to 38" waist. Measure your waist at the point where you normally wear your belt.'
    },
    {
        id: 9,
        name: 'Washed Crew Neck',
        category: 'T-Shirts',
        subcategory: 't-shirts',
        gender: 'unisex',
        rating: 4.6,
        isNewArrival: false,
        isBestSeller: false,
        price: 849,
        oldPrice: null,
        badge: null,
        color: 'gray',
        sizes: ['XS', 'S', 'M', 'L'],
        colors: [
            { name: 'Gray', hex: '#808080' },
            { name: 'Washed Black', hex: '#333' },
            { name: 'Stone', hex: '#b8a99a' }
        ],
        images: [
            'https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1564557287817-3785e38ec1f5?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1571945153237-4929e783af4a?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1618354691438-25bc04584c23?w=800&q=80&fit=crop'
        ],
        description: 'Lived-in luxury from the first wear. The Washed Crew Neck is garment-dyed and enzyme-washed to achieve a perfectly broken-in feel. Heavyweight cotton ensures it holds its shape wash after wash.',
        details: [
            '100% combed cotton, 240GSM heavyweight',
            'Garment-dyed and enzyme-washed',
            'Relaxed fit with set-in sleeves',
            'Double-needle stitching throughout',
            'Tonal Siya Clothing embroidery on back neck'
        ],
        fit: 'Relaxed fit — slightly wider and longer. True to size for a relaxed look. Model is 5\'11" / 180cm wearing size M.'
    },
    {
        id: 10,
        name: 'Tailored Wool Overcoat',
        category: 'Outerwear',
        subcategory: 'outerwear',
        gender: '',
        rating: 4.5,
        isNewArrival: false,
        isBestSeller: false,
        price: 1999,
        oldPrice: null,
        badge: null,
        color: 'black',
        sizes: ['M', 'L', 'XL'],
        colors: [
            { name: 'Black', hex: '#1a1a1a' },
            { name: 'Camel', hex: '#C19A6B' },
            { name: 'Charcoal', hex: '#444' }
        ],
        images: [
            'https://images.unsplash.com/photo-1539533113208-f6df8cc8b543?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1558171813-4c088753af8f?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1608063615781-e2ef8c73d114?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1548624313-0396c75e4b1a?w=800&q=80&fit=crop'
        ],
        description: 'Effortless sophistication. The Tailored Wool Overcoat is constructed from a premium Italian wool-cashmere blend, fully lined with a half-canvas construction for a natural drape that improves with wear.',
        details: [
            '80% Italian wool, 20% cashmere blend',
            'Half-canvas construction for natural drape',
            'Fully lined in cupro — smooth and breathable',
            'Notch lapel with functional buttonholes',
            'Two interior pockets, two exterior pockets'
        ],
        fit: 'Tailored fit — designed to layer over suits or knitwear. True to size. Model is 6\'1" / 185cm wearing size L.'
    },
    {
        id: 11,
        name: 'Classic Chino Trousers',
        category: 'Bottoms',
        subcategory: 'trousers',
        gender: '',
        rating: 4.5,
        isNewArrival: false,
        isBestSeller: false,
        price: 1199,
        oldPrice: null,
        badge: null,
        color: 'brown',
        sizes: ['S', 'M', 'L'],
        colors: [
            { name: 'Khaki', hex: '#8B6914' },
            { name: 'Navy', hex: '#1a2744' },
            { name: 'Black', hex: '#1a1a1a' }
        ],
        images: [
            'https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1594633312681-425c7b97ccd1?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1519722417352-7d6959729417?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1506629082955-511b1aa562c8?w=800&q=80&fit=crop'
        ],
        description: 'The elevated essential. Our Classic Chino Trousers are crafted from Portuguese stretch twill with a garment wash for softness. A modern slim-straight silhouette bridges smart-casual and weekend wear.',
        details: [
            '97% cotton, 3% elastane Portuguese twill',
            'Garment-washed for softness and color depth',
            'Slim-straight fit with medium rise',
            'Clean front — no pleats',
            'Bar-tacked stress points for durability'
        ],
        fit: 'Slim-straight fit — comfortable through the thigh with a straight leg. True to size. Model is 6\'0" / 183cm wearing size 32.'
    },
    {
        id: 12,
        name: 'Leather Weekender Bag',
        category: 'Accessories',
        subcategory: 'bags',
        gender: 'unisex',
        rating: 4.5,
        isNewArrival: false,
        isBestSeller: false,
        price: 1099,
        oldPrice: null,
        badge: 'New',
        color: 'brown',
        sizes: ['M'],
        colors: [
            { name: 'Brown', hex: '#8B6914' },
            { name: 'Black', hex: '#1a1a1a' }
        ],
        images: [
            'https://images.unsplash.com/photo-1548036328-c9fa89d128fa?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1553062407-98eeb64c6a62?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1590874103328-eac38a683ce7?w=800&q=80&fit=crop',
            'https://images.unsplash.com/photo-1547949003-9792a18a2601?w=800&q=80&fit=crop'
        ],
        description: 'Travel in style. The Leather Weekender Bag is crafted from vegetable-tanned leather with a spacious canvas-lined interior. Features a padded laptop sleeve, shoe compartment, and detachable shoulder strap.',
        details: [
            'Vegetable-tanned full-grain leather',
            'Canvas-lined interior with zip pocket',
            'Padded 15" laptop sleeve',
            'Separate shoe compartment',
            'Detachable, adjustable shoulder strap'
        ],
        fit: 'Dimensions: 52cm × 28cm × 24cm. Carry-on compliant for most airlines.'
    }
];

// Utility: find product by ID
function getProductById(id) {
    return PRODUCTS.find(p => p.id === parseInt(id)) || null;
}

// Utility: get image URL (returns as-is since URLs already include parameters)
function productImg(url, w = 800, q = 80) {
    if (url.includes('images.unsplash.com')) return url;
    return `${url}?w=${w}&q=${q}&fit=crop`;
}


