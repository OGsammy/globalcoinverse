<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start(); // Start output buffering
session_start();
require_once 'user_storage.php';

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (!$username) {
        $errors[] = "Username is required.";
    }
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Valid email is required.";
    }
    if (!$password) {
        $errors[] = "Password is required.";
    }
    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }
    if (strlen($password) < 6) {
        $errors[] = "Password must be at least 6 characters.";
    }

    if (empty($errors)) {
        $users = get_users();
        foreach ($users as $user) {
            if (strcasecmp($user['username'], $username) === 0 || strcasecmp($user['email'], $email) === 0) {
                $errors[] = "Username or email already taken.";
                break;
            }
        }

        if (empty($errors)) {
            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $balance = 0.00;
            $role = 'user';

            $new_user = [
                'id' => count($users) > 0 ? max(array_column($users, 'id')) + 1 : 1,
                'username' => $username,
                'email' => $email,
                'password_hash' => $password_hash,
                'balance' => $balance,
                'role' => $role
            ];

            $users[] = $new_user;

            if (save_users($users)) {
                $_SESSION['message'] = "Registration successful. Please log in.";
                header('Location: login.php');
                exit;
            } else {
                $errors[] = "Failed to save user data.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Register - Crypto Broker</title>
  <style>
    * {
      box-sizing: border-box;
    }
    body {
      margin: 0;
      background: linear-gradient(135deg, #0f111a, #1c1f2a);
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #eee;
      display: flex;
      justify-content: center;
      align-items: center;
      min-height: 100vh;
      padding: 20px;
    }
    .container {
      background: linear-gradient(145deg, #222533, #1a1d2b);
      padding: 30px;
      border-radius: 12px;
      max-width: 400px;
      width: 100%;
      box-shadow:
        0 4px 8px rgba(0, 0, 0, 0.4),
        0 8px 16px rgba(0, 0, 0, 0.3),
        0 12px 24px rgba(0, 0, 0, 0.2);
      border: 1px solid #2e2f41;
    }
    h2 {
      text-align: center;
      color: #d4af37;
      margin-bottom: 20px;
      letter-spacing: 1px;
    }
    input[type="text"],
    input[type="email"],
    input[type="password"] {
      width: 100%;
      padding: 12px 40px 12px 12px;
      margin-top: 8px;
      margin-bottom: 15px;
      border-radius: 5px;
      border: none;
      background: #2a2a2a;
      color: #eee;
      position: relative;
      font-size: 14px;
    }
    .password-container {
      position: relative;
    }
    .toggle-password {
      position: absolute;
      top: 50%;
      right: 10px;
      transform: translateY(-50%);
      cursor: pointer;
      color: #888;
      user-select: none;
    }
    button {
      width: 100%;
      padding: 12px;
      background: #d4af37;
      border: none;
      border-radius: 4px;
      color: #121212;
      font-weight: bold;
      font-size: 16px;
      cursor: pointer;
      letter-spacing: 1px;
      transition: background 0.3s ease;
    }
    button:hover {
      background: #b4942f;
    }
    .errors, .message {
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      font-size: 14px;
    }
    .errors {
      background: #c72c41;
    }
    .message {
      background: #1b8534;
    }
    .login-link {
      margin-top: 12px;
      text-align: center;
      font-size: 14px;
    }
    .login-link a {
      color: #d4af37;
      text-decoration: none;
      font-weight: 600;
    }
    .login-link a:hover {
      text-decoration: underline;
    }
    label {
      font-size: 14px;
      display: block;
      margin-top: 10px;
      margin-bottom: 5px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Create Account</h2>

    <?php if (!empty($errors)) : ?>
      <div class="errors">
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="registration.php" novalidate>
      <label for="username">Username</label>
      <input type="text" id="username" name="username" required value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" />

      <label for="email">Email</label>
      <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" />

      <label for="password">Password</label>
      <div class="password-container">
        <input type="password" id="password" name="password" required />
        <span class="toggle-password" onclick="togglePassword('password')">👁️</span>
      </div>

      <label for="confirm_password">Confirm Password</label>
      <div class="password-container">
        <input type="password" id="confirm_password" name="confirm_password" required />
        <span class="toggle-password" onclick="togglePassword('confirm_password')">👁️</span>
      </div>

      <button type="submit">Register</button>
    </form>

    <div class="login-link">
      Already have an account? <a href="login.php">Log in here</a>
    </div>
  </div>

  <script>
    function togglePassword(fieldId) {
      const input = document.getElementById(fieldId);
      input.type = input.type === 'password' ? 'text' : 'password';
    }
  </script>
</body>
</html>
<?php ob_end_flush(); ?>
