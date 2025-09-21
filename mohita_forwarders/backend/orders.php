<?php
header('Content-Type: application/json');
require_once 'config.php';

$action = $_GET['action'] ?? 'list';

try {
    $pdo = getConnection();
    
    switch ($action) {
        case 'list':
            $status = $_GET['status'] ?? 'all';
            $search = $_GET['search'] ?? '';
            $page = max(1, intval($_GET['page'] ?? 1));
            $limit = 20;
            $offset = ($page - 1) * $limit;
            
            $query = "SELECT 
                        b.id,
                        b.booking_id,
                        b.booking_date,
                        b.source,
                        b.destination,
                        b.sender_name,
                        b.receiver_name,
                        b.receiver_contact,
                        COALESCE(b.status, 'pending') as status,
                        COALESCE(b.grand_total, 0) as grand_total,
                        b.packing_type,
                        b.no_packets,
                        b.charged_weight,
                        b.created_at,
                        b.updated_at
                      FROM bookings b";
            
            $countQuery = "SELECT COUNT(*) as total FROM bookings b";
            $params = [];
            $conditions = [];
            
            if ($status !== 'all') {
                if ($status === 'pending') {
                    $conditions[] = "(b.status != 'delivered' OR b.status IS NULL)";
                } else {
                    $conditions[] = "b.status = ?";
                    $params[] = $status;
                }
            }
            
            if (!empty($search)) {
                $conditions[] = "(b.booking_id LIKE ? OR b.sender_name LIKE ? OR b.receiver_name LIKE ? OR b.source LIKE ? OR b.destination LIKE ?)";
                $searchParam = "%$search%";
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam, $searchParam, $searchParam]);
            }
            
            if (!empty($conditions)) {
                $whereClause = " WHERE " . implode(" AND ", $conditions);
                $query .= $whereClause;
                $countQuery .= $whereClause;
            }
            
            $query .= " ORDER BY b.created_at DESC LIMIT $limit OFFSET $offset";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            $countStmt = $pdo->prepare($countQuery);
            $countStmt->execute($params);
            $totalCount = $countStmt->fetch()['total'];
            
            echo json_encode([
                'success' => true,
                'orders' => $orders,
                'total' => $totalCount,
                'page' => $page,
                'pages' => ceil($totalCount / $limit)
            ]);
            break;
            
        case 'get':
            $bookingId = $_GET['booking_id'] ?? '';
            if (empty($bookingId)) {
                throw new Exception('Booking ID is required');
            }
            
            $query = "SELECT * FROM bookings WHERE booking_id = ?";
            $stmt = $pdo->prepare($query);
            $stmt->execute([$bookingId]);
            $booking = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$booking) {
                throw new Exception('Booking not found');
            }
            
            echo json_encode([
                'success' => true,
                'booking' => $booking
            ]);
            break;
            
        case 'update':
            if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
                throw new Exception('Invalid request method');
            }
            
            $bookingId = sanitize($_POST['booking_id']);
            $updateData = [];
            $params = [];
            
            // Build update query dynamically based on provided fields
            $allowedFields = [
                'sender_name', 'receiver_name', 'receiver_contact', 'freight',
                'source', 'destination', 'booking_mode', 'nature_content',
                'no_packets', 'packing_type', 'actual_weight', 'charged_weight',
                'docket_charges', 'fov', 'dod_cod_charges', 'oda_misc_charges',
                'gst_service_tax', 'grand_total', 'declared_value', 'invoice_no',
                'booking_clerk_name', 'cod_dod_amount'
            ];
            
            foreach ($allowedFields as $field) {
                if (isset($_POST[$field])) {
                    $updateData[] = "$field = ?";
                    $params[] = sanitize($_POST[$field]);
                }
            }
            
            if (empty($updateData)) {
                throw new Exception('No fields to update');
            }
            
            $params[] = $bookingId;
            
            $updateQuery = "UPDATE bookings SET " . implode(', ', $updateData) . ", updated_at = CURRENT_TIMESTAMP WHERE booking_id = ?";
            $updateStmt = $pdo->prepare($updateQuery);
            $updateStmt->execute($params);
            
            echo json_encode([
                'success' => true,
                'message' => 'Booking updated successfully'
            ]);
            break;
            
        case 'export':
            $status = $_GET['status'] ?? 'all';
            $search = $_GET['search'] ?? '';
            
            $query = "SELECT 
                        b.booking_id,
                        b.booking_date,
                        b.source,
                        b.destination,
                        b.sender_name,
                        b.receiver_name,
                        b.receiver_contact,
                        COALESCE(b.status, 'pending') as status,
                        COALESCE(b.grand_total, 0) as grand_total,
                        b.packing_type,
                        b.no_packets,
                        b.charged_weight,
                        b.created_at
                      FROM bookings b";
            
            $params = [];
            $conditions = [];
            
            if ($status !== 'all') {
                if ($status === 'pending') {
                    $conditions[] = "(b.status != 'delivered' OR b.status IS NULL)";
                } else {
                    $conditions[] = "b.status = ?";
                    $params[] = $status;
                }
            }
            
            if (!empty($search)) {
                $conditions[] = "(b.booking_id LIKE ? OR b.sender_name LIKE ? OR b.receiver_name LIKE ?)";
                $searchParam = "%$search%";
                $params = array_merge($params, [$searchParam, $searchParam, $searchParam]);
            }
            
            if (!empty($conditions)) {
                $query .= " WHERE " . implode(" AND ", $conditions);
            }
            
            $query .= " ORDER BY b.created_at DESC";
            
            $stmt = $pdo->prepare($query);
            $stmt->execute($params);
            $orders = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Generate Excel file
            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment; filename="orders_' . date('Y-m-d') . '.xls"');
            
            echo "<table border='1'>";
            echo "<tr>";
            echo "<th>Booking ID</th>";
            echo "<th>Booking Date</th>";
            echo "<th>Source</th>";
            echo "<th>Destination</th>";
            echo "<th>Sender Name</th>";
            echo "<th>Receiver Name</th>";
            echo "<th>Receiver Contact</th>";
            echo "<th>Status</th>";
            echo "<th>Grand Total</th>";
            echo "<th>Packing Type</th>";
            echo "<th>No. Packets</th>";
            echo "<th>Charged Weight</th>";
            echo "<th>Created At</th>";
            echo "</tr>";
            
            foreach ($orders as $order) {
                echo "<tr>";
                echo "<td>" . htmlspecialchars($order['booking_id']) . "</td>";
                echo "<td>" . htmlspecialchars($order['booking_date']) . "</td>";
                echo "<td>" . htmlspecialchars($order['source']) . "</td>";
                echo "<td>" . htmlspecialchars($order['destination']) . "</td>";
                echo "<td>" . htmlspecialchars($order['sender_name']) . "</td>";
                echo "<td>" . htmlspecialchars($order['receiver_name']) . "</td>";
                echo "<td>" . htmlspecialchars($order['receiver_contact']) . "</td>";
                echo "<td>" . htmlspecialchars($order['status']) . "</td>";
                echo "<td>₹" . number_format($order['grand_total'], 2) . "</td>";
                echo "<td>" . htmlspecialchars($order['packing_type']) . "</td>";
                echo "<td>" . htmlspecialchars($order['no_packets']) . "</td>";
                echo "<td>" . htmlspecialchars($order['charged_weight']) . " kg</td>";
                echo "<td>" . htmlspecialchars($order['created_at']) . "</td>";
                echo "</tr>";
            }
            echo "</table>";
            exit;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    error_log("Orders Error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
