<?php
include 'components/connect.php';
// require 'mail/sendmail.php';
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:login.php');
}

// $select_user_points = $conn->prepare("SELECT points FROM `users` WHERE id = ?");
// $select_user_points->execute([$user_id]);
// $user_points = $select_user_points->fetchColumn();

// Kiểm tra cookie và lấy thông tin người dùng
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];

    // Truy vấn thông tin người dùng từ bảng users
    $get_user_info = $conn->prepare("SELECT * FROM `users` WHERE id = ?");
    $get_user_info->execute([$user_id]);

    if ($get_user_info->rowCount() > 0) {
        $user_info = $get_user_info->fetch(PDO::FETCH_ASSOC);
        $name = $user_info['name'];
        $email = $user_info['email'];
        $phone = $user_info['phone'];
    } else {
        // Nếu không tìm thấy người dùng, chuyển hướng về trang đăng nhập
        header('location:login.php');
    }
} else {
    // Nếu không có cookie user_id, chuyển hướng về trang đăng nhập
    header('location:login.php');
}

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'F:/xampp/htdocs/flowershop/mail/PHPMailer/src/Exception.php';
require 'F:/xampp/htdocs/flowershop/mail/PHPMailer/src/PHPMailer.php';
require 'F:/xampp/htdocs/flowershop/mail/PHPMailer/src/SMTP.php';

