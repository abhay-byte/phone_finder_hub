#!/usr/bin/env python3
"""
GPU Benchmark Fetcher Script

This script fetches 3DMark Wildlife Extreme GPU benchmark scores:
- Peak score (0-8100+ range)
- Stability score (0-100% scale)

Usage:
    python gpu_benchmark_fetcher.py "iPhone 15 Pro" [--output gpu_scores.json]

Requirements:
    pip install requests beautifulsoup4 ddgs
"""

import argparse
import sys
import re
import json
import time
import logging
from typing import Optional
from urllib.parse import quote_plus

# Suppress ddgs logging
logging.getLogger('ddgs').setLevel(logging.CRITICAL)

try:
    import requests
    from bs4 import BeautifulSoup
    from ddgs import DDGS
except ImportError as e:
    print(f"Missing dependency: {e}", file=sys.stderr)
    print("Please install required packages:", file=sys.stderr)
    print("pip install requests beautifulsoup4 ddgs", file=sys.stderr)
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


def extract_score_from_text(text: str) -> Optional[int]:
    """Extract 3DMark Wildlife Extreme score from text."""
    # Common patterns for 3DMark Wildlife Extreme scores
    # Wildlife Extreme scores are typically 1000-8000+ for modern phones
    patterns = [
        # "scores 6,722 points in Wildlife Extreme"
        r'scores?\s*([\d,]+)\s*points?\s*in\s*wildlife\s*extreme',
        # "Wildlife Extreme score: 4500" or "Wildlife Extreme: 4500"
        r'wildlife\s*extreme\s*(?:score|result|peak)?[:\s]*([\d,]+)',
        # "3DMark Wildlife Extreme: 4500"
        r'3dmark\s*wildlife\s*extreme[:\s]*([\d,]+)',
        # "Wildlife Extreme 4500 points"
        r'wildlife\s*extreme\s*([\d,]+)\s*points?',
        # "6,722 in Wildlife Extreme"
        r'([\d,]+)\s*(?:points?\s*)?in\s*wildlife\s*extreme',
        # "Wild Life Extreme: 6722" (alternate spelling)
        r'wild\s*life\s*extreme[:\s]*([\d,]+)',
        # "score of 4,485" pattern
        r'score\s*of\s*([\d,]+)',
        # "S24 Ultra's score of 4,485"
        r'ultra[\'\'"]?s?\s*score\s*(?:of\s*)?([\d,]+)',
    ]
    
    for pattern in patterns:
        match = re.search(pattern, text, re.IGNORECASE)
        if match:
            score_str = match.group(1).replace(',', '').strip()
            if score_str:
                try:
                    score = int(score_str)
                    # Validate score range for Wildlife Extreme (typically 500-10000)
                    if 500 <= score <= 10000:
                        return score
                except ValueError:
                    continue
    
    return None


def extract_stability_from_text(text: str) -> Optional[float]:
    """Extract stability percentage from text."""
    patterns = [
        # "Stability: 95%"
        r'stability[:\s]*(\d+(?:\.\d+)?)\s*%',
        # "95% stability"
        r'(\d+(?:\.\d+)?)\s*%\s*stability',
        # "Stability score: 95"
        r'stability\s*(?:score)?[:\s]*(\d+(?:\.\d+)?)',
        # "Score stability 95%"
        r'score\s*stability[:\s]*(\d+(?:\.\d+)?)\s*%?',
    ]
    
    for pattern in patterns:
        match = re.search(pattern, text, re.IGNORECASE)
        if match:
            stability = float(match.group(1))
            # Validate range (0-100%)
            if 0 <= stability <= 100:
                return round(stability, 1)
    
    return None


def search_3dmark_score(phone_name: str) -> tuple[Optional[int], Optional[float]]:
    """Search for 3DMark Wildlife Extreme score and stability."""
    try:
        print("Searching 3DMark Wildlife Extreme scores...", file=sys.stderr)
        
        peak_score = None
        stability = None
        
        # Search queries
        queries = [
            f"{phone_name} 3DMark Wildlife Extreme score benchmark",
            f"{phone_name} Wildlife Extreme GPU score stability",
            f"{phone_name} 3DMark benchmark GPU performance",
        ]
        
        for query in queries:
            results = web_search(query, max_results=10)
            
            for result in results:
                url = result['url']
                snippet = result['snippet']
                title = result['title']
                
                # Combine all text for searching
                combined_text = f"{title} {snippet}"
                
                # Try to extract peak score
                if peak_score is None:
                    score = extract_score_from_text(combined_text)
                    if score:
                        peak_score = score
                        print(f"  Found peak score: {score}", file=sys.stderr)
                
                # Try to extract stability
                if stability is None:
                    stab = extract_stability_from_text(combined_text)
                    if stab:
                        stability = stab
                        print(f"  Found stability: {stab}%", file=sys.stderr)
                
                # If we have both, we're done
                if peak_score is not None and stability is not None:
                    return peak_score, stability
                
                # Try to scrape the page for more details
                if peak_score is None or stability is None:
                    # Skip certain sites that are hard to parse
                    skip_domains = ['youtube.com', 'facebook.com', 'twitter.com', 'instagram.com']
                    if any(domain in url.lower() for domain in skip_domains):
                        continue
                    
                    response = make_request(url)
                    if response:
                        page_text = response.text
                        
                        # Try to extract from page
                        if peak_score is None:
                            score = extract_score_from_text(page_text)
                            if score:
                                peak_score = score
                                print(f"  Found peak score from page: {score}", file=sys.stderr)
                        
                        if stability is None:
                            stab = extract_stability_from_text(page_text)
                            if stab:
                                stability = stab
                                print(f"  Found stability from page: {stab}%", file=sys.stderr)
                        
                        if peak_score is not None and stability is not None:
                            return peak_score, stability
            
            time.sleep(0.5)
        
        # If we still don't have a score, try a more specific search
        if peak_score is None:
            print("  Trying additional search patterns...", file=sys.stderr)
            specific_query = f'"{phone_name}" "Wildlife Extreme" score'
            results = web_search(specific_query, max_results=10)
            
            for result in results:
                combined_text = f"{result['title']} {result['snippet']}"
                score = extract_score_from_text(combined_text)
                if score:
                    peak_score = score
                    print(f"  Found peak score: {score}", file=sys.stderr)
                    break
        
        return peak_score, stability
        
    except Exception as e:
        print(f"3DMark search failed: {e}", file=sys.stderr)
        return None, None


