<?php
declare(strict_types=1);

namespace Alaosaf\Modules\Invoice\Helpers;

class PdfGenerator {

    public static function generateInvoicePdf(\WC_Order $order, string $output_mode = 'I') {
        if (!class_exists('\Mpdf\Mpdf')) {
            $autoload_path = AA_PLUGIN_DIR . 'vendor/autoload.php';
            if (file_exists($autoload_path)) {
                require_once $autoload_path;
            } else {
                wp_die('PDF library not installed. Please run composer install.');
            }
        }

        // Setup custom font directory for Bengali support
        $defaultConfig = (new \Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new \Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        $tmp_dir = AA_PLUGIN_DIR . 'modules/Invoice/tmp';
        if (!file_exists($tmp_dir)) {
            mkdir($tmp_dir, 0777, true);
        }

        $mpdf = new \Mpdf\Mpdf([
            'mode' => 'utf-8',
            'format' => 'A4',
            'tempDir' => $tmp_dir,
            'fontDir' => array_merge($fontDirs, [
                AA_PLUGIN_DIR . 'modules/Invoice/assets/fonts',
            ]),
            'fontdata' => $fontData + [
                'kalpurush' => [
                    'R' => 'kalpurush.ttf',
                    'useOTL' => 0xFF,
                    'useKashida' => 75,
                ]
            ],
            'default_font' => 'kalpurush'
        ]);

        // Disable auto script/lang to prevent infinite page loops (kalpurush is already default font)
        $mpdf->autoScriptToLang = false;
        $mpdf->autoLangToFont = false;

        // Start capturing HTML
        ob_start();
        $template_path = AA_PLUGIN_DIR . 'modules/Invoice/Views/invoice-pdf-template.php';
        if (file_exists($template_path)) {
            include $template_path;
        } else {
            // fallback to the print template if specific pdf template doesn't exist
            $template_path = AA_PLUGIN_DIR . 'modules/Invoice/Views/invoice-template.php';
            include $template_path;
        }
        $html = ob_get_clean();

        // Write HTML to PDF
        $mpdf->WriteHTML($html);

        $filename = 'invoice-' . $order->get_order_number() . '.pdf';

        if ($output_mode === 'F') {
            $upload_dir = wp_upload_dir();
            $file_path = $upload_dir['basedir'] . '/' . $filename;
            $mpdf->Output($file_path, 'F');
            return $file_path;
        } else {
            // Clean any output buffers to prevent corrupted PDF
            while (ob_get_level()) {
                ob_end_clean();
            }
            $mpdf->Output($filename, $output_mode);
            exit;
        }
    }
}
