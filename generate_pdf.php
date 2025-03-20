<?php
require('pdfgenerate/fpdf.php'); // Ensure FPDF library is included

// Include database connection
include 'config.php'; // Your DB connection configuration

// Check if po_number is set
if (isset($_GET['po_number'])) {
    $po_number = $_GET['po_number'];

    // Query to fetch all details for the specified PO number
    $sql = "SELECT * FROM order_alusol WHERE po_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $po_number);
    $stmt->execute();
    $result = $stmt->get_result();

    // Check if there are results
    if ($result->num_rows > 0) {
        $firstRow = $result->fetch_assoc(); // First row data
        $orderFrom = strtoupper($firstRow['order_from']); // Convert to uppercase
        $dateTime = date('d F, Y', strtotime($firstRow['date'])); // Format date

        // Create a new PDF document in A4 size
        $pdf = new FPDF('P', 'mm', 'A4');
        $pdf->AddPage();

        // Add logo
        $pdf->Image('gulf.png', 10, 10, 90);
        $pdf->Ln(10);

        // Company details
        $pdf->SetFont('Arial', '', 10);
        $pdf->SetXY(110, 10);
        $pdf->MultiCell(90, 5, "SHOP NO. 01, BUILDING NO. 287, MOHAMMAD IBRAHIM STREET (32), BLOCK NO. 03, AL RAI, KUWAIT\nKUWAIT CONTACT: +965 99223382, 90012272\nprocurement@gulfhousefactory.com\nwww.gulfhousefactory.com", 0, 'L');
        $pdf->Ln(5);

        // Purchase Order heading
        $pdf->SetFont('Arial', 'B', 14);
        $pdf->Cell(0, 10, 'PURCHASE ORDER', 0, 1, 'C');
        $pdf->Ln(1);
        $pdf->Line(10, $pdf->GetY(), 200, $pdf->GetY()); // Line
        $pdf->Ln(4);

        // PO Date
        $pdf->SetFont('Arial', '', 12);
        $pdf->Cell(0, 10, strtoupper($dateTime), 0, 1, 'L');
        $pdf->Ln(10);

        // Vendor Details
        $pdf->SetFont('Arial', '', 9);
        $pdf->SetXY(110, 62);
        $pdf->Cell(0, 6, 'TO,', 0, 1, 'L');
        $pdf->SetX(110);
        $pdf->Cell(0, 6, 'ALUSOL ROLLING SHUTTER', 0, 1, 'L');
        $pdf->SetX(110);
        $pdf->Cell(70, 6, "SOUTHERN SABHAN, BLOCK 8, ST 101, BUILDING 174", 0, 1, 'L');
        $pdf->SetX(110);
        $pdf->Cell(70, 6, "P.O BOX 29599, SAFAT 15154", 0, 1, 'L');
        $pdf->SetX(110);
        $pdf->Cell(0, 6, 'info@alusol-kw.com | (965) 22090000', 0, 1, 'L');
        $pdf->Ln(2);

        // PO Number
        $pdf->SetFont('Arial', 'B', 12);
        $pdf->Cell(0, 10, 'PO-' . strtoupper($firstRow['po_number']), 0, 1, 'L');
        $pdf->Ln(4);

        // Greeting
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, 'Dear,', 0, 1, 'L');
        $pdf->Cell(0, 10, 'Greetings from GULF HOUSE FACTORY. We would like to place the order of the following items:', 0, 1, 'L');
        $pdf->Ln(5);

        // Table headers
        $pdf->SetFont('Arial', 'B', 7);
        $pdf->Cell(40, 10, 'PRODUCT NAME', 1);
        $pdf->Cell(20, 10, 'LENGTH/UNIT', 1);
        $pdf->Cell(20, 10, 'OPEN (PIECE)', 1);
        $pdf->Cell(20, 10, 'CLOSE (PIECE)', 1);
        $pdf->Cell(25, 10, 'OPEN METER', 1);
        $pdf->Cell(25, 10, 'CLOSE METER', 1);
        $pdf->Cell(17, 10, 'BUNDLE', 1);
        $pdf->Cell(27, 10, 'COMMENT', 1, 1);

        // Table data
        $pdf->SetFont('Arial', '', 7);
        do {
            $pdf->Cell(40, 10, $firstRow['product_name'], 1);
            $pdf->Cell(20, 10, $firstRow['length'], 1);
            $pdf->Cell(20, 10, $firstRow['open_piece'], 1);
            $pdf->Cell(20, 10, $firstRow['close_piece'], 1);
            $pdf->Cell(25, 10, $firstRow['open_meter'], 1);
            $pdf->Cell(25, 10, $firstRow['close_meter'], 1);
            $pdf->Cell(17, 10, $firstRow['bundle'], 1);
            $pdf->Cell(27, 10, $firstRow['comment'], 1, 1);
        } while ($firstRow = $result->fetch_assoc());

        $pdf->Ln(10);

        // Notes Section
        $pdf->SetFont('Arial', 'B', 10);
        $pdf->Cell(0, 10, 'NOTE:', 0, 1, 'L');
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 6, '* Please mention the PO number in your invoice.', 0, 1, 'L');
        $pdf->Cell(0, 6, '* If you find any discrepancies, kindly let us know immediately.', 0, 1, 'L');
        $pdf->Ln(10);

        // Footer
        $pdf->SetFont('Arial', 'I', 10);
        $pdf->Cell(0, 6, 'Your time to review our request is greatly appreciated. We look forward to hearing from you soon.', 0, 1, 'L');
        $pdf->Cell(0, 6, '- Thanks for the ongoing support!', 0, 1, 'L');
        $pdf->Cell(0, 6, 'PROCUREMENT, GULF HOUSE FACTORY', 0, 1, 'L');

        // Output the PDF
        $pdf->Output('D', 'PO-' . $po_number . '.pdf');
        exit;
    } else {
        echo "No details available for PO Number: " . $po_number;
    }
} else {
    echo "Error: PO Number not specified.";
}
?>
