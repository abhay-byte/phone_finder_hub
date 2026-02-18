#!/usr/bin/env python3
"""
Script to get Amazon and Flipkart product links for a given device.
Example usage: python shopping_links.py "Oneplus 15"

Note: These sites have anti-bot protections. If blocked, try:
1. Running from a different IP
2. Adding cookies from a real browser session
3. Using a proxy service
"""

import requests
from requests.adapters import HTTPAdapter
from urllib3.util.retry import Retry
from bs4 import BeautifulSoup
import argparse
import json
import sys
import time
import urllib.parse
import random
from typing import Dict, List, Optional

try:
    import cloudscraper
    HAS_CLOUDSCRAPER = True
except ImportError:
    HAS_CLOUDSCRAPER = False

try:
    from playwright.sync_api import sync_playwright
    HAS_PLAYWRIGHT = True
except ImportError:
    HAS_PLAYWRIGHT = False


def get_session(use_cloudscraper: bool = True) -> requests.Session:
    """Create a session with retry strategy and proper headers."""
    if use_cloudscraper and HAS_CLOUDSCRAPER:
        # Use cloudscraper which handles Cloudflare and similar protections
        session = cloudscraper.create_scraper(
            browser={
                'browser': 'chrome',
                'platform': 'windows',
                'desktop': True,
            },
            delay=10,
        )
        return session
    
    session = requests.Session()
    
    # Retry strategy
    retry_strategy = Retry(
        total=3,
        backoff_factor=1,
        status_forcelist=[429, 500, 502, 503, 504],
    )
    adapter = HTTPAdapter(max_retries=retry_strategy)
    session.mount("http://", adapter)
    session.mount("https://", adapter)
    
    return session


def get_random_user_agent() -> str:
    """Return a random user agent to avoid detection."""
    user_agents = [
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/119.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
        'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:121.0) Gecko/20100101 Firefox/121.0',
        'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/17.2 Safari/605.1.15',
        'Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36',
    ]
    return random.choice(user_agents)


def get_amazon_headers() -> Dict[str, str]:
    """Return headers specifically for Amazon requests."""
    return {
        'User-Agent': get_random_user_agent(),
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
        'Accept-Language': 'en-IN,en-GB;q=0.9,en-US;q=0.8,en;q=0.7',
        'Accept-Encoding': 'gzip, deflate, br',
        'Connection': 'keep-alive',
        'Upgrade-Insecure-Requests': '1',
        'Sec-Fetch-Dest': 'document',
        'Sec-Fetch-Mode': 'navigate',
        'Sec-Fetch-Site': 'none',
        'Sec-Fetch-User': '?1',
        'Cache-Control': 'max-age=0',
        'sec-ch-ua': '"Not_A Brand";v="8", "Chromium";v="120", "Google Chrome";v="120"',
        'sec-ch-ua-mobile': '?0',
        'sec-ch-ua-platform': '"Windows"',
    }


