<?php

/**
 * Parse PostgreSQL dump and convert to Firestore-compatible JSON
 *
 * Usage: php scripts/sql-to-json.php
 */
$sqlFile = __DIR__.'/../database_dump.sql';
$outputDir = __DIR__.'/../database/firestore_export';

if (! file_exists($sqlFile)) {
    echo "Error: database_dump.sql not found. Run pg_dump first.\n";
    exit(1);
}

@mkdir($outputDir, 0755, true);

$content = file_get_contents($sqlFile);

// Extract COPY data blocks
$tables = [];

// Pattern to find COPY ... FROM stdin; blocks
$pattern = '/COPY public\.(\w+) \(([^)]+)\) FROM stdin;\n(.*?)\\\./s';
if (preg_match_all($pattern, $content, $matches, PREG_SET_ORDER)) {
    foreach ($matches as $match) {
        $tableName = $match[1];
        $columns = array_map('trim', explode(',', $match[2]));
        $dataBlock = $match[3];

        $rows = [];
        $lines = explode("\n", trim($dataBlock));
        foreach ($lines as $line) {
            if (empty($line) || $line === '\\.') {
                continue;
            }

            // PostgreSQL COPY format uses tab delimiter
            $values = explode("\t", $line);
            $row = [];
            foreach ($columns as $i => $col) {
                $val = $values[$i] ?? null;

                // Convert \N to null
                if ($val === '\\N') {
                    $val = null;
                }

                // Try to decode JSON columns
                if ($val && (str_starts_with($val, '{') || str_starts_with($val, '['))) {
                    $decoded = json_decode($val, true);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        $val = $decoded;
                    }
                }

                // Convert boolean strings
                if ($val === 't') {
                    $val = true;
                }
                if ($val === 'f') {
                    $val = false;
                }

                // Try to convert numeric strings to numbers
                if (is_string($val) && is_numeric($val)) {
                    $val = strpos($val, '.') !== false ? (float) $val : (int) $val;
                }

                $row[$col] = $val;
            }
            $rows[] = $row;
        }

        $tables[$tableName] = $rows;
        echo "Table: $tableName - ".count($rows)." rows\n";
    }
}

// Also try INSERT INTO pattern for tables without COPY blocks
$insertPattern = '/INSERT INTO public\.(\w+) VALUES \((.*?)\);/s';
if (preg_match_all($insertPattern, $content, $matches, PREG_SET_ORDER)) {
    foreach ($matches as $match) {
        $tableName = $match[1];
        $valuesStr = $match[2];

        // Simple parsing for single-row inserts (this is naive)
        // For now, COPY blocks should handle most data
    }
}

// Save each table as JSON
foreach ($tables as $tableName => $rows) {
    $fileName = $outputDir.'/'.$tableName.'.json';
    file_put_contents($fileName, json_encode($rows, JSON_PRETTY_PRINT));
    echo "Saved: $fileName\n";
}

// Create Firestore collection mapping
$collectionMap = [
    'users' => 'users',
    'phones' => 'phones',
    'benchmarks' => 'benchmarks',
    'spec_batteries' => 'spec_batteries',
    'spec_bodies' => 'spec_bodies',
    'spec_cameras' => 'spec_cameras',
    'spec_connectivities' => 'spec_connectivities',
    'spec_platforms' => 'spec_platforms',
    'comments' => 'comments',
    'comment_upvotes' => 'comment_upvotes',
    'blogs' => 'blogs',
    'forum_categories' => 'forum_categories',
    'forum_posts' => 'forum_posts',
    'forum_comments' => 'forum_comments',
    'chats' => 'chats',
    'chat_messages' => 'chat_messages',
];

// Save collection mapping
file_put_contents($outputDir.'/_collection_map.json', json_encode($collectionMap, JSON_PRETTY_PRINT));

echo "\nExport complete. Files saved to: $outputDir\n";
echo 'Total tables exported: '.count($tables)."\n";

// Print summary
foreach ($tables as $table => $rows) {
    echo "  - $table: ".count($rows)." documents\n";
}
