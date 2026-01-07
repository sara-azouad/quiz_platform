<?php
session_start();

require_once('db.php');
include 'auth.php';

/* Vérification connexion */
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$user_id  = $_SESSION['user_id'];
$username = $_SESSION['username'];
$email    = $_SESSION['email'];

/* Infos utilisateur */
$sql_user = "SELECT * FROM users WHERE id = $user_id";
$user_result = $conn->query($sql_user);
$user = $user_result->fetch_assoc();

/* =========================
   Dernier score (SESSION → COOKIE)
   ========================= */
if (isset($_SESSION['last_score'])) {
    $last_score = $_SESSION['last_score'];
} elseif (isset($_COOKIE['last_score'])) {
    $last_score = $_COOKIE['last_score'];
} else {
    $last_score = 'Aucun quiz effectué';
}

/* =========================
   Dernier quiz pour réessayer
   ========================= */
$sql_last_quiz = "SELECT quiz_id FROM results WHERE user_id = $user_id ORDER BY id DESC LIMIT 1";
$result_last_quiz = $conn->query($sql_last_quiz);
$last_quiz_id = ($result_last_quiz && $result_last_quiz->num_rows > 0)
    ? $result_last_quiz->fetch_assoc()['quiz_id']
    : null;

/* Quiz complétés */
$sql_completed = "SELECT COUNT(*) AS completed FROM results WHERE user_id = $user_id";
$completed_result = $conn->query($sql_completed);
$completed = $completed_result->fetch_assoc()['completed'] ?? 0;

/* Tous les quizzes */
$sql_quizzes = "SELECT * FROM quizzes";
$quizzes_result = $conn->query($sql_quizzes);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Dashboard - Quiz</title>
<style>
body { font-family: Arial, sans-serif; background:#f0f2f5; margin:0; padding:0; }
.dashboard-container { max-width:1000px; margin:40px auto; padding:30px; background:#fff; border-radius:12px; box-shadow:0 4px 15px rgba(0,0,0,0.05); text-align:center; }
.welcome-message { font-size:24px; font-weight:bold; margin-bottom:25px; }
.user-info { display:flex; gap:20px; margin-bottom:35px; justify-content:center; flex-wrap:wrap; }
.info-item { background:#f8f9fa; padding:20px; border-radius:10px; flex:1; min-width:180px; border:1px solid #eee; }
.info-label { font-weight:bold; display:block; margin-bottom:8px; }
.last-score { font-size:24px; color:#4caf50; font-weight:bold; margin-bottom:25px; }
.quiz-categories { display:flex; justify-content:center; flex-wrap:wrap; gap:20px; margin-bottom:30px; }
.quiz-category { width:250px; background:#e3f2fd; padding:25px; border-radius:10px; cursor:pointer; transition:.3s; border:1px solid #d1e9ff; }
.quiz-category:hover { background:#d1e9ff; transform:translateY(-3px); }
.quiz-actions { display:flex; gap:15px; justify-content:center; flex-wrap:wrap; margin-bottom:30px; }
.quiz-button { padding:12px 25px; border:none; border-radius:6px; cursor:pointer; font-size:16px; }
.start-quiz { background:#4caf50; color:#fff; }
.retry-button { background:#2196f3; color:#fff; }
.logout-button { background:#f44336; color:#fff; }
a { text-decoration:none; color:inherit; }
</style>
</head>

<body>

<div class="dashboard-container">

    <h2 class="welcome-message">Bienvenue, <?= htmlspecialchars($username) ?> 👋</h2>

    <!-- Dernier score -->
    <div class="last-score">
        Dernier score : <?= htmlspecialchars($last_score) ?>
    </div>

    <div class="user-info">
        <div class="info-item">
            <span class="info-label">Email :</span>
            <?= htmlspecialchars($email) ?>
        </div>
        <div class="info-item">
            <span class="info-label">Quiz complétés :</span>
            <?= $completed ?>
        </div>
    </div>

    <!-- Réessayer dernier quiz -->
    <?php if ($last_quiz_id): ?>
        <div class="quiz-actions">
            <a href="takequiz.php?quiz_id=<?= $last_quiz_id ?>" class="quiz-button retry-button">
                Réessayer le dernier quiz
            </a>
        </div>
    <?php endif; ?>

    <!-- Liste des quiz -->
    <h3>Choisissez une catégorie :</h3>
    <div class="quiz-categories">
        <?php while ($quiz = $quizzes_result->fetch_assoc()): ?>
            <div class="quiz-category"
                 onclick="location.href='takequiz.php?quiz_id=<?= $quiz['id'] ?>'">
                <strong><?= htmlspecialchars($quiz['titre']) ?></strong><br>
                <small><?= htmlspecialchars($quiz['description']) ?></small>
            </div>
        <?php endwhile; ?>
    </div>

    <div class="quiz-actions">
        <button class="quiz-button start-quiz"
                onclick="alert('Sélectionnez un quiz pour commencer !')">
            Commencer
        </button>

        <a href="logout.php">
            <button class="quiz-button logout-button">Déconnexion</button>
        </a>
    </div>

</div>

</body>
</html>
