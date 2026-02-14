import os
import requests
from rembg import remove
from PIL import Image
from io import BytesIO

image_url = "https://m.media-amazon.com/images/I/61KXgizurpL._SL1500_.jpg"
output_path = "/home/abhay/repos/phone_finder/public/storage/phones/oneplus_15.png"

def process_image():
    try:
        print(f"Downloading image from {image_url}...")
        response = requests.get(image_url)
        response.raise_for_status()
        
        input_image = Image.open(BytesIO(response.content))
        
        print("Removing background...")
        output_image = remove(input_image)
        
        # Ensure directory exists
        os.makedirs(os.path.dirname(output_path), exist_ok=True)
        
        print(f"Saving to {output_path}...")
        output_image.save(output_path, "PNG")
        print("Done!")
        
    except Exception as e:
        print(f"Error: {e}")

if __name__ == "__main__":
    process_image()
