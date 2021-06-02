$(function(){
    $('.project_theme-tabs a[data-toggle="tab"]').on('click', function () {
        var $filters = $('#projectIndexGrid-filters');
        var $projectThemeInput = $filters.find('[name="Project[project_theme_id]"]');
        if ($projectThemeInput.length == 0) {
            $projectThemeInput = jQuery('<input type="hidden" name="Project[project_theme_id]" />');
            $filters.append($projectThemeInput);
        }
        var value = $(this).attr('href').replace('#','');
        $projectThemeInput.val(value);
        $('#projectIndexGrid').yiiGridView('applyFilter');
    });
});