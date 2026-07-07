<?php
declare(strict_types=1);

namespace Alaosaf\Base;

abstract class AbstractController {
    
    /**
     * Enforce a required capability before proceeding.
     */
    protected function requireCapability(string $capability = 'manage_options'): void {
        if (!current_user_can($capability)) {
            wp_die(__('You do not have permission to access this page.', 'al-aosaf'), '', ['response' => 403]);
        }
    }

    /**
     * Verify nonce securely for form submissions or AJAX requests.
     */
    protected function verifyNonce(string $nonceValue, string $action): void {
        if (!wp_verify_nonce($nonceValue, $action)) {
            wp_die(__('Security check failed.', 'al-aosaf'), '', ['response' => 403]);
        }
    }
}
