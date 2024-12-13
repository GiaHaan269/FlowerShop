<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
}

// Lấy giá trị ngày bắt đầu và ngày kết thúc từ form lọc
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <title>Doanh Thu - Quản Trị</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <!-- <div class="banner">
        <div class="detail">
            <h1>Doanh Thu</h1>
            <span><a href="dashboard.php">Quản trị</a><i class="bx bx-right-arrow-alt"></i>doanh thu</span>
        </div>
    </div> -->

    <section class="dashboard">
        <div class="heading">
            <h1>Doanh Thu Cửa Hàng</h1>
            <img src="../image/separator.png" width="100">
        </div>

        <!-- Form lọc ngày -->
        <form action="" method="GET">
            <label for="start_date">Từ ngày:</label>
            <input type="date" id="start_date" name="start_date"
                value="<?= isset($_GET['start_date']) ? $_GET['start_date'] : '' ?>">
            <label for="end_date">Đến ngày:</label>
            <input type="date" id="end_date" name="end_date"
                value="<?= isset($_GET['end_date']) ? $_GET['end_date'] : '' ?>">
            <button type="submit" class="btn-product">Lọc</button>

        </form>

        <div class="box-doanhthu">
            <table class="bang">
                <thead>
                    <tr>
                        <th>Tên Sản Phẩm</th>
                        <th>Số Lượng Đã Bán</th>
                        <th>Chi Phí Nhập</th>
                        <th>Doanh Thu</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    // Truy vấn tất cả sản phẩm của cửa hàng
                    $select_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ?");
                    $select_products->execute([$seller_id]);
                    $products = $select_products->fetchAll(PDO::FETCH_ASSOC);

                    $total_price_after_all = 0;
                    $total_revenue = 0; // Tổng doanh thu
                    $total_cost = 0; // Tổng chi phí nhập
                    
                    // Lặp qua tất cả sản phẩm
                    foreach ($products as $product) {
                        $product_id = $product['id'];
                        $product_name = $product['name'];
                        $product_price = $product['price'];
                        $product_import_price = $product['gia_nhap']; // Lấy giá nhập từ bảng products
                    
                        // Truy vấn số lượng bán của sản phẩm theo product_id từ bảng orders, có lọc theo ngày
                        $select_sales = $conn->prepare("
                            SELECT SUM(qty) AS total_qty
                            FROM `orders`
                            WHERE product_id = ? 
                            AND seller_id = ? 
                            AND status = 'received' 
                            AND (DATE(date) BETWEEN ? AND ?)
                        ");
                        $select_sales->execute([$product_id, $seller_id, $start_date, $end_date]);
                        $sales = $select_sales->fetch(PDO::FETCH_ASSOC);
                        $total_qty = $sales['total_qty'] ? $sales['total_qty'] : 0;

                        // Truy vấn tổng giá trị price_after từ bảng orders, có lọc theo ngày
                        // $select_price_after = $conn->prepare("
                        //     SELECT SUM(price_after) AS total_price_after 
                        //     FROM `orders`
                        //     WHERE product_id = ? 
                        //     AND seller_id = ? 
                        //     AND status = 'received' 
                        //     AND (DATE(date) BETWEEN ? AND ?)
                        // ");
                        // $select_price_after->execute([$product_id, $seller_id, $start_date, $end_date]);
                        // $sales = $select_price_after->fetch(PDO::FETCH_ASSOC);
                        // $price_after = $sales['total_price_after'] ? $sales['total_price_after'] : 0;

                        // Truy vấn tổng giá trị của đơn hàng bị hủy từ bảng orders, có lọc theo ngày
                        $select_canceled_orders = $conn->prepare("
                            SELECT SUM(price * qty) AS total_canceled_value
                            FROM `orders`
                            WHERE seller_id = ? 
                            AND status IN ('Hủy hàng', 'canceled')
                            AND (DATE(date) BETWEEN ? AND ?)
                        ");
                        $select_canceled_orders->execute([$seller_id, $start_date, $end_date]);
                        $canceled_orders = $select_canceled_orders->fetch(PDO::FETCH_ASSOC);
                        $total_canceled_value = $canceled_orders['total_canceled_value'] ? $canceled_orders['total_canceled_value'] : 0;


                        // Cộng giá trị price_after vào tổng giá trị sau giảm giá
                        // $total_price_after_all += $price_after;

                        // Nếu số lượng bán lớn hơn 0, tính doanh thu và chi phí nhập
                        if ($total_qty > 0) {
                            // Tính doanh thu cho sản phẩm
                            $revenue = $product_price * $total_qty;

                            // Tính chi phí nhập cho sản phẩm
                            $cost = $product_import_price * $total_qty;

                            // Cộng vào tổng doanh thu và tổng chi phí
                            $total_revenue += $revenue;
                            $total_cost += $cost;

                            ?>
                            <tr>
                                <td><?= $product_name ?></td>
                                <td><?= $total_qty ?></td>
                                <td><?= number_format($cost) ?></td> <!-- Chi Phí Nhập -->
                                <td><?= number_format($revenue) ?></td>
                            </tr>
                            <?php
                        }
                    }
                    ?>
                </tbody>
            </table>

            <!-- Hiển thị tổng doanh thu, chi phí nhập và giá trị đơn hàng hủy -->
            <div class="total-revenue">
                <h2>Tổng Doanh Thu Cửa Hàng: <?= number_format($total_revenue) ?> VND</h2>
                <h2>Tổng Chi Phí Nhập: <?= number_format($total_cost) ?> VND</h2>
                <!-- <h2>Tổng Giảm Giá: <?= number_format($total_price_after_all) ?> VND</h2> -->
                                <h2 style="color: green;">Lợi nhuận: <?= number_format($total_revenue-($total_cost+ $total_price_after_all)) ?> VND</h2>
                <h2 style="color: red;">Tổng Giá Trị Đơn Hàng Hủy: <?= number_format($total_canceled_value) ?> VND</h2>

            </div>
        </div>
    </section>

    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>