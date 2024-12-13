<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'F:/xampp/htdocs/flowershop/mail/PHPMailer/src/Exception.php';
require 'F:/xampp/htdocs/flowershop/mail/PHPMailer/src/PHPMailer.php';
require 'F:/xampp/htdocs/flowershop/mail/PHPMailer/src/SMTP.php';

function sendOrderConfirmation($customerEmail, $orderDetails)
{
    $mail = new PHPMailer(true);

    try {
        // Thiết lập SMTP
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com'; // Địa chỉ SMTP của nhà cung cấp dịch vụ email
        $mail->SMTPAuth = true;
        $mail->Username = 'vanilashop65@gmail.com'; // Email của bạn
        $mail->Password = 'wgtu zyaf fgfm gkvx';    // Mật khẩu email của bạn
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587; // Cổng SMTP

        // Thông tin người gửi
        $mail->setFrom('vanilashop65@gmail.com', 'Vanila Shop');
        $mail->addAddress($customerEmail); // Địa chỉ email của khách hàng

        // Cấu hình nội dung email
        $mail->isHTML(true);
        $mail->Subject = 'Xác nhận đơn hàng #' . $orderDetails['order_id'];
        $mail->Body = "<h2>Cảm ơn bạn đã mua hàng tại Your Shop Name!</h2>
                          <p><strong>Mã đơn hàng:</strong> {$orderDetails['order_id']}</p>
                          <p><strong>Ngày đặt hàng:</strong> {$orderDetails['order_date']}</p>
                          <p><strong>Sản phẩm:</strong> {$orderDetails['product_list']}</p>
                          <p><strong>Tổng cộng:</strong> {$orderDetails['total']} VNĐ</p>
                          <p>Đơn hàng sẽ được giao đến địa chỉ của bạn trong thời gian sớm nhất.</p>
                          <p>Cảm ơn bạn đã tin tưởng và mua hàng!</p>";

        $mail->AltBody = "Cảm ơn bạn đã mua hàng tại Your Shop Name! Mã đơn hàng: {$orderDetails['order_id']}.";

        // Gửi email
        $mail->send();
        echo 'Email xác nhận đã được gửi!';
    } catch (Exception $e) {
        echo "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
    }
}

// Kiểm tra nếu người dùng đã đăng nhập
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];

    // Kết nối cơ sở dữ liệu
    include 'components/connect.php';

    // Lấy thông tin người dùng từ bảng users
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE `id` = ?");
    $select_user->execute([$user_id]);

    if ($select_user->rowCount() > 0) {
        // Lấy thông tin email của người dùng
        $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);
        $customerEmail = $fetch_user['email']; // Lấy email người dùng

        // Ví dụ về thông tin đơn hàng (cần thay thế bằng dữ liệu thực tế từ hệ thống của bạn)
        $orderDetails = [
            'order_id' => '12345',
            'order_date' => '2024-10-29',
            'product_list' => 'Sản phẩm A, Sản phẩm B, Sản phẩm C',
            'total' => '500,000'
        ];

        // Gọi hàm gửi email xác nhận
        sendOrderConfirmation($customerEmail, $orderDetails);
    } else {
        echo 'Không tìm thấy người dùng với ID này.';
    }
} else {
    echo 'Vui lòng đăng nhập để đặt hàng.';
}
?>