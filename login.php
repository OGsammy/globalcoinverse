<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);
ob_start(); // Prevents early output blocking header()
session_start();
require_once 'user_storage.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']);

    if (!$identity) {
        $errors[] = "Username or Email is required.";
    }

    if (!$password) {
        $errors[] = "Password is required.";
    }

    if (empty($errors)) {
        $user = find_user_by_identity($identity);

        if ($user !== null) {
            if (password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_username'] = $user['username'];
                $_SESSION['role'] = $user['role'];

                // Optionally store a cookie
                if ($remember) {
                    setcookie('remember_user', $identity, time() + (86400 * 30), "/"); // 30 days
                }

                header("Location: " . ($user['role'] === 'admin' ? 'admin_confirm.php' : 'dashboard.php'));
                exit;
            } else {
                $errors[] = "Invalid password.";
            }
        } else {
            $errors[] = "User not found.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Login - Crypto Broker</title>
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
  /* Soft multi-layered shadow for depth */
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
      font-size: 16px; /* 👈 Add this to prevent zoom */
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
    .register-link, .remember-me {
      margin-top: 12px;
      text-align: center;
      font-size: 14px;
    }
    .register-link a {
      color: #d4af37;
      text-decoration: none;
      font-weight: 600;
    }
    .register-link a:hover {
      text-decoration: underline;
    }
    label {
      font-size: 14px;
    }
    .remember-me {
      text-align: left;
      margin-top: -10px;
      margin-bottom: 10px;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Login</h2>

    <?php if (!empty($_SESSION['message'])): ?>
      <div class="message"><?= htmlspecialchars($_SESSION['message']) ?></div>
      <?php unset($_SESSION['message']); ?>
    <?php endif; ?>

    <?php if (!empty($errors)) : ?>
      <div class="errors">
        <ul>
          <?php foreach ($errors as $err): ?>
            <li><?= htmlspecialchars($err) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" action="login.php" novalidate>
      <label for="identity">Username or Email</label>
      <input type="text" id="identity" name="identity" required value="<?= htmlspecialchars($_POST['identity'] ?? ($_COOKIE['remember_user'] ?? '')) ?>" />

      <label for="password">Password</label>
      <div class="password-container">
        <input type="password" id="password" name="password" required />
        <span class="toggle-password" onclick="togglePassword()">👁️</span>
      </div>

      <div class="remember-me">
        <label><input type="checkbox" name="remember" <?= isset($_POST['remember']) ? 'checked' : '' ?>> Remember Me</label>
      </div>

      <button type="submit">Log In</button>
    </form>

    <div class="register-link">
      Don't have an account? <a href="registration.php">Register</a>
    </div>
  </div>

  <script>
    function togglePassword() {
      const passwordInput = document.getElementById("password");
      passwordInput.type = passwordInput.type === "password" ? "text" : "password";
    }
  </script>
</body>
</html>
<?php ob_end_flush(); ?>
