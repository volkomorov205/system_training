<?php
include 'config.php';
checkAdmin();

$course_id = (int)$_GET['id'];

// Добавление вопроса
if(isset($_POST['add_question'])) {
    try {
        $question_text = sanitize($_POST['question_text']);
        $question_type = sanitize($_POST['question_type']);
        
        if(empty(trim($question_text))) {
            throw new Exception("Текст вопроса не может быть пустым");
        }

        $options = NULL;
        $correct_answer = NULL;

        if($question_type !== 'text') {
            // Валидация опций
            if(empty(trim($_POST['options']))) {
                throw new Exception("Необходимо указать варианты ответов");
            }
            
            $raw_options = explode("\n", trim($_POST['options']));
            $clean_options = array_map('trim', $raw_options);
            $options = json_encode($clean_options, JSON_UNESCAPED_UNICODE);
            
            // Валидация правильных ответов
            if(empty(trim($_POST['correct_answer']))) {
                throw new Exception("Укажите правильные ответы");
            }
            
            $correct = array_map('intval', explode(',', $_POST['correct_answer']));
            $correct_answer = json_encode($correct);
        }

        $query = sprintf(
            "INSERT INTO questions (course_id, question_text, question_type, options, correct_answer)
            VALUES (%d, '%s', '%s', %s, %s)",
            $course_id,
            $question_text,
            $question_type,
            $options ? "'$options'" : "NULL",
            $correct_answer ? "'$correct_answer'" : "NULL"
        );

        if(!mysqli_query($conn, $query)) {
            throw new Exception(mysqli_error($conn));
        }
        
        $_SESSION['success'] = "Вопрос успешно добавлен";
        header("Location: course_edit.php?id=$course_id");
        exit();
        
    } catch (Exception $e) {
        $error = $e->getMessage();
    }
}

// Удаление вопроса
if(isset($_GET['delete_question'])) {
    $question_id = (int)$_GET['delete_question'];
    mysqli_query($conn, "DELETE FROM questions WHERE id = $question_id");
    header("Location: course_edit.php?id=$course_id");
    exit();
}

$course = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM courses WHERE id = $course_id"));
$questions = mysqli_query($conn, "SELECT * FROM questions WHERE course_id = $course_id");
include 'header.php';
?>

<div class="container">
    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>
    
    <div class="card mb-4">
        <div class="card-body">
            <h4>Редактирование курса: <?= $course['title'] ?></h4>
            
            <form method="POST">
                <div class="mb-3">
                    <textarea name="question_text" class="form-control" 
                        placeholder="Текст вопроса" required rows="3"></textarea>
                </div>
                
                <div class="row mb-3">
                    <div class="col-md-4">
                        <select name="question_type" class="form-select" id="questionType">
                            <option value="single">Один ответ</option>
                            <option value="multiple">Несколько ответов</option>
                            <option value="text">Текстовый ответ</option>
                        </select>
                    </div>
                </div>

                <div id="optionsSection" style="display: none;">
                    <div class="mb-3">
                        <label class="form-label">Варианты ответов (каждый с новой строки):</label>
                        <textarea name="options" class="form-control" rows="4"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Правильные ответы (номера через запятую):</label>
                        <input type="text" name="correct_answer" class="form-control">
                        <small class="text-muted">Пример: 1,3</small>
                    </div>
                </div>

                <button type="submit" name="add_question" class="btn btn-primary">
                    Добавить вопрос
                </button>
            </form>
        </div>
    </div>

    <div class="card">
        <div class="card-body">
            <h5>Список вопросов</h5>
            <?php while($question = mysqli_fetch_assoc($questions)): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h6><?= $question['question_text'] ?></h6>
                    <p class="text-muted">Тип: <?= $question['question_type'] ?></p>
                    
                    <?php if($question['question_type'] !== 'text'): ?>
                        <div class="options">
                            <?php 
                            $options = json_decode($question['options']);
                            foreach($options as $index => $option): 
                            ?>
                                <div class="form-check">
                                    <input class="form-check-input" 
                                        type="<?= $question['question_type'] === 'single' ? 'radio' : 'checkbox' ?>" 
                                        disabled>
                                    <label class="form-check-label">
                                        <?= ($index + 1) ?>. <?= $option ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="mt-2">
                            <strong>Правильные ответы:</strong> 
                            <?= implode(', ', json_decode($question['correct_answer'])) ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-info">Текстовый ответ</div>
                    <?php endif; ?>
                    
                    <div class="mt-3">
                        <a href="?delete_question=<?= $question['id'] ?>&id=<?= $course_id ?>" 
                            class="btn btn-sm btn-danger"
                            onclick="return confirm('Удалить вопрос?')">
                            Удалить
                        </a>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const questionType = document.getElementById('questionType');
    const optionsSection = document.getElementById('optionsSection');
    
    function toggleOptions() {
        optionsSection.style.display = questionType.value === 'text' ? 'none' : 'block';
        
        // Добавляем обязательные поля
        const requiredFields = optionsSection.querySelectorAll('[name="options"], [name="correct_answer"]');
        requiredFields.forEach(field => {
            field.required = questionType.value !== 'text';
        });
    }
    
    questionType.addEventListener('change', toggleOptions);
    toggleOptions(); // Инициализация при загрузке
});
</script>

<?php include 'footer.php'; ?>