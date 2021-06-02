$(function(){

    function putCsvFilterFileInfo($ff, key, name, count) {
        if (key) {
            $ff.find('.grid_view-csv_filter-input').val(key);
            $ff.find('.grid_view-csv_filter-note-text').html('<i class="fa fa-file-text-o"></i> ' + name + ' (' + count + ')');
        } else {
            $ff.find('.grid_view-csv_filter-input').val("");
            $ff.find('.grid_view-csv_filter-note-text').html('CSV');
        }
    }

    $().on('apply.daterangepicker', function(ev, picker) {
        $(this).val(picker.startDate.format('MM/DD/YYYY') + ' - ' + picker.endDate.format('MM/DD/YYYY'));
    });

    $('input[name="datefilter"]')

    $("body").on('drag dragstart dragend dragover dragenter dragleave drop','.grid_view-csv_filter', function(e) {
        e.preventDefault();
        e.stopPropagation();
    }).on('click','.grid_view-remove-csv_filter', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var $ff     = $(this).parents('.grid_view-csv_filter');
        var gridId  = $ff.attr('data-grid_id');
        putCsvFilterFileInfo($ff, null);
        $('#'+gridId).yiiGridView('applyFilter');
        return false;
    })
    .on('dragover dragenter','.grid_view-csv_filter', function() {
        var $ff = $(this);
        $ff.addClass('is-dragover');
    })
    .on('dragleave dragend drop','.grid_view-csv_filter', function() {
        var $ff = $(this);
        $ff.removeClass('is-dragover');
    })
    .on('drop','.grid_view-csv_filter', function(e) {
        var $ff = $(this);

        var gridId = $ff.attr('data-grid_id');

        var droppedFiles = e.originalEvent.dataTransfer.files;

        var ajaxData = new FormData();

        if (droppedFiles) {
            $.each( droppedFiles, function(i, file) {
                ajaxData.append( 'csv-filter', file );
            });
        }

        $.ajax({
            url: '/site/search-csv-filter-upload?id='+$ff.attr('data-entity_id'),
            type: 'post',
            data: ajaxData,
            dataType: 'json',
            cache: false,
            contentType: false,
            processData: false,
            complete: function() {
                $ff.removeClass('is-uploading');
            },
            success: function(data) {
                $ff.addClass( data.success === true ? 'is-success' : 'is-error' );

                if (!data.success) {
                    alert(data.error);
                } else {
                    putCsvFilterFileInfo($ff, data.key, data.fileName, data.count);

                    $('#'+gridId).yiiGridView('applyFilter');
                }
            },
            error: function() {
                $ff.find('.grid_view-csv_filter-note-text').text("Ошибка");
            }
        });
    }).on('apply.daterangepicker', 'input[data-krajee-daterangepicker]', function(ev, picker) {
        $(this).val(picker.startDate.format('DD.MM.YYYY') + ' - ' + picker.endDate.format('DD.MM.YYYY'));
        $(this).parents('.grid-view').yiiGridView('applyFilter');
    }).on('cancel.daterangepicker', 'input[data-krajee-daterangepicker]', function(ev, picker) {
    });
});