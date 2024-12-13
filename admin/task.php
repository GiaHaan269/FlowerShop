<?php
include '../components/connect.php';

if (isset($_POST['title'], $_POST['description'], $_POST['assigned_to'])) {
    $title = htmlspecialchars($_POST['title']);
    $description = htmlspecialchars($_POST['description']);
    $assigned_to = intval($_POST['assigned_to']);

    $sql = "INSERT INTO tasks (title, description, assigned_to) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->execute([$title, $description, $assigned_to]);

    header('location:manage_products.php?message=task_assigned');
    exit();
} else {
    header('location:manage_products.php?message=error');
    exit();
}
?>