def get_flipkart_headers(referer: str = None) -> Dict[str, str]:
    """Return headers specifically for Flipkart requests."""
    headers = {
        'User-Agent': get_random_user_agent(),
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/avif,image/webp,image/apng,*/*;q=0.8',
        'Accept-Language': 'en-IN,en;q=0.9,hi;q=0.8',
        'Accept-Encoding': 'gzip, deflate, br, zstd',
        'Connection': 'keep-alive',
        'Upgrade-Insecure-Requests': '1',
        'Sec-Fetch-Dest': 'document',
        'Sec-Fetch-Mode': 'navigate',
        'Sec-Fetch-Site': 'same-origin',
        'Sec-Fetch-User': '?1',
        'Cache-Control': 'no-cache',
        'Pragma': 'no-cache',
        'sec-ch-ua': '"Chromium";v="122", "Not(A:Brand";v="24", "Google Chrome";v="122"',
        'sec-ch-ua-mobile': '?0',
        'sec-ch-ua-platform': '"Windows"',
        'sec-ch-ua-full-version-list': '"Chromium";v="122.0.6261.112", "Not(A:Brand";v="24.0.0.0", "Google Chrome";v="122.0.6261.112"',
        'sec-ch-ua-platform-version': '"15.0.0"',
        'sec-ch-ua-wow64': '?0',
        'priority': 'u=0, i',
    }
    if referer:
        headers['Referer'] = referer
    return headers


def search_amazon(query: str, max_results: int = 5, session: Optional[requests.Session] = None) -> List[Dict[str, str]]:
    """
    Search Amazon.in for products matching the query.
    
    Args:
        query: Search query (e.g., "Oneplus 15")
        max_results: Maximum number of results to return
        session: Optional requests session
        
    Returns:
        List of dictionaries containing product title, link, and price
    """
    results = []
    
    if session is None:
        session = get_session(use_cloudscraper=False)  # Amazon doesn't use Cloudflare
    
    try:
        # Encode the query for URL
        encoded_query = urllib.parse.quote_plus(query)
        search_url = f"https://www.amazon.in/s?k={encoded_query}"
        
        headers = get_amazon_headers()
        
        # First visit homepage to get cookies
        try:
            session.get("https://www.amazon.in/", headers=headers, timeout=10)
            time.sleep(1)
        except:
            pass
        
        response = session.get(search_url, headers=headers, timeout=15)
        
        # Check if we're blocked
        if response.status_code == 503:
            print("Amazon returned 503 (Service Unavailable). Anti-bot protection triggered.", file=sys.stderr)
            print("Tip: Try running from a different network or use browser cookies.", file=sys.stderr)
            return results
            
        if response.status_code == 403:
            print("Amazon returned 403 (Forbidden). Request blocked.", file=sys.stderr)
            return results
        
        response.raise_for_status()
        
        soup = BeautifulSoup(response.content, 'html.parser')
        
        # Check for captcha
        if 'captcha' in response.text.lower() or 'Type the characters you see' in response.text:
            print("Amazon showing CAPTCHA. Request blocked.", file=sys.stderr)
            return results
        
        # Find all product containers
        # Amazon uses various class names, we'll try multiple selectors
        product_containers = soup.select('[data-component-type="s-search-result"]')
        
        if not product_containers:
            # Fallback selector
            product_containers = soup.select('.s-result-item[data-asin]')
        
        for container in product_containers[:max_results]:
            try:
                product = {}
                
                # Skip sponsored products and non-product items
                asin = container.get('data-asin', '')
                if not asin:
                    continue
                
                # Get product title - try multiple selectors
                title_elem = container.select_one('h2 a.a-link-normal')
                if not title_elem:
                    title_elem = container.select_one('.a-link-normal.s-link-style')
                if not title_elem:
                    title_elem = container.select_one('span.a-size-medium.a-color-base.a-text-normal')
                
                # Get title text
                if title_elem:
                    title_text = title_elem.get_text(strip=True)
                    # Skip if title is not useful
                    if title_text.lower() in ['let us know', 'see available options', '']:
                        # Try getting title from span inside h2
                        h2_elem = container.select_one('h2')
                        if h2_elem:
                            title_text = h2_elem.get_text(strip=True)
                    product['title'] = title_text
                    href = title_elem.get('href', '')
                    if href.startswith('/'):
                        product['link'] = f"https://www.amazon.in{href.split('?')[0]}"
                    else:
                        product['link'] = href
                
                # Get price
                price_elem = container.select_one('.a-price .a-offscreen')
                if not price_elem:
                    price_elem = container.select_one('.a-price-whole')
                
                if price_elem:
                    product['price'] = price_elem.get_text(strip=True)
                else:
                    product['price'] = 'N/A'
                
                # Get rating (optional)
                rating_elem = container.select_one('.a-icon-star-small .a-icon-alt')
                if rating_elem:
                    product['rating'] = rating_elem.get_text(strip=True)
                
                # Only add if we have at least a title and link
                if product.get('title') and product.get('link'):
                    product['source'] = 'Amazon'
                    product['asin'] = asin
                    results.append(product)
                    
            except Exception as e:
                print(f"Error parsing Amazon product: {e}", file=sys.stderr)
                continue
        
        if not results:
            # Save debug info
            print("No Amazon results found. Debugging info:", file=sys.stderr)
            print(f"  - Status code: {response.status_code}", file=sys.stderr)
            print(f"  - Product containers found: {len(product_containers)}", file=sys.stderr)
            
    except requests.exceptions.RequestException as e:
        print(f"Error fetching Amazon: {e}", file=sys.stderr)
    except Exception as e:
        print(f"Error parsing Amazon results: {e}", file=sys.stderr)
    
    return results


def search_flipkart(query: str, max_results: int = 5, session: Optional[requests.Session] = None) -> List[Dict[str, str]]:
    """
    Search Flipkart.com for products matching the query.
    
    Args:
        query: Search query (e.g., "Oneplus 15")
        max_results: Maximum number of results to return
        session: Optional requests session
        
    Returns:
        List of dictionaries containing product title, link, and price
    """
    results = []
    
    if session is None:
        # Use cloudscraper for Flipkart
        session = get_session(use_cloudscraper=True)
    
    # Try multiple approaches
    approaches = [
        # Approach 1: Playwright (most reliable for bot protection)
        lambda: _flipkart_playwright_search(query, max_results) if HAS_PLAYWRIGHT else [],
        # Approach 2: Cloudscraper direct search
        lambda: _flipkart_cloudscraper_search(query, max_results, session),
        # Approach 3: Direct search with enhanced headers
        lambda: _flipkart_direct_search(query, session),
        # Approach 4: API-based search
        lambda: _flipkart_api_search(query, max_results, session),
        # Approach 5: Mobile site
        lambda: _flipkart_mobile_search(query, max_results, session),
    ]
    
    for i, approach in enumerate(approaches):
        try:
            results = approach()
            if results:
                return results
            print(f"Flipkart approach {i+1} failed, trying next...", file=sys.stderr)
            time.sleep(1)
        except Exception as e:
            print(f"Flipkart approach {i+1} error: {e}", file=sys.stderr)
            continue
    
    return results


def _flipkart_cloudscraper_search(query: str, max_results: int, session: requests.Session) -> List[Dict[str, str]]:
    """Use cloudscraper to bypass Flipkart protection."""
    results = []
    encoded_query = urllib.parse.quote(query)
    
    # Try different URL patterns
    urls_to_try = [
        f"https://www.flipkart.com/search?q={encoded_query}&otracker=search&otracker1=search&marketplace=FLIPKART",
        f"https://www.flipkart.com/search?q={encoded_query}&sort=relevance",
        f"https://www.flipkart.com/search?q={encoded_query}",
        f"https://www.flipkart.com/search?q={encoded_query}&fm=search-autosuggest",
    ]
    
    for search_url in urls_to_try:
        try:
            response = session.get(search_url, timeout=20)
            
            if response.status_code == 200:
                results = _parse_flipkart_results(response)
                if results:
                    return results[:max_results]
        except Exception as e:
            print(f"Cloudscraper error for {search_url}: {e}", file=sys.stderr)
            continue
    
    return results


def _flipkart_selenium_search(query: str, max_results: int) -> List[Dict[str, str]]:
    """Use Playwright to bypass Flipkart protection."""
    results = []
    
    if not HAS_PLAYWRIGHT:
        return results
    
    try:
        print("Starting Playwright browser for Flipkart...", file=sys.stderr)
        
        with sync_playwright() as p:
            # Launch browser
            browser = p.chromium.launch(
                headless=True,
                args=['--no-sandbox', '--disable-dev-shm-usage']
            )
            
            context = browser.new_context(
                user_agent='Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
                viewport={'width': 1920, 'height': 1080}
            )
            
            page = context.new_page()
            
            # Build search URL
            encoded_query = urllib.parse.quote(query)
            search_url = f"https://www.flipkart.com/search?q={encoded_query}"
            
            # Navigate to the page
            page.goto(search_url, wait_until='networkidle', timeout=30000)
            
            # Wait for products to load
            try:
                page.wait_for_selector('div[data-id]', timeout=15000)
            except:
                pass
            
            # Get page content
            content = page.content()
            browser.close()
            
            # Parse with BeautifulSoup
            soup = BeautifulSoup(content, 'html.parser')
            
            # Find product containers - Flipkart uses div[data-id] for products
            product_containers = soup.select('div[data-id]')
            
            for container in product_containers[:max_results * 2]:
                try:
                    product = {}
                    
                    # Get product title from img alt attribute or from text
                    img = container.select_one('img')
                    if img:
                        product['title'] = img.get('alt', '')
                    
                    if not product.get('title'):
                        # Try other selectors
                        for sel in ['div._4rR01T', 'a.IRpwTa', 'a.s1Q9rs', 'span.ZhHnSZ']:
                            title_elem = container.select_one(sel)
                            if title_elem:
                                product['title'] = title_elem.get_text(strip=True)
                                break
                    
                    if not product.get('title'):
                        continue
                    
                    # Get product link
                    link_elem = container.select_one('a.k7wcnx') or container.select_one('a')
                    if link_elem:
                        href = link_elem.get('href', '')
                        if href.startswith('/'):
                            product['link'] = f"https://www.flipkart.com{href.split('?')[0]}"
                        elif href.startswith('http'):
                            product['link'] = href
                    
                    if not product.get('link'):
                        continue
                    
                    # Get price - look for elements with price class
                    price_elem = container.select_one('div._30jeq3')
                    if not price_elem:
                        # Try new Flipkart price selector
                        price_elem = container.select_one('div.hZ3P6w.DeU9vF')
                    if not price_elem:
                        # Try to find price by looking for rupee symbol
                        for elem in container.select('div'):
                            text = elem.get_text(strip=True)
                            if text.startswith('â') or 'â¹' in text:
                                price_elem = elem
                                break
                    
                    if price_elem:
                        product['price'] = price_elem.get_text(strip=True)
                    else:
                        product['price'] = 'N/A'
                    
                    # Get rating
                    rating_elem = container.select_one('div._3LWZlK, span._1lRcQB, span.Wphh3N')
                    if rating_elem:
                        product['rating'] = rating_elem.get_text(strip=True)
                    
                    product['source'] = 'Flipkart'
                    results.append(product)
                    
                    if len(results) >= max_results:
                        break
                        
                except Exception as e:
                    continue
        
    except Exception as e:
        print(f"Playwright error: {e}", file=sys.stderr)
    
    return results


# Alias for backward compatibility
_flipkart_playwright_search = _flipkart_selenium_search


def _flipkart_direct_search(query: str, session: requests.Session) -> List[Dict[str, str]]:
    """Direct search approach for Flipkart."""
    results = []
    encoded_query = urllib.parse.quote(query)
    
    # Step 1: Visit homepage first to establish session
    headers = get_flipkart_headers()
    try:
        homepage_resp = session.get("https://www.flipkart.com/", headers=headers, timeout=15)
        time.sleep(random.uniform(1.5, 3.0))
    except:
        pass
    
    # Step 2: Visit a category page
    try:
        category_headers = get_flipkart_headers(referer="https://www.flipkart.com/")
        session.get("https://www.flipkart.com/mobiles", headers=category_headers, timeout=15)
        time.sleep(random.uniform(1.0, 2.0))
    except:
        pass
    
    # Step 3: Perform the search
    search_url = f"https://www.flipkart.com/search?q={encoded_query}"
    search_headers = get_flipkart_headers(referer="https://www.flipkart.com/mobiles")
    
    response = session.get(search_url, headers=search_headers, timeout=20)
    
    if response.status_code != 200:
        return results
    
    return _parse_flipkart_results(response)


def _flipkart_api_search(query: str, max_results: int, session: requests.Session) -> List[Dict[str, str]]:
    """Use Flipkart's internal API for search."""
    results = []
    encoded_query = urllib.parse.quote(query)
    
    # Flipkart uses an autocomplete/suggestion API
    api_url = f"https://www.flipkart.com/api/3/product/dropdown?"
    params = {
        'q': query,
    }
    
    headers = {
        'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Safari/537.36',
        'Accept': 'application/json, text/plain, */*',
        'Accept-Language': 'en-IN,en;q=0.9',
        'Referer': 'https://www.flipkart.com/',
        'Origin': 'https://www.flipkart.com',
    }
    
    try:
        response = session.get(api_url, params=params, headers=headers, timeout=15)
        if response.status_code == 200:
            data = response.json()
            # Parse API response if successful
            if data and isinstance(data, dict):
                # Extract products from response
                products = data.get('products', []) or data.get('items', [])
                for item in products[:max_results]:
                    product = {
                        'title': item.get('title', item.get('name', '')),
                        'link': f"https://www.flipkart.com{item.get('url', '')}",
                        'price': item.get('price', 'N/A'),
                        'source': 'Flipkart'
                    }
                    if product['title']:
                        results.append(product)
    except:
        pass
    
    # Alternative: Try the search API
    if not results:
        search_api = f"https://www.flipkart.com/api/3/search?q={encoded_query}"
        try:
            response = session.get(search_api, headers=headers, timeout=15)
            if response.status_code == 200:
                return _parse_flipkart_api_response(response, max_results)
        except:
            pass
    
    return results


