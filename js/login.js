$(function() {
    focusField('email');
    var validator = $("#frm_main").validate({
        onclick: true,
        errorPlacement: function(error, element) {
            return true;
        },
        rules: {
            email: { required: true },
            password: { required: true }
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass("input-validation-error");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("input-validation-error");
        }
    });
    $("#frm_main").on("submit", function(e) {
        e.preventDefault();
        formValidate(validator);
        if ($("#frm_main").valid()) {
            httpHandler("/" + getBaseUrl() + "account/loginfeedback", "post", $("#frm_main").serialize(), redirect);
        }
    });
});

function redirect(data) {
    if (typeof data.redirect_to === 'undefined') {
        return false;
    }
    return window.location = data.redirect_to;
}