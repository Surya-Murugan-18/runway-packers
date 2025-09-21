<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    $pdo = getConnection();
    
    $bookingId = sanitize($_POST['id_no'] ?? '');
    $statusDate = sanitize($_POST['status_date'] ?? '');
    $statusTime = sanitize($_POST['status_time'] ?? '');
    $status = sanitize($_POST['status'] ?? '');
    $remarks = sanitize($_POST['remarks'] ?? '');
    $receiverName = sanitize($_POST['receiver_name'] ?? '');
    
    if (empty($bookingId) || empty($status)) {
        throw new Exception('Booking ID and status are required');
    }
    
    // Set default date and time if not provided
    if (empty($statusDate)) {
        $statusDate = date('Y-m-d');
    }
    if (empty($statusTime)) {
        $statusTime = date('H:i:s');
    }
    
    // Check if booking exists
    $checkStmt = $pdo->prepare("SELECT id FROM bookings WHERE booking_id = ?");
    $checkStmt->execute([$bookingId]);
    if (!$checkStmt->fetch()) {
        throw new Exception('Booking not found');
    }
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Insert status update
    $stmt = $pdo->prepare("INSERT INTO status_updates (booking_id, status, status_date, status_time, remarks, receiver_name) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->execute([$bookingId, $status, $statusDate, $statusTime, $remarks, $receiverName]);
    
    // Update booking status
    $updateStmt = $pdo->prepare("UPDATE bookings SET status = ?, updated_at = CURRENT_TIMESTAMP WHERE booking_id = ?");
    $updateStmt->execute([$status, $bookingId]);
    
    $pdo->commit();
    
    echo json_encode(['success' => true, 'message' => 'Status updated successfully']);
    
} catch (Exception $e) {
    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    error_log("Status Update Error: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
