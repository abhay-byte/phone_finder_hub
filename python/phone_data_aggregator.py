#!/usr/bin/env python3
"""
Phone Data Aggregator Script

This script combines multiple data sources to provide complete phone information:
1. GSMArena scraper - specifications
2. Nanoreview scraper - benchmark scores only
3. Phone image fetcher - with background removal
4. GPU benchmark fetcher - 3DMark Wildlife Extreme scores
5. Camera benchmark fetcher - DXOMARK, PhoneArena, etc.
6. Shopping links - Amazon and Flipkart

Usage:
    python phone_data_aggregator.py "OnePlus 15"
    python phone_data_aggregator.py "iPhone 15 Pro" --output phone_data.json

Requirements:
    pip install requests beautifulsoup4 pillow icrawler ddgs playwright cloudscraper rembg
    playwright install chromium
"""

import argparse
import sys
import os
import json
import re
import tempfile
import time
import logging
from pathlib import Path
from typing import Dict, Any, Optional
from datetime import datetime

# Suppress verbose logging
logging.getLogger('icrawler').setLevel(logging.CRITICAL)
logging.getLogger('ddgs').setLevel(logging.CRITICAL)
logging.getLogger('urllib3').setLevel(logging.WARNING)

# Import functions from existing scripts
sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))

from gsmarena_scraper import search_gsmarena, scrape_gsmarena
from nanoreview_scraper import scrape_benchmark_ranking
from gpu_benchmark_fetcher import get_gpu_benchmarks
from camera_benchmark_fetcher import get_camera_benchmarks
from shopping_links import get_shopping_links, generate_direct_links

# Try importing optional dependencies
try:
    from PIL import Image
    import requests
    from icrawler.builtin import GoogleImageCrawler, BingImageCrawler
    HAS_IMAGE_LIBS = True
except ImportError:
    HAS_IMAGE_LIBS = False

try:
    from rembg import remove, new_session
    HAS_REMBG = True
except ImportError:
    HAS_REMBG = False


# Constants
MIN_IMAGE_SIZE = 600
DEFAULT_OUTPUT_DIR = "storage/app/public"


def sanitize_filename(name: str) -> str:
    """Sanitize a string to be used as a filename."""
    sanitized = re.sub(r'[<>:"/\\|?*]', '_', name)
    sanitized = sanitized.replace(' ', '_')
    sanitized = re.sub(r'_+', '_', sanitized)
    return sanitized.strip('_').lower()


def remove_background(input_path: str, output_path: str) -> bool:
    """
    Remove white background around the phone (not inside).
    Uses rembg library for background removal.
    
    Args:
        input_path: Path to input image
        output_path: Path to save processed image
        
    Returns:
        True if successful, False otherwise
    """
    if not HAS_REMBG:
        print("Warning: rembg not installed. Skipping background removal.", file=sys.stderr)
        print("Install with: pip install rembg", file=sys.stderr)
        return False
    
    try:
        print(f"Removing background from image...", file=sys.stderr)
        
        with open(input_path, 'rb') as input_file:
            input_data = input_file.read()
        
        if 'new_session' in globals():
            # Use isnet-general-use model for better quality on product images
            session = new_session("isnet-general-use")
            output_data = remove(input_data, session=session)
        else:
            output_data = remove(input_data)
        
        with open(output_path, 'wb') as output_file:
            output_file.write(output_data)
        
        print(f"Background removed and saved to: {output_path}", file=sys.stderr)
        return True
        
    except Exception as e:
        print(f"Error removing background: {e}", file=sys.stderr)
        return False


def search_and_download_images(query: str, output_dir: str, max_num: int = 30) -> list:
    """Search and download images using Google and Bing."""
    if not HAS_IMAGE_LIBS:
        print("Image libraries not available. Install with: pip install pillow icrawler", file=sys.stderr)
        return []
    
    downloaded_files = []
    
    import threading
    original_excepthook = threading.excepthook
    
    def silent_excepthook(args):
        pass
    
    threading.excepthook = silent_excepthook
    
    try:
        # Use Google Image Crawler
        try:
            google_crawler = GoogleImageCrawler(
                storage={'root_dir': output_dir},
                log_level='CRITICAL'
            )
            google_crawler.crawl(
                keyword=f"{query} phone product image",
                max_num=max_num,
                file_idx_offset=0
            )
        except Exception:
            pass
        
        # Use Bing Image Crawler
        try:
            bing_crawler = BingImageCrawler(
                storage={'root_dir': output_dir},
                log_level='CRITICAL'
            )
            bing_crawler.crawl(
                keyword=f"{query} official product",
                max_num=max_num,
                file_idx_offset='auto'
            )
        except Exception:
            pass
    finally:
        threading.excepthook = original_excepthook
    
    # Collect all downloaded files
    for f in os.listdir(output_dir):
        if f.lower().endswith(('.jpg', '.jpeg', '.png', '.webp')):
            downloaded_files.append(os.path.join(output_dir, f))
    
    return downloaded_files


