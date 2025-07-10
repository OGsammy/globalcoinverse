<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT role FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($role);
$stmt->fetch();
$stmt->close();

if ($role !== 'admin') {
    die("Access denied.");
}

// Handle asset creation
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asset_name'], $_POST['return_percent'])) {
    $name = trim($_POST['asset_name']);
    $percent = floatval($_POST['return_percent']);
    $conn->prepare("INSERT INTO investment_assets (name, return_percent) VALUES (?, ?)")
         ->bind_param("sd", $name, $percent)
         ->execute();
    header("Location: admin_assets.php");
    exit;
}

$assets = $conn->query("SELECT * FROM investment_assets ORDER BY id DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin - Manage Investment Assets</title>
    <style>
        body { font-family: sans-serif; padding: 20px; }
        input, button { padding: 8px; margin: 5px; }
        table { border-collapse: collapse; width: 100%; margin-top: 20px; }
        th, td { border: 1px solid #aaa; padding: 10px; text-align: center; }
        th { background: #00d8ff; }
    </style>
</head>
<body>
    <h1>Manage Investment Assets</h1>
    <form method="post">
        <input type="text" name="asset_name" placeholder="e.g. USD/AUD" required />
        <input type="number" step="0.01" name="return_percent" placeholder="Return %" required />
        <button type="submit">Add Asset</button>
    </form>

    <table>
        <tr>
            <th>ID</th><th>Name</th><th>Return %</th><th>Status</th>
        </tr>
        <?php while ($a = $assets->fetch_assoc()): ?>
        <tr>
            <td><?= $a['id'] ?></td>
            <td><?= htmlspecialchars($a['name']) ?></td>
            <td><?= number_format($a['return_percent'], 2) ?>%</td>
            <td><?= $a['status'] ?></td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
