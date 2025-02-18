<?php
// Database Connection
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "zoning_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Include form handling script
include 'handle_form.php';

// Include fetch records script
include 'fetch_records.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Zoning Certification System</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="asset/style/style.css">
    <style>
        .logo-container {
            position: absolute;
            left: 50px;
            top: 50px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: calc(100% - 100px);
            box-sizing: border-box;
        }
        .logo {
            width: 100px;
            height: auto;
        }
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }
        .records-container {
            max-height: 400px; /* Adjust the height as needed */
            overflow-y: auto;
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>ZONING CERTIFICATION</h2>
    <form method="post" action="">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Registered Owner:</label>
                    <input type="text" class="form-control" name="owner" placeholder="Last Name, First Name, Middle Name" required>
                </div>

                <div class="form-group">
                    <label>Owner Address:</label>
                    <input type="text" class="form-control" name="address" required>
                </div>

                <div class="form-group">
                    <h5><b>LOCATION OF PROPERTY</b></h5>
                </div>

                <div class="form-group">
                    <label>Municipality:</label>
                    <input type="text" class="form-control" name="municipality" value="Bangar" readonly>
                </div>

                <div class="form-group">
                    <label>Barangay:</label>
                    <select name="barangay" class="form-control" required>
                        <option value="">Select a Barangay</option>
                        <option value="Agdeppa">Agdeppa</option>
                        <option value="Alzate">Alzate</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Province:</label>
                    <input type="text" class="form-control" name="province" value="La Union" readonly>
                </div>

                <div class="form-group">
                    <label>Certification Number:</label>
                    <input type="number" class="form-control" name="certification_number" placeholder="0000-0000-0000" required pattern="\d{4}-\d{4}-\d{4}" title="Format: 0000-0000-0000">
                </div>

                <div class="col-md-0">
                <div class="form-group">
                    <label><b>Zoning Classification:</b></label><br>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="zoning[]" value="Residential" onclick="toggleInput('residential')">
                        <label class="form-check-label">Residential</label>
                        <input type="number" class="form-control" id="residential" name="area_residential" min="0" step="1" disabled oninput="calculateTotal()">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="zoning[]" value="Commercial" onclick="toggleInput('commercial')">
                        <label class="form-check-label">Commercial</label>
                        <input type="number" class="form-control" id="commercial" name="area_commercial" min="0" step="1" disabled oninput="calculateTotal()">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="zoning[]" value="Agricultural" onclick="toggleInput('agricultural')">
                        <label class="form-check-label">Agricultural</label>
                        <input type="number" class="form-control" id="agricultural" name="area_agricultural" min="0" step="1" disabled oninput="calculateTotal()">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="zoning[]" value="Institutional" onclick="toggleInput('institutional')">
                        <label class="form-check-label">Institutional</label>
                        <input type="number" class="form-control" id="institutional" name="area_institutional" min="0" step="1" disabled oninput="calculateTotal()">
                    </div>
                    </div>

                    <div class="form-group mt-4">
                        <label>Total Area (sq.m):</label>
                        <input type="number" class="form-control" id="total_sqm" name="total_sqm" readonly>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group">
                    <label>Select Lot No. / Cadastral Lot No.:</label>
                    <select name="selected_lot" class="form-control" required>
                        <option value="lot no">Lot no.</option>
                        <option value="Cadastral">Cadastral</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <h3>FEES</h3>
                <div class="form-group">
                    <label>Date Issued:</label>
                    <input type="date" class="form-control" name="date_issued">
                </div>

                <div class="form-group">
                    <label>Zoning Certification (Php):</label>
                    <input type="number" class="form-control" id="zoning_certification" name="zoning_certification" step="0.01">
                </div>

                <div class="form-group">
                    <label>Documentary Stamp (Php):</label>
                    <input type="number" class="form-control" id="documentary_stamp" name="documentary_stamp" step="0.01">
                </div>

                <div class="form-group">
                    <label>Total (Php):</label>
                    <input type="number" class="form-control" id="total_amount" name="total_amount" step="0.01" readonly>
                </div>

                <button type="button" class="btn btn-primary" onclick="computeTotal()">Compute</button>
                <button type="submit" class="btn btn-success mt-0">Save</button>
                <button type="button" class="btn btn-secondary mt-0 no-print" onclick="printRecord()">Print</button>
            </div>
        </div>
    </form>
</div>
    <div class="container mt-5">
        <h2>CLIENT RECORDS</h2>
        <form method="GET" class="form-inline mb-3">
            <input type="text" class="form-control mr-2" name="search" placeholder="Search by Owner">
            <input type="number" class="form-control mr-2" name="year" placeholder="Year">
            <input type="number" class="form-control mr-2" name="month" placeholder="Month">
            <button type="submit" class="btn btn-primary">Search</button>
        </form>

        <div class="records-container">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Owner</th>
                        <th>Municipality</th>
                        <th>Barangay</th>
                        <th>Province</th>
                        <th>Certification Number</th>
                        <th>Zoning Classifications</th>
                        <th>Date Issued</th>
                        <th>Selected Lot</th>
                        <th>Total Sqm</th>
                        <th>Total Amount</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) : ?>
                    <tr>
                        <td><?= htmlspecialchars($row['owner']) ?></td>
                        <td><?= htmlspecialchars($row['municipality']) ?></td>
                        <td><?= htmlspecialchars($row['barangay']) ?></td>
                        <td><?= htmlspecialchars($row['province']) ?></td>
                        <td><?= htmlspecialchars($row['certification_number']) ?></td>
                        <td><?= htmlspecialchars($row['zoning_classifications']) ?></td>
                        <td><?= htmlspecialchars($row['date_issued']) ?></td>
                        <td><?= htmlspecialchars($row['selected_lot']) ?></td>
                        <td><?= htmlspecialchars($row['total_sqm']) ?></td>
                        <td><?= htmlspecialchars($row['total_amount']) ?></td>
                        <td>
                            <a href="#" class="btn btn-warning btn-sm" onclick="showPasswordModal('edit', <?= $row['id'] ?>)">Edit</a>
                            <a href="#" class="btn btn-danger btn-sm" onclick="showPasswordModal('delete', <?= $row['id'] ?>)">Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Password Modal -->
    <div id="passwordModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Enter Password</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <input type="password" id="modalPassword" class="form-control" placeholder="Password">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" onclick="verifyPassword()">Submit</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Printable Area -->
