/* ============================================================
   ELEVÉ — Extended Product Catalog
   ============================================================
   Reserved for optional future expansion.
   All current Men, Women, and Kids products are defined in
   product-data.js. Footwear has been completely removed.
   ============================================================ */

const EXTRA_PRODUCTS = [];

if (typeof PRODUCTS !== 'undefined' && EXTRA_PRODUCTS.length > 0) {
  PRODUCTS.push(...EXTRA_PRODUCTS);
}
