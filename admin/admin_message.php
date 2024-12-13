<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
}

$messages = [];
// Lấy tất cả các tin nhắn của người dùng
$query_messages = $conn->prepare("SELECT * FROM message ORDER BY id DESC");
$query_messages->execute();
if ($query_messages->rowCount() > 0) {
    $messages = $query_messages->fetchAll(PDO::FETCH_ASSOC);
}

if (isset($_POST['send_reply'])) {
    $message_id = $_POST['message_id'];
    $reply = $_POST['reply'];
    $reply = filter_var($reply, FILTER_SANITIZE_STRING);

    // Lưu phản hồi của quản trị viên vào bảng message
    $update_reply = $conn->prepare("UPDATE message SET reply = ? WHERE id = ?");
    $update_reply->execute([$reply, $message_id]);

    $success_msg[] = "Phản hồi đã được gửi.";
}
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="../image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="../css/admin_style.css?v=<?php echo time(); ?>">
    <title>Quản Trị - Tin Nhắn</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Tin Nhắn</h1>
            <span><a href="dashboard.php">Quản trị</a><i class="bx bx-right-arrow-alt"></i>Xem Tin Nhắn</span>
        </div>
    </div>

    <section class="message-section">
        <div class="heading">
            <h1>Xem Tin Nhắn</h1>
            <img src="../image/separator.png" width="100">
        </div>
        <div class="review-section">
            <?php
            // Hiển thị tin nhắn
            foreach ($messages as $message) {
                $user_name = $message['name'];
                $user_message = $message['message'];
                $user_reply = $message['reply'];

                echo '<div class="product-reviews">';
                echo '<h3 style="font-size: 1.5rem; font-weight: bold;">Tin nhắn từ: ' . htmlspecialchars($user_name) . '</h3>';

                echo '<p style="font-size: 1.4rem; line-height: 1.5; color: #000;"><strong>Nội dung:</strong> ' . nl2br(htmlspecialchars($user_message)) . '</p>';

                if (!empty($user_reply)) {
                    echo '<p style="font-size: 1.4rem; line-height: 1.5; color: #000;"><strong>Phản hồi:</strong> ' . nl2br(htmlspecialchars($user_reply)) . '</p>';
                } else {
                    echo '<p style="font-size: 1.4rem; line-height: 1.5; color: #000;"><strong>Phản hồi:</strong> Chưa có phản hồi.</p>';
                }


                // Form để gửi phản hồi
                echo '<form action="" method="POST">';
                echo '<textarea name="reply" placeholder="Nhập phản hồi..." required></textarea>';
                echo '<input type="hidden" name="message_id" value="' . $message['id'] . '">';
                echo '<button type="submit" name="send_reply" class="btn">Gửi Phản Hồi</button>';
                echo '</form>';
                echo '</div>';
            }
            ?>
        </div>
    </section>

    <?php include '../components/admin_footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="../js/admin_script.js"></script>
    <?php include '../components/alert.php'; ?>
</body>

</html>

<style>
    /* Style cho bảng tin nhắn */
table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 20px;
}

table, th, td {
    border: 1px solid #ddd;
}

th, td {
    padding: 10px;
    text-align: left;
}

th {
    background-color: #f2f2f2;
}

td {
    background-color: #f9f9f9;
}

textarea {
    width: 100%;
    padding: 10px;
    font-size: 14px;
    border: 1px solid #ddd;
    border-radius: 5px;
}

.send-btn {
    padding: 10px 20px;
    background-color: #f2a7ad;
    color: white;
    border: none;
    cursor: pointer;
    font-size: 16px;
    border-radius: 5px;
}

.send-btn:hover {
    background-color: #45a049;
}

.success-msg {
    color: green;
    font-weight: bold;
    margin-bottom: 15px;
}



</style>