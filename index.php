<?php
include 'config.php';
checkAuth();
include 'header.php';

if($_SESSION['role'] === 'admin') {
    // Админ-панель
    $courses = mysqli_query($conn, "SELECT * FROM courses");
    $users = mysqli_query($conn, "SELECT * FROM users");
    ?>
    <h3 class="mb-4">Панель администратора</h3>
    
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary mb-3">
                <div class="card-body">
                    <h5>Курсы</h5>
                    <h2><?= mysqli_num_rows($courses) ?></h2>
                    <a href="courses.php" class="text-white">Управление →</a>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-success mb-3">
                <div class="card-body">
                    <h5>Пользователи</h5>
                    <h2><?= mysqli_num_rows($users) ?></h2>
                    <a href="users.php" class="text-white">Управление →</a>
                </div>
            </div>
        </div>
    </div>
    
    <?php
} else {
    // Панель пользователя
    $user_id = $_SESSION['user_id'];
    $courses = mysqli_query($conn, 
        "SELECT c.*, r.score 
        FROM courses c
        LEFT JOIN assignments a ON c.id = a.course_id
        LEFT JOIN results r ON c.id = r.course_id AND r.user_id = $user_id
        WHERE a.user_id = $user_id"
    );
    ?>
    <h3 class="mb-4">Мои курсы</h3>
    <div class="row">
    <?php while($course = mysqli_fetch_assoc($courses)): ?>
        <div class="col-md-4 mb-4">
            <div class="card h-100">
                <div class="card-body">
                    <h5 class="card-title"><?= $course['title'] ?></h5>
                    <p class="card-text"><?= substr($course['description'], 0, 100) ?>...</p>
                    <?php if($course['score'] !== null): ?>
                        <div class="progress mb-3">
                            <div class="progress-bar" style="width: <?= $course['score'] ?>%"><?= $course['score'] ?>%</div>
                        </div>
                        <button class="btn btn-outline-secondary w-100" disabled>Пройдено</button>
                    <?php else: ?>
                        <a href="course.php?id=<?= $course['id'] ?>" class="btn btn-primary w-100">Начать курс</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
    </div>
    <?php
}

include 'footer.php';
?>