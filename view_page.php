<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}

$pid = $_GET['pid'];

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
    <title>Chi Tiết Sản phẩm</title>
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Chi Tiết Sản Phẩm</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <span><a href="home.php">home</a><i class="bx bx-right-arrow-alt"></i>Chi Tiết Sản phẩm</span>
        </div>
    </div>

    <section class="view_page">
        <div class="heading">
            <h1>Chi Tiết Sản Phẩm</h1>
            <img src="image/separator.png">
        </div>

        <?php
        if (isset($_GET['pid'])) {
            if (isset($_POST['submit_review'])) {
                $product_id = $_POST['product_id'];
                $rating = $_POST['rating'];
                $review_text = htmlspecialchars($_POST['review_text'], ENT_QUOTES);

                // Lấy seller_id từ sản phẩm
                $select_product = $conn->prepare("SELECT seller_id FROM `products` WHERE id = ?");
                $select_product->execute([$product_id]);
                $product = $select_product->fetch(PDO::FETCH_ASSOC);
                $seller_id = $product['seller_id'];

                // Thêm đánh giá mới, không kiểm tra xem người dùng đã đánh giá hay chưa
                $insert_review = $conn->prepare("INSERT INTO `product_reviews` (product_id, user_id, seller_id, rating, review_text) VALUES (?, ?, ?, ?, ?)");
                $insert_review->execute([$product_id, $user_id, $seller_id, $rating, $review_text]);
                $message[] = 'Đánh giá của bạn đã được gửi!';
            }
            $pid = $_GET['pid'];
            $select_product = $conn->prepare("SELECT * FROM `products` WHERE id = '$pid'");
            $select_product->execute();

            if ($select_product->rowCount() > 0) {
                while ($fetch_products = $select_product->fetch(PDO::FETCH_ASSOC)) {

                    ?>
                    <form action="" method="post" class="box">
                        <div class="img-box">
                            <img src="uploaded_files/<?= $fetch_products['image']; ?>">
                        </div>
                        <div class="detail">
                            <?php if ($fetch_products['stock'] > 5) { ?>
                                <span class="stock" style="color: green;">Còn hàng</span>
                            <?php } elseif ($fetch_products['stock'] == 0) { ?>
                                <span class="stock" style="color: red;">Hết hàng</span>
                            <?php } else { ?>
                                <span class="stock" style="color: red;">Chỉ còn <?= $fetch_products['stock'];
                                ?> sản phẩm</span>
                            <?php } ?>


                            <div class="name"><?= $fetch_products['name']; ?></div>
                            <p class="price">Giá: <?= number_format($fetch_products['price'], 0, ',', '.') ?> VND</p>

                            <p class="product-detail"><?= $fetch_products['product_detail']; ?></p>
                            <input type="hidden" name="product_id" value="<?= $fetch_products['id'] ?>">

                            <label for="qty">Số lượng:</label>
                            <input type="number" name="qty" min="1" max="<?= $fetch_products['stock']; ?>" value="1"
                                class="quantity">

                            <div class="button">
                                <button type="submit" name="add_to_wishlist" class="btn">Thêm vào yêu thích
                                    <i class="bx bx-heart"></i></button>
                                <button type="submit" name="add_to_cart" class="btn">
                                    Thêm vào giỏ hàng<i class="bx bx-cart"></i></button>
                            </div>
                        </div>
                    </form>

                    <?php
                }
            }
        }
        ?>
    </section>
    <section class="product-review">
    <div class="heading">
        <h1>Đánh giá sản phẩm</h1>
    </div>
    <?php if (!empty($user_id)) { ?>
            <form action="" method="post" class="review-form">
                <input type="hidden" name="product_id" value="<?= $pid; ?>">
    
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
            <section class="review-list">
                <div class="heading">
                    <h1>Nhận xét từ khách hàng</h1>
                </div>
                <?php
                $fetch_reviews = $conn->prepare("SELECT r.*, u.name FROM `product_reviews` r JOIN `users` u ON r.user_id = u.id WHERE r.product_id = ? ORDER BY r.id DESC");
                $fetch_reviews->execute([$pid]);

                if ($fetch_reviews->rowCount() > 0) {
                    while ($review = $fetch_reviews->fetch(PDO::FETCH_ASSOC)) {
                        ?>
                <div class="review-box">
                    <h3><?= htmlspecialchars($review['name']); ?></h3>
                    <p>Đánh giá: 
                        <span class="stars"><?= str_repeat('★', $review['rating']) . str_repeat('☆', 5 - $review['rating']); ?></span>
                    </p>
                    <p>Nhận xét: <?= nl2br(htmlspecialchars($review['review_text'])); ?></p>
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
                    echo '<p>Chưa có đánh giá nào cho sản phẩm này.</p>';
                }

                ?>
            </section>
    
        <?php } else { ?>
            <p>Vui lòng <a href="login.php">đăng nhập</a> để đánh giá sản phẩm.</p>
        <?php } ?>
    </section>


    <!-- <section class="products">
        <div class="heading">
            <h1>sản phẩm tương tự</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <img src="image/separator.png">
        </div>
        <?php include 'components/shop.php'; ?>
    </section> -->

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