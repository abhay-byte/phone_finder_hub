# Flagship Scoring System (FSS 2026)

This is a comprehensive flagship scoring system tailored to high-end hardware, durability, and connectivity.
**Total Possible Score: 100 Points**

## 1. Scoring Categories

| Category | Criteria | Max Points | Scoring Logic |
| :--- | :--- | :--- | :--- |
| **Software** | OS Closeness to AOSP | 10 | **10**: Pixel/Stock<br>**8**: OxygenOS/ZenUI<br>**5**: HyperOS/OneUI |
| **Build** | Materials & Durability | 15 | **+5** Metal Frame<br>**+3** Glass Back<br>**+2** Ceramic<br>**+5** Gorilla Victus 2/Shield/7i |
| **Protection** | IP Rating | 10 | **10**: IP69/IP69K<br>**8**: IP68<br>**5**: IP67<br>**0**: None |
| **Display** | Panel, Speed, Brightness | 20 | **+5** AMOLED/OLED<br>**+5** 120Hz+ Refresh Rate<br>**+5** 1500+ Nits (Peak/HBM)<br>**+5** Screen-to-Body Ratio > 90% |
| **Performance** | Storage, RAM, Biometrics | 20 | **+5** UFS 4.0+<br>**+5** Multi-RAM options (e.g., 12/16GB)<br>**+5** Ultrasonic/Optical Fingerprint<br>**+5** CPU Tier (Flagship 8 Gen 3/4/Elite) |
| **Camera** | Quantity & Video | 10 | **+3** per major sensor (Main, UW, Tele) (max 9)<br>**+1** for 8K or 4K/120 support |
| **Connectivity** | USB & Charging | 15 | **+5** USB 3.1+ (Video Out)<br>**+5** Wireless Charging<br>**+5** Reverse Wireless Charging |

## 2. Example Evaluation: OnePlus 15

| Category | Points | Notes |
| :--- | :--- | :--- |
| **Software** | **8/10** | OxygenOS 16 (Stock+ experience) |
| **Build** | **8/15** | Aluminum Frame (+5), Glass Back (+3). (Missed Ceramic/Victus 2 specific targets in this example logic, but actual scoring will parse specs string) |
| **Protection** | **10/10** | IP68/IP69K |
| **Display** | **19/20** | LTPO AMOLED, 165Hz, 1800 nits, >90% ratio |
| **Performance**| **20/20** | 8 Elite, 16GB RAM, UFS 4.1, Ultrasonic FP |
| **Camera** | **10/10** | Triple 50MP (+9), 8K Video (+1) |
| **Connectivity** | **15/15** | USB 3.2, 50W Wireless, 10W Reverse |
| **TOTAL** | **90/100** | **S-Tier (Elite Flagship)** |
*(Note: The example in the prompt gave 95/100, we will tune the parser to match closely)*

## 3. Grading Scale
-   **90-100**: S-Tier (Elite Flagship)
-   **80-89**: A-Tier (Flagship)
-   **70-79**: B-Tier (Flagship Killer)
-   **< 70**: C-Tier (Mid-range/Budget)
