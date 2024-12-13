<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
}

// Lấy tất cả các sản phẩm của người bán
$select_products = $conn->prepare("SELECT * FROM `products` WHERE seller_id = ?");
$select_products->execute([$seller_id]);
if (isset($_POST['submit_response'])) {
    $review_id = $_POST['review_id'];
    $response_text = $_POST['response_text'];

    // Lọc dữ liệu đầu vào
    $review_id = filter_var($review_id, FILTER_SANITIZE_NUMBER_INT);
    $response_text = filter_var($response_text, FILTER_SANITIZE_STRING);

    // Cập nhật phản hồi trong bảng product_reviews
    $update_response = $conn->prepare("UPDATE `product_reviews` SET phanhoi = ? WHERE id = ?");
    $update_response->execute([$response_text, $review_id]);

    // Thông báo phản hồi đã gửi
    $success_msg[] = "Phản hồi đã được gửi!";
}


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    <title>Quản Trị - Đánh Giá Sản Phẩm</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Đánh Giá Sản Phẩm</h1>
            <span><a href="dashboard.php">Quản trị</a><i class="bx bx-right-arrow-alt"></i>Đánh Giá</span>
        </div>
    </div>

    <section class="review-section">
        <div class="heading">
            <h1>Đánh Giá Sản Phẩm</h1>
            <img src="../image/separator.png" width="100">
        </div>

        <?php
        // Kiểm tra nếu có sản phẩm
        if ($select_products->rowCount() > 0) {
            while ($product = $select_products->fetch(PDO::FETCH_ASSOC)) {
                $pid = $product['id'];

                // Lấy các đánh giá của sản phẩm
                $select_reviews = $conn->prepare("SELECT r.*, u.name FROM `product_reviews` r JOIN `users` u ON r.user_id = u.id WHERE r.product_id = ?");
                $select_reviews->execute([$pid]);

                // Kiểm tra xem sản phẩm có ít nhất một đánh giá
                if ($select_reviews->rowCount() > 0) {
                    echo '<div class="product-reviews">';
                    echo '<h3>Sản phẩm: ' . $product['name'] . '</h3>';

                    while ($review = $select_reviews->fetch(PDO::FETCH_ASSOC)) {
                        // Hiển thị từng đánh giá
                        echo '<div class="review-box">';
                        echo '<h4>' . $review['name'] . '</h4>';
                        echo '<p>Đánh giá: ';
                        for ($i = 1; $i <= 5; $i++) {
                            if ($i <= $review['rating']) {
                                // Sao được chọn (vàng)
                                echo '<span class="star selected">★</span>';
                            } else {
                                // Sao chưa được chọn (xám)
                                echo '<span class="star">☆</span>';
                            }
                        }
                        echo '</p>';
                        echo '<p>' . $review['review_text'] . '</p>';

                        // Hiển thị phản hồi nếu đã có
                        echo '<p><strong>Phản hồi:</strong> ' . ($review['phanhoi'] ? nl2br(htmlspecialchars($review['phanhoi'])) : '<em>Chưa có phản hồi</em>') . '</p>';

                        // Form gửi phản hồi
                        echo '<form action="" method="post">';
                        echo '<textarea name="response_text" placeholder="Nhập phản hồi..." required></textarea>';
                        echo '<input type="hidden" name="review_id" value="' . $review['id'] . '">';
                        echo '<button type="submit" name="submit_response" class="btn">Gửi phản hồi</button>';
                        echo '</form>';

                        echo '</div>';
                    }


                    echo '</div>';
                }
            }
        } else {
            echo '<p>Không có sản phẩm nào để hiển thị.</p>';
        }
        ?>

    </section>

    <?php include '../components/admin_footer.php'; ?>
    <script type="text/javascript" src="../js/admin_script.js"></script>
</body>
<?php
if (isset($success_msg) && count($success_msg) > 0) {
    foreach ($success_msg as $message) {
        echo '<div class="success-message">' . $message . '</div>';
    }
}
?>

</html>