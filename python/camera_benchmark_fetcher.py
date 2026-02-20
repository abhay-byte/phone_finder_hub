#!/usr/bin/env python3
"""
Camera Benchmark Fetcher Script

This script fetches camera benchmark scores by searching the web for:
- DXOMARK camera score
- PhoneArena camera rating
- GSMArena user rating
- 91mobiles rating

Usage:
    python camera_benchmark_fetcher.py "iPhone 15 Pro" [--output camera_scores.json]

Requirements:
    pip install requests beautifulsoup4 ddgs
"""

import argparse
import sys
import re
import json
import time
import base64
import os
import tempfile
import threading
import logging
from typing import Optional
from urllib.parse import quote_plus

# Suppress icrawler/duckduckgo_search logging
logging.getLogger('icrawler').setLevel(logging.CRITICAL)
logging.getLogger('duckduckgo_search').setLevel(logging.CRITICAL)

try:
    import requests
    from bs4 import BeautifulSoup
    from duckduckgo_search import DDGS
    from PIL import Image
    from icrawler.builtin import GoogleImageCrawler, BingImageCrawler
except ImportError as e:
    print(f"Missing dependency: {e}", file=sys.stderr)
    print("Please install required packages:", file=sys.stderr)
    print("pip install requests beautifulsoup4 duckduckgo-search icrawler pillow", file=sys.stderr)
    sys.exit(1)

# Headers for web requests
HEADERS = {
    'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
    'Accept-Language': 'en-US,en;q=0.5',
}


def web_search(query: str, max_results: int = 10) -> list[dict]:
    """Search the web using DuckDuckGo."""
    try:
        results = []
        with DDGS() as ddgs:
            search_results = list(ddgs.text(query, max_results=max_results))
            for r in search_results:
                results.append({
                    'title': r.get('title', ''),
                    'url': r.get('href') or r.get('url', ''),
                    'snippet': r.get('body', ''),
                })
        return results
    except Exception as e:
        print(f"Web search failed: {e}", file=sys.stderr)
        return []


def make_request(url: str) -> Optional[requests.Response]:
    """Make HTTP request."""
    try:
        response = requests.get(url, headers=HEADERS, timeout=30)
        response.raise_for_status()
        return response
    except Exception as e:
        return None


def search_dxomark_score(phone_name: str) -> Optional[int]:
    """Search for DXOMARK camera score."""
    try:
        print("Searching DXOMARK...", file=sys.stderr)
        
        # Search for DXOMARK score
        query = f"{phone_name} DXOMARK camera score"
        results = web_search(query, max_results=5)
        
        for result in results:
            url = result['url']
            snippet = result['snippet']
            title = result['title']
            
            # Look for score in snippet
            # DXOMARK scores are typically 100-200+ for modern phones
            score_match = re.search(r'(?:score|rating)[:\s]*(\d{2,3})', snippet, re.IGNORECASE)
            if score_match:
                score = int(score_match.group(1))
                if 50 <= score <= 250:  # Reasonable DXOMARK range
                    return score
            
            # Look for pattern like "154 points" or "score of 154"
            points_match = re.search(r'(\d{2,3})\s*(?:points|score)', snippet, re.IGNORECASE)
            if points_match:
                score = int(points_match.group(1))
                if 50 <= score <= 250:
                    return score
            
            # If URL is DXOMARK, try to scrape the page
            if 'dxomark' in url.lower():
                response = make_request(url)
                if response:
                    soup = BeautifulSoup(response.text, 'html.parser')
                    page_text = soup.get_text()
                    
                    # Look for camera score
                    score_patterns = [
                        r'camera\s*score[:\s]*(\d{2,3})',
                        r'(\d{2,3})\s*points',
                        r'score[:\s]*(\d{2,3})',
                    ]
                    for pattern in score_patterns:
                        match = re.search(pattern, page_text, re.IGNORECASE)
                        if match:
                            score = int(match.group(1))
                            if 50 <= score <= 250:
                                return score
        
        return None
        
    except Exception as e:
        print(f"DXOMARK search failed: {e}", file=sys.stderr)
        return None


