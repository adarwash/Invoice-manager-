<?php
require 'vendor/autoload.php'; // Composer autoload for FPDF

// Database configuration
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = "@1SpinningM00n"; // Replace with your MySQL password
$dbname = "invoice_db"; // Replace with your database name

// Create a connection to the MySQL database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the database connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Retrieve form data
$customer_name = $_POST['customer_name'] ?? null;
$customer_address = $_POST['customer_address'] ?? null;
$invoice_date = $_POST['invoice_date'] ?? null;
$quantities = $_POST['quantity'] ?? [];
$descriptions = $_POST['description'] ?? [];
$unit_prices = $_POST['unit_price'] ?? [];
$apply_vat = isset($_POST['apply_vat']) ? true : false; // Check if VAT should be applied

// Validate customer address
if (empty($customer_address)) {
    die('Customer address is required');
}

// Generate a unique invoice number
$invoice_number = uniqid('INV-');

// Prepare items and calculate total amount
$itemsArray = [];
$totalAmount = 0;

for ($i = 0; $i < count($quantities); $i++) {
    $qty = $quantities[$i];
    $description = $descriptions[$i];
    $unit_price = $unit_prices[$i];
    $total = $qty * $unit_price;
    $totalAmount += $total;

    $itemsArray[] = [
        'qty' => $qty,
        'description' => $description,
        'unit_price' => $unit_price,
        'total' => $total
    ];
}

// Calculate VAT if applied
$vatRate = 0.20;
$vatAmount = 0;
if ($apply_vat) {
    $vatAmount = $totalAmount * $vatRate;
}

// Insert the invoice data into the MySQL database
$items_json = json_encode($itemsArray); // Store items as JSON in the database
$sql = "INSERT INTO invoices (customer_name, customer_address, invoice_date, invoice_number, items, total_amount) VALUES (?, ?, ?, ?, ?, ?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param('sssssd', $customer_name, $customer_address, $invoice_date, $invoice_number, $items_json, $totalAmount);

// Suppress any output before PDF generation
ob_start();

if ($stmt->execute()) {
    // Invoice saved successfully
} else {
    // Handle database insertion error (but no echo or output)
    die("Error saving the invoice: " . $stmt->error);
}

// Close the statement and connection
$stmt->close();
$conn->close();

// Create an instance of FPDF class
$pdf = new FPDF();
$pdf->AddPage();

// Set font for the invoice
$pdf->SetFont('Arial', 'B', 14);

// Add a title
$pdf->Cell(0, 10, utf8_decode('Invoice'), 0, 1, 'C');

// Add some space
$pdf->Ln(10);

// Invoice information
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, utf8_decode('From: Your Company Name'), 0, 0);
$pdf->Cell(90, 10, utf8_decode('To: ' . $customer_name), 0, 1);

$pdf->Cell(100, 10, utf8_decode('Your Address Line 1'), 0, 0);

// Use MultiCell to properly display the customer address with reduced line height
$pdf->SetFont('Arial', '', 12);
$pdf->MultiCell(90, 5, utf8_decode($customer_address), 0, 'L'); // Adjusted line height to 5

$pdf->Ln(10); // Line break

// Invoice date and number
// Invoice date and number
$formatted_invoice_date = date('d/m/Y', strtotime($invoice_date)); // Convert date to DD/MM/YYYY format
$pdf->Cell(100, 10, utf8_decode('Invoice Date: ' . $formatted_invoice_date), 0, 0);
$pdf->Cell(90, 10, utf8_decode('Invoice No: ' . $invoice_number), 0, 1);

$pdf->Cell(90, 10, utf8_decode('Invoice No: ' . $invoice_number), 0, 1);

$pdf->Ln(10); // Line break

// Table header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(10, 10, utf8_decode('Qty'), 1);
$pdf->Cell(100, 10, utf8_decode('Description'), 1);
$pdf->Cell(40, 10, utf8_decode('Unit Price'), 1);
$pdf->Cell(40, 10, utf8_decode('Total'), 1);
$pdf->Ln(10); // Line break

// Table content (loop through items)
$pdf->SetFont('Arial', '', 12);
foreach ($itemsArray as $item) {
    $pdf->Cell(10, 10, utf8_decode($item['qty']), 1);
    $pdf->Cell(100, 10, utf8_decode($item['description']), 1);
    $pdf->Cell(40, 10, utf8_decode('£' . number_format($item['unit_price'], 2)), 1);
    $pdf->Cell(40, 10, utf8_decode('£' . number_format($item['total'], 2)), 1);
    $pdf->Ln(10); // Line break for each row
}

// Footer with VAT and total amount
$pdf->SetFont('Arial', 'B', 12);

// Subtotal
$pdf->Cell(150, 10, utf8_decode('Subtotal'), 1);
$pdf->Cell(40, 10, utf8_decode('£' . number_format($totalAmount, 2)), 1);
$pdf->Ln(10); // Line break

// VAT if applied
if ($apply_vat) {
    $pdf->Cell(150, 10, utf8_decode('VAT (20%)'), 1);
    $pdf->Cell(40, 10, utf8_decode('£' . number_format($vatAmount, 2)), 1);
    $pdf->Ln(10); // Line break after VAT

    // Total including VAT
    $totalWithVAT = $totalAmount + $vatAmount;
    $pdf->Cell(150, 10, utf8_decode('Total (incl. VAT)'), 1);
    $pdf->Cell(40, 10, utf8_decode('£' . number_format($totalWithVAT, 2)), 1);
    $pdf->Ln(20); // Add more space after the total to avoid overlap
}

// Add signature or note section if needed
$pdf->SetFont('Arial', '', 10);
$pdf->Ln(10); // Add extra space before the thank you message
$pdf->Cell(0, 10, utf8_decode('Thank you for your business!'), 0, 1, 'C');

// Clear the buffer and send PDF to browser
ob_end_clean();
$pdf->Output('I', 'invoice.pdf'); // 'I' sends the PDF to the browser inline
?>
