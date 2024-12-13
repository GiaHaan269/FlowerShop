<?php
include '../components/connect.php';

if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $email = filter_var($email, FILTER_SANITIZE_STRING);

    $pass = sha1($_POST['pass']);
    $pass = filter_var($pass, FILTER_SANITIZE_STRING);

    $select_seller = $conn->prepare("SELECT * FROM `sellers` WHERE email=? AND password=? LIMIT 1");
    $select_seller->execute([$email, $pass]);
    $row = $select_seller->fetch(PDO::FETCH_ASSOC);

    if ($select_seller->rowCount() > 0) {
        // Kiểm tra loại tài khoản
        if ($row['type_seller'] === 'admin') {
            // Đăng nhập thành công với quyền admin
            setcookie('seller_id', $row['id'], time() + 60 * 60 * 24 * 30, '/');
            header('location:dashboard.php'); // Admin vào trang dashboard
        } elseif ($row['type_seller'] === 'employee') {
            // Đăng nhập thành công với quyền nhân viên nhập hàng
            setcookie('seller_id', $row['id'], time() + 60 * 60 * 24 * 30, '/');
            header('location:employee_dashboard.php'); // Nhân viên nhập hàng vào trang giới hạn
        }
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
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <title>Quản Trị - Đăng Nhập</title>
</head>
<body>
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
                <input type="password" name="pass" placeholder="nhập mật khẩu" maxlength="50" required class="box">
            </div>
                    
            <p class="link">không có tài khoản ? <a href="register.php">đăng ký</a></p>
            <input type="submit" name="login" class="btn" value="đăng nhập">
            
        </from>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>