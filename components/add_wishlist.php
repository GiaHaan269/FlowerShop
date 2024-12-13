<?php

    if(isset($_POST['add_to_wishlist'])){
        if($user_id != ''){
            $id = unique_id();
            $product_id = $_POST['product_id'];

            $varify_wishlist = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?
                AND product_id = ?");
            $varify_wishlist->execute([$user_id, $product_id]);

            

            $cart_num = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id = ?
            AND product_id = ?");
            $cart_num->execute([$user_id, $product_id]);

            if($varify_wishlist->rowCount() >0){
                $warning_msg[] = 'sản phẩm đã có trong danh sách yêu thích';
            }else if($cart_num->rowCount() > 0){
                $warning_msg[] = 'sản phẩm đã có trong giỏ hàng';
            }else{
                $select_price = $conn->prepare("SELECT * FROM `products` WHERE id = ? LIMIT
                 1");
                $select_price->execute([$product_id]);
                $fetch_price = $select_price->fetch(PDO::FETCH_ASSOC);

                $insert_wishlist = $conn->prepare("INSERT INTO `wishlist`(id, user_id,product_id, price) VALUES(?,?,?,?)");
                $insert_wishlist->execute([$id, $user_id, $product_id, $fetch_price['price']]);
                $success_msg[] = 'sản phẩm được thêm vào danh sách yêu thích';
            }
        }else{
            $warning_msg[] = 'vui lòng đăng nhập';
        }
    }
?>