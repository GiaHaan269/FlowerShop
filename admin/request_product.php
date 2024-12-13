<?php
include '../components/connect.php';

if (!isset($_GET['id'])) {
    header('location:manage_products.php');
    exit();
}

$productId = $_GET['id'];

$sql = "UPDATE nhap_hang SET yeu_cau_nhap = 1 WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->execute([$productId]);

header('location:manage_products.php?message=requested');
exit();
?>