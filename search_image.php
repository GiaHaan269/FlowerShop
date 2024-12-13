<?php
// Kết nối cơ sở dữ liệu
include 'components/connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    // Thư mục lưu trữ ảnh tải lên
    $uploadDir = 'uploaded_images/';
    $imageName = basename($_FILES['image']['name']);
    $targetFile = $uploadDir . $imageName;

    // Kiểm tra và tải lên ảnh
    if (move_uploaded_file($_FILES['image']['tmp_name'], $targetFile)) {
        // Tại đây bạn có thể thực hiện xử lý ảnh (ví dụ: gọi model AI hoặc tìm kiếm sản phẩm tương tự)

        // Hiển thị kết quả
        echo "<h1>Kết quả tìm kiếm</h1>";
        echo "<img src='$targetFile' alt='Uploaded Image' style='max-width: 300px;'>";
        echo "<p>Đây là kết quả tương ứng cho hình ảnh của bạn:</p>";
        // Giả sử bạn có logic để tìm sản phẩm, thêm kết quả vào đây
        // Ví dụ: echo "<p>Sản phẩm tương tự: Sản phẩm A, Sản phẩm B...</p>";
    } else {
        echo "<h1>Không thể tải ảnh lên</h1>";
    }
} else {
    echo "<h1>Vui lòng tải ảnh lên</h1>";
}
?>