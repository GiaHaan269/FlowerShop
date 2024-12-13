<?php
    include 'components/connect.php';

    if(isset($_COOKIE['user_id'])){
        $user_id = $_COOKIE['user_id'];
    }else{
        $user_id = '';
    }

    
?>




<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name = "viewport" content="width=device-width, initial-scale=1">
    <link href='https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css' rel='stylesheet'>
    <link rel="shortcut icon" href="image/logo.png" type="image/vnd.microsoft.icon">
    <link rel="stylesheet" type="text/css" href="css/user_style.css?v = <?php echo time(); ?>">
    <title>Giới Thiệu</title>
</head>
<body>
    <?php include 'components/user_header.php'; ?>

    <div class="banner">
        <div class="detail">
            <h1>Giới Thiệu</h1>
            <p>Chào mừng đến với shop hoa của chúng tôi, nơi tinh tế của sắc hoa và yêu thương</p>
            <span><a href="home.php">trang chủ</a><i class="bx bx-right-arrow-alt"></i>Giới Thiệu</span>
        </div>
    </div>
    <div class="who">
        <div class="box-container">
            <div class="box">
                <div class="heading">
                    <span>Giới Thiệu</span>
                    <h1>Hoa là biểu tượng của sự tươi mới và hy vọng.</h1>
                    <img src="image/separator.png">
                </div>
                <p>Không gian sang trọng và ấm áp của shop là nơi bạn có thể thả mình vào thế giới của hoa và tìm cho mình một sắc hoa phản ánh tâm trạng, 
                    thông điệp riêng của mình. Đến với Vanila, 
                    bạn không chỉ mua những bó hoa tinh khôi mà còn được trải nghiệm chân thành và sự chăm sóc đặc biệt mỗi khi ghé thăm.</p>
                <div class="flex-btn">
                    <a href="menu.php" class="btn">Xem Thêm</a>
                    <a href="menu.php" class="btn">Cửa Hàng</a>
                </div>
            </div>
            <div class="img-box">
                <img src="image/shop1.png" class="img">
                <!-- <img src="image/shap.png" class="shape"> -->
            </div>
        </div>
    </div>
    <div class="use">
        <div class="box-container">
            <div class="box">
                <!-- <img src="image/flowers.png" class="img"> -->
            </div> 
            <div class="box">
                <h1>Những bó hoa lộng lẫy và sang trọng</h1>
                <p>Chúng tôi cam kết mang những bó hoa tươi và đặc biệt nhất gửi đến người đặc biệt của bạn, 
                    vào dịp Lễ Tình Nhân hay Ngày Của Mẹ hoặc những dịp đặc biệt khác. 
                    Nếu quý khách có yêu cầu đặc biệt cho bó hoa của mình, hãy liên lạc với chúng tôi.
                </p>
                <div class="icon">
                    <div class="icon-detail">
                        <div class="img-box"><img src="image/use.png"></div>
                        <p>hoa chất lượng</p>
                    </div>
                    <div class="icon-detail">
                        <div class="img-box"><img src="image/use0.png"></div>
                        <p>mịn màng</p>
                    </div>
                </div>
                <div class="icon">
                    <div class="icon-detail">
                        <div class="img-box"><img src="image/use1.png"></div>
                        <p>tăng trưởng hữu cơ</p>
                    </div>
                    <div class="icon-detail">
                        <div class="img-box"><img src="image/use2.png"></div>
                        <p>Không có hoá chất</p>
                    </div>
                </div>
                <div class="flex-btn">
                    <a href="menu.php" class="btn">Cửa hàng</a>
                    <a href="contact.php" class="btn">gọi cho chúng tôi</a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="about">
        <div class="box-container">
            <div class="box">
                <div class="heading">
                    <span>Về Cửa Hàng Chúng Tôi</span>
                    
                </div>
            
                <p>Vanila Flowers là 1 trong những shop hoa tươi ở Việt Nam được đánh giá rất cao về chất lượng sản phẩm, dịch vụ và tuổi đời thương hiệu. 
                     ghi nhận không chỉ bởi khách hàng mà Vanila Flowers còn được các cơ quan báo chí, đài truyền hình Việt Nam trực tiếp đến phỏng vấn và hỗ trợ quảng bá.
                    Cung cấp hơn 500 sản phẩm hoa hồng và các loại khác nhau được nhập khẩu từ các nước Châu Âu với chất lượng cao cho khách hàng. 
                    Chúng tôi luôn luôn chăm chút đến từng chi tiết để người nhận hoa có được trải nghiệm tặng quà thật tuyệt vời.</p>
            </div>
        </div>
    </div>
    <div class="choose">
        <div class="box-container">
            <div class="img-box">
                <img src="image/hinh2.png"> 
            </div>
            <div class="box">
                <div class="heading">
                    <span>TẠI SAO CHỌN CHÚNG TÔI</span>
                    <h1>Dịch Vụ Khách hàng</h1>
                </div>
                <div class="box-detail">
                    <div class="detail">
                        <img src="image/discount.png">
                        <h2>giảm giá</h2>
                        
                        <span>1</span>
                    </div>
                    <div class="detail">
                        <img src="image/gift.png">
                        <h2>ưu đãi quà tặng</h2>
                        
                        <span>2</span>
                    </div>
                    <div class="detail">
                        <img src="image/return.png">
                        <h2>chính sách hoàn trả</h2>
                        
                        <span>3</span>
                    </div>
                    <div class="detail">
                        <img src="image/support.png">
                        <h2>hỗ trợ trực tuyến</h2>
                        
                        <span>4</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php include 'components/user_footer.php'; ?>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/sweetalert/2.1.2/sweetalert.min.js"></script>
    <script type="text/javascript" src="js/user_script.js"></script>
    <?php include 'components/alert.php'; ?>
    
</body>
    
</html>