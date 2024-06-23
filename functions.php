<?php
function connectDatabase() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "shopping_list";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

function registerUser($username, $password) {
    $conn = connectDatabase();
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $username, $hashedPassword);
    $success = $stmt->execute();
    $stmt->close();
    $conn->close();
    return $success;
}

function loginUser($username, $password) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("SELECT id, password FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows > 0) {
        $stmt->bind_result($userId, $hashedPassword);
        $stmt->fetch();
        if (password_verify($password, $hashedPassword)) {
            return $userId;
        }
    }
    $stmt->close();
    $conn->close();
    return false;
}

function addItem($userId, $itemName, $quantity, $urgent) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("INSERT INTO items (user_id, item_name, quantity, urgent) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isii", $userId, $itemName, $quantity, $urgent);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

function getItems($userId) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("SELECT id, item_name, quantity, urgent, purchased FROM items WHERE user_id = ? ORDER BY urgent DESC, id ASC");
    $stmt->bind_param("i", $userId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
    $conn->close();
    return $result;
}

function getItemById($itemId) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("SELECT * FROM items WHERE id = ?");
    $stmt->bind_param("i", $itemId);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();
    $stmt->close();
    $conn->close();
    return $item;
}

function updateItemQuantity($itemId, $quantity) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("UPDATE items SET quantity = ? WHERE id = ?");
    $stmt->bind_param("ii", $quantity, $itemId);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}


function deleteItem($itemId) {
    $conn = connectDatabase();
    $stmt = $conn->prepare("DELETE FROM items WHERE id = ?");
    if (!$stmt) {
        error_log("Prepare failed: " . $conn->error);
        return false;
    }

    $stmt->bind_param("i", $itemId);
    if (!$stmt->execute()) {
        error_log("Execute failed: " . $stmt->error);
        $stmt->close();
        $conn->close();
        return false;
    }

    $stmt->close();
    $conn->close();
    return true;
}

?>