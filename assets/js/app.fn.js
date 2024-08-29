(function($) {
	'use strict';
    
    $("form.frm-submit").each(function(i, el)
    {
        var $this = $(el);
        $this.on('submit', function(e){
            e.preventDefault();
            var btn = $this.find('[type="submit"]');
            
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function () {
                    btn.button('loading');
                },
                success: function (data) {
                    console.log(data.error);
                    $('.error').html("");
                    if (data.status == "fail") {
                        $.each(data.error, function (index, value) {
                            $this.find("[name='" + index + "']").parents('.form-group').find('.error').html(value);
                        });
                        btn.button('reset');
                    } else if (data.status == "access_denied") {
                        window.location.href = base_url + "dashboard";
                    } else {
                        if (data.url) {
                            window.location.href = data.url;
                        } else{
                            location.reload(true);
                        }
                    }
                },
                error: function () {
                    btn.button('reset');
                }
            });
        });
    });

    $("form.frm-submit-msg").each(function(i, el)
    {
        var $this = $(el);
        $this.on('submit', function(e){
            e.preventDefault();
            var btn = $this.find('[type="submit"]');
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: $(this).serialize(),
                dataType: 'json',
                beforeSend: function () {
                    btn.button('loading');
                },
                success: function (data) {
                    console.log(data.error);
                    $('.error').html("");
                    if (data.status == "fail") {
                        $.each(data.error, function (index, value) {
                            $this.find("[name='" + index + "']").parents('.form-group').find('.error').html(value);
                        });
                        btn.button('reset');
                    } else if (data.status == "access_denied") {
                        window.location.href = base_url + "dashboard";
                    } else {
                        swal({
                            toast: true,
                            position: 'top-end',
                            type: 'success',
                            title: data.message,
                            confirmButtonClass: 'btn btn-default',
                            buttonsStyling: false,
                            timer: 8000
                        });
                    }
                },
                complete: function (data) {
                    btn.button('reset'); 
                },
                error: function () {
                    btn.button('reset');
                }
            });
        });
    });

    $("form.frm-submit-data").each(function(i, el)
    {
        var $this = $(el);
        $this.on('submit', function(e){
            e.preventDefault();
            var btn = $this.find('[type="submit"]');
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: "json",
                contentType: false,
                processData: false,
                cache: false,
                beforeSend: function () {
                    btn.button('loading');
                },
                success: function (data) {
                    console.log(data.error);
                    $('.error').html("");
                    if (data.status == "fail") {
                        $.each(data.error, function (index, value) {
                            $this.find("[name='" + index + "']").parents('.form-group').find('.error').html(value);
                        });
                        btn.button('reset');
                    } else {
                        if (data.url) {
                            window.location.href = data.url;
                        } else if (data.status == "access_denied") {
                            window.location.href = base_url + "dashboard";
                        } else {
                            location.reload(true);
                        }
                    }
                },
                error: function () {
                    btn.button('reset');
                }
            });
        });
    });

    $("form.frm-submit-data-msg").each(function(i, el)
    {
        var $this = $(el);
        $this.on('submit', function(e){
            e.preventDefault();
            var btn = $this.find('[type="submit"]');
            $.ajax({
                url: $(this).attr('action'),
                type: "POST",
                data: new FormData(this),
                dataType: "json",
                contentType: false,
                processData: false,
                cache: false,
                beforeSend: function () {
                    btn.button('loading');
                },
                success: function (data) {
                    console.log(data.error);
                    $('.error').html("");
                    if (data.status == "fail") {
                        $.each(data.error, function (index, value) {
                            $this.find("[name='" + index + "']").parents('.form-group').find('.error').html(value);
                        });
                        btn.button('reset');
                    } else if (data.status == "access_denied") {
                        window.location.href = base_url + "dashboard";
                    } else {
                        swal({
                            toast: true,
                            position: 'top-end',
                            type: 'success',
                            title: data.message,
                            confirmButtonClass: 'btn btn-default',
                            buttonsStyling: false,
                            timer: 8000
                        });
                    }
                },
                complete: function (data) {
                    btn.button('reset'); 
                },
                error: function () {
                    btn.button('reset');
                }
            });
        });
    });

    // staff bank accountad modal show
    $('#advanceSalary').on('click', function(){
        mfp_modal('#advanceSalaryModal');
    });

    // user authentication
    $('#authentication_btn').on('click', function(){
        if(authenStatus == 0){
            $('#cb_authentication').prop('checked', true).prop('disabled', true);
            $('.password').val("").prop('disabled', true);
        }else{
            $('#cb_authentication').prop('checked', false).prop('disabled', false);
            $('.password').val("").prop('disabled', false);
        }
        mfp_modal('#authentication_modal');
    });
    
    $('#showPassword').on('click', function(){
        var password = $(this).parents('.form-group').find("[name='password']");
        if(password.prop('type') == 'password'){
            password.prop('type', 'text');
        }else{
            password.prop('type', 'password');
        }
    });
    
    $('#cb_authentication').on('click', function(){
        var password = $(this).parents('.form-group').find("[name='password']");
        if (this.checked) {
            password.val('').prop('disabled', true);
            
            $('#disableReason').show('slow');
        } else {
            $('#disableReason').hide('slow');
            password.prop('disabled', false);
        }
    });
})(jQuery);

