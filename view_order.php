<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = 'login.php';
}

// Lấy thông tin đơn hàng từ URL
if (isset($_GET['get_id'])) {
    $get_id = $_GET['get_id'];
} else {
    $get_id = '';
    header('location:order.php');
}

// Lấy thông tin đơn hàng từ bảng orders
$select_order = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
$select_order->execute([$get_id]);
$order_info = $select_order->fetch(PDO::FETCH_ASSOC);


// Kiểm tra nếu đơn hàng tồn tại
if ($order_info) {
    $price_after = $order_info['price_after'];
    $shipping_fee = $order_info['shipping_fee'];
} else {
    echo "<p>Không tìm thấy thông tin đơn hàng.</p>";
    exit;
}

// Lấy seller_id từ bảng sellers
$select_seller = $conn->prepare("SELECT seller_id FROM `products` WHERE id = ?");
$select_seller->execute([$order_info['product_id']]);
$seller_info = $select_seller->fetch(PDO::FETCH_ASSOC);
$seller_id = $seller_info['seller_id'];


$get_id = $_GET['get_id']; // ID của đơn hàng


if (isset($_POST['canceled'])) {
    $update_order = $conn->prepare("UPDATE `orders` SET status=? WHERE id =?");
    $update_order->execute(['canceled', $get_id]);
    header('location:order.php');
}

if (isset($_POST['received'])) {
    // Update the status of the order to "received"
    $update_order = $conn->prepare("UPDATE `orders` SET status=? WHERE id =?");
    $update_order->execute(['received', $get_id]);
    header('location:order.php'); // Redirect to the orders page after confirmation
}

