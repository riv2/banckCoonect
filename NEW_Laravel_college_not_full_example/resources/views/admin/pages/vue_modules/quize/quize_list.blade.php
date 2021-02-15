<div class="form-group">
    <label for="self_with_teacher_hours" class="col-md-3 control-label">Вопросы</label>
    <div class="col-md-9">

        <table id="question-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
            <tbody>
            @foreach($quizeQuestionList as $question)
                <tr>
                    <td>{!! $question->question ? $question->question : ($question->question_en ? $question->question_en : $question->question_kz)  !!}</td>

                    <td class="text-right">

                        <div class="btn-group">
                            <a class="btn btn-default" onclick="showQuestionDialog('question-modal-{{$question->id}}')"><i class="md md-edit"></i></a>
                            <!--<a class="btn btn-default" onclick="deleteQuestion('question-modal-{{$question->id}}')"><i class="fa fa-remove"></i></a></li>-->
                            <a class="btn btn-default" href="{{route('adminSyllabusDeleteQuize', ['themeId' => isset($syllabus->id)])}}?id={{$question->id}}"><i class="fa fa-remove"></i></a></li>
                        </div>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

        <a onclick="createQuestionForm()" class="btn btn-default">Добавить вопрос <i class="fa fa-plus"></i></a>
    </div>
</div>

@include('admin.pages.vue_modules.quize.quize_edit')

<script type="text/javascript">
    function updateQuestionTable()
    {
        $('#question-table tbody').html('');

        $('.modal-question').each(function(index){
            var dialogId = $(this).attr('id');
            var question = $(this).find('textarea.question-text').val();

            if(!question) question = $(this).find('textarea.question-text_en').val();
            if(!question) question = $(this).find('textarea.question-text_kz').val();

            $('#question-table tbody').append('<tr>\n' +
                '                    <td>' + question + '</td>\n' +
                '\n' +
                '                    <td class="text-right">\n' +
                '\n' +
                '                        <div class="btn-group">\n' +
                '                            <a class="btn btn-default" onclick="showQuestionDialog(\'' + dialogId + '\')"><i class="md md-edit"></i></a>\n' +
                '                            <a class="btn btn-default" onclick="deleteQuestion(\'' + dialogId + '\')"><i class="fa fa-remove"></i></a></li>\n' +
                '                        </div>\n' +
                '                    </td>\n' +
                '                </tr>');
        });
    }
</script>