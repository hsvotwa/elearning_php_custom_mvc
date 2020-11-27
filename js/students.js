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