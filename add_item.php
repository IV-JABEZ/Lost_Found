<?php
include 'includes/db.php';

if(isset($_POST['item_name'])){

    $name = $_POST['item_name'];
    $date = $_POST['date_found'];
    $location = $_POST['location'];
    $type = $_POST['lost_found'];
    $status = $_POST['status'];

    $imageName = $_FILES['image']['name'];
    $tmp = $_FILES['image']['tmp_name'];

    $path = "uploads/" . time() . "_" . $imageName;
    move_uploaded_file($tmp, $path);

    $stmt = $conn->prepare("INSERT INTO items 
    (item_name,date_found,location,lost_found,status,image)
    VALUES(?,?,?,?,?,?)");

    $stmt->bind_param("ssssss",$name,$date,$location,$type,$status,$path);
    $stmt->execute();

    header("Location: dashboard.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Add Item</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
*{
    margin:0;
    padding:0;
    box-sizing:border-box;
}

body{
    font-family:'Segoe UI',sans-serif;
    background:linear-gradient(135deg,#667eea,#764ba2);
    height:100vh;
    display:flex;
    justify-content:center;
    align-items:center;
}

/* CARD */
.card{
    width:420px;
    background:white;
    padding:30px;
    border-radius:20px;
    box-shadow:0 20px 40px rgba(0,0,0,0.2);
    animation:slideUp 0.6s ease;
}

/* TITLE */
.card h2{
    text-align:center;
    margin-bottom:20px;
    color:#2c3e50;
}

/* INPUTS */
.form-group{
    margin-bottom:15px;
}

input,select{
    width:100%;
    padding:12px;
    border-radius:10px;
    border:1px solid #ddd;
    transition:0.3s;
}

input:focus,select:focus{
    border-color:#3498db;
    box-shadow:0 0 8px rgba(52,152,219,0.4);
}

/* BUTTON */
button{
    width:100%;
    padding:12px;
    border:none;
    border-radius:10px;
    background:linear-gradient(135deg,#3498db,#2980b9);
    color:white;
    font-weight:bold;
    cursor:pointer;
    transition:0.3s;
}

button:hover{
    transform:translateY(-2px);
    box-shadow:0 10px 20px rgba(0,0,0,0.2);
}

/* BACK LINK */
.back{
    display:block;
    text-align:center;
    margin-top:15px;
    color:#555;
    text-decoration:none;
}

/* IMAGE PREVIEW */
.preview{
    width:100%;
    height:150px;
    border:2px dashed #ccc;
    border-radius:10px;
    display:flex;
    justify-content:center;
    align-items:center;
    overflow:hidden;
    margin-bottom:10px;
}

.preview img{
    width:100%;
    height:100%;
    object-fit:cover;
}

/* ANIMATION */
@keyframes slideUp{
    from{
        opacity:0;
        transform:translateY(40px);
    }
    to{
        opacity:1;
        transform:translateY(0);
    }
}
</style>
</head>

<body>

<div class="card">
    <h2><i class="fas fa-plus-circle"></i> Add Item</h2>

    <form method="POST" enctype="multipart/form-data">

        <div class="form-group">
            <input type="text" name="item_name" placeholder="Item Name" required>
        </div>

        <div class="form-group">
            <input type="date" name="date_found" required>
        </div>

        <div class="form-group">
            <input type="text" name="location" placeholder="Location" required>
        </div>

        <div class="form-group">
            <select name="lost_found" required>
                <option value="">Lost or Found?</option>
                <option>Lost</option>
                <option>Found</option>
            </select>
        </div>

        <div class="form-group">
            <select name="status" required>
                <option>Unclaimed</option>
                <option>Claimed</option>
            </select>
        </div>

        <!-- IMAGE PREVIEW -->
        <div class="preview" id="preview">
            <span>Select Image</span>
        </div>

        <div class="form-group">
            <input type="file" name="image" id="imageInput" required>
        </div>

        <button type="submit">Save Item</button>
    </form>

    <a href="index.php" class="back">← Back</a>
</div>

<script>
// IMAGE PREVIEW
const input = document.getElementById("imageInput");
const preview = document.getElementById("preview");

input.addEventListener("change", function(){
    const file = this.files[0];
    if(file){
        const reader = new FileReader();
        reader.onload = function(e){
            preview.innerHTML = `<img src="${e.target.result}">`;
        }
        reader.readAsDataURL(file);
    }
});
</script>

</body>
</html>