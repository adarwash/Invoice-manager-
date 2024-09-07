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

// Get the invoice number from the URL
$invoice_number = $_GET['invoice_number'];

// Fetch the invoice details from the database
$stmt = $conn->prepare("SELECT * FROM invoices WHERE invoice_number = ?");
$stmt->bind_param('s', $invoice_number);
$stmt->execute();
$result = $stmt->get_result();
$invoice = $result->fetch_assoc();

// Decode the items JSON
$itemsArray = json_decode($invoice['items'], true);

// Close the database connection
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
$pdf->Cell(90, 10, utf8_decode('To: ' . $invoice['customer_name']), 0, 1);

$pdf->Cell(100, 10, utf8_decode('Your Address Line 1'), 0, 0);
$pdf->Cell(90, 10, utf8_decode($invoice['customer_address']), 0, 1);

$pdf->Ln(10); // Line break

// Invoice date and number
$pdf->Cell(100, 10, utf8_decode('Invoice Date: ' . $invoice['invoice_date']), 0, 0);
$pdf->Cell(90, 10, utf8_decode('Invoice No: ' . $invoice['invoice_number']), 0, 1);

$pdf->Ln(10); // Line break

// Table header
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(10, 10, utf8_decode('Qty'), 1);
$pdf->Cell(100, 10, utf8_decode('Description'), 1);
$pdf->Cell(40, 10, utf8_decode('Unit Price (£)'), 1);
$pdf->Cell(40, 10, utf8_decode('Total (£)'), 1);
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

// Footer with total amount
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(150, 10, utf8_decode('Total'), 1);
$pdf->Cell(40, 10, utf8_decode('£' . number_format($invoice['total_amount'], 2)), 1);

$pdf->Ln(20); // Line break

// Add signature or note section if needed
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 10, utf8_decode('Thank you for your business!'), 0, 1, 'C');

// Output the PDF to browser
$pdf->Output('I', 'invoice.pdf'); // 'I' sends the PDF to the browser inline
?>
