<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start(); // Prevents early output blocking header()
session_start();
require_once 'data_storage.php';

// Ensure admin is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}
$user_id = $_SESSION['user_id'];

// Check user role for admin access
$users = get_users();
$role = null;
foreach ($users as $user) {
    if ($user['id'] == $user_id) {
        $role = $user['role'] ?? null;
        break;
    }
}
if ($role !== 'admin') {
    die("Access denied.");
}
// Handle updating copy trade gain/loss
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_copy_trade_gain'])) {
    $ct_id = intval($_POST['copy_trade_id']);
    $gain_loss = floatval($_POST['gain_loss']);

    $copy_trades = get_copy_trades();
    $updated = false;
    foreach ($copy_trades as &$trade) {
        if ($trade['id'] == $ct_id) {
            $trade['gain_loss'] = $gain_loss;
            $updated = true;
            break;
        }
    }
    if ($updated) {
        file_put_contents(__DIR__ . '/copy_trades.json', json_encode($copy_trades, JSON_PRETTY_PRINT));
    }

    header("Location: admin_confirm.php?msg=" . urlencode("Copy trade gain/loss updated."));
    exit;
}


// Get message from GET for feedback display
$message = isset($_GET['msg']) ? $_GET['msg'] : '';

// Handle payment confirmation
if (isset($_GET['confirm_payment_id'])) {
    $payment_id = intval($_GET['confirm_payment_id']);

    $payments = get_payments();
    $users = get_users();

    $payment = null;
    foreach ($payments as &$p) {
        if ($p['id'] == $payment_id) {
            $payment = &$p;
            break;
        }
    }
    if (!$payment) {
        die("Payment not found.");
    }
    if (($payment['status'] ?? '') !== 'pending') {
        die("Payment already processed.");
    }

    $p_user_id = $payment['user_id'];
    $amount_usd = $payment['amount_usd'];

    // Check if this is the user's first confirmed deposit
    $confirmed_count = 0;
    foreach ($payments as $p) {
        if ($p['user_id'] == $p_user_id && ($p['status'] ?? '') === 'confirmed') {
            $confirmed_count++;
        }
    }

    $is_first_deposit = ($confirmed_count === 0);
    $bonus = 0;

    if ($is_first_deposit) {
        $bonus = round($amount_usd * 0.15, 2); // 15% bonus
    }

    $total_credit = $amount_usd + $bonus;

    // Update payment status
    $payment['status'] = 'confirmed';
    $payment['confirmed_at'] = date('Y-m-d H:i:s');

    // Remove the confirmed payment from payments array
    $payments = array_filter($payments, function($p) use ($payment_id) {
        return $p['id'] !== $payment_id;
    });
    $payments = array_values($payments); // reindex array

    // Credit the user’s balance
    foreach ($users as &$user) {
        if ($user['id'] == $p_user_id) {
            $user['balance'] = ($user['balance'] ?? 0) + $total_credit;
            break;
        }
    }

    save_payments($payments);
    save_users($users);

    // Log the deposit transaction
    $transactions = get_transactions();
    $transactions[] = [
        'user_id' => $p_user_id,
        'amount' => $amount_usd,
        'type' => 'deposit',
        'created_at' => date('Y-m-d H:i:s'),
    ];

    // Optionally log the bonus separately
    if ($bonus > 0) {
        $transactions[] = [
            'user_id' => $p_user_id,
            'amount' => $bonus,
            'type' => 'bonus',
            'created_at' => date('Y-m-d H:i:s'),
        ];
    }

    save_transactions($transactions);

    $msg = "Payment ID {$payment_id} confirmed. Credited: \${$amount_usd}";
    if ($bonus > 0) {
        $msg .= " + \$${bonus} bonus.";
    }

    header("Location: admin_confirm.php?msg=" . urlencode($msg));
    exit;
}

// Handle adding new investment asset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['asset_name'], $_POST['return_percent']) && !isset($_POST['investment_return_submit'])) {
    $name = trim($_POST['asset_name']);
    $percent = floatval($_POST['return_percent']);

    $assets = get_investment_assets();
    $new_id = count($assets) > 0 ? max(array_column($assets, 'id')) + 1 : 1;
    $assets[] = [
        'id' => $new_id,
        'name' => $name,
        'return_percent' => $percent,
        'status' => 'active'
    ];
    file_put_contents(__DIR__ . '/investment_assets.json', json_encode($assets, JSON_PRETTY_PRINT));

    header("Location: admin_confirm.php"); // Refresh to avoid resubmission
    exit;
}

