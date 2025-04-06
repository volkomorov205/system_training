<?php
include 'config.php';

if(isset($_POST['register'])) {
    $name = sanitize($_POST['name']);
    $email = sanitize($_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    
    if(mysqli_query($conn, "INSERT INTO users (name, email, password) VALUES ('$name', '$email', '$password')")) {
        $_SESSION['success'] = "Регистрация успешна! Войдите в систему";
        header("Location: login.php");
    } else {
        $error = "Ошибка регистрации: " . mysqli_error($conn);
    }
}
?>

<?php include 'header.php'; ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body p-4">
                <h3 class="mb-4 text-center">Регистрация</h3>
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" name="name" class="form-control" placeholder="Имя" required>
                    </div>
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Пароль" required>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary w-100">Зарегистрироваться</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>