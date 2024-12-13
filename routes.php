<?php
define('BASEPATH') OR exit('No direct script access allowed');

$route['confirm-checkout']['POST'] ='IndexController/confirm_checkout';
$route['online-checkout']['POST'] = 'onlinecheckoutController/online_checkout';
?>