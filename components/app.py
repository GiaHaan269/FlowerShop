from flask import Flask, request, jsonify
import tensorflow as tf
import numpy as np
import pickle
from sklearn.metrics.pairwise import cosine_similarity

# Load model và feature database
model = tf.keras.models.load_model(r'F:\xampp\htdocs\flowershop\modelai\mobilenet_finetuned.h5')
with open(r'F:\xampp\htdocs\flowershop\modelai\features_database.pkl', 'rb') as f:
    features_db = pickle.load(f)
    
categories = ['cam tu cau', 'hoa cuc', 'hoa lan', 'hoa oai huong', 'hoa mau don', 'hoa hong', 'hoa huong duong', 'hoa tulip']


app = Flask(__name__)

def extract_features(image_path):
    from tensorflow.keras.preprocessing import image
    img = image.load_img(image_path, target_size=(224, 224))
    img_array = image.img_to_array(img)
    img_array = np.expand_dims(img_array, axis=0) / 255.0
    features = model.predict(img_array)
    return features

@app.route('/search', methods=['POST'])
def search():
    image_file = request.files['image']
    query_image_path = f"/tmp/{image_file.filename}"
    image_file.save(query_image_path)
    
    query_features = extract_features(query_image_path)
    
    # Tính toán độ tương đồng
    similarities = cosine_similarity(query_features, features_db['features'])
    indices = np.argsort(similarities[0])[::-1][:5]  # 5 kết quả tương tự nhất
    
    results = [{"image_path": features_db['paths'][i], "similarity": float(similarities[0][i])} for i in indices]
    return jsonify(results)

if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000)
