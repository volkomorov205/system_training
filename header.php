<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Training Platform</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .card { transition: transform 0.3s; }
        .card:hover { transform: translateY(-5px); }
        .navbar { box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .progress-bar { background-color: #2c3e50; }
    </style>
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary mb-4">
    <div class="container">
        <a class="navbar-brand" href="index.php">Обучение персонала</a>
        <div class="navbar-nav">
            <?php if(isset($_SESSION['user_id'])): ?>
                <span class="nav-item text-white me-3"><?= $_SESSION['name'] ?></span>
                <a class="nav-item btn btn-light btn-sm" href="logout.php">Выход</a>
            <?php else: ?>
                <a class="nav-link" href="login.php">Вход</a>
                <a class="nav-link" href="register.php">Регистрация</a>
            <?php endif; ?>
        </div>
    </div>
</nav>
<div class="container">