// Handle updating user's asset investment return (gain/loss)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['investment_return_submit'])) {
    $investment_id = intval($_POST['investment_id']);
    $gain_loss = floatval($_POST['gain_loss']);

    $investments = get_user_asset_investments();
    $updated = false;
    foreach ($investments as &$inv) {
        if ($inv['id'] == $investment_id) {
            $inv['gain_loss'] = $gain_loss;
            $updated = true;
            break;
        }
    }
    if ($updated) {
        file_put_contents(__DIR__ . '/user_asset_investments.json', json_encode($investments, JSON_PRETTY_PRINT));
        $msg = "Investment return updated successfully.";
    } else {
        $msg = "Error updating gain/loss: Investment not found.";
    }
    header("Location: admin_confirm.php?msg=" . urlencode($msg));
    exit;
}

$payments = get_payments();
$users = get_users();
$assets = get_investment_assets();
$user_asset_investments = get_user_asset_investments();
$traders = get_copy_traders();
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['copy_trade_submit'])) {
    $user_id = intval($_POST['user_id']);
    $asset_id = intval($_POST['asset_id']);
    $amount = floatval($_POST['amount']);

    $users = get_users();
    $user = null;
    foreach ($users as &$u) {
        if ($u['id'] == $user_id) {
            $user = &$u;
            break;
        }
    }
    if (!$user) {
        $msg = "User not found.";
        header("Location: admin_confirm.php?msg=" . urlencode($msg));
        exit;
    }

    if (($user['balance'] ?? 0) < $amount) {
        $msg = "User does not have enough balance.";
        header("Location: admin_confirm.php?msg=" . urlencode($msg));
        exit;
    }

    // Deduct balance
    $user['balance'] -= $amount;
    save_users($users);

    $return_percent = floatval($_POST['return_percent']);
    $trader_id = intval($_POST['trader_id']); // Get trader ID from form

    $copy_trades = get_copy_trades();
    $new_id = count($copy_trades) > 0 ? max(array_column($copy_trades, 'id')) + 1 : 1;
    $copy_trades[] = [
        'id' => $new_id,
        'user_id' => $user_id,
        'trader_id' => $trader_id,
        'asset_id' => $asset_id,
        'amount' => $amount,
        'return_percent' => $return_percent,
        'status' => 'active',
        'invested_at' => date('Y-m-d H:i:s'),
        'gain_loss' => 0
    ];
    file_put_contents(__DIR__ . '/copy_trades.json', json_encode($copy_trades, JSON_PRETTY_PRINT));

    // Log transaction
    $transactions = get_transactions();
    $transactions[] = [
        'user_id' => $user_id,
        'amount' => $amount,
        'type' => 'investment',
        'created_at' => date('Y-m-d H:i:s'),
    ];
    save_transactions($transactions);

    $msg = "Copy trade initiated successfully.";
    header("Location: admin_confirm.php?msg=" . urlencode($msg));
    exit;
}

if (isset($_POST['approve_withdrawal']) && is_numeric($_POST['withdrawal_id'])) {
    $withdrawal_id = intval($_POST['withdrawal_id']);

    $withdrawals = get_withdrawals();
    $users = get_users();

    $withdrawal = null;
    foreach ($withdrawals as &$w) {
        if ($w['id'] == $withdrawal_id) {
            $withdrawal = &$w;
            break;
        }
    }
    if (!$withdrawal) {
        echo "<p style='color:red;'>Withdrawal not found.</p>";
        exit;
    }

    $withdrawal['status'] = 'approved';
    save_withdrawals($withdrawals);

    echo "<p style='color:green;'>✅ Withdrawal #$withdrawal_id approved.</p>";
}