def find_square_image(files: list, min_size: int = MIN_IMAGE_SIZE) -> tuple:
    """Find a square image from downloaded files."""
    if not HAS_IMAGE_LIBS:
        return None
    
    best_match = None
    best_size = 0
    
    for i, filepath in enumerate(files):
        try:
            print(f"Checking image {i+1}/{len(files)}...", end=' ', file=sys.stderr)
            
            with Image.open(filepath) as img:
                width, height = img.size
            
            # Check if square (within 5% tolerance)
            ratio = width / height if height > 0 else 0
            is_square = 0.95 <= ratio <= 1.05
            
            print(f"{width}x{height}, square={is_square}", file=sys.stderr)
            
            # Check if meets criteria
            if is_square and width >= min_size:
                if width > best_size:
                    best_size = width
                    best_match = (filepath, width, height)
                    print(f"  -> Best candidate so far: {best_size}x{best_size}", file=sys.stderr)
        
        except Exception as e:
            print(f"error: {e}", file=sys.stderr)
            continue
    
    return best_match


def fetch_phone_image(phone_name: str, output_dir: str = DEFAULT_OUTPUT_DIR, remove_bg: bool = True) -> Dict[str, Any]:
    """
    Fetch phone image with optional background removal.
    
    Args:
        phone_name: Name of the phone
        output_dir: Directory to save the image
        remove_bg: Whether to remove background
        
    Returns:
        Dictionary with image path and status
    """
    result = {
        'success': False,
        'image_path': None,
        'original_path': None,
        'background_removed': False,
        'error': None
    }
    
    if not HAS_IMAGE_LIBS:
        result['error'] = 'Image libraries not installed. Run: pip install pillow icrawler'
        return result
    
    # Create output directory if it doesn't exist
    os.makedirs(output_dir, exist_ok=True)
    
    safe_name = sanitize_filename(phone_name)
    
    with tempfile.TemporaryDirectory() as temp_dir:
        print(f"Searching for phone image: {phone_name}", file=sys.stderr)
        
        # Download images
        files = search_and_download_images(phone_name, temp_dir, max_num=30)
        
        if not files:
            result['error'] = 'No images found'
            return result
        
        print(f"Downloaded {len(files)} images", file=sys.stderr)
        
        # Find a suitable square image
        match = find_square_image(files, MIN_IMAGE_SIZE)
        
        if not match:
            result['error'] = f'No square image of at least {MIN_IMAGE_SIZE}x{MIN_IMAGE_SIZE} found'
            return result
        
        filepath, width, height = match
        print(f"Found suitable image: {width}x{height}", file=sys.stderr)
        
        # Determine output paths
        original_path = os.path.join(output_dir, f"{safe_name}.png")
        processed_path = os.path.join(output_dir, f"{safe_name}_nobg.png")
        
        # Copy original image
        with Image.open(filepath) as img:
            if img.mode == 'RGBA':
                img.save(original_path, 'PNG', optimize=True)
            elif img.mode == 'RGB':
                img.save(original_path, 'PNG', optimize=True)
            else:
                img = img.convert('RGB')
                img.save(original_path, 'PNG', optimize=True)
        
        result['original_path'] = original_path
        print(f"Original image saved to: {original_path}", file=sys.stderr)
        
        # Remove background if requested
        if remove_bg and HAS_REMBG:
            bg_removed = remove_background(original_path, processed_path)
            if bg_removed:
                result['image_path'] = processed_path
                result['background_removed'] = True
            else:
                result['image_path'] = original_path
        else:
            result['image_path'] = original_path
        
        result['success'] = True
    
    return result


