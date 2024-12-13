<?php
// Kết nối cơ sở dữ liệu
include 'components/connect.php';
// require 'mail/sendmail.php';
if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
    header('location:login.php');
}

// Hàm thực hiện yêu cầu POST
function execPostRequest($url, $data)
{
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ]);
    curl_setopt($ch, CURLOPT_TIMEOUT, 5);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);

    $result = curl_exec($ch);
    curl_close($ch);
    return $result;
}

// Kiểm tra nếu có bill_id trong URL
if (isset($_GET['id'])) {
    $bill_id = $_GET['id'];

    // Truy vấn thông tin hóa đơn từ bảng bill
    $select_bill = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
    $select_bill->execute([$bill_id]);
    $order_total = 0; // Tổng tiền hàng
    $amount = 0; // Tổng tiền thanh toán (bao gồm phí vận chuyển)
    $shipping_fee = 0;
    // Kiểm tra xem hóa đơn có tồn tại không
    if ($select_bill->rowCount() > 0) {
        while ($bill_info = $select_bill->fetch(PDO::FETCH_ASSOC)) {
            $price = $bill_info['price']; // Giá sản phẩm
            $qty = $bill_info['qty']; // Số lượng
            $shipping_fee = $bill_info['shipping_fee']; // Phí vận chuyển của đơn hàng (giả sử giống nhau cho tất cả sản phẩm)

            $total = $price * $qty; // Tổng giá trị hàng hóa của sản phẩm hiện tại
            $order_total += $total; // Cộng dồn vào tổng tiền hàng
        }

        // Tổng tiền thanh toán bao gồm phí vận chuyển (chỉ cộng 1 lần)
        $amount = $order_total + $shipping_fee;

        // Thông tin thanh toán
        $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
        $partnerCode = 'MOMOBKUN20180529';
        $accessKey = 'klm05TvNBzhg7h7j';
        $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';
        $orderInfo = "Thanh toán qua MoMo ATM";
        $orderId = $bill_id;
        $redirectUrl = "http://localhost/menu.php?bill_id=" . $bill_id;
        $ipnUrl = "http://localhost/menu.php";
        $requestId = time() . "";
        $requestType = "payWithATM";
        $extraData = ""; // Thông tin bổ sung

        // Tạo chuỗi hash để ký
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        // Dữ liệu gửi đi
        $data = [
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount, // Gán giá trị amount
            'orderId' => $orderId,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipnUrl' => $ipnUrl,
            'lang' => 'vi',
            'extraData' => $extraData,
            'requestType' => $requestType,
            'signature' => $signature
        ];

        // Gửi yêu cầu thanh toán đến MoMo
        $result = execPostRequest($endpoint, json_encode($data));
        $jsonResult = json_decode($result, true);  // Giải mã JSON

        // Chuyển hướng đến trang thanh toán của MoMo
        header('Location: ' . $jsonResult['payUrl']);
        exit();
    } else {
        echo "<p>Không tìm thấy hóa đơn.</p>";
        exit();
    }
}

// Xử lý phản hồi từ MoMo
// Xử lý phản hồi từ MoMo
$data = file_get_contents('php://input');
$json_data = json_decode($data, true);

if (isset($json_data['status'], $json_data['orderId'])) {
    $status = $json_data['status'];
    $orderId = $json_data['orderId'];

    // Truy vấn thông tin hóa đơn từ bảng bill dựa trên orderId
    $select_bill = $conn->prepare("SELECT * FROM `orders` WHERE id = ?");
    $select_bill->execute([$orderId]);

    if ($select_bill->rowCount() > 0) {
        $bill_info = $select_bill->fetch(PDO::FETCH_ASSOC);
        $bill_id = $bill_info['id'];
        $user_id = $bill_info['user_id'];

        if ($status == 'success') {
            // Chuyển hướng về trang menu khi thanh toán thành công
            header('Location: menu.php');
            exit();
        } else {
            // Chuyển hướng về trang thông tin thanh toán nếu thanh toán thất bại
            header('Location: checkout.php');
            exit();
        }
    } else {
        echo "Không tìm thấy hóa đơn tương ứng.";
    }
} else {
    echo "Dữ liệu không hợp lệ.";
}
