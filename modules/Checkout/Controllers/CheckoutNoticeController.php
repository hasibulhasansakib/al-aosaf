<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Checkout\Controllers;

use Alaosaf\Base\AbstractController;

class CheckoutNoticeController extends AbstractController {
    
    public function init(): void {
        // Hook into the very top of the checkout form
        add_action('woocommerce_before_checkout_form', [$this, 'renderCheckoutNotice'], 5);
        add_action('woocommerce_after_checkout_form', [$this, 'renderPostCheckoutInstructions'], 5);
        add_action('wp_footer', [$this, 'renderNoticeScripts']);
        add_action('wp_head', [$this, 'renderNoticeStyles']);
        add_action('wp_head', [$this, 'injectInlineFallbackCSS'], 999);
    }

    public function injectInlineFallbackCSS(): void {
        if (!is_checkout() || is_order_received_page()) return;
        ?>
        <style>
        /* INLINE FALLBACK - STRIP ELEMENTOR COLUMNS */
        body .elementor-widget-woocommerce-checkout-page .elementor-widget-container,
        body .elementor-widget-woocommerce-checkout-page .e-checkout__container,
        body .elementor-widget-woocommerce-checkout-page .e-checkout__column,
        body .elementor-widget-woocommerce-checkout-page .e-checkout__column-start,
        body .elementor-widget-woocommerce-checkout-page .e-checkout__column-end,
        body .elementor-widget-woocommerce-checkout-page .e-checkout__column-inner,
        body .elementor-widget-woocommerce-checkout-page .e-checkout__order-review,
        body .elementor-widget-woocommerce-checkout-page .elementor-column,
        body .elementor-widget-woocommerce-checkout-page .elementor-widget-wrap,
        body .woocommerce-checkout #customer_details .col-1,
        body .woocommerce-checkout #customer_details .col-2,
        body .woocommerce-checkout .woocommerce {
            border: none !important;
            box-shadow: none !important;
            background: transparent !important;
        }

        /* INLINE FALLBACK - GUARANTEED TO LOAD */
        body .elementor-widget-woocommerce-checkout-page .elementor-widget-container .woocommerce form.checkout h3,
        body .woocommerce-checkout h3 {
            font-size: 22px !important;
            font-weight: 800 !important;
            color: #111 !important;
            margin-bottom: 24px !important;
            border-bottom: none !important;
            padding-bottom: 0 !important;
            margin-top: 0 !important;
        }

        body .elementor-widget-woocommerce-checkout-page .elementor-widget-container .woocommerce form.checkout .form-row label,
        body .woocommerce-checkout .form-row label {
            font-size: 13px !important;
            font-weight: 600 !important;
            color: #6b7280 !important;
            margin-bottom: 6px !important;
            display: block !important;
        }

        body .elementor-widget-woocommerce-checkout-page .elementor-widget-container .woocommerce form.checkout .form-row label abbr.required,
        body .woocommerce-checkout .form-row label abbr.required {
            color: #f87171 !important;
            text-decoration: none !important;
            border: none !important;
            font-weight: 700 !important;
            margin-left: 2px !important;
        }

        body .elementor-widget-woocommerce-checkout-page .elementor-widget-container .woocommerce form.checkout .form-row input.input-text,
        body .elementor-widget-woocommerce-checkout-page .elementor-widget-container .woocommerce form.checkout .form-row input[type="text"],
        body .elementor-widget-woocommerce-checkout-page .elementor-widget-container .woocommerce form.checkout .form-row input[type="tel"],
        body .elementor-widget-woocommerce-checkout-page .elementor-widget-container .woocommerce form.checkout .form-row input[type="email"],
        body .woocommerce-checkout .form-row input.input-text,
        body .woocommerce-checkout .form-row input[type="text"] {
            width: 100% !important;
            background: #f8fafc !important;
            border: 1px solid #cbd5e1 !important;
            border-radius: 10px !important;
            padding: 14px 16px !important;
            font-size: 15px !important;
            color: #111 !important;
            box-shadow: inset 0 2px 4px rgba(0, 0, 0, 0.02) !important;
        }
        </style>
        <?php
    }

