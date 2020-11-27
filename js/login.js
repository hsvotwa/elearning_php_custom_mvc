$(function() {
    focusField('email');
    var comp_fields = "email,password";
    $("#frm_main input").blur(function(element) {
        validCompulsory(element.target.id, comp_fields);
        formValidate(validatorBlur);
    })
    var validatorBlur = $("#frm_main").validate({
        onclick: false,
        errorPlacement: function(error, element) {
            return true;
        },
        rules: {
            email: { required: true },
            password: { required: true }
        }
    });
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
            httpHandler("/" + getBaseUrl() + "account/trylogin", "post", $("#frm_main").serialize(), redirect);
        }
    });
});

function redirect(data) {
    if (typeof data.redirect_to === 'undefined') {
        return false;
    }
    return window.location = data.redirect_to;
}