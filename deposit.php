<?php
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$amount = floatval($_POST['amount']);
$currency_input = strtolower(trim($_POST['currency']));

// Map wallet addresses by currency (use lowercase keys for matching)
$wallet_addresses = [
    'sol' => '4xor6WRhgpwRsgCErQ3U4vVTdDunBWogYhMdrMUPfD8y',
    'usdt(trc20)' => 'TQyuZjpa5yagEZtqDWXCtsund9YMFtytp7',
    'xrp' => 'r9VdhfhnSKWdTMTjdFsoyTrdgVScupN1XV',
    'bnb' => '0xF5e40b2A12b522a7AF4c0b20209FabD7DABBe608',
    'btc' => '',   // Add BTC wallet address here if you have
    'eth' => '',   // Add ETH wallet address here if you have
    'usdt' => '',  // Add USDT (ERC20?) wallet address if different from TRC20
];

// Validate amount and currency
if ($amount <= 0 || !array_key_exists($currency_input, $wallet_addresses)) {
    $_SESSION['message'] = 'Invalid deposit amount or currency.';
    header("Location: dashboard.php");
    exit;
}

// Use wallet address from map
$wallet_address = $wallet_addresses[$currency_input];

// You can optionally get amount_crypto from POST if you want, or leave empty
$amount_crypto = '';

// Check if this is user's first confirmed deposit
$stmt = $conn->prepare("SELECT COUNT(*) FROM payments WHERE user_id = ? AND status = 'confirmed'");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($confirmed_deposits_count);
$stmt->fetch();
$stmt->close();

// Bonus calculation only happens on admin confirm, so no bonus applied here

// Insert deposit as pending
$stmt = $conn->prepare("INSERT INTO payments (user_id, amount_usd, crypto, amount_crypto, wallet_address, status, created_at) VALUES (?, ?, ?, ?, ?, 'pending', NOW())");
$stmt->bind_param("idsss", $user_id, $amount, $currency_input, $amount_crypto, $wallet_address);
$stmt->execute();
$stmt->close();

$_SESSION['message'] = "Deposit submitted successfully. Admin will confirm soon.";
header("Location: dashboard.php");
exit;
