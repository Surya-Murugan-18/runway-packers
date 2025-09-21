<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    $pdo = getConnection();
    
    $fromDate = $_GET['reportFromDate'] ?? '';
    $toDate = $_GET['reportToDate'] ?? '';
    $download = $_GET['download'] ?? '';
    
    $query = "SELECT 
                b.booking_id as id_no,
                b.booking_date,
                b.source,
                b.destination,
                b.sender_name,
                b.receiver_name,
                b.receiver_contact,
                b.packing_type,
                b.nature_content,
                COALESCE(b.status, 'pending') as status,
                COALESCE(b.grand_total, 0) as grand_total,
                CASE WHEN b.status = 'delivered' THEN b.updated_at ELSE NULL END as delivered_date
              FROM bookings b";
    
    $params = [];
    $conditions = [];
    
    if (!empty($fromDate)) {
        $conditions[] = "b.booking_date >= ?";
        $params[] = $fromDate;
    }
    
    if (!empty($toDate)) {
        $conditions[] = "b.booking_date <= ?";
        $params[] = $toDate;
    }
    
    if (!empty($conditions)) {
        $query .= " WHERE " . implode(" AND ", $conditions);
    }
    
    $query .= " ORDER BY b.booking_date DESC";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute($params);
    $data = $stmt->fetchAll();
    
    if ($download === '1') {
        // Generate Excel file
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment; filename="booking_report_' . date('Y-m-d') . '.xls"');
        
        echo "<table border='1'>";
        echo "<tr>";
        echo "<th>ID No</th>";
        echo "<th>Booking Date</th>";
        echo "<th>Source</th>";
        echo "<th>Destination</th>";
        echo "<th>Sender Name</th>";
        echo "<th>Receiver Name</th>";
        echo "<th>Contact</th>";
        echo "<th>Packing</th>";
        echo "<th>Description</th>";
        echo "<th>Status</th>";
        echo "<th>Amount</th>";
        echo "<th>Delivered Date</th>";
        echo "</tr>";
        
        foreach ($data as $row) {
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id_no']) . "</td>";
            echo "<td>" . htmlspecialchars($row['booking_date']) . "</td>";
            echo "<td>" . htmlspecialchars($row['source']) . "</td>";
            echo "<td>" . htmlspecialchars($row['destination']) . "</td>";
            echo "<td>" . htmlspecialchars($row['sender_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['receiver_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['receiver_contact']) . "</td>";
            echo "<td>" . htmlspecialchars($row['packing_type']) . "</td>";
            echo "<td>" . htmlspecialchars($row['nature_content']) . "</td>";
            echo "<td>" . htmlspecialchars($row['status']) . "</td>";
            echo "<td>₹" . number_format($row['grand_total'], 2) . "</td>";
            echo "<td>" . htmlspecialchars($row['delivered_date'] ?? '-') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        exit;
    }
    
    echo json_encode([
        'success' => true,
        'data' => $data
    ]);
    
} catch (Exception $e) {
    error_log("Report Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
