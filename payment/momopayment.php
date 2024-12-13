<?php
    include 'components/connect.php';
    session_start();
    $admin_id = $_SESSION['user_name'];
    $user_id = $_SESSION['user_id'];
header('Content-type: text/html; charset=utf-8');

include('helper_momo.php');

$select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id='$user_id'") or die('query failed');
$grand_total = 0;
if(mysqli_num_rows($select_cart) > 0){
    while($fetch_cart = mysqli_fetch_assoc($select_cart)){
        $total_price = ($fetch_cart['product_price'] * $fetch_cart['quantity']);
        $grand_total += $total_price;
    }
}

$amount = $grand_total;

$endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
$partnerCode = 'MOMOBKUN20180529';
$accessKey = 'klm05TvNBzhg7h7j';
$secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

$orderInfo = "Thanh toán qua mã qr MoMo";
$orderId = time() . "";
$redirectUrl = "http://localhost/flowershop/checkout.php";
$ipnUrl = "http://localhost/flowershop/checkout.php";
$extraData = "";
$requestId = time() . "";
$requestType = "captureWallet";

$rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
$signature = hash_hmac("sha256", $rawHash, $secretKey);
$data = array(
    'partnerCode' => $partnerCode,
    'partnerName' => "Test",
    "storeId" => "MomoTestStore",
    'requestId' => $requestId,
    'amount' => $amount,
    'orderId' => $orderId,
    'orderInfo' => $orderInfo,
    'redirectUrl' => $redirectUrl,
    'ipnUrl' => $ipnUrl,
    'lang' => 'vi',
    'extraData' => $extraData,
    'requestType' => $requestType,
    'signature' => $signature
);

$result = execPostRequest($endpoint, json_encode($data));
$jsonResult = json_decode($result, true);

if (isset($jsonResult['payUrl'])) {
    $payUrl = $jsonResult['payUrl'];
    header('Location: ' . $payUrl);
} else {
    echo "Không tìm thấy URL thanh toán.";
}
?>
