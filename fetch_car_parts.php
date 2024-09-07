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
if (isset($_POST['query'])) {
    $query = $_POST['query'];
    
    // Prepare and execute SQL query to fetch car parts matching the input
    $stmt = $conn->prepare("SELECT part_name FROM car_parts WHERE part_name LIKE ? LIMIT 10");
    $search = "%" . $query . "%"; // Add wildcard for partial matching
    $stmt->bind_param('s', $search);
    $stmt->execute();
    $result = $stmt->get_result();

    // Output matching car parts
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo '<div class="description-suggestion-item">' . htmlspecialchars($row['part_name']) . '</div>';
        }
    } else {
        echo '<div class="description-suggestion-item">No matching parts found</div>';
    }

    // Close the statement and connection
    $stmt->close();
    $conn->close();
}
?>
