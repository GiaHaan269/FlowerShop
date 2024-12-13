<?php
if (isset($_FILES['image'])) {
    $url = 'http://127.0.0.1:5000/search';
    $filePath = $_FILES['image']['tmp_name'];

    $cFile = new CURLFile($filePath, 'image/jpeg', $_FILES['image']['name']);
    $data = array('image' => $cFile);

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    curl_close($ch);

    $results = json_decode($response, true);
    echo "<h2>Search Results:</h2>";
    foreach ($results as $result) {
        echo "<p><img src='{$result['image_path']}' style='width:100px;'> Similarity: {$result['similarity']}</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Search</title>
</head>

<body>
    <h1>Search by Image</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <input type="file" name="image" accept="image/*" required>
        <button type="submit">Search</button>
    </form>
</body>

</html>