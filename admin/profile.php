<?php
    include '../components/connect.php';

    if(isset($_COOKIE['seller_id'])){
        $seller_id = $_COOKIE['seller_id'];
    }else{
        $seller_id ='';
        header('location:login.php');
    }
    $select_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id=?");
    $select_products->execute([$seller_id]);
    $total_products = $select_products->rowCount();

    $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE seller_id=?");
    $select_orders->execute([$seller_id]);
    $total_orders = $select_orders->rowCount();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name = "viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <title> Quản Trị - Hồ Sơ</title>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <!-- <div class="banner">
        <div class="detail">
            <h1>Hồ sơ</h1>
            
            <span><a href="dashboard.php">Quản Trị</a><i class="bx bx-right-arrow-alt"></i>hồ sơ</span>
        </div>
    </div> -->
    <section class="seller_profile">
        <div class="heading">
            <h1>hồ sơ</h1>
            <img src="../image/separator.png" width="100">
        </div>
        <div class="detail">
            <div class="seller">
                <h3><?= $fetch_profile['name'];?></h3>
                <span>người bán</span>
                <a href="update.php" class="btn">cập nhật hồ sơ</a>
            </div>
            <div class="flex">
                <div class="box">
                    <span><?= $total_products; ?></span>
                    <p>tổng sản phẩm</p>
                    <a href="view_products.php" class="btn">xem sản phẩm</a>
                </div>
                <div class="box">
                    <span><?= $total_orders; ?></span>
                    <p>số đơn đặt hàng</p>
                    <a href="admin_order.php" class="btn">xem đơn đặt hàng</a>
                </div>
        </div>
    </section>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>
