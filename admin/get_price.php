<?php
include '../components/connect.php';

if (isset($_GET['title'])) {
    $title = filter_var($_GET['title'], FILTER_SANITIZE_STRING);

    // Truy vấn giá bán từ bảng nhap_hang
    $query = "SELECT gia_ban FROM nhap_hang WHERE ten_hang = :ten_hang LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':ten_hang', $title, PDO::PARAM_STR);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'price' => $row['gia_ban']]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy giá bán cho sản phẩm này']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Tên sản phẩm không được cung cấp']);
}
?>