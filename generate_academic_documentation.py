# -*- coding: utf-8 -*-
"""
ELEVÉ Clothing Store — Academic Project Documentation Builder
Generates the comprehensive academic report for Lok Jagruti Kendra University (LJKU)
Department of Computer Applications (LJICA) — MCA (Integrated).
Features clean UTF-8 encoding, professional academic typography, and all 15 system screenshots.
"""

import os
import sys
import shutil
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import parse_xml, OxmlElement
from docx.oxml.ns import nsdecls, qn

from docx_helpers import set_cell_shading, set_cell_border, set_cell_margins

def build_academic_documentation():
    print("Initiating ELEVÉ Academic Documentation Generation...")
    doc = Document()

    # 1. Page Setup — 1 Inch Universal Margins
    for section in doc.sections:
        section.top_margin = Inches(1.0)
        section.bottom_margin = Inches(1.0)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(1.0)

        # Academic Running Header
        header = section.header
        hp = header.paragraphs[0]
        hp.text = "ELEVÉ — Luxury Apparel E-Commerce Web Application | Academic Project Report"
        hp.alignment = WD_ALIGN_PARAGRAPH.RIGHT
        if hp.runs:
            hrun = hp.runs[0]
            hrun.font.name = "Calibri"
            hrun.font.size = Pt(8.5)
            hrun.font.color.rgb = RGBColor(148, 163, 184)

        # Academic Running Footer
        footer = section.footer
        fp = footer.paragraphs[0]
        fp.text = "Lok Jagruti Kendra University (LJKU) — Department of Computer Applications (LJICA)"
        fp.alignment = WD_ALIGN_PARAGRAPH.LEFT
        if fp.runs:
            frun = fp.runs[0]
            frun.font.name = "Calibri"
            frun.font.size = Pt(8.5)
            frun.font.color.rgb = RGBColor(148, 163, 184)

    # 2. Typography Styles
    styles = doc.styles
    normal_style = styles['Normal']
    normal_style.font.name = 'Calibri'
    normal_style.font.size = Pt(11)
    normal_style.font.color.rgb = RGBColor(34, 34, 34)
    normal_style.paragraph_format.line_spacing = 1.2
    normal_style.paragraph_format.space_after = Pt(6)

    # Reusable Formatting Helpers
    def add_h1(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(20)
        p.paragraph_format.space_after = Pt(8)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(17)
        run.font.bold = True
        run.font.color.rgb = RGBColor(27, 54, 93) # Deep Navy
        return p

    def add_h2(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(14)
        p.paragraph_format.space_after = Pt(6)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(13.5)
        run.font.bold = True
        run.font.color.rgb = RGBColor(184, 147, 85) # Warm Gold
        return p

    def add_h3(text):
        p = doc.add_paragraph()
        p.paragraph_format.space_before = Pt(10)
        p.paragraph_format.space_after = Pt(4)
        p.paragraph_format.keep_with_next = True
        run = p.add_run(text)
        run.font.name = 'Calibri'
        run.font.size = Pt(11.5)
        run.font.bold = True
        run.font.color.rgb = RGBColor(51, 65, 85) # Slate
        return p

    def add_bullet(bold_prefix, text):
        p = doc.add_paragraph(style='List Bullet')
        p.paragraph_format.space_after = Pt(3)
        p.paragraph_format.line_spacing = 1.15
        if bold_prefix:
            r_bold = p.add_run(bold_prefix + " ")
            r_bold.font.name = 'Calibri'
            r_bold.font.bold = True
            r_bold.font.color.rgb = RGBColor(27, 54, 93)
        r_text = p.add_run(text)
        r_text.font.name = 'Calibri'
        r_text.font.color.rgb = RGBColor(34, 34, 34)
        return p

    def build_table(headers, rows_data, col_widths=None):
        table = doc.add_table(rows=len(rows_data) + 1, cols=len(headers))
        table.alignment = WD_TABLE_ALIGNMENT.CENTER
        
        # Header Row
        hdr_row = table.rows[0]
        for c_idx, title in enumerate(headers):
            cell = hdr_row.cells[c_idx]
            cell.text = str(title)
            set_cell_shading(cell, "1B365D") # Navy Header
            set_cell_margins(cell, top=100, bottom=100, left=140, right=140)
            set_cell_border(cell, top={'sz': 6, 'color': '1B365D'},
                                  bottom={'sz': 8, 'color': 'B89355'},
                                  left={'sz': 4, 'color': '2E4B75'},
                                  right={'sz': 4, 'color': '2E4B75'})
            p = cell.paragraphs[0]
            p.alignment = WD_ALIGN_PARAGRAPH.CENTER
            for r in p.runs:
                r.font.name = 'Calibri'
                r.font.size = Pt(9.5)
                r.font.bold = True
                r.font.color.rgb = RGBColor(255, 255, 255)

        # Data Rows
        for r_idx, row_values in enumerate(rows_data):
            row_cells = table.rows[r_idx + 1].cells
            shd_color = "F8FAFC" if (r_idx % 2 == 1) else "FFFFFF"
            for c_idx, val in enumerate(row_values):
                cell = row_cells[c_idx]
                cell.text = str(val)
                set_cell_shading(cell, shd_color)
                set_cell_margins(cell, top=80, bottom=80, left=130, right=130)
                set_cell_border(cell, top={'sz': 4, 'color': 'E2E8F0'},
                                      bottom={'sz': 4, 'color': 'E2E8F0'},
                                      left={'sz': 4, 'color': 'E2E8F0'},
                                      right={'sz': 4, 'color': 'E2E8F0'})
                p = cell.paragraphs[0]
                for r in p.runs:
                    r.font.name = 'Calibri'
                    r.font.size = Pt(9.5)
                    r.font.color.rgb = RGBColor(34, 34, 34)

        if col_widths:
            for row in table.rows:
                for c_idx, width in enumerate(col_widths):
                    row.cells[c_idx].width = Inches(width)

        doc.add_paragraph().paragraph_format.space_after = Pt(6)
        return table

    def embed_diagram(img_path, caption_num, caption_title, width_inches=6.0):
        if os.path.exists(img_path):
            p_img = doc.add_paragraph()
            p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p_img.paragraph_format.space_before = Pt(8)
            p_img.paragraph_format.space_after = Pt(4)
            p_img.paragraph_format.keep_with_next = True
            
            run_img = p_img.add_run()
            run_img.add_picture(img_path, width=Inches(width_inches))
            
            p_cap = doc.add_paragraph()
            p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p_cap.paragraph_format.space_after = Pt(8)
            p_cap.paragraph_format.keep_with_next = True
            r_cap = p_cap.add_run(f"Figure {caption_num} — {caption_title}")
            r_cap.font.name = 'Calibri'
            r_cap.font.size = Pt(9.5)
            r_cap.font.italic = True
            r_cap.font.bold = True
            r_cap.font.color.rgb = RGBColor(100, 116, 139)
        else:
            p_err = doc.add_paragraph(f"[Image file not found: {img_path}]")
            p_err.runs[0].font.color.rgb = RGBColor(239, 68, 68)

    # =========================================================================
    # PRELIMINARY SECTION 1: COVER PAGE / TITLE BLOCK
    # =========================================================================
    p_univ = doc.add_paragraph()
    p_univ.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_univ.paragraph_format.space_before = Pt(18)
    p_univ.paragraph_format.space_after = Pt(2)
    r_univ = p_univ.add_run("LOK JAGRUTI KENDRA UNIVERSITY")
    r_univ.font.size = Pt(20)
    r_univ.font.bold = True
    r_univ.font.color.rgb = RGBColor(27, 54, 93)

    p_dept = doc.add_paragraph()
    p_dept.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_dept.paragraph_format.space_after = Pt(2)
    r_dept = p_dept.add_run("DEPARTMENT OF COMPUTER APPLICATIONS (LJICA)")
    r_dept.font.size = Pt(13)
    r_dept.font.bold = True
    r_dept.font.color.rgb = RGBColor(71, 85, 105)

    p_course = doc.add_paragraph()
    p_course.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_course.paragraph_format.space_after = Pt(14)
    r_course = p_course.add_run("MASTER OF COMPUTER APPLICATIONS (INTEGRATED)")
    r_course.font.size = Pt(11)
    r_course.font.bold = True
    r_course.font.color.rgb = RGBColor(184, 147, 85)

    # Title Banner Box
    tbl_title = doc.add_table(rows=1, cols=1)
    tbl_title.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell_t = tbl_title.cell(0, 0)
    cell_t.width = Inches(6.5)
    set_cell_shading(cell_t, "1B365D")
    set_cell_margins(cell_t, top=180, bottom=180, left=240, right=240)
    set_cell_border(cell_t, top={'sz': 12, 'color': 'B89355'}, bottom={'sz': 12, 'color': 'B89355'},
                            left={'sz': 12, 'color': 'B89355'}, right={'sz': 12, 'color': 'B89355'})
    
    pt1 = cell_t.paragraphs[0]
    pt1.alignment = WD_ALIGN_PARAGRAPH.CENTER
    rt1 = pt1.add_run("ELEVÉ CLOTHING STORE")
    rt1.font.name = 'Calibri'
    rt1.font.size = Pt(23)
    rt1.font.bold = True
    rt1.font.color.rgb = RGBColor(226, 192, 141)

    pt2 = cell_t.add_paragraph()
    pt2.alignment = WD_ALIGN_PARAGRAPH.CENTER
    rt2 = pt2.add_run("An End-to-End Luxury Apparel & Fashion E-Commerce Web Application\nFull-Stack Implementation with PHP 8.x PDO & MySQL")
    rt2.font.name = 'Calibri'
    rt2.font.size = Pt(10.5)
    rt2.font.italic = True
    rt2.font.color.rgb = RGBColor(241, 245, 249)

    doc.add_paragraph().paragraph_format.space_after = Pt(10)

    # Project Metadata Table
    meta_headers = ["Academic Project Attribute", "Detailed Institutional Specification"]
    meta_data = [
        ["Project Title", "ELEVÉ Clothing Store (Luxury Apparel E-Commerce)"],
        ["Course & Degree", "MCA (Integrated) — Master of Computer Applications"],
        ["Department & Institute", "Dept. of Computer Applications, LJICA, LJKU, Ahmedabad"],
        ["Curated Merchandising Scope", "Strictly Curated Apparel: Men, Women, Kids (29 Verified Products, Zero Footwear)"],
        ["Internal Guide / Mentor", "Dr. Anjum Y. Mansuri, Assistant Professor (GitHub: @anjum-mansuri)"],
        ["Academic Year", "2024 – 2028"],
        ["Project Submission Date", "21/08/2026"],
        ["Project Repository", "https://github.com/jaimisha15/Eleve-clothing-ecommerce"]
    ]
    build_table(meta_headers, meta_data, col_widths=[2.3, 4.2])

    # Group Members Table
    doc.add_paragraph("GROUP MEMBERS & ROLE ALLOCATION", style='Normal').runs[0].font.bold = True
    team_headers = ["Div / Roll No.", "Student Name", "Assigned Project Role", "Core Responsibilities"]
    team_data = [
        ["E-01", "Harsh Agechaniya", "Documentation Lead & QA Analyst", "SRS authoring, UML diagrams, test verification, data dictionary"],
        ["E-10", "Mit Dodiya", "Backend Developer & Database Designer", "MySQL schema, PDO secure architecture, REST APIs, order engine"],
        ["H-11", "Jaimisha Chunara", "Frontend Developer & UI/UX Designer", "Luxury UI/UX, responsive views, cart/wishlist drawers, admin panel"]
    ]
    build_table(team_headers, team_data, col_widths=[1.1, 1.6, 1.8, 2.0])

    doc.add_page_break()

    # =========================================================================
    # PRELIMINARY SECTION 2: CERTIFICATE OF ORIGINALITY
    # =========================================================================
    add_h1("CERTIFICATE OF ORIGINALITY")
    doc.add_paragraph(
        "This is to certify that the project report entitled 'ELEVÉ CLOTHING STORE: A Full-Stack Luxury Apparel & Streetwear "
        "E-Commerce Web Application' submitted by Harsh Agechaniya (Roll No: E-01), Mit Dodiya (Roll No: E-10), and Jaimisha Chunara "
        "(Roll No: H-11) in partial fulfillment of the requirements for the award of the degree of Master of Computer Applications (Integrated) "
        "of Lok Jagruti Kendra University (LJKU), Ahmedabad, is a bonafide record of authentic software engineering work carried out by them "
        "under my guidance and supervision."
    )
    doc.add_paragraph(
        "The software architecture, database schemas, responsive interfaces, administrative controls, and system documentation "
        "embodied in this project have been independently engineered and have not been submitted to any other university or institute "
        "for the award of any other degree, diploma, or academic credential."
    )
    doc.add_paragraph().paragraph_format.space_after = Pt(36)

    # Signature Block
    sig_table = doc.add_table(rows=1, cols=2)
    sig_table.alignment = WD_TABLE_ALIGNMENT.CENTER
    c_left, c_right = sig_table.rows[0].cells
    c_left.width = Inches(3.2)
    c_right.width = Inches(3.3)

    p_sig_l = c_left.paragraphs[0]
    p_sig_l.add_run("_______________________________\n").font.bold = True
    p_sig_l.add_run("Dr. Anjum Y. Mansuri\n").font.bold = True
    p_sig_l.add_run("Internal Guide & Assistant Professor\nDepartment of Computer Applications\nLJ Institute of Computer Applications (LJICA)\nLok Jagruti Kendra University (LJKU)")

    p_sig_r = c_right.paragraphs[0]
    p_sig_r.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    p_sig_r.add_run("_______________________________\n").font.bold = True
    p_sig_r.add_run("Head of Department\n").font.bold = True
    p_sig_r.add_run("Department of Computer Applications\nLJ Institute of Computer Applications (LJICA)\nLok Jagruti Kendra University (LJKU)\nAhmedabad, Gujarat")

    doc.add_page_break()

    # =========================================================================
    # PRELIMINARY SECTION 3: ACKNOWLEDGEMENT
    # =========================================================================
    add_h1("ACKNOWLEDGEMENT")
    doc.add_paragraph(
        "We express our deepest sense of gratitude and respect to our revered project guide, Dr. Anjum Y. Mansuri, Assistant Professor, "
        "Department of Computer Applications, LJ Institute of Computer Applications, Lok Jagruti Kendra University, for her invaluable guidance, "
        "continuous technical critique, and inspiring encouragement throughout the conceptualization, development, and documentation phases "
        "of the ELEVÉ e-commerce platform."
    )
    doc.add_paragraph(
        "We extend our sincere thanks to the Department of Computer Applications and the administrative leadership of LJ Institute of "
        "Computer Applications (LJICA) for providing the computing infrastructure, local development environments, and academic resources "
        "required to bring this full-stack engineering project to fruition."
    )
    doc.add_paragraph(
        "Finally, we wish to express our heartfelt appreciation to our parents, family members, and classmates for their unceasing moral support "
        "and collaborative enthusiasm during long development sprints and testing sessions."
    )
    doc.add_paragraph().paragraph_format.space_after = Pt(28)

    p_ack_names = doc.add_paragraph()
    p_ack_names.alignment = WD_ALIGN_PARAGRAPH.RIGHT
    p_ack_names.add_run("Harsh Agechaniya (Div-Roll: E-01)\n").font.bold = True
    p_ack_names.add_run("Mit Dodiya (Div-Roll: E-10)\n").font.bold = True
    p_ack_names.add_run("Jaimisha Chunara (Div-Roll: H-11)\n").font.bold = True
    p_ack_names.add_run("MCA (Integrated) — Academic Year 2024–2028\nLok Jagruti Kendra University (LJKU)")

    doc.add_page_break()

    # =========================================================================
    # PRELIMINARY SECTION 4: TABLE OF CONTENTS
    # =========================================================================
    add_h1("TABLE OF CONTENTS")
    toc_data = [
        ["1.", "INTRODUCTION & PROJECT OVERVIEW", "1.1 Existing Systems | 1.2 Need for New System | 1.3 Objectives | 1.4 Problem Definition | 1.5 Core Modules | 1.6 Project Profile | 1.7 Assumptions | 1.8 Advantages & Scope"],
        ["2.", "PROJECT MANAGEMENT & PLANNING", "2.1 Team Structure & Role Allocation | 2.2 Agile Sprints & 16-Week Implementation Timeline (Gantt Chart)"],
        ["3.", "REQUIREMENT DETERMINATION & ANALYSIS", "3.1 Requirement Gathering Methodology | 3.2 Targeted User Personas | 3.3 Functional Requirements (FR-01 to FR-07) | 3.4 Non-Functional Requirements"],
        ["4.", "SYSTEM DESIGN & ARCHITECTURE", "4.1 3-Tier Enterprise Architecture | 4.2 Use Case Modeling & Flow of Events | 4.3 Domain Class Modeling | 4.4 UML Activity Diagram | 4.5 Data Dictionary (14 Relational Tables)"],
        ["5.", "DEVELOPMENT & IMPLEMENTATION", "5.1 Agile Sprint Review | 5.2 Coding Standards & Security | 5.3 Curated Catalog (29 Items, 0 Footwear) | 5.4 Key Algorithms | 5.5 Visual Implementation Walkthrough (15 Screenshots & Details)"],
        ["6.", "SOFTWARE TESTING & QUALITY ASSURANCE", "6.1 Testing Methodology | 6.2 Test Case Matrix & Validation (10 Critical Tests) | 6.3 Security Verification"],
        ["7.", "DEPLOYMENT & MAINTENANCE", "7.1 Local XAMPP Environment (Apache, PHP, MySQL) | 7.2 Database Self-Healing Migration | 7.3 Git Version Control Workflow"],
        ["8.", "CONCLUSION & FUTURE ENHANCEMENTS", "8.1 Key Milestones | 8.2 Academic Competency Review | 8.3 Future Enhancements | References & Citations"]
    ]
    build_table(["Chapter", "Topic Title", "Detailed Content Breakdown"], toc_data, col_widths=[0.8, 2.5, 3.2])

    # List of Figures Table
    doc.add_paragraph("LIST OF FIGURES & SYSTEM SCREENSHOTS", style='Normal').runs[0].font.bold = True
    lof_data = [
        ["Figure 2.1", "Project Implementation Gantt Chart (16-Week Agile Sprints)", "Chapter 2"],
        ["Figure 4.1", "Decoupled 3-Tier Enterprise Architecture Diagram", "Chapter 4"],
        ["Figure 4.2", "Comprehensive Use Case Diagram (Customer & Admin Workflows)", "Chapter 4"],
        ["Figure 4.3", "UML Activity Diagram — Order Placement & Transaction Pipeline", "Chapter 4"],
        ["Figure 5.1", "Storefront Landing Page & Hero Section (index.html)", "Chapter 5"],
        ["Figure 5.2", "Storefront Value Propositions & 'Shop By Gender' Section", "Chapter 5"],
        ["Figure 5.3", "Instant Live Search Modal Overlay with Trending Suggestions", "Chapter 5"],
        ["Figure 5.4", "Women's Department Showcase & Editorial Collection Banner (women.html)", "Chapter 5"],
        ["Figure 5.5", "Apparel Product Catalog Grid & Department Count (Women - 10 Items)", "Chapter 5"],
        ["Figure 5.6", "Multi-Faceted Catalog Filtering by Category (Jeans), Sizes & Price", "Chapter 5"],
        ["Figure 5.7", "Product Detail Quick-View, Size Matrix & Slide-Out Wishlist Drawer", "Chapter 5"],
        ["Figure 5.8", "Slide-Out Cart Drawer with Real-Time Total Recalculation", "Chapter 5"],
        ["Figure 5.9", "Customer Authentication Modal (Sign In / Create Account / Guest Checkout)", "Chapter 5"],
        ["Figure 5.10", "Checkout Pipeline Stage 1 — Customer Shipping Address & Order Summary", "Chapter 5"],
        ["Figure 5.11", "Checkout Pipeline Stage 2 — Payment Method Selection & Order Placement", "Chapter 5"],
        ["Figure 5.12", "Admin Control Center Dashboard — Key Performance Indicators & Sales Analytics", "Chapter 5"],
        ["Figure 5.13", "Admin Products Inventory Master Management — 29 Verified Apparel Items", "Chapter 5"],
        ["Figure 5.14", "Admin Categories Registry & Department Management (admin/categories.php)", "Chapter 5"],
        ["Figure 5.15", "Admin Site Settings & System Economics Configuration (admin/settings.php)", "Chapter 5"]
    ]
    build_table(["Figure No.", "Figure Title & System Description", "Chapter Location"], lof_data, col_widths=[1.1, 4.3, 1.1])

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 1: INTRODUCTION & PROJECT OVERVIEW
    # =========================================================================
    add_h1("1. INTRODUCTION & PROJECT OVERVIEW")
    doc.add_paragraph(
        "ELEVÉ is an enterprise-grade, full-stack responsive e-commerce web application engineered specifically for luxury modern apparel "
        "and curated fashion merchandising. Developed natively using semantic HTML5, modern CSS3 custom properties, modular client-side "
        "JavaScript (ES6+), and a high-performance PHP 8.x PDO backend connecting to a normalized MySQL 8.0 relational database, ELEVÉ delivers "
        "a boutique fashion shopping experience free of framework overhead and visual clutter."
    )
    doc.add_paragraph(
        "A central architectural differentiator of the ELEVÉ platform is its strictly curated product catalog: the application offers 29 verified "
        "garments exclusively distributed across three apparel departments — Men (11 items), Women (10 items), and Kids (8 items) — with all "
        "non-apparel categories (specifically footwear and footwear imagery) completely eliminated from the store taxonomy. Shoppers benefit "
        "from sub-150ms client-side live search, a sliding Cart drawer with live state persistence, an interactive Wishlist drawer, and an "
        "integrated checkout flow supporting multiple payment channels. Concurrently, store managers access a secure Admin Control Center "
        "equipped with real-time sales KPI widgets, a 7-day revenue trend chart, product inventory management, category governance, and "
        "order lifecycle management including single-click order deletion."
    )

    add_h2("1.1 Existing Systems & Operational Friction")
    doc.add_paragraph("In conventional brick-and-mortar apparel retail and generic multi-vendor e-commerce marketplaces, fashion consumers encounter persistent operational friction:")
    add_bullet("1. Brick-and-Mortar Retail Bottlenecks:", "Shoppers must physically travel to shopping centers, search through crowded garment racks, endure fitting room queues, and experience frequent stock-outs where desired sizes or colorways are unavailable on the showroom floor.")
    add_bullet("2. Generic Marketplaces Overhead:", "Large commercial marketplaces intersperse apparel with electronics, home goods, and promotional advertisements. Heavy page bundles and third-party tracking scripts lead to sluggish mobile browsing speeds and confusing multi-level navigation.")
    add_bullet("3. Top-Only Navigation Fatigue:", "Most conventional platforms anchor cart, search, and navigation controls solely at the top of the viewport. When users scroll down long catalog grids, they are forced to repetitively scroll back to the page header to inspect their cart or alter search parameters.")

    add_h2("1.2 Need for the New Curated Platform")
    doc.add_paragraph("To overcome these inefficiencies, ELEVÉ was conceptualized around four foundational pillars:")
    add_bullet("24/7 Omnichannel Accessibility:", "Customers can browse, filter, customize, and order premium apparel anytime from smartphones, tablets, or desktop computers.")
    add_bullet("Strict Apparel Focus:", "By eliminating unrelated categories like shoes and accessories, ELEVÉ maintains 100% merchandising clarity centered strictly on stylish Men's, Women's, and Kids' garments.")
    add_bullet("Frictionless Shopping Ergonomics:", "Slide-out side drawers for Cart and Wishlist allow instantaneous quantity adjustments and bookmarking without leaving the browsing context.")
    add_bullet("Self-Sustaining Store Governance:", "A robust administrative control suite enables store operators to manage product listings, track fulfillment states, review customer revenue, and adjust store economics in real time.")

    add_h2("1.3 Project Objectives")
    add_bullet("Objective 1 (Curated Visual Merchandising):", "Structure 29 authentic garments across Men's T-Shirts & Jeans, Women's T-Shirts & Jeans, and Kids' T-Shirts, Jeans & Co-Ord Sets.")
    add_bullet("Objective 2 (High-Performance Client Layer):", "Engineer debounced live search, multi-facet category filters, price range sliders, and size matrices executing client-side in under 150ms.")
    add_bullet("Objective 3 (Enterprise Security & Integrity):", "Implement PHP Data Objects (PDO) with parameterized prepared statements, preventing SQL injection vulnerabilities, and Bcrypt password hashing.")
    add_bullet("Objective 4 (Unified Administration Center):", "Provide administrative dashboards tracking revenue metrics, order statuses, customer shipping names, and catalog CRUD operations.")

    add_h2("1.4 Problem Definition")
    doc.add_paragraph(
        "Modern fashion consumers demand the aesthetic elegance of high-end editorial boutiques paired with the instant reactivity of modern web apps. "
        "Traditional retail cannot offer 24/7 availability or instant size-based filtering, while bloated multi-vendor platforms overwhelm users with clutter. "
        "ELEVÉ addresses this problem by implementing a dedicated, ultra-responsive luxury fashion storefront backed by secure local database persistence."
    )

    add_h2("1.5 Core Modules Breakdown")
    add_bullet("Header & Live Search Module:", "Includes brand logo, instant modal search overlay, dedicated [ADMIN] button, and [LOGIN / REGISTER] customer authentication control.")
    add_bullet("Curated Department Bar:", "Visual pill navigation with garment thumbnails providing one-click routing to Women, Men, and Kids departments.")
    add_bullet("Storefront Landing Page (index.html):", "Editorial hero section ('DEFINE YOUR ELEGANCE'), value proposition badges, and 'Shop By Gender' portal.")
    add_bullet("Departmental Portals (women.html, men.html, kids.html):", "Demographic-specific views with dedicated editorial banners and filtered product grids.")
    add_bullet("Interactive Detail View (product-detail.html):", "Garment photo galleries, color swatches, size selectors (XS-XXL), and accordion fabric specifications.")
    add_bullet("Cart & Wishlist Drawers:", "Off-canvas sliding side panels with live line-item totals, promo coupon application, and shipping threshold trackers.")
    add_bullet("Secure Checkout Pipeline (checkout.html):", "Two-stage checkout capturing recipient shipping details and multi-channel payment selection (COD, UPI, Card, Net Banking).")
    add_bullet("Admin Control Center (admin/):", "Protected back-office suite for KPI analytics, 7-day revenue charts, product CRUD, category management, and order processing.")

    add_h2("1.6 Project Profile")
    p_prof = [
        ["Project Title", "ELEVÉ — Luxury Apparel E-Commerce Platform"],
        ["Application Architecture", "Decoupled 3-Tier Enterprise Web Application"],
        ["Frontend Technologies", "Semantic HTML5, CSS3 Custom Properties (Flexbox/Grid), Vanilla JavaScript (ES6+)"],
        ["Backend Technologies", "PHP 8.2+ PDO Driver (Parameterized Prepared Statements, Native Session Management)"],
        ["Database Engine", "MySQL 8.0 / MariaDB (InnoDB, utf8mb4_unicode_ci) deployed via XAMPP"],
        ["Web Server Environment", "Apache 2.4 HTTP Server (Local XAMPP Virtual Host)"],
        ["Development Team", "3 MCA (Integrated) Students, LJICA, Lok Jagruti Kendra University"],
        ["Project Timeline", "March 2026 – August 2026 (16-Week Agile Delivery Lifecycle)"],
        ["Verified Apparel Catalog", "29 Authentic Items (11 Men, 10 Women, 8 Kids, 0 Footwear)"],
        ["Administrative Credentials", "admin@gmail.com / Password: 123 (Role: admin)"]
    ]
    build_table(["Technical Parameter", "Implementation Specification"], p_prof, col_widths=[2.2, 4.3])

    add_h2("1.7 Assumptions & Constraints")
    add_bullet("Assumptions:", "Clients access the platform via modern standards-compliant web browsers (Chrome, Edge, Firefox, Safari) with JavaScript enabled; local Apache and MySQL services are active via XAMPP.")
    add_bullet("Constraints:", "Payment gateways are simulated for academic demonstration; production deployment requires commercial SSL/TLS certificates and registered payment gateway API keys.")

    add_h2("1.8 Advantages & Operational Scope")
    add_bullet("Advantages:", "Near-instantaneous client-side page transitions without heavy SPA bundle downloads; complete immunity to SQL injection via PDO; automated database self-healing initialization; elegant luxury aesthetic.")
    add_bullet("Scope Boundaries:", "Current implementation is structured for single-brand direct-to-consumer apparel sales; multi-vendor marketplace onboarding and automated courier dispatch webhooks are scheduled for future iterations.")

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 2: PROJECT MANAGEMENT & PLANNING
    # =========================================================================
    add_h1("2. PROJECT MANAGEMENT & PLANNING")
    add_h2("2.1 Team Structure & Role Allocation")
    doc.add_paragraph(
        "To ensure disciplined execution across both client and server tiers, the project was organized into three specialized functional roles. "
        "Each team member assumed leadership over distinct lifecycle deliverables while maintaining collaborative integration:"
    )
    team_mat = [
        ["Harsh Agechaniya (Div-Roll: E-01)", "Documentation Lead & QA Analyst", "SRS authoring, UML modeling (Use Case, Activity, Architecture), formal test matrix execution, data dictionary specification, and academic reporting."],
        ["Mit Dodiya (Div-Roll: E-10)", "Backend Developer & Database Designer", "MySQL relational schema design, 3NF normalization, PHP PDO REST controllers, session state verification, transaction atomicity, and order processing APIs."],
        ["Jaimisha Chunara (Div-Roll: H-11)", "Frontend Developer & UI/UX Designer", "Luxury fashion UI design system, responsive layouts, client state persistence (Cart, Wishlist), debounced live search, checkout flow, and admin dashboard styling."]
    ]
    build_table(["Student Name & Roll No.", "Project Role", "Key Technical Responsibilities"], team_mat, col_widths=[1.8, 1.8, 2.9])

    add_h2("2.2 Proposed Timeline & 16-Week Agile Sprints")
    doc.add_paragraph(
        "The project was executed following Agile Scrum principles partitioned into eight 2-week sprints over a 16-week timeline. "
        "Figure 2.1 displays the project implementation Gantt chart illustrating parallel workflows across database engineering, "
        "frontend template design, backend API development, and quality assurance."
    )
    gantt_data = [
        ["Sprint 1 (Weeks 1-2)", "01-Mar to 14-Mar", "Inception & Feasibility: Problem definition, stakeholder interviews, literature review", "Harsh A."],
        ["Sprint 2 (Weeks 3-4)", "15-Mar to 28-Mar", "System Modeling & UI Wireframes: UML design (Use Case, Class, Activity), color palettes", "Jaimisha C."],
        ["Sprint 3 (Weeks 5-6)", "29-Mar to 11-Apr", "Database Engineering: Relational 3NF normalization, database.sql schema, seed scripts", "Mit D."],
        ["Sprint 4 (Weeks 7-9)", "12-Apr to 02-May", "Frontend Development: HTML5 semantic views, CSS design tokens, responsive grid layouts", "Jaimisha C."],
        ["Sprint 5 (Weeks 10-12)", "03-May to 23-May", "Backend & API Integration: PHP PDO services, session handling, cart & orders API", "Mit D."],
        ["Sprint 6 (Weeks 13-14)", "24-May to 13-Jun", "Admin Panel Development: KPI dashboard, order lifecycle status updates, single-click deletion", "Full Team"],
        ["Sprint 7 (Weeks 15-16)", "14-Jun to 11-Jul", "Catalog Finalization: Local clothing reference cataloging (29 products), purging footwear", "Full Team"],
        ["Sprint 8 (Weeks 17-20)", "12-Jul to 21-Aug", "QA, Testing & Final Documentation: Cross-browser testing, Word report preparation, submission", "Harsh A."]
    ]
    build_table(["Sprint Phase", "Timeline", "Milestones & Core Deliverables", "Sprint Lead"], gantt_data, col_widths=[1.5, 1.2, 3.0, 0.8])

    # Embed Gantt Chart Diagram
    gantt_path = r'D:\FINALUDPFOLDER\doc_assets\gantt_chart.png'
    embed_diagram(gantt_path, "2.1", "Project Implementation Gantt Chart (16-Week Agile Sprints)", width_inches=6.2)

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 3: REQUIREMENT DETERMINATION & ANALYSIS
    # =========================================================================
    add_h1("3. REQUIREMENT DETERMINATION & ANALYSIS")
    add_h2("3.1 Requirement Determination Methodology")
    doc.add_paragraph("Requirements were systematically elicited through stakeholder interviews, competitive retail benchmarking, and workflow modeling:")
    add_bullet("User Questionnaires & Interviews:", "Conducted interviews with university students and online shoppers to identify pain points in apparel browsing, size selection, and cart abandonment.")
    add_bullet("Competitive Benchmarking:", "Analyzed premier fashion platforms (Zara, ASOS, Max Fashion) to extract best practices in sticky category navigation, off-canvas drawers, and inline search.")
    add_bullet("Workflow Modeling:", "Mapped traditional in-store apparel checkout procedures into an automated digital equivalent with dynamic coupon validation and real-time shipping threshold logic.")

    add_h2("3.2 Targeted User Personas")
    user_data = [
        ["Store Administrator", "System Admin", "Full governance privileges: audit gross revenue, monitor pending orders, update fulfillment statuses, perform single-click order deletions, and manage product inventory."],
        ["Registered Customer", "Primary End User", "Authenticated shopper: bookmark garments to wishlist, complete checkout, review past order histories, and update delivery profile information."],
        ["Guest Shopper", "Unauthenticated User", "Casual visitor: browse complete catalog, utilize live search, filter garments by size and price, manage cart drawer, and complete checkout without forced account registration."],
        ["Gift / Occasion Buyer", "Targeted Shopper", "Demographic-focused shopper seeking specific apparel items for family members using direct Men, Women, or Kids department filters."]
    ]
    build_table(["User Persona", "System Role", "System Privileges & Primary User Goals"], user_data, col_widths=[1.4, 1.3, 3.8])

    add_h2("3.3 Functional Requirements (FR)")
    add_bullet("FR-01 (Authentication & Authorization):", "Provide secure user registration, Bcrypt-hashed password authentication, role-based access control separating admin from standard user, and frictionless guest checkout.")
    add_bullet("FR-02 (Curated Apparel Catalog):", "Display 29 verified apparel items strictly partitioned into Men, Women, and Kids wear with zero non-clothing entries.")
    add_bullet("FR-03 (Real-Time Live Search):", "Execute client-side substring matching on garment titles, subcategories, and tags within 150ms via debounced event listeners.")
    add_bullet("FR-04 (Multi-Faceted Filtering & Sorting):", "Allow shoppers to filter products dynamically by department, apparel category (T-Shirts, Jeans, Co-Ord Sets), garment size (XS-XXL), and price range slider (₹0 – ₹4,000).")
    add_bullet("FR-05 (Garment Customization & Drawers):", "Support dynamic size pill selection, color swatch indicators, sliding Cart drawer with real-time recalculation, and slide-out Wishlist flyout.")
    add_bullet("FR-06 (Order Processing & Checkout):", "Validate customer recipient details, compute delivery charges and coupon savings, atomically insert master order and line-item records, and generate unique tracking numbers.")
    add_bullet("FR-07 (Administrative Store Governance):", "Equip administrators with live KPI metrics, 7-day revenue charts, order lifecycle status transitions, single-click order deletion, and inventory CRUD.")

    add_h2("3.4 Non-Functional Requirements (NFR)")
    add_bullet("NFR-01 (Performance & Latency):", "Initial page load time under 1.5 seconds; client-side search and filtering latency under 150 milliseconds.")
    add_bullet("NFR-02 (Responsive Adaptability):", "Fluid typography and flex/grid layouts adapting seamlessly across mobile (360px+), tablet (768px+), laptop (1024px+), and desktop (1440px+) displays.")
    add_bullet("NFR-03 (Application Security):", "100% parameterized SQL queries via PHP PDO driver preventing SQL Injection; input sanitization preventing Cross-Site Scripting (XSS); session fixation defenses.")
    add_bullet("NFR-04 (Data Integrity & Fault Tolerance):", "Database transactions wrapped in PDO beginTransaction() / commit() blocks ensuring atomic order creation; resilient fallback links for offline browsing.")

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 4: SYSTEM DESIGN & ARCHITECTURE
    # =========================================================================
    add_h1("4. SYSTEM DESIGN & ARCHITECTURE")
    add_h2("4.1 3-Tier Enterprise Architecture")
    doc.add_paragraph(
        "ELEVÉ is architected as a decoupled 3-tier enterprise web application: the Client Presentation Tier (HTML5/CSS3/JavaScript), "
        "the Application Business Logic Tier (Apache 2.4 & PHP 8.x PDO REST controllers), and the Relational Data Persistence Tier (MySQL 8.0 InnoDB). "
        "Figure 4.1 depicts the structural interaction between these tiers."
    )
    arch_path = r'D:\FINALUDPFOLDER\doc_assets\architecture_diagram.png'
    embed_diagram(arch_path, "4.1", "Decoupled 3-Tier Enterprise Architecture Diagram — ELEVÉ Platform", width_inches=6.2)

    add_h2("4.2 Use Case Modeling & Actor Specifications")
    doc.add_paragraph(
        "Figure 4.2 illustrates the comprehensive use case diagram defining interactions between the system's two primary actors: "
        "the Customer (Guest & Registered) and the Store Administrator."
    )
    uc_path = r'D:\FINALUDPFOLDER\doc_assets\use_case_diagram.png'
    embed_diagram(uc_path, "4.2", "Comprehensive Use Case Diagram — Customer & Administrator Interactions", width_inches=6.2)

    uc_data = [
        ["UC-01", "Browse Department Catalogs", "Customer / Guest", "Navigate curated garments filtered strictly by Men, Women, or Kids departments."],
        ["UC-02", "Instant Live Search", "Customer / Guest", "Type keywords into search bar to view immediate visual product suggestions and pricing."],
        ["UC-03", "Customize & Select Garment", "Customer / Guest", "View product detail page, select garment size and color swatches, verify inventory availability."],
        ["UC-04", "Manage Cart & Apply Coupon", "Customer / Guest", "Adjust item quantities in sliding drawer; validate promo codes (WELCOME10) for instant savings."],
        ["UC-05", "Submit Order via Checkout", "Customer / Guest", "Fill recipient shipping details, select payment channel (COD, UPI, Card), submit order to database."],
        ["UC-06", "Customer Authentication", "Registered Customer", "Authenticate credentials, access order history, manage personal delivery address."],
        ["UC-07", "Admin Control & Dashboard", "Store Administrator", "Access admin console, audit revenue, update order statuses, delete test orders, manage products."]
    ]
    build_table(["Use Case ID", "Use Case Name", "Primary Actor", "Description & Flow of Events"], uc_data, col_widths=[1.1, 1.8, 1.3, 2.3])

    add_h2("4.3 Domain Class Modeling & Entity Operations")
    class_data = [
        ["User", "id, name, email, password, phone, role, is_active", "login(), register(), updateProfile(), verifyPassword()"],
        ["Category", "id, name, slug, gender, image_url, is_active, sort_order", "create(), update(), delete(), getProducts()"],
        ["Product", "id, name, category, gender, price, stock_qty, rating", "save(), updateStock(), calculateDiscount(), delete()"],
        ["Order", "id, order_number, user_id, status, payment_status, total", "create(), updateStatus(), delete(), calculateSubtotal()"],
        ["OrderItem", "id, order_id, product_id, size, color, qty, price, total", "insert(), getLineTotal(), validateInventory()"],
        ["Coupon", "id, code, type, value, min_order, is_active", "apply(), calculateSavings(), incrementUsage()"]
    ]
    build_table(["Domain Entity", "Core Attributes", "Business Logic Methods"], class_data, col_widths=[1.2, 2.6, 2.7])

    add_h2("4.4 UML Activity Diagram & Order Placement Pipeline")
    doc.add_paragraph(
        "Figure 4.3 illustrates the end-to-end UML activity diagram tracing the user's operational path from product discovery, "
        "garment customization, sliding cart inspection, shipping address submission, and database transaction commit to order confirmation."
    )
    act_path = r'D:\FINALUDPFOLDER\doc_assets\activity_diagram.png'
    embed_diagram(act_path, "4.3", "UML Activity Diagram — End-to-End Order Placement & Transaction Flow", width_inches=6.0)

    add_h2("4.5 Comprehensive Data Dictionary (14 Relational Tables)")
    doc.add_paragraph("The relational schema comprises 14 normalized tables in 3NF, enforcing referential integrity with foreign key constraints:")

    # Table: users
    doc.add_paragraph("Table 1: users (Customer & Administrator Accounts)", style='Normal').runs[0].font.bold = True
    users_dict = [
        ["id", "INT", "PK, AUTO_INC", "Unique account identifier", "1"],
        ["name", "VARCHAR(100)", "NOT NULL", "Full commercial name", "Admin / Rahul Sharma"],
        ["email", "VARCHAR(150)", "UNIQUE, NOT NULL", "Login email address", "admin@gmail.com"],
        ["password", "VARCHAR(255)", "NOT NULL", "Bcrypt cryptographic hash", "$2y$12$NdjdsG..."],
        ["phone", "VARCHAR(20)", "NULL", "Contact telephone number", "+91 98251 12345"],
        ["address", "TEXT", "NULL", "Primary delivery address", "A-402, Titanium City"],
        ["role", "ENUM('user','admin')", "DEFAULT 'user'", "Authorization privilege level", "admin"],
        ["is_active", "TINYINT(1)", "DEFAULT 1", "Account activity status toggle", "1"],
        ["created_at", "TIMESTAMP", "CURRENT_TIMESTAMP", "Registration timestamp", "2026-11-20 10:00:00"]
    ]
    build_table(["Column Name", "Data Type", "Constraints", "Description", "Example Value"], users_dict, col_widths=[1.1, 1.2, 1.1, 2.0, 1.1])

    # Table: categories
    doc.add_paragraph("Table 2: categories (Apparel Department Master Registry)", style='Normal').runs[0].font.bold = True
    cat_dict = [
        ["id", "INT", "PK, AUTO_INC", "Unique category identifier", "1"],
        ["name", "VARCHAR(100)", "NOT NULL", "Display name of apparel department", "Men's T-Shirts"],
        ["slug", "VARCHAR(100)", "UNIQUE, NOT NULL", "URL-safe routing identifier", "men-tshirts"],
        ["gender", "ENUM('men','women','kids')", "NOT NULL", "Target demographic", "men"],
        ["image_url", "VARCHAR(500)", "NULL", "Relative thumbnail asset path", "images/men/tshirt-1.jpg"],
        ["is_active", "TINYINT(1)", "DEFAULT 1", "Navigation visibility flag", "1"],
        ["sort_order", "INT", "DEFAULT 0", "Ordering priority sequence", "1"]
    ]
    build_table(["Column Name", "Data Type", "Constraints", "Description", "Example Value"], cat_dict, col_widths=[1.1, 1.2, 1.1, 2.0, 1.1])

    # Table: products
    doc.add_paragraph("Table 3: products (Apparel Master Registry — 29 Verified Items)", style='Normal').runs[0].font.bold = True
    prod_dict = [
        ["id", "INT", "PK, AUTO_INC", "Unique garment item identifier", "1"],
        ["name", "VARCHAR(200)", "NOT NULL", "Full commercial garment title", "Men's Noir Graphic Tee"],
        ["category", "VARCHAR(100)", "NOT NULL", "Primary apparel classification", "T-Shirts"],
        ["subcategory", "VARCHAR(100)", "NULL", "Secondary filter tag", "t-shirts / jeans"],
        ["gender", "ENUM('men','women','kids')", "NOT NULL", "Apparel demographic target", "men"],
        ["price", "DECIMAL(10,2)", "NOT NULL", "Selling price in INR (₹)", "699.00"],
        ["old_price", "DECIMAL(10,2)", "NULL", "MRP for strikethrough comparison", "899.00"],
        ["badge", "VARCHAR(50)", "NULL", "Merchandising highlight badge", "Best Seller / New"],
        ["rating", "DECIMAL(3,2)", "DEFAULT 4.50", "Customer star rating", "4.80"],
        ["stock_qty", "INT", "DEFAULT 100", "Warehouse inventory level", "85"],
        ["description", "TEXT", "NULL", "Garment styling narrative", "Signature luxury streetwear..."],
        ["fit", "TEXT", "NULL", "Garment silhouette guide", "Relaxed streetwear fit"],
        ["details", "TEXT", "NULL", "Pipe-delimited specifications", "100% Supima cotton|220GSM"],
        ["is_active", "TINYINT(1)", "DEFAULT 1", "Catalog display toggle", "1"]
    ]
    build_table(["Column Name", "Data Type", "Constraints", "Description", "Example Value"], prod_dict, col_widths=[1.1, 1.2, 1.1, 2.0, 1.1])

    # Table: orders
    doc.add_paragraph("Table 4: orders (Master Customer Purchases Registry)", style='Normal').runs[0].font.bold = True
    order_dict = [
        ["id", "INT", "PK, AUTO_INC", "Unique order identifier", "1"],
        ["order_number", "VARCHAR(20)", "UNIQUE, NOT NULL", "Public serial tracking number", "ELV-2026-1001"],
        ["user_id", "INT", "FK (users.id) NULL", "Account ID if registered shopper", "NULL / 2"],
        ["status", "ENUM('pending','confirmed','shipped','delivered')", "DEFAULT 'pending'", "Fulfillment lifecycle state", "delivered"],
        ["payment_status", "ENUM('pending','paid','failed')", "DEFAULT 'pending'", "Payment settlement state", "paid"],
        ["payment_method", "VARCHAR(50)", "DEFAULT 'cod'", "Selected payment mode", "cod / upi / card"],
        ["subtotal", "DECIMAL(10,2)", "NOT NULL", "Subtotal before discount (₹)", "2098.00"],
        ["discount", "DECIMAL(10,2)", "DEFAULT 0.00", "Promo savings deducted (₹)", "200.00"],
        ["total", "DECIMAL(10,2)", "NOT NULL", "Final payable transaction amount (₹)", "1898.00"],
        ["shipping_name", "VARCHAR(150)", "NOT NULL", "Recipient full customer name", "Rahul Sharma"],
        ["shipping_email", "VARCHAR(150)", "NOT NULL", "Delivery notification email", "rahul.sharma@example.com"],
        ["shipping_phone", "VARCHAR(20)", "NOT NULL", "Delivery contact mobile number", "+91 98251 12345"],
        ["shipping_address", "TEXT", "NOT NULL", "Premises and street address", "A-402, Titanium City"],
        ["shipping_city", "VARCHAR(100)", "NOT NULL", "Destination city", "Ahmedabad"],
        ["shipping_state", "VARCHAR(100)", "NOT NULL", "Destination state", "Gujarat"],
        ["shipping_pincode", "VARCHAR(10)", "NOT NULL", "6-digit postal code", "380015"],
        ["created_at", "TIMESTAMP", "CURRENT_TIMESTAMP", "Order timestamp", "2026-11-20 10:15:00"]
    ]
    build_table(["Column Name", "Data Type", "Constraints", "Description", "Example Value"], order_dict, col_widths=[1.1, 1.2, 1.1, 2.0, 1.1])

    # Table: order_items
    doc.add_paragraph("Table 5: order_items (Purchased Line Items Registry)", style='Normal').runs[0].font.bold = True
    oi_dict = [
        ["id", "INT", "PK, AUTO_INC", "Order item line identifier", "1"],
        ["order_id", "INT", "FK (orders.id) CASCADE", "Parent order reference", "1"],
        ["product_id", "INT", "FK (products.id) SET NULL", "Source garment reference", "1"],
        ["product_name", "VARCHAR(200)", "NOT NULL", "Garment title snapshot", "Men's Noir Graphic Tee"],
        ["product_image", "VARCHAR(500)", "NULL", "Garment thumbnail path snapshot", "images/men/tshirt-1.jpg"],
        ["size", "VARCHAR(20)", "NOT NULL", "Ordered garment size", "L"],
        ["color", "VARCHAR(50)", "NULL", "Ordered color swatch", "Black"],
        ["qty", "INT", "NOT NULL", "Quantity purchased", "1"],
        ["price", "DECIMAL(10,2)", "NOT NULL", "Unit price at purchase (₹)", "699.00"],
        ["total", "DECIMAL(10,2)", "NOT NULL", "Line total (price × qty) (₹)", "699.00"]
    ]
    build_table(["Column Name", "Data Type", "Constraints", "Description", "Example Value"], oi_dict, col_widths=[1.1, 1.2, 1.1, 2.0, 1.1])

    # Summary of Tables 6 to 14
    doc.add_paragraph("Tables 6 to 14: Ancillary Relational Schemas", style='Normal').runs[0].font.bold = True
    anc_dict = [
        ["Table 6: product_images", "id, product_id (FK), image_url, is_primary, sort_order", "Supports multi-photo high-definition product galleries."],
        ["Table 7: product_sizes", "id, product_id (FK), size (XS-XXL), stock_qty", "Manages discrete inventory levels per garment size."],
        ["Table 8: product_colors", "id, product_id (FK), color_name, color_hex", "Stores visual hex color swatches for product detail selection."],
        ["Table 9: coupons", "id, code, type (percent/fixed), value, min_order, is_active", "Powers promo discount validation (WELCOME10, FLAT200, SAVE15)."],
        ["Table 10: wishlist", "id, user_id (FK), product_id (FK), created_at", "Maintains customer bookmarked favorites with persistent sync."],
        ["Table 11: cart_items", "id, user_id, session_id, product_id (FK), size, qty", "Persists server-side shopping cart across user browsing sessions."],
        ["Table 12: reviews", "id, product_id (FK), user_id (FK), rating, comment, is_approved", "Stores customer reviews, ratings, and moderation flags."],
        ["Table 13: settings", "id, setting_key, setting_value, updated_at", "Stores global platform economics (free shipping threshold, tax rate)."],
        ["Table 14: newsletter", "id, email, is_subscribed, created_at", "Captures marketing newsletter subscriber leads."]
    ]
    build_table(["Table Name", "Key Column Definitions", "Functional Role in System Architecture"], anc_dict, col_widths=[1.8, 2.3, 2.4])

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 5: DEVELOPMENT & IMPLEMENTATION (THE 5TH POINT)
    # =========================================================================
    add_h1("5. DEVELOPMENT & IMPLEMENTATION")
    add_h2("5.1 Agile Sprint Implementation Review")
    doc.add_paragraph(
        "Development was executed using Agile sprints with continuous peer code reviews. "
        "Frontend templates were authored using semantic HTML5 and vanilla JavaScript IIFEs (Immediately Invoked Function Expressions) "
        "to prevent global namespace pollution. The backend was organized into clean modular REST controllers in the php/ directory, "
        "relying strictly on the PHP Data Objects (PDO) driver for database transactions. Version control was maintained via Git, "
        "enabling safe feature branching and automated self-healing database seeding via seed_db.php."
    )

    add_h2("5.2 Coding Standards, Prepared Statements & Security Best Practices")
    add_bullet("SQL Injection Prevention:", "100% of database queries execute via PDO parameterized prepared statements ($stmt = $pdo->prepare(...); $stmt->execute([...])). No raw user inputs are ever concatenated into SQL strings.")
    add_bullet("Cross-Site Scripting (XSS) Defense:", "All dynamic client outputs are passed through htmlspecialchars() on the server or assigned via DOM textContent / sanitizeHTML utilities on the client.")
    add_bullet("Password Cryptography:", "User and administrator passwords are encrypted using Bcrypt with a computational cost factor of 12 (password_hash(..., PASSWORD_BCRYPT)).")
    add_bullet("Customer vs. Admin Auth Separation:", "Strict architectural decoupling ensures the administrative session never overrides or modifies the customer login control in the navigation bar.")

    add_h2("5.3 Curated Catalog Architecture (29 Items, Zero Footwear)")
    doc.add_paragraph(
        "In strict accordance with project requirements, the product database and image assets were curated to contain exactly 29 verified "
        "apparel items spanning Men, Women, and Kids. All footwear products, shoe categories, and shoe imagery were completely eradicated:"
    )
    cat_summary = [
        ["Men's Apparel", "11 Items", "Men's T-Shirts (6 items) & Men's Jeans (5 items)", "images/men/"],
        ["Women's Apparel", "10 Items", "Women's T-Shirts (5 items) & Women's Jeans (5 items)", "images/women/"],
        ["Kids' Apparel", "8 Items", "Kids' T-Shirts (3 items), Jeans (3 items) & Co-Ord Sets (2 items)", "images/kids/"],
        ["Footwear", "0 Items (PURGED)", "Completely removed from database, taxonomy, and site views", "None"]
    ]
    build_table(["Department Classification", "Verified Items", "Apparel Categories Included", "Asset Directory"], cat_summary, col_widths=[1.8, 1.2, 2.3, 1.2])

    add_h2("5.4 Key Frontend & Backend Algorithms")
    add_bullet("Debounced Live Search Algorithm:", "A timer-based debounce wrapper (300ms) intercepts keystrokes in the search bar, executing client-side substring matching across product names, subcategories, and tags without flooding the DOM.")
    add_bullet("Real-Time Cart Calculation Engine:", "Calculates line totals (price × quantity), computes shipping threshold progress (₹999 free shipping threshold), applies validated coupon discount multipliers, and updates all header badges synchronously.")
    add_bullet("Atomic Order Insertion Engine:", "Wrapped inside $pdo->beginTransaction(), the order creation engine inserts the parent record into orders, maps line items into order_items, preserves the customer shipping name, and commits atomically.")

    # =========================================================================
    # 5.5 SCREENSHOTS & IMPLEMENTATION DETAILS (ALL 15 AUTHENTIC SCREENSHOTS)
    # =========================================================================
    add_h2("5.5 Visual Implementation Walkthrough & Live System Screenshots")
    doc.add_paragraph(
        "The following section presents the comprehensive visual walkthrough of the ELEVÉ e-commerce platform, "
        "featuring all 15 authentic system module screenshots captured directly from the live working environment. "
        "Each figure is accompanied by an in-depth technical breakdown of its architecture, DOM manipulation, and database operations."
    )

    doc_assets_dir = r'D:\FINALUDPFOLDER\doc_assets'
    backup_assets_dir = r'C:\Users\jaimisha\.gemini\antigravity\brain\f2ef212f-279b-48a2-9af8-8748f6793618\.user_uploaded'

    screenshots_data = [
        {
            "num": "5.1",
            "primary_file": "fig5_01_landing_hero.png",
            "backup_file": "media_1795617548214_c0f4f815.png",
            "title": "Storefront Landing Page & Hero Section (index.html)",
            "width": 6.2,
            "overview": (
                "The primary landing gateway establishes brand authority with a full-bleed luxury editorial banner featuring the brand statement "
                "'DEFINE YOUR ELEGANCE'. The sticky top navigation bar includes the ELEVÉ brand logo, an inline search input with instant matching, "
                "a single dedicated [ADMIN] pill button linking directly to the administrative control center, and a dedicated [LOGIN / REGISTER] "
                "button for customer account management. Circular department navigation pills for Women, Men, and Kids allow immediate 1-click routing."
            ),
            "tech": [
                ["View Template", "index.html & styles.css"],
                ["Header Architecture", "CSS Sticky positioning with backdrop-filter blur on scroll (app.js initHeader throttled via requestAnimationFrame)"],
                ["Navigation Controls", "Strict separation between administrative navigation (.btn-admin-pill) and customer authentication (#authBtn)"],
                ["Category Bar", "Dynamic department pills displaying garment thumbnails with persistent hover scale micro-interactions"]
            ]
        },
        {
            "num": "5.2",
            "primary_file": "fig5_02_value_props.png",
            "backup_file": "media_1795617547691_0b4b2800.png",
            "title": "Storefront Value Propositions & 'Shop By Gender' Section",
            "width": 6.2,
            "overview": (
                "Highlights core brand assurances: Free Shipping on orders over ₹999, Premium Handcrafted Materials, 30-Day Easy Returns, "
                "and 100% Secure Encrypted Payment. A highlighted gold banner announces the free delivery threshold. Below, the 'SHOP BY GENDER' "
                "section presents curated entry points for Men, Women, and Kids fashion."
            ),
            "tech": [
                ["Grid Layout", "CSS Grid with responsive repeat(auto-fit, minmax(200px, 1fr))"],
                ["Typography Hierarchy", "Playfair Display serif headings paired with clean Inter body typography"],
                ["Threshold Logic", "Synchronized with backend free_shipping_min setting (₹999) stored in database settings table"],
                ["Visual Badges", "Scalable SVG vector icons with subtle hover elevation transitions"]
            ]
        },
        {
            "num": "5.3",
            "primary_file": "fig5_03_live_search.png",
            "backup_file": "media_1795617547759_caa7855f.png",
            "title": "Instant Live Search Modal Overlay with Trending Suggestions",
            "width": 6.2,
            "overview": (
                "Clicking the header search bar or bottom navigation triggers a glassmorphic modal overlay. As the customer types, "
                "client-side search algorithms query product titles, subcategories, and tags in real time without reloading the page. "
                "Trending apparel picks (e.g. Men's Noir Graphic Tee, Minimalist Crew Neck Tee) are surfaced dynamically with thumbnails and pricing."
            ),
            "tech": [
                ["Component Controller", "app.js initSearch() with debounced input listener (300ms delay)"],
                ["Data Index", "Queries in-memory product arrays loaded from product-data.js and product-data-extra.js"],
                ["UX Ergonomics", "Escape key listener and backdrop click dismissing the search overlay instantly"],
                ["Rendering Performance", "DocumentFragment batch rendering avoiding repetitive DOM repaints"]
            ]
        },
        {
            "num": "5.4",
            "primary_file": "fig5_04_womens_banner.png",
            "backup_file": "media_1795617547747_6ea0f749.png",
            "title": "Women's Department Showcase & Editorial Collection Banner (women.html)",
            "width": 6.2,
            "overview": (
                "Dedicated departmental portal displaying the 'Women's Collection — Elegant, bold, effortless'. "
                "Features high-fashion editorial imagery showcasing everyday essentials and statement apparel pieces. "
                "A direct call-to-action 'SHOP ALL WOMEN'S' scrolls smoothly to the curated catalog grid."
            ),
            "tech": [
                ["Department Filter", "Hard-filtered for gender='women' displaying only the 10 verified women's apparel garments"],
                ["Department Pill Bar", "Active gold pill indicator highlighting Women department in persistent sub-navigation"],
                ["Responsive Imagery", "CSS object-fit: cover with aspect-ratio management preventing Cumulative Layout Shift (CLS)"],
                ["Smooth Scroll", "Native CSS scroll-behavior: smooth linking hero CTA directly to product grid"]
            ]
        },
        {
            "num": "5.5",
            "primary_file": "fig5_05_catalog_grid.png",
            "backup_file": "media_1795617548158_eeb34a0c.png",
            "title": "Apparel Product Catalog Grid & Department Count (Women - 10 Items)",
            "width": 6.2,
            "overview": (
                "The catalog view demonstrates responsive multi-column product card rendering. The sidebar shows Gender filter set to 'Women' "
                "with an active count badge '10 Products'. Garment cards display high-resolution local photography, gold 'NEW' badges, "
                "garment titles, category classifications, and pricing."
            ),
            "tech": [
                ["State Management", "Dynamic product rendering via app.js initFilters() reading data-* attributes"],
                ["Hover Effects", "Subtle 3D card tilt and hover zoom implemented in CSS3 transforms"],
                ["Image Pipeline", "Loads local optimized assets from images/women/ directory"],
                ["Responsive Grid", "CSS Grid repeat(auto-fill, minmax(260px, 1fr)) with 24px gap"]
            ]
        },
        {
            "num": "5.6",
            "primary_file": "fig5_06_catalog_filters.png",
            "backup_file": "media_1795617548125_d8b36ddd.png",
            "title": "Multi-Faceted Catalog Filtering by Category (Jeans), Sizes & Price",
            "width": 6.2,
            "overview": (
                "Illustrates the multi-faceted filtering engine. The shopper filters specifically for Jeans, selecting size parameters (XS, S, M, L, XL). "
                "The product listing dynamically updates to show Women's Slim Fit Ankle Jeans (₹1,399) and Women's Classic Denim Trousers (₹1,249) "
                "along with their star ratings (4.7 and 4.5). A 'CLEAR ALL FILTERS' button allows 1-click filter reset."
            ),
            "tech": [
                ["Filter Engine", "Multi-parameter predicate filtering in JavaScript combining gender, category, size, and price slider values"],
                ["Sorting Algorithm", "Client-side sort by Price (Low to High, High to Low), Customer Rating, and Newest Arrival"],
                ["DOM Performance", "DocumentFragment batch insertion minimizing browser reflows and repaints"],
                ["State Reset", "Single-click reset restoring default full catalog parameters without page refresh"]
            ]
        },
        {
            "num": "5.7",
            "primary_file": "fig5_07_product_wishlist.png",
            "backup_file": "media_1795617547735_3482751e.png",
            "title": "Product Detail Quick-View, Size Matrix & Slide-Out Wishlist Drawer",
            "width": 6.2,
            "overview": (
                "Displays the interactive garment detail page for 'Men's Noir Graphic Tee' (₹699). Highlights include color swatch selection (Black, White, Navy), "
                "size selector pills (S, M, L, XL, XXL), quantity counter, and 'ADD TO CART' / 'ADDED TO WISHLIST' action buttons. "
                "Simultaneously, the 'YOUR WISHLIST' drawer slides out smoothly showing bookmarked favorites with 1-click remove."
            ),
            "tech": [
                ["Customization Logic", "app.js initSizeSelector() and initColorSelector() binding selected options to checkout payload"],
                ["Wishlist Persistence", "localStorage persistence under key 'elev_wishlist' with real-time navbar counter badge updates"],
                ["Fabric Composition", "Accordion specifications parsing pipe-separated details from database (100% Supima cotton, 220GSM)"],
                ["Drawer Synchronization", "Shared state listener notifying cart and wishlist drawer instances on item mutation"]
            ]
        },
        {
            "num": "5.8",
            "primary_file": "fig5_08_cart_drawer.png",
            "backup_file": "media_1795617547697_d89d6c52.png",
            "title": "Slide-Out Cart Drawer with Real-Time Total Recalculation",
            "width": 4.0,
            "overview": (
                "The slide-out Cart drawer provides instant order feedback without navigating away from the catalog. "
                "Shows 'Women's Cropped Graphic T-Shirt' (Size: XS, Color: White, Qty: 1) priced at ₹599. "
                "Features an item removal cross button, automated subtotal calculation (Total: ₹599), and a prominent 'PROCEED TO CHECKOUT' button."
            ),
            "tech": [
                ["Drawer Animation", "CSS3 transform: translateX(0) with cubic-bezier timing and backdrop overlay blur"],
                ["Cart Calculation", "app.js Cart.updateUI() recalculating line totals, shipping threshold progress, and badge counters"],
                ["Session Sync", "Background non-blocking sync to server-side cart_items table via php/cart.php"],
                ["Direct Routing", "One-click transition from cart drawer directly into checkout.html"]
            ]
        },
        {
            "num": "5.9",
            "primary_file": "fig5_09_auth_modal.png",
            "backup_file": "media_1795617547688_ebdfff68.png",
            "title": "Customer Authentication Modal (Sign In / Create Account / Guest Checkout)",
            "width": 4.2,
            "overview": (
                "The luxury customer account modal opened via the header [LOGIN / REGISTER] button. Features tabbed navigation between 'SIGN IN' "
                "and 'CREATE ACCOUNT'. Validates email format and password entry. For shoppers who prefer not to create an account, "
                "a dedicated 'CONTINUE AS GUEST' button allows direct, frictionless checkout."
            ),
            "tech": [
                ["Auth Controller", "app.js initAuthModal() submitting asynchronous FormData to php/auth.php"],
                ["Security Protocol", "Server-side password_verify() against bcrypt hashes with cost factor 12 in users table"],
                ["Separation of Concerns", "Strict separation ensures admin authentication never overrides the customer login interface"],
                ["Modal Ergonomics", "Full keyboard trap (Tab navigation) and Escape key listener closing modal"]
            ]
        },
        {
            "num": "5.10",
            "primary_file": "fig5_10_checkout_shipping.png",
            "backup_file": "media_1795617547700_518e89c7.png",
            "title": "Checkout Pipeline Stage 1 — Customer Shipping Address & Order Summary",
            "width": 6.2,
            "overview": (
                "First stage of the checkout pipeline (checkout.html). The customer enters Full Name, Mobile Phone, Email, Delivery Street Address, "
                "City (Mumbai), State, and 6-digit Pincode (400001). The right-hand column presents the Order Summary card with garment thumbnails, "
                "size/color specifications, subtotal (₹599), standard shipping fee (₹99), coupon input, and total calculation (₹698)."
            ),
            "tech": [
                ["Form Validation", "Client-side regex verification for mandatory fields, valid email syntax, and 6-digit Indian pincodes"],
                ["Coupon Engine", "Asynchronous validation against coupons table (WELCOME10, FLAT200, SAVE15) with instant discount deduction"],
                ["Local Routing", "checkout.html getApiUrl() ensuring cross-origin requests route seamlessly to local XAMPP Apache endpoint"],
                ["Shipping Calculation", "Free shipping automatically applied if subtotal meets or exceeds ₹999 threshold"]
            ]
        },
        {
            "num": "5.11",
            "primary_file": "fig5_11_checkout_payment.png",
            "backup_file": "media_1795617547706_ec6271ff.png",
            "title": "Checkout Pipeline Stage 2 — Payment Method Selection & Order Placement",
            "width": 6.2,
            "overview": (
                "Second stage of the checkout pipeline showing multi-channel payment method selection: Cash on Delivery (selected by default), "
                "UPI Payment (PhonePe, GPay, Paytm), Credit / Debit Card (Visa, Mastercard, RuPay), and Net Banking. "
                "Clicking 'PLACE ORDER →' commits the transaction, generates an authentic order number (e.g. ELV369A0E25), and displays the confirmation modal."
            ),
            "tech": [
                ["Transaction Engine", "php/orders.php placeOrder() executing within PDO beginTransaction() / commit() boundary"],
                ["Relational Inserts", "Atomic creation of master order record in orders table and item line entries in order_items table"],
                ["Customer Name Preservation", "Accurately records the customer's shipping name into orders.shipping_name for administrative reporting"],
                ["Serial Generator", "Autonomous collision-free order serial generation algorithm formatted as ELV-YYYY-XXXX"]
            ]
        },
        {
            "num": "5.12",
            "primary_file": "fig5_12_admin_dashboard.png",
            "backup_file": "media_1795617547720_fd01cb2b.png",
            "title": "Admin Control Center Dashboard — Key Performance Indicators & Sales Analytics",
            "width": 6.2,
            "overview": (
                "The administrative control suite (admin/index.php) protected by admin@gmail.com / 123 credentials. "
                "Features live KPI metric cards: Total Revenue (₹9,700 from paid orders), Total Orders (6 orders, 2 placed today), "
                "Customers (5 newsletter subscribers), Active Products (29 verified apparel items), and Pending Orders (2 requiring fulfillment). "
                "An interactive Chart.js revenue graph tracks daily sales over the preceding 7 days."
            ),
            "tech": [
                ["Access Control", "Enforced via admin/includes/header.php requireAdmin() checking $_SESSION['role'] === 'admin'"],
                ["Database Queries", "Real-time SQL aggregations: SUM(total) WHERE payment_status='paid', COUNT(*) GROUP BY DATE(created_at)"],
                ["Quick Action Hub", "Direct navigation pills to Add Product, View Pending Orders, Read Contact Messages, and Create Coupons"],
                ["Data Visualization", "Responsive Chart.js canvas with dual-axis representation of revenue volume and order frequency"]
            ]
        },
        {
            "num": "5.13",
            "primary_file": "fig5_13_admin_products.png",
            "backup_file": "media_1795617547805_ba2f2788.png",
            "title": "Admin Products Inventory Master Management — 29 Verified Apparel Items",
            "width": 6.2,
            "overview": (
                "The comprehensive product catalog management console displaying All Products (29). "
                "Each garment entry presents its thumbnail photograph, product name, subcategory tag, gender target (Kids, Men, Women), "
                "selling price, warehouse stock level (100 units), and 'Active' status badge. "
                "Action buttons provide instant Edit, Stock Alert, and Delete capabilities."
            ),
            "tech": [
                ["Asset Resolver", "adminImgUrl() helper function resolving relative image paths cleanly between admin/ and images/ directories"],
                ["Inventory CRUD", "Full create, read, update, and soft/hard delete capabilities linked to products, product_images, and product_sizes tables"],
                ["Data Integrity", "Foreign key CASCADE ensures deletion cleans up associated images and sizes without database orphan records"],
                ["Search & Filter", "Real-time client-side table search filtering products by title, gender, or subcategory"]
            ]
        },
        {
            "num": "5.14",
            "primary_file": "fig5_14_admin_categories.png",
            "backup_file": "media_1795617547728_2efa378e.png",
            "title": "Admin Categories Registry & Department Management (admin/categories.php)",
            "width": 6.2,
            "overview": (
                "Registry view showing All Categories (7): Men's T-Shirts, Men's Jeans, Women's T-Shirts, Women's Jeans, "
                "Kids T-Shirts, Kids Jeans, and Kids Co-Ord Sets. Displays thumbnail previews, URL slug identifiers, gender association, "
                "display sort order, and active status toggles. The right-hand panel includes an '+ Add Category' form for creating new departments."
            ),
            "tech": [
                ["Data Mapping", "Direct PDO query against categories table sorted by sort_order ASC"],
                ["Slug Generation", "Automatic sanitization converting department titles into URL-safe slugs (e.g. men-tshirts)"],
                ["Image Thumbnailing", "Circular 36x36px object-fit cover previews with fallback icons"],
                ["Sort Priority", "Integer sort_order weighting controlling menu arrangement order on client views"]
            ]
        },
        {
            "num": "5.15",
            "primary_file": "fig5_15_admin_settings.png",
            "backup_file": "media_1795617547681_ebce4817.png",
            "title": "Admin Site Settings & System Economics Configuration (admin/settings.php)",
            "width": 6.2,
            "overview": (
                "Global platform configuration console divided into modular cards: General Settings (Site Name: ELEVÉ, Support Email, "
                "Support Phone, Currency Symbol: ₹, Maintenance Mode toggle), Commerce Settings (Free Shipping Minimum: ₹999, Tax Rate: 18%), "
                "Homepage Hero Customization (Hero Title: 'Define Your Elegance'), and Administrative Password Management."
            ),
            "tech": [
                ["Key-Value Storage", "Dynamically loads and updates settings key-value rows in the settings table"],
                ["Security Control", "Allows secure administrative password updates using Bcrypt password_hash() routines"],
                ["Immediate Effect", "Configuration changes apply instantly across storefront headers, banners, and checkout tax calculations"],
                ["Maintenance Mode", "Global toggle intercepting client requests with friendly maintenance notice when active"]
            ]
        }
    ]

    for item in screenshots_data:
        add_h3(f"Figure {item['num']}: {item['title']}")
        
        # Resolve Image Path (check doc_assets first, fallback to user_uploaded)
        img_p1 = os.path.join(doc_assets_dir, item['primary_file'])
        img_p2 = os.path.join(backup_assets_dir, item['backup_file'])
        chosen_img = img_p1 if os.path.exists(img_p1) else (img_p2 if os.path.exists(img_p2) else None)

        if chosen_img:
            p_img = doc.add_paragraph()
            p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p_img.paragraph_format.space_before = Pt(6)
            p_img.paragraph_format.space_after = Pt(4)
            p_img.paragraph_format.keep_with_next = True
            
            run_img = p_img.add_run()
            run_img.add_picture(chosen_img, width=Inches(item['width']))
            
            p_cap = doc.add_paragraph()
            p_cap.alignment = WD_ALIGN_PARAGRAPH.CENTER
            p_cap.paragraph_format.space_after = Pt(8)
            p_cap.paragraph_format.keep_with_next = True
            r_cap = p_cap.add_run(f"Figure {item['num']} — {item['title']}")
            r_cap.font.name = 'Calibri'
            r_cap.font.size = Pt(9.5)
            r_cap.font.italic = True
            r_cap.font.bold = True
            r_cap.font.color.rgb = RGBColor(100, 116, 139)
        else:
            p_err = doc.add_paragraph(f"[Image file not found: {item['primary_file']} / {item['backup_file']}]")
            p_err.runs[0].font.color.rgb = RGBColor(239, 68, 68)

        # Overview Paragraph
        p_desc = doc.add_paragraph()
        p_desc.paragraph_format.space_after = Pt(4)
        r_lbl = p_desc.add_run("Module Overview: ")
        r_lbl.font.bold = True
        r_lbl.font.color.rgb = RGBColor(27, 54, 93)
        p_desc.add_run(item['overview'])

        # Implementation Table
        build_table(["Technical Aspect", "Architectural Implementation Details"], item['tech'], col_widths=[2.0, 4.5])

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 6: SOFTWARE TESTING & QUALITY ASSURANCE
    # =========================================================================
    add_h1("6. SOFTWARE TESTING & QUALITY ASSURANCE")
    add_h2("6.1 Testing Methodology")
    doc.add_paragraph(
        "Quality assurance followed a multi-phase testing strategy encompassing Unit Testing, Integration Testing, "
        "User Acceptance Testing (UAT), and Cross-Browser / Cross-Device Compatibility Verification. "
        "All test procedures were conducted against PHP 8.x PDO services and MySQL 8.0 on local Apache servers."
    )

    add_h2("6.2 Formal Test Case Matrix & Validation Results")
    test_data = [
        ["TC-01", "Live Product Search", "Type 'jeans' in search input", "Dropdown renders only matching jeans with images and prices within 150ms", "PASSED"],
        ["TC-02", "Apparel Taxonomy", "Verify Men, Women, Kids", "All 29 items display strictly under clothing departments; 0 footwear found", "PASSED"],
        ["TC-03", "Cart Drawer Mutation", "Add 'Noir Tee' (Size: L)", "Cart drawer slides out; subtotal updates to ₹699; counter badge increments to 1", "PASSED"],
        ["TC-04", "Coupon Code Savings", "Apply coupon 'WELCOME10'", "10% discount computed; total decreases; feedback alert displays '10% applied'", "PASSED"],
        ["TC-05", "Shipping Validation", "Submit empty shipping form", "HTML5 client validation highlights mandatory fields (Name, Phone, Address)", "PASSED"],
        ["TC-06", "Order Placement Atomicity", "Complete COD checkout", "Record created in orders table; items inserted in order_items; cart cleared", "PASSED"],
        ["TC-07", "Customer Name Preservation", "Inspect admin orders view", "Order displays customer recipient name 'Rahul Sharma' instead of 'Guest'", "PASSED"],
        ["TC-08", "Admin Authentication", "Login: admin@gmail.com / 123", "Session $_SESSION['role'] === 'admin' verified; routes to admin/index.php", "PASSED"],
        ["TC-09", "Order Deletion Lifecycle", "Click Delete on test order", "Order removed from database via CASCADE; revenue metrics recalculate immediately", "PASSED"],
        ["TC-10", "SQL Injection Resistance", "Input: ' OR '1'='1 in search/auth", "PDO prepared statement treats payload as literal string; 0 exploits found", "PASSED"]
    ]
    build_table(["Test ID", "Test Scenario", "Input / Action", "Expected Behavioral Outcome", "Test Status"], test_data, col_widths=[0.8, 1.6, 1.6, 2.0, 0.8])

    add_h2("6.3 Security Verification & Penetration Testing")
    doc.add_paragraph("Security audits confirmed resilience against common web vulnerabilities:")
    add_bullet("SQL Injection Defense:", "Verified that all queries use PDO prepared statements with bounded parameters. Penetration test attempts utilizing tautologies (' OR '1'='1) and UNION SELECT attacks failed completely.")
    add_bullet("Cross-Site Scripting Defense:", "Tested malicious script payloads (<script>alert('xss')</script>) in customer name, shipping address, and review forms. All outputs were correctly sanitized via htmlspecialchars().")
    add_bullet("Session Hijacking & Fixation:", "Verified session_regenerate_id(true) on authentication and strict privilege isolation preventing user sessions from acquiring admin rights.")

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 7: DEPLOYMENT & MAINTENANCE
    # =========================================================================
    add_h1("7. DEPLOYMENT & MAINTENANCE")
    add_h2("7.1 Local XAMPP Environment Configuration")
    doc.add_paragraph(
        "ELEVÉ is designed for rapid zero-dependency deployment on local XAMPP stacks. The system operates on standard ports "
        "(Apache: 80/443, MySQL: 3306) and requires no Node.js build steps or Composer dependencies for production execution."
    )
    deploy_steps = [
        ["Step 1", "Environment Setup", "Download and launch XAMPP Control Panel; start Apache and MySQL services."],
        ["Step 2", "Project Placement", "Place project directory at D:\\FINALUDPFOLDER or copy into C:\\xampp\\htdocs\\FINALUDPFOLDER."],
        ["Step 3", "Database Initialization", "Access phpMyAdmin or run http://localhost/FINALUDPFOLDER/seed_db.php to auto-create and seed all 14 tables and 29 products."],
        ["Step 4", "Storefront Access", "Open http://localhost/FINALUDPFOLDER/index.html to explore the live customer storefront."],
        ["Step 5", "Admin Access", "Navigate to http://localhost/FINALUDPFOLDER/admin/login.php and sign in with admin@gmail.com / 123."]
    ]
    build_table(["Deployment Step", "Operational Action", "Technical Procedure & Verification Command"], deploy_steps, col_widths=[1.2, 1.8, 3.5])

    add_h2("7.2 Database Self-Healing Migration (seed_db.php)")
    doc.add_paragraph(
        "To guarantee zero manual setup overhead for evaluators and faculty, ELEVÉ includes an autonomous self-healing database "
        "migration script (seed_db.php). When executed, it verifies the existence of eleve_db, provisions all 14 relational tables "
        "with proper foreign keys, populates the verified 29 apparel products with accurate pricing and image paths, creates the "
        "administrator credentials, and injects realistic demonstration orders."
    )

    add_h2("7.3 Git Version Control Workflow")
    doc.add_paragraph(
        "The project repository is hosted on GitHub at https://github.com/jaimisha15/Eleve-clothing-ecommerce. "
        "The repository follows standard Git branching conventions, tracking semantic feature commits with detailed message logs."
    )

    doc.add_page_break()

    # =========================================================================
    # CHAPTER 8: CONCLUSION & FUTURE ENHANCEMENTS
    # =========================================================================
    add_h1("8. CONCLUSION & FUTURE ENHANCEMENTS")
    add_h2("8.1 Key Project Milestones Achieved")
    doc.add_paragraph(
        "The ELEVÉ clothing store e-commerce application successfully demonstrates the end-to-end design, implementation, and deployment "
        "of a full-stack luxury apparel shopping platform. Over the 16-week Agile lifecycle, our team achieved the following core milestones:"
    )
    add_bullet("Curated Merchandising Mastery:", "Created an exclusive catalog of 29 real apparel items spanning Men, Women, and Kids wear, completely purging footwear.")
    add_bullet("Full-Stack Architectural Rigor:", "Engineered lightweight, responsive client interfaces integrated with secure PHP 8.x PDO controllers and a 14-table MySQL database.")
    add_bullet("Comprehensive Store Administration:", "Built an administrative dashboard with real-time KPI metrics, revenue charts, product CRUD, and order lifecycle management.")
    add_bullet("Academic Standards Compliance:", "Fulfilled all curriculum benchmarks for the Master of Computer Applications (Integrated) degree at Lok Jagruti Kendra University.")

    add_h2("8.2 Academic Learning Outcomes")
    doc.add_paragraph(
        "Through this User Driven Project (UDP), our team gained deep practical experience in relational database normalization (3NF), "
        "parameterized query security, stateful off-canvas UI components, asynchronous REST API communication, and collaborative Git workflows."
    )

    add_h2("8.3 Future Product Roadmap")
    add_bullet("Production Payment Gateways:", "Integrate live merchant webhooks via Razorpay and Stripe for automated UPI QR code and NetBanking callbacks.")
    add_bullet("AI-Powered Fashion Advisor:", "Implement client-side machine learning algorithms to suggest coordinated apparel sets based on browsing history.")
    add_bullet("Automated Courier Logistics API:", "Connect order dispatches directly to Shiprocket or Delhivery APIs for automated Airway Bill (AWB) generation and live tracking.")
    add_bullet("Progressive Web App (PWA):", "Add service workers and offline web app manifests allowing mobile users to install ELEVÉ directly to their home screens.")

    add_h2("8.4 References & Academic Bibliography")
    refs = [
        ["[1]", "World Wide Web Consortium (W3C)", "HTML5 & CSS3 Specification Standards", "https://www.w3.org/TR/html52/"],
        ["[2]", "Mozilla Developer Network (MDN)", "JavaScript ES6+ Language Specification & DOM API Documentation", "https://developer.mozilla.org/"],
        ["[3]", "The PHP Group", "PHP 8.x PDO Manual & Prepared Statement Security Guidelines", "https://www.php.net/manual/en/book.pdo.php"],
        ["[4]", "Oracle Corporation", "MySQL 8.0 Reference Manual: InnoDB Foreign Key Optimization", "https://dev.mysql.com/doc/"],
        ["[5]", "Martin, Robert C.", "Clean Code: A Handbook of Agile Software Craftsmanship", "Prentice Hall, 2008"],
        ["[6]", "Dr. Anjum Y. Mansuri", "Academic Project Guidance, Assistant Professor, LJICA, LJKU", "GitHub: @anjum-mansuri"]
    ]
    build_table(["No.", "Author / Organization", "Publication Title / Academic Reference", "Resource URL / Location"], refs, col_widths=[0.6, 2.0, 2.2, 1.7])

    # =========================================================================
    # DOCUMENT PERSISTENCE & MULTI-DESTINATION SAVING
    # =========================================================================
    primary_doc_name = "ELEVE_Clothing_Store_Project_Documentation_Final.docx"
    standard_doc_name = "ELEVE_Clothing_Store_Project_Documentation.docx"

    out_final_workspace = os.path.join(r'D:\FINALUDPFOLDER', primary_doc_name)
    out_std_workspace = os.path.join(r'D:\FINALUDPFOLDER', standard_doc_name)

    # Save to primary workspace path
    doc.save(out_final_workspace)
    print(f"SUCCESS: Master documentation saved to: {out_final_workspace}")

    # Attempt to save to standard workspace path (if unlocked)
    try:
        doc.save(out_std_workspace)
        print(f"SUCCESS: Standard documentation saved to: {out_std_workspace}")
    except PermissionError:
        print(f"NOTE: {out_std_workspace} is currently locked by Microsoft Word. Master copy saved cleanly as {primary_doc_name}.")

    # Copy to Desktop
    desktop_dir = r'C:\Users\jaimisha\OneDrive\Pictures\Desktop'
    if not os.path.exists(desktop_dir):
        desktop_dir = os.path.expanduser('~/Desktop')

    if os.path.exists(desktop_dir):
        dest_desktop_final = os.path.join(desktop_dir, primary_doc_name)
        try:
            doc.save(dest_desktop_final)
            print(f"SUCCESS: Master copy saved to Desktop: {dest_desktop_final}")
        except Exception as e:
            print(f"Desktop save error: {e}")

        dest_desktop_std = os.path.join(desktop_dir, standard_doc_name)
        try:
            doc.save(dest_desktop_std)
            print(f"SUCCESS: Standard copy saved to Desktop: {dest_desktop_std}")
        except Exception as e:
            print(f"Desktop standard copy error: {e}")

    print("Academic Documentation generation fully completed.")

if __name__ == '__main__':
    build_academic_documentation()
