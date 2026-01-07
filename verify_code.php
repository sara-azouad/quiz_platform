<?php
require_once('db.php');

$email = $_GET['email'] ?? '';
$error = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $code = $conn->real_escape_string($_POST['code']);

    $sql = "SELECT id FROM users 
            WHERE email='$email' 
            AND reset_code='$code' 
            AND reset_expires > NOW()";

    $res = $conn->query($sql);

    if ($res && $res->num_rows > 0) {
        header("Location: reset_password.php?email=$email");
        exit();
    } else {
        $error = "Code invalide ou expiré.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="quiz.css">
  <title>Vérification du code</title>
</head>
<body>
<div class="login-box">
  <h2>Vérification du code</h2>
  <?php if ($error != "") { echo "<p class='error'>$error</p>"; } ?>

  <form method="POST">
    <div class="input-group">
      <span class="icon">🔑</span>
      <input type="text" name="code" placeholder="Code reçu" required>
    </div>
    <button type="submit">Vérifier</button>
  </form>
</div>
</body>
</html>