def _flipkart_mobile_search(query: str, max_results: int, session: requests.Session) -> List[Dict[str, str]]:
    """Mobile site search for Flipkart."""
    results = []
    encoded_query = urllib.parse.quote(query)
    
    mobile_url = f"https://m.flipkart.com/search?q={encoded_query}"
    mobile_headers = {
        'User-Agent': 'Mozilla/5.0 (Linux; Android 13; SM-G991B) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/122.0.0.0 Mobile Safari/537.36',
        'Accept': 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
        'Accept-Language': 'en-IN,en;q=0.9',
        'Accept-Encoding': 'gzip, deflate, br',
    }
    
    response = session.get(mobile_url, headers=mobile_headers, timeout=20)
    
    if response.status_code != 200:
        return results
    
    return _parse_flipkart_results(response, mobile=True)


def _parse_flipkart_results(response: requests.Response, mobile: bool = False) -> List[Dict[str, str]]:
    """Parse Flipkart search results from HTML response."""
    results = []
    soup = BeautifulSoup(response.content, 'html.parser')
    
    # Check for blocking
    if 'captcha' in response.text.lower() or 'access denied' in response.text.lower():
        return results
    
    if len(response.text) < 1000:
        return results
    
    # Find product containers - try multiple selectors
    selectors = [
        'div._1AtVbE',
        'div[data-id]',
        'div._13oc-S > div',
        'div._1UoZlX',  # Mobile
        'div[data-tkid]',  # Mobile
        'div._2kHMte',  # Another variant
        'div._4ddWXP',  # Grid view
        'a._1fQZEK',  # Direct product links
    ]
    
    product_containers = []
    for selector in selectors:
        product_containers = soup.select(selector)
        if product_containers:
            break
    
    for container in product_containers:
        try:
            product = {}
            
            # Get product title
            title_selectors = ['div._4rR01T', 'a.IRpwTa', 'a.s1Q9rs', 'div._2WkVRV', 'a._2cLu-l']
            for sel in title_selectors:
                title_elem = container.select_one(sel)
                if title_elem:
                    product['title'] = title_elem.get_text(strip=True)
                    break
            
            if not product.get('title'):
                # If container is an anchor, get text directly
                if container.name == 'a':
                    product['title'] = container.get_text(strip=True)
            
            if not product.get('title'):
                continue
            
            # Get product link
            link_elem = container if container.name == 'a' else container.select_one('a')
            if link_elem:
                href = link_elem.get('href', '')
                if href.startswith('/'):
                    product['link'] = f"https://www.flipkart.com{href.split('?')[0]}"
                elif href.startswith('http'):
                    product['link'] = href
            
            if not product.get('link'):
                continue
            
            # Get price
            price_selectors = ['div._30jeq3', 'div._1vC4OE', 'div._25b18c']
            for sel in price_selectors:
                price_elem = container.select_one(sel)
                if price_elem:
                    product['price'] = price_elem.get_text(strip=True)
                    break
            
            if not product.get('price'):
                product['price'] = 'N/A'
            
            # Get rating
            rating_elem = container.select_one('div._3LWZlK, span._1lRcQB')
            if rating_elem:
                product['rating'] = rating_elem.get_text(strip=True)
            
            product['source'] = 'Flipkart'
            results.append(product)
            
        except Exception:
            continue
    
    return results


