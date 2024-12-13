<?php
include '../components/connect.php'; // Kết nối cơ sở dữ liệu với PDO

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['publish'])) {
        $id = unique_id();

        $name = $_POST['title'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $price = $_POST['price'];
        $price = filter_var($price, FILTER_SANITIZE_STRING);

        $content = $_POST['content'];
        $content = filter_var($content, FILTER_SANITIZE_STRING);

        $stock = $_POST['stock'];
        $stock = filter_var($stock, FILTER_SANITIZE_STRING);
        $status = 'active';

        // Kiểm tra và cập nhật số lượng hàng từ xuất kho
        $sql_check_stock = "SELECT so_luong FROM nhap_hang WHERE ten_hang = :ten_hang";
        $stmt_check_stock = $conn->prepare($sql_check_stock);
        $stmt_check_stock->bindParam(':ten_hang', $name);
        $stmt_check_stock->execute();
        $row_stock = $stmt_check_stock->fetch(PDO::FETCH_ASSOC);

        if ($row_stock) {
            $so_luong_hien_tai = $row_stock['so_luong'];

            if ($so_luong_hien_tai >= $stock) {
                // Cập nhật số lượng hàng trong kho
                $so_luong_con_lai = $so_luong_hien_tai - $stock;
                $sql_update_stock = "UPDATE nhap_hang SET so_luong = :so_luong_con_lai WHERE ten_hang = :ten_hang";
                $stmt_update_stock = $conn->prepare($sql_update_stock);
                $stmt_update_stock->bindParam(':so_luong_con_lai', $so_luong_con_lai);
                $stmt_update_stock->bindParam(':ten_hang', $name);
                $stmt_update_stock->execute();

                // Cập nhật số lượng trong chitiet_nhap_hang
                // $sql_update_detail_stock = "UPDATE chitiet_nhap_hang SET so_luong = so_luong - :stock WHERE ten_hang = :ten_hang";
                // $stmt_update_detail_stock = $conn->prepare($sql_update_detail_stock);
                // $stmt_update_detail_stock->bindParam(':stock', $stock);
                // $stmt_update_detail_stock->bindParam(':ten_hang', $name);
                // $stmt_update_detail_stock->execute();

                // Thêm sản phẩm mới vào bảng products
                if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
                    $image = $_FILES['image']['name'];
                    $image = filter_var($image, FILTER_SANITIZE_STRING);
                    $image_size = $_FILES['image']['size'];
                    $image_tmp_name = $_FILES['image']['tmp_name'];
                    $image_folder = '../uploaded_files/' . $image;

                    $select_image = $conn->prepare("SELECT * FROM `products` WHERE image = ? AND seller_id = ?");
                    $select_image->execute([$image, $seller_id]);

                    if ($select_image->rowCount() > 0) {
                        $warning_msg[] = 'Tên hình ảnh bị lặp lại';
                    } elseif ($image_size > 2000000) {
                        $warning_msg[] = 'Kích thước hình ảnh lớn';
                    } else {
                        move_uploaded_file($image_tmp_name, $image_folder);
                    }
                } else {
                    $image = '';
                }

                if ($select_image->rowCount() > 0 && $image != '') {
                    $warning_msg[] = 'Vui lòng đổi tên hình ảnh';
                } else {
                    $insert_product = $conn->prepare("INSERT INTO `products`(id, seller_id, name, price, image, stock, product_detail, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $insert_product->execute([$id, $seller_id, $name, $price, $image, $stock, $content, $status]);

                    $success_msg[] = 'Sản phẩm được thêm thành công';
                }
            } else {
                $warning_msg[] = 'Số lượng hàng trong kho không đủ để thêm sản phẩm';
            }
        } else {
            $warning_msg[] = 'Không tìm thấy sản phẩm trong kho để thêm';
        }
    }
}




//save draft

