<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = 'login.php';
}
include 'components/add_wishlist.php';
include 'components/add_cart.php';

if (isset($_POST['delete_item'])) {
    $wishlist_id = $_POST['wishlist_id'];
    $wishlist_id = filter_var($wishlist_id, FILTER_SANITIZE_STRING);

    $verify_delete = $conn->prepare("SELECT * FROM `wishlist` WHERE id = ?");
    $verify_delete->execute([$wishlist_id]);

    if ($verify_delete->rowCount() > 0) {
        $delete_wishlist = $conn->prepare("DELETE FROM `wishlist` WHERE id = ?");
        $delete_wishlist->execute([$wishlist_id]);
        $success_msg[] = 'Sản phẩm được xóa khỏi danh sách';
    } else {
        $warning_msg[] = 'Mục danh sách yêu thích đã bị xóa';
    }
}


?>




<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <title>Danh sách yêu thích</title>
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Danh sách yêu thích</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <span><a href="home.php">Trang Chủ</a><i class="bx bx-right-arrow-alt"></i>Danh sách yêu thích</span>
        </div>
    </div>

    <section class="products">
        <div class="heading">
            <h1>Sản phẩm được thêm vào danh sách yêu thích</h1>
            <img src="image/separator.png">
        </div>
        <div class="box-container">
            <?php
            $grand_total = 0;

            $select_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id=?");
            $select_wishlist->execute([$user_id]);

            if ($select_wishlist->rowCount() > 0) {
                while ($fetch_wishlist = $select_wishlist->fetch((PDO::FETCH_ASSOC))) {
                    $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                    $select_products->execute([$fetch_wishlist['product_id']]);

                    if ($select_products->rowCount() > 0) {
                        $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
                        ?>
                        <form action="" method="post" class="box <?php if ($fetch_products['stock'] == 0) {
                            echo 'disabled';
                        }
                        ; ?>">
                            <input type="hidden" name="wishlist_id" value="<?= $fetch_wishlist['id']; ?>">
                            <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                            <?php if ($fetch_products['stock'] > 5) { ?>
                                <span class="stock" style="color: green;">Còn Hàng</span>
                            <?php } elseif ($fetch_products['stock'] == 0) { ?>
                                <span class="stock" style="color: red;">Hết Hàng</span>
                            <?php } else { ?>
                                <span class="stock" style="color: red;">Chỉ còn <?= $fetch_products['stock'];
                                ?> sản phẩm</span>
                            <?php } ?>
                            <div class="flex">
                                <p class="price"> <?= number_format($fetch_products['price'], 0, ',', '.') . ' VNĐ'; ?></p>

                            </div>
                            <div class="content">
                                <div class="button">
                                    <div>
                                        <h3 class="name"><?= $fetch_products['name']; ?></h3>
                                    </div>
                                    <div>
                                        <button type="submit" name="add_to_cart"><i class="bx bx-cart">
                                            </i></button>
                                        <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="bx bxs-show"></a>
                                        <button type="submit" name="delete_item" onclick="return
                                confirm('Xóa sản phẩm');"><i class="bx bx-x"></i>
                                        </button>
                                    </div>
                                </div>
                                <input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
                                <div class="flex">
                                    <input type="hidden" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
                                    <a href="checkout.php?get_id=<?= $fetch_products['id']; ?>" class="
                        btn" style="width: 100% !important;">Mua ngay</a>
                                </div>
                            </div>
                        </form>
                        <?php
                    }
                }
            } else {
                echo '
                        <div class="empty">
                            <p>không có sản phẩm nào được thêm </p>
                        </div>
                    ';
            }
            ?>
        </div>
    </section>

    <?php include 'components/user_footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>

</body>

</html>