<?php if (!defined('ABSPATH')) exit; ?>
<div class="aa-settings-header">
    <div class="aa-settings-title-area">
        <h1><?php _e('Homepage Settings', 'al-aosaf'); ?></h1>
        <p><?php _e('Manage all homepage sections from here.', 'al-aosaf'); ?></p>
    </div>
</div>

<?php if (isset($_GET['updated']) && $_GET['updated'] === 'true'): ?>
    <div class="aa-notice aa-notice-success">
        <p><?php _e('Homepage settings saved successfully.', 'al-aosaf'); ?></p>
    </div>
<?php endif; ?>

<div class="aa-settings-tabs">
    <a href="?page=aa-homepage&tab=hero" class="aa-tab <?php echo $active_tab === 'hero' ? 'active' : ''; ?>">Hero Section</a>
    <a href="?page=aa-homepage&tab=categories" class="aa-tab <?php echo $active_tab === 'categories' ? 'active' : ''; ?>">Featured Categories</a>
    <a href="?page=aa-homepage&tab=top_selling" class="aa-tab <?php echo $active_tab === 'top_selling' ? 'active' : ''; ?>">Top Selling</a>
    <a href="?page=aa-homepage&tab=dynamic_sliders" class="aa-tab <?php echo $active_tab === 'dynamic_sliders' ? 'active' : ''; ?>">Product Sliders</a>
</div>

