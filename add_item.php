<?php
include 'includes/db.php';

if (isset($_POST['submit'])) {

    $name = $_POST['item_name'];
    $date = $_POST['date_found'];
    $location = $_POST['location'];
    $lost_found = $_POST['lost_found'];
    $status = $_POST['status'];

    // IMAGE UPLOAD FIX
    $image = $_FILES['item_image']['name'];
    $tmp = $_FILES['item_image']['tmp_name'];

    move_uploaded_file($tmp, "uploads/" . $image);

    $image = "uploads/" . $image;

    // CORRECT INSERT QUERY
    $query = "INSERT INTO items (item_name, date_found, location, image, lost_found, status)
              VALUES ('$name', '$date', '$location', '$image', '$lost_found', '$status')";

    mysqli_query($conn, $query);

    header("Location: index.php");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add New Item</title>
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
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .container {
            max-width: 600px;
            width: 100%;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 20px 40px rgba(0,0,0,0.1);
            overflow: hidden;
        }

        .header {
            background: linear-gradient(135deg, #2c3e50, #3498db);
            color: white;
            padding: 40px 30px;
            text-align: center;
        }

        h2 {
            font-size: 2.2em;
            margin-bottom: 10px;
            text-shadow: 2px 2px 4px rgba(0,0,0,0.3);
        }

        .form-container {
            padding: 40px 30px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 25px;
        }

        input[type="text"],
        input[type="date"],
        input[type="file"],
        select {
            width: 100%;
            padding: 18px 22px;
            border: 2px solid #e1e8ed;
            border-radius: 15px;
            font-size: 1.05em;
            background: #fafbfc;
            transition: all 0.3s ease;
            font-family: inherit;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        input[type="file"]:focus,
        select:focus {
            outline: none;
            border-color: #3498db;
            background: white;
            box-shadow: 0 0 0 4px rgba(52, 152, 219, 0.1);
            transform: translateY(-2px);
        }

        select {
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='currentColor' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3e%3cpolyline points='6,9 12,15 18,9'%3e%3c/polyline%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 18px center;
            background-size: 22px;
            padding-right: 55px;
            cursor: pointer;
            appearance: none;
        }

        button[type="submit"] {
            background: linear-gradient(135deg, #27ae60, #2ecc71);
            color: white;
            padding: 20px;
            border: none;
            border-radius: 15px;
            font-size: 1.2em;
            font-weight: bold;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 5px 15px rgba(46, 204, 113, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        button[type="submit"]:hover {
            background: linear-gradient(135deg, #229954, #27ae60);
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(46, 204, 113, 0.6);
        }

        button[type="submit"]:active {
            transform: translateY(-1px);
        }

        .back-link {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            color: white;
            text-decoration: none;
            background: linear-gradient(135deg, #95a5a6, #bdc3c7);
            padding: 15px 25px;
            border-radius: 15px;
            margin-top: 20px;
            transition: all 0.3s ease;
            font-weight: 600;
            box-shadow: 0 5px 15px rgba(149, 165, 166, 0.4);
        }

        .back-link:hover {
            background: linear-gradient(135deg, #7f8c8d, #95a5a6);
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(149, 165, 166, 0.6);
        }

        @media (max-width: 768px) {
            body {
                padding: 10px;
            }
            
            .container {
                margin: 10px;
                border-radius: 15px;
            }
            
            .header {
                padding: 30px 20px;
            }
            
            h2 {
                font-size: 1.8em;
            }
            
            .form-container {
                padding: 30px 20px;
            }
            
            input, select, button {
                padding: 16px 18px;
                font-size: 1em;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2><i class="fas fa-plus-circle"></i> Add Item</h2>
        </div>
        
        <div class="form-container">
            <form method="POST" enctype="multipart/form-data">
                <input type="text" name="item_name" placeholder="Item Name" required>
                
                <input type="date" name="date_found" required>
                
                <input type="text" name="location" placeholder="Location" required>
                
                <select name="lost_found">
                    <option value="Lost">Lost</option>
                    <option value="Found">Found</option>
                </select>
                
                <select name="status">
                    <option value="Unclaimed">Unclaimed</option>
                    <option value="Claimed">Claimed</option>
                </select>
                
                <input type="file" name="item_image" accept="image/*" required>
                
                <button type="submit" name="submit">
                    <i class="fas fa-save"></i> Save
                </button>
            </form>
            
            <a href="index.php" class="back-link">
                <i class="fas fa-arrow-left"></i> Back to List
            </a>
        </div>
    </div>
</body>
</html>