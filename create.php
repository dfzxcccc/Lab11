<?php
session_start();

if (!isset($_SESSION['is_logged_in']) || $_SESSION['is_logged_in'] !== true) {
    header('Location: login.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Створити завдання</title>
</head>
<body>
    <h2>Створення нового завдання</h2>
    <p>Сюди пускає тільки авторизованого користувача!</p>
    <a href="index.php">На головну</a>
</body>
</html>