if (isset($_POST['reject_withdrawal']) && is_numeric($_POST['withdrawal_id'])) {
    $withdrawal_id = intval($_POST['withdrawal_id']);

    $withdrawals = get_withdrawals();
    $users = get_users();

    $withdrawal = null;
    foreach ($withdrawals as &$w) {
        if ($w['id'] == $withdrawal_id) {
            $withdrawal = &$w;
            break;
        }
    }
    if (!$withdrawal) {
        echo "<p style='color:red;'>Withdrawal not found.</p>";
        exit;
    }

    if (($withdrawal['status'] ?? '') !== 'pending') {
        echo "<p style='color:red;'>Withdrawal #$withdrawal_id is not pending and cannot be rejected.</p>";
        exit;
    }

    $withdrawal['status'] = 'rejected';
    save_withdrawals($withdrawals);

    // Refund the amount to withdrawable_balance, NOT balance
    $user_id = $withdrawal['user_id'];
    $amount = $withdrawal['amount'];
    foreach ($users as &$user) {
        if ($user['id'] == $user_id) {
            $user['withdrawable_balance'] = ($user['withdrawable_balance'] ?? 0) + $amount;
            break;
        }
    }
    save_users($users);

    echo "<p style='color:red;'>❌ Withdrawal #$withdrawal_id rejected and amount refunded to withdrawable balance.</p>";
}


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['credit_user_id'], $_POST['credit_amount'])) {
    $credit_user_id = intval($_POST['credit_user_id']);
    $credit_amount = floatval($_POST['credit_amount']);

    if ($credit_amount > 0) {
        $users = get_users();
        foreach ($users as &$user) {
            if ($user['id'] == $credit_user_id) {
                $user['withdrawable_balance'] = ($user['withdrawable_balance'] ?? 0) + $credit_amount;
                break;
            }
        }
        save_users($users);

        echo "<p style='color:green;'>Credited \${$credit_amount} to user's withdrawable balance.</p>";
    }
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel - Confirm Deposits &amp; Manage Asset Returns</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        :root {
            --primary: #2563eb;
            --primary-light: #3b82f6;
            --success: #22c55e;
            --danger: #ef4444;
            --bg: #fff;
            --text: #374151;
            --muted: #6b7280;
            --border: #e5e7eb;
            --radius: 0.75rem;
            --shadow: 0 10px 15px rgba(0,0,0,0.1);
        }
        body {
            font-family: 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            max-width: 1200px;
            margin: 40px auto;
            padding: 0 20px;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        h1 {
            font-weight: 700;
            font-size: 48px;
            margin-bottom: 1rem;
            color: var(--primary);
        }
        h2 {
            font-weight: 600;
            font-size: 24px;
            margin: 3rem 0 1rem;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 0.5rem;
            color: var(--primary);
        }
        table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 0 12px;
            font-size: 16px;
            color: var(--muted);
        }
        thead th {
            color: var(--primary-light);
            font-weight: 700;
            text-align: center;
            border-bottom: 2px solid var(--primary-light);
            padding-bottom: 0.75rem;
        }
        tbody tr {
            background: #f9fafb;
            border-radius: var(--radius);
            box-shadow: var(--shadow);
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        tbody tr:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 25px rgba(0,0,0,0.15);
        }
        tbody td {
            padding: 1rem 0.75rem;
            text-align: center;
            vertical-align: middle;
        }
        tbody td:first-child {
            border-top-left-radius: var(--radius);
            border-bottom-left-radius: var(--radius);
        }
        tbody td:last-child {
            border-top-right-radius: var(--radius);
            border-bottom-right-radius: var(--radius);
        }
        a.confirm-btn, button.submit-btn {
            background-color: var(--primary);
            border: none;
            border-radius: var(--radius);
            padding: 0.6rem 1.5rem;
            font-weight: 700;
            color: white;
            cursor: pointer;
            transition: background-color 0.3s ease;
            text-decoration: none;
            display: inline-block;
        }
        a.confirm-btn:hover, button.submit-btn:hover {
            background-color: var(--primary-light);
        }
        form.inline-form {
            margin: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 0.5rem;
        }
        input[type="number"] {
            width: 85px;
            padding: 0.5rem 1rem;
            border-radius: var(--radius);
            border: 1.5px solid var(--border);
            font-size: 1rem;
            color: var(--text);
            outline-offset: 2px;
        }
        input[type="number"]:focus {
            border-color: var(--primary);
            outline: none;
        }
        form#asset-form {
            max-width: 600px;
            display: flex;
            gap: 1rem;
            flex-wrap: wrap;
            align-items: center;
            margin-top: 2rem;
        }
        form#asset-form input[type="text"],
        form#asset-form input[type="number"] {
            flex: 1 1 250px;
            padding: 0.75rem 1rem;
            border: 1.5px solid var(--border);
            border-radius: var(--radius);
            font-size: 1.1rem;
        }
        form#asset-form input[type="text"]:focus,
        form#asset-form input[type="number"]:focus {
            border-color: var(--primary);
            outline: none;
        }
        .message {
            max-width: 600px;
            margin-bottom: 2rem;
            padding: 1rem 1.5rem;
            border-radius: var(--radius);
            background-color: var(--success);
            color: white;
            font-weight: 600;
            font-size: 1.1rem;
        }
        .icon-up {
            color: var(--success);
            font-weight: 700;
            font-size: 1.25rem;
        }
        .icon-down {
            color: var(--danger);
            font-weight: 700;
            font-size: 1.25rem;
        }
        @media (max-width: 640px) {
            h1 { font-size: 36px; }
            h2 { font-size: 20px; margin-top: 2rem; }
            form#asset-form {
                flex-direction: column;
            }
            form#asset-form input, form#asset-form button {
                width: 100%;
            }
            tbody td {
                padding: 0.75rem 0.5rem;
                font-size: 14px;
            }
        }
    </style>
