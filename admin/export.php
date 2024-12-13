<?php
include '../components/connect.php'; // Kết nối cơ sở dữ liệu với PDO

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ POST
    $ten_hang = $_POST['ten_hang'];
    $so_luong_xuat = $_POST['so_luong'];
    $ngay_xuat = $_POST['ngay_xuat'];

    try {
        // Kiểm tra số lượng hàng trong kho
        $sql_check = "SELECT so_luong FROM nhap_hang WHERE ten_hang = :ten_hang";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bindParam(':ten_hang', $ten_hang);
        $stmt_check->execute();

        if ($stmt_check->rowCount() > 0) {
            $row = $stmt_check->fetch(PDO::FETCH_ASSOC);
            $so_luong_hien_tai = $row['so_luong'];

            if ($so_luong_hien_tai >= $so_luong_xuat) {
                // Cập nhật số lượng hàng sau khi xuất
                $so_luong_con_lai = $so_luong_hien_tai - $so_luong_xuat;
                $sql_update = "UPDATE nhap_hang SET so_luong = :so_luong WHERE ten_hang = :ten_hang";
                $stmt_update = $conn->prepare($sql_update);
                $stmt_update->bindParam(':so_luong', $so_luong_con_lai);
                $stmt_update->bindParam(':ten_hang', $ten_hang);
                
                if ($stmt_update->execute()) {
                    // Thêm vào bảng xuất hàng
                    $sql_insert = "INSERT INTO xuat_hang (ten_hang, so_luong, ngay_xuat) VALUES (:ten_hang, :so_luong, :ngay_xuat)";
                    $stmt_insert = $conn->prepare($sql_insert);
                    $stmt_insert->bindParam(':ten_hang', $ten_hang);
                    $stmt_insert->bindParam(':so_luong', $so_luong_xuat);
                    $stmt_insert->bindParam(':ngay_xuat', $ngay_xuat);
                    
                    if ($stmt_insert->execute()) {
                        header("Location: xuat_warehouse.php"); // Quay lại trang chính sau khi xuất hàng
                        exit();
                    } else {
                        echo "Lỗi khi thêm vào bảng xuất hàng: " . $stmt_insert->errorInfo()[2];
                    }
                } else {
                    echo "Lỗi khi cập nhật số lượng hàng: " . $stmt_update->errorInfo()[2];
                }
            } else {
                echo "Số lượng hàng trong kho không đủ! <a href='xuat_warehouse.php'>Quay lại</a>";
            }
        } else {
            echo "Không tìm thấy sản phẩm trong kho! <a href='xuat_warehouse.php'>Quay lại</a>";
        }
    } catch (PDOException $e) {
        echo "Lỗi: " . $e->getMessage();
    }
}
?>
