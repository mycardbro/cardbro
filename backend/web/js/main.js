//Start upload
$('#start_upload').click(function (e){
    e.preventDefault();
//    if ($('#uploader-file').val() !== '') {
    if ($('#parser-file').val() !== '') {
        $('#start_upload').html('Uploading <i class="fa fa-refresh fa-spin fa-fw"></i>');
        var formData = new FormData($('#upload_modal')[0]);
        // console.log(formData);
        $.ajax({
            type: 'POST',
            url: 'upload',
            data: formData,
            processData: false,
            contentType: false,
            success: function (data) {
                if (data.code == '200'){
                    $('#myModal').modal('hide');
                    $('.modal_left').html(data.text)
                    $('.modal_right').html('<i class="fa fa-check" aria-hidden="true"></i>');
                    $('#myModal2').modal('show');
                } else if (data.code == '400') {
                    $('#myModal').modal('hide');
                    $('#uploader-file').val('');
                    $('#start_upload').html('Start upload');
                    $('.modal_left').html(data.text);
                    $('.modal_right').html('<i class="fa fa-times" aria-hidden="true"></i>');
                    $('#myModal2').modal('show');
                } else {
                    alert('something went wrong');
                }
            }
        });
    }
});

//Start upload card
//Start upload
$('#start_upload_card').click(function (e){
    e.preventDefault();
   // if ($('#uploader-file').val() !== '') {
        if ($('#parser-file_card').val() !== '') {
            $('#start_upload_card').html('Uploading <i class="fa fa-refresh fa-spin fa-fw"></i>');
            var formData = new FormData($('#upload_modal_card')[0]);
            // console.log(formData);
            $.ajax({
                type: 'POST',
                url: 'uploadcards',
                data: formData,
                processData: false,
                contentType: false,
                success: function (data) {
                    if (data.code == '200') {
                        $('#myModalCard').modal('hide');
                        $('.modal_left').html(data.text)
                        $('.modal_right').html('<i class="fa fa-check" aria-hidden="true"></i>');
                        $('#myModal2').modal('show');
                    } else if (data.code == '400') {
                        $('#myModalCard').modal('hide');
                        $('#uploader-file_card').val('');
                        $('#start_upload_card').html('Start upload');
                        $('.modal_left').html(data.text);
                        $('.modal_right').html('<i class="fa fa-times" aria-hidden="true"></i>');
                        $('#myModal2').modal('show');
                    } else {
                        alert('something went wrong');
                    }
                }
            });
        }
});
//get selected id and export as CSV
$('.btn.btn-success.grey.bulk').click(function () {
    var id_arr = [];
    var IDs = [];
    $('tbody').find('input:checked').each(function(){
        id_arr.push($(this).parents('tr').attr('data-key'));
    });
    console.log(id_arr);
    $.ajax({
        type: 'POST',
        url: 'download',
        data: {data: id_arr},
        success: function (data) {
            window.location = data;
        }
    });
});

//get partners selected id and export as CSV
$('.btn.btn-success.grey.bulk-partner').click(function () {
    var id_arr = [];
    var IDs = [];
    $('tbody').find('input:checked').each(function(){
        id_arr.push($(this).parents('tr').attr('data-key'));
    });
    console.log(id_arr);
    $.ajax({
        type: 'POST',
        url: 'partner',
        data: {data: id_arr},
        success: function (data) {
            window.location = data;
        }
    });
});

$('.btn.btn-success.blue.remind').click(function () {
    $.ajax({
        type: 'POST',
        url: 'send',
        data: {},
        success: function (data) {
            if (data.code == '200') {
                $('.modal_left').html(data.text)
                $('.modal_right').html('<i class="fa fa-check" aria-hidden="true"></i>');
                $('#myModal2').modal('show');
            } else if (data.code == '400') {
                $('.modal_left').html(data.text);
                $('.modal_right').html('<i class="fa fa-times" aria-hidden="true"></i>');
                $('#myModal2').modal('show');
            } else {
                alert('something went wrong');
            }
        }
    });
});

