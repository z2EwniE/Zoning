<?php
// Fetch records
$search = $_GET['search'] ?? '';
$year = $_GET['year'] ?? '';
$month = $_GET['month'] ?? '';

$query = "SELECT * FROM zoning_certifications WHERE 1";

if ($search) {
    $query .= " AND owner LIKE ?";
    $search = "%$search%";
}
if ($year) {
    $query .= " AND YEAR(date_issued) = ?";
}
if ($month) {
    $query .= " AND MONTH(date_issued) = ?";
}

$stmt = $conn->prepare($query);

if ($search && $year && $month) {
    $stmt->bind_param("sss", $search, $year, $month);
} elseif ($search && $year) {
    $stmt->bind_param("ss", $search, $year);
} elseif ($search && $month) {
    $stmt->bind_param("ss", $search, $month);
} elseif ($year && $month) {
    $stmt->bind_param("ss", $year, $month);
} elseif ($search) {
    $stmt->bind_param("s", $search);
} elseif ($year) {
    $stmt->bind_param("s", $year);
} elseif ($month) {
    $stmt->bind_param("s", $month);
}

$stmt->execute();
$result = $stmt->get_result();
?>
