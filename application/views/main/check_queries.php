<?php
$dir = __DIR__;
$files = scandir($dir);
foreach ($files as $file) {
    if (pathinfo($file, PATHINFO_EXTENSION) === 'php') {
        $content = file_get_contents($file);
        if (strpos($content, 'mysqli_query') !== false) {
            echo "$file\n";
        }
    }
}
?>