$('.btn.btn-success.blue.report').click(function () {
     //var id_arr = [];
     //$('tbody').find('input:checked').each(function () {
     //    id_arr.push($(this).parents('tr').attr('data-key'));
     //});
     //console.log(id_arr);
    $.ajax({
        type: 'POST',
        url: 'report',
        cache: false,
        //data: {data: id_arr},
        success: function (data) {
            window.location = data;
        }
    });

    $(document).ajaxStop(function(){
        console.log('OK!');
        window.location.reload();
    });
});

$('.btn.btn-success.grey.mdelete').click(function () {
    var id_arr = [];
    $('tbody').find('input:checked').each(function () {
        id_arr.push($(this).parents('tr').attr('data-key'));
    });
    console.log(id_arr);
    $.ajax({
        type: 'POST',
        url: 'mdelete',
        data: {data: id_arr},
        success: function (data) {
            if (data.code == '200') {
                $('.modal_left').html(data.text)
                $('.modal_right').html('<i class="fa fa-check" aria-hidden="true"></i>');
                $('#myModal2').modal('show');
            } else if (data.code == '400') {
                $('.modal_left').html(data.text);
                $('.modal_right').html('<i class="fa fa-times" aria-hidden="true"></i>');
                $('#myModal2').modal('show');
            } else {
                alert('something went wrong');
            }
        }
    });
});


$('.row_click').click(function(){
    if ($('#modal').data('bs.modal').isShown) {
        $('#modal').find('#modalContent')
            .load($(this).attr('href'));
        //dynamiclly set the header for the modal
        document.getElementById('modalHeader').innerHTML = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h4>' + $(this).attr('title') + '</h4>';
    } else {
        //if modal isn't open; open it and load content
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('href'));
        //dynamiclly set the header for the modal
        document.getElementById('modalHeader').innerHTML = '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button><h4>' + $(this).attr('title') + '</h4>';
    }
});

$('.row_check').click(function (e) {
    e.stopPropagation();
    if ($('.row_check:checked').length > 0 ){
        $('.btn.btn-success.grey.bulk.disabled').removeClass('disabled');
        $('.btn.btn-success.grey.bulk-partner.disabled').removeClass('disabled');
        $('.btn.btn-success.grey.remind.bulk.disabled').removeClass('disabled');
        $('.btn.btn-success.grey.report.bulk.disabled').removeClass('disabled');
        $('.btn.btn-success.grey.mdelete.bulk.disabled').removeClass('disabled');
    } else {
        $('.btn.btn-success.grey.bulk').addClass('disabled');
        $('.btn.btn-success.grey.bulk-partner').addClass('disabled');
        $('.btn.btn-success.grey.remind.bulk').addClass('disabled');
        $('.btn.btn-success.grey.report.bulk').addClass('disabled');
        $('.btn.btn-success.grey.mdelete.bulk').addClass('disabled');
    }
});

$('table.data > thead:first-child > tr:first-child > th:first-child').html('<input type="checkbox" class="row_check_all">');

$('.row_check_all').click(function (e) {
    e.stopPropagation();
    if ($('.row_check_all').prop('checked')){
        $('.row_check').prop('checked', true);
    } else {
        $('.row_check').prop('checked', false);
    }
    if ($('.row_check:checked').length > 0 ){
        $('.btn.btn-success.grey.download.bulk.disabled').removeClass('disabled');
        $('.btn.btn-success.grey.mdelete.bulk.disabled').removeClass('disabled');
        $('.btn.btn-success.grey.report.bulk.disabled').removeClass('disabled');
        $('.btn.btn-success.grey.remind.bulk.disabled').removeClass('disabled');
    } else {
        $('.btn.btn-success.grey.download.bulk').addClass('disabled');
        $('.btn.btn-success.grey.mdelete.bulk').addClass('disabled');
        $('.btn.btn-success.grey.report.bulk').addClass('disabled');
        $('.btn.btn-success.grey.remind.bulk').addClass('disabled');
    }
});

