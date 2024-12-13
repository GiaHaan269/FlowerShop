<?php
include '../components/connect.php';

// Kiểm tra nếu người dùng đã đăng nhập và là nhân viên nhập hàng
if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
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
    <title>Nhân Viên - Trang Chủ</title>
</head>

<body>
    <?php include '../components/employee_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Trang Nhân Viên</h1>
            <span><a href="employee_dashboard.php">Nhân viên</a> <i class="bx bx-right-arrow-alt"></i> Trang Chủ</span>
        </div>
    </div>

    <section class="dashboard">
        <div class="heading">
            <h1>Trang Chủ Nhân Viên</h1>
            <img src="../image/separator.png" width="100">
        </div>
        <div class="box-container">
            <!-- Nhập hàng -->
            <div class="box">
                <h3>Nhập Hàng</h3>
                <p>Hàng nhập</p>
                <a href="add_warehouse_employee.php" class="btn">Nhập Hàng</a>
            </div>

            <!-- Xem danh sách hàng đã nhập -->
            <div class="box">
                <h3>Danh sách</h3>
                <p>Sản phẩm đã nhập</p>
                <a href="manage_products_employee.php" class="btn">Danh Sách Hàng Đã Nhập</a>
            </div>

            <!-- Hồ sơ cá nhân -->
            <div class="box">
                <h3>Hồ Sơ</h3>
                <p>Thông tin tài khoản</p>
                <a href="employee_profile.php" class="btn">Xem Hồ Sơ</a>
            </div>

            <!-- Đăng xuất -->
            <!-- <div class="box">
                <h3>Đăng Xuất</h3>
                <p>Đăng xuất tài khoản</p>
                <a href="logout.php" class="btn">Đăng Xuất</a>
            </div> -->
        </div>
    </section>

    <?php include '../components/admin_footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>