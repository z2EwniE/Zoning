<!-- filepath: /c:/xampp/htdocs/zoning/edit.php -->
<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "zoning_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch record to edit
$id = $_GET['id'] ?? '';
if ($id) {
    $stmt = $conn->prepare("SELECT * FROM zoning_certifications WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $record = $stmt->get_result()->fetch_assoc();
}

// Handle Form Submission for Edit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST['id'];
    $owner = $_POST['owner'];
    $address = $_POST['address'];
    $municipality = "Bangar"; // Fixed value
    $barangay = $_POST['barangay'];
    $province = "La Union"; // Fixed value
    $certification_number = $_POST['certification_number'];

    $zoning_classifications = isset($_POST['zoning']) ? implode(", ", $_POST['zoning']) : "";
    $selected_lot = isset($_POST['selected_lot']) ? $_POST['selected_lot'] : null;
    $lot_no = $_POST['lot_no'] ?? '';
    $oct_tct_td_no = $_POST['oct_tct_td_no'] ?? '';
    $area_for_title = $_POST['area_for_title'] ?? null;

    $receipt_no = $_POST['receipt_no'] ?? '';
    $date_issued = $_POST['date_issued'];
    $zoning_certification = $_POST['zoning_certification'];
    $documentary_stamp = $_POST['documentary_stamp'];
    $total_amount = $_POST['total_amount'];

    $area_residential = isset($_POST['area_residential']) ? $_POST['area_residential'] : 0;
    $area_commercial = isset($_POST['area_commercial']) ? $_POST['area_commercial'] : 0;
    $area_agricultural = isset($_POST['area_agricultural']) ? $_POST['area_agricultural'] : 0;
    $area_institutional = isset($_POST['area_institutional']) ? $_POST['area_institutional'] : 0;

    // Compute total square meters (total_sqm)
    $total_sqm = $area_residential + $area_commercial + $area_agricultural + $area_institutional;

    // Update data in database
    $sql = "UPDATE zoning_certifications SET 
            owner = ?, address = ?, municipality = ?, barangay = ?, province = ?, certification_number = ?, 
            zoning_classifications = ?, selected_lot = ?, lot_no = ?, oct_tct_td_no = ?, area_for_title = ?, 
            receipt_no = ?, date_issued = ?, zoning_certification = ?, documentary_stamp = ?, total_amount = ?, 
            area_residential = ?, area_commercial = ?, area_agricultural = ?, area_institutional = ?, total_sqm = ? 
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssssssssssssssssdsi", $owner, $address, $municipality, $barangay, $province, $certification_number, $zoning_classifications, 
                    $selected_lot, $lot_no, $oct_tct_td_no, $area_for_title, $receipt_no, $date_issued, 
                    $zoning_certification, $documentary_stamp, $total_amount, 
                    $area_residential, $area_commercial, $area_agricultural, $area_institutional, $total_sqm, $id);

    if ($stmt->execute() === TRUE) {
        echo "<div class='alert alert-success'>Record updated successfully!</div>";
        header("Location: index.php"); // Redirect to index page
        exit();
    } else {
        echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Zoning Certification</title>
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
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Edit Zoning Certification</h2>
    <form method="post" action="">
        <input type="hidden" name="id" value="<?= htmlspecialchars($record['id']) ?>">
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Registered Owner:</label>
                    <input type="text" class="form-control" name="owner" value="<?= htmlspecialchars($record['owner']) ?>" required>
                </div>

                <div class="form-group">
                    <label>Owner Address:</label>
                    <input type="text" class="form-control" name="address" value="<?= htmlspecialchars($record['address']) ?>" required>
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
                        <option value="Agdeppa" <?= $record['barangay'] == 'Agdeppa' ? 'selected' : '' ?>>Agdeppa</option>
                        <option value="Alzate" <?= $record['barangay'] == 'Alzate' ? 'selected' : '' ?>>Alzate</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Province:</label>
                    <input type="text" class="form-control" name="province" value="La Union" readonly>
                </div>

                <div class="form-group">
                    <label>Certification Number:</label>
                    <input type="number" class="form-control" name="certification_number" value="<?= htmlspecialchars($record['certification_number']) ?>" required pattern="\d{4}-\d{4}-\d{4}" title="Format: 0000-0000-0000">
                </div>

                <div class="col-md-0">
                <div class="form-group">
                    <label><b>Zoning Classification:</b></label><br>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="zoning[]" value="Residential" <?= strpos($record['zoning_classifications'], 'Residential') !== false ? 'checked' : '' ?> onclick="toggleInput('residential')">
                        <label class="form-check-label">Residential</label>
                        <input type="number" class="form-control" id="residential" name="area_residential" min="0" step="1" value="<?= htmlspecialchars($record['area_residential']) ?>" <?= strpos($record['zoning_classifications'], 'Residential') === false ? 'disabled' : '' ?> oninput="calculateTotal()">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="zoning[]" value="Commercial" <?= strpos($record['zoning_classifications'], 'Commercial') !== false ? 'checked' : '' ?> onclick="toggleInput('commercial')">
                        <label class="form-check-label">Commercial</label>
                        <input type="number" class="form-control" id="commercial" name="area_commercial" min="0" step="1" value="<?= htmlspecialchars($record['area_commercial']) ?>" <?= strpos($record['zoning_classifications'], 'Commercial') === false ? 'disabled' : '' ?> oninput="calculateTotal()">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="zoning[]" value="Agricultural" <?= strpos($record['zoning_classifications'], 'Agricultural') !== false ? 'checked' : '' ?> onclick="toggleInput('agricultural')">
                        <label class="form-check-label">Agricultural</label>
                        <input type="number" class="form-control" id="agricultural" name="area_agricultural" min="0" step="1" value="<?= htmlspecialchars($record['area_agricultural']) ?>" <?= strpos($record['zoning_classifications'], 'Agricultural') === false ? 'disabled' : '' ?> oninput="calculateTotal()">
                    </div>

                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" name="zoning[]" value="Institutional" <?= strpos($record['zoning_classifications'], 'Institutional') !== false ? 'checked' : '' ?> onclick="toggleInput('institutional')">
                        <label class="form-check-label">Institutional</label>
                        <input type="number" class="form-control" id="institutional" name="area_institutional" min="0" step="1" value="<?= htmlspecialchars($record['area_institutional']) ?>" <?= strpos($record['zoning_classifications'], 'Institutional') === false ? 'disabled' : '' ?> oninput="calculateTotal()">
                    </div>
                    </div>

                    <div class="form-group mt-4">
                        <label>Total Area (sq.m):</label>
                        <input type="number" class="form-control" id="total_sqm" name="total_sqm" value="<?= htmlspecialchars($record['total_sqm']) ?>" readonly>
                    </div>
                </div>
            </div>
            
            <div class="col-md-2">
                <div class="form-group">
                    <label>Select Lot No. / Cadastral Lot No.:</label>
                    <select name="selected_lot" class="form-control" required>
                        <option value="lot no" <?= $record['selected_lot'] == 'lot no' ? 'selected' : '' ?>>Lot no.</option>
                        <option value="Cadastral" <?= $record['selected_lot'] == 'Cadastral' ? 'selected' : '' ?>>Cadastral</option>
                    </select>
                </div>
            </div>

            <div class="col-md-3">
                <h3>FEES</h3>
                <div class="form-group">
                    <label>Date Issued:</label>
                    <input type="date" class="form-control" name="date_issued" value="<?= htmlspecialchars($record['date_issued']) ?>">
                </div>

                <div class="form-group">
                    <label>Zoning Certification (Php):</label>
                    <input type="number" class="form-control" id="zoning_certification" name="zoning_certification" step="0.01" value="<?= htmlspecialchars($record['zoning_certification']) ?>">
                </div>

                <div class="form-group">
                    <label>Documentary Stamp (Php):</label>
                    <input type="number" class="form-control" id="documentary_stamp" name="documentary_stamp" step="0.01" value="<?= htmlspecialchars($record['documentary_stamp']) ?>">
                </div>

                <div class="form-group">
                    <label>Total (Php):</label>
                    <input type="number" class="form-control" id="total_amount" name="total_amount" step="0.01" value="<?= htmlspecialchars($record['total_amount']) ?>" readonly>
                </div>

                <button type="button" class="btn btn-primary" onclick="computeTotal()">Compute</button>
                <button type="submit" class="btn btn-success mt-0">Update</button>
                <a href="index.php" class="btn btn-secondary mt-0">Cancel</a>
            </div>
        </div>
    </form>
</div>

<script src="asset/js/script.js"></script>
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="asset/js/custom.js"></script> <!-- New script file -->

<footer class="text-center mt-5">
    <h1>melongthegads</h1>
</footer>
</body>
</html>