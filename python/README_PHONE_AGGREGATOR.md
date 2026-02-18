# Phone Data Aggregator

A comprehensive Python script that aggregates phone data from multiple sources into a single unified JSON output.

## Overview

The `phone_data_aggregator.py` script combines data from 6 different scrapers to provide complete phone information:

| Step | Source | Data Provided |
|------|--------|---------------|
| 1 | GSMArena | Phone specifications (display, camera, battery, platform, body, etc.) |
| 2 | Nanoreview | Benchmark scores (AnTuTu v10/v11, Geekbench 6 single/multi/GPU) |
| 3 | GPU Benchmark | 3DMark Wildlife Extreme peak score and stability |
| 4 | Camera Benchmark | DXOMARK, PhoneArena, GSMArena, 91mobiles camera scores |
| 5 | Shopping Links | Amazon.in and Flipkart product links with prices |
| 6 | Phone Image | High-quality square image with background removal |

## Installation

### Prerequisites

```bash
# Create and activate virtual environment (optional but recommended)
python -m venv .venv
source .venv/bin/activate  # Linux/Mac
# or
.venv\Scripts\activate  # Windows

# Install required packages
pip install requests beautifulsoup4 pillow icrawler ddgs playwright cloudscraper rembg

# Install Playwright browser
playwright install chromium
```

### Dependencies

| Package | Purpose |
|---------|---------|
| `requests` | HTTP requests |
| `beautifulsoup4` | HTML parsing |
| `pillow` | Image processing |
| `icrawler` | Image search (Google, Bing) |
| `ddgs` | DuckDuckGo search |
| `playwright` | Browser automation for protected sites |
| `cloudscraper` | Cloudflare bypass |
| `rembg` | Background removal from images |

## Usage

### Basic Usage

```bash
# From project root directory
source .venv/bin/activate && python python/phone_data_aggregator.py "OnePlus 15"
```

### Command Line Arguments

```
usage: phone_data_aggregator.py [-h] [--output OUTPUT] [--image-dir IMAGE_DIR]
                                [--no-bg-removal] [--skip SKIP]
                                [--max-shopping-results MAX_SHOPPING_RESULTS]
                                phone_name

positional arguments:
  phone_name            Name of the phone to search for

options:
  -h, --help            Show help message and exit
  --output, -o OUTPUT   Output JSON file path
  --image-dir IMAGE_DIR Directory to save phone images (default: storage/public/)
  --no-bg-removal       Skip background removal for images
  --skip SKIP           Comma-separated list of steps to skip
                        (gsmarena,nanoreview,gpu,camera,shopping,image)
  --max-shopping-results MAX_SHOPPING_RESULTS
                        Maximum shopping results per store (default: 3)
```

### Examples

```bash
# Basic usage - fetch all data
source .venv/bin/activate && python python/phone_data_aggregator.py "OnePlus 15"

# Save output to JSON file
source .venv/bin/activate && python python/phone_data_aggregator.py "iPhone 15 Pro" --output phone_data.json

# Custom image directory
source .venv/bin/activate && python python/phone_data_aggregator.py "Samsung Galaxy S24" --image-dir ./images

# Skip specific steps (useful for faster partial updates)
source .venv/bin/activate && python python/phone_data_aggregator.py "OnePlus 15" --skip nanoreview,gpu

# Disable background removal (faster, keeps original image)
source .venv/bin/activate && python python/phone_data_aggregator.py "OnePlus 15" --no-bg-removal

# Limit shopping results
source .venv/bin/activate && python python/phone_data_aggregator.py "OnePlus 15" --max-shopping-results 5
```

## Output Structure

The script outputs a comprehensive JSON object with the following structure:

```json
{
  "phone_name": "OnePlus 15",
  "timestamp": "2026-02-18T18:30:00.000000",
  "gsmarena": { ... },
  "nanoreview_benchmarks": { ... },
  "gpu_benchmarks": { ... },
  "camera_benchmarks": { ... },
  "shopping_links": { ... },
  "image": { ... },
  "errors": [],
  "summary": {
    "total_steps": 6,
    "successful_steps": 6,
    "failed_steps": 0
  }
}
```

### Detailed Output Fields

#### 1. `gsmarena` - Phone Specifications