if (isset($_POST['place_order'])) {
    if ($user_id != '') {
        $id = unique_id();
        $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_STRING);
        $number = filter_var($_POST['number'], FILTER_SANITIZE_STRING);
        $address = filter_var($_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['phuong'] . ', ' . $_POST['quan'] . ', ' . $_POST['country'], FILTER_SANITIZE_STRING);
        $address_type = filter_var($_POST['address_type'], FILTER_SANITIZE_STRING);
        $method = filter_var($_POST['method'], FILTER_SANITIZE_STRING);
        $shipping = filter_var($_POST['shipping'], FILTER_SANITIZE_STRING);


        // Tính phí vận chuyển dựa trên loại hình vận chuyển
        $shipping_fee = ($shipping === "Vận chuyển tiêu chuẩn") ? 30000 : 50000;

        // Kiểm tra giỏ hàng
        $verify_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id = ?");
        $verify_cart->execute([$user_id]);

        if ($verify_cart->rowCount() > 0) {
            $order_total = 0; // Khởi tạo tổng tiền
        //    if ($method = "Thanh toán MOMO ATM"){
        //         while ($f_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)) {
        //             $s_products = $conn->prepare("SELECT * FROM `products` WHERE id =? LIMIT 1");
        //             $s_products->execute([$f_cart['product_id']]);
        //             $f_product = $s_products->fetch(PDO::FETCH_ASSOC);


        //             $seller_id = $f_product['seller_id'];

        //             // Tính điểm tích lũy dựa trên tổng tiền
        //             // $points_earned = floor($order_total / 100000) * 10;
        //             // // Tính tổng tiền cho từng sản phẩm
        //             // $total_price = $f_cart['price'] * $f_cart['qty'];
        //             // $order_total += $total_price;


        //             $update_points = $conn->prepare("UPDATE `users` SET points = points + ? WHERE id = ?");
        //             $update_points->execute([$points_earned, $user_id]);


        //             // Lấy số điểm tích lũy của người dùng từ bảng users
        //             $select_user_points = $conn->prepare("SELECT points FROM `users` WHERE id = ?");
        //             $select_user_points->execute([$user_id]);
        //             $user_points = $select_user_points->fetchColumn();

        //             // $discount = 0;
        //             // if (!isset($_SESSION['total']) || $_SESSION['order_id'] !== $current_order_id) {
        //             //     if ($user_points >= 100) {
        //             //         $discount = floor($user_points / 100) * 100000;

        //             //         $_SESSION['total'] = true;
        //             //         // $_SESSION['order_id'] = $current_order_id;
        //             //     }
        //             // }
        //             $total_price = $f_cart['price'] * $f_cart['qty'];
        //             $order_total += $total_price;
        //             $insert_order = $conn->prepare("INSERT INTO `orders` 
        //         (id, user_id, seller_id, name, number, email, address, address_type, method, hienthi,
        //         product_id, price, qty, shipping_fee) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
        //             $insert_order->execute([
        //                 $id,
        //                 $user_id,
        //                 $seller_id,
        //                 $name,
        //                 $number,
        //                 $email,
        //                 $address,
        //                 $address_type,
        //                 $method, 0,
        //                 $f_cart['product_id'],
        //                 $f_cart['price'],
        //                 $f_cart['qty'],
        //                 $shipping_fee 
        //             ]);

        //             // Chèn vào bảng order_items
        //             $insert_order_item = $conn->prepare("INSERT INTO `order_items`
        //             (order_item_id, order_id, product_id, product_name, product_image, quantity, price, total_price)
        //             VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        //             $insert_order_item->execute([unique_id(), $id, $f_cart['product_id'], $f_product['name'], $f_product['image'], $f_cart['qty'], $f_cart['price'], $total_price]);

        //             // Cập nhật stock trong bảng products
        //             $update_stock = $conn->prepare("UPDATE `products` SET stock = stock - ? WHERE id = ?");
        //             $update_stock->execute([$f_cart['qty'], $f_cart['product_id']]);
        //         }
                
        //         // $mail = new PHPMailer(true);
        //         // try {
        //         //     $mail->isSMTP();
        //         //     $mail->Host = 'smtp.gmail.com';
        //         //     $mail->SMTPAuth = true;
        //         //     $mail->Username = 'vanilashop65@gmail.com'; 
        //         //     $mail->Password = 'wgtu zyaf fgfm gkvx'; 
        //         //     $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        //         //     $mail->Port = 587;

        //         //     $mail->setFrom('vanilashop65@gmail.com', 'Vanila Shop');
        //         //     $mail->addAddress($email); 
        //         //     $mail->isHTML(true);
        //         //     $mail->CharSet = 'UTF-8';
        //         //     $mail->Subject = 'Xác nhận đơn hàng #' . $id;


        //         //     $mail->Body .= "<p>Cảm ơn bạn đã đặt hàng tại VanilaShop.</p>";
        //         //     $mail->Body .= '<p>Đơn hàng <strong>#' . htmlspecialchars($id) . ' </strong> của bạn đã được đặt thành công vào ngày ' . date('d/m/Y') . '.</p>';


        //         //     $mail->Body .= "<li><strong>Tổng tiền hàng:</strong> " . number_format($order_total) . "VNĐ</li>";
        //         //     $mail->Body .= "<li><strong>Giảm giá:</strong> " . number_format($discount) . "VNĐ</li>";
        //         //     $mail->Body .= "<li><strong>Phí vận chuyển:</strong> " . number_format($shipping_fee) . "VNĐ</li>";
        //         //     $mail->Body .= '<li><strong>Địa chỉ giao hàng:</strong> ' . htmlspecialchars($address) . '</li>';
        //         //     $mail->Body .= "<p>Đơn hàng sẽ được giao đến địa chỉ của bạn trong thời gian sớm nhất.</p>";
        //         //     $mail->Body .= "<p>Cảm ơn bạn đã tin tưởng và mua hàng!</p>";

        //         //     $mail->send();
        //         //     echo 'Email xác nhận đã được gửi!';
        //         // } catch (Exception $e) {
        //         //     echo "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
        //         // }


        //         // Xóa giỏ hàng
        //         // $delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE user_id =?");
        //         // $delete_cart_id->execute([$user_id]);
        //         header("location:thanhtoanmomoatm.php?id=" . urlencode($id));
        //     } else {
                while ($f_cart = $verify_cart->fetch(PDO::FETCH_ASSOC)) {
                    $s_products = $conn->prepare("SELECT * FROM `products` WHERE id =? LIMIT 1");
                    $s_products->execute([$f_cart['product_id']]);
                    $f_product = $s_products->fetch(PDO::FETCH_ASSOC);


                    $seller_id = $f_product['seller_id'];

                
                    // Tính tổng tiền cho từng sản phẩm
                    $total_price = $f_cart['price'] * $f_cart['qty'];
                    $order_total += $total_price;

                    // Chèn đơn hàng vào bảng orders
                    $insert_order = $conn->prepare("INSERT INTO `orders` 
                (id, user_id, seller_id, name, number, email, address, address_type, method,
                product_id, price, qty, shipping_fee,tong_thanh_toan) VALUES(?,?,?,?,?,?,?,?,?,?,?,?,?,?)");
                    $insert_order->execute([
                        $id,
                        $user_id,
                        $seller_id,
                        $name,
                        $number,
                        $email,
                        $address,
                        $address_type,
                        $method,
                        $f_cart['product_id'],
                        $f_cart['price'],
                        $f_cart['qty'],
                        $shipping_fee,
                        
                        $order_total
                    ]);

                    // Chèn vào bảng order_items
                    $insert_order_item = $conn->prepare("INSERT INTO `order_items`
                    (order_item_id, order_id, product_id, product_name, product_image, quantity, price, total_price)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
                    $insert_order_item->execute([unique_id(), $id, $f_cart['product_id'], $f_product['name'], $f_product['image'], $f_cart['qty'], $f_cart['price'], $total_price]);

                    // Cập nhật stock trong bảng products
                    $update_stock = $conn->prepare("UPDATE `products` SET stock = stock - ? WHERE id = ?");
                    $update_stock->execute([$f_cart['qty'], $f_cart['product_id']]);
                }

    //             if ($product['stock'] <= 0) { // Kiểm tra nếu tồn kho nhỏ hơn hoặc bằng 0
    //                 echo "
    //     <script>
    //         alert('Sản phẩm \"{$product['name']}\" đã hết hàng. Vui lòng chọn sản phẩm khác.');
    //         window.history.back(); // Quay lại trang trước
    //     </script>
    // ";
    //                 exit();
    //             }
                
                // Gửi email xác nhận (không dùng hàm)
                $mail = new PHPMailer(true);
                try {
                    // Cấu hình SMTP
                    $mail->isSMTP();
                    $mail->Host = 'smtp.gmail.com';
                    $mail->SMTPAuth = true;
                    $mail->Username = 'vanilashop65@gmail.com'; // Email gửi đi
                    $mail->Password = 'wgtu zyaf fgfm gkvx'; // Mật khẩu email
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                    $mail->Port = 587;

                    // Thiết lập người gửi và người nhận
                    $mail->setFrom('vanilashop65@gmail.com', 'Vanila Shop');
                    $mail->addAddress($email); // Địa chỉ email của khách hàng

                    // Cấu hình nội dung email
                    $mail->isHTML(true);
                    $mail->CharSet = 'UTF-8';
                    $mail->Subject = 'Xác nhận đơn hàng #' . $id;


                    $mail->Body .= "<p>Cảm ơn bạn đã đặt hàng tại VanilaShop.</p>";
                    $mail->Body .= '<p>Đơn hàng <strong>#' . htmlspecialchars($id) . ' </strong> của bạn đã được đặt thành công vào ngày ' . date('d/m/Y') . '.</p>';


                    $mail->Body .= "<li><strong>Tổng tiền hàng:</strong> " . number_format($order_total) . "VNĐ</li>";
                    $mail->Body .= "<li><strong>Giảm giá:</strong> " . number_format($discount) . "VNĐ</li>";
                    $mail->Body .= "<li><strong>Phí vận chuyển:</strong> " . number_format($shipping_fee) . "VNĐ</li>";
                    $mail->Body .= '<li><strong>Địa chỉ giao hàng:</strong> ' . htmlspecialchars($address) . '</li>';
                    $mail->Body .= "<p>Đơn hàng sẽ được giao đến địa chỉ của bạn trong thời gian sớm nhất.</p>";
                    $mail->Body .= "<p>Cảm ơn bạn đã tin tưởng và mua hàng!</p>";

                    $mail->send();
                    echo 'Email xác nhận đã được gửi!';
                } catch (Exception $e) {
                    echo "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
                }


                // Xóa giỏ hàng
                $delete_cart_id = $conn->prepare("DELETE FROM `cart` WHERE user_id =?");
                $delete_cart_id->execute([$user_id]);

                // Chuyển hướng đến trang "Đơn hàng" hoặc trang xử lý thanh toán
                header("location:order.php");
           }
            

            // if ($method == "Thanh toán MOMO QRcode") {
            //     header("Location: xulythanhtoanmomo.php?order_id=$id");
            //     exit;
            // } elseif ($method == "Thanh toán MOMO ATM") {
            //     header("Location: thanhtoanmomoatm.php?order_id=$id");
            //     exit;
            // } else {
            //     header("Location: order.php");
            //     exit;
            // }
        // } else if (isset($_GET['get_id'])) {
        //     // Nếu có `get_id` trên URL, lấy sản phẩm từ bảng `products` với `get_id`
        //     $get_id = $_GET['get_id'];
        //     $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
        //     $select_product->execute([$get_id]);

        //     // Lấy số điểm tích lũy của người dùng từ bảng users
        //     $select_user_points = $conn->prepare("SELECT points FROM `users` WHERE id = ?");
        //     $select_user_points->execute([$user_id]);
        //     $user_points = $select_user_points->fetchColumn();

    
            

            // if ($select_product->rowCount() > 0) {
            //     $product = $select_product->fetch(PDO::FETCH_ASSOC);

            //     // Tính tổng giá tiền của sản phẩm
            //     $total_price = $product['price'];
            //     $order_total = $total_price;
            //     // Tính số tiền giảm giá dựa trên điểm tích lũy
            //     $discount = 0;
            //     if ($user_points >= 100) {
            //         $discount = floor($user_points / 100) * 100000;
            //     }
                
            //     $total= $order_total - $discount;

                
            //     // Chèn dữ liệu vào bảng `orders`
            //     $insert_order = $conn->prepare("INSERT INTO `orders` 
            // (id, user_id, seller_id, name, number, email, address, address_type, method,
            // product_id, price, qty, shipping_fee,price_after,total) VALUES(?,?,?,?,?,?,?,?,?,?,?,?, ?,?,?)");
            //     $insert_order->execute([
            //         $id,
            //         $user_id,
            //         $product['seller_id'],
            //         $name,
            //         $number,
            //         $email,
            //         $address,
            //         $address_type,
            //         $method,
            //         $product['id'],
            //         $product['price'],
            //         1, // Số lượng là 1 vì sản phẩm này không nằm trong giỏ hàng
            //         $shipping_fee,
            //         $discount,
            //         $total
            //     ]);

            //     // Chèn chi tiết vào bảng `order_items`
            //     $insert_order_item = $conn->prepare("INSERT INTO `order_items`
            // (order_item_id, order_id, product_id, product_name, product_image, quantity, price, total_price)
            // VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            //     $insert_order_item->execute([
            //         unique_id(),
            //         $id,
            //         $product['id'],
            //         $product['name'],
            //         $product['image'],
            //         1,  // Số lượng là 1
            //         $product['price'],
            //         $total_price
            //     ]);

            //     if (1 > $product['stock']) { // Số lượng là 1 trong trường hợp này
            //         echo "
            // <script>
            //     alert('Số lượng yêu cầu của sản phẩm \"{$product['name']}\" vượt quá số lượng tồn kho. Vui lòng chọn sản phẩm khác.');
            //     window.history.back(); // Quay lại trang trước
            // </script>
            // ";
            //         exit;
            //     }
            //     // Cập nhật stock trong bảng products
            //     $update_stock = $conn->prepare("UPDATE `products` SET stock = stock - 1 WHERE id = ?");
            //     $update_stock->execute([$get_id]);

            //     // Gửi email xác nhận (không dùng hàm)
            //     $mail = new PHPMailer(true);
            //     try {
            //         // Cấu hình SMTP
            //         $mail->isSMTP();
            //         $mail->Host = 'smtp.gmail.com';
            //         $mail->SMTPAuth = true;
            //         $mail->Username = 'vanilashop65@gmail.com'; // Email gửi đi
            //         $mail->Password = 'wgtu zyaf fgfm gkvx'; // Mật khẩu email
            //         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            //         $mail->Port = 587;

            //         // Thiết lập người gửi và người nhận
            //         $mail->setFrom('vanilashop65@gmail.com', 'Vanila Shop');
            //         $mail->addAddress($email); // Địa chỉ email của khách hàng

            //         // Cấu hình nội dung email
            //         $mail->isHTML(true);
            //         $mail->CharSet = 'UTF-8';
            //         $mail->Subject = 'Xác nhận đơn hàng #' . $id;


            //         $mail->Body .= "<p>Cảm ơn bạn đã đặt hàng tại VanilaShop.</p>";
            //         $mail->Body .= '<p>Đơn hàng <strong>#' . htmlspecialchars($id) . ' </strong> của bạn đã được đặt thành công vào ngày ' . date('d/m/Y') . '.</p>';


            //         $mail->Body .= "<li><strong>Tổng tiền hàng:</strong> " . number_format($order_total) . "VNĐ</li>";
            //         $mail->Body .= "<li><strong>Giảm giá:</strong> " . number_format($discount) . "VNĐ</li>";
            //         $mail->Body .= "<li><strong>Phí vận chuyển:</strong> " . number_format($shipping_fee) . "VNĐ</li>";
            //         $mail->Body .= '<li><strong>Địa chỉ giao hàng:</strong> ' . htmlspecialchars($address) . '</li>';
            //         $mail->Body .= "<p>Đơn hàng sẽ được giao đến địa chỉ của bạn trong thời gian sớm nhất.</p>";
            //         $mail->Body .= "<p>Cảm ơn bạn đã tin tưởng và mua hàng!</p>";

            //         $mail->send();
            //         echo 'Email xác nhận đã được gửi!';
            //     } catch (Exception $e) {
            //         echo "Không thể gửi email. Lỗi: {$mail->ErrorInfo}";
            //     }


                // Chuyển hướng đến trang "Đơn hàng" hoặc trang xử lý thanh toán
            //     header("location:order.php");
            // } else {
            //     echo "Sản phẩm không tồn tại.";
            // }
            // if ($method == "Thanh toán MOMO QRcode") {
            //     header("Location: xulythanhtoanmomo.php?order_id=$id");
            //     exit;
            // } elseif ($method == "Thanh toán MOMO ATM") {
            //     header("Location: thanhtoanmomoatm.php?order_id=$id");
            //     exit;
            // } else {
            //     header("Location: order.php");
            //     exit;
            // }
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
    <script src="https://code.jquery.com/jquery-3.6.4.js"></script>
    <script src="js/app.js"></script>
    <title>Thanh toán</title>
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Thanh toán</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <span><a href="home.php">trang chủ</a><i class="bx bx-right-arrow-alt"></i>Thanh toán</span>
        </div>
    </div>
    <section class="checkout">
        <div class="heading">
            <h1>Thanh Toán</h1>
            <img src="image/separator.png">
        </div>
        <div class="row">
            <div class="form-container">
                <form action="" method="post" class="register">
                    <input type="hidden" name="p_id" value="<?= $get_id; ?>">
                    <h3>Chi tiết thanh toán</h3>
                    <div class="flex">
                        <div class="col">
                            <div class="input-field">
                                <p>Họ và Tên <span>*</span></p>
                                <input type="text" name="name" placeholder="nhập tên của bạn" maxlength="50" required
                                    class="box" value="<?= isset($name) ? $name : ''; ?>">
                            </div>
                            <div class="input-field">
                                <p>Số điện thoại <span>*</span></p>
                                <input type="number" name="number" placeholder="nhập số điện thoại của bạn"
                                    maxlength="50" required class="box" value="<?= isset($phone) ? $phone : ''; ?>">
                            </div>
                            <div class="input-field">
                                <p>Email <span>*</span></p>
                                <input type="email" name="email" placeholder="nhập email của bạn" maxlength="50"
                                    required class="box" value="<?= isset($email) ? $email : ''; ?>">
                            </div>
                            <div class="input-field">
                                <p>Hình thức thanh toán <span>*</span></p>
                                <select name="method" class="box">
                                    <option value="Thanh toán khi nhận hàng">Thanh toán khi nhận hàng</option>
                                    <!-- <option value="Thanh toán MOMO QRcode">Thanh toán MOMO QRcode</option> -->
                                    <option value="Thanh toán MOMO ATM">Thanh toán MOMO ATM</option>
                                </select>
                            </div>
                            <div class="input-field">
                                <p>Địa chỉ<span>*</span></p>
                                <select name="address_type" class="box">
                                    <option value="home">Nhà riêng</option>
                                    <option value="van phong">Văn phòng</option>
                                </select>
                            </div>
                            <!-- Hiển thị số điểm hiện tại của người dùng -->
                            <!-- <div class="input-field">
                                <p>Số tích lũy:</p>
                                <input type="text" class="box" value="<?= number_format($user_points); ?> điểm"
                                    disabled>
                            </div> -->
                        </div>
                        <div class="col">
                            <div class="input-field">
                                <p>Số nhà<span>*</span></p>
                                <input type="text" name="flat" placeholder="số nhà" maxlength="50" required class="box">
                            </div>
                            <div class="input-field">
                                <p>Tên đường<span>*</span></p>
                                <input type="text" name="street" placeholder="tên đường" maxlength="50" required
                                    class="box">
                            </div>
                            <div class="input-field">
                                <p>Phường/Xã<span>*</span></p>
                                <input type="text" name="phuong" placeholder="phường" maxlength="50" required
                                    class="box">
                            </div>
                            <div class="input-field">
                                <p>Quận/Huyện <span>*</span></p>
                                <input type="text" name="quan" placeholder="Quận" maxlength="50" required class="box">
                            </div>
                            <div class="input-field">
                                <p>Tên Tỉnh/Thành Phố<span>*</span></p>
                                <input type="text" name="country" placeholder="nhập tên thành phố" maxlength="50"
                                    required class="box">
                            </div>
                            <div class="input-field">
                                <p>Hình thức vận chuyển <span>*</span></p>
                                <select name="shipping" class="box">
                                    <option value="Vận chuyển tiêu chuẩn" <?= isset($_POST['shipping']) && $_POST['shipping'] == 'Vận chuyển tiêu chuẩn' ? 'selected' : ''; ?>>Vận chuyển
                                        tiêu chuẩn: 30.000</option>
                                    <option value="Vận chuyển nhanh" <?= isset($_POST['shipping']) && $_POST['shipping'] == 'Vận chuyển nhanh' ? 'selected' : ''; ?>>Vận chuyển nhanh:
                                        50.000</option>
                                </select>
                            </div>

                        </div>
                    </div>
                    <form action="" method="POST">
    
                        <button type="submit" name="place_order"class="btn">Thanh toán</button>
                    </form>

                </form>


            </div>
            <div class="summary">
                <h3>Giỏ hàng của tôi</h3>
                <div class="box-container">
                    <?php
                    $grand_total = 0;
                    $used_points = 0;
                    $discount = 0; // Khởi tạo biến giảm giá
                    
                    $shipping_fee = 0;
                    if (isset($_GET['get_id'])) {
                        $select_get = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
                        $select_get->execute([$_GET['get_id']]);

                        while ($fetch_get = $select_get->fetch(PDO::FETCH_ASSOC)) {
                            $sub_total = $fetch_get['price'];
                            $grand_total += $sub_total;

                            ?>
                            <div class="flex">
                                <img src="uploaded_files/<?= $fetch_get['image']; ?>" class="image">
                                <div>
                                    <h3 class="name"><?= $fetch_get['name']; ?></h3>
                                    <p class="price"><?= $fetch_get['price']; ?></p>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        $select_cart = $conn->prepare("SELECT * FROM `cart` WHERE user_id =?");
                        $select_cart->execute([$user_id]);

                        if ($select_cart->rowCount() > 0) {
                            while ($fetch_cart = $select_cart->fetch(PDO::FETCH_ASSOC)) {
                                $select_products = $conn->prepare("SELECT * FROM `products` WHERE id =?");
                                $select_products->execute([$fetch_cart['product_id']]);
                                $fetch_product = $select_products->fetch(PDO::FETCH_ASSOC);
                                $sub_total = ($fetch_cart['qty'] * $fetch_product['price']);
                                $grand_total += $sub_total;

                                ?>
                                <div class="flex">
                                    <img src="uploaded_files/<?= $fetch_product['image']; ?>" class="image">
                                    <div>
                                        <h3 class="name"><?= $fetch_product['name']; ?></h3>
                                        <p class="price"><?= number_format($fetch_product['price'], 0, ',', '.') . ''; ?> x
                                            <?=
                                                $fetch_cart['qty']; ?>
                                        </p>
                                    </div>
                                </div>
                                <?php
                            }
                        } else {
                            echo '
                                    <div class="empty">
                                        <p>không có sản phẩm nào được thêm</p>
                                    </div>
                                ';
                        }
                    }
                    // Sau khi tính toán grand_total, áp dụng logic giảm giá
                    // if ($user_points >= 100) {
                    //     // Chia số điểm cho 100 và lấy phần nguyên, sau đó nhân với 100,000
                    //     $discount = floor($user_points / 100) * 100000;
                    // }

                    // Tổng tiền sau giảm giá
                    $grand_total_after_discount = max(0, $grand_total - $discount);

                    $used_points = 0;
                    // Tính toán số điểm sử dụng
                    $used_points = floor($discount / 100000) * 100;  // Mỗi 100.000 VNĐ giảm tương ứng với 100 điểm
                    
                    // Cập nhật lại điểm trong bảng users (trừ điểm đã sử dụng và cộng điểm cho lần mua)
                    // $new_user_points = $user_points - $used_points;  // Trừ số điểm đã sử dụng
                    
                    // Cộng điểm cho đơn hàng hiện tại (ví dụ, 1 điểm = 1000 VNĐ)
                    // $earned_points = floor($grand_total_after_discount / 10000);  // Cộng 1 điểm cho mỗi 1000 VNĐ
                    // $new_user_points += $earned_points;  // Cộng điểm mới vào
                    
                    // Cập nhật số điểm mới vào bảng users
                    // $update_points = $conn->prepare("UPDATE `users` SET `points` = ? WHERE `id` = ?");
                    // $update_points->execute([$new_user_points, $user_id]);

                    
                    ?>
                </div>
                <div class="grand-total"><span>Thành tiền: </span> <?= number_format($grand_total, 0, ',', '.') ?> VNĐ</div>
                <!-- <div class="discount">
                    <span>Giảm giá dựa trên điểm tích lũy: </span>
                    <span class="value"><?= number_format($discount) . ' VNĐ'; ?></span>
                </div> -->

                <!-- <div class="grand-total"><span>Thành tiền (sau giảm giá): </span>
                    <?= number_format($grand_total_after_discount); ?> VNĐ
                </div> -->
                <!-- Display the shipping fee -->
                <div class="shipping-fee">
                    <span>Phí vận chuyển: </span>
                    <?php
                    // Set default shipping fee to 30,000 if no method is selected
                    $shipping_fee = 30000;
                    if (isset($_POST['shipping'])) {
                        $shipping = $_POST['shipping'];
                        $shipping_fee = ($shipping == 'Vận chuyển tiêu chuẩn') ? 30000 : 50000;
                    }
                    echo number_format($shipping_fee) . ' VNĐ';
                    ?>
                </div>

                <!-- Show the grand total including shipping fee -->
                <div class="total-with-shipping">
                    <span>Tổng thanh toán (bao gồm phí vận chuyển): </span>
                    <?= number_format($grand_total + $shipping_fee); ?> VNĐ
                </div>
                <!-- Displaying the shipping fee below the total -->

            </div>
    </section>


    <?php include 'components/user_footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>
    <script>
        // Lắng nghe sự kiện thay đổi phương thức vận chuyển
        document.querySelector('select[name="shipping"]').addEventListener('change', function () {
            let shippingFee = 0;

            // Lấy phí vận chuyển dựa trên lựa chọn
            if (this.value == 'Vận chuyển tiêu chuẩn') {
                shippingFee = 30000;
            } else if (this.value == 'Vận chuyển nhanh') {
                shippingFee = 50000;
            }

            let discount = <?= $discount; ?>; // Lấy giá trị giảm giá từ PHP
            let grandTotalAfterDiscount = <?= $grand_total_after_discount; ?>; // Tổng tiền sau giảm giá

            // Cập nhật hiển thị
            document.querySelector('.shipping-fee').innerHTML = '<span>Phí vận chuyển: </span>' + shippingFee.toLocaleString() + ' VNĐ';
            document.querySelector('.total-with-shipping').innerHTML = '<span>Tổng thanh toán (bao gồm phí vận chuyển): </span>' +
                (grandTotalAfterDiscount + shippingFee).toLocaleString() + ' VNĐ';
        });

        // Thiết lập giá trị mặc định khi tải trang
        document.addEventListener("DOMContentLoaded", function () {
            let shippingFee = 30000; // Phí vận chuyển mặc định
            let discount = <?= $discount; ?>; // Lấy giá trị giảm giá từ PHP
            let grandTotalAfterDiscount = <?= $grand_total_after_discount; ?>; // Tổng tiền sau giảm giá

            // Cập nhật hiển thị
            document.querySelector('.shipping-fee').innerHTML = '<span>Phí vận chuyển: </span>' + shippingFee.toLocaleString() + ' VNĐ';
            document.querySelector('.total-with-shipping').innerHTML = '<span>Tổng thanh toán (bao gồm phí vận chuyển): </span>' +
                (grandTotalAfterDiscount + shippingFee).toLocaleString() + ' VNĐ';
        });
        
    </script>

</body>

</html>