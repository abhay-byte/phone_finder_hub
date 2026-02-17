<?php

$specs = "50 MP, f/1.8, 24mm (wide), 1/1.56\", 1.0µm, multi-directional PDAF, OIS 50 MP, f/2.8, 80mm (periscope telephoto), 1/2.76\", 0.64µm, 3.5x optical zoom, PDAF, OIS 50 MP, f/2.0, 16mm, 116˚ (ultrawide), 1/2.88\", 0.61µm, PDAF Laser focus, color spectrum sensor, LED flash, HDR, panorama, LUT preview";

echo "Specs: $specs\n";

if (preg_match_all('/(\d+)x\s*optical/i', $specs, $matches)) {
    print_r($matches);
}

$zoomLevel = 0;
if (preg_match('/(\d+)x\s*optical/i', $specs, $matches)) {
    $zoomLevel = intval($matches[1]);
}
echo "Zoom Level (int): $zoomLevel\n";

// Test decimal regex
if (preg_match('/(\d+(\.\d+)?)x\s*optical/i', $specs, $matches)) {
    echo "Decimal Match: {$matches[1]}\n";
    echo "Zoom Level (float): " . floatval($matches[1]) . "\n";
}
