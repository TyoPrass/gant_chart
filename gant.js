$(document).ready(function () {
    gantt.config.date_format = "%Y-%m-%d";
    gantt.config.show_progress = true;
    gantt.init("gantt_here");

    // Load data from server
    $.getJSON("data.php", function (response) {
        // Parse the response directly since it's already in JSON format
        gantt.parse({ data: JSON.parse(response) });
        updateTaskCards(JSON.parse(response));
    });

    // Add new task
    gantt.attachEvent("onAfterTaskAdd", function (id, task) {
        $.ajax({
            url: "data.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                action: "create",
                id: id, // Include the id
                text: task.text,
                start_date: gantt.date.date_to_str("%Y-%m-%d")(task.start_date),
                duration: task.duration,
                progress: task.progress || 0,
                parent: task.parent || 0
            }),
            success: function (response) {
                const res = JSON.parse(response);
                if (res.status === "success") {
                    gantt.changeTaskId(id, res.id);
                    updateTaskCards(gantt.serialize().data);
                }
            }
        });
    });

    // Update task
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
                progress: task.progress || 0,
                parent: task.parent || 0
            }),
            success: function (response) {
                const res = JSON.parse(response);
                if (res.status === "updated") {
                    updateTaskCards(gantt.serialize().data);
                }
            }
        });
    });

    // Delete task
    gantt.attachEvent("onAfterTaskDelete", function (id) {
        $.ajax({
            url: "data.php",
            type: "POST",
            contentType: "application/json",
            data: JSON.stringify({
                action: "delete",
                id: id
            }),
            success: function (response) {
                const res = JSON.parse(response);
                if (res.status === "deleted") {
                    updateTaskCards(gantt.serialize().data);
                }
            }
        });
    });
});