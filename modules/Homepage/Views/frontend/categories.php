<?php if (!defined('ABSPATH')) exit; ?>
<div class="aa-section aa-featured-categories">
    <div class="aa-container">
        
        <?php if (!empty($title)): ?>
            <div class="aa-section-header">
                <h2 class="aa-section-title"><?php echo esc_html($title); ?></h2>
            </div>
        <?php endif; ?>

        <?php if (!empty($categories_data)): ?>
            <div class="swiper aa-categories-swiper">
                <div class="swiper-wrapper">
                    <?php foreach ($categories_data as $cat): ?>
                        <div class="swiper-slide aa-category-slide">
                            <a href="<?php echo esc_url($cat['url']); ?>" class="aa-category-card">
                                <div class="aa-category-img-wrapper">
                                    <img src="<?php echo esc_url($cat['image']); ?>" alt="<?php echo esc_attr($cat['name']); ?>" />
                                </div>
                                <h3 class="aa-category-name"><?php echo esc_html($cat['name']); ?></h3>
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
                <!-- Navigation -->
                <div class="swiper-button-prev aa-cat-prev"></div>
                <div class="swiper-button-next aa-cat-next"></div>
                <!-- Pagination -->
                <div class="swiper-pagination aa-cat-pagination"></div>
            </div>
        <?php else: ?>
            <div class="aa-placeholder">
                <p><?php _e('Please select categories in Al Aosaf -> Homepage settings.', 'al-aosaf'); ?></p>
            </div>
        <?php endif; ?>

    </div>
</div>
