<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
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
    <title>Quản Trị - Trang Chủ</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Trang Chủ</h1>

            <span><a href="dashboard.php">Quản trị</a><i class="bx bx-right-arrow-alt"></i>trang chủ</span>
        </div>
    </div>
    <section class="dashboard">
        <div class="heading">
            <h1>Trang Chủ</h1>
            <img src="../image/separator.png" width="100">
        </div>
        <div class="box-container">
            <div class="box">
                <h3>Tài Khoản</h3>
                <p><?= $fetch_profile['name']; ?></p>
                <a href="update.php" class="btn">cập nhật hồ sơ</a>
            </div>
            <div class="box">
                <?php
                $select_message = $conn->prepare("SELECT * FROM `message`");
                $select_message->execute();
                $number_of_msg = $select_message->rowCount();
                ?>

                <h3>Tin nhắn</h3>
                <p>tin nhắn chưa đọc</p>
                <a href="admin_message.php" class="btn">xem tin nhắn</a>
            </div>
            <div class="box">
                <?php
                $select_product = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ?");
                $select_product->execute([$seller_id]);
                $num_of_product = $select_product->rowCount();
                ?>
                <h3>Sản phẩm</h3>
                <p>sản phẩm đã thêm</p>
                <a href="add_product.php" class="btn">thêm sản phẩm mới</a>

            </div>
            <div class="box">
                <?php
                $select_active_product = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ? AND status=?");
                $select_active_product->execute([$seller_id, 'active']);
                $num_of_active_product = $select_active_product->rowCount();
                ?>
                <h3>Sản phẩm</h3>
                <p>sản phẩm đang có</p>
                <a href="view_products.php" class="btn">xem sản phẩm đang có</a>

            </div>
            <div class="box">
                <?php
                $select_deactive_product = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ? AND status=?");
                $select_deactive_product->execute([$seller_id, 'deactive']);
                $num_of_deactive_product = $select_deactive_product->rowCount();
                ?>
                <h3>Sản phẩm</h3>
                <p>sản phẩm hết hàng</p>
                <a href="view_product.php" class="btn">xem sản phẩm hết hàng</a>

            </div>
            <div class="box">
                <?php
                $select_users = $conn->prepare("SELECT * FROM `users` ");
                $select_users->execute();
                $num_of_users = $select_users->rowCount();
                ?>
                <h3>Khách hàng</h3>
                <p>khách hàng đăng ký</p>
                <a href="user_account.php" class="btn">xem số khách hàng </a>

            </div>
            <div class="box">
                <?php
                $select_sellers = $conn->prepare("SELECT * FROM `sellers` ");
                $select_sellers->execute();
                $num_of_sellers = $select_sellers->rowCount();
                ?>
                <h3>Tài khoản</h3>
                <p>tài khoản quản trị </p>
                <a href="profile.php" class="btn">xem tài khoản</a>
            </div>
            <!-- <div class="box">
                <?php
                $select_canceled_orders = $conn->prepare("SELECT * FROM `orders` WHERE seller_id = ? AND status=?");
                $select_canceled_orders->execute([$seller_id, 'canceled']);
                $total_canceled_orders = $select_canceled_orders->rowCount();
                ?>
                <h3><?= $total_canceled_orders ?></h3>
                <p>đơn hàng đã hủy</p>
                <a href="admin_order.php" class="btn">đơn hàng đã hủy</a>

            </div> -->
            <div class="box">
                <?php
                $select_confirm_orders = $conn->prepare("SELECT * FROM `orders` WHERE seller_id = ? AND status=?");
                $select_confirm_orders->execute([$seller_id, 'in progress']);
                $total_confirm_orders = $select_confirm_orders->rowCount();
                ?>
                <h3>Hàng hóa</h3>
                <p>Nhập hàng</p>
                <a href="add_warehouse.php" class="btn">Nhập hàng</a>
            </div>
            <div class="box">
                <?php
                $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE seller_id = ? ");
                $select_orders->execute([$seller_id]);
                $total_orders = $select_orders->rowCount();
                ?>
                <h3>Đơn hàng</h3>
                <p>tổng đơn đặt hàng</p>
                <a href="admin_orderss.php" class="btn">tổng đơn đặt hàng</a>
            </div>
            <div class="box">
                <?php
                $select_reviews = $conn->prepare("SELECT * FROM product_reviews WHERE seller_id = ?");
                $select_reviews->execute([$seller_id]);
                $num_of_reviews = $select_reviews->rowCount();
                ?>
                <h3>Đánh giá</h3>
                <p>đánh giá sản phẩm</p>
                <a href="review.php" class="btn">Xem đánh giá</a>
            </div>
            <div class="box">
                <?php
                $select_order_reviews = $conn->prepare("SELECT * FROM order_reviews WHERE seller_id = ?");
                $select_order_reviews->execute([$seller_id]);
                $num_of_order_reviews = $select_order_reviews->rowCount();
                ?>
                <h3>Đánh giá </h3>
                <p>đánh giá đơn hàng</p>
                <a href="order_reviews.php" class="btn">Xem đánh giá đơn hàng</a>
            </div>

            <div class="box">
                <?php
                // Lấy danh sách nhân viên (type_seller = 'employee')
                $select_employees = $conn->prepare("SELECT * FROM `sellers` WHERE type_seller = ?");
                $select_employees->execute(['employee']);
                $num_of_employees = $select_employees->rowCount();
                ?>
                <h3>Nhân viên</h3>
                <p>thông tin nhân viên</p>
                <a href="view_employees.php" class="btn">Xem nhân viên</a>
            </div>
            <div class="box">
                <h3>Doanh Thu</h3>
                <p>Doanh thu cửa hàng</p>
                <a href="revenue.php" class="btn">Xem Doanh Thu</a>
            </div>
        </div>
    </section>
    <?php include '../components/admin_footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>