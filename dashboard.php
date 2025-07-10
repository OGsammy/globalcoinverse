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
$username = $_SESSION['user_username'];

// Fetch user data
$users = get_users();
$user = null;
foreach ($users as $u) {
    if ($u['id'] == $user_id) {
        $user = $u;
        break;
    }
}

$balance = $user['balance'] ?? 0.0;
$withdrawable_balance = $user['withdrawable_balance'] ?? 0.0;
$last_message = $user['last_message'] ?? null;

// Safely assign message
$message = $_SESSION['message'] ?? $last_message ?? '';

if (!empty($last_message)) {
    $_SESSION['message'] = $last_message;
    // Clear last_message in user data
    foreach ($users as &$u) {
        if ($u['id'] == $user_id) {
            $u['last_message'] = null;
            break;
        }
    }
    save_users($users);
}
unset($_SESSION['message']);

// Fetch transactions for user, order by created_at desc, limit 5
$all_transactions = get_transactions();
$transactions = array_filter($all_transactions, fn($tx) => $tx['user_id'] == $user_id);
usort($transactions, fn($a, $b) => strtotime($b['created_at']) <=> strtotime($a['created_at']));
$transactions = array_slice($transactions, 0, 5);

// Fetch active investments for user
$all_investments = get_investments();
$investments = array_filter($all_investments, fn($inv) => $inv['user_id'] == $user_id && $inv['status'] === 'active');

// Fetch user's asset investments joined with investment assets
$all_user_asset_investments = get_user_asset_investments();
$all_investment_assets = get_investment_assets();
$asset_investments = [];
foreach ($all_user_asset_investments as $inv) {
    if ($inv['user_id'] == $user_id) {
        $asset = null;
        foreach ($all_investment_assets as $a) {
            if ($a['id'] == $inv['asset_id']) {
                $asset = $a;
                break;
            }
        }
        if ($asset) {
            // Use gain_loss from data if available, else calculate
            $gain_loss = $inv['gain_loss'] ?? null;
            $asset_investments[] = array_merge($inv, [
                'name' => $asset['name'],
                'return_percent' => $asset['return_percent'] ?? 0,
                'gain_loss' => $gain_loss,
            ]);
        }
    }
}

// Fetch copied traders for user
$all_copy_traders = get_copy_traders();
$all_copied_traders = []; // copied_traders table equivalent
// Since copied_traders table is not in JSON, we need to simulate it or create a file for it
// For now, assume copied_traders.json exists or create an empty array
$copied_traders_file = __DIR__ . '/copied_traders.json';
if (file_exists($copied_traders_file)) {
    $all_copied_traders = json_decode(file_get_contents($copied_traders_file), true) ?: [];
} else {
    $all_copied_traders = [];
}
$copying_traders = [];
foreach ($all_copied_traders as $cp) {
    if ($cp['user_id'] == $user_id) {
        foreach ($all_copy_traders as $ct) {
            if ($ct['id'] == $cp['trader_id']) {
                $copying_traders[] = [
                    'trader_id' => $ct['id'],
                    'username' => $ct['name'],
                    'roi_percent' => $ct['roi_percent'],
                ];
                break;
            }
        }
    }
}

