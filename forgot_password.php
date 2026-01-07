<?php
require_once('db.php');

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $conn->real_escape_string($_POST['email']);

    $check = $conn->query("SELECT id FROM users WHERE email='$email'");

    if ($check && $check->num_rows > 0) {
        $code = rand(100000, 999999);
        $expires = date("Y-m-d H:i:s", strtotime("+10 minutes"));

        $conn->query("UPDATE users 
                      SET reset_code='$code', reset_expires='$expires' 
                      WHERE email='$email'");

        $subject = "Code de réinitialisation";
        $body = "Votre code de réinitialisation est : $code";
        $headers = "From: quiz@test.com";

        mail($email, $subject, $body, $headers);

        header("Location: verify_code.php?email=$email");
        exit();
    } else {
        $message = "Email introuvable.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="quiz.css">
  <title>Mot de passe oublié</title>
</head>
<body>
<div class="login-box">
  <h2>Mot de passe oublié</h2>
  <p class="subtitle"><?= $message ?></p>

  <form method="POST">
    <div class="input-group">
      <span class="icon">📧</span>
      <input type="email" name="email" placeholder="Email" required>
    </div>
    <button type="submit">Envoyer le code</button>
  </form>
</div>
</body>
</html>
