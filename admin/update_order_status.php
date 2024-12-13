<?php
include '../components/connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy thông tin từ form
    $order_id = $_POST['order_id'];
    $new_status = $_POST['order_status']; // Kiểm tra giá trị này có được gửi không

    // Debug để kiểm tra giá trị nhận được
    error_log("Order ID: " . $order_id);
    error_log("New Status: " . $new_status);

    // Cập nhật trạng thái đơn hàng
    $update_query = "UPDATE orders SET status = :status WHERE id = :order_id";
    $stmt = $conn->prepare($update_query);
    $stmt->bindParam(':status', $new_status);
    $stmt->bindParam(':order_id', $order_id);

    if ($stmt->execute()) {
        // Cập nhật thành công
        header("Location: order_detail.php?id=" . $order_id);
        exit();
    } else {
        // Thông báo lỗi nếu không thành công
        echo "Có lỗi xảy ra khi cập nhật trạng thái đơn hàng.";
    }
}

// Lấy order_id từ form
$order_id = isset($_POST['order_id']) ? $_POST['order_id'] : '';
$order_status = isset($_POST['order_status']) ? $_POST['order_status'] : '';

// Lấy thông tin đơn hàng
$query = "SELECT status FROM orders WHERE id = :order_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_STR);
$stmt->execute();
$orderInfo = $stmt->fetch(PDO::FETCH_ASSOC);

// Kiểm tra xem trạng thái có phải là "Đã hoàn tất" không
if ($orderInfo['status'] === 'completed') {
    echo 'Không thể cập nhật trạng thái vì đơn hàng đã hoàn tất.';
    exit; // Dừng quá trình cập nhật
}

// Cập nhật trạng thái đơn hàng
$query = "UPDATE orders SET status = :order_status WHERE id = :order_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':order_status', $order_status, PDO::PARAM_STR);
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_STR);
$stmt->execute();

echo 'Cập nhật trạng thái đơn hàng thành công.';



?>