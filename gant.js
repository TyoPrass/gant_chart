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
