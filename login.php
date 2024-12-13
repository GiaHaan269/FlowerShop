<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);

    // Không mã hóa mật khẩu
    $pass = $_POST['pass'];
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $select_user = $conn->prepare("SELECT * FROM `users` WHERE email=? AND password=? LIMIT 1");
    $select_user->execute([$email, $pass]);
    $row = $select_user->fetch(PDO::FETCH_ASSOC);

    if ($select_user->rowCount() > 0) {
        // Đăng nhập thành công
        setcookie('user_id', $row['id'], time() + 60 * 60 * 24 * 30, '/');
        header('location:home.php');
    } else {
        $warning_msg[] = 'Email hoặc mật khẩu không chính xác!';
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
    <title>Đăng nhập</title>
</head>
<body>
    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Đăng nhập</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <span><a href="home.php">home</a><i class="bx bx-right-arrow-alt"></i>Đăng nhập</span>
        </div>
    </div>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="login">
            <h3>đăng nhập</h3>
            <div class ="input-field">
                <p>email <span>*</span></p>
                <input type="email" name="email" placeholder="nhập email" maxlength="50"
                required class="box">
            </div>
                    
                
                
            <div class="input-field">
                <p>mật khẩu<span>*</span></p>
                    <div class="password-container">
                        <input type="password" name="pass" placeholder="nhập mật khẩu của bạn" maxlength="50" required class="box" id="password">
                        <i class="bx bx-show" id="togglePassword" style="cursor: pointer;"></i> <!-- Biểu tượng con mắt -->
                    </div>
                </div>
                    
            <p class="link">không có tài khoản ? <a href="register.php">đăng ký</a></p>
            <input type="submit" name="login" class="btn" value="đăng nhập">
            
        </from>
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