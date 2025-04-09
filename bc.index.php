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
    <h2>Gantt Chart dengan CRUD (PHP & JQUERY)</h2>
    <div id="gantt_here"></div>

    <div class="container mt-4">
        <h3>Task List</h3>
        <div id="task_cards" class="row row-cols-1 row-cols-md-3 g-4">
            <!-- Cards akan ditambahkan di sini -->
        </div>
    </div>

    <script src="gant.js">
      
    </script>
</body>
</html>