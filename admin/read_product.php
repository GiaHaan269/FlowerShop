<?php
    include '../components/connect.php';

    if(isset($_COOKIE['seller_id'])){
        $seller_id = $_COOKIE['seller_id'];
    }else{
        $seller_id ='';
        header('location:login.php');
    }

    $get_id = $_GET['post_id'];

    if (isset($_POST['delete'])) {
        $p_id = $_POST['product_id'];
        $p_id = filter_var($p_id, FILTER_SANITIZE_STRING);

        $delete_image = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        $delete_image->execute([$p_id]);
        $fetch_delete_image = $delete_image->fetch(PDO::FETCH_ASSOC);

        // Kiểm tra xem có ảnh trong bản ghi không trước khi xóa
        if ($fetch_delete_image[''] != ''){
            unlink('../uploaded_files/'.$fetch_delete_image['image']);
        }
        // Thực hiện xóa sản phẩm
        $delete_product = $conn->prepare("DELETE FROM `products` WHERE id = ?");
        $delete_product->execute([$p_id]);

        // Chuyển hướng người dùng sau khi xóa
        header('Location: view_products.php');
        exit(); // Đảm bảo kết thúc chương trình sau khi chuyển hướng
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
    <title>Quản Trị - Chi Tiết Sản Phẩm</title>
</head>
<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>chi tiết sản phẩm</h1>
            
            <span><a href="dashboard.php">Quản Trị</a><i class="bx bx-right-arrow-alt"></i>sản phẩm</span>
        </div>
    </div>
    <section class="read_product">
        <div class="heading">
            <h1>chi tiết sản phẩm</h1>
            <img src="../image/separator.png">
        </div>
        <div class="container">
            <?php
            $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
            $select_product->execute([$get_id]);

            if($select_product->rowCount() > 0){
                while($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)){
            
            ?>
            <form action="" method="post" class="box">
                <input type="hidden" name="product_id" value="<?= $fetch_product['id'];?>">
                <div class="status" style="color: <?php if ($fetch_product['status'] == 'active') {
                    echo "limegreen";
                } else {
                    echo "red";
                } ?>">
                    <?= $fetch_product['status'] == 'active' ? 'còn hàng' : 'hết hàng'; ?>
                </div>
                <?php if($fetch_product['image']!=''){?>
                    <img src="../uploaded_files/<?= $fetch_product['image']; ?>" class="image">
                <?php } ?>
               <div class="price">
                <?php
                // Giả sử $fetch_product['price'] chứa giá sản phẩm (ví dụ: 1500000)
                $price = $fetch_product['price'];

                // Định dạng số theo kiểu Việt Nam Đồng
                $formatted_price = number_format($price, 0, ',', '.');

                echo $formatted_price . " VNĐ";
                ?>
                </div>
                <div class="title"><?=$fetch_product['name']; ?></div>
                <div class="content"><?=$fetch_product['product_detail']; ?></div>
                <div class="flex-btn">
                    <a href="edit_product.php?id=<?= $fetch_product['id'];?>" class="btn">sửa</a>
                    <button type="submit" name="delete" class="btn" onclick="return confirm
                        ('xóa sản phẩm này');">xóa</button>
                    <a href="view_products.php?post_id=<?= $fetch_product['id'];?>" class="btn">
                    trở về</a>
            </form>
            <?php
                    }
                }else{
                    echo'
                    <div class="empty">
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
