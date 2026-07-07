document.addEventListener('DOMContentLoaded', function() {
    
    // Sticky Header Logic
    const header = document.querySelector('.aa-header-wrapper');
    if (header) {
        const threshold = 120;
        window.addEventListener('scroll', () => {
            if (window.scrollY > threshold) {
                header.classList.add('is-sticky');
            } else {
                header.classList.remove('is-sticky');
            }
        });
    }

    // Mobile Menu Toggle Logic
    const toggleBtn = document.querySelector('.aa-mobile-toggle');
    const closeBtn = document.querySelector('.aa-offcanvas-close-bottom-icon');
    const overlay = document.querySelector('.aa-offcanvas-overlay');
    const offcanvas = document.querySelector('.aa-offcanvas-menu');

    function openMenu() {
        if (offcanvas) offcanvas.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        if (offcanvas) offcanvas.classList.remove('is-active');
        document.body.style.overflow = '';
    }

    if (toggleBtn) toggleBtn.addEventListener('click', openMenu);
    if (closeBtn) closeBtn.addEventListener('click', closeMenu);
    if (overlay) overlay.addEventListener('click', closeMenu);

    // Mobile Menu Accordion
    const mobileMenuItems = document.querySelectorAll('.aa-mobile-menu-list .menu-item-has-children');
    mobileMenuItems.forEach(item => {
        // Create toggle button
        const toggle = document.createElement('button');
        toggle.className = 'aa-menu-toggle-btn';
        toggle.innerHTML = '<span class="aa-menu-toggle-icon"></span>';
        
        const link = item.querySelector('a');
        if (link && link.parentNode) {
            link.parentNode.insertBefore(toggle, link.nextSibling);
        }

        const subMenu = item.querySelector('.sub-menu');
        
        if (toggle && subMenu) {
            toggle.addEventListener('click', function(e) {
                e.preventDefault();
                const isActive = item.classList.contains('is-open');

                if (isActive) {
                    item.classList.remove('is-open');
                    subMenu.style.maxHeight = null;
                } else {
                    item.classList.add('is-open');
                    subMenu.style.maxHeight = subMenu.scrollHeight + "px";
                }
            });
        }
    });

    // Cart Drawer Logic
    const cartTriggers = document.querySelectorAll('.aa-cart-drawer-trigger');
    const cartDrawer = document.querySelector('.aa-cart-drawer');
    const cartCloseBtn = document.querySelector('.aa-cart-drawer-close');
    const cartOverlay = document.querySelector('.aa-cart-drawer-overlay');

    function openCart(e) {
        e.preventDefault();
        if (cartDrawer) {
            cartDrawer.classList.add('is-active');
            document.body.style.overflow = 'hidden';
        }
    }

    function closeCart() {
        if (cartDrawer) {
            cartDrawer.classList.remove('is-active');
            document.body.style.overflow = '';
        }
    }

    cartTriggers.forEach(trigger => trigger.addEventListener('click', openCart));
    if (cartCloseBtn) cartCloseBtn.addEventListener('click', closeCart);
    if (cartOverlay) cartOverlay.addEventListener('click', closeCart);

    // Mobile Search Toggle Logic
    const mobileSearchToggle = document.querySelector('.aa-mobile-search-toggle');
    const mobileSearchOverlay = document.querySelector('.aa-mobile-search-overlay');
    const mobileSearchClose = document.querySelector('.aa-mobile-search-close');
    const mobileSearchInput = document.querySelector('.aa-mobile-search-form .aa-search-input');

    if (mobileSearchToggle && mobileSearchOverlay) {
        mobileSearchToggle.addEventListener('click', function(e) {
            e.preventDefault();
            mobileSearchOverlay.classList.toggle('is-active');
            if (mobileSearchOverlay.classList.contains('is-active') && mobileSearchInput) {
                setTimeout(() => mobileSearchInput.focus(), 300);
            }
        });
    }

    if (mobileSearchClose && mobileSearchOverlay) {
        mobileSearchClose.addEventListener('click', function(e) {
            e.preventDefault();
            mobileSearchOverlay.classList.remove('is-active');
        });
    }

    // WooCommerce AJAX cart update
    if (typeof jQuery !== 'undefined') {
        jQuery('body').on('added_to_cart removed_from_cart updated_cart_totals', function() {
            // Cart updated natively by Woo
        });
        
        // Custom Qty Updater
        let qtyTimeout;
        jQuery(document).on('click', '.aa-qty-btn', function(e) {
            e.preventDefault();
            let $btn = jQuery(this);
            let key = $btn.data('cart_item_key');
            let $input = $btn.siblings('.aa-qty-input');
            let currentQty = parseInt($input.val());
            let newQty = $btn.hasClass('aa-qty-plus') ? currentQty + 1 : currentQty - 1;
            
            if (newQty < 1) return; // Min 1. Use the X button to remove entirely.
            
            $input.val(newQty);
            
            // Block UI smoothly using CSS to avoid blockUI DOM orphans
            let $drawerBody = jQuery('.aa-cart-drawer-body');
            $drawerBody.css('opacity', '0.4').css('pointer-events', 'none');
            
            // Debounce the AJAX call to handle rapid clicking smoothly
            clearTimeout(qtyTimeout);
            qtyTimeout = setTimeout(function() {
                jQuery.ajax({
                    url: aa_header_ajax.ajax_url,
                    type: 'POST',
                    data: {
                        action: 'aa_update_mini_cart_qty',
                        cart_item_key: key,
                        qty: newQty
                    },
                    success: function(response) {
                        if (response && response.fragments) {
                            // Manually replace fragments
                            jQuery.each(response.fragments, function(key, value) {
                                jQuery(key).replaceWith(value);
                            });
                            // Trigger native woo event to let themes know it updated
                            jQuery(document.body).trigger('wc_fragments_refreshed');
                        }
                        // Instantly unblock since we manually replaced the HTML
                        jQuery('.aa-cart-drawer-body').css('opacity', '1').css('pointer-events', 'auto');
                    },
                    error: function() {
                        jQuery('.aa-cart-drawer-body').css('opacity', '1').css('pointer-events', 'auto');
                    }
                });
            }, 300);
        });
    }

    // --- Sidebar Drawer Logic --- //
    const sidebarTrigger = document.querySelector('.aa-desktop-more-trigger');
    const sidebarDrawer = document.querySelector('.aa-sidebar-drawer');
    const sidebarOverlay = document.querySelector('.aa-sidebar-overlay');
    const sidebarClose = document.querySelector('.aa-sidebar-close');

    function openSidebar(e) {
        e.preventDefault();
        if (sidebarDrawer) sidebarDrawer.classList.add('is-active');
        if (sidebarOverlay) sidebarOverlay.classList.add('is-active');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar(e) {
        if (e) e.preventDefault();
        if (sidebarDrawer) sidebarDrawer.classList.remove('is-active');
        if (sidebarOverlay) sidebarOverlay.classList.remove('is-active');
        document.body.style.overflow = '';
    }

    if (sidebarTrigger) sidebarTrigger.addEventListener('click', openSidebar);
    if (sidebarClose) sidebarClose.addEventListener('click', closeSidebar);
    if (sidebarOverlay) sidebarOverlay.addEventListener('click', closeSidebar);

    // --- Live Search Logic --- //
    const searchForms = document.querySelectorAll('.aa-search-form');
    searchForms.forEach(form => {
        const input = form.querySelector('.aa-search-input');
        const suggestionsBox = form.querySelector('.aa-search-suggestions');
        let searchTimeout;

        if (input && suggestionsBox) {
            input.addEventListener('input', function() {
                const query = this.value.trim();
                
                if (query.length < 2) {
                    suggestionsBox.classList.remove('is-active');
                    suggestionsBox.innerHTML = '';
                    return;
                }

                // Show loading state
                suggestionsBox.classList.add('is-active');
                suggestionsBox.innerHTML = '<div class="aa-search-loading"><div class="aa-spinner"></div> Searching...</div>';

                clearTimeout(searchTimeout);
                searchTimeout = setTimeout(function() {
                    jQuery.ajax({
                        url: aa_header_ajax.ajax_url,
                        type: 'POST',
                        data: {
                            action: 'aa_live_search',
                            query: query
                        },
                        success: function(response) {
                            if (response.success) {
                                const results = response.data;
                                if (results.length > 0) {
                                    let html = '<ul class="aa-search-results-list">';
                                    results.forEach(item => {
                                        html += `
                                            <li>
                                                <a href="${item.url}">
                                                    <div class="aa-search-item-img"><img src="${item.image}" alt="${item.title}"></div>
                                                    <div class="aa-search-item-info">
                                                        <span class="aa-search-item-title">${item.title}</span>
                                                        <span class="aa-search-item-price">${item.price}</span>
                                                    </div>
                                                </a>
                                            </li>
                                        `;
                                    });
                                    html += '</ul>';
                                    html += `<div class="aa-search-view-all"><a href="${form.action}?s=${encodeURIComponent(query)}&post_type=product">View all results</a></div>`;
                                    suggestionsBox.innerHTML = html;
                                } else {
                                    suggestionsBox.innerHTML = '<div class="aa-search-no-results">No products found.</div>';
                                }
                            }
                        },
                        error: function() {
                            suggestionsBox.innerHTML = '<div class="aa-search-no-results">An error occurred.</div>';
                        }
                    });
                }, 400); // 400ms debounce
            });

            // Close suggestions when clicking outside
            document.addEventListener('click', function(e) {
                if (!form.contains(e.target)) {
                    suggestionsBox.classList.remove('is-active');
                }
            });
            
            // Re-open if clicking back on input
            input.addEventListener('focus', function() {
                if (this.value.trim().length >= 2 && suggestionsBox.innerHTML !== '') {
                    suggestionsBox.classList.add('is-active');
                }
            });
        }
    });

});
