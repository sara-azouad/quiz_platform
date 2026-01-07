<?php
session_start();
require_once('db.php');

/* =========================
   1. Vérifier connexion
   ========================= */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

/* =========================
   2. Vérifier quiz_id
   ========================= */
if (!isset($_GET['quiz_id']) || !is_numeric($_GET['quiz_id'])) {
    die("Quiz introuvable.");
}

$quiz_id = (int) $_GET['quiz_id'];

/* =========================
   3. Récupérer questions
   ========================= */
$sql = "SELECT * FROM questions WHERE quiz_id = $quiz_id";
$result = $conn->query($sql);

if (!$result || $result->num_rows === 0) {
    die("Aucune question disponible pour ce quiz.");
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Quiz</title>
    <style>
        body { font-family: Arial; background:#f4f4f4; padding:20px; }
        .question { background:#fff; padding:15px; margin-bottom:15px; border-radius:5px; }
        button { padding:10px 20px; cursor:pointer; }
    </style>
</head>
<body>

<h2>Quiz</h2>

<form action="submit_quiz.php" method="POST">

    <!-- quiz_id envoyé -->
    <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">

    <?php
    $index = 1;
    while ($row = $result->fetch_assoc()):
    ?>
        <div class="question">
            <p><strong><?= $index ?>. <?= htmlspecialchars($row['question_text']) ?></strong></p>

            <?php for ($i = 1; $i <= 4; $i++): ?>
                <label>
                    <input type="radio"
                           name="answers[<?= $row['id'] ?>]"
                           value="<?= $i ?>"
                           required>
                    <?= htmlspecialchars($row["option$i"]) ?>
                </label><br>
            <?php endfor; ?>
        </div>
    <?php
        $index++;
    endwhile;
    ?>

    <button type="submit">Soumettre le quiz</button>
</form>

</body>
</html>
