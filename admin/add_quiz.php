<?php
require_once('../db.php');
include 'admin_auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $desc  = $_POST['description'];

    $conn->query("INSERT INTO quizzes (titre, description)
                  VALUES ('$titre','$desc')");

    header("Location: quizzes.php");
}
?>

<form method="post">
    <input name="titre" placeholder="Titre" required><br><br>
    <textarea name="description" placeholder="Description"></textarea><br><br>
    <button>Ajouter</button>
</form>
