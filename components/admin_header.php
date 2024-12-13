<header>
    <div class="logo">
        <img src="../image/logo.png" width="150">
    </div>
    <div class="right">
        <div class="" id="user-btn"></div>
        <div class="toggle-btn"><i class="bx bx-menu"></i></div>
    </div>
    <div class="profile-detail">
        <?php
            // Kiểm tra và thực hiện truy vấn
            $select_profile = $conn->prepare("SELECT * FROM `sellers` WHERE id=?");
            $select_profile->execute([$seller_id]);

            if ($select_profile->rowCount() > 0) {
                $fetch_profile = $select_profile->fetch(PDO::FETCH_ASSOC);
        ?>
        <div class="profile">
            <img src="../uploaded_files/<?= $fetch_profile['image']; ?>" class="logo-img">
            <p><?= $fetch_profile['name'];?></p>
        </div>
        <div class="flex-btn">
            <a href="profile.php" class="btn">profile</a>
            <a href="../components/admin_logout.php" onclick="return confirm('logout from this website');" class="btn">logout</a>
        </div>
        <?php } ?>
    </div>
</header>


<div class="sidebar">
    <?php
        // Kiểm tra và thực hiện truy vấn
        $select_profile_sidebar = $conn->prepare("SELECT * FROM `sellers` WHERE id=?");
        $select_profile_sidebar->execute([$seller_id]);

        if ($select_profile_sidebar->rowCount() > 0) {
            $fetch_profile_sidebar = $select_profile_sidebar->fetch(PDO::FETCH_ASSOC);
    ?>
        <div class="profile">
            
            <p><?= $fetch_profile_sidebar['name'];?></p>
        </div>
        
    <?php } ?>

    <h5>menu</h5>
    <div class ="navbar">
        <ul>
            <li><a href="dashboard.php"><i class="bx bxs-home-smile"></i>trang chủ</a></li>
            <li><a href="add_product.php"><i class="bx bxs-shopping-bags"></i>thêm sản phẩm</a></li>
            <li><a href="view_products.php"><i class="bx bxs-food-menu"></i>sản phẩm</a></li>
            <li><a href="user_account.php"><i class="bx bxs-user-detail"></i>tài khoản</a></li>
            <li><a href="profile.php"><i class="bx bxs-user"></i>hồ sơ</a></li>
            <li><a href="add_warehouse.php"><i class='bx bxs-home-alt-2'></i>nhập hàng</a></li>
            <li><a href="manage_products.php"><i class='bx bx-list-ol'></i>danh sách nhập hàng</a></li>
            <!-- <li><a href="xuat_warehouse.php"><i class='bx bxs-home-circle'></i>xuất hàng</a></li> -->
            <li><a href="../components/admin_logout.php" onclick="return confirm('logout from this website');"><i class="bx bxs-log-out"></i>đăng xuất</a></li>           
        </ul>
    </div>

    <h5>tìm chúng tôi</h5>
    <div class="social-links">
        <i class="bx bxl-facebook"></i>
        <i class="bx bxl-instagram-alt"></i>
        <i class="bx bxl-linkedin"></i>
        <i class="bx bxl-twitter"></i>
        <i class="bx bxl-pinterest-alt"></i>
    </div>
</div>
