<?php
include 'includes/db.php';

/* GET ITEM DATA */
if(isset($_GET['id'])){
    $id = $_GET['id'];
    $result = mysqli_query($conn,"SELECT * FROM items WHERE id=$id");
    $row = mysqli_fetch_assoc($result);
}

/* UPDATE ITEM */
if(isset($_POST['update'])){

    $id = $_POST['id'];
    $name = $_POST['item_name'];
    $date = $_POST['date_found'];
    $location = $_POST['location'];
    $type = $_POST['lost_found'];
    $status = $_POST['status'];

    // IMAGE UPDATE
    if($_FILES['image']['name']){
        $img = $_FILES['image']['name'];
        $tmp = $_FILES['image']['tmp_name'];

        $path = "uploads/" . time() . "_" . $img;
        move_uploaded_file($tmp,$path);

        mysqli_query($conn,"UPDATE items SET
            item_name='$name',
            date_found='$date',
            location='$location',
            lost_found='$type',
            status='$status',
            image='$path'
            WHERE id=$id");
    } else {
        mysqli_query($conn,"UPDATE items SET
            item_name='$name',
            date_found='$date',
            location='$location',
            lost_found='$type',
            status='$status'
            WHERE id=$id");
    }

    header("Location: dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Item</title>

<style>
body{
    background:#0a0f2c;
    color:white;
    font-family:Arial;
    display:flex;
    justify-content:center;
    align-items:center;
    height:100vh;
}

.box{
    width:400px;
    background:rgba(255,255,255,0.1);
    padding:25px;
    border-radius:10px;
}

input, select{
    width:100%;
    padding:10px;
    margin:8px 0;
}

button{
    width:100%;
    padding:10px;
    background:#00e5ff;
    border:none;
}
img{
    width:100%;
    margin-top:10px;
}
</style>

</head>

<body>

<div class="box">

<h2>Edit Item</h2>

<form method="POST" enctype="multipart/form-data">

<input type="hidden" name="id" value="<?= $row['id'] ?>">

<input type="text" name="item_name" value="<?= $row['item_name'] ?>" required>

<input type="date" name="date_found" value="<?= $row['date_found'] ?>" required>

<input type="text" name="location" value="<?= $row['location'] ?>" required>

<select name="lost_found">
<option <?= $row['lost_found']=="Lost"?"selected":"" ?>>Lost</option>
<option <?= $row['lost_found']=="Found"?"selected":"" ?>>Found</option>
</select>

<select name="status">
<option <?= $row['status']=="Unclaimed"?"selected":"" ?>>Unclaimed</option>
<option <?= $row['status']=="Claimed"?"selected":"" ?>>Claimed</option>
</select>

<!-- CURRENT IMAGE -->
<img src="<?= $row['image'] ?>">

<input type="file" name="image">

<button name="update">Update Item</button>

</form>

</div>

</body>
</html>