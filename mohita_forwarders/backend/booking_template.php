<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Booking Receipt - <?php echo htmlspecialchars($booking['booking_id']); ?></title>
    <style>
        @page {
            size: A4 landscape;
            margin: 2mm;
        }
        body { 
            font-family: Arial, sans-serif; 
            margin: 0;
            padding: 0;
            font-size: 8px;
            line-height: 1.0;
        }
        
        .page-title {
            text-align: center;
            font-size: 12px;
            font-weight: bold;
            margin-bottom: 2px;
            padding: 2px;
            background: #f0f0f0;
            border: 1px solid #000;
        }
        
        /* Header */
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
            margin-bottom: 1px;
        }
        
        .header-table td {
            border-right: 1px solid #000;
            padding: 3px;
            vertical-align: middle;
        }
        
        .header-table td:last-child {
            border-right: none;
        }
        
        .logo-cell {
            width: 163px;
            text-align: center;
        }
        
       .logo {
    display: flex;
    justify-content: center;
    align-items: center;
    height: 300px; /* optional: adjust as needed */
}

.logo-img {
    width: 10%;
    height: 10%;
    object-fit: cover;
}

        
        .company-cell {
            width: 406px;
            text-align: center;
        }
        
        .company-name {
            font-size: 16px;
            font-weight: bold;
            margin: 1px 0;
        }
        
        .company-desc {
            font-size: 9px;
            margin: 0;
        }
        
        .gst-text {
            font-size: 9px;
            font-weight: bold;
            margin-top: 2px;
        }
        
        .contact-cell {
            width: 244px;
            font-size: 10px;
            text-align: right;
            
        }
        
        /* Main Form Table - Exact proportions */
        .main-table {
            width: 100%;
            border-collapse: collapse;
            border: 2px solid #000;
        }
        
        .main-table td {
            border: 1px solid #000;
            padding: 2px;
            vertical-align: top;
            font-size: 7px;
        }
        
        /* Column widths - exact proportions */
        .col-left {
            width: 20%;
        }
        
        .col-center {
            width: 50%;
        }
        
        .col-right {
            width: 30%;
        }
        
        /* Row heights - exact proportions */
        .row-1 { height: 25px; }
        .row-2 { height: 60px; }
        .row-3 { height: 40px; }
        .row-4 { height: 40px; }
        .row-5 { height: 120px; }
        .row-6 { height: 25px; }
        .row-7 { height: 35px; }
        
        /* Field styling */
        .field-label {
            font-size: 6px;
            font-weight: bold;
            margin-bottom: 1px;
        }
        
        .field-value {
            font-size: 7px;
        }
        
        .field-line {
            border-bottom: 1px solid #000;
            height: 8px;
            font-size: 7px;
            padding-left: 2px;
        }
        
        /* Center column styling */
        .to-header {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            line-height: 25px;
        }
        
        .consignor-consignee-container {
            position: relative;
            height: 60px;
            padding: 0;
        }
        
        .consignor-box {
            position: absolute;
            left: 2px;
            top: 2px;
            width: 47%;
            height: 56px;
            border-right: 1px solid #000;
        }
        
        .consignee-box {
            position: absolute;
            right: 2px;
            top: 2px;
            width: 47%;
            height: 56px;
        }
        
        .section-title {
            border: 1px solid #000;
            text-align: center;
            font-weight: bold;
            font-size: 6px;
            padding: 1px;
            margin-bottom: 2px;
            background: #f0f0f0;
        }
        
        .booked-risk-text {
            text-align: center;
            font-size: 5px;
            margin-bottom: 2px;
        }
        
        .address-text {
            font-size: 6px;
            line-height: 1.2;
        }
        
        /* Booking details table */
        .booking-details-container {
            height: 40px;
            padding: 0;
        }
        
        .booking-details-table {
            width: 100%;
            height: 100%;
            border-collapse: collapse;
        }
        
        .booking-details-table th {
            border: 1px solid #000;
            padding: 1px;
            font-size: 5px;
            font-weight: bold;
            text-align: center;
            background: #f0f0f0;
            height: 15px;
        }
        
        .booking-details-table td {
            border: 1px solid #000;
            padding: 1px;
            font-size: 6px;
            text-align: center;
            height: 25px;
        }
        
        .dimensions-text {
            text-align: center;
            font-weight: bold;
            font-size: 9px;
            line-height: 20px;
        }
        
        /* Right column styling */
        .booking-number-container {
            text-align: center;
            height: 25px;
            line-height: 25px;
        }
        
        .no-text {
            font-size: 8px;
            font-weight: bold;
            display: inline;
            margin-right: 10px;
        }
        
        .booking-number {
            font-size: 18px;
            font-weight: bold;
            display: inline;
        }
        
        .received-consignment {
            text-align: center;
            font-size: 6px;
            font-weight: bold;
            height: 60px;
            line-height: 1.2;
            padding: 8px 2px;
        }
        
        .booking-mode-container {
            height: 40px;
            padding: 3px;
        }
        
        .booking-mode-label {
            font-weight: bold;
            font-size: 7px;
            margin-bottom: 3px;
        }
        
        .checkbox-item {
            margin: 1px 0;
            font-size: 6px;
        }
        
        .checkbox {
            display: inline-block;
            width: 6px;
            height: 6px;
            border: 1px solid #000;
            margin-right: 3px;
            
            line-height: 4px;
            font-size: 4px;
        }
        
        .receiver-name-container {
            height: 20px;
            padding: 2px;
        }
        
        .date-time-container {
            height: 120px;
            padding: 3px;
        }
        
        .date-time-field {
            margin-bottom: 8px;
        }
        
        .signature-field {
            margin-top: 15px;
            
        }
        
        /* Charges section */
        .charges-container {
            height: 120px;
            padding: 0;
        }
        
        .charges-table {
            width: 100%;
            height: 500%;
            border-collapse: collapse;
        }
        
        .charges-table td {
            border: 1px solid #000;
            padding: 2px;
            height: 14px;
            font-size: 6px;
        }
        
        .charges-header {
            font-weight: bold;
            text-align: center;
            background: #f0f0f0;
            height: 12px;
        }
        
        .charge-label {
            font-weight: bold;
            width: 65%;
            padding-left: 3px;
        }
        
        .charge-amount {
            width: 35%;
            text-align: right;
            padding-right: 3px;
        }
        
        .grand-total-row {
            font-weight: bold;
            background: #f0f0f0;
        }
        
        .cod-dod-text {
            font-size: 7px;
            font-weight: bold;
            line-height: 25px;
            padding-left: 3px;
        }
        
        /* Signature section - part of main table */
        .signature-row {
            height: 35px;
        }
        
        .signature-cell {
            text-align: center;
            font-size: 14px;
            font-weight: bold;
            vertical-align: bottom;
            padding: 5px 2px;
        }
    </style>
