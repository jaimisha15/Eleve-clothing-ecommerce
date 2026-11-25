import os
import docx
from docx import Document
from docx.shared import Inches, Pt, RGBColor
from docx.enum.text import WD_ALIGN_PARAGRAPH
from docx.enum.table import WD_TABLE_ALIGNMENT, WD_ALIGN_VERTICAL
from docx.oxml import OxmlElement, parse_xml
from docx.oxml.ns import nsdecls, qn

def create_element(name):
    return OxmlElement(name)

def set_cell_background(cell, fill_hex):
    tcPr = cell._tc.get_or_add_tcPr()
    shd = parse_xml(f'<w:shd {nsdecls("w")} w:fill="{fill_hex}"/>')
    tcPr.append(shd)

def set_cell_margins(cell, top=100, bottom=100, left=150, right=150):
    tcPr = cell._tc.get_or_add_tcPr()
    tcMar = parse_xml(f'<w:tcMar {nsdecls("w")}><w:top w:w="{top}" w:type="dxa"/><w:bottom w:w="{bottom}" w:type="dxa"/><w:left w:w="{left}" w:type="dxa"/><w:right w:w="{right}" w:type="dxa"/></w:tcMar>')
    tcPr.append(tcMar)

def set_table_borders(table, color="D3D3D3", sz="4", val="single"):
    tblPr = table._tbl.tblPr
    borders = parse_xml(
        f'<w:tblBorders {nsdecls("w")}>'
        f'<w:top w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:bottom w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:left w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:right w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:insideH w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'<w:insideV w:val="{val}" w:sz="{sz}" w:space="0" w:color="{color}"/>'
        f'</w:tblBorders>'
    )
    tblPr.append(borders)

def style_heading(doc, text, level):
    h = doc.add_heading(text, level=level)
    h.paragraph_format.keep_with_next = True
    if level == 1:
        h.paragraph_format.space_before = Pt(18)
        h.paragraph_format.space_after = Pt(6)
        for r in h.runs:
            r.font.name = 'Calibri'
            r.font.size = Pt(16)
            r.font.bold = True
            r.font.color.rgb = RGBColor(0x1E, 0x3A, 0x8A) # Deep Navy
    elif level == 2:
        h.paragraph_format.space_before = Pt(14)
        h.paragraph_format.space_after = Pt(4)
        for r in h.runs:
            r.font.name = 'Calibri'
            r.font.size = Pt(13)
            r.font.bold = True
            r.font.color.rgb = RGBColor(0x25, 0x63, 0xEB) # Blue
    elif level == 3:
        h.paragraph_format.space_before = Pt(10)
        h.paragraph_format.space_after = Pt(2)
        for r in h.runs:
            r.font.name = 'Calibri'
            r.font.size = Pt(11)
            r.font.bold = True
            r.font.color.rgb = RGBColor(0x37, 0x41, 0x51) # Charcoal
    return h

def add_p(doc, text="", bold_prefix="", space_after=4):
    p = doc.add_paragraph()
    p.paragraph_format.space_after = Pt(space_after)
    p.paragraph_format.line_spacing = 1.15
    if bold_prefix:
        r_b = p.add_run(bold_prefix)
        r_b.font.name = 'Calibri'
        r_b.font.size = Pt(10.5)
        r_b.font.bold = True
        r_b.font.color.rgb = RGBColor(0x1F, 0x29, 0x37)
    if text:
        r_t = p.add_run(text)
        r_t.font.name = 'Calibri'
        r_t.font.size = Pt(10.5)
        r_t.font.color.rgb = RGBColor(0x37, 0x41, 0x51)
    return p

def add_bullet(doc, text="", bold_prefix=""):
    p = doc.add_paragraph(style='List Bullet')
    p.paragraph_format.space_after = Pt(3)
    p.paragraph_format.line_spacing = 1.15
    if bold_prefix:
        r_b = p.add_run(bold_prefix)
        r_b.font.name = 'Calibri'
        r_b.font.size = Pt(10)
        r_b.font.bold = True
        r_b.font.color.rgb = RGBColor(0x1F, 0x29, 0x37)
    if text:
        r_t = p.add_run(text)
        r_t.font.name = 'Calibri'
        r_t.font.size = Pt(10)
        r_t.font.color.rgb = RGBColor(0x37, 0x41, 0x51)
    return p

def format_table(table, col_widths, headers, rows):
    set_table_borders(table, color="D1D5DB")
    table.alignment = WD_TABLE_ALIGNMENT.CENTER
    
    # Header row
    hdr_cells = table.rows[0].cells
    for i, h in enumerate(headers):
        hdr_cells[i].text = h
        set_cell_background(hdr_cells[i], "1E3A8A")
        set_cell_margins(hdr_cells[i], top=120, bottom=120, left=150, right=150)
        p = hdr_cells[i].paragraphs[0]
        p.alignment = WD_ALIGN_PARAGRAPH.LEFT
        for r in p.runs:
            r.font.name = 'Calibri'
            r.font.size = Pt(9.5)
            r.font.bold = True
            r.font.color.rgb = RGBColor(0xFF, 0xFF, 0xFF)
            
    # Set repeat header on each page
    trPr = table.rows[0]._tr.get_or_add_trPr()
    trPr.append(parse_xml(f'<w:tblHeader {nsdecls("w")}/>'))
    
    # Rows
    for r_idx, row_data in enumerate(rows):
        row = table.add_row()
        # Prevent row split across pages
        trPr = row._tr.get_or_add_trPr()
        trPr.append(parse_xml(f'<w:cantSplit {nsdecls("w")}/>'))
        
        bg_color = "F9FAFB" if r_idx % 2 == 1 else "FFFFFF"
        for c_idx, cell_value in enumerate(row_data):
            c = row.cells[c_idx]
            c.text = str(cell_value)
            set_cell_background(c, bg_color)
            set_cell_margins(c, top=80, bottom=80, left=140, right=140)
            p = c.paragraphs[0]
            p.alignment = WD_ALIGN_PARAGRAPH.LEFT
            for r in p.runs:
                r.font.name = 'Calibri'
                r.font.size = Pt(9)
                r.font.color.rgb = RGBColor(0x1F, 0x29, 0x37)

    # Set column widths
    for row in table.rows:
        for idx, width in enumerate(col_widths):
            row.cells[idx].width = Inches(width)

