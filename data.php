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
