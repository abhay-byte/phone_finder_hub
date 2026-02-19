#!/bin/bash

# Batch 1: Flagships 2025
echo "Running Batch 1..."
php artisan phone:import "Samsung Galaxy S25" "Samsung Galaxy S25+" "Samsung Galaxy S25 Ultra" "Samsung Galaxy S25 Slim" "Apple iPhone 17" "Apple iPhone 17 Air" "Apple iPhone 17 Pro" "Apple iPhone 17 Pro Max" "Google Pixel 10" "Google Pixel 10 Pro" --skip-image

# Batch 2: Flagships/High-end 2025/2026
echo "Running Batch 2..."
php artisan phone:import "Google Pixel 10 Pro XL" "OnePlus 13" "OnePlus 13R" "OnePlus 13 Pro" "Xiaomi 15" "Xiaomi 15 Ultra" "Oppo Find X8" "Oppo Find N5 Fold" "Vivo X200 Pro+" "Vivo X45" --skip-image

# Batch 3: More High-end
echo "Running Batch 3..."
php artisan phone:import "Vivo X200 FE" "Honor Magic 7 Pro" "Moto G96" "Moto G86 Power" "Nothing Phone 3" "Samsung Galaxy Z Fold 7" "Samsung Galaxy Z Flip 7" "Realme GT 6 Pro" "Samsung Galaxy S26" "Samsung Galaxy S26+" --skip-image

# Batch 4: Future/Rumored
echo "Running Batch 4..."
php artisan phone:import "Samsung Galaxy S26 Ultra" "Apple iPhone 18" "Google Pixel 11" "OnePlus 14" "Oppo Find N6" "Oppo Find X9 Ultra" "Xiaomi 16 Ultra" "Huawei Pura 90" "Sony Xperia 1 VII" "Honor Magic V6" --skip-image

# Batch 5: Samsung A/M/F 2025
echo "Running Batch 5..."
php artisan phone:import "Motorola Razr 2026" "Poco F8 Ultra" "Samsung Galaxy A06" "Samsung Galaxy A07" "Samsung Galaxy A16" "Samsung Galaxy A17" "Samsung Galaxy A26" "Samsung Galaxy A36" "Samsung Galaxy A56" "Samsung Galaxy M05" --skip-image

# Batch 6: Samsung A/M/F 2025/2026
echo "Running Batch 6..."
php artisan phone:import "Samsung Galaxy M06" "Samsung Galaxy M16" "Samsung Galaxy M35" "Samsung Galaxy M36" "Samsung Galaxy M55" "Samsung Galaxy M56" "Samsung Galaxy F06" "Samsung Galaxy F16" "Samsung Galaxy F36" "Samsung Galaxy F56" --skip-image

# Batch 7: Samsung 2026
echo "Running Batch 7..."
php artisan phone:import "Samsung Galaxy F07" "Samsung Galaxy A26 5G" "Samsung Galaxy A36 5G" "Samsung Galaxy A56 5G" "Samsung Galaxy A76 5G" "Samsung Galaxy A86 5G" "Samsung Galaxy M07" "Samsung Galaxy M17" "Samsung Galaxy M36 5G" "Samsung Galaxy M56 5G" --skip-image

# Batch 8: Poco/Redmi 2025/2026
echo "Running Batch 8..."
php artisan phone:import "Samsung Galaxy M66 5G" "Samsung Galaxy F06 5G" "Poco M8" "Poco M8 Pro" "Poco M8 Plus 5G" "Poco X8 Pro" "Poco X8 Pro Max" "Poco X8 5G" "Poco F8 Pro" "Poco F7 Ultra" --skip-image

# Batch 9: Redmi 2025/2026
echo "Running Batch 9..."
php artisan phone:import "Redmi Note 15 5G" "Redmi Note 15 Pro" "Redmi Note 15 Pro+" "Redmi Note 14 SE" "Redmi Turbo 5" "Redmi 15C" "Redmi 14R 5G" "Redmi K90 Pro Max" "Redmi K90" "Xiaomi 14 Ultra" --skip-image

# Batch 10: Extras/Polishing
echo "Running Batch 10..."
php artisan phone:import "Xiaomi 14" "Samsung Galaxy F17" "Samsung Galaxy F36 5G" "Samsung Galaxy F70e 5G" "Realme 12 Pro" "Realme 12 Pro+" "Realme 13 Pro" "Realme 13 Pro+" "Realme GT 5 Pro" "OnePlus Open 2" --skip-image

echo "Done!"
