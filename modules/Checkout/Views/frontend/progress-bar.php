<?php
/**
 * Checkout Progress Bar Template
 * 
 * @var int $current_step The current active step (1, 2, or 3)
 * @var array $steps The array of steps
 */
?>

<div class="aa-checkout-progress-wrapper">
    <?php if ($current_step === 1): ?>
        <div class="aa-checkout-header">
            <h2>Review Your Cart</h2>
            <p>You're almost there! Review your items before proceeding to checkout.</p>
        </div>
    <?php elseif ($current_step === 2): ?>
        <div class="aa-checkout-header">
            <h2>Secure Checkout</h2>
            <p>Enter your details to complete your order.</p>
        </div>
    <?php elseif ($current_step === 3): ?>
        <div class="aa-checkout-header aa-success-header">
            <div class="aa-success-animation">
                <svg class="checkmark" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 52 52">
                    <circle class="checkmark__circle" cx="26" cy="26" r="25" fill="none"/>
                    <path class="checkmark__check" fill="none" d="M14.1 27.2l7.1 7.2 16.7-16.8"/>
                </svg>
            </div>
            <h2>Order Confirmed!</h2>
            <p>Your order has been successfully placed and is being prepared for delivery.</p>
        </div>
    <?php endif; ?>

    <div class="aa-checkout-progress-bar">
        <?php foreach ($steps as $step_num => $step): ?>
            <?php 
                $status_class = '';
                if ($step_num < $current_step) {
                    $status_class = 'is-complete';
                } elseif ($step_num === $current_step) {
                    $status_class = 'is-active';
                }
            ?>
            <div class="aa-progress-step <?php echo esc_attr($status_class); ?>">
                <div class="aa-step-icon">
                    <?php echo $step['icon']; ?>
                </div>
                <div class="aa-step-content">
                    <span class="aa-step-title"><?php echo esc_html($step['title']); ?></span>
                    <span class="aa-step-desc"><?php echo esc_html($step['desc']); ?></span>
                </div>
            </div>
            
            <?php if ($step_num < count($steps)): ?>
                <div class="aa-progress-line <?php echo $step_num < $current_step ? 'is-complete' : ''; ?>"></div>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>
</div>

<?php if ($current_step === 3): ?>
    <div class="aa-order-confirmation-notice">
        <div class="aa-notice-icon">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
        </div>
        <div class="aa-notice-text">
            <h3>অর্ডারটি সফলভাবে রিসিভ হয়েছে!</h3>
            <?php if (!empty($customer_name) && !empty($customer_phone)): ?>
                <p>প্রিয় <span class="aa-highlight-text"><?php echo esc_html($customer_name); ?></span>, আপনার অর্ডারটি আমরা রিসিভ করেছি। খুব দ্রুত আমাদের প্রতিনিধি আপনাকে কল দিয়ে অর্ডারটি কনফার্ম করবে। অনুগ্রহ করে আপনার <span class="aa-highlight-text"><?php echo esc_html($customer_phone); ?></span> নাম্বারটি সচল রাখুন।</p>
            <?php else: ?>
                <p>আপনার অর্ডারটি আমরা রিসিভ করেছি। খুব দ্রুত আমাদের প্রতিনিধি আপনাকে কল দিয়ে অর্ডারটি কনফার্ম করবে। অনুগ্রহ করে আপনার ফোনটি সচল রাখুন।</p>
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>
