<?php 
session_start();
include 'db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $quiz_id = (int) ($_POST['quiz_id'] ?? 0);
    $answers = $_POST['answers'] ?? []; // [question_id => selected_option]
    $save_score = isset($_POST['save_score']);

    $score = 0;
    $total_questions = count($answers);
    $review = [];

    // Calculer le score et préparer le review
    foreach ($answers as $question_id => $selected_option) {
        $question_id = (int)$question_id;
        $selected_option = (int)$selected_option;

        $sql = "SELECT question_text, correct_option FROM questions WHERE id = $question_id";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $is_correct = ($selected_option == $row['correct_option']);
            if ($is_correct) $score++;

            $review[] = [
                'questions' => $row['question_text'],
                'selected' => $selected_option,
                'correct' => $row['correct_option'],
                'is_correct' => $is_correct
            ];
        }
    }

    if ($save_score) {
        // Enregistrer le score dans la base
        $stmt = $conn->prepare("INSERT INTO results (user_id, quiz_id, score, total_questions) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiii", $user_id, $quiz_id, $score, $total_questions);
        $stmt->execute();
        $stmt->close();

        // Stocker dans session et cookie pour dashboard immédiat
        $_SESSION['last_score'] = "$score/$total_questions";
        setcookie("last_score", "$score/$total_questions", time() + 7*24*60*60, "/");

        // Rediriger vers dashboard
        header("Location: dashboard.php");
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Résultat du quiz</title>
<style>
body { font-family: Arial, sans-serif; background: #f0f2f5; margin:0; padding:0; }
.result-container { max-width: 700px; margin:50px auto; background:#fff; padding:30px; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05); text-align:center; }
.result-container h2 { font-size:28px; margin-bottom:20px; color:#333; }
.score { font-size:48px; font-weight:bold; margin-bottom:20px; color:#4caf50; }
.saved-msg { color: #4caf50; font-weight:bold; margin-bottom:20px; }
.question-review { text-align:left; margin-top:30px; }
.question-review p { margin:10px 0; }
.correct { color: green; font-weight:bold; }
.incorrect { color: red; font-weight:bold; }
.quiz-actions { display:flex; justify-content:center; gap:20px; margin-top:30px; }
.quiz-actions button, .quiz-actions a { padding:12px 25px; border-radius:8px; font-size:16px; border:none; cursor:pointer; text-decoration:none; color:#fff; }
.save-btn { background:#4caf50; }
.retry-btn { background:#f44336; }
.save-btn:hover, .retry-btn:hover { opacity:0.9; }
</style>
</head>
<body>

<div class="result-container">
    <h2>Résultat du quiz</h2>
    <div class="score"><?= $score ?> / <?= $total_questions ?></div>

    <div class="question-review">
        <h3>Revoir vos réponses :</h3>
        <?php foreach($review as $i => $q): ?>
            <?php $status_class = $q['is_correct'] ? 'correct' : 'incorrect'; ?>
            <?php $status_text = $q['is_correct'] ? 'Correct' : 'Incorrect (Réponse: '.$q['correct'].')'; ?>
            <p><strong><?= $i+1 ?>. <?= htmlspecialchars($q['questions']) ?></strong> - <span class="<?= $status_class ?>"><?= $status_text ?></span></p>
        <?php endforeach; ?>
    </div>

    <div class="quiz-actions">
        <!-- Save Score Form -->
        <form method="POST" action="submit_quiz.php">
            <input type="hidden" name="quiz_id" value="<?= $quiz_id ?>">
            <?php foreach($answers as $qid => $ans): ?>
                <input type="hidden" name="answers[<?= $qid ?>]" value="<?= $ans ?>">
            <?php endforeach; ?>
            <input type="hidden" name="save_score" value="1">
            <button type="submit" class="save-btn">Enregistrer votre score</button>
        </form>

        <!-- Retry Button -->
        <a href="takequiz.php?quiz_id=<?= $quiz_id ?>" class="retry-btn">Réessayer</a>
    </div>
</div>

</body>
</html>
