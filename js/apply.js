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
    $("#btn_save").on("click", function(e) {
        e.preventDefault();
        formValidate(validator);
        if ($("#frm_main").valid()) {
            httpHandler("/" + getBaseUrl() + "student/saveapplication", "post", $("#frm_main").serialize(), doUIUpdateMain, undefined, undefined, undefined, 'error_label');
        }
    });
    $("#btn_submit").on("click", function(e) {
        e.preventDefault();
        formValidate(validator);
        if ($("#frm_main").valid()) {
            httpHandler("/" + getBaseUrl() + "student/submitapplication", "post", $("#frm_main").serialize(), doUIUpdateMain, undefined, undefined, undefined, 'error_label');
        }
    });
});

function doUIUpdateMain() {
    $("#li-tab-subjects").fadeIn();
    $("#tab-subjects").fadeIn();
    // $("#tab-link-quotation").fadeIn();
    $("#tab-link-subjects").trigger('click');
}

function doUIUpdateSubjects(show) {
    if (show) {
        $("#li-tab-quotation").fadeIn();
        $("#tab-quotation").fadeIn();
        // $("#tab-link-quotation").trigger('click');
        $("#subject").focus();
        return;
    }
    $("#li-tab-quotation").fadeOut();
    $("#tab-quotation").fadeOut();
}

function doUIUpdateAids(show) {
    if (show) {
        $("#li-tab-quotation").fadeIn();
        $("#tab-quotation").fadeIn();
        // $("#tab-link-quotation").trigger('click');
        return;
    }
    $("#li-tab-quotation").fadeOut();
    $("#tab-quotation").fadeOut();
}

//Link student
function linkSubject(subject_id) {
    var data = {
        subject_id: subject_id,
        student_uuid: $("#uuid").val()
    };
    httpHandler("/" + getBaseUrl() + "student/linksubject", "post", data, loadSubjects);
}

function removeSubject(student_subject_uuid) {
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
            loadStatement();
            doUIUpdateSubjects(true);
        }, null, false);
}

function loadQuotation() {
    httpHandler("/" + getBaseUrl() + "student/getsubjects/" + $("#uuid").val() + "/", "get", null,
        function(html) {
            $("#subject_list").html(html);
        }, null, false);
}

//Study aids
function linkAid(aid_id) {
    var data = {
        aid_id: aid_id,
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
            doUIUpdateAids(true);
            loadStatement();
            $("#aid_list").html(html);
        }, null, false);
}

function loadStatement() {
    httpHandler("/" + getBaseUrl() + "student/getstatement/" + $("#uuid").val() + "/", "get", null,
        function(html) {
            $("#tab-quotation").html(html);
            doUIUpdateSubjects(true);
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