// Fetch trades initiated by copy traders the user is copying
$all_copy_trades = get_copy_trades();
$trader_ids = array_map(fn($t) => $t['trader_id'], $copying_traders);
$copy_trades = [];
if (!empty($trader_ids)) {
    foreach ($all_copy_trades as $trade) {
        if (in_array($trade['trader_id'], $trader_ids)) {
            $asset = null;
            foreach ($all_investment_assets as $a) {
                if ($a['id'] == $trade['asset_id']) {
                    $asset = $a;
                    break;
                }
            }
            $trader = null;
            foreach ($all_copy_traders as $ct) {
                if ($ct['id'] == $trade['trader_id']) {
                    $trader = $ct;
                    break;
                }
            }
            if ($asset && $trader) {
                $copy_trades[] = array_merge($trade, [
                    'asset_name' => $asset['name'],
                    'trader_name' => $trader['name'],
                ]);
            }
        }
    }
} else {
    $copy_trades = [];
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
     <meta charset="UTF-8" />
    <title>Dashboard - Crypto Broker</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
   <link rel="stylesheet" href="CSS/style.css">
   <style>
    
        body { font-family: 'Segoe UI', sans-serif;background: #0c0c0c; color: #f5f5f5; margin: 0; padding: 0; }
        header {
    background: #111;
    padding: 16px 24px;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #333;

  }

  header div {
    font-size: 18px;
    font-weight: bold;
    color: #d4af37;
  }
       a.logout {
    text-decoration: none;
    color: #d4af37;
    font-weight: 500;
    border: 1px solid #d4af37;
    padding: 6px 14px;
    border-radius: 6px;
    transition: background 0.3s;
  }

  a.logout:hover {
    background: #d4af37;
    color: #111;
  }


        main { padding: 20px; max-width: 1100px; margin: 0 auto; display: flex; gap: 30px; flex-wrap: wrap; }
      .card {
    background: #161616;
    border: 1px solid #333;
    box-shadow: 0 0 15px rgba(212, 175, 55, 0.1);
    border-radius: 12px;
    padding: 25px;
    flex: 1 1 320px;
    min-width: 300px;
  }
       h2, h3 {
    color: #fff;
    color: #d4af37;
    margin-top: 0;
    margin-bottom: 15px;
    font-size:2rem;
   }
        .balance { font-size: 24px; font-weight: bold; color: #d4af37; }
        p {
    margin: 8px 0;
    color: #ccc;
  }

  
       

        button, .plan-btn { padding: 10px 16px;  background: #d4af37; color: #111; border: none; border-radius: 4px; cursor: pointer; font-weight: bold; margin-top: 10px; transition: background 0.3s; }
        button:hover, .plan-btn:hover { background: #f5d76e; }
        .message {
    background: #1b8534;
    color: #fff;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 10px;
    font-weight: 500;
  }
        table { width: 100%; margin-top: 10px; border-collapse: collapse; font-size: 14px; table-layout: auto; word-break: break-word;}
        th, td { padding: 10px 8px; border-bottom: 1px solid #333; text-align: center;  min-width: 80px;}
        th {
    background: #1b1b1b;
    color: #d4af37;
    font-weight:600;
  }

  .table-responsive {
  width: 100%;
  overflow-x: auto;
  -webkit-overflow-scrolling: touch;
}

@media (max-width: 600px) {
  table {
    font-size: 12px;
  }
  th, td {
    padding: 6px 4px;
  }
}
        .plan { border: 1px solid #d4af37; padding: 10px; border-radius: 6px; margin-top: 10px; }
        .plan.disabled { opacity: 0.4; pointer-events: none; }
       


         .bonus-alert {
    background: linear-gradient(90deg, #d4af37, #1bff00);
    color: #111;
    font-weight: bold;
    padding: 15px;
    text-align: center;
    position: relative;
    animation: slideIn 1s ease-out forwards;
    transform: translateY(-100%);
    opacity: 0;
    z-index: 1000;
    
  }

.bonus-alert.show {
    transform: translateY(0);
    opacity: 1;
}

.bonus-alert .close-btn {
    position: absolute;
    right: 20px;
    top: 12px;
    font-size: 20px;
    cursor: pointer;
    color: #111;
  }

@keyframes slideIn {
    from { transform: translateY(-100%); opacity: 0; }
    to { transform: translateY(0); opacity: 1; }
}


        .trader-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    justify-content: space-between;
}

.trader-card {
    background: #1e1e1e;
    border-radius: 8px;
    padding: 20px;
    box-shadow: 0 0 10px rgba(212, 175, 55, 0.1);
    flex: 1 1 300px;
    min-width: 300px;
    transition: transform 0.2s ease-in-out;
}

.trader-card:hover {
    transform: scale(1.02);
}

.trader-details {
    margin: 10px 0;
}

.roi {
  color: #1bff00;
    font-weight: bold;
}

.followers {
    color: #ffcc00;
}

.plan-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
}

.plan {
    background: #1e1e1e;
    border-radius: 8px;
    padding: 20px;
   box-shadow: 0 0 10px rgba(212, 175, 55, 0.1);
    flex: 1 1 300px;
    min-width: 300px;
    transition: transform 0.2s ease-in-out;
}

.plan:hover {
    transform: scale(1.02);
}

.return {
   color: #1bff00;
    font-weight: bold;
}

.min {
    color:#ffcc00;
}

.icon-up { color: green; } .icon-down { color: red; }

 input[type="number"], input[type="text"], select {
    padding: 10px;
    border-radius: 6px;
    background: #1e1e1e;
    border: 1px solid #444;
    color: #eee;
    width: 100%;
    max-width: 400px;
  }

  label {
    display: block;
    margin-bottom: 5px;
    font-weight: 500;
    color:#aaa;
  }

  @media(max-width:360px){
  body{
    width: 100%;
    overflow-x:none;
  }
 
  }
 @media (max-width: 480px) {
  #investment-plans {
    flex-direction: column;
    gap: 15px;
  }
  #investment-plans .plan {
    flex: 1 1 100%;
    min-width: auto;
    max-width: 100%;
  }
}

  @media (max-width: 600px) {
  #copy-trading {
    padding: 18px 21px;
  }

  #copy-trading h2 {
    font-size: 1.6rem;
    margin-bottom: 10px;
  }

  .trader-container {
    flex-direction: column;
    gap: 15px;
  }

  .trader-card {
    flex: 1 1 100%;
    min-width: auto;
    padding: 15px;
  }

  .trader-card img {
    width: 50px !important;
    height: 50px !important;
    margin-bottom: 8px !important;
  }

  .trader-card strong {
    display: block;
    font-size: 1.2rem;
    margin-bottom: 6px;
  }

  .trader-details p {
    font-size: 0.9rem;
    margin: 4px 0;
  }

  .trader-details span.roi,
  .trader-details span.followers {
    font-weight: 600;
  }

  /* Buttons container adjustments */
  .trader-card > div[style*="display: flex"] {
    flex-direction: column;
    gap: 8px;
  }

  .plan-btn {
    width: 100%;
    padding: 12px 0;
    font-size: 1rem;
    border-radius: 6px;
  }
}

#withdrawal-request {
  max-width: 480px;     /* limit width on bigger screens */
  margin: 20px auto;    /* center horizontally */
  padding: 20px;
  box-sizing: border-box;
}

/* Make form and inputs take full width inside the container */
#withdrawal-request form {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

#withdrawal-request input,
#withdrawal-request select,
#withdrawal-request button {
  width: 100%;
  max-width: 100%;
  box-sizing: border-box;
}

