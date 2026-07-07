jQuery(document).ready(function($) {
    
    // Elements
    const $gridContent = $('#aa-shop-grid-content');
    const $loader = $('.aa-shop-loader');
    const $resultCount = $('#aa-shop-result-count');
    const $paginationContainer = $('.aa-shop-pagination');
    
    let currentAjax = null;

    function fetchProducts(page = 1, updateUrl = true) {
        if (currentAjax) {
            currentAjax.abort();
        }

        $loader.fadeIn(200);

        // Gather Filters
        const category = $('input[name="product_cat"]:checked').val() || '';
        const tag = $('input[name="product_tag"]:checked').val() || '';
        const search = $('#aa-filter-search').val() || '';
        const orderby = $('#aa-filter-orderby').val() || 'menu_order';
        const instock = $('#aa-filter-instock').is(':checked') ? 'true' : 'false';
        const min_price = $('#aa-min-price').val() || '';
        const max_price = $('#aa-max-price').val() || '';

        // Build State/URL
        const params = new URLSearchParams();
        if (category) params.set('category', category);
        if (tag) params.set('tag', tag);
        if (search) params.set('search', search);
        if (orderby !== 'menu_order') params.set('orderby', orderby);
        if (instock === 'true') params.set('instock', 'true');
        if (min_price && min_price !== '0') params.set('min_price', min_price);
        if (max_price && max_price !== '10000') params.set('max_price', max_price);
        if (page > 1) params.set('paged', page);

        if (updateUrl) {
            const newUrl = window.location.pathname + '?' + params.toString();
            window.history.pushState({ path: newUrl }, '', newUrl);
        }

        currentAjax = $.ajax({
            url: aaShopAjax.ajax_url,
            type: 'POST',
            data: {
                action: 'aa_filter_shop',
                nonce: aaShopAjax.nonce,
                category: category,
                tag: tag,
                search: search,
                orderby: orderby,
                instock: instock,
                min_price: min_price,
                max_price: max_price,
                paged: page,
                posts_per_page: 16
            },
            success: function(response) {
                if (response.success) {
                    $gridContent.html(response.data.html);
                    $resultCount.text(response.data.result_text);
                    
                    // Re-bind pagination links
                    bindPagination();
                    
                    // Scroll to top
                    if ($('.aa-shop-layout').length) {
                        $('html, body').animate({
                            scrollTop: $('.aa-shop-layout').offset().top - 50
                        }, 500);
                    }
                }
            },
            complete: function() {
                $loader.fadeOut(200);
            }
        });
    }

    function bindPagination() {
        $('.aa-shop-pagination a').on('click', function(e) {
            e.preventDefault();
            const url = new URL($(this).attr('href'), window.location.origin);
            const paged = url.searchParams.get('paged') || url.pathname.match(/\/page\/(\d+)/)?.[1] || 1;
            fetchProducts(paged);
        });
    }

    // Event Listeners
    
    // Category Change
    $('input[name="product_cat"]').on('change', function() {
        fetchProducts(1);
    });

    // Tag Change
    $('input[name="product_tag"]').on('change', function() {
        $('.aa-tag-label').removeClass('active');
        $(this).closest('label').addClass('active');
        fetchProducts(1);
    });

    // Stock Status Change
    $('#aa-filter-instock').on('change', function() {
        fetchProducts(1);
    });

    // Order By Change
    $('#aa-filter-orderby').on('change', function() {
        fetchProducts(1);
    });

    // Search Trigger (Enter or Button)
    $('#aa-filter-search').on('keypress', function(e) {
        if (e.which === 13) {
            e.preventDefault();
            fetchProducts(1);
        }
    });
    $('#aa-filter-search-btn').on('click', function(e) {
        e.preventDefault();
        fetchProducts(1);
    });

    // Clear All Filters
    $(document).on('click', '#aa-clear-filters-btn', function() {
        $('input[name="product_cat"][value=""]').prop('checked', true);
        $('input[name="product_tag"]').prop('checked', false);
        $('.aa-tag-label').removeClass('active');
        $('#aa-filter-search').val('');
        $('#aa-filter-instock').prop('checked', false);
        $('#aa-filter-orderby').val('menu_order');
        
        // Reset Slider
        if ($.fn.slider) {
            $('#aa-price-slider').slider('values', [0, 10000]);
            $('#aa-min-price').val(0);
            $('#aa-max-price').val(10000);
            $('#aa-min-price-text').text(0);
            $('#aa-max-price-text').text(10000);
        }

        fetchProducts(1);
    });

    // Initialize Price Slider
    if ($.fn.slider) {
        let timer;
        $('#aa-price-slider').slider({
            range: true,
            min: 0,
            max: 10000,
            values: [0, 10000],
            slide: function(event, ui) {
                $('#aa-min-price-text').text(ui.values[0]);
                $('#aa-max-price-text').text(ui.values[1]);
                $('#aa-min-price').val(ui.values[0]);
                $('#aa-max-price').val(ui.values[1]);
            },
            change: function(event, ui) {
                clearTimeout(timer);
                timer = setTimeout(function() {
                    fetchProducts(1);
                }, 300);
            }
        });
    }

    // History Back/Forward handling
    window.addEventListener('popstate', function(e) {
        // Simple reload for now on back/forward to ensure state sync, 
        // or parse URL params and trigger fetch. We'll just reload.
        window.location.reload();
    });

    // Initial Fetch (on page load to get first batch)
    fetchProducts(1, false);

    // Mobile Sidebar Toggle
    $('.aa-mobile-filter-toggle').on('click', function() {
        $('.aa-shop-sidebar').addClass('active');
    });
    $('.aa-mobile-filter-close').on('click', function() {
        $('.aa-shop-sidebar').removeClass('active');
    });
});
