<?php
// Database connection
include 'config.php';

if (isset($_POST['po_number'])) {
    $po_number = $_POST['po_number'];

    // Query to delete the PO from the database
    $query = "DELETE FROM data_alusol WHERE po_number = '$po_number'";
    
    if ($connection->query($query) === TRUE) {
        echo "Success";
    } else {
        echo "Error: " . $conn->error;
    }
}
$connection->close();
?>
