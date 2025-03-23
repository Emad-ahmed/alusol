<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "newalusol";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$po_number = $_POST['po_number'];
$order_from = $_POST['order_from'];
$rows = json_decode($_POST['rows'], true);

foreach ($rows as $row) {
    $item_name = $row['item_name'];
    $product_name = $row['product_name'];
    $length = $row['length'];
    $open_piece = $row['open_piece'];
    $close_piece = $row['close_piece'];
    $total_piece = $row['total_piece'];
    $open_meter = $row['open_meter'];
    $close_meter = $row['close_meter'];
    $total_meter = $row['total_meter'];
    $bundle = $row['bundle'];
    $price = $row['price'];
    $amount = $row['amount'];
    $comment = $row['comment'];

    $sql = "INSERT INTO order_alusol (po_number, order_from, item_name, product_name, length, open_piece, close_piece, total_piece, open_meter, close_meter, total_meter, bundle, price, amount, comment)
            VALUES ('$po_number', '$order_from', '$item_name', '$product_name', '$length', '$open_piece', '$close_piece', '$total_piece', '$open_meter', '$close_meter', '$total_meter', '$bundle', '$price', '$amount', '$comment')";

    if (!$conn->query($sql)) {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

echo "Data saved successfully!";
$conn->close();
?>