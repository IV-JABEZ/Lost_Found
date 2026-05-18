<?php
include 'includes/db.php';
session_start();

/* ===================== HANDLE UPDATE ===================== */
if (isset($_POST['update'])) {

    $id       = (int)$_POST['id'];
    $name     = mysqli_real_escape_string($conn, $_POST['item_name']);
    $date     = mysqli_real_escape_string($conn, $_POST['date_found']);
    $location = mysqli_real_escape_string($conn, $_POST['location']);
    $type     = mysqli_real_escape_string($conn, $_POST['lost_found']);
    $status   = mysqli_real_escape_string($conn, $_POST['status']);

    if ($_FILES['image']['name']) {
        $img  = $_FILES['image']['name'];
        $tmp  = $_FILES['image']['tmp_name'];
        $path = "uploads/" . time() . "_" . $img;
        move_uploaded_file($tmp, $path);

        mysqli_query($conn, "UPDATE items SET
            item_name='$name', date_found='$date', location='$location',
            lost_found='$type', status='$status', image='$path'
            WHERE id=$id");
    } else {
        mysqli_query($conn, "UPDATE items SET
            item_name='$name', date_found='$date', location='$location',
            lost_found='$type', status='$status'
            WHERE id=$id");
    }

    $redirect = "dashboard.php";
    if (isset($_POST['filter']) && $_POST['filter'] !== '') {
        $redirect .= "?filter=" . urlencode($_POST['filter']);
    }
    header("Location: $redirect");
    exit();
}

/* ===================== STAT COUNTS ===================== */
$totalItems     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM items"))['c'];
$totalLost      = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM items WHERE lost_found='Lost'"))['c'];
$totalFound     = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM items WHERE lost_found='Found'"))['c'];
$totalClaimed   = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM items WHERE status='Claimed'"))['c'];
$totalUnclaimed = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM items WHERE status='Unclaimed'"))['c'];

/* ===================== ACTIVE FILTER ===================== */
$filter = isset($_GET['filter']) ? $_GET['filter'] : '';

$whereMap = [
    'lost'      => "WHERE lost_found='Lost'",
    'found'     => "WHERE lost_found='Found'",
    'claimed'   => "WHERE status='Claimed'",
    'unclaimed' => "WHERE status='Unclaimed'",
];
$whereClause = isset($whereMap[$filter]) ? $whereMap[$filter] : '';

/* ===================== PAGINATION ===================== */
$limit  = 5;
$page   = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

$filteredTotal = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS c FROM items $whereClause"))['c'];
$totalPages    = ceil($filteredTotal / $limit);

$result = mysqli_query($conn, "SELECT * FROM items $whereClause ORDER BY id ASC LIMIT $limit OFFSET $offset");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Dashboard</title>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
<style>
@import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;700&family=Segoe+UI:wght@300;400;600&display=swap');

* { margin:0; padding:0; box-sizing:border-box; font-family:'Segoe UI',sans-serif; }

body {
    min-height:100vh;
    color:#e6edf3;
    padding:30px;
    background:
        radial-gradient(circle at 20% 20%, rgba(0,255,255,0.08), transparent 40%),
        radial-gradient(circle at 80% 80%, rgba(0,150,255,0.08), transparent 40%),
        linear-gradient(rgba(0,0,0,0.65), rgba(0,0,0,0.65)),
        url('assets/img/ICAS.webp');
    background-size:cover;
    background-position:center;
    background-attachment:fixed;
}

/* TITLE */
.title {
    text-align:center;
    font-family:Orbitron,sans-serif;
    color:#38bdf8;
    font-size:1.6em;
    margin-bottom:25px;
    letter-spacing:2px;
    text-shadow:0 0 12px rgba(56,189,248,0.6);
}

/* ===== STAT CARDS ===== */
.stats-grid {
    display:flex;
    gap:14px;
    max-width:1200px;
    margin:0 auto 22px;
    flex-wrap:wrap;
}

.stat-card {
    flex:1;
    min-width:160px;
    background:rgba(15,23,42,0.85);
    backdrop-filter:blur(14px);
    border-radius:14px;
    padding:18px 20px;
    border:1px solid rgba(56,189,248,0.2);
    box-shadow:0 0 18px rgba(0,191,255,0.12);
    text-decoration:none;
    color:#e6edf3;
    transition:0.3s;
    display:block;
    text-align:center;
}

.stat-card:hover, .stat-card.active { transform:translateY(-4px); box-shadow:0 0 24px rgba(56,189,248,0.3); }
.stat-card .stat-icon { font-size:1.6em; margin-bottom:8px; display:block; }
.stat-card .stat-num  { font-family:Orbitron,sans-serif; font-size:2.2em; font-weight:700; line-height:1; margin-bottom:5px; }
.stat-card .stat-label{ font-size:0.72em; letter-spacing:1.5px; text-transform:uppercase; opacity:0.7; }

.stat-total  { border-color:rgba(56,189,248,0.25); }
.stat-total  .stat-num, .stat-total  .stat-icon { color:#38bdf8; }
.stat-total.active  { border-color:#38bdf8; background:rgba(56,189,248,0.1); }

.stat-lost   { border-color:rgba(248,113,113,0.25); }
.stat-lost   .stat-num, .stat-lost   .stat-icon { color:#f87171; }
.stat-lost.active   { border-color:#f87171; background:rgba(248,113,113,0.1); }

.stat-found  { border-color:rgba(74,222,128,0.25); }
.stat-found  .stat-num, .stat-found  .stat-icon { color:#4ade80; }
.stat-found.active  { border-color:#4ade80; background:rgba(74,222,128,0.1); }

.stat-claimed{ border-color:rgba(167,139,250,0.25); }
.stat-claimed .stat-num, .stat-claimed .stat-icon { color:#a78bfa; }
.stat-claimed.active{ border-color:#a78bfa; background:rgba(167,139,250,0.1); }

.stat-unclaim{ border-color:rgba(251,191,36,0.25); }
.stat-unclaim .stat-num, .stat-unclaim .stat-icon { color:#fbbf24; }
.stat-unclaim.active{ border-color:#fbbf24; background:rgba(251,191,36,0.1); }

/* FILTER BAR */
.filter-bar {
    max-width:1200px;
    margin:0 auto 14px;
    display:flex;
    align-items:center;
    gap:10px;
}
.filter-label { font-size:0.8em; color:#94a3b8; letter-spacing:1px; text-transform:uppercase; }
.filter-badge {
    background:rgba(56,189,248,0.12);
    border:1px solid rgba(56,189,248,0.4);
    color:#38bdf8;
    padding:4px 14px;
    border-radius:20px;
    font-size:0.8em;
    font-weight:600;
    letter-spacing:1px;
}
.clear-filter {
    color:#94a3b8;
    font-size:0.8em;
    text-decoration:none;
    padding:4px 10px;
    border-radius:6px;
    border:1px solid rgba(255,255,255,0.1);
    transition:0.2s;
}
.clear-filter:hover { color:#e6edf3; border-color:rgba(255,255,255,0.3); }

/* MAIN CONTAINER */
.container {
    max-width:1200px;
    margin:auto;
    background:rgba(15,23,42,0.85);
    backdrop-filter:blur(14px);
    border-radius:20px;
    border:1px solid rgba(56,189,248,0.2);
    box-shadow:0 0 25px rgba(0,191,255,0.25), 0 20px 50px rgba(0,0,0,0.5);
    overflow:hidden;
}

.header {
    position:relative;
    padding:18px 24px;
    background:linear-gradient(90deg,#0f172a,#1e293b);
    border-bottom:1px solid rgba(56,189,248,0.3);
    display:flex;
    align-items:center;
    justify-content:center;
}
.header h2 {
    font-family:Orbitron,sans-serif;
    color:#38bdf8;
    font-size:1.1em;
    letter-spacing:2px;
    text-shadow:0 0 8px rgba(56,189,248,0.5);
}

.add-btn {
    position:absolute;
    right:20px;
    width:42px; height:42px;
    background:#0ea5e9;
    color:white;
    display:flex; align-items:center; justify-content:center;
    border-radius:50%;
    text-decoration:none;
    font-size:1.2em;
    box-shadow:0 0 12px rgba(14,165,233,0.7);
    transition:0.3s;
}
.add-btn:hover {
    background:#38bdf8;
    box-shadow:0 0 20px rgba(56,189,248,0.8);
    transform:rotate(90deg) scale(1.1);
}

.table-container { padding:20px; }

table { width:100%; border-collapse:collapse; color:#e6edf3; }

th {
    background:#020617;
    color:#38bdf8;
    padding:12px;
    text-transform:uppercase;
    font-size:12px;
    letter-spacing:1px;
    font-family:Orbitron,sans-serif;
}
td { padding:12px; border-bottom:1px solid rgba(255,255,255,0.07); vertical-align:middle; }
tr:hover { background:rgba(56,189,248,0.06); }

.item-img {
    width:58px; height:58px;
    object-fit:cover;
    border-radius:10px;
    border:1px solid rgba(56,189,248,0.4);
    cursor:pointer;
    transition:0.3s;
}
.item-img:hover {
    border-color:#38bdf8;
    box-shadow:0 0 10px rgba(56,189,248,0.5);
    transform:scale(1.08);
}

.badge {
    display:inline-block;
    padding:4px 10px;
    border-radius:20px;
    font-size:12px;
    font-weight:600;
    letter-spacing:0.5px;
}
.badge-lost      { background:rgba(239,68,68,0.2);   color:#fca5a5; border:1px solid #ef4444; }
.badge-found     { background:rgba(34,197,94,0.2);   color:#86efac; border:1px solid #22c55e; }
.badge-claimed   { background:rgba(167,139,250,0.2); color:#c4b5fd; border:1px solid #a78bfa; }
.badge-unclaimed { background:rgba(251,191,36,0.2);  color:#fde68a; border:1px solid #fbbf24; }

.actions { display:flex; gap:8px; }
.icon-btn {
    width:36px; height:36px;
    display:flex; align-items:center; justify-content:center;
    border-radius:8px;
    color:white;
    text-decoration:none;
    font-size:14px;
    transition:0.3s;
    cursor:pointer;
    border:none;
}
.icon-btn:hover { transform:scale(1.15); }
.editBtn   { background:#0ea5e9; box-shadow:0 0 6px rgba(14,165,233,0.5); }
.deleteBtn { background:#ef4444; box-shadow:0 0 6px rgba(239,68,68,0.5); }

/* PAGINATION */
.pagination { text-align:center; padding:16px 0 8px; }
.pagination a {
    display:inline-block;
    padding:7px 13px;
    margin:3px;
    border-radius:6px;
    border:1px solid rgba(56,189,248,0.4);
    color:#38bdf8;
    text-decoration:none;
    font-size:13px;
    transition:0.3s;
}
.pagination a:hover, .pagination a.active {
    background:#0ea5e9; border-color:#0ea5e9; color:white;
    box-shadow:0 0 8px rgba(14,165,233,0.5);
}

/* LOGOUT */
.logout { text-align:center; padding:16px 0 20px; }
.logout a {
    display:inline-block;
    padding:10px 24px;
    background:#ef4444;
    color:white;
    text-decoration:none;
    border-radius:10px;
    font-weight:bold;
    font-size:13px;
    transition:0.3s;
    box-shadow:0 0 10px rgba(239,68,68,0.4);
}
.logout a:hover { background:#dc2626; transform:scale(1.05); }

/* NO RESULTS */
.no-results { text-align:center; padding:40px; color:#64748b; font-size:0.95em; }
.no-results i { font-size:2em; margin-bottom:10px; display:block; }

/* ===== MODALS ===== */
.modal {
    display:none;
    position:fixed;
    top:0; left:0;
    width:100%; height:100%;
    background:rgba(0,0,0,0.78);
    z-index:999;
    align-items:center;
    justify-content:center;
}
.modal.active { display:flex; }

.modal-content {
    background:#0f172a;
    color:#e6edf3;
    width:480px;
    max-width:95vw;
    padding:30px;
    border-radius:16px;
    border:1px solid rgba(56,189,248,0.3);
    box-shadow:0 0 30px rgba(56,189,248,0.2);
    position:relative;
    max-height:90vh;
    overflow-y:auto;
}

.modal-content h3 {
    font-family:Orbitron,sans-serif;
    color:#38bdf8;
    font-size:1em;
    letter-spacing:2px;
    margin-bottom:20px;
    padding-bottom:12px;
    border-bottom:1px solid rgba(56,189,248,0.2);
}

.close {
    position:absolute;
    top:14px; right:18px;
    font-size:22px;
    cursor:pointer;
    color:#38bdf8;
    line-height:1;
    background:none;
    border:none;
    width:auto;
    padding:0;
    margin:0;
    box-shadow:none;
    font-weight:normal;
}
.close:hover { color:#7dd3fc; box-shadow:none; background:none; }

/* FORM FIELDS */
.form-group {
    margin-bottom:14px;
}

.form-group label {
    display:block;
    font-size:0.78em;
    color:#94a3b8;
    letter-spacing:1px;
    text-transform:uppercase;
    margin-bottom:5px;
    font-weight:600;
}

.form-group input,
.form-group select {
    width:100%;
    padding:11px 14px;
    border-radius:8px;
    border:1px solid rgba(56,189,248,0.25);
    background:#020617;
    color:#e6edf3;
    font-size:0.92em;
    outline:none;
    transition:border-color 0.3s, box-shadow 0.3s;
    margin:0;
}

.form-group input:focus,
.form-group select:focus {
    border-color:#38bdf8;
    box-shadow:0 0 8px rgba(56,189,248,0.25);
}

.form-row {
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:14px;
}

/* CURRENT IMAGE PREVIEW */
.current-img-wrap {
    margin-bottom:14px;
}

.current-img-wrap label {
    display:block;
    font-size:0.78em;
    color:#94a3b8;
    letter-spacing:1px;
    text-transform:uppercase;
    margin-bottom:8px;
    font-weight:600;
}

.current-img-wrap img {
    width:100%;
    height:160px;
    object-fit:cover;
    border-radius:10px;
    border:1px solid rgba(56,189,248,0.3);
}

/* MODAL BUTTONS */
.modal-actions {
    display:flex;
    gap:10px;
    margin-top:20px;
}

.btn-update {
    flex:1;
    padding:12px;
    background:#0ea5e9;
    color:white;
    border:none;
    border-radius:8px;
    font-weight:bold;
    font-size:0.92em;
    cursor:pointer;
    transition:0.3s;
    letter-spacing:1px;
    width:auto;
    margin:0;
}
.btn-update:hover { background:#38bdf8; box-shadow:0 0 12px rgba(56,189,248,0.5); }

.btn-cancel {
    flex:1;
    padding:12px;
    background:rgba(255,255,255,0.07);
    color:#94a3b8;
    border:1px solid rgba(255,255,255,0.15);
    border-radius:8px;
    font-weight:bold;
    font-size:0.92em;
    cursor:pointer;
    transition:0.3s;
    letter-spacing:1px;
    width:auto;
    margin:0;
}
.btn-cancel:hover { background:rgba(255,255,255,0.12); color:#e6edf3; }

/* IMAGE PREVIEW MODAL */
#imgModal .modal-content {
    background:transparent;
    border:none;
    box-shadow:none;
    text-align:center;
    width:auto;
    max-width:90vw;
    padding:0;
}
#imgModal img {
    max-width:80vw;
    max-height:80vh;
    border-radius:14px;
    border:2px solid rgba(56,189,248,0.5);
    box-shadow:0 0 30px rgba(56,189,248,0.3);
    width:auto;
    height:auto;
}
#imgModal .close {
    position:static;
    display:block;
    margin-bottom:10px;
    font-size:28px;
    color:white;
    text-align:right;
}
</style>
</head>
<body>

<div class="title">LOST & FOUND INVENTORY SYSTEM</div>

<!-- STAT CARDS -->
<div class="stats-grid">
    <a href="?filter=" class="stat-card stat-total <?= $filter==='' ? 'active' : '' ?>">
        <span class="stat-icon"><i class="fas fa-layer-group"></i></span>
        <div class="stat-num"><?= $totalItems ?></div>
        <div class="stat-label">Total Items</div>
    </a>
    <a href="?filter=lost" class="stat-card stat-lost <?= $filter==='lost' ? 'active' : '' ?>">
        <span class="stat-icon"><i class="fas fa-search"></i></span>
        <div class="stat-num"><?= $totalLost ?></div>
        <div class="stat-label">Lost</div>
    </a>
    <a href="?filter=found" class="stat-card stat-found <?= $filter==='found' ? 'active' : '' ?>">
        <span class="stat-icon"><i class="fas fa-box-open"></i></span>
        <div class="stat-num"><?= $totalFound ?></div>
        <div class="stat-label">Found</div>
    </a>
    <a href="?filter=claimed" class="stat-card stat-claimed <?= $filter==='claimed' ? 'active' : '' ?>">
        <span class="stat-icon"><i class="fas fa-check-circle"></i></span>
        <div class="stat-num"><?= $totalClaimed ?></div>
        <div class="stat-label">Claimed</div>
    </a>
    <a href="?filter=unclaimed" class="stat-card stat-unclaim <?= $filter==='unclaimed' ? 'active' : '' ?>">
        <span class="stat-icon"><i class="fas fa-clock"></i></span>
        <div class="stat-num"><?= $totalUnclaimed ?></div>
        <div class="stat-label">Unclaimed</div>
    </a>
</div>

<!-- FILTER INDICATOR -->
<?php if($filter !== ''): ?>
<div class="filter-bar">
    <span class="filter-label">Showing:</span>
    <span class="filter-badge"><?= ucfirst($filter) ?> &mdash; <?= $filteredTotal ?> item<?= $filteredTotal != 1 ? 's' : '' ?></span>
    <a href="?" class="clear-filter"><i class="fas fa-times"></i> Clear Filter</a>
</div>
<?php endif; ?>

<!-- MAIN TABLE -->
<div class="container">

    <div class="header">
        <h2>ITEM RECORDS</h2>
        <a href="#" class="add-btn" onclick="openAddModal(); return false;" title="Add Item">
            <i class="fas fa-plus"></i>
        </a>
    </div>

    <div class="table-container">
        <table>
            <thead>
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
            </thead>
            <tbody>
                <?php if(mysqli_num_rows($result) > 0): ?>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td>#<?= $row['id'] ?></td>
                        <td>
                            <img
                                src="<?= htmlspecialchars($row['image']) ?>"
                                class="item-img"
                                onclick="openImg('<?= htmlspecialchars($row['image']) ?>')"
                                title="Click to enlarge"
                            >
                        </td>
                        <td><?= htmlspecialchars($row['item_name']) ?></td>
                        <td><?= htmlspecialchars($row['date_found']) ?></td>
                        <td><?= htmlspecialchars($row['location']) ?></td>
                        <td>
                            <?php $lf = strtolower($row['lost_found']); ?>
                            <span class="badge <?= $lf==='lost' ? 'badge-lost' : 'badge-found' ?>">
                                <?= htmlspecialchars($row['lost_found']) ?>
                            </span>
                        </td>
                        <td>
                            <?php $st = strtolower($row['status']); ?>
                            <span class="badge <?= $st==='claimed' ? 'badge-claimed' : 'badge-unclaimed' ?>">
                                <?= htmlspecialchars($row['status']) ?>
                            </span>
                        </td>
                        <td>
                            <div class="actions">
                                <!-- EDIT BUTTON — passes data to modal via JS -->
                                <button class="icon-btn editBtn" title="Edit"
                                    onclick="openEditModal(
                                        <?= $row['id'] ?>,
                                        '<?= addslashes(htmlspecialchars($row['item_name'])) ?>',
                                        '<?= $row['date_found'] ?>',
                                        '<?= addslashes(htmlspecialchars($row['location'])) ?>',
                                        '<?= $row['lost_found'] ?>',
                                        '<?= $row['status'] ?>',
                                        '<?= htmlspecialchars($row['image']) ?>'
                                    )">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="delete_item.php?id=<?= $row['id'] ?>" class="icon-btn deleteBtn" title="Delete"
                                   onclick="return confirm('Delete this item?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8">
                            <div class="no-results">
                                <i class="fas fa-inbox"></i>
                                No <?= $filter ? ucfirst($filter) : '' ?> items found.
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>

        <!-- PAGINATION -->
        <?php if($totalPages > 1): ?>
        <div class="pagination">
            <?php if($page > 1): ?>
                <a href="?filter=<?= $filter ?>&page=<?= $page-1 ?>"><i class="fas fa-chevron-left"></i> Prev</a>
            <?php endif; ?>
            <?php for($i=1; $i<=$totalPages; $i++): ?>
                <a class="<?= ($i==$page) ? 'active' : '' ?>" href="?filter=<?= $filter ?>&page=<?= $i ?>"><?= $i ?></a>
            <?php endfor; ?>
            <?php if($page < $totalPages): ?>
                <a href="?filter=<?= $filter ?>&page=<?= $page+1 ?>">Next <i class="fas fa-chevron-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- LOGOUT -->
        <div class="logout">
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</div>


<!-- ===== ADD ITEM MODAL ===== -->
<div class="modal" id="addModal">
    <div class="modal-content">
        <button class="close" onclick="closeModal('addModal')">&times;</button>
        <h3><i class="fas fa-plus-circle" style="margin-right:8px;"></i>ADD NEW ITEM</h3>
        <form method="POST" action="add_item.php" enctype="multipart/form-data">
            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">

            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="item_name" placeholder="e.g. Black Umbrella" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date Found</label>
                    <input type="date" name="date_found" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="lost_found">
                        <option value="Lost">Lost</option>
                        <option value="Found">Found</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Location Found</label>
                <input type="text" name="location" placeholder="e.g. Room 101, Library" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status">
                    <option value="Unclaimed">Unclaimed</option>
                    <option value="Claimed">Claimed</option>
                </select>
            </div>

            <div class="form-group">
                <label>Image (optional)</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('addModal')">Cancel</button>
                <button type="submit" class="btn-update"><i class="fas fa-save" style="margin-right:6px;"></i>Save Item</button>
            </div>
        </form>
    </div>
</div>


<!-- ===== EDIT ITEM MODAL ===== -->
<div class="modal" id="editModal">
    <div class="modal-content">
        <button class="close" onclick="closeModal('editModal')">&times;</button>
        <h3><i class="fas fa-edit" style="margin-right:8px;"></i>EDIT ITEM</h3>
        <form method="POST" enctype="multipart/form-data">
            <input type="hidden" name="update" value="1">
            <input type="hidden" name="id"     id="edit_id">
            <input type="hidden" name="filter" value="<?= htmlspecialchars($filter) ?>">

            <div class="form-group">
                <label>Item Name</label>
                <input type="text" name="item_name" id="edit_name" placeholder="Item Name" required>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label>Date Found</label>
                    <input type="date" name="date_found" id="edit_date" required>
                </div>
                <div class="form-group">
                    <label>Type</label>
                    <select name="lost_found" id="edit_type">
                        <option value="Lost">Lost</option>
                        <option value="Found">Found</option>
                    </select>
                </div>
            </div>

            <div class="form-group">
                <label>Location</label>
                <input type="text" name="location" id="edit_location" placeholder="Location" required>
            </div>

            <div class="form-group">
                <label>Status</label>
                <select name="status" id="edit_status">
                    <option value="Unclaimed">Unclaimed</option>
                    <option value="Claimed">Claimed</option>
                </select>
            </div>

            <div class="current-img-wrap">
                <label>Current Image</label>
                <img id="edit_img_preview" src="" alt="Current Image">
            </div>

            <div class="form-group">
                <label>Replace Image (optional)</label>
                <input type="file" name="image" accept="image/*">
            </div>

            <div class="modal-actions">
                <button type="button" class="btn-cancel" onclick="closeModal('editModal')">Cancel</button>
                <button type="submit" class="btn-update"><i class="fas fa-save" style="margin-right:6px;"></i>Update Item</button>
            </div>
        </form>
    </div>
</div>


<!-- ===== IMAGE PREVIEW MODAL ===== -->
<div class="modal" id="imgModal" onclick="closeModal('imgModal')">
    <div class="modal-content">
        <button class="close">&times;</button>
        <img id="imgPreview" src="" alt="Item Image">
    </div>
</div>


<script>
function openEditModal(id, name, date, location, type, status, imgSrc) {
    document.getElementById('edit_id').value       = id;
    document.getElementById('edit_name').value     = name;
    document.getElementById('edit_date').value     = date;
    document.getElementById('edit_location').value = location;
    document.getElementById('edit_img_preview').src = imgSrc;

    // Set select values
    const typeSelect   = document.getElementById('edit_type');
    const statusSelect = document.getElementById('edit_status');
    for (let opt of typeSelect.options)   opt.selected = (opt.value === type);
    for (let opt of statusSelect.options) opt.selected = (opt.value === status);

    document.getElementById('editModal').classList.add('active');
}

function openAddModal() {
    document.getElementById('addModal').classList.add('active');
}

function closeModal(id) {
    document.getElementById(id).classList.remove('active');
}

function openImg(src) {
    document.getElementById('imgPreview').src = src;
    document.getElementById('imgModal').classList.add('active');
}

// Close modals on backdrop click
['addModal','editModal'].forEach(function(id) {
    document.getElementById(id).addEventListener('click', function(e) {
        if (e.target === this) closeModal(id);
    });
});
</script>

</body>
</html>