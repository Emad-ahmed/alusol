<?php
header('Content-Type: application/json'); // Ensure JSON response

// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$dbname = "newalusol";

// Create MySQLi connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die(json_encode(["success" => false, "message" => "Connection failed: " . $conn->connect_error]));
}

// Get the orders data from the POST request
$orders = json_decode($_POST['orders'], true);

// Update each order in the database
foreach ($orders as $order) {
    $id = $conn->real_escape_string($order['id']);
    $po_number = $conn->real_escape_string($order['po_number']);
    $item_name = $conn->real_escape_string($order['item_name']);
    $product_name = $conn->real_escape_string($order['product_name']);
    $length = $conn->real_escape_string($order['length']);
    $open_piece = $conn->real_escape_string($order['open_piece']);
    $close_piece = $conn->real_escape_string($order['close_piece']);
    $total_piece = $conn->real_escape_string($order['total_piece']);
    $open_meter = $conn->real_escape_string($order['open_meter']);
    $close_meter = $conn->real_escape_string($order['close_meter']);
    $total_meter = $conn->real_escape_string($order['total_meter']);
    $bundle = $conn->real_escape_string($order['bundle']);
    $price = $conn->real_escape_string($order['price']);
    $amount = $conn->real_escape_string($order['amount']);
    $comment = $conn->real_escape_string($order['comment']);
    $order_from = $conn->real_escape_string($order['order_from']);
    $date = $conn->real_escape_string($order['date']);

    // Update query
    $sql = "UPDATE `order_alusol`
            SET `po_number` = '$po_number',
                `item_name` = '$item_name',
                `product_name` = '$product_name',
                `length` = '$length',
                `open_piece` = '$open_piece',
                `close_piece` = '$close_piece',
                `total_piece` = '$total_piece',
                `open_meter` = '$open_meter',
                `close_meter` = '$close_meter',
                `total_meter` = '$total_meter',
                `bundle` = '$bundle',
                `price` = '$price',
                `amount` = '$amount',
                `comment` = '$comment',
                `order_from` = '$order_from',
                `date` = '$date'
            WHERE `id` = '$id'";

    if (!$conn->query($sql)) {
        echo json_encode(["success" => false, "message" => "Error: " . $conn->error]);
        $conn->close();
        exit();
    }
}

// Return success response
echo json_encode(["success" => true, "message" => "Orders updated successfully!"]);
$conn->close();
?>