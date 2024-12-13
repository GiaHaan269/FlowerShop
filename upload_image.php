<?php
session_start(); // Khởi động session
include 'components/connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploaded_images/';

        // Ensure the upload directory exists
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Delete old images
        $files = glob($uploadDir . '*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }

        // Save the new image
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = time() . '_' . $_FILES['image']['name'];
        $uploadFilePath = $uploadDir . $fileName;

        $allowedMimeTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($fileTmpPath);

        if (!in_array($fileType, $allowedMimeTypes)) {
            $_SESSION['error'] = 'Chỉ chấp nhận tệp hình ảnh (JPEG, PNG, GIF).';
            header('Location: result.php');
            exit;
        }

        if (move_uploaded_file($fileTmpPath, $uploadFilePath)) {
            // Run Python script for prediction
            $command = escapeshellcmd("python F:/xampp/htdocs/flowershop/modelai/predict.py " . escapeshellarg($uploadFilePath));
            $output = shell_exec($command);

            $predicted_label = trim($output); // Trim any extra whitespace

            // Store the result and file path in session
            $_SESSION['image_path'] = $uploadFilePath;
            $_SESSION['predicted_label'] = $predicted_label;

            // Redirect to result page
            header('Location: result.php');
            exit;
        } else {
            $_SESSION['error'] = 'Đã xảy ra lỗi khi lưu tệp.';
            header('Location: result.php');
            exit;
        }
    } else {
        $_SESSION['error'] = 'Không tìm thấy tệp hoặc xảy ra lỗi trong quá trình tải lên.';
        header('Location: result.php');
        exit;
    }
}
?>