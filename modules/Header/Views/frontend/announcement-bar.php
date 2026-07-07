<?php
if (!defined('ABSPATH')) exit;
use Alaosaf\Helpers\Brand;

$text = $this->settings['announcement_text'] ?? '';
if (empty($text)) return;
?>
<div class="aa-announcement-bar aa-hide-on-mobile" style="background: var(--aa-primary, #C8A15A); color: #fff; padding: 8px 0; font-size: 13px;">
    <div class="aa-container" style="display: flex; justify-content: space-between; align-items: center;">
        
        <!-- Left: Contact Info -->
        <div class="aa-announcement-left" style="display: flex; gap: 20px; flex-shrink: 0; align-items: center; font-weight: 500;">
            <span style="display: flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                <?php echo esc_html(Brand::phone()); ?>
            </span>
            <span style="display: flex; align-items: center; gap: 6px;">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                <?php echo esc_html(Brand::address() ?: 'Dhaka, Bangladesh'); ?>
            </span>
        </div>

        <!-- Center: Moving Announcement -->
        <div class="aa-announcement-center" style="flex-grow: 1; overflow: hidden; white-space: nowrap; margin: 0 30px; border-left: 1px solid rgba(255,255,255,0.2); border-right: 1px solid rgba(255,255,255,0.2);">
            <div class="aa-marquee">
                <p style="margin: 0; display: inline-block; padding-left: 100%; animation: aaMarquee 20s linear infinite; font-weight: 600; letter-spacing: 0.5px; text-transform: uppercase;">
                    <?php echo esc_html($text); ?>
                </p>
            </div>
        </div>

        <!-- Right: Social Media -->
        <div class="aa-announcement-right" style="display: flex; gap: 15px; flex-shrink: 0; align-items: center;">
            <?php if (Brand::facebook()): ?>
                <a href="<?php echo esc_url(Brand::facebook()); ?>" style="color: #fff; text-decoration: none; display: flex; align-items: center; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.7" onmouseout="this.style.opacity=1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path></svg>
                </a>
            <?php endif; ?>
            <?php if (Brand::instagram()): ?>
                <a href="<?php echo esc_url(Brand::instagram()); ?>" style="color: #fff; text-decoration: none; display: flex; align-items: center; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.7" onmouseout="this.style.opacity=1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path><line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line></svg>
                </a>
            <?php endif; ?>
            <?php if (Brand::twitter()): ?>
                <a href="<?php echo esc_url(Brand::twitter()); ?>" style="color: #fff; text-decoration: none; display: flex; align-items: center; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.7" onmouseout="this.style.opacity=1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M23 3a10.9 10.9 0 0 1-3.14 1.53 4.48 4.48 0 0 0-7.86 3v1A10.66 10.66 0 0 1 3 4s-4 9 5 13a11.64 11.64 0 0 1-7 2c9 5 20 0 20-11.5a4.5 4.5 0 0 0-.08-.83A7.72 7.72 0 0 0 23 3z"></path></svg>
                </a>
            <?php endif; ?>
            <?php if (Brand::youtube()): ?>
                <a href="<?php echo esc_url(Brand::youtube()); ?>" style="color: #fff; text-decoration: none; display: flex; align-items: center; transition: opacity 0.2s;" onmouseover="this.style.opacity=0.7" onmouseout="this.style.opacity=1">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22.54 6.42a2.78 2.78 0 0 0-1.94-2C18.88 4 12 4 12 4s-6.88 0-8.6.42a2.78 2.78 0 0 0-1.94 2C1 8.13 1 12 1 12s0 3.87.46 5.58a2.78 2.78 0 0 0 1.94 2C5.12 20 12 20 12 20s6.88 0 8.6-.42a2.78 2.78 0 0 0 1.94-2C23 15.87 23 12 23 12s0-3.87-.46-5.58z"></path><polygon points="9.75 15.02 15.5 12 9.75 8.98 9.75 15.02"></polygon></svg>
                </a>
            <?php endif; ?>
        </div>
        
    </div>
</div>
