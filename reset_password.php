<?php
require_once('db.php');

$email = $_GET['email'] ?? '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new = password_hash($conn->real_escape_string($_POST['password']), PASSWORD_DEFAULT);

    $conn->query("UPDATE users 
                  SET password='$new', reset_code=NULL, reset_expires=NULL 
                  WHERE email='$email'");

    // Set success to true to show the message
    $success = true;
}
?>
<!DOCTYPE html>
<html>
<head>
  <link rel="stylesheet" href="quiz.css">
  <title>Nouveau mot de passe</title>

  <?php if ($success): ?>
  <script>
    // Redirect to login.php after 2 seconds
    setTimeout(function(){
      window.location.href = 'login.php';
    }, 2000);
  </script>
  <?php endif; ?>
</head>
<body>
<div class="login-box">
  <?php if ($success): ?>
      <h2 style="text-align:center; color:green; font-weight:600; margin-top:20px;">
          Mot de passe modifié avec succès !
      </h2>
      <p style="text-align:center; font-size:14px;">Redirection vers la page de connexion...</p>
  <?php else: ?>
      <h2>Nouveau mot de passe</h2>
      <form method="POST">
        <div class="input-group">
          <span class="icon">🔒</span>
          <input type="password" name="password" placeholder="Nouveau mot de passe" required>
        </div>
        <button type="submit">Changer</button>
      </form>
  <?php endif; ?>
</div>
</body>
</html>
