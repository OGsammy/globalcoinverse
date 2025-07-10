<?php
session_start();
require_once 'data_storage.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'user') {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
function is_valid_wallet_address($method, $address) {
    switch (strtoupper($method)) {
      case 'BTC':
        // Bitcoin legacy (starts with 1 or 3) and Bech32 (starts with bc1)
        return preg_match('/^(bc1)[0-9a-z]{25,90}$|^[13][a-km-zA-HJ-NP-Z1-9]{25,39}$/i', $address) === 1;

      case 'ETH':
      case 'USDT':
          // ETH and USDT (ERC20): 0x + 40 hex chars
          return preg_match('/^0x[a-fA-F0-9]{40}$/', $address) === 1;
      case 'BNB':
          // BNB: starts with 'bnb' + 39 lowercase letters/numbers
          return preg_match('/^bnb[0-9a-z]{39}$/', $address) === 1;
      default:
          return false;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = isset($_POST['amount']) ? floatval($_POST['amount']) : 0;
    $method = trim($_POST['method']);
    $details = trim($_POST['details']);

    // Validate input
    if ($amount <= 0 || empty($method) || empty($details)) {
        $_SESSION['message'] = "Please fill all fields correctly.";
        header("Location: dashboard.php");
        exit;
    }
    if (!is_valid_wallet_address($method, $details)) {
        $_SESSION['message'] = "❌ Invalid wallet address format for $method.";
        header("Location: dashboard.php");
        exit;
    }

    // Get user's withdrawable balance
    $users = get_users();
    $user = null;
    foreach ($users as &$u) {
        if ($u['id'] == $user_id) {
            $user = &$u;
            break;
        }
    }
    $withdrawable_balance = $user['withdrawable_balance'] ?? 0;

    if ($amount > $withdrawable_balance) {
        $_SESSION['message'] = "❌ You don't have enough withdrawable balance. Please wait for admin approval.";
        header("Location: dashboard.php");
        exit;
    }

    // Insert withdrawal request
    $withdrawals = get_withdrawals();
    $new_id = count($withdrawals) > 0 ? max(array_column($withdrawals, 'id')) + 1 : 1;
    $withdrawals[] = [
        'id' => $new_id,
        'user_id' => $user_id,
        'amount' => $amount,
        'method' => $method,
        'details' => $details,
        'status' => 'pending',
        'requested_at' => date('Y-m-d H:i:s'),
    ];

    if (file_put_contents(__DIR__ . '/withdrawals.json', json_encode($withdrawals, JSON_PRETTY_PRINT))) {
        // Deduct withdrawable balance
        $user['withdrawable_balance'] = $withdrawable_balance - $amount;
        save_users($users);

        $_SESSION['message'] = "✅ Withdrawal request for \$$amount submitted successfully!";
    } else {
        $_SESSION['message'] = "❌ Error: Could not submit withdrawal request.";
    }

    header("Location: dashboard.php");
    exit;
} else {
    $_SESSION['message'] = "Invalid withdrawal request.";
    header("Location: dashboard.php");
    exit;
}
?>