def build_document():
    doc = Document()
    
    # Page Setup: Letter, 1-inch margins
    sections = doc.sections
    for section in sections:
        section.top_margin = Inches(1.0)
        section.bottom_margin = Inches(1.0)
        section.left_margin = Inches(1.0)
        section.right_margin = Inches(1.0)
        
    print("Building Document Structure...")

    # ==============================================================
    # COVER / TITLE PAGE
    # ==============================================================
    p_univ = doc.add_paragraph()
    p_univ.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_univ.paragraph_format.space_before = Pt(20)
    p_univ.paragraph_format.space_after = Pt(4)
    r = p_univ.add_run("LOK JAGRUTI KENDRA UNIVERSITY")
    r.font.name = 'Calibri'
    r.font.size = Pt(18)
    r.font.bold = True
    r.font.color.rgb = RGBColor(0x0F, 0x17, 0x2A)
    
    p_dept = doc.add_paragraph()
    p_dept.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_dept.paragraph_format.space_after = Pt(2)
    r = p_dept.add_run("Department of Computer Applications")
    r.font.name = 'Calibri'
    r.font.size = Pt(13)
    r.font.color.rgb = RGBColor(0x47, 0x55, 0x69)
    
    p_degree = doc.add_paragraph()
    p_degree.alignment = WD_ALIGN_PARAGRAPH.CENTER
    p_degree.paragraph_format.space_after = Pt(25)
    r = p_degree.add_run("MASTER OF COMPUTER APPLICATIONS (INTEGRATED) — 2024–2028")
    r.font.name = 'Calibri'
    r.font.size = Pt(11)
    r.font.bold = True
    r.font.color.rgb = RGBColor(0xB4, 0x53, 0x09) # Amber/Gold
    
    # Title Box (Table with dark blue fill)
    t_box = doc.add_table(rows=1, cols=1)
    t_box.alignment = WD_TABLE_ALIGNMENT.CENTER
    cell_box = t_box.rows[0].cells[0]
    cell_box.width = Inches(6.5)
    set_cell_background(cell_box, "0F172A")
    set_cell_margins(cell_box, top=200, bottom=200, left=200, right=200)
    set_table_borders(t_box, color="0F172A")
    
    p_box = cell_box.paragraphs[0]
    p_box.alignment = WD_ALIGN_PARAGRAPH.CENTER
    r_t1 = p_box.add_run("ELEVE CLOTHING STORE\n")
    r_t1.font.name = 'Calibri'
    r_t1.font.size = Pt(22)
    r_t1.font.bold = True
    r_t1.font.color.rgb = RGBColor(0xE2, 0xC0, 0x8D) # Gold
    
    r_t2 = p_box.add_run("A Full-Stack Luxury Fashion & Apparel E-Commerce Web Platform\nUser Defined Project (UDP) Comprehensive Technical Documentation")
    r_t2.font.name = 'Calibri'
    r_t2.font.size = Pt(10.5)
    r_t2.font.color.rgb = RGBColor(0xCB, 0xD5, 0xE1)
    
    # Spacing
    p_sp = doc.add_paragraph()
    p_sp.paragraph_format.space_before = Pt(25)
    
    # Academic Guidance & Authors Table
    table_meta = doc.add_table(rows=1, cols=2)
    meta_headers = ["Project & Institutional Details", "Student Group & Mentorship"]
    meta_rows = [
        ["Project Title", "ELEVÉ — Luxury Apparel E-Commerce Platform"],
        ["Course / Department", "Integrated MCA / Dept. of Computer Applications"],
        ["Institute", "LJ Institute of Computer Applications (LJICA), LJKU"],
        ["Internal Guide / Mentor", "Dr. Anjum Y. Mansuri, Assistant Professor, Computer Applications, LJ University (GitHub: @anjum-mansuri)"],
        ["Group Members", "1. Harsh Agechaniya (Roll: E-01) — Documentation Lead\n2. Mit Dodiya (Roll: E-10) — Backend Developer\n3. Jaimisha Chunara (Roll: H-11) — Frontend Lead"],
        ["Academic Year", "2024 - 2028"],
        ["Submission Date", "21/08/2026"],
        ["GitHub Repository", "https://github.com/jaimisha15/Eleve-clothing-ecommerce"]
    ]
    format_table(table_meta, [2.3, 4.2], meta_headers, meta_rows)
    
    doc.add_page_break()

    # ==============================================================
    # TABLE OF CONTENTS
    # ==============================================================
    style_heading(doc, "TABLE OF CONTENTS", level=1)
    
    toc_items = [
        ("1. Introduction", "3"),
        ("   1.1 Existing System", "3"),
        ("   1.2 Need for the New System", "3"),
        ("   1.3 Objective of the New System", "4"),
        ("   1.4 Problem Definition & Solution", "4"),
        ("   1.5 Core Components & Modules", "4"),
        ("   1.6 Project Profile", "5"),
        ("   1.7 Assumptions and Constraints", "5"),
        ("   1.8 Advantages and Limitations", "5"),
        ("2. Project Planning", "6"),
        ("   2.1 Team Details and Roles", "6"),
        ("   2.2 Proposed Timeline & Implementation Schedule", "6"),
        ("3. Requirement Determination & Analysis", "7"),
        ("   3.1 Requirement Determination Methodology", "7"),
        ("   3.2 Functional Requirements (FR-01 to FR-07)", "7"),
        ("   3.3 Non-Functional Requirements (NFR-01 to NFR-04)", "8"),
        ("   3.4 Targeted Users & Roles", "8"),
        ("4. System Design", "9"),
        ("   4.1 System Architecture Diagram", "9"),
        ("   4.2 Use Case Diagram & Actor Descriptions", "10"),
        ("   4.3 Class Diagram & Domain Entities", "11"),
        ("   4.4 Interaction / Sequence Diagram", "12"),
        ("   4.5 Activity Diagram (Order Placement Pipeline)", "13"),
        ("   4.6 Comprehensive Data Dictionary (14 Relational Tables)", "14"),
        ("5. Development & Implementation", "20"),
        ("   5.1 Development Approach (Agile Methodology)", "20"),
        ("   5.2 Coding Standards & Architectural Best Practices", "20"),
        ("   5.3 GitHub Repository Details & Version Control Structure", "21"),
        ("   5.4 AI Tools, Libraries and APIs Used", "21"),
        ("   5.5 Implementation Walkthrough & Screen Modules", "22"),
        ("6. Proposed Enhancements", "24"),
        ("7. Conclusion", "25"),
        ("8. Bibliography & References", "26")
    ]
    
    table_toc = doc.add_table(rows=1, cols=2)
    format_table(table_toc, [5.5, 1.0], ["Section / Module", "Page"], toc_items)
    
    doc.add_page_break()

    # ==============================================================
    # 1. INTRODUCTION
    # ==============================================================
    style_heading(doc, "1. INTRODUCTION", level=1)
    add_p(doc, "ELEVÉ is an enterprise-grade, responsive full-stack e-commerce web application engineered specifically for contemporary luxury apparel and high-fashion streetwear. Built using semantic HTML5, modern CSS3, modular Vanilla JavaScript (ES6+), and a secure PHP 8.x PDO backend interfacing with a MySQL relational database, the platform delivers a premium, uninterrupted digital shopping experience across three curated apparel collections: Men, Women, and Kids (with all footwear completely purged).")
    
    add_p(doc, "The platform features real-time client search, an interactive product detail view with multi-angle photography, dynamic size and color swatches, persistent sliding cart and wishlist drawers, and an authenticated administrative suite equipped with sales analytics, product management, and order fulfillment controls.")

    style_heading(doc, "1.1 Existing System", level=2)
    add_p(doc, "In traditional consumer retail and standard multi-purpose e-commerce platforms, clothing shopping is characterized by multiple friction points:")
    add_bullet(doc, "Customers must physically visit retail brick-and-mortar stores, navigate manual garment racks, wait in queue for trial rooms and counter billing, and face inventory uncertainty where desired sizes or colorways may be out of stock.", bold_prefix="1. Physical Retail Shopping: ")
    add_bullet(doc, "Most commercial platforms position action buttons exclusively in the top navigation header. When shoppers scroll through long product pages or catalogs, they must repeatedly scroll all the way back to the top to access their cart or search for items. Furthermore, heavy third-party framework dependencies lead to sluggish loading speeds and cluttered visual designs.", bold_prefix="2. Standard E-Commerce Platforms: ")
    
    add_p(doc, "Key Drawbacks of the Existing System:", bold_prefix="Drawbacks Identified: ")
    add_bullet(doc, "Shopping is restricted by retail opening hours and geographic travel.", bold_prefix="Time and Location Constraints: ")
    add_bullet(doc, "Top-only navigation causes continuous page jumping on long apparel lists.", bold_prefix="Navigation Friction: ")
    add_bullet(doc, "Essential controls are located outside comfortable thumb reach on mobile screens.", bold_prefix="Poor Mobile Ergonomics: ")
    add_bullet(doc, "Generic shopping sites present excessive advertising clutter rather than a focused apparel experience.", bold_prefix="Overcrowded Interfaces: ")
    add_bullet(doc, "Bloated frameworks degrade performance on low-bandwidth networks.", bold_prefix="Performance Overhead: ")

    style_heading(doc, "1.2 Need for the New System", level=2)
    add_p(doc, "The development of ELEVÉ was motivated by the rapid transition toward mobile-first digital lifestyles:")
    add_bullet(doc, "Customers require the flexibility to browse, customize, and buy apparel anytime, from any device.", bold_prefix="24/7 Omnichannel Availability: ")
    add_bullet(doc, "Modern shoppers expect instantaneous live search, category filtering, and real-time size selection.", bold_prefix="Instant Product Discovery: ")
    add_bullet(doc, "Eliminates travel overhead, trial room waiting, and manual cash billing queues.", bold_prefix="Time Efficiency: ")
    add_bullet(doc, "Enables the brand to showcase comprehensive collections without physical shelf-space limits.", bold_prefix="Broader Reach & Scalability: ")

    style_heading(doc, "1.3 Objective of the New System", level=2)
    add_bullet(doc, "Provide a clean, elegant dark-mode luxury theme with gold typography, responsive grids, and subtle micro-interactions.", bold_prefix="1. Refined User Experience: ")
    add_bullet(doc, "Curate all products into Men, Women, and Kids (29 verified products) while strictly excluding non-apparel items.", bold_prefix="2. Structured Categorization: ")
    add_bullet(doc, "Provide live client-side search, a sliding cart drawer, a wishlist drawer, and an authenticated customer modal.", bold_prefix="3. Ergonomic Shopping Utilities: ")
    add_bullet(doc, "Build on local Apache/MySQL architecture with secure PDO prepared statements, input sanitization, and session state persistence.", bold_prefix="4. Robust Backend & Database: ")
    add_bullet(doc, "Equip store managers with real-time revenue analytics, order status updates, and product catalog controls.", bold_prefix="5. Centralized Administration: ")

    style_heading(doc, "1.4 Problem Definition & Solution", level=2)
    add_p(doc, "In conventional apparel commerce, store owners face escalating operational expenses (commercial rent, utility bills, floor staffing) while struggling to track consumer demand patterns accurately. Customers simultaneously struggle with rigid retail hours and slow product comparisons.")
    add_p(doc, "ELEVÉ solves these problems by providing an all-in-one digital fashion platform that is open 24/7, responds instantaneously, records transactions securely into a MySQL database, and allows administrators to oversee orders, stock counts, and customer inquiries with zero external software licensing fees.", bold_prefix="The Solution: ")

    style_heading(doc, "1.5 Core Components & Modules", level=2)
    add_bullet(doc, "Brand logo, live search bar, dedicated customer LOGIN / REGISTER button, and dedicated Admin button.", bold_prefix="Storefront Header & Navigation: ")
    add_bullet(doc, "Visual floating quick-bar with responsive thumbnails for Women, Men, and Kids.", bold_prefix="Category Quick-Bar: ")
    add_bullet(doc, "Hero banner, brand philosophy ('Define Your Elegance'), new arrivals, and customer testimonials.", bold_prefix="Home Page (index.html): ")
    add_bullet(doc, "Curated collections with category filter chips (T-shirts, Jeans, Co-Ord Sets).", bold_prefix="Department Portals (men.html, women.html, kids.html): ")
    add_bullet(doc, "Complete gallery with price sorting (low-high, high-low), badge filtering, and search highlights.", bold_prefix="Products Catalog (products.html): ")
    add_bullet(doc, "Multi-angle photo gallery, size pills (XS–XXL, Kids age tags), color swatches, stock counter, and fabric specs.", bold_prefix="Product Detail (product-detail.html): ")
    add_bullet(doc, "Asynchronous slide-out sidebars with quantity controls, free shipping thresholds, and coupon validation.", bold_prefix="Cart & Wishlist Drawers: ")
    add_bullet(doc, "Shipping address entry, multi-payment options (COD, Card, UPI, Net Banking), and automated order dispatch.", bold_prefix="Checkout System (checkout.html): ")
    add_bullet(doc, "Authenticated suite with revenue metrics, order status workflows, single-click order deletion, and inventory CRUD.", bold_prefix="Admin Control Center (admin/index.php): ")

    style_heading(doc, "1.6 Project Profile", level=2)
    t_prof = doc.add_table(rows=1, cols=2)
    prof_rows = [
        ["Project Title", "ELEVÉ — Luxury Apparel E-Commerce Web Platform"],
        ["Course / Degree", "Master of Computer Applications (Integrated) — MCA (Integrated)"],
        ["Institute / University", "LJ Institute of Computer Applications, Lok Jagruti Kendra University (LJKU)"],
        ["Internal Guide / Mentor", "Dr. Anjum Y. Mansuri, Assistant Professor, Computer Applications, LJ University (GitHub: @anjum-mansuri)"],
        ["Team Members", "Harsh Agechaniya (E-01), Mit Dodiya (E-10), Jaimisha Chunara (H-11)"],
        ["Development Platform", "HTML5, CSS3, JavaScript (ES6+), PHP 8.x PDO Driver"],
        ["Database Management System", "MySQL / MariaDB (utf8mb4_unicode_ci) via XAMPP"],
        ["Application Server", "Apache HTTP Server (XAMPP for Windows)"],
        ["Total Garment Products", "29 Verified Apparel Products (Men: 11, Women: 10, Kids: 8 — 0 footwear)"],
        ["Primary Admin Credentials", "Email: admin@gmail.com | Password: 123"],
        ["Project Duration", "16 Weeks (Agile Sprint Lifecycle)"],
        ["Target Audience", "Online fashion consumers seeking premium modern apparel"]
    ]
    format_table(t_prof, [2.5, 4.0], ["Project Attribute", "Specification Details"], prof_rows)

    style_heading(doc, "1.7 Assumptions and Constraints", level=2)
    add_p(doc, "The user possesses a web-enabled device (smartphone, tablet, laptop) running modern browsers (Google Chrome, Microsoft Edge, Mozilla Firefox, Safari) with JavaScript enabled. The local deployment environment runs XAMPP with Apache and MySQL active.", bold_prefix="Assumptions: ")
    add_p(doc, "Payment processing at checkout is simulated with full validation logic for academic evaluation purposes. Production multi-region deployments require HTTPS certificates for secure cookie transmission.", bold_prefix="Constraints: ")

    style_heading(doc, "1.8 Advantages and Limitations", level=2)
    add_bullet(doc, "Zero third-party runtime framework dependencies (No React/Angular/Vue); near-instant rendering.", bold_prefix="High Performance: ")
    add_bullet(doc, "Luxury dark theme with gold typography (#c9a96e), glassmorphic drawers, and responsive typography.", bold_prefix="Luxury UI/UX: ")
    add_bullet(doc, "Persistent fixed navigation allows instant access to Cart, Wishlist, and Search from any scroll position.", bold_prefix="Ergonomic Navigation: ")
    add_bullet(doc, "Database seeder script (seed_db.php) populates all 29 products, 7 categories, and realistic sample orders automatically.", bold_prefix="Single-Click Setup: ")
    add_bullet(doc, "Multi-warehouse stock allocation is not implemented in the current single-store release.", bold_prefix="Limitation 1: ")
    add_bullet(doc, "Automated courier tracking API webhooks (e.g. Shiprocket) require live commercial merchant keys.", bold_prefix="Limitation 2: ")

    doc.add_page_break()

    # ==============================================================
    # 2. PROJECT PLANNING
    # ==============================================================
    style_heading(doc, "2. PROJECT PLANNING", level=1)
    
    style_heading(doc, "2.1 Team Details and Roles", level=2)
    t_team = doc.add_table(rows=1, cols=3)
    team_rows = [
        ["Harsh Agechaniya\n(Roll No: E-01)", "Documentation Lead &\nQA Analyst", "System requirements specification, UML diagram compilation, user-flow test case authoring, and documentation validation."],
        ["Mit Dodiya\n(Roll No: E-10)", "Backend Developer &\nDatabase Designer", "Relational MySQL schema design, PDO secure connection layer, session management, REST API endpoints, and order processing."],
        ["Jaimisha Chunara\n(Roll No: H-11)", "Frontend Developer &\nUI/UX Designer", "Luxury storefront design system, responsive layouts across 9 HTML views, client state logic (app.js), cart synchronization, and admin UI."]
    ]
    format_table(t_team, [1.8, 1.8, 2.9], ["Team Member", "Assigned Role", "Core Responsibilities"], team_rows)

    style_heading(doc, "2.2 Proposed Timeline (Gantt Chart & Sprint Schedule)", level=2)
    add_p(doc, "The project followed an intensive 16-week timeline divided into 8 distinct Agile sprints:")
    
    t_sprints = doc.add_table(rows=1, cols=4)
    sprint_rows = [
        ["Sprint 1", "Topic Approval, Stakeholder Interviews & Inception", "Weeks 1–2 (01 Mar – 14 Mar 2026)", "Harsh Agechaniya"],
        ["Sprint 2", "Requirement Gathering & UML System Modeling", "Weeks 3–4 (15 Mar – 28 Mar 2026)", "Harsh / Jaimisha"],
        ["Sprint 3", "MySQL Relational Schema Design & database.sql", "Weeks 5–6 (29 Mar – 11 Apr 2026)", "Mit Dodiya"],
        ["Sprint 4", "Storefront Frontend Development (HTML5 & CSS3)", "Weeks 7–9 (12 Apr – 02 May 2026)", "Jaimisha Chunara"],
        ["Sprint 5", "Backend PHP PDO Architecture & REST APIs", "Weeks 10–12 (03 May – 23 May 2026)", "Mit Dodiya"],
        ["Sprint 6", "Admin Control Center & Order Fulfillment Suite", "Weeks 13–14 (24 May – 13 Jun 2026)", "Mit / Jaimisha"],
        ["Sprint 7", "29 Garments Integration & Purging Footwear", "Weeks 15–16 (14 Jun – 11 Jul 2026)", "Full Team"],
        ["Sprint 8", "System Testing, XAMPP Sync & Documentation", "Weeks 17–18 (12 Jul – 21 Aug 2026)", "Harsh Agechaniya"]
    ]
    format_table(t_sprints, [1.0, 2.5, 1.8, 1.2], ["Sprint", "Milestone / Deliverable", "Timeline", "Lead"], sprint_rows)

    # Embed Gantt Chart Image
    gantt_path = "doc_assets/gantt_chart.png"
    if os.path.exists(gantt_path):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(12)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture(gantt_path, width=Inches(6.2))
        add_p(doc, "Fig 2.1: Project Implementation Gantt Chart (Weeks 1 to 16)", bold_prefix="Figure 2.1: ", space_after=12).alignment = WD_ALIGN_PARAGRAPH.CENTER

    doc.add_page_break()

    # ==============================================================
    # 3. REQUIREMENT DETERMINATION & ANALYSIS
    # ==============================================================
    style_heading(doc, "3. REQUIREMENT DETERMINATION & ANALYSIS", level=1)
    
    style_heading(doc, "3.1 Requirement Determination Methodology", level=2)
    add_p(doc, "Requirement determination is the systematic process of gathering, categorizing, and documenting the exact operational and technical expectations of customers, retail managers, and academic evaluators. For ELEVÉ, requirements were gathered using four proven techniques:")
    add_bullet(doc, "Conducted one-on-one structured discussions with urban fashion consumers and retail boutique owners regarding pain points in physical retail, checkout barriers, and mobile shopping preferences.", bold_prefix="1. Stakeholder Interviews: ")
    add_bullet(doc, "Distributed digital surveys to young consumers (ages 18–35) to quantify the most desired e-commerce capabilities (instant search, size availability guides, and wishlist saving).", bold_prefix="2. Consumer Questionnaires: ")
    add_bullet(doc, "Benchmarked leading fashion platforms (Zara, ASOS, Max Fashion) to extract best practices in category layout, product detail anatomy, and sliding cart behaviors.", bold_prefix="3. Competitive Benchmarking: ")
    add_bullet(doc, "Mapped the physical store experience into an automated digital equivalent with real-time tax calculation, coupon deduction, and immediate order receipts.", bold_prefix="4. Workflow Observation: ")

    style_heading(doc, "3.2 Functional Requirements", level=2)
    add_bullet(doc, "Provide secure user registration, bcrypt password hashing, and role-based login differentiating regular shoppers ('user') from administrators ('admin').", bold_prefix="FR-01 (Authentication & Access Control): ")
    add_bullet(doc, "Organize inventory strictly into Men, Women, and Kids, with subcategory filters (T-shirts, Jeans, Co-Ord Sets) and badge filters (Best Sellers, New Arrivals, Sale).", bold_prefix="FR-02 (Catalog Browsing & Categorization): ")
    add_bullet(doc, "Provide an inline search bar that instantly matches product titles, subcategories, and tags with zero page reloads.", bold_prefix="FR-03 (Live Search Engine): ")
    add_bullet(doc, "Display multi-angle photography, real-time size selector (XS, S, M, L, XL, XXL, and Kids ages), color swatches, stock availability, and fabric specs.", bold_prefix="FR-04 (Product Customization): ")
    add_bullet(doc, "Slide-out side drawers supporting live quantity modification, item removal, free shipping thresholds, and coupon validation (WELCOME10, FLAT200, SAVE15).", bold_prefix="FR-05 (Cart & Wishlist Drawers): ")
    add_bullet(doc, "Collect delivery details, validate 6-digit postal codes, support multi-payment options (COD, Card, UPI, Net Banking), and store transactions atomically in MySQL.", bold_prefix="FR-06 (Checkout & Order Generation): ")
    add_bullet(doc, "Provide a password-protected dashboard displaying revenue totals, order counts, status transitions (Confirmed, Shipped, Delivered), and single-click order deletion.", bold_prefix="FR-07 (Admin Control Center): ")

    style_heading(doc, "3.3 Non-Functional Requirements", level=2)
    add_bullet(doc, "Catalog filtering and search queries execute within 150 milliseconds; initial page load times under 1.5 seconds.", bold_prefix="NFR-01 (Performance & Latency): ")
    add_bullet(doc, "Flawless responsive adaptation across mobile smartphones (360px+), tablets (768px+), laptops, and desktop monitors.", bold_prefix="NFR-02 (Responsive Ergonomics): ")
    add_bullet(doc, "Strict input sanitization via htmlspecialchars(), email validation filters, and parameterized PDO SQL queries preventing SQLi and XSS attacks.", bold_prefix="NFR-03 (Security & Sanitization): ")
    add_bullet(doc, "Modular separation of concerns across presentation (HTML5/CSS3), client behavior (app.js), and database services (php/config.php).", bold_prefix="NFR-04 (Maintainability & Code Hygiene): ")

    style_heading(doc, "3.4 Targeted Users & Roles", level=2)
    t_users = doc.add_table(rows=1, cols=3)
    user_rows = [
        ["Store Administrator", "System Admin / Store Manager", "Authenticate via admin portal, audit revenues, update order fulfillment states, delete test records, and manage product inventory."],
        ["Registered Shopper", "Authenticated End-User", "Log into personal account, browse catalog, manage wishlist, submit orders, view past order history, and track status."],
        ["Guest Shopper", "Unauthenticated Visitor", "Browse collections, perform live searches, manage local cart, and place orders directly without mandatory account registration."],
        ["Gift / Quick Buyer", "Occasional User", "Quickly find specific apparel items via live search, add directly to cart, and utilize express checkout with promo coupons."]
    ]
    format_table(t_users, [1.6, 1.8, 3.1], ["User Type", "System Role", "Primary Responsibilities & Access Rights"], user_rows)

    doc.add_page_break()

    # ==============================================================
    # 4. SYSTEM DESIGN
    # ==============================================================
    style_heading(doc, "4. SYSTEM DESIGN", level=1)
    
    style_heading(doc, "4.1 System Architecture Diagram", level=2)
    add_p(doc, "ELEVÉ follows a decoupled 3-tier web architecture comprising the Client Presentation Layer, the Application / Web Server Layer, and the Relational Database Layer:")
    
    arch_path = "doc_assets/architecture_diagram.png"
    if os.path.exists(arch_path):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(8)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture(arch_path, width=Inches(6.2))
        add_p(doc, "Fig 4.1: Multi-Tier System Architecture — ELEVÉ Platform", bold_prefix="Figure 4.1: ", space_after=10).alignment = WD_ALIGN_PARAGRAPH.CENTER

    style_heading(doc, "4.2 Use Case Diagram & Actor Descriptions", level=2)
    add_p(doc, "The Use Case diagram visualizes the behavioral interactions between the two primary system actors (Customer and Store Admin) and the core platform functionalities:")

    uc_path = "doc_assets/use_case_diagram.png"
    if os.path.exists(uc_path):
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(8)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture(uc_path, width=Inches(6.0))
        add_p(doc, "Fig 4.2: Use Case Diagram — Customer & Admin Interactions", bold_prefix="Figure 4.2: ", space_after=10).alignment = WD_ALIGN_PARAGRAPH.CENTER

    style_heading(doc, "4.3 Class Diagram & Domain Entities", level=2)
    add_p(doc, "The domain model represents the core object entities, attributes, and relationships governing business logic:")
    add_bullet(doc, "Attributes: id, name, email, password, phone, address, role, is_active. Methods: register(), login(), logout(), updateProfile().", bold_prefix="User Entity: ")
    add_bullet(doc, "Attributes: id, name, category, subcategory, gender, price, old_price, stock_qty, rating. Methods: getDetails(), updateStock(), calculateDiscount().", bold_prefix="Product Entity: ")
    add_bullet(doc, "Attributes: id, order_number, user_id, status, payment_status, total, shipping_name, shipping_address. Methods: placeOrder(), updateStatus(), deleteOrder(), calculateTax().", bold_prefix="Order Entity: ")
    add_bullet(doc, "Attributes: id, order_id, product_id, product_name, size, color, qty, price, total. Methods: calculateLineTotal().", bold_prefix="OrderItem Entity: ")
    add_bullet(doc, "Attributes: id, code, type, value, min_order, is_active. Methods: validate(), applyDiscount().", bold_prefix="Coupon Entity: ")

    style_heading(doc, "4.4 Interaction / Sequence Diagram", level=2)
    add_p(doc, "The sequence of interactions during customer checkout and administrative fulfillment proceeds through five clear stages:")
    add_bullet(doc, "1. Shopper submits shipping address and selects payment method on checkout.html.", bold_prefix="Step 1 (Checkout Dispatch): ")
    add_bullet(doc, "2. checkout.html sends an asynchronous POST request with FormData and cart line items to php/orders.php.", bold_prefix="Step 2 (API Handshake): ")
    add_bullet(doc, "3. php/orders.php opens a database transaction, inserts master record into orders table, and batch-inserts line items into order_items.", bold_prefix="Step 3 (Atomic Insertion): ")
    add_bullet(doc, "4. Database commits transaction; php/orders.php returns JSON confirmation with unique order number (e.g. ELV369A0E25).", bold_prefix="Step 4 (Receipt Generation): ")
    add_bullet(doc, "5. Store Administrator accesses admin/orders.php to review customer name, shipping destination, order total, and update status to 'Shipped' or 'Delivered'.", bold_prefix="Step 5 (Admin Processing): ")

    style_heading(doc, "4.5 Activity Diagram (Order Placement Pipeline)", level=2)
    add_p(doc, "The procedural flow of activities from garment selection to order confirmation follows this validated workflow:")
    add_bullet(doc, "Browse collection by gender (Men, Women, Kids) -> Select garment -> Choose size and colorway -> Add to Cart -> Open Cart Drawer -> Validate promo coupon -> Proceed to Checkout -> Input shipping info -> Validate form fields -> Transmit to server -> Save order in MySQL -> Display success modal -> Deduct inventory.", bold_prefix="Operational Activity Pipeline: ")

    doc.add_page_break()

    # ==============================================================
    # 4.6 DATA DICTIONARY (ALL 14 TABLES)
    # ==============================================================
    style_heading(doc, "4.6 Comprehensive Data Dictionary (14 Relational Tables)", level=2)
    add_p(doc, "The ELEVÉ relational database (eleve_ecommerce) is structured into 14 normalized tables in 3rd Normal Form (3NF) with foreign key cascading:")

    # Table 1: users
    style_heading(doc, "Table 1: users (User Accounts & Administrators)", level=3)
    t_u = doc.add_table(rows=1, cols=5)
    u_rows = [
        ["id", "INT", "PRIMARY KEY, AUTO_INCREMENT", "Unique identifier for user", "1"],
        ["name", "VARCHAR(100)", "NOT NULL", "Full customer or admin name", "Admin / Rahul Sharma"],
        ["email", "VARCHAR(150)", "UNIQUE, NOT NULL", "Login email address", "admin@gmail.com"],
        ["password", "VARCHAR(255)", "NOT NULL", "Bcrypt cryptographic password hash", "$2y$12$NdjdsGvv7T..."],
        ["phone", "VARCHAR(20)", "NULL", "Contact telephone number", "+91 98251 12345"],
        ["address", "TEXT", "NULL", "Default residential delivery address", "A-402, Titanium City"],
        ["role", "ENUM('user','admin')", "DEFAULT 'user'", "Access control authority", "admin"],
        ["is_active", "TINYINT(1)", "DEFAULT 1", "Account enablement flag", "1"],
        ["created_at", "TIMESTAMP", "DEFAULT CURRENT_TIMESTAMP", "Record creation timestamp", "2026-11-20 10:00:00"]
    ]
    format_table(t_u, [1.0, 1.4, 1.8, 1.4, 0.9], ["Column Name", "Data Type", "Constraints", "Description", "Sample Value"], u_rows)

    # Table 2: categories
    style_heading(doc, "Table 2: categories (Apparel Departments)", level=3)
    t_c = doc.add_table(rows=1, cols=5)
    c_rows = [
        ["id", "INT", "PRIMARY KEY, AUTO_INCREMENT", "Unique category identifier", "1"],
        ["name", "VARCHAR(100)", "NOT NULL", "Department display title", "Men's T-Shirts"],
        ["slug", "VARCHAR(100)", "UNIQUE, NOT NULL", "URL identifier", "men-tshirts"],
        ["gender", "ENUM('men','women','kids')", "NOT NULL", "Department target group", "men"],
        ["image_url", "VARCHAR(500)", "NULL", "Category thumbnail image path", "images/men/tshirt-1.jpg"],
        ["is_active", "TINYINT(1)", "DEFAULT 1", "Display toggle", "1"],
        ["sort_order", "INT", "DEFAULT 0", "Navigation sorting priority", "1"]
    ]
    format_table(t_c, [1.0, 1.4, 1.8, 1.4, 0.9], ["Column Name", "Data Type", "Constraints", "Description", "Sample Value"], c_rows)

    # Table 3: products
    style_heading(doc, "Table 3: products (29 Verified Apparel Products)", level=3)
    t_p = doc.add_table(rows=1, cols=5)
    p_rows = [
        ["id", "INT", "PRIMARY KEY, AUTO_INCREMENT", "Unique product identifier", "1"],
        ["name", "VARCHAR(200)", "NOT NULL", "Garment commercial title", "Men's Noir Graphic Tee"],
        ["category", "VARCHAR(100)", "NOT NULL", "Primary apparel type", "T-Shirts"],
        ["subcategory", "VARCHAR(100)", "NULL", "Subcategory classification", "t-shirts"],
        ["gender", "ENUM('men','women','kids')", "NOT NULL", "Apparel target demographic", "men"],
        ["price", "DECIMAL(10,2)", "NOT NULL", "Selling price in INR (₹)", "699.00"],
        ["old_price", "DECIMAL(10,2)", "NULL", "Original price for strikethrough", "899.00"],
        ["badge", "VARCHAR(50)", "NULL", "Merchandising badge label", "Best Seller"],
        ["rating", "DECIMAL(3,2)", "DEFAULT 4.50", "Average customer rating", "4.80"],
        ["stock_qty", "INT", "DEFAULT 100", "Available inventory stock", "85"],
        ["description", "TEXT", "NULL", "Marketing narrative", "Signature streetwear..."],
        ["fit", "TEXT", "NULL", "Fit specification", "Regular streetwear fit"],
        ["details", "TEXT", "NULL", "Pipe-separated fabric specs", "100% cotton|220GSM"],
        ["is_active", "TINYINT(1)", "DEFAULT 1", "Catalog display toggle", "1"]
    ]
    format_table(t_p, [1.0, 1.4, 1.8, 1.4, 0.9], ["Column Name", "Data Type", "Constraints", "Description", "Sample Value"], p_rows)

    # Table 4: orders
    style_heading(doc, "Table 4: orders (Customer Transactions Registry)", level=3)
    t_o = doc.add_table(rows=1, cols=5)
    o_rows = [
        ["id", "INT", "PRIMARY KEY, AUTO_INCREMENT", "Unique order ID", "1"],
        ["order_number", "VARCHAR(20)", "UNIQUE, NOT NULL", "Public order tracking serial", "ELV-2026-1001"],
        ["user_id", "INT", "FOREIGN KEY (users.id) NULL", "Associated account ID if logged in", "NULL"],
        ["status", "ENUM('pending','confirmed','processing','shipped','delivered','cancelled')", "DEFAULT 'pending'", "Order fulfillment lifecycle state", "delivered"],
        ["payment_status", "ENUM('pending','paid','failed','refunded')", "DEFAULT 'pending'", "Payment settlement status", "paid"],
        ["payment_method", "VARCHAR(50)", "DEFAULT 'cod'", "Payment method used", "card / upi / cod"],
        ["subtotal", "DECIMAL(10,2)", "NOT NULL", "Sum of line items before promo", "2098.00"],
        ["discount", "DECIMAL(10,2)", "DEFAULT 0.00", "Promo coupon deduction in ₹", "200.00"],
        ["total", "DECIMAL(10,2)", "NOT NULL", "Net payable order total in ₹", "1898.00"],
        ["shipping_name", "VARCHAR(150)", "NOT NULL", "Customer recipient name", "Rahul Sharma"],
        ["shipping_email", "VARCHAR(150)", "NOT NULL", "Customer notification email", "rahul.sharma@example.com"],
        ["shipping_phone", "VARCHAR(20)", "NOT NULL", "Contact phone number", "+91 98251 12345"],
        ["shipping_address", "TEXT", "NOT NULL", "Premises delivery address", "A-402, Titanium City"],
        ["shipping_city", "VARCHAR(100)", "NOT NULL", "Destination city", "Ahmedabad"],
        ["shipping_pincode", "VARCHAR(10)", "NOT NULL", "Postal PIN code", "380015"],
        ["created_at", "TIMESTAMP", "DEFAULT CURRENT_TIMESTAMP", "Order submission time", "2026-11-20 10:15:00"]
    ]
    format_table(t_o, [1.0, 1.4, 1.8, 1.4, 0.9], ["Column Name", "Data Type", "Constraints", "Description", "Sample Value"], o_rows)

    # Table 5: order_items
    style_heading(doc, "Table 5: order_items (Order Line Items)", level=3)
    t_oi = doc.add_table(rows=1, cols=5)
    oi_rows = [
        ["id", "INT", "PRIMARY KEY, AUTO_INCREMENT", "Line item identifier", "1"],
        ["order_id", "INT", "FOREIGN KEY (orders.id) CASCADE", "Parent order reference", "1"],
        ["product_id", "INT", "FOREIGN KEY (products.id)", "Garment reference", "1"],
        ["product_name", "VARCHAR(200)", "NOT NULL", "Snapshot of garment name", "Men's Noir Graphic Tee"],
        ["product_image", "VARCHAR(500)", "NULL", "Snapshot of image thumbnail", "images/men/tshirt-1.jpg"],
        ["size", "VARCHAR(20)", "NOT NULL", "Selected garment size", "L"],
        ["color", "VARCHAR(50)", "NULL", "Selected garment color", "Black"],
        ["qty", "INT", "NOT NULL", "Quantity purchased", "1"],
        ["price", "DECIMAL(10,2)", "NOT NULL", "Unit price in ₹", "699.00"],
        ["total", "DECIMAL(10,2)", "NOT NULL", "Line total (price × qty)", "699.00"]
    ]
    format_table(t_oi, [1.0, 1.4, 1.8, 1.4, 0.9], ["Column Name", "Data Type", "Constraints", "Description", "Sample Value"], oi_rows)

    # Tables 6 to 14 Summary Table
    style_heading(doc, "Tables 6 to 14: Auxiliary Platform Data Stores", level=3)
    t_aux = doc.add_table(rows=1, cols=4)
    aux_rows = [
        ["product_images", "id, product_id, url, sort_order", "FOREIGN KEY (products.id)", "Stores multi-angle garment photography paths"],
        ["product_sizes", "id, product_id, size", "FOREIGN KEY (products.id)", "Stores available garment size options (S, M, L, XL, 32, 7-8Y)"],
        ["product_colors", "id, product_id, name, hex", "FOREIGN KEY (products.id)", "Stores garment color swatches and hexadecimal values (#1a1a1a)"],
        ["cart_items", "id, session_id, user_id, product_id, qty", "FOREIGN KEY (products.id)", "Stores server-side session shopping cart data"],
        ["wishlist_items", "id, session_id, user_id, product_id", "FOREIGN KEY (products.id)", "Stores bookmarked customer favorites across visits"],
        ["coupons", "id, code, type, value, min_order, is_active", "UNIQUE(code)", "Stores discount promo codes (WELCOME10, FLAT200, SAVE15)"],
        ["reviews", "id, product_id, name, rating, comment, is_approved", "FOREIGN KEY (products.id)", "Stores customer product reviews and star ratings (1 to 5)"],
        ["newsletter_subscribers", "id, email, is_active, subscribed_at", "UNIQUE(email)", "Stores email subscriptions for fashion release alerts"],
        ["contact_messages", "id, name, email, subject, message, status", "ENUM('new','read','replied')", "Stores customer support messages submitted via contact.html"],
        ["settings", "id, setting_key, setting_value", "UNIQUE(setting_key)", "Stores global configuration values (site_name, free_shipping_min)"]
    ]
    format_table(t_aux, [1.5, 2.0, 1.5, 1.5], ["Table Name", "Key Column Attributes", "Primary Constraints", "Functional Purpose"], aux_rows)

    doc.add_page_break()

    # ==============================================================
    # 5. DEVELOPMENT & IMPLEMENTATION
    # ==============================================================
    style_heading(doc, "5. DEVELOPMENT & IMPLEMENTATION", level=1)
    
    style_heading(doc, "5.1 Development Approach (Agile Methodology)", level=2)
    add_p(doc, "The team implemented an iterative Agile Scrum methodology structured into 2-week sprint cycles. Daily stand-ups allowed team members to coordinate UI component styling, database migration scripts, and REST controller endpoints. Version control via Git ensured clean feature branches were reviewed and tested before merging into the main deployment branch.")

    style_heading(doc, "5.2 Coding Standards & Architectural Best Practices", level=2)
    add_p(doc, "The codebase follows strict industry conventions to ensure maintainability, security, and performance:")
    add_bullet(doc, "Classes use PascalCase (e.g. OrderController); JavaScript functions and variables use camelCase (e.g. placeOrder, cartItems); constants use UPPER_CASE (e.g. SITE_URL); database tables use lowercase snake_case (e.g. order_items).", bold_prefix="Naming Conventions: ")
    add_bullet(doc, "Strict adherence to HTML5 semantic tags (<header>, <main>, <section>, <article>, <aside>, <footer>) for screen reader accessibility and SEO optimization.", bold_prefix="Frontend Standards: ")
    add_bullet(doc, "All database queries execute through PHP Data Objects (PDO) with parameterized prepared statements, completely mitigating SQL Injection risks.", bold_prefix="Backend Security: ")
    add_bullet(doc, "All dynamic user inputs rendered onto HTML views pass through htmlspecialchars() with UTF-8 encoding to prevent Cross-Site Scripting (XSS).", bold_prefix="Input Sanitization: ")

    style_heading(doc, "5.3 GitHub Repository Details & Version Control Structure", level=2)
    t_git = doc.add_table(rows=1, cols=2)
    git_rows = [
        ["Repository URL", "https://github.com/jaimisha15/Eleve-clothing-ecommerce"],
        ["Primary Branch", "main (production deployment branch)"],
        ["Version Control System", "Git 2.40+ with core.longpaths enabled for Windows path stability"],
        ["Commit '066cb36'", "Organize reference images for Men, Women, Kids; purge footwear"],
        ["Commit '2e9803b'", "Fix cart query selector errors and add safe component initialization"],
        ["Commit 'd8c28f0'", "Auto-redirect admin navigation, populate 29 apparel items and 6 sample orders"],
        ["Commit '8fd4006'", "Fix customer name rendering in admin orders, add order deletion, patch checkout CORS"],
        ["Commit 'c29303a'", "Enforce strict separation between customer login button and admin panel button in navbar"],
        ["Commit '5722b4e'", "Add comprehensive academic project documentation (MD and printable HTML)"]
    ]
    format_table(t_git, [2.2, 4.3], ["Repository Property", "Configuration & Commit Records"], git_rows)

    style_heading(doc, "5.4 AI Tools, Libraries and APIs Used", level=2)
    add_bullet(doc, "Advanced agentic coding assistant used for automated codebase refactoring, terminal orchestration, script generation, and integration testing.", bold_prefix="Google Antigravity: ")
    add_bullet(doc, "Employed for drafting luxury brand copywriting, product narrative descriptions, and edge-case testing scripts.", bold_prefix="ChatGPT (OpenAI): ")
    add_bullet(doc, "Built-in native PHP database abstraction layer providing robust, prepared statement database transactions.", bold_prefix="PHP Data Objects (PDO): ")
    add_bullet(doc, "Asynchronous JavaScript API used for frictionless order submission and background cart synchronization without page reloads.", bold_prefix="Modern Fetch API: ")
    add_bullet(doc, "Editorial web fonts providing the modern luxury visual identity across storefront and admin views.", bold_prefix="Google Fonts (Inter & Playfair): ")

    doc.add_page_break()

    # ==============================================================
    # 5.5 IMPLEMENTATION DETAILS & SCREENSHOTS
    # ==============================================================
    style_heading(doc, "5.5 Implementation Walkthrough & Screen Modules", level=2)
    
    # Storefront Header & Hero Screenshot
    home_img = "doc_assets/home_storefront.png"
    if os.path.exists(home_img):
        add_p(doc, "1. Storefront Header Bar & Luxury Hero Section:", bold_prefix="Screen Module 1: ")
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(4)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture(home_img, width=Inches(6.2))
        add_p(doc, "Fig 5.1: Storefront Header — Separate '👑 ADMIN' and 'LOGIN / REGISTER' Controls", bold_prefix="Figure 5.1: ", space_after=8).alignment = WD_ALIGN_PARAGRAPH.CENTER
        add_bullet(doc, "Features the brand logo, live search bar, dedicated customer LOGIN / REGISTER button, and dedicated gold Admin button. The visual category bar sits directly below with thumbnails for Women, Men, and Kids.", bold_prefix="Implementation Highlights: ")

    # Admin Orders Management Screenshot
    admin_img = "doc_assets/admin_orders.png"
    if os.path.exists(admin_img):
        add_p(doc, "2. Admin Control Center — Orders Fulfillment & Customer Registry:", bold_prefix="Screen Module 2: ")
        p_img = doc.add_paragraph()
        p_img.alignment = WD_ALIGN_PARAGRAPH.CENTER
        p_img.paragraph_format.space_before = Pt(4)
        p_img.paragraph_format.space_after = Pt(4)
        run_img = p_img.add_run()
        run_img.add_picture(admin_img, width=Inches(6.2))
        add_p(doc, "Fig 5.2: Admin Orders Management — Real Customer Names, Status Pills & Single-Click Deletion", bold_prefix="Figure 5.2: ", space_after=8).alignment = WD_ALIGN_PARAGRAPH.CENTER
        add_bullet(doc, "Accurately resolves customer shipping names (Rahul Sharma, Ananya Desai, Jaimisha Patel) rather than 'Guest'. Supports fulfillment status transitions (Confirmed, Shipped, Delivered) and one-click order deletion.", bold_prefix="Implementation Highlights: ")

    add_p(doc, "3. Apparel Department Modules (29 Total Garments):", bold_prefix="Screen Module 3: ")
    add_bullet(doc, "Curated exclusively for men: 7 Graphic and Minimalist T-Shirts (₹599–₹799) and 4 Washed and Distressed Denims (₹1,299–₹1,499). All photos sourced locally from images/men/.", bold_prefix="Men's Collection (men.html — 11 Items): ")
    add_bullet(doc, "Curated for contemporary women: 5 Cropped and Oversized T-Shirts (₹549–₹699) and 5 High-Rise Flare and Wide-Leg Jeans (₹1,249–₹1,599). All photos sourced locally from images/women/.", bold_prefix="Women's Collection (women.html — 10 Items): ")
    add_bullet(doc, "Curated for active youth: 2 Crew Neck Tees & Denim Pants (₹449–₹699) and 6 Coordinated Outfit Sets (₹549–₹799). All photos sourced locally from images/kids/.", bold_prefix="Kids' Collection (kids.html — 8 Items): ")

    doc.add_page_break()

    # ==============================================================
    # 6. PROPOSED ENHANCEMENTS
    # ==============================================================
    style_heading(doc, "6. PROPOSED ENHANCEMENTS", level=1)
    add_p(doc, "While ELEVÉ delivers a fully operational e-commerce platform, the following architectural enhancements are planned for future production versions:")
    add_bullet(doc, "Integrate Razorpay and Stripe merchant webhooks for automated UPI QR code scanning and instant payment callbacks.", bold_prefix="1. Live Payment Gateway Integration: ")
    add_bullet(doc, "Implement an embedded machine-learning chatbot capable of recommending garment sizes based on height/weight and suggesting matching apparel sets.", bold_prefix="2. AI-Powered Virtual Style Advisor: ")
    add_bullet(doc, "Connect backend order dispatches to courier APIs (Shiprocket / Delhivery) to generate live airway bill (AWB) tracking numbers automatically.", bold_prefix="3. Automated Logistics Tracking: ")
    add_bullet(doc, "Register service workers and offline caching manifests enabling shoppers to install ELEVÉ directly onto Android and iOS home screens as a native app.", bold_prefix="4. Progressive Web App (PWA): ")
    add_bullet(doc, "Support real-time currency conversion (USD, EUR, GBP, AED) with multi-language locale switching for global luxury distribution.", bold_prefix="5. Multi-Currency & Internationalization: ")

    # ==============================================================
    # 7. CONCLUSION
    # ==============================================================
    style_heading(doc, "7. CONCLUSION", level=1)
    add_p(doc, "The ELEVÉ clothing store e-commerce project successfully demonstrates the comprehensive design, engineering, and deployment of a full-stack digital commerce platform tailored to the luxury apparel industry. Over the course of the project lifecycle, our team achieved the following deliverables:")
    add_bullet(doc, "Curated an exclusive inventory of 29 real apparel items strictly spanning Men, Women, and Kids, with all footwear purged.", bold_prefix="Focused Apparel Merchandising: ")
    add_bullet(doc, "Integrated semantic frontend interfaces with secure PHP PDO prepared statement controllers and a normalized 14-table MySQL database.", bold_prefix="Robust Full-Stack Pipeline: ")
    add_bullet(doc, "Constructed an administrative suite capable of real-time sales reporting, lifecycle order status transitions, and data governance.", bold_prefix="Unified Store Governance: ")
    add_bullet(doc, "Applied real-world software engineering concepts including Git version management, transaction atomicity, and parameterized SQL security.", bold_prefix="Academic Rigor & Teamwork: ")
    add_p(doc, "The completed application satisfies all curriculum benchmarks for the Master of Computer Applications (Integrated) program at Lok Jagruti Kendra University (LJKU).")

    # ==============================================================
    # 8. BIBLIOGRAPHY & REFERENCES
    # ==============================================================
    style_heading(doc, "8. BIBLIOGRAPHY & REFERENCES", level=1)
    add_bullet(doc, "World Wide Web Consortium, 'HTML5 & CSS3 Recommendation Standards', https://www.w3.org/TR/html52/", bold_prefix="[1] W3C Standards: ")
    add_bullet(doc, "Mozilla Developer Network, 'JavaScript ES6+ Language Specification & DOM API Documentation', https://developer.mozilla.org/", bold_prefix="[2] MDN Web Docs: ")
    add_bullet(doc, "The PHP Group, 'PHP 8.x PDO Manual & Prepared Statement Security', https://www.php.net/manual/en/book.pdo.php", bold_prefix="[3] The PHP Group: ")
    add_bullet(doc, "Oracle Corporation, 'MySQL 8.0 Reference Manual: Relational Schema Design & InnoDB Foreign Key Optimization', https://dev.mysql.com/doc/", bold_prefix="[4] Oracle Corporation: ")
    add_bullet(doc, "Martin, Robert C., 'Clean Code: A Handbook of Agile Software Craftsmanship', Prentice Hall, 2008.", bold_prefix="[5] Martin, Robert C.: ")
    add_bullet(doc, "Project completed under the guidance of Dr. Anjum Y. Mansuri, Assistant Professor, Computer Applications, LJ Institute of Computer Applications, LJ University, Ahmedabad. GitHub: @anjum-mansuri.", bold_prefix="[6] Academic Guidance: ")

    # Save Word Document
    docx_filename = "ELEVE_Clothing_Store_Project_Documentation.docx"
    doc.save(docx_filename)
    print(f"Successfully generated {docx_filename} ({os.path.getsize(docx_filename)} bytes)")

if __name__ == '__main__':
    build_document()
