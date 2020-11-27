$(function() {
    loadAuditTrail();
    $("#frm_main input").change(function(element) {
        $("#frm_main").submit();
    });
    $("#frm_main").on("submit", function(e) {
        e.preventDefault();
        httpHandler("/" + getBaseUrl() + "user/save", "post", $("#frm_main").serialize(), loadAuditTrail);
    });
});