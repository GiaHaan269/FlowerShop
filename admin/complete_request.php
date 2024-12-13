<?php
include '../components/connect.php';

if (!isset($_GET['id'])) {
    header('location:manage_products_employee.php');
    exit();
}

$productId = $_GET['id'];

// Cập nhật trạng thái yêu cầu nhập hàng
$sql = "UPDATE nhap_hang SET yeu_cau_nhap = 0 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$productId]);

header('location:manage_products_employee.php?message=completed');
exit();
?>