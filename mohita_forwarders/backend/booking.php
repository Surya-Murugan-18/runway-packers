<?php
header('Content-Type: application/json');
require_once 'config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'error' => 'Invalid request method']);
    exit;
}

try {
    $pdo = getConnection();
    
    // Start transaction
    $pdo->beginTransaction();
    
    // Handle file uploads
    $consignorSignature = null;
    $receiverSealSignature = null;
    
    if (isset($_FILES['consignorSignature']) && $_FILES['consignorSignature']['error'] === UPLOAD_ERR_OK) {
        $consignorSignature = handleFileUpload($_FILES['consignorSignature'], 'uploads/signatures/');
    }
    
    if (isset($_FILES['receiverSealSignature']) && $_FILES['receiverSealSignature']['error'] === UPLOAD_ERR_OK) {
        $receiverSealSignature = handleFileUpload($_FILES['receiverSealSignature'], 'uploads/signatures/');
    }
    
    // Prepare booking data
    $bookingId = sanitize($_POST['bookingId']);
    $bookingDate = sanitize($_POST['bookingDate']);
    $source = sanitize($_POST['source']);
    $destination = sanitize($_POST['destination']);
    
    // Sender Details
    $senderName = sanitize($_POST['senderName']);
    $senderAddress1 = sanitize($_POST['senderAddress1']);
    $senderAddress2 = sanitize($_POST['senderAddress2'] ?? '');
    $senderContact = sanitize($_POST['senderContact']);
    
    // Receiver Details
    $receiverCompany = sanitize($_POST['receiverCompany'] ?? '');
    $receiverName = sanitize($_POST['receiverName']);
    $receiverAddress1 = sanitize($_POST['receiverAddress1']);
    $receiverAddress2 = sanitize($_POST['receiverAddress2'] ?? '');
    $receiverPincode = sanitize($_POST['receiverPincode']);
    $receiverContact = sanitize($_POST['receiverContact']);
    $expectedDelivery = sanitize($_POST['expectedDelivery'] ?? null);
    $receiverSignatureDate = sanitize($_POST['receiverSignatureDate'] ?? null);
    $receiverSignatureTime = sanitize($_POST['receiverSignatureTime'] ?? null);
    
    // Additional Info
    $receivedGoodCondition = isset($_POST['receivedGoodCondition']) ? 1 : 0;
    $bookingMode = sanitize($_POST['bookingMode']);
    $declaredValue = floatval($_POST['declaredValue'] ?? 0);
    $invoiceNo = sanitize($_POST['invoiceNo'] ?? '');
    $natureContent = sanitize($_POST['natureContent']);
    
    // Package Info
    $noPackets = intval($_POST['noPackets']);
    $packingType = sanitize($_POST['packingType']);
    $actualWeight = floatval($_POST['actualWeight']);
    $chargedWeight = floatval($_POST['chargedWeight']);
    
    // Charges
    $freight = floatval($_POST['freight'] ?? 0);
    $docketCharges = floatval($_POST['docketCharges'] ?? 0);
    $fov = floatval($_POST['fov'] ?? 0);
    $dodCodCharges = floatval($_POST['dodCodCharges'] ?? 0);
    $odaMiscCharges = floatval($_POST['odaMiscCharges'] ?? 0);
    $gstServiceTax = floatval($_POST['gstServiceTax'] ?? 0);
    $grandTotal = floatval($_POST['grandTotal'] ?? 0);
    
    // Signatures & Agreement
    $bookingClerkName = sanitize($_POST['bookingClerkName'] ?? '');
    $codDodAmount = floatval($_POST['codDodAmount'] ?? 0);
    $termsAgreed = isset($_POST['termsAgreed']) ? 1 : 0;
    
    // Check if booking ID already exists
    $checkQuery = "SELECT id FROM bookings WHERE booking_id = ?";
    $checkStmt = $pdo->prepare($checkQuery);
    $checkStmt->execute([$bookingId]);
    
    if ($checkStmt->fetch()) {
        throw new Exception('Booking ID already exists');
    }
    
    // Insert booking
    $insertQuery = "INSERT INTO bookings (
        booking_id, booking_date, source, destination,
        sender_name, sender_address1, sender_address2, sender_contact, consignor_signature,
        receiver_company, receiver_name, receiver_address1, receiver_address2, receiver_pincode, 
        receiver_contact, expected_delivery_date, receiver_signature_date, receiver_signature_time, 
        receiver_seal_signature, received_good_condition, booking_mode, declared_value, 
        invoice_no, nature_content, no_packets, packing_type, actual_weight, charged_weight,
        freight, docket_charges, fov, dod_cod_charges, oda_misc_charges, 
        gst_service_tax, grand_total, booking_clerk_name, cod_dod_amount, terms_agreed
    ) VALUES (
        :booking_id, :booking_date, :source, :destination,
        :sender_name, :sender_address1, :sender_address2, :sender_contact, :consignor_signature,
        :receiver_company, :receiver_name, :receiver_address1, :receiver_address2, :receiver_pincode,
        :receiver_contact, :expected_delivery_date, :receiver_signature_date, :receiver_signature_time,
        :receiver_seal_signature, :received_good_condition, :booking_mode, :declared_value,
        :invoice_no, :nature_content, :no_packets, :packing_type, :actual_weight, :charged_weight,
        :freight, :docket_charges, :fov, :dod_cod_charges, :oda_misc_charges,
        :gst_service_tax, :grand_total, :booking_clerk_name, :cod_dod_amount, :terms_agreed
    )";
    
    $insertStmt = $pdo->prepare($insertQuery);
    $insertStmt->execute([
        'booking_id' => $bookingId,
        'booking_date' => $bookingDate,
        'source' => $source,
        'destination' => $destination,
        'sender_name' => $senderName,
        'sender_address1' => $senderAddress1,
        'sender_address2' => $senderAddress2,
        'sender_contact' => $senderContact,
        'consignor_signature' => $consignorSignature,
        'receiver_company' => $receiverCompany,
        'receiver_name' => $receiverName,
        'receiver_address1' => $receiverAddress1,
        'receiver_address2' => $receiverAddress2,
        'receiver_pincode' => $receiverPincode,
        'receiver_contact' => $receiverContact,
        'expected_delivery_date' => $expectedDelivery,
        'receiver_signature_date' => $receiverSignatureDate,
        'receiver_signature_time' => $receiverSignatureTime,
        'receiver_seal_signature' => $receiverSealSignature,
        'received_good_condition' => $receivedGoodCondition,
        'booking_mode' => $bookingMode,
        'declared_value' => $declaredValue,
        'invoice_no' => $invoiceNo,
        'nature_content' => $natureContent,
        'no_packets' => $noPackets,
        'packing_type' => $packingType,
        'actual_weight' => $actualWeight,
        'charged_weight' => $chargedWeight,
        'freight' => $freight,
        'docket_charges' => $docketCharges,
        'fov' => $fov,
        'dod_cod_charges' => $dodCodCharges,
        'oda_misc_charges' => $odaMiscCharges,
        'gst_service_tax' => $gstServiceTax,
        'grand_total' => $grandTotal,
        'booking_clerk_name' => $bookingClerkName,
        'cod_dod_amount' => $codDodAmount,
        'terms_agreed' => $termsAgreed
    ]);
    
    // Handle dimensions - Parse from form data
    if (isset($_POST['dimension_length']) && is_array($_POST['dimension_length'])) {
        // Extract dimensions from POST data
        $dimensionQuery = "INSERT INTO booking_dimensions (booking_id, length_cm, breadth_cm, height_cm, quantity, volumetric_weight) VALUES (?, ?, ?, ?, ?, ?)";
        $dimensionStmt = $pdo->prepare($dimensionQuery);
        
        // Get all dimension inputs - handle both array and individual field formats
        $lengths = $_POST['dimension_length'];
        $breadths = $_POST['dimension_breadth'];
        $heights = $_POST['dimension_height'];
        $quantities = $_POST['dimension_quantity'];
        
        // If arrays are set and not empty, use them
        for ($i = 0; $i < count($lengths); $i++) {
            $length = floatval($lengths[$i] ?? 0);
            $breadth = floatval($breadths[$i] ?? 0);
            $height = floatval($heights[$i] ?? 0);
            $quantity = intval($quantities[$i] ?? 1);
            
            if ($length > 0 && $breadth > 0 && $height > 0 && $quantity > 0) {
                $volumetricWeight = ($length * $breadth * $height * $quantity) / 6000;
                
                $dimensionStmt->execute([
                    $bookingId,
                    $length,
                    $breadth,
                    $height,
                    $quantity,
                    $volumetricWeight
                ]);
            }
        }
    }
    
    // Commit transaction
    $pdo->commit();
    
    echo json_encode([
        'success' => true,
        'message' => 'Booking saved successfully',
        'booking_id' => $bookingId
    ]);
    
} catch (Exception $e) {
    // Rollback transaction on error
    if ($pdo && $pdo->inTransaction()) {
        $pdo->rollback();
    }
    
    // Log the error for debugging
    error_log("Booking Error: " . $e->getMessage());
    
    // Return proper JSON error response
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
    exit;
}
?>