def get_gsmarena_data(phone_name: str) -> Dict[str, Any]:
    """
    Get phone specifications from GSMArena.
    
    Args:
        phone_name: Name of the phone
        
    Returns:
        Dictionary with GSMArena data
    """
    result = {
        'success': False,
        'data': None,
        'url': None,
        'error': None
    }
    
    try:
        print(f"\n{'='*60}", file=sys.stderr)
        print("STEP 1: Fetching GSMArena specifications...", file=sys.stderr)
        print(f"{'='*60}", file=sys.stderr)
        
        # Search for the phone
        url = search_gsmarena(phone_name)
        
        if not url:
            result['error'] = f'Phone "{phone_name}" not found on GSMArena'
            return result
        
        result['url'] = url
        print(f"Found GSMArena URL: {url}", file=sys.stderr)
        
        # Scrape the data
        data = scrape_gsmarena(url)
        
        if data:
            result['success'] = True
            result['data'] = data
        else:
            result['error'] = 'Failed to scrape GSMArena data'
        
    except Exception as e:
        result['error'] = str(e)
        print(f"GSMArena error: {e}", file=sys.stderr)
    
    return result


def get_nanoreview_benchmarks(phone_name: str) -> Dict[str, Any]:
    """
    Get benchmark scores from Nanoreview.
    
    Args:
        phone_name: Name of the phone
        
    Returns:
        Dictionary with benchmark data
    """
    result = {
        'success': False,
        'data': None,
        'error': None
    }
    
    try:
        print(f"\n{'='*60}", file=sys.stderr)
        print("STEP 2: Fetching Nanoreview benchmark scores...", file=sys.stderr)
        print(f"{'='*60}", file=sys.stderr)
        
        data = scrape_benchmark_ranking(phone_name)
        
        if data and 'scores' in data and data['scores']:
            result['success'] = True
            result['data'] = data
        else:
            result['error'] = data.get('error', 'No benchmark scores found')
            result['data'] = data  # Still include partial data
        
    except Exception as e:
        result['error'] = str(e)
        print(f"Nanoreview error: {e}", file=sys.stderr)
    
    return result


def get_gpu_benchmark_data(phone_name: str) -> Dict[str, Any]:
    """
    Get GPU benchmark scores.
    
    Args:
        phone_name: Name of the phone
        
    Returns:
        Dictionary with GPU benchmark data
    """
    result = {
        'success': False,
        'data': None,
        'error': None
    }
    
    try:
        print(f"\n{'='*60}", file=sys.stderr)
        print("STEP 3: Fetching GPU benchmark scores...", file=sys.stderr)
        print(f"{'='*60}", file=sys.stderr)
        
        data = get_gpu_benchmarks(phone_name)
        
        if data and data.get('gpu_benchmark'):
            result['success'] = True
            result['data'] = data
        else:
            result['error'] = 'No GPU benchmark data found'
            result['data'] = data
        
    except Exception as e:
        result['error'] = str(e)
        print(f"GPU benchmark error: {e}", file=sys.stderr)
    
    return result


def get_camera_benchmark_data(phone_name: str) -> Dict[str, Any]:
    """
    Get camera benchmark scores.
    
    Args:
        phone_name: Name of the phone
        
    Returns:
        Dictionary with camera benchmark data
    """
    result = {
        'success': False,
        'data': None,
        'error': None
    }
    
    try:
        print(f"\n{'='*60}", file=sys.stderr)
        print("STEP 4: Fetching camera benchmark scores...", file=sys.stderr)
        print(f"{'='*60}", file=sys.stderr)
        
        data = get_camera_benchmarks(phone_name)
        
        if data and data.get('camera_benchmark'):
            result['success'] = True
            result['data'] = data
        else:
            result['error'] = 'No camera benchmark data found'
            result['data'] = data
        
    except Exception as e:
        result['error'] = str(e)
        print(f"Camera benchmark error: {e}", file=sys.stderr)
    
    return result


def get_shopping_data(phone_name: str, max_results: int = 3) -> Dict[str, Any]:
    """
    Get shopping links from Amazon and Flipkart.
    
    Args:
        phone_name: Name of the phone
        max_results: Maximum results per store
        
    Returns:
        Dictionary with shopping data
    """
    result = {
        'success': False,
        'data': None,
        'error': None
    }
    
    try:
        print(f"\n{'='*60}", file=sys.stderr)
        print("STEP 5: Fetching shopping links...", file=sys.stderr)
        print(f"{'='*60}", file=sys.stderr)
        
        data = get_shopping_links(phone_name, max_results=max_results)
        
        if data:
            result['success'] = True
            result['data'] = data
        else:
            result['error'] = 'No shopping links found'
        
    except Exception as e:
        result['error'] = str(e)
        print(f"Shopping links error: {e}", file=sys.stderr)
    
    return result