if (isset($_POST['submit_review'])) {
    $order_id = $_POST['order_id'];
    $rating = $_POST['rating'];
    $review_text = htmlspecialchars($_POST['review_text'], ENT_QUOTES);

    // Kiểm tra xem người dùng đã đánh giá cho đơn hàng này chưa
    $check_review = $conn->prepare("SELECT * FROM `order_reviews` WHERE order_id = ? AND user_id = ?");
    $check_review->execute([$order_id, $user_id]);

    if ($check_review->rowCount() > 0) {
        $message[] = 'Bạn đã đánh giá đơn hàng này rồi!';
    } else {
        // Lấy thông tin seller_id từ đơn hàng
        $select_order = $conn->prepare("SELECT seller_id FROM `orders` WHERE id = ?");
        $select_order->execute([$order_id]);
        $order = $select_order->fetch(PDO::FETCH_ASSOC);
        $seller_id = $order['seller_id'];

        // Thêm đánh giá mới
        $insert_review = $conn->prepare("INSERT INTO `order_reviews` (order_id, user_id, seller_id, rating, review) VALUES (?, ?, ?, ?, ?)");
        $insert_review->execute([$order_id, $user_id, $seller_id, $rating, $review_text]);
        $message[] = 'Đánh giá của bạn đã được gửi!';
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
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v=<?php echo time(); ?>">
    <title>Chi tiết đơn hàng</title>
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Chi tiết đơn hàng</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <span><a href="home.php">Trang chủ</a><i class="bx bx-right-arrow-alt"></i>Chi tiết đơn hàng</span>
        </div>
    </div>
    <div class="view_order">
        <div class="heading">
            <h1>Chi tiết đơn hàng</h1>
            <img src="image/separator.png">
        </div>
        <div class="view_orderr">
            <div class="order-box-container">
                <?php
                $grand_total = 0;
                $select_orders = $conn->prepare("SELECT * FROM `orders` WHERE id = ?"); // Lấy đơn hàng theo ID
                $select_orders->execute([$get_id]);

                $order_info = null; // Khai báo biến lưu thông tin đơn hàng
                
                if ($select_orders->rowCount() > 0) {
                    while ($fetch_order = $select_orders->fetch(PDO::FETCH_ASSOC)) {
                        // Lưu thông tin đơn hàng vào biến
                        $order_info = $fetch_order;

                        // Lấy thông tin sản phẩm liên quan đến đơn hàng
                        $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = ?"); // Lấy sản phẩm theo product_id
                        $select_product->execute([$fetch_order['product_id']]);

                        if ($select_product->rowCount() > 0) {
                            while ($fetch_product = $select_product->fetch(PDO::FETCH_ASSOC)) {
                                $sub_total = ($fetch_order['price'] * $fetch_order['qty']);
                                $grand_total += $sub_total; // Cộng dồn tổng thanh toán
                                ?>
                                <div class="order-item">
                                    <div class="order-image">
                                        <img src="uploaded_files/<?= $fetch_product['image']; ?>" class="image">
                                    </div>
                                    <div class="order-details">
                                        <p class="order-date"><i class="bx bxs-calender-alt"></i><?= $fetch_order['date']; ?></p>
                                        <p class="order-price"><?= number_format($fetch_product['price'], 0, ',', '.') . ''; ?> x
                                            <?= $fetch_order['qty']; ?>
                                        </p>
                                        <h3 class="order-name"><?= $fetch_product['name']; ?></h3>
                                    </div>
                                </div>
                                <?php
                            } // Kết thúc vòng lặp sản phẩm
                        }

                    } // Kết thúc vòng lặp đơn hàng
                    ?>
                    <div class="grand-total">
                        <p>Tổng thanh toán: <span><?= number_format($grand_total, 0, ',', '.') . ' VND'; ?></span></p>
                        <?php if (isset($price_after) && $price_after > 0): ?>
                            <p>Giảm giá: <span><?= number_format(-$price_after, 0, ',', '.') . ' VND'; ?></span></p>
                        <?php endif; ?>
                        <p>Phí vận chuyển: <span><?= number_format($shipping_fee, 0, ',', '.') . ' VND'; ?></span></p>
                        <p><strong>Tổng tiền phải trả:
                                <span><?= number_format($total_amount = ($grand_total - $price_after) + $shipping_fee, 0, ',', '.') . ' VND'; ?></span></strong></p>
                    </div>
                    <?php
                } else {
                    echo '
                <div class="empty">
                    <p>Không có sản phẩm nào được thêm</p>
                </div>
            ';
                }
                ?>
            </div>

            <div class="order-summary">
                <div class="col">
                    <p class="title">Thông tin khách hàng</p>
                    <?php if ($order_info): ?>
                        <p class="user"><i class='bx bxs-user-rectangle'></i><?= htmlspecialchars($order_info['name']); ?>
                        </p>
                        <p class="user"><i class='bx bxs-phone-outgoing'></i><?= htmlspecialchars($order_info['number']); ?>
                        </p>
                        <p class="user"><i class='bx bxs-envelope'></i><?= htmlspecialchars($order_info['email']); ?></p>
                        <p class="user"><i class='bx bxs-map-alt'></i><?= htmlspecialchars($order_info['address']); ?></p>

                        <p class="title">Trạng thái</p>
                        <p class="status" style="color:<?php
                        // Hiển thị màu sắc cho từng trạng thái
                        if ($order_info['status'] == 'delivered') {
                            echo "green";
                        } elseif ($order_info['status'] == 'canceled') {
                            echo "red"; // Đặt màu đỏ cho trạng thái "Đã hủy"
                        } elseif ($order_info['status'] == 'packaged') {
                            echo "blue";  // Đặt màu xanh dương cho trạng thái "Đóng gói"
                        } else {
                            echo "orange";
                        }
                        ?>">
                            <?=
                                // Hiển thị trạng thái
                                $order_info['status'] == 'canceled' ? 'Đã hủy' :
                                ($order_info['status'] == 'in progress' ? 'Đang xử lý' :
                                    ($order_info['status'] == 'Giao hàng' ? 'Đang giao hàng' :
                                        ($order_info['status'] == 'packaged' ? 'Đang đóng gói' : '')))
                                ?>
                        </p>
                        <!-- Hiển thị giá trị đơn hàng và giảm giá nếu có -->
                        <!-- <div class="order-price-summary">
                <p>Tổng giá trị đơn hàng: <strong><?= number_format($grand_total, 0, ',', '.') . ' VND'; ?></strong></p>
                            <?php if (isset($price_after) && $price_after > 0): ?>
                                <p>Giá trị sau giảm giá: <strong><?= number_format($price_after, 0, ',', '.') . ' VND'; ?></strong></p>
                            <?php endif; ?>
                        </div> -->

                        <?php if ($order_info['status'] == 'canceled') { ?>
                            <!-- Hiển thị "Đã hủy" khi trạng thái là 'canceled' -->
                            <!-- <p class="status" style="color: red;">Đã hủy</p> -->
                        <?php } elseif ($order_info['status'] == 'Giao hàng') { ?>
                            <!-- Hiển thị nút "Đã nhận" khi đơn hàng đã giao -->
                            <form action="" method="post">
                                <button type="submit" name="received" class="btn"
                                    onclick="return confirm('Bạn có chắc chắn đã nhận hàng?');">Đã nhận</button>
                            </form>
                        <?php } elseif ($order_info['status'] == 'received') { ?>
                            <!-- Hiển thị "Đã nhận" khi trạng thái là 'received' -->
                            <p class="status" style="color: green;">Đã nhận</p>
                        <?php } elseif ($order_info['status'] == 'Đóng gói') { ?>
                            <!-- Hiển thị trạng thái "Đang đóng gói" khi trạng thái là 'packaged' -->
                            <p class="status" style="color: orange;">Đang đóng gói</p>
                            <!-- Hiển thị nút "Đã giao" khi trạng thái là "Đóng gói" -->
                            <!-- <form action="" method="post">
                                    <button type="submit" name="shipped" class="btn"
                                        onclick="return confirm('Bạn có chắc chắn đã giao hàng?');">Đã giao</button>
                                </form> -->
                        <?php } else { ?>
                            <!-- Hiển thị nút hủy nếu đơn hàng chưa được giao hoặc đang xử lý -->
                            <form action="" method="post">
                                <button type="submit" name="canceled" class="btn"
                                    onclick="return confirm('Bạn có muốn hủy sản phẩm?');">Hủy
                                    hàng</button>
                            </form>
                        <?php } ?>
                    <?php else: ?>
                        <p>Không tìm thấy thông tin đơn hàng.</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
<section class="product-review">
        <div class="heading">
            <h1>Đánh giá đơn hàng</h1>
        </div>

      <?php if (!empty($user_id)) { ?>
        <form action="" method="post" class="review-form">
            <input type="hidden" name="order_id" value="<?= $get_id; ?>">

            <label for="rating">Chọn đánh giá:</label>
            <div class="star-rating">
                <span class="star" data-value="1">&#9733;</span>
                <span class="star" data-value="2">&#9733;</span>
                <span class="star" data-value="3">&#9733;</span>
                <span class="star" data-value="4">&#9733;</span>
                <span class="star" data-value="5">&#9733;</span>
            </div>
            <input type="hidden" name="rating" id="rating" required>

            <label for="review_text">Nhận xét của bạn:</label>
            <textarea name="review_text" placeholder="Nhập nhận xét của bạn..." required></textarea>
            <button type="submit" name="submit_review" class="btn">Gửi đánh giá</button>
        </form>
    <?php } else { ?>
        <p>Vui lòng <a href="login.php">đăng nhập</a> để đánh giá đơn hàng.</p>
    <?php } ?>

    <section class="review-list">
    <div class="heading">
        <h1>Nhận xét từ khách hàng</h1>
    </div>
    <?php
        // Lấy các đánh giá cho đơn hàng từ bảng order_reviews
        $fetch_reviews = $conn->prepare("SELECT r.*, u.name FROM `order_reviews` r JOIN `users` u ON r.user_id = u.id WHERE r.order_id = ? ORDER BY r.id DESC");
        $fetch_reviews->execute([$get_id]);

        if ($fetch_reviews->rowCount() > 0) {
            while ($review = $fetch_reviews->fetch(PDO::FETCH_ASSOC)) {
                ?>
                <div class="review-box">
                    <h3><?= htmlspecialchars($review['name']); ?></h3>
                    <p>Đánh giá:
                        <span
                            class="stars"><?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></span>
                    </p>
                    <p>Nhận xét: <?= nl2br(htmlspecialchars($review['review'])); ?></p>
                    <?php if (!empty($review['phanhoi'])) { ?>
                        <div class="response">
                            <strong>Phản hồi từ người bán:</strong>
                            <p><?= nl2br(htmlspecialchars($review['phanhoi'])); ?></p>
                        </div>
                    <?php } ?>
                </div>
                <?php
            }
        } else {
            echo '<p>Chưa có đánh giá nào cho đơn hàng này.</p>';
        }
        ?>
    </section>


</section>

</section>


    <?php include 'components/user_footer.php'; ?>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const stars = document.querySelectorAll('.star');
        const ratingInput = document.getElementById('rating');

        stars.forEach(star => {
            star.addEventListener('mouseover', function () {
                const value = this.getAttribute('data-value');
                resetStars();
                highlightStars(value);
            });

            star.addEventListener('mouseout', function () {
                const selectedValue = ratingInput.value;
                resetStars();
                if (selectedValue) {
                    highlightStars(selectedValue);
                }
            });

            star.addEventListener('click', function () {
                const value = this.getAttribute('data-value');
                ratingInput.value = value; // Lưu giá trị đã chọn vào input ẩn
                resetStars();
                highlightStars(value);
            });
        });

        function resetStars() {
            stars.forEach(star => {
                star.classList.remove('selected');
            });
        }

        function highlightStars(value) {
            for (let i = 0; i < value; i++) {
                stars[i].classList.add('selected');
            }
        }
    });
</script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>
</body>

</html>