/* Your existing media queries will override styles for smaller screens */
@media (max-width: 600px) {
  #withdrawal-request {
    padding: 15px 20px;
    max-width: 100%;   /* Let container fill smaller screens */
  }

  #withdrawal-request h2 {
    font-size: 1.8rem;
  }

  #withdrawal-request form {
    flex-direction: column !important;
    gap: 12px !important;
  }

  #withdrawal-request input,
  #withdrawal-request select,
  #withdrawal-request button {
    width: 100% !important;
    max-width: 100% !important;
    font-size: 1rem;
  }

  #withdrawal-request button {
    font-size: 1.1rem;
    padding: 14px 0;
  }
}

@media (max-width: 360px) {
  #withdrawal-request h2 {
    font-size: 1.5rem;
  }
}


@media (max-width: 600px) {
  header,
  .bonus-alert {
    position: relative;
    left: 0;
    right: 0;
    width: 100%;
    max-width: 100%;
    margin: 0;
    box-sizing: border-box;
    padding-left: 16px;
    padding-right: 16px;
    z-index: 10;
  }
}







    </style>
</head>
<body>
<header>
    <div>Welcome, <?=htmlspecialchars($username)?>!</div>
    <a class="logout" href="logout.php">Logout</a>
</header>

<!-- 🎁 Bonus Alert Message -->
<div class="bonus-alert" id="bonusAlert">
    🎁 Get <strong>15% bonus</strong> on your <strong>first deposit</strong>! Start investing smartly today.
    <span class="close-btn" onclick="dismissBonus()">×</span>
