<?php include 'includes/db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Lost & Found</title>

<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

<!-- CSS PATH UPDATED -->
<link rel="stylesheet" href="assets/style.css">
</head>

<body>

<div class="container">

    <div class="header">
        <h2>Lost & Found Items</h2>
        <a href="#" id="openModal"><i class="fas fa-plus"></i></a>
    </div>

    <div class="table-container">
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

            <?php
            $result = mysqli_query($conn, "SELECT * FROM items ORDER BY id DESC");

            while($row = mysqli_fetch_assoc($result)){
            ?>

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

                        <a href="#" class="icon-btn editBtn"
                            data-id="<?= $row['id'] ?>"
                            data-name="<?= $row['item_name'] ?>"
                            data-date="<?= $row['date_found'] ?>"
                            data-location="<?= $row['location'] ?>"
                            data-type="<?= $row['lost_found'] ?>"
                            data-status="<?= $row['status'] ?>"
                        >
                            <i class="fas fa-edit"></i>
                        </a>

                        <a href="delete_item.php?id=<?= $row['id'] ?>" class="icon-btn deleteBtn">
                            <i class="fas fa-trash"></i>
                        </a>

                    </div>
                </td>
            </tr>

            <?php } ?>
        </table>
    </div>
</div>

<!-- MODAL -->
<div id="modal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>

        <h3 id="modalTitle">Add Item</h3>

        <form id="itemForm" action="add_item.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="id" id="item_id">

            <input type="text" name="item_name" id="item_name" placeholder="Item Name" required>

            <input type="date" name="date_found" id="date_found" required>

            <input type="text" name="location" id="location" placeholder="Location" required>

            <select name="lost_found" id="lost_found" required>
                <option value="">Select Type</option>
                <option value="Lost">Lost</option>
                <option value="Found">Found</option>
            </select>

            <select name="status" id="status" required>
                <option value="Unclaimed">Unclaimed</option>
                <option value="Claimed">Claimed</option>
            </select>

            <input type="file" name="image">

            <button type="submit" id="submitBtn">Save</button>

        </form>
    </div>
</div>

<script>
const modal = document.getElementById("modal");
const form = document.getElementById("itemForm");

const item_id = document.getElementById("item_id");
const item_name = document.getElementById("item_name");
const date_found = document.getElementById("date_found");
const locationInput = document.getElementById("location");
const lost_found = document.getElementById("lost_found");
const status = document.getElementById("status");
const modalTitle = document.getElementById("modalTitle");
const submitBtn = document.getElementById("submitBtn");

// ADD
document.getElementById("openModal").onclick = e => {
    e.preventDefault();

    form.reset();
    item_id.value = "";

    form.action = "add_item.php";
    modalTitle.innerText = "Add Item";
    submitBtn.innerText = "Save";

    modal.style.display = "block";
};

// EDIT
document.querySelectorAll(".editBtn").forEach(btn => {
    btn.onclick = function(e) {
        e.preventDefault();

        modal.style.display = "block";

        item_id.value = this.dataset.id;
        item_name.value = this.dataset.name;
        date_found.value = this.dataset.date;
        locationInput.value = this.dataset.location;
        lost_found.value = this.dataset.type;
        status.value = this.dataset.status;

        form.action = "update_item.php";
        modalTitle.innerText = "Edit Item";
        submitBtn.innerText = "Update";
    };
});

// CLOSE
document.querySelector(".close").onclick = () => {
    modal.style.display = "none";
};

window.onclick = e => {
    if (e.target == modal) {
        modal.style.display = "none";
    }
};
</script>

</body>
</html>