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
                    $this->Image('gulf.png', 57, 10, 90);
                    $this->Ln(20);
                    
                    // Set font for the "PURCHASE ORDER" text only
                    $this->SetFont('Arial', 'B', 16);
                    $this->Cell(0, 10, 'PURCHASE ORDER', 0, 1, 'C');
                    
                    // Set default font for the rest of the header
                    $this->SetFont('Arial', '', 9); // Adjust size for other texts if needed
            
                    $this->Ln(2);
                    $this->Line(10, $this->GetY(), 200, $this->GetY());
                    $this->Ln(5);
                    $this->headerPrinted = true; // Ensure header only prints once
                }
            }
            

            function Footer() {
                $this->SetY(-50);
                $this->SetFont('Arial', '', 8);
                $footerText = "Your time to review our request is greatly appreciated. We look forward to hearing from you soon\n\n";
                $footerText .= "- Thanks for the ongoing support!\n";
                $footerText .= "PROCUREMENT\n";
                $footerText .= "+965 99223382, +965 90012272\n";
                $footerText .= "procurement@gulfhousefactory.com\n";
                $footerText .= "GULF HOUSE FACTORY\n\n";
                
                // MultiCell to print the footer text
                $this->MultiCell(0, 4, $footerText, 0, 'L');
            
                // Add centered Shop address at the bottom
                $this->SetFont('Arial', '', 9);
                $this->SetXY(10, $this->GetY());
                $this->Cell(0, 4, "Shop No. 01, Building No. 287, Mohammad Ibrahim Street, Block No. 03, Al Rai, Kuwait", 0, 1, 'C');
            }
            

            function TableHeader() {
                $this->SetFont('Arial', 'B', 7);
                $this->Cell(16, 10, 'ITEM', 1, 0, 'C');
                $this->Cell(25, 10, 'PRODUCT NAME', 1, 0, 'C');
                $this->Cell(17, 10, 'LENGTH/UNIT', 1, 0, 'C');
                $this->Cell(18, 10, 'OPEN (PIECE)', 1, 0, 'C');
                $this->Cell(18, 10, 'CLOSE (PIECE)', 1, 0, 'C');
                $this->Cell(18, 10, 'TOTAL (PIECE)', 1, 0, 'C');
                $this->Cell(18, 10, 'OPEN METER', 1, 0, 'C');
                $this->Cell(18, 10, 'CLOSE METER', 1, 0, 'C');
                $this->Cell(17, 10, 'TOTAL PACKET', 1, 0, 'C');
                $this->Cell(24, 10, 'COMMENT', 1, 1, 'C');
            }
        }

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
        if ($pdf->GetY() > 220) { // Check if page break is needed
            $pdf->AddPage();
            // Set the header font to bold for the next page
            $pdf->SetFont('Arial', 'B', 7);
            $pdf->TableHeader(); // Print the table header in bold
            // Reset font to normal for table content
            $pdf->SetFont('Arial', '', 7);
        }

        // Use MultiCell for wrapping long content in the cell
        $pdf->Cell(16, 10, $row['item_name'], 1);
        $pdf->Cell(25, 10, $row['product_name'], 1);
        $pdf->Cell(17, 10, $row['length'], 1);
        $pdf->Cell(18, 10, $row['open_piece'], 1);
        $pdf->Cell(18, 10, $row['close_piece'], 1);
        $pdf->Cell(18, 10, $row['total_piece'], 1);
        $pdf->Cell(18, 10, $row['open_meter'], 1);
        $pdf->Cell(18, 10, $row['close_meter'], 1);
        $pdf->Cell(17, 10, $row['bundle'], 1);
        
        // Use MultiCell for the comment column to allow text wrapping if needed
        $pdf->MultiCell(24, 10, $row['comment'], 1);
    }


        $pdf->Output();
    } else {
        echo "No records found.";
    }
}
?>
