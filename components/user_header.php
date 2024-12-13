<header class="header">
    <section class="flex">
        <a href="home.php"><img src="image/logo.png" width="130px"></a>
        <nav class="navbar">
            <a href="home.php">Trang Chủ</a>
            <a href="about.php">Giới Thiệu</a>
            <a href="menu.php">cửa hàng</a>
            <a href="order.php">đơn hàng</a>
            <a href="contact.php">liên hệ</a>
        </nav>
        <!-- <form id="uploadForm" action="upload_image.php" method="POST" enctype="multipart/form-data">
            <label for="image" id="camera-icon" style="cursor: pointer;">
                <i class="bx bx-camera"  style="font-size: 30px; color:#f2a7ad"></i>
            </label>
            <input type="file" name="image" id="image" accept="image/*" required style="display: none;">
            <button type="submit" style="display: none;">Upload</button> 
        </form> -->
        <form action="search_product.php" method="post" class="search-form">

            <input type="text" name="search_product" placeholder="Tìm sản phẩm.." required maxlength="100">
            <button type="submit" class="bx bx-search-alt-2" name="search_product_btn"></button>
            
        </form>
        <!-- <form id="uploadForm" action="upload_image.php" method="POST" enctype="multipart/form-data">
            <label for="image" id="camera-icon" style="cursor: pointer;">
                <i class="bx bx-camera" style="font-size: 30px;"></i> 
            </label>
            <input type="file" name="image" id="image" accept="image/*" required style="display: none;">
            <button type="submit" style="display: none;">Upload</button> 
        </form> -->


        <!-- Khu vực hiển thị kết quả dự đoán -->
        <div id="result" style="margin-top: 20px;"></div>

        <div class="icons">
            <form id="uploadForm" action="search.php" method="POST" enctype="multipart/form-data">
        <label for="image" id="camera-icon" style="cursor: pointer;">
            <i class="bx bx-camera" style="font-size: 30px; color:#f2a7ad"></i>
        </label>
        <input type="file" name="image" id="image" accept="image/*" required style="display: none;">
        <button type="submit"></button>
    </form>

        
            <div id="menu-btn" class="bx bx-list-plus"></div>
            <div id="search-btn" class="bx bx-search-alt-2"></div>

            <?php
            $count_wishlist_item = $conn->prepare("SELECT * FROM `wishlist` WHERE user_id =?");
            $count_wishlist_item->execute([$user_id]);
            $total_wishlist_item = $count_wishlist_item->rowCount();
            ?>

            <a href="wishlist.php" class="cart-btn"><i class="bx bx-heart"></i><sup>
                    <?=
                        $total_wishlist_item ?>
                </sup></a>
            <?php
            $count_cart_item = $conn->prepare("SELECT * FROM `cart` WHERE user_id =?");
            $count_cart_item->execute([$user_id]);
            $total_cart_item = $count_cart_item->rowCount();
            ?>
            <a href="cart.php" class="cart-btn"><i class="bx bx-cart"></i><sup>
                    <?=
                        $total_cart_item ?>
                </sup></a>
            <div class="bx bxs-user" id="user-btn"></div>
        </div>
        <div class="profile">
            <?php

            $select_profile = $conn->prepare("SELECT * FROM `users` WHERE id=?");
            $select_profile->execute([$user_id]);

            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
                ?>
                <!-- <img src="uploaded_files/ ?>"> -->
                <h3 style="margin-bottom: 1rem"><?= $fetch_profile['name']; ?></h3>
                <div class="flex-btn">
                    <a href="profile.php" class="btn">xem hồ sơ</a>
                    <a href="components/user_logout.php" onclick="return confirm('Đăng xuất khỏi trang này');"
                        class="btn">đăng xuất</a>
                </div>
            <?php } else { ?>

                <!-- <img src="image/user.jpg" alt=""> -->
                <h3 style="margin-bottom: 1rem">vui lòng đăng ký hoặc đăng nhập</h3>
                <div class="flex-btn">
                    <a href="login.php" class="btn">đăng nhập</a>
                    <a href="register.php" class="btn">đăng ký</a>
                </div>


            <?php } ?>
        </div>
    </section>
</header>
<script>
    // Lắng nghe sự kiện khi người dùng chọn ảnh
    document.getElementById('image').addEventListener('change', function (event) {
        var formData = new FormData();
        var fileInput = document.getElementById('image');

        if (fileInput.files.length > 0) {
            formData.append('image', fileInput.files[0]);

            // Gửi tệp ảnh lên server bằng AJAX
            var xhr = new XMLHttpRequest();
            xhr.open('POST', 'upload_image.php', true);

            // Khi quá trình tải lên hoàn tất
            xhr.onload = function () {
                if (xhr.status === 200) {
                    // Nếu tải lên thành công, hiển thị kết quả
                    var resultDiv = document.getElementById('result');
                    resultDiv.innerHTML = '<h3>Kết quả dự đoán:</h3>' + xhr.responseText;
                    window.location.href = 'result.php';
                } else {
                    console.error('Lỗi khi tải ảnh lên!');
                }
            };

            // Gửi yêu cầu
            xhr.send(formData);
        }
    });
</script>
