<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = 'login.php';
}

$select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
$select_orders->execute([$user_id]);
$total_orders = $select_orders->rowCount();

$select_message = $conn->prepare("SELECT * FROM `message` WHERE user_id = ?");
$select_message->execute([$user_id]);
$total_message = $select_message->rowCount();

// Truy vấn số điểm tích lũy của người dùng
// $select_user_points = $conn->prepare("SELECT points FROM `users` WHERE id = ?");
// $select_user_points->execute([$user_id]);
// $user_data = $select_user_points->fetch(PDO::FETCH_ASSOC);
// $user_points = $user_data['points']; // Số điểm tích lũy của người dùng

// Truy vấn đơn hàng và tin nhắn của người dùng
$select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
$select_orders->execute([$user_id]);
$total_orders = $select_orders->rowCount();

$select_message = $conn->prepare("SELECT * FROM `message` WHERE user_id = ?");
$select_message->execute([$user_id]);
$total_message = $select_message->rowCount();
?>




<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <title>Hồ Sơ</title>
</head>

<body>
    <?php include 'components/user_header.php'; ?>



    <div class="banner">
        <div class="detail">
            <h1>Hồ Sơ</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <span><a href="home.php">home</a><i class="bx bx-right-arrow-alt"></i>Hồ sơ</span>
        </div>
    </div>
    <section class="profile">
        <div class="heading">
            <h1>Hồ Sơ</h1>
            <img src="image/separator.png">
        </div>
        <div class="details">
            <div class="user">
                <h3><?= $fetch_profile['name']; ?></h3>
                <p>Khách Hàng</p>
                <a href="update.php" class="btn">Cập nhật hồ sơ</a>
            </div>
            <div class="box-container">
                <div class="box">
                    <div class="flex">
                        <i class="bx bxs-food-menu"></i>
                        <h3><?= $total_orders; ?></h3>
                    </div>
                    <a href="order.php" class="btn">Đặt hàng</a>
                </div>
                <div class="box">
                    <div class="flex">
                        <i class="bx bxs-chat"></i>
                        <h3><?= $total_message; ?></h3>
                    </div>
                    <a href="contact.php" class="btn">Gửi tin nhắn</a>
                </div>
                <!-- Hiển thị số điểm tích lũy -->
                <!-- <div class="box">
                    <div class="flex">
                        <i class="bx bx-gift"></i>
                        <h3>Điểm tích lũy của bạn: <?= $user_points; ?> điểm</h3>
                    </div>
                    
                </div> -->
            </div>
        </div>
    </section>

    <?php include 'components/user_footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>

</body>

</html>