def _parse_flipkart_api_response(response: requests.Response, max_results: int) -> List[Dict[str, str]]:
    """Parse Flipkart API JSON response."""
    results = []
    try:
        data = response.json()
        # Navigate the API response structure
        items = []
        if 'result' in data:
            items = data['result'].get('items', [])
        elif 'products' in data:
            items = data['products']
        elif isinstance(data, list):
            items = data
        
        for item in items[:max_results]:
            product = {
                'title': item.get('title', item.get('name', item.get('productName', ''))),
                'link': f"https://www.flipkart.com{item.get('url', item.get('productUrl', ''))}",
                'price': item.get('price', item.get('sellingPrice', 'N/A')),
                'source': 'Flipkart'
            }
            if product['title'] and product['link']:
                results.append(product)
    except:
        pass
    return results


def generate_direct_links(query: str) -> Dict[str, str]:
    """
    Generate direct search URLs for Amazon and Flipkart.
    These are guaranteed to work even if scraping fails.
    
    Args:
        query: Search query
        
    Returns:
        Dictionary with direct search URLs
    """
    encoded_amazon = urllib.parse.quote_plus(query)
    encoded_flipkart = urllib.parse.quote(query)
    
    return {
        'amazon_search': f"https://www.amazon.in/s?k={encoded_amazon}",
        'flipkart_search': f"https://www.flipkart.com/search?q={encoded_flipkart}",
    }


