
import sys
import os
import requests
from rembg import remove
from PIL import Image
from io import BytesIO

def process_image(url, output_path):
    print(f"Downloading image from {url}...")
    response = requests.get(url)
    if response.status_code != 200:
        print(f"Failed to download image: {response.status_code}")
        sys.exit(1)

    print("Removing background...")
    input_image = Image.open(BytesIO(response.content))
    output_image = remove(input_image)

    # Resize if needed (standardizing width to 800px or similar)
    # output_image.thumbnail((800, 800)) 

    print(f"Saving to {output_path}...")
    output_image.save(output_path, "PNG")
    print("Done!")

if __name__ == "__main__":
    if len(sys.argv) < 3:
        print("Usage: python3 process_image.py <URL> <OUTPUT_PATH>")
        sys.exit(1)
    
    url = sys.argv[1]
    output = sys.argv[2]
    process_image(url, output)
