<?php
if (!defined('ABSPATH')) {
    exit;
}
?>
<style>
    .aa-admin-wrap { margin-top: 20px; font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif; }
    .aa-premium-header {
        background: linear-gradient(135deg, #111111 0%, #1a1a1a 100%);
        border-radius: 8px;
        padding: 24px 30px;
        margin-bottom: 25px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 10px 30px rgba(0,0,0,0.15);
        border-left: 4px solid var(--aa-primary, #C8A15A);
    }
    .aa-premium-header h1 {
        margin: 0;
        padding: 0;
        color: #ffffff;
        font-size: 24px;
        font-weight: 600;
        letter-spacing: -0.5px;
    }
    .aa-premium-breadcrumb {
        font-size: 13px;
        color: #A0A0A0;
        display: flex;
        align-items: center;
        gap: 8px;
        font-weight: 500;
    }
    .aa-premium-breadcrumb a {
        color: var(--aa-primary, #C8A15A);
        text-decoration: none;
        transition: color 0.2s ease;
    }
    .aa-premium-breadcrumb a:hover {
        color: var(--aa-primary-hover, #E0B96D);
    }
    .aa-premium-breadcrumb svg {
        width: 14px;
        height: 14px;
        opacity: 0.6;
    }
    .aa-admin-content {
        animation: aaFadeIn 0.4s ease-out forwards;
    }
    @keyframes aaFadeIn {
        from { opacity: 0; transform: translateY(5px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>

<div class="wrap aa-admin-wrap">
    <div class="aa-premium-header">
        <div class="aa-header-left">
            <h1>Al Aosaf</h1>
        </div>
        
        <?php if (!empty($page_title) && $page_title !== 'Dashboard'): ?>
            <div class="aa-premium-breadcrumb">
                <a href="?page=aa-dashboard">Dashboard</a>
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
                <span style="color: #ffffff;"><?php echo esc_html($page_title); ?></span>
            </div>
        <?php endif; ?>
    </div>
    <div class="aa-admin-content">
