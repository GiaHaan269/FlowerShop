from tensorflow.keras.models import load_model
import joblib

# Load mô hình MobileNet đã fine-tune
mobilenet_model = load_model('mobilenet_finetuned.h5')

# Load mô hình SVM
with open('svm_model_new.pkl', 'rb') as f:
    svm_model = joblib.load(f)

import os
import cv2
import numpy as np
import pickle
from tensorflow.keras.applications.mobilenet import preprocess_input



# Hàm trích xuất đặc trưng
def extract_features(image_path, model):
    img = cv2.imread(image_path)
    img_resized = cv2.resize(img, (128, 128))  # Kích thước tương thích với MobileNet
    img_preprocessed = preprocess_input(img_resized.astype("float32"))
    img_expanded = np.expand_dims(img_preprocessed, axis=0)
    feature_extractor = tf.keras.Model(inputs=model.input, outputs=model.layers[-2].output)
    features = feature_extractor.predict(img_expanded, verbose=0).flatten()
    return features

# Tạo cơ sở dữ liệu
product_database = []
image_directory = 'F:/xampp/htdocs/flowershop/uploaded_files' # Thư mục chứa ảnh
for image_name in os.listdir(image_directory):
    image_path = os.path.join(image_directory, image_name)
    features = extract_features(image_path, mobilenet_model)
    product_database.append({'features': features, 'image_path': image_path})

# Lưu cơ sở dữ liệu vào file
with open('product_database.pkl', 'wb') as f:
    pickle.dump(product_database, f)

print("Cơ sở dữ liệu đặc trưng đã được tạo.")

# Load cơ sở dữ liệu
with open('product_database.pkl', 'rb') as f:
    product_database = pickle.load(f)

from sklearn.metrics.pairwise import cosine_similarity

# Hàm tìm kiếm sản phẩm tương tự
def search_similar_product(query_image_path, model, database):
    query_features = extract_features(query_image_path, model)
    similarities = []
    for product in database:
        similarity = cosine_similarity([query_features], [product['features']])[0][0]
        similarities.append({'image_path': product['image_path'], 'similarity': similarity})
    
    # Sắp xếp theo độ tương đồng
    similarities = sorted(similarities, key=lambda x: x['similarity'], reverse=True)
    return similarities[:5]  # Trả về top 5 kết quả

query_image_path = "path_to_user_uploaded_image.jpg"
top_results = search_similar_product(query_image_path, mobilenet_model, product_database)

for result in top_results:
    print(f"Image Path: {result['image_path']}, Similarity: {result['similarity']:.2f}")
