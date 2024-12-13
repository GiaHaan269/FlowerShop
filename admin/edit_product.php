<?php
    include '../components/connect.php';

    if(isset($_COOKIE['seller_id'])){
        $seller_id = $_COOKIE['seller_id'];
    }else{
        $seller_id ='';
        header('location:login.php');
    }

    if(isset($_POST['update'])){
        $product_id = $_POST['product_id'];
        $product_id = filter_var($product_id, FILTER_SANITIZE_STRING);

        $name = $_POST['name'];
        $name = filter_var($name, FILTER_SANITIZE_STRING);

        $price = $_POST['price'];
        $price = filter_var($price, FILTER_SANITIZE_STRING);

        $content = $_POST['content'];
        $content = filter_var($content, FILTER_SANITIZE_STRING);

        $stock = $_POST['stock'];
        $stock = filter_var($stock, FILTER_SANITIZE_STRING);

        $status = $_POST['status'];
        $status = filter_var($status, FILTER_SANITIZE_STRING);

    $new_stock = isset($_POST['stock']) ? filter_var($_POST['stock'], FILTER_SANITIZE_NUMBER_INT) : 0;

    // Truy vấn sản phẩm hiện tại
    $query = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $query->execute([$product_id]);
    $fetch_product = $query->fetch(PDO::FETCH_ASSOC);

    if ($fetch_product) {
        $current_stock = $fetch_product['stock']; // Số lượng hiện tại

        // Lấy tổng số lượng trong bảng nhap_hang
        $get_stock_query = $conn->prepare("
            SELECT SUM(so_luong) AS total_quantity
            FROM nhap_hang
            WHERE ten_hang = ?
        ");
        $get_stock_query->execute([$name]);
        $stock_data = $get_stock_query->fetch(PDO::FETCH_ASSOC);

        $available_stock = $stock_data['total_quantity'] ?? 0;

        if ($new_stock > $available_stock) {
            $warning_msg[] = 'Không đủ sản phẩm trong kho!';
        } else {
            // Cập nhật số lượng trong nhap_hang
            $update_stock_query = $conn->prepare("
                UPDATE nhap_hang
                SET so_luong = so_luong - ?
                WHERE ten_hang = ?
            ");
            $update_stock_query->execute([$new_stock - $current_stock, $name]);

            // Cập nhật chi tiết sản phẩm
            $update_product = $conn->prepare("
                UPDATE products
                SET name = ?, price = ?, product_detail = ?, stock = ?, status = ?
                WHERE id = ?
            ");
            $update_product->execute([$name, $price, $content, $new_stock, $status, $product_id]);

            $success_msg[] = 'Cập nhật sản phẩm thành công!';
        }
    } else {
        $warning_msg[] = 'Không tìm thấy sản phẩm!';
    }
        $old_image = $_POST['old_image'];
        $image = $_FILES['image']['name'];
        $image_size = $_FILES['image']['size'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_folder ='../uploaded_files/' .$image;

        $select_image = $conn->prepare("SELECT * FROM `products` WHERE image = ? AND seller_id = ?");

        $select_image->execute([$image, $seller_id]);

        if(!empty($image)){
            if($image_size > 2000000){
                $warning_msg[] = 'kích thước hình ảnh lớn';
            }elseif($select_image->rowCount() > 0 AND $image != ''){
                $warning_msg[] = 'vui lòng đổi tên hình ảnh của bạn';
            }else{
                $update_image= $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
                $update_image->execute([$image, $product_id]);

                move_uploaded_file($image_tmp_name, $image_folder);
                if($old_image != $image AND $old_image != ''){
                    unlink('../uploaded_files/' .$old_image);
                }
                $success_msg[] = 'hình ảnh được cập nhật';
            }
        }
    }
    
    if(isset($_POST['delete'])){
        $product_id = $_POST['product_id'];
        $product_id = filter_var($product_id, FILTER_SANITIZE_STRING);

        $delete_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        $delete_image->execute([$product_id]);
        $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);

        if($fetch_delete_image['image'] != ''){
            unlink('../uploaded_files/' .$fetch_delete_image['image']);
        }
        $delete_product = $conn->prepare("DELETE  FROM `products` WHERE id = ?");
        $delete_product->execute([$product_id]);

        $success_msg[] = 'sản phẩm đã xóa thành công';
    }

    

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name = "viewport" content="width=device-width, initial-scale=1">
    
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <title>Quản Trị - Sửa Sản Phẩm</title>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>sửa sản phẩm</h1>
            
            <span><a href="dashboard.php">Quản Trị</a><i class="bx bx-right-arrow-alt"></i>sửa sản phẩm</span>
        </div>
    </div>
    <section class="post-editor">
        <div class="heading">
            <h1>sửa sản phẩm</h1>
            <img src="../image/separator.png">
        </div>
        <div class="container">
            <?php 
                $product_id = $_GET['id'];
                $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                $select_product->execute([$product_id]);

                if($select_product->rowCount() > 0){
                    while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)){

            ?>
            <div class="form-container">
                <form action="" method="post" enctype="multipart/form-data" class="register">
                    <input type="hidden" name="old_image" value="<?= $fetch_product['image']; ?>">
                    <input type="hidden" name="product_id" value="<?= $fetch_product['id']; ?>">

                    <div class="input-field">
                        <p>trạng thái sản phẩm <span>*</span></p>
                        <select name="status" class="box">
                            <option selected value="<?= $fetch_product['status']; ?>">
                                <?= $fetch_product['status'] == 'active' ? 'Trạng thái' : 'hết hàng'; ?></option>
                            <option value="active">Còn hàng</option>
                            <option value="deactive">Hết hàng</option>
                        </select>
                    </div>

                    <div class="input-field">
                        <p>tên sản phẩm <span>*</span></p>
                        <input type="text" name="name" value="<?= $fetch_product['name']; ?>"
                            class="box">
                    </div>

                    <div class="input-field">
                        <p>giá sản phẩm <span>*</span></p>
                        <input type="number" name="price" value="<?= $fetch_product['price']; ?>"
                            class="box">
                    </div>

                    <div class="input-field">
                        <p>mô tả sản phẩm <span>*</span></p>
                        <textarea name="content" class="box"><?= $fetch_product['product_detail']; ?> </textarea>
                    </div>

                    <div class="input-field">
                        <p>tổng số sản phẩm <span>*</span></p>
                        <input type="number" name="stock" value="<?= $fetch_product['stock']; ?>"
                            class="box" maxlength="10" min="0" max="9999999999">
                    </div>

                    <div class="input-field">
                        <p>hình ảnh sản phẩm<span>*</span></p>
                        <input type="file" name="image" accept="image/*" class="box">
                        <?php if($fetch_product['image'] != ''){?>
                            <img src="../uploaded_files/<?= $fetch_product['image']; ?>" class="image">
                        <?php } ?>
                    </div>

                    <div class="flex-btn">
                        <input type="submit" name="update" value="cập nhật sản phẩm" class="btn">
                        <input type="submit" name="delete" value="xóa sản phẩm" class="btn"
                        onclick="return confirm('xóa sản phẩm này');">
                    </div>
                </form>
            </div>
            <?php
                    }
                }else{
                    echo'
                    <div class="empty" style="margin: 2rem auto;">
                        <p>không có sản phẩm nào được thêm! <br> <a href="add_product.php" class="btn style="margin-top:
                        1rem;">thêm sản phẩm</a></p>
                    </div
                    ';
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
