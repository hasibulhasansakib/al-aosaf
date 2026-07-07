<?php 
if (!defined('ABSPATH')) exit;

get_header();

// Determine pre-selected tax if loaded via archive link
$current_cat = is_product_category() ? get_queried_object()->slug : '';
$current_tag = is_product_tag() ? get_queried_object()->slug : '';
$search_query = get_search_query();

// Get Categories
$categories = get_terms([
    'taxonomy' => 'product_cat',
    'hide_empty' => true,
    'exclude' => [get_option('default_product_cat')]
]);

// Get Tags
$tags = get_terms([
    'taxonomy' => 'product_tag',
    'hide_empty' => true
]);

?>

<div class="aa-shop-page aa-section">
    <div class="aa-container">
        
        <!-- Shop Header Hidden as requested -->
        <!--
        <div class="aa-shop-header">
            <h1 class="aa-shop-title"><?php echo is_search() ? 'Search Results for "' . esc_html($search_query) . '"' : 'Shop'; ?></h1>
            <div class="aa-shop-breadcrumbs">
                <?php woocommerce_breadcrumb(); ?>
            </div>
        </div>
        -->

        <div class="aa-shop-layout">
            
            <!-- Mobile Filter Toggle -->
            <button class="aa-mobile-filter-toggle">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="4" y1="21" x2="4" y2="14"></line><line x1="4" y1="10" x2="4" y2="3"></line><line x1="12" y1="21" x2="12" y2="12"></line><line x1="12" y1="8" x2="12" y2="3"></line><line x1="20" y1="21" x2="20" y2="16"></line><line x1="20" y1="12" x2="20" y2="3"></line><line x1="1" y1="14" x2="7" y2="14"></line><line x1="9" y1="8" x2="15" y2="8"></line><line x1="17" y1="16" x2="23" y2="16"></line></svg>
                Filters
            </button>

            <!-- Sidebar -->
            <aside class="aa-shop-sidebar">
                <div class="aa-shop-sidebar-inner">
                    <button class="aa-mobile-filter-close">&times;</button>
                    
                    <!-- Search Widget -->
                    <div class="aa-widget aa-widget-search">
                        <h3 class="aa-widget-title">Search</h3>
                        <div class="aa-search-box">
                            <input type="text" id="aa-filter-search" placeholder="Search products..." value="<?php echo esc_attr($search_query); ?>">
                            <button type="button" id="aa-filter-search-btn">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Price Filter Widget -->
                    <div class="aa-widget aa-widget-price">
                        <h3 class="aa-widget-title">PRICE RANGE</h3>
                        <div class="aa-price-filter-labels">
                            <span class="aa-price-min-label">৳ <span id="aa-min-price-text">0</span></span>
                            <span class="aa-price-max-label">৳ <span id="aa-max-price-text">10000</span></span>
                        </div>
                        <div id="aa-price-slider"></div>
                        <input type="hidden" id="aa-min-price" value="0">
                        <input type="hidden" id="aa-max-price" value="10000">
                    </div>

                    <!-- Stock Status Widget -->
                    <div class="aa-widget aa-widget-stock">
                        <h3 class="aa-widget-title">Availability</h3>
                        <ul class="aa-filter-list">
                            <li>
                                <label>
                                    <input type="checkbox" id="aa-filter-instock" value="true">
                                    <span>In Stock Only</span>
                                </label>
                            </li>
                        </ul>
                    </div>

                    <!-- Category Widget -->
                    <?php if (!empty($categories) && !is_wp_error($categories)): ?>
                    <div class="aa-widget aa-widget-categories">
                        <h3 class="aa-widget-title">Categories</h3>
                        <ul class="aa-filter-list" id="aa-filter-category">
                            <li>
                                <label>
                                    <input type="radio" name="product_cat" value="" <?php checked($current_cat, ''); ?>>
                                    <span>All Categories</span>
                                </label>
                            </li>
                            <?php foreach ($categories as $cat): ?>
                            <li>
                                <label>
                                    <input type="radio" name="product_cat" value="<?php echo esc_attr($cat->slug); ?>" <?php checked($current_cat, $cat->slug); ?>>
                                    <span><?php echo esc_html($cat->name); ?> <small>(<?php echo $cat->count; ?>)</small></span>
                                </label>
                            </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                    <?php endif; ?>

                </div>
            </aside>

            <!-- Main Content -->
            <main class="aa-shop-content">
                
                <!-- Toolbar -->
                <div class="aa-shop-toolbar">
                    <div class="aa-shop-result-count" id="aa-shop-result-count">
                        Loading...
                    </div>
                    <div class="aa-shop-ordering">
                        <select id="aa-filter-orderby" class="aa-select">
                            <option value="menu_order">Default sorting</option>
                            <option value="popularity">Sort by popularity</option>
                            <option value="date">Sort by latest</option>
                            <option value="price">Sort by price: low to high</option>
                            <option value="price-desc">Sort by price: high to low</option>
                        </select>
                    </div>
                </div>

                <!-- Product Grid Container -->
                <div class="aa-shop-grid-wrapper">
                    <!-- Skeleton Loader -->
                    <div class="aa-shop-loader" style="display:none;">
                        <div class="aa-spinner"></div>
                    </div>
                    
                    <!-- Dynamic Grid Content injected here -->
                    <div id="aa-shop-grid-content"></div>
                </div>

            </main>
        </div>
    </div>
</div>

<?php 
get_footer(); 
?>
