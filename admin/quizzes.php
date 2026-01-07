<?php
require_once('../db.php');
include 'admin_auth.php';

$quizzes = $conn->query("SELECT * FROM quizzes");
?>

<h3>Liste des quiz</h3>
<a href="add_quiz.php">➕ Ajouter</a>

<table border="1">
<tr><th>ID</th><th>Titre</th><th>Actions</th></tr>

<?php while($q = $quizzes->fetch_assoc()): ?>
<tr>
    <td><?= $q['id'] ?></td>
    <td><?= htmlspecialchars($q['titre']) ?></td>
    <td>
        <a href="quiz_edit.php?id=<?= $q['id'] ?>">Modifier</a>
        <a href="quiz_delete.php?id=<?= $q['id'] ?>"
           onclick="return confirm('Supprimer ?')">Supprimer</a>
    </td>
</tr>
<?php endwhile; ?>
</table>
