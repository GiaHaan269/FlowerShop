<?php
include '../components/connect.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Xóa sản phẩm từ bảng nhap_hang
    $sql_delete = "DELETE FROM nhap_hang WHERE id = :id";
    $stmt = $conn->prepare($sql_delete);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        header('Location: manage_products.php?message=deleted'); // Quay lại trang quản lý sau khi xóa
        exit();
    } else {
        echo "Có lỗi xảy ra khi xóa sản phẩm.";
    }
} else {
    echo "ID sản phẩm không hợp lệ.";
}


?>