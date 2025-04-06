<?php
include 'config.php';
checkAdmin();

// Создание курса
if(isset($_POST['create_course'])) {
    $title = sanitize($_POST['title']);
    $description = sanitize($_POST['description']);
    $admin_id = $_SESSION['user_id'];
    
    mysqli_query($conn, "INSERT INTO courses (title, description, admin_id) VALUES ('$title', '$description', $admin_id)");
}

// Удаление курса
if(isset($_GET['delete'])) {
    $course_id = (int)$_GET['delete'];
    mysqli_query($conn, "DELETE FROM courses WHERE id = $course_id");
    header("Location: courses.php");
}

$courses = mysqli_query($conn, "SELECT * FROM courses");
include 'header.php';
?>

<h3 class="mb-4">Управление курсами</h3>

<!-- Форма создания курса -->
<div class="card mb-4">
    <div class="card-body">
        <form method="POST">
            <div class="row">
                <div class="col-md-5">
                    <input type="text" name="title" class="form-control" placeholder="Название курса" required>
                </div>
                <div class="col-md-5">
                    <input type="text" name="description" class="form-control" placeholder="Описание курса">
                </div>
                <div class="col-md-2">
                    <button type="submit" name="create_course" class="btn btn-success w-100">Создать</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Список курсов -->
<div class="card">
    <div class="card-body">
        <table class="table">
            <thead>
                <tr>
                    <th>Название</th>
                    <th>Описание</th>
                    <th>Действия</th>
                </tr>
            </thead>
            <tbody>
                <?php while($course = mysqli_fetch_assoc($courses)): ?>
                <tr>
                    <td><?= $course['title'] ?></td>
                    <td><?= $course['description'] ?></td>
                    <td>
                        <a href="course_edit.php?id=<?= $course['id'] ?>" class="btn btn-sm btn-primary">Редактировать</a>
                        <a href="?delete=<?= $course['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Удалить курс?')">Удалить</a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>