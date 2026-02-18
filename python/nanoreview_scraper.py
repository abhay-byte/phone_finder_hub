#!/usr/bin/env python3
"""
Script to scrape phone information from nanoreview.net
Example usage: python nanoreview_scraper.py "Oneplus 15"
"""

import requests
from bs4 import BeautifulSoup
import argparse
import json
import sys
import time
import urllib.parse
import re
from typing import Dict, List, Optional, Any

try:
    from playwright.sync_api import sync_playwright
    HAS_PLAYWRIGHT = True
except ImportError:
    HAS_PLAYWRIGHT = False

try:
    from playwright_stealth import stealth_sync
    HAS_STEALTH = True
except ImportError:
    HAS_STEALTH = False

try:
    import cloudscraper
    HAS_CLOUDSCRAPER = True
except ImportError:
    HAS_CLOUDSCRAPER = False


def get_random_user_agent() -> str:
    """Return a random user agent."""
    user_agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:122.0) Gecko/20100101 Firefox/122.0',
    ]
    import random
    return random.choice(user_agents)


def search_nanoreview(query: str) -> Optional[str]:
    """Search for a phone on nanoreview.net and return the first result URL."""
    # First try direct URL construction
    phone_slug = query.lower().replace(' ', '-').replace('+', '-')
    direct_url = f"https://nanoreview.net/en/phone/{phone_slug}"
    
    # If we have Playwright, verify the URL works
    if HAS_PLAYWRIGHT:
        try:
            from playwright.sync_api import sync_playwright
            with sync_playwright() as p:
                browser = p.chromium.launch(headless=True)
                page = browser.new_page()
                page.goto(direct_url, wait_until='domcontentloaded', timeout=30000)
                
                # Check if we got a valid page
                if page.title() and '404' not in page.title() and 'Not Found' not in page.title():
                    browser.close()
                    return direct_url
                
                browser.close()
        except Exception as e:
            print(f"Direct URL check failed: {e}", file=sys.stderr)
    
    # Fallback: try cloudscraper for search
    if HAS_CLOUDSCRAPER:
        try:
            scraper = cloudscraper.create_scraper(
                browser={'browser': 'chrome', 'platform': 'windows'}
            )
            
            search_url = f"https://nanoreview.net/en/search?q={urllib.parse.quote(query)}"
            response = scraper.get(search_url, timeout=15)
            
            if response.status_code == 200:
                soup = BeautifulSoup(response.content, 'html.parser')
                results = soup.select('a[href*="/en/phone/"]')
                if results:
                    href = results[0].get('href', '')
                    if href:
                        if href.startswith('/'):
                            return f"https://nanoreview.net{href}"
                        return href
        except Exception as e:
            print(f"Search error: {e}", file=sys.stderr)
    
    # Return direct URL as fallback
    return direct_url


def scrape_nanoreview_playwright(url: str) -> Dict[str, Any]:
    """Scrape phone data from nanoreview using Playwright."""
    data = {'url': url, 'source': 'nanoreview.net'}
    
    if not HAS_PLAYWRIGHT:
        print("Playwright not installed. Install with: pip install playwright && playwright install chromium", file=sys.stderr)
        return data
    
    try:
        print(f"Scraping {url} with Playwright...", file=sys.stderr)
        
        with sync_playwright() as p:
            browser = p.chromium.launch(
                headless=True,
                args=['--no-sandbox', '--disable-dev-shm-usage']
            )
            
            context = browser.new_context(
                user_agent=get_random_user_agent(),
                viewport={'width': 1920, 'height': 1080}
            )
            
            page = context.new_page()
            
            # Apply stealth mode if available
            if HAS_STEALTH:
                stealth_sync(page)
            
            page.goto(url, wait_until='domcontentloaded', timeout=30000)
            
            # Wait for Cloudflare challenge to complete
            for _ in range(20):
                if 'Just a moment' not in page.title():
                    break
                time.sleep(1)
            
            # Wait for tables to load
            try:
                page.wait_for_selector('table', timeout=15000)
            except:
                # If no tables, check if we're still on challenge page
                if 'Just a moment' in page.title():
                    print("Cloudflare challenge not solved. Try again later or use a different IP.", file=sys.stderr)
                    browser.close()
                    return data
            
            # Additional wait for dynamic content
            time.sleep(2)
            
            content = page.content()
            browser.close()
            
            soup = BeautifulSoup(content, 'html.parser')
            
            # Parse the page
            data.update(_parse_nanoreview_page(soup))
            
    except Exception as e:
        print(f"Playwright error: {e}", file=sys.stderr)
    
    return data


