<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "newalusol";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode([]));
}

$po_number = $_POST['po_number'] ?? '';

$sql = "SELECT * FROM `order_alusol` WHERE `po_number` = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $po_number);
$stmt->execute();
$result = $stmt->get_result();

$data = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];
echo json_encode($data);

$stmt->close();
$conn->close();
?>
