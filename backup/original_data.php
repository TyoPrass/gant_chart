<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gantt Chart dengan CRUD</title>
    <script src="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.js"></script>
    <link rel="stylesheet" href="https://cdn.dhtmlx.com/gantt/edge/dhtmlxgantt.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <style>
        html, body {
            height: 100%;
            padding: 0;
            margin: 0;
        }
        #gantt_here {
            width: 100%;
            height: 500px;
        }
    </style>
</head>
<body>
    <h2>Gantt Chart dengan CRUD (PHP & MySQL)</h2>
    <div id="gantt_here"></div>

    <div class="container mt-4">
        <h3>Task List</h3>
        <div id="task_cards" class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Cards akan ditambahkan di sini -->
        </div>
    </div>

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

            // Fungsi untuk memperbarui card view
            function updateTaskCards(tasks) {
                const taskCards = $("#task_cards");
                taskCards.empty();
                tasks.forEach(task => {
                    const card = `
                        <div class="col">
                            <div class="card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">${task.text}</h5>
                                    <p class="card-text">
                                        <strong>Start Date:</strong> ${task.start_date}<br>
                                        <strong>Duration:</strong> ${task.duration} days<br>
                                        <strong>Progress:</strong> ${(task.progress * 100).toFixed(0)}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    `;
                    taskCards.append(card);
                });
            }
        });
    </script>
</body>
</html>