def _parse_nanoreview_page(soup: BeautifulSoup) -> Dict[str, Any]:
    """Parse the nanoreview phone page."""
    data = {}
    
    # Get phone name
    title_elem = soup.select_one('h1')
    if title_elem:
        data['name'] = title_elem.get_text(strip=True)
    
    # Get main image
    img_elem = soup.select_one('.phone-image img, .product-image img, img[src*="phone"]')
    if img_elem:
        data['image'] = img_elem.get('src', '')
    
    # Get rating/score
    score_elem = soup.select_one('.score-value, .rating-value, [class*="score"]')
    if score_elem:
        data['score'] = score_elem.get_text(strip=True)
    
    # Get specifications from tables
    specs = {}
    current_section = 'General'
    
    # Find all tables and their preceding headers
    for table in soup.select('table'):
        # Check if there's a header before this table
        prev = table.find_previous(['h2', 'h3', 'h4'])
        if prev:
            current_section = prev.get_text(strip=True)
        
        if current_section not in specs:
            specs[current_section] = {}
        
        for row in table.select('tr'):
            cells = row.select('td')
            if len(cells) >= 2:
                label = cells[0].get_text(strip=True)
                value = cells[1].get_text(strip=True)
                if label and value:
                    specs[current_section][label] = value
    
    if specs:
        data['specifications'] = specs
    
    # Get pros and cons
    pros = []
    cons = []
    
    # Look for pros/cons sections
    for section in soup.select('[class*="pros"], [class*="cons"], [class*="advantage"], [class*="disadvantage"]'):
        section_text = section.get('class', [''])[0] if section.get('class') else ''
        is_pros = 'pros' in section_text.lower() or 'advantage' in section_text.lower()
        is_cons = 'cons' in section_text.lower() or 'disadvantage' in section_text.lower()
        
        for item in section.select('li, .item, div'):
            text = item.get_text(strip=True)
            if text and len(text) > 5:
                if is_pros:
                    pros.append(text)
                elif is_cons:
                    cons.append(text)
    
    # Alternative: Look for lists with + or - prefixes
    if not pros and not cons:
        for li in soup.select('li'):
            text = li.get_text(strip=True)
            if text.startswith('+'):
                pros.append(text[1:].strip())
            elif text.startswith('-'):
                cons.append(text[1:].strip())
    
    if pros:
        data['pros'] = pros
    if cons:
        data['cons'] = cons
    
    # Get benchmarks
    benchmarks = {}
    
    # Look for benchmark scores
    for elem in soup.select('[class*="benchmark"], [class*="score"]'):
        text = elem.get_text(strip=True)
        # Look for patterns like "Geekbench: 1234"
        if ':' in text or any(bench in text.lower() for bench in ['geekbench', 'antutu', '3dmark', 'gfxbench']):
            parent = elem.find_parent()
            if parent:
                parent_text = parent.get_text(strip=True)
                if len(parent_text) < 100:  # Avoid capturing too much
                    benchmarks[parent_text] = text
    
    # Look for specific benchmark sections
    bench_section = soup.select_one('[id*="benchmark"], [id*="performance"]')
    if bench_section:
        for row in bench_section.select('tr, div'):
            text = row.get_text(strip=True)
            if any(bench in text.lower() for bench in ['geekbench', 'antutu', '3dmark', 'gfxbench', 'score']):
                parts = text.split()
                if len(parts) >= 2:
                    benchmarks[parts[0]] = ' '.join(parts[1:])
    
    if benchmarks:
        data['benchmarks'] = benchmarks
    
    # Get review summary
    review_elem = soup.select_one('.review-summary, .summary, [class*="review"] p, article p')
    if review_elem:
        text = review_elem.get_text(strip=True)
        if len(text) > 50:
            data['review_summary'] = text[:500]  # Limit length
    
    # Get price information
    price_elem = soup.select_one('.price, [class*="price"]')
    if price_elem:
        data['price'] = price_elem.get_text(strip=True)
    
    # Get user ratings
    rating_elem = soup.select_one('[class*="rating"], [class*="user-score"]')
    if rating_elem:
        rating_text = rating_elem.get_text(strip=True)
        if rating_text and len(rating_text) < 20:
            data['user_rating'] = rating_text
    
    return data


