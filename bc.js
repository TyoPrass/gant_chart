$(document).ready(function () {
    gantt.config.date_format = "%Y-%m-%d";
    
    // Modify lightbox to use a text input for progress
    gantt.config.lightbox.sections = [
        {name: "description", height: 70, map_to: "text", type: "textarea", focus: true},
        {name: "time", height: 72, type: "duration", map_to: "auto"},
        
    ];

    // Add custom editor for progress
    gantt.form_blocks["progress_editor"] = {
        render: function() {
            return "<div class='gantt_cal_ltext'><input type='number' min='0' max='100' step='1'></div>";
        },
        set_value: function(node, value) {
            node.querySelector("input").value = Math.round(value * 100) || 0;
        },
        get_value: function(node) {
            return node.querySelector("input").value / 100;
        }
    };

    // Define the updateTaskCards function before using it
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
                                <strong>Progress:</strong> 
                                <input type="number" class="form-control progress-input" 
                                    value="${(task.progress * 100).toFixed(0)}" 
                                    min="0" max="100" 
                                    data-task-id="${task.id}"
                                    style="width: 80px; display: inline-block;">%
                            </p>
                        </div>
                    </div>
                </div>
            `;
            taskCards.append(card);
        });

        // Event listener for progress input changes
        $('.progress-input').on('change', function() {
            const taskId = $(this).data('task-id');
            const newProgress = $(this).val() / 100;
            const task = gantt.getTask(taskId);
            task.progress = newProgress;
            gantt.updateTask(taskId);
        });
    }

    gantt.init("gantt_here");

    // Ambil data dari server
    $.getJSON("data.php", function (data) {
        // Normalize progress values in data - already in decimal form (0-1)
        data = data.map(task => {
            task.progress = parseFloat(task.progress);
            return task;
        });
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
                progress: task.progress, // Progress is already in decimal form
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
                progress: task.progress, // Progress is already in decimal form
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
                                <strong>Progress:</strong> 
                                <input type="number" class="form-control progress-input" 
                                    value="${(task.progress * 100).toFixed(0)}" 
                                    min="0" max="100" 
                                    data-task-id="${task.id}"
                                    style="width: 80px; display: inline-block;">%
                            </p>
                        </div>
                    </div>
                </div>
            `;
            taskCards.append(card);
        });

        // Event listener for progress input changes
        $('.progress-input').on('change', function() {
            const taskId = $(this).data('task-id');
            const newProgress = $(this).val() / 100;
            const task = gantt.getTask(taskId);
            task.progress = newProgress;
            gantt.updateTask(taskId);
        });
    }
});