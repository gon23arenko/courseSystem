<?php
$db = new mysqli('localhost', 'root', '', 'drum_school');

if ($db->connect_error) {
    die("Ошибка подключения: " . $db->connect_error);
}

// Обработка формы добавления ученика
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_student'])) {
    $full_name = $db->real_escape_string($_POST['full_name']);
    $teacher_id = intval($_POST['teacher_id']);
    $has_experience = $_POST['has_experience'] === 'Да' ? 'Да' : 'Нет';
    $age = intval($_POST['age']);
    $duration = intval($_POST['duration']);
    
    $query = "INSERT INTO students (full_name, teacher_id, has_experience, age, course_duration_months) 
              VALUES ('$full_name', $teacher_id, '$has_experience', $age, $duration)";
    $db->query($query);
}

// Обработка удаления ученика
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $db->query("DELETE FROM students WHERE id = $id");
    header("Location: index.php");
    exit;
}

// Получение списка преподавателей
$teachers = $db->query("SELECT * FROM teachers");

// Получение списка учеников
$students_query = "SELECT s.*, t.full_name as teacher_name FROM students s JOIN teachers t ON s.teacher_id = t.id";
if (isset($_GET['teacher_filter']) && $_GET['teacher_filter'] !== 'all') {
    $teacher_filter = intval($_GET['teacher_filter']);
    $students_query .= " WHERE s.teacher_id = $teacher_filter";
}
$students = $db->query($students_query);
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Информационная система "Ритм"</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        form { margin-bottom: 20px; background: #f9f9f9; padding: 15px; border-radius: 5px; }
        .form-group { margin-bottom: 10px; }
        label { display: inline-block; width: 150px; }
        input, select { padding: 5px; width: 200px; }
        button { padding: 8px 15px; background: #4CAF50; color: white; border: none; cursor: pointer; }
        button:hover { background: #45a049; }
        .delete-btn { background: #f44336; }
        .delete-btn:hover { background: #d32f2f; }
        .filter-form { display: inline-block; margin-left: 20px; }
    </style>
</head>
<body>
    <h1>Информационная система "Ритм"</h1>
    <h2>Учет учеников курсов игры на ударных инструментах</h2>
    
    <!-- Форма добавления нового ученика -->
    <form method="POST">
        <h3>Добавить нового ученика</h3>
        <div class="form-group">
            <label for="full_name">ФИО ученика:</label>
            <input type="text" id="full_name" name="full_name" required>
        </div>
        <div class="form-group">
            <label for="teacher_id">Преподаватель:</label>
            <select id="teacher_id" name="teacher_id" required>
                <?php while($teacher = $teachers->fetch_assoc()): ?>
                    <option value="<?= $teacher['id'] ?>"><?= $teacher['full_name'] ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="has_experience">Музыкальный опыт:</label>
            <select id="has_experience" name="has_experience" required>
                <option value="Да">Да</option>
                <option value="Нет">Нет</option>
            </select>
        </div>
        <div class="form-group">
            <label for="age">Возраст:</label>
            <input type="number" id="age" name="age" min="5" max="100" required>
        </div>
        <div class="form-group">
            <label for="duration">Длительность курса (мес.):</label>
            <input type="number" id="duration" name="duration" min="1" max="36" required>
        </div>
        <button type="submit" name="add_student">Добавить ученика</button>
    </form>
    
    <!-- Фильтр по преподавателям -->
    <form method="GET" class="filter-form">
        <label for="teacher_filter">Фильтр по преподавателю:</label>
        <select id="teacher_filter" name="teacher_filter" onchange="this.form.submit()">
            <option value="all">Все преподаватели</option>
            <?php 
            $teachers->data_seek(0); // Сброс указателя результата
            while($teacher = $teachers->fetch_assoc()): 
                $selected = isset($_GET['teacher_filter']) && $_GET['teacher_filter'] == $teacher['id'] ? 'selected' : '';
            ?>
                <option value="<?= $teacher['id'] ?>" <?= $selected ?>><?= $teacher['full_name'] ?></option>
            <?php endwhile; ?>
        </select>
    </form>
    
    <!-- Таблица с учениками -->
    <h3>Список учеников</h3>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>ФИО</th>
                <th>Преподаватель</th>
                <th>Опыт</th>
                <th>Возраст</th>
                <th>Длительность курса (мес.)</th>
                <th>Действия</th>
            </tr>
        </thead>
        <tbody>
            <?php while($student = $students->fetch_assoc()): ?>
            <tr>
                <td><?= $student['id'] ?></td>
                <td><?= htmlspecialchars($student['full_name']) ?></td>
                <td><?= htmlspecialchars($student['teacher_name']) ?></td>
                <td><?= $student['has_experience'] ?></td>
                <td><?= $student['age'] ?></td>
                <td><?= $student['course_duration_months'] ?></td>
                <td>
                    <a href="edit.php?id=<?= $student['id'] ?>">Редактировать</a> | 
                    <a href="index.php?delete=<?= $student['id'] ?>" onclick="return confirm('Вы уверены?')" class="delete-btn">Удалить</a>
                </td>
            </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>

<?php $db->close(); ?>