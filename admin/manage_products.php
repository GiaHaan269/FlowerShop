<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
    exit();
}

if (isset($_GET['message']) && $_GET['message'] == 'deleted') {
    echo "<script>
        swal('Thành công!', 'Hàng đã được xóa!', 'success');
    </script>";
}

// Kiểm tra xem có filter không
$filterLowStock = isset($_GET['filter']) && $_GET['filter'] == 'low_stock';

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <title>Quản Trị - Kho Hàng</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <!-- <div class="banner">
        <div class="detail">
            <h1>Quản Lý Kho</h1>
            <span><a href="dashboard.php">Quản Trị</a><i class="bx bx-right-arrow-alt"></i>Danh Sách Hàng Đã Nhập</span>
        </div>
    </div> -->
    <section class="manage-products">
        <div class="heading">
            <h1>Danh Sách Hàng Đã Nhập</h1>
            <img src="../image/separator.png">
        </div>

        <!-- Form lọc sản phẩm sắp hết hàng -->
        <form method="GET" action="" class="filter-form">
            <button type="submit" name="filter" value="low_stock" class="btn-product">
                Sản phẩm sắp hết hàng
            </button>
            <button type="submit" name="filter" value="" class="btn-product">
                Tất cả sản phẩm
            </button>
        </form>

        <div class="table-container">
            <table class="table table-bordered mt-3">
                <tbody>
                    <?php
                    // Truy vấn dữ liệu hàng đã nhập
                    if ($filterLowStock) {
                        // Lọc chỉ những sản phẩm có số lượng dưới 10
                        $sql_nhap = "SELECT * FROM nhap_hang WHERE so_luong < 10";
                    } else {
                        // Truy vấn tất cả sản phẩm
                        $sql_nhap = "SELECT * FROM nhap_hang";
                    }

                    $stmt = $conn->prepare($sql_nhap);
                    $stmt->execute();

                    if ($stmt->rowCount() > 0) {
                        echo "<table class='table table-bordered mt-3'>";
                        echo "<thead><th>Tên hàng</th><th>Số lượng</th><th>Giá Nhập</th><th>Giá Bán</th><th>Nhân Viên Nhập</th><th>Xóa đơn</th><th>Nhiệm vụ</th><th>Trạng thái</th></tr></thead>";
                        echo "<tbody>";
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            $productName = htmlspecialchars($row["ten_hang"]);
                            $productQty = $row["so_luong"];
                            $productPriceIn = number_format($row["gia_nhap"], 0, ',', '.');
                            $productPriceOut = number_format($row["gia_ban"], 0, ',', '.');
                            $employeeName = htmlspecialchars($row["nhan_vien_nhap"]);
                            $productId = $row["id"];
                            $stockStatus = $row["yeu_cau_nhap"];  // Trạng thái yêu cầu nhập
                    
                            // Kiểm tra nếu sản phẩm có số lượng dưới 10
                            $lowStockWarning = '';
                            if ($productQty < 10) {
                                $lowStockWarning = '<span style="color: red; font-weight: bold;">Sắp hết hàng</span>';
                            }

                            // Hiển thị trạng thái hoàn thành (0: hoàn thành, 1: chưa hoàn thành)
                            $completionStatus = ($stockStatus == 0) ? "Hoàn thành" : "Chưa hoàn thành";

                            $completionStatusColor = ($stockStatus == 0) ? "green" : "red";
                            // Hiển thị dữ liệu sản phẩm và cảnh báo nếu cần
                            echo "<tr>";
                            echo "<td>" . $productName . " " . $lowStockWarning . "</td>";
                            echo "<td>" . $productQty . "</td>";
                            echo "<td>" . $productPriceIn . " </td>";
                            echo "<td>" . $productPriceOut . " </td>";
                            echo "<td>" . $employeeName . "</td>";

                            echo "<td>
    <a href='delete_product.php?id=" . $productId . "' 
       class='btn-product' 
       style='background-color: #ff4d4d; color: white;' 
       onclick='return confirm(\"Bạn có chắc muốn xóa hàng này?\");'>
       Xóa
    </a>
</td>";
                            echo "<td><a href='request_product.php?id=" . $productId . "' class='btn-product'>Yêu cầu nhập</a></td>";
                            echo "<td style='color: $completionStatusColor; font-weight: bold;'>" . $completionStatus . "</td>";

                            echo "</tr>";
                        }

                        echo "</tbody></table>";
                    } else {
                        echo '
                            <div class="empty">
                                <p>Chưa có đơn hàng được nhập</p>
                            </div>
                        ';
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>


    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>