$('.show_filters').click(function () {
    $('.filters').toggle();
});

$(document).on('click', '.btn.btn-primary.update', function(){
    if ($('#modal').data('bs.modal').isShown) {
        $('#modal').find('#modalContent')
            .load($(this).attr('href'));
    } else {
        //if modal isn't open; open it and load content
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('href'));
    }
});
$(document).on('click', '.btn.btn-primary.edit', function(){
    if ($('#modal').data('bs.modal').isShown) {
        $('#modal').find('#modalContent')
            .load($(this).attr('href'));
    } else {
        //if modal isn't open; open it and load content
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('href'));
    }
});
$(document).on('click', '.btn.btn-success.replace', function(){
    if ($('#modal').data('bs.modal').isShown) {
        $('#modal').find('#modalContent')
            .load($(this).attr('href'));
    } else {
        //if modal isn't open; open it and load content
        $('#modal').modal('show')
            .find('#modalContent')
            .load($(this).attr('href'));
    }
});

$(document).ready(
    //show filters after reload if not empty
    function () {
        var show = false;
        $('.filters td input').each(function () {
            if ($(this).val() != ''){
                show = true;
                return false;
            }
        });
        if (show){
            $('.filters').show();
        }

        $('.search .form-control').keyup(function () {
            $('.dataTables_filter input').val($(this).val());
            $('.dataTables_filter input').keyup();
            if ($(this).val() != '' && ($('.dataTables_empty').length == 0)){
                $('.start_search').hide();
                $('.customers.table-responsive').show();
            } else {
                $('.start_search').show();
                $('.customers.table-responsive').hide();
            }
        });

        if ($('#daterange').length > 0){
            $('#daterange').daterangepicker({
                // "startDate": "",
                // "endDate": "",
                locale: {
                    format: 'YYYY-MM-DD'
                }
            });
        }

        //refresh page after modal with result hidden
        $('#myModal2').on('hidden.bs.modal', function () {
            if ($('.modal_right').html() == '<i class="fa fa-check" aria-hidden="true"></i>'){
                window.location.reload();
            }
        })
    }
);

/* Custom filtering function which will search data in column four between two values */
$.fn.dataTable.ext.search.push(
    function( settings, data, dataIndex ) {
        var min = new Date($('input[name="daterangepicker_start"]').val()) || Date();
        var max = new Date($('input[name="daterangepicker_end"]').val());
        var age = new Date(data[8]); // use data for the TIMESTAMP column

        if ( ( isNaN( min ) && isNaN( max ) ) ||
            ( isNaN( min ) && age <= max ) ||
            ( min <= age   && isNaN( max ) ) ||
            ( min <= age   && age <= max ) )
        {
            return true;
        }
        return false;
    }
);

$(document).ready(function() {
    $('#daterange').val('');
    $('.applyBtn').click( function() {
        var table = $('.table-responsive table').DataTable();
        table.draw();
    } );

    $('#daterange').on('cancel.daterangepicker', function(ev, picker) {
        //do something, like clearing an input
        $('#daterange').val('');
        $('input[name="daterangepicker_start"]').val('');
        $('input[name="daterangepicker_end"]').val('');
        var table = $('.table-responsive table').DataTable();
        table.draw();
    });
} );

function clearForm() {
    document.getElementById("upload_modal").reset();
    $('#upload_modal #start_upload').html('Start upload');
}

$(document).ready(function(){
    $("#replace-reason").change(function(){
        if ($('#replace-reason').find(":selected").attr('class') == 'showcomment') {
            $("#ptext").show();
        } else {
            $("#ptext").hide();
        }
    });
});


$(document).ready(function(){
    $("#replace-reason-new").change(function(){
        if ($('#replace-reason-new').find(":selected").attr('class') == 'showcomment') {
            $("#ptext-new").show();
        } else {
            $("#ptext-new").hide();
        }
    });
});