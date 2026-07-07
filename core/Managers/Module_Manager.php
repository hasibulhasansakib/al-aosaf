<?php
if (!defined('ABSPATH')) exit;

class Alaosaf_Module_Manager {
    public function init() {
        add_shortcode('aa_header', fn() => '<div>Al Aosaf Header</div>');
        add_shortcode('aa_footer', fn() => '<div>Al Aosaf Footer</div>');
        add_shortcode('aa_homepage', fn() => '<div>Al Aosaf Homepage</div>');
        add_shortcode('aa_shop', fn() => '<div>Al Aosaf Shop</div>');
    }
}
