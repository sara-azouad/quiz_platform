<?php
//session
session_start();

//(ROLE_USER)
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'ROLE_USER') {
    header("Location: 403.php");
    exit();
}

require_once 'db.php';


$user_id = $_SESSION['user_id'];
$sql = "
    SELECT r.score, r.created_at, q.titre
    FROM results r
    JOIN quizzes q ON r.quiz_id = q.id
    WHERE r.user_id = $user_id
    ORDER BY r.created_at DESC
";

$result = $conn->query($sql);


?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Historique des quiz</title>
</head>
<body>
