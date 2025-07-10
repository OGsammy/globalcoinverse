<?php
// data_storage.php - file-based storage for various entities

function read_json_file($filename) {
    if (!file_exists($filename)) {
        file_put_contents($filename, json_encode([]));
    }
    $json = file_get_contents($filename);
    $data = json_decode($json, true);
    if (!is_array($data)) {
        $data = [];
    }
    return $data;
}

function write_json_file($filename, $data) {
    $json = json_encode($data, JSON_PRETTY_PRINT);
    return file_put_contents($filename, $json) !== false;
}

// Users
function get_users() {
    return read_json_file(__DIR__ . '/users.json');
}

function save_users($users) {
    return write_json_file(__DIR__ . '/users.json', $users);
}

// Transactions
function get_transactions() {
    return read_json_file(__DIR__ . '/transactions.json');
}

function save_transactions($transactions) {
    return write_json_file(__DIR__ . '/transactions.json', $transactions);
}

// Investments
function get_investments() {
    return read_json_file(__DIR__ . '/investments.json');
}

function save_investments($investments) {
    return write_json_file(__DIR__ . '/investments.json', $investments);
}

// Copy Traders
function get_copy_traders() {
    return read_json_file(__DIR__ . '/copy_traders.json');
}

function save_copy_traders($copy_traders) {
    return write_json_file(__DIR__ . '/copy_traders.json', $copy_traders);
}

// Copy Trades
function get_copy_trades() {
    return read_json_file(__DIR__ . '/copy_trades.json');
}

function save_copy_trades($copy_trades) {
    return write_json_file(__DIR__ . '/copy_trades.json', $copy_trades);
}

// Investment Assets
function get_investment_assets() {
    return read_json_file(__DIR__ . '/investment_assets.json');
}

function save_investment_assets($investment_assets) {
    return write_json_file(__DIR__ . '/investment_assets.json', $investment_assets);
}

// User Asset Investments
function get_user_asset_investments() {
    return read_json_file(__DIR__ . '/user_asset_investments.json');
}

function save_user_asset_investments($user_asset_investments) {
    return write_json_file(__DIR__ . '/user_asset_investments.json', $user_asset_investments);
}

// Payments
function get_payments() {
    return read_json_file(__DIR__ . '/payments.json');
}

function save_payments($payments) {
    return write_json_file(__DIR__ . '/payments.json', $payments);
}

// Withdrawals
function get_withdrawals() {
    return read_json_file(__DIR__ . '/withdrawals.json');
}

function save_withdrawals($withdrawals) {
    return write_json_file(__DIR__ . '/withdrawals.json', $withdrawals);
}
?>
