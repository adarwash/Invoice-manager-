<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to the Invoice Management System</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <div class="container-fluid">
            <a class="navbar-brand" href="index.php">Invoice Management</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php">View All Parts Orders</a> <!-- Link to orders.php -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="admin_search.php">View Customer Orders</a> <!-- Link to admin_search.php -->
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="create_invoice.php">Create Invoice</a> <!-- Link to create_invoice.php -->
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container mt-5">
        <h1 class="text-center">Welcome to the Invoice Management System</h1>
        <p class="text-center">Please use the navigation bar to manage invoices and view orders.</p>

        <div class="row mt-4">
            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">View All Parts Orders</h5>
                        <p class="card-text">View all parts that have been ordered, with filtering options.</p>
                        <a href="orders.php" class="btn btn-primary">Go to Orders</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">View Past Customer Orders</h5>
                        <p class="card-text">Search and view past orders made by specific customers.</p>
                        <a href="admin_search.php" class="btn btn-primary">View Customer Orders</a>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-body">
                        <h5 class="card-title">Create a New Invoice</h5>
                        <p class="card-text">Generate new invoices for customers, add parts, and calculate total costs including VAT.</p>
                        <a href="create_invoice.php" class="btn btn-primary">Create Invoice</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.min.js"></script>
</body>
</html>
