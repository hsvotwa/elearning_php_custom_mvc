var EnumYesNo = {
    yes: 1,
    no: 2
};

$(function() {
    $.ajaxSetup({ 'cache': true });
    $("#tab").tabs();
});

var httpHandler = function(http_url, _type, _model, callBack, extra, showError, html_to_field, message_field) {
    var _returnedModel = null;
    $("#nav-div-prog").fadeIn();
    switch (_type) {
        case "post":
            $.post(http_url, _model)
                .done(function(response, status, jqxhr) {
                    $("#nav-div-prog").fadeOut();
                    response = JSON.parse(response);
                    if (response.success && typeof(response.html) !== 'undefined' &&
                        typeof(html_to_field) !== 'undefined' &&
                        $("#" + html_to_field).length) {
                        $("#" + html_to_field).html(response.html);
                        if (callBack !== null) {
                            callBack(response);
                        }
                        return;
                    }
                    if (typeof(response.message) !== "undefined") {
                        if (response.success) {
                            if (callBack !== null) {
                                callBack(response);
                            }
                            toastr.success(response.success, "Success");
                            return;
                        } else if (typeof(message_field) !== "undefined") {
                            $("#" + message_field).html(response.message);
                            setTimeout(function() {
                                $("#" + message_field).html('');
                            }, 8000);
                            return;
                        }
                        toastr.error(response.message, "Error");
                    } else if (response.success) {
                        if (callBack !== null) {
                            callBack(response);
                        }
                    }
                    if (typeof response.redirect_to !== 'undefined' && response.redirect_to !== '') {
                        return setTimeout(function() {
                            window.location = response.redirect_to
                        }, 3000);
                    }
                    if (response.title !== 'undefined' && response.title !== '') {
                        $("#main_title").html(response.title);
                        $(document).prop('title', response.title);
                    }
                    if (callBack !== null) {
                        callBack(response);
                        return;
                    }
                })
                .fail(function(jqxhr, status, error) {
                    $("#nav-div-prog").fadeOut();
                    showError = showError === null ? true : showError;
                    if (showError) {
                        toastr.error("Something went wrong. Please retry.", "Error");
                    }
                });
            break;
        case "get":
            $.get(http_url)
                .done(function(response, status, jqxhr) {
                    $("#nav-div-prog").fadeOut();
                    try {
                        var json = $.parseJSON(input);
                        if (typeof(json.message) !== 'undefined') {
                            toastr.error(json.message, "Error");
                            if (callBack !== null) {
                                callBack(json.message);
                            }
                        }
                    } catch (e) {
                        if (callBack !== null) {
                            callBack(response);
                        }
                    }
                    return;
                })
                .fail(function(jqxhr, status, error) {
                    $("#nav-div-prog").fadeOut();
                    showError = showError === null ? true : showError;
                    if (showError) {
                        toastr.error("Something went wrong. Please retry.", "Error");
                    }
                });
    }
    return _returnedModel;
};

/* Dialog Handler*/
var dialogHandler = function(title, _dialogContent, callBack, validateCallback, width, closeCallBack, autoOpen, closeOnSave, autoClose, saveBtnText, closeButtonText) {
    var _NewDialog;
    width = width === null ? 200 : width;
    _NewDialog = _dialogContent;
    _NewDialog.dialog({
        resizable: false,
        modal: true,
        autoOpen: (typeof autoOpen !== "undefined" ? autoOpen : true),
        width: width,
        title: title,
        buttons: [{
            text: (typeof closeButtonText === 'undefined' ? "Close" : closeButtonText),
            click: function() {
                if (closeCallBack === null) {
                    $(this).dialog("destroy");
                } else {
                    closeCallBack();
                }
            }
        }, {
            text: (typeof saveBtnText === 'undefined' ? "Save" : saveBtnText),
            click: function() {
                if (validateCallback !== null) {
                    if (validateCallback()) {
                        if (callBack !== null) {
                            callBack();
                        }
                    }
                } else {
                    if (callBack !== null) {
                        callBack();
                    }
                }
                if (closeOnSave) {
                    if (closeCallBack === null) {
                        $(this).dialog("destroy");
                    } else {
                        closeCallBack();
                    }
                }

            }
        }],
        beforeClose: function(event, ui) {
            if (closeOnSave) {
                event.preventDefault();
                if (closeCallBack === null) {
                    $(this).dialog("destroy");
                } else if (closeCallBack($(this))) {
                    $(this).dialog("destroy");
                }
            }
        },
        cancel: function() {
            $(this).dialog('close');
        }
    });
}

