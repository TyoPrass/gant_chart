<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "project_management";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    // Fetch tasks from the single JSON column
    $sql = "SELECT task_data FROM tasks_json";
    $result = $conn->query($sql);
    $row = $result->fetch_assoc();
    
    // If data exists, return it, otherwise return empty array
    echo $row ? $row['task_data'] : '[]';
}

if ($method == 'POST') {
    $data = json_decode(file_get_contents("php://input"), true);
    
    if (isset($data['action'])) {
        $action = $data['action'];

        // Get current tasks
        $sql = "SELECT task_data FROM tasks_json LIMIT 1";
        $result = $conn->query($sql);
        $tasks = [];
        
        if ($result->num_rows > 0) {
            $row = $result->fetch_assoc();
            $tasks = json_decode($row['task_data'], true);
        }

        if ($action == 'create') {
            $task = [
                'id' => uniqid(),
                'text' => $data['text'],
                'start_date' => $data['start_date'],
                'duration' => $data['duration'],
                'progress' => $data['progress'],
                'parent' => $data['parent']
            ];
            $tasks[] = $task;
            
            $json_tasks = json_encode($tasks);
            $stmt = $conn->prepare("UPDATE tasks_json SET task_data = ?");
            $stmt->bind_param("s", $json_tasks);
            $stmt->execute();
            echo json_encode(["status" => "success", "id" => $task['id']]);
        }

        if ($action == 'update' || $action == 'delete') {
            if ($action == 'update') {
                foreach ($tasks as &$task) {
                    if ($task['id'] == $data['id']) {
                        $task = $data;
                        break;
                    }
                }
            } else {
                $tasks = array_filter($tasks, function($task) use ($data) {
                    return $task['id'] != $data['id'];
                });
            }
            
            $json_tasks = json_encode(array_values($tasks));
            $stmt = $conn->prepare("UPDATE tasks_json SET task_data = ?");
            $stmt->bind_param("s", $json_tasks);
            $stmt->execute();
            echo json_encode(["status" => $action == 'update' ? "updated" : "deleted"]);
        }
    }
}

$conn->close();
?>