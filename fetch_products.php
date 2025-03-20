<?php
header('Content-Type: application/json'); // Ensure JSON response

$host = "localhost";
$user = "root";
$password = "";
$dbname = "newalusol";

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

$search = isset($_GET['query']) ? $_GET['query'] : '';

// Fetch product name and price
$sql = "SELECT productname, price FROM product WHERE productname LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param("s", $searchTerm);
$stmt->execute();
$result = $stmt->get_result();

$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row; // Add product name and price to the response
}

echo json_encode($data); // Return data as JSON

$stmt->close();
$conn->close();
?>