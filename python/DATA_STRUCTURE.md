# Python Scripts Data Structure Documentation

This document describes all the data that can be obtained from each Python scraper script.

---

## 1. `shopping_links.py` - Amazon & Flipkart Shopping Links

### Usage
```bash
.venv/bin/python python/shopping_links.py "Oneplus 15"
.venv/bin/python python/shopping_links.py "iPhone 16" --json
```

### Output Structure (JSON)
```json
{
  "query": "Oneplus 15",
  "amazon": [
    {
      "title": "15 | 12GB+256GB | Infinite Black | India's First Snapdragon...",
      "link": "https://www.amazon.in/OnePlus-Infinite-...",
      "price": "72,998",
      "rating": "4.5 out of 5 stars",
      "source": "Amazon",
      "asin": "B0FTR5NGHJ"
    }
  ],
  "flipkart": [
    {
      "title": "OnePlus 15 5G (Sand Storm, 256 GB)",
      "link": "https://www.flipkart.com/oneplus-15-5g-...",
      "price": "72,947",
      "rating": "4.8",
      "source": "Flipkart"
    }
  ],
  "direct_links": {
    "amazon_search": "https://www.amazon.in/s?k=Oneplus+15",
    "flipkart_search": "https://www.flipkart.com/search?q=Oneplus%2015"
  }
}
```

### Available Fields Per Product
| Field | Description | Source |
|-------|-------------|--------|
| `title` | Product name/title | Amazon, Flipkart |
| `link` | Product page URL | Amazon, Flipkart |
| `price` | Current price (with currency symbol) | Amazon, Flipkart |
| `rating` | Customer rating (if available) | Amazon, Flipkart |
| `asin` | Amazon Standard Identification Number | Amazon only |
| `source` | "Amazon" or "Flipkart" | Both |

---

## 2. `nanoreview_scraper.py` - Detailed Phone Specifications

### Usage
```bash
.venv/bin/python python/nanoreview_scraper.py "Oneplus 15"
.venv/bin/python python/nanoreview_scraper.py "iPhone 16" --json
```

### Output Structure (JSON)
```json
{
  "url": "https://nanoreview.net/en/phone/oneplus-15",
  "source": "nanoreview.net",
  "name": "OnePlus 15",
  "image": "/common/images/phone/oneplus-15-mini@2x.jpeg",
  "score": "Display93",
  "specifications": {
    "Display": { ... },
    "Design and build": { ... },
    "Performance": { ... },
    "Benchmarks": { ... },
    "Memory": { ... },
    "Software": { ... },
    "Battery": { ... },
    "Main camera": { ... },
    "Selfie camera": { ... },
    "Connectivity": { ... },
    "Sound": { ... },
    "Other": { ... }
  },
  "benchmarks": { ... },
  "pros": [ ... ],
  "cons": [ ... ]
}
```

### Detailed Specifications Structure

#### Display
| Field | Example Value | Description |
|-------|---------------|-------------|
| `Type` | AMOLED | Display technology |
| `Size` | 6.78 inches | Screen diagonal |
| `Resolution` | 1272 x 2772 pixels | Pixel dimensions |
| `Aspect ratio` | 19.5:9 | Width:Height ratio |
| `PPI` | 450 ppi | Pixels per inch |
| `Refresh rate` | 165 Hz | Maximum refresh rate |
| `Adaptive refresh rate` | Yes (1-120 Hz) | LTPO support |
| `Max rated brightness` | 1800 nits | Typical brightness |
| `Max rated brightness in HDR` | 6000 nits | HDR peak brightness |
| `HDR support` | Yes, Dolby Vision | HDR formats |
| `Touch sampling rate` | 330 Hz | Touch response rate |
| `Screen protection` | Gorilla Glass Victus 2 | Glass type |
| `Screen-to-body ratio` | 91.2% | Screen coverage |
| `Display features` | DCI-P3, Always-On Display | Additional features |
| `RGB color space` | 99.9% | Color gamut coverage |
| `PWM` | 120 Hz | PWM dimming frequency |
| `Response time` | 3 ms | Pixel response time |
| `Contrast` | Infinity | Contrast ratio |

