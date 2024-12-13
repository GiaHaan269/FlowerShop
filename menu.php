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
    <title>Cửa Hàng</title>
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Sản phẩm</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <span><a href="home.php">Trang chủ</a><i class="bx bx-right-arrow-alt"></i>Sản phẩm</span>
        </div>
    </div>
    <div class="heading">
        <h1>Cửa Hàng</h1>
        <img src="image/separator.png">
    </div>
    <div class="products">
        <div class="box-container">
            <?php
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE status = ? ");
            $select_products->execute(['active']);

            if ($select_products->rowCount() > 0) {
                while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)) {
                    $product_name = $fetch_products['name'];
                    $product_id = $fetch_products['id'];

                    // Truy vấn bảng nhap_hang để lấy gia_nhap dựa trên ten_hang
                    $select_purchase_price = $conn->prepare("SELECT gia_nhap FROM `nhap_hang` WHERE ten_hang = ? ORDER BY id DESC LIMIT 1");
                    $select_purchase_price->execute([$product_name]);
                    $purchase_price = $select_purchase_price->fetch(PDO::FETCH_ASSOC);
                    $gia_nhap = $purchase_price ? $purchase_price['gia_nhap'] : 0;

                    // Cập nhật giá nhập vào bảng products
                    $update_product_price = $conn->prepare("UPDATE `products` SET gia_nhap = ? WHERE id = ?");
                    $update_product_price->execute([$gia_nhap, $product_id]);

                    ?>
                    <form action="" method="post" class="box" <?php if ($fetch_products['stock'] == 0) {
                        echo 'disabled';
                    } ?>>
                        <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                        <?php if ($fetch_products['stock'] > 5) { ?>
                            <span class="stock" style="color: green;">Còn hàng</span>
                        <?php } elseif ($fetch_products['stock'] == 0) { ?>
                            <span class="stock" style="color: red;">Hết hàng</span>
                        <?php } else { ?>
                            <span class="stock" style="color: red;">Chỉ còn <?= $fetch_products['stock']; ?> sản phẩm</span>
                        <?php } ?>

                        <p class="price"> <?= number_format($fetch_products['price'], 0, ',', '.') . ' VNĐ'; ?></p>

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
                                <!-- <a href="checkout.php?get_id=<?= $fetch_products['id']; ?>" class="btn">Mua Ngay</a> -->
                                <input type="number" name="qty" required min="1" value="1" max="99" maxlength="2" class="qty">
                            </div>
                        </div>
                    </form>

                    <?php
                }
            } else {
                echo '
                        <div class="empty">
                            <p>Không có sản phẩm được thêm!</p>
                        </div>
                    ';
            }
            ?>
        </div>
    </div>

    <?php include 'components/user_footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>
    <!-- <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
    <df-messenger intent="WELCOME" chat-title="chào" agent-id="f0597888-4dac-4b4c-8f8f-7bea88eb090d" language-code="en"
        chat-icon="uploaded_files/robot.jpg"></df-messenger> -->
</body>

</html>