<?php

// check apa bisa dijalanin di cli
if (php_sapi_name() !== 'cli') {
    die("command line only.\n");
}

// clear screen
function clearScreen()
{
    if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
        system('cls');
    } else {
        system('clear');
    }
}

// Function to print colored text in CLI (using ANSI escape codes if supported)
function color($text, $colorCode)
{
    return "\033[{$colorCode}m{$text}\033[0m";
}

clearScreen();

echo color("=============================================\n", "36;1");
echo color("          HIDDEN ITEM FINDER GAME            \n", "33;1");
echo color("=============================================\n", "36;1");

// baca grid example untuk mempermudah
$gridFile = __DIR__ . '/grid.txt';
$gridRaw = [];

// cek apakah ada atau tidak si filenya
if (file_exists($gridFile)) {
    $content = trim(file_get_contents($gridFile));
    if (!empty($content)) {
        $lines = explode("\n", $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '')
                continue;
            // Split by whitespace
            $gridRaw[] = preg_split('/\s+/', $line);
        }
    }
}

$height = count($gridRaw);
$width = count($gridRaw[0]);

// Find starting position X
$startX = -1;
$startY = -1;
for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        if ($gridRaw[$y][$x] === 'X' || $gridRaw[$y][$x] === 'x') {
            $startX = $x;
            $startY = $y;
            // Standardize as uppercase X
            $gridRaw[$y][$x] = 'X';
        }
    }
}

if ($startX === -1 || $startY === -1) {
    die(color("Error: Player starting position 'X' not found in the grid.\n", "31;1"));
}
