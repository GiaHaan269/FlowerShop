<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!isset($_FILES['image']) || $_FILES['image']['error'] != UPLOAD_ERR_OK) {
        die("Error uploading file.");
    }

    // Đọc file ảnh
    $imagePath = $_FILES['image']['tmp_name'];

    // Gửi yêu cầu tới Flask API
    $apiUrl = "http://localhost:5000/search"; // Địa chỉ Flask API
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $apiUrl);
    curl_setopt($ch, CURLOPT_POST, 1);

    $cfile = new CURLFile($imagePath, $_FILES['image']['type'], $_FILES['image']['name']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, ['image' => $cfile]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die("Error: " . curl_error($ch));
    }

    curl_close($ch);

    // Hiển thị kết quả
    $result = json_decode($response, true);
    if (isset($result['result'])) {
        echo "Closest image: " . htmlspecialchars($result['result']);
    } else {
        echo "Error: " . htmlspecialchars($response);
    }
}
?>