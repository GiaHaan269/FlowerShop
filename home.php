<?php
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
<html>
<head>
    <meta charset="utf-8">
    <meta name = "viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <title>Trang Chủ</title>
</head>
<body>
    <?php include 'components/user_header.php'; ?>

    <div class="slider-container">
        <div class="container">
            <div class="slider-item active" >
                <img src="image/slider5.png" width="80">
            </div>
            <div class="slider-item " >
                <img src="image/slider2.jpg" >
            </div>
            <div class="slider-item " >
                <img src="image/slider1.png" >
            </div>
        </div>
        <div class="left-arrow" onclick="nextSlide()"><i class="bx bx-left-arrow-alt"></i></div>
        <div class="right-arrow" onclick="prevSlide()"><i class="bx bx-right-arrow-alt"></i></div>
    </div>

    <div class="services">
        <div class="box-container">
            <div class="box">
                <div class="icon">
                    <img src="image/service.png">
                </div>
                <div class="detail">
                    <h4>mua sắm trực tuyến</h4>
                    <span>uy tín</span>
                </div>
            </div>
            <div class="box">
                <div class="icon">
                    <img src="image/services2.png">
                </div>
                <div class="detail">
                    <h4>chất lượng sản phẩm</h4>
                    <span>an toàn</span>
                </div>
            </div>
            <div class="box">
                <div class="icon">
                    <img src="image/services.png">
                </div>
                <div class="detail">
                    <h4>giao hàng</h4>
                    <span> 24 * 7 giờ</span>
                </div>
            </div>
            <div class="box">
                <div class="icon">
                    <img src="image/services0.png">
                </div>
                <div class="detail">
                    <h4>dịch vụ khách hàng</h4>
                    <span>hỗ trợ </span>
                </div>
            </div>
            <div class="box">
                <div class="icon">
                    <img src="image/services0.png">
                </div>
                <div class="detail">
                    <h4>hỗ trợ khách hàng</h4>
                    <span>hỗ trợ</span>
                </div>
            </div>
            <div class="box">
                <div class="icon">
                    <img src="image/services1.png">
                </div>
                <div class="detail">
                    <h4>dịch vụ quà tặng</h4>
                    <span>hỗ trợ</span>
                </div>
            </div>
        </div>
    </div>

    <img src="image/s-banner.png" class="sub-banner">
    <!-- <div class="frame-container">
        <div class="box-container">
            <div class="frame">
                <div class="detail">
                    <span>shop seasonal</span>
                    <h2>50% of</h2>
                    <h1>all seasonal flowers</h1>
                    <a class="btn">shop now</a> 
                </div>
            </div>
            <div class="box">
                <div class="box-detail">
                    <img src="image/shopnow4.png" class="img">
                    <div class="img-detail">
                        <span>wide range</span>
                        <h1>fresh organic flowers</h1>
                        <a class="btn">shop now</a>
                    </div>
                </div>
                <div class="box-detail">
                    <img src="image/shopnow3.png" class="img">
                    <div class="img-detail">
                        <span>wide range</span>
                        <h1>fresh organic flowers</h1>
                        <a class="btn">shop now</a>
                    </div>
                </div>
                
            </div>
            </div>
        </div>
    </div> -->
    <div class="about-us">
        <div class="box-container">
            <div class="img-box">
                <img src="image/shopnow5.png" class="img">
                    
                <img src="image/shopnow4.png" class="img1">
                <div class="play"><i class="bx bx-play"></i></div>
            </div>
            <div class ="box">
                <div class="heading">
                    <span>Tại sao chọn chúng tôi</span>
                    <h1> vanila flower shop</h1>
                    <img src="image/separator.png">
                    <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương</p>
                    <a href="about.php" class="btn">chi tiết</a>
                    <a href="contact.php" class="btn">liên hệ với chúng tôi</a>
                </div>
            </div>
        </div>
    </div>

    <!-- <div class="sub-banner">
        <div class ="box-container">
            <img src="image/sub-banner.png">
            <img src="image/s-banner.png" height="85%">
        </div>
    </div> -->

    <div class="categories">
        <div class="heading">
            <h1>Danh Mục</h1>
            <img src="image/separator.png">
        </div>
        <div class="box-container">
            <div class="box">
                <img src="image/hoatuoi.jpg">
                <div class="detail">
                    <span>hoa tươi từ trang trại</span>
                    <h1>Hoa Tươi</h1>
                    <a href="menu.php" class="btn">Cửa hàng</a>
                </div>
            </div>
            <div class="box">
                <img src="image/giohoa.jpg">
                <div class="detail">
                    <span>hoa tươi từ trang trại</span>
                    <h1>Giỏ Hoa</h1>
                    <a href="menu.php" class="btn">Cửa hàng</a>
                </div>
            </div>
            <div class="box">
                <img src="image/hoacuoi.jpg">
                <div class="detail">
                    <span>hoa tươi từ trang trại</span>
                    <h1>Hoa Cưới</h1>
                    <a href="menu.php" class="btn">Cửa hàng</a>
                </div>
            </div>
            <div class="box">
                <img src="image/hoasinhnhat.jpg">
                <div class="detail">
                    <span>hoa tươi từ trang trại</span>
                    <h1>Hoa Sinh Nhật</h1>
                    <a href="menu.php" class="btn">Cửa hàng</a>
                </div>
            </div>
        </div>
    </div>
    <div class="sub-banner">
        <div class ="box-container">
            <img src="image/sub-banner2.avif">
            <img src="image/sub-banner3.avif">
        </div>
    </div>
    <div class="offer">
        <div class="heading">
            <h1>Hoa Tươi</h1>
            <img src="image/separator.png">
        </div>
        <div class="box-container">
            <div class="box">
                <div class="detail">
                    <h1>Hoa Tulip</h1>
                    <p>Hoa là biểu tượng của sự tươi mới và hy vọng.</p>
                    <a href="menu.php" class="btn">Cửa hàng</a>
                </div>
                <img src="image/tulip.jpg">
            </div>
            <div class="box">
                <div class="detail">
                    <h1>Hoa Hồng</h1>
                    <p>Hoa là biểu tượng của sự tươi mới và hy vọng.</p>
                    <a href="menu.php" class="btn">Cửa hàng</a>
                </div>
                <img src="image/hoahong.jpg">
            </div>
            <div class="box">
                <div class="detail">
                    <h1>Hoa Hướng Dương</h1>
                    <p>Hoa là biểu tượng của sự tươi mới và hy vọng.</p>
                    <a href="menu.php" class="btn">Cửa hàng</a>
                </div>
                <img src="image/huongduong.jpg">
            </div>
            <div class="box">
                <div class="detail">
                    <h1>Hoa Oải Hương</h1>
                    <p>Hoa là biểu tượng của sự tươi mới và hy vọng.</p>
                    <a href="menu.php" class="btn">Cửa hàng</a>
                </div>
                <img src="image/lavender2.jpg" >
            </div>
        </div>
    </div>
    <div class="products">
        <div class="heading">
            <h1>Sản phẩm</h1>
            <img src="image/separator.png">
        </div>
            <?php include 'components/shop.php'; ?>
    </div>
    
    <!-- <div class="offer-1">
        <div class="detail">
            <h1>giảm giá đặc biệt cho tất cả sản phẩm<br>sản phẩm hoa</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương.</p>
            <div class="container">
                <div id="countdown" style="color:;">
                    <ul>
                        <li><span id="days"></span>ngay</li>
                        <li><span id="hours"></span>gio</li>
                        <li><span id="minutes"></span>phut</li>
                        <li><span id="seconds"></span>giay</li>
                    </ul>
                </div>
            </div>
            <a href="shop.php" class="btn">mua ngay</a>
        </div>
    </div> -->
    <?php include 'components/user_footer.php'; ?>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>
    <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
    <!-- <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>
<df-messenger
  chat-title="Chat"
  agent-id="34b292d7-b2e4-4b3e-972b-99f6d1f4b395"
  language-code="en"
  chat-icon="uploaded_files/robot1.png"
></df-messenger> -->
    
</body>
    
</html>
