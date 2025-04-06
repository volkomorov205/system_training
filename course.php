<?php
include 'config.php';
checkAuth();

$course_id = (int)$_GET['id'];
$user_id = $_SESSION['user_id'];

// Проверка назначения курса
$assignment = mysqli_query($conn, 
    "SELECT * FROM assignments 
    WHERE user_id = $user_id AND course_id = $course_id"
);

if(mysqli_num_rows($assignment) === 0) {
    header("Location: index.php");
    exit();
}

// Обработка ответов
if(isset($_POST['submit_test'])) {
    $answers = [];
    $score = 0;
    
    $questions = mysqli_query($conn, 
        "SELECT * FROM questions WHERE course_id = $course_id"
    );
    
    while($question = mysqli_fetch_assoc($questions)) {
        $user_answer = $_POST['q'.$question['id']] ?? '';
        $correct = json_decode($question['correct_answer'], true);
        
        // Проверка ответа
        if($question['question_type'] === 'text') {
            $is_correct = (strtolower(trim($user_answer)) === strtolower(trim($correct[0]))) ? 1 : 0;
        } else {
            $user_answers = is_array($user_answer) ? $user_answer : [$user_answer];
            $is_correct = (array_values($user_answers) == array_values($correct)) ? 1 : 0;
        }
        
        $score += $is_correct;
        $answers[$question['id']] = $user_answer;
    }
    
    // Сохранение результата
    $total_questions = mysqli_num_rows($questions);
    $score_percent = round(($score / $total_questions) * 100);
    
    mysqli_query($conn, 
        "INSERT INTO results (user_id, course_id, score, answers) 
        VALUES ($user_id, $course_id, $score_percent, '".json_encode($answers)."')"
    );
    
    header("Location: index.php");
    exit();
}

$questions = mysqli_query($conn, 
    "SELECT * FROM questions WHERE course_id = $course_id"
);

include 'header.php';
?>

<div class="card">
    <div class="card-body">
        <form method="POST">
            <?php while($question = mysqli_fetch_assoc($questions)): ?>
            <div class="mb-4">
                <h5><?= $question['question_text'] ?></h5>
                
                <?php if($question['question_type'] === 'text'): ?>
                    <textarea name="q<?= $question['id'] ?>" class="form-control"></textarea>
                
                <?php else: 
                    $options = json_decode($question['options']);
                ?>
                    <div class="ms-3">
                        <?php foreach($options as $i => $option): ?>
                            <div class="form-check">
                                <input class="form-check-input" 
                                    type="<?= $question['question_type'] === 'single' ? 'radio' : 'checkbox' ?>" 
                                    name="q<?= $question['id'] ?><?= $question['question_type'] === 'multiple' ? '[]' : '' ?>" 
                                    value="<?= $i + 1 ?>">
                                <label class="form-check-label"><?= $option ?></label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php endwhile; ?>
            
            <button type="submit" name="submit_test" class="btn btn-primary">Завершить тест</button>
        </form>
    </div>
</div>

<?php include 'footer.php'; ?>