if (isset($_POST['draft'])) {
    $id = unique_id();

    $name = $_POST['title'];
    $name = filter_var($name, FILTER_SANITIZE_STRING);

    $price = $_POST['price'];
    $price = filter_var($price, FILTER_SANITIZE_STRING);

    $content = $_POST['content'];
    $content = filter_var($content, FILTER_SANITIZE_STRING);

    // $stock = $_POST['stock'];
    // $stock = filter_var($stock, FILTER_SANITIZE_STRING);
    // $status = 'deactive';

    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $image = $_FILES['image']['name'];
        $image = filter_var($image, FILTER_SANITIZE_STRING);
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder = '../uploaded_files/' . $image;

        $select_image = $conn->prepare("SELECT * FROM `products` WHERE image = ? AND seller_id = ?");
        $select_image->execute([$image, $seller_id]);

        if ($select_image->rowCount() > 0) {
            $warning_msg[] = 'tên hình ảnh bị lặp lại';
        } elseif ($image_size > 2000000) {
            $warning_msg[] = 'kích thước hình ảnh lớn';
        } else {
            move_uploaded_file($image_tmp_name, $image_folder);
        }
    } else {
        $image = '';
    }

    if ($select_image->rowCount() > 0 and $image != '') {
        $warning_msg[] = 'vui lòng đổi tên hình ảnh';
    } else {
        $insert_product = $conn->prepare("INSERT INTO `products`(id, seller_id, name, price, image, stock, product_detail, status) VALUE(?,?,?,?,?,?,?,?)");
        $insert_product->execute([$id, $seller_id, $name, $price, $image, $stock, $content, $status]);

        $success_msg[] = 'lưu sản phẩm thành công';
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
    <title>Quản Trị - Thêm Sản Phẩm</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <!-- <div class="banner">
        <div class="detail">
            <h1>thêm sản phẩm</h1>
            <span><a href="dashboard.php">Quản Trị</a><i class="bx bx-right-arrow-alt"></i>thêm sản phẩm</span>
        </div>
    </div> -->
    <section class="add-product">
        <div class="heading">
            <h1>thêm sản phẩm</h1>
            <img src="../image/separator.png">
        </div>
        <div class="form-container">
            <form action="" method="post" enctype="multipart/form-data" class="register">
                <div class="input-field">
                    <p>tên sản phẩm<span>*</span></p>
                    <select name="title" required class="box">
                        <option value="" disabled selected>Chọn sản phẩm</option>
                        <?php
                        // Truy vấn danh sách tên sản phẩm từ bảng nhap_hang
                        $query = "SELECT ten_hang FROM nhap_hang";
                        $stmt = $conn->prepare($query);
                        $stmt->execute();

                        // Duyệt qua các kết quả truy vấn và tạo các tùy chọn trong thẻ select
                        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                            echo '<option value="' . htmlspecialchars($row['ten_hang']) . '">' . htmlspecialchars($row['ten_hang']) . '</option>';
                        }
                        ?>
                    </select>
                </div>

                <div class="input-field">
                    <p>giá sản phẩm<span>*</span></p>
                    <input type="number" name="price" maxlength="100" placeholder="Thêm giá sản phẩm" required class="box" readonly>
                </div>

                <div class="input-field">
                    <p>mô tả sản phẩm<span>*</span></p>
                    <textarea name="content" required maxlength="10000" placeholder="mô tả sản phẩm"
                        class="box"></textarea>
                </div>
                <div class="input-field">
                    <p>số lượng<span>*</span></p>
                    <input type="number" name="stock" maxlength="10000" placeholder="số lượng" min="0" max="999999999"
                        required class="box">
                </div>
                <div class="input-field">
                    <p>hình ảnh sản phẩm<span>*</span></p>
                    <input type="file" name="image" accept="image/*" required class="box">
                </div>
                <div class="flex-btn">
                    <input type="submit" name="publish" value="thêm sản phẩm" class="btn">
                    <!-- <input type="submit" name="draft" value="lưu nháp" class="btn"> -->
                </div>
            </form>
        </div>
    </section>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        const titleSelect = document.querySelector('select[name="title"]');
        const priceInput = document.querySelector('input[name="price"]');

        titleSelect.addEventListener('change', () => {
            const selectedTitle = titleSelect.value;

            if (selectedTitle) {
                fetch(`get_price.php?title=${encodeURIComponent(selectedTitle)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            priceInput.value = data.price;
                        } else {
                            alert(data.message);
                        }
                    })
                    .catch(error => console.error('Error fetching price:', error));
            }
        });
    });
</script>

</body>

</html>