#### Design and Build
| Field | Example Value | Description |
|-------|---------------|-------------|
| `Height` | 161.42 mm (6.36 inches) | Device height |
| `Width` | 76.67 mm (3.02 inches) | Device width |
| `Thickness` | 8.1 mm (0.32 inches) | Device thickness |
| `Weight` | 211 g (7.44 oz) | Device weight |
| `Waterproof` | IP69 | IP rating |
| `Advanced cooling` | Vapor chamber | Cooling system |
| `Rear material` | Glass | Back panel material |
| `Frame material` | Metal | Frame material |
| `Colors` | Black, Gold, Purple | Available colors |
| `Fingerprint scanner` | Yes, in-display | Biometric security |

#### Performance
| Field | Example Value | Description |
|-------|---------------|-------------|
| `Chipset` | Qualcomm Snapdragon 8 Elite Gen 5 | SoC name |
| `Max clock` | 4600 MHz | Maximum CPU frequency |
| `CPU cores` | 8 (2 + 6) | Core count and arrangement |
| `Architecture` | 6 cores at 3.63 GHz... | Core details |
| `L3 cache` | 16 MB | Cache size |
| `Manufacturing` | TSMC | Foundry |
| `Lithography process` | 3 nanometers | Process node |
| `Neural processor (NPU)` | Hexagon | AI processor |
| `Graphics` | Adreno 840 | GPU name |
| `GPU shading units` | 1536 | Shader count |
| `GPU clock` | 1200 MHz | GPU frequency |
| `FLOPS` | ~3686.4 GFLOPS | Theoretical performance |

#### Benchmarks
| Field | Example Value | Description |
|-------|---------------|-------------|
| `Geekbench Compute (GPU)` | 27829 | GPU compute score |
| `Single-core score` | 3726 | Geekbench single-core |
| `Multi-core score` | 11199 | Geekbench multi-core |
| `CPU` | 1073348 | AnTuTu CPU score |
| `GPU` | 1388825 | AnTuTu GPU score |
| `Memory` | 445438 | AnTuTu Memory score |
| `UX` | 783318 | AnTuTu UX score |
| `Total score` | 3690929 | AnTuTu total score |

#### Memory
| Field | Example Value | Description |
|-------|---------------|-------------|
| `RAM size` | 12 GB | RAM capacity |
| `Memory type` | LPDDR5X | RAM type |
| `Memory clock` | 5333 MHz | RAM frequency |
| `Channels` | 4 | Memory channels |
| `Storage size` | 256 GB | Internal storage |
| `Storage type` | UFS 4.1 | Storage standard |
| `Memory card` | No | SD card support |

#### Software
| Field | Example Value | Description |
|-------|---------------|-------------|
| `Operating system` | Android 16 | OS version |
| `ROM` | OxygenOS 16 | Custom UI |
| `OS size` | 26 GB | System partition size |

#### Battery
| Field | Example Value | Description |
|-------|---------------|-------------|
| `Capacity` | 7300 mAh | Battery capacity |
| `Max charge power` | 120 W | Maximum charging speed |
| `Battery type` | Silicon-Carbon (Si/C) | Battery chemistry |
| `Replaceable` | No | User-replaceable |
| `Wireless charging` | Yes (50 W) | Qi charging |
| `Reverse charging` | Yes, (wireless) | Power share |
| `Bypass charging` | Yes | Pass-through charging |
| `Fast charging` | Yes (50% in 15 min) | Quick charge info |
| `Full charging time` | 0:43 hr | Time to full charge |
| `Web browsing` | 18:32 hr | Battery life test |
| `Watching video` | 28:44 hr | Video playback time |
| `Gaming` | 10:01 hr | Gaming battery life |
| `Standby` | 139 hr | Standby time |