<div class="aa-settings-content">
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="aa_save_homepage_settings">
        <?php wp_nonce_field('aa_save_homepage_settings', 'aa_homepage_nonce'); ?>
        <input type="hidden" name="current_tab" value="<?php echo esc_attr($active_tab); ?>">

        <?php if ($active_tab === 'hero'): ?>
            <div class="aa-card">
                <h2><?php _e('Hero Slider (Left Column)', 'al-aosaf'); ?></h2>
                <p class="description"><?php _e('Add as many slides as you want. Drag and drop to reorder.', 'al-aosaf'); ?></p>
                
                <div id="aa-hero-slides-wrapper" style="margin-top: 20px;">
                    <?php 
                    $slides = $settings['hero_slides'] ?? [];
                    // Migrate old data if the new array doesn't exist
                    if (empty($slides) && !empty($settings['hero_slide_1_image'])) {
                        for($i=1; $i<=3; $i++) {
                            if (!empty($settings['hero_slide_'.$i.'_image'])) {
                                $slides[] = [
                                    'image' => $settings['hero_slide_'.$i.'_image'],
                                    'link' => $settings['hero_slide_'.$i.'_link'] ?? ''
                                ];
                            }
                        }
                    }

                    if (!empty($slides)): 
                        foreach ($slides as $index => $slide): ?>
                            <div class="aa-slide-item" style="border: 1px solid #ccc; margin-bottom: 15px; background: #fafafa; position: relative; border-radius: 4px;">
                                <div class="aa-slide-header" style="padding: 15px; background: #f0f0f1; border-bottom: 1px solid #ccc; cursor: pointer; display: flex; justify-content: space-between; align-items: center; border-radius: 4px 4px 0 0;">
                                    <h3 style="margin: 0;">Slide <span class="aa-slide-number"><?php echo $index + 1; ?></span></h3>
                                    <div>
                                        <span class="aa-slide-toggle" style="margin-right: 15px;">&#9660;</span>
                                        <span class="aa-slide-handle" style="cursor: move; font-size: 20px; color: #888;">&#x2630;</span>
                                    </div>
                                </div>
                                <div class="aa-slide-content" style="padding: 15px; display: none;">
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row"><label>Image</label></th>
                                            <td>
                                                <input name="aa_homepage_hero_slides[<?php echo $index; ?>][image]" type="text" value="<?php echo esc_attr($slide['image'] ?? ''); ?>" class="large-text aa-image-url-input" />
                                                <button class="button aa-upload-image-btn" type="button" style="margin-top: 5px;">Select Image</button>
                                                <?php if (!empty($slide['image'])): ?>
                                                    <div class="aa-image-preview" style="margin-top: 10px;">
                                                        <img src="<?php echo esc_url($slide['image']); ?>" style="max-width: 150px; height: auto;" />
                                                    </div>
                                                <?php endif; ?>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label>Link URL</label></th>
                                            <td>
                                                <input name="aa_homepage_hero_slides[<?php echo $index; ?>][link]" type="url" value="<?php echo esc_attr($slide['link'] ?? ''); ?>" class="large-text" />
                                            </td>
                                        </tr>
                                    </table>
                                    <button type="button" class="button button-link-delete aa-remove-slide" style="color: #a00; margin-top: 10px;">Remove Slide</button>
                                </div>
                            </div>
                        <?php endforeach; 
                    endif; ?>
                </div>
                
                <button type="button" id="aa-add-hero-slide" class="button button-secondary" style="margin-top: 10px;">+ Add New Slide</button>
            </div>

            <div class="aa-card" style="margin-top: 20px;">
                <h2><?php _e('Dynamic Products (Right Column)', 'al-aosaf'); ?></h2>
                <p class="description"><?php _e('Select a product category to automatically display on the right column.', 'al-aosaf'); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label>Product Category</label></th>
                        <td>
                            <select name="aa_homepage_hero_right_category">
                                <option value=""><?php _e('Select Category', 'al-aosaf'); ?></option>
                                <?php if (!empty($categories) && !is_wp_error($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <option value="<?php echo esc_attr($cat->slug); ?>" <?php selected($settings['hero_right_category'] ?? '', $cat->slug); ?>>
                                            <?php echo esc_html($cat->name); ?>
                                        </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </td>
                    </tr>
                </table>
            </div>
        <?php elseif ($active_tab === 'categories'): ?>
            <div class="aa-card">
                <h2><?php _e('Featured Categories Section', 'al-aosaf'); ?></h2>
                <p class="description"><?php _e('Configure the dynamic category slider that appears below the hero section.', 'al-aosaf'); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label>Enable Section</label></th>
                        <td>
                            <label>
                                <input type="checkbox" name="aa_homepage_featured_cats_enable" value="yes" <?php checked($settings['featured_cats_enable'] ?? 'yes', 'yes'); ?> />
                                Show Featured Categories on Homepage
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label>Section Title</label></th>
                        <td>
                            <input name="aa_homepage_featured_cats_title" type="text" value="<?php echo esc_attr($settings['featured_cats_title'] ?? 'Featured Categories'); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label>Select Categories</label></th>
                        <td>
                            <p class="description" style="margin-bottom: 10px;">Check the categories you want to feature in the slider.</p>
                            <div style="max-height: 250px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff; max-width: 400px;">
                                <?php 
                                $selected_cats = $settings['featured_cats_list'] ?? [];
                                if (!is_array($selected_cats)) $selected_cats = [];
                                
                                if (!empty($categories) && !is_wp_error($categories)): ?>
                                    <?php foreach ($categories as $cat): ?>
                                        <label style="display: block; margin-bottom: 8px;">
                                            <input type="checkbox" name="aa_homepage_featured_cats_list[]" value="<?php echo esc_attr($cat->term_id); ?>" <?php checked(in_array($cat->term_id, $selected_cats)); ?> />
                                            <?php echo esc_html($cat->name); ?> (<?php echo esc_html($cat->count); ?>)
                                        </label>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <p>No product categories found.</p>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        <?php elseif ($active_tab === 'top_selling'): ?>
            <div class="aa-card">
                <h2><?php _e('Top Selling Products', 'al-aosaf'); ?></h2>
                <p class="description"><?php _e('Configure the horizontal product cards for top sellers.', 'al-aosaf'); ?></p>
                
                <table class="form-table">
                    <tr>
                        <th scope="row"><label>Enable Section</label></th>
                        <td>
                            <label>
                                <input type="checkbox" name="aa_homepage_top_selling_enable" value="yes" <?php checked($settings['top_selling_enable'] ?? 'yes', 'yes'); ?> />
                                Show Top Selling Products on Homepage
                            </label>
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label>Section Title</label></th>
                        <td>
                            <input name="aa_homepage_top_selling_title" type="text" value="<?php echo esc_attr($settings['top_selling_title'] ?? 'Top Selling'); ?>" class="regular-text" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><label>Number of Products</label></th>
                        <td>
                            <input name="aa_homepage_top_selling_count" type="number" min="1" max="24" value="<?php echo esc_attr($settings['top_selling_count'] ?? 4); ?>" class="small-text" />
                            <p class="description">How many products to display.</p>
                        </td>
                    </tr>
                </table>
            </div>
        <?php elseif ($active_tab === 'dynamic_sliders'): ?>
            <div class="aa-card">
                <h2><?php _e('Dynamic Product Sliders', 'al-aosaf'); ?></h2>
                <p class="description"><?php _e('Create multiple product sliders and generate shortcodes to place them anywhere on your site.', 'al-aosaf'); ?></p>
                
                <div id="aa-dynamic-sliders-wrapper" style="margin-top: 20px;">
                    <?php 
                    $dynamic_sliders = $settings['dynamic_sliders'] ?? [];
                    if (!empty($dynamic_sliders)): 
                        foreach ($dynamic_sliders as $index => $slider): 
                            $shortcode_id = $index + 1;
                        ?>
                            <div class="aa-slide-item" style="border: 1px solid #ccc; margin-bottom: 15px; background: #fafafa; position: relative; border-radius: 4px;">
                                <div class="aa-slide-header" style="padding: 15px; background: #f0f0f1; border-bottom: 1px solid #ccc; cursor: pointer; display: flex; justify-content: space-between; align-items: center; border-radius: 4px 4px 0 0;">
                                    <h3 style="margin: 0;">Slider: <span class="aa-slide-number"><?php echo esc_html($slider['title'] ?? 'Slider ' . $shortcode_id); ?></span></h3>
                                    <div>
                                        <span class="aa-slide-toggle" style="margin-right: 15px;">&#9660;</span>
                                        <span class="aa-slide-handle" style="cursor: move; font-size: 20px; color: #888;">&#x2630;</span>
                                    </div>
                                </div>
                                <div class="aa-slide-content" style="padding: 15px; display: none;">
                                    <div style="background: #e6f4ea; padding: 10px; border-radius: 4px; margin-bottom: 15px; display: inline-block;">
                                        <strong>Shortcode:</strong> <code>[aa_dynamic_slider id="<?php echo $shortcode_id; ?>"]</code>
                                    </div>
                                    <table class="form-table">
                                        <tr>
                                            <th scope="row"><label>Slider Title</label></th>
                                            <td>
                                                <input name="aa_homepage_dynamic_sliders[<?php echo $index; ?>][title]" type="text" value="<?php echo esc_attr($slider['title'] ?? ''); ?>" class="regular-text aa-dynamic-title-input" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label>Select Categories</label></th>
                                            <td>
                                                <div style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff; max-width: 400px;">
                                                    <?php 
                                                    $selected_cats = $slider['categories'] ?? [];
                                                    if (!is_array($selected_cats)) $selected_cats = [];
                                                    if (!empty($categories) && !is_wp_error($categories)): 
                                                        foreach ($categories as $cat): ?>
                                                            <label style="display: block; margin-bottom: 8px;">
                                                                <input type="checkbox" name="aa_homepage_dynamic_sliders[<?php echo $index; ?>][categories][]" value="<?php echo esc_attr($cat->term_id); ?>" <?php checked(in_array($cat->term_id, $selected_cats)); ?> />
                                                                <?php echo esc_html($cat->name); ?>
                                                            </label>
                                                        <?php endforeach; 
                                                    endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label>Order By</label></th>
                                            <td>
                                                <select name="aa_homepage_dynamic_sliders[<?php echo $index; ?>][orderby]">
                                                    <option value="date" <?php selected($slider['orderby'] ?? 'date', 'date'); ?>>Recent (Newest First)</option>
                                                    <option value="sales" <?php selected($slider['orderby'] ?? 'date', 'sales'); ?>>Best Selling</option>
                                                    <option value="rand" <?php selected($slider['orderby'] ?? 'date', 'rand'); ?>>Random</option>
                                                </select>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label>Product Limit</label></th>
                                            <td>
                                                <input name="aa_homepage_dynamic_sliders[<?php echo $index; ?>][limit]" type="number" min="1" max="24" value="<?php echo esc_attr($slider['limit'] ?? 8); ?>" class="small-text" />
                                            </td>
                                        </tr>
                                        <tr>
                                            <th scope="row"><label>Autoplay</label></th>
                                            <td>
                                                <label>
                                                    <input type="checkbox" name="aa_homepage_dynamic_sliders[<?php echo $index; ?>][autoplay]" value="yes" <?php checked($slider['autoplay'] ?? 'yes', 'yes'); ?> />
                                                    Enable Autoplay
                                                </label>
                                            </td>
                                        </tr>
                                    </table>
                                    <button type="button" class="button button-link-delete aa-remove-slide" style="color: #a00; margin-top: 10px;">Remove Slider</button>
                                </div>
                            </div>
                        <?php endforeach; 
                    endif; ?>
                </div>
                
                <button type="button" id="aa-add-dynamic-slider" class="button button-secondary" style="margin-top: 10px;">+ Add New Slider</button>
            </div>
        <?php endif; ?>

        <div class="aa-settings-footer">
            <button type="submit" class="button button-primary button-hero"><?php _e('Save Settings', 'al-aosaf'); ?></button>
        </div>
    </form>
</div>

<!-- Repeater Template -->
<script type="text/template" id="aa-slide-template">
    <div class="aa-slide-item" style="border: 1px solid #ccc; margin-bottom: 15px; background: #fafafa; position: relative; border-radius: 4px;">
        <div class="aa-slide-header" style="padding: 15px; background: #f0f0f1; border-bottom: 1px solid #ccc; cursor: pointer; display: flex; justify-content: space-between; align-items: center; border-radius: 4px 4px 0 0;">
            <h3 style="margin: 0;">Slide <span class="aa-slide-number">__NUM__</span></h3>
            <div>
                <span class="aa-slide-toggle" style="margin-right: 15px;">&#9650;</span>
                <span class="aa-slide-handle" style="cursor: move; font-size: 20px; color: #888;">&#x2630;</span>
            </div>
        </div>
        <div class="aa-slide-content" style="padding: 15px; display: block;">
            <table class="form-table">
                <tr>
                    <th scope="row"><label>Image</label></th>
                    <td>
                        <input name="aa_homepage_hero_slides[__INDEX__][image]" type="text" value="" class="large-text aa-image-url-input" />
                        <button class="button aa-upload-image-btn" type="button" style="margin-top: 5px;">Select Image</button>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label>Link URL</label></th>
                    <td>
                        <input name="aa_homepage_hero_slides[__INDEX__][link]" type="url" value="" class="large-text" />
                    </td>
                </tr>
            </table>
            <button type="button" class="button button-link-delete aa-remove-slide" style="color: #a00; margin-top: 10px;">Remove Slide</button>
        </div>
    </div>
</script>

<!-- Dynamic Slider Repeater Template -->
<script type="text/template" id="aa-dynamic-slider-template">
    <div class="aa-slide-item" style="border: 1px solid #ccc; margin-bottom: 15px; background: #fafafa; position: relative; border-radius: 4px;">
        <div class="aa-slide-header" style="padding: 15px; background: #f0f0f1; border-bottom: 1px solid #ccc; cursor: pointer; display: flex; justify-content: space-between; align-items: center; border-radius: 4px 4px 0 0;">
            <h3 style="margin: 0;">Slider: <span class="aa-slide-number">New Slider</span></h3>
            <div>
                <span class="aa-slide-toggle" style="margin-right: 15px;">&#9650;</span>
                <span class="aa-slide-handle" style="cursor: move; font-size: 20px; color: #888;">&#x2630;</span>
            </div>
        </div>
        <div class="aa-slide-content" style="padding: 15px; display: block;">
            <div style="background: #e6f4ea; padding: 10px; border-radius: 4px; margin-bottom: 15px; display: inline-block;">
                <strong>Shortcode:</strong> <code>[aa_dynamic_slider id="__NUM__"]</code>
                <p style="margin: 5px 0 0; font-size: 12px; color: #555;">Save settings first to lock in this ID.</p>
            </div>
            <table class="form-table">
                <tr>
                    <th scope="row"><label>Slider Title</label></th>
                    <td>
                        <input name="aa_homepage_dynamic_sliders[__INDEX__][title]" type="text" value="New Slider" class="regular-text aa-dynamic-title-input" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label>Select Categories</label></th>
                    <td>
                        <div style="max-height: 150px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; background: #fff; max-width: 400px;">
                            <?php 
                            if (!empty($categories) && !is_wp_error($categories)): 
                                foreach ($categories as $cat): ?>
                                    <label style="display: block; margin-bottom: 8px;">
                                        <input type="checkbox" name="aa_homepage_dynamic_sliders[__INDEX__][categories][]" value="<?php echo esc_attr($cat->term_id); ?>" />
                                        <?php echo esc_html($cat->name); ?>
                                    </label>
                                <?php endforeach; 
                            endif; ?>
                        </div>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label>Order By</label></th>
                    <td>
                        <select name="aa_homepage_dynamic_sliders[__INDEX__][orderby]">
                            <option value="date">Recent (Newest First)</option>
                            <option value="sales">Best Selling</option>
                            <option value="rand">Random</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label>Product Limit</label></th>
                    <td>
                        <input name="aa_homepage_dynamic_sliders[__INDEX__][limit]" type="number" min="1" max="24" value="8" class="small-text" />
                    </td>
                </tr>
                <tr>
                    <th scope="row"><label>Autoplay</label></th>
                    <td>
                        <label>
                            <input type="checkbox" name="aa_homepage_dynamic_sliders[__INDEX__][autoplay]" value="yes" checked="checked" />
                            Enable Autoplay
                        </label>
                    </td>
                </tr>
            </table>
            <button type="button" class="button button-link-delete aa-remove-slide" style="color: #a00; margin-top: 10px;">Remove Slider</button>
        </div>
    </div>
</script>