function initDatatable(selector,url, params={}, pageLength=20, aoColumnDefs=[{"orderable": false, "targets": 'no-sort'},{"orderable": false, "targets": [-1],'class':'action'}]){
    var cusDataTable = $(selector).DataTable({
        "dom": '<"row"<"col-sm-6 mb-xs"B><"col-sm-6"f>><"table-responsive"tr>p',
        "lengthChange": false,
        "order": [],
        "pageLength": pageLength,
        "columnDefs": aoColumnDefs,
        "buttons": [
            {
                extend: 'copyHtml5',
                text: '<i class="far fa-copy"></i>',
                titleAttr: 'Copy',
                title: $('.export_title').html(),
                exportOptions: {
                    columns: [':not(:last-child, th.no-export)','th.isExport']
                }
            },
            {
                extend: 'excelHtml5',
                text: '<i class="fa fa-file-excel"></i>',
                titleAttr: 'Excel',
                title: $('.export_title').html(),
                exportOptions: {
                    columns: [':not(:last-child, th.no-export)','th.isExport']
                }
            },
            {
                extend: 'csvHtml5',
                text: '<i class="fa fa-file-alt"></i>',
                titleAttr: 'CSV',
                title: $('.export_title').html(),
                exportOptions: {
                    columns: [':not(:last-child, th.no-export)','th.isExport']
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fa fa-file-pdf"></i>',
                titleAttr: 'PDF',
                title: $('.export_title').html(),
                footer: true,
                customize: function ( win ) {
                    win.styles.tableHeader.fontSize = 10;
                    win.styles.tableFooter.fontSize = 10;
                    win.styles.tableHeader.alignment = 'left';
                },
                exportOptions: {
                    columns: [':not(:last-child, th.no-export)','th.isExport']
                }
            },
            {
                extend: 'print',
                text: '<i class="fa fa-print"></i>',
                titleAttr: 'Print',
                title: $('.export_title').html(),
                customize: function ( win ) {
                    $(win.document.body)
                        .css( 'font-size', '9pt' );

                    $(win.document.body).find( 'table' )
                        .addClass( 'compact' )
                        .css( 'font-size', 'inherit' );

                    $(win.document.body).find( 'h1' )
                        .css( 'font-size', '14pt' );
                },
                footer: true,
                exportOptions: {
                    columns: [':not(:last-child, th.no-export)','th.isExport']
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fas fa-columns"></i>',
                titleAttr: 'Columns',
                title: $('.export_title').html(),
                postfixButtons: ['colvisRestore']
            },
        ],
        'processing': true,
        'serverSide': true,
        'dataSrc': 'data',
        'serverMethod': 'POST',
        'ajax': {
            'url': base_url + url,
            'data': params,
        },
        "fnDrawCallback": function( ) {
            $('[data-toggle="tooltip"]').tooltip();
        },
    });
}

// swal alert message
function alertMsg(msg, type='success', title='' ,footer='*Note : You can undo this action at any time') {
    if (title === '') {
        title = (type == 'success' ? 'Successfully' : 'Error');
    }
    swal({
        type: type,
        title: title,
        text: msg,
        showCloseButton: true,
        focusConfirm: false,
        buttonsStyling: false,
        confirmButtonClass: 'btn btn-default swal2-btn-default',
        footer: footer
    });
}

// popup alert message
function popupMsg(msg, type='success') {
    swal({
        toast: true,
        position: 'top-end',
        type: type,
        title: msg,
        confirmButtonClass: 'btn btn-default',
        buttonsStyling: false,
        timer: 8000
    });
}

// staff documents edit modal show
function editDocument(id, user) {
    $.ajax({
        url: base_url + user + "/document_details",
        type: 'POST',
        data: {'id': id},
        dataType: 'json',
        success: function (res) {
            $('#edocument_id').val(res.id);
            $('#exist_file_name').val(res.enc_name);
            $('#edocument_title').val(res.title);
            if (user == 'employee') {
                $('#edocument_category').val(res.category_id).trigger('change.select2');
            } else {
                $('#edocument_category').val(res.type);
            }
            $('#edocuments_remarks').val(res.remarks);
            mfp_modal('#editDocModal');
        }
    });
}

function getExamByBranch(id) {
    $.ajax({
        url: base_url + 'ajax/getExamByBranch',
        type: 'POST',
        data: {
            branch_id: id
        },
        success: function (data) {
            $('#exam_id').html(data);
        }
    });
}

// get leave category
function getLeaveCategory(id) {
    $.ajax({
        url: base_url + 'ajax/getLeaveCategoryDetails',
        type: 'POST',
        data: {'id': id},
        dataType: "json",
        success: function (data) {
            $('.error').html('');
            $('#ecategory_id').val(data.id);
            $('#eleave_category').val(data.name);
            $('#eleave_days').val(data.days);
            $('#erole_id').val(data.role_id).trigger('change');
            if ($('#ebranch_id').length) {
                $('#ebranch_id').val(data.branch_id).trigger('change');
            }
            mfp_modal('#modal');
        }
    });
}

// get patient disable reason details
function getDisable_reason(id) {
    $.ajax({
        url: base_url + 'student/disableReasonDetails',
        type: 'POST',
        data: {'id': id},
        dataType: "json",
        success: function (data) {
            $('#ereason_id').val(data.id);
            $('#ecategory_name').val(data.name);
            $('#ebranch_id').val(data.branch_id).trigger('change');
            mfp_modal('#modal');
        }
    });
}

// get advance salary details
function getAdvanceSalaryDetails(id) {
    $.ajax({
        url: base_url + 'ajax/getAdvanceSalaryDetails',
        type: 'POST',
        data: {'id': id},
        dataType: "html",
        success: function (data) {
            $('#quick_view').html(data);
            mfp_modal('#modal');
        }
    });
}

function getCategoryModal(obj) {
    var id = $(obj).data("id");
    var name = $(obj).data('name');
    var branch = $(obj).data('branch');
    $('.error').html("");
    $('#ecategory_id').val(id);
    $('#ename').val(name);
    if ($('#ebranch_id').length) {
        $('#ebranch_id').val(branch).trigger('change');
    }
    mfp_modal('#modal');
}

function getClassByBranch(branch_id) {
    $.ajax({
        url: base_url + 'ajax/getClassByBranch',
        type: 'POST',
        data:{ branch_id: branch_id },
        beforeSend: function () {
            $('#select2-class_id-container').parent().addClass('select2loading');
        },
        success: function (data){
            $('#class_id').html(data);
        },
        complete: function () {
            $('#select2-class_id-container').parent().removeClass('select2loading');
        }
    });
    $('#section_id').html('');
    $('#section_id').append('<option value="">Select Class First</option>');
}

// get patient category details
function getStudentCategory(id, elem) {
    var btn = $(elem);
    $.ajax({
        url: base_url + 'student/categoryDetails',
        type: 'POST',
        data: {'id': id},
        dataType: "json",
        beforeSend: function () {
            btn.button('loading');
        },
        success: function (data) {
            $('#ecategory_id').val(data.id);
            $('#ecategory_name').val(data.name);
            $('#ebranch_id').val(data.branch_id).trigger('change');
            mfp_modal('#modal');
        },
        error: function (xhr) {
            btn.button('reset');
        },
        complete: function () {
            btn.button('reset');
        }
    });
}

// get patient category details
function getClassAssignM(class_id, section_id) {
    $.ajax({
        url: base_url + 'ajax/getClassAssignM',
        type: 'POST',
        data: {
            class_id: class_id,
            section_id: section_id
        },
        dataType: "json",
        success: function (data) {
            $('#ebranch_id').val(data.branch_id);
            $('#eclass_id').val(data.class_id);
            $('#esection_id').val(data.section_id);
            $('#esubject_holder').html(data.subject);
            mfp_modal('#modal');
        }
    });
}

function getSectionByClass(class_id, all=0, multi=0) {
    if (class_id !== "") {
        $.ajax({
            url: base_url + 'ajax/getSectionByClass',
            type: 'POST',
            data: {
                class_id: class_id,
                all : all,
                multi : multi
            },
            beforeSend: function () {
                $('#select2-section_id-container').parent().addClass('select2loading');
            },
            success: function (response) {
                $('#section_id').html(response);
            },
            complete: function () {
                $('#select2-section_id-container').parent().removeClass('select2loading');
            }
        });
    }
}

function getStaffListRole(branchID = '', roleID = '') {
    if (branchID != '' && roleID != '') {
        $.ajax({
            url: base_url + "ajax/getStafflistRole",
            type: 'POST',
            data: {
                branch_id: branchID,
                role_id: roleID
            },
            success: function (data) {
                $('#staff_id').html(data);
            }
        });
    }
}

function getExamTimetableM(examID, classID, sectionID) {
    $.ajax({
        url: base_url + 'timetable/getExamTimetableM',
        type: 'POST',
        data: {
            exam_id: examID,
            class_id: classID,
            section_id: sectionID
        },
        dataType: "html",
        success: function (data) {
            $('#quick_view').html(data);
            mfp_modal('#modal');
        }
    });
}

function getSubjectByClass(id) {
    $.ajax({
        url: base_url + 'ajax/getSubjectByClass',
        type: 'POST',
        data: {
            classID: id
        },
        success: function (response) {
            $('#subject_id').html(response);
        }
    });
}


function getDesignationByBranch(id) {
    $.ajax({
        url: base_url + 'ajax/getDataByBranch',
        type: 'POST',
        data: {
            table: "staff_designation",
            branch_id: id
        },
        success: function (response) {
            $('#designation_id').html(response);
        }
    });
}

function getDepartmentByBranch(id) {
    $.ajax({
        url: base_url + 'ajax/getDataByBranch',
        type: 'POST',
        data: {
            table: "staff_department",
            branch_id: id
        },
        success: function (response) {
            $('#department_id').html(response);
        }
    });
}

function selAtten_all(val) {
    if (val == "") {
        $('input[type="radio"]').prop('checked', false);
    } else {
        $('input[type="radio"]').filter('[value="' + val + '"]').prop('checked', true);
    }
}

function getSectionByBranch(id) {
    $.ajax({
        url: base_url + 'ajax/getDataByBranch',
        type: 'POST',
        data: {
            table: "section",
            branch_id: id
        },
        success: function (response) {
            $('#section_id').html(response);
        }
    });
}

function getHallModal(obj) {
    var id = $(obj).data("id");
    var hall_no = $(obj).data('number');
    var seats = $(obj).data('seats');
    var branch = $(obj).data('branch');
    $('.error').html("");
    $('#hall_id').val(id);
    $('#ehall_no').val(hall_no);
    $('#eno_of_seats').val(seats);
    if ($('#ebranch_id').length) {
        $('#ebranch_id').val(branch).trigger('change');
    }
    mfp_modal('#modal');
}


function read_number(inputelm) {
    if (isNaN(inputelm) || inputelm.length == 0 ) {
        return 0;
    } else {
        return parseFloat(inputelm);
    }
}

// get salary template
function getSalaryTemplate(id) {
    $.ajax({
        url: base_url + 'ajax/get_salary_template_details',
        type: 'POST',
        data: {'id': id},
        dataType: "html",
        success: function (data) {
            $('#quick_view').html(data);
            mfp_modal('#modal');
        }
    });
}

// get department details
function getDepartmentDetails(id) {
    $.ajax({
        url: base_url + 'ajax/department_details',
        type: 'POST',
        data: {'id': id},
        dataType: "json",
        success: function (data) {
            $('.error').html("");
            $('#edepartment_id').val(data.id);
            $('#cdepartment_name').val(data.name);
            $('#ebranch_id').val(data.branch_id).trigger('change');
            mfp_modal('#modal');
        }
    });
}

// get designation details
function getDesignationDetails(id) {
    $.ajax({
        url: base_url + 'ajax/designation_details',
        type: 'POST',
        data: {'id': id},
        dataType: "json",
        success: function (data) {
            $('#edesignation_id').val(data.id);
            $('#edesignation_name').val(data.name);
            $('#ebranch_id').val(data.branch_id).trigger('change');
            mfp_modal('#modal');
        }
    });
}

// staff bank account edit modal show
function editStaffBank(id) {
    $.ajax({
        url: base_url + "employee/bank_details",
        type: 'POST',
        data: {'id' : id},
        dataType: 'json',
        success: function (res) {
            $('#ebank_id').val(res.id);
            $('#ebank_name').val(res.bank_name);
            $('#eholder_name').val(res.holder_name);
            $('#ebank_branch').val(res.bank_branch);
            $('#ebank_address').val(res.bank_address);
            $('#eifsc_code').val(res.ifsc_code);
            $('#eaccount_no').val(res.account_no);
            mfp_modal('#editBankModal');
        }
    });
}

// print function
function fn_printElem(elem, html = false)
{
    if (html == false) {
        var oContent = document.getElementById(elem).innerHTML;
    } else {
       var oContent = elem; 
    }
    var frameDoc=window.open('', 'document-print');
    frameDoc.document.open();
    //create a new HTML document.
    frameDoc.document.write('<html><head><title></title>');
    frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'assets/vendor/bootstrap/css/bootstrap.min.css">');
    if (isRTLenabled == '1') {
        frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'assets/css/bootstrap.rtl.min.css">');
    }
    frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'assets/css/custom-style.css">');
    frameDoc.document.write('<link rel="stylesheet" href="' + base_url + 'assets/css/ramom.css">');
    frameDoc.document.write('</head><body onload="window.print()">');
    frameDoc.document.write(oContent);
    frameDoc.document.write('</body></html>');
    frameDoc.document.close();
    setTimeout(function () {
        frameDoc.close();      
    }, 5000);
    return true;
}