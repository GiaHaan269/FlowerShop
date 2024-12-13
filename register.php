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

if (isset($_POST['register'])) {
    $id = unique_id();
    $name = $_POST['name'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);

    $pass = $_POST['pass'];
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $cpass = $_POST['cpass'];
    $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

    $phone = $_POST['phone'];
    $phone = filter_var($phone, FILTER_SANITIZE_STRING);

    // Kiểm tra email đã tồn tại
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email = ?");
    $select_user->execute([$email]);

    if ($select_user->rowCount() > 0) {
        $warning_msg[] = 'Email đã tồn tại!';
    } else {
        if ($pass != $cpass) {
            $warning_msg[] = 'Mật khẩu xác nhận không khớp';
        } elseif (!preg_match('/^(?=.*[A-Z])(?=.*[0-9])(?=.{8,})/', $pass)) {
            $warning_msg[] = 'Mật khẩu phải có ít nhất 8 ký tự, bao gồm số và ký tự viết hoa.';
        } else {
            // Tạo mã token ngẫu nhiên cho xác thực
            $token = mt_rand(100000, 999999);

            $insert_user = $conn->prepare("INSERT INTO `users` (id, name, email, password, phone, token) VALUES (?, ?, ?, ?, ?, ?)");
            $insert_user->execute([$id, $name, $email, $cpass, $phone, $token]);

            // Gửi email xác nhận
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com';
                $mail->SMTPAuth = true;
                $mail->Username = 'vanilashop65@gmail.com';
                $mail->Password = 'wgtu zyaf fgfm gkvx';
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('vanilashop65@gmail.com', 'Vanila Shop');
                $mail->addAddress($email, htmlspecialchars($name));
                $mail->isHTML(true);
                $mail->CharSet = 'UTF-8';
                $mail->Subject = 'Xác nhận đăng ký tài khoản';
                $mail->Body = '<p>Chào ' . htmlspecialchars($name) . ', cảm ơn bạn đã đăng ký tài khoản. Mã xác nhận của bạn là: <strong>' . $token . '</strong></p>';

                $mail->send();
                header("Location: verified.php?email=" . urlencode($email));
                exit;
            } catch (Exception $e) {
                $warning_msg[] = 'Không thể gửi email: ' . $mail->ErrorInfo;
            }
        }
    }
}

?>




<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name = "viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <title>Đăng Ký</title>
</head>
<body>
    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Đăng Ký</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <span><a href="home.php">home</a><i class="bx bx-right-arrow-alt"></i>Đăng Ký</span>
        </div>
    </div>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <h3>đăng ký</h3>
            <div class="flex">
                <div class ="col">
                    <div class ="input-field">
                        <p>tên  <span>*</span></p>
                        <input type="text" name="name" placeholder="nhập tên của bạn" maxlength="50"
                        required class="box">
                    </div>
                    <div class="input-field">
                        <p>email <span>*</span></p>
                        <input type="email" name="email"placeholder="nhập email của bạn"maxlength="50"
                        required class="box">
                    </div>
                    <div class="input-field">
                        <p>số điện thoại <span>*</span></p>
                        <input type="tel" name="phone" placeholder="nhập số điện thoại của bạn" maxlength="10" required class="box">
                    </div>
                </div>
                <div class="col">
                    <div class="input-field">
                        <p>mật khẩu<span>*</span></p>
                        <div class="password-container">
                            <input type="password" name="pass" placeholder="nhập mật khẩu của bạn" maxlength="50" required class="box" id="password">
                            <i class="bx bx-show" id="togglePassword" style="cursor: pointer;"></i> <!-- Biểu tượng con mắt -->
                        </div>
                    </div>
                    <div class="input-field">
                        <p>xác nhận mật khẩu<span>*</span></p>
                        <div class="password-container">
                            <input type="password" name="cpass" placeholder="nhập mật khẩu của bạn" maxlength="50" required class="box" id="confirm_password">
                            <i class="bx bx-show" id="toggleConfirmPassword" style="cursor: pointer;"></i> <!-- Biểu tượng con mắt -->
                        </div>
                    </div>
                </div>
            </div>
            
            <p class="link">bạn đã có tài khoản.  <a href="login.php"> Đăng nhập</a></p>
            <input type="submit" name="register" class="btn" value="đăng ký">
            
        </form>
    </div>
    
    <?php include 'components/user_footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>
    <script>
        // Lấy các phần tử mật khẩu và biểu tượng con mắt
        const togglePassword = document.querySelector('#togglePassword');
        const password = document.querySelector('#password');

        const toggleConfirmPassword = document.querySelector('#toggleConfirmPassword');
        const confirmPassword = document.querySelector('#confirm_password');

        // Xử lý sự kiện click cho mật khẩu
        togglePassword.addEventListener('click', function () {
            const type = password.getAttribute('type') === 'password' ? 'text' : 'password';
            password.setAttribute('type', type);
            this.classList.toggle('bx-show');
            this.classList.toggle('bx-hide');
        });

        // Xử lý sự kiện click cho xác nhận mật khẩu
        toggleConfirmPassword.addEventListener('click', function () {
            const type = confirmPassword.getAttribute('type') === 'password' ? 'text' : 'password';
            confirmPassword.setAttribute('type', type);
            this.classList.toggle('bx-show');
            this.classList.toggle('bx-hide');
        });
    </script>
    
</body>
    
</html>