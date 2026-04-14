<?php
include 'includes/db.php';

$id = $_GET['id'];

// Get image path first
$result = mysqli_query($conn, "SELECT image FROM items WHERE id=$id");
$row = mysqli_fetch_assoc($result);

// Delete image file if it exists
if (!empty($row['image']) && file_exists($row['image'])) {
    unlink($row['image']);
}

// Delete record from database
mysqli_query($conn, "DELETE FROM items WHERE id=$id");

header("Location: index.php");
exit();
?>