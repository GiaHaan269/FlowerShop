<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = 'login.php';
}

if (isset($_POST['update'])) {
    $select_user = $conn->prepare("SELECT * FROM `users` WHERE id = ? LIMIT 1");
    $select_user->execute([$user_id]);
    $fetch_user = $select_user->fetch(PDO::FETCH_ASSOC);

    $prev_pass = $fetch_user['password'];

    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);

    if (!empty($name)) {
        $update_name = $conn->prepare("UPDATE `users` SET name = ? WHERE id = ?");
        $update_name->execute([$name, $user_id]);
        $success_msg[] = 'Tên đã được cập nhật thành công.';
    }

    if (!empty($email)) {
        $update_email = $conn->prepare("UPDATE `users` SET email = ? WHERE id = ?");
        $update_email->execute([$email, $user_id]);
        $success_msg[] = 'Email đã được cập nhật thành công.';
    }

    $old_pass = $_POST['old_pass'];
    $new_pass = $_POST['new_pass'];
    $cpass = $_POST['cpass'];

    if (!empty($old_pass)) {
        if ($old_pass != $prev_pass) {
            $warning_msg[] = 'Mật khẩu cũ không chính xác.';
        } elseif ($new_pass != $cpass) {
            $warning_msg[] = 'Xác nhận mật khẩu không khớp.';
        } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d).{8,}$/', $new_pass)) {
            $warning_msg[] = 'Mật khẩu mới phải có ít nhất 8 ký tự, bao gồm số, chữ hoa, và chữ thường.';
        } else {
            $update_pass = $conn->prepare("UPDATE `users` SET password = ? WHERE id = ?");
            $update_pass->execute([$new_pass, $user_id]);
            $success_msg[] = 'Mật khẩu đã được cập nhật thành công.';
        }
    } else {
        $warning_msg[] = 'Vui lòng nhập mật khẩu cũ.';
    }
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <title>Cập nhật hồ sơ</title>
</head>

<body>
    <?php include 'components/user_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Cập nhật hồ sơ</h1>

            <span><a href="home.php">Trang chủ</a><i class="bx bx-right-arrow-alt"></i>Hồ sơ</span>
        </div>
    </div>

    <section class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <h3>Cập nhật hồ sơ</h3>
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>tên của bạn</p>
                        <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>" class="box">
                    </div>
                    <div class="input-field">
                        <p>email của bạn</p>
                        <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>" class="box">
                    </div>

                </div>
                <div class="col">
                <div class="input-field">
                    <p>Mật khẩu cũ</p>
                    <div class="password-container">
                        <input type="password" id="old_pass" name="old_pass" placeholder="Nhập mật khẩu cũ" class="box">
                        <i class="bx bx-show toggle-password" toggle="#old_pass"></i>
                    </div>
                </div>

                <div class="input-field">
                    <p>Mật khẩu mới</p>
                    <div class="password-container">
                        <input type="password" id="new_pass" name="new_pass" placeholder="Mật khẩu mới" class="box">
                        <i class="bx bx-show toggle-password" toggle="#new_pass"></i>
                    </div>
                </div>

                <div class="input-field">
                    <p>Xác nhận mật khẩu</p>
                    <div class="password-container">
                        <input type="password" id="cpass" name="cpass" placeholder="Xác nhận mật khẩu" class="box">
                        <i class="bx bx-show toggle-password" toggle="#cpass"></i>
                    </div>
                </div>
                </div>
            </div>
            <input type="submit" name="update" class="btn" value="cập nhật hồ sơ">
        </form>

    </section>
    <?php include 'components/user_footer.php'; ?>
    <script>document.querySelectorAll('.toggle-password').forEach(item => {
    item.addEventListener('click', function () {
        const input = document.querySelector(this.getAttribute('toggle'));
        if (input.type === 'password') {
            input.type = 'text';
            this.classList.remove('bx-show');
            this.classList.add('bx-hide');
        } else {
            input.type = 'password';
            this.classList.remove('bx-hide');
            this.classList.add('bx-show');
        }
    });
});
</script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>
</body>

</html>
<style>
    /* Đảm bảo phần cha bao quanh field được căn chỉnh */
.password-container {
    position: relative;
    width: 100%;
}

/* Input field chiếm toàn bộ chiều rộng */
.password-container input.box {
    width: 100%;
    padding-right: 40px; /* Dành không gian cho biểu tượng */
}

/* Vị trí của biểu tượng con mắt */
.password-container .toggle-password {
    position: absolute;
    top: 50%;
    right: 10px; /* Khoảng cách từ cạnh phải */
    transform: translateY(-50%);
    cursor: pointer;
    color: #888;
    font-size: 20px;
}

/* Hover để thay đổi màu sắc */
.password-container .toggle-password:hover {
    color: #333;
}

</style>