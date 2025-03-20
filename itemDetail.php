<?php include 'navbar.php'; ?>

<?php
// Check if a session is already active
if (session_status() === PHP_SESSION_NONE) {
    // Set session timeout to a longer period (e.g., 1 week) before starting the session
    $lifetime = 60 * 60 * 24 * 7; // 1 week
    session_set_cookie_params($lifetime);
    session_start();
}

// Check if the user is logged in, if not redirect to login page
if (!isset($_SESSION['userid'])) {
    header("Location: login.php");
    exit();
}
?>


<link rel="stylesheet" href="https://cdn.datatables.net/1.10.24/css/jquery.dataTables.min.css">

<div class="container-fluid mt-5">
    <h3 class="text-center mb-5">Details for <?php echo htmlspecialchars($_GET['item_name']); ?> - <?php echo htmlspecialchars($_GET['type']); ?></h3>
    <table id="detailTable" class="display">
        <thead>
            <tr>
                <th>PO Number</th>
                <th>Order From</th>
                <th>Color</th>
                <th>Quantity</th>
                <th>MTR</th>
                <th>Open Y</th>
                <th>Close N</th>
                <th>Bundle</th>
                <th>Price</th>
                <th>Amount</th>
              
                <th>Date Time</th>
            </tr>
        </thead>
        <tbody>
            <!-- Data will be inserted here by JavaScript -->
        </tbody>
    </table>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    $(document).ready(function() {
        // Fetch details based on item_name and type
        const itemName = '<?php echo addslashes($_GET["item_name"]); ?>';
        const type = '<?php echo addslashes($_GET["type"]); ?>';

        $.ajax({
            url: 'fetch_details_endpoint.php', // Create this endpoint to fetch data based on item_name and type
            method: 'GET',
            dataType: 'json',
            data: { item_name: itemName, type: type },
            success: function(data) {
                let tableBody = '';
                let totalQty = 0;
                let totalPrice = 0;
                let totalAmount = 0;
                let totalBundle = 0;

                data.forEach(item => {
                    tableBody += `
                        <tr>
                            <td>${item.po_number}</td>
                            <td>${item.order_from}</td>
                            <td>${item.color}</td>
                            <td>${item.qty}</td>
                            <td>${item.mtr}</td>
                            <td>${item.open_y}</td>
                            <td>${item.close_n}</td>
                            <td>${item.bundle}</td>
                            <td>${item.price}</td>
                            <td>${item.amount}</td>
                            <td>${new Date(item.date_time).toLocaleString()}</td>
                        </tr>
                    `;

                    // Accumulate totals
                    totalQty += parseFloat(item.qty);
                    totalPrice += parseFloat(item.price);
                    totalAmount += parseFloat(item.amount);
                    totalBundle += parseFloat(item.bundle);
                });

                // Insert the data into the table body
                $('#detailTable tbody').html(tableBody);

                // Create a total row and append it to the table body
                let totalRow = `
                    <tr>
                        <td colspan="3" style="text-align:right;"><strong>Total:</strong></td>
                        <td><strong>${totalQty}</strong></td>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><strong>${totalBundle.toFixed(2)}</strong></td>
                        <td><strong>${totalPrice.toFixed(2)}</strong></td>
                        <td><strong>${totalAmount.toFixed(2)}</strong></td>
                        <td></td>
                    </tr>
                `;

                $('#detailTable tbody').append(totalRow);

                // Initialize DataTable
                $('#detailTable').DataTable();
            }
        });
    });
</script>
