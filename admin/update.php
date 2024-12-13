<?php
    include '../components/connect.php';

    if(isset($_COOKIE['seller_id'])){
        $seller_id = $_COOKIE['seller_id'];
    }else{
        $seller_id ='';
        header('location:login.php');
    }
    
    if(isset($_POST['update'])){
        $select_seller = $conn->prepare("SELECT * FROM `sellers` WHERE id =? LIMIT 1");
        $select_seller->execute([$seller_id]);
        $fetch_seller = $select_seller->fetch(PDO::FETCH_ASSOC);

        $prev_pass = $fetch_seller['password'];

        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $email = $_POST['email'];
        $email = filter_var($email, FILTER_SANITIZE_STRING);

        if(!empty($name)){
            $update_name = $conn->prepare("UPDATE `sellers` SET name = ? WHERE id = ?");
            $update_name->execute([$name, $seller_id]);
            $success_msg[] = 'Ten duoc cap nhat thanh cong';
        }
        //update user email address

        if(!empty($email)){
            $select_email = $conn->prepare("UPDATE `sellers` SET email = ? WHERE id = ?");
            $select_email->execute([$seller_id, $email]);
        }else{
            $update_email = $conn->prepare("UPDATE `sellers` SET email = ? WHERE id = ?");
            $update_email->execute([$email, $seller_id]);
            $upadate_msg[] = 'email update successfully';
        }

        //update password
        $empty_pass = 'da39a3ee5e6b4b0d3255bfef95601890afd80709';
        $old_pass = sha1($_POST['old_pass']);
        $old_pass = filter_var($old_pass, FILTER_SANITIZE_STRING);

        $new_pass = sha1($_POST['new_pass']);
        $new_pass = filter_var($new_pass, FILTER_SANITIZE_STRING);

        $cpass = sha1($_POST['cpass']);
        $cpass = filter_var($cpass, FILTER_SANITIZE_STRING);

        if($old_pass != $empty_pass){
            if($old_pass != $prev_pass){
                $warning_msg[] = 'Mật khẩu cũ không trùng khớp';
            }elseif ($new_pass != $cpass){
                $warning_msg[] = 'Mật khẩu không trùng khớp';
            }else{
                if($new_pass != $empty_pass){
                    $update_pass = $conn->prepare("UPDATE `sellers` SET password =? WHERE id =?");
                    $update_pass->execute([$cpass, $seller_id]);
                    $success_msg[] = 'Mật khẩu được cập nhật thành công';
                }else{
                    $warning_msg[] = 'Vui lòng nhập mật khẩu';
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
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <title>Cập nhật hồ sơ</title>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <!-- <div class="banner">
        <div class="detail">
            <h1>cập nhật hồ sơ</h1>
            
            <span><a href="dashboard.php">admin</a><i class="bx bx-right-arrow-alt"></i>hồ sơ</span>
        </div>
    </div> -->
    
    <section class="form-container">
        <form action="" method="post" enctype="multipart/form-data" class="register">
            <h3>cập nhật hồ sơ</h3>
            <div class="flex">
                <div class="col">
                    <div class="input-field">
                        <p>tên của bạn</p>
                        <input type="text" name="name" placeholder="<?= $fetch_profile['name']; ?>"
                        class="box">
                    </div>
                    <div class="input-field">
                        <p>email của bạn</p>
                        <input type="email" name="email" placeholder="<?= $fetch_profile['email']; ?>"
                        class="box">
                    </div>
                    
                </div>
                <div class="col">
                    <div class="input-field">
                        <p>mật khẩu cũ</p>
                        <input type="password" name="old_pass" placeholder="nhập mật khẩu cũ"
                        class="box">
                    </div>
                    <div class="input-field">
                        <p>mật khẩu mới</p>
                        <input type="password" name="new_pass" placeholder="mật khẩu mới"
                        class="box">
                    </div>
                    <div class="input-field">
                        <p>xác nhận mật khẩu</p>
                        <input type="password" name="cpass" placeholder="xác nhận mật khẩu"
                        class="box">
                    </div>
                </div>
            </div>
            <input type="submit" name="update" class="btn" value="cập nhật hồ sơ">
        </form>
        
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>