</div>


<main>
    
     <section class="card" style="text-align: center;">
  <?php if ($message): ?>
      <div class="message"><?= htmlspecialchars($message) ?></div>
  <?php endif; ?>  

  <div class="balance-card" style="background: #161616; border: 1px solid #333; border-radius: 12px; padding: 30px 20px; box-shadow: 0 0 15px rgba(212, 175, 55, 0.15); max-width: 400px; margin: 0 auto;">
    <h2 style="color: #aaa; font-size: 18px;">
      <i class="fas fa-wallet icon" style="color: #d4af37; margin-right: 8px;"></i> Account Overview
      <i id="toggleBalance" class="fas fa-eye" style="margin-left: 10px; cursor: pointer; color: #d4af37;" title="Hide/Show Balance"></i>
    </h2>

    <div class="username" style="font-size: 22px; font-weight: 600; color: #fff; margin: 12px 0;">
      <?= htmlspecialchars($username) ?>
    </div>

    <div class="balance-label" style="font-size: 15px; color: #bbb; margin-bottom: 6px;">Available Balance</div>
    <div id="availableBalance" class="balance-amount" style="font-size: 36px; font-weight: bold; color: #d4af37;">
      $<?= number_format($balance, 2) ?>
    </div>

    <div class="balance-label" style="font-size: 14px; color: #aaa; margin-top: 10px;">Withdrawable Balance</div>
    <div id="withdrawableBalance" style="font-size: 18px; font-weight: 500; color: #d4af37; margin-top: 6px;">
      $<?= number_format($withdrawable_balance, 2) ?>
    </div>
  </div>





       <br><br> <h2>Deposit Now</h2>
        <form method="post" action="invest.php">
            <input type="number" step="0.01" name="amount_usd" placeholder="Amount in USD" min="1" required />
            <button type="submit">Deposit</button>
        </form>

        <br><section class="card" id="withdrawal-request">
    <h2>Request a Withdrawal</h2>

<?php if ($withdrawable_balance > 0): ?>
    <form method="post" action="withdraw.php" style="display: flex; flex-direction: column; gap: 12px;">

        <div>
            <label for="amount" style="font-weight: bold;">Amount to Withdraw ($)</label><br>
            <input type="number" name="amount" id="amount" min="1" max="<?= $withdrawable_balance ?>" step="0.01" required>
        </div>

        <div>
            <label for="method" style="font-weight: bold;">Withdrawal Method</label><br>
            <select name="method" id="method" required onchange="adjustValidation()">
                <option value="">Select Cryptocurrency</option>
                <option value="BTC">Bitcoin (BTC)</option>
                <option value="ETH">Ethereum (ETH)</option>
                <option value="USDT">Tether (USDT)</option>
                <option value="BNB">Binance Coin (BNB)</option>
            </select>
        </div>

        <div>
            <label for="details" style="font-weight: bold;">Wallet Address</label><br>
           <input type="text" name="details" id="details" required>

        </div>

        <button type="submit">Submit Withdrawal Request</button>
    </form>
     <script>