def search_nanoreview_gpu(phone_name: str) -> tuple[Optional[int], Optional[float]]:
    """Search nanoreview for GPU benchmark scores."""
    try:
        print("Searching nanoreview...", file=sys.stderr)
        
        # Try direct nanoreview URL
        phone_slug = phone_name.lower().replace(' ', '-').replace('+', '-')
        direct_url = f"https://nanoreview.net/en/phone/{phone_slug}"
        
        response = make_request(direct_url)
        if response:
            soup = BeautifulSoup(response.text, 'html.parser')
            page_text = soup.get_text()
            
            # Look for 3DMark Wildlife Extreme scores
            score = extract_score_from_text(page_text)
            stability = extract_stability_from_text(page_text)
            
            if score or stability:
                return score, stability
        
        # Fallback to web search
        query = f"{phone_name} nanoreview GPU performance benchmark"
        results = web_search(query, max_results=5)
        
        for result in results:
            url = result['url']
            
            if 'nanoreview' in url.lower():
                response = make_request(url)
                if response:
                    soup = BeautifulSoup(response.text, 'html.parser')
                    page_text = soup.get_text()
                    
                    # Look for 3DMark scores
                    score = extract_score_from_text(page_text)
                    stability = extract_stability_from_text(page_text)
                    
                    if score or stability:
                        return score, stability
        
        return None, None
        
    except Exception as e:
        print(f"nanoreview search failed: {e}", file=sys.stderr)
        return None, None


def search_gsmarena_gpu(phone_name: str) -> tuple[Optional[int], Optional[float]]:
    """Search GSMArena for GPU benchmark scores."""
    try:
        print("Searching GSMArena for GPU benchmarks...", file=sys.stderr)
        
        query = f"{phone_name} site:gsmarena.com benchmark GPU"
        results = web_search(query, max_results=5)
        
        for result in results:
            url = result['url']
            snippet = result['snippet']
            
            # Check snippet first
            score = extract_score_from_text(snippet)
            stability = extract_stability_from_text(snippet)
            
            if score or stability:
                return score, stability
            
            # Try to scrape the page
            if 'gsmarena.com' in url.lower():
                response = make_request(url)
                if response:
                    soup = BeautifulSoup(response.text, 'html.parser')
                    page_text = soup.get_text()
                    
                    score = extract_score_from_text(page_text)
                    stability = extract_stability_from_text(page_text)
                    
                    if score or stability:
                        return score, stability
        
        return None, None
        
    except Exception as e:
        print(f"GSMArena GPU search failed: {e}", file=sys.stderr)
        return None, None


def get_gpu_benchmarks(phone_name: str) -> dict:
    """Get GPU benchmark scores from all sources."""
    results = {
        'phone_name': phone_name,
        'gpu_benchmark': {
            'wildlife_extreme_peak': 0,
            'wildlife_extreme_stability': 0.0
        }
    }
    
    # Search 3DMark scores
    peak_score, stability = search_3dmark_score(phone_name)
    
    # If not found, try nanoreview
    if peak_score is None and stability is None:
        peak_score, stability = search_nanoreview_gpu(phone_name)
    
    # If still not found, try GSMArena
    if peak_score is None and stability is None:
        peak_score, stability = search_gsmarena_gpu(phone_name)
    
    # Set values (0 if not found)
    results['gpu_benchmark']['wildlife_extreme_peak'] = peak_score if peak_score else 0
    results['gpu_benchmark']['wildlife_extreme_stability'] = stability if stability else 0.0
    
    return results


def main():
    parser = argparse.ArgumentParser(
        description='Fetch 3DMark Wildlife Extreme GPU benchmark scores.',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog="""
Output format:
{
    "phone_name": "iPhone 15 Pro",
    "gpu_benchmark": {
        "wildlife_extreme_peak": 4500,      # 0-8100+ range
        "wildlife_extreme_stability": 95.5  # 0-100% scale
    }
}

Examples:
    python gpu_benchmark_fetcher.py "iPhone 15 Pro"
    python gpu_benchmark_fetcher.py "Samsung Galaxy S24 Ultra" --output gpu_scores.json
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
    
    print(f"Fetching GPU benchmarks for: {args.phone_name}", file=sys.stderr)
    print("=" * 50, file=sys.stderr)
    
    results = get_gpu_benchmarks(args.phone_name)
    
    # Output results
    json_output = json.dumps(results, indent=4)
    
    if args.output:
        with open(args.output, 'w') as f:
            f.write(json_output)
        print(f"\nResults saved to: {args.output}", file=sys.stderr)
    
    print(json_output)


if __name__ == "__main__":
    main()