</head>
<body>
    <h1>Pending Deposits</h1>
    <?php if (!empty($message)): ?>
        <div class="message" role="alert"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>
    <table aria-label="Pending Deposits Table">
        <thead>
            <tr>
                <th>ID</th>
                <th>User</th>
                <th>Amount (USD)</th>
                <th>Crypto</th>
                <th>Amount (Crypto)</th>
                <th>Wallet Address</th>
                <th>Proof</th>
                <th>Date</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($payments as $row): ?>
            <tr>
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($users[array_search($row['user_id'], array_column($users, 'id'))]['username'] ?? '') ?></td>
                <td>$<?= number_format($row['amount_usd'], 2) ?></td>
                <td><?= htmlspecialchars($row['crypto']) ?></td>
                <td><?= htmlspecialchars($row['amount_crypto']) ?></td>
                <td><?= htmlspecialchars($row['wallet_address']) ?></td>
                <td>
                    <?php if (!empty($row['proof_file'])): ?>
                        <a href="uploads/<?= htmlspecialchars($row['proof_file']) ?>" target="_blank">View Proof</a>
                    <?php else: ?>
                        No proof
                    <?php endif; ?>
                </td>
                <td><?= $row['created_at'] ?></td>
                <td>
                    <a class="confirm-btn" href="?confirm_payment_id=<?= $row['id'] ?>">Confirm</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>User Asset Investments & Returns</h2>
    <table aria-label="User Investments">
        <thead>
            <tr>
                <th>User</th>
                <th>Asset</th>
                <th>Invested Amount</th>
                <th>Return %</th>
                <th>Status</th>
                <th>Gain/Loss ($)</th>
                <th>Invested At</th>
                <th>Update</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($user_asset_investments as $inv): ?>
            <tr>
                <td><?= htmlspecialchars($users[array_search($inv['user_id'], array_column($users, 'id'))]['username'] ?? '') ?></td>
                <td><?= htmlspecialchars($assets[array_search($inv['asset_id'], array_column($assets, 'id'))]['name'] ?? '') ?></td>
                <td>$<?= number_format($inv['amount'], 2) ?></td>
                <td><?= $inv['return_percent'] ?>%</td>
                <td><?= htmlspecialchars($inv['status']) ?></td>
                <td class="<?= $inv['gain_loss'] >= 0 ? 'icon-up' : 'icon-down' ?>">
                    <?= $inv['gain_loss'] >= 0 ? '+' : '' ?><?= number_format($inv['gain_loss'], 2) ?>
                </td>
                <td><?= $inv['invested_at'] ?></td>
                <td>
                    <form class="inline-form" method="post">
                        <input type="hidden" name="investment_id" value="<?= $inv['id'] ?>">
                        <input type="number" step="0.01" name="gain_loss" required placeholder="e.g. 50 or -20">
                        <button type="submit" class="submit-btn" name="investment_return_submit">Update</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>

    <h2>Pending Withdrawals</h2>
