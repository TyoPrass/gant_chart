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
            $stmt = $conn->prepare("INSERT INTO tasks (text, start_date, duration, progress, parent) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssidi", $data['text'], $data['start_date'], $data['duration'], $data['progress'], $data['parent']);
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
