
<?php include 'navbar.php'; ?>


<?php
$host = "localhost";
$user = "root";
$password = "";
$dbname = "newalusol"; // আপনার ডাটাবেজের নাম

$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>



<style>
        #suggestions {
            border: 1px solid #ccc;
            max-width: 300px;
            background: #fff;
            position: absolute;
            display: none;
        }
        .suggestion-item {
            padding: 8px;
            cursor: pointer;
        }
        .suggestion-item:hover {
            background: #f0f0f0;
        }
    </style>


<div class="container-fluid mt-5">
<form action="#" method="post" id="priceForm">
    <div class="row g-2 align-items-center">
        <div class="col-lg-1 col-md-1">
            <label for="item" class="form-label">ITEM</label>
            <select id="item" name="item" class="form-select">
                <option value="">Select</option>
                <option value="ITEM">ITEM</option>
                <option value="BOX">BOX</option>
                <option value="SLAT">SLAT</option>
                <option value="END SLAT">END SLAT</option>
                <option value="TUBE">TUBE</option>
                <option value="GUIDE CHANNEL">GUIDE CHANNEL</option>
                <option value="MOTOR">MOTOR</option>
                <option value="MOTOLY MOTOR">MOTOLY MOTOR</option>
                <option value="ONLY TOP">ONLY TOP</option>
                <option value="ONLY FRONT">ONLY FRONT</option>
                <option value="KEY SWITCH">KEY SWITCH</option>
                <option value="SOMFY ACCESSORY">SOMFY ACCESSORY</option>
                <option value="SWITCH">SWITCH</option>
                <option value="BRACKET">BRACKET</option>
            </select>
        </div>

        <div class="col-lg-1 col-md-1">
            <label for="searchInput" class="form-label">PRODUCT</label>
            <input type="text" id="searchInput" class="form-control" placeholder="Search Product...">
            <div id="suggestions"></div>
        </div>

        <div class="col-lg-1 col-md-1">
            <label for="mtr" class="form-label">LENGTH PER UNIT</label>
            <select id="mtr" name="mtr" class="form-select">
                <option value="">Select</option>
                <option value="6">6</option>
                <option value="5.8">5.8</option>
                <option value="5.5">5.5</option>
                <option value="4.5">4.5</option>
                <option value="4">4</option>
                <option value="3.5">3.5</option>
                <option value="3.5">7</option>
            </select>
        </div>

        <div class="col-lg-1 col-md-1">
            <label for="open_y" class="form-label">OPEN (PIECES)</label>
            <input type="text" class="form-control" id="open_y" name="open_y" placeholder="OPEN Y">
        </div>

        <div class="col-lg-1 col-md-1">
            <label for="close_n" class="form-label">CLOSE (PIECES)</label>
            <input type="text" class="form-control" id="close_n" name="close_n" placeholder="CLOSE N">
        </div>

        <div class="col-lg-1 col-md-1">
            <label for="quantity" class="form-label">TOTAL PIECES</label>
            <input type="text" class="form-control" id="quantity" name="quantity" placeholder="Qty">
        </div>

        <div class="col-lg-1 col-md-1">
            <label for="mtr_y" class="form-label">OPEN MTR</label>
            <input type="text" class="form-control" id="mtr_y" name="mtr_y" placeholder="MTR Y" readonly>
        </div>

        <div class="col-lg-1 col-md-1">
            <label for="mtr_n" class="form-label">CLOSE MTR</label>
            <input type="text" class="form-control" id="mtr_n" name="mtr_n" placeholder="MTR N" readonly>
        </div>

        <div class="col-lg-1 col-md-1">
            <label for="total_meter" class="form-label">TOTAL MTR</label>
            <input type="text" class="form-control" id="total_meter" name="total_meter" placeholder="TOTAL" readonly>
        </div>

        <div class="col-lg-1 col-md-1">
            <label for="bundle" class="form-label">PACKET</label>
            <input type="text" class="form-control" id="bundle" name="bundle" placeholder="BUNDLE" readonly>
        </div>

        <div class="col-lg-1 col-md-1">
            <label for="price" class="form-label">PRICE</label>
            <input type="number" step="0.001" class="form-control" id="price" name="price" placeholder="Price" readonly>
        </div>

        <div class="col-lg-1 col-md-1">
            <label for="amount" class="form-label">AMOUNT</label>
            <input type="number" step="0.001" class="form-control" id="amount" name="amount" placeholder="Amount" readonly>
        </div>
        <div>
        <div class="col-lg-1 col-md-1">
            <label for="comment" class="form-label">COMMENT</label>
                <input type="text" step="0.001" class="form-control" id="comment" name="comment" placeholder="Comment">
            </select>
        </div>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-12 d-grid">
            <button type="submit" class="btn btn-primary">Submit</button>
        </div>
    </div>