def search_phonearena_score(phone_name: str) -> Optional[int]:
    """Search for PhoneArena camera score."""
    try:
        print("Searching PhoneArena...", file=sys.stderr)
        
        # Search for PhoneArena camera review
        query = f"{phone_name} PhoneArena camera review rating"
        results = web_search(query, max_results=5)
        
        for result in results:
            url = result['url']
            snippet = result['snippet']
            
            # Look for rating in snippet (typically X/10)
            rating_match = re.search(r'(\d+(?:\.\d+)?)\s*(?:\/\s*10|out of 10)', snippet, re.IGNORECASE)
            if rating_match:
                rating = float(rating_match.group(1))
                # Convert to 0-200 scale
                return int(rating * 20)
            
            # If URL is PhoneArena, try to scrape
            if 'phonearena' in url.lower():
                response = make_request(url)
                if response:
                    soup = BeautifulSoup(response.text, 'html.parser')
                    page_text = soup.get_text()
                    
                    # Look for camera rating
                    rating_patterns = [
                        r'camera\s*(?:rating|score)[:\s]*(\d+(?:\.\d+)?)',
                        r'(\d+(?:\.\d+)?)\s*(?:\/\s*10|out of 10)',
                    ]
                    for pattern in rating_patterns:
                        match = re.search(pattern, page_text, re.IGNORECASE)
                        if match:
                            rating = float(match.group(1))
                            if rating <= 10:
                                return int(rating * 20)
                            return int(rating)
        
        return None
        
    except Exception as e:
        print(f"PhoneArena search failed: {e}", file=sys.stderr)
        return None


def decrypt_gsmarena_data(iv_b64: str, key_b64: str, data_b64: str) -> Optional[str]:
    """Decrypt GSMArena encrypted search results."""
    try:
        from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes
        from cryptography.hazmat.backends import default_backend
        
        iv = base64.b64decode(iv_b64)
        key = base64.b64decode(key_b64)
        data = base64.b64decode(data_b64)

        cipher = Cipher(algorithms.AES(key), modes.CBC(iv), backend=default_backend())
        decryptor = cipher.decryptor()
        decrypted_padded = decryptor.update(data) + decryptor.finalize()
        
        padding_len = decrypted_padded[-1]
        decrypted = decrypted_padded[:-padding_len]
        return decrypted.decode('utf-8')
    except:
        return None


def search_gsmarena_score(phone_name: str) -> Optional[float]:
    """Search for GSMArena camera/user rating."""
    try:
        print("Searching GSMArena...", file=sys.stderr)
        
        # First try direct search on GSMArena
        search_url = f"https://www.gsmarena.com/res.php3?sSearch={quote_plus(phone_name)}"
        response = make_request(search_url)
        
        phone_url = None
        
        if response:
            html_content = response.text
            
            # Check for encryption
            key_match = re.search(r'const KEY\s*=\s*"([^"]+)"', html_content)
            iv_match = re.search(r'const IV\s*=\s*"([^"]+)"', html_content)
            data_match = re.search(r'const DATA\s*=\s*"([^"]+)"', html_content)
            
            if key_match and iv_match and data_match:
                decrypted = decrypt_gsmarena_data(iv_match.group(1), key_match.group(1), data_match.group(1))
                if decrypted:
                    soup = BeautifulSoup(decrypted, 'html.parser')
                    makers = soup.select('.makers ul li a')
                    if makers:
                        phone_url = f"https://www.gsmarena.com/{makers[0]['href']}"
            else:
                soup = BeautifulSoup(html_content, 'html.parser')
                makers = soup.select('.makers ul li a')
                if makers:
                    phone_url = f"https://www.gsmarena.com/{makers[0]['href']}"
        
        # If direct search failed, try web search
        if not phone_url:
            query = f"{phone_name} site:gsmarena.com"
            results = web_search(query, max_results=3)
            
            for result in results:
                if 'gsmarena.com' in result['url'].lower() and 'review' not in result['url'].lower():
                    phone_url = result['url']
                    break
        
        if phone_url:
            phone_response = make_request(phone_url)
            if phone_response:
                soup = BeautifulSoup(phone_response.text, 'html.parser')
                
                # Look for user rating (typically X/5)
                rating_selectors = [
                    '.rating',
                    '[class*="score"]',
                    '.user-rating',
                ]
                
                for selector in rating_selectors:
                    rating_elem = soup.select_one(selector)
                    if rating_elem:
                        rating_text = rating_elem.get_text(strip=True)
                        match = re.search(r'(\d+(?:\.\d+)?)\s*(?:\/\s*(\d+))?', rating_text)
                        if match:
                            rating = float(match.group(1))
                            max_val = float(match.group(2)) if match.group(2) else 5
                            if max_val != 5:
                                rating = (rating / max_val) * 5
                            return round(rating, 1)
        
        return None
        
    except Exception as e:
        print(f"GSMArena search failed: {e}", file=sys.stderr)
        return None