var dialogHandlerNoButtons = function(title, _dialogContent, callBack, validateCallback, width, closeCallBack, autoOpen, destroyOnClose, autoClose) {
    var _NewDialog;
    width = width === null ? 200 : width;
    _NewDialog = _dialogContent;
    _NewDialog.dialog({
        resizable: false,
        modal: true,
        autoOpen: (typeof autoOpen !== "undefined" ? autoOpen : true),
        hide: 'fade',
        show: 'fade',
        width: width,
        title: title,
        buttons: [{
            text: "Close",
            click: function() {
                $(this).dialog("close");
            }
        }]
    });
};

var confirmDialog = function(element, title, message, callBack) {
    if (!$("#" + element).length) {
        $('<div></div>').appendTo('body').html('<div id="' + element + '"><h6 class="form_label">' + message + '</h6></div>');
    }
    $("#" + element).dialog({
        modal: true,
        title: title,
        hide: 'fade',
        show: 'fade',
        zIndex: 10000,
        autoOpen: true,
        width: 'auto',
        resizable: false,
        buttons: {
            No: function() {
                $(this).dialog("close");
            },
            Yes: function() {
                callBack();
                $(this).dialog("close");
            }
        },
        close: function(event, ui) {
            $(this).dialog("close");
        }
    });
};

var formValidate = function(validator) {
    var errList = [];
    if (validator.errorList.length > 0) {
        var errString = "";
        for (var i = 0; i < validator.errorList.length; i++) {
            if ($.inArray(validator.errorList[i]["message"], errList) === -1) {
                errString += "• " + validator.errorList[i]["message"] + "<br />";
                errList.push(validator.errorList[i]["message"]);
            }
        }
        if (errString !== "") {
            validator.lastActive = validator.errorList[0].element;
            // validator.errorList[0].element.focus();
        }
    }
};

function numberFormat(nStr) {
    if (!reverse) {
        nStr += '';
        x = nStr.split('.');
        x1 = x[0];
        x2 = x.length > 1 ? '.' + x[1] : '.00';
        var rgx = /(\d+)(\d{3})/;
        while (rgx.test(x1)) {
            x1 = x1.replace(rgx, '$1' + ',' + '$2');
        }
        return x1 + (x1 === "" ? "" : x2);
    } else {
        return parseFloat(nStr.replace(/,/g, ''));
    }
}

$(".number-format").on("blur", function() {
    var orig = $(this).val();
    $(this).val(numberFormat(orig));
});

$(document).ready(function() {
    if (typeof($.validator) !== 'undefined') {
        $.validator.addMethod("checkNum", function(value, element) {
            x = value.split('.');
            x1 = x[0];
            return this.optional(element) || $.isNumeric(x1.replace(/,/g, ""));
        }, "The field value must be numeric");

        $.validator.addMethod("minNum", function(value, element) {
            x = value.split('.');
            x1 = x[0];
            return this.optional(element) || (x1.replace(/,/g, "") > 0);
        }, "The field value must be greater than 0");
    }
});

function close(dialog) {
    dialog.dialog("destroy");
}

function isEmpty(value) {
    if (typeof(value) === 'undefined' ||
        value === null) {
        return true;
    }
    if (typeof(value.length) !== 'undefined') {
        return value.length === 0;
    }
    if (typeof(value) === 'number' ||
        typeof(value) === 'boolean') {
        return false;
    }
    var count = 0;
    for (var i in value) {
        if (value.hasOwnProperty(i)) {
            count++;
        }
    }
    return count === 0;
}

