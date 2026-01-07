<?php
require_once('../db.php');
include 'admin_auth.php';

$id = $_GET['id'];
$conn->query("DELETE FROM quizzes WHERE id=$id");

header("Location: quizzes.php");
