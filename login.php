<?php
include 'config.php';

if(isset($_POST['login'])) {
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];
    
    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    if($user = mysqli_fetch_assoc($result)) {
        if(password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            header("Location: index.php");
        } else {
            $error = "Неверный пароль";
        }
    } else {
        $error = "Пользователь не найден";
    }
}
?>

<?php include 'header.php'; ?>
<div class="row justify-content-center mt-5">
    <div class="col-md-6">
        <div class="card shadow">
            <div class="card-body p-4">
                <h3 class="mb-4 text-center">Вход в систему</h3>
                <?php if(isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                <form method="POST">
                    <div class="mb-3">
                        <input type="email" name="email" class="form-control" placeholder="Email" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" name="password" class="form-control" placeholder="Пароль" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">Войти</button>
                </form>
            </div>
        </div>
    </div>
</div>
<?php include 'footer.php'; ?>