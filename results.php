<?php
include 'config.php';
checkAdmin();

$results = mysqli_query($conn, 
    "SELECT r.*, u.name, c.title 
    FROM results r
    JOIN users u ON r.user_id = u.id
    JOIN courses c ON r.course_id = c.id"
);

include 'header.php';
?>

<div class="card">
    <div class="card-body">
        <h4>Результаты тестирования</h4>
        
        <table class="table">
            <thead>
                <tr>
                    <th>Пользователь</th>
                    <th>Курс</th>
                    <th>Результат</th>
                    <th>Дата</th>
                </tr>
            </thead>
            <tbody>
                <?php while($result = mysqli_fetch_assoc($results)): ?>
                <tr>
                    <td><?= $result['name'] ?></td>
                    <td><?= $result['title'] ?></td>
                    <td><?= $result['score'] ?>%</td>
                    <td><?= date('d.m.Y H:i', strtotime($result['completed_at'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</div>

<?php include 'footer.php'; ?>