<div id="printable" style="display:none;">
    <div class="print-container">
        <div class="header" style="text-align: center;">
            <div class="logo-container">
                <img src="asset/img/bangar logo.png" alt="Bangar Logo" class="logo">
                <img src="asset/img/work with joy logo.png" alt="Work with Joy Logo" class="logo">
            </div>
            <p>Republic of the Philippines<br>
            Province of La Union<br>
            MUNICIPALITY OF BANGAR<br>
            <strong>OFFICE OF THE MUNICIPAL PLANNING & DEVELOPMENT COORDINATOR</strong></p>
        </div>

        <div class="content">
            <p><span class="label">Date:</span> <span id="print_date"></span></p>
            <p><span class="label">Name:</span> <span id="print_name"></span></p>
            <p><span class="label">Location:</span> <span id="print_location"></span> Bangar, La Union</p>
            <p><span class="label">Area:</span> <span id="print_area"></span> sq.m.</p> <!-- Area is dynamically updated here -->
            <p><span class="label">Requested by:</span> <span id="print_requested"></span></p>

            <p class="section-title">ZONING CERTIFICATION</p>
            <p><span class="label">Zoning Certification Fee:</span> Php <span id="print_zoning_fee"></span></p>
            <p><span class="label">Documentary Stamp:</span> Php <span id="print_stamp_fee"></span></p>
            <p><span class="label">Total:</span> Php <span id="print_total"></span></p>
        </div>

        <div class="footer" style="margin-top: 40px;">
            <p><strong>Glynnis S. Casuga, EnP</strong><br>MPDC/Zoning Officer</p>
        </div>
    </div>
</div>

<script src="asset/js/script.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<footer class="text-center mt-5">
    <h1>melongthegads</h1>
</footer>
</body>
</html>
