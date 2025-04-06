<?php
include 'config.php';
checkAdmin();

// Назначение курса
if(isset($_POST['assign_course'])) {
    $user_id = (int)$_POST['user_id'];
    $course_id = (int)$_POST['course_id'];
    $assigned_by = $_SESSION['user_id'];
    
    // Проверка существования пользователя и курса
    $user_exists = mysqli_query($conn, "SELECT id FROM users WHERE id = $user_id");
    $course_exists = mysqli_query($conn, "SELECT id FROM courses WHERE id = $course_id");
    
    if(mysqli_num_rows($user_exists) === 0 || mysqli_num_rows($course_exists) === 0) {
        $_SESSION['error'] = "Пользователь или курс не найден";
    } else {
        // Проверка существующего назначения
        $exists = mysqli_query($conn, 
            "SELECT id FROM assignments 
            WHERE user_id = $user_id AND course_id = $course_id"
        );
        
        if(mysqli_num_rows($exists)) {
            $_SESSION['error'] = "Курс уже назначен пользователю";
        } else {
            mysqli_query($conn, 
                "INSERT INTO assignments (user_id, course_id, assigned_by) 
                VALUES ($user_id, $course_id, $assigned_by)"
            );
            $_SESSION['success'] = "Курс успешно назначен";
        }
    }
    header("Location: users.php");
    exit();
}

// Получение списка пользователей
$users = mysqli_query($conn, "SELECT * FROM users");
// Получение списка всех курсов
$courses = mysqli_query($conn, "SELECT * FROM courses");
$all_courses = mysqli_fetch_all($courses, MYSQLI_ASSOC);

include 'header.php';
?>

<?php if(isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?= $_SESSION['error'] ?></div>
    <?php unset($_SESSION['error']); ?>
<?php endif; ?>

<?php if(isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?= $_SESSION['success'] ?></div>
    <?php unset($_SESSION['success']); ?>
<?php endif; ?>

<div class="card">
    <div class="card-body">
        <h4>Управление пользователями</h4>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Имя</th>
                    <th>Email</th>
                    <th>Роль</th>
                    <th>Назначение курсов</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = mysqli_fetch_assoc($users)): ?>
                <tr>
                    <td><?= $user['name'] ?></td>
                    <td><?= $user['email'] ?></td>
                    <td>
                        <form method="POST" class="d-inline">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="role" class="form-select" onchange="this.form.submit()">
                                <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Админ</option>
                                <option value="user" <?= $user['role'] === 'user' ? 'selected' : '' ?>>Пользователь</option>
                            </select>
                        </form>
                    </td>
                    <td>
                        <form method="POST" class="mb-3">
                            <input type="hidden" name="user_id" value="<?= $user['id'] ?>">
                            <select name="course_id" class="form-select" required>
                                <option value="">Выберите курс</option>
                                <?php foreach($all_courses as $course): ?>
                                    <option value="<?= $course['id'] ?>"><?= $course['title'] ?></option>
                                <?php endforeach; ?>
                            </select>
                            <button type="submit" name="assign_course" class="btn btn-sm btn-primary mt-2">
                                Назначить курс
                            </button>
                        </form>
                        
                        <!-- Показать назначенные курсы -->
                        <?php 
                        $assigned = mysqli_query($conn, 
                            "SELECT c.title, c.id 
                            FROM assignments a
                            JOIN courses c ON a.course_id = c.id
                            WHERE a.user_id = {$user['id']}"
                        );
                        if(mysqli_num_rows($assigned) > 0): ?>
                            <div class="mt-2">
                                <small>Назначенные курсы:</small>
                                <ul class="list-unstyled">
                                    <?php while($a = mysqli_fetch_assoc($assigned)): ?>
                                        <li>
                                            <?= $a['title'] ?>
                                            <a href="remove_assignment.php?user_id=<?= $user['id'] ?>&course_id=<?= $a['id'] ?>" 
                                                class="text-danger ms-2"
                                                onclick="return confirm('Отменить назначение?')">
                                                ×
                                            </a>
                                        </li>
                                    <?php endwhile; ?>
                                </ul>
                            </div>
                        <?php endif; ?>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>