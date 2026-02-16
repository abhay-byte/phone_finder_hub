<?php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

echo "Phones with CMS Scores:\n";
echo str_repeat("=", 60) . "\n\n";

$phones = \App\Models\Phone::whereNotNull('cms_score')
    ->where('cms_score', '>', 0)
    ->orderBy('cms_score', 'desc')
    ->get(['id', 'name', 'cms_score']);

if ($phones->isEmpty()) {
    echo "No phones have CMS scores yet!\n";
    echo "Run: php artisan tinker --execute=\"App\Models\Phone::all()->each(fn(\$p) => \$p->updateScores());\"\n";
} else {
    foreach ($phones as $phone) {
        echo sprintf("ID: %2d | %-40s | CMS: %.1f/1330\n", 
            $phone->id, 
            $phone->name, 
            $phone->cms_score
        );
    }
    echo "\n" . str_repeat("=", 60) . "\n";
    echo "Total phones with CMS: " . $phones->count() . "\n";
    echo "\nTo view a phone page: http://localhost:8000/phones/{id}\n";
}
