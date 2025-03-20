<?php
// Database connection
$host = "localhost";
$user = "root";
$password = "";
$dbname = "newalusol";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the data from the AJAX request
$poNumber = $_POST['po_number'];
$orderFrom = $_POST['order_from'];
$rows = json_decode($_POST['rows'], true);

// Insert each row into the database
foreach ($rows as $row) {
    $itemName = $row['item_name'];
    $productName = $row['product_name'];
    $length = $row['length'];
    $openPiece = $row['open_piece'];
    $closePiece = $row['close_piece'];
    $totalPiece = $row['total_piece'];
    $openMeter = $row['open_meter'];
    $closeMeter = $row['close_meter'];
    $totalMeter = $row['total_meter'];
    $bundle = $row['bundle'];
    $price = $row['price'];
    $amount = $row['amount'];
    $comment = $row['comment'];
    $date = date("Y-m-d H:i:s"); // Current date and time

    // Prepare the SQL query
    $sql = "INSERT INTO `order_alusol` 
            (`po_number`, `item_name`, `product_name`, `length`, `open_piece`, `close_piece`, `total_piece`, `open_meter`, `close_meter`, `total_meter`, `bundle`, `price`, `amount`, `comment`, `date`) 
            VALUES 
            ('$poNumber', '$itemName', '$productName', '$length', '$openPiece', '$closePiece', '$totalPiece', '$openMeter', '$closeMeter', '$totalMeter', '$bundle', '$price', '$amount', '$comment', '$date')";

    // Execute the query
    if (!$conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

echo "Data saved successfully!";