#### Main Camera
| Field | Example Value | Description |
|-------|---------------|-------------|
| `Matrix` | 50 megapixels | Sensor resolution |
| `Image resolution` | 50 MP | Output resolution |
| `Zoom` | Optical, 3.5x | Zoom type |
| `Flash` | Dual LED | Flash type |
| `Stabilization` | Digital | OIS/EIS |
| `8K video recording` | Up to 30FPS | 8K video support |
| `4K video recording` | Up to 120FPS | 4K video support |
| `1080p video recording` | Up to 60FPS | FHD video support |
| `Slow motion` | 240 FPS (1080p) | Slow-mo support |
| `Angle of widest lens` | 116° | Ultrawide angle |
| `Lenses` | 1) Standard: 50 MP... | Camera setup |
| `Camera features` | Bokeh mode, Pro mode... | Features list |
| `Aperture` | f/2.0 | Aperture value |
| `Focal length` | 16 mm | Focal length |
| `Sensor` | OmniVision OV50D | Sensor model |
| `Sensor size` | 1/2.88" | Sensor dimensions |
| `Pixel size` | 0.612 micron | Pixel pitch |
| `Autofocus` | Phase autofocus | AF type |

#### Selfie Camera
| Field | Example Value | Description |
|-------|---------------|-------------|
| `Megapixels` | 32 megapixels | Resolution |
| `Image resolution` | 6560 x 4928 | Pixel dimensions |
| `Aperture` | f/2.4 | Aperture value |
| `Focal length` | 21 mm | Focal length |
| `Sensor` | Sony IMX709 | Sensor model |
| `Sensor size` | 1/2.74" | Sensor dimensions |
| `Pixel size` | 0.64 micron | Pixel pitch |
| `Autofocus` | Phase autofocus | AF type |
| `Stabilization` | Digital | Stabilization type |
| `Video resolution` | 2160p (4K) at 60 FPS | Video support |
| `Depth sensor (TOF 3D)` | No | 3D face unlock |

#### Connectivity
| Field | Example Value | Description |
|-------|---------------|-------------|
| `Wi-Fi standard` | Wi-Fi 7 (802.11 a/b/g/n/ac/ax/be) | Wi-Fi version |
| `Wi-Fi features` | Dual Band, MiMO, Hotspot... | Wi-Fi features |
| `Bluetooth version` | 6.0 | Bluetooth version |
| `Bluetooth features` | LE | Bluetooth features |
| `USB type` | USB Type-C | USB connector |
| `USB version` | 3.2 | USB standard |
| `USB features` | Charging, OTG... | USB capabilities |
| `DisplayPort` | Yes | Video out |
| `GPS` | GPS, GLONASS, Beidou... | Positioning systems |
| `NFC` | Yes | Contactless payment |
| `Infrared port` | Yes | IR blaster |
| `Number of SIM` | 2 | SIM slots |
| `Type of SIM card` | Nano | SIM format |
| `Multi SIM mode` | Standby | Dual SIM mode |
| `eSIM support` | Yes | eSIM capability |
| `Hybrid slot` | No | SD/SIM hybrid |
| `2G network` | GSM 850/900/1800/1900MHz | 2G bands |
| `3G network` | WCDMA 1/2/4/5/6/8/19 | 3G bands |
| `4G network` | LTE 1/2/3/4/5/7/8... | 4G bands |
| `5G support` | Yes | 5G capability |

#### Sound
| Field | Example Value | Description |
|-------|---------------|-------------|
| `Speakers` | Stereo | Speaker setup |
| `Headphone audio jack` | No | 3.5mm jack |
| `FM radio` | No | FM radio |
| `Dolby Atmos` | No | Audio enhancement |

