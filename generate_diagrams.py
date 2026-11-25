import os
import matplotlib
matplotlib.use('Agg')
import matplotlib.pyplot as plt
import matplotlib.patches as patches
import numpy as np

os.makedirs('doc_assets', exist_ok=True)

# 1. Generate Gantt Chart Image
def create_gantt_chart():
    fig, ax = plt.subplots(figsize=(10, 5), dpi=200)
    
    tasks = [
        "1. Topic Approval & Inception",
        "2. Requirement Gathering & Analysis",
        "3. System Design & UML Modeling",
        "4. Relational Database Implementation",
        "5. Storefront Frontend Development",
        "6. Backend PHP PDO & API Services",
        "7. Admin Control Center & Fulfillment",
        "8. Integration, QA & Final Documentation"
    ]
    
    start_weeks = [0, 2, 4, 6, 8, 10, 12, 14]
    durations   = [2, 2, 2, 2, 3, 3, 2, 2]
    colors      = ['#1E3A8A', '#2563EB', '#0D9488', '#059669', '#D97706', '#DC2626', '#7C3AED', '#4F46E5']
    
    y_pos = np.arange(len(tasks))
    ax.barh(y_pos, durations, left=start_weeks, align='center', color=colors, alpha=0.9, height=0.55, edgecolor='#111827')
    
    ax.set_yticks(y_pos)
    ax.set_yticklabels(tasks, fontsize=9, fontweight='bold', color='#1F2937')
    ax.invert_yaxis()
    
    ax.set_xlabel('Project Weeks (March 2026 - August 2026)', fontsize=10, fontweight='bold', color='#111827', labelpad=10)
    ax.set_xlim(0, 16)
    
    week_labels = [f"W{i+1}\nMar-Aug" if i%2==0 else f"W{i+1}" for i in range(16)]
    ax.set_xticks(range(16))
    ax.set_xticklabels(week_labels, fontsize=8)
    
    ax.grid(axis='x', linestyle='--', alpha=0.5)
    ax.set_title('ELEVÉ E-COMMERCE — PROJECT IMPLEMENTATION GANTT CHART', fontsize=12, fontweight='bold', pad=15, color='#1E3A8A')
    
    plt.tight_layout()
    plt.savefig('doc_assets/gantt_chart.png', bbox_inches='tight')
    plt.close()
    print("Created doc_assets/gantt_chart.png")

# 2. Generate Use Case Diagram Image
def create_use_case_diagram():
    fig, ax = plt.subplots(figsize=(10, 6.5), dpi=200)
    ax.set_xlim(0, 10)
    ax.set_ylim(0, 8)
    ax.axis('off')
    
    # Boundary Box
    system_box = patches.FancyBboxPatch((2.2, 0.4), 5.6, 7.2, boxstyle="round,pad=0.2", ec="#1E3A8A", fc="#F8FAFC", lw=2)
    ax.add_patch(system_box)
    ax.text(5.0, 7.3, "ELEVÉ E-Commerce Platform Boundary", fontsize=11, fontweight='bold', ha='center', color='#1E3A8A')
    
    # Use cases
    use_cases = [
        "Browse Catalog (Men, Women, Kids)",
        "Live Search & Filter Garments",
        "View Product Details & Size Options",
        "Manage Cart & Wishlist Drawers",
        "Submit Checkout & Generate Order",
        "Customer Login / Register Account",
        "Admin Login & Revenue Analytics",
        "Manage Orders & Update Fulfillment Status",
        "Delete Test Orders / Stale Records",
        "Product & Category Inventory CRUD"
    ]
    
    y_coords = [6.5, 5.8, 5.1, 4.4, 3.7, 3.0, 2.3, 1.6, 1.0, 0.5]
    
    for i, uc in enumerate(use_cases):
        yc = y_coords[i]
        color = "#C9A96E" if i < 6 else "#93C5FD"
        ellipse = patches.FancyBboxPatch((2.5, yc - 0.25), 5.0, 0.5, boxstyle="round,pad=0.1", ec="#334155", fc=color, alpha=0.25, lw=1.2)
        ax.add_patch(ellipse)
        ax.text(5.0, yc, uc, fontsize=8.5, fontweight='bold', ha='center', va='center', color='#0F172A')
    
    # Actors
    # Customer (Left)
    ax.text(0.8, 5.0, "👤", fontsize=32, ha='center', va='center')
    ax.text(0.8, 4.2, "CUSTOMER\n(Shopper / Guest)", fontsize=9, fontweight='bold', ha='center', color='#1E3A8A')
    
    # Lines from Customer to use cases 0..5
    for i in range(6):
        ax.annotate("", xy=(2.5, y_coords[i]), xytext=(1.4, 4.6), arrowprops=dict(arrowstyle="-", color="#64748B", lw=1.2))
        
    # Admin (Right)
    ax.text(9.2, 2.0, "👑", fontsize=30, ha='center', va='center')
    ax.text(9.2, 1.2, "STORE ADMIN\n(System Manager)", fontsize=9, fontweight='bold', ha='center', color='#B45309')
    
    # Lines from Admin to use cases 6..9
    for i in range(6, 10):
        ax.annotate("", xy=(7.5, y_coords[i]), xytext=(8.6, 1.6), arrowprops=dict(arrowstyle="-", color="#B45309", lw=1.2))
    
    ax.set_title('FIG 1: USE CASE DIAGRAM — ELEVÉ PLATFORM', fontsize=12, fontweight='bold', pad=15, color='#1E3A8A')
    
    plt.tight_layout()
    plt.savefig('doc_assets/use_case_diagram.png', bbox_inches='tight')
    plt.close()
    print("Created doc_assets/use_case_diagram.png")

