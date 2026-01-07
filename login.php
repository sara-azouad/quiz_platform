<?php
session_start();
require_once('db.php');

$error = "";
$success = "";

/* =========================
   Message après reset password
   ========================= */
if (isset($_GET['reset']) && $_GET['reset'] == 1) {
    $success = "Mot de passe modifié avec succès !";
}

/* =========================
   Traitement du formulaire
   ========================= */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $email = isset($_POST['email']) ? $conn->real_escape_string($_POST['email']) : '';
    $password = isset($_POST['password']) ? $_POST['password'] : '';

    if ($email === '' || $password === '') {
        $error = "Veuillez remplir tous les champs.";
    } else {

        $sql = "SELECT id, email, password, username, role 
                FROM users 
                WHERE email = '$email'";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {

            $user = $result->fetch_assoc();

            if (password_verify($password, $user['password'])) {

                /* =========================
                   Générer username si vide
                   ========================= */
                if (empty($user['username'])) {
                    $user['username'] = explode("@", $user['email'])[0];

                    $sql_update = "UPDATE users 
                                   SET username = '".$conn->real_escape_string($user['username'])."' 
                                   WHERE id = ".$user['id'];
                    $conn->query($sql_update);
                }

                /* =========================
                   Sessions
                   ========================= */
                $_SESSION['user_id']  = $user['id'];
                $_SESSION['email']    = $user['email'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role']     = $user['role'];

                /* =========================
                   Redirection selon le rôle
                   ========================= */
                if ($user['role'] === 'admin') {
                    header("Location: admin/admin_dashboard.php");
                } else {
                    header("Location: dashboard.php");
                }
                exit();

            } else {
                $error = "Email ou mot de passe incorrect.";
            }

        } else {
            $error = "Email ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="quiz.css">

    <?php if ($success): ?>
        <!-- Redirection automatique après 3 secondes -->
        <meta http-equiv="refresh" content="3;url=login.php">
    <?php endif; ?>
</head>

<body>

<div class="login-box">
    <h2>Accès au Quiz</h2>
    <p class="subtitle">Entrez vos informations pour commencer</p>

    <?php
    if ($success != "") {
        echo "<h2 style='text-align:center; color:green; margin-bottom:15px;'>$success</h2>";
        echo "<p style='text-align:center; font-size:14px;'>Vous allez être redirigé vers la page de connexion...</p>";
    }

    if ($error != "") {
        echo "<p class='error'>$error</p>";
    }
    ?>

    <form method="POST">
        <div class="input-group">
            <span class="icon">📧</span>
            <input type="email" name="email" placeholder="Email" required>
        </div>

        <div class="input-group">
            <span class="icon">🔒</span>
            <input type="password" name="password" placeholder="Mot de passe" required>
        </div>

        <button type="submit">Accéder au Quiz</button>

        <div class="forgot">
            <a href="forgot_password.php">Mot de passe oublié ?</a>
        </div>
    </form>
</div>

</body>
</html>
