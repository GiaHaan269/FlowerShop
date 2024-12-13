<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
    exit();
}





if (isset($_POST['filter']) && isset($_POST['filter_date']) && !empty($_POST['filter_date'])) {
    // Lọc theo ngày được chọn
    $filter_date = $_POST['filter_date'];
    $select_details = $conn->prepare("SELECT * FROM chitiet_nhap_hang WHERE ngay_nhap = :filter_date ORDER BY ngay_nhap DESC");
    $select_details->bindParam(':filter_date', $filter_date);
    $select_details->execute();
} else {
    // Nếu không có lọc, lấy tất cả dữ liệu
    $select_details = $conn->prepare("SELECT * FROM chitiet_nhap_hang ORDER BY ngay_nhap DESC");
    $select_details->execute();
}
$filter_date = '';
$low_stock = false;

// Kiểm tra nếu có chọn lọc theo ngày hoặc sản phẩm dưới 10
if (isset($_POST['filter'])) {
    if (isset($_POST['filter_date']) && !empty($_POST['filter_date'])) {
        $filter_date = $_POST['filter_date'];
    }

    if (isset($_POST['low_stock'])) {
        $low_stock = true;
    }
}

// Xây dựng câu truy vấn SQL
$query = "SELECT * FROM chitiet_nhap_hang";

$conditions = [];
$params = [];

if (!empty($filter_date)) {
    $conditions[] = "ngay_nhap = :filter_date";
    $params[':filter_date'] = $filter_date;
}

if ($low_stock) {
    $conditions[] = "so_luong < 10";
}

if (count($conditions) > 0) {
    $query .= " WHERE " . implode(' AND ', $conditions);
}

$query .= " ORDER BY ngay_nhap DESC";

// Thực hiện truy vấn
$select_details = $conn->prepare($query);
$select_details->execute($params);

if (isset($_POST['delete']) && isset($_POST['delete_id'])) {
    $delete_id = $_POST['delete_id'];

    // Xóa bản ghi theo id
    $delete_query = $conn->prepare("DELETE FROM chitiet_nhap_hang WHERE id = :delete_id");
    $delete_query->bindParam(':delete_id', $delete_id);

    if ($delete_query->execute()) {
        echo "<script>alert('Xóa đơn hàng thành công!');</script>";
    } else {
        echo "<script>alert('Xóa đơn hàng thất bại!');</script>";
    }

    // Chuyển hướng để tránh lặp lại hành động xóa khi reload trang
    header("Location: " . $_SERVER['PHP_SELF']);
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
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <title>Chi Tiết Nhập Hàng</title>
</head>

<body>
    <?php include '../components/employee_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Trang Nhân Viên</h1>
            <span><a href="employee_dashboard.php">Nhân viên</a> <i class="bx bx-right-arrow-alt"></i> Trang Chủ</span>
        </div>
    </div>
    <section class="order-container">
        <div class="table-wrapper">
            <div class="heading">
                <h1>Danh Sách Chi Tiết Nhập Hàng</h1>
                <img src="../image/separator.png">
            </div>

            <!-- Thêm Form lọc theo ngày -->
            <form method="POST" action="" style="margin-bottom: 20px;">
                <div class="filter-container">
                    <label for="filter_date">Chọn ngày:</label>
                    <input type="date" id="filter_date" name="filter_date"
                        value="<?= isset($_POST['filter_date']) ? $_POST['filter_date'] : '' ?>">
                    <button type="submit" name="filter" class="btn-product">Lọc</button>
                </div>

                <!-- <button type="submit" name="filter" class="btn">Lọc</button> -->



                <table class="order-table">
                    <thead>
                        <tr>
                            <th>Tên Hàng</th>
                            <th>Số Lượng</th>
                            <th>Ngày Nhập</th>
                            <th>Giá Nhập</th>
                            <th>Giá Bán</th>
                            <th>Nhân Viên Nhập</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        // Nếu có filter ngày, lọc theo ngày được chọn
                        if (isset($_POST['filter']) && isset($_POST['filter_date']) && !empty($_POST['filter_date'])) {
                            $filter_date = $_POST['filter_date'];
                            $select_details = $conn->prepare("SELECT * FROM chitiet_nhap_hang WHERE ngay_nhap = :filter_date ORDER BY ngay_nhap DESC");
                            $select_details->bindParam(':filter_date', $filter_date);
                            $select_details->execute();
                        } else {
                            // Nếu không có filter, lấy tất cả dữ liệu
                            $select_details = $conn->prepare("SELECT * FROM chitiet_nhap_hang ORDER BY ngay_nhap DESC");
                            $select_details->execute();
                        }

                        if ($select_details->rowCount() > 0) {
                            while ($fetch_detail = $select_details->fetch(PDO::FETCH_ASSOC)) {
                                // Kiểm tra số lượng sản phẩm và hiển thị cảnh báo nếu nhỏ hơn 10
                                $so_luong = $fetch_detail['so_luong'];
                                $low_stock_warning = $so_luong < 10 ? '<span style="color: red;">Sắp hết hàng!</span>' : '';
                                ?>
                                <tr>
                                    <td><?= $fetch_detail['ten_hang']; ?></td>
                                    <td><?= $fetch_detail['so_luong']; ?>         <?= $low_stock_warning; ?></td>
                                    <!-- Hiển thị cảnh báo trong cột số lượng -->
                                    <td><?= $fetch_detail['ngay_nhap']; ?></td>
                                    <td><?= number_format($fetch_detail['gia_nhap']); ?></td>
                                    <td><?= number_format($fetch_detail['gia_ban']); ?></td>
                                    <td><?= $fetch_detail['nhan_vien_nhap']; ?></td>
                                    <td>
                                        <!-- Nút xóa -->
                                        <form method="POST" action=""
                                            onsubmit="return confirm('Bạn có chắc muốn xóa đơn hàng này?');">
                                            <input type="hidden" name="delete_id" value="<?= $fetch_detail['id']; ?>">

                                            <button type="submit" name="delete" class="btn-product"
                                                style="background: red; color: white;">Xóa</button>
                                        </form>


                                    </td>
                                </tr>
                                <?php
                            }
                        } else {
                            echo '<tr><td colspan="7" class="empty" style="text-align: center;">Không có chi tiết nhập hàng</td></tr>';
                        }
                        ?>
                    </tbody>

                </table>
        </div>
    </section>


    <?php include '../components/admin_footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
</body>

</html>