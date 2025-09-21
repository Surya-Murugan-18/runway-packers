<?php
require_once 'config.php';

$action = $_GET['action'] ?? '';

try {
    $pdo = getConnection();
    
    switch ($action) {
        case 'add':
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = sanitize($_POST['name']);
                $password = sanitize($_POST['password']);
                
                if (empty($username) || empty($password)) {
                    throw new Exception('Username and password are required');
                }
                
                // Check if username already exists
                $checkQuery = "SELECT id FROM users WHERE username = ?";
                $checkStmt = $pdo->prepare($checkQuery);
                $checkStmt->execute([$username]);
                
                if ($checkStmt->fetch()) {
                    throw new Exception('Username already exists');
                }
                
                // Insert new user
                $insertQuery = "INSERT INTO users (username, password) VALUES (?, ?)";
                $insertStmt = $pdo->prepare($insertQuery);
                $insertStmt->execute([$username, $password]);
                
                echo json_encode(['success' => true, 'message' => 'User added successfully']);
            }
            break;
            
        case 'list':
            $query = "SELECT id, username, password, created_at FROM users ORDER BY created_at DESC";
            $result = $pdo->query($query)->fetchAll();
            echo json_encode($result);
            break;
            
        case 'delete':
            $id = (int)($_GET['id'] ?? 0);
            if ($id <= 0) {
                throw new Exception('Invalid user ID');
            }
            
            // Don't allow deletion of the first user (admin)
            if ($id === 1) {
                throw new Exception('Cannot delete admin user');
            }
            
            $deleteQuery = "DELETE FROM users WHERE id = ?";
            $deleteStmt = $pdo->prepare($deleteQuery);
            $deleteStmt->execute([$id]);
            
            echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
            break;
            
        default:
            throw new Exception('Invalid action');
    }
    
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>
