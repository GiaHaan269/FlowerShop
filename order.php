<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    $user_id = 'login.php';
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
    <title>Đặt Hàng</title>
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <div class="orders">
        <div class="heading">
            <h1>Đơn hàng của tôi</h1>
            <img src="image/separator.png">
        </div>
        <div class="box-container">
            <?php
            $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE user_id = ?");
            $select_orders->execute([$user_id]);

            if ($select_orders->rowCount() > 0) {
                $displayed_order_ids = []; // Mảng lưu trữ các order_id đã hiển thị
                while ($fetch_orders = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                    $order_id = $fetch_orders['id']; // Lấy ID đơn hàng
            
                    // Kiểm tra nếu đơn hàng đã được hiển thị chưa
                    if (!in_array($order_id, $displayed_order_ids)) {
                        // Nếu chưa hiển thị, thêm vào mảng
                        $displayed_order_ids[] = $order_id;

                        $product_id = $fetch_orders['product_id'];
                        $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                        $select_products->execute([$product_id]);

                        if ($select_products->rowCount() > 0) {
                            while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                                ?>
                                <div class="box">
                                    <a href="view_order.php?get_id=<?= $fetch_orders['id']; ?>">
                                        <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                                        <div class="content">
                                            <p class="date"><i class="bx bxs-calender-alt"></i><span><?= $fetch_orders['date']; ?></span>
                                            </p>
                                            <div class="row">
                                                <h3 class="name"><?= $fetch_products['name']; ?></h3>
                                                <!-- <p class="price"><?= number_format($fetch_products['price'], 0, ',', '.') . ''; ?></p> -->
                                                <p class="status">
                                                    <?php
                                                    
                                                    if ($fetch_orders['status'] == 'canceled') {
                                                        // Hiển thị "đã hủy" với màu đỏ
                                                        echo '<span style="color: red;">đã hủy</span>';
                                                    } elseif ($fetch_orders['status'] == 'in progress') {
                                                        // Hiển thị "đang xử lý" với màu cam
                                                        echo '<span style="color: orange; font-weight: bold;">đang xử lý</span>';
                                                    } elseif ($fetch_orders['status'] == 'Đóng gói') {
                                                        // Hiển thị "đang đóng gói" với màu xanh
                                                        echo '<span style="color: orange; font-weight: bold;">đang đóng gói</span>';
                                                    } elseif ($fetch_orders['status'] == 'Giao hàng') {
                                                        // Hiển thị "đang giao hàng" với màu xanh lá cây
                                                        echo '<span style="color: orange; font-weight: bold;">đang giao hàng</span>';
                                                    }elseif ($fetch_orders['status'] == 'received') {
                                                        // Hiển thị "đang giao hàng" với màu xanh lá cây
                                                        echo '<span style="color: green; font-weight: bold;">đã nhận</span>';
                                                    }
                                                    ?>
                                                </p>


                                            </div>
                                        </div>
                                    </a>
                                    <!-- <a href="checkout.php?get_id=<?= $fetch_products['id']; ?>" class="buy-again-btn">Mua lại</a> -->
                                </div>
                                <?php
                            }
                        }
                    }
                }
            } else {
                echo '
                    <div class="empty">
                        <p>Không có đơn hàng</p>
                    </div>
                ';
            }
            ?>
        </div>
    </div>

    <?php include 'components/user_footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>


</body>

</html>