def get_image_data(phone_name: str, output_dir: str, remove_bg: bool = True) -> Dict[str, Any]:
    """
    Get phone image with background removal.
    
    Args:
        phone_name: Name of the phone
        output_dir: Directory to save images
        remove_bg: Whether to remove background
        
    Returns:
        Dictionary with image data
    """
    result = {
        'success': False,
        'data': None,
        'error': None
    }
    
    try:
        print(f"\n{'='*60}", file=sys.stderr)
        print("STEP 6: Fetching phone image...", file=sys.stderr)
        print(f"{'='*60}", file=sys.stderr)
        
        data = fetch_phone_image(phone_name, output_dir, remove_bg)
        
        if data.get('success'):
            result['success'] = True
            result['data'] = data
        else:
            result['error'] = data.get('error', 'Failed to fetch image')
            result['data'] = data
        
    except Exception as e:
        result['error'] = str(e)
        print(f"Image fetch error: {e}", file=sys.stderr)
    
    return result


def aggregate_phone_data(phone_name: str, output_dir: str = DEFAULT_OUTPUT_DIR, 
                         remove_bg: bool = True, skip_steps: list = None) -> Dict[str, Any]:
    """
    Aggregate all phone data from multiple sources.
    
    Args:
        phone_name: Name of the phone
        output_dir: Directory for images
        remove_bg: Whether to remove image background
        skip_steps: List of steps to skip ('gsmarena', 'nanoreview', 'gpu', 'camera', 'shopping', 'image')
        
    Returns:
        Complete aggregated phone data
    """
    skip_steps = skip_steps or []
    
    result = {
        'phone_name': phone_name,
        'timestamp': datetime.now().isoformat(),
        'gsmarena': None,
        'nanoreview_benchmarks': None,
        'gpu_benchmarks': None,
        'camera_benchmarks': None,
        'shopping_links': None,
        'image': None,
        'errors': [],
        'summary': {
            'total_steps': 6,
            'successful_steps': 0,
            'failed_steps': 0
        }
    }
    
    print(f"\n{'#'*60}", file=sys.stderr)
    print(f"# AGGREGATING DATA FOR: {phone_name}", file=sys.stderr)
    print(f"{'#'*60}", file=sys.stderr)
    
    # Step 1: GSMArena specifications
    if 'gsmarena' not in skip_steps:
        gsmarena_result = get_gsmarena_data(phone_name)
        if gsmarena_result['success']:
            result['gsmarena'] = gsmarena_result['data']
            result['summary']['successful_steps'] += 1
        else:
            result['errors'].append(f"GSMArena: {gsmarena_result['error']}")
            result['summary']['failed_steps'] += 1
        time.sleep(1)  # Rate limiting
    else:
        result['summary']['total_steps'] -= 1
    
    # Step 2: Nanoreview benchmarks
    if 'nanoreview' not in skip_steps:
        nanoreview_result = get_nanoreview_benchmarks(phone_name)
        if nanoreview_result['success']:
            result['nanoreview_benchmarks'] = nanoreview_result['data']
            result['summary']['successful_steps'] += 1
        else:
            result['errors'].append(f"Nanoreview: {nanoreview_result['error']}")
            result['summary']['failed_steps'] += 1
        time.sleep(1)
    else:
        result['summary']['total_steps'] -= 1
    
    # Step 3: GPU benchmarks
    if 'gpu' not in skip_steps:
        gpu_result = get_gpu_benchmark_data(phone_name)
        if gpu_result['success']:
            result['gpu_benchmarks'] = gpu_result['data']
            result['summary']['successful_steps'] += 1
        else:
            result['errors'].append(f"GPU Benchmarks: {gpu_result['error']}")
            result['summary']['failed_steps'] += 1
        time.sleep(1)
    else:
        result['summary']['total_steps'] -= 1
    
    # Step 4: Camera benchmarks
    if 'camera' not in skip_steps:
        camera_result = get_camera_benchmark_data(phone_name)
        if camera_result['success']:
            result['camera_benchmarks'] = camera_result['data']
            result['summary']['successful_steps'] += 1
        else:
            result['errors'].append(f"Camera Benchmarks: {camera_result['error']}")
            result['summary']['failed_steps'] += 1
        time.sleep(1)
    else:
        result['summary']['total_steps'] -= 1
    
    # Step 5: Shopping links
    if 'shopping' not in skip_steps:
        shopping_result = get_shopping_data(phone_name)
        if shopping_result['success']:
            result['shopping_links'] = shopping_result['data']
            result['summary']['successful_steps'] += 1
        else:
            result['errors'].append(f"Shopping Links: {shopping_result['error']}")
            result['summary']['failed_steps'] += 1
        time.sleep(1)
    else:
        result['summary']['total_steps'] -= 1
    
    # Step 6: Phone image
    if 'image' not in skip_steps:
        image_result = get_image_data(phone_name, output_dir, remove_bg)
        if image_result['success']:
            result['image'] = image_result['data']
            result['summary']['successful_steps'] += 1
        else:
            result['errors'].append(f"Image: {image_result['error']}")
            result['summary']['failed_steps'] += 1
    else:
        result['summary']['total_steps'] -= 1
    
    # Print summary
    print(f"\n{'#'*60}", file=sys.stderr)
    print("# AGGREGATION COMPLETE", file=sys.stderr)
    print(f"{'#'*60}", file=sys.stderr)
    print(f"Successful steps: {result['summary']['successful_steps']}/{result['summary']['total_steps']}", file=sys.stderr)
    
    if result['errors']:
        print(f"\nErrors encountered:", file=sys.stderr)
        for error in result['errors']:
            print(f"  - {error}", file=sys.stderr)
    
    return result


