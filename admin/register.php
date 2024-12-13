<?php
    include '../components/connect.php';

    if(isset($_POST['register'])){
        $id = unique_id();
        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $email = $_POST['email'];
        $email = filter_var($email, FILTER_SANITIZE_STRING);

        $pass = sha1($_POST['pass']);
        $pass = filter_var($pass, FILTER_SANITIZE_STRING);

        $cpass = sha1($_POST['cpass']);
        $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

        $select_seller = $conn->prepare("SELECT * FROM `sellers` WHERE email=?");
        $select_seller->execute([$email]);

        // Lấy giá trị type_seller từ form
        $type_seller = $_POST['type_seller'];
        $type_seller = filter_var($type_seller, FILTER_SANITIZE_STRING);

        $select_seller = $conn->prepare("SELECT * FROM `sellers` WHERE email=?");
        $select_seller->execute([$email]);

        if ($select_seller->rowCount() > 0){
            $warning_msg[] = 'Email already exists';
        }else{
            if ($pass != $cpass){
                $warning_msg[] = 'Confirm password does not match';
            }else{
                $insert_seller = $conn->prepare("INSERT INTO `sellers` (id, name, email, password, type_seller) VALUES (?, ?, ?, ?, ?)");
                $insert_seller->execute([$id, $name, $email, $cpass, $type_seller]);
                
                $success_msg[] = 'New seller registered! Please login now';
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
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <title>Quản Trị - Đăng Ký</title>
</head>
<body>
    <div class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="register">
    <h3>đăng ký</h3>
    <div class="flex">
        <div class="col">
            <div class="input-field">
                <p>tên  <span>*</span></p>
                <input type="text" name="name" placeholder="nhập tên của bạn" maxlength="50" required class="box">
            </div>
            <div class="input-field">
                <p>email <span>*</span></p>
                <input type="email" name="email" placeholder="nhập email của bạn" maxlength="50" required class="box">
            </div>
            <div class="input-field">
                <p>Loại tài khoản <span>*</span></p>
                <select name="type_seller" class="box" required>
                    <option value="admin">Quản Trị Viên</option>
                    <option value="employee">Nhân viên nhập hàng</option>
                </select>
            </div>
        </div>
        <div class="col">
            <div class="input-field">
                <p>mật khẩu <span>*</span></p>
                <input type="password" name="pass" placeholder="nhập mật khẩu của bạn" maxlength="50" required class="box">
            </div>
            <div class="input-field">
                <p>xác nhận mật khẩu <span>*</span></p>
                <input type="password" name="cpass" placeholder="nhập mật khẩu của bạn" maxlength="50" required class="box">
            </div>
        </div>
    </div>
    
    <p class="link">bạn đã có tài khoản  <a href="login.php"> Đăng nhập</a></p>
    <input type="submit" name="register" class="btn" value="đăng ký">
    
</form>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>