# 3. Generate Architecture Diagram Image
def create_architecture_diagram():
    fig, ax = plt.subplots(figsize=(10, 4.5), dpi=200)
    ax.set_xlim(0, 10)
    ax.set_ylim(0, 5)
    ax.axis('off')
    
    # 3 Layers: Client, App Server, Database
    # Client Box
    box_client = patches.FancyBboxPatch((0.5, 0.8), 2.5, 3.4, boxstyle="round,pad=0.2", ec="#2563EB", fc="#EFF6FF", lw=1.8)
    ax.add_patch(box_client)
    ax.text(1.75, 3.8, "CLIENT LAYER", fontsize=10, fontweight='bold', ha='center', color='#1E40AF')
    ax.text(1.75, 3.2, "• Responsive HTML5\n• Luxury CSS3 Theme\n• Vanilla ES6+ JS\n• LocalStorage Cart\n• Async Fetch API", fontsize=8.5, ha='center', va='center', color='#1E293B', linespacing=1.6)
    
    # App Server Box
    box_server = patches.FancyBboxPatch((3.75, 0.8), 2.5, 3.4, boxstyle="round,pad=0.2", ec="#D97706", fc="#FFFBEB", lw=1.8)
    ax.add_patch(box_server)
    ax.text(5.0, 3.8, "APPLICATION SERVER", fontsize=10, fontweight='bold', ha='center', color='#92400E')
    ax.text(5.0, 3.2, "• Apache (XAMPP)\n• PHP 8.x PDO Driver\n• php/orders.php\n• php/cart.php\n• php/auth.php\n• admin/ Control Suite", fontsize=8.5, ha='center', va='center', color='#1E293B', linespacing=1.6)
    
    # Database Box
    box_db = patches.FancyBboxPatch((7.0, 0.8), 2.5, 3.4, boxstyle="round,pad=0.2", ec="#059669", fc="#ECFDF5", lw=1.8)
    ax.add_patch(box_db)
    ax.text(8.25, 3.8, "DATABASE LAYER", fontsize=10, fontweight='bold', ha='center', color='#065F46')
    ax.text(8.25, 3.2, "• MySQL / MariaDB\n• Database: eleve_ecommerce\n• 14 Normalized Tables\n• Prepared SQL Engine\n• InnoDB Foreign Keys\n• Seed Migrations", fontsize=8.5, ha='center', va='center', color='#1E293B', linespacing=1.6)
    
    # Arrows
    ax.annotate("", xy=(3.65, 2.5), xytext=(3.1, 2.5), arrowprops=dict(arrowstyle="<->", color="#1E3A8A", lw=2))
    ax.text(3.375, 2.8, "JSON / HTTP", fontsize=7.5, fontweight='bold', ha='center', color='#1E3A8A')
    
    ax.annotate("", xy=(6.9, 2.5), xytext=(6.35, 2.5), arrowprops=dict(arrowstyle="<->", color="#059669", lw=2))
    ax.text(6.625, 2.8, "PDO SQL", fontsize=7.5, fontweight='bold', ha='center', color='#059669')
    
    ax.set_title('FIG 2: 3-TIER SYSTEM ARCHITECTURE — ELEVÉ PLATFORM', fontsize=12, fontweight='bold', pad=15, color='#1E3A8A')
    
    plt.tight_layout()
    plt.savefig('doc_assets/architecture_diagram.png', bbox_inches='tight')
    plt.close()
    print("Created doc_assets/architecture_diagram.png")

create_gantt_chart()
create_use_case_diagram()
create_architecture_diagram()
