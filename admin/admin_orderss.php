<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    header('location:login.php');
    exit();
}

// Kiểm tra xem người dùng có phải là admin không
$check_admin = $conn->prepare("SELECT type_seller FROM `sellers` WHERE id = ?");
$check_admin->execute([$seller_id]);
$fetch_role = $check_admin->fetch(PDO::FETCH_ASSOC);

if ($fetch_role['type_seller'] != 'admin') {
    header('location:login.php');
    exit();
}

// Cập nhật trạng thái đơn hàng
if (isset($_POST['update_order'])) {
    $order_id = $_POST['order_id'];
    $order_id = filter_var($order_id, FILTER_SANITIZE_STRING);

    $update_payment = $_POST['update_payment'];
    $update_payment = filter_var($update_payment, FILTER_SANITIZE_STRING);

    $update_pay = $conn->prepare("UPDATE `orders` SET payment_status = ? WHERE id = ?");
    $update_pay->execute([$update_payment, $order_id]);

    $success_msg[] = 'Trạng thái đơn hàng đã được cập nhật';
}

// Xóa đơn hàng
if (isset($_POST['delete_order'])) {
    $delete_id = $_POST['order_id'];
    $delete_id = filter_var($delete_id, FILTER_SANITIZE_STRING);

    $verify_delete = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
    $verify_delete->execute([$delete_id]);

    if ($verify_delete->rowCount() > 0) {
        $delete_order = $conn->prepare("DELETE FROM `orders` WHERE id = ?");
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
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <title>Đơn hàng</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Đơn Hàng</h1>
            <span><a href="dashboard.php">Admin</a> <i class="bx bx-right-arrow-alt"></i> Đơn hàng</span>
        </div>
    </div>
    <section class="order-container">
        <div class="table-wrapper">
            <div class="heading">
                <h1>Tất Cả Đơn Hàng</h1>
                <img src="../image/separator.png">
            </div>
            <table class="order-table">
                <thead>
                    <tr>
                        <th>Trạng thái</th>
                        <th>Tên</th>
                        <!-- <th>ID</th> -->
                        <th>Ngày</th>
                        <th>Số điện thoại</th>
                        <th>Email</th>
                        <th>Phương thức thanh toán</th>
                        <th>Địa chỉ</th>
                        <th>Chi tiết</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Truy vấn tất cả các đơn hàng (không lọc theo `seller_id`)
                    $select_order = $conn->prepare("
                        SELECT 
                            id, user_id, name, date, number, email, method, address, status, 
                            SUM(price) AS total_price, 
                            payment_status 
                        FROM `orders`
                        GROUP BY id
                    ");
                    $select_order->execute();

                    if ($select_order->rowCount() > 0) {
                        while ($fetch_order = $select_order->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <tr>
                                <td class="status" style="color: 
                                    <?php
                                        if ($fetch_order['status'] == 'in progress') {
                                            echo 'orange';
                                        } elseif ($fetch_order['status'] == 'received') {
                                            echo 'green';
                                        } elseif ($fetch_order['status'] == 'canceled') {
                                            echo 'red';
                                        } elseif ($fetch_order['status'] == 'Hủy hàng') {
                                            echo 'red';
                                        } else {
                                            echo 'orange';
                                        }
                                        ?>
                                    ">
                                    <?php
                                    if ($fetch_order['status'] == 'received') {
                                        echo 'Đã hoàn tất'; // Show 'Đã hoàn tất' for 'received'
                                    } else {
                                        echo $fetch_order['status'] == 'in progress' ? 'Đang xử lý' : ($fetch_order['status'] == 'canceled' ? 'Đã hủy' : $fetch_order['status']);
                                    }
                                    ?>
                                </td>
                                <td><?= $fetch_order['name']; ?></td>
                                <!-- <td><?= $fetch_order['user_id']; ?></td> -->
                                <td><?= $fetch_order['date']; ?></td>
                                <td><?= $fetch_order['number']; ?></td>
                                <td><?= $fetch_order['email']; ?></td>
                                <td><?= $fetch_order['method']; ?></td>
                                <td><?= $fetch_order['address']; ?></td>
                                <td>
                                    <a href="order_detail.php?id=<?= $fetch_order['id']; ?>"
                                        style="padding: 0.5rem 1rem; font-size: 1.2rem; color: black; display: inline-block; text-align: center;">Chi
                                        tiết</a>

                                </td>
                                <td>
                                    <form action="" method="post">
                                        <input type="hidden" name="order_id" value="<?= $fetch_order['id']; ?>">
                                        <!-- <select name="update_payment" class="box" style="width:90%;">
                                            <option value="Chờ xử lý" <?= $fetch_order['payment_status'] == 'Chờ xử lý' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                            <option value="đã giao" <?= $fetch_order['payment_status'] == 'đã giao' ? 'selected' : ''; ?>>Đã giao</option>
                                            <option value="hoàn thành" <?= $fetch_order['payment_status'] == 'hoàn thành' ? 'selected' : ''; ?>>Hoàn thành</option>
                                        </select> -->
                                        <div class="flex-btn">
                                            <!-- <button type="submit" name="update_order" class="btn" style="padding: 0.5rem 1rem; font-size: 0.7rem;">Cập nhật</button> -->
                                            <button type="submit" name="delete_order" class="btn"
                                                style="padding: 0.5rem 1rem; font-size: 0.9rem;">Xóa</button>
                                        </div>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo '
                        <tr>
                            <td colspan="10" class="empty" style="text-align: center;">Không có đơn hàng</td>
                        </tr>
                        ';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <?php include '../components/admin_footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>