<?php
if (isset($_FILES['image'])) {
    $uploadDir = 'uploaded_files/';

    // Tạo thư mục nếu chưa tồn tại
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Đường dẫn lưu file
    $filePath = $uploadDir . basename($_FILES['image']['name']);

    // Kiểm tra và di chuyển file tải lên
    if (move_uploaded_file($_FILES['image']['tmp_name'], $filePath)) {
        echo "File đã được tải lên thành công: $filePath";
    } else {
        echo "Lỗi khi tải file.";
        exit;
    }

    // Tiếp tục xử lý file đã lưu
    $url = 'http://127.0.0.1:5000/search';

    $cFile = new CURLFile($filePath, 'image/jpeg', $_FILES['image']['name']);
    $data = array('image' => $cFile);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

    if ($response === false) {
        echo 'Lỗi cURL: ' . curl_error($ch);
        curl_close($ch);
        exit;
    }

    curl_close($ch);

    $results = json_decode($response, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo "Lỗi giải mã JSON: " . json_last_error_msg();
        echo "<pre>Phản hồi từ API: $response</pre>";
        exit;
    }

    if (is_array($results)) {
        echo "<h2>Kết quả tìm kiếm:</h2>";
        foreach ($results as $result) {
            echo "<p><img src='{$result['image_path']}' style='width:100px;'> Độ tương đồng: {$result['similarity']}</p>";
        }
    } else {
        echo "Phản hồi từ API không hợp lệ.";
    }
}
?>