function loadSelectOptions(model, name) {
    if ($("select[name=" + name + "]").length) {
        var field = $("select[name=" + name + "]");
        if (!isEmpty(model)) {
            field.html(model);
            field.removeAttr("disabled");
        } else {
            field.html("");
            field.attr("disabled", "disabled");
        }
    }
}


function genericGetAuditTrail(link_uuid, content_div) {
    httpHandler(base_url() + "/AuditTrail/get-audit-trail/" + link_uuid, "get", null, function(data) {
        $('#' + content_div).html(data);
    }, null, false);
}

function downloadFileFromPath(path, name) {
    var link = document.createElement('a');
    link.href = path;
    link.download = name;
    link.dispatchEvent(new MouseEvent('click'));
}

function openDialog(id) {
    $('#' + id).dialog('open');
}

function postFormAndFile(url, field, other_data, form_name, success_callback, message_field) {
    var file_upload = $("#" + field).get(0);
    var files = file_upload.files;
    var file_data = new FormData();
    $("input, textarea, select", form_name).each(function() {
        var field_name = $(this).attr('name');
        file_data.append(field_name, $(this).val());
    });

    if (other_data !== null) {
        // for (var item_key of Object.keys(other_data)) {
        //     file_data.append(item_key, other_data[item_key]);
        // }
        Object.keys(other_data).forEach(function(item, index) {
            file_data.append(item, other_data[item]);
        });
    }
    for (var i = 0; i < files.length; i++) {
        file_data.append("document", files[i]);
    }
    $.ajax({
        url: url,
        type: "POST",
        contentType: false,
        processData: false,
        data: file_data,
        success: function(data) {
            var response = data;
            response = JSON.parse(response);
            if (typeof(response.message) !== "undefined") {
                if (response.success) {
                    if (success_callback !== null) {
                        success_callback(response);
                    }
                } else if (typeof(message_field) !== "undefined") {
                    $("#" + message_field).html(response.message);
                    setTimeout(function() {
                        $("#" + message_field).html('');
                    }, 8000);
                    return;
                }
            } else if (response.success) {
                if (success_callback !== null) {
                    success_callback(response);
                }
            }
            if (response.Message !== "") {
                if (success_callback !== null) {
                    success_callback(response);
                }
                return;
            }
        },
        error: function(data) {
            var response = data;
            if (response.Message !== "") {
                return;
            }
        }
    });
}

function inArray(needle, haystack) {
    var length = haystack.length;
    for (var i = 0; i < length; i++) {
        if (haystack[i] === needle) {
            return true;
        }
    }
    return false;
}

function isNum(values) {
    if (!isArray(values)) {
        values = new Array(values);
    }
    var len = values.length,
        val = null;
    for (var i = 0; i < len; i++) {
        val = values[i].toString().replace(/,/g, '').replace(/ /g, '');
        if (val === '' ||
            isNaN(val)) {
            return false;
        }
    }
    return true;
}

function isArray(value) {
    return value instanceof Array;
}

function closeDialogue(_id) {
    $("#" + _id).dialog("close");
}

function getBaseUrl() {
    // if ( window.location.href.indexOf("account") > -1) {
    //     return "trustco1/";
    // }
    return "trustco1/";
}

function focusField(field_name) {
    $("#" + field_name).focus();
}

function validCompulsory(field, fields) {
    var arr = fields.split(",");
    if ($("#" + field).val() === "" && arr.indexOf(field) !== -1) {
        $("#" + field).addClass("input-validation-error");
        return false;
    }
    $("#" + field).removeClass("input-validation-error");
    return true;
}

function formatDatePart(val) {
    if (val.toString().length === 1) {
        return "0" + val;
    }
    return val;
}

function formatDatePart(val) {
    if (val.toString().length === 1) {
        return "0" + val;
    }
    return val;
}