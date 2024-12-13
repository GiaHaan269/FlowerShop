<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
    exit(); // Đảm bảo không tiếp tục xử lý mã sau khi chuyển hướng
}

if (isset($_POST['publish'])) {
    // Check for all required POST parameters
    if (
        isset(
        $_POST['ten_hang'],
        $_POST['so_luong'],
        $_POST['ngay_nhap'],
        $_POST['gia_nhap'],
        $_POST['gia_ban'],
        $_POST['nhan_vien_nhap']
    )
    ) {
        $ten_hang = trim($_POST['ten_hang']);
        $so_luong = trim($_POST['so_luong']);
        $ngay_nhap = trim($_POST['ngay_nhap']);
        $gia_nhap = trim($_POST['gia_nhap']);
        $gia_ban = trim($_POST['gia_ban']);
        $nhan_vien_nhap = trim($_POST['nhan_vien_nhap']);

        // Validate the inputs
        if (
            !empty($ten_hang) && is_numeric($so_luong) && !empty($ngay_nhap) &&
            is_numeric($gia_nhap) && is_numeric($gia_ban) && !empty($nhan_vien_nhap)
        ) {
            try {
                // Check if the item already exists in the database
                $sql_check = "SELECT so_luong FROM nhap_hang WHERE ten_hang = :ten_hang";
                $stmt_check = $conn->prepare($sql_check);
                $stmt_check->bindParam(':ten_hang', $ten_hang);
                $stmt_check->execute();

                if ($stmt_check->rowCount() > 0) {
                    // If the item exists, update the record
                    $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
                    $so_luong_hien_tai = $row['so_luong'];
                    $so_luong_moi = $so_luong_hien_tai + $so_luong;

                    $sql_update = "UPDATE nhap_hang SET 
                                   so_luong = :so_luong, ngay_nhap = :ngay_nhap, 
                                   gia_nhap = :gia_nhap, gia_ban = :gia_ban, nhan_vien_nhap = :nhan_vien_nhap 
                                   WHERE ten_hang = :ten_hang";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bindParam(':so_luong', $so_luong_moi);
                    $stmt_update->bindParam(':ngay_nhap', $ngay_nhap);
                    $stmt_update->bindParam(':gia_nhap', $gia_nhap);
                    $stmt_update->bindParam(':gia_ban', $gia_ban);
                    $stmt_update->bindParam(':nhan_vien_nhap', $nhan_vien_nhap);
                    $stmt_update->bindParam(':ten_hang', $ten_hang);
                    $stmt_update->execute();

                    $success_msg[] = 'Cập nhật số lượng hàng thành công!';
                } else {
                    // If the item doesn't exist, insert a new record
                    $sql_insert = "INSERT INTO nhap_hang 
                                  (ten_hang, so_luong, ngay_nhap, gia_nhap, gia_ban, nhan_vien_nhap) 
                                   VALUES (:ten_hang, :so_luong, :ngay_nhap, :gia_nhap, :gia_ban, :nhan_vien_nhap)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bindParam(':ten_hang', $ten_hang);
                    $stmt_insert->bindParam(':so_luong', $so_luong);
                    $stmt_insert->bindParam(':ngay_nhap', $ngay_nhap);
                    $stmt_insert->bindParam(':gia_nhap', $gia_nhap);
                    $stmt_insert->bindParam(':gia_ban', $gia_ban);
                    $stmt_insert->bindParam(':nhan_vien_nhap', $nhan_vien_nhap);
                    $stmt_insert->execute();

                    $success_msg[] = 'Thêm hàng thành công!';
                }
            } catch (PDOException $e) {
                $warning_msg[] = 'Lỗi khi xử lý hàng: ' . $e->getMessage();
            }
        } else {
            $warning_msg[] = 'Dữ liệu đầu vào không hợp lệ.';
        }
    } else {
        $warning_msg[] = 'Dữ liệu đầu vào thiếu.';
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
    <title>Quản Trị - Kho Hàng</title>
</head>

<body>
    <?php include '../components/employee_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Trang Nhân Viên</h1>
            <span><a href="employee_dashboard.php">Nhân viên</a> <i class="bx bx-right-arrow-alt"></i> Trang Chủ</span>
        </div>
    </div>
    <section class="add-product">
        <div class="heading">
            <h1>Nhập Hàng</h1>
            <img src="../image/separator.png">
        </div>
        <div class="form-container-warehouse">
            <form action="" method="post" enctype="multipart/form-data" class="register">
                <!-- Form nhập hàng giữ nguyên như cũ -->
                <!-- Các input cho Tên hàng, Số lượng, Ngày nhập, Giá nhập, Giá bán, Nhân viên nhập -->
                <div class="input-field">
                    <p>Tên hàng<span>*</span></p>
                    <input type="text" id="ten_hang" name="ten_hang" maxlength="100" placeholder="Thêm hàng" required
                        class="box">
                </div>
                <div class="input-field">
                    <p>Số lượng<span>*</span></p>
                    <input type="number" id="so_luong" name="so_luong" maxlength="100" placeholder="Số lượng" required
                        class="box">
                </div>
                <div class="input-field">
                    <p>Ngày Nhập<span>*</span></p>
                    <input type="date" id="ngay_nhap" name="ngay_nhap" maxlength="100" placeholder="" required
                        class="box">
                </div>
                <div class="input-field">
                    <p>Giá Nhập<span>*</span></p>
                    <input type="number" id="gia_nhap" name="gia_nhap" step="0.01" placeholder="Giá nhập" required
                        class="box">
                </div>
                <div class="input-field">
                    <p>Giá Bán<span>*</span></p>
                    <input type="number" id="gia_ban" name="gia_ban" step="0.01" placeholder="Giá bán" required
                        class="box">
                </div>
                <div class="input-field">
                    <p>Nhân Viên Nhập<span>*</span></p>
                    <input type="text" id="nhan_vien_nhap" name="nhan_vien_nhap" maxlength="100"
                        placeholder="Tên nhân viên nhập" required class="box">
                </div>
                <div class="flex-btn">
                    <input type="submit" name="publish" value="Nhập Hàng" class="btn">
                    <a href="manage_products_employee.php" class="btn">Xem Danh Sách Hàng Đã Nhập</a>
                    <a href="chitiethangnv.php" class="btn">Chi tiết Danh Sách Hàng Đã Nhập</a>
                </div>
            </form>
            <!-- <a href="manage_products.php" class="btn">Xem Danh Sách Hàng Đã Nhập</a> -->
        </div>
    </section>

    <?php include '../components/admin_footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>