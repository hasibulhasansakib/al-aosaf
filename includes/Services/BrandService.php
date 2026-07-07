<?php
declare(strict_types=1);

namespace Alaosaf\Services;

class BrandService {
    private array $settings;

    public function __construct() {
        $this->settings = get_option('aa_brand_settings', []);
    }

    public function get(string $key, $default = '') {
        $value = $this->settings[$key] ?? '';
        return $value !== '' ? $value : $default;
    }

    // Branding
    public function logoType(): string { return $this->get('branding_logo_type', 'image'); }
    public function logoText(): string { $text = $this->get('branding_logo_text'); return !empty($text) ? $text : $this->businessName(); }
    public function primaryLogo(): string { return $this->get('branding_primary_logo', AA_PLUGIN_URL . 'assets/img/logo.png'); }
    public function darkLogo(): string { return $this->get('branding_dark_logo'); }
    public function lightLogo(): string { return $this->get('branding_light_logo'); }
    public function mobileLogo(): string { return $this->get('branding_mobile_logo'); }
    public function favicon(): string { return $this->get('branding_favicon'); }
    public function invoiceLogo(): string { return $this->get('branding_invoice_logo'); }
    public function emailLogo(): string { return $this->get('branding_email_logo'); }

    /**
     * Dynamically renders the complete logo HTML based on the configured Logo Type.
     */
    public function renderLogo(string $customClass = 'aa-logo-wrapper'): string {
        $type = $this->logoType();
        $name = esc_html($this->logoText());
        $image = esc_url($this->primaryLogo());
        
        $html = '<div class="' . esc_attr($customClass) . '">';
        
        if ($type === 'text') {
            $html .= '<span class="aa-logo-text">' . $name . '</span>';
        } elseif ($type === 'image_text') {
            if (!empty($image)) {
                $html .= '<img src="' . $image . '" alt="' . esc_attr($name) . '" class="aa-logo-image" style="max-height: 50px; vertical-align: middle; margin-right: 10px;">';
            }
            $html .= '<span class="aa-logo-text" style="vertical-align: middle;">' . $name . '</span>';
        } else {
            // Default to image
            if (!empty($image)) {
                $html .= '<img src="' . $image . '" alt="' . esc_attr($name) . '" class="aa-logo-image" style="max-height: 50px;">';
            } else {
                // Fallback to text if image is missing
                $html .= '<span class="aa-logo-text">' . $name . '</span>';
            }
        }
        
        $html .= '</div>';
        return $html;
    }

    // Colors
    public function primaryColor(): string { return $this->get('colors_primary', '#C8A15A'); }
    public function primaryHover(): string { return $this->get('colors_primary_hover', '#E0B96D'); }
    public function secondaryColor(): string { return $this->get('colors_secondary', '#D9D9D9'); }
    public function backgroundColor(): string { return $this->get('colors_background', '#050505'); }
    public function surfaceColor(): string { return $this->get('colors_surface', '#111111'); }
    public function textColor(): string { return $this->get('colors_text', '#F5F5F5'); }
    public function mutedText(): string { return $this->get('colors_muted', '#A0A0A0'); }
    public function borderColor(): string { return $this->get('colors_border', 'rgba(200,161,90,.2)'); }

    // Contact
    public function phone(): string { return $this->get('contact_phone', '+8801700000000'); }
    public function whatsapp(): string { return $this->get('contact_whatsapp', '+8801700000000'); }
    public function email(): string { return $this->get('contact_email', 'info@alaosaf.com'); }
    public function supportEmail(): string { return $this->get('contact_support_email', 'support@alaosaf.com'); }
    public function address(): string { return $this->get('contact_address', 'Dhaka, Bangladesh'); }
    public function mapUrl(): string { return $this->get('contact_map_url'); }
    public function businessHours(): string { return $this->get('contact_hours', 'Mon - Sat | 9:00 AM - 6:00 PM'); }

    // Social
    public function facebook(): string { return $this->get('social_facebook', '#'); }
    public function instagram(): string { return $this->get('social_instagram', '#'); }
    public function twitter(): string { return $this->get('social_twitter', '#'); }
    public function youtube(): string { return $this->get('social_youtube', '#'); }
    public function tiktok(): string { return $this->get('social_tiktok'); }
    public function linkedin(): string { return $this->get('social_linkedin', '#'); }
    public function pinterest(): string { return $this->get('social_pinterest'); }
    public function telegram(): string { return $this->get('social_telegram'); }

    // General
    public function businessName(): string { return $this->get('general_business_name', 'Al Aosaf'); }
    public function websiteName(): string { return $this->get('general_website_name', 'Al Aosaf'); }
    public function tagline(): string { return $this->get('general_tagline', 'Fashion Redefined'); }
    
    // Business
    public function companyName(): string { return $this->get('business_company_name', 'Al Aosaf'); }
    public function vatNumber(): string { return $this->get('business_vat'); }
    public function taxNumber(): string { return $this->get('business_tax'); }
    public function invoiceFooter(): string { return $this->get('business_invoice_footer', 'Thank you for choosing Al Aosaf.'); }
    public function currency(): string { return $this->get('business_currency', 'BDT'); }
    public function country(): string { return $this->get('business_country', 'Bangladesh'); }
}
