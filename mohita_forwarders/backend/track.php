<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

try {
    $trackingId = $_GET['id'] ?? '';
    
    if (empty($trackingId)) {
        throw new Exception('Tracking ID is required');
    }
    
    $pdo = getConnection();
    
    // Get booking details
    $bookingStmt = $pdo->prepare("SELECT * FROM bookings WHERE booking_id = ?");
    $bookingStmt->execute([$trackingId]);
    $booking = $bookingStmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$booking) {
        throw new Exception('Booking not found');
    }
    
    // Get status updates
    $statusStmt = $pdo->prepare("SELECT * FROM status_updates WHERE booking_id = ? ORDER BY status_date DESC, status_time DESC");
    $statusStmt->execute([$trackingId]);
    $updates = $statusStmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get POD upload if status is delivered
    $podData = null;
    if ($booking['status'] === 'delivered') {
        $podStmt = $pdo->prepare("SELECT * FROM pod_uploads WHERE booking_id = ? ORDER BY id DESC LIMIT 1");
        $podStmt->execute([$trackingId]);
        $podData = $podStmt->fetch(PDO::FETCH_ASSOC);
    }
    
    echo json_encode([
        'success' => true,
        'booking' => $booking,
        'updates' => $updates,
        'pod' => $podData
    ]);
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
