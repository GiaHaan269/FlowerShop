<?php
include 'components/connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require '../vendor/autoload.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:login.php');
}

// Kiểm tra xem có bill_id trong URL không
if (isset($_GET['id'])) {
    $bill_id = $_GET['id'];

    // Truy vấn thông tin hóa đơn
    $select_bill = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
    $select_bill->execute([$bill_id]);

    if ($select_bill->rowCount() > 0) {
        $bill_info = $select_bill->fetch(PDO::FETCH_ASSOC);
        
        $update_bill = $conn->prepare("UPDATE orders SET hienthi = 1 WHERE id = ?");
        $update_bill->execute([$bill_id]);

        $mail = new PHPMailer(true);
        try {
            // Cấu hình SMTP
            $mail->isSMTP();
            $mail->Host = 'smtp.gmail.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'vanilashop65@gmail.com'; // Email gửi đi
            $mail->Password = 'wgtu zyaf fgfm gkvx'; // Mật khẩu email
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            // Thiết lập người gửi và người nhận
            $mail->setFrom('vanilashop65@gmail.com', 'Vanila Shop');
            $mail->addAddress($email); // Địa chỉ email của khách hàng

            // Cấu hình nội dung email
            $mail->isHTML(true);
            $mail->CharSet = 'UTF-8';
            $mail->Subject = 'Xác nhận đơn hàng #' . $id;


            $mail->Body .= "<p>Cảm ơn bạn đã đặt hàng tại VanilaShop.</p>";
            $mail->Body .= '<p>Đơn hàng <strong>#' . htmlspecialchars($id) . ' </strong> của bạn đã được đặt thành công vào ngày ' . date('d/m/Y') . '.</p>';


            $mail->Body .= "<li><strong>Tổng tiền hàng:</strong> " . number_format($order_total) . "VNĐ</li>";
            $mail->Body .= "<li><strong>Giảm giá:</strong> " . number_format($discount) . "VNĐ</li>";
            $mail->Body .= "<li><strong>Phí vận chuyển:</strong> " . number_format($shipping_fee) . "VNĐ</li>";
            $mail->Body .= '<li><strong>Địa chỉ giao hàng:</strong> ' . htmlspecialchars($address) . '</li>';
            $mail->Body .= "<p>Đơn hàng sẽ được giao đến địa chỉ của bạn trong thời gian sớm nhất.</p>";
            $mail->Body .= "<p>Cảm ơn bạn đã tin tưởng và mua hàng!</p>";

            $mail->send();
            echo 'Email xác nhận đã được gửi!';
        } catch (Exception $e) {
            echo "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
        }
        header('Location: order.php');
        exit();
    } else {
        echo "Không tìm thấy hóa đơn.";
    }
} else {
    echo "Không có bill_id trong URL.";
}
?>