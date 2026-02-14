import os
from rembg import remove
from PIL import Image

def remove_backgrounds(directory):
    # Ensure the directory exists
    if not os.path.exists(directory):
        print(f"Directory {directory} does not exist.")
        return

    # iterate over files in that directory
    for filename in os.listdir(directory):
        if filename.lower().endswith(('.png', '.jpg', '.jpeg')):
            # Skip icons and already processed files
            if 'icon' in filename.lower() or '_nobg' in filename.lower():
                continue

            input_path = os.path.join(directory, filename)
            output_filename = os.path.splitext(filename)[0] + "_nobg.png"
            output_path = os.path.join(directory, output_filename)

            print(f"Processing: {filename} -> {output_filename}")

            try:
                input_image = Image.open(input_path)
                output_image = remove(input_image)
                output_image.save(output_path)
                print(f"Successfully saved to {output_path}")
            except Exception as e:
                print(f"Error processing {filename}: {e}")

if __name__ == "__main__":
    assets_dir = os.path.join(os.getcwd(), 'public/assets')
    remove_backgrounds(assets_dir)