function adjustValidation() {
    const method = document.getElementById('method').value;
    const detailsInput = document.getElementById('details');

    switch (method) {
        case 'BTC':
            // Updated BTC regex for legacy + Bech32
            detailsInput.pattern = "^(bc1)[0-9a-z]{39,59}$|^[13][a-km-zA-HJ-NP-Z1-9]{25,34}$";
            detailsInput.title = "Enter a valid Bitcoin (BTC) address starting with 1, 3, or bc1.";
            break;
        case 'ETH':
        case 'USDT':
            detailsInput.pattern = "^0x[a-fA-F0-9]{40}$";
            detailsInput.title = "Enter a valid Ethereum or USDT (ERC-20) address starting with 0x.";
            break;
        case 'BNB':
            detailsInput.pattern = "^bnb[0-9a-z]{39}$";
            detailsInput.title = "Enter a valid Binance Coin (BNB) address starting with bnb.";
            break;
        default:
            detailsInput.pattern = ".*";
            detailsInput.title = "Enter a wallet address.";
    }
}

</script>

<?php else: ?>
    <p style="color: red;">🚫 You currently have no withdrawable balance</p>
<?php endif; ?>



</section>


        <h2>Recent Transactions</h2>
        <?php if (count($transactions) > 0): ?>
             
            <table>
                <thead><tr><th>Amount</th><th>Type</th><th>Date</th></tr></thead>
                <tbody>
                <?php foreach ($transactions as $tx): ?>
                    <tr>
                        <td>$<?=number_format($tx['amount'], 2)?></td>
                        <td><?=htmlspecialchars(ucfirst($tx['type']))?></td>
                        <td><?=htmlspecialchars($tx['created_at'])?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
               
        <?php else: ?>
            <p>No transactions found.</p>
        <?php endif; ?>
    </section>

    <section class="card" id="chart-container">
        <h2>Live Bitcoin Price (USD)</h2>
        <canvas id="btcChart" width="600" height="320"></canvas>
    </section>

   <section class="card"  id="copy-trading">
    <h2>Copy Trading</h2>
    <p>Select a top trader to automatically mirror their trades:</p>

    <div class="trader-container">
        <?php
        $traders = get_copy_traders();
        usort($traders, fn($a, $b) => $b['roi_percent'] <=> $a['roi_percent']);
        foreach ($traders as $trader):
        ?>
        <div class="trader-card">
<img src="<?= htmlspecialchars('avatars/' . basename($trader['avatar_url'] ?? '')) ?>" alt="Avatar" style="width:60px;height:60px;border-radius:50%;object-fit:cover;margin-bottom:10px;">
         <strong><?=htmlspecialchars($trader['name'])?></strong>

            <div class="trader-details">
                <p>ROI: <span class="roi"><?=number_format($trader['roi_percent'], 2)?>%</span></p>
                <p>Followers: <span class="followers"><?= htmlspecialchars($trader['followers']) ?></span></p>
                <p>Strategy: <?=htmlspecialchars($trader['strategy'])?></p>
            </div>
           <div style="margin-top: 15px; display: flex; gap: 10px; flex-wrap: wrap;">
    <form method="post" action="copy_trader.php">
        <input type="hidden" name="trader_id" value="<?= $trader['id'] ?>">
        <button type="submit" class="plan-btn">Start Copying</button>
    </form>
   

</div>

<div style="margin-top: 10px; display: flex; gap: 10px;">
    <?php if (!empty($trader['twitter_url'])): ?>
        <a href="<?= htmlspecialchars($trader['twitter_url']) ?>" target="_blank" title="Twitter/X">
            <i class="fab fa-x-twitter" style="color: #000000; font-size: 18px;"></i>
        </a>
    <?php endif; ?>
    <?php if (!empty($trader['instagram_url'])): ?>
        <a href="<?= htmlspecialchars($trader['instagram_url']) ?>" target="_blank" title="Instagram">
            <i class="fab fa-instagram" style="color: #e4405f; font-size: 18px;"></i>
        </a>
    <?php endif; ?>
    <?php if (!empty($trader['facebook_url'])): ?>
        <a href="<?= htmlspecialchars($trader['facebook_url']) ?>" target="_blank" title="Facebook">
            <i class="fab fa-facebook" style="color: #3b5998; font-size: 18px;"></i>
        </a>
    <?php endif; ?>
    <?php if (!empty($trader['tiktok_url'])): ?>
        <a href="<?= htmlspecialchars($trader['tiktok_url']) ?>" target="_blank" title="TikTok">
            <i class="fab fa-tiktok" style="color: #fff; font-size: 18px;"></i>
        </a>
    <?php endif; ?>
