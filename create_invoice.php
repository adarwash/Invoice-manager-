<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Invoice</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Include jQuery for AJAX functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        .suggestion-list {
            border: 1px solid #ccc;
            max-height: 150px;
            overflow-y: auto;
            position: absolute;
            z-index: 1000;
            background-color: white;
        }
        .suggestion-list div {
            padding: 8px;
            cursor: pointer;
        }
        .suggestion-list div:hover {
            background-color: #f0f0f0;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center mb-4">Create Invoice</h1>
        <form action="generate_invoice.php" method="post" id="invoiceForm">
            <!-- Customer Name with Suggestions -->
            <div class="row mb-3 position-relative">
                <div class="col-md-6">
                    <label for="customer_name" class="form-label">Customer Name</label>
                    <input type="text" id="customer_name" name="customer_name" class="form-control" autocomplete="off" required>
                    <div id="suggestions" class="suggestion-list"></div>
                </div>
                <div class="col-md-6">
                    <label for="invoice_date" class="form-label">Invoice Date</label>
                    <input type="date" id="invoice_date" name="invoice_date" class="form-control" required>
                </div>
            </div>

            <!-- Customer Address Field -->
            <div class="mb-3">
                <label for="customer_address" class="form-label">Customer Address</label>
                <textarea id="customer_address" name="customer_address" rows="3" class="form-control" required></textarea>
            </div>

            <!-- VAT Checkbox -->
            <div class="form-check mb-3">
                <input class="form-check-input" type="checkbox" id="apply_vat" name="apply_vat">
                <label class="form-check-label" for="apply_vat">Apply 20% VAT</label>
            </div>

            <!-- Items Section -->
            <div class="mb-3">
                <label class="form-label">Items</label>
                <div id="itemsContainer">
                    <div class="row mb-2 item-row">
                        <div class="col-md-2">
                            <input type="number" name="quantity[]" class="form-control" placeholder="Qty" required>
                        </div>
                        <div class="col-md-6 position-relative">
                            <input type="text" name="description[]" class="form-control description-input" placeholder="Description" required autocomplete="off">
                            <div class="suggestion-list description-suggestions"></div>
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" name="unit_price[]" class="form-control" placeholder="Unit Price" required>
                        </div>
                        <div class="col-md-1">
                            <span class="remove-btn btn btn-danger">&times;</span>
                        </div>
                    </div>
                </div>
                <button type="button" id="addItemBtn" class="btn btn-success">Add Item</button>
            </div>

            <!-- Submit Button -->
            <button type="submit" class="btn btn-primary">Generate Invoice</button>
        </form>
    </div>

    <!-- JavaScript to handle customer name suggestions, address auto-fill, VAT checkbox, and dynamic item rows -->
    <script>
        $(document).ready(function() {
            // Event listener for customer name input field
            $('#customer_name').on('input', function() {
                var query = $(this).val();
                if (query.length > 1) {
                    $.ajax({
                        url: 'fetch_customers.php', // The PHP file that fetches customer names
                        method: 'POST',
                        data: {query: query},
                        success: function(data) {
                            $('#suggestions').html(data);
                        }
                    });
                } else {
                    $('#suggestions').html('');
                }
            });

            // Populate the input field when a suggestion is clicked and auto-fill the customer address
            $(document).on('click', '.suggestion-item', function() {
                var customerName = $(this).text();
                $('#customer_name').val(customerName);
                $('#suggestions').html('');

                // Fetch the customer address for the selected customer
                $.ajax({
                    url: 'fetch_customer_address.php', // The PHP file that fetches customer address
                    method: 'POST',
                    data: {customer_name: customerName},
                    success: function(address) {
                        $('#customer_address').val(address); // Auto-fill the address field
                    }
                });
            });

            // Add new item rows dynamically
            $('#addItemBtn').on('click', function() {
                const newRow = `
                    <div class="row mb-2 item-row">
                        <div class="col-md-2">
                            <input type="number" name="quantity[]" class="form-control" placeholder="Qty" required>
                        </div>
                        <div class="col-md-6 position-relative">
                            <input type="text" name="description[]" class="form-control description-input" placeholder="Description" required autocomplete="off">
                            <div class="suggestion-list description-suggestions"></div>
                        </div>
                        <div class="col-md-3">
                            <input type="number" step="0.01" name="unit_price[]" class="form-control" placeholder="Unit Price" required>
                        </div>
                        <div class="col-md-1">
                            <span class="remove-btn btn btn-danger">&times;</span>
                        </div>
                    </div>`;
                $('#itemsContainer').append(newRow);
            });

            // Remove an item row when the delete button is clicked
            $(document).on('click', '.remove-btn', function() {
                $(this).closest('.item-row').remove();
            });

            // Description auto-suggestion for car parts
            $(document).on('input', '.description-input', function() {
                var query = $(this).val();
                var descriptionField = $(this); // Reference to the input field
                if (query.length > 1) {
                    $.ajax({
                        url: 'fetch_car_parts.php', // The PHP file that fetches car parts
                        method: 'POST',
                        data: {query: query},
                        success: function(data) {
                            descriptionField.siblings('.description-suggestions').html(data);
                        }
                    });
                } else {
                    descriptionField.siblings('.description-suggestions').html('');
                }
            });

            // Populate the description field when a car part is clicked
            $(document).on('click', '.description-suggestion-item', function() {
                var partName = $(this).text();
                $(this).closest('.description-suggestions').siblings('.description-input').val(partName);
                $(this).closest('.description-suggestions').html('');
            });
        });
    </script>
</body>
</html>
