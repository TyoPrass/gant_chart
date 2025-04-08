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
    <script src="gant.js"></script>
</body>
</html>