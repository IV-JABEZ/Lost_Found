<?php include 'includes/db.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found Items</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 30px;
            text-align: center;
        }

        h2 {
            font-size: 2.5em;
            margin-bottom: 15px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        a[href="add_item.php"] {
            display: inline-block;
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 50px;
            font-weight: bold;
            font-size: 1.1em;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(231, 76, 60, 0.4);
        }

        a[href="add_item.php"]:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(231, 76, 60, 0.6);
        }

        .table-container {
            overflow-x: auto;
            margin: 30px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 15px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            table-layout: fixed;
        }

        tr:first-child {
            background: linear-gradient(135deg, #34495e, #2c3e50) !important;
            color: white !important;
        }

        th {
            padding: 20px 15px;
            text-align: left;
            font-weight: 600;
            font-size: 0.95em;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: white !important;
        }

        td {
            padding: 20px 15px;
            border-bottom: 1px solid #ecf0f1;
            vertical-align: middle;
            word-wrap: break-word;
        }

        tr:hover {
            background: #f8f9fa !important;
            transform: scale(1.01);
            transition: all 0.3s ease;
        }

        tr:nth-child(even) {
            background: #f8f9fa;
        }

        img {
            width: 80px !important;
            height: 80px !important;
            object-fit: cover;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            transition: transform 0.3s ease;
            display: block;
        }

        img:hover {
            transform: scale(1.1);
        }

        .action-icons {
            display: flex;
            gap: 8px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 42px;
            height: 42px;
            border-radius: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            box-shadow: 0 3px 10px rgba(0,0,0,0.2);
            font-size: 1.1em;
        }

        .edit-btn {
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
        }

        .edit-btn:hover {
            background: linear-gradient(135deg, #2980b9, #1f618d);
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 6px 20px rgba(52, 152, 219, 0.4);
        }

        .delete-btn {
            background: linear-gradient(135deg, #e74c3c, #c0392b);
            color: white;
        }

        .delete-btn:hover {
            background: linear-gradient(135deg, #c0392b, #a93226);
            transform: translateY(-3px) scale(1.1);
            box-shadow: 0 6px 20px rgba(231, 76, 60, 0.4);
        }

        @media (max-width: 768px) {
            .container {
                margin: 10px;
                border-radius: 15px;
            }

            .header {
                padding: 25px 20px;
            }

            h2 {
                font-size: 2em;
            }

            th, td {
                padding: 15px 10px;
                font-size: 0.9em;
            }

            .action-btn {
                width: 38px;
                height: 38px;
                font-size: 1em;
            }
        }

        .no-items {
            text-align: center;
            padding: 60px 20px;
            color: #7f8c8d;
            font-size: 1.2em;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-search"></i> Lost & Found Items</h2>
            <a href="add_item.php"><i class="fas fa-plus"></i> </a>
        </div>

        <div class="table-container">
            <table border="1" cellpadding="10">
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Date Found/Lost</th>
                <th>Location</th>
                <th>Lost/Found</th>
                <th>Claim Status</th>
                <th>Action</th>
            </tr>

            <?php
            $result = mysqli_query($conn, "SELECT * FROM items ORDER BY id DESC");

            if(mysqli_num_rows($result) > 0){
                while($row = mysqli_fetch_assoc($result)):
            ?>

            <tr>
                <td><strong>#<?= $row['id'] ?></strong></td>

                <td>
                    <img src="<?= $row['image'] ?>" alt="Item image">
                </td>

                <td><strong><?= $row['item_name'] ?></strong></td>
                <td><?= date('M j, Y', strtotime($row['date_found'])) ?></td>
                <td><?= $row['location'] ?></td>

                <td><?= $row['lost_found'] ?></td>

                <!-- CLAIM STATUS (THIS IS CORRECT) -->
                <td><strong><?= $row['status'] ?></strong></td>

                <td>
                    <div class="action-icons">
                        <a href="edit_item.php?id=<?= $row['id'] ?>" class="action-btn edit-btn" title="Edit">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="delete_item.php?id=<?= $row['id'] ?>" class="action-btn delete-btn" onclick="return confirm('Delete?')" title="Delete">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>
            </tr>

            <?php endwhile;
            } else { ?>
                <tr>
                    <td colspan="8" class="no-items">
                        <i class="fas fa-inbox" style="font-size:4em;color:#bdc3c7;margin-bottom:20px;"></i><br>
                        No items found
                    </td>
                </tr>
            <?php } ?>

            </table>
        </div>
    </div>
</body>
</html>