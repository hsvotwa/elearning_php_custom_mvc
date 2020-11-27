$(function() {
    loadOtherContent();
    focusField('no_of_rooms');
    var comp_fields = "number,student_uuid,number_of_rooms,number_of_students";
    $("#frm_main input, #frm_main input select").blur(function(element) {
        var validatorBlur = $("#frm_main").validate({
            onclick: false,
            errorPlacement: function(error, element) {
                return true;
            },
            rules: validField(element.target.id),
            highlight: function(element, errorClass, validClass) {
                $("#" + element.target.id).addClass("input-validation-error");
            },
            unhighlight: function(element, errorClass, validClass) {
                $("#" + element.target.id).removeClass("input-validation-error");
            }
        });
        formValidate(validatorBlur);
        if (!$("#" + element.target.id).valid()) {
            $("#" + element.target.id).addClass("input-validation-error");
            return false;
        }
        $("#frm_main").submit();
    });
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
            httpHandler("/" + getBaseUrl() + "unit/save", "post", $("#frm_main").serialize(), doUIUpdate, undefined, undefined, undefined, 'error_label');
        }
    });
});

//Link student
function linkstudents() {
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

function studentPostSuccess() {
    $('#link_student').dialog("destroy");
    $('#tab-link-student').trigger("click");
    loadOtherContent();
}

function removestudent(unit_student_uuid) {
    confirmDialog("remove_occ", "Confirm", "Are you sure you want to remove this student?", function() {
        var data = {
            unit_student_uuid: unit_student_uuid
        };
        httpHandler("/" + getBaseUrl() + "unit/removestudent", "post", data, loadstudents);
    });
}

function makeLeaseHolder(unit_student_uuid) {
    var data = {
        unit_student_uuid: unit_student_uuid
    };
    httpHandler("/" + getBaseUrl() + "unit/makeleaseholder", "post", data, loadstudents);
}

function removeLeaseHolder(unit_student_uuid) {
    var data = {
        unit_student_uuid: unit_student_uuid
    };
    httpHandler("/" + getBaseUrl() + "unit/removeleaseholder", "post", data, loadstudents);
}

function loadstudents() {
    httpHandler("/" + getBaseUrl() + "units/studentlist/" + $("#uuid").val() + "/", "get", null,
        function(html) {
            $("#tab-students").html(html);
            loadAuditTrail();
        }, null, false);
}

//Link device
function linkDevice(device_uuid) {
    var data = {
        device_uuid: device_uuid,
        unit_uuid: $("#uuid").val()
    };
    httpHandler("/" + getBaseUrl() + "unit/savedevice", "post", data, loadOtherContent);
}

function removeDevice(unit_device_uuid) {
    confirmDialog(null, "Confirm", "Are you sure you want to remove this device?", function() {
        var data = {
            unit_device_uuid: unit_device_uuid
        };
        httpHandler("/" + getBaseUrl() + "unit/removedevice", "post", data, loadDevices);
    });
}

function loadDevices() {
    httpHandler("/" + getBaseUrl() + "units/devicelist/" + $("#uuid").val() + "/", "get", null,
        function(html) {
            $("#device_list").html(html);
            loadAuditTrail();
        }, null, false);
}

function loadOtherContent() {
    loadAuditTrail();
    loadstudents();
    loadDevices();
}

function doUIUpdate() {
    loadAuditTrail();
    $("#btn_link_student").removeClass("hidden");
}

$(function() {
    $("#device").autocomplete({
        source: function(request, response) {
            $.ajax({
                url: "/" + getBaseUrl() + "devices/unlinkedlist/" + request.term + "/" + $("#uuid").val(),
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
                linkDevice(ui.item.value);
                $("#device").val("");
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
            $("#device").val(ui.item.label);
        }
    });
});

function validField(field_name) {
    return getAllFields()[field_name];
}

function getAllFields() {
    return {
        number: { required: true },
        number_of_rooms: { required: true, checkNum: true, minNum: true },
        number_of_students: { required: true, checkNum: true, minNum: true }
    }
}