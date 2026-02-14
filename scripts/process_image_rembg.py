
import sys
import argparse
import requests
from rembg import remove
from PIL import Image
from io import BytesIO
import os

def process_image(url, output_path):
    print(f"Downloading image from {url}...")
    try:
        response = requests.get(url, stream=True)
        response.raise_for_status()
        input_image = Image.open(BytesIO(response.content))
        
        print("Removing background using rembg...")
        output_image = remove(input_image)
        
        # Ensure directory exists
        os.makedirs(os.path.dirname(output_path), exist_ok=True)

        print(f"Saving to {output_path}...")
        output_image.save(output_path, "PNG")
        print("Done!")
    except Exception as e:
        print(f"Error processing image: {e}")
        sys.exit(1)

if __name__ == "__main__":
    parser = argparse.ArgumentParser(description="Download an image and remove its background.")
    parser.add_argument("url", help="URL of the image to download")
    parser.add_argument("output", help="Output path for the processed image (e.g., storage/app/public/phones/my-phone.png)")
    
    args = parser.parse_args()
    process_image(args.url, args.output)
