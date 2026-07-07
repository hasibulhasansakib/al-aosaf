<?php
$source = __DIR__ . '/modules/Invoice/Views/invoice-template.php';
$dest = __DIR__ . '/modules/Invoice/Views/invoice-pdf-template.php';
$content = file_get_contents($source);

// Replace CSS variables
$replacements = [
    'var(--primary)' => '#c59b5f',
    'var(--primary-light)' => '#f8fafc',
    'var(--text-dark)' => '#0f172a',
    'var(--text-main)' => '#334155',
    'var(--text-light)' => '#64748b',
    'var(--border)' => '#e2e8f0',
    'padding: 1.5cm 2cm;' => 'padding: 0;',
    'margin: 1rem auto;' => 'margin: 0;',
    'max-width: 21cm;' => 'width: 100%;',
    'box-shadow: 0 20px 40px rgba(0,0,0,0.08);' => '',
];

$content = str_replace(array_keys($replacements), array_values($replacements), $content);

file_put_contents($dest, $content);
echo "Created invoice-pdf-template.php";