def get_shopping_links(query: str, max_results: int = 5) -> Dict[str, any]:
    """
    Get shopping links from both Amazon and Flipkart.
    
    Args:
        query: Search query (e.g., "Oneplus 15")
        max_results: Maximum number of results per store
        
    Returns:
        Dictionary with 'amazon' and 'flipkart' keys containing lists of products
    """
    results = {
        'query': query,
        'amazon': [],
        'flipkart': [],
        'direct_links': generate_direct_links(query)
    }
    
    print(f"Searching for: {query}", file=sys.stderr)
    print("-" * 50, file=sys.stderr)
    
    session = get_session()
    
    # Search Amazon
    print("Fetching Amazon results...", file=sys.stderr)
    results['amazon'] = search_amazon(query, max_results, session)
    print(f"Found {len(results['amazon'])} Amazon results", file=sys.stderr)
    
    # Add a delay between requests
    time.sleep(2)
    
    # Search Flipkart
    print("Fetching Flipkart results...", file=sys.stderr)
    results['flipkart'] = search_flipkart(query, max_results, session)
    print(f"Found {len(results['flipkart'])} Flipkart results", file=sys.stderr)
    
    return results


def main():
    parser = argparse.ArgumentParser(
        description='Get Amazon and Flipkart shopping links for a device.',
        formatter_class=argparse.RawDescriptionHelpFormatter,
        epilog='''
Examples:
  python shopping_links.py "Oneplus 15"
  python shopping_links.py "iPhone 15 Pro" --max-results 10
  python shopping_links.py "Samsung Galaxy S24" --json
  python shopping_links.py "Oneplus 15" --direct-only
  
Note: 
  Amazon and Flipkart have anti-bot protections. If scraping fails,
  use --direct-only to get direct search URLs, or run from a different
  network/VPN.
        '''
    )
    parser.add_argument('query', help='The device name to search for (e.g., "Oneplus 15")')
    parser.add_argument('--max-results', '-m', type=int, default=5,
                        help='Maximum number of results per store (default: 5)')
    parser.add_argument('--json', '-j', action='store_true',
                        help='Output results as JSON')
    parser.add_argument('--amazon-only', action='store_true',
                        help='Search only Amazon')
    parser.add_argument('--flipkart-only', action='store_true',
                        help='Search only Flipkart')
    parser.add_argument('--direct-only', action='store_true',
                        help='Only generate direct search URLs (no scraping)')
    
    args = parser.parse_args()
    
    if args.direct_only:
        direct_links = generate_direct_links(args.query)
        if args.json:
            result = {'query': args.query, 'direct_links': direct_links}
            print(json.dumps(result, indent=2))
        else:
            print(f"\nDirect search URLs for: {args.query}")
            print("=" * 60)
            print(f"\n📱 Amazon: {direct_links['amazon_search']}")
            print(f"\n🛒 Flipkart: {direct_links['flipkart_search']}")
            print()
        return
    
    results = {
        'query': args.query,
        'amazon': [],
        'flipkart': [],
        'direct_links': generate_direct_links(args.query)
    }
    
    session = get_session()
    
    if args.amazon_only:
        results['amazon'] = search_amazon(args.query, args.max_results, session)
    elif args.flipkart_only:
        results['flipkart'] = search_flipkart(args.query, args.max_results, session)
    else:
        results['amazon'] = search_amazon(args.query, args.max_results, session)
        time.sleep(2)
        results['flipkart'] = search_flipkart(args.query, args.max_results, session)
    
    if args.json:
        print(json.dumps(results, indent=2))
    else:
        # Pretty print results
        print("\n" + "=" * 60)
        print(f"SEARCH RESULTS FOR: {args.query}")
        print("=" * 60)
        
        if results['amazon']:
            print("\n📱 AMAZON.IN")
            print("-" * 40)
            for i, product in enumerate(results['amazon'], 1):
                print(f"\n{i}. {product.get('title', 'N/A')}")
                print(f"   Price: {product.get('price', 'N/A')}")
                if product.get('rating'):
                    print(f"   Rating: {product.get('rating')}")
                print(f"   Link: {product.get('link', 'N/A')}")
        else:
            print("\n📱 AMAZON.IN")
            print("-" * 40)
            print("No results found (or blocked by anti-bot protection)")
            print(f"Direct link: {results['direct_links']['amazon_search']}")
        
        if results['flipkart']:
            print("\n\n🛒 FLIPKART.COM")
            print("-" * 40)
            for i, product in enumerate(results['flipkart'], 1):
                print(f"\n{i}. {product.get('title', 'N/A')}")
                print(f"   Price: {product.get('price', 'N/A')}")
                if product.get('rating'):
                    print(f"   Rating: {product.get('rating')}")
                print(f"   Link: {product.get('link', 'N/A')}")
        else:
            print("\n\n🛒 FLIPKART.COM")
            print("-" * 40)
            print("No results found (or blocked by anti-bot protection)")
            print(f"Direct link: {results['direct_links']['flipkart_search']}")
        
        print("\n" + "=" * 60)


if __name__ == "__main__":
    main()
