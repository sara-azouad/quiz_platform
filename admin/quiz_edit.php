<?php
require_once('../db.php');
include 'admin_auth.php';

$id = $_GET['id'];
$quiz = $conn->query("SELECT * FROM quizzes WHERE id=$id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $desc  = $_POST['description'];

    $conn->query("UPDATE quizzes
                  SET titre='$titre', description='$desc'
                  WHERE id=$id");

    header("Location: quizzes.php");
}
?>

<form method="post">
    <input name="titre" value="<?= $quiz['titre'] ?>" required><br><br>
    <textarea name="description"><?= $quiz['description'] ?></textarea><br><br>
    <button>Modifier</button>
</form>
