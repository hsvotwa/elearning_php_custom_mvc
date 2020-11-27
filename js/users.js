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
    httpHandler("/" + getBaseUrl() + "users/list/" + $("#search").val(), "get", null,
        function(html) {
            $("#record_list").html(html);
            $("#refresh_link").removeClass('url_orange');
        }, null, false);
}

function linkUsers() {
    $("input[name='role_type_id']").change(function() {
        if ($("input[name='role_type_id']").parent().hasClass("radio-validation-error")) {
            $("input[name='role_type_id']").parent().removeClass("radio-validation-error");
        }
    });
    var validator = $("#frm_link_user").validate({
        onclick: false,
        errorPlacement: function(error, element) {
            return true;
        },
        rules: {
            email: { required: true }
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass("input-validation-error");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("input-validation-error");
        }
    });
    formValidate(validator);
    if ($("#frm_link_user").valid()) {
        if (!$("input[name='role_type_id']:checked").length > 0) {
            $("input[name='role_type_id']").parent().addClass("radio-validation-error");
            return;
        }
        httpHandler("/" + getBaseUrl() + "profile/saveuser", "post", $("#frm_link_user").serialize(), refresh, undefined, undefined, undefined, 'error_label');
    }
}

function removeUser(user_uuid) {
    confirmDialog("remove_user", "Confirm", "Are you sure you want to revoke this user's access?", function() {
        var data = {
            user_uuid: user_uuid
        };
        httpHandler("/" + getBaseUrl() + "profile/removeuser", "post", data, refresh);
    });
}