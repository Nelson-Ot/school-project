(function ($) {
    'use strict';
    $(document).ready(function () {
        if ($(".question_note").length) {
            var summernoteEditor = $('.question_note').summernote({
                dialogsInBody: true,
                code: " ",
                height: 150,
                toolbar: [
                    ["style", ["style"]],
                    ["name", ["fontname", "fontsize"]],
                    ["font", ["bold", "italic", "underline", "clear"]],
                    ["color", ["color"]],
                    ["para", ["ul", "ol", "paragraph"]],
                    ["insert", ["link", "table", "picture"]],
                    ["misc", ["fullscreen", "undo", "codeview"]]
                ],
            });

            summernoteEditor.on("summernote.enter", function (we, e) {
                $(this).summernote("pasteHTML", "<br><br>");
                e.preventDefault();
            });
        }

        $('.chk-sendsmsmail').on('change', function () {
            if ($(this).is(':checked')) {
                $(this).parents('.form-group').find('select > option').prop("selected", "selected");
                $(this).parents('.form-group').find('select').trigger("change");
            } else {
                $(this).parents('.form-group').find('select').val(null).trigger('change');
            }
        });

        $("#examType").on("change", function (ev) {
            if ($(this).val() == 0) {
                $("#examFee").hide("slow");
            } else {
                $("#examFee").show("slow");
            }
        });
    });
})(jQuery);

// exam duration update
function durationUpdate() {
    function zeroPad(number) {
        return (number < 10 ? '0' : '') + number;
    }
    splitTime = examDuration.split(':');
    var hours = parseInt(splitTime[0], 10);
    var minutes = parseInt(splitTime[1], 10);
    var seconds = parseInt(splitTime[2], 10);
    --seconds;
    minutes = (seconds < 0) ? --minutes : minutes;
    seconds = (seconds < 0) ? 59 : seconds;
    hours = (minutes < 0) ? --hours : hours;
    minutes = (minutes < 0) ? 59 : minutes;

    hours = zeroPad(hours);
    minutes = zeroPad(minutes);
    seconds = zeroPad(seconds);
    if (hours < 0)
        clearInterval(interval);

    if ((seconds <= 0) && (minutes <= 0) && (hours <= 0)) {
        clearInterval(interval);
        $('#answerForm').submit();
    }

    examDuration = hours + ":" + minutes + ":" + seconds;
    var r = hours + ":" + minutes + ":" + seconds;
    return r;
}

function clearAnswer() {
    $.each($('.step-pane.active input'), function () {
        elementType = $(this).attr('type');
        if (elementType == 'radio' || elementType == 'checkbox') {
            $(this).prop('checked', false);
        } else if (elementType == 'text') {
            $(this).val('');
        }
    });
}

function makeAnswered(questionID) {
    var answer = 0;
    $.each($('#answerForm #step' + questionID + ' input'), function () {
        elementType = $(this).attr('type');
        if (elementType == 'radio' || elementType == 'checkbox') {
            if ($(this).prop('checked')) {
                answer = 1;
                return answer;
            }
        } else if (elementType == 'text') {
            if ($(this).val() != '') {
                answer = 1;
                return answer;
            }
        }
    });
    if (answer == 1) {
        $('#question' + questionID).html('<i class="fas fa-check"></i>');
    } else {
        $('#question' + questionID).html(questionID);
    }
}

function getQuestionGroup(id) {
    $.ajax({
        url: base_url + 'onlineexam/groupDetails',
        type: 'POST',
        data: { 'id': id },
        dataType: "json",
        success: function (data) {
            $('#egroup_id').val(data.id);
            $('#egroup_name').val(data.name);
            $('#ebranch_id').val(data.branch_id).trigger('change');
            mfp_modal('#modal');
        }
    });
}

function getQuestion(id, elem) {
    var btn = $(elem);
    $.ajax({
        url: base_url + 'onlineexam/getQuestion',
        type: 'POST',
        data: { 'id': id },
        dataType: "html",
        beforeSend: function () {
            btn.button('loading');
        },
        success: function (data) {
            $('#quick_view').html(data);
            mfp_modal('#modal');
        },
        error: function (xhr) {
            btn.button('reset');
        },
        complete: function () {
            btn.button('reset');
            btn.tooltip("hide");
        }
    });
}

$("#examType").on("change", function () {
    if (this.value == 1) {
        $("#examFee").show("slow");
    } else {
        $("#examFee").hide("slow");
    }
});

$(document).on("click", ".exam-status", function () {
    var state = $(this).prop('checked');
    var id = $(this).data('id');
    if (state != null) {
        $.ajax({
            type: 'POST',
            url: base_url + "onlineexam/exam_status",
            data: {
                id: id,
                status: state
            },
            dataType: "json",
            success: function (data) {
                if (data.status == true) {
                    swal({
                        title: "Successfully",
                        text: data.msg,
                        buttonsStyling: false,
                        showCloseButton: true,
                        focusConfirm: false,
                        confirmButtonClass: "btn btn-default swal2-btn-default",
                        type: "success"
                        }).then((result) => {
                            if (result.value) {
                               window.location.reload();
                            }
                        }
                    );
                }
            }
        });
    }
});

function getExamByClass(class_id) {
    $.ajax({
        url: base_url + 'onlineexam/getExamByClass',
        type: 'POST',
        data: { class_id: class_id },
        success: function (data) {
            $('#examID').html(data);
        }
    });
}

function getStudentResult(id) {
    $.ajax({
        url: base_url + 'userrole/getStudent_result',
        type: 'POST',
        data: { 'id': id },
        dataType: "html",
        success: function (data) {
            $('#quick_view').html(data);
            mfp_modal('#modal');
        }
    });
}

function getAdminStudentResult(examID, studentID) {
    $.ajax({
        url: base_url + 'onlineexam/getStudent_result',
        type: 'POST',
        data: { 'examID': examID, 'studentID': studentID },
        dataType: "html",
        success: function (data) {
            $('#quick_view').html(data);
            mfp_modal('#modal');
        }
    });
}