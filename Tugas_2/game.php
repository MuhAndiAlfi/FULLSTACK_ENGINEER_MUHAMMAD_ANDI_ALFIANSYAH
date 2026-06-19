<?php

// check apa bisa dijalanin di cli
if (php_sapi_name() !== 'cli') {
    die("command line only\n");
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

// kalau kosong pake default
if (empty($gridRaw)) {
    $defaultGrid = [
        ['.', '.', '.', '#', '.', '.', '.', '.'],
        ['.', '.', '#', '.', '.', '.', '#', '.'],
        ['.', '.', '.', '.', '#', '.', '.', '.'],
        ['.', '#', '.', '.', '.', '.', '.', '.'],
        ['.', '.', '.', '#', '.', '.', '.', '.'],
        ['.', '.', 'X', '.', '.', '#', '.', '.'],
        ['.', '.', '.', '.', '.', '.', '.', '.'],
    ];
    $gridRaw = $defaultGrid;
}

$height = count($gridRaw);
$width = count($gridRaw[0]);

// cari posisi X
$startX = -1;
$startY = -1;
for ($y = 0; $y < $height; $y++) {
    for ($x = 0; $x < $width; $x++) {
        if ($gridRaw[$y][$x] === 'X' || $gridRaw[$y][$x] === 'x') {
            $startX = $x;
            $startY = $y;
            $gridRaw[$y][$x] = 'X';
        }
    }
}


// Function to print a grid layout
function printGrid($grid, $markedCoords = [], $highlightStart = true)
{
    $h = count($grid);
    $w = count($grid[0]);

    // Print column headers
    echo "    ";
    for ($x = 0; $x < $w; $x++) {
        echo color($x . " ", "36;1");
    }
    echo "\n";

    echo "   " . str_repeat("—", $w * 2 + 1) . "\n";

    for ($y = 0; $y < $h; $y++) {
        echo color(sprintf("%2d |", $y), "36;1") . " ";
        for ($x = 0; $x < $w; $x++) {
            $cell = $grid[$y][$x];

            // Check if this coordinate is in marked coords
            $isMarked = false;
            foreach ($markedCoords as $coord) {
                if ($coord['x'] === $x && $coord['y'] === $y) {
                    $isMarked = true;
                    break;
                }
            }

            if ($isMarked) {
                echo color("$ ", "32;1"); // Green Bold $
            } elseif ($cell === 'X' && $highlightStart) {
                echo color("X ", "33;1"); // Yellow Bold X
            } elseif ($cell === '#') {
                echo color("# ", "31");   // Red Obstacle
            } else {
                echo ". ";
            }
        }
        echo "\n";
    }
    echo "\n";
}

echo color("--- Original Grid Layout ---\n", "35;1");
printGrid($gridRaw, [], true);
echo "Starting Position (X): " . color("({$startX}, {$startY})", "33;1") . "\n\n";

// Function to calculate all probable locations
function findProbableLocations($grid, $startX, $startY)
{
    $height = count($grid);
    $width = count($grid[0]);
    $probableLocations = [];

    // Loop for A (Up/North steps)
    // Moving Up decreases Y. So startY - A must be >= 0.
    for ($A = 1; $startY - $A >= 0; $A++) {
        // Check if path Up is clear
        if ($grid[$startY - $A][$startX] === '#') {
            // Blocked by obstacle, cannot go further Up/North
            break;
        }

        // Loop for B (Right/East steps)
        // Moving Right increases X. So startX + B must be < width.
        for ($B = 1; $startX + $B < $width; $B++) {
            // Check if path Right/East is clear at the current Y position (startY - A)
            if ($grid[$startY - $A][$startX + $B] === '#') {
                // Blocked by obstacle, cannot go further Right/East
                break;
            }

            // Loop for C (Down/South steps)
            // Moving Down increases Y. So (startY - A) + C must be < height.
            for ($C = 1; ($startY - $A) + $C < $height; $C++) {
                $targetY = ($startY - $A) + $C;
                $targetX = $startX + $B;

                // Check if path Down/South is clear at column targetX
                if ($grid[$targetY][$targetX] === '#') {
                    // Blocked by obstacle, cannot go further Down/South
                    break;
                }

                // If path is clear, this is a probable coordinate!
                $key = "{$targetX},{$targetY}";
                $probableLocations[$key] = [
                    'x' => $targetX,
                    'y' => $targetY,
                    'path' => [
                        'A' => $A,
                        'B' => $B,
                        'C' => $C
                    ]
                ];
            }
        }
    }

    return array_values($probableLocations);
}

// Calculate the solutions
$solutions = findProbableLocations($gridRaw, $startX, $startY);

// Interactive input or automatic discovery
echo color("Choose an option:\n", "35;1");
echo "1. Run Automatic Solver (list all probable coordinates & display marked grid)\n";
echo "2. Input specific steps (A, B, C) to test navigation\n";
echo "Enter option [1 or 2, default 1]: ";

$handle = fopen("php://stdin", "r");
$option = trim(fgets($handle));

if ($option === '2') {
    echo "\n";
    echo color("--- Custom Step Tester ---\n", "35;1");
    echo "Enter number of steps Up/North (A): ";
    $A = intval(trim(fgets($handle)));
    echo "Enter number of steps Right/East (B): ";
    $B = intval(trim(fgets($handle)));
    echo "Enter number of steps Down/South (C): ";
    $C = intval(trim(fgets($handle)));

    echo "\n" . color("Testing path from ({$startX}, {$startY}) with Up={$A}, Right={$B}, Down={$C}...\n", "35") . "\n";

    $valid = true;
    $currentX = $startX;
    $currentY = $startY;

    // Step 1: Up/North A steps
    for ($i = 1; $i <= $A; $i++) {
        $nextY = $currentY - 1;
        if ($nextY < 0) {
            echo color("Blocked: Step Up {$i} goes out of grid bounds (above top edge).\n", "31");
            $valid = false;
            break;
        }
        if ($gridRaw[$nextY][$currentX] === '#') {
            echo color("Blocked: Obstacle encountered at coordinate ({$currentX}, {$nextY}) while going Up.\n", "31");
            $valid = false;
            break;
        }
        $currentY = $nextY;
    }

    // Step 2: Right/East B steps
    if ($valid) {
        for ($i = 1; $i <= $B; $i++) {
            $nextX = $currentX + 1;
            if ($nextX >= $width) {
                echo color("Blocked: Step Right {$i} goes out of grid bounds (past right edge).\n", "31");
                $valid = false;
                break;
            }
            if ($gridRaw[$currentY][$nextX] === '#') {
                echo color("Blocked: Obstacle encountered at coordinate ({$nextX}, {$currentY}) while going Right.\n", "31");
                $valid = false;
                break;
            }
            $currentX = $nextX;
        }
    }

    // Step 3: Down/South C steps
    if ($valid) {
        for ($i = 1; $i <= $C; $i++) {
            $nextY = $currentY + 1;
            if ($nextY >= $height) {
                echo color("Blocked: Step Down {$i} goes out of grid bounds (below bottom edge).\n", "31");
                $valid = false;
                break;
            }
            if ($gridRaw[$nextY][$currentX] === '#') {
                echo color("Blocked: Obstacle encountered at coordinate ({$currentX}, {$nextY}) while going Down.\n", "31");
                $valid = false;
                break;
            }
            $currentY = $nextY;
        }
    }

    if ($valid) {
        echo color("Success! Path is clear! Item location found at: ", "32;1") . color("({$currentX}, {$currentY})\n\n", "33;1");
        echo color("--- Grid with final location marked ($) ---\n", "35;1");
        printGrid($gridRaw, [['x' => $currentX, 'y' => $currentY]], true);
    } else {
        echo color("Failed: Path is blocked by obstacles or bounds.\n\n", "31;1");
    }

} else {
    // Display all solutions
    echo "\n";
    echo color("--- Calculated Probable Locations ---\n", "35;1");
    if (empty($solutions)) {
        echo color("No probable locations found. Check grid obstacles.\n\n", "31");
    } else {
        echo "Found " . color(count($solutions), "32;1") . " probable coordinate point(s):\n";
        foreach ($solutions as $sol) {
            echo "- " . color("({$sol['x']}, {$sol['y']})", "32;1") . " (path: Up " . color($sol['path']['A'], "33") . ", Right " . color($sol['path']['B'], "33") . ", Down " . color($sol['path']['C'], "33") . ")\n";
        }
        echo "\n";

        echo color("--- Solved Grid Layout (marked with $) ---\n", "35;1");
        printGrid($gridRaw, $solutions, true);
    }
}

fclose($handle);

echo color("=============================================\n", "36;1");
echo color("Thank you for playing Hidden Item Finder!\n", "33;1");
echo color("=============================================\n", "36;1");

