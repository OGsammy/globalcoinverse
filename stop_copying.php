<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start(); // Prevents early output blocking header()
session_start();
require_once 'data_storage.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Validate and sanitize input
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['trader_id'])) {
    $trader_id = intval($_POST['trader_id']);

    // Delete from copied_traders.json
    $copied_traders_file = __DIR__ . '/copied_traders.json';
    $copied_traders = [];
    if (file_exists($copied_traders_file)) {
        $copied_traders = json_decode(file_get_contents($copied_traders_file), true) ?: [];
    }
    $copied_traders = array_filter($copied_traders, fn($entry) => !($entry['user_id'] == $user_id && $entry['trader_id'] == $trader_id));
    file_put_contents($copied_traders_file, json_encode(array_values($copied_traders), JSON_PRETTY_PRINT));

    // Delete related trades from copy_trades.json
    $copy_trades_file = __DIR__ . '/copy_trades.json';
    $copy_trades = [];
    if (file_exists($copy_trades_file)) {
        $copy_trades = json_decode(file_get_contents($copy_trades_file), true) ?: [];
    }
    $copy_trades = array_filter($copy_trades, fn($trade) => !($trade['user_id'] == $user_id && $trade['trader_id'] == $trader_id));
    file_put_contents($copy_trades_file, json_encode(array_values($copy_trades), JSON_PRETTY_PRINT));

    $_SESSION['message'] = "You have stopped copying this trader.";
} else {
    $_SESSION['message'] = "Invalid request.";
}

header("Location: dashboard.php");
exit;
ob_end_flush();
?>