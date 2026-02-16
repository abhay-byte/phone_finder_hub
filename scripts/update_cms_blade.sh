#!/bin/bash

# Update CMS Blade view from 1000 to 1330 points
FILE="/home/abhay/repos/phone_finder/resources/views/cms/methodology.blade.php"

# Update badge
sed -i 's/CMS-1000/CMS-1330/g' "$FILE"

# Update description
sed -i 's/1000-point system/1330-point system/g' "$FILE"

# Update Core System total
sed -i 's/610 Points/940 Points/g' "$FILE"

# Update score cards
sed -i 's/>80</>200</g' "$FILE" | head -1  # Focus & Stability
sed -i 's/>120</>200</g' "$FILE" | head -1  # Video System  
sed -i 's/>50</>200</g' "$FILE" | head -1  # Multi-Camera Fusion
sed -i 's/>30</>100</g' "$FILE" | head -1  # Special Features

# Update section headers
sed -i 's/3\. Focus & Stability (80 Points)/3. Focus & Stability (200 Points)/g' "$FILE"
sed -i 's/4\. Video System (120 Points)/4. Video System (200 Points)/g' "$FILE"
sed -i 's/5\. Multi-Camera Fusion (50 Points)/5. Multi-Camera Fusion (200 Points)/g' "$FILE"
sed -i 's/6\. Special Features (30 Points)/6. Special Features (100 Points)/g' "$FILE"

# Update Autofocus subsection
sed -i 's/Autofocus (40 pts)/Autofocus (100 pts)/g' "$FILE"
sed -i 's/Stabilization (40 pts)/Stabilization (100 pts)/g' "$FILE"

# Update Final Formula
sed -i 's/FINAL CMS-1000 SCORE/FINAL CMS-1330 SCORE/g' "$FILE"
sed -i 's/CORE SYSTEM (max 610)/CORE SYSTEM (max 940)/g' "$FILE"
sed -i 's/= 1000 points/= 1330 points/g' "$FILE"

echo "✅ CMS Blade view updated to 1330 points"
