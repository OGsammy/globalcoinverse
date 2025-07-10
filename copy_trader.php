<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start(); // Prevents early output blocking header()
session_start();
require_once 'data_storage.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || !isset($_POST['trader_id'])) {
    die("Unauthorized.");
}

$user_id = $_SESSION['user_id'];
$trader_id = intval($_POST['trader_id']);

// Load copied traders data
$copied_traders_file = __DIR__ . '/copied_traders.json';
$copied_traders = [];
if (file_exists($copied_traders_file)) {
    $copied_traders = json_decode(file_get_contents($copied_traders_file), true) ?: [];
}

// Check if user is already copying this trader
$found = false;
foreach ($copied_traders as &$entry) {
    if ($entry['user_id'] == $user_id && $entry['trader_id'] == $trader_id) {
        $found = true;
        break;
    }
}
if (!$found) {
    $copied_traders[] = ['user_id' => $user_id, 'trader_id' => $trader_id];
    file_put_contents($copied_traders_file, json_encode($copied_traders, JSON_PRETTY_PRINT));
}

// Update followers count in copy_traders.json
$copy_traders_file = __DIR__ . '/copy_traders.json';
$copy_traders = [];
if (file_exists($copy_traders_file)) {
    $copy_traders = json_decode(file_get_contents($copy_traders_file), true) ?: [];
}
foreach ($copy_traders as &$trader) {
    if ($trader['id'] == $trader_id) {
        $trader['followers'] = ($trader['followers'] ?? 0) + 1;
        break;
    }
}
file_put_contents($copy_traders_file, json_encode($copy_traders, JSON_PRETTY_PRINT));

$_SESSION['message'] = "You're now copying this trader!";
header("Location: dashboard.php");
exit;
ob_end_flush();
?>