<table border="1" cellpadding="10" cellspacing="0">
    <thead>
        <tr>
            <th>User ID</th>
            <th>Amount</th>
            <th>Method</th>
            <th>Details</th>
            <th>Requested At</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $withdrawals = get_withdrawals();
        if (count($withdrawals) > 0):
            foreach ($withdrawals as $row):
                if (($row['status'] ?? '') !== 'pending') {
                    continue;
                }
        ?>
            <tr>
                <td><?= htmlspecialchars($users[array_search($row['user_id'], array_column($users, 'id'))]['username'] ?? '') ?> (#<?= $row['user_id'] ?>)</td>
                <td>$<?= number_format($row['amount'], 2) ?></td>
                <td><?= htmlspecialchars($row['method']) ?></td>
                <td><?= htmlspecialchars($row['details']) ?></td>
                <td><?= htmlspecialchars($row['requested_at']) ?></td>
                <td>
                    <form method="post" action="admin_confirm.php" style="display:inline;">
                        <input type="hidden" name="withdrawal_id" value="<?= $row['id'] ?>">
                        <button type="submit" name="approve_withdrawal">✅ Approve</button>
                        <button type="submit" name="reject_withdrawal">❌ Reject</button>
                    </form>
                </td>
            </tr>
        <?php
            endforeach;
        else:
            echo "<tr><td colspan='6'>No pending withdrawals.</td></tr>";
        endif;
        ?>
    </tbody>
</table>

<h2>Credit User's Withdrawable Balance</h2>
<form method="POST" style="display: flex; gap: 1rem;">
    <select name="credit_user_id" required>
        <option value="">Select User</option>
        <?php
        foreach ($users as $u):
        ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
        <?php endforeach; ?>
    </select>
    <input type="number" step="0.01" name="credit_amount" placeholder="Amount ($)" required />
    <button type="submit">Credit Balance</button>
</form>



    <h2>Copy Trades Initiated</h2>
<table>
<thead><tr>
    <th>User</th><th>Trader</th><th>Asset</th><th>Amount</th><th>Return %</th>
    <th>Status</th><th>Gain/Loss</th><th>Update</th>
</tr></thead>
<tbody>
<?php
$ct_rs = get_copy_trades();
$copy_traders = get_copy_traders();
foreach ($ct_rs as $ct):
    $trader_name = '';
    foreach ($copy_traders as $trader) {
        if ($trader['id'] == $ct['trader_id']) {
            $trader_name = $trader['name'];
            break;
        }
    }
?>
<tr>
    <td><?= htmlspecialchars($users[array_search($ct['user_id'], array_column($users, 'id'))]['username'] ?? '') ?></td>
    <td><?= htmlspecialchars($trader_name) ?></td>
    <td><?= htmlspecialchars($ct['asset'] ?? 'N/A') ?></td>
    <td>$<?= number_format($ct['amount'],2) ?></td>
    <td><?= number_format($ct['return_percent'],2) ?>%</td>
    <td><?= htmlspecialchars($ct['status']) ?></td>
   <td>
    <?php if ($ct['gain_loss'] >= 0): ?>
        <span style="color: green;">&#9650; $<?= number_format($ct['gain_loss'], 2) ?></span>
    <?php else: ?>
        <span style="color: red;">&#9660; $<?= number_format(abs($ct['gain_loss']), 2) ?></span>
    <?php endif; ?>
</td>

    <td>
        <form method="post" class="inline-form">
            <input type="hidden" name="copy_trade_id" value="<?= $ct['id'] ?>">
            <input type="number" step="0.01" name="gain_loss" required placeholder="e.g. 10 or -5">
            <button type="submit" class="submit-btn" name="update_copy_trade_gain">Update</button>
        </form>
    </td>
</tr>
<?php endforeach; ?>
</tbody>
</table>


    <h2>Add New Investment Asset</h2>
    <form id="asset-form" method="post">
        <input type="text" name="asset_name" placeholder="Asset Name (e.g. Bitcoin)" required />
        <input type="number" step="0.01" name="return_percent" placeholder="Return % (e.g. 5.5)" required />
        <button class="submit-btn" type="submit">Add Asset</button>
    </form>

   <h2>Initiate Copy Trade</h2>
<form method="POST" style="max-width: 600px; display: flex; gap: 1rem; flex-wrap: wrap;">
    <select name="user_id" required>
        <option value="">Select User</option>
        <?php
        foreach ($users as $u):
        ?>
            <option value="<?= $u['id'] ?>"><?= htmlspecialchars($u['username']) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="asset_id" required>
        <option value="">Select Asset</option>
        <?php
        foreach ($assets as $a):
        ?>
            <option value="<?= $a['id'] ?>"><?= htmlspecialchars($a['name']) ?></option>
        <?php endforeach; ?>
    </select>

    <select name="trader_id" required>
    <option value="">Select Trader</option>
    <?php
    if (empty($traders)) {
        echo '<option disabled>No traders available</option>';
    } else {
        foreach ($traders as $t):
    ?>
        <option value="<?= $t['id'] ?>"><?= htmlspecialchars($t['name']) ?></option>
    <?php
        endforeach;
    }
    ?>
</select>



    <input type="number" step="0.01" name="amount" placeholder="Amount ($)" required />
    <input type="number" step="0.01" name="return_percent" placeholder="Return %" required />

    <button type="submit" name="copy_trade_submit" class="submit-btn">Initiate</button>
</form>



</body>
</html>
<?php ob_end_flush(); ?>
