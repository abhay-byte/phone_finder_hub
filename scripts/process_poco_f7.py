
import requests
from PIL import Image
from io import BytesIO
import os

URL = "https://rukminim2.flixcart.com/image/1900/1900/xif0q/mobile/w/2/d/-original-imahdpu7ndangb4q.jpeg?q=90"
OUTPUT_PATH = "storage/app/public/phones/poco-f7.png"

def remove_white_bg(image, threshold=250):
    img = image.convert("RGBA")
    datas = img.getdata()
    
    newData = []
    for item in datas:
        if item[0] >= threshold and item[1] >= threshold and item[2] >= threshold:
            newData.append((255, 255, 255, 0))
        else:
            newData.append(item)
    
    img.putdata(newData)
    return img

def process_image():
    print(f"Downloading image from {URL}...")
    response = requests.get(URL)
    response.raise_for_status()

    input_image = Image.open(BytesIO(response.content))
    
    print("Removing white background...")
    output_image = remove_white_bg(input_image)
    
    # Ensure directory exists
    os.makedirs(os.path.dirname(OUTPUT_PATH), exist_ok=True)

    print(f"Saving to {OUTPUT_PATH}...")
    output_image.save(OUTPUT_PATH, "PNG")
    print("Done!")

if __name__ == "__main__":
    process_image()
