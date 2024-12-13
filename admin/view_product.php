<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
}

if (isset($_POST['delete'])) {

    $p_id = $_POST['product_id'];
    $p_id = filter_var($p_id, FILTER_SANITIZE_STRING);

    $delete_product = $conn->prepare("DELETE FROM `products` WHERE id=?");
    $delete_product->execute([$p_id]);

    $success_msg[] = 'sản phẩm đã được xóa thành công';
}

$select_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ? AND status = 'deactive'");
$select_products->execute([$seller_id]);



?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <title>Quản Trị - Xem Sản Phẩm</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <!-- <div class="banner">
        <div class="detail">
            <h1>sản phẩm</h1>

            <span><a href="dashboard.php">Quản Trị</a><i class="bx bx-right-arrow-alt"></i>sản phẩm</span>
        </div>
    </div> -->
    <section class="show_products">
        <div class="heading">
            <h1>sản phẩm</h1>
            <img src="../image/separator.png">
        </div>
        <div class="box-container">
            <?php
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ? AND status = 'deactive'");

            $select_products->execute([$seller_id]);


            if ($select_products->rowCount() > 0) {
                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <form action="" method="post" class="box">
                        <input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
                        <?php if ($fetch_products['image'] != '') { ?>
                            <img src="../uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                        <?php } ?>

                        <div class="status" style="color: <?php if ($fetch_products['status'] == 'active') {
                            echo "limegreen";
                        } else {
                            echo "coral";
                        } ?>">
                            <?= $fetch_products['status'] == 'active' ? 'còn hàng' : 'hết hàng'; ?>
                        </div>
                        <p class="price"><?= number_format($fetch_products['price'], 0, ',', '.'); ?></p>
                        <div class="content">
                            <div class="title"><?= $fetch_products['name']; ?></div>
                            <div class="flex-btn">
                                <a href="edit_product.php?id=<?= $fetch_products['id']; ?>" class="btn">sửa</a>
                                <button type="submit" name="delete" class="btn" onclick="confirm('Xóa sản phẩm này');"
                                    class="btn">xóa</button>
                                <a href="read_product.php?post_id=<?= $fetch_products['id']; ?>" class="btn">chi tiết</a>
                            </div>
                        </div>
                    </form>
                    <?php
                }
            } else {
                echo '
                        <div class="empty">
                            <p>không có sản phẩm nào được thêm! <br> <a href="add_product.php" class="btn style="margin-top:
                            1rem;">thêm sản phẩm</a></p>
                        </div
                    ';
            }
            ?>
        </div>

    </section>
   
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>