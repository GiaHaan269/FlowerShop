<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
}
$select_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id=?");
$select_products->execute([$seller_id]);
$total_products = $select_products->rowCount();

$select_orders = $conn->prepare("SELECT * FROM `orders` WHERE seller_id=?");
$select_orders->execute([$seller_id]);
$total_orders = $select_orders->rowCount();

// Truy vấn để lấy thông tin từ bảng seller
$select_seller = $conn->prepare("SELECT `name`, `email`, `type_seller` FROM `sellers` WHERE `id` = ?");
$select_seller->execute([$seller_id]);

// Kiểm tra nếu có kết quả
if ($select_seller->rowCount() > 0) {
    $fetch_profile = $select_seller->fetch(PDO::FETCH_ASSOC);
} else {
    echo "Không tìm thấy hồ sơ người bán.";
    exit();
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <title> Quản Trị - Hồ Sơ</title>
    <style>
        /* CSS cho email và chức vụ */
        .seller_profile p {
            font-size: 1.5rem; /* Kích thước mặc định */
            color: #333; /* Màu chữ mặc định */
        }

        /* CSS cho email */
        .seller_profile p.email {
            font-size: 18px; /* Tăng kích thước chữ cho email */
            font-weight: bold; /* Làm chữ đậm */
        }

        /* CSS cho chức vụ */
        .seller_profile p.type_seller {
            font-size: 18px; /* Tăng kích thước chữ cho chức vụ */
            font-weight: bold; /* Làm chữ đậm */
        }
    </style>
</head>

<body>
    <?php include '../components/employee_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Trang Nhân Viên</h1>
            <span><a href="employee_dashboard.php">Nhân viên</a> <i class="bx bx-right-arrow-alt"></i> Trang Chủ</span>
        </div>
    </div>
    <section class="seller_profile">
        <div class="heading">
            <h1>hồ sơ</h1>
            <img src="../image/separator.png" width="100">
        </div>
        <div class="detail">
            <div class="seller">
                <h3><?= $fetch_profile['name']; ?></h3>
                
                <p>Email: <?= $fetch_profile['email']; ?></p>
                <!-- Kiểm tra và hiển thị chức vụ -->
                <p>Chức vụ: 
                    <?php
                    if ($fetch_profile['type_seller'] == 'employee') {
                        echo 'Nhân viên';
                    } else {
                        echo $fetch_profile['type_seller'];
                    }
                    ?>
                </p>
                
                <a href="update_nv.php" class="btn">cập nhật hồ sơ</a>
            </div>
            <!-- <div class="flex">
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
            </div> -->
    </section>
    <?php include '../components/admin_footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>