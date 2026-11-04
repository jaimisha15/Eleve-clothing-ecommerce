/* ============================================================
   Siya Clothing — Core Application JavaScript
   ============================================================
   Features:
   - Page loader with timeout
   - Sticky header with blur-on-scroll
   - Mobile navigation hamburger
   - 3D card tilt (rAF-throttled)
   - Product filtering via data-* attributes
   - Image gallery with lightbox
   - Cart with localStorage persistence + focus trap
   - Scroll reveal animations (IntersectionObserver)
   - Form validation (newsletter + contact)
   - Toast notifications
   - Skeleton loader removal
   - Accordion toggle
   - Quantity selector
   ============================================================ */

; (function () {
  'use strict';

  /* ---------- Utility Helpers ---------- */
  const $ = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => [...ctx.querySelectorAll(sel)];

  function productImg(url, width = 800) {
    if (!url) return 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=800&q=80&fit=crop';
    if (url.includes('unsplash.com')) {
      return url.replace(/w=\d+/, 'w=' + width);
    }
    return url;
  }

  /* ---------- Page Loader ---------- */
  function initLoader() {
    const loader = $('#pageLoader');
    if (!loader) return;
    const hide = () => {
      loader.classList.add('hidden');
      setTimeout(() => { loader.style.display = 'none'; }, 600);
    };
    if (document.readyState === 'complete') { hide(); }
    else { window.addEventListener('load', hide); }
    // Safety fallback
    setTimeout(hide, 3500);
  }

  /* ---------- Sticky Header ---------- */
  function initHeader() {
    const header = $('#header');
    if (!header) return;
    let ticking = false;
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          header.classList.toggle('scrolled', window.scrollY > 40);
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }

  /* ---------- Mobile Nav ---------- */
  function initMobileNav() {
    const hamburger = $('#hamburger');
    const mobileNav = $('#mobileNav');
    if (!hamburger || !mobileNav) return;

    hamburger.addEventListener('click', () => {
      const isOpen = hamburger.classList.toggle('active');
      mobileNav.classList.toggle('open');
      hamburger.setAttribute('aria-expanded', isOpen);
      document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    // Close on link click
    $$('a', mobileNav).forEach(link => {
      link.addEventListener('click', () => {
        hamburger.classList.remove('active');
        mobileNav.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
      });
    });
  }

  /* ---------- 3D Card Tilt (rAF-throttled) ---------- */
  function initTiltCards() {
    const cards = $$('[data-tilt]');
    if (!cards.length) return;

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReducedMotion) return;

    cards.forEach(card => {
      let rafId = null;

      card.addEventListener('mousemove', (e) => {
        if (rafId) return; // throttle
        rafId = requestAnimationFrame(() => {
          const rect = card.getBoundingClientRect();
          const x = e.clientX - rect.left;
          const y = e.clientY - rect.top;
          const centerX = rect.width / 2;
          const centerY = rect.height / 2;
          const rotateX = ((y - centerY) / centerY) * -8;
          const rotateY = ((x - centerX) / centerX) * 8;
          card.style.transform = `perspective(800px) rotateX(${rotateX}deg) rotateY(${rotateY}deg) scale3d(1.02,1.02,1.02)`;
          rafId = null;
        });
      });

      card.addEventListener('mouseleave', () => {
        if (rafId) { cancelAnimationFrame(rafId); rafId = null; }
        card.style.transform = '';
        card.style.transition = 'transform 0.5s cubic-bezier(.25,.46,.45,.94)';
        setTimeout(() => { card.style.transition = ''; }, 500);
      });
    });
  }

  /* ---------- Scroll Reveal ---------- */
  function initScrollReveal() {
    const reveals = $$('.reveal');
    if (!reveals.length) return;

    const observer = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          entry.target.classList.add('visible');
          observer.unobserve(entry.target);
        }
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    reveals.forEach(el => observer.observe(el));
  }

  /* ---------- Cart System (localStorage persistent) ---------- */
  const Cart = {
    KEY: 'elev_cart',

    getItems() {
      try { return JSON.parse(localStorage.getItem(this.KEY)) || []; }
      catch { return []; }
    },

    save(items) {
      localStorage.setItem(this.KEY, JSON.stringify(items));
      this.updateUI();
    },

    addItem(item) {
      if (!item || !item.name) return;
      if (!item.id && typeof PRODUCTS !== 'undefined') {
        const found = PRODUCTS.find(p => p.name === item.name);
        if (found) item.id = found.id;
      }
      const items = this.getItems();
      const existing = items.find(i => i.name === item.name && (i.size || '') === (item.size || '') && (i.color || '') === (item.color || ''));
      if (existing) {
        existing.qty = (parseInt(existing.qty) || 1) + (parseInt(item.qty) || 1);
      } else {
        items.push({
          id: item.id || 0,
          name: item.name,
          price: parseFloat(item.price) || 0,
          img: item.img || 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=150',
          size: item.size || 'M',
          color: item.color || '',
          qty: parseInt(item.qty) || 1
        });
      }
      this.save(items);
      showToast(`${item.name} added to cart!`, 'success');
    },

    removeItem(index) {
      const items = this.getItems();
      items.splice(index, 1);
      this.save(items);
    },

    getTotal() {
      return this.getItems().reduce((sum, i) => sum + ((parseFloat(i.price) || 0) * (parseInt(i.qty) || 1)), 0);
    },

    getCount() {
      return this.getItems().reduce((sum, i) => sum + (parseInt(i.qty) || 1), 0);
    },

    updateUI() {
      const count = this.getCount();
      // Update cart count badges everywhere
      $('.cart-count').forEach(badge => {
        badge.textContent = count;
        badge.classList.toggle('show', count > 0);
      });

      // Update cart totals
      const totalFormatted = `₹${this.getTotal().toLocaleString('en-IN')}`;
      $('#cartTotal, .cart-total .amount').forEach(el => {
        el.textContent = totalFormatted;
      });

      // Update cart items displays
      const containers = $('#cartItems');
      if (!containers.length) return;
      const items = this.getItems();

      containers.forEach(container => {
        if (items.length === 0) {
          container.innerHTML = '<div class="cart-empty" style="text-align:center;padding:2.5rem 1rem;color:var(--clr-text-muted);">Your cart is empty</div>';
          return;
        }

        container.innerHTML = items.map((item, i) => `
          <div class="cart-item" style="display:flex;gap:0.75rem;padding:0.75rem 0;border-bottom:1px solid var(--clr-border);align-items:center;">
            <div class="cart-item-img" style="width:55px;height:65px;flex-shrink:0;border-radius:4px;overflow:hidden;background:#1e1e1e;">
              <img src="${item.img || 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=150'}" alt="${item.name}" style="width:100%;height:100%;object-fit:cover;" loading="lazy">
            </div>
            <div style="flex:1;min-width:0;">
              <div class="cart-item-name" style="font-weight:600;font-size:0.85rem;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">${item.name}</div>
              <div style="font-size:0.75rem;color:var(--clr-text-muted);margin:0.2rem 0;">${item.size ? 'Size: ' + item.size : ''} ${item.color ? '• ' + item.color : ''} × ${item.qty}</div>
              <div class="cart-item-price" style="color:var(--clr-accent);font-weight:700;font-size:0.85rem;">₹${((parseFloat(item.price) || 0) * (parseInt(item.qty) || 1)).toLocaleString('en-IN')}</div>
            </div>
            <button type="button" class="cart-item-remove" data-remove="${i}" aria-label="Remove ${item.name}" style="background:none;border:none;color:#ff6b6b;font-size:1.1rem;cursor:pointer;padding:0.4rem;" title="Remove item">✕</button>
          </div>
        `).join('');

        // Remove buttons
        $('[data-remove]', container).forEach(btn => {
          btn.onclick = () => {
            Cart.removeItem(parseInt(btn.dataset.remove));
          };
        });
      });
    }
  };

  window.Cart = Cart;

  /* ---------- Wishlist Data ---------- */
  const Wishlist = {
    KEY: 'elev_wishlist',

    getItems() {
      try { return JSON.parse(localStorage.getItem(this.KEY)) || []; }
      catch { return []; }
    },

    save(items) {
      localStorage.setItem(this.KEY, JSON.stringify(items));
      this.updateUI();
    },

    addItem(item) {
      const items = this.getItems();
      if (items.find(i => i.name === item.name)) return; // Already exists
      items.push(item);
      this.save(items);
      showToast(`${item.name} added to wishlist!`, 'success');
    },

    removeItem(index) {
      const items = this.getItems();
      const removed = items[index];
      items.splice(index, 1);
      this.save(items);
      if (removed) showToast(`${removed.name} removed from wishlist`, 'success');
    },

    hasItem(name) {
      return this.getItems().some(i => i.name === name);
    },

    getCount() {
      return this.getItems().length;
    },

    updateUI() {
      // Update wishlist count badges
      $$('.wishlist-count').forEach(badge => {
        const count = this.getCount();
        badge.textContent = count;
        badge.classList.toggle('show', count > 0);
      });

      // Update wishlist items display
      const container = $('#wishlistItems');
      if (!container) return;
      const items = this.getItems();

      if (items.length === 0) {
        container.innerHTML = '<div class="cart-empty">Your wishlist is empty</div>';
        return;
      }

      container.innerHTML = items.map((item, i) => `
        <div class="cart-item">
          <div class="cart-item-img"><img src="${item.img}" alt="${item.name}" loading="lazy"></div>
          <div>
            <div class="cart-item-name">${item.name}</div>
            <div class="cart-item-price">₹${item.price.toFixed(0)}</div>
            <div style="display:flex;gap:.5rem;margin-top:.25rem">
              <a href="product-detail.html?id=${item.id}" class="cart-item-remove" style="color:var(--clr-accent)">View</a>
              <button class="cart-item-remove" data-wishlist-remove="${i}" aria-label="Remove ${item.name}">Remove</button>
            </div>
          </div>
        </div>
      `).join('');

      // Remove buttons
      $$('[data-wishlist-remove]', container).forEach(btn => {
        btn.addEventListener('click', () => {
          Wishlist.removeItem(parseInt(btn.dataset.wishlistRemove));
        });
      });
    }
  };

  /* ---------- Cart Flyout ---------- */
  function initCart() {
    const cartBtn = $('#cartBtn');
    const cartClose = $('#cartClose');
    const cartOverlay = $('#cartOverlay');
    const cartFlyout = $('#cartFlyout');
    if (!cartBtn || !cartFlyout) return;

    let focusTrapElements = [];
    let previousFocus = null;

    function openCart() {
      previousFocus = document.activeElement;
      cartFlyout.classList.add('open');
      cartOverlay.classList.add('open');
      cartFlyout.setAttribute('aria-hidden', 'false');
      cartOverlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      Cart.updateUI();

      // Focus trap
      focusTrapElements = $$('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])', cartFlyout);
      if (focusTrapElements.length) focusTrapElements[0].focus();
    }

    function closeCart() {
      cartFlyout.classList.remove('open');
      cartOverlay.classList.remove('open');
      cartFlyout.setAttribute('aria-hidden', 'true');
      cartOverlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      if (previousFocus) previousFocus.focus();
    }

    cartBtn.addEventListener('click', openCart);
    if (cartClose) cartClose.addEventListener('click', closeCart);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCart);

    // Focus trap on Tab
    cartFlyout.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') { closeCart(); return; }
      if (e.key !== 'Tab') return;
      const focusable = $$('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])', cartFlyout);
      if (!focusable.length) return;
      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault(); last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault(); first.focus();
      }
    });

    // Initialize cart display from localStorage
    Cart.updateUI();
  }

  /* ---------- Add to Cart (Product Detail) ---------- */
  function initAddToCart() {
    const btn = $('#addToCartBtn');
    if (!btn) return;

    const qtyMinus = $('#qtyMinus');
    const qtyPlus = $('#qtyPlus');
    const qtyValue = $('#qtyValue');
    let qty = 1;

    if (qtyMinus) {
      qtyMinus.onclick = (e) => {
        e.preventDefault();
        if (qty > 1) { qty--; if (qtyValue) qtyValue.textContent = qty; }
      };
    }
    if (qtyPlus) {
      qtyPlus.onclick = (e) => {
        e.preventDefault();
        if (qty < 10) { qty++; if (qtyValue) qtyValue.textContent = qty; }
      };
    }

    btn.onclick = (e) => {
      e.preventDefault();
      let selectedSize = $('#selectedSize')?.textContent?.trim() || '';
      const selectedColor = $('#selectedColor')?.textContent?.trim() || '';

      // If user hasn't explicitly chosen a size, automatically pick the active or first available size
      if (selectedSize === 'Select a size' || !selectedSize) {
        const activeSizeBtn = $('.size-btn.active') || $('.size-btn:not(.disabled)');
        if (activeSizeBtn) {
          selectedSize = activeSizeBtn.dataset.size || activeSizeBtn.textContent.trim();
          const sizeLabel = $('#selectedSize');
          if (sizeLabel) sizeLabel.textContent = selectedSize;
          activeSizeBtn.classList.add('active');
        } else {
          selectedSize = 'M';
        }
      }

      let pid = parseInt(btn.dataset.id) || 0;
      if (!pid && typeof PRODUCTS !== 'undefined') {
        const found = PRODUCTS.find(p => p.name === btn.dataset.name);
        if (found) pid = found.id;
      }

      Cart.addItem({
        id: pid,
        name: btn.dataset.name || 'Fashion Product',
        price: parseFloat(btn.dataset.price) || 0,
        img: btn.dataset.img || '',
        size: selectedSize,
        color: selectedColor || '',
        qty: qty
      });

      // Visual feedback
      btn.classList.add('added');
      btn.disabled = true;
      setTimeout(() => {
        btn.classList.remove('added');
        btn.disabled = false;
      }, 1200);

      // Automatically open the cart drawer so user sees item!
      const cartBtn = $('#cartBtn');
      if (cartBtn) cartBtn.click();
    };
  }

  /* ---------- Size Selector ---------- */
  function initSizeSelector() {
    const sizeBtns = $$('.size-btn:not(.disabled)');
    const sizeLabel = $('#selectedSize');
    if (!sizeBtns.length) return;

    sizeBtns.forEach(btn => {
      btn.addEventListener('click', () => {
        sizeBtns.forEach(b => { b.classList.remove('active'); b.setAttribute('aria-checked', 'false'); });
        btn.classList.add('active');
        btn.setAttribute('aria-checked', 'true');
        if (sizeLabel) sizeLabel.textContent = btn.dataset.size;
      });
    });
  }

  /* ---------- Color Selector (Product Detail) ---------- */
  function initColorSelector() {
    const swatches = $$('.product-info .color-swatch, .product-detail .color-swatch');
    const colorLabel = $('#selectedColor');
    if (!swatches.length) return;

    swatches.forEach(swatch => {
      swatch.addEventListener('click', () => {
        swatches.forEach(s => s.classList.remove('active'));
        swatch.classList.add('active');
        if (colorLabel && swatch.dataset.color) colorLabel.textContent = swatch.dataset.color;
      });
    });
  }

  /* ---------- Image Gallery ---------- */
  function initGallery() {
    const thumbs = $$('.gallery-thumb');
    const mainImg = $('#mainImage');
    if (!thumbs.length || !mainImg) return;

    // Use dynamic images set by initProductDetail()
    const images = window._productImages || [];

    thumbs.forEach(thumb => {
      thumb.addEventListener('click', () => {
        thumbs.forEach(t => t.classList.remove('active'));
        thumb.classList.add('active');
        mainImg.style.opacity = '0';
        setTimeout(() => {
          mainImg.src = images[parseInt(thumb.dataset.index)];
          mainImg.style.opacity = '1';
        }, 200);
      });
    });

    // Lightbox
    const galleryMain = $('#galleryMain');
    const lightbox = $('#lightbox');
    const lightboxImg = $('#lightboxImg');
    const lightboxClose = $('#lightboxClose');

    if (galleryMain && lightbox && lightboxImg) {
      galleryMain.addEventListener('click', () => {
        lightboxImg.src = mainImg.src;
        lightbox.classList.add('open');
        lightbox.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
        if (lightboxClose) lightboxClose.focus();
      });

      const closeLightbox = () => {
        lightbox.classList.remove('open');
        lightbox.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
      };

      if (lightboxClose) lightboxClose.addEventListener('click', closeLightbox);
      lightbox.addEventListener('click', (e) => {
        if (e.target === lightbox) closeLightbox();
      });
      document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && lightbox.classList.contains('open')) closeLightbox();
      });
    }
  }

  /* ---------- Accordion ---------- */
  function initAccordion() {
    $$('.accordion-header').forEach(header => {
      header.addEventListener('click', () => {
        const item = header.parentElement;
        const isOpen = item.classList.contains('open');
        // Close siblings (optional — remove for independent toggles)
        const siblings = $$('.accordion-item', item.parentElement);
        siblings.forEach(s => {
          s.classList.remove('open');
          const h = $('button', s);
          if (h) h.setAttribute('aria-expanded', 'false');
        });
        if (!isOpen) {
          item.classList.add('open');
          header.setAttribute('aria-expanded', 'true');
        }
      });

      // Keyboard accessibility
      header.addEventListener('keydown', (e) => {
        if (e.key === 'Enter' || e.key === ' ') {
          e.preventDefault();
          header.click();
        }
      });
    });
  }

  /* ---------- Dynamic Product Rendering & Filters ---------- */
  function createProductCard(p) {
    const oldPriceHTML = p.oldPrice ? `<span class="old-price">₹${p.oldPrice.toLocaleString('en-IN')}</span>` : '';
    let badges = [];
    if (p.isNewArrival) badges.push('<span class="product-badge">New</span>');
    else if (p.isBestSeller) badges.push('<span class="product-badge">Best Seller</span>');
    else if (p.badge) badges.push(`<span class="product-badge">${p.badge}</span>`);
    
    // Default image
    const imgUrl = p.images && p.images.length > 0 ? p.images[0] : 'https://images.unsplash.com/photo-1556821840-3a63f95609a7?w=800&q=80&fit=crop';
    
    // Build comma separated sizes for data attribute
    const sizesStr = p.sizes ? p.sizes.join(',') : '';

    return `
      <article class="product-card reveal" data-tilt 
        data-gender="${p.gender || ''}"
        data-category="${p.category || ''}" 
        data-subcategory="${p.subcategory || ''}"
        data-price="${p.price}" 
        data-size="${sizesStr}" 
        data-color="${(p.colors && p.colors[0]) || ''}">
        <div class="product-card-img">
          <img src="${imgUrl}" alt="${p.name}" loading="lazy">
          ${badges.join('')}
          <div class="product-overlay" style="display:flex;gap:0.4rem;flex-wrap:wrap;justify-content:center;padding:0.5rem;">
            <a href="product-detail.html?id=${p.id}" class="btn btn-primary btn-sm">Quick View</a>
            <button type="button" class="btn btn-outline btn-sm btn-quick-add"
                    data-id="${p.id}"
                    data-name="${(p.name || '').replace(/"/g, '&quot;')}"
                    data-price="${p.price}"
                    data-img="${imgUrl}"
                    data-size="${(p.sizes && p.sizes[0]) || 'M'}"
                    data-color="${(p.colors && p.colors[0] && p.colors[0].name) || ''}">
              + Add to Cart
            </button>
          </div>
        </div>
        <div class="product-card-info">
          <div class="product-card-category">${p.category}</div>
          <h3 class="product-card-name">${p.name}</h3>
          <div class="product-card-price">₹${p.price.toLocaleString('en-IN')} ${oldPriceHTML}</div>
          ${p.rating ? `<div class="product-card-rating">★ ${p.rating.toFixed(1)}</div>` : ''}
        </div>
      </article>
    `;
  }

  function initFilters() {
    const productGrid = $('#productGrid');
    const skeletonGrid = $('#skeletonGrid');
    const featuredGrid = $('#featuredGrid'); // For homepage
    
    // Check if we need to render the featured grid on homepage
    if (featuredGrid && typeof PRODUCTS !== 'undefined') {
      const featuredProducts = PRODUCTS.slice(0, 4); // Just take first 4 for now, or filter by isBestSeller
      featuredGrid.innerHTML = featuredProducts.map(createProductCard).join('');
      setTimeout(() => {
        if (skeletonGrid) skeletonGrid.style.display = 'none';
        featuredGrid.style.display = '';
        initTiltCards();
        initScrollReveal();
      }, 1200);
    }

    if (!productGrid) return;
    
    // Read URL filter params
    const urlParams = new URLSearchParams(window.location.search);
    let activeTag = urlParams.get('filter') || ''; // 'new', 'bestseller', 'sale'
    let urlGender = urlParams.get('gender') || '';
    let urlCategory = urlParams.get('category') || '';
    
    // Update page title if tag filter active
    const pageTitle = $('.page-title');
    if (pageTitle) {
      if (activeTag === 'new') pageTitle.textContent = 'New Arrivals';
      else if (activeTag === 'bestseller') pageTitle.textContent = 'Best Sellers';
      else if (activeTag === 'sale') pageTitle.textContent = 'Sale';
      else if (urlGender) pageTitle.textContent = urlGender.charAt(0).toUpperCase() + urlGender.slice(1) + "'s Collection";
    }

    const filterCheckboxes = $$('.filter-sidebar input[type="checkbox"]');
    const colorSwatches = $$('.filter-sidebar .color-swatch');
    const priceRange = $('#priceRange');
    const priceValue = $('#priceValue');
    const sortSelect = $('#sortSelect');
    const resultsCount = $('#resultsCount');
    const clearBtn = $('#clearFilters');
    const filterToggle = $('#filterToggle');
    const filterSidebar = $('#filterSidebar');

    // Pre-check checkboxes based on URL
    if (urlGender) {
      const cb = $(`[data-filter="gender"][value="${urlGender}"]`);
      if (cb) cb.checked = true;
    }
    if (urlCategory) {
      // Subcategories are mapped to category filter in sidebar
      const cb = $(`[data-filter="category"][value="${urlCategory}"]`);
      if (cb) cb.checked = true;
    }

    // Active color filters
    let activeColors = [];

    colorSwatches.forEach(swatch => {
      swatch.addEventListener('click', () => {
        swatch.classList.toggle('active');
        const color = swatch.dataset.value;
        if (activeColors.includes(color)) {
          activeColors = activeColors.filter(c => c !== color);
        } else {
          activeColors.push(color);
        }
        applyFilters();
      });
    });

    if (priceRange) {
      priceRange.addEventListener('input', () => {
        const val = parseInt(priceRange.value);
        if (priceValue) priceValue.textContent = `₹${val.toLocaleString('en-IN')}`;
        applyFilters();
      });
    }

    filterCheckboxes.forEach(cb => cb.addEventListener('change', applyFilters));
    if (sortSelect) sortSelect.addEventListener('change', applyFilters);

    if (clearBtn) {
      clearBtn.addEventListener('click', () => {
        filterCheckboxes.forEach(cb => cb.checked = false);
        colorSwatches.forEach(s => s.classList.remove('active'));
        activeColors = [];
        activeTag = '';
        urlGender = '';
        urlCategory = '';
        if (priceRange) { priceRange.value = 4000; if (priceValue) priceValue.textContent = '₹4,000'; }
        if (sortSelect) sortSelect.value = 'featured';
        if (pageTitle) pageTitle.textContent = 'Shop All';
        history.replaceState(null, '', window.location.pathname);
        applyFilters();
      });
    }

    // Mobile filter toggle
    if (filterToggle && filterSidebar) {
      if (window.innerWidth <= 768) filterToggle.style.display = '';
      window.addEventListener('resize', () => {
        filterToggle.style.display = window.innerWidth <= 768 ? '' : 'none';
      });
      filterToggle.addEventListener('click', () => {
        filterSidebar.classList.toggle('open');
        filterSidebar.style.display = filterSidebar.classList.contains('open') ? 'block' : '';
      });
    }

    function applyFilters() {
      if (typeof PRODUCTS === 'undefined') return;

      const activeGenders = $$('[data-filter="gender"]:checked').map(cb => cb.value);
      const activeCategories = $$('[data-filter="category"]:checked').map(cb => cb.value);
      const activeSizes = $$('[data-filter="size"]:checked').map(cb => cb.value);
      const activeTags = $$('[data-filter="tag"]:checked').map(cb => cb.value); // If there are tag checkboxes
      const maxPrice = priceRange ? parseInt(priceRange.value) : 9999;

      let filteredProducts = PRODUCTS.filter(p => {
        const pGender = p.gender || '';
        const pSubCat = p.subcategory || '';
        const pCat = p.category || '';
        const pSizes = p.sizes || [];
        const pColors = p.colors || [];
        const priceMatch = p.price <= maxPrice;

        const genderMatch = activeGenders.length === 0 || activeGenders.includes(pGender) || (urlGender && pGender === urlGender && activeGenders.length === 0);
        const catMatch = activeCategories.length === 0 || activeCategories.includes(pSubCat) || activeCategories.includes(pCat.toLowerCase()) || (urlCategory && (pSubCat === urlCategory || pCat.toLowerCase() === urlCategory) && activeCategories.length === 0);
        const sizeMatch = activeSizes.length === 0 || activeSizes.some(s => pSizes.includes(s));
        const colorMatch = activeColors.length === 0 || activeColors.some(c => pColors.includes(c));
        
        let tagMatch = true;
        if (activeTag === 'new') tagMatch = p.isNewArrival;
        if (activeTag === 'bestseller') tagMatch = p.isBestSeller;
        if (activeTag === 'sale') tagMatch = !!p.oldPrice;

        if (activeTags.length > 0) {
          if (activeTags.includes('new') && !p.isNewArrival) tagMatch = false;
          if (activeTags.includes('bestseller') && !p.isBestSeller) tagMatch = false;
          if (activeTags.includes('sale') && !p.oldPrice) tagMatch = false;
        }

        return genderMatch && catMatch && sizeMatch && colorMatch && tagMatch && priceMatch;
      });

      // Sort
      if (sortSelect) {
        const sortVal = sortSelect.value;
        filteredProducts.sort((a, b) => {
          if (sortVal === 'price-low') return a.price - b.price;
          if (sortVal === 'price-high') return b.price - a.price;
          if (sortVal === 'rating') return (b.rating || 0) - (a.rating || 0);
          return 0; // 'featured' uses default array order
        });
      }

      // Render
      productGrid.innerHTML = filteredProducts.map(createProductCard).join('');
      
      if (resultsCount) resultsCount.textContent = filteredProducts.length;

      // Re-init tilt and reveal for new elements
      initTiltCards();
      initScrollReveal();
    }

    // Initial render
    setTimeout(() => {
      if (skeletonGrid) skeletonGrid.style.display = 'none';
      productGrid.style.display = '';
      applyFilters();
    }, 800);
  }

  /* ---------- Newsletter Form (PHP Backend) ---------- */
  function initNewsletter() {
    const form = $('#newsletterForm');
    if (!form) return;
    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      const emailEl = $('#nlEmail');
      const email   = emailEl?.value?.trim();
      if (!email || !email.includes('@')) {
        showToast('Please enter a valid email address', 'error');
        return;
      }
      try {
        const fd = new FormData();
        fd.append('action', 'newsletter');
        fd.append('email', email);
        const res  = await fetch('php/cart.php', { method: 'POST', body: fd });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success && emailEl) emailEl.value = '';
      } catch {
        showToast('Welcome! Check your inbox for 10% off 🎉', 'success');
        if (emailEl) emailEl.value = '';
      }
    });
  }

  /* ---------- Contact Form (PHP Backend) ---------- */
  function initContactForm() {
    const form = $('#contactForm');
    if (!form) return;

    form.addEventListener('submit', async (e) => {
      e.preventDefault();
      let valid = true;

      const nameGroup = $('#nameGroup');
      const name = $('#contactName');
      if (!name.value.trim()) { nameGroup.classList.add('error'); valid = false; }
      else { nameGroup.classList.remove('error'); }

      const emailGroup = $('#emailGroup');
      const email = $('#contactEmail');
      if (!email.value.trim() || !email.value.includes('@')) { emailGroup.classList.add('error'); valid = false; }
      else { emailGroup.classList.remove('error'); }

      const msgGroup = $('#messageGroup');
      const msg = $('#contactMessage');
      if (!msg.value.trim()) { msgGroup.classList.add('error'); valid = false; }
      else { msgGroup.classList.remove('error'); }

      if (!valid) return;

      try {
        const fd = new FormData();
        fd.append('action', 'contact');
        fd.append('name', name.value.trim());
        fd.append('email', email.value.trim());
        fd.append('subject', $('#contactSubject')?.value || 'General Inquiry');
        fd.append('message', msg.value.trim());
        const res  = await fetch('php/cart.php', { method: 'POST', body: fd });
        const data = await res.json();
        showToast(data.message, data.success ? 'success' : 'error');
        if (data.success) form.reset();
      } catch {
        showToast('Message sent successfully! We\'ll be in touch soon. ✉️', 'success');
        form.reset();
      }
    });

    $$('input, textarea', form).forEach(field => {
      field.addEventListener('input', () => {
        field.closest('.form-group')?.classList.remove('error');
      });
    });
  }

  /* ---------- Toast ---------- */
  let toastTimer = null;
  function showToast(message, type = 'success') {
    const toast = $('#toast');
    const toastMsg = $('#toastMessage');
    const toastIcon = toast?.querySelector('.toast-icon');
    if (!toast || !toastMsg) return;

    clearTimeout(toastTimer);
    toast.classList.remove('show', 'success', 'error');

    toastMsg.textContent = message;
    toast.classList.add(type);
    if (toastIcon) toastIcon.textContent = type === 'success' ? '✓' : '✕';

    // Force reflow
    void toast.offsetWidth;
    toast.classList.add('show');

    toastTimer = setTimeout(() => {
      toast.classList.remove('show');
    }, 3500);
  }

  /* ---------- Wishlist Button ---------- */
  function initWishlist() {
    const btn = $('#wishlistBtn');
    if (!btn) return;

    // Get product info from the add-to-cart button or data attributes
    const cartBtn = $('#addToCartBtn');
    const productName = cartBtn?.dataset.name || '';
    const productPrice = parseFloat(cartBtn?.dataset.price || 0);
    const productImg = cartBtn?.dataset.img || '';
    // Try to get product ID from URL
    const urlParams = new URLSearchParams(window.location.search);
    const productId = parseInt(urlParams.get('id')) || 0;

    // Check if already wishlisted
    let wishlisted = Wishlist.hasItem(productName);
    if (wishlisted) {
      btn.textContent = '♥ Added to Wishlist';
      btn.style.borderColor = 'var(--clr-accent)';
      btn.style.color = 'var(--clr-accent)';
    }

    btn.addEventListener('click', () => {
      wishlisted = !wishlisted;
      if (wishlisted) {
        Wishlist.addItem({
          id: productId,
          name: productName,
          price: productPrice,
          img: productImg
        });
      } else {
        // Remove by finding the index
        const items = Wishlist.getItems();
        const idx = items.findIndex(i => i.name === productName);
        if (idx !== -1) Wishlist.removeItem(idx);
        else showToast('Removed from wishlist', 'success');
      }
      btn.textContent = wishlisted ? '♥ Added to Wishlist' : '♡ Add to Wishlist';
      btn.style.borderColor = wishlisted ? 'var(--clr-accent)' : '';
      btn.style.color = wishlisted ? 'var(--clr-accent)' : '';
    });
  }

  /* ---------- Wishlist Flyout ---------- */
  function initWishlistFlyout() {
    const wishBtn = $('#wishlistNavBtn');
    const wishClose = $('#wishlistClose');
    const wishOverlay = $('#wishlistOverlay');
    const wishFlyout = $('#wishlistFlyout');
    if (!wishBtn || !wishFlyout) return;

    let previousFocus = null;

    function openWishlist() {
      previousFocus = document.activeElement;
      wishFlyout.classList.add('open');
      wishOverlay.classList.add('open');
      wishFlyout.setAttribute('aria-hidden', 'false');
      wishOverlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      Wishlist.updateUI();
      const focusable = $$('button, [href], input', wishFlyout);
      if (focusable.length) focusable[0].focus();
    }

    function closeWishlist() {
      wishFlyout.classList.remove('open');
      wishOverlay.classList.remove('open');
      wishFlyout.setAttribute('aria-hidden', 'true');
      wishOverlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      if (previousFocus) previousFocus.focus();
    }

    wishBtn.addEventListener('click', openWishlist);
    if (wishClose) wishClose.addEventListener('click', closeWishlist);
    if (wishOverlay) wishOverlay.addEventListener('click', closeWishlist);

    wishFlyout.addEventListener('keydown', (e) => {
      if (e.key === 'Escape') { closeWishlist(); return; }
      if (e.key !== 'Tab') return;
      const focusable = $$('button, [href], input', wishFlyout);
      if (!focusable.length) return;
      const first = focusable[0];
      const last = focusable[focusable.length - 1];
      if (e.shiftKey && document.activeElement === first) {
        e.preventDefault(); last.focus();
      } else if (!e.shiftKey && document.activeElement === last) {
        e.preventDefault(); first.focus();
      }
    });

    Wishlist.updateUI();
  }

  /* ---------- Parallax Hero ---------- */
  function initParallax() {
    const heroBg = $('.hero-bg');
    if (!heroBg) return;
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) return;

    let ticking = false;
    window.addEventListener('scroll', () => {
      if (!ticking) {
        requestAnimationFrame(() => {
          const scroll = window.scrollY;
          heroBg.style.transform = `translateY(${scroll * 0.3}px) scale(1.1)`;
          ticking = false;
        });
        ticking = true;
      }
    }, { passive: true });
  }

  /* ---------- Hero Cursor Glow ---------- */
  function initHeroCursorEffect() {
    const hero = $('.hero');
    if (!hero) return;
    const prefersReduced = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    if (prefersReduced) return;

    const glow = document.createElement('div');
    glow.style.cssText = `
      position:absolute;width:400px;height:400px;border-radius:50%;
      background:radial-gradient(circle,rgba(201,169,110,.08) 0%,transparent 70%);
      pointer-events:none;z-index:1;transition:transform .1s;
      transform:translate(-50%,-50%)
    `;
    hero.style.position = 'relative';
    hero.appendChild(glow);

    let rafId = null;
    hero.addEventListener('mousemove', (e) => {
      if (rafId) return;
      rafId = requestAnimationFrame(() => {
        const rect = hero.getBoundingClientRect();
        glow.style.left = `${e.clientX - rect.left}px`;
        glow.style.top = `${e.clientY - rect.top}px`;
        rafId = null;
      });
    });
  }

  /* ---------- Smooth Page Transition ---------- */
  function initPageTransitions() {
    document.body.style.opacity = '0';
    document.body.style.transition = 'opacity 0.4s ease';
    requestAnimationFrame(() => {
      document.body.style.opacity = '1';
    });
  }

  /* ---------- Dynamic Product Detail Page ---------- */
  function initProductDetail() {
    const main = $('#productDetailMain');
    if (!main) return; // Not on product-detail page

    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    // Check if PRODUCTS data is available
    if (typeof PRODUCTS === 'undefined' || typeof getProductById === 'undefined') {
      main.innerHTML = '<div style="text-align:center;padding:4rem 1rem"><h2>Product data not available</h2><p style="color:var(--clr-text-muted);margin-top:1rem">Please try again later.</p><a href="products.html" class="btn btn-primary" style="margin-top:2rem;display:inline-block">Browse Products</a></div>';
      return;
    }

    if (!productId) {
      // No ID — redirect to products page
      window.location.href = 'products.html';
      return;
    }

    const product = getProductById(productId);

    if (!product) {
      // Invalid ID — show error with back link
      main.innerHTML = `
        <div style="text-align:center;padding:4rem 1rem">
          <h2 style="font-size:var(--fs-xl)">Product Not Found</h2>
          <p style="color:var(--clr-text-muted);margin-top:1rem;max-width:400px;margin-left:auto;margin-right:auto">
            Sorry, we couldn't find the product you're looking for. It may have been removed or the link may be incorrect.
          </p>
          <a href="products.html" class="btn btn-primary" style="margin-top:2rem;display:inline-block">Browse All Products</a>
        </div>`;
      document.title = 'Product Not Found — ELEVÉ';
      const bcName = $('#breadcrumbName');
      if (bcName) bcName.textContent = 'Not Found';
      return;
    }

    // Update page title and breadcrumb
    document.title = `${product.name} — ELEVÉ`;
    const bcName = $('#breadcrumbName');
    if (bcName) bcName.textContent = product.name;


    // Build color swatches
    const colorsHTML = product.colors.map((c, i) => `
      <button class="color-swatch${i === 0 ? ' active' : ''}" style="background:${c.hex}" data-color="${c.name}" aria-label="${c.name}" title="${c.name}"></button>
    `).join('');

    // Build size buttons
    const defaultSize = (product.sizes && product.sizes.length) ? product.sizes[0] : 'M';
    const sizesHTML = (product.sizes && product.sizes.length ? product.sizes : ['M']).map((s, i) => `
      <button class="size-btn${i === 0 ? ' active' : ''}" role="radio" aria-checked="${i === 0 ? 'true' : 'false'}" data-size="${s}">${s}</button>
    `).join('');

    // Build price display
    const priceHTML = product.oldPrice
      ? `₹${product.price.toLocaleString('en-IN')} <span class="old-price">₹${product.oldPrice.toLocaleString('en-IN')}</span>`
      : `₹${product.price.toLocaleString('en-IN')}`;

    // Build details list
    const detailsHTML = product.details.map(d => `<li>${d}</li>`).join('');

    // Render the full product detail
    main.innerHTML = `
    <div class="product-detail">
      <!-- Gallery -->
      <div class="gallery reveal">
        <div class="gallery-main" id="galleryMain" role="img" aria-label="Product image">
          <img src="${productImg(product.images[0], 800)}" alt="${product.name}" id="mainImage">
        </div>
      </div>

      <!-- Product Info -->
      <div class="product-info reveal">
        <p class="product-category">${product.category}</p>
        <h1 class="product-name">${product.name}</h1>
        <p class="product-price">${priceHTML}</p>
        <p class="product-desc">${product.description}</p>

        <!-- Color Selector -->
        <div class="size-selector">
          <div class="label">Color — <span id="selectedColor">${product.colors[0].name}</span></div>
          <div class="color-swatches" style="margin-top:.5rem">${colorsHTML}</div>
        </div>

        <!-- Size Selector -->
        <div class="size-selector">
          <div class="label">Size — <span id="selectedSize">${defaultSize}</span></div>
          <div class="size-options" role="radiogroup" aria-label="Size selection">${sizesHTML}</div>
        </div>

        <!-- Add to Cart -->
        <div class="add-to-cart-group">
          <div class="qty-selector">
            <button class="qty-btn" id="qtyMinus" aria-label="Decrease quantity">−</button>
            <span class="qty-value" id="qtyValue" aria-live="polite">1</span>
            <button class="qty-btn" id="qtyPlus" aria-label="Increase quantity">+</button>
          </div>
          <button class="btn btn-primary btn-add-cart" id="addToCartBtn"
                  data-id="${product.id}"
                  data-name="${product.name}" data-price="${product.price}"
                  data-img="${productImg(product.images[0], 150)}">
            Add to Cart — ₹${product.price.toLocaleString('en-IN')}
          </button>
        </div>

        <!-- Wishlist -->
        <button class="btn btn-outline" style="width:100%;margin-bottom:var(--sp-lg)" id="wishlistBtn">
          ♡ Add to Wishlist
        </button>
      </div>
    </div>

    <!-- Full-width Accordion Section -->
    <div class="product-detail-extra">
      <div class="accordion">
          <div class="accordion-item open">
            <button class="accordion-header" aria-expanded="true">
              Description <span class="accordion-icon">▾</span>
            </button>
            <div class="accordion-body">
              <div class="accordion-body-inner">
                <p>${product.description}</p>
                <ul style="margin-top:.5rem;padding-left:1.2rem;list-style:disc;color:var(--clr-text-muted)">
                  ${detailsHTML}
                </ul>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <button class="accordion-header" aria-expanded="false">
              Size & Fit <span class="accordion-icon">▾</span>
            </button>
            <div class="accordion-body">
              <div class="accordion-body-inner">
                <p>${product.fit}</p>
              </div>
            </div>
          </div>
          <div class="accordion-item">
            <button class="accordion-header" aria-expanded="false">
              Shipping & Returns <span class="accordion-icon">▾</span>
            </button>
            <div class="accordion-body">
              <div class="accordion-body-inner">
                <p>Free standard shipping on orders over $150. Express shipping available at checkout.</p>
                <p style="margin-top:.5rem">We accept returns within 30 days of purchase. Items must be unworn with original tags attached.</p>
              </div>
            </div>
          </div>
        </div>
    </div>`;

    // Store images array on the gallery for the gallery init to pick up
    window._productImages = product.images.map(img => productImg(img, 800));

    // Re-initialize interactive components after DOM injection
    window._pdpInitialized = true;
    initAddToCart();
    initSizeSelector();
    initColorSelector();
    initGallery();
    initAccordion();
    initWishlist();
    initScrollReveal();
  }

  /* ---------- Search Overlay ---------- */
  function initSearch() {
    const searchBtn = $('#searchBtn');
    const headerInput = $('#headerSearchInput');
    const bottomSearchBtn = $('#bottomSearchBtn');

    // Build overlay HTML and inject into body if not already present
    if (!$('#searchOverlay')) {
      const overlay = document.createElement('div');
      overlay.id = 'searchOverlay';
      overlay.className = 'search-overlay';
      overlay.setAttribute('role', 'dialog');
      overlay.setAttribute('aria-label', 'Search products');
      overlay.setAttribute('aria-hidden', 'true');
      overlay.innerHTML = `
        <div class="search-modal">
          <div class="search-bar">
            <span class="search-icon-inner">🔍</span>
            <input
              id="searchInput"
              type="search"
              class="search-input"
              placeholder="Search products, categories, styles..."
              autocomplete="off"
              aria-label="Search products"
            />
            <button id="searchClose" class="search-close" aria-label="Close search">✕</button>
          </div>
          <div id="searchResults" class="search-results" role="listbox" aria-live="polite"></div>
        </div>
      `;
      document.body.appendChild(overlay);
    }

    const overlay = $('#searchOverlay');
    const input = $('#searchInput');
    const results = $('#searchResults');
    const closeBtn = $('#searchClose');
    let previousFocus = null;

    function openSearch(initialQuery = '') {
      previousFocus = document.activeElement;
      overlay.classList.add('open');
      overlay.setAttribute('aria-hidden', 'false');
      document.body.style.overflow = 'hidden';
      if (input) {
        input.value = initialQuery;
        setTimeout(() => input.focus(), 60);
      }
      renderResults(initialQuery);
    }

    function closeSearch() {
      overlay.classList.remove('open');
      overlay.setAttribute('aria-hidden', 'true');
      document.body.style.overflow = '';
      if (input) input.value = '';
      if (results) results.innerHTML = '';
      if (previousFocus) previousFocus.focus();
    }

    function renderResults(query) {
      if (!results) return;
      const q = (query || '').trim().toLowerCase();

      // Guard if PRODUCTS not available (non-product pages)
      if (typeof PRODUCTS === 'undefined') {
        results.innerHTML = `
          <div class="search-empty">
            <div class="search-empty-icon">🔍</div>
            <p>Start typing to search products</p>
            <a href="products.html?q=${encodeURIComponent(q)}" class="btn btn-primary" style="margin-top:1rem">Browse All Products →</a>
          </div>`;
        return;
      }

      const matched = q === ''
        ? PRODUCTS.slice(0, 6)
        : PRODUCTS.filter(p =>
          (p.name && p.name.toLowerCase().includes(q)) ||
          (p.category && p.category.toLowerCase().includes(q)) ||
          (p.subcategory && p.subcategory.toLowerCase().includes(q)) ||
          (p.gender && p.gender.toLowerCase().includes(q)) ||
          (p.description && p.description.toLowerCase().includes(q))
        ).slice(0, 8);

      if (matched.length === 0) {
        results.innerHTML = `
          <div class="search-empty">
            <div class="search-empty-icon">😞</div>
            <p>No products found for "<strong>${query}</strong>"</p>
            <a href="products.html" class="btn btn-outline" style="margin-top:1rem;display:inline-block">Browse All Products</a>
          </div>`;
        return;
      }

      results.innerHTML = `
        <p class="search-hint">${q === '' ? 'Popular trending picks' : `${matched.length} result${matched.length !== 1 ? 's' : ''} for "${query}"`}</p>
        <div class="search-grid">
          ${matched.map(p => {
        const img = (p.images && p.images[0]) ? `${p.images[0]}&w=200&q=70&fit=crop` : '';
        const price = p.oldPrice
          ? `<span class="search-price">₹${p.price.toFixed(0)}</span><span class="search-old-price">₹${p.oldPrice.toFixed(0)}</span>`
          : `<span class="search-price">₹${p.price.toFixed(0)}</span>`;
        return `
              <a href="product-detail.html?id=${p.id}" class="search-card" role="option">
                <div class="search-card-img">
                  ${img ? `<img src="${img}" alt="${p.name}" loading="lazy">` : `<div class="search-card-placeholder">📦</div>`}
                </div>
                <div class="search-card-info">
                  <p class="search-card-cat">${p.category || 'Luxury Fashion'}</p>
                  <p class="search-card-name">${p.name}</p>
                  <div class="search-card-price">${price}</div>
                </div>
              </a>`;
      }).join('')}
        </div>
        ${matched.length >= 6 && q !== '' ? `<div class="search-footer"><a href="products.html?q=${encodeURIComponent(q)}" class="btn btn-outline">View all results ↗</a></div>` : ''}`;
    }

    // Wire legacy search button if present
    if (searchBtn) searchBtn.addEventListener('click', () => openSearch(''));
    
    // Wire bottom search button
    if (bottomSearchBtn) bottomSearchBtn.addEventListener('click', () => openSearch(''));
    
    // Wire Max Fashion inline header search input
    if (headerInput) {
      headerInput.addEventListener('focus', () => {
        openSearch(headerInput.value);
      });
      headerInput.addEventListener('input', () => {
        openSearch(headerInput.value);
      });
      headerInput.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          e.preventDefault();
          const query = headerInput.value.trim();
          if (query) {
            window.location.href = `products.html?q=${encodeURIComponent(query)}`;
          } else {
            openSearch('');
          }
        }
      });
    }

    if (closeBtn) closeBtn.addEventListener('click', closeSearch);
    
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) closeSearch();
    });
    
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && overlay.classList.contains('open')) closeSearch();
    });
    
    if (input) {
      input.addEventListener('input', () => renderResults(input.value));
      input.addEventListener('keydown', (e) => {
        if (e.key === 'Enter') {
          const query = input.value.trim();
          if (query) {
            window.location.href = `products.html?q=${encodeURIComponent(query)}`;
          }
        }
      });
    }
  }

    /* ---------- Auth Modal (PHP Backend) ---------- */
  function initAuthModal() {
    const authBtn     = $('#authBtn');
    const authOverlay = $('#authOverlay');
    const authModal   = $('#authModal');
    const authClose   = $('#authClose');
    const guestBtn    = $('#guestCheckoutBtn');
    const loginForm   = $('#authForm');
    const regForm     = $('#registerForm');
    const tabSignIn   = $('#tabSignInBtn');
    const tabRegister = $('#tabRegisterBtn');
    const linkSwitch  = $('#linkSwitchToSignIn');

    let currentUser = null;

    // Check auth status
    fetch('php/auth.php?action=check').then(r => r.json()).then(data => {
      if (data.logged_in && data.user) {
        currentUser = data.user;
        if (authBtn) {
          if (data.user.role === 'admin') {
            authBtn.innerHTML = '👑 ' + (data.user.name || 'Admin');
            authBtn.style.color = '#c9a96e';
          } else {
            authBtn.innerHTML = '👤 ' + data.user.name.split(' ')[0];
          }
        }
      }
    }).catch(() => {});

    function openAuth() {
      if (currentUser && currentUser.role === 'admin') {
        window.location.href = 'admin/index.php';
        return;
      }
      if (!authModal || !authOverlay) return;
      if (currentUser) {
        showAccountView(currentUser);
      } else {
        showSignInTab();
      }
      authOverlay.classList.add('open'); authModal.classList.add('open');
      authModal.setAttribute('aria-hidden','false'); authOverlay.setAttribute('aria-hidden','false');
      document.body.style.overflow = 'hidden';
    }

    function closeAuth() {
      if (!authModal || !authOverlay) return;
      authOverlay.classList.remove('open'); authModal.classList.remove('open');
      authModal.setAttribute('aria-hidden','true'); authOverlay.setAttribute('aria-hidden','true');
      document.body.style.overflow = '';
    }

    function showSignInTab() {
      if (loginForm) loginForm.style.display = 'block';
      if (regForm)   regForm.style.display = 'none';
      if (tabSignIn) tabSignIn.classList.add('active');
      if (tabRegister) tabRegister.classList.remove('active');
    }

    function showRegisterTab() {
      if (loginForm) loginForm.style.display = 'none';
      if (regForm)   regForm.style.display = 'block';
      if (tabSignIn) tabSignIn.classList.remove('active');
      if (tabRegister) tabRegister.classList.add('active');
    }

    function showAccountView(user) {
      const headerTitle = $('.auth-title', authModal);
      const headerSub   = $('.auth-subtitle', authModal);
      const tabs        = $('#authTabs');
      if (tabs) tabs.style.display = 'none';
      if (regForm) regForm.style.display = 'none';

      if (headerTitle) headerTitle.textContent = 'My Account';
      if (headerSub) headerSub.textContent = `Signed in as ${user.name} (${user.email})`;

      if (loginForm) {
        loginForm.innerHTML = `
          <div style="background:rgba(201,169,110,0.08);border:1px solid rgba(201,169,110,0.3);border-radius:8px;padding:1rem;margin-bottom:1rem;text-align:center;">
            <div style="font-size:1.1rem;font-weight:600;color:#c9a96e;margin-bottom:0.25rem;">${user.name}</div>
            <div style="font-size:0.85rem;color:#a09a93;">${user.email}</div>
            <div style="font-size:0.75rem;text-transform:uppercase;letter-spacing:0.1em;color:${user.role === 'admin' ? '#c9a96e' : '#2ecc71'};margin-top:0.4rem;font-weight:700;">Role: ${user.role}</div>
          </div>

          <div style="display:flex;gap:0.5rem;margin-bottom:1rem;">
            <a href="checkout.html" class="btn btn-primary" style="flex:1;text-align:center;text-decoration:none;font-size:0.85rem;padding:0.6rem;">🛒 Place Order / Checkout</a>
          </div>

          <div style="margin-bottom:1.25rem;text-align:left;">
            <div style="font-size:0.85rem;font-weight:700;margin-bottom:0.5rem;color:#c9a96e;display:flex;align-items:center;gap:0.4rem;">
              <span>📦</span> My Orders
            </div>
            <div id="userOrdersList" style="max-height:180px;overflow-y:auto;background:var(--clr-surface-2,#141414);border:1px solid var(--clr-border,#2a2a2a);border-radius:8px;padding:0.6rem;font-size:0.8rem;">
              <div style="text-align:center;color:var(--clr-text-muted,#777);padding:0.8rem;">Loading your orders...</div>
            </div>
          </div>

          ${user.role === 'admin' ? '<a href="admin/index.php" class="btn btn-outline" style="width:100%;display:block;text-align:center;text-decoration:none;margin-bottom:0.75rem;">👑 Open Admin Panel</a>' : ''}
          <button type="button" class="btn btn-outline" id="btnLogoutAction" style="width:100%;">Sign Out</button>
          <div class="auth-full-links" style="margin-top:1rem;">
            <a href="products.html">Continue Shopping</a> &bull;
            <a href="checkout.html">Checkout</a>
          </div>
        `;

        // Load user orders from history API
        fetch('php/orders.php?action=history')
          .then(r => r.json())
          .then(d => {
            const listEl = document.getElementById('userOrdersList');
            if (!listEl) return;
            if (d.success && d.orders && d.orders.length > 0) {
              listEl.innerHTML = d.orders.map(o => `
                <div style="padding:0.5rem 0;border-bottom:1px solid rgba(255,255,255,0.06);display:flex;justify-content:space-between;align-items:center;">
                  <div>
                    <div style="font-weight:600;font-family:monospace;color:#c9a96e;">${o.order_number}</div>
                    <div style="font-size:0.72rem;color:var(--clr-text-muted,#888);">${(o.created_at || '').substring(0,10)} &bull; ${o.item_count || 1} item(s)</div>
                  </div>
                  <div style="text-align:right;">
                    <div style="font-weight:700;color:var(--clr-text,#fff);">₹${Number(o.total).toLocaleString('en-IN')}</div>
                    <span style="font-size:0.68rem;padding:0.15rem 0.4rem;border-radius:4px;background:rgba(201,169,110,0.15);color:#c9a96e;text-transform:capitalize;">${o.status}</span>
                  </div>
                </div>
              `).join('');
            } else {
              listEl.innerHTML = '<div style="text-align:center;color:var(--clr-text-muted,#888);padding:0.75rem 0.5rem;">No orders placed yet.<br><a href="products.html" style="color:#c9a96e;text-decoration:underline;margin-top:0.3rem;display:inline-block;">Start shopping</a></div>';
            }
          })
          .catch(() => {
            const listEl = document.getElementById('userOrdersList');
            if (listEl) listEl.innerHTML = '<div style="text-align:center;color:#888;padding:0.5rem;">Unable to load orders.</div>';
          });

        const btnLogout = $('#btnLogoutAction');
        if (btnLogout) {
          btnLogout.addEventListener('click', () => {
            fetch('php/auth.php?action=logout').then(() => location.reload());
          });
        }
      }
    }

    if (authBtn)     authBtn.addEventListener('click', openAuth);
    if (authClose)   authClose.addEventListener('click', closeAuth);
    if (authOverlay) authOverlay.addEventListener('click', closeAuth);
    if (tabSignIn)   tabSignIn.addEventListener('click', showSignInTab);
    if (tabRegister) tabRegister.addEventListener('click', showRegisterTab);
    if (linkSwitch)  linkSwitch.addEventListener('click', (e) => { e.preventDefault(); showSignInTab(); });

    if (guestBtn) {
      guestBtn.addEventListener('click', () => {
        closeAuth();
        showToast('Continuing as guest shopper', 'success');
      });
    }

    // Handle Login Form Submission
    if (loginForm) {
      loginForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const emailInput = $('#authEmail');
        const passInput  = $('#authPassword');
        const emailVal   = emailInput ? emailInput.value.trim() : '';
        const passVal    = passInput ? passInput.value.trim() : '';

        if (!emailVal || !passVal) {
          showToast('Please enter both email and password', 'error');
          return;
        }

        const isAdminCreds = (emailVal.toLowerCase() === 'admin@gmail.com' && passVal === '123');

        const fd = new FormData();
        fd.append('action', 'login');
        fd.append('email', emailVal);
        fd.append('password', passVal);

        try {
          const res  = await fetch('php/auth.php', { method: 'POST', body: fd });
          const data = await res.json();
          if (data.success) {
            closeAuth();
            showToast(data.message || 'Login successful!', 'success');
            if (isAdminCreds || data.is_admin || (data.user && data.user.role === 'admin')) {
              window.location.href = data.redirect || 'admin/index.php';
            } else {
              setTimeout(() => location.reload(), 400);
            }
          } else {
            showToast(data.message || 'Invalid credentials', 'error');
          }
        } catch (err) {
          showToast('Connection error. Please try again.', 'error');
        }
      });
    }

    // Handle Registration Form Submission
    if (regForm) {
      regForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const nameVal  = $('#regName')?.value.trim() || '';
        const emailVal = $('#regEmail')?.value.trim() || '';
        const phoneVal = $('#regPhone')?.value.trim() || '';
        const passVal  = $('#regPassword')?.value.trim() || '';

        if (!nameVal || !emailVal || !passVal) {
          showToast('Name, email and password are required', 'error');
          return;
        }

        const fd = new FormData();
        fd.append('action', 'register');
        fd.append('name', nameVal);
        fd.append('email', emailVal);
        fd.append('phone', phoneVal);
        fd.append('password', passVal);

        try {
          const res  = await fetch('php/auth.php', { method: 'POST', body: fd });
          const data = await res.json();
          if (data.success) {
            closeAuth();
            showToast(data.message || `Account created successfully! ?`, 'success');
            setTimeout(() => location.reload(), 800);
          } else {
            showToast(data.message || 'Registration failed', 'error');
          }
        } catch (err) {
          showToast('Connection error during registration.', 'error');
        }
      });
    }
  }

  /* ---------- Bottom Persistent Bar Interactions ---------- */
  function initBottomNav() {
    const bottomWishlistBtn = $('#bottomWishlistBtn');
    const bottomCartBtn = $('#bottomCartBtn');
    const wishlistNavBtn = $('#wishlistNavBtn');
    const cartBtn = $('#cartBtn');

    if (bottomWishlistBtn && wishlistNavBtn) {
      bottomWishlistBtn.addEventListener('click', () => wishlistNavBtn.click());
    }
    if (bottomCartBtn && cartBtn) {
      bottomCartBtn.addEventListener('click', () => cartBtn.click());
    }
  }

  
  /* ---------- Global Quick Add to Cart ---------- */
  function initQuickAdd() {
    document.addEventListener('click', (e) => {
      const btn = e.target.closest('.btn-quick-add');
      if (!btn) return;
      e.preventDefault();
      e.stopPropagation();

      const item = {
        id: parseInt(btn.dataset.id) || 0,
        name: btn.dataset.name || 'Product',
        price: parseFloat(btn.dataset.price) || 0,
        img: btn.dataset.img || '',
        size: btn.dataset.size || 'M',
        color: btn.dataset.color || '',
        qty: 1
      };

      Cart.addItem(item);

      const oldText = btn.textContent;
      btn.textContent = '✓ Added!';
      btn.style.background = 'var(--clr-accent)';
      btn.style.color = '#0a0a0a';
      setTimeout(() => {
        btn.textContent = oldText;
        btn.style.background = '';
        btn.style.color = '';
      }, 1200);

      // Open cart drawer
      const cartBtn = $('#cartBtn');
      if (cartBtn) cartBtn.click();
    });
  }

  /* ---------- Initialize Everything ---------- */
  function init() {
    initPageTransitions();
    initLoader();
    initHeader();
    initMobileNav();
    initTiltCards();
    initScrollReveal();
    initCart();
    initWishlistFlyout();
    initSearch();
    initAuthModal();
    initBottomNav();
    initQuickAdd();
    initProductDetail(); // Must run before individual component inits

    // Skip these if product detail page already initialized them
    if (!window._pdpInitialized) {
      initAddToCart();
      initSizeSelector();
      initColorSelector();
      initGallery();
      initAccordion();
      initWishlist();
    }

    initFilters();
    initNewsletter();
    initContactForm();
    initParallax();
    initHeroCursorEffect();
  }

  // Run when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();
