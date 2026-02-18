#!/usr/bin/env python3
"""
Phone Image Fetcher Script

This script searches the web for high-quality phone images that are:
- At least 600x600 pixels
- 1:1 aspect ratio (square)
- High quality

Usage:
    python phone_image_fetcher.py "iPhone 15 Pro" [--output ./output.png]

Requirements:
    pip install requests pillow icrawler
"""

import argparse
import sys
import os
import re
import io
import tempfile
import warnings
import threading
from pathlib import Path

# Suppress threading exceptions from icrawler
import logging
logging.getLogger('icrawler').setLevel(logging.CRITICAL)

try:
    import requests
    from PIL import Image
    from icrawler.builtin import GoogleImageCrawler, BingImageCrawler
except ImportError as e:
    print(f"Missing dependency: {e}", file=sys.stderr)
    print("Please install required packages:", file=sys.stderr)
    print("pip install requests pillow icrawler", file=sys.stderr)
    sys.exit(1)

# Minimum output size for high quality
MIN_SIZE = 600


def search_and_download_images(query: str, output_dir: str, max_num: int = 30) -> list[str]:
    """Search and download images using Google and Bing."""
    downloaded_files = []
    
    # Suppress threading exceptions from icrawler
    import threading
    original_excepthook = threading.excepthook
    
    def silent_excepthook(args):
        pass  # Silently ignore threading exceptions
    
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
        except Exception as e:
            pass  # Silently ignore errors
        
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
        except Exception as e:
            pass  # Silently ignore errors
    finally:
        # Restore original excepthook
        threading.excepthook = original_excepthook
    
    # Collect all downloaded files
    for f in os.listdir(output_dir):
        if f.lower().endswith(('.jpg', '.jpeg', '.png', '.webp')):
            downloaded_files.append(os.path.join(output_dir, f))
    
    return downloaded_files


def find_square_image(files: list[str], min_size: int = MIN_SIZE) -> tuple[str, int, int] | None:
    """Find a square image from downloaded files."""
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


def sanitize_filename(name: str) -> str:
    """Sanitize a string to be used as a filename."""
    sanitized = re.sub(r'[<>:"/\\|?*]', '_', name)
    sanitized = sanitized.replace(' ', '_')
    sanitized = re.sub(r'_+', '_', sanitized)
    return sanitized.strip('_').lower()


def main():
    parser = argparse.ArgumentParser(
        description='Fetch high-quality square phone images (600x600 or larger, 1:1 aspect ratio).',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Examples:
    python phone_image_fetcher.py "iPhone 15 Pro"
    python phone_image_fetcher.py "Samsung Galaxy S24 Ultra" --output ./s24.png
    python phone_image_fetcher.py "OnePlus 13" --output ./oneplus13.png
        """
    )
    
    parser.add_argument(
        'phone_name',
        help='Name of the phone to search for'
    )
    
    parser.add_argument(
        '--output', '-o',
        help='Output file path (default: phone_name.png in current directory)',
        default=None
    )
    
    parser.add_argument(
        '--min-size',
        type=int,
        default=MIN_SIZE,
        help=f'Minimum image size (default: {MIN_SIZE})'
    )
    
    args = parser.parse_args()
    
    # Determine output path
    if args.output:
        output_path = Path(args.output)
    else:
        safe_name = sanitize_filename(args.phone_name)
        output_path = Path(f"{safe_name}.png")
    
    # Create temporary directory for downloads
    with tempfile.TemporaryDirectory() as temp_dir:
        print(f"Searching for: {args.phone_name}", file=sys.stderr)
        
        # Download images
        files = search_and_download_images(args.phone_name, temp_dir, max_num=30)
        
        if not files:
            print("No images found.", file=sys.stderr)
            sys.exit(1)
        
        print(f"Downloaded {len(files)} images", file=sys.stderr)
        
        # Find a suitable square image
        match = find_square_image(files, args.min_size)
        
        if not match:
            print(f"No square image of at least {args.min_size}x{args.min_size} found.", file=sys.stderr)
            sys.exit(1)
        
        filepath, width, height = match
        print(f"\nFound suitable image: {width}x{height}", file=sys.stderr)
        
        # Copy to output path
        with Image.open(filepath) as img:
            # Convert to RGB if needed for PNG
            if img.mode == 'RGBA':
                img.save(output_path, 'PNG', optimize=True)
            elif img.mode == 'RGB':
                img.save(output_path, 'PNG', optimize=True)
            else:
                img = img.convert('RGB')
                img.save(output_path, 'PNG', optimize=True)
        
        print(f"Image saved to: {output_path}", file=sys.stderr)
        print(str(output_path.resolve()))


if __name__ == "__main__":
    main()
