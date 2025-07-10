<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start(); // Prevents early output blocking header()
session_start();
require_once 'data_storage.php';

if (!isset($_FILES['proof_file']) || !isset($_POST['payment_id'])) {
    die("Invalid request.");
}

$payment_id = intval($_POST['payment_id']);
$file = $_FILES['proof_file'];
$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$allowed = ['jpg', 'jpeg', 'png', 'pdf'];

if (!in_array(strtolower($ext), $allowed)) {
    die("Invalid file type.");
}

$filename = 'proof_' . time() . '_' . rand(1000, 9999) . '.' . $ext;
$destination = __DIR__ . "/uploads/" . $filename;

if (!move_uploaded_file($file['tmp_name'], $destination)) {
    die("Upload failed.");
}

// Update payments.json
$payments_file = __DIR__ . '/payments.json';
$payments = [];
if (file_exists($payments_file)) {
    $payments = json_decode(file_get_contents($payments_file), true) ?: [];
}
foreach ($payments as &$payment) {
    if ($payment['id'] == $payment_id) {
        $payment['proof_file'] = $filename;
        break;
    }
}
file_put_contents($payments_file, json_encode($payments, JSON_PRETTY_PRINT));

$_SESSION['message'] = "Proof uploaded successfully. Await admin confirmation.";
header("Location: dashboard.php");
exit;
?>
<?php ob_end_flush(); ?>
