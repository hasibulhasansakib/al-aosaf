<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Appearance;

use Alaosaf\Interfaces\ModuleInterface;
use Alaosaf\Modules\Appearance\Controllers\AppearanceController;

class AppearanceModule implements ModuleInterface {
    public function init(): void {
        $this->seedDefaults();
        
        $controller = new AppearanceController();
        $controller->init();
    }

    private function seedDefaults(): void {
        if (get_option('aa_brand_settings', false) === false) {
            $defaults = [
                'general_business_name' => 'Al Aosaf',
                'general_website_name' => 'Al Aosaf',
                'general_tagline' => 'Fashion Redefined',
                'branding_logo_type' => 'image',
                'branding_logo_text' => 'Al Aosaf',
                'branding_primary_logo' => AA_PLUGIN_URL . 'assets/img/logo.png',
                'colors_primary' => '#C8A15A',
                'colors_primary_hover' => '#E0B96D',
                'colors_secondary' => '#D9D9D9',
                'colors_background' => '#050505',
                'colors_surface' => '#111111',
                'colors_text' => '#F5F5F5',
                'colors_muted' => '#A0A0A0',
                'colors_border' => 'rgba(200,161,90,.2)',
                'contact_phone' => '+8801700000000',
                'contact_whatsapp' => '+8801700000000',
                'contact_email' => 'info@alaosaf.com',
                'contact_support_email' => 'support@alaosaf.com',
                'contact_address' => 'Dhaka, Bangladesh',
                'contact_hours' => 'Mon - Sat | 9:00 AM - 6:00 PM',
                'social_facebook' => '#',
                'social_instagram' => '#',
                'social_twitter' => '#',
                'social_youtube' => '#',
                'social_linkedin' => '#',
                'business_company_name' => 'Al Aosaf',
                'business_currency' => 'BDT',
                'business_country' => 'Bangladesh',
                'business_invoice_footer' => 'Thank you for choosing Al Aosaf.'
            ];
            
            // This is only triggered if the option completely doesn't exist
            add_option('aa_brand_settings', $defaults);
        }
    }

    public function getModuleId(): string {
        return 'appearance';
    }
}
