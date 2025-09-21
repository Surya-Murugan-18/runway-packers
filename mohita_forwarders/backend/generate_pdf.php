<?php
require_once 'config.php';
require_once 'C:\xampp\htdocs\rework\mohita_forwarders\TCPDF-main\tcpdf.php'; // Ensure correct path

$bookingId = $_GET['booking_id'] ?? '';

if (empty($bookingId)) {
    die('Booking ID is required');
}

try {
    $pdo = getConnection();

    // Fetch booking info
    $query = "SELECT * FROM bookings WHERE booking_id = ?";
    $stmt = $pdo->prepare($query);
    $stmt->execute([$bookingId]);
    $booking = $stmt->fetch();

    if (!$booking) {
        die('Booking not found');
    }

    // Fetch dimensions info
    $dimensionQuery = "SELECT * FROM booking_dimensions WHERE booking_id = ?";
    $dimensionStmt = $pdo->prepare($dimensionQuery);
    $dimensionStmt->execute([$bookingId]);
    $dimensions = $dimensionStmt->fetchAll();

    // Create PDF in landscape orientation
    $pdf = new TCPDF('L', PDF_UNIT, 'A4', true, 'UTF-8', false); // 'L' for Landscape
    $pdf->SetCreator('Mohita Forwarders');
    $pdf->SetAuthor('Mohita Forwarders');
    $pdf->SetTitle("Booking Receipt - $bookingId");
    $pdf->SetMargins(5, 5, 5); // Minimal margins for landscape
    $pdf->SetAutoPageBreak(FALSE); // Disable auto page break to fit everything on one page
    $pdf->SetFont('helvetica', '', 9);

    // Define the three page titles
    $pageTitles = ['POD', 'Customer Copy', 'Accounts Copy'];

    // Generate 3 pages with different titles
    for ($i = 0; $i < 3; $i++) {
        $pdf->AddPage('L'); // 'L' for Landscape orientation
        
        // Set the page title for this iteration
        $pageTitle = $pageTitles[$i];
        
        // Capture HTML from template
        ob_start();
        include 'booking_template.php';
        $html = ob_get_clean();

        // Write HTML to PDF with specific settings for better rendering
        $pdf->writeHTML($html, true, false, true, false, '');
    }

    // Output PDF for download
    $pdf->Output("Booking_$bookingId.pdf", 'D'); // 'D' = Download

} catch (Exception $e) {
    die('Error generating PDF: ' . $e->getMessage());
}
?>