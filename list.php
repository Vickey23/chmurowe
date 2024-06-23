<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include 'functions.php';
$userId = $_SESSION['user_id'];
$username = $_SESSION['username'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_product'])) {
        $itemName = $_POST['name'];
        $quantity = $_POST['quantity'];
        $urgent = isset($_POST['urgent']) ? 1 : 0;
        addItem($userId, $itemName, $quantity, $urgent);
    } elseif (isset($_POST['update_list'])) {
        if (isset($_POST['purchased_quantity'])) {
            foreach ($_POST['purchased_quantity'] as $itemId => $purchasedQuantity) {
                $purchasedQuantity = (int)$purchasedQuantity;
                $item = getItemById($itemId);
                $remainingQuantity = (int)$item['quantity'] - $purchasedQuantity;
                if ($remainingQuantity < 0) {
                    $remainingQuantity = 0;
                }
                updateItemQuantity($itemId, $remainingQuantity);
            }
        }
    } elseif (isset($_POST['delete_list'])) {
        if (isset($_POST['delete_items'])) {
            foreach ($_POST['delete_items'] as $itemId) {
                deleteItem($itemId);
            }
        }
    }
}

$items = getItems($userId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shopping List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .urgent {
            background-color: #f8d7da;
            color: #721c24;
        }
        .completed {
            background-color: #d4edda;
            color: #155724;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1>Shopping List</h1>
            <div>
                <span class="me-3">Logged in as: <?php echo htmlspecialchars($username); ?></span>
                <a href="logout.php" class="btn btn-secondary">Logout</a>
            </div>
        </div>
        <form action="list.php" method="POST" class="mb-4">
            <div class="mb-3">
                <label for="name" class="form-label">Product Name</label>
                <input type="text" class="form-control" id="name" name="name" required>
            </div>
            <div class="mb-3">
                <label for="quantity" class="form-label">Quantity</label>
                <input type="number" class="form-control" id="quantity" name="quantity" required>
            </div>
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="urgent" name="urgent">
                <label class="form-check-label" for="urgent">Urgent</label>
            </div>
            <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
        </form>

        <h2>Shopping List</h2>
        <form action="list.php" method="POST">
            <ul class="list-group">
                <?php while ($item = $items->fetch_assoc()) { 
                    $class = $item['urgent'] ? 'list-group-item urgent' : 'list-group-item';
                    if ($item['quantity'] == 0) {
                        $class = 'list-group-item completed';
                    }
                    ?>
                    <li class="<?php echo $class; ?>">
                        <input type="checkbox" name="delete_items[]" value="<?php echo $item['id']; ?>" class="form-check-input me-2">
                        <?php echo htmlspecialchars($item['item_name']); ?> - <?php echo $item['quantity'] == 0 ? ':)' : htmlspecialchars($item['quantity']); ?>
                        <div class="d-inline-block ms-3">
                            <label for="purchased_quantity[<?php echo $item['id']; ?>]">Kupiono:</label>
                            <input type="number" name="purchased_quantity[<?php echo $item['id']; ?>]" value="" min="0" class="form-control d-inline-block w-auto">
                        </div>
                    </li>
                <?php } ?>
            </ul>
            <div class="d-flex justify-content-end mt-3">
                <button type="submit" name="update_list" class="btn btn-primary me-2">Update List</button>
                <button type="submit" name="delete_list" class="btn btn-danger">Delete Selected</button>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
</body>
</html>