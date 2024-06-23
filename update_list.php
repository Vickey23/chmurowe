<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'functions.php';
$userId = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $purchasedItems = $_POST['purchased'];

    $stmt = $conn->prepare("UPDATE items SET purchased = 1 WHERE user_id = ? AND id = ?");
    foreach ($purchasedItems as $itemId) {
        $stmt->bind_param("ii", $userId, $itemId);
        $stmt->execute();
    }

    header("Location: list.php");
}
?>
