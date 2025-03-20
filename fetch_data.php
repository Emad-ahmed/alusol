<?php
include 'config.php';

// Create connection

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch distinct PO numbers sorted by ID DESC
$sql = "SELECT DISTINCT po_number, order_from, date_time FROM data_alusol ORDER BY id DESC";
$result = $conn->query($sql);

$data = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Close the connection
$conn->close();

// Return the data as JSON
echo json_encode($data);
?>
