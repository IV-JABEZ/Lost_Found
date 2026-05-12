<?php
include 'includes/db.php';

$q = $_GET['q'];

$stmt = $conn->prepare("SELECT * FROM items WHERE item_name LIKE ?");
$search = "%$q%";
$stmt->bind_param("s",$search);
$stmt->execute();

$result = $stmt->get_result();
?>

<h2>Search Results</h2>

<table border="1">
<tr><th>Image</th><th>Name</th><th>Status</th></tr>

<?php while($row = $result->fetch_assoc()) { ?>
<tr>
<td><img src="<?= $row['image'] ?>" width="80"></td>
<td><?= $row['item_name'] ?></td>
<td><?= $row['status'] ?></td>
</tr>
<?php } ?>

</table>

<a href="dashboard.php">Back</a>