    public function renderCheckoutNotice(): void {
        $enabled = get_option('aa_checkout_notice_enabled', 'no');
        if ($enabled !== 'yes') {
            return;
        }

        $content = get_option('aa_checkout_notice_content', '');
        if (empty($content)) {
            return;
        }

        ?>
        <div class="aa-checkout-notice-wrapper" id="aa-checkout-notice">
            <div class="aa-checkout-notice-inner">
                <div class="aa-checkout-notice-content">
                    <?php echo wp_kses_post($content); ?>
                </div>
                <div class="aa-checkout-notice-overlay"></div>
            </div>
            <button type="button" class="aa-checkout-notice-toggle" aria-expanded="false">
                <svg class="aa-arrow-down" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"></polyline></svg>
                <svg class="aa-arrow-up" xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><polyline points="18 15 12 9 6 15"></polyline></svg>
            </button>
        </div>
        <?php
    }

    public function renderNoticeStyles(): void {
        if (!is_checkout() || is_wc_endpoint_url('order-received')) {
            return;
        }

        $enabled = get_option('aa_checkout_notice_enabled', 'no');
        if ($enabled !== 'yes') return;
        
        $height = get_option('aa_checkout_notice_height', 120);

        ?>
        <style>
        .aa-checkout-notice-wrapper {
            margin-bottom: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid rgba(0,0,0,0.05);
            position: relative;
            overflow: hidden;
            transition: all 0.3s ease;
        }
        
        .aa-checkout-notice-inner {
            position: relative;
            max-height: <?php echo esc_attr($height); ?>px;
            overflow: hidden;
            transition: max-height 0.4s ease-in-out;
        }

        .aa-checkout-notice-wrapper.is-expanded .aa-checkout-notice-inner {
            max-height: 2000px; /* Arbitrary large value to allow full expansion */
        }

        /* Compressed text styling */
        .aa-checkout-notice-content {
            font-size: 13px !important;
            line-height: 1.4 !important;
            padding-bottom: 20px;
        }
        
        .aa-checkout-notice-content p {
            margin-bottom: 8px !important;
        }

        /* The gradient overlay that fades the text out at the bottom */
        .aa-checkout-notice-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            width: 100%;
            height: 60px;
            background: linear-gradient(to bottom, rgba(255,255,255,0) 0%, rgba(255,255,255,1) 90%);
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .aa-checkout-notice-wrapper.is-expanded .aa-checkout-notice-overlay {
            opacity: 0;
        }

        /* The Toggle Button */
        .aa-checkout-notice-toggle {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 100%;
            background: #f9fafb;
            border: none;
            border-top: 1px solid #f3f4f6;
            padding: 8px 0;
            cursor: pointer;
            color: #6b7280;
            transition: background 0.2s, color 0.2s;
            position: relative;
            z-index: 2;
        }

        .aa-checkout-notice-toggle:hover {
            background: #f3f4f6;
            color: #111;
        }

        .aa-checkout-notice-wrapper.is-expanded .aa-arrow-down {
            display: none;
        }
        .aa-checkout-notice-wrapper.is-expanded .aa-arrow-up {
            display: block !important;
        }
        </style>
        <?php
    }