def main():
    parser = argparse.ArgumentParser(
        description='Aggregate phone data from multiple sources.',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog='''
Examples:
    python phone_data_aggregator.py "OnePlus 15"
    python phone_data_aggregator.py "iPhone 15 Pro" --output phone_data.json
    python phone_data_aggregator.py "Samsung Galaxy S24" --image-dir ./images
    python phone_data_aggregator.py "OnePlus 15" --skip nanoreview,gpu
    python phone_data_aggregator.py "OnePlus 15" --no-bg-removal

Data sources:
    1. GSMArena - Phone specifications
    2. Nanoreview - Benchmark scores (AnTuTu, Geekbench)
    3. GPU Benchmarks - 3DMark Wildlife Extreme
    4. Camera Benchmarks - DXOMARK, PhoneArena, GSMArena, 91mobiles
    5. Shopping Links - Amazon.in, Flipkart
    6. Phone Image - Google/Bing image search with background removal
        '''
    )
    
    parser.add_argument(
        'phone_name',
        help='Name of the phone to search for'
    )
    
    parser.add_argument(
        '--output', '-o',
        help='Output JSON file path',
        default=None
    )
    
    parser.add_argument(
        '--image-dir',
        help='Directory to save phone images (default: storage/public/)',
        default=DEFAULT_OUTPUT_DIR
    )
    
    parser.add_argument(
        '--no-bg-removal',
        action='store_true',
        help='Skip background removal for images'
    )
    
    parser.add_argument(
        '--skip',
        help='Comma-separated list of steps to skip (gsmarena,nanoreview,gpu,camera,shopping,image)',
        default=None
    )
    
    parser.add_argument(
        '--max-shopping-results',
        type=int,
        default=3,
        help='Maximum shopping results per store (default: 3)'
    )
    
    args = parser.parse_args()
    
    # Parse skip steps
    skip_steps = []
    if args.skip:
        skip_steps = [s.strip().lower() for s in args.skip.split(',')]
    
    # Aggregate data
    result = aggregate_phone_data(
        phone_name=args.phone_name,
        output_dir=args.image_dir,
        remove_bg=not args.no_bg_removal,
        skip_steps=skip_steps
    )
    
    # Output results
    json_output = json.dumps(result, indent=2, ensure_ascii=False)
    
    if args.output:
        with open(args.output, 'w', encoding='utf-8') as f:
            f.write(json_output)
        print(f"\nResults saved to: {args.output}", file=sys.stderr)
    
    # Print to stdout
    print(json_output)


if __name__ == "__main__":
    main()
