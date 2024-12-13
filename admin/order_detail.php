<?php
include '../components/connect.php';

// Lấy order_id từ URL
$order_id = isset($_GET['id']) ? $_GET['id'] : '';

// Truy vấn thông tin sản phẩm từ bảng order_items theo order_id
$query = "SELECT product_id, product_name, product_image, quantity, price, quantity * price AS total_price
          FROM order_items
          WHERE order_id = :order_id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':order_id', $order_id, PDO::PARAM_STR);
$stmt->execute();
$orderItems = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Lấy thông tin đơn hàng để hiển thị bên phải
$select_order = $conn->prepare("
    SELECT 
        id, user_id, name, date, number, email, method, address, status
    FROM `orders` 
    WHERE id = :order_id
");
$select_order->bindParam(':order_id', $order_id, PDO::PARAM_STR);
$select_order->execute();
$orderInfo = $select_order->fetch(PDO::FETCH_ASSOC);

// Nếu trạng thái là "Đã nhận" hoặc "Đã hoàn tất", không cho phép cập nhật
$isCompletedOrReceived = in_array($orderInfo['status'], ['completed', 'received']);

// Tính tổng giá trị của các sản phẩm trong đơn hàng
$calculatedTotal = 0;
if ($orderItems) {
    foreach ($orderItems as $item) {
        $calculatedTotal += $item['total_price'];
    }
}
?>



<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v = <?php echo time(); ?>">
    
    <title>Chi tiết đơn hàng</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            /* Hoặc space-evenly */
            padding: 20px;
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        h1 {
            margin-bottom: 20px;
            color: #333;
        }

        .order-container {
            display: flex;
            /* Sử dụng Flexbox để sắp xếp các phần tử */
            justify-content: flex-start;
            /* Căn giữa các box về bên trái */
            width: 100%;
            /* Chiều rộng 100% */
            align-items: flex-start;
            /* Căn giữa theo chiều dọc */
        }

        .order-items-container {
            padding: 15px;
            /* Khoảng cách xung quanh */
            border: 2px solid #990000;
            /* Đường viền cho toàn bộ khối */
            border-radius: 8px;
            /* Bo góc cho khối */
            background-color: #fff;
            /* Màu nền trắng cho toàn bộ khối */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Bóng đổ nhẹ cho khối */
            margin-right: 20px;
            /* Khoảng cách bên phải */
            width: auto;
            /* Để box tự động điều chỉnh kích thước */
            flex: 0 1 auto;
            /* Cho phép box tự động co lại theo kích thước của nội dung */
            min-width: 0;
            /* Cho phép box co lại đến kích thước tối thiểu */
            max-width: 100%;
            /* Đảm bảo không vượt quá chiều rộng của container cha */
            height: auto;
            /* Để box tự động điều chỉnh chiều dài theo nội dung */
            display: inline-block;
            /* Cải thiện cách mà box hiển thị và điều chỉnh theo nội dung */
        }


        .order-summary {
            padding: 20px;
            /* Khoảng cách bên trong */
            background-color: #fff;
            /* Màu nền trắng */
            border-radius: 8px;
            /* Bo góc cho khối */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Bóng đổ nhẹ cho khối */
            margin-bottom: 100px;
            /* Khoảng cách bên dưới nếu cần */
            width: auto;
            /* Để box tự động điều chỉnh kích thước */

            flex: 0 1 auto;
            /* Để box tự động co lại theo nội dung */
            min-width: 0;
            /* Cho phép box co lại đến kích thước tối thiểu */
            height: auto;
            /* Để box tự động điều chỉnh chiều dài theo nội dung */
        }

        .order-item {
            display: flex;
            align-items: flex-start;
            justify-content: flex-start;
            padding: 15px;
            /* Khoảng cách bên trong mỗi mục */
            border-bottom: 1px solid #eee;
            /* Đường viền dưới để tách các sản phẩm */
        }

        .order-item:last-child {
            border-bottom: none;
            /* Bỏ đường viền dưới cho mục cuối cùng */
        }


        .order-item img {
            width: 150px;
            /* Đặt chiều rộng cố định */
            height: 150px;
            /* Đặt chiều cao cố định */
            margin-right: 20px;
            /* Khoảng cách bên phải */
            border-radius: 4px;
            /* Bo góc cho hình ảnh */
            object-fit: cover;
            /* Cách thức hiển thị hình ảnh */
        }

        .order-details {
            display: flex;
            flex-direction: column;
            /* Sắp xếp các phần tử theo chiều dọc */
            flex-grow: 1;
            /* Chiếm không gian còn lại */
            padding: 15px;
            /* Khoảng cách bên trong để tạo không gian cho nội dung */
            background-color: #f9f9f9;
            /* Màu nền nhẹ để tách biệt với hình ảnh */
            border-radius: 8px;
            /* Bo góc cho khối */
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            /* Bóng đổ nhẹ cho khối */
            margin-top: 10px;
            /* Khoảng cách phía trên */
        }

        .product-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 10px;
        }

        .order-details h3 {
            margin: 0;
            font-size: 18px;
            color: #000;
            letter-spacing: 1px;
        }

        .product-info p {
            margin: 0 10px;
            color: #000;
            font-weight: 500;
        }

        .total-price {
            margin: 0;
            color: #000;
            font-weight: bold;
        }



        .order-summary h2 {
            margin-top: 0;
            /* Bỏ khoảng cách trên */
            font-size: 24px;
            /* Kích thước phông chữ lớn hơn */
            color: #990000;
            /* Màu chữ khác biệt cho tiêu đề */
            border-bottom: 2px solid #f2f2f2;
            /* Đường viền dưới để làm nổi bật tiêu đề */
            padding-bottom: 10px;
            /* Khoảng cách dưới tiêu đề */
            letter-spacing: 0.5px;
            /* Khoảng cách giữa các chữ cái */
        }

        .order-summary p {
            margin: 5px 0;
            /* Khoảng cách giữa các đoạn */
            font-size: 16px;
            /* Kích thước phông chữ dễ đọc */
            color: #000;
            /* Màu chữ tối hơn */
            line-height: 1.5;
            /* Khoảng cách giữa các dòng */
        }


        .header {
            display: flex;
            justify-content: center;
            /* Căn giữa theo chiều ngang */
            align-items: center;
            /* Căn giữa theo chiều dọc nếu cần */
            margin-bottom: 20px;
            /* Khoảng cách phía dưới tiêu đề */
        }

        h1 {
            color: #000;
            /* Màu chữ */
            text-transform: capitalize;
        }

        .product-name {
            text-transform: uppercase;
            /* Viết hoa tất cả các chữ cái */
            color: #000;
            /* Màu chữ đen */
            margin: 0;
            /* Loại bỏ khoảng cách mặc định */
        }

        #order_status {
            padding: 10px;
            /* Khoảng cách bên trong */
            border: 1px solid #ccc;
            /* Đường viền */
            border-radius: 5px;
            /* Bo góc */
            background-color: #fff;
            /* Màu nền */
            font-size: 16px;
            /* Kích thước chữ */
            width: 100%;
            /* Chiều rộng 100% */
            max-width: 200px;
            /* Giới hạn chiều rộng tối đa nếu cần */
        }

        .update-button {
            background-color: #f2a7ad;
            /* Màu nền của nút */
            color: #000;
            /* Màu chữ trắng */
            font-weight: bold;
            /* Chữ đậm */
            border: none;
            /* Bỏ viền */
            border-radius: 5px;
            /* Bo góc cho nút */
            padding: 10px 20px;
            /* Khoảng cách trong nút */
            font-size: 16px;
            /* Kích thước chữ */
            cursor: pointer;
            /* Con trỏ chuột thay đổi khi di chuột qua nút */
            transition: background-color 0.3s ease;
            /* Hiệu ứng chuyển màu khi hover */
        }

        .update-button:hover {
            background-color: #f2e9e9;
            /* Màu nền khi hover */
        }

        .update-button:active {
            background-color: #660000;
            /* Màu nền khi nhấn giữ nút */
        }

        /* Các kiểu khác có thể tùy chỉnh theo ý thích */
    </style>
