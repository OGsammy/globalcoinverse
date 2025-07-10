<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start(); // Prevents early output blocking header()
session_start();
require_once 'data_storage.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = floatval($_POST['amount'] ?? 0);
$asset_id = intval($_POST['asset_id'] ?? 0);
$plan = $_POST['plan'] ?? null; // optionally use plan

if ($amount <= 0 || $asset_id <= 0) {
    $_SESSION['message'] = "Invalid input.";
    header("Location: invest.php");
    exit;
}

// Get user's balance from users.json
$users = get_users();
$user = null;
foreach ($users as &$u) {
    if ($u['id'] == $user_id) {
        $user = &$u;
        break;
    }
}
$balance = $user['balance'] ?? 0.0;

if ($amount > $balance) {
    $_SESSION['message'] = "Insufficient balance.";
    header("Location: invest.php");
    exit;
}

// Fetch return percent from investment_assets.json
$all_assets = get_investment_assets();
$return_percent = null;
foreach ($all_assets as $a) {
    if (($a['id'] ?? 0) == $asset_id && ($a['status'] ?? '') === 'active') {
        $return_percent = $a['return_percent'];
        break;
    }
}
if ($return_percent === null) {
    $_SESSION['message'] = "Invalid asset selected.";
    header("Location: invest.php");
    exit;
}

// Insert into user_asset_investments.json
$user_asset_investments_file = __DIR__ . '/user_asset_investments.json';
$user_asset_investments = [];
if (file_exists($user_asset_investments_file)) {
    $user_asset_investments = json_decode(file_get_contents($user_asset_investments_file), true) ?: [];
}
$investment_id = count($user_asset_investments) > 0 ? max(array_column($user_asset_investments, 'id')) + 1 : 1;
$user_asset_investments[] = [
    'id' => $investment_id,
    'user_id' => $user_id,
    'asset_id' => $asset_id,
    'amount' => $amount,
    'return_percent' => $return_percent,
    'invested_at' => date('Y-m-d H:i:s'),
    'status' => 'active',
    'gain_loss' => 0,
    'source' => 'investment'
];
file_put_contents($user_asset_investments_file, json_encode($user_asset_investments, JSON_PRETTY_PRINT));

// Deduct amount from balance and save users.json
$user['balance'] = $balance - $amount;
save_users($users);

$_SESSION['message'] = "Investment placed successfully!";
header("Location: dashboard.php");
exit;
ob_end_flush();
?>
