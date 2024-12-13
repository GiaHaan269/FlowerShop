<?php
if (isset($_POST['add_to_cart'])) {
    if ($user_id != '') {
        $id = unique_id(); // Tạo id giỏ hàng mới (sử dụng id sản phẩm khi cần)
        $product_id = $_POST['product_id'];
        $qty = $_POST['qty'];
        $qty = filter_var($qty, FILTER_SANITIZE_STRING);

        // Kiểm tra tồn kho của sản phẩm
        $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT 1");
        $select_product->execute([$product_id]);
        $product = $select_product->fetch(PDO::FETCH_ASSOC);

        // Nếu sản phẩm không tồn tại
        if (!$product) {
            $warning_msg[] = 'Sản phẩm không tồn tại';
        } else {
            // Kiểm tra xem số lượng yêu cầu có vượt quá tồn kho không
            if ($qty > $product['stock']) {
                $warning_msg[] = 'Số lượng yêu cầu vượt quá số lượng tồn kho. Vui lòng giảm số lượng.';
            } else {
                // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
                $varify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ? AND product_id = ?");
                $varify_cart->execute([$user_id, $product_id]);

                // Kiểm tra số lượng sản phẩm trong giỏ hàng
                $max_cart_items = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
                $max_cart_items->execute([$user_id]);

                if ($varify_cart->rowCount() > 0) {
                    // Nếu sản phẩm đã có trong giỏ hàng, cập nhật số lượng
                    $update_cart = $conn->prepare("UPDATE `cart` SET qty = qty + ? WHERE user_id = ? AND product_id = ?");
                    $update_cart->execute([$qty, $user_id, $product_id]);
                    $success_msg[] = 'Sản phẩm đã được cập nhật vào giỏ hàng';
                } else if ($max_cart_items->rowCount() >= 20) {
                    // Nếu giỏ hàng đã đầy
                    $warning_msg[] = 'Giỏ hàng đầy';
                } else {
                    // Nếu không có lỗi, thêm sản phẩm vào giỏ hàng
                    $insert_cart = $conn->prepare("INSERT INTO `cart` (id, user_id, product_id, price, qty) VALUES(?,?,?,?,?)");
                    $insert_cart->execute([$id, $user_id, $product_id, $product['price'], $qty]);
                    $success_msg[] = 'Sản phẩm đã được thêm vào giỏ hàng';
                }
            }
        }
    } else {
        $warning_msg[] = 'Vui lòng đăng nhập';
    }
}
?>