</head>
<body>
    <div class="page-title"><?php echo htmlspecialchars($pageTitle ?? 'POD'); ?></div>
    
    <!-- Header -->
    <table class="header-table">
        <tr>
            <td class="logo-cell">
                <div class="logo">
                    <img src="http://localhost/runway/mohita_forwarders/img/admin2.jpg">
                </div>
            </td>

            <td class="company-cell">
                <div class="company-name">RUNWAY PACKERS</div>
                <div class="company-desc">(Complete Freight Services)</div>
                <div class="company-desc">(All courier & cargo services)</div>
                <div class="gst-text">GST -33GJNPR9643N1Z5</div>
            </td>
            <td class="contact-cell"><br>
                11B - Raghavendra road,<br><br>
                Vanashakthi nagar Extn,<br><br>
                Puthagaram,Kolathur,<br><br>
                Chennai-600099. Mob:044-22241022<br><br>
                enterprisemohita@gmail.com
            </td>
        </tr>
    </table>
    
    <!-- Main Form Table -->
    <table class="main-table">
        <!-- Row 1: Insurance | To | Booking Number -->
        <tr class="row-1">
    <td class="col-left">
        <div class="field-label">If Insured details of Insurance policy</div>
    </td>
    <td class="col-center to-header" style="text-align: center;">
        
        Source : <?php echo htmlspecialchars($booking['source']); ?> 
        &nbsp;To&nbsp; 
        Destination : <?php echo htmlspecialchars($booking['destination']); ?>
        <div class="booked-risk-text">BOOKED AT OWNERS RISK</div>
    </td>
    
    <td class="col-right booking-number-container">
        <span class="no-text">No.</span>
        <span class="booking-number"><?php echo htmlspecialchars($booking['booking_id']); ?></span>
    </td>
