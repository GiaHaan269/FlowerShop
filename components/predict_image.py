import sys
import numpy as np
import cv2
import os
import traceback
from tensorflow.keras.models import load_model

# ======================== Kiểm tra tham số ========================
if len(sys.argv) < 2:
    print("Error: No image path provided!")
    sys.exit(1)

image_path = sys.argv[1]

# ======================== Kiểm tra đường dẫn ảnh ========================
if not os.path.exists(image_path):
    print(f"Error: Image path '{image_path}' not found!")
    sys.exit(1)

# ======================== Kiểm tra mô hình ========================
model_path = 'F:/xampp/htdocs/flowershop/modelai/mobilenet_finetuned.h5'
if not os.path.exists(model_path):
    print(f"Error: Model path '{model_path}' not found!")
    sys.exit(1)

# ======================== Load ảnh ========================
img = cv2.imread(image_path)
if img is None:
    print(f"Error: Failed to load image from '{image_path}'. Check the file path and format.")
    sys.exit(1)

# ======================== Load mô hình ========================
try:
    model = load_model(model_path)
except Exception as e:
    print(f"Error: Failed to load model from '{model_path}': {str(e)}")
    traceback.print_exc()
    sys.exit(1)

# ======================== Tiền xử lý ảnh ========================
try:
    img = cv2.resize(img, (128, 128))  # Resize to model input size
    img = img.astype("float32") / 255.0  # Normalize pixel values
    img = np.expand_dims(img, axis=0)  # Add batch dimension
except Exception as e:
    print(f"Error in image preprocessing: {str(e)}")
    traceback.print_exc()
    sys.exit(1)

# ======================== Dự đoán với mô hình ========================
try:
    predictions = model.predict(img)
    categories = ['camtucau', 'hoacuc', 'hoalan', 'hoaoaihuong', 'hoamaudon', 'hoahong', 'hoahuongduong', 'hoatulip']
    predicted_class = np.argmax(predictions, axis=1)[0]
    print(f"Predicted Class: {categories[predicted_class]}")
    print(f"Prediction Probabilities: {predictions}")
except Exception as e:
    print(f"Error during prediction: {str(e)}")
    traceback.print_exc()
    sys.exit(1)