</head>

<body>
    
    <div class="table-wrapper">

        <div class="header">
            <h1>Chi tiết sản phẩm trong đơn hàng</h1>
        </div>

        <div class="order-container">
            <div class="order-items-container">
                <?php
                $calculatedTotal = 0;
                if ($orderItems): ?>
                    <?php foreach ($orderItems as $item): ?>
                        <div class="order-item">
                            <?php 
                                $imagePath = '../uploaded_files/' . htmlspecialchars($item['product_image']);
                                $calculatedTotal += $item['total_price']; 
                            ?>
                            <img src="<?= $imagePath; ?>" alt="<?= htmlspecialchars($item['product_name']); ?>" />
                            <div class="order-details">
                                <div class="product-info">
                                    <h3 class="product-name"><?= htmlspecialchars($item['product_name']); ?></h3>
                                    <p>Số lượng: <?= htmlspecialchars($item['quantity']); ?></p>
                                    <p>Giá mỗi sản phẩm: <?= number_format(htmlspecialchars($item['price']), 0, ',', '.') . ' '; ?></p>

                                </div>
                                <p class="total-price">Tổng giá: <?= number_format(htmlspecialchars($item['total_price']), 0, ',', '.') . ' VNĐ'; ?></p>

                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Không có sản phẩm nào trong đơn hàng này.</p>
                <?php endif; ?>
            </div>

            <div class="order-summary">
                <h2>Thông tin đơn hàng</h2>
                <?php if ($orderInfo): ?>
                    <p><strong>Tên khách hàng:</strong> <?= htmlspecialchars($orderInfo['name']); ?></p>
                    <p><strong>ID đơn hàng:</strong> <?= htmlspecialchars($orderInfo['id']); ?></p>
                    <p><strong>Ngày đặt:</strong> <?= htmlspecialchars($orderInfo['date']); ?></p>
                    <p><strong>Số điện thoại:</strong> <?= htmlspecialchars($orderInfo['number']); ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($orderInfo['email']); ?></p>
                    <p><strong>Tổng tiền:</strong> <?= number_format(htmlspecialchars($calculatedTotal), 0, ',', '.') . ' VNĐ'; ?></p>
                    <!-- <p><strong>Số tiền giảm giá:</strong> <?= number_format($priceAfter, 0, ',', '.') . ' VNĐ'; ?></p>
                    <p><strong>Tổng tiền sau giảm giá:</strong> <?= number_format($totalAfterDiscount, 0, ',', '.') . ' VNĐ'; ?></p> -->
                    <p><strong>Phương thức thanh toán:</strong> <?= htmlspecialchars($orderInfo['method']); ?></p>
                    <p><strong>Địa chỉ:</strong> <?= htmlspecialchars($orderInfo['address']); ?></p>

                    <form method="POST" action="update_order_status.php" onsubmit="return confirmUpdate();">
                        <p><strong>Trạng thái:</strong>
                            <select name="order_status" id="order_status" 
                                <?php if ($isCompletedOrReceived) echo 'disabled'; ?>>
                                <option value="Đóng gói" <?= $orderInfo['status'] === 'packaged' ? 'selected' : ''; ?>>Đóng gói</option>
                                <option value="Giao hàng" <?= $orderInfo['status'] === 'shipped' ? 'selected' : ''; ?>>Giao hàng</option>
                                <!-- <option value="Đã hoàn tất" <?= $orderInfo['status'] === 'completed' || $orderInfo['status'] === 'received' ? 'selected' : ''; ?> disabled>Đã hoàn tất</option> -->
                                <option value="Hủy hàng" <?= $orderInfo['status'] === 'canceled' ? 'selected' : ''; ?>>Hủy hàng</option>
                            </select>
                        </p>

                        <input type="hidden" name="order_id" value="<?= htmlspecialchars($orderInfo['id']); ?>">
                        <input type="submit" class="update-button" value="Cập nhật trạng thái" 
                            <?php if ($isCompletedOrReceived) echo 'disabled'; ?>/>
                            <a href="admin_orderss.php" class="update-button">Tất cả đơn hàng</a>
                    </form>

                    <script>
                        function confirmUpdate() {
                            return confirm('Bạn có chắc chắn muốn cập nhật trạng thái đơn hàng?');
                        }
                    </script>

                <?php else: ?>
                    <p>Không có thông tin đơn hàng.</p>
                <?php endif; ?>
            </div>

        </div>

</body>


</html>