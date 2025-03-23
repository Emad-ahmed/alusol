
<?php include 'navbar.php'; ?>
<?php
// Database configuration
$host = "localhost";
$user = "root";
$password = "";
$dbname = "newalusol";

// Create MySQLi connection
$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle autocomplete request
if (isset($_GET['query'])) {
    header('Content-Type: application/json'); // Ensure JSON response
    $search = $_GET['query'];

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
    exit(); // Stop further execution
}

// Fetch unique po_number with order_from and date
$sql = "SELECT DISTINCT `po_number`, `order_from`, `date` FROM `order_alusol` ORDER BY `date` DESC";
$result = $conn->query($sql);

$orders = ($result->num_rows > 0) ? $result->fetch_all(MYSQLI_ASSOC) : [];

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Table</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <style>
        .modal-content {
            width: 110rem !important;
            margin-left: -30rem !important;
        }
        .ui-autocomplete {
            position: absolute;
            z-index: 1000; /* Ensure it appears above other elements */
            cursor: default;
            padding: 0;
            margin-top: 2px;
            list-style: none;
            background-color: #ffffff;
            border: 1px solid #ccc;
            max-height: 200px;
            overflow-y: auto;
            overflow-x: hidden;
        }
        .ui-autocomplete li {
            padding: 8px 12px;
        }
        .ui-autocomplete li:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2>Order Table</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>PO Number</th>
                    <th>Order From</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td><?= htmlspecialchars($order['po_number']) ?></td>
                        <td><?= htmlspecialchars($order['order_from']) ?></td>
                        <td><?= htmlspecialchars($order['date']) ?></td>
                        <td>
                            <button class="btn btn-primary btn-sm show-details" data-po-number="<?= htmlspecialchars($order['po_number']) ?>">Show</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Order Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>PO Number</th>
                                <th>Item Name</th>
                                <th>Product Name</th>
                                <th>Length</th>
                                <th>Open Piece</th>
                                <th>Close Piece</th>
                                <th>Total Piece</th>
                                <th>Open Meter</th>
                                <th>Close Meter</th>
                                <th>Total Meter</th>
                                <th>Bundle</th>
                                <th>Price</th>
                                <th>Amount</th>
                                <th>Comment</th>
                                <th>Order From</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody id="modalTableBody">
                            <tr><td colspan="16" class="text-center">Loading...</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="saveOrder">Save</button>
                </div>
            </div>
        </div>
    </div>

    <script>
    $(document).ready(function () {
        // Show order details when "Show" button is clicked
        $('.show-details').on('click', function () {
            const poNumber = $(this).data('po-number');
            $('#modalTableBody').html('<tr><td colspan="16" class="text-center">Loading...</td></tr>');
            $('#detailsModal').modal('show');

            $.ajax({
                url: 'fetch_details.php',
                type: 'POST',
                data: { po_number: poNumber },
                success: function (response) {
                    const data = JSON.parse(response);
                    let tableBody = '';

                    if (data.length > 0) {
                        data.forEach(row => {
                            tableBody += `<tr>
                                <td><input type="text" class="form-control po-number" value="${row.po_number}" readonly></td>
                                <td>
                                    <select class="form-control item-name">
                                        <option value="">Select</option>
                                        <option value="ITEM" ${row.item_name === 'ITEM' ? 'selected' : ''}>ITEM</option>
                                        <option value="BOX" ${row.item_name === 'BOX' ? 'selected' : ''}>BOX</option>
                                        <option value="SLAT" ${row.item_name === 'SLAT' ? 'selected' : ''}>SLAT</option>
                                        <option value="END SLAT" ${row.item_name === 'END SLAT' ? 'selected' : ''}>END SLAT</option>
                                        <option value="TUBE" ${row.item_name === 'TUBE' ? 'selected' : ''}>TUBE</option>
                                        <option value="GUIDE CHANNEL" ${row.item_name === 'GUIDE CHANNEL' ? 'selected' : ''}>GUIDE CHANNEL</option>
                                        <option value="MOTOR" ${row.item_name === 'MOTOR' ? 'selected' : ''}>MOTOR</option>
                                        <option value="MOTOLY MOTOR" ${row.item_name === 'MOTOLY MOTOR' ? 'selected' : ''}>MOTOLY MOTOR</option>
                                        <option value="ONLY TOP" ${row.item_name === 'ONLY TOP' ? 'selected' : ''}>ONLY TOP</option>
                                        <option value="ONLY FRONT" ${row.item_name === 'ONLY FRONT' ? 'selected' : ''}>ONLY FRONT</option>
                                        <option value="KEY SWITCH" ${row.item_name === 'KEY SWITCH' ? 'selected' : ''}>KEY SWITCH</option>
                                        <option value="SOMFY ACCESSORY" ${row.item_name === 'SOMFY ACCESSORY' ? 'selected' : ''}>SOMFY ACCESSORY</option>
                                        <option value="SWITCH" ${row.item_name === 'SWITCH' ? 'selected' : ''}>SWITCH</option>
                                        <option value="BRACKET" ${row.item_name === 'BRACKET' ? 'selected' : ''}>BRACKET</option>
                                    </select>
                                </td>
                                <td><input type="text" class="form-control product-name" value="${row.product_name}"></td>
                                <td><input type="text" class="form-control length" value="${row.length}"></td>
                                <td><input type="text" class="form-control open-y" value="${row.open_piece}"></td>
                                <td><input type="text" class="form-control close-n" value="${row.close_piece}"></td>
                                <td><input type="text" class="form-control total-piece" value="${row.total_piece}" readonly></td>
                                <td><input type="text" class="form-control open-meter" value="${row.open_meter}"></td>
                                <td><input type="text" class="form-control close-meter" value="${row.close_meter}"></td>
                                <td><input type="text" class="form-control total-meter" value="${row.total_meter}" readonly></td>
                                <td><input type="text" class="form-control bundle" value="${row.bundle}" readonly></td>
                                <td><input type="text" class="form-control product-price" value="${row.price}"></td>
                                <td><input type="text" class="form-control amount" value="${row.amount}" readonly></td>
                                <td><input type="text" class="form-control comment" value="${row.comment || 'N/A'}"></td>
                                <td><input type="text" class="form-control order-from" value="${row.order_from}" readonly></td>
                                <td><input type="text" class="form-control date" value="${row.date}" readonly></td>
                                <td><input type="hidden" class="form-control id" value="${row.id}"></td> <!-- Hidden ID field -->
                            </tr>`;
                        });
                    } else {
                        tableBody = '<tr><td colspan="16" class="text-center">No data found</td></tr>';
                    }

                    $('#modalTableBody').html(tableBody);

                    // Attach event listeners to dynamically created input fields
                    attachEventListeners();

                    // Initialize autocomplete for product name
                    $('.product-name').autocomplete({
                        source: function (request, response) {
                            $.ajax({
                                url: 'showorder.php',
                                type: 'GET',
                                dataType: 'json',
                                data: { query: request.term },
                                success: function (data) {
                                    response(data.map(item => {
                                        return {
                                            label: item.productname, // Display product name in the dropdown
                                            value: item.productname, // Set product name as the value
                                            price: item.price // Include price for further use
                                        };
                                    }));
                                },
                                error: function () {
                                    console.error('Error fetching autocomplete data');
                                }
                            });
                        },
                        minLength: 2, // Minimum characters before triggering autocomplete
                        select: function (event, ui) {
                            // Set the product name and price when a product is selected
                            $(this).val(ui.item.value); // Set the product name
                            $(this).closest('tr').find('.product-price').val(ui.item.price); // Set the price
                            calculateAmount($(this).closest('tr')); // Recalculate amount
                        },
                        open: function () {
                            // Ensure the dropdown is positioned correctly
                            $(this).autocomplete('widget').css({
                                'z-index': 10000 // Ensure it appears above other elements
                            });
                        }
                    });
                },
                error: function () {
                    $('#modalTableBody').html('<tr><td colspan="16" class="text-center text-danger">Error loading data</td></tr>');
                }
            });
        });

        // Save (Update) order details when "Save" button is clicked
        $('#saveOrder').on('click', function () {
            const rows = $('#modalTableBody tr');
            const orders = [];

            // Loop through each row and collect data
            rows.each(function () {
                const row = $(this);
                const order = {
                    id: row.find('.id').val(), // Include the ID for updating
                    po_number: row.find('.po-number').val(),
                    item_name: row.find('.item-name').val(),
                    product_name: row.find('.product-name').val(),
                    length: row.find('.length').val(),
                    open_piece: row.find('.open-y').val(),
                    close_piece: row.find('.close-n').val(),
                    total_piece: row.find('.total-piece').val(),
                    open_meter: row.find('.open-meter').val(),
                    close_meter: row.find('.close-meter').val(),
                    total_meter: row.find('.total-meter').val(),
                    bundle: row.find('.bundle').val(),
                    price: row.find('.product-price').val(),
                    amount: row.find('.amount').val(),
                    comment: row.find('.comment').val(),
                    order_from: row.find('.order-from').val(),
                    date: row.find('.date').val()
                };
                orders.push(order);
            });

            // Send data to update_order.php via AJAX
            $.ajax({
                url: 'update_order.php',
                type: 'POST',
                dataType: 'json',
                data: { orders: JSON.stringify(orders) }, // Send data as JSON
                success: function (response) {
                    if (response.success) {
                        alert('Order updated successfully!');
                        $('#detailsModal').modal('hide'); // Close the modal
                    } else {
                        alert('Error updating order: ' + response.message);
                    }
                },
                error: function () {
                    alert('Error updating order. Please try again.');
                }
            });
        });
    });

    // Function to attach event listeners to dynamically created input fields
    function attachEventListeners() {
        // Add event listeners to each row
        $('#modalTableBody tr').each(function () {
            const row = $(this);

            // Get references to the input fields
            const itemSelect = row.find('.item-name');
            const openYInput = row.find('.open-y');
            const closeNInput = row.find('.close-n');
            const mtrSelect = row.find('.length'); // Assuming 'length' is the mtr field
            const mtrYInput = row.find('.open-meter');
            const mtrNInput = row.find('.close-meter');
            const quantityInput = row.find('.total-piece');
            const totalMeterInput = row.find('.total-meter');
            const searchInput = row.find('.product-name');
            const bundleInput = row.find('.bundle');
            const priceInput = row.find('.product-price');
            const amountInput = row.find('.amount');

            // Function to calculate and set the values
            function calculateMeters() {
                console.log('Calculating meters...'); // Debugging
                // Check if the selected ITEM is "SLAT"
                if (itemSelect.val() === 'SLAT') {
                    // Get the selected mtr value and convert it to a number
                    const mtrValue = parseFloat(mtrSelect.val()) || 0;

                    // Get the open_y and close_n values and convert them to numbers
                    const openYValue = parseFloat(openYInput.val()) || 0;
                    const closeNValue = parseFloat(closeNInput.val()) || 0;

                    // Calculate mtr_y and mtr_n
                    const mtrY = openYValue * mtrValue;
                    const mtrN = closeNValue * mtrValue;

                    // Set the calculated values to the respective fields
                    mtrYInput.val(mtrY.toFixed(2)); // Round to 2 decimal places
                    mtrNInput.val(mtrN.toFixed(2)); // Round to 2 decimal places

                    // Calculate and set the quantity (open_y + close_n)
                    const quantity = openYValue + closeNValue;
                    quantityInput.val(quantity.toFixed(2)); // Round to 2 decimal places

                    // Calculate and set the total_meter (mtr_y + mtr_n)
                    const totalMeter = mtrY + mtrN;
                    totalMeterInput.val(totalMeter.toFixed(2)); // Round to 2 decimal places

                    // Call the calculateBundle function to update the bundle field
                    calculateBundle();

                    // Recalculate amount
                    calculateAmount(row);
                } else {
                    // If the ITEM is not "SLAT", clear the fields
                    mtrYInput.val('');
                    mtrNInput.val('');
                    quantityInput.val('');
                    totalMeterInput.val('');
                    bundleInput.val(''); // Clear bundle field as well
                }
            }

            // Function to calculate and set the bundle value
            function calculateBundle() {
                console.log('Calculating bundle...'); // Debugging
                // Check if the selected ITEM is "SLAT" and PRODUCT starts with "ALS 41"
                if (itemSelect.val() === 'SLAT' && searchInput.val().startsWith('ALS 41')) {
                    // Get the total_meter value and convert it to a number
                    const totalMeterValue = parseFloat(totalMeterInput.val()) || 0;

                    // Calculate bundle (total_meter / 300)
                    const bundleValue = totalMeterValue / 300;

                    // Set the calculated value to the bundle field
                    bundleInput.val(bundleValue.toFixed(2)); // Round to 2 decimal places
                } else {
                    // If conditions are not met, clear the bundle field
                    bundleInput.val('');
                }
            }

            // Function to calculate and set the amount value
            function calculateAmount(row) {
                console.log('Calculating amount...'); // Debugging
                const totalMeter = parseFloat(row.find('.total-meter').val()) || 0;
                const totalPiece = parseFloat(row.find('.total-piece').val()) || 0;
                const price = parseFloat(row.find('.product-price').val()) || 0;

                let amount = 0;
                if (totalMeter > 0) {
                    amount = totalMeter * price; // Amount = Total Meter * Price
                } else if (totalPiece > 0) {
                    amount = totalPiece * price; // Amount = Total Piece * Price
                }

                row.find('.amount').val(amount.toFixed(2)); // Set the calculated amount
            }

            // Add event listeners to the relevant input fields
            itemSelect.on('change', calculateMeters);
            openYInput.on('input', calculateMeters);
            closeNInput.on('input', calculateMeters);
            mtrSelect.on('change', calculateMeters);
            searchInput.on('input', calculateBundle);
            totalMeterInput.on('input', calculateBundle);
            priceInput.on('input', function () {
                calculateAmount(row); // Recalculate amount when price changes
            });
            totalMeterInput.on('input', function () {
                calculateAmount(row); // Recalculate amount when total meter changes
            });
            quantityInput.on('input', function () {
                calculateAmount(row); // Recalculate amount when total piece changes
            });
        });
    }
    </script>
</body>
</html>