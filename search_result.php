<?php
session_start();

// Lấy dữ liệu từ session
$image_path = isset($_SESSION['image_path']) ? $_SESSION['image_path'] : null;
$predicted_label = isset($_SESSION['predicted_label']) ? $_SESSION['predicted_label'] : null;
$error = isset($_SESSION['error']) ? $_SESSION['error'] : null;

// Kết nối cơ sở dữ liệu
include 'components/connect.php';
?>

<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <title>Kết quả dự đoán</title>
</head>

<body>
    <h1>Kết quả dự đoán</h1>

    <?php if ($error): ?>
        <p style="color: red;"><?= htmlspecialchars($error); ?></p>
    <?php else: ?>
        <?php if ($image_path): ?>
            <h3>Hình ảnh đã tải lên:</h3>
            <img src="<?= $image_path ?>" alt="Uploaded Image" style="max-width: 300px; margin-bottom: 20px;"><br>
        <?php endif; ?>

        <?php if ($predicted_label): ?>
            <h3>Dự đoán của mô hình: </h3>
            <h3><?= htmlspecialchars($predicted_label); ?></h3>

            <?php
            // Truy vấn cơ sở dữ liệu để tìm sản phẩm theo nhãn dự đoán
            $select_products = $conn->prepare("SELECT * FROM `products` WHERE name LIKE ? AND status = ?");
            $search_term = "%" . $predicted_label . "%";
            $select_products->execute([$search_term, 'active']);

            // Kiểm tra nếu có sản phẩm phù hợp
            if ($select_products->rowCount() > 0): ?>
                <div class="product-container">
                    <?php while ($fetch_products = $select_products->fetch(PDO::FETCH_ASSOC)): ?>
                        <div class="product-box">
                            <img src="uploaded_files/<?= $fetch_products['image']; ?>" class="image" alt="Product Image">
                            <h3><?= htmlspecialchars($fetch_products['name']); ?></h3>
                            <p class="price"><?= number_format($fetch_products['price'], 0, ',', '.'); ?> VNĐ</p>
                            <p class="stock"><?= $fetch_products['stock'] > 0 ? 'Còn hàng: ' . $fetch_products['stock'] : 'Hết hàng'; ?>
                            </p>
                            <a href="view_page.php?pid=<?= $fetch_products['id']; ?>" class="btn">Xem chi tiết</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else: ?>
                <p>Không tìm thấy sản phẩm phù hợp với dự đoán.</p>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</body>

</html>