</div>


        </div>
        <?php endforeach; ?>
    </div>
</section>

<section class="card">
    <h2>Traders You're Copying</h2>
     <div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Trader</th>
                <th>ROI (%)</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($copying_traders as $trader): ?>
            <tr>
                <td><?= htmlspecialchars($trader['username']) ?></td>
                <td><?= number_format($trader['roi_percent'], 2) ?>%</td>
                <td>
                    <form method="post" action="stop_copying.php">
                        <input type="hidden" name="trader_id" value="<?= $trader['trader_id'] ?>">
                        <button type="submit" class="plan-btn">Stop Copying</button>
                    </form>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
   </div>
</section>




    <section class="card"   id="investment-plans">
    <h2>Investment Plans</h2>
    <form method="post" action="process_invest.php">
        <div class="plan-container">
            <?php
            $plans = [
                'starter' => ['label' => 'Starter Plan', 'percent' => 10, 'days' => 2, 'min' => 500],
                'silver' => ['label' => 'Silver Plan', 'percent' => 25, 'days' => 5, 'min' => 5000],
                'Gold' => ['label' => 'Gold plan', 'percent' => 30, 'days' => 7, 'min' => 50000],
                'platinum' => ['label' => 'Platinum plan', 'percent' => 70, 'days' => 10, 'min' => 100000],
                 
            ];
            foreach ($plans as $key => $plan):
                $disabled = $balance < $plan['min'];
            ?>
            <div class="plan <?= $disabled ? 'disabled' : '' ?>">
                <strong><?= $plan['label'] ?></strong>
                <p>Return: <span class="return"><?= $plan['percent'] ?>% in <?= $plan['days'] ?> days</span></p>
                <p>Min Investment: <span class="min">$<?= number_format($plan['min'], 2) ?></span></p>
                <button class="plan-btn" formaction="invest_options.php" formmethod="post" name="plan" value="<?= $key ?>">View Options</button>
            </div>
            <?php endforeach; ?>
        </div>
    </form>

    <?php if (!empty($investments)): ?>
    <h3 style="margin-top:20px;">Your Active Investments</h3>
     <div class="table-responsive">
    <table>
        <thead><tr><th>Plan</th><th>Amount</th><th>Return (%)</th><th>Ends At</th></tr></thead>
        <tbody>
        <?php foreach ($investments as $inv): ?>
            <tr>
                <td><?= ucfirst($inv['plan']) ?></td>
                <td>$<?= number_format($inv['amount'], 2) ?></td>
                <td><?= number_format($inv['return_percent'], 2) ?>%</td>
                <td><?= htmlspecialchars($inv['end_time']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
        </div>
    <?php endif; ?>
</section>

<?php if (!empty($asset_investments)): ?>
<section class="card">
    <h2>Your Currency Asset Investments</h2>
    <div class="table-responsive">
    <table>
        <thead>
            <tr>
                <th>Asset</th>
                <th>Invested Amount ($)</th>
                <th>Return (%)</th>
                <th>Status</th>
                <th>Gain/Loss</th>
                <th>Date</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($asset_investments as $inv): ?>
            <tr>
                <td><?= htmlspecialchars($inv['name'] ?? 'N/A') ?></td>
                <td>$<?= number_format($inv['amount'] ?? 0, 2) ?></td>
                <td><?= number_format($inv['return_percent'] ?? 0, 2) ?>%</td>
                <td><?= ucfirst($inv['status'] ?? 'unknown') ?></td>
                <td>
                    <?php if (isset($inv['gain_loss']) && $inv['gain_loss'] !== null && $inv['gain_loss'] !== ''): ?>
                        <?php if ($inv['gain_loss'] > 0): ?>
                            <span style="color: green;">&#9650; $<?= number_format($inv['gain_loss'], 2) ?></span> <!-- Upward arrow for gain -->
                        <?php elseif ($inv['gain_loss'] < 0): ?>
                            <span style="color: red;">&#9660; $<?= number_format(abs($inv['gain_loss']), 2) ?></span> <!-- Downward arrow for loss -->
                        <?php else: ?>
                            <span style="color: gray;">$0.00</span> <!-- No gain or loss -->
                        <?php endif; ?>
                    <?php else: ?>
                        <em>N/A</em>
                    <?php endif; ?>
                </td>
                <td><?= htmlspecialchars($inv['invested_at'] ?? $inv['created_at'] ?? 'N/A') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
 </div>
</section>
<?php endif; ?>

<section class="card">
<h2>Trades Initiated by Copy Trader</h2>
<div class="table-responsive">
<table>
    <thead>
        <tr>
            <th>Asset</th>
            <th>Amount</th>
            <th>Return %</th>
            <th>Status</th>
            <th>Gain/Loss</th>
            <th>Trader</th>
            <th>Invested At</th>
        </tr>
    </thead>
    <tbody>
    <?php if (!empty($copy_trades)): ?>
        <?php foreach ($copy_trades as $row): ?>
            <tr>
                <td><?= htmlspecialchars($row['asset_name']) ?></td>
                <td>$<?= number_format($row['amount'], 2) ?></td>
                <td><?= number_format($row['return_percent'], 2) ?>%</td>
                <td><?= htmlspecialchars($row['status']) ?></td>
               <td>
    <?php if (isset($row['gain_loss'])): ?>
        <?php if ($row['gain_loss'] >= 0): ?>
            <span style="color: green;">&#9650; $<?= number_format($row['gain_loss'], 2) ?></span> <!-- Upward arrow -->
        <?php else: ?>
            <span style="color: red;">&#9660; $<?= number_format(abs($row['gain_loss']), 2) ?></span> <!-- Downward arrow -->
        <?php endif; ?>
    <?php else: ?>
        <em>N/A</em>
    <?php endif; ?>
</td>


                <td><?= htmlspecialchars($row['trader_name']) ?></td>
                <td><?= htmlspecialchars($row['invested_at']) ?></td>
            </tr>
        <?php endforeach; ?>
    <?php else: ?>
        <tr><td colspan="7">No trades found for the copy traders you follow.</td></tr>
    <?php endif; ?>
    </tbody>
</table>
</div>
</section>




</main>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.getElementById('method').addEventListener('change', adjustValidation);
});
</script>

