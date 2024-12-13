import sys
import numpy as np
import cv2
import os
import pickle
from tensorflow.keras.models import load_model
from tensorflow.keras.applications.mobilenet import preprocess_input
import tensorflow as tf
import traceback
import joblib  # Import joblib


# Vô hiệu hóa các thông báo của TensorFlow
os.environ['TF_CPP_MIN_LOG_LEVEL'] = '3'
tf.get_logger().setLevel('ERROR')

# Đường dẫn ảnh (nhận từ PHP hoặc trực tiếp)
image_path = sys.argv[1]

# Kiểm tra nếu đường dẫn tồn tại
if not os.path.exists(image_path):
    print(f"Error: Image path {image_path} not found!")
    sys.exit()

# Load ảnh
img = cv2.imread(image_path)
if img is None:
    print(f"Error: Failed to load image from {image_path}. Check the file path and file format.")
    sys.exit()

# Đường dẫn mô hình
mobilenet_model_path = 'F:/xampp/htdocs/flowershop/modelai/mobilenet_finetuned.h5'
svm_model_path = 'F:/xampp/htdocs/flowershop/modelai/svm_model_new.pkl'

if not os.path.exists(mobilenet_model_path):
    print(f"Error: MobileNet model path {mobilenet_model_path} not found!")
    sys.exit()

if not os.path.exists(svm_model_path):
    print(f"Error: SVM model path {svm_model_path} not found!")
    sys.exit()

try:
    # Load mô hình MobileNet đã huấn luyện
    mobilenet_model = load_model(mobilenet_model_path)

    # Load mô hình SVM từ file pkl
    with open(svm_model_path, 'rb') as f:
        svm_model = joblib.load(svm_model_path)

except Exception as e:
    print("An error occurred while loading models:")
    print(str(e))  # Display the error message
    print("Full traceback:")
    traceback.print_exc()  # Display the full traceback
    sys.exit()

# Tiền xử lý ảnh
img_resized = cv2.resize(img, (128, 128))  # Resize image to the input size expected by the model
img_normalized = img_resized.astype("float32") / 255.0  # Normalize the image
img_expanded = np.expand_dims(img_normalized, axis=0)  # Thêm chiều batch
img_preprocessed = preprocess_input(img_expanded)

# Trích xuất đặc trưng từ MobileNet
feature_extractor = tf.keras.Model(inputs=mobilenet_model.input, outputs=mobilenet_model.layers[-2].output)
features = feature_extractor.predict(img_preprocessed, verbose=0)  # Đầu ra đặc trưng 2D

# Chuyển đổi đặc trưng thành vector 1D cho SVM
flattened_features = features.flatten().reshape(1, -1)

# Dự đoán bằng MobileNet
mobilenet_predictions = mobilenet_model.predict(img_preprocessed, verbose=0)
mobilenet_predicted_class = np.argmax(mobilenet_predictions, axis=1)[0]

# Dự đoán bằng SVM
svm_predicted_class = svm_model.predict(flattened_features)[0]

# Lớp danh mục
categories = ['cam tu cau', 'hoa cuc', 'hoa lan', 'hoa oai huong', 'hoa mau don', 'hoa hong', 'hoa huong duong', 'hoa tulip']

# Lấy dự đoán của cả hai mô hình
mobilenet_label = categories[mobilenet_predicted_class]
svm_label = categories[svm_predicted_class]

# Kết hợp kết quả từ cả hai mô hình
if mobilenet_label == svm_label:
    combined_prediction = mobilenet_label
else:
    combined_prediction = f"MobileNet: {mobilenet_label} | SVM: {svm_label}"

# In ra kết quả dự đoán cuối cùng
print(categories[mobilenet_predicted_class])
