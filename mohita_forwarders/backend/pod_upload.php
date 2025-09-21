<?php
header('Content-Type: application/json');
require_once 'config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception('Invalid request method');
    }
    
    $pdo = getConnection();
    
    $bookingId = sanitize($_POST['id_no']);
    
    if (empty($bookingId)) {
        throw new Exception('Booking ID is required');
    }
    
    // Check if booking exists
    $checkStmt = $pdo->prepare("SELECT id FROM bookings WHERE booking_id = ?");
    $checkStmt->execute([$bookingId]);
    if (!$checkStmt->fetch()) {
        throw new Exception('Booking not found');
    }
    
    if (!isset($_FILES['pod_file']) || $_FILES['pod_file']['error'] !== UPLOAD_ERR_OK) {
        throw new Exception('File upload failed');
    }
    
    $file = $_FILES['pod_file'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'application/pdf'];
    
    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only images and PDF files are allowed');
    }
    
    // Create upload directory if it doesn't exist
    $uploadDir = '../uploads/pod/';
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    $fileName = $bookingId . '_' . time() . '_' . basename($file['name']);
    $filePath = $uploadDir . $fileName;
    
    if (!move_uploaded_file($file['tmp_name'], $filePath)) {
        throw new Exception('Failed to save file');
    }
    
    // Save to database
    $stmt = $pdo->prepare("INSERT INTO pod_uploads (booking_id, file_name, file_path) VALUES (?, ?, ?)");
    $stmt->execute([$bookingId, $fileName, $filePath]);
    
    echo json_encode(['success' => true, 'message' => 'POD uploaded successfully']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