<script>
  // Wait 5 seconds, then fade out and remove the message
  setTimeout(() => {
    const msg = document.querySelector('.message');
    if (!msg) return;

    // Fade out
    msg.style.transition = 'opacity 0.5s ease';
    msg.style.opacity = '0';

    // Remove from DOM after fade out
    setTimeout(() => {
      msg.remove();
    }, 500);
  }, 5000);
</script>


<script>
  const toggleBtn = document.getElementById('toggleBalance');
  const availableBalance = document.getElementById('availableBalance');
  const withdrawableBalance = document.getElementById('withdrawableBalance');

  let isVisible = true;
  const originalAvailable = availableBalance.textContent;
  const originalWithdrawable = withdrawableBalance.textContent;

  toggleBtn.addEventListener('click', () => {
    isVisible = !isVisible;
    if (isVisible) {
      availableBalance.textContent = originalAvailable;
      withdrawableBalance.textContent = originalWithdrawable;
      toggleBtn.classList.remove('fa-eye-slash');
      toggleBtn.classList.add('fa-eye');
    } else {
      availableBalance.textContent = '••••••••';
      withdrawableBalance.textContent = '••••••••';
      toggleBtn.classList.remove('fa-eye');
      toggleBtn.classList.add('fa-eye-slash');
    }
  });