def scrape_nanoreview_cloudscraper(url: str) -> Dict[str, Any]:
    """Scrape phone data using cloudscraper."""
    data = {'url': url, 'source': 'nanoreview.net'}
    
    if not HAS_CLOUDSCRAPER:
        print("Cloudscraper not installed. Install with: pip install cloudscraper", file=sys.stderr)
        return data
    
    try:
        print(f"Scraping {url} with Cloudscraper...", file=sys.stderr)
        
        scraper = cloudscraper.create_scraper(
            browser={'browser': 'chrome', 'platform': 'windows'}
        )
        
        response = scraper.get(url, timeout=30)
        
        if response.status_code != 200:
            print(f"Failed with status {response.status_code}", file=sys.stderr)
            return data
        
        soup = BeautifulSoup(response.content, 'html.parser')
        data.update(_parse_nanoreview_page(soup))
        
    except Exception as e:
        print(f"Cloudscraper error: {e}", file=sys.stderr)
    
    return data


def scrape_benchmark_ranking(phone_name: str, debug: bool = False) -> Dict[str, Any]:
    """
    Scrape benchmark scores from nanoreview.net/en/benchmark-ranking/{phone_name}.
    
    Extracts:
    - AnTuTu v10 score (average from AnTuTu 10 section)
    - AnTuTu v11 score (average from AnTuTu 11 section, if available)
    - Geekbench 6 single-core score (average from GeekBench 6 CPU section)
    - Geekbench 6 multi-core score (average from GeekBench 6 CPU section)
    - Geekbench 6 GPU Compute score (average from GeekBench 6 GPU section)
    - All individual scores for each benchmark
    - Average of the benchmark averages
    
    Args:
        phone_name: Phone name for benchmark ranking URL (e.g., "oneplus-15", "OnePlus 15")
        debug: If True, print debug information
        
    Returns:
        Dictionary with benchmark scores and average
    """
    data = {'phone': phone_name, 'source': 'nanoreview.net/benchmark-ranking'}
    
    # Construct URL - phone name needs to be slugified
    phone_slug = phone_name.lower().replace(' ', '-').replace('+', '-')
    url = f"https://nanoreview.net/en/benchmark-ranking/{phone_slug}"
    
    if not HAS_PLAYWRIGHT:
        print("Playwright not installed. Install with: pip install playwright && playwright install chromium", file=sys.stderr)
        data['error'] = 'Playwright required for benchmark ranking'
        return data
    
    try:
        print(f"Scraping benchmark ranking {url} with Playwright...", file=sys.stderr)
        
        with sync_playwright() as p:
            browser = p.chromium.launch(
                headless=True,
                args=['--no-sandbox', '--disable-dev-shm-usage']
            )
            
            context = browser.new_context(
                user_agent=get_random_user_agent(),
                viewport={'width': 1920, 'height': 1080}
            )
            
            page = context.new_page()
            
            if HAS_STEALTH:
                stealth_sync(page)
            
            page.goto(url, wait_until='domcontentloaded', timeout=30000)
            
            # Wait for Cloudflare challenge
            for _ in range(20):
                if 'Just a moment' not in page.title():
                    break
                time.sleep(1)
            
            # Wait for content to load
            try:
                page.wait_for_selector('table, .benchmark-table, [class*="score"]', timeout=15000)
            except:
                pass
            
            time.sleep(3)  # Extra wait for dynamic content
            
            content = page.content()
            browser.close()
            
            soup = BeautifulSoup(content, 'html.parser')
            
            if debug:
                # Save HTML for debugging
                with open('debug_benchmark_page.html', 'w') as f:
                    f.write(content)
                print("Saved debug HTML to debug_benchmark_page.html", file=sys.stderr)
            
            # Parse benchmark scores
            scores = {}
            benchmark_list = []  # List of benchmarks used for average
            all_individual_scores = {}  # Store individual scores for each benchmark
            
            # ===== Parse AnTuTu Scores (average shown at top) =====
            antutu_tables = []
            for table in soup.select('table'):
                caption = table.find('caption')
                if caption and 'antutu' in caption.get_text(strip=True).lower():
                    antutu_tables.append(table)
            
            for heading in soup.find_all(['h2', 'h3']):
                heading_text = heading.get_text(strip=True).lower()
                if 'antutu' in heading_text:
                    next_table = heading.find_next('table')
                    if next_table and next_table not in antutu_tables:
                        antutu_tables.append(next_table)
            
            for table in antutu_tables:
                caption = table.find('caption')
                heading = table.find_previous(['h2', 'h3'])
                section_text = ''
                if caption:
                    section_text = caption.get_text(strip=True).lower()
                if heading:
                    section_text += ' ' + heading.get_text(strip=True).lower()
                
                is_v10 = 'v10' in section_text or 'antutu 10' in section_text or ('10' in section_text and 'v11' not in section_text and '11' not in section_text)
                is_v11 = 'v11' in section_text or 'antutu 11' in section_text
                
                for row in table.select('tr'):
                    cells = row.select('td')
                    if len(cells) >= 2:
                        label = cells[0].get_text(strip=True).lower()
                        value_text = cells[1].get_text(strip=True)
                        
                        if 'total' in label:
                            score_match = re.match(r'^[\d,]+', value_text)
                            if score_match:
                                score = int(score_match.group().replace(',', ''))
                                if is_v11 and 'antutu_v11' not in scores:
                                    scores['antutu_v11'] = score
                                    benchmark_list.append({'name': 'AnTuTu v11', 'score': score})
                                elif is_v10 and 'antutu_v10' not in scores:
                                    scores['antutu_v10'] = score
                                    benchmark_list.append({'name': 'AnTuTu v10', 'score': score})
                                elif 'antutu_v10' not in scores:
                                    scores['antutu_v10'] = score
                                    benchmark_list.append({'name': 'AnTuTu v10', 'score': score})
            
            # ===== Parse AnTuTu 11 individual results =====
            antutu_11_scores = []
            for heading in soup.find_all(['h2', 'h3']):
                heading_text = heading.get_text(strip=True).lower()
                # Look for "Users' AnTuTu 11 Results" or similar
                if 'antutu' in heading_text and '11' in heading_text and 'result' in heading_text:
                    next_table = heading.find_next('table')
                    if next_table:
                        for row in next_table.select('tr'):
                            cells = row.select('td')
                            if len(cells) >= 2:
                                # AnTuTu results have: Date, Total Score, CPU, GPU, Memory, UX
                                # The second cell is the total score
                                first_cell = cells[1].get_text(strip=True)
                                score_match = re.match(r'^[\d,]+', first_cell)
                                if score_match:
                                    score = int(score_match.group().replace(',', ''))
                                    # AnTuTu scores are typically > 100000
                                    if score > 100000:
                                        antutu_11_scores.append(score)
            
            if antutu_11_scores:
                avg_v11 = sum(antutu_11_scores) // len(antutu_11_scores)
                if 'antutu_v11' not in scores:
                    scores['antutu_v11'] = avg_v11
                    benchmark_list.append({'name': 'AnTuTu v11', 'score': avg_v11})
                all_individual_scores['antutu_v11_values'] = antutu_11_scores
            
            # ===== Parse AnTuTu 10 individual results =====
            # Note: The page shows "Users' AnTuTu 10 Results" heading, but often has no results
            # The average AnTuTu 10 score is shown in the summary table at the top
            antutu_10_scores = []
            for heading in soup.find_all(['h2', 'h3']):
                heading_text = heading.get_text(strip=True).lower()
                # Look for "Users' AnTuTu 10 Results" or similar
                if 'antutu' in heading_text and '10' in heading_text and '11' not in heading_text and 'result' in heading_text:
                    next_table = heading.find_next('table')
                    if next_table:
                        # Check if table has actual data (not "No community results yet")
                        table_text = next_table.get_text(strip=True).lower()
                        if 'no community results' not in table_text and 'no results' not in table_text:
                            for row in next_table.select('tr'):
                                cells = row.select('td')
                                if len(cells) >= 2:
                                    # AnTuTu results have: Date, Total Score, CPU, GPU, Memory, UX
                                    # The second cell is the total score
                                    first_cell = cells[1].get_text(strip=True)
                                    score_match = re.match(r'^[\d,]+', first_cell)
                                    if score_match:
                                        score = int(score_match.group().replace(',', ''))
                                        # AnTuTu scores are typically > 100000
                                        if score > 100000:
                                            antutu_10_scores.append(score)
            
            if antutu_10_scores:
                avg_v10 = sum(antutu_10_scores) // len(antutu_10_scores)
                # Only update if we don't already have the average from the summary table
                all_individual_scores['antutu_v10_values'] = antutu_10_scores
            
            # ===== Parse GeekBench 6 CPU section =====
            geekbench_single_scores = []
            geekbench_multi_scores = []
            
            for heading in soup.find_all(['h2', 'h3']):
                heading_text = heading.get_text(strip=True).lower()
                if 'geekbench' in heading_text and '6' in heading_text and 'cpu' in heading_text:
                    next_table = heading.find_next('table')
                    if next_table:
                        for row in next_table.select('tr'):
                            cells = row.select('td')
                            if len(cells) >= 3:
                                single_text = cells[1].get_text(strip=True)
                                multi_text = cells[2].get_text(strip=True)
                                
                                single_match = re.match(r'^[\d,]+', single_text)
                                multi_match = re.match(r'^[\d,]+', multi_text)
                                
                                if single_match:
                                    geekbench_single_scores.append(int(single_match.group().replace(',', '')))
                                if multi_match:
                                    geekbench_multi_scores.append(int(multi_match.group().replace(',', '')))
            
            if geekbench_single_scores:
                avg_single = sum(geekbench_single_scores) // len(geekbench_single_scores)
                scores['geekbench_6_single'] = avg_single
                benchmark_list.append({'name': 'Geekbench 6 Single', 'score': avg_single})
                all_individual_scores['geekbench_6_single_values'] = geekbench_single_scores
            
            if geekbench_multi_scores:
                avg_multi = sum(geekbench_multi_scores) // len(geekbench_multi_scores)
                scores['geekbench_6_multi'] = avg_multi
                benchmark_list.append({'name': 'Geekbench 6 Multi', 'score': avg_multi})
                all_individual_scores['geekbench_6_multi_values'] = geekbench_multi_scores
            
            # ===== Parse GeekBench 6 GPU Compute section =====
            geekbench_gpu_scores = []
            for heading in soup.find_all(['h2', 'h3']):
                heading_text = heading.get_text(strip=True).lower()
                if 'geekbench' in heading_text and '6' in heading_text and ('gpu' in heading_text or 'compute' in heading_text):
                    next_table = heading.find_next('table')
                    if next_table:
                        for row in next_table.select('tr'):
                            cells = row.select('td')
                            if len(cells) >= 2:
                                # First numeric value is the GPU compute score
                                score_text = cells[1].get_text(strip=True)
                                score_match = re.match(r'^[\d,]+', score_text)
                                if score_match:
                                    geekbench_gpu_scores.append(int(score_match.group().replace(',', '')))
            
            if geekbench_gpu_scores:
                avg_gpu = sum(geekbench_gpu_scores) // len(geekbench_gpu_scores)
                scores['geekbench_6_gpu'] = avg_gpu
                benchmark_list.append({'name': 'Geekbench 6 GPU', 'score': avg_gpu})
                all_individual_scores['geekbench_6_gpu_values'] = geekbench_gpu_scores
            
            # Calculate average of the benchmark averages
            if benchmark_list:
                total = sum(b['score'] for b in benchmark_list)
                scores['average'] = total // len(benchmark_list)
            
            data['scores'] = scores
            data['benchmark_list'] = benchmark_list
            data['individual_scores'] = all_individual_scores
            data['url'] = url
            
    except Exception as e:
        print(f"Benchmark ranking error: {e}", file=sys.stderr)
        data['error'] = str(e)
    
    return data


