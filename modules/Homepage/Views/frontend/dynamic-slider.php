<?php if (!defined('ABSPATH')) exit; ?>
<div class="aa-section aa-dynamic-slider-section">
    <div class="aa-container">
        
        <?php if (!empty($title) || !empty($view_all_url)): ?>
            <div class="aa-section-header aa-flex-header">
                <?php if (!empty($title)): ?>
                    <h2 class="aa-section-title"><?php echo esc_html($title); ?></h2>
                <?php endif; ?>
                <?php if (!empty($view_all_url)): ?>
                    <a href="<?php echo esc_url($view_all_url); ?>" class="aa-view-all-btn">View All</a>
                <?php endif; ?>
            </div>
        <?php endif; ?>

        <?php if ($products->have_posts()): ?>
            <div class="aa-dynamic-slider-wrapper" style="position: relative;">
                <div class="swiper aa-dynamic-swiper" data-autoplay="<?php echo $autoplay ? 'true' : 'false'; ?>">
                    <div class="swiper-wrapper">
                        <?php while ($products->have_posts()): $products->the_post(); ?>
                            <div class="swiper-slide">
                                <?php 
                                $card_path = AA_PLUGIN_DIR . 'modules/Homepage/Views/frontend/components/product-card-vertical.php';
                                if (file_exists($card_path)) {
                                    include $card_path;
                                }
                                ?>
                            </div>
                        <?php endwhile; ?>
                    </div>
                    <!-- Pagination -->
                    <div class="swiper-pagination aa-dynamic-pagination"></div>
                </div>
                <!-- Navigation -->
                <div class="swiper-button-prev aa-dynamic-prev"></div>
                <div class="swiper-button-next aa-dynamic-next"></div>
            </div>
        <?php else: ?>
            <div class="aa-placeholder">
                <p><?php _e('No products found for this slider.', 'al-aosaf'); ?></p>
            </div>
        <?php endif; ?>

    </div>
</div>