#### Other
| Field | Example Value | Description |
|-------|---------------|-------------|
| `Category` | Flagship | Phone category |
| `Announced` | October 2025 | Announcement date |
| `Release date` | October 2025 | Release date |
| `Launch price (MSRP)` | $899 | Launch price |
| `Sensors` | Hall-effect, Proximity... | Sensor list |
| `Bundled charger` | Not included | Box contents |

---

## 3. `nanoreview_scraper.py` - Benchmark Ranking (with `--benchmark` flag)

### Usage
```bash
.venv/bin/python python/nanoreview_scraper.py "Oneplus 15" --benchmark
.venv/bin/python python/nanoreview_scraper.py "oneplus-15" --benchmark --json
```

### Output Structure (JSON)
```json
{
  "phone": "oneplus-15",
  "source": "nanoreview.net/benchmark-ranking",
  "scores": {
    "antutu_v10": 3690929,
    "antutu_v11": 3990737,
    "geekbench_6_single": 3619,
    "geekbench_6_multi": 11054,
    "geekbench_6_gpu": 24350,
    "average": 1544137
  },
  "benchmark_list": [
    {"name": "AnTuTu v10", "score": 3690929},
    {"name": "AnTuTu v11", "score": 3990737},
    {"name": "Geekbench 6 Single", "score": 3619},
    {"name": "Geekbench 6 Multi", "score": 11054},
    {"name": "Geekbench 6 GPU", "score": 24350}
  ],
  "individual_scores": {
    "antutu_v11_values": [3532526, 4427278, 4824534, ...],
    "geekbench_6_single_values": [3697, 3588, 3607, 3691, 3513],
    "geekbench_6_multi_values": [11547, 10893, 10732, 11559, 10543],
    "geekbench_6_gpu_values": [23864, 24727, 24461]
  },
  "url": "https://nanoreview.net/en/benchmark-ranking/oneplus-15"
}
```

### Available Fields

#### `scores` - Benchmark Averages
| Field | Type | Description |
|-------|------|-------------|
| `antutu_v10` | integer | Average AnTuTu v10 score (from summary table) |
| `antutu_v11` | integer | Average AnTuTu v11 score (from individual results) |
| `geekbench_6_single` | integer | Average Geekbench 6 single-core score |
| `geekbench_6_multi` | integer | Average Geekbench 6 multi-core score |
| `geekbench_6_gpu` | integer | Average Geekbench 6 GPU compute score |
| `average` | integer | Average of all benchmark averages |

#### `benchmark_list` - List of Benchmarks Used
Array of objects with:
| Field | Type | Description |
|-------|------|-------------|
| `name` | string | Benchmark name |
| `score` | integer | Average score for that benchmark |

#### `individual_scores` - Raw Individual Scores
| Field | Type | Description |
|-------|------|-------------|
| `antutu_v11_values` | array | All individual AnTuTu v11 scores submitted by users |
| `geekbench_6_single_values` | array | All individual Geekbench 6 single-core scores |
| `geekbench_6_multi_values` | array | All individual Geekbench 6 multi-core scores |
| `geekbench_6_gpu_values` | array | All individual Geekbench 6 GPU compute scores |

### Notes
- **AnTuTu v10 individual values**: Often not available as the page shows "No community results yet". The score comes from the summary table.
- **AnTuTu v11 individual values**: Typically 20-50 scores from community submissions
- **Geekbench scores**: Typically 3-10 scores from community submissions
- **Score filtering**: AnTuTu scores are filtered to only include values > 100,000 to avoid capturing non-score data

---

## 4. `gsmarena_scraper.py` - GSMArena Specifications

### Usage
```bash
.venv/bin/python python/gsmarena_scraper.py "Oneplus 15"
.venv/bin/python python/gsmarena_scraper.py "https://www.gsmarena.com/oneplus_15-13537.php"
```

