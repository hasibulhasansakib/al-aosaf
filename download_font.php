<?php
$url = 'https://www.omicronlab.com/download/fonts/kalpurush.ttf';
$dest = __DIR__ . '/modules/Invoice/assets/fonts/kalpurush.ttf';

$ch = curl_init($url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)');
$data = curl_exec($ch);
curl_close($ch);

if ($data && strlen($data) > 10000) {
    file_put_contents($dest, $data);
    
    $f = fopen($dest, 'rb');
    echo "Hex: " . bin2hex(fread($f, 10)) . "\n";
    fclose($f);
    
    echo "Success: " . filesize($dest) . " bytes";
} else {
    echo "Failed. Size: " . strlen($data);
}
