<?php
include 'components/connect.php';

if (isset($_COOKIE['user_id'])) {
    $user_id = $_COOKIE['user_id'];
} else {
    $user_id = '';
}
// Lấy thông tin người dùng
$user_name = '';
$user_email = '';
if ($user_id != '') {
    $query_user = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
    $query_user->execute([$user_id]);
    if ($query_user->rowCount() > 0) {
        $user = $query_user->fetch(PDO::FETCH_ASSOC);
        $user_name = $user['name'];
        $user_email = $user['email'];
    }
}

$messages_with_replies = [];
if ($user_id != '') {
    $query_replies = $conn->prepare("SELECT subject, message, reply FROM message WHERE user_id = ?");
    $query_replies->execute([$user_id]);
    if ($query_replies->rowCount() > 0) {
        $messages_with_replies = $query_replies->fetchAll(PDO::FETCH_ASSOC);
    }
}


if (isset($_POST['send_message'])) {
    if ($user_id != '') {
        $id = unique_id(); // Tạo ID duy nhất cho tin nhắn

        // Lấy thông tin tin nhắn
        $message = $_POST['message'];
        $message = filter_var($message, FILTER_SANITIZE_STRING);

        // Lấy thông tin người dùng
        $query_user = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
        $query_user->execute([$user_id]);
        if ($query_user->rowCount() > 0) {
            $user = $query_user->fetch(PDO::FETCH_ASSOC);
            $user_name = $user['name'];
            $user_email = $user['email'];

            // Lưu tin nhắn vào bảng message
            $insert_message = $conn->prepare("INSERT INTO `message` (id, user_id, name, email, subject, message, reply) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $insert_message->execute([$id, $user_id, $user_name, $user_email, 'No Subject', $message, '']);
            $success_msg[] = 'Tin nhắn của bạn đã được gửi thành công';
        }
    } else {
        $warning_msg[] = 'Vui lòng đăng nhập để gửi tin nhắn';
    }
}


$user_name = '';
$user_email = '';
if ($user_id != '') {
    $query_user = $conn->prepare("SELECT name, email FROM users WHERE id = ?");
    $query_user->execute([$user_id]);
    if ($query_user->rowCount() > 0) {
        $user = $query_user->fetch(PDO::FETCH_ASSOC);
        $user_name = $user['name'];
        $user_email = $user['email'];
    }
}




?>




<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <title>Liên hệ</title>
</head>

<body>
    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Liên hệ</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <span><a href="home.php">Trang chủ</a><i class="bx bx-right-arrow-alt"></i>Liên hệ</span>
        </div>
    </div>
    <div class="contact">
        <div class="heading">
            <h1>Gửi tin nhắn cho chúng tôi</h1>
            <p>Vanila Flowers luôn nỗ lực hết mình để cung cấp cho quý khách dịch vụ giao hoa đúng giờ và chuyên nghiệp
                nhất.</p>
            <img src="image/separator.png">
        </div>
        <div class="chat-container">


            <!-- Hiển thị tin nhắn và phản hồi -->
            <div class="chat-messages">
                <?php
                $messages_with_replies = [];
                if ($user_id != '') {
                    $query_replies = $conn->prepare("SELECT id, subject, message, reply FROM message WHERE user_id = ?");
                    $query_replies->execute([$user_id]);
                    if ($query_replies->rowCount() > 0) {
                        $messages_with_replies = $query_replies->fetchAll(PDO::FETCH_ASSOC);
                    }
                }

                // Hiển thị tin nhắn và phản hồi
                foreach ($messages_with_replies as $msg) {
                    // Tin nhắn của người dùng (hiển thị bên phải)
                    echo '<div class="chat-message user">';
                    echo '<div class="bubble">' . nl2br(htmlspecialchars($msg['message'])) . '</div>';
                    echo '</div>';

                    // Phản hồi từ quản trị viên (hiển thị bên trái)
                    if (!empty($msg['reply'])) {
                        echo '<div class="chat-message admin">';
                        echo '<div class="bubble">' . nl2br(htmlspecialchars($msg['reply'])) . '</div>';
                        echo '</div>';
                    } else {
                        echo '<div class="chat-message admin">';
                        // echo '<div class="bubble"><em>Chưa có phản hồi từ quản trị viên</em></div>';
                        echo '</div>';
                    }
                }
                ?>
            </div>

            <!-- Form gửi tin nhắn -->
            <form action="" method="post" class="chat-form">
                <textarea name="message" placeholder="Nhập tin nhắn của bạn..." required class="chat-input"></textarea>
                <button type="submit" name="send_message" class="send-btn">Gửi tin nhắn</button>
            </form>
        </div>



    </div>
    <div class="address">
        <div class="heading">
            <h1>Chi tiết liên hệ</h1>
            <p>Vanila Flowers luôn nỗ lực hết mình để cung cấp cho quý khách dịch vụ giao hoa đúng giờ và chuyên nghiệp
                nhất.</p>
            <img src="image/separator.png">
        </div>
        <div class="box-container">
            <div class="box">
                <i class="bx bxs-map-alt"></i>
                <div>
                    <h4>Địa chỉ</h4>
                    <p>123, Đường 3 Tháng 2, <br>
                        Ninh Kiều, Cần Thơ</p>
                </div>
            </div>
            <div class="box">
                <i class="bx bxs-phone-incoming"></i>
                <div>
                    <h4>Số điện thoại</h4>
                    <p>0999223344</p>
                    <p>0997869977</p>
                </div>
            </div>
            <div class="box">
                <i class="bx bxs-envelope"></i>
                <div>
                    <h4>Email</h4>
                    <p>vanila@gmail.com</p>
                    <p>sellervanila@gmail.com</p>
                </div>
            </div>
        </div>
    </div>


    <?php include 'components/user_footer.php'; ?>
    <script>
        document.querySelector('form').addEventListener('submit', async function (e) {
            e.preventDefault(); // Ngăn tải lại trang

            const formData = new FormData(this);
            const response = await fetch('', {
                method: 'POST',
                body: formData
            });

            if (response.ok) {
                const newMessage = await response.text(); // Lấy nội dung tin nhắn vừa gửi
                document.querySelector('.chat-container').innerHTML += `
            <div class="chat-message user">
                <div class="bubble">
                    <p><strong>Bạn:</strong> ${formData.get('message')}</p>
                </div>
            </div>
        `;
                this.reset(); // Xóa nội dung form
            } else {
                alert('Có lỗi xảy ra. Vui lòng thử lại.');
            }
        });
    </script>
    </script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>
    <!-- <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
    <df-messenger intent="WELCOME" chat-title="Xin chào" agent-id="f0597888-4dac-4b4c-8f8f-7bea88eb090d"
        language-code="en" chat-icon="uploaded_files/robot.jpg"></df-messenger> -->

</body>

</html>
<style>
    /* Style cho chat container */
    .chat-container {
        max-width: 600px;
        margin: 0 auto;
        padding: 20px;
        background-color: #fff;
        border-radius: 10px;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
    }

    /* Style cho header của chat */
    .chat-header {
        text-align: center;
        margin-bottom: 20px;
    }

    .chat-header h1 {
        font-size: 24px;
        color: #333;
    }

    /* Style cho phần tin nhắn */
    .chat-messages {
        display: flex;
        flex-direction: column;
        gap: 10px;
        margin-bottom: 20px;
    }

    .chat-message {
        display: flex;
        gap: 10px;
    }

    .chat-message.user {
        justify-content: flex-end;
    }

    .chat-message.admin {
        justify-content: flex-start;
    }

    .bubble {
        max-width: 70%;
        padding: 10px 15px;
        border-radius: 15px;
        background-color: #e6f7ff;
        color: #333;
        font-size: 14px;
        box-shadow: 0 1px 5px rgba(0, 0, 0, 0.1);
    }

    .chat-message.user .bubble {
        background-color: #d1e7dd;
        text-align: right;
    }

    .chat-message.admin .bubble {
        background-color: #ffe4e1;
        text-align: left;
    }

    /* Style cho form gửi tin nhắn */
    .chat-form {
        display: flex;
        flex-direction: column;
    }

    .chat-input {
        padding: 12px;
        font-size: 16px;
        border: 1px solid #ddd;
        /* Viền nhẹ, màu sáng */
        border-radius: 12px;
        /* Viền bo tròn mềm mại */
        margin-bottom: 15px;
        resize: vertical;
        width: 100%;
        box-sizing: border-box;
        /* Đảm bảo chiều rộng đầy đủ */
        background-color: #f9f9f9;
        /* Màu nền nhẹ nhàng */
        color: #333;
        /* Màu chữ tối */
        transition: border-color 0.3s ease, box-shadow 0.3s ease;
        /* Hiệu ứng chuyển tiếp mượt mà */
    }

    /* Thêm hiệu ứng khi người dùng tập trung vào ô nhập liệu */
    .chat-input:focus {
        outline: none;
        /* Loại bỏ viền mặc định */
        border-color: #ffe4e1;
        /* Màu viền khi focus */
        box-shadow: 0 0 8px #ffe4e1;
        /* Thêm bóng đổ để làm nổi bật */
    }

    /* Thêm một chút căn chỉnh cho font chữ */
    .chat-input::placeholder {
        color: #aaa;
        /* Màu chữ placeholder nhẹ nhàng */
        font-style: italic;
        /* Để placeholder có kiểu chữ nghiêng */
    }


    .send-btn {
        padding: 10px 20px;
        background-color: #f2a7ad;
        /* Màu nền xanh */
        color: white;
        border: none;
        cursor: pointer;
        font-size: 16px;
        border-radius: 5px;
        border-radius: 1rem;
    }

    .send-btn:hover {
        background-color: #45a049;
    }
</style>