</script>

<script>

    

    async function fetchBalance() {
        try {
            const response = await fetch('get_balance.php');
            if (!response.ok) throw new Error('Network response was not ok');
            const data = await response.json();
            if (data.success) {
                // Update the balance div's text content (with $ sign)
                document.querySelector('.balance').textContent = '$' + parseFloat(data.balance).toFixed(2);
            } else {
                console.error('Failed to fetch balance:', data.message);
            }
        } catch (error) {
            console.error('Error fetching balance:', error);
        }
    }

    // Update balance every 10 seconds
    setInterval(fetchBalance, 10000);
const ctx = document.getElementById('btcChart').getContext('2d');
const data = {
    labels: [],
    datasets: [{
        label: 'BTC Price (USD)',
       backgroundColor: 'rgba(212, 175, 55, 0.3)', // light gold background with transparency
       borderColor: 'rgba(212, 175, 55, 1)',       // sharp gold border
        borderWidth: 2,
        fill: true,
        data: []
    }]
};
const config = {
    type: 'line',
    data: data,
    options: { responsive: true, scales: { y: { beginAtZero: false } } }
};
const btcChart = new Chart(ctx, config);

async function fetchBTC() {
    try {
        const res = await fetch('https://api.coingecko.com/api/v3/simple/price?ids=bitcoin&vs_currencies=usd');
        const json = await res.json();
        const price = json.bitcoin.usd;
        const now = new Date().toLocaleTimeString();

        if (data.labels.length > 20) {
            data.labels.shift();
            data.datasets[0].data.shift();
        }
        data.labels.push(now);
        data.datasets[0].data.push(price);
        btcChart.update();
    } catch (err) {
        console.error("Error fetching BTC price:", err);
    }
}

fetchBTC();
setInterval(fetchBTC, 10000);

// Bonus Alert Logic
window.addEventListener('DOMContentLoaded', () => {
    const alert = document.getElementById('bonusAlert');
    alert.classList.add('show');
    setTimeout(() => {
        if (alert) alert.style.display = 'none';
    }, 10000); // Hide after 10 seconds
});

function dismissBonus() {
    const alert = document.getElementById('bonusAlert');
    if (alert) alert.style.display = 'none';
}

</script>

<script>
function adjustValidation() {
    const method = document.getElementById("method").value;
    const details = document.getElementById("details");

    // Reset pattern and title first
    details.pattern = ".*";
    details.title = "Enter a valid wallet address.";

    switch (method) {
        case "BTC": // Bitcoin address length usually 26–35
            // Updated pattern to allow Bech32 addresses starting with bc1 and legacy addresses starting with 1 or 3
            details.pattern = "^(bc1)[0-9a-z]{25,90}$|^[13][a-km-zA-HJ-NP-Z1-9]{25,39}$";
            details.title = "Enter a valid BTC address (26–90 characters, starts with 1, 3, or bc1)";
            break;
        case "ETH": // Ethereum address: 42 chars, starts with 0x
            details.pattern = "^0x[a-fA-F0-9]{40}$";
            details.title = "Enter a valid Ethereum address (42 characters starting with 0x)";
            break;
        case "USDT":
        case "BNB": // Commonly follows similar ETH format (depends on chain)
            details.pattern = "^0x[a-fA-F0-9]{40}$";
            details.title = "Enter a valid address (42 characters starting with 0x)";
            break;
        default:
            details.pattern = ".*";
            details.title = "Enter a valid wallet address.";
            break;
    }
}
</script>

</body>
</html>
<?php ob_end_flush(); ?>
