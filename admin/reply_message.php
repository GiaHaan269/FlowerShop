<?php
include '../components/connect.php';

if (isset($_COOKIE['seller_id'])) {
    $seller_id = $_COOKIE['seller_id'];
} else {
    $seller_id = '';
    header('location:login.php');
}
// Lấy ID của khách hàng từ URL (hoặc bạn có thể lấy từ session)
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];
    $user_id = filter_var($user_id, FILTER_SANITIZE_STRING);

    // Lấy tất cả tin nhắn của khách hàng này
    $select_messages = $conn->prepare("SELECT * FROM `message` WHERE user_id = ? ORDER BY created_at DESC");
    $select_messages->execute([$user_id]);
} else {
    header('location:messages.php'); // Quay lại trang danh sách nếu không có user_id
}

// Lấy ID của tin nhắn từ URL
if (isset($_GET['message_id'])) {
    $message_id = $_GET['message_id'];
    $message_id = filter_var($message_id, FILTER_SANITIZE_STRING);

    // Lấy thông tin tin nhắn từ cơ sở dữ liệu
    $select_message = $conn->prepare("SELECT * FROM `message` WHERE id = ?");
    $select_message->execute([$message_id]);
    $fetch_message = $select_message->fetch(PDO::FETCH_ASSOC);
} else {
    header('location:messages.php'); // Quay lại trang danh sách nếu không có ID
}

if (isset($_GET['message_id']) && isset($_GET['user_id'])) {
    $message_id = $_GET['message_id'];
    $user_id = $_GET['user_id'];

    $message_id = filter_var($message_id, FILTER_SANITIZE_STRING);
    $user_id = filter_var($user_id, FILTER_SANITIZE_STRING);

    // Lấy tất cả tin nhắn của khách hàng này
    $select_messages = $conn->prepare("SELECT * FROM `message` WHERE user_id = ? ORDER BY created_at DESC");
    $select_messages->execute([$user_id]);

    // Lấy thông tin tin nhắn cụ thể
    $select_message = $conn->prepare("SELECT * FROM `message` WHERE id = ?");
    $select_message->execute([$message_id]);
    $fetch_message = $select_message->fetch(PDO::FETCH_ASSOC);
} else {
    header('location:messages.php'); // Quay lại trang danh sách nếu không có ID tin nhắn hoặc ID khách hàng
}

if (isset($_POST['reply'])) {
    $reply_message = $_POST['reply_message'];
    $reply_message = filter_var($reply_message, FILTER_SANITIZE_STRING);

    // Kiểm tra nếu có phản hồi cũ, nối phản hồi mới vào
    if (!empty($fetch_message['reply'])) {
        // Nối phản hồi mới vào phản hồi cũ
        $new_reply = $fetch_message['reply'] . "\n\n" . $reply_message;
    } else {
        // Nếu không có phản hồi cũ, sử dụng phản hồi mới
        $new_reply = $reply_message;
    }

    // Cập nhật phản hồi vào cơ sở dữ liệu
    $update_reply = $conn->prepare("UPDATE `message` SET reply = ? WHERE id = ?");
    $update_reply->execute([$new_reply, $message_id]);

    $success_msg[] = 'Phản hồi đã được gửi';
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
    <title>Phản hồi tin nhắn</title>
</head>

<body>
    <?php include '../components/admin_header.php'; ?>
    <div class="banner">
        <div class="detail">
            <h1>Phản hồi tin nhắn</h1>
            <span><a href="dashboard.php">Quản trị</a><i class="bx bx-right-arrow-alt"></i><a href="messages.php">Tin
                    nhắn khách hàng</a><i class="bx bx-right-arrow-alt"></i>Phản hồi tin nhắn</span>
        </div>
    </div>

    <section class="message-container">
    <div class="heading">
        <h1>Xem tin nhắn và phản hồi</h1>
        <img src="../image/separator.png">
    </div>

    <div class="box-container">
        <div class="chat-messages">
            <?php
                // Hiển thị tất cả tin nhắn và phản hồi
                while ($fetch_message = $select_messages->fetch(PDO::FETCH_ASSOC)) {
                    ?>
                    <div class="chat-message user">
                        <div class="bubble"><?= nl2br(htmlspecialchars($fetch_message['message'])); ?></div>
                    </div>
    
                    <div class="chat-message admin">
                        <?php
                        if (!empty($fetch_message['reply'])) {
                            // Hiển thị phản hồi cũ và mới
                            echo '<div class="bubble">' . nl2br(htmlspecialchars($fetch_message['reply'])) . '</div>';
                        } else {
                            echo '<div class="bubble"><em>Chưa có phản hồi từ quản trị viên</em></div>';
                        }
                        ?>
                    </div>
                    <?php
                }
                ?>
            </div>
    
            <!-- Form phản hồi -->
            <form action="" method="post">
                <textarea name="reply_message" rows="5" placeholder="Nhập phản hồi của bạn..."></textarea>
                <button type="submit" name="reply" class="btn">Gửi phản hồi</button>
            </form>
        </div>
    </section>


    <?php include '../components/admin_footer.php'; ?>
    <script type="text/javascript" src="../js/admin_script.js"></script>
</body>

</html>
<style>
    /* Kiểu cho các tin nhắn và phản hồi */
.chat-messages {
    margin-top: 2rem;
}

.chat-message {
    margin-bottom: 1rem;
}

.chat-message.user .bubble {
    background-color: #e6e6e6;
    padding: 10px;
    border-radius: 10px;
    max-width: 70%;
    margin-left: auto;
}

.chat-message.admin .bubble {
    background-color: #f4f4f4;
    padding: 10px;
    border-radius: 10px;
    max-width: 70%;
}

textarea {
    width: 100%;
    padding: 10px;
    margin-top: 1rem;
    border-radius: 5px;
}

button.btn {
    margin-top: 1rem;
    padding: 10px 20px;
    background-color: #4CAF50;
    color: white;
    border: none;
    cursor: pointer;
}

button.btn:hover {
    background-color: #45a049;
}

</style>