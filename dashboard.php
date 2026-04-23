<?php
include 'includes/db.php';
session_start();

/* ===================== PAGINATION ===================== */
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;

$offset = ($page - 1) * $limit;

$totalResult = mysqli_query($conn, "SELECT COUNT(*) AS total FROM items");
$totalRow = mysqli_fetch_assoc($totalResult);
$totalItems = $totalRow['total'];

$totalPages = ceil($totalItems / $limit);

$result = mysqli_query($conn, "SELECT * FROM items ORDER BY id ASC LIMIT $limit OFFSET $offset");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700&family=Inter:wght@300;400;600&display=swap');

*{
    margin:0;
    padding:0;
    box-sizing:border-box;
    font-family:Inter;
}

body{
    background:#0a0f2c;
    color:white;
    min-height:100vh;
    padding:30px;
}

/* GRID BACKGROUND */
body::before{
    content:"";
    position:fixed;
    width:200%;
    height:200%;
    background:
        linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px),
        linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px);
    background-size:50px 50px;
    animation: move 20s linear infinite;
    z-index:-1;
}

@keyframes move{
    0%{transform:translate(0,0);}
    100%{transform:translate(-50px,-50px);}
}

/* TITLE */
.title{
    text-align:center;
    font-family:Orbitron;
    color:#00e5ff;
    font-size:28px;
    margin-bottom:25px;
    letter-spacing:2px;
}

/* TABLE CONTAINER */
.container{
    max-width:1200px;
    margin:auto;
    background:rgba(255,255,255,0.06);
    backdrop-filter:blur(12px);
    border:1px solid rgba(0,229,255,0.2);
    border-radius:15px;
    padding:20px;
    box-shadow:0 0 25px rgba(0,229,255,0.15);
}

/* TABLE */
table{
    width:100%;
    border-collapse:collapse;
}

th{
    background:#00e5ff;
    color:black;
    padding:12px;
    font-family:Orbitron;
}

td{
    padding:12px;
    border-bottom:1px solid rgba(255,255,255,0.1);
}

tr:hover{
    background:rgba(0,229,255,0.08);
}

/* IMAGE */
img{
    width:60px;
    height:60px;
    object-fit:cover;
    border-radius:10px;
}

/* ACTION BUTTONS */
.actions{
    display:flex;
    gap:10px;
}

.icon-btn{
    width:38px;
    height:38px;
    display:flex;
    align-items:center;
    justify-content:center;
    border-radius:8px;
    color:white;
    text-decoration:none;
    transition:0.3s;
}

.icon-btn:hover{
    transform:scale(1.1);
}

.editBtn{background:#00e5ff;color:black;}
.deleteBtn{background:#ff4d4d;}

/* PAGINATION */
.pagination{
    text-align:center;
    margin-top:20px;
}

.pagination a{
    display:inline-block;
    padding:8px 12px;
    margin:3px;
    border-radius:6px;
    border:1px solid #00e5ff;
    color:#00e5ff;
    text-decoration:none;
    transition:0.3s;
}

.pagination a:hover{
    background:#00e5ff;
    color:black;
}

.pagination a.active{
    background:#00e5ff;
    color:black;
    font-weight:bold;
}

/* LOGOUT */
.logout{
    text-align:center;
    margin-top:25px;
}

.logout a{
    display:inline-block;
    padding:10px 18px;
    background:#ff3b3b;
    color:white;
    text-decoration:none;
    border-radius:8px;
    transition:0.3s;
}

.logout a:hover{
    transform:scale(1.05);
    background:#ff0000;
}

/* MODAL */
.modal{
    display:none;
    position:fixed;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background:rgba(0,0,0,0.8);
}

.modal-content{
    width:400px;
    margin:8% auto;
    background:rgba(255,255,255,0.08);
    backdrop-filter:blur(15px);
    padding:25px;
    border-radius:12px;
    border:1px solid rgba(0,229,255,0.2);
}

input, select{
    width:100%;
    padding:12px;
    margin:10px 0;
    border:none;
    border-radius:8px;
    background:rgba(255,255,255,0.1);
    color:white;
}

button{
    width:100%;
    padding:12px;
    background:#00e5ff;
    border:none;
    border-radius:8px;
    font-weight:bold;
    cursor:pointer;
}

.close{
    float:right;
    cursor:pointer;
    color:#00e5ff;
}
</style>

</head>

<body>

<div class="title">LOST & FOUND INVENTORY SYSTEM</div>

<div class="container">

    <table>
        <tr>
            <th>ID</th>
            <th>Image</th>
            <th>Name</th>
            <th>Date</th>
            <th>Location</th>
            <th>Type</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while($row = mysqli_fetch_assoc($result)) { ?>

        <tr>
            <td>#<?= $row['id'] ?></td>
            <td><img src="<?= $row['image'] ?>"></td>
            <td><?= $row['item_name'] ?></td>
            <td><?= $row['date_found'] ?></td>
            <td><?= $row['location'] ?></td>
            <td><?= $row['lost_found'] ?></td>
            <td><?= $row['status'] ?></td>
            <td>
                <div class="actions">
                    <a href="#" class="icon-btn editBtn"><i class="fas fa-edit"></i></a>
                    <a href="delete_item.php?id=<?= $row['id'] ?>" class="icon-btn deleteBtn"><i class="fas fa-trash"></i></a>
                </div>
            </td>
        </tr>

        <?php } ?>

    </table>

    <!-- PAGINATION -->
    <div class="pagination">
        <?php if($page > 1): ?>
            <a href="?page=<?= $page-1 ?>">Prev</a>
        <?php endif; ?>

        <?php for($i=1;$i<=$totalPages;$i++): ?>
            <a class="<?= ($i==$page)?'active':'' ?>" href="?page=<?= $i ?>">
                <?= $i ?>
            </a>
        <?php endfor; ?>

        <?php if($page < $totalPages): ?>
            <a href="?page=<?= $page+1 ?>">Next</a>
        <?php endif; ?>
    </div>

    <!-- LOGOUT -->
    <div class="logout">
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>

</div>

</body>
</html>