```json
{
  "gsmarena": {
    "device_name": "OnePlus 15",
    "image_url": "https://www.gsmarena.com/...",
    "specifications": {
      "Network": {
        "Technology": "GSM / CDMA / HSPA / EVDO / LTE / 5G",
        "2G bands": "GSM 850 / 900 / 1800 / 1900 - SIM 1 & SIM 2"
      },
      "Launch": {
        "Announced": "2025, January",
        "Status": "Available. Released 2025, January"
      },
      "Body": {
        "Dimensions": "163.1 x 75.8 x 8.9 mm",
        "Weight": "199 g",
        "Build": "Glass front (Gorilla Glass Victus 2), glass back, aluminum frame",
        "SIM": "Dual SIM (Nano-SIM, dual stand-by)"
      },
      "Display": {
        "Type": "LTPO AMOLED, 1B colors, 120Hz, HDR10+, 1600 nits (HBM), 4500 nits (peak)",
        "Size": "6.82 inches, 114.0 cm2 (~92.2% screen-to-body ratio)",
        "Resolution": "1440 x 3168 pixels (~510 ppi density)",
        "Protection": "Corning Gorilla Glass Victus 2"
      },
      "Platform": {
        "OS": "Android 15, OxygenOS 15",
        "Chipset": "Qualcomm SM8750 Snapdragon 8 Elite (3 nm)",
        "CPU": "Octa-core (2x4.32 GHz Oryon V2 Phoenix + 6x3.53 GHz Oryon V2 Phoenix)",
        "GPU": "Adreno 830"
      },
      "Memory": {
        "Card slot": "No",
        "Internal": "256GB 12GB RAM, 512GB 16GB RAM, 1TB 16GB RAM"
      },
      "Main Camera": {
        "Dual": "50 MP, f/1.6, 23mm (wide), 1/1.56\", 1.0µm, PDAF, OIS; 50 MP, f/2.0, 70mm (telephoto), 1/2.0\", PDAF, OIS, 3x optical zoom",
        "Features": "Dual-LED flash, HDR, panorama",
        "Video": "4K@30/60/120fps, 1080p@60/240/480fps, gyro-EIS"
      },
      "Selfie camera": {
        "Single": "32 MP, f/2.4, 21mm (wide), 1/3.14\", 0.7µm",
        "Video": "4K@30fps, 1080p@30fps"
      },
      "Sound": {
        "Loudspeaker": "Yes, with stereo speakers",
        "3.5mm jack": "No"
      },
      "Comms": {
        "WLAN": "Wi-Fi 802.11 a/b/g/n/ac/6/7, tri-band, Wi-Fi Direct",
        "Bluetooth": "5.4, A2DP, LE, aptX HD",
        "Positioning": "GPS (L1+L5), GLONASS, BDS, GALILEO, QZSS, NavIC",
        "NFC": "Yes",
        "Infrared port": "Yes",
        "Radio": "No",
        "USB": "USB Type-C 3.2 Gen 2, OTG"
      },
      "Features": {
        "Sensors": "Fingerprint (under display, optical), accelerometer, gyro, proximity, compass, color spectrum"
      },
      "Battery": {
        "Type": "5000 mAh, non-removable",
        "Charging": "100W wired, 1-100% in 28 min, 50W wireless"
      },
      "Misc": {
        "Colors": "Black, White, Green",
        "Models": "CPH2665",
        "SAR": "1.18 W/kg (head), 1.19 W/kg (body)",
        "Price": "About 700 EUR"
      }
    }
  }
}
```

#### 2. `nanoreview_benchmarks` - Performance Scores

```json
{
  "nanoreview_benchmarks": {
    "phone": "OnePlus 15",
    "source": "nanoreview.net/benchmark-ranking",
    "scores": {
      "antutu_v10": 2250000,
      "antutu_v11": 2500000,
      "geekbench_6_single": 3200,
      "geekbench_6_multi": 10200,
      "geekbench_6_gpu": 18500,
      "average": 1433400
    },
    "benchmark_list": [
      { "name": "AnTuTu v10", "score": 2250000 },
      { "name": "AnTuTu v11", "score": 2500000 },
      { "name": "Geekbench 6 Single", "score": 3200 },
      { "name": "Geekbench 6 Multi", "score": 10200 },
      { "name": "Geekbench 6 GPU", "score": 18500 }
    ],
    "individual_scores": {
      "antutu_v10_values": [2245000, 2255000],
      "antutu_v11_values": [2490000, 2510000],
      "geekbench_6_single_values": [3180, 3220],
      "geekbench_6_multi_values": [10150, 10250],
      "geekbench_6_gpu_values": [18400, 18600]
    },
    "url": "https://nanoreview.net/en/benchmark-ranking/oneplus-15"
  }
}
```

#### 3. `gpu_benchmarks` - GPU Performance

```json
{
  "gpu_benchmarks": {
    "phone_name": "OnePlus 15",
    "gpu_benchmark": {
      "wildlife_extreme_peak": 4500,
      "wildlife_extreme_stability": 95.5
    }
  }
}
```

| Field | Description | Range |
|-------|-------------|-------|
| `wildlife_extreme_peak` | 3DMark Wildlife Extreme peak score | 0-8100+ |
| `wildlife_extreme_stability` | GPU stability percentage | 0-100% |

