$(function() {
    refresh();
    focusField('search');
    $("#refresh_link").click(function() {
        refresh();
    });
    $("#search").keypress(function(e) {
        if (e.which === 13) {
            e.preventDefault();
            refresh();
        }
    });
    $("#search").keydown(function(e) {
        if (!$("#refresh_link").hasClass('url_orange')) {
            $("#refresh_link").addClass('url_orange');
        }
    });
});

function refresh() {
    httpHandler("/" + getBaseUrl() + "students/list/" + $("#search").val(), "get", null,
        function(html) {
            $("#record_list").html(html);
            $("#refresh_link").removeClass('url_orange');
        }, null, false);
}

function approve(student_uuid) {
    confirmDialog("approve", "Confirm", "Are you sure you want to approve this student's application?", function() {
        var data = {
            student_uuid: student_uuid
        };
        httpHandler("/" + getBaseUrl() + "student/approveapplication", "post", data, refresh);
    });
}

function decline(student_uuid) {
    confirmDialog("decline", "Confirm", "Are you sure you want to decline this student's application?", function() {
        var data = {
            student_uuid: student_uuid
        };
        httpHandler("/" + getBaseUrl() + "student/declineapplication", "post", data, refresh);
    });
}