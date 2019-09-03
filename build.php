<?php

$indexFile = file_get_contents('index.php');
$target = 'dist/index.php';
define('BASE_URL', '');

function clean_content($content)
{
    return $content;
}

preg_match_all('/require\s?\(.*\)\;/', $indexFile, $imports);
$imports = $imports[0];

foreach ($imports as $import) {
    preg_match('/\(\'(.*)\'\)/', $import, $phpPath);
    $phpPath = $phpPath[1];
    $phpContent = file_get_contents($phpPath);
    $phpContent = preg_replace(array('/\<\?php/'), '', $phpContent);

    $indexFile = str_replace($import, $phpContent, $indexFile);
}

preg_match_all('/\<\?php\s?style\(.*\)\s?\?\>/', $indexFile, $styles);
$styles = $styles[0];

foreach ($styles as $style) {
    preg_match('/\(\'(.*)\'\)/', $style, $path);
    $cssPath = $path[1];
    $cssContent = '<style>';
    $cssContent .= clean_content(file_get_contents($cssPath));
    $cssContent .= '</style>';

    $indexFile = str_replace($style, $cssContent, $indexFile);
}

preg_match_all('/\<\?php\s?script\(.*\)\s?\?\>/', $indexFile, $scripts);
$scripts = $scripts[0];

foreach ($scripts as $script) {
    preg_match('/\(\'(.*)\'\)/', $script, $path);
    $jsPath = $path[1];
    $jsContent = '<script>';
    $jsContent .= clean_content(file_get_contents($jsPath));
    $jsContent .= '</script>';

    $indexFile = str_replace($script, $jsContent, $indexFile);
}

preg_match_all('/\<\?php\s?echo\s?BASE_URL\s?\?\>/', $indexFile, $baseUrls);
$baseUrls = $baseUrls[0];

foreach ($baseUrls as $baseUrl) {
    $indexFile = str_replace($baseUrl, BASE_URL, $indexFile);
}

file_put_contents($target, $indexFile);