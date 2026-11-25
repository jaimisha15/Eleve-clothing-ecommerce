# LOK JAGRUTI KENDRA UNIVERSITY
## Department of Computer Applications
### MASTER OF COMPUTER APPLICATIONS (INTEGRATED) — MCA (Integrated)
### ACADEMIC YEAR: 2024 – 2028 | SEMESTER PROJECT DOCUMENTATION

---

# PROJECT TITLE: ELEVÉ CLOTHING STORE
### *An End-to-End Luxury Apparel & Fashion E-Commerce Web Application*

| **Project Metadata** | **Details** |
|:---|:---|
| **Course** | Master of Computer Applications (Integrated) |
| **Institute** | LJ Institute of Computer Applications (LJICA), LJKU, Ahmedabad |
| **Project Type** | User Defined Project (UDP) / Full-Stack Web Application |
| **Academic Guide / Mentor** | **Dr. Anjum Y. Mansuri**, Assistant Professor, Computer Applications, LJ University (GitHub: [@anjum-mansuri](https://github.com/anjum-mansuri)) |
| **Submission Date** | 21/08/2026 |
| **GitHub Repository** | [https://github.com/jaimisha15/Eleve-clothing-ecommerce](https://github.com/jaimisha15/Eleve-clothing-ecommerce) |

### **Team Members & Responsibilities**

| **Roll No.** | **Student Name** | **Role in Project** | **Core Responsibilities** |
|:---|:---|:---|:---|
| **E-01** | **Harsh Agechaniya** | Documentation Lead & QA Analyst | System requirements specification, documentation compilation, test-case authoring, and user-flow validation. |
| **E-10** | **Mit Dodiya** | Backend Developer & Database Designer | MySQL relational database schema, PDO secure architecture, session management, REST APIs, and order processing. |
| **H-11** | **Jaimisha Chunara** | Frontend Developer & UI/UX Designer | Luxury storefront interface, responsive layouts, client state management, catalog filtering, and admin dashboard styling. |

---

## TABLE OF CONTENTS

1. **Introduction**
   - 1.1 Existing System
   - 1.2 Need for the New System
   - 1.3 Objective of the New System
   - 1.4 Problem Definition
   - 1.5 Core Components & Modules
   - 1.6 Project Profile
   - 1.7 Assumptions and Constraints
   - 1.8 Advantages and Limitations
2. **Project Planning**
   - 2.1 Team Details and Roles
   - 2.2 Proposed Timeline (Gantt Chart & Sprint Schedule)
3. **Requirement Determination & Analysis**
   - 3.1 Requirement Determination Methodology
   - 3.2 Functional Requirements
   - 3.3 Non-Functional Requirements
   - 3.4 Targeted Users & Personas
4. **System Design**
   - 4.1 System Architecture Diagram
   - 4.2 Use Case Diagram & Actor Descriptions
   - 4.3 Class Diagram & Domain Entities
   - 4.4 Interaction / Sequence Diagram (Customer Checkout & Admin Workflow)
   - 4.5 Activity Diagram (Order Placement Pipeline)
   - 4.6 Comprehensive Data Dictionary (14 Relational Tables)
5. **Development & Implementation**
   - 5.1 Development Methodology (Agile & Iterative Sprints)
   - 5.2 Coding Standards & Architectural Best Practices
   - 5.3 GitHub Repository Architecture & Version Control
   - 5.4 AI Tools, Development Libraries & Frameworks
   - 5.5 Implementation Walkthrough & Screen Modules
6. **Proposed Enhancements**
7. **Conclusion**
8. **Bibliography & References**

---

# 1. INTRODUCTION

**ELEVÉ** is a full-stack, enterprise-grade e-commerce web application engineered specifically for luxury modern fashion and contemporary apparel. Developed with semantic **HTML5**, modern **CSS3**, modular **Vanilla JavaScript (ES6+)**, and a high-performance **PHP 8.x PDO** and **MySQL** backend, ELEVÉ bridges the gap between high-fashion aesthetics and responsive, ergonomic web design.

The platform provides an exclusive shopping catalog structured strictly into three core apparel categories: **Men**, **Women**, and **Kids** (with all non-clothing items such as footwear completely purged). It features real-time product discovery, a sliding cart drawer with live state persistence, an intuitive guest and authenticated checkout pipeline, and a centralized, password-protected **Admin Control Center** allowing administrators to monitor revenues, process and update customer orders, moderate products, and audit transaction histories.

### 1.1 Existing System
In traditional retail and conventional multi-purpose shopping websites, apparel purchasing faces significant operational barriers:
1. **Physical Retail Shopping:** Consumers must travel to physical retail stores, navigate manual garment racks, wait in queue for trial rooms and checkout counters, and face inventory uncertainty where desired sizes or colorways may be out of stock.
2. **Standard Online Marketplaces:** Traditional e-commerce websites frequently crowd user interfaces with intrusive advertising, generic product listings, and top-only navigation hierarchies. When shoppers scroll through hundreds of apparel items, they are forced to scroll all the way back to the top just to view their cart or search for items. Furthermore, excessive framework bloat and unoptimized third-party plugins lead to sluggish loading speeds, high bounce rates, and degraded mobile usability.

#### Drawbacks of the Existing System:
- **Severe Time & Geographic Constraints:** Shopping is tied to shop operating hours and physical travel.
- **High Navigation Friction:** Monolithic top bars force continuous page jumping on long product catalogs.
- **Suboptimal Mobile Ergonomics:** Critical controls like Cart, Wishlist, and Search are positioned out of easy thumb reach on mobile devices.
- **Cluttered Visual Interfaces:** Absence of luxury, minimalist brand identity.
- **Performance Overhead:** Heavy framework dependencies result in slow First Contentful Paint (FCP) and high data consumption.

### 1.2 Need for the New System
The digital transformation of consumer retail demands an apparel-focused platform offering:
- **24/7 Omnichannel Accessibility:** Shoppers can browse, customize, and order high-end fashion anytime, anywhere.
- **Instantaneous Product Discovery:** Zero-latency client filtering by gender, subcategory, size, color, price range, and search keyword.
- **Streamlined Frictionless Checkout:** Fast guest checkout without requiring lengthy mandatory registration barriers.
- **Direct Inventory & Sales Oversight:** A centralized administrative console for store owners to track revenues, manage stock levels, and update order fulfillment statuses in real time.

### 1.3 Objective of the New System
- **Elevated Shopping Experience:** Deliver a fast, responsive, and luxury-themed apparel destination.
- **Strictly Curated Apparel Categories:** Eliminate cluttered multi-vendor listings; organize inventory cleanly into **Men**, **Women**, and **Kids**.
- **Ergonomic Shopping Utilities:** Provide sliding cart and wishlist drawers, live search with highlight matches, and an automated coupon validation engine.
- **Unified Administrative Governance:** Provide an administrative portal equipped with role-based authentication, real-time analytics dashboards, order status workflows, and product CRUD capabilities.
- **Zero-Dependency Architecture:** Implement core capabilities using lightweight vanilla standards without fragile external runtime frameworks.

### 1.4 Problem Definition
Traditional retail and legacy platforms suffer from inventory opacity, rigid store hours, high physical operating overhead (store leases, electrical utilities, floor staffing), and inability to maintain persistent consumer preferences across visits.

**The Solution:** ELEVÉ provides an agile digital storefront engineered on local XAMPP Apache/MySQL architecture, featuring real-time session persistence, secure prepared SQL execution, automatic pricing calculation, and complete order management.

### 1.5 Core Components & Modules
The application is architected into modular sub-systems:
- **Storefront Header & Navigation:** Responsive sticky header featuring brand logo, inline live search bar, unified `LOGIN / REGISTER` customer modal button, and a dedicated `👑 ADMIN` pill button.
- **Visual Category Quick-Bar:** Floating pill navigation for Men, Women, and Kids with responsive image thumbnails.
- **Landing Gateway (`index.html`):** Hero brand showcase, collection banners, new arrivals carousel, and customer review highlights.
- **Department Portals (`men.html`, `women.html`, `kids.html`):** Tailored apparel portals featuring curated collections (T-shirts, Jeans, Co-Ord Sets).
- **Global Catalog (`products.html`):** Complete collection gallery with dynamic price sorting, badge filters, and category toggles.
- **Interactive Product Detail (`product-detail.html`):** Multi-angle photo gallery, dynamic size selector (XS, S, M, L, XL, XXL, Kids age sizes), color swatch preview, stock counter, and fabric specifications.
- **Sliding Drawers (Cart & Wishlist):** Asynchronous side panels displaying subtotal calculations, free shipping progress bars, quantity selectors, and item removal.
- **Streamlined Checkout (`checkout.html`):** Multi-payment checkout (COD, UPI, Credit/Debit Card, Net Banking), shipping address validation, coupon engine (`WELCOME10`, `FLAT200`, `SAVE15`), and instant database order dispatch.
- **Admin Control Center (`admin/index.php`):** Authenticated administrative suite featuring revenue charts, live order processing, order status transitions (`pending`, `confirmed`, `processing`, `shipped`, `delivered`, `cancelled`), single-click order deletion, product inventory management, category editing, customer reviews moderation, and newsletter tracking.

### 1.6 Project Profile

| Attribute | Specification |
|:---|:---|
| **Project Name** | **ELEVÉ — Luxury Apparel E-Commerce System** |
| **Application Type** | Responsive Full-Stack Web Application |
| **Frontend Technologies** | HTML5, CSS3 (Custom Variables, Flexbox, Grid), Modern JavaScript (ES6+ Vanilla) |
| **Backend Technologies** | PHP 8.x (PDO Object-Oriented Database Driver, Prepared Statements) |
| **Database Server** | MySQL / MariaDB (utf8mb4_unicode_ci) |
| **Local Web Server** | Apache (XAMPP for Windows) |
| **Version Control** | Git 2.x, GitHub Remote Repository |
| **Target Audience** | Urban fashion consumers seeking premium Men, Women, and Kids wear |
| **Total Apparel Products** | 29 Verified Real Garment Products (with local high-definition photographs) |
| **Apparel Departments** | Men (11 items), Women (10 items), Kids (8 items) — *Zero footwear* |
| **Primary Admin Credentials** | `admin@gmail.com` / Password: `123` |

### 1.7 Assumptions and Constraints
- **Assumptions:**
  - Client devices operate modern standards-compliant web browsers (Google Chrome, Microsoft Edge, Mozilla Firefox, Safari).
  - Local server environment runs XAMPP with Apache and MySQL active on standard ports (80/443 and 3306).
  - Users have active network connectivity for loading web fonts and submitting orders.
- **Constraints:**
  - Online payment gateways (Razorpay / Stripe) are simulated with authentic checkout validation logic.
  - Production deployments require HTTPS for secure cookie transmission.

### 1.8 Advantages and Limitations
- **Advantages:**
  - **High Performance:** Zero third-party JavaScript framework overhead (No React/Angular/Vue dependencies); near-instant rendering.
  - **Elegant UI/UX:** Luxury dark mode aesthetic accented with gold typography (`#c9a96e`) and subtle glassmorphic overlays.
  - **Single-Click Setup:** Auto-initialization database routine (`seed_db.php` and `ensureAllTablesAndAdmin`) guarantees that products, categories, and sample orders populate automatically without manual SQL configuration.
  - **Resilient Navigation:** Smart routing prevents broken `file:///` executions by gracefully redirecting to the local server.
- **Limitations:**
  - Multi-warehouse inventory synchronization is not implemented in current single-store release.
  - Automated shipping courier API webhooks (e.g., Shiprocket) require live merchant keys.

---

# 2. PROJECT PLANNING

### 2.1 Team Details and Roles

```
               ┌────────────────────────────────────────────────────────┐
               │         PROJECT MENTOR & ACADEMIC GUIDE                │
               │               Dr. Anjum Y. Mansuri                     │
               │   Assistant Professor, Computer Applications, LJKU     │
               └──────────────────────────┬─────────────────────────────┘
                                          │
            ┌─────────────────────────────┼─────────────────────────────┐
            │                             │                             │
            ▼                             ▼                             ▼
┌────────────────────────┐   ┌────────────────────────┐   ┌────────────────────────┐
│   Jaimisha Chunara     │   │      Mit Dodiya        │   │    Harsh Agechaniya    │
│  (Roll No: H-11)       │   │  (Roll No: E-10)       │   │   (Roll No: E-01)      │
│  Frontend Developer    │   │  Backend Developer     │   │   Documentation Lead   │
│  & UI/UX Designer      │   │  & Database Designer   │   │   & QA Specialist      │
└────────────────────────┘   └────────────────────────┘   └────────────────────────┘
```

- **Jaimisha Chunara (Frontend Lead & UI/UX Designer):** Architected the luxury visual identity, responsive layouts across 9 HTML views, client state logic (`app.js`), local storage cart synchronization, modal management, and admin visual dashboards.
- **Mit Dodiya (Backend Lead & Database Designer):** Engineered the relational MySQL schema (`database.sql`), PDO connection engine, security sanitization routines, session-based authentication, and REST API controllers (`php/orders.php`, `php/cart.php`, `php/auth.php`).
- **Harsh Agechaniya (Documentation Lead & QA):** Spearheaded system requirement specifications, authored UML behavioral diagrams, constructed the data dictionary, and coordinated functional user-acceptance testing across all checkout and admin pipelines.

### 2.2 Proposed Timeline (Gantt Chart & Sprint Schedule)

The project was executed across an intensive 16-week structured lifecycle:

| Phase / Sprint | Milestone Description | Start Date | End Date | Lead Responsible |
|:---|:---|:---|:---|:---|
| **Sprint 1: Inception** | Problem definition, stakeholder interviews, literature review of luxury apparel platforms | 01-Mar-2026 | 14-Mar-2026 | Harsh Agechaniya |
| **Sprint 2: Design** | Wireframing, color palette selection, UML system modeling (Use Case, Class, Activity) | 15-Mar-2026 | 28-Mar-2026 | Jaimisha Chunara |
| **Sprint 3: Database** | Relational schema design, normalization (3NF), database script creation | 29-Mar-2026 | 11-Apr-2026 | Mit Dodiya |
| **Sprint 4: Frontend** | HTML5 semantic templates, CSS design system, responsive grid layouts | 12-Apr-2026 | 02-May-2026 | Jaimisha Chunara |
| **Sprint 5: Backend** | PHP PDO services, REST endpoints, session handling, authentication | 03-May-2026 | 23-May-2026 | Mit Dodiya |
| **Sprint 6: Admin** | Administrative control suite, CRUD modules, order status management, deletion | 24-May-2026 | 13-Jun-2026 | Mit / Jaimisha |
| **Sprint 7: Integration** | Local clothing reference cataloging (29 products), purging footwear, checkout wiring | 14-Jun-2026 | 11-Jul-2026 | Full Team |
| **Sprint 8: QA & Audit** | Cross-browser verification, XAMPP synchronization, bug fixes, final documentation | 12-Jul-2026 | 21-Aug-2026 | Harsh Agechaniya |

---

# 3. REQUIREMENT DETERMINATION & ANALYSIS

### 3.1 Requirement Determination Methodology
To ensure high commercial relevance, requirements were gathered through three core techniques:
1. **Target User Interviews:** Conducted structured interviews with college students and retail apparel buyers regarding navigation pain points, mobile cart abandonment, and preferred checkout steps.
2. **Competitive Feature Benchmarking:** Analyzed leading fashion platforms (Zara, ASOS, Max Fashion) to study optimal product card ergonomics, size guide presentations, and sliding cart behaviors.
3. **Operational Workflow Mapping:** Mapped the physical retail counter billing workflow into an automated digital equivalent with real-time tax calculation, discount coupon deduction, and immediate order receipts.

### 3.2 Functional Requirements
- **FR-01: User & Admin Authentication:** Secure bcrypt hashing for passwords; distinct role-based access for shoppers (`user`) and administrators (`admin`).
- **FR-02: Categorized Catalog Exploration:** Dedicated views for **Men**, **Women**, and **Kids**, with instantaneous filter chips (T-shirts, Jeans, Co-Ord Sets).
- **FR-03: Real-Time Live Search:** Fast client-side search indexing product names, tags, and subcategories with zero page reloading.
- **FR-04: Product Customization:** Selection of garment sizes (XS to XXL and kids age brackets), dynamic color swatches, stock availability checking, and price display in Indian Rupees (₹).
- **FR-05: Cart & Wishlist Drawers:** Asynchronous slide-out sidebars allowing item quantity modification, real-time total recalculation, and coupon validation (`WELCOME10`, `FLAT200`, `SAVE15`).
- **FR-06: Checkout & Order Creation:** Form validation for recipient name, phone, address, and city; generation of unique order serials (`ELV-2026-XXXX`); transactional database insertion across `orders` and `order_items`.
- **FR-07: Admin Dashboard & Order Management:** Metric cards for revenue and order count, status updates (`pending` to `delivered`), single-click order deletion, and product catalog management.

### 3.3 Non-Functional Requirements
- **NFR-01: Performance & Latency:** Catalog filtering and search queries execute within **150 milliseconds**; page load times under 1.5 seconds.
- **NFR-02: Responsive Ergonomics:** Seamless multi-breakpoint adaptation across mobile phones (360px+), tablets (768px+), laptops (1024px+), and ultrawide monitors (1440px+).
- **NFR-03: Security & Sanitization:** All incoming user parameters sanitized using `htmlspecialchars` and prepared SQL statements to completely prevent SQL Injection and Cross-Site Scripting (XSS).
- **NFR-04: Maintainability & Modularity:** Adherence to DRY (Don't Repeat Yourself) principles with separate concern layers for styling (`styles.css`), behavior (`app.js`), database config (`php/config.php`), and administrative logic.

### 3.4 Targeted Users & Personas

| User Persona | Role | Primary Responsibilities & Permissions |
|:---|:---|:---|
| **Apparel Shopper (End User)** | Customer / Shopper | Browse curated garments, search items, apply coupons, add garments to cart/wishlist, submit checkout orders, and track order histories. |
| **Guest Shopper** | Unauthenticated User | Experience full catalog browsing, live cart management, and guest checkout without mandatory account creation. |
| **Store Administrator** | Business Owner / Manager | Authenticate via administrative portal, audit revenues and sales metrics, update fulfillment statuses, manage products/categories, and delete orders. |

---

# 4. SYSTEM DESIGN

### 4.1 System Architecture Diagram

```
┌────────────────────────────────────────────────────────────────────────┐
│                        CLIENT LAYER (BROWSER)                          │
│   HTML5 Views (Storefront & Admin)  │  CSS3 Styles  │  ES6+ JavaScript │
└───────────────────────────────────┬────────────────────────────────────┘
                                    │ HTTP / Fetch API Requests
                                    ▼
┌────────────────────────────────────────────────────────────────────────┐
│                     APPLICATION SERVER (APACHE / PHP)                  │
│  ┌────────────────────────┐  ┌──────────────────────────────────────┐  │
│  │   Storefront API       │  │       Admin Control Center           │  │
│  │   php/orders.php       │  │       admin/index.php (Dashboard)    │  │
│  │   php/cart.php         │  │       admin/products.php (Catalog)   │  │
│  │   php/auth.php         │  │       admin/orders.php (Fulfillment) │  │
│  │   php/config.php       │  │       admin/categories.php           │  │
│  └───────────┬────────────┘  └──────────────────┬───────────────────┘  │
└──────────────┼──────────────────────────────────┼──────────────────────┘
               │ PDO Prepared Statements          │
               ▼                                  ▼
┌────────────────────────────────────────────────────────────────────────┐
│                 RELATIONAL DATABASE LAYER (MYSQL)                      │
│   eleve_ecommerce (users, products, categories, orders, order_items)   │
└────────────────────────────────────────────────────────────────────────┘
```

### 4.2 Use Case Diagram & Actor Descriptions

```
                        ELEVÉ E-COMMERCE PLATFORM
        ┌─────────────────────────────────────────────────────────┐
        │                                                         │
        │   ( Browse Categories: Men, Women, Kids )               │
        │                         ▲                               │
        │                         │                               │
        │   ( Live Product Search & Filter )                      │
        │                         ▲                               │
        │                         │                               │
        │   ( View Product Details, Select Size & Color )         │
        │                         ▲                               │
        │                         │                               │
        │   ( Manage Cart Drawer & Apply Coupon )                 │
        │                         ▲                               │
        │                         │                               │
[Shopper] ──( Submit Order via Checkout )                         │
        │                         ▲                               │
        │                         │                               │
        │   ( Manage Wishlist Favorites )                         │
        │                         ▲                               │
        │                         │                               │
        │   ( Customer Account Login / Register )                 │
        │                                                         │
        ├─────────────────────────────────────────────────────────┤
        │                                                         │
        │   ( Admin Secure Login: admin@gmail.com ) ◄─────────────┤
        │                                                         │
        │   ( View Revenue Metrics & 7-Day Chart )  ◄─────────────┤
        │                                                         │ [Store Admin]
        │   ( Update Order Status: Shipped/Delivered ) ◄──────────┤
        │                                                         │
        │   ( Delete Test / Stale Orders )          ◄─────────────┤
        │                                                         │
        │   ( Product Catalog CRUD & Inventory )    ◄─────────────┤
        │                                                         │
        └─────────────────────────────────────────────────────────┘
```

### 4.3 Class Diagram & Domain Entities

```
┌────────────────────────┐         1..* ┌────────────────────────┐
│        Category        │◄─────────────┤        Product         │
├────────────────────────┤              ├────────────────────────┤
│ + id: int (PK)         │              │ + id: int (PK)         │
│ + name: string         │              │ + category_id: int(FK) │
│ + slug: string         │              │ + name: string         │
│ + gender: string       │              │ + gender: string       │
│ + image_url: string    │              │ + price: decimal       │
│ + is_active: boolean   │              │ + old_price: decimal   │
└────────────────────────┘              │ + stock_qty: int       │
                                        │ + rating: decimal      │
                                        │ + badge: string        │
                                        └───────────┬────────────┘
                                                    │ 1
                                                    │
                                                    │ 1..*
                                        ┌───────────▼────────────┐
                                        │       OrderItem        │
┌────────────────────────┐ 1            ├────────────────────────┤
│         Order          │◄─────────────┤ + id: int (PK)         │
├────────────────────────┤              │ + order_id: int (FK)   │
│ + id: int (PK)         │ 1..*         │ + product_id: int(FK)  │
│ + order_number: string │              │ + product_name: string │
│ + user_id: int (FK)    │              │ + product_image: string│
│ + status: string       │              │ + size: string         │
│ + payment_status: string              │ + color: string        │
│ + total: decimal       │              │ + qty: int             │
│ + shipping_name: string│              │ + price: decimal       │
│ + shipping_address: str│              │ + total: decimal       │
└────────────────────────┘              └────────────────────────┘
```

### 4.4 Interaction / Sequence Diagram (Checkout & Fulfillment)

```
Shopper                checkout.html           php/orders.php             MySQL Database             Admin Panel
   │                         │                        │                         │                         │
   │── Fills Shipping Info ─>│                        │                         │                         │
   │── Clicks Place Order ──>│                        │                         │                         │
   │                         │── POST /php/orders.php>│                         │                         │
   │                         │   (FormData + Items)   │                         │                         │
   │                         │                        │── BEGIN TRANSACTION ───>│                         │
   │                         │                        │── INSERT INTO orders ──>│                         │
   │                         │                        │── INSERT order_items ──>│                         │
   │                         │                        │── COMMIT TRANSACTION ──>│                         │
   │                         │                        │<─ Return Order ID / Num─│                         │
   │                         │<─ JSON {success: true}─│                         │                         │
   │<─ Show Success Modal ───│                        │                         │                         │
   │                         │                        │                         │                         │
   │                         │                        │                         │── Admin Views Orders ──>│
   │                         │                        │                         │<─ Display Name & Total ─│
   │                         │                        │                         │                         │
   │                         │                        │                         │<─ Status Updated / Del ─│
```

### 4.5 Activity Diagram (Customer Shopping & Order Workflow)

```
        ( Start )
            │
            ▼
   [ Browse Collection ] ──── (Filter by Men / Women / Kids)
            │
            ▼
    [ View Garment Details ]
            │
            ▼
    [ Select Size & Color ]
            │
            ▼
      [ Add to Cart ] ───────> [ Save to Wishlist ]
            │
            ▼
   [ Open Cart Drawer ]
            │
            ├── (Apply Promo Coupon)
            │
            ▼
    [ Proceed to Checkout ]
            │
            ▼
   [ Enter Shipping Address & Choose Payment Mode ]
            │
            ▼
     < Form Valid? >
       ├── No ───> [ Show Validation Warning ] ───┐
       │                                          │
       └── Yes ──> [ Send Order Data to Server ]  │
                         │                        │
                         ▼                        │
                [ Save in Database ]              │
                         │                        │
                         ▼                        │
              [ Display Order Number ] <──────────┘
                         │
                      ( End )
```

### 4.6 Comprehensive Data Dictionary (14 Relational Tables)

#### Table 1: `users`
Represents registered customers and system administrators.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique identifier for the user | `1` |
| `name` | VARCHAR(100) | NOT NULL | Full name of the user | `Admin` / `Rahul Sharma` |
| `email` | VARCHAR(150) | UNIQUE, NOT NULL | Account login email address | `admin@gmail.com` |
| `password` | VARCHAR(255) | NOT NULL | Bcrypt cryptographic password hash | `$2y$12$NdjdsG...` |
| `phone` | VARCHAR(20) | NULL | Contact mobile number | `+91 98251 12345` |
| `address` | TEXT | NULL | Default delivery residential address | `A-402, Titanium City` |
| `role` | ENUM('user','admin') | DEFAULT 'user' | Access control level | `admin` |
| `is_active` | TINYINT(1) | DEFAULT 1 | Account status flag | `1` |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Account creation timestamp | `2026-11-20 10:00:00` |

#### Table 2: `categories`
Defines main apparel departments.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique category identifier | `1` |
| `name` | VARCHAR(100) | NOT NULL | Display name of the category | `Men's T-Shirts` |
| `slug` | VARCHAR(100) | UNIQUE, NOT NULL | URL-friendly unique identifier | `men-tshirts` |
| `gender` | ENUM('men','women','kids','unisex') | NOT NULL | Gender target group | `men` |
| `image_url` | VARCHAR(500) | NULL | Relative path to category visual image | `images/men/tshirt-1.jpg` |
| `is_active` | TINYINT(1) | DEFAULT 1 | Whether category is shown | `1` |
| `sort_order` | INT | DEFAULT 0 | Display priority sequence | `1` |

#### Table 3: `products`
The core catalog table holding all 29 apparel items.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique product identifier | `1` |
| `name` | VARCHAR(200) | NOT NULL | Garment commercial name | `Men's Noir Graphic Tee` |
| `category` | VARCHAR(100) | NOT NULL | High-level apparel category | `T-Shirts` |
| `subcategory`| VARCHAR(100) | NULL | Secondary categorization | `t-shirts` / `jeans` |
| `gender` | ENUM('men','women','kids') | NOT NULL | Apparel target group | `men` |
| `price` | DECIMAL(10,2)| NOT NULL | Selling price in ₹ | `699.00` |
| `old_price` | DECIMAL(10,2)| NULL | Original MRP for discount strikethrough| `899.00` |
| `badge` | VARCHAR(50) | NULL | Marketing badge tag | `Best Seller` / `New` |
| `rating` | DECIMAL(3,2)| DEFAULT 4.50 | Average star rating (1.0 to 5.0) | `4.80` |
| `stock_qty` | INT | DEFAULT 100 | Current warehouse inventory count | `85` |
| `description`| TEXT | NULL | Detailed product narrative | `Signature streetwear...` |
| `fit` | TEXT | NULL | Garment fit and drape guide | `Regular streetwear fit` |
| `details` | TEXT | NULL | Pipe-separated fabric specifications | `100% combed cotton\|220GSM`|
| `is_active` | TINYINT(1) | DEFAULT 1 | Visibility toggle flag | `1` |

#### Table 4: `product_images`
Stores relative file paths for multi-angle product photography.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Image record ID | `1` |
| `product_id` | INT | FOREIGN KEY (products.id) | Referenced product | `1` |
| `url` | VARCHAR(500) | NOT NULL | Relative image file path | `images/men/tshirt-1.jpg` |
| `sort_order` | INT | DEFAULT 0 | Thumbnail sorting sequence | `0` |

#### Table 5: `product_sizes`
Garment sizing options available for each apparel item.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Size record ID | `1` |
| `product_id` | INT | FOREIGN KEY (products.id) | Referenced product | `1` |
| `size` | VARCHAR(20) | NOT NULL | Garment size label | `S`, `M`, `L`, `32`, `7-8Y` |

#### Table 6: `product_colors`
Color swatches and hexadecimal values for apparel items.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Color record ID | `1` |
| `product_id` | INT | FOREIGN KEY (products.id) | Referenced product | `1` |
| `name` | VARCHAR(50) | NOT NULL | Color commercial name | `Black` / `Medium Denim` |
| `hex` | VARCHAR(10) | NOT NULL | CSS Hexadecimal color representation | `#1a1a1a` |

#### Table 7: `orders`
Master transaction registry for all submitted customer purchases.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Unique order record ID | `1` |
| `order_number`| VARCHAR(20)| UNIQUE, NOT NULL | Human-readable tracking number | `ELV-2026-1001` |
| `user_id` | INT | FOREIGN KEY (users.id) NULL | Account ID if authenticated shopper | `NULL` / `2` |
| `status` | ENUM('pending','confirmed','processing','shipped','delivered','cancelled') | DEFAULT 'pending' | Fulfillment lifecycle state | `delivered` |
| `payment_status`| ENUM('pending','paid','failed','refunded') | DEFAULT 'pending' | Payment settlement state | `paid` |
| `payment_method`| VARCHAR(50)| DEFAULT 'cod' | Payment channel selected | `upi` / `card` / `cod` |
| `subtotal` | DECIMAL(10,2)| NOT NULL | Sum of item totals before discounts | `2098.00` |
| `discount` | DECIMAL(10,2)| DEFAULT 0.00 | Total promo discount deducted in ₹ | `200.00` |
| `total` | DECIMAL(10,2)| NOT NULL | Final payable order amount | `1898.00` |
| `shipping_name`| VARCHAR(150)| NOT NULL | Full name of recipient customer | `Rahul Sharma` |
| `shipping_email`| VARCHAR(150)| NOT NULL | Notification email address | `rahul.sharma@example.com` |
| `shipping_phone`| VARCHAR(20)| NOT NULL | Delivery contact mobile number | `+91 98251 12345` |
| `shipping_address`| TEXT | NOT NULL | Detailed street and premises address | `A-402, Titanium City` |
| `shipping_city`| VARCHAR(100)| NOT NULL | Destination municipality | `Ahmedabad` |
| `shipping_state`| VARCHAR(100)| NOT NULL | Destination state/province | `Gujarat` |
| `shipping_pincode`| VARCHAR(10)| NOT NULL | Postal code | `380015` |
| `created_at` | TIMESTAMP | DEFAULT CURRENT_TIMESTAMP | Order submission timestamp | `2026-11-20 10:15:00` |

#### Table 8: `order_items`
Individual apparel line items associated with each order.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Line item record ID | `1` |
| `order_id` | INT | FOREIGN KEY (orders.id) | Parent order reference (CASCADE) | `1` |
| `product_id` | INT | FOREIGN KEY (products.id) | Source product reference | `1` |
| `product_name`| VARCHAR(200)| NOT NULL | Product name snapshot at checkout | `Men's Noir Graphic Tee` |
| `product_image`| VARCHAR(500)| NULL | Relative thumbnail image snapshot | `images/men/tshirt-1.jpg` |
| `size` | VARCHAR(20) | NOT NULL | Ordered size | `L` |
| `color` | VARCHAR(50) | NULL | Ordered color swatch | `Black` |
| `qty` | INT | NOT NULL | Number of units purchased | `1` |
| `price` | DECIMAL(10,2)| NOT NULL | Unit price at time of purchase | `699.00` |
| `total` | DECIMAL(10,2)| NOT NULL | Line item total (price × qty) | `699.00` |

#### Table 9: `coupons`
Discount promo codes for cart savings.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Coupon record ID | `1` |
| `code` | VARCHAR(50) | UNIQUE, NOT NULL | Promo voucher code string | `WELCOME10` |
| `type` | ENUM('percent','fixed') | DEFAULT 'percent' | Discount calculation method | `percent` |
| `value` | DECIMAL(10,2)| NOT NULL | Discount percentage or flat ₹ off | `10.00` |
| `min_order` | DECIMAL(10,2)| DEFAULT 0.00 | Minimum cart subtotal required | `500.00` |
| `is_active` | TINYINT(1) | DEFAULT 1 | Coupon enablement flag | `1` |

#### Table 10: `reviews`
Verified customer ratings and product testimonials.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Review record ID | `1` |
| `product_id` | INT | FOREIGN KEY (products.id) | Garment reviewed | `1` |
| `name` | VARCHAR(100) | NOT NULL | Reviewer author name | `Rahul Sharma` |
| `rating` | INT | CHECK (rating BETWEEN 1 AND 5)| Star score | `5` |
| `title` | VARCHAR(200) | NULL | Review headline summary | `Exceptional Quality` |
| `comment` | TEXT | NOT NULL | Review commentary text | `Heavy combed cotton feels...`|
| `is_approved` | TINYINT(1) | DEFAULT 0 | Administrative approval status | `1` |

#### Table 11: `newsletter_subscribers`
Email registry for brand marketing dispatches.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Subscriber record ID | `1` |
| `email` | VARCHAR(150) | UNIQUE, NOT NULL | Customer subscription email | `rahul.sharma@example.com` |
| `is_active` | TINYINT(1) | DEFAULT 1 | Active subscriber flag | `1` |

#### Table 12: `contact_messages`
Customer inquiries submitted through `contact.html`.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Message record ID | `1` |
| `name` | VARCHAR(100) | NOT NULL | Inquirer full name | `Rohan Trivedi` |
| `email` | VARCHAR(150) | NOT NULL | Inquirer email | `rohan.t@example.com` |
| `subject` | VARCHAR(200) | NULL | Message subject line | `Size exchange inquiry` |
| `message` | TEXT | NOT NULL | Full inquiry body text | `Hi team, I would like to...`|
| `status` | ENUM('new','read','replied')| DEFAULT 'new' | Administrative triage state | `new` |

#### Table 13: `cart_items`
Server-side session cart storage for cross-request consistency.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Cart row identifier | `1` |
| `session_id` | VARCHAR(100)| NOT NULL | Unique session token | `a8f3b20...` |
| `product_id` | INT | FOREIGN KEY (products.id) | Referenced product | `1` |
| `qty` | INT | DEFAULT 1 | Item quantity | `2` |

#### Table 14: `settings`
Global website configuration key-value storage.

| Column Name | Data Type | Constraint | Description | Example |
|:---|:---|:---|:---|:---|
| `id` | INT | PRIMARY KEY, AUTO_INCREMENT | Setting identifier | `1` |
| `setting_key`| VARCHAR(100)| UNIQUE, NOT NULL | Configuration identifier | `free_shipping_min` |
| `setting_value`| TEXT | NULL | Stored configuration setting | `999` |

---

# 5. DEVELOPMENT & IMPLEMENTATION

### 5.1 Development Methodology
The team executed an **Agile Scrum** methodology structured into 2-week sprints:
- **Daily Progress Stand-Ups:** Rapid 15-minute syncs to identify blockers, review CSS component integration, and coordinate backend endpoints.
- **Continuous Version Control:** Git branch-based feature delivery (`feature/catalog`, `feature/checkout`, `bugfix/admin-nav`) merged into `main` after verification.
- **Continuous Database Synchronization:** Creation of automated self-healing database seeding routines (`seed_db.php`) allowing zero-effort migration across different developer workstations.

### 5.2 Coding Standards & Architectural Best Practices
- **Frontend Architecture:**
  - Semantic HTML5 elements (`<header>`, `<main>`, `<section>`, `<article>`, `<aside>`, `<footer>`).
  - CSS3 Modern Design System utilizing CSS Variables for unified theming (`--clr-accent: #c9a96e; --clr-bg: #0a0a0a;`).
  - Modular, immediately invoked function expressions (IIFE) in JavaScript preventing global namespace pollution.
  - Event delegation on dynamic product lists and cart drawers to optimize memory footprint.
- **Backend Architecture:**
  - Secure PHP Object Data (PDO) driver utilizing parameterized prepared statements.
  - Strict input sanitization with `htmlspecialchars()` and `filter_var(..., FILTER_SANITIZE_EMAIL)`.
  - Transactional integrity via `$pdo->beginTransaction()` and `$pdo->commit()` ensuring order and item rows insert atomically.
  - Centralized JSON response helpers (`jsonResponse()`) with correct HTTP status codes (200, 400, 422, 500).

### 5.3 GitHub Repository Details
- **Repository URL:** [https://github.com/jaimisha15/Eleve-clothing-ecommerce](https://github.com/jaimisha15/Eleve-clothing-ecommerce)
- **Primary Branch:** `main`
- **Tracked Clean Commits:**
  - `066cb36`: Organize reference images for Men, Women, Kids and completely purge footwear.
  - `2e9803b`: Fix cart query selector errors and add safe component initialization.
  - `d8c28f0`: Auto-redirect admin navigation, populate 29 apparel items and 6 sample orders in database.
  - `8fd4006`: Fix customer name rendering in admin orders table, add single-click order deletion, and patch checkout CORS routing.
  - `c29303a`: Enforce strict separation between customer login button and admin panel button in storefront navbar.

### 5.4 AI Tools, Development Libraries & Frameworks
- **Google Antigravity:** Advanced agentic coding assistant used for continuous repository refactoring, automated testing, terminal orchestration, and architectural validation.
- **ChatGPT (OpenAI):** Utilized for generating luxury copywriting, product descriptions, and drafting structural test cases.
- **PHP Data Objects (PDO):** Built-in native PHP database interface ensuring robust, secure database access.
- **Google Fonts (Inter & Playfair Display):** Web typography providing the modern luxury editorial appearance.
- **KaTeX / LaTeX:** Standardized mathematical and financial rendering.

### 5.5 Implementation Walkthrough & Screen Modules

#### 1. Storefront Home Page (`index.html`)
The landing experience establishes brand authority with a full-bleed luxury banner, brand statement (*"Define Your Elegance"*), curated department quick-links (Women, Men, Kids), trending apparel spotlights, customer testimonials, and an interactive FAQ accordion.

#### 2. Men's Department (`men.html` — 11 Garments)
Curated exclusively for men's fashion:
- **T-Shirts (7 Items):** Men's Noir Graphic Tee (₹699), Minimalist Crew Neck Tee (₹599), Urban Street Tee (₹749), Vintage Wash Graphic Tee (₹699), Oversized Statement Tee (₹799), Athletics Printed T-Shirt (₹649), Signature Essential Tee (₹679).
- **Jeans (4 Items):** Slim Fit Washed Jeans (₹1,399), Distressed Street Denim (₹1,499), Dark Indigo Tapered Jeans (₹1,299), Relaxed Straight Leg Jeans (₹1,349).
- *All items link to authentic local photography located in `images/men/`.*

#### 3. Women's Department (`women.html` — 10 Garments)
Curated exclusively for women's contemporary wear:
- **T-Shirts (5 Items):** Women's Cropped Graphic T-Shirt (₹599), Minimalist Aesthetic Tee (₹649), Oversized Graphic Tee (₹699), Casual Cotton Daily Tee (₹549), Ribbed Fitted T-Shirt (₹629).
- **Jeans (5 Items):** High-Rise Flare Jeans (₹1,499), Mid-Rise Wide Leg Washed Jeans (₹1,599), Straight Cut Vintage Denim (₹1,299), Slim Fit Ankle Jeans (₹1,399), Classic Denim Trousers (₹1,249).
- *All items link to authentic local photography located in `images/women/`.*

#### 4. Kids' Department (`kids.html` — 8 Garments)
Curated for children's comfort and energetic play:
- **T-Shirts & Jeans (2 Items):** Kids Star Printed Crew Neck Tee (₹449), Kids Durable Summer Denim Pants (₹699).
- **Co-Ord Sets (6 Items):** Explorer Playtime Outfit Set (₹799), Casual Summer Duo Set (₹649), Adventure Active Wear Set (₹599), Sunshine Weekend Co-Ord (₹699), Daily Smiles Casual Outfit (₹549), Bright Smile Graphic Outfit (₹599).
- *All items link to authentic local photography located in `images/kids/`.*

#### 5. Product Detail View (`product-detail.html`)
Presents high-resolution garment previews, dynamic thumbnail swapping, real-time size pill selection (XS to XXL and kids age tags), color swatch indicators, real-time subtotal pricing, and detailed fabric composition specifications.

#### 6. Sliding Cart & Wishlist Drawers
Accessible from anywhere via the sticky header or bottom utility bar. Displays live item counter badges, instant quantity increment/decrement buttons, individual item removal, free shipping thresholds, and subtotal summaries.

#### 7. Frictionless Checkout (`checkout.html`)
Permits customers to input their delivery details (Recipient Name, Mobile Phone, Street Address, City, State, and 6-digit Pincode) and select payment method (COD, Card, UPI, Net Banking). Upon submission, data is sent to `php/orders.php`, generating an authentic order number and storing the record in MySQL.

#### 8. Admin Control Center (`admin/index.php` & `admin/orders.php`)
- **Protected Access:** Accessible via credentials `admin@gmail.com` / `123`.
- **Live Business Metrics:** Aggregates total paid revenue, total order count, active registered users, and active product counts.
- **Customer Name Resolution:** Accurately reflects customer names (e.g. *Rahul Sharma*, *Ananya Desai*, *Jaimisha Patel*) instead of generic "Guest" labels.
- **Status Lifecycle Control:** Allows one-click status transitions between `Pending`, `Confirmed`, `Processing`, `Shipped`, and `Delivered`.
- **Order Deletion:** Administrators can safely delete completed or test orders with a single click, protected by a browser confirmation dialog.

---

# 6. PROPOSED ENHANCEMENTS

1. **Production Payment Gateway Integration:** Incorporate live merchant webhooks via Razorpay or Stripe for automated UPI QR code and NetBanking payment callbacks.
2. **AI-Powered Style Advisor:** Implement an embedded machine-learning chatbot capable of recommending garment sizes based on height/weight and suggesting matching outfits (e.g., matching Wide Leg Jeans with Cropped Graphic Tees).
3. **Automated Courier Logistics API:** Connect backend order dispatches to courier APIs (e.g. Shiprocket / Delhivery) to generate live airway bill (AWB) tracking numbers automatically.
4. **Progressive Web App (PWA):** Register service workers and offline caching manifests enabling shoppers to install ELEVE directly on Android and iOS home screens.
5. **Multi-Currency & Internationalization:** Support real-time currency conversion (USD, EUR, GBP, AED) with multi-language locale switching.

---

# 7. CONCLUSION

The **ELEVÉ** clothing store e-commerce application successfully demonstrates the design, engineering, and deployment of a full-stack digital commerce platform tailored to the luxury apparel industry. Over the course of the project lifecycle, our team achieved the following deliverables:
- **Clean Apparel Architecture:** Eliminated clutter and curated a focused catalog of 29 high-quality garments strictly spanning Men, Women, and Kids.
- **Robust Full-Stack Pipeline:** Integrated semantic frontend interfaces with secure PHP PDO controllers and a normalized 14-table MySQL database.
- **Unified Store Administration:** Built an administrative console capable of real-time sales reporting, lifecycle order status transitions, and data governance.
- **Academic Rigor:** Mastered real-world engineering concepts including Git version management, transaction atomicity, responsive mobile-first ergonomics, and parameterized database security.

The resulting platform fulfills all curriculum benchmarks for the Master of Computer Applications (Integrated) program at Lok Jagruti Kendra University.

---

# 8. BIBLIOGRAPHY & REFERENCES

1. **W3C Standards:** World Wide Web Consortium, *"HTML5 & CSS3 Specification Standards"*, [https://www.w3.org/TR/html52/](https://www.w3.org/TR/html52/)
2. **MDN Web Docs:** Mozilla Developer Network, *"JavaScript ES6+ Language Specification & DOM API Documentation"*, [https://developer.mozilla.org/](https://developer.mozilla.org/)
3. **The PHP Group:** *"PHP 8.x PDO Manual & Prepared Statement Security"*, [https://www.php.net/manual/en/book.pdo.php](https://www.php.net/manual/en/book.pdo.php)
4. **Oracle Corporation:** *"MySQL 8.0 Reference Manual: Relational Schema Design & InnoDB Foreign Key Optimization"*, [https://dev.mysql.com/doc/](https://dev.mysql.com/doc/)
5. **Martin, Robert C.:** *"Clean Code: A Handbook of Agile Software Craftsmanship"*, Prentice Hall, 2008.
6. **Academic Project Guidance:** Dr. Anjum Y. Mansuri, Assistant Professor, Department of Computer Applications, LJ Institute of Computer Applications, Lok Jagruti Kendra University, Ahmedabad. GitHub: [@anjum-mansuri](https://github.com/anjum-mansuri).
