<?php
include 'components/connect.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'F:/xampp/htdocs/flowershop/mail/PHPMailer/src/Exception.php';
require 'F:/xampp/htdocs/flowershop/mail/PHPMailer/src/PHPMailer.php';
require 'F:/xampp/htdocs/flowershop/mail/PHPMailer/src/SMTP.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

$email = $_GET['email'] ?? '';

if (isset($_POST['verify'])) {
    $token = $_POST['token'] ?? ''; // Lấy mã xác nhận từ người dùng

    if (empty($token)) {
        $warning_msg[] = 'Vui lòng nhập mã xác nhận';
    } else {
        // Kiểm tra mã xác nhận trong cơ sở dữ liệu
        $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ? AND token = ?");
        $select_user->execute([$email, $token]);

        if ($select_user->rowCount() > 0) {
            // Cập nhật trạng thái tài khoản là đã xác nhận
            $update_user = $conn->prepare("UPDATE `users` SET is_verified = ? WHERE email = ?");
            $update_user->execute(['1', $email]);
            header('Location: login.php');
            exit;
        } else {
            $warning_msg[] = 'Mã xác nhận không đúng! Vui lòng kiểm tra lại';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name = "viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <title>Đăng Ký</title>
</head>

<body>
    <?php include 'components/user_header.php' ?>
    <div class="form-container" style="margin-top: -12rem; margin-bottom: -10rem;">
        <form action="" method="post" class="verify">
            <h3>Xác nhận tài khoản</h3>
            <div class="input-field">
                <p style="font-size: 1.1rem;">Vui lòng nhập mã xác nhận vừa được gửi về email
                    <strong><?= htmlspecialchars($email); ?></strong> <span>*</span></p>
                <input type="text" name="token" placeholder="Nhập mã xác nhận" maxlength="6" required class="box">
            </div>
            <input type="submit" name="verify" value="Xác Nhận" class="btn">
        </form>
    </div>

    <?php include 'components/user_footer.php'; ?>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>
</body>

</html>