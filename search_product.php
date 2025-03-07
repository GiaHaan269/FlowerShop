<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

include 'components/add_wishlist.php';
include 'components/add_cart.php';






?>




<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <title>Tìm Kiếm Sản Phẩm</title>
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Tìm Kiếm Sản Phẩm</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <span><a href="home.php">home</a><i class="bx bx-right-arrow-alt"></i>Tìm Kiếm Sản Phẩm</span>
        </div>
    </div>

    <section class="products">
        <div class="heading">
            <h1>kết quả tìm kiếm</h1>
            <img src="image/separator.png">
        </div>
        <div class="box-container">
            <?php
            if (isset($_POST['search_product']) or isset($_POST['search_product_btn'])) {
                $search_products = $_POST['search_product'];
                $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ? AND status = ?");
                $search_term = "%" . $search_products . "%";
                $select_products->execute([$search_term, 'active']);

                if ($select_products->rowCount() > 0) {
                    while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                        $product_id = $fetch_products['id'];

                        ?>
                        <form action="" method="post" class="box" <?php if ($fetch_products['stock'] == 0) {
                            echo 'disabled';
                        } ?>>
                            <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                            <?php if ($fetch_products['stock'] > 9) { ?>
                                <span class="stock" style="color:green;">Còn Hàng</span>
                            <?php } elseif ($fetch_products['stock'] == 0) { ?>
                                <span class="stock" style="color:red;">Hết Hàng</span>
                            <?php } else { ?>
                                <span class="stock" style="color:red;">Chỉ còn <?= $fetch_products['stock']; ?> sản phẩm</span>
                            <?php } ?>
                            <p class="price"> <?= number_format($fetch_products['price'], 0, ',', '.'); ?> VNĐ</p>

                            <div class="content">
                                <div class="button">
                                    <div>
                                        <h3><?= $fetch_products['name']; ?></h3>
                                    </div>
                                    <div>
                                        <button type="submit" name="add_to_cart"><i class="bx bx-cart"></i></button>
                                        <button type="submit" name="add_to_wishlist"><i class="bx bx-heart"></i></button>
                                        <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="bx bxs-show"></a>
                                    </div>
                                </div>
                                <input type="hidden" name="product_id" value="<?= $fetch_products['id']; ?>">
                                <div class="flex-btn">
                                    <a href="checkout.php?get_id=<?= $fetch_products['id']; ?>" class="btn">Mua ngay</a>
                                    <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
                                </div>
                            </div>
                        </form>
                        <?php
                    }
                } else {
                    echo '
                                <div class="empty">
                                    <p>không tìm thấy sản phẩm</p>
                                </div>
                            ';
                }
            } else {
                echo '
                            <div class="empty">
                                <p>không có sản phẩm</p>
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