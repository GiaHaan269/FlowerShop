<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
}

if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $order_id = filter_var($order_id, FILTER_SANITIZE_STRING);

    $update_payment = $_POST['update_payment'];
    $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);

    $update_pay = $conn->prepare("UPDATE `orders` SET payment_status =? WHERE id =?");
    $update_pay->execute([$update_payment, $order_id]);

    $success_msg[] = 'Trạng thái đơn hàng đã được cập nhật';
}

if (isset($_POST['delete_order'])) {
    $delete_id = $_POST['order_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_delete = $conn->prepare("SELECT * FROM `orders` WHERE id =?");
    $verify_delete->execute([$delete_id]);

    if ($verify_delete->rowCount() > 0) {
        $delete_order = $conn->prepare("DELETE FROM  `orders` WHERE id =?");
        $delete_order->execute([$delete_id]);

        $success_msg[] = 'Đã xóa đơn hàng';
    } else {
        $warning_msg[] = 'Đơn hàng đã được xóa';
    }
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
    <title>Đơn hàng</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Đơn Hàng</h1>

            <span><a href="dashboard.php">admin</a><i class="bx bx-right-arrow-alt"></i>Đơn hàng</span>
        </div>
    </div>
    <section class="order-container">
        <div class="heading">
            <h1>Tổng đơn hàng</h1>
            <img src="../image/separator.png">
        </div>
        <div class="box-container">
            <?php
            // Cập nhật truy vấn để nhóm các đơn hàng có cùng id và tính tổng số tiền
            $select_order = $conn->prepare("
                    SELECT 
                        id, user_id, name, date, number, email, method, address, status, 
                        SUM(price) AS total_price, 
                        payment_status 
                    FROM `orders` 
                    WHERE seller_id =? AND status = 'in progress'
                    GROUP BY id
                ");
            $select_order->execute([$seller_id]);

            if ($select_order->rowCount() > 0) {
                while ($fetch_order = $select_order->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="box">
                        <div class="status"
                            style="color: <?php if ($fetch_order['status'] == 'in progress') {
                                echo "limegreen";
                            } else {
                                echo "red";
                            } ?>">
                            <?= $fetch_order['status'] == 'in progress' ? 'đang xử lý' : ($fetch_order['status'] == 'canceled' ? 'đã hủy' : $fetch_order['status']); ?>
                        </div>


                        <div class="detail">
                            <!-- Hiển thị các thông tin chi tiết của đơn hàng -->
                            <p>Tên : <span><?= $fetch_order['name']; ?></span></p>
                            <p>ID : <span><?= $fetch_order['user_id']; ?></span></p>
                            <p>Ngày : <span><?= $fetch_order['date']; ?></span></p>
                            <p>Số điện thoại : <span><?= $fetch_order['number']; ?></span></p>
                            <p>Email : <span><?= $fetch_order['email']; ?></span></p>
                            <p>Tổng tiền: <span><?= $fetch_order['total_price'] ?></span></p>

                            <p>Phương thức thanh toán : <span><?= $fetch_order['method']; ?></span></p>
                            <p>Địa chỉ : <span><?= $fetch_order['address']; ?></span></p>
                        </div>
                        <form action="" method="post">
                            <input type="hidden" name="order_id" value="<?= $fetch_order['id']; ?>">
                            <select name="update_payment" class="box" style="width:90%;">
                                <option disabled selected><?= $fetch_order['payment_status']; ?></option>
                                <option value="chưa giải quyết">chưa giải quyết</option>
                                <option value="hoàn thành">hoàn thành</option>
                            </select>
                            <div class="flex-btn">
                                <button type="submit" name="update_order" class="btn">Cập nhật đơn hàng</button>
                                <button type="submit" name="delete_order" class="btn">Xóa đơn hàng</button>
                            </div>
                        </form>
                    </div>
                    <?php
                }
            } else {
                echo '
                        <div class="empty" style="margin: 2rem auto;">
                            <p>Không có đơn hàng</p>
                        </div>
                    ';
            }
            ?>
        </div>

    </section>
    <?php include '../components/admin_footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>