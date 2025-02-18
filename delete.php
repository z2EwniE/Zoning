<!-- filepath: /c:/xampp/htdocs/zoning/delete.php -->
<?php
// Database Connection
$conn = new mysqli("localhost", "root", "", "zoning_db");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get the record ID
$id = intval($_GET['id']); // Ensure the ID is an integer

// Delete the record
$sql = "DELETE FROM zoning_certifications WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('Record deleted successfully!'); window.location.href='index.php';</script>";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>