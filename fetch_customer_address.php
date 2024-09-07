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

// Check if an AJAX request was made
if (isset($_POST['customer_name'])) {
    $customer_name = $_POST['customer_name'];
    
    // Prepare and execute SQL query to fetch customer address based on name
    $stmt = $conn->prepare("SELECT customer_address FROM invoices WHERE customer_name = ? LIMIT 1");
    $stmt->bind_param('s', $customer_name);
    $stmt->execute();
    $result = $stmt->get_result();

    // If an address is found, return it
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        echo htmlspecialchars($row['customer_address']);
    } else {
        echo ''; // No address found for this customer
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
