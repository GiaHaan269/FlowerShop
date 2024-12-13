<?php
include('components/connect.php');
require('carbon/autoload.php');

use Carbon\Carbon;
use Carbon\CarbonInterval;

$now = Carbon::now('Asia/Ho_Chi_Minh');

if(isset($GET['partnerCode'])){
    $partnerCode = $GET['partnerCode'];
    $orderId = $GET['orderId'];
    $requestId = $GET['requestId'];
    $amount = $GET['amount'];
    $orderInfo = $GET['orderInfo'];
    $orderType = $GET['orderType'];
    $transId = $GET['transId'];
    $payType = $GET['payType'];
    
}
?>