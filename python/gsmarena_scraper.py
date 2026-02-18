import requests
from bs4 import BeautifulSoup
import argparse
import json
import sys
import re
import base64
try:
    from cryptography.hazmat.primitives.ciphers import Cipher, algorithms, modes
    from cryptography.hazmat.backends import default_backend
except ImportError:
    pass # Handle gracefully if not installed or used

def decrypt_data(iv_b64, key_b64, data_b64):
    try:
        iv = base64.b64decode(iv_b64)
        key = base64.b64decode(key_b64)
        data = base64.b64decode(data_b64)

        cipher = Cipher(algorithms.AES(key), modes.CBC(iv), backend=default_backend())
        decryptor = cipher.decryptor()
        decrypted_padded = decryptor.update(data) + decryptor.finalize()
        
        # Remove PKCS7 padding
        padding_len = decrypted_padded[-1]
        decrypted = decrypted_padded[:-padding_len]
        return decrypted.decode('utf-8')
    except Exception as e:
        print(f"Decryption error: {e}", file=sys.stderr)
        return None

def scrape_gsmarena(url):
    try:
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        }
        response = requests.get(url, headers=headers)
        response.raise_for_status()
        
        soup = BeautifulSoup(response.content, 'html.parser')
        
        data = {}
        
        # 1. Device Name
        device_name_tag = soup.select_one('h1.specs-phone-name-title')
        if device_name_tag:
            data['device_name'] = device_name_tag.get_text(strip=True)
            
        # 2. Device Image
        img_tag = soup.select_one('.specs-photo-main img')
        if img_tag:
            src = img_tag.get('src')
            if src:
                data['image_url'] = src
                
        # 3. Specifications
        specs_list = soup.find(id='specs-list')
        if specs_list:
            tables = specs_list.find_all('table')
            data['specifications'] = {}
            
            for table in tables:
                # Category (th)
                th = table.find('th')
                if th:
                    category = th.get_text(strip=True)
                    data['specifications'][category] = {}
                    
                    # Rows (tr)
                    rows = table.find_all('tr')
                    for row in rows:
                        ttl = row.find('td', class_='ttl')
                        nfo = row.find('td', class_='nfo')
                        
                        if ttl and nfo:
                            label = ttl.get_text(strip=True)
                            # Remove "Review" link text if present in label
                            # sometimes label has an 'a' tag inside
                            
                            value = nfo.get_text(strip=True)
                            
                            # Handle cases where multiple values might be present, but typically get_text handles it.
                            # We might want to key by label.
                            
                            # Clean up label (sometimes has strange characters)
                            label = label.replace('\u00a0', ' ')

                            if label == "Performance":
                                # Special handling for Performance to break it down
                                raw_perf = nfo.get_text(separator='|', strip=True)
                                perf_parts = raw_perf.split('|')
                                perf_dict = {}
                                for part in perf_parts:
                                    if ':' in part:
                                        key, val = part.split(':', 1)
                                        perf_dict[key.strip()] = val.strip()
                                    else:
                                        # Fallback if no colon
                                        perf_dict[part.strip()] = ""
                                value = perf_dict
                            else:
                                value = nfo.get_text(separator=' ', strip=True) # Use separator to avoid concatenation
                                
                            if not label:
                                # Handle empty keys (e.g. IP rating, UFS)
                                # Append to the last used key in this category if it exists
                                # Or use a placeholder like "Other"
                                if data['specifications'][category]:
                                    last_key = list(data['specifications'][category].keys())[-1]
                                    # Append to previous value
                                    current_val = data['specifications'][category][last_key]
                                    if isinstance(current_val, list):
                                        current_val.append(value)
                                    else:
                                        data['specifications'][category][last_key] = [current_val, value]
                                else:
                                    # Fallback if it's the first item
                                    data['specifications'][category]["Other"] = value
                            
                            elif label in data['specifications'][category]:
                                # Append if duplicate key (rare but possible in some formats)
                                if isinstance(data['specifications'][category][label], list):
                                    data['specifications'][category][label].append(value)
                                else:
                                    data['specifications'][category][label] = [data['specifications'][category][label], value]
                            else:
                                data['specifications'][category][label] = value
                                
        return data

    except requests.exceptions.RequestException as e:
        print(f"Error fetching URL: {e}", file=sys.stderr)
        return None
    except Exception as e:
        print(f"An error occurred: {e}", file=sys.stderr)
        return None

def search_gsmarena(query):
    try:
        url = f"https://www.gsmarena.com/res.php3?sSearch={query}"
        headers = {
            'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        }
        response = requests.get(url, headers=headers)
        response.raise_for_status()
        
        html_content = response.text
        
        # Check for encryption
        key_match = re.search(r'const KEY\s*=\s*"([^"]+)"', html_content)
        iv_match = re.search(r'const IV\s*=\s*"([^"]+)"', html_content)
        data_match = re.search(r'const DATA\s*=\s*"([^"]+)"', html_content)

        if key_match and iv_match and data_match and 'cryptography' in sys.modules:
            try:
                decrypted_html = decrypt_data(iv_match.group(1), key_match.group(1), data_match.group(1))
                if decrypted_html:
                    soup = BeautifulSoup(decrypted_html, 'html.parser')
                    makers = soup.select('.makers ul li a')
                    if makers:
                        first_result = makers[0]
                        link = f"https://www.gsmarena.com/{first_result['href']}"
                        print(f"Found phone (encrypted): {first_result.get_text(strip=True)}", file=sys.stderr)
                        print(f"URL: {link}", file=sys.stderr)
                        return link
            except Exception as e:
                print(f"Failed to process encrypted results: {e}", file=sys.stderr)

        # Fallback to standard parsing
        soup = BeautifulSoup(html_content, 'html.parser')
        
        makers = soup.select('.makers ul li a')
        if makers:
            first_result = makers[0]
            link = f"https://www.gsmarena.com/{first_result['href']}"
            print(f"Found phone: {first_result.get_text(strip=True)}", file=sys.stderr)
            print(f"URL: {link}", file=sys.stderr)
            return link
        else:
            print("No results found.", file=sys.stderr)
            return None
            
    except Exception as e:
        print(f"Error searching: {e}", file=sys.stderr)
        return None

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description='Scrape phone specifications from GSMArena.')
    parser.add_argument('query', help='The GSMArena URL or phone name to scrape.')
    args = parser.parse_args()
    
    query = args.query
    if "gsmarena.com" in query:
        url = query
    else:
        url = search_gsmarena(query)
        
    if url:
        result = scrape_gsmarena(url)
        
        if result:
            print(json.dumps(result, indent=4))
        else:
            sys.exit(1)
    else:
        sys.exit(1)
