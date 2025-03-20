<?php
require('pdfgenerate/fpdf.php'); // Ensure FPDF library is included
include 'config.php'; // Include database connection

if (isset($_GET['po_number'])) {
    $po_number = $_GET['po_number'];

    // Query to fetch order details
    $sql = "SELECT * FROM order_alusol WHERE po_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $po_number);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $firstRow = $result->fetch_assoc();
        $orderFrom = strtoupper($firstRow['order_from']);
        $dateTime = date('d F, Y', strtotime($firstRow['date']));

        // Calculating total type and total packing
        $totalType = $result->num_rows;
        $totalPacking = 0;
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            $totalPacking += $row['bundle'];
        }

        class PDF extends FPDF {
            private $headerPrinted = false;

            function Header() {
                if (!$this->headerPrinted) {
                    $this->Image('gulf.png', 57, 10, 90); // Add logo
                    $this->Ln(20);
                    
                    // Set font for the "PURCHASE ORDER" text
                    $this->SetFont('Arial', 'B', 16);
                    $this->Cell(0, 10, 'PURCHASE ORDER', 0, 1, 'C');
                    
                    // Set default font for the rest of the header
                    $this->SetFont('Arial', '', 9);
            
                    $this->Ln(2);
                    $this->Line(10, $this->GetY(), 200, $this->GetY()); // Add a line
                    $this->Ln(5);
                    $this->headerPrinted = true; // Ensure header only prints once
                }
            }

            function Footer() {
                $this->SetY(-70); // Position footer 50mm from the bottom
                $this->SetFont('Arial', '', 8);
                $footerText = "Note:\n\n";
                $footerText .= "Please mention the PO number in your invoice.\n";
                $footerText .= "If you find any discrepancies, kindly let us know immediately\n\n";
                $footerText .= "Your time to review our request is greatly appreciated. We look forward to hearing from you soon\n\n";
                $footerText .= "- Thanks for the ongoing support!\n";
                $footerText .= "PROCUREMENT\n";
                $footerText .= "+965 99223382, +965 90012272\n";
                $footerText .= "procurement@gulfhousefactory.com\n";
                $footerText .= "GULF HOUSE FACTORY\n\n";
                
                // Print footer text
                $this->MultiCell(0, 4, $footerText, 0, 'L');
            
                // Add centered shop address at the bottom
                $this->SetFont('Arial', '', 9);
                $this->SetY(-15); // Position the shop address 15mm from the bottom
                $this->Cell(0, 4, "Shop No. 01, Building No. 287, Mohammad Ibrahim Street, Block No. 03, Al Rai, Kuwait", 0, 1, 'C');
            }

            function TableHeader() {
                $this->SetFont('Arial', 'B', 6.5);
                $this->Cell(14, 10, 'ITEM', 1, 0, 'C');
                $this->Cell(33, 10, 'PRODUCT NAME', 1, 0, 'C');
                $this->Cell(14, 10, 'PCS/MTR', 1, 0, 'C');
                $this->Cell(18, 10, 'OPEN (PCS)', 1, 0, 'C');
                $this->Cell(18, 10, 'CLOSE (PCS)', 1, 0, 'C');
                $this->Cell(18, 10, 'TOTAL (PCS)', 1, 0, 'C');
                $this->Cell(15, 10, 'OPEN MTR', 1, 0, 'C');
                $this->Cell(16, 10, 'CLOSE MTR', 1, 0, 'C');
                $this->Cell(15, 10, 'BUNDLE', 1, 0, 'C');
                $this->Cell(20, 10, 'COMMENT', 1, 1, 'C');
            }
        }

        // Create PDF instance
        $pdf = new PDF();
        $pdf->AddPage();

        // --- Two-Column Layout for Vendor & PO/Date Details --- //
        $rowHeight = 6;      // Height for each row
        $leftX = 10;         // X position for left column (Vendor Details)
        $rightX = 140;       // X position for right column (PO Details)
        $startY = 50;        // Starting Y position

        // Row 1: Vendor: 'TO,' ; Right: Date
        $pdf->SetXY($leftX, $startY);
        $pdf->Cell(90, $rowHeight, 'TO,', 0, 0, 'L');
        $pdf->SetXY($rightX, $startY);
        $pdf->Cell(0, $rowHeight, strtoupper($dateTime), 0, 1, 'L');

        // Row 2: Vendor: 'ALUSOL ROLLING SHUTTER' ; Right: PO Number
        $startY += $rowHeight;
        $pdf->SetXY($leftX, $startY);
        $pdf->Cell(90, $rowHeight, 'ALUSOL ROLLING SHUTTER', 0, 0, 'L');
        $pdf->SetXY($rightX, $startY);
        $pdf->Cell(0, $rowHeight, 'PO-' . strtoupper($po_number), 0, 1, 'L');

        // Row 3: Vendor: 'SOUTHERN SABHAN, BLOCK 8, ST 101, BUILDING 174' ; Right: TOTAL TYPE
        $startY += $rowHeight;
        $pdf->SetXY($leftX, $startY);
        $pdf->Cell(90, $rowHeight, 'SOUTHERN SABHAN, BLOCK 8, ST 101, BUILDING 174', 0, 0, 'L');
        $pdf->SetXY($rightX, $startY);
        $pdf->Cell(0, $rowHeight, 'TOTAL ITEM: ' . $totalType, 0, 1, 'L');

        // Row 4: Vendor: 'P.O BOX 29599, SAFAT 15154' ; Right: TOTAL PACKING
        $startY += $rowHeight;
        $pdf->SetXY($leftX, $startY);
        $pdf->Cell(90, $rowHeight, 'P.O BOX 29599, SAFAT 15154', 0, 0, 'L');
        $pdf->SetXY($rightX, $startY);
        $pdf->Cell(0, $rowHeight, 'TOTAL BUNDLE: ' . $totalPacking, 0, 1, 'L');

        // Row 5: Vendor: 'info@alusol-kw.com | (965) 22090000' ; Right: WAREHOUSE
        $startY += $rowHeight;
        $pdf->SetXY($leftX, $startY);
        $pdf->Cell(90, $rowHeight, 'info@alusol-kw.com | (965) 22090000', 0, 0, 'L');
        $pdf->SetXY($rightX, $startY);
        $pdf->Cell(0, $rowHeight, 'WAREHOUSE: ' . $orderFrom, 0, 1, 'L');

        // Row 6: Right column only: STATUS
        $startY += $rowHeight;
        $pdf->SetXY($rightX, $startY);
        $pdf->Cell(0, $rowHeight, 'STATUS: APPROVED', 0, 1, 'L');

        $pdf->Ln(5);

        // Greeting Section
        $pdf->SetFont('Arial', '', 10);
        $pdf->Cell(0, 10, 'Dear,', 0, 1, 'L');
        $pdf->Cell(0, 10, 'Greetings from GULF HOUSE FACTORY. We would like to place the order of the following items:', 0, 1, 'L');
        $pdf->Ln(5);

        // Print Table Header
        $pdf->TableHeader();

        // Print Table Data
        $pdf->SetFont('Arial', '', 7);
        $result->data_seek(0);
        while ($row = $result->fetch_assoc()) {
            if ($pdf->GetY() > 210) { // Check if page break is needed
                $pdf->AddPage();
                $pdf->SetFont('Arial', 'B', 7);
                $pdf->TableHeader(); // Print the table header in bold
                $pdf->SetFont('Arial', '', 7); // Reset font for table content
            }

            // Print table row
            $pdf->Cell(14, 10, $row['item_name'], 1);
            $pdf->Cell(33, 10, $row['product_name'], 1);
            $pdf->Cell(14, 10, $row['length'], 1);
            $pdf->Cell(18, 10, $row['open_piece'], 1);
            $pdf->Cell(18, 10, $row['close_piece'], 1);
            $pdf->Cell(18, 10, $row['total_piece'], 1);
            $pdf->Cell(15, 10, $row['open_meter'], 1);
            $pdf->Cell(16, 10, $row['close_meter'], 1);
            $pdf->Cell(15, 10, $row['bundle'], 1);
            $pdf->MultiCell(20, 10, $row['comment'], 1); // Use MultiCell for comments
        }

        // Output the PDF and force download
        $pdf->Output('D', 'PO-' . $po_number . '.pdf');
    } else {
        echo "No records found.";
    }
} else {
    echo "PO number not provided.";
}
?>