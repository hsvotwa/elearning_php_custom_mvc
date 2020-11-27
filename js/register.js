$(function() {
    loadOtherContent();
    focusField('name');
    $("#name, #surname, #id_no, #cell_no").blur(function(element) {
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
    $("#birthday, #lease_expiry_date, #can_load_visitor").change(function(element) {
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
        onclick: true,
        errorPlacement: function(error, element) {
            return false;
        },
        rules: getAllFields(),
        highlight: function(element, errorClass, validClass) {},
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("input-validation-error");
        }
    });
    $("#frm_main").on("submit", function(e) {
        e.preventDefault();
        $("#birthday").removeClass("error");
        $("#lease_expiry_date").removeClass("error");
        if (!isPast("birthday", true, "Birthday")) {
            $("#birthday").addClass("error");
            return false;
        }
        if (!isFuture("lease_expiry_date", true, "Lease expiry date")) {
            $("#lease_expiry_date").addClass("error");
            return false;
        }
        formValidate(validator);
        if ($("#frm_main").valid()) {
            httpHandler("/" + getBaseUrl() + "student/save", "post", $("#frm_main").serialize(), loadAuditTrail, undefined, undefined, undefined, 'student_error');
        }
    });
});

//Link document
function linkDocument() {
    var comp_fields = "document_type_id,document";
    var validator = $("#frm_link_document").validate({
        onclick: true,
        errorPlacement: function(error, element) {
            return true;
        },
        rules: {
            document_type_id: { required: true, checkNum: true, minNum: true },
            document: { required: true }
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass("input-validation-error");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("input-validation-error");
        }
    });
    formValidate(validator);
    if ($("#frm_link_document").valid()) {
        var other_data = {
            document_type_id: $("#document_type_id").val(),
            student_uuid: $("#student_uuid").val()
        };
        postFormAndFile("/" + getBaseUrl() + "student/savedocument", "document", other_data, "frm_link_document", documentPostSuccess, "error_label")
    }
}

function documentPostSuccess() {
    $('#link_document').dialog("destroy");
    $('#doc-tab-link').trigger("click");
    loadOtherContent();
}

function removeDoc(document_uuid) {
    confirmDialog("remove_doc", "Confirm", "Are you sure you want to remove this unit?", function() {
        var data = {
            document_uuid: document_uuid
        };
        httpHandler("/" + getBaseUrl() + "student/removedocument", "post", data, loadOtherContent);
    });
}

function loadDocuments() {
    httpHandler("/" + getBaseUrl() + "students/documentlist/" + $("#uuid").val() + "/", "get", null,
        function(html) {
            $("#tab-documents").html(html);
        }, null, false);
}

function loadOtherContent() {
    loadAuditTrail();
    loadDocuments();
}

function validField(field_name) {
    return getAllFields()[field_name];
}

function getAllFields() {
    return {
        name: { required: true },
        surname: { required: true },
        birthday: { required: true },
        id_no: { required: true },
        cell_no: { required: true, checkNum: false },
        lease_expiry_date: { required: true }
    }
}