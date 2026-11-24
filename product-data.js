/* ============================================================
   ELEVÉ — Product Data Catalog
   ============================================================
   All product information in one place.
   Used by products.html, index.html, men.html, women.html,
   kids.html, and product-detail.html.
   Categorized strictly by Men, Women, and Kids.
   ============================================================ */

const PRODUCTS = [
  /* ============================================================
     MEN'S COLLECTION (T-Shirts & Jeans)
     ============================================================ */
  {
    id: 1,
    name: "Men's Noir Graphic Tee",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "men",
    rating: 4.8,
    isNewArrival: true,
    isBestSeller: true,
    price: 699,
    oldPrice: 899,
    badge: "Best Seller",
    color: "black",
    sizes: ["S", "M", "L", "XL", "XXL"],
    colors: [
      { name: "Black", hex: "#1a1a1a" },
      { name: "White", hex: "#f5f5f5" },
      { name: "Navy", hex: "#1a2744" }
    ],
    images: [
      "images/men/tshirt-1.jpg",
      "images/men/tshirt-2.jpg",
      "images/men/tshirt-3.jpg"
    ],
    description: "Signature streetwear graphic tee in 220GSM heavy combed cotton. Breathable, durable, and finished with a comfortable crew neck.",
    details: [
      "100% premium combed cotton",
      "220GSM heavyweight fabric",
      "Screen-printed chest graphic with water-based inks",
      "Pre-shrunk to maintain fit after washing",
      "Reinforced rib collar"
    ],
    fit: "Regular streetwear fit. True to size. Choose one size up for an oversized drape."
  },
  {
    id: 2,
    name: "Men's Minimalist Crew Neck Tee",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "men",
    rating: 4.6,
    isNewArrival: true,
    isBestSeller: false,
    price: 599,
    oldPrice: null,
    badge: "New",
    color: "white",
    sizes: ["S", "M", "L", "XL"],
    colors: [
      { name: "White", hex: "#f5f5f5" },
      { name: "Black", hex: "#1a1a1a" },
      { name: "Gray", hex: "#808080" }
    ],
    images: [
      "images/men/tshirt-2.jpg",
      "images/men/tshirt-1.jpg",
      "images/men/tshirt-3.jpg"
    ],
    description: "Everyday clean aesthetic crew neck t-shirt. Ultra-soft bio-washed cotton engineered for premium comfort and long-lasting wear.",
    details: [
      "100% bio-washed organic cotton",
      "190GSM medium weight",
      "Ribbed collar that holds shape",
      "Subtle ELEVÉ hem tag"
    ],
    fit: "Classic regular fit. True to size."
  },
  {
    id: 3,
    name: "Men's Urban Street Tee",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "men",
    rating: 4.7,
    isNewArrival: false,
    isBestSeller: true,
    price: 749,
    oldPrice: null,
    badge: "Popular",
    color: "black",
    sizes: ["S", "M", "L", "XL", "XXL"],
    colors: [
      { name: "Black", hex: "#1a1a1a" },
      { name: "Olive", hex: "#556B2F" }
    ],
    images: [
      "images/men/tshirt-3.jpg",
      "images/men/tshirt-4.jpg",
      "images/men/tshirt-2.jpg"
    ],
    description: "Modern street style graphic t-shirt crafted for weekend casuals and layering beneath jackets.",
    details: [
      "100% cotton jersey knit",
      "Dropped shoulders",
      "Soft hand-feel print on front"
    ],
    fit: "Relaxed fit with slightly dropped shoulders."
  },
  {
    id: 4,
    name: "Men's Vintage Wash Graphic Tee",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "men",
    rating: 4.5,
    isNewArrival: false,
    isBestSeller: false,
    price: 699,
    oldPrice: 849,
    badge: "Sale",
    color: "gray",
    sizes: ["M", "L", "XL"],
    colors: [
      { name: "Charcoal", hex: "#333333" },
      { name: "White", hex: "#f5f5f5" }
    ],
    images: [
      "images/men/tshirt-4.jpg",
      "images/men/tshirt-5.jpg"
    ],
    description: "Acid-washed vintage finish gives this tee an authentic retro feel with supreme softness.",
    details: [
      "Garment-dyed and enzyme washed",
      "Comfortable breathable knit",
      "Seamless tubular body construction"
    ],
    fit: "Standard regular fit."
  },
  {
    id: 5,
    name: "Men's Oversized Statement Tee",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "men",
    rating: 4.9,
    isNewArrival: true,
    isBestSeller: true,
    price: 799,
    oldPrice: null,
    badge: "New",
    color: "black",
    sizes: ["S", "M", "L", "XL"],
    colors: [
      { name: "Black", hex: "#1a1a1a" },
      { name: "Beige", hex: "#d4b483" }
    ],
    images: [
      "images/men/tshirt-5.jpg",
      "images/men/tshirt-6.jpg"
    ],
    description: "Heavy drape oversized tee featuring custom typography. A cornerstone piece of contemporary streetwear.",
    details: [
      "240GSM heavyweight cotton",
      "Oversized boxy drape",
      "Reinforced twin-needle stitching"
    ],
    fit: "Generously oversized. Size down for regular fit."
  },
  {
    id: 6,
    name: "Men's Athletics Printed T-Shirt",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "men",
    rating: 4.4,
    isNewArrival: false,
    isBestSeller: false,
    price: 649,
    oldPrice: null,
    badge: null,
    color: "navy",
    sizes: ["S", "M", "L", "XL"],
    colors: [
      { name: "Navy", hex: "#1a2744" },
      { name: "White", hex: "#f5f5f5" }
    ],
    images: [
      "images/men/tshirt-6.jpg",
      "images/men/tshirt-7.jpg"
    ],
    description: "Athletics-inspired graphic t-shirt engineered with stretch cotton for all-day motion and breathability.",
    details: [
      "95% cotton, 5% elastane for stretch",
      "Anti-odor bio-treatment",
      "Athletic chest fit"
    ],
    fit: "Athletic regular fit."
  },
  {
    id: 7,
    name: "Men's Signature Essential Tee",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "men",
    rating: 4.6,
    isNewArrival: true,
    isBestSeller: false,
    price: 679,
    oldPrice: null,
    badge: "New",
    color: "white",
    sizes: ["S", "M", "L", "XL", "XXL"],
    colors: [
      { name: "White", hex: "#f5f5f5" },
      { name: "Black", hex: "#1a1a1a" }
    ],
    images: [
      "images/men/tshirt-7.jpg",
      "images/men/tshirt-1.jpg"
    ],
    description: "Clean lines and versatile design make this essential t-shirt an irreplaceable base layer for any outfit.",
    details: [
      "100% Supima long-staple cotton",
      "Silky-smooth finish",
      "Pre-shrunk fabric"
    ],
    fit: "True to size."
  },
  {
    id: 8,
    name: "Men's Slim Fit Washed Jeans",
    category: "Jeans",
    subcategory: "jeans",
    gender: "men",
    rating: 4.8,
    isNewArrival: true,
    isBestSeller: true,
    price: 1399,
    oldPrice: 1799,
    badge: "Best Seller",
    color: "indigo",
    sizes: ["30", "32", "34", "36"],
    colors: [
      { name: "Indigo", hex: "#2E2B5F" },
      { name: "Black", hex: "#1a1a1a" }
    ],
    images: [
      "images/men/jeans-1.jpg",
      "images/men/jeans-2.jpg"
    ],
    description: "Premium stretch denim engineered for comfortable all-day movement. Tailored slim through the thigh and calf.",
    details: [
      "98% cotton denim, 2% elastane",
      "12.5oz authentic denim weight",
      "Classic five-pocket styling",
      "Antique copper button fly and rivets"
    ],
    fit: "Slim fit with slight taper at ankle. True to waist size."
  },
  {
    id: 9,
    name: "Men's Distressed Street Denim",
    category: "Jeans",
    subcategory: "jeans",
    gender: "men",
    rating: 4.7,
    isNewArrival: false,
    isBestSeller: true,
    price: 1499,
    oldPrice: 1999,
    badge: "Sale",
    color: "blue",
    sizes: ["30", "32", "34", "36"],
    colors: [
      { name: "Light Wash", hex: "#87ceeb" },
      { name: "Medium Blue", hex: "#3b5998" }
    ],
    images: [
      "images/men/jeans-2.jpg",
      "images/men/jeans-3.jpg"
    ],
    description: "Hand-distressed ripped accents with subtle fading for an effortless, rugged streetwear aesthetic.",
    details: [
      "99% cotton, 1% spandex",
      "Hand-finished distress detailing",
      "Reinforced pockets and stress points"
    ],
    fit: "Slim straight fit."
  },
  {
    id: 10,
    name: "Men's Dark Indigo Tapered Jeans",
    category: "Jeans",
    subcategory: "jeans",
    gender: "men",
    rating: 4.6,
    isNewArrival: true,
    isBestSeller: false,
    price: 1299,
    oldPrice: null,
    badge: "New",
    color: "dark-blue",
    sizes: ["28", "30", "32", "34", "36"],
    colors: [
      { name: "Dark Indigo", hex: "#1c2841" }
    ],
    images: [
      "images/men/jeans-3.jpg",
      "images/men/jeans-4.jpg"
    ],
    description: "Deep indigo wash with clean tone-on-tone stitching — transitions easily from office to evening dinner.",
    details: [
      "Raw-look dark rinse denim",
      "Tapered leg opening",
      "Zero sagging stretch recovery"
    ],
    fit: "Tapered regular fit."
  },
  {
    id: 11,
    name: "Men's Relaxed Straight Leg Jeans",
    category: "Jeans",
    subcategory: "jeans",
    gender: "men",
    rating: 4.5,
    isNewArrival: false,
    isBestSeller: false,
    price: 1349,
    oldPrice: null,
    badge: null,
    color: "blue",
    sizes: ["30", "32", "34", "36", "38"],
    colors: [
      { name: "Medium Blue", hex: "#3b5998" },
      { name: "Black", hex: "#1a1a1a" }
    ],
    images: [
      "images/men/jeans-4.jpg",
      "images/men/jeans-1.jpg"
    ],
    description: "Timeless straight leg jeans offering ample room through thigh and knee with authentic vintage wash.",
    details: [
      "100% durable cotton denim",
      "Standard straight cut",
      "Heavy-duty YKK brass zipper"
    ],
    fit: "Relaxed straight fit. True to size."
  },

  /* ============================================================
     WOMEN'S COLLECTION (T-Shirts & Jeans)
     ============================================================ */
  {
    id: 12,
    name: "Women's Cropped Graphic T-Shirt",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "women",
    rating: 4.9,
    isNewArrival: true,
    isBestSeller: true,
    price: 599,
    oldPrice: 799,
    badge: "Best Seller",
    color: "white",
    sizes: ["XS", "S", "M", "L"],
    colors: [
      { name: "White", hex: "#f5f5f5" },
      { name: "Black", hex: "#1a1a1a" },
      { name: "Pink", hex: "#FFB6C1" }
    ],
    images: [
      "images/women/tshirt-1.jpg",
      "images/women/tshirt-2.jpg",
      "images/women/tshirt-3.jpg"
    ],
    description: "Chic cropped silhouette cut from breathable organic cotton. Perfectly pairs with high-waist jeans or skirts.",
    details: [
      "100% soft organic cotton",
      "Trendy cropped cut sits at natural waist",
      "Fade-resistant front art print",
      "Ribbed crew neckline"
    ],
    fit: "Boxy cropped fit. True to size."
  },
  {
    id: 13,
    name: "Women's Minimalist Aesthetic Tee",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "women",
    rating: 4.7,
    isNewArrival: true,
    isBestSeller: false,
    price: 649,
    oldPrice: null,
    badge: "New",
    color: "black",
    sizes: ["XS", "S", "M", "L", "XL"],
    colors: [
      { name: "Black", hex: "#1a1a1a" },
      { name: "Beige", hex: "#d4b483" }
    ],
    images: [
      "images/women/tshirt-2.jpg",
      "images/women/tshirt-1.jpg"
    ],
    description: "Understated elegance for daily styling. Made with premium modal-cotton blend for an ultra-draping luxurious hand feel.",
    details: [
      "60% cotton, 40% modal",
      "Silky touch against skin",
      "Tailored feminine shoulder seam"
    ],
    fit: "Fitted yet comfortable silhouette."
  },
  {
    id: 14,
    name: "Women's Oversized Graphic Tee",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "women",
    rating: 4.8,
    isNewArrival: false,
    isBestSeller: true,
    price: 699,
    oldPrice: null,
    badge: "Popular",
    color: "gray",
    sizes: ["S", "M", "L"],
    colors: [
      { name: "Heather Gray", hex: "#808080" },
      { name: "White", hex: "#f5f5f5" }
    ],
    images: [
      "images/women/tshirt-3.jpg",
      "images/women/tshirt-4.jpg"
    ],
    description: "Boyfriend-fit oversized graphic tee with dropped shoulders for effortless brunch and streetwear vibes.",
    details: [
      "100% combed cotton jersey",
      "Relaxed dropped shoulders",
      "Screen-printed typography"
    ],
    fit: "Intentionally oversized. Order usual size."
  },
  {
    id: 15,
    name: "Women's Casual Cotton Daily Tee",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "women",
    rating: 4.5,
    isNewArrival: false,
    isBestSeller: false,
    price: 549,
    oldPrice: 699,
    badge: "Sale",
    color: "white",
    sizes: ["XS", "S", "M", "L"],
    colors: [
      { name: "White", hex: "#f5f5f5" },
      { name: "Lilac", hex: "#C8A2C8" }
    ],
    images: [
      "images/women/tshirt-4.jpg",
      "images/women/tshirt-5.jpg"
    ],
    description: "Breathable everyday staple t-shirt with soft bio-polish for anti-pilling and lasting color richness.",
    details: [
      "100% bio-polished cotton",
      "Breathable lightweight fabric",
      "Tagless comfort neck label"
    ],
    fit: "Regular fit."
  },
  {
    id: 16,
    name: "Women's Ribbed Fitted T-Shirt",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "women",
    rating: 4.6,
    isNewArrival: true,
    isBestSeller: false,
    price: 629,
    oldPrice: null,
    badge: "New",
    color: "black",
    sizes: ["XS", "S", "M", "L"],
    colors: [
      { name: "Black", hex: "#1a1a1a" },
      { name: "Olive", hex: "#556B2F" }
    ],
    images: [
      "images/women/tshirt-5.jpg",
      "images/women/tshirt-1.jpg"
    ],
    description: "Form-flattering fine-rib knit tee that hugs your curves effortlessly. Great for tucking into denim.",
    details: [
      "95% cotton, 5% spandex rib knit",
      "Fine vertical ribbed texture",
      "Shape-holding elastane"
    ],
    fit: "Slim body-hugging fit."
  },
  {
    id: 17,
    name: "Women's High-Rise Flare Jeans",
    category: "Jeans",
    subcategory: "jeans",
    gender: "women",
    rating: 4.9,
    isNewArrival: true,
    isBestSeller: true,
    price: 1499,
    oldPrice: 1999,
    badge: "Best Seller",
    color: "blue",
    sizes: ["26", "28", "30", "32"],
    colors: [
      { name: "Medium Denim", hex: "#3b5998" },
      { name: "Dark Blue", hex: "#1c2841" }
    ],
    images: [
      "images/women/jeans-flare.webp",
      "images/women/jeans-1.jpg",
      "images/women/jeans-2.jpg"
    ],
    description: "Retro 70s inspired high-rise flare jeans tailored to elongate your legs. Features sculpting stretch denim.",
    details: [
      "98% cotton, 2% spandex sculpting denim",
      "Flattering 11-inch high rise",
      "Fitted through hip & knee, flaring at ankle",
      "Signature five-pocket construction"
    ],
    fit: "High rise with flare hem. True to waist size."
  },
  {
    id: 18,
    name: "Women's Mid-Rise Wide Leg Washed Jeans",
    category: "Jeans",
    subcategory: "jeans",
    gender: "women",
    rating: 4.8,
    isNewArrival: true,
    isBestSeller: false,
    price: 1599,
    oldPrice: null,
    badge: "New",
    color: "light-blue",
    sizes: ["26", "28", "30", "32"],
    colors: [
      { name: "Light Wash", hex: "#87ceeb" },
      { name: "Vintage Indigo", hex: "#2E2B5F" }
    ],
    images: [
      "images/women/jeans-wide-leg.avif",
      "images/women/jeans-2.jpg"
    ],
    description: "The trending wide-leg silhouette. Relaxed washed denim with a loose, comfortable fit through the leg.",
    details: [
      "100% premium cotton denim",
      "Modern wide-leg cut with clean drape",
      "Mid-rise waistband with belt loops"
    ],
    fit: "Wide-leg relaxed fit."
  },
  {
    id: 19,
    name: "Women's Straight Cut Vintage Denim",
    category: "Jeans",
    subcategory: "jeans",
    gender: "women",
    rating: 4.6,
    isNewArrival: false,
    isBestSeller: false,
    price: 1299,
    oldPrice: null,
    badge: null,
    color: "blue",
    sizes: ["26", "28", "30", "32", "34"],
    colors: [
      { name: "Classic Blue", hex: "#3b5998" }
    ],
    images: [
      "images/women/jeans-1.jpg",
      "images/women/jeans-3.jpg"
    ],
    description: "Timeless straight-leg cut that flatters every silhouette. Medium wash denim with gentle whiskering.",
    details: [
      "99% cotton, 1% elastane",
      "Authentic vintage wash",
      "Medium rise sits just below belly button"
    ],
    fit: "Straight leg. True to size."
  },
  {
    id: 20,
    name: "Women's Slim Fit Ankle Jeans",
    category: "Jeans",
    subcategory: "jeans",
    gender: "women",
    rating: 4.7,
    isNewArrival: false,
    isBestSeller: true,
    price: 1399,
    oldPrice: 1799,
    badge: "Sale",
    color: "indigo",
    sizes: ["26", "28", "30", "32"],
    colors: [
      { name: "Dark Indigo", hex: "#1c2841" },
      { name: "Black", hex: "#1a1a1a" }
    ],
    images: [
      "images/women/jeans-2.jpg",
      "images/women/jeans-1.jpg"
    ],
    description: "Second-skin ankle jeans crafted with high-recovery stretch for all-day comfort without bagging at knees.",
    details: [
      "Power-stretch denim blend",
      "Ankle-grazing length",
      "Clean back pocket design"
    ],
    fit: "Slim fit ankle crop."
  },
  {
    id: 21,
    name: "Women's Classic Denim Trousers",
    category: "Jeans",
    subcategory: "jeans",
    gender: "women",
    rating: 4.5,
    isNewArrival: false,
    isBestSeller: false,
    price: 1249,
    oldPrice: null,
    badge: null,
    color: "blue",
    sizes: ["26", "28", "30", "32"],
    colors: [
      { name: "Medium Blue", hex: "#3b5998" }
    ],
    images: [
      "images/women/jeans-3.jpg",
      "images/women/jeans-flare.webp"
    ],
    description: "Essential denim trousers with a smooth flat front and comfortable straight silhouette.",
    details: [
      "Soft washed cotton twill denim",
      "Clean hem finish",
      "Belt loops and zip closure"
    ],
    fit: "Comfort regular fit."
  },

  /* ============================================================
     KIDS' COLLECTION (Clothes, T-Shirts & Pants)
     ============================================================ */
  {
    id: 22,
    name: "Kids Star Printed Crew Neck Tee",
    category: "T-Shirts",
    subcategory: "t-shirts",
    gender: "kids",
    rating: 4.9,
    isNewArrival: true,
    isBestSeller: true,
    price: 449,
    oldPrice: 599,
    badge: "Best Seller",
    color: "yellow",
    sizes: ["3-4Y", "5-6Y", "7-8Y", "9-10Y", "11-12Y"],
    colors: [
      { name: "Star Print", hex: "#FFD700" },
      { name: "White", hex: "#f5f5f5" },
      { name: "Navy", hex: "#1a2744" }
    ],
    images: [
      "images/kids/kids-star-tee.avif",
      "images/kids/kids-1.jpg",
      "images/kids/kids-2.jpg"
    ],
    description: "Fun all-over star print crew neck t-shirt in lightweight breathable cotton. Gentle on young skin for all-day play.",
    details: [
      "100% pure organic cotton",
      "Safe, non-toxic baby-grade dye",
      "Tagless itch-free neckline",
      "Machine washable with color-fast print"
    ],
    fit: "Regular kids fit. Order standard age size."
  },
  {
    id: 23,
    name: "Kids Durable Summer Denim Pants",
    category: "Jeans",
    subcategory: "jeans",
    gender: "kids",
    rating: 4.8,
    isNewArrival: true,
    isBestSeller: true,
    price: 699,
    oldPrice: 899,
    badge: "Popular",
    color: "blue",
    sizes: ["3-4Y", "5-6Y", "7-8Y", "9-10Y", "11-12Y"],
    colors: [
      { name: "Medium Denim", hex: "#3b5998" },
      { name: "Light Wash", hex: "#87ceeb" }
    ],
    images: [
      "images/kids/kids-denim-pants.avif",
      "images/kids/kids-3.jpg",
      "images/kids/kids-4.jpg"
    ],
    description: "Reinforced knee denim pants designed for rough-and-tumble active kids. Features an elastic waistband for effortless dressing.",
    details: [
      "98% soft washed cotton, 2% stretch",
      "Elasticated comfort waist with drawstring",
      "Reinforced double-stitched knees",
      "Four functional deep pockets"
    ],
    fit: "Comfortable relaxed fit with stretch waist."
  },
  {
    id: 24,
    name: "Kids Explorer Playtime Outfit Set",
    category: "Co-Ord Sets",
    subcategory: "coord-sets",
    gender: "kids",
    rating: 4.7,
    isNewArrival: true,
    isBestSeller: false,
    price: 799,
    oldPrice: null,
    badge: "New",
    color: "multicolor",
    sizes: ["3-4Y", "5-6Y", "7-8Y", "9-10Y"],
    colors: [
      { name: "Multi", hex: "#FF6347" },
      { name: "Blue Set", hex: "#4a90d9" }
    ],
    images: [
      "images/kids/kids-1.jpg",
      "images/kids/kids-2.jpg"
    ],
    description: "Complete coordinated outfit set for boys and girls. Soft cotton tee paired with matching easy-pull bottoms.",
    details: [
      "Two-piece coordinated set",
      "100% combed cotton jersey",
      "Elastic waistband bottoms"
    ],
    fit: "Standard regular fit."
  },
  {
    id: 25,
    name: "Kids Casual Summer Duo Set",
    category: "Co-Ord Sets",
    subcategory: "coord-sets",
    gender: "kids",
    rating: 4.6,
    isNewArrival: false,
    isBestSeller: false,
    price: 649,
    oldPrice: null,
    badge: null,
    color: "blue",
    sizes: ["3-4Y", "5-6Y", "7-8Y", "9-10Y"],
    colors: [
      { name: "Blue / Gray", hex: "#4a90d9" }
    ],
    images: [
      "images/kids/kids-2.jpg",
      "images/kids/kids-3.jpg"
    ],
    description: "Breathable cotton matching pair engineered for outdoor park days, summer vacations, and family outings.",
    details: [
      "Lightweight summer cotton knit",
      "Contrast rib trims",
      "Wash-and-wear easy care"
    ],
    fit: "Regular fit."
  },
  {
    id: 26,
    name: "Kids Adventure Active Wear Set",
    category: "Co-Ord Sets",
    subcategory: "coord-sets",
    gender: "kids",
    rating: 4.5,
    isNewArrival: false,
    isBestSeller: false,
    price: 599,
    oldPrice: 749,
    badge: "Sale",
    color: "red",
    sizes: ["3-4Y", "5-6Y", "7-8Y", "9-10Y"],
    colors: [
      { name: "Red / Navy", hex: "#c0392b" }
    ],
    images: [
      "images/kids/kids-3.jpg",
      "images/kids/kids-4.jpg"
    ],
    description: "Vibrant and energetic activewear set with soft interior fleece for cooler mornings and playground fun.",
    details: [
      "Cotton-poly active fleece",
      "Cuffed sleeves and ankles",
      "Color-blocked sporty panels"
    ],
    fit: "Relaxed sporty fit."
  },
  {
    id: 27,
    name: "Kids Sunshine Weekend Co-Ord",
    category: "Co-Ord Sets",
    subcategory: "coord-sets",
    gender: "kids",
    rating: 4.8,
    isNewArrival: true,
    isBestSeller: true,
    price: 699,
    oldPrice: null,
    badge: "Best Seller",
    color: "yellow",
    sizes: ["3-4Y", "5-6Y", "7-8Y", "9-10Y"],
    colors: [
      { name: "Sunshine Yellow", hex: "#FFD700" }
    ],
    images: [
      "images/kids/kids-4.jpg",
      "images/kids/kids-5.jpg"
    ],
    description: "Bright and cheerful weekend coordinated outfit. Premium cotton knit ensures non-stop comfort.",
    details: [
      "100% premium cotton",
      "Fun friendly graphics",
      "Covered elastic waistband"
    ],
    fit: "True to size."
  },
  {
    id: 28,
    name: "Kids Daily Smiles Casual Outfit",
    category: "Co-Ord Sets",
    subcategory: "coord-sets",
    gender: "kids",
    rating: 4.4,
    isNewArrival: false,
    isBestSeller: false,
    price: 549,
    oldPrice: null,
    badge: null,
    color: "green",
    sizes: ["3-4Y", "5-6Y", "7-8Y", "9-10Y", "11-12Y"],
    colors: [
      { name: "Mint Green", hex: "#98FB98" }
    ],
    images: [
      "images/kids/kids-5.jpg",
      "images/kids/kids-6.jpg"
    ],
    description: "Everyday easy-dressing set that makes school mornings and weekend play effortless.",
    details: [
      "Bio-washed cotton jersey",
      "Resists shrinking & pilling",
      "Durable flatlock seams"
    ],
    fit: "Regular fit."
  },
  {
    id: 29,
    name: "Kids Bright Smile Graphic Outfit",
    category: "Co-Ord Sets",
    subcategory: "coord-sets",
    gender: "kids",
    rating: 4.7,
    isNewArrival: true,
    isBestSeller: false,
    price: 599,
    oldPrice: null,
    badge: "New",
    color: "orange",
    sizes: ["3-4Y", "5-6Y", "7-8Y", "9-10Y"],
    colors: [
      { name: "Tangerine", hex: "#FFA500" }
    ],
    images: [
      "images/kids/kids-6.jpg",
      "images/kids/kids-star-tee.avif"
    ],
    description: "Playful graphic print outfit designed with supreme softness so kids can move, jump, and play freely.",
    details: [
      "100% soft organic cotton",
      "Breathable summer weight",
      "Machine wash friendly"
    ],
    fit: "Regular comfortable fit."
  }
];

// Utility: find product by ID
function getProductById(id) {
  return PRODUCTS.find(p => p.id === parseInt(id)) || null;
}

// Utility: get image URL (returns local image path or Unsplash URL cleanly)
function productImg(url, w = 800, q = 80) {
  if (!url) return 'images/men/tshirt-1.jpg';
  if (url.includes('images.unsplash.com')) {
    return url.replace(/w=\d+/, 'w=' + w);
  }
  return url;
}
