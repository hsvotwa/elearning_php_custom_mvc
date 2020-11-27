$(function() {
    loadOtherContent();
    focusField('name');
    var comp_fields = "name,tel_no,tel_no,no_of_units,client_id,client_secret,encryption_key";
    $("#frm_main input").blur(function(element) {
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
        toggleTabPending("li-tab-gen", "name,tel_no,no_of_units");
        toggleTabPending("li-tab-cred", "client_id,client_secret,encryption_key");
        if (!$("#" + element.target.id).valid()) {
            $("#" + element.target.id).addClass("input-validation-error");
            return false;
        }
        $("#frm_main").submit();
    })
    var validator = $("#frm_main").validate({
        onclick: true,
        ignore: [],
        errorPlacement: function(error, element) {
            return true;
        },
        rules: getAllFields(),
        highlight: function(element, errorClass, validClass) {},
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("input-validation-error");
        }
    });
    $("#frm_main").on("submit", function(e) {
        e.preventDefault();
        formValidate(validator);
        if ($("#frm_main").valid()) {
            httpHandler("/" + getBaseUrl() + "student/save", "post", $("#frm_main").serialize(), doUIUpdate);
        }
    });
});

//Create units
function createUnits() {
    $("#student_uuid").val($("#uuid").val());
    httpHandler("/" + getBaseUrl() + "student/saveunits", "post", $("#frm_create_units").serialize(), loadContentAfterUnitCreated, null, false, "create_units", "error_label");
}

function loadContentAfterUnitCreated() {
    loadOtherContent();
    $("#tab-link-units").click();
    $("#create_units").dialog("destroy");
}

var g_floor_count = "";
var g_floor_number_type = "";
var g_unit_number_type = "";
var g_first_floor_field = "";

function getUnitsDialog() {
    var validatorUnit = $("#frm_create_units").validate({
        onclick: true,
        errorPlacement: function(error, element) {
            return true;
        },
        rules: {
            number_of_floors: { required: true, checkNum: true, minNum: true },
            floor_number_type: { required: true, checkNum: true, minNum: true },
            unit_number_type: { required: true, checkNum: true, minNum: true }
        },
        highlight: function(element, errorClass, validClass) {
            $(element).addClass("input-validation-error");
        },
        unhighlight: function(element, errorClass, validClass) {
            $(element).removeClass("input-validation-error");
        }
    });
    formValidate(validatorUnit);
    if ($("#frm_create_units").valid()) {
        httpHandler("/" + getBaseUrl() + "student/getstudentunit", "post", $("#frm_create_units").serialize(), function() {
            buildFloorUnitDataFromCache();
            $("#student_uuid").val($("#uuid").val());
            $("#" + $("#first_field").val()).focus();
            dialogHandler('Identify number of units per floor', $('#create_units'), createUnits, null, 300, function() {
                g_floor_count = $("#floor_count").val();
                g_floor_number_type = $("#floor_number_type").val();
                g_unit_number_type = $("#unit_number_type").val();
                showDialog();
                cacheFloorUnitData();
            }, false, false, false, "Save", "Back");
        }, null, false, "create_units", "error_label");
    }
}

function removeUnit(unit_uuid) {
    confirmDialog("remove_unit", "Confirm", "Are you sure you want to remove this unit?", function() {
        var data = {
            unit_uuid: unit_uuid
        };
        httpHandler("/" + getBaseUrl() + "student/removeunit", "post", data, loadUnits);
    });
}

function loadUnits() {
    httpHandler("/" + getBaseUrl() + "students/unitlist/" + $("#uuid").val() + "/", "get", null,
        function(html) {
            $("#tab-units").html(html);
        }, null, false);
}

function loadOtherContent() {
    loadAuditTrail();
    loadUnits();
}

function doUIUpdate() {
    loadOtherContent();
    $("#btn_create_unit").removeClass("hidden");
    $("#li-tab-cred").removeClass("tab_pending");
    $("#li-tab-gen").removeClass("tab_pending");
}

var g_floor_unit_data = {};

function cacheFloorUnitData() {
    var floor_unit_data = {};
    $("#frm_create_units input[type=number]").each(function() {
        floor_unit_data[$(this).attr("id")] = $(this).val();
    });
    g_floor_unit_data = floor_unit_data;
}

function buildFloorUnitDataFromCache() {
    if (g_floor_unit_data == null) {
        return;
    }
    $("#frm_create_units input[type=number]").each(function() {
        if (typeof g_floor_unit_data[$(this).attr("id")] !== 'undefined') {
            $(this).val(g_floor_unit_data[$(this).attr("id")]);
        }
    });
    g_floor_unit_data = {};
}

function toggleTabPending(tab_id, fields) {
    var success = true;
    fields.split(",").forEach(function(item, index) {
        if ($("#" + item).val() === "" || $("#" + item).hasClass("input-validation-error") || $("#" + item).hasClass("error")) {
            if (!$("#" + tab_id).hasClass("tab_pending")) {
                $("#" + tab_id).addClass("tab_pending");
            }
            success = false;
        }
    });
    if (success) {
        $("#" + tab_id).removeClass("tab_pending");
    }
}

function validField(field_name) {
    return getAllFields()[field_name];
}

function getAllFields() {
    return {
        name: { required: true },
        tel_no: { required: true, checkNum: false },
        no_of_units: { required: true, checkNum: true, minNum: true },
        client_id: { required: true },
        client_secret: { required: true },
        encryption_key: { required: true }
    }
}