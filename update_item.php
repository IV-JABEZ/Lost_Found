<?php
include 'includes/db.php';

if(isset($_POST['id'])){

    $id = $_POST['id'];
    $name = $_POST['item_name'];
    $date = $_POST['date_found'];
    $location = $_POST['location'];
    $type = $_POST['lost_found'];
    $status = $_POST['status'];

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

    header("Location: index.php");
}
?>