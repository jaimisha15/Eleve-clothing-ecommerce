# 🚀 ELEVÉ — Setup Guide

## Step 1: Database Setup

1. **XAMPP start karo** — Apache + MySQL dono

2. **phpMyAdmin kholo**: `http://localhost/phpmyadmin`

3. **database.sql import karo**:
   - Left panel mein "New" click karo
   - Database naam: `eleve_ecommerce` → Create
   - "Import" tab → `database.sql` file choose karo → Go

## Step 2: Admin Password Fix

Browser mein visit karo:
```
http://localhost/Eleve-clothing-ecommerce/php/fix_admin_password.php
```
Ek baar run karo, phir ye file delete kar do.

## Step 3: Admin Panel Access

```
URL:      http://localhost/Eleve-clothing-ecommerce/admin/login.php
Email:    admin@gmail.com
Password: admin
```

## Step 4: Store Access

```
http://localhost/Eleve-clothing-ecommerce/index.html
```

---

## Files Structure

```
Eleve E-commerce -main/
├── index.html          ← Homepage
├── products.html       ← Shop All
├── product-detail.html ← Product Detail
├── men.html, women.html, kids.html
├── checkout.html       ← ✅ NEW: Full checkout page
├── contact.html
├── about.html
├── app.js              ← Updated: PHP backend connected
├── styles.css
├── product-data.js
├── product-data-extra.js
├── database.sql        ← ✅ NEW: Complete MySQL schema
│
├── php/                ← ✅ NEW: PHP Backend
│   ├── config.php      ← DB config + helpers
│   ├── products.php    ← Products API
│   ├── auth.php        ← Login/Register/Logout API
│   ├── cart.php        ← Cart + Wishlist + Newsletter + Contact API
│   ├── orders.php      ← Orders API
│   └── fix_admin_password.php  (delete after use)
│
└── admin/              ← ✅ NEW: Full Admin Panel
    ├── login.php       ← Admin login
    ├── index.php       ← Dashboard with charts
    ├── products.php    ← Add/Edit/Delete products
    ├── orders.php      ← View/Update orders
    ├── users.php       ← Manage customers
    ├── messages.php    ← Contact messages
    ├── coupons.php     ← Coupon management
    ├── newsletter.php  ← Subscribers + CSV export
    ├── reviews.php     ← Product reviews
    ├── categories.php  ← Category management
    ├── settings.php    ← Site settings + Admin password
    └── logout.php
```

---

## Admin Panel Features

| Feature | Description |
|---------|-------------|
| 📊 Dashboard | Revenue charts, order stats, top products |
| 🛍️ Products | Add/Edit/Delete, images, sizes, colors |
| 📦 Orders | View/Update status, order details |
| 👥 Users | Customer list, block/unblock |
| 🏷️ Coupons | Percentage + fixed discount codes |
| 📧 Newsletter | Subscribers, CSV export |
| 💬 Messages | Contact form messages |
| ⭐ Reviews | Approve/Reject product reviews |
| ⚙️ Settings | Site config, shipping, admin password |

## New Features Added

- ✅ **PHP Backend** — REST API with PDO MySQL
- ✅ **Full Database** — 13 normalized tables
- ✅ **Admin Panel** — Complete with Charts.js dashboard
- ✅ **Checkout Page** — With COD/UPI/Card/NetBanking
- ✅ **Coupon System** — Percent + fixed discounts
- ✅ **User Auth** — Login/Register with bcrypt
- ✅ **Contact Form** — Saves to DB, visible in admin
- ✅ **Newsletter** — Saves to DB, CSV export
- ✅ **Order Management** — Full order lifecycle
- ✅ **Product Reviews** — Approve/reject system

## Troubleshooting

**DB Connection Error?**
→ Edit `php/config.php`: change DB_HOST, DB_USER, DB_PASS if needed

**Admin login not working?**
→ Run `fix_admin_password.php` first

**Products not loading?**
→ Check that `database.sql` was imported correctly
→ Static JS fallback still works without DB
