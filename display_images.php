<!DOCTYPE html>
<html lang="vi">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tải Ảnh và Dự Đoán</title>
</head>

<body>
    <h1>Tải ảnh lên để dự đoán</h1>

    <!-- Form upload ảnh (không cần nút submit, chỉ cần input file) -->
    <form id="uploadForm" action="upload_image.php" method="POST" enctype="multipart/form-data">
        <label for="image">Chọn ảnh:</label>
        <input type="file" name="image" id="image" accept="image/*" required>
    </form>

    <!-- Khu vực hiển thị kết quả dự đoán -->
    <div id="result" style="margin-top: 20px;">
        <!-- Kết quả sẽ được hiển thị tại đây -->
    </div>

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
                    } else {
                        console.error('Lỗi khi tải ảnh lên!');
                    }
                };

                // Gửi yêu cầu
                xhr.send(formData);
            }
        });
    </script>
</body>

</html>