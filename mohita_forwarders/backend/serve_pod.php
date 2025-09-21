<?php
require_once 'config.php';

try {
    $bookingId = $_GET['booking_id'] ?? '';
    
    if (empty($bookingId)) {
        throw new Exception('Booking ID is required');
    }
    
    $pdo = getConnection();
    
    // Get POD file info
    $stmt = $pdo->prepare("SELECT * FROM pod_uploads WHERE booking_id = ? ORDER BY id DESC LIMIT 1");
    $stmt->execute([$bookingId]);
    $podData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$podData) {
        throw new Exception('POD file not found');
    }
    
    $filePath = $podData['file_path'];
    
    // Check if file exists
    if (!file_exists($filePath)) {
        throw new Exception('File not found on server');
    }
    
    // Get file info
    $fileInfo = pathinfo($filePath);
    $fileName = $podData['file_name'];
    
    // Set appropriate headers
    $mimeType = mime_content_type($filePath);
    
    if (isset($_GET['download']) && $_GET['download'] == '1') {
        // Force download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $fileName . '"');
    } else {
        // Display in browser
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: inline; filename="' . $fileName . '"');
    }
    
    header('Content-Length: ' . filesize($filePath));
    header('Cache-Control: no-cache, must-revalidate');
    header('Expires: Sat, 26 Jul 1997 05:00:00 GMT');
    
    // Output file
    readfile($filePath);
    
} catch (Exception $e) {
    header('HTTP/1.1 404 Not Found');
    echo 'Error: ' . $e->getMessage();
}
?>