    public function renderNoticeScripts(): void {
        if (!is_checkout() || is_wc_endpoint_url('order-received')) {
            return;
        }

        $enabled = get_option('aa_checkout_notice_enabled', 'no');
        if ($enabled !== 'yes') return;

        ?>
        <script>
        document.addEventListener('DOMContentLoaded', function() {
            var toggleBtn = document.querySelector('.aa-checkout-notice-toggle');
            var wrapper = document.querySelector('.aa-checkout-notice-wrapper');
            
            if (toggleBtn && wrapper) {
                toggleBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    wrapper.classList.toggle('is-expanded');
                    
                    var expanded = wrapper.classList.contains('is-expanded');
                    toggleBtn.setAttribute('aria-expanded', expanded);
                });
            }
        });
        </script>
        <?php
    }

    public function renderPostCheckoutInstructions(): void {
        if (!is_checkout() || is_order_received_page()) return;
        
        $pci_enabled = get_option('aa_checkout_pci_enabled', 'yes');
        if ($pci_enabled !== 'yes') return;

        $def_pci_items = "<strong>Water Resistant</strong> ফিচার ঠিক আছে কিনা\nচেইন সঠিকভাবে কাজ করছে কিনা\nঘড়ি সচল ও সঠিকভাবে চলছে কিনা\nপণ্যের অন্যান্য সকল ফিচার ও অবস্থা";
        $pci_header = get_option('aa_checkout_pci_header', 'গুরুত্বপূর্ণ নির্দেশনা');
        $pci_intro = get_option('aa_checkout_pci_intro', 'ডেলিভারি ম্যানের উপস্থিতিতে পন্যটি ভালোভাবে পরীক্ষা করে সন্তুষ্ট হওয়ার পরই মূল্য পরিশোধ করুন।');
        $pci_list_title = get_option('aa_checkout_pci_list_title', 'অবশ্যই নিচের বিষয়গুলো ভালোভাবে চেক করে নিন:');
        $pci_list_items = get_option('aa_checkout_pci_list_items', $def_pci_items);
        $pci_warning = get_option('aa_checkout_pci_warning', 'ডেলিভারি ম্যান চলে যাওয়ার পর কোনো অভিযোগ গ্রহণযোগ্য হবে না।');

        // Parse items into array
        $items = array_filter(array_map('trim', explode("\n", $pci_list_items)));
        ?>
        <div class="aa-pci-container">
            <div class="aa-pci-header">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="8" x2="12" y2="12"></line><line x1="12" y1="16" x2="12.01" y2="16"></line></svg>
                <h3><?php echo esc_html($pci_header); ?></h3>
            </div>
            <div class="aa-pci-body">
                <?php if (!empty($pci_intro)): ?>
                    <p class="aa-pci-intro"><?php echo nl2br(esc_html($pci_intro)); ?></p>
                <?php endif; ?>
                
                <?php if (!empty($items)): ?>
                <div class="aa-pci-checklist-box">
                    <?php if (!empty($pci_list_title)): ?>
                        <h4><?php echo esc_html($pci_list_title); ?></h4>
                    <?php endif; ?>
                    <ul class="aa-pci-checklist">
                        <?php foreach ($items as $item): ?>
                        <li>
                            <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#f38624" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"></polyline></svg>
                            <span><?php echo wp_kses_post($item); ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endif; ?>

                <?php if (!empty($pci_warning)): ?>
                <div class="aa-pci-warning">
                    <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                    <span><?php echo nl2br(esc_html($pci_warning)); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <style>
        .aa-pci-container {
            max-width: 1100px;
            margin: 40px auto;
            background: #ffffff;
            border-radius: 16px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 10px 30px rgba(0,0,0,0.04);
            overflow: hidden;
            font-family: inherit;
        }
        .aa-pci-header {
            background: linear-gradient(135deg, #f38624 0%, #e0771c 100%);
            padding: 20px 25px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #ffffff;
        }
        .aa-pci-header h3 {
            margin: 0 !important;
            padding: 0 !important;
            font-size: 18px !important;
            font-weight: 700 !important;
            color: #ffffff !important;
            border: none !important;
            line-height: 1 !important;
        }
        .aa-pci-body {
            padding: 30px;
        }
        .aa-pci-intro {
            font-size: 16px;
            font-weight: 600;
            color: #1f2937;
            margin-bottom: 25px;
            margin-top: 0;
            line-height: 1.6;
        }
        .aa-pci-checklist-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 25px;
        }
        .aa-pci-checklist-box h4 {
            font-size: 15px !important;
            font-weight: 700 !important;
            color: #334155 !important;
            margin-bottom: 20px !important;
            margin-top: 0 !important;
            border: none !important;
            padding: 0 !important;
            line-height: 1.4 !important;
        }
        ul.aa-pci-checklist {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        ul.aa-pci-checklist li {
            display: flex;
            align-items: flex-start;
            gap: 12px;
            margin-bottom: 16px;
            font-size: 15px;
            color: #1e293b;
            font-weight: 500;
            line-height: 1.5;
        }
        ul.aa-pci-checklist li:last-child {
            margin-bottom: 0;
        }
        ul.aa-pci-checklist li svg {
            flex-shrink: 0;
            margin-top: 2px;
        }
        .aa-pci-warning {
            margin-top: 25px;
            padding: 16px 20px;
            background: #fef2f2;
            border-left: 4px solid #ef4444;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 12px;
            color: #b91c1c;
            font-size: 14px;
            font-weight: 700;
        }
        .aa-pci-warning svg {
            flex-shrink: 0;
        }
        @media (max-width: 768px) {
            .aa-pci-container {
                margin: 30px 0;
                border-radius: 12px;
            }
            .aa-pci-body {
                padding: 20px;
            }
            .aa-pci-header {
                padding: 15px 20px;
            }
            .aa-pci-checklist-box {
                padding: 20px;
            }
        }
        </style>
        <?php
    }
}