### Output Structure (JSON)
```json
{
  "device_name": "OnePlus 15",
  "image_url": "https://www.gsmarena.com/...",
  "specifications": {
    "Network": {
      "Technology": "GSM / CDMA / HSPA / EVDO / LTE / 5G"
    },
    "Launch": {
      "Announced": "2025, October",
      "Status": "Available. Released 2025, October"
    },
    "Body": {
      "Dimensions": "161.4 x 76.7 x 8.1 mm",
      "Weight": "211 g",
      "Build": "Glass front (Gorilla Glass Victus 2), glass back, aluminum frame",
      "SIM": "Dual SIM (Nano-SIM, dual stand-by)"
    },
    "Display": {
      "Type": "AMOLED, 1B colors, 165Hz, Dolby Vision, HDR10+",
      "Size": "6.78 inches, 112.0 cm2",
      "Resolution": "1272 x 2772 pixels (~450 ppi density)"
    },
    "Platform": {
      "OS": "Android 16, OxygenOS 16",
      "Chipset": "Qualcomm SM8950 Snapdragon 8 Elite Gen 5 (3 nm)",
      "CPU": "Octa-core (2x4.61 GHz Oryon Gen 3 Prime + 6x3.63 GHz Oryon Gen 3 Performance)",
      "GPU": "Adreno 840"
    },
    "Memory": {
      "Card slot": "No",
      "Internal": "256GB 12GB RAM, 512GB 16GB RAM"
    },
    "Main Camera": { ... },
    "Selfie Camera": { ... },
    "Sound": { ... },
    "Comms": { ... },
    "Features": { ... },
    "Battery": { ... },
    "Misc": { ... },
    "Tests": { ... }
  }
}
```

### Available Specification Categories
- **Network** - Technology, 2G/3G/4G/5G bands
- **Launch** - Announcement date, status
- **Body** - Dimensions, weight, build, SIM
- **Display** - Type, size, resolution, protection
- **Platform** - OS, chipset, CPU, GPU
- **Memory** - Card slot, internal storage variants
- **Main Camera** - Dual/triple camera specs, video
- **Selfie Camera** - Front camera specs
- **Sound** - Loudspeaker, 3.5mm jack
- **Comms** - WLAN, Bluetooth, USB, NFC, radio
- **Features** - Sensors, extras
- **Battery** - Capacity, charging
- **Misc** - Colors, price, models
- **Tests** - Performance tests (if available)

---

## Summary: Data Available Per Source

| Data Type | Amazon | Flipkart | NanoReview | NanoReview (Benchmark) | GSMArena |
|-----------|--------|----------|------------|------------------------|----------|
| Product Title | Yes | Yes | Yes | Yes | Yes |
| Product Link | Yes | Yes | N/A | N/A | N/A |
| Price | Yes | Yes | Launch price | N/A | Launch price |
| Rating | Yes | Yes | User rating | N/A | N/A |
| Image | No | No | Yes | N/A | Yes |
| Full Specifications | No | No | Yes | N/A | Yes |
| Benchmarks | No | No | Yes | Yes | Yes |
| Individual Benchmark Scores | No | No | No | Yes | No |
| Battery Tests | No | No | Yes | N/A | Sometimes |
| Pros/Cons | No | No | Yes | N/A | No |
| User Reviews | No | No | Yes | N/A | Yes |

---

## Quick Reference Commands

```bash
# Get shopping links
.venv/bin/python python/shopping_links.py "Oneplus 15" --json

# Get detailed specs from NanoReview
.venv/bin/python python/nanoreview_scraper.py "Oneplus 15" --json

# Get benchmark ranking from NanoReview (AnTuTu v10/v11, Geekbench 6)
.venv/bin/python python/nanoreview_scraper.py "Oneplus 15" --benchmark --json

# Get specs from GSMArena
.venv/bin/python python/gsmarena_scraper.py "Oneplus 15"

# Get direct search URLs only (no scraping)
.venv/bin/python python/shopping_links.py "Oneplus 15" --direct-only