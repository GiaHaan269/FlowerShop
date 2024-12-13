<?php
session_start();

// Lấy dữ liệu từ session
$image_path = isset($_SESSION['image_path']) ? $_SESSION['image_path'] : null;
$predicted_label = isset($_SESSION['predicted_label']) ? $_SESSION['predicted_label'] : null;
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// Kết nối cơ sở dữ liệu
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
<html lang="vi">

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
    <section class="products">
        <div class="heading">
            <h1>Kết quả tìm kiếm</h1>
            <img src="image/separator.png">
        </div>
        <!-- <div class="box-container"> -->
            <?php if ($error): ?>
                <p style="color: red;"><?= htmlspecialchars($error); ?></p>
            <?php else: ?>
                <!-- <?php if ($image_path): ?>
                <h3>Hình ảnh đã tải lên:</h3>
                <img src="<?= $image_path ?>" alt="Uploaded Image" style="max-width: 300px; margin-bottom: 20px;"><br>
            <?php endif; ?>

            <?php if ($predicted_label): ?>
                <h3>Dự đoán của mô hình: </h3>
                <h3><?= htmlspecialchars($predicted_label); ?></h3> -->

                    <?php
                    // Truy vấn cơ sở dữ liệu để tìm sản phẩm theo nhãn dự đoán
                    $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ? AND status = ?");
                    $search_term = "%" . $predicted_label . "%";
                    $select_products->execute([$search_term, 'active']);

                    // Kiểm tra nếu có sản phẩm phù hợp
                    if ($select_products->rowCount() > 0): ?>
                        <div class="box-container">
                            <?php while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)): ?>
                                <div class="box">
                                    <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image" alt="Product Image">
                                    <h3><?= htmlspecialchars($fetch_products['name']); ?></h3>
                                    <p class="price"><?= number_format($fetch_products['price'], 0, ',', '.'); ?> VNĐ</p>
                                    
                                        <?php if ($fetch_products['stock'] > 9) { ?>
                                            <span class="stock" style="color:green;">Còn Hàng</span>
                                        <?php } elseif ($fetch_products['stock'] == 0) { ?>
                                            <span class="stock" style="color:red;">Hết Hàng</span>
                                        <?php } else { ?>
                                            <span class="stock" style="color:red;">Chỉ còn <?= $fetch_products['stock']; ?> sản phẩm</span>
                                        <?php } ?>
                                    
                                    <!-- <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="btn">Xem chi tiết</a> -->
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
                                </div>
                                
                            <?php endwhile; ?>
                        </div>
                    <?php else: ?>
                        <p>Không tìm thấy sản phẩm phù hợp với dự đoán.</p>
                    <?php endif; ?>
                <?php endif; ?>
            <?php endif; ?>
        <!-- </div> -->
    </section>
</body>

</html>



    
    <!-- <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            background-color: #f4f4f4;
        }

        h1 {
            text-align: center;
            color: #333;
        }

        .product-container {
            display: flex;
            flex-wrap: wrap;
            /* Cho phép xuống dòng khi không đủ chỗ */
            justify-content: center;
            /* Canh giữa các sản phẩm */
            gap: 20px;
            /* Khoảng cách giữa các sản phẩm */
            margin-top: 20px;
        }

        .product-box {
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            width: 280px;
            /* Độ rộng của mỗi box */
            text-align: center;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
        }

        .product-box:hover {
            transform: translateY(-5px);
        }

        .product-box img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 15px;
        }

        .product-box h3 {
            font-size: 18px;
            color: #333;
            margin-bottom: 10px;
        }

        .product-box .price {
            font-size: 16px;
            font-weight: bold;
            color: #e91e63;
            margin-bottom: 10px;
        }

        .product-box .stock {
            font-size: 14px;
            color: #555;
            margin-bottom: 15px;
        }

        .product-box .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.2s;
        }

        .product-box .btn:hover {
            background-color: #0056b3;
        }
    </style> -->
