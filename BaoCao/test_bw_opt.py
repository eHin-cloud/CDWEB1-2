import os
from PIL import Image, ImageEnhance, ImageFilter

def optimize_for_bw_print(img_path, output_path):
    img = Image.open(img_path)
    
    # 1. Convert to RGB if RGBA/P
    if img.mode != 'RGB':
        img = img.convert('RGB')
        
    # 2. Convert to Grayscale
    gray = img.convert('L')
    
    # 3. Enhance Contrast (increase contrast so text is dark and background is light)
    enhancer = ImageEnhance.Contrast(gray)
    contrasted = enhancer.enhance(1.4)
    
    # 4. Enhance Brightness slightly so light backgrounds go to pure white
    enhancer_b = ImageEnhance.Brightness(contrasted)
    brightened = enhancer_b.enhance(1.1)
    
    # 5. Point curve / thresholding: make near-white pure white (> 220 -> 255)
    # and make dark text darker (< 100 -> dark)
    table = []
    for i in range(256):
        if i > 215:
            table.append(255)
        elif i < 90:
            table.append(int(i * 0.75))
        else:
            table.append(i)
    processed = brightened.point(table)
    
    # 6. Subtle sharpen for crisp UI lines and fonts
    sharpened = processed.filter(ImageFilter.UnsharpMask(radius=1.5, percent=120, threshold=3))
    
    sharpened.save(output_path, quality=95)
    print(f"Saved: {output_path}")

test_img = "extracted_images/image1.png"
if os.path.exists(test_img):
    optimize_for_bw_print(test_img, "scratch/test_bw_print.png")
