<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $pdo = getConnection();
    
    // Get total bookings
    $totalStmt = $pdo->query("SELECT COUNT(*) as total FROM bookings");
    $total = $totalStmt->fetch()['total'];
    
    // Get delivered count
    $deliveredStmt = $pdo->query("SELECT COUNT(*) as delivered FROM bookings WHERE status = 'delivered'");
    $delivered = $deliveredStmt->fetch()['delivered'];
    
    // Get pending count (all non-delivered)
    $pendingStmt = $pdo->query("SELECT COUNT(*) as pending FROM bookings WHERE status != 'delivered' OR status IS NULL");
    $pending = $pendingStmt->fetch()['pending'];
    
    // Get recent orders
    $recentStmt = $pdo->query("SELECT booking_id as id_no, booking_date, source, destination, receiver_name, status, grand_total FROM bookings ORDER BY created_at DESC LIMIT 10");
    $recent = $recentStmt->fetchAll();
    
    echo json_encode([
        'success' => true,
        'total' => $total,
        'delivered' => $delivered,
        'pending' => $pending,
        'recent' => $recent
    ]);
    
} catch (Exception $e) {
    error_log("Dashboard Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'total' => 0,
        'delivered' => 0,
        'pending' => 0,
        'recent' => []
    ]);
}
?>
