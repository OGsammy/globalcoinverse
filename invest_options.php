<?php
session_start();
require_once 'data_storage.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$plan = $_POST['plan'] ?? null;

$plans = [
    'starter' => ['label' => 'Starter Plan', 'percent' => 10, 'days' => 2, 'min' => 500],
    'silver' => ['label' => 'Silver Plan', 'percent' => 25, 'days' => 5, 'min' => 5000],
    'Gold' => ['label' => 'Gold plan', 'percent' => 30, 'days' => 7, 'min' => 50000],
    'platinum' => ['label' => 'Platinum plan', 'percent' => 70, 'days' => 10, 'min' => 100000],
];

if (!isset($plans[$plan])) {
    die("Invalid plan selected.");
}

// Fetch user's balance from users.json
$users = get_users();
$user = null;
foreach ($users as $u) {
    if ($u['id'] == $user_id) {
        $user = $u;
        break;
    }
}
$balance = $user['balance'] ?? 0.0;

// Fetch currency assets from investment_assets.json
$all_assets = get_investment_assets();
$assets = array_filter($all_assets, fn($a) => ($a['status'] ?? '') === 'active');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta charset="UTF-8">
    <title><?= $plans[$plan]['label'] ?> - Choose Asset</title>
    <style>
        body { background: #121212; color: #eee; font-family: sans-serif; padding: 40px; text-align: center; }
        .container { max-width: 700px; margin: auto; background: #1e1e1e; padding: 30px; border-radius: 10px; box-shadow:  0 0 15px rgba(212, 175, 55, 0.1) }
        h1 { color: #00d8ff; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 12px; border-bottom: 1px solid #333; }
        th { background: #00d8ff22; }
        input, button, select {
            padding: 12px;
            border: none;
            border-radius: 6px;
            margin-top: 12px;
            font-size: 16px;
            width: 100%;
        }
        button {
            background: #d4af37;
            color: #121212;
            font-weight: bold;
            cursor: pointer;
        }
        button:hover { background:rgb(156, 133, 55) }
        .balance { margin-top: 15px; color: #d4af37; font-weight: bold; }
    </style>
</head>
<body>
    <div class="container">
        <h1><?= $plans[$plan]['label'] ?></h1>
        <p>Return: <?= $plans[$plan]['percent'] ?>% | Minimum: $<?= $plans[$plan]['min'] ?></p>
        <p class="balance">Available Balance: $<?= number_format($balance, 2) ?></p>

        <form method="post" action="process_asset_invest.php">
            <input type="hidden" name="plan" value="<?= $plan ?>">
            <label for="asset_id">Choose Asset:</label>
            <select name="asset_id" id="asset_id" required>
                <option value="">-- Select --</option>
                <?php foreach ($assets as $a): ?>
                    <option value="<?= $a['id'] ?>">
                        <?= htmlspecialchars($a['name']) ?> (Return: <?= $a['return_percent'] ?>%)
                    </option>
                <?php endforeach; ?>
            </select>

            <input type="number" name="amount" min="<?= $plans[$plan]['min'] ?>" max="<?= $balance ?>" step="0.01" placeholder="Amount in USD" required>
            <button type="submit">Invest</button>
        </form>
    </div>
</body>
</html>
