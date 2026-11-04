# ELEVÉ — Luxury Fashion & Streetwear E-Commerce Platform

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777bb4.svg?style=flat-square&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL Database](https://img.shields.io/badge/MySQL-Database-4479A1.svg?style=flat-square&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Frontend](https://img.shields.io/badge/Frontend-HTML5%20%7C%20CSS3%20%7C%20JavaScript-E34F26.svg?style=flat-square&logo=html5&logoColor=white)](https://developer.mozilla.org/)
[![License](https://img.shields.io/badge/License-Academic%20Project-green.svg?style=flat-square)]()
[![Status](https://img.shields.io/badge/Project%20Status-Completed-success.svg?style=flat-square)]()

An end-to-end, full-stack e-commerce web application designed for **ELEVÉ**, a contemporary luxury fashion and streetwear brand. The platform provides a sleek, high-fashion storefront for customers coupled with a feature-packed administrative dashboard and a robust PHP & MySQL backend.

---

## 🎓 Academic Project Guidance & Mentorship

> **Project Guidance:**  
> This project was completed under the guidance of **Dr. Anjum Y. Mansuri**, Assistant Professor, Computer Applications, LJ Institute of Computer Applications, LJ University, Ahmedabad.  
> **GitHub:** [@anjum-mansuri](https://github.com/anjum-mansuri)

---

## 📌 Table of Contents

- [Project Overview](#-project-overview)
- [Key Features](#-key-features)
  - [1. Customer Front-End Experience](#1-customer-front-end-experience)
  - [2. Shopping & Checkout System](#2-shopping--checkout-system)
  - [3. Admin Management Portal](#3-admin-management-portal)
  - [4. Backend Architecture & Security](#4-backend-architecture--security)
- [Technology Stack](#-technology-stack)
- [Database Architecture](#-database-architecture)
- [Directory Structure](#-directory-structure)
- [Installation & Setup Guide](#-installation--setup-guide)
  - [Prerequisites](#prerequisites)
  - [Step 1: Clone / Copy Files to XAMPP](#step-1-clone--copy-files-to-xampp)
  - [Step 2: Database Setup in phpMyAdmin](#step-2-database-setup-in-phpmyadmin)
  - [Step 3: Admin & User Credentials](#step-3-admin--user-credentials)
  - [Step 4: Launching the Application](#step-4-launching-the-application)
- [REST API Endpoints](#-rest-api-endpoints)
- [Academic Acknowledgment & Disclaimers](#-academic-acknowledgment--disclaimers)

---

## 📖 Project Overview

**ELEVÉ** is a modern e-commerce solution developed as part of the **User Defined Project (UDP)** curriculum. It provides an immersive shopping environment inspired by top-tier global luxury streetwear brands. 

The application solves common e-commerce requirements:
- **Instant Product Discovery:** Search, filter, and view curated collections with zero latency.
- **Cart & Wishlist Synchronization:** Both client-side state and server-side session persistence.
- **Order Pipeline:** Multi-step checkout with multiple payment methods and order tracking.
- **Centralized Administration:** Complete data-driven control over products, orders, categories, customer feedback, and sales analytics.

---

## 🌟 Key Features

### 1. Customer Front-End Experience
- **Luxury Brand Aesthetic:** High-end dark & gold-accented typography, fluid responsive grid, micro-animations, and custom scrollbars.
- **Curated Collections:** Dedicated department portals for **Women**, **Men**, and **Kids**, with category badges (*New Arrivals*, *Best Sellers*, *Featured*).
- **Dynamic Product Catalog:** Real-time search bar, sorting (price low-high, high-low, rating), gender filters, and live category chips.
- **Interactive Product Details:**
  - Multi-image gallery with thumbnail selection.
  - Interactive Size Selector (`XS`, `S`, `M`, `L`, `XL`, `XXL`).
  - Dynamic Color Swatch picker with real-time UI feedback.
  - Live stock display and quantity counters.
  - Customer review display with star ratings.
- **Mobile-First Responsive Layout:** Persistent bottom navigation bar for effortless single-thumb mobile browsing across all device sizes.

### 2. Shopping & Checkout System
- **Slide-Out Cart & Wishlist Drawers:** Off-canvas flyout panels that allow reviewing items and adjusting quantities without leaving the current page.
- **Dedicated Checkout Page (`checkout.html`):**
  - Customer contact details & multi-field shipping address form.
  - Order summary breakdown (Subtotal, Shipping fee, Discount calculation, Final total).
  - **Coupon System:** Promo code validation with percentage and flat-rate discounts.
  - **Payment Options:** Support for Cash on Delivery (COD), UPI / QR Code, Credit/Debit Card, and Net Banking.
  - Instant order confirmation and receipt display.

### 3. Admin Management Portal (`/admin`)
- **Interactive Dashboard (`index.php`):**
  - KPI summary metrics (Total Revenue, Total Orders, Active Products, Total Users).
  - Visual analytics with Chart.js (monthly sales trend graphs & order status distribution).
  - Recent orders feed and low-stock alerts.
- **Product Management (`products.php`):**
  - Add, edit, delete, and view products.
  - Multi-image URL management, size tagging, color assigning, stock tracking, and pricing control.
- **Order Processing (`orders.php`):**
  - Full order inspection (items ordered, customer address, payment mode, totals).
  - Real-time status updater (`Pending` → `Confirmed` → `Processing` → `Shipped` → `Delivered` → `Cancelled`).
- **User Management (`users.php`):** Manage registered customers, view roles, and toggle account activation status.
- **Category Control (`categories.php`):** Create and organize fashion taxonomy with custom slugs and banners.
- **Coupon Manager (`coupons.php`):** Issue custom coupon codes, specify discount percentage or flat amount, expiry dates, and usage limits.
- **Review Moderation (`reviews.php`):** Approve or reject customer reviews before they appear publicly.
- **Customer Inquiries (`messages.php`):** Read and manage inquiries sent through the Contact Us form.
- **Newsletter Management (`newsletter.php`):** View subscriber list and export contacts directly to CSV.
- **Store Settings (`settings.php`):** Configure site name, contact email, currency, shipping fees, tax rates, and administrator password.

### 4. Backend Architecture & Security
- **Modern PHP REST API:** Modular endpoints for authentication, cart, orders, and products.
- **Secure Authentication:** `password_hash()` and `password_verify()` (Bcrypt) for user and admin credentials.
- **SQL Injection Prevention:** 100% prepared statements via PHP Data Objects (PDO).
- **Session Security:** Strict session cookies with `HttpOnly` and `SameSite` flags.
- **Error Handling:** Standardized JSON responses with HTTP status codes.

---

## 🛠️ Technology Stack

| Layer | Technologies Used |
|---|---|
| **Frontend** | HTML5, CSS3, Vanilla JavaScript (ES6+), Font Awesome / Unicode Icons |
| **Backend** | PHP 8.x (RESTful API, Session Management, PDO) |
| **Database** | MySQL (13 Relational Normalized Tables, Foreign Key Constraints) |
| **Data Visualization** | Chart.js (Admin Analytics Dashboard) |
| **Development Environment** | XAMPP (Apache Web Server & MySQL), VS Code |

---

## 🗄️ Database Architecture

The system utilizes an optimized MySQL database (`eleve_ecommerce`) composed of 13 interconnected tables:

- `users` — Customer accounts, password hashes, and user roles (`user`, `admin`).
- `categories` — Product categories with department mapping (Men, Women, Kids, Unisex).
- `products` — Core inventory details, pricing, discount badges, ratings, and descriptions.
- `product_images` — Image URLs associated with each product.
- `product_sizes` & `product_colors` — Available product variations.
- `orders` — Order headers with shipping information, order status, and payment details.
- `order_items` — Itemized line records for each order (product, size, color, quantity, price).
- `cart_items` & `wishlist_items` — Server-side persistent cart and wishlist storage.
- `coupons` — Promotional codes, discount parameters, and expiration timestamps.
- `reviews` — User product ratings, comments, and admin approval flags.
- `messages` — Submissions from the customer contact form.
- `newsletter` — Email subscriptions with timestamp logging.
- `settings` — Key-value store for application-wide parameters.

---

## 📂 Directory Structure

```text
Eleve-clothing-ecommerce/
│
├── .gitignore                         # Git ignore configuration
├── README.md                          # Repository overview and documentation
├── SETUP_GUIDE.md                     # Quick setup instructions
│
├── index.html                         # Storefront Home Page
├── products.html                      # All Products / Catalog
├── product-detail.html                # Product Detail Page
├── men.html                           # Men's Collection
├── women.html                         # Women's Collection
├── kids.html                          # Kids' Collection
├── about.html                         # About Brand & Story
├── contact.html                       # Customer Support & Form
├── checkout.html                      # Order Checkout & Payment
├── 404.html                           # Not Found Error Page
├── login.php                          # Customer Login
├── register.php                       # Customer Registration
├── logout.php                         # Session Logout
├── styles.css                         # Master Stylesheet (Variables, Responsive UI)
├── app.js                             # Client-side Interactions & State Logic
├── product-data.js                    # Catalog Dataset / Client Fallback
├── product-data-extra.js              # Additional Product Mock Data
├── database.sql                       # Complete MySQL Schema & Initial Data
├── manifest.json                      # PWA Web App Manifest
│
├── admin/                             # Admin Management Portal
│   ├── index.php                      # Admin Dashboard with Sales Charts
│   ├── login.php / logout.php         # Admin Authentication
│   ├── products.php                   # Product Inventory CRUD
│   ├── orders.php                     # Order Tracking & Status
│   ├── users.php                      # Customer Account Management
│   ├── categories.php                 # Category Management
│   ├── coupons.php                    # Coupon & Discount Codes
│   ├── reviews.php                    # Product Review Moderation
│   ├── messages.php                   # Contact Form Inbox
│   ├── newsletter.php                 # Newsletter Subscribers & CSV Export
│   ├── settings.php                   # Store Configuration & Password Reset
│   └── includes/                      # Admin Layout Templates & Partials
│
└── php/                               # Backend REST API
    ├── config.php                     # PDO Connection, CORS & Helpers
    ├── auth.php                       # Authentication API (Login/Register/Session)
    ├── products.php                   # Products API (Search/Filter/List/Detail)
    ├── cart.php                       # Cart, Wishlist, Contact & Newsletter API
    ├── orders.php                     # Orders Creation & History API
    └── fix_admin_password.php         # Admin Password Reset Utility
```

---

## 🚀 Installation & Setup Guide

### Prerequisites
- [XAMPP](https://www.apachefriends.org/) (with **PHP 8.0+** and **MySQL / MariaDB**)
- Modern web browser (Chrome, Edge, Firefox, or Safari)
- Git (optional, for cloning)

---

### Step 1: Clone / Copy Files to XAMPP

1. Copy the project folder into your XAMPP `htdocs` directory:
   ```text
   C:\xampp\htdocs\Eleve-clothing-ecommerce
   ```
2. Start **Apache** and **MySQL** modules from the **XAMPP Control Panel**.

---

### Step 2: Database Setup in phpMyAdmin

1. Open your web browser and navigate to:
   ```text
   http://localhost/phpmyadmin
   ```
2. In the left panel, click **New** to create a database:
   - Database name: `eleve_ecommerce`
   - Collation: `utf8mb4_unicode_ci`
   - Click **Create**.
3. Select the `eleve_ecommerce` database, then navigate to the **Import** tab.
4. Click **Choose File** and select `database.sql` from the project root.
5. Click **Import** (or **Go**) at the bottom. All 13 tables with starter data will be created automatically.

---

### Step 3: Admin & User Credentials

#### 👑 Admin Portal Credentials
- **Login URL:** `http://localhost/Eleve-clothing-ecommerce/admin/login.php`
- **Email:** `admin@gmail.com`
- **Password:** `123` *(or `admin`)*

> **Tip:** If you need to re-hash the admin password, run:  
> `http://localhost/Eleve-clothing-ecommerce/php/fix_admin_password.php` once in your browser.

#### 🛍️ Customer Test Account
- You can register a new account on the storefront or use any existing customer entry in the database.

---

### Step 4: Launching the Application

- **Storefront (Customer View):**
  ```text
  http://localhost/Eleve-clothing-ecommerce/index.html
  ```
- **Admin Dashboard (Management View):**
  ```text
  http://localhost/Eleve-clothing-ecommerce/admin/index.php
  ```

---

## 📡 REST API Endpoints

All backend API requests return standard JSON formatted responses:

| Endpoint | Method | Description |
|---|---|---|
| `/php/products.php` | `GET` | Fetch all products with filter, category, and search parameters |
| `/php/products.php?id={id}` | `GET` | Fetch detailed product data including images, sizes, and colors |
| `/php/auth.php?action=register` | `POST` | Register a new customer account |
| `/php/auth.php?action=login` | `POST` | Authenticate customer credentials & start session |
| `/php/auth.php?action=check` | `GET` | Verify active session & retrieve current user profile |
| `/php/auth.php?action=logout` | `POST` | Destroy session & logout |
| `/php/orders.php?action=create` | `POST` | Place a new order with line items & shipping details |
| `/php/orders.php?action=my_orders`| `GET` | Retrieve logged-in customer's order history |
| `/php/cart.php?action=coupon` | `POST` | Validate promotional discount coupon code |
| `/php/cart.php?action=contact` | `POST` | Submit customer support inquiry |
| `/php/cart.php?action=newsletter` | `POST` | Subscribe email to the newsletter |

---

## 📜 Academic Acknowledgment & Disclaimers

This project is developed as an academic requirement for the **User Defined Project (UDP)** under:
- **Institution:** LJ Institute of Computer Applications, LJ University, Ahmedabad, Gujarat, India.
- **Faculty Guide:** Dr. Anjum Y. Mansuri ([@anjum-mansuri](https://github.com/anjum-mansuri))
- **Student Developer:** Jaimisha Chunara ([@jaimisha15](https://github.com/jaimisha15))

*All brand names, sample imagery, and logos used within this project are for educational and academic demonstrative purposes only.*

---

&copy; 2026 ELEVÉ. Designed & Developed for Academic Demonstration.
