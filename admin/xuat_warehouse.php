<?php
include '../components/connect.php'; // Kết nối cơ sở dữ liệu với PDO

if(isset($_COOKIE['seller_id'])){
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Kiểm tra và lấy giá trị từ POST request
    $ten_hang = isset($_POST['ten_hang']) ? $_POST['ten_hang'] : null;
    $so_luong_xuat = isset($_POST['so_luong']) ? $_POST['so_luong'] : null;
    $ngay_xuat = isset($_POST['ngay_xuat']) ? $_POST['ngay_xuat'] : null; // Đảm bảo ngay_xuat tồn tại

    // Kiểm tra xem tất cả các giá trị đã được đặt
    if ($ten_hang && $so_luong_xuat && $ngay_xuat) {
        try {
            // Bắt đầu giao dịch để đảm bảo tính toàn vẹn dữ liệu
            $conn->beginTransaction();

            // Kiểm tra số lượng hàng trong kho
            $sql_check = "SELECT so_luong FROM nhap_hang WHERE ten_hang = :ten_hang";
            $stmt_check = $conn->prepare($sql_check);
            $stmt_check->bindParam(':ten_hang', $ten_hang);
            $stmt_check->execute();
            $row_check = $stmt_check->fetch(PDO::FETCH_ASSOC);

            if ($row_check) {
                $so_luong_hien_tai = $row_check['so_luong'];

                if ($so_luong_hien_tai >= $so_luong_xuat) {
                    // Cập nhật số lượng hàng trong kho
                    $so_luong_con_lai = $so_luong_hien_tai - $so_luong_xuat;
                    $sql_update = "UPDATE nhap_hang SET so_luong = :so_luong_con_lai WHERE ten_hang = :ten_hang";
                    $stmt_update = $conn->prepare($sql_update);
                    $stmt_update->bindParam(':so_luong_con_lai', $so_luong_con_lai);
                    $stmt_update->bindParam(':ten_hang', $ten_hang);
                    $stmt_update->execute();

                    // Thêm vào bảng xuất hàng
                    $sql_insert = "INSERT INTO xuat_hang (ten_hang, so_luong, ngay_xuat) VALUES (:ten_hang, :so_luong_xuat, :ngay_xuat)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bindParam(':ten_hang', $ten_hang);
                    $stmt_insert->bindParam(':so_luong_xuat', $so_luong_xuat);
                    $stmt_insert->bindParam(':ngay_xuat', $ngay_xuat);
                    $stmt_insert->execute();

                    // Nếu tất cả các câu lệnh đều thành công, commit giao dịch
                    $conn->commit();
                    echo "Xuất hàng thành công!";
                } else {
                    echo "Số lượng hàng trong kho không đủ! <a href='xuat_warehouse.php'>Quay lại</a>";
                }
            } else {
                echo "Không tìm thấy sản phẩm trong kho! <a href='xuat_warehouse.php'>Quay lại</a>";
            }
        } catch (PDOException $e) {
            // Rollback nếu có lỗi
            $conn->rollBack();
            echo "Lỗi khi xuất hàng: " . $e->getMessage();
        }
    } else {
        echo "Vui lòng điền đầy đủ thông tin!";
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
    <?php include '../components/admin_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Quản Lý Kho</h1>
            <span><a href="dashboard.php">Quản Trị</a><i class="bx bx-right-arrow-alt"></i>Xuất Hàng</span>
        </div>
    </div>
    <section class="add-product">
        <div class="heading">
            <h1>Xuất Hàng</h1>
            <img src="../image/separator.png">
        </div>
        <div class="form-and-table-container">
            <div class="form-container-warehouse">
                <form action="" method="post" enctype="multipart/form-data" class="register">
                    <div class="input-field">
                        <p>Tên hàng<span>*</span></p>
                        <input type="text" id="ten_hang" name="ten_hang" maxlength="100" placeholder="xuất hàng" required class="box">
                    </div>
                    <div class="input-field">
                        <p>Số lượng<span>*</span></p>
                        <input type="number" id="so_luong" name="so_luong" maxlength="100" placeholder="số lượng" required class="box">
                    </div>
                    <div class="input-field">
                        <p>Ngày Xuất<span>*</span></p>
                        <input type="date" id="ngay_xuat" name="ngay_xuat" maxlength="100" placeholder="" required class="box">
                    </div>
                    
                    <!-- <div class="input-field">
                        <p>hình ảnh loại hoa<span>*</span></p>
                        <input type="file" name="image" accept="image/*" required class="box">
                    </div> -->
                    <div class="flex-btn">
                        <input type="submit" name="publish" value="Xuất Hàng" class="btn">
                        <!-- <input type="submit" name="draft" value="lưu nháp" class="btn"> -->
                    </div>
                </form>
            </div>
            <div class="table-container">
                <!-- <h2 class='mt-5'>Danh sách hàng đã xuất</h2> -->
                <table class="table table-bordered mt-3">
                
                    <tbody>
                        <?php
                        // Hiển thị hàng đã xuất
                        try {
                            $sql_xuat = "SELECT * FROM xuat_hang";
                            $stmt_xuat = $conn->prepare($sql_xuat);
                            $stmt_xuat->execute();

                            if ($stmt_xuat->rowCount() > 0) {
                                echo "<h2 class='mt-5'>Danh sách hàng đã xuất</h2>";
                                echo "<table class='table table-bordered mt-3'>";
                                echo "<thead><tr><th>Tên hàng</th><th>Số lượng</th><th>Ngày xuất</th></tr></thead>";
                                echo "<tbody>";
                                while ($row = $stmt_xuat->fetch(PDO::FETCH_ASSOC)) {
                                    echo "<tr>";
                                    // echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["ten_hang"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["so_luong"]) . "</td>";
                                    echo "<td>" . htmlspecialchars($row["ngay_xuat"]) . "</td>";
                                    echo "</tr>";
                                }
                                echo "</tbody></table>";
                            } else {
                                echo "<p>Chưa có hàng nào được xuất.</p>";
                            }
                        } catch (PDOException $e) {
                            echo "Lỗi truy vấn SQL: " . $e->getMessage();
                        }

                        // Không cần gọi $conn->close() với PDO
                        ?>

                    </tbody>
                </table>
            </div>
        </div>
    </section>
    
    
    <?php include '../components/admin_footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>
</html>