</tr>


        
        <!-- Row 2: Date of Booking | Consignor/Consignee | Received Consignment -->
        <tr class="row-2">
            <td class="col-left">
                <div class="field-label">Date of Booking</div>
                <div class="field-line"style="border-bottom: none;"><?php echo date('d-m-Y', strtotime($booking['booking_date'])); ?></div>
            </td>
            <td class="col-center consignor-consignee-container">
                <div class="consignor-box">
                    <div class="section-title">CONSIGNOR</div>
                    <div class="address-text">
                        <?php echo htmlspecialchars($booking['sender_name']); ?><br>
                        <?php 
                            echo htmlspecialchars($booking['sender_address1']) . ',  ' . 
                            htmlspecialchars($booking['sender_address2']) . ',  ' . 
                             htmlspecialchars($booking['sender_contact']); 
                        ?>

                    </div>
                </div>
                <div class="consignee-box">
                    
                    <div class="section-title">CONSIGNEE</div>
                    <div class="address-text">
                        <?php echo htmlspecialchars($booking['receiver_name']); ?><br>
                        <?php 
                            echo htmlspecialchars($booking['receiver_address1']) . ', ' . 
                            htmlspecialchars($booking['receiver_address2']) . ', ' . 
                            htmlspecialchars($booking['receiver_contact']); 
                        ?>

                    </div>
                </div>
            </td>
            <td class="col-right received-consignment">
                RECEIVED CONSIGNMENT<br>IN GOOD CONDITION 
                <div class="booking-mode-label">Booking</div>
                <div class="checkbox-item" style="text-align:left;padding-left:10px">
                    <span class="checkbox"><?php echo $booking['booking_mode'] == 'By Air' ? '✓' : ''; ?></span> By Air
                </div>
                <div class="checkbox-item" style="text-align:left;padding-left:10px">
                    <span class="checkbox"><?php echo $booking['booking_mode'] == 'By Train' ? '✓' : ''; ?></span> By Train
                </div>
                <div class="checkbox-item" style="text-align:left;padding-left:10px">
                    <span class="checkbox"><?php echo $booking['booking_mode'] == 'By Road' ? '✓' : ''; ?></span> By Road
                </div>
            </td>
        </tr>
        
        <!-- Row 3: Declared Value | Booking Details Table | Booking Mode -->
        <tr class="row-3">
            <td class="col-left">
                <div class="field-label">Declared<br>Value Rs.</div>
                <div class="field-value" style="margin-top: 5px;">
                    <?php echo $booking['declared_value'] > 0 ? '₹' . number_format($booking['declared_value'], 2) : ''; ?>
                </div>
            </td>
            <td class="col-center booking-details-container">
                <table class="booking-details-table">
                    <tr>
                       
                        <th>PIN</th>
                    </tr>
                    <tr>
                        
                        <td><?php echo htmlspecialchars($booking['receiver_pincode']); ?></td>
                    </tr>
                </table>
            </td>
           <td class="col-right receiver-name-container">
                <div class="field-label">Receiver's Name</div>
                <div class="field-line"style="border-bottom: none;"></div>
            </td>
        </tr>
        
        <!-- Row 4: Inv.No | L X B X H | Receiver's Name -->
        <tr class="row-4">
            <td class="col-left">
                <div class="field-label">Inv.No.</div>
                <?php echo htmlspecialchars($booking['invoice_no'] ?? ''); ?>
            </td>
            <td class="col-center dimensions-text">L X B X H</td>
            <td class="col-right receiver-name-container">
               
                    <div class="field-label">Date:</div>
           
            </td>
        </tr>
        
        <!-- Row 5: Pvt Mark | Charges Table | Date/Time/Signature -->
        <tr class="row-5" >
            <td class="col-left">
                <div class="field-label">Nature of Content</div>
                 <?php echo htmlspecialchars($booking['nature_content']); ?><br>
            </td>
            <td class="col-center charges-container">
                <table class="charges-table">
                    <tr>
                        <td class="charges-header charge-label">PARTICULARS</td>
                        <td class="charges-header charge-amount">FREIGHT(PAID) IN Rs.</td>
                    </tr>
                    <tr>
                        <td class="charge-label">Freight</td>
                        <td class="charge-amount"><?php echo number_format($booking['freight'], 2); ?></td>
                    </tr>
                    <tr>
                        <td class="charge-label">Docket Charges</td>
                        <td class="charge-amount"><?php echo number_format($booking['docket_charges'], 2); ?></td>
                    </tr>
                    <tr>
                        <td class="charge-label">FOV</td>
                        <td class="charge-amount"><?php echo number_format($booking['fov'], 2); ?></td>
                    </tr>
                    <tr>
                        <td class="charge-label">D.O.D/C.O.D. Charges</td>
                        <td class="charge-amount"><?php echo number_format($booking['dod_cod_charges'], 2); ?></td>
                    </tr>
                    <tr>
                        <td class="charge-label">ODA/Misc. Charges</td>
                        <td class="charge-amount"><?php echo number_format($booking['oda_misc_charges'], 2); ?></td>
                    </tr>
                    <tr>
                        <td class="charge-label">SUB TOTAL</td>
                        <td class="charge-amount"><?php echo number_format($booking['freight'] + $booking['docket_charges'] + $booking['fov'] + $booking['dod_cod_charges'] + $booking['oda_misc_charges'], 2); ?></td>
                    </tr>
                    <tr>
                        <td class="charge-label">Service Tax/GST</td>
                        <td class="charge-amount"><?php echo number_format($booking['gst_service_tax'], 2); ?></td>
                    </tr>
                    <tr class="grand-total-row">
                        <td class="charge-label">GRAND TOTAL</td>
                        <td class="charge-amount"><?php echo number_format($booking['grand_total'], 2); ?></td>
                    </tr>
                </table>
            </td>
            <td class="col-right date-time-container"rowspan="3">
                
                <div class="date-time-field">
                    <div class="field-label">Time:</div>
                    <div class="field-line"></div>
                </div>
                <div class="signature-field">
                    <div class="field-label">Receiver Seal & Signature</div>
                </div>
            </td>
        </tr>
        
        <!-- Row 6: Agreement | COD/DOD | Empty -->
        <tr class="row-6">
            <td class="col-left">
                <div style="font-size: 6px;">I/We hereby agree to the Terms & Conditions Set for this ways bill.</div>
            </td>
            <td class="col-center cod-dod-text">
                COD/DOD Rs. <?php echo $booking['cod_dod_amount'] > 0 ? '₹' . number_format($booking['cod_dod_amount'], 2) : ''; ?>
            </td>
          
        </tr>
        
        <tr class="signature-row">
    <td class="col-left signature-cell" style="height: 60px; vertical-align: top;">
        Consignor Signature
    </td>
    <td class="col-center signature-cell" style="height: 60px; vertical-align: top;text-align:center;">
        Booking Clerk : <?php echo htmlspecialchars($booking['booking_clerk_name'] ?? ''); ?>
    </td>
</tr>

    </table>
</body>
</html>