<?php 
if (!defined('ABSPATH')) exit;

if ($query->have_posts()): ?>
    <div class="aa-shop-product-grid">
        <?php while ($query->have_posts()): $query->the_post(); 
            $product = wc_get_product(get_the_ID());
            if (!$product) continue;
            $card_path = AA_PLUGIN_DIR . 'modules/Homepage/Views/frontend/components/product-card-vertical.php';
            if (file_exists($card_path)) {
                include $card_path;
            }
        endwhile; ?>
    </div>

    <?php 
    // Pagination
    $total_pages = $query->max_num_pages;
    if ($total_pages > 1): 
        $current_page = max(1, $args['paged']);
    ?>
        <div class="aa-shop-pagination">
            <?php 
            echo paginate_links([
                'base' => '%_%',
                'format' => '?paged=%#%',
                'current' => $current_page,
                'total' => $total_pages,
                'prev_text' => '&larr;',
                'next_text' => '&rarr;',
                'type' => 'list',
                'end_size' => 1,
                'mid_size' => 1
            ]);
            ?>
        </div>
    <?php endif; ?>

<?php else: ?>
    <div class="aa-shop-no-results">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
        <h3>No products found</h3>
        <p>Try adjusting your filters or search query to find what you're looking for.</p>
        <button id="aa-clear-filters-btn" class="aa-btn aa-btn-primary">Clear All Filters</button>
    </div>
<?php endif; ?>