</form>


    <div class="mt-5">
        <h3>Submitted Values</h3>
        <div class="row">
    <div class="col-4 mb-4">
    <label for="po_number" class="form-label">PO NUMBER</label>
        <input type="text" name="po_number" id="po_number" class="form-control" placeholder="PO NUMBER">
    </div>
    <div class="col-4 mb-4">
        <label for="order_from" class="form-label">ORDER FROM</label>
        <select id="order_from" name="order_from" class="form-select">
            <option value="">Select Place</option>
            <option value="SHUBHAN">SHUBHAN</option>
            <option value="AL RAI">AL RAI</option>
        </select>
    </div>
</div>
       
        
<table class="table table-bordered" id="dataTable">
                <thead>
                    <tr>
                        <th>ITEM</th>
                        <th>PRODUCT</th>
                        <th>LENGTH PER UNIT</th>
                        <th>OPEN PIECES</th>
                        <th>CLOSE PIECES</th>
                        <th>TOTAL PIECES</th>
                        <th>OPEN METER</th>
                        <th>CLOSE METER</th>
                        <th>TOTAL METER</th>
                        <th>BUNDLE</th>
                        <th>PRICE</th>
                        <th>AMOUNT</th>
                        <th>STATUS</th>
                        <th>ACTION</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Rows will be added here -->
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="11" style="text-align: right;"><strong>Total:</strong></td>
                        <td id="totalRow">0.000</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>

        <button id="saveButton" class="btn btn-success w-100 mt-3">Save</button>

    </div>

</div>

<!-- Bootstrap 5 JS -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
<script src="alusol.js"></script>


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>




<script>
$(document).ready(function() {
    // Event listener for the Save button
    $("#saveButton").on("click", function() {
        // Get the PO number and order from
        let poNumber = $("#po_number").val();
        let orderFrom = $("#order_from").val();

        // Check if PO number and order from are filled
        if (!poNumber || !orderFrom) {
            alert("Please fill in the PO NUMBER and ORDER FROM fields.");
            return;
        }

        // Prepare the data to send
        let rows = [];
        $("#dataTable tbody tr").each(function() {
            let row = {
                item_name: $(this).find("td").eq(0).text(),
                product_name: $(this).find("td").eq(1).text(),
                length: $(this).find("td").eq(2).text(),
                open_piece: $(this).find("td").eq(3).text(),
                close_piece: $(this).find("td").eq(4).text(),
                total_piece: $(this).find("td").eq(5).text(),
                open_meter: $(this).find("td").eq(6).text(),
                close_meter: $(this).find("td").eq(7).text(),
                total_meter: $(this).find("td").eq(8).text(),
                bundle: $(this).find("td").eq(9).text(),
                price: $(this).find("td").eq(10).text(),
                amount: $(this).find("td").eq(11).text(),
                comment: $(this).find("td").eq(12).text()
            };
            rows.push(row);
        });

        // Send data to the server using AJAX
        $.ajax({
            url: "save_order.php", // PHP file to handle the save operation
            type: "POST",
            data: {
                po_number: poNumber,
                order_from: orderFrom,
                rows: JSON.stringify(rows) // Convert rows to JSON
            },
            success: function(response) {
                alert("Data saved successfully!");
                console.log(response);

                // Redirect to generate_pdf.php to download the PDF
                window.location.href = "generate_pdf.php?po_number=" + poNumber;
            },
            error: function(xhr, status, error) {
                alert("An error occurred while saving the data.");
                console.error(error);
            }
        });
    });
});
</script>


