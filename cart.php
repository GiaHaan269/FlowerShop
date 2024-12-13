<?php
    include 'components/connect.php';

    if(isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = 'login.php';
    }
    

    if(isset($_POST['delete_item'])){
        $cart_id = $_POST['cart_id'];
        $cart_id = filter_var($cart_id, FILTER_SANITIZE_STRING);

        $verify_delete = $conn->prepare("SELECT * FROM `cart` WHERE id = ?");
        $verify_delete->execute([$cart_id]);

        if($verify_delete->rowCount() > 0 ){
            $delete_cart = $conn->prepare("DELETE FROM `cart` WHERE id = ?");
            $delete_cart->execute([$cart_id]);
            $success_msg[] = 'sản phẩm được xóa khỏi danh sách';
        }else{
            $warning_msg[] = 'mục trong giỏ hàng đã bị xóa';
        }
    }

if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $cart_id = filter_var($cart_id, FILTER_SANITIZE_STRING);

    $qty = $_POST['qty'];
    $qty = filter_var($qty, FILTER_SANITIZE_STRING);

    // Lấy thông tin sản phẩm từ bảng `cart` và `products`
    $get_product = $conn->prepare("
        SELECT p.stock 
        FROM `cart` c 
        JOIN `products` p ON c.product_id = p.id 
        WHERE c.id = ?
    ");
    $get_product->execute([$cart_id]);
    $product = $get_product->fetch(PDO::FETCH_ASSOC);

    // Kiểm tra tồn kho
    if ($product['stock'] <= 0) {
        echo "
            <script>
                alert('Sản phẩm đã hết hàng. Không thể cập nhật số lượng.');
                window.history.back();
            </script>
        ";
        exit();
    }

    if ($qty <= 0) {
        echo "
            <script>
                alert('Số lượng phải lớn hơn 0. Vui lòng nhập lại.');
                window.history.back();
            </script>
        ";
        exit();
    }

    if ($qty > $product['stock']) {
        echo "
            <script>
                alert('Số lượng yêu cầu vượt quá số lượng tồn kho hiện tại. Vui lòng giảm số lượng.');
                window.history.back();
            </script>
        ";
        exit();
    }

    // Cập nhật số lượng trong giỏ hàng
    $update_qty = $conn->prepare("UPDATE `cart` SET qty = ? WHERE id = ?");
    $update_qty->execute([$qty, $cart_id]);

    $success_msg[] = 'Số lượng trong giỏ hàng được cập nhật';
}


if(isset($_POST['empty_cart'])){
        $verify_empty_item = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
        $verify_empty_item->execute([$user_id]);

        if($verify_empty_item->rowCount() > 0){
            $delete_cart_item = $conn->prepare("DELETE FROM `cart` WHERE user_id = ?");
            $delete_cart_item->execute([$user_id]);
            $success_msg[] = 'giỏ hàng trống';
        }else{
            $warning_msg[] ='giỏ hàng của bạn đã trống';
        }
    }

    
?>




<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name = "viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <title>Giỏ Hàng</title>
</head>
<body>
    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Giỏ Hàng</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <span><a href="home.php">Trang chủ</a><i class="bx bx-right-arrow-alt"></i>Giỏ Hàng</span>
        </div>
    </div>

    <section class="products">
        <div class="heading">
            <h1>Sản phẩm được thêm</h1>
            <img src="image/separator.png">
        </div>
        <div class="box-container">
            <?php 
                $grand_total =0;

                $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                $select_cart->execute([$user_id]);

                if($select_cart->rowCount() > 0){
                    while($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)){
                        $select_products = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                        $select_products->execute([$fetch_cart['product_id']]);

                        if($select_products->rowCount() > 0){
                            $fetch_products = $select_products->fetch(PDO::FETCH_ASSOC);
            ?>
            <form action="" method="post" class="box <?php if($fetch_products['stock'] == 0)
                {echo 'disabled';} ?>">
                <input type="hidden" name="cart_id" value="<?= $fetch_cart['id'];?>">
                <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image">
                <?php if($fetch_products['stock'] > 5 ){ ?>
                    <span class="stock" style ="color: green;">Còn hàng</span>
                <?php }elseif($fetch_products['stock'] == 0){ ?>
                    <span class="stock" style ="color: red;">Hết hàng</span>
                <?php }else{ ?>
                    <span class="stock" style ="color: red;">Chỉ còn <?= $fetch_products['stock'];
                     ?> sản phẩm</span>
                <?php } ?>
                <p class="price"> <?= number_format($fetch_products['price'], 0, ',', '.') . ' VND'; ?></p>

                <div class="content cart-content">
                    <div class="flex-btn">
                        <h3 class="name"><?= $fetch_products['name']; ?></h3>
                        <p class="sub-total">Tổng : <?= number_format($sub_total = $fetch_cart['qty'] * $fetch_cart['price'], 0, ',', '.') ?>
                        </p>

                    </div>
                    <div class="flex-btn">
                        <input type="number" name="qty" required min="1" value="<?= 
                        $fetch_cart['qty']; ?>" max="99" maxlength="2" class="qty">
                        <button type="submit" name="update_cart" class="bx bxs-edit fa-edit
                        "style="box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.40)"> </button>
                        <button type="submit" name="delete_item" onclick="return confirm('xóa sản phẩm');"
                        class="btn">Xóa sản phẩm</button>
                    </div>
                </div>
            </form>
            <?php
                        $grand_total+= $sub_total;
                        }else{
                            echo'
                                <div class="empty">
                                    <p>không tìm thấy sản phẩm</p>
                                </div>
                            ';
                        }
                    }
                }else{
                    echo'
                        <div class="empty">
                            <p>không có sản phẩm nào </p>
                        </div>
                    ';
                }
            ?>
            
        </div>
        <?php
            if($grand_total !=0){
        ?>
            <div class="cart-total">
                <p>Tổng tiền phải trả : <span><?= number_format($grand_total, ) ?> VNĐ </span></p>

                <div class="button">
                    <form action="" method="post">
                        <button type="submit" name="empty_cart" onclick="return confirm
                        ('bạn có muốn xóa sản phẩm này');" class="btn">Giỏ hàng trống</button>
                        <a href="checkout.php" class="btn">Thanh Toán</a>
                    </form>
                </div>
            </div>
        <?php } ?>
    </section>
    
    <?php include 'components/user_footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>
    
</body>
    
</html>