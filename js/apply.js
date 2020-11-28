$(function() {
    focusField('name');
    var validator = $("#frm_main").validate({
        onclick: false,
        errorPlacement: function(error, element) {
            return true;
        },
        rules: getAllFields(),
        highlight: function(element, errorClass, validClass) {
            // $(element).addClass("input-validation-error");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("input-validation-error");
        }
    });
    $("#frm_main").on("submit", function(e) {
        e.preventDefault();
        formValidate(validator);
        if ($("#frm_main").valid()) {
            httpHandler("/" + getBaseUrl() + "student/saveapplication", "post", $("#frm_main").serialize(), doUIUpdate, undefined, undefined, undefined, 'error_label');
        }
    });
});

//Link student
function linkssubject() {
    var validatorstudent = $("#frm_link_student").validate({
        onclick: true,
        ignore: [],
        errorPlacement: function(error, element) {
            return true;
        },
        rules: {
            student_uuid: { required: true },
            student: { required: true },
            unit_uuid: { required: true },
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass("input-validation-error");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("input-validation-error");
        }
    });
    if ($("#student_uuid").val() === "") {
        $("#student").addClass("input-validation-error");
        $("#student").val("");
        $("#student").focus();
        return;
    }
    formValidate(validatorstudent);
    if ($("#frm_link_student").valid()) {
        httpHandler("/" + getBaseUrl() + "unit/savestudent", "post", $("#frm_link_student").serialize(), studentPostSuccess, undefined, undefined, undefined, 'error_label');
    }
}

function linkSubject(subject_uuid) {
    var data = {
        subject_uuid: subject_uuid,
        student_uuid: $("#uuid").val()
    };
    httpHandler("/" + getBaseUrl() + "student/linksubject", "post", data, loadSubjects);
}

function removeSubject($student_subject_uuid) {
    confirmDialog("remove_subject", "Confirm", "Are you sure you want to remove this student?", function() {
        var data = {
            student_subject_uuid: student_subject_uuid
        };
        httpHandler("/" + getBaseUrl() + "student/removesubject", "post", data, loadSubjects);
    });
}

function loadSubjects() {
    httpHandler("/" + getBaseUrl() + "student/getsubjects/" + $("#uuid").val() + "/", "get", null,
        function(html) {
            $("#subject_list").html(html);
        }, null, false);
}

//Study aids
function linkAid(aid_uuid) {
    var data = {
        aid_uuid: aid_uuid,
        student_uuid: $("#uuid").val()
    };
    httpHandler("/" + getBaseUrl() + "student/linkaid", "post", data, loadAids);
}

function removeAid(student_aid_uuid) {
    confirmDialog("remove_aid", "Confirm", "Are you sure you want to remove this aid?", function() {
        var data = {
            student_aid_uuid: student_aid_uuid
        };
        httpHandler("/" + getBaseUrl() + "student/removeaid", "post", data, loadAids);
    });
}

function loadAids() {
    httpHandler("/" + getBaseUrl() + "student/getaids/" + $("#uuid").val() + "/", "get", null,
        function(html) {
            $("#aid_list").html(html);
        }, null, false);
}

$(function() {
    $("#subject").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "/" + getBaseUrl() + "subjects/unlinkedlist/" + request.term + "/" + $("#uuid").val(),
                dataType: "json",
                success: function(data) {
                    if (data !== null && data !== '') {
                        response($.map(data, function(item) {
                            return {
                                label: item.name,
                                value: item.value
                            };
                        }));
                    } else {
                        $('ul[class*=ui-autocomplete]').hide();
                    }
                }
            });
        },
        minLength: 2,
        cache: false,
        select: function(event, ui) {
            if (ui !== null && ui.item !== null) {
                linkSubject(ui.item.value);
                $("#subject").val("");
            }
            var code = event.keyCode ? event.keyCode : event.which;
            if (code === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            return false;
        },
        focus: function(event, ui) {
            event.preventDefault();
            $("#subject").val(ui.item.label);
        }
    });
});

$(function() {
    $("#aid").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "/" + getBaseUrl() + "aids/unlinkedlist/" + request.term + "/" + $("#uuid").val(),
                dataType: "json",
                success: function(data) {
                    if (data !== null && data !== '') {
                        response($.map(data, function(item) {
                            return {
                                label: item.name,
                                value: item.value
                            };
                        }));
                    } else {
                        $('ul[class*=ui-autocomplete]').hide();
                    }
                }
            });
        },
        minLength: 2,
        cache: false,
        select: function(event, ui) {
            if (ui !== null && ui.item !== null) {
                linkAid(ui.item.value);
                $("#aid").val("");
            }
            var code = event.keyCode ? event.keyCode : event.which;
            if (code === 13) {
                event.preventDefault();
                event.stopPropagation();
            }
            return false;
        },
        focus: function(event, ui) {
            event.preventDefault();
            $("#aid").val(ui.item.label);
        }
    });
});

function validField(field_name) {
    return getAllFields()[field_name];
}

function getAllFields() {
    return {
        name: { required: true },
        tel_no: { required: true },
        email: { required: true }
    }
}