<script>
    $(document).ready(function() {
        // Event listener for form submission
        $("#priceForm").on("submit", function(event) {
            event.preventDefault(); // Prevent the form from submitting

            // Get form values
            let item = $("#item").val();
            let product = $("#searchInput").val();
            let lengthPerUnit = parseFloat($("#mtr").val()) || 0; // Ensure it's a number or default to 0
            let openPieces = parseFloat($("#open_y").val()) || 0;
            let closePieces = parseFloat($("#close_n").val()) || 0;
            let totalPieces = parseFloat($("#quantity").val()) || 0;
            let openMeter = parseFloat($("#mtr_y").val()) || 0;
            let closeMeter = parseFloat($("#mtr_n").val()) || 0;
            let totalMeter = parseFloat($("#total_meter").val()) || 0;
            let bundle = $("#bundle").val();
            let price = parseFloat($("#price").val()) || 0; // Ensure it's a number or default to 0
            let amount = 0; // Initialize amount
            let comment = $("#comment").val();

           

            // Add a new row to the table
            let newRow = `<tr>
                <td>${item}</td>
                <td>${product}</td>
                <td>${lengthPerUnit}</td>
                <td>${openPieces}</td>
                <td>${closePieces}</td>
                <td>${totalPieces}</td>
                <td>${openMeter}</td>
                <td>${closeMeter}</td>
                <td>${totalMeter}</td>
                <td>${bundle}</td>
                <td>${price}</td>
                <td>${amount}</td>
                <td>${comment}</td>
                <td><button class="btn btn-warning btn-sm edit-btn">Edit</button> <button class="btn btn-danger btn-sm delete-btn">Delete</button></td>
            </tr>`;

            $("#dataTable tbody").append(newRow);

            // Clear the form fields
            $("#priceForm")[0].reset();

            // Update the total amount
            updateTotalAmount();
        });

        // Event delegation for edit button
        $(document).on("click", ".edit-btn", function() {
            let row = $(this).closest("tr");
            let cells = row.find("td");

            // Populate the form with the row data
            $("#item").val(cells.eq(0).text());
            $("#searchInput").val(cells.eq(1).text());
            $("#mtr").val(cells.eq(2).text());
            $("#open_y").val(cells.eq(3).text());
            $("#close_n").val(cells.eq(4).text());
            $("#quantity").val(cells.eq(5).text());
            $("#mtr_y").val(cells.eq(6).text());
            $("#mtr_n").val(cells.eq(7).text());
            $("#total_meter").val(cells.eq(8).text());
            $("#bundle").val(cells.eq(9).text());
            $("#price").val(cells.eq(10).text());
            $("#amount").val(cells.eq(11).text());
            $("#comment").val(cells.eq(12).text());

            // Remove the row from the table
            row.remove();

            // Update the total amount
            updateTotalAmount();
        });

        $(document).on("click", ".delete-btn", function() {
            let row = $(this).closest("tr");
            row.remove(); // Remove the row
            updateTotalAmount(); // Update the total amount
        });

        // Function to update the total amount
        function updateTotalAmount() {
            let total = 0;
            $("#dataTable tbody tr").each(function() {
                let amount = parseFloat($(this).find("td").eq(11).text());
                if (!isNaN(amount)) {
                    total += amount;
                }
            });
            $("#totalRow").text(total.toFixed(3));
        }
    });
</script>


<script>
$(document).ready(function() {
    // Event listener for product input
    $("#searchInput").on("keyup", function() {
        let query = $(this).val().trim();
        if (query.length > 0) {
            $.ajax({
                url: "fetch_products.php",
                type: "GET",
                data: { query: query },
                dataType: "json",
                success: function(response) {
                    let suggestions = "";
                    if (response.length > 0) {
                        response.forEach(item => {
                            // Add product name and price to the suggestion
                            suggestions += `<div class='suggestion-item' onclick='selectProduct("${item.productname}", ${item.price})'>${item.productname}</div>`;
                        });
                        $("#suggestions").html(suggestions).show();
                    } else {
                        $("#suggestions").hide();
                    }
                },
                error: function(xhr, status, error) {
                    console.error("AJAX Error: ", error);
                }
            });
        } else {
            $("#suggestions").hide();
        }
    });
});

// Function to select a product and update the price
function selectProduct(name, price) {
    $("#searchInput").val(name); // Set product name in the input field
    $("#price").val(price); // Set product price in the price field
    $("#suggestions").hide(); // Hide suggestions
}
</script>



</body>
</html>