def search_91mobiles_score(phone_name: str) -> Optional[float]:
    """Search for 91mobiles rating."""
    try:
        print("Searching 91mobiles...", file=sys.stderr)
        
        # Search for 91mobiles review
        query = f"{phone_name} 91mobiles review rating"
        results = web_search(query, max_results=5)
        
        for result in results:
            url = result['url']
            snippet = result['snippet']
            
            # Look for rating in snippet (typically X/10)
            rating_match = re.search(r'(\d+(?:\.\d+)?)\s*(?:\/\s*10|out of 10)', snippet, re.IGNORECASE)
            if rating_match:
                return float(rating_match.group(1))
            
            # If URL is 91mobiles, try to scrape
            if '91mobiles' in url.lower():
                response = make_request(url)
                if response:
                    soup = BeautifulSoup(response.text, 'html.parser')
                    page_text = soup.get_text()
                    
                    # Look for rating
                    rating_patterns = [
                        r'rating[:\s]*(\d+(?:\.\d+)?)\s*(?:\/\s*10)?',
                        r'(\d+(?:\.\d+)?)\s*(?:\/\s*10|out of 10)',
                    ]
                    for pattern in rating_patterns:
                        match = re.search(pattern, page_text, re.IGNORECASE)
                        if match:
                            rating = float(match.group(1))
                            if rating <= 10:
                                return round(rating, 1)
        
        return None
        
    except Exception as e:
        print(f"91mobiles search failed: {e}", file=sys.stderr)
        return None


def get_camera_benchmarks(phone_name: str) -> dict:
    """Get camera benchmark scores from all sources."""
    results = {
        'phone_name': phone_name,
        'camera_benchmark': {
            'dxomark': 0,
            'phonearena': 0,
            'gsmarena': 0.0,
            'mobile91': 0.0
        }
    }
    
    # DXOMARK (0-200+ scale)
    dxomark_score = search_dxomark_score(phone_name)
    if dxomark_score:
        results['camera_benchmark']['dxomark'] = dxomark_score
    time.sleep(0.5)
    
    # PhoneArena (converted to 0-200 scale)
    phonearena_score = search_phonearena_score(phone_name)
    if phonearena_score:
        results['camera_benchmark']['phonearena'] = phonearena_score
    time.sleep(0.5)
    
    # GSMArena (0-5 scale)
    gsmarena_score = search_gsmarena_score(phone_name)
    if gsmarena_score:
        results['camera_benchmark']['gsmarena'] = gsmarena_score
    time.sleep(0.5)
    
    # 91mobiles (0-10 scale)
    mobile91_score = search_91mobiles_score(phone_name)
    if mobile91_score:
        results['camera_benchmark']['mobile91'] = mobile91_score
    
    return results


def main():
    parser = argparse.ArgumentParser(
        description='Fetch camera benchmark scores from various sources.',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Output format:
{
    "phone_name": "iPhone 15 Pro",
    "camera_benchmark": {
        "dxomark": 166,      # 0-200+ scale
        "phonearena": 152,   # 0-200 scale (converted from 0-10)
        "gsmarena": 4.6,     # 0-5 scale
        "mobile91": 9.0      # 0-10 scale
    }
}

Examples:
    python camera_benchmark_fetcher.py "iPhone 15 Pro"
    python camera_benchmark_fetcher.py "Samsung Galaxy S24 Ultra" --output scores.json
        """
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
    
    args = parser.parse_args()
    
    print(f"Fetching camera benchmarks for: {args.phone_name}", file=sys.stderr)
    print("=" * 50, file=sys.stderr)
    
    results = get_camera_benchmarks(args.phone_name)
    
    # Output results
    json_output = json.dumps(results, indent=4)
    
    if args.output:
        with open(args.output, 'w') as f:
            f.write(json_output)
        print(f"\nResults saved to: {args.output}", file=sys.stderr)
    
    print(json_output)


if __name__ == "__main__":
    main()