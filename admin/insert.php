<?php
include '../components/connect.php'; // Kết nối cơ sở dữ liệu

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Lấy dữ liệu từ form
    $ten_hang = $_POST['ten_hang'];
    $so_luong = $_POST['so_luong'];
    $ngay_nhap = $_POST['ngay_nhap'];

    try {
        // Chuẩn bị câu lệnh SQL bằng cách sử dụng Prepared Statement
        $sql = "INSERT INTO nhap_hang (ten_hang, so_luong, ngay_nhap) VALUES (:ten_hang, :so_luong, :ngay_nhap)";
        $stmt = $conn->prepare($sql);

        // Gán giá trị cho các biến
        $stmt->bindParam(':ten_hang', $ten_hang);
        $stmt->bindParam(':so_luong', $so_luong);
        $stmt->bindParam(':ngay_nhap', $ngay_nhap);

        // Thực thi câu lệnh
        if ($stmt->execute()) {
            // Nếu thêm thành công, chuyển hướng về lại trang chính
            header("Location: add_warehouse.php");
            exit();
        } else {
            // Nếu có lỗi trong quá trình thực thi
            echo "Lỗi khi nhập hàng!";
        }
    } catch (PDOException $e) {
        // Bắt lỗi ngoại lệ và hiển thị thông báo
        echo "Lỗi: " . $e->getMessage();
    }

    // Đóng kết nối cơ sở dữ liệu (không cần thiết với PDO vì nó tự động đóng khi kết thúc script)
}
?>
