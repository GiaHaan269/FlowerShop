<?php
    include '../components/connect.php';

    if(isset($_COOKIE['seller_id'])){
        $seller_id = $_COOKIE['seller_id'];
    }else{
        $seller_id ='';
        header('location:login.php');
    }

    if(isset($_POST['delete'])){
        $delete_id = $_POST['delete_id'];
        $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT * FROM `message` WHERE id = ?");
        $verify_delete->execute([$delete_id]);

        if($verify_delete->rowCount() > 0){
            $delete_message = $conn->prepare("DELETE FROM `message` WHERE id = ?");
            $delete_message->execute([$delete_id]);

            $success_msg[] = 'tin nhắn đã xóa';
        }else{
            $warning_msg[] = 'tin nhắn chưa được xóa';
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
    <title>Quản trị - Tài khoản khách hàng</title>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <!-- <div class="banner">
        <div class="detail">
            <h1>Tài khoản khách hàng</h1>
            
            <span><a href="dashboard.php">Quản trị</a><i class="bx bx-right-arrow-alt"></i>Tài khoản khách hàng</span>
        </div>
    </div> -->
    <section class="user-container">
        <div class="heading">
            <h1>Khách Hàng Đăng Ký</h1>
            <img src="../image/separator.png">
        </div>
        <div class="box-container">
            <?php
                $select_users = $conn->prepare("SELECT * FROM `users`");
                $select_users->execute();

                if($select_users->rowCount() > 0){
                    while($fetch_users = $select_users->fetch(PDO::FETCH_ASSOC)){
                        $user_id = $fetch_users['id'];
            ?>
            <div class="box">
                <div class="detail">
                    <p>ID :<span> <?= $user_id; ?></span></p>
                    <p>Tên :<span> <?= $fetch_users['name']; ?></span></p>
                    <p>Email :<span> <?= $fetch_users['email']; ?></span></p>
                     <p>Điểm tích lũy :<span> <?= number_format($fetch_users['points'], 0, ',', '.'); ?> điểm</span></p>
                </div>
            </div>
            <?php
                    }
                }else{
                    echo'
                        <div class="empty" style="margin: 2rem auto;">
                            <p>Không có tài khoản</p>
                        </div>
                    ';
                }
            ?>
        </div>
        
    </section>
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>
