<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start(); // Prevents early output blocking header()
session_start();
require_once 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$plans = [
    'starter' => ['min' => 500, 'percent' => 5, 'duration' => 1],
    'advanced' => ['min' => 5000, 'percent' => 15, 'duration' => 3],
    'pro' => ['min' => 20000, 'percent' => 30, 'duration' => 7],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['plan'])) {
    $plan = $_POST['plan'];
    if (!isset($plans[$plan])) {
        $_SESSION['message'] = "Invalid investment plan.";
        header("Location: dashboard.php");
        exit;
    }

    $stmt = $conn->prepare("SELECT balance FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->bind_result($balance);
    $stmt->fetch();
    $stmt->close();

    $min = $plans[$plan]['min'];
    if ($balance < $min) {
        $_SESSION['message'] = "Insufficient balance for $plan plan.";
        header("Location: dashboard.php");
        exit;
    }

    $amount = $min;
    $percent = $plans[$plan]['percent'];
    $days = $plans[$plan]['duration'];

    $end_time = date('Y-m-d H:i:s', strtotime("+$days days"));

    $conn->begin_transaction();

    $stmt = $conn->prepare("INSERT INTO investments (user_id, amount, plan, return_percent, end_time) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("idsss", $user_id, $amount, $plan, $percent, $end_time);
    $stmt->execute();

    $stmt = $conn->prepare("UPDATE users SET balance = balance - ? WHERE id = ?");
    $stmt->bind_param("di", $amount, $user_id);
    $stmt->execute();

    $conn->commit();

    $_SESSION['message'] = "You successfully invested in the $plan plan.";
    header("Location: dashboard.php");
    exit;
}
ob_end_flush();
?>
