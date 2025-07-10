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

// Step 1: User submits deposit amount
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['amount_usd']) && !isset($_POST['crypto'])) {
    $amount_usd = floatval($_POST['amount_usd']);
    if ($amount_usd < 1) {
        $_SESSION['message'] = "Please enter at least $1.";
        header("Location: dashboard.php");
        exit;
    }
    $_SESSION['pending_deposit_amount'] = $amount_usd;
    header("Location: invest.php");
    exit;
}

// Step 2: User selects cryptocurrency
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['crypto'])) {
    $crypto = $_POST['crypto'];
    $amount_usd = $_SESSION['pending_deposit_amount'] ?? 0;

    if ($amount_usd <= 0) {
        $_SESSION['message'] = "Invalid deposit amount.";
        header("Location: dashboard.php");
        exit;
    }

    $crypto_ids = [
        'btc' => 'bitcoin',
        'eth' => 'ethereum',
        'ltc' => 'litecoin',
        'sol' => 'solana',
        'usdt' => 'tether',
        'xrp' => 'ripple',
        'bnb' => 'binancecoin'
    ];

    $walletAddresses = [
        'btc' => 'bc1qtfdm43x6yzts3yfpedvz26x7hlfddl84egpk24',
        'eth' => '0xF5e40b2A12b522a7AF4c0b20209FabD7DABBe608',
        'ltc' => 'LcYourLitecoinWalletAddressHere12345',
        'sol' => '4xor6WRhgpwRsgCErQ3U4vVTdDunBWogYhMdrMUPfD8y',
        'usdt' => 'TQyuZjpa5yagEZtqDWXCtsund9YMFtytp7',
        'xrp' => 'r9VdhfhnSKWdTMTjdFsoyTrdgVScupN1XV',
        'bnb' => '0xF5e40b2A12b522a7AF4c0b20209FabD7DABBe608'
    ];

    if (!array_key_exists($crypto, $crypto_ids)) {
        die("Invalid cryptocurrency.");
    }

    // ✅ More reliable price fetch using cURL
    $api_url = "https://api.coingecko.com/api/v3/simple/price?ids=" . $crypto_ids[$crypto] . "&vs_currencies=usd";
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $api_url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $response = curl_exec($ch);
    if (curl_errno($ch)) {
        die("Failed to fetch price. Error: " . curl_error($ch));
    }
    curl_close($ch);

    $data = json_decode($response, true);
    if (!$data || !isset($data[$crypto_ids[$crypto]]['usd'])) {
        $_SESSION['message'] = "Unable to fetch current price. Please try again later.";
        header("Location: invest.php");
        exit;
    }

    $crypto_price = $data[$crypto_ids[$crypto]]['usd'];
    $amount_crypto = $amount_usd / $crypto_price;
    $wallet_address = $walletAddresses[$crypto];

    // Save payment data to payments.json
    $payments_file = __DIR__ . '/payments.json';
    $payments = [];
    if (file_exists($payments_file)) {
        $payments = json_decode(file_get_contents($payments_file), true) ?: [];
    }
    $payment_id = count($payments) > 0 ? max(array_column($payments, 'id')) + 1 : 1;
    $payments[] = [
        'id' => $payment_id,
        'user_id' => $user_id,
        'amount_usd' => $amount_usd,
        'crypto' => $crypto,
        'amount_crypto' => $amount_crypto,
        'wallet_address' => $wallet_address,
        'created_at' => date('Y-m-d H:i:s'),
        'status' => 'pending'
    ];
    file_put_contents($payments_file, json_encode($payments, JSON_PRETTY_PRINT));

    $_SESSION['payment_id'] = $payment_id;
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <title>Complete Your Deposit</title>
        <script src="https://cdn.jsdelivr.net/npm/qrious@4.0.2/dist/qrious.min.js"></script>
        <style>
            body { background: #121212; color: #eee; font-family: sans-serif; padding: 20px; text-align: center; }
            .wallet { background: #222; padding: 12px; border-radius: 5px; font-family: monospace; user-select: all; cursor: pointer; }
            .copy-btn { background: #d4af37; color: #000; padding: 8px 14px; border: none; border-radius: 5px; margin-top: 10px; cursor: pointer; }
            .timer { font-size: 20px; margin-top: 15px; }
        </style>
    </head>
    <body>
        <h1>Deposit <?= strtoupper($crypto) ?></h1>
        <p>Please send exactly <strong><?= number_format($amount_crypto, 8) ?> <?= strtoupper($crypto) ?></strong><br> (~ $<?= number_format($amount_usd, 2) ?> USD)</p>

        <div class="wallet" id="walletAddress"><?= $wallet_address ?></div>
        <button class="copy-btn" onclick="copyToClipboard()">Copy Address</button>
        <canvas id="qrCode" style="margin-top: 20px;"></canvas>
        <div class="timer" id="timer">Time left: 15:00</div>

        <script>
            const qr = new QRious({
                element: document.getElementById("qrCode"),
                value: document.getElementById("walletAddress").textContent,
                size: 200
            });

            function copyToClipboard() {
                const text = document.getElementById("walletAddress").textContent;
                navigator.clipboard.writeText(text).then(() => alert("Address copied to clipboard!"));
            }

            let seconds = 900;
            const timerEl = document.getElementById("timer");
            const countdown = setInterval(() => {
                const min = String(Math.floor(seconds / 60)).padStart(2, '0');
                const sec = String(seconds % 60).padStart(2, '0');
                timerEl.textContent = `Time left: ${min}:${sec}`;
                seconds--;
                if (seconds < 0) {
                    clearInterval(countdown);
                    alert("Payment session expired. Please deposit again.");
                    window.location.href = 'dashboard.php';
                }
            }, 1000);
        </script>

        <form action="upload_proof.php" method="post" enctype="multipart/form-data" style="margin-top: 30px;">
            <p>Please upload proof of payment (image or PDF):</p>
            <input type="file" name="proof_file" accept=".jpg,.jpeg,.png,.pdf" required>
            <input type="hidden" name="payment_id" value="<?= $payment_id ?>">
            <button class="copy-btn" type="submit">Upload Proof</button>
        </form>
    </body>
    </html>

    <?php
    exit;
}

// Initial page load or after amount input
$message = $_SESSION['message'] ?? '';
unset($_SESSION['message']);

// Fetch user balance from users.json
$users = get_users();
$user = null;
foreach ($users as $u) {
    if ($u['id'] == $user_id) {
        $user = $u;
        break;
    }
}
$balance = $user['balance'] ?? 0.0;

// Get currency investment options from investment_assets.json
$all_assets = get_investment_assets();
$assets = array_filter($all_assets, fn($a) => ($a['status'] ?? '') === 'active');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link href="https://fonts.googleapis.com/css2?family=Inter&display=swap" rel="stylesheet">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Deposit Funds</title>
    <style>
        body { background: #121212; color: #eee; font-family: sans-serif; padding: 40px; text-align: center; }
        form { max-width: 400px; margin: auto; }
        input, select, button {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }
        button {
            background: #d4af37;
            color: #121212;
            font-weight: bold;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <h1>Deposit Funds</h1>
    <?php if ($message): ?>
        <p style="color: red;"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if (!isset($_SESSION['pending_deposit_amount'])): ?>
    <form method="post">
        <input type="number" step="0.01" min="1" name="amount_usd" placeholder="Amount in USD" required>
        <button type="submit">Continue</button>
    </form>
    <?php else: ?>
    <form method="post">
        <p>Amount: $<?= number_format($_SESSION['pending_deposit_amount'], 2) ?></p>
        <select name="crypto" required>
          <option value="btc">Bitcoin (BTC)</option>
          <option value="eth">Ethereum (ETH)</option>
          <option value="ltc">Litecoin (LTC)</option>
          <option value="sol">Solana (SOL)</option>
          <option value="usdt">Tether USDT (TRC20)</option>
          <option value="xrp">Ripple (XRP)</option>
          <option value="bnb">Binance Coin (BNB)</option>
        </select>
        <button type="submit">Proceed to Payment</button>
    </form>
    <?php endif; ?>

    <?php /*
<hr style="margin: 40px 0; border: 0; height: 1px; background: #333;">
<h2>Or Invest in Currency Assets</h2>
<p style="font-size: 15px;">Available Balance: <strong style="color: #1bff00;">$<?= number_format($balance, 2) ?></strong></p>

<form method="post" action="process_asset_invest.php">
    <select name="asset_id" required>
        <option value="">-- Select Asset --</option>
        <?php while ($a = $assets->fetch_assoc()): ?>
            <option value="<?= $a['id'] ?>">
                <?= htmlspecialchars($a['name']) ?> (Return: <?= $a['return_percent'] ?>%)
            </option>
        <?php endwhile; ?>
    </select>
    <input type="number" step="0.01" min="1" max="<?= $balance ?>" name="amount" placeholder="Amount to Invest (USD)" required>
    <button type="submit">Start Investment</button>
</form>
*/ ?>

</body>
</html>
<?php ob_end_flush(); ?>
