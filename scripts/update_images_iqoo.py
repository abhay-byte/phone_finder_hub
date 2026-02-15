
import os
import requests
from rembg import remove
from PIL import Image
import io

# Phone configuration
phones = [
    {
        'name': 'iQOO Neo 10',
        'url': 'https://cdn.beebom.com/mobile/Untitled%20design%20(94).png',
        'filename': 'iqoo-neo-10_nobg.png'
    },
    {
        'name': 'iQOO 15',
        'url': 'https://asia-exstatic-vivofs.vivo.com/PSee2l50xoirPK7y/1766462834284/88b55d5b8388f8d95a3334d8a040740c.png',
        'filename': 'iqoo-15_nobg.png' # Keeping consistent naming
    }
]

output_dir = '/home/abhay/repos/phone_finder/public/assets'

for phone in phones:
    print(f"Processing {phone['name']}...")
    try:
        # 1. Download Image
        headers = {'User-Agent': 'Mozilla/5.0'}
        response = requests.get(phone['url'], headers=headers)
        response.raise_for_status()
        
        # 2. Process Image (Remove Background)
        input_image = Image.open(io.BytesIO(response.content))
        output_image = remove(input_image)
        
        # 3. Save Image
        output_path = os.path.join(output_dir, phone['filename'])
        output_image.save(output_path)
        print(f"Saved processed image to: {output_path}")

    except Exception as e:
        print(f"Error processing {phone['name']}: {e}")

print("Image processing complete.")