#### 4. `camera_benchmarks` - Camera Scores

```json
{
  "camera_benchmarks": {
    "phone_name": "OnePlus 15",
    "camera_benchmark": {
      "dxomark": 166,
      "phonearena": 152,
      "gsmarena": 4.6,
      "mobile91": 9.0
    }
  }
}
```

| Field | Source | Scale |
|-------|--------|-------|
| `dxomark` | DXOMARK Camera | 0-200+ |
| `phonearena` | PhoneArena Camera Rating | 0-200 (converted from 0-10) |
| `gsmarena` | GSMArena User Rating | 0-5 |
| `mobile91` | 91mobiles Rating | 0-10 |

#### 5. `shopping_links` - Purchase Links

```json
{
  "shopping_links": {
    "query": "OnePlus 15",
    "amazon": [
      {
        "title": "OnePlus 15 5G (Black, 256GB)",
        "link": "https://www.amazon.in/dp/B0XXXXX",
        "price": "₹64,999",
        "rating": "4.2 out of 5 stars",
        "source": "Amazon",
        "asin": "B0XXXXX"
      }
    ],
    "flipkart": [
      {
        "title": "OnePlus 15 (Black, 256 GB)",
        "link": "https://www.flipkart.com/oneplus-15/p/itmXXXXX",
        "price": "₹62,999",
        "rating": "4.3",
        "source": "Flipkart"
      }
    ],
    "direct_links": {
      "amazon_search": "https://www.amazon.in/s?k=OnePlus+15",
      "flipkart_search": "https://www.flipkart.com/search?q=OnePlus%2015"
    }
  }
}
```

#### 6. `image` - Phone Image

```json
{
  "image": {
    "success": true,
    "image_path": "storage/public/oneplus_15_nobg.png",
    "original_path": "storage/public/oneplus_15.png",
    "background_removed": true,
    "error": null
  }
}
```

| Field | Description |
|-------|-------------|
| `success` | Whether image fetch was successful |
| `image_path` | Path to the final image (with or without background) |
| `original_path` | Path to the original downloaded image |
| `background_removed` | Whether background was removed |
| `error` | Error message if failed |

#### 7. `errors` - Error Tracking

```json
{
  "errors": []
}
```

If any step fails, errors are logged:

```json
{
  "errors": [
    "GSMArena: Phone \"Unknown Phone\" not found on GSMArena",
    "Nanoreview: No benchmark scores found"
  ]
}
```

#### 8. `summary` - Execution Summary

```json
{
  "summary": {
    "total_steps": 6,
    "successful_steps": 6,
    "failed_steps": 0
  }
}
```

## Individual Scripts

The aggregator uses these individual scripts which can also be run separately:

### 1. GSMArena Scraper

```bash
python python/gsmarena_scraper.py "OnePlus 15"
```

### 2. Nanoreview Benchmark Scraper

```bash
python python/nanoreview_scraper.py "OnePlus 15" --benchmark --json
```

### 3. GPU Benchmark Fetcher

```bash
python python/gpu_benchmark_fetcher.py "OnePlus 15"
```

### 4. Camera Benchmark Fetcher

```bash
python python/camera_benchmark_fetcher.py "OnePlus 15"
```

### 5. Shopping Links Fetcher

```bash
python python/shopping_links.py "OnePlus 15" --json
```

### 6. Phone Image Fetcher

```bash
python python/phone_image_fetcher.py "OnePlus 15" --output ./image.png
```

## Error Handling

The script handles errors gracefully:

- **Network errors**: Retries with exponential backoff
- **Missing data**: Returns `null` for failed steps, continues with remaining steps
- **Anti-bot protection**: Uses Playwright and Cloudscraper for protected sites
- **Rate limiting**: Built-in delays between requests

## Notes

1. **Rate Limiting**: The script includes delays between requests to avoid being blocked
2. **Anti-Bot Protection**: Some sites (Flipkart, Nanoreview) use Cloudflare protection - Playwright handles this
3. **Image Quality**: Images are filtered for square aspect ratio (1:1) and minimum 600x600 resolution
4. **Background Removal**: Uses `rembg` library which requires additional dependencies

## Troubleshooting

### "No module named 'bs4'"
```bash
pip install beautifulsoup4
```

### "Playwright not installed"
```bash
pip install playwright
playwright install chromium
```

### "rembg not installed"
```bash
pip install rembg
# Or skip background removal with --no-bg-removal
```

### "Cloudflare challenge not solved"
- Wait a few minutes and try again
- Use a different IP/VPN
- The script will fall back to alternative methods

### "No images found"
- Try a different phone name variation
- Check internet connection
- Image search may be rate-limited, wait and retry
