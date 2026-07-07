<div class="aa-advanced-hero">
    <div class="aa-container aa-hero-grid">
        
        <!-- Left Column: Slider -->
        <div class="aa-hero-left">
            <?php if (!empty($slides)): ?>
                <div class="swiper aa-hero-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($slides as $slide): ?>
                            <div class="swiper-slide">
                                <?php if (!empty($slide['link'])): ?>
                                    <a href="<?php echo esc_url($slide['link']); ?>" class="aa-slide-link">
                                        <img src="<?php echo esc_url($slide['image']); ?>" alt="Hero Slide" />
                                    </a>
                                <?php else: ?>
                                    <img src="<?php echo esc_url($slide['image']); ?>" alt="Hero Slide" />
                                <?php endif; ?>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <!-- Navigation Arrows -->
                    <div class="swiper-button-prev aa-hero-prev">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"></line><polyline points="12 19 5 12 12 5"></polyline></svg>
                    </div>
                    <div class="swiper-button-next aa-hero-next">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                    </div>
                    <!-- Pagination Dots -->
                    <div class="swiper-pagination aa-hero-pagination"></div>
                </div>
            <?php else: ?>
                <div class="aa-hero-placeholder">
                    <p>Please add slides in Al Aosaf -> Homepage settings.</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Right Column: Dynamic Products -->
        <div class="aa-hero-right">
            <?php if (!empty($right_products)): ?>
                <div class="swiper aa-right-product-swiper">
                    <div class="swiper-wrapper">
                        <?php foreach ($right_products as $prod): ?>
                            <div class="swiper-slide aa-right-prod-slide">
                                <a href="<?php echo esc_url($prod['url']); ?>" class="aa-right-prod-link" title="<?php echo esc_attr($prod['title']); ?>">
                                    <img src="<?php echo esc_url($prod['image']); ?>" alt="<?php echo esc_attr($prod['title']); ?>" />
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php else: ?>
                <div class="aa-hero-placeholder">
                    <p>No products found or category not selected.</p>
                </div>
            <?php endif; ?>
        </div>

    </div>
</div>
