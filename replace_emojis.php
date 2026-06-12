<?php
$dir = new RecursiveDirectoryIterator('resources/views');
$ite = new RecursiveIteratorIterator($dir);
$replacements = [
    '👶' => '<i data-feather="smile"></i>',
    '👦' => '<i data-feather="user"></i>',
    '👧' => '<i data-feather="user"></i>',
    '👩' => '<i data-feather="user"></i>',
    '⚖️' => '<i data-feather="activity"></i>',
    '📏' => '<i data-feather="bar-chart-2"></i>',
    '🥕' => '<i data-feather="book-open"></i>',
    '🥣' => '<i data-feather="coffee"></i>',
    '🔍' => '<i data-feather="search"></i>',
    '🔔' => '<i data-feather="bell"></i>',
];

foreach($ite as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
        $content = file_get_contents($file);
        $newContent = $content;
        foreach($replacements as $emoji => $svg) {
            $newContent = str_replace($emoji, $svg, $newContent);
        }
        
        // Specific fixes for curly braces around the SVGs
        // This regex ensures we only replace {{ ... }} if they contain <i data-feather on the SAME line!
        // We use 'm' for multi-line if needed, but not 's' so '.' doesn't match newlines.
        $newContent = preg_replace_callback('/\{\{(.*?(?:<i data-feather.*?><\/i>).*?)\}\}/', function($matches) {
            return '{!!' . $matches[1] . '!!}';
        }, $newContent);
        
        if ($content !== $newContent) {
            file_put_contents($file, $newContent);
            echo "Updated: $file\n";
        }
    }
}
echo "Done.\n";
