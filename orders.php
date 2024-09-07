<?php
// Database connection
$servername = "localhost";
$username = "root"; // Replace with your MySQL username
$password = "@1SpinningM00n"; // Replace with your MySQL password
$dbname = "invoice_db"; // Replace with your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get filter values from the form
$part_description = isset($_GET['part_description']) ? $_GET['part_description'] : '';
$start_date = isset($_GET['start_date']) ? $_GET['start_date'] : '';
$end_date = isset($_GET['end_date']) ? $_GET['end_date'] : '';
$min_cost = isset($_GET['min_cost']) ? $_GET['min_cost'] : '';
$max_cost = isset($_GET['max_cost']) ? $_GET['max_cost'] : '';

// Build the SQL query with filters
$sql = "SELECT customer_name, invoice_number, invoice_date, items FROM invoices WHERE 1=1";

// Add date range filter
if ($start_date && $end_date) {
    $sql .= " AND invoice_date BETWEEN '$start_date' AND '$end_date'";
}

// Execute query
$result = $conn->query($sql);

$orderedParts = [];
$totalParts = 0;
$totalCost = 0;

if ($result->num_rows > 0) {
    // Process each invoice
    while ($row = $result->fetch_assoc()) {
        $customer_name = $row['customer_name'];
        $invoice_number = $row['invoice_number'];
        $invoice_date = date('d/m/Y', strtotime($row['invoice_date'])); // Format the date as DD/MM/YYYY
        $items_json = $row['items'];
        
        // Decode the items JSON to get part details
        $items = json_decode($items_json, true);
        
        // Loop through each item and store the relevant details
        foreach ($items as $item) {
            // Apply part description filter
            if ($part_description && stripos($item['description'], $part_description) === false) {
                continue;
            }

            // Apply cost filter
            if ($min_cost && $item['total'] < $min_cost) {
                continue;
            }
            if ($max_cost && $item['total'] > $max_cost) {
                continue;
            }

            $orderedParts[] = [
                'customer_name' => $customer_name,
                'invoice_number' => $invoice_number,
                'invoice_date' => $invoice_date,
                'part_description' => $item['description'],
                'quantity' => $item['qty'],
                'unit_price' => $item['unit_price'],
                'total' => $item['total']
            ];

            // Add to total parts and total cost
            $totalParts += $item['qty'];
            $totalCost += $item['total'];
        }
    }
}

// Close the database connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Orders</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">All Customer Orders</h1>

        <!-- Filter Form -->
        <form method="GET" action="orders.php" class="mb-4">
            <div class="row">
                <!-- Part Description Filter -->
                <div class="col-md-3">
                    <label for="part_description" class="form-label">Part Description</label>
                    <input type="text" id="part_description" name="part_description" class="form-control" value="<?php echo htmlspecialchars($part_description); ?>">
                </div>

                <!-- Date Range Filter -->
                <div class="col-md-3">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" id="start_date" name="start_date" class="form-control" value="<?php echo htmlspecialchars($start_date); ?>">
                </div>
                <div class="col-md-3">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" id="end_date" name="end_date" class="form-control" value="<?php echo htmlspecialchars($end_date); ?>">
                </div>

                <!-- Cost Filter -->
                <div class="col-md-3">
                    <label for="min_cost" class="form-label">Min Cost (£)</label>
                    <input type="number" step="0.01" id="min_cost" name="min_cost" class="form-control" value="<?php echo htmlspecialchars($min_cost); ?>">
                </div>
                <div class="col-md-3">
                    <label for="max_cost" class="form-label">Max Cost (£)</label>
                    <input type="number" step="0.01" id="max_cost" name="max_cost" class="form-control" value="<?php echo htmlspecialchars($max_cost); ?>">
                </div>
            </div>

            <div class="mt-3">
                <button type="submit" class="btn btn-primary">Filter Results</button>
                <a href="orders.php" class="btn btn-secondary">Clear Filters</a>
            </div>
        </form>

        <!-- Table to display ordered parts -->
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Customer Name</th>
                    <th>Invoice Number</th>
                    <th>Date</th>
                    <th>Part Description</th>
                    <th>Quantity</th>
                    <th>Unit Price (£)</th>
                    <th>Total (£)</th>
                </tr>
            </thead>
            <tbody>
                <?php if (count($orderedParts) > 0): ?>
                    <?php foreach ($orderedParts as $part): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($part['customer_name']); ?></td>
                            <td><?php echo htmlspecialchars($part['invoice_number']); ?></td>
                            <td><?php echo htmlspecialchars($part['invoice_date']); ?></td>
                            <td><?php echo htmlspecialchars($part['part_description']); ?></td>
                            <td><?php echo htmlspecialchars($part['quantity']); ?></td>
                            <td>£<?php echo number_format($part['unit_price'], 2); ?></td>
                            <td>£<?php echo number_format($part['total'], 2); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">No parts found with the selected filters.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- Display total numbers of parts and total cost -->
        <div class="mt-4">
            <h4>Total Number of Parts Ordered: <?php echo $totalParts; ?></h4>
            <h4>Total Cost of All Parts: £<?php echo number_format($totalCost, 2); ?></h4>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
