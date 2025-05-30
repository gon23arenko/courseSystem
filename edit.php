<?php
$db = new mysqli('localhost', 'root', '', 'drum_school');

if ($db->connect_error) {
    die("Ошибка подключения: " . $db->connect_error);
}

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = intval($_GET['id']);

// Получение данных ученика
$student = $db->query("SELECT * FROM students WHERE id = $id")->fetch_assoc();
if (!$student) {
    header("Location: index.php");
    exit;
}

// Получение списка преподавателей
$teachers = $db->query("SELECT * FROM teachers");

// Обработка формы редактирования
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_student'])) {
    $full_name = $db->real_escape_string($_POST['full_name']);
    $teacher_id = intval($_POST['teacher_id']);
    $has_experience = $_POST['has_experience'] === 'Да' ? 'Да' : 'Нет';
    $age = intval($_POST['age']);
    $duration = intval($_POST['duration']);
    
    $query = "UPDATE students SET 
              full_name = '$full_name', 
              teacher_id = $teacher_id, 
              has_experience = '$has_experience', 
              age = $age, 
              course_duration_months = $duration 
              WHERE id = $id";
    $db->query($query);
    header("Location: index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Редактирование ученика</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        form { margin-bottom: 20px; background: #f9f9f9; padding: 15px; border-radius: 5px; }
        .form-group { margin-bottom: 10px; }
        label { display: inline-block; width: 150px; }
        input, select { padding: 5px; width: 200px; }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
        .back-btn { background: #2196F3; }
    </style>
</head>
<body>
    <h1>Редактирование ученика</h1>
    
    <form method="POST">
        <div class="form-group">
            <label for="full_name">ФИО ученика:</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($student['full_name']) ?>" required>
        </div>
        <div class="form-group">
            <label for="teacher_id">Преподаватель:</label>
            <select id="teacher_id" name="teacher_id" required>
                <?php while($teacher = $teachers->fetch_assoc()): ?>
                    <option value="<?= $teacher['id'] ?>" <?= $teacher['id'] == $student['teacher_id'] ? 'selected' : '' ?>>
                        <?= $teacher['full_name'] ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="has_experience">Музыкальный опыт:</label>
            <select id="has_experience" name="has_experience" required>
                <option value="Да" <?= $student['has_experience'] === 'Да' ? 'selected' : '' ?>>Да</option>
                <option value="Нет" <?= $student['has_experience'] === 'Нет' ? 'selected' : '' ?>>Нет</option>
            </select>
        </div>
        <div class="form-group">
            <label for="age">Возраст:</label>
            <input type="number" id="age" name="age" min="5" max="100" value="<?= $student['age'] ?>" required>
        </div>
        <div class="form-group">
            <label for="duration">Длительность курса (мес.):</label>
            <input type="number" id="duration" name="duration" min="1" max="36" value="<?= $student['course_duration_months'] ?>" required>
        </div>
        <button type="submit" name="update_student">Сохранить изменения</button>
        <a href="index.php" class="back-btn">Назад к списку</a>
    </form>
</body>
</html>

<?php $db->close(); ?>