<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
}
if (isset($_POST['delete_employee'])) {
    $delete_id = $_POST['delete_id'];

    $delete_query = $conn->prepare("DELETE FROM `sellers` WHERE id = :id");
    $delete_query->bindParam(':id', $delete_id);

    if ($delete_query->execute()) {
        echo "<script>alert('Xóa nhân viên thành công!');</script>";
    } else {
        echo "<script>alert('Xóa nhân viên thất bại!');</script>";
    }

    // Tránh lặp lại hành động xóa khi reload trang
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <title>Thông Tin Nhân Viên</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Thông Tin Nhân Viên</h1>
            <span><a href="dashboard.php">Quản trị</a><i class="bx bx-right-arrow-alt"></i>Thông tin nhân viên</span>
        </div>
    </div>
    <section class="dashboard">
        <div class="heading">
            <h1>Danh Sách Nhân Viên</h1>
            <img src="../image/separator.png" width="100">
        </div>
        <div class="table-container">
            <?php
            // Truy vấn thông tin nhân viên
            $select_employees = $conn->prepare("SELECT * FROM `sellers` WHERE type_seller = ?");
            $select_employees->execute(['employee']);
            if ($select_employees->rowCount() > 0) {
                ?>
                <table>
                    <thead>
                        <tr>
                            <th>Số thứ tự</th>
                            <th>Tên Nhân Viên</th>
                            <th>Email</th>
                            <th>Hành Động</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $counter = 1;
                        while ($fetch_employee = $select_employees->fetch(PDO::FETCH_ASSOC)) {
                            ?>
                            <tr>
                                <td><?php echo $counter++; ?></td>
                                <td><?php echo htmlspecialchars($fetch_employee['name']); ?></td>
                                <td><?php echo htmlspecialchars($fetch_employee['email']); ?></td>
                                <td>
                                    <!-- Nút xóa -->
                                    <form method="POST" action=""
                                        onsubmit="return confirm('Bạn có chắc muốn xóa nhân viên này?');">
                                        <input type="hidden" name="delete_id" value="<?php echo $fetch_employee['id']; ?>">
                                        <button type="submit" name="delete_employee" class="btn-product"
                                            style="background-color: red;">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                            <?php
                        }
                        ?>
                    </tbody>
                </table>
                <?php
            } else {
                echo '<p class="empty">Không có nhân viên nào.</p>';
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
<style>
    /* General Table Styles */
    .table-container {
        margin: 20px auto;
        padding: 20px;
        background-color: #ffffff;
        border-radius: 10px;
        box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        overflow-x: auto;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 10px;
        border-radius: 1rem;
    }

    thead tr {
        background-color: #ffeae5;
        color: #000;
        text-align: left;
        
    }

    thead th {
        padding: 12px 15px;
        font-size: 1.4rem;
    }

    tbody tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tbody tr:hover {
        background-color: #ddd;
    }

    td,
    th {
        padding: 10px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
        font-size: 1.4rem;
    }

    /* Button Styling */
    .btn {
        display: inline-block;
        padding: 8px 12px;
        color: #fff;
        background-color: #4CAF50;
        border: none;
        border-radius: 5px;
        text-decoration: none;
        font-size: 14px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .btn:hover {
        background-color: #45a049;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        table {
            font-size: 14px;
        }

        .btn {
            font-size: 12px;
            padding: 6px 10px;
        }
    }
</style>