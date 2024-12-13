<?php
// Kết nối với cơ sở dữ liệu
include 'components/connect.php';

// Kiểm tra quyền quản trị viên (Ví dụ: Kiểm tra nếu người dùng có quyền admin)
session_start();
if (!isset($_SESSION['admin_id'])) {
    header('Location: login.php');
    exit;
}

// Lấy danh sách các sản phẩm và nhận xét từ cơ sở dữ liệu
$fetch_reviews = $conn->prepare("
    SELECT r.*, u.name AS user_name, p.name AS product_name, p.image AS product_image
    FROM `product_reviews` r
    JOIN `users` u ON r.user_id = u.id
    JOIN `products` p ON r.product_id = p.id
    ORDER BY r.id DESC
");
$fetch_reviews->execute();
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Nhận Xét</title>
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css/admin_style.css">
</head>

<body>
    <?php include 'components/admin_header.php'; ?>

    <div class="main-content">
        <h1>Quản Lý Nhận Xét Sản Phẩm</h1>
        <div class="reviews-list">
            <?php
            if ($fetch_reviews->rowCount() > 0) {
                while ($review = $fetch_reviews->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="review-box">
                        <div class="review-product-info">
                            <img src="uploaded_files/<?= $review['product_image']; ?>" alt="Product Image" class="product-img">
                            <div class="product-details">
                                <h3><?= $review['product_name']; ?></h3>
                                <p><strong>Đánh giá:</strong>
                                    <span
                                        class="stars"><?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></span>
                                </p>
                            </div>
                        </div>
                        <div class="review-details">
                            <p><strong>Người đánh giá:</strong> <?= $review['user_name']; ?></p>
                            <p><strong>Nhận xét:</strong> <?= $review['review_text']; ?></p>
                        </div>
                    </div>
                    <?php
                }
            } else {
                echo '<p>Chưa có nhận xét nào!</p>';
            }
            ?>
        </div>
    </div>

    <?php include 'components/admin_footer.php'; ?>

</body>

</html>