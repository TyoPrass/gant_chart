<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "project_management";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

// Cek apakah request adalah GET atau POST
$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Ambil data tugas dari database
    $sql = "SELECT * FROM tasks";
    $result = $conn->query($sql);
    $tasks = [];

    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }

    echo json_encode($tasks);
}

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['action'])) {
        $action = $data['action'];

        if ($action == 'create') {
            $text = $_POST['text'];
            $start_date = $_POST['start_date'];
            $duration = $_POST['duration'];
            $progress = $_POST['progress'];
            $parent = $_POST['parent'];

            // Handle file upload
            $filePath = null;
            if (isset($_FILES['file']) && $_FILES['file']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = 'uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $filePath = $uploadDir . basename($_FILES['file']['name']);
                move_uploaded_file($_FILES['file']['tmp_name'], $filePath);
            }

            // Save task to database (include file path if uploaded)
            $stmt = $conn->prepare("INSERT INTO tasks (text, start_date, duration, progress, parent, file_path) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssidis", $text, $start_date, $duration, $progress, $parent, $filePath);
            $stmt->execute();
            echo json_encode(["status" => "success", "id" => $conn->insert_id]);
        }

        if ($action == 'update') {
            $stmt = $conn->prepare("UPDATE tasks SET text=?, start_date=?, duration=?, progress=?, parent=? WHERE id=?");
            $stmt->bind_param("ssidii", $data['text'], $data['start_date'], $data['duration'], $data['progress'], $data['parent'], $data['id']);
            $stmt->execute();
            echo json_encode(["status" => "updated"]);
        }

        if ($action == 'delete') {
            $stmt = $conn->prepare("DELETE FROM tasks WHERE id=?");
            $stmt->bind_param("i", $data['id']);
            $stmt->execute();
            echo json_encode(["status" => "deleted"]);
        }
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gantt Chart dengan CRUD</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Gantt Chart CSS -->
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
    <style>
        html, body {
            height: 100%;
            padding: 0;
            margin: 0;
            background-color: #f8f9fa;
        } 
        #gantt_here {
            width: 100%;
            height: 500px;
            background: white;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .card {
            margin-bottom: 10px;
        }
        .navbar {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="#">Gantt Chart System</a>
        </div>
    </nav>

    <div class="container-fluid mt-4">
        <div class="row">
            <div class="col-md-12 mb-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="card-title mb-0">Project Timeline</h5>
                    </div>
                    <div class="card-body">
                        <div id="gantt_here"></div>
                    </div>
                </div>
            </div>
        </div>

      
    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
    <script>
        $(document).ready(function () {
        gantt.config.date_format = "%Y-%m-%d";

        gantt.init("gantt_here");

        // Ambil data dari server
        $.getJSON("data.php", function (data) {
            gantt.parse({ data: data });
            updateTaskCards(data);
        });

        // Tambah data baru
        gantt.attachEvent("onAfterTaskAdd", function (id, task) {
            $.ajax({
                url: "data.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    action: "create",
                    text: task.text,
                    start_date: gantt.date.date_to_str("%Y-%m-%d")(task.start_date),
                    duration: task.duration,
                    progress: task.progress,
                    parent: task.parent
                }),
                success: function (response) {
                    let res = JSON.parse(response);
                    gantt.changeTaskId(id, res.id);
                    updateTaskCards(gantt.serialize().data);
                }
            });
        });

        // Update data
        gantt.attachEvent("onAfterTaskUpdate", function (id, task) {
            $.ajax({
                url: "data.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    action: "update",
                    id: id,
                    text: task.text,
                    start_date: gantt.date.date_to_str("%Y-%m-%d")(task.start_date),
                    duration: task.duration,
                    progress: task.progress,
                    parent: task.parent
                }),
                success: function () {
                    console.log("Task updated");
                    updateTaskCards(gantt.serialize().data);
                }
            });
        });

        // Hapus data   
        gantt.attachEvent("onAfterTaskDelete", function (id) {
            $.ajax({
                url: "data.php",
                type: "POST",
                contentType: "application/json",
                data: JSON.stringify({
                    action: "delete",
                    id: id
                }),
                success: function () {
                    console.log("Task deleted");
                    updateTaskCards(gantt.serialize().data);
                }
            });
        });

        // Function to update task cards with action buttons
        function updateTaskCards(data) {
            let taskContainer = $("#task_cards");
            taskContainer.empty();
            data.forEach(task => {
                let taskCard = $(`
                    <div class="task-card" data-id="${task.id}">
                        <p>${task.text}</p>
                        <button class="update-task">Update</button>
                        <button class="delete-task">Delete</button>
                    </div>
                `);
                taskContainer.append(taskCard);
            });

            // Attach event listeners for update and delete buttons
            $(".update-task").click(function () {
                let taskId = $(this).parent().data("id");
                let task = gantt.getTask(taskId);
                task.text = prompt("Update task text:", task.text) || task.text;
                gantt.updateTask(taskId);
            });

            $(".delete-task").click(function () {
                let taskId = $(this).parent().data("id");
                if (confirm("Are you sure you want to delete this task?")) {
                    gantt.deleteTask(taskId);
                }
            });
        }
    });
    </script>
</body>
</html>