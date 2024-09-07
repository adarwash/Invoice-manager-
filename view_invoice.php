<?php
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

// Fetch invoice details
$invoice_number = $_GET['invoice_number'];
$stmt = $conn->prepare("SELECT * FROM invoices WHERE invoice_number = ?");
$stmt->bind_param('s', $invoice_number);
$stmt->execute();
$invoice = $stmt->get_result()->fetch_assoc();

// Close the statement and connection
$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Invoice Details</h1>

        <?php if ($invoice): ?>
            <table class="table table-bordered">
                <tr>
                    <th>Invoice Number</th>
                    <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                </tr>
                <tr>
                    <th>Customer Name</th>
                    <td><?php echo htmlspecialchars($invoice['customer_name']); ?></td>
                </tr>
                <tr>
                    <th>Invoice Date</th>
                    <td><?php echo htmlspecialchars($invoice['invoice_date']); ?></td>
                </tr>
                <tr>
                    <th>Total Amount</th>
                    <td>£<?php echo number_format($invoice['total_amount'], 2); ?></td>
                </tr>
                <tr>
                    <th>Items</th>
                    <td>
                        <?php
                        // Decode the JSON data for items
                        $items = json_decode($invoice['items'], true);
                        if ($items) {
                            echo '<ul>';
                            foreach ($items as $item) {
                                echo '<li>' . htmlspecialchars($item['qty']) . ' x ' . htmlspecialchars($item['description']) . ' @ £' . number_format($item['unit_price'], 2) . ' = £' . number_format($item['total'], 2) . '</li>';
                            }
                            echo '</ul>';
                        }
                        ?>
                    </td>
                </tr>
            </table>
        <?php else: ?>
            <p class="text-danger">Invoice not found.</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
