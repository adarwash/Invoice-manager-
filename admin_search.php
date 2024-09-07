<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = "@1SpinningM00n"; // Replace with your MySQL password
$dbname = "invoice_db"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$searchResults = [];
$searchTerm = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the search term from the admin
    $searchTerm = $_POST['customer_name'];

    // Prepare the SQL query to search for the customer name
    $stmt = $conn->prepare("SELECT * FROM invoices WHERE customer_name LIKE ?");
    $searchParam = "%" . $searchTerm . "%"; // Adding wildcard characters for partial match
    $stmt->bind_param('s', $searchParam);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch all results
    $searchResults = $result->fetch_all(MYSQLI_ASSOC);

    // Close the statement
    $stmt->close();
}

// Close the connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin: Search for Customer Invoice</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Search for Customer Invoice</h1>
        <form action="admin_search.php" method="POST">
            <div class="mb-3">
                <label for="customer_name" class="form-label">Customer Name</label>
                <input type="text" id="customer_name" name="customer_name" class="form-control" value="<?php echo htmlspecialchars($searchTerm); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <!-- Display Search Results -->
        <?php if (!empty($searchResults)): ?>
            <h2 class="mt-5">Search Results</h2>
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Invoice Number</th>
                        <th>Customer Name</th>
                        <th>Invoice Date</th>
                        <th>Total Amount</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($searchResults as $invoice): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($invoice['invoice_number']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['customer_name']); ?></td>
                        <td><?php echo htmlspecialchars($invoice['invoice_date']); ?></td>
                        <td>£<?php echo number_format($invoice['total_amount'], 2); ?></td>
                        <td>
                            <a href="regenerate_invoice.php?invoice_number=<?php echo htmlspecialchars($invoice['invoice_number']); ?>" class="btn btn-info btn-sm">View PDF</a> <!-- Button to regenerate PDF -->
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>
            <p class="mt-5 text-danger">No invoices found for the customer "<?php echo htmlspecialchars($searchTerm); ?>"</p>
        <?php endif; ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
