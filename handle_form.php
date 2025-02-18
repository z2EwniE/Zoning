<?php
// Handle Form Submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Check for duplicate certification number
    $check_sql = "SELECT * FROM zoning_certifications WHERE certification_number = ?";
    $stmt = $conn->prepare($check_sql);
    $stmt->bind_param("s", $certification_number);
    $stmt->execute();
    $check_result = $stmt->get_result();

    if ($check_result->num_rows > 0) {
        echo "<div class='alert alert-danger'>Certification number already exists. Please enter a unique certification number.</div>";
    } else {
        // Insert data into database, including total_sqm
        $sql = "INSERT INTO zoning_certifications 
        (owner, address, municipality, barangay, province, certification_number, zoning_classifications, 
        selected_lot, lot_no, oct_tct_td_no, area_for_title, receipt_no, date_issued, 
        zoning_certification, documentary_stamp, total_amount, 
        area_residential, area_commercial, area_agricultural, area_institutional, total_sqm)
        VALUES 
        (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssssssssssssssssd", $owner, $address, $municipality, $barangay, $province, $certification_number, $zoning_classifications, 
                        $selected_lot, $lot_no, $oct_tct_td_no, $area_for_title, $receipt_no, $date_issued, 
                        $zoning_certification, $documentary_stamp, $total_amount, 
                        $area_residential, $area_commercial, $area_agricultural, $area_institutional, $total_sqm);

        if ($stmt->execute() === TRUE) {
            echo "<div class='alert alert-success'>Record saved successfully!</div>";
        } else {
            echo "<div class='alert alert-danger'>Error: " . $stmt->error . "</div>";
        }
    }
}
?>