def get_phone_info(query: str, use_playwright: bool = True) -> Dict[str, Any]:
    """
    Get phone information from nanoreview.net.
    
    Args:
        query: Phone name or nanoreview URL
        use_playwright: Whether to use Playwright (more reliable but slower)
        
    Returns:
        Dictionary with phone information
    """
    # Check if query is a URL
    if 'nanoreview.net' in query:
        url = query
    else:
        # Search for the phone
        url = search_nanoreview(query)
        if not url:
            return {'error': f'Phone "{query}" not found on nanoreview.net'}
    
    # Try Playwright first (more reliable for Cloudflare)
    if use_playwright and HAS_PLAYWRIGHT:
        return scrape_nanoreview_playwright(url)
    
    # Fallback to cloudscraper
    if HAS_CLOUDSCRAPER:
        return scrape_nanoreview_cloudscraper(url)
    
    return {'error': 'No scraping method available. Install playwright or cloudscraper.'}


def main():
    parser = argparse.ArgumentParser(
        description='Scrape phone information from nanoreview.net',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog='''
Examples:
  python nanoreview_scraper.py "Oneplus 15"
  python nanoreview_scraper.py "iPhone 16" --json
  python nanoreview_scraper.py "https://nanoreview.net/en/phone/oneplus-15"
  python nanoreview_scraper.py "snapdragon-8-gen-3" --benchmark
  python nanoreview_scraper.py "Snapdragon 8 Elite" --benchmark --json
        '''
    )
    parser.add_argument('query', help='Phone name or nanoreview.net URL')
    parser.add_argument('--json', '-j', action='store_true', help='Output as JSON')
    parser.add_argument('--no-playwright', action='store_true', help='Disable Playwright (use cloudscraper)')
    parser.add_argument('--benchmark', '-b', action='store_true', 
                        help='Scrape benchmark ranking page instead of phone page. '
                             'Returns AnTuTu v11, Geekbench 6 single/multi scores and average.')
    
    args = parser.parse_args()
    
    # Handle benchmark ranking mode
    if args.benchmark:
        result = scrape_benchmark_ranking(args.query)
        
        if args.json:
            print(json.dumps(result, indent=2, ensure_ascii=False))
        else:
            print("\n" + "=" * 60)
            print(f"BENCHMARK RANKING: {result.get('device', args.query)}")
            print("=" * 60)
            
            if 'scores' in result:
                scores = result['scores']
                print("\nBENCHMARK SCORES:")
                # Show all scores
                if 'antutu_v10' in scores:
                    print(f"  AnTuTu v10:        {scores['antutu_v10']:,}")
                if 'antutu_v11' in scores:
                    print(f"  AnTuTu v11:        {scores['antutu_v11']:,}")
                if 'geekbench_6_single' in scores:
                    print(f"  Geekbench 6 SC:    {scores['geekbench_6_single']:,}")
                if 'geekbench_6_multi' in scores:
                    print(f"  Geekbench 6 MC:    {scores['geekbench_6_multi']:,}")
                if 'geekbench_6_gpu' in scores:
                    print(f"  Geekbench 6 GPU:   {scores['geekbench_6_gpu']:,}")
                
                # Show individual scores used for averages
                if 'individual_scores' in result:
                    ind = result['individual_scores']
                    print("\n  Individual scores used for averages:")
                    if 'antutu_v10_values' in ind and ind['antutu_v10_values']:
                        print(f"    AnTuTu v10: {ind['antutu_v10_values']}")
                    if 'antutu_v11_values' in ind and ind['antutu_v11_values']:
                        print(f"    AnTuTu v11: {ind['antutu_v11_values']}")
                    if 'geekbench_6_single_values' in ind and ind['geekbench_6_single_values']:
                        print(f"    Geekbench 6 Single: {ind['geekbench_6_single_values']}")
                    if 'geekbench_6_multi_values' in ind and ind['geekbench_6_multi_values']:
                        print(f"    Geekbench 6 Multi: {ind['geekbench_6_multi_values']}")
                    if 'geekbench_6_gpu_values' in ind and ind['geekbench_6_gpu_values']:
                        print(f"    Geekbench 6 GPU: {ind['geekbench_6_gpu_values']}")
                
                # Show list of benchmarks used for final average
                if 'benchmark_list' in result:
                    print(f"\n  Benchmarks used for final average:")
                    for b in result['benchmark_list']:
                        print(f"    - {b['name']}: {b['score']:,}")
                
                # Show average
                if 'average' in scores:
                    print(f"\n  AVERAGE:           {scores['average']:,}")
            
            if 'url' in result:
                print(f"\nURL: {result['url']}")
            
            if 'error' in result:
                print(f"\nError: {result['error']}")
            
            print("\n" + "=" * 60)
        return
    
    use_playwright = not args.no_playwright and HAS_PLAYWRIGHT
    
    result = get_phone_info(args.query, use_playwright=use_playwright)
    
    if args.json:
        print(json.dumps(result, indent=2, ensure_ascii=False))
    else:
        # Pretty print
        print("\n" + "=" * 60)
        if 'name' in result:
            print(f"PHONE: {result['name']}")
        print("=" * 60)
        
        if 'score' in result:
            print(f"\nScore: {result['score']}")
        
        if 'price' in result:
            print(f"Price: {result['price']}")
        
        if 'specifications' in result:
            print("\nSPECIFICATIONS:")
            print("-" * 40)
            for section, specs in result['specifications'].items():
                if isinstance(specs, dict):
                    print(f"\n{section}:")
                    for label, value in specs.items():
                        print(f"  {label}: {value}")
                else:
                    print(f"{section}: {specs}")
        
        if 'pros' in result:
            print("\nPROS:")
            for pro in result['pros']:
                print(f"  + {pro}")
        
        if 'cons' in result:
            print("\nCONS:")
            for con in result['cons']:
                print(f"  - {con}")
        
        if 'benchmarks' in result:
            print("\nBENCHMARKS:")
            for name, score in result['benchmarks'].items():
                print(f"  {name}: {score}")
        
        if 'error' in result:
            print(f"\nError: {result['error']}")
        
        print("\n" + "=" * 60)


if __name__ == "__main__":
    main()