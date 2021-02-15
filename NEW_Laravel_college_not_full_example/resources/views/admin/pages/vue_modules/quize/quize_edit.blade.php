
<div id="question-modal-list">

    @foreach($quizeQuestionList as $question)
    <div id="question-modal-{{ $question->id }}" class="modal modal-question fade" tabindex="-1" role="dialog" aria-labelledby="">
        {!! Form::open(array('url' => $questionEditUrl,'class'=>'form-horizontal padding-15','name'=>'service_form','id'=>'service_form','role'=>'form', 'enctype' => 'multipart/form-data')) !!}
        <div class="modal-dialog modal-lg" style="min-width:950px;" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">Вопрос</h4>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Вопрос</label>
                        <div class="col-sm-9">
                            <ul class="nav nav-tabs nav-tabs-question-{{$question->id}}">
                                <li role="presentation" onclick="changeQuestionLang('ru', {{$question->id}})" class="active tab-ru"><a href="#">Русский</a></li>
                                <li role="presentation" onclick="changeQuestionLang('en', {{$question->id}})" class="tab-en"><a href="#">Английский</a></li>
                                <li role="presentation" onclick="changeQuestionLang('kz', {{$question->id}})" class="tab-kz"><a href="#">Казахский</a></li>
                                <li role="presentation" onclick="changeQuestionLang('fr', {{$question->id}})" class="tab-fr"><a href="#">Французский</a></li>
                                <li role="presentation" onclick="changeQuestionLang('ar', {{$question->id}})" class="tab-ar"><a href="#">Арабский</a></li>
                                <li role="presentation" onclick="changeQuestionLang('de', {{$question->id}})" class="tab-de"><a href="#">Немецкий</a></li>
                            </ul>
                            <div class="question_lang_block_{{$question->id}}" id="question_lang_ru_{{$question->id}}">
                                <textarea type="text" name="questions[update][{{ $question->id }}][question]" class="form-control question-text summernote" rows="2">{{ $question->question }}</textarea>
                            </div>
                            <div class="question_lang_block_{{$question->id}}" id="question_lang_en_{{$question->id}}" style="display: none;">
                                <textarea type="text" name="questions[update][{{ $question->id }}][question_en]" class="form-control question-text_en summernote" rows="2">{{ $question->question_en }}</textarea>
                            </div>
                            <div class="question_lang_block_{{$question->id}}" id="question_lang_kz_{{$question->id}}" style="display: none;">
                                <textarea type="text" name="questions[update][{{ $question->id }}][question_kz]" class="form-control question-text_kz summernote" rows="2">{{ $question->question_kz }}</textarea>
                            </div>
                            <div class="question_lang_block_{{$question->id}}" id="question_lang_fr_{{$question->id}}" style="display: none;">
                                <textarea type="text" name="questions[update][{{ $question->id }}][question_fr]" class="form-control question-text_fr summernote" rows="2">{{ $question->question_fr }}</textarea>
                            </div>
                            <div class="question_lang_block_{{$question->id}}" id="question_lang_ar_{{$question->id}}" style="display: none;">
                                <textarea type="text" name="questions[update][{{ $question->id }}][question_ar]" class="form-control question-text_ar summernote" rows="2">{{ $question->question_ar }}</textarea>
                            </div>
                            <div class="question_lang_block_{{$question->id}}" id="question_lang_de_{{$question->id}}" style="display: none;">
                                <textarea type="text" name="questions[update][{{ $question->id }}][question_de]" class="form-control question-text_de summernote" rows="2">{{ $question->question_de }}</textarea>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="" class="col-sm-3 control-label">Аудиофайл</label>
                        <div class="col-sm-9">
                            @if(count($question->audiofiles) > 0)
                                <audio style="width: 100%" src="/audio/{{ $question->audiofiles[0]->filename }}" controls></audio>
                            @endif
                            <input type="file" name="questions[update][{{ $question->id }}][audio]" />
                        </div>
                    </div>
                    <hr>
                    <div class="answerBlock">
                        @if(isset($question->answers))
                        @foreach($question->answers as $answer)
                        <div class="form-group" id="answer-{{ $answer->id }}">
                            <div class="col-sm-3 control-label">
                                <div class="answerTitle">Ответ <a onclick="removeAnswer({{ $answer->id }})" class="btn btn-default"><i class="fa fa-trash"></i></a></div><br />
                                <label>
                                    <input type="checkbox" onchange="changeCorrect({{ $answer->id }}, this, 'update' )" value="true" @if($answer->correct) checked @endif name="questions[update][{{ $question->id }}][answers][update][{{ $answer->id }}][correct]"> Правильный
                                </label>

                                <div class="form-group" id="points-answer-update{{ $answer->id }}" @if(!$answer->correct) style="display: none;" @endif>
                                    <label for="" class="col-sm-3 control-label">Баллов</label>
                                    <div class="col-sm-9">
                                        <input type="text" name="questions[update][{{ $question->id }}][answers][update][{{ $answer->id }}][points]" value="{{ $answer->points }}" class="form-control">
                                    </div>
                                </div>
                            </div>

                            <div class="col-sm-9">
                                <ul class="nav nav-tabs nav-tabs-answer-{{ $answer->id }}">
                                    <li role="presentation" onclick="changeAnswerLang('ru', {{ $answer->id }})" class="active tab-ru"><a href="#">Русский</a></li>
                                    <li role="presentation" onclick="changeAnswerLang('en', {{ $answer->id }})" class="tab-en"><a href="#">Английский</a></li>
                                    <li role="presentation" onclick="changeAnswerLang('kz', {{ $answer->id }})" class="tab-kz"><a href="#">Казахский</a></li>
                                    <li role="presentation" onclick="changeAnswerLang('fr', {{ $answer->id }})" class="tab-fr"><a href="#">Французский</a></li>
                                    <li role="presentation" onclick="changeAnswerLang('ar', {{ $answer->id }})" class="tab-ar"><a href="#">Арабский</a></li>
                                    <li role="presentation" onclick="changeAnswerLang('de', {{ $answer->id }})" class="tab-de"><a href="#">Немецкий</a></li>
                                </ul>
                                <div class="answer_lang_block_{{$answer->id}}" id="answer_lang_ru_{{$answer->id}}">
                                    <textarea type="text" name="questions[update][{{ $question->id }}][answers][update][{{ $answer->id }}][answer]" class="form-control summernote" rows="1">{{ $answer->answer }}</textarea>
                                </div>
                                <div class="answer_lang_block_{{$answer->id}}" id="answer_lang_en_{{$answer->id}}" style="display: none;">
                                    <textarea type="text" name="questions[update][{{ $question->id }}][answers][update][{{ $answer->id }}][answer_en]" class="form-control summernote" rows="1">{{ $answer->answer_en }}</textarea>
                                </div>
                                <div class="answer_lang_block_{{$answer->id}}" id="answer_lang_kz_{{$answer->id}}" style="display: none;">
                                    <textarea type="text" name="questions[update][{{ $question->id }}][answers][update][{{ $answer->id }}][answer_kz]" class="form-control summernote" rows="1">{{ $answer->answer_kz }}</textarea>
                                </div>
                                <div class="answer_lang_block_{{$answer->id}}" id="answer_lang_fr_{{$answer->id}}" style="display: none;">
                                    <textarea type="text" name="questions[update][{{ $question->id }}][answers][update][{{ $answer->id }}][answer_fr]" class="form-control summernote" rows="1">{{ $answer->answer_fr }}</textarea>
                                </div>
                                <div class="answer_lang_block_{{$answer->id}}" id="answer_lang_ar_{{$answer->id}}" style="display: none;">
                                    <textarea type="text" name="questions[update][{{ $question->id }}][answers][update][{{ $answer->id }}][answer_ar]" class="form-control summernote" rows="1">{{ $answer->answer_ar }}</textarea>
                                </div>
                                <div class="answer_lang_block_{{$answer->id}}" id="answer_lang_de_{{$answer->id}}" style="display: none;">
                                    <textarea type="text" name="questions[update][{{ $question->id }}][answers][update][{{ $answer->id }}][answer_de]" class="form-control summernote" rows="1">{{ $answer->answer_de }}</textarea>
                                </div>
                            </div>
                        </div>
                        @endforeach
                        @endif
                    </div>
                    <a onclick="createAnswer({{ $question->id }}, true)" class="btn btn-default">Добавить ответ <i class="fa fa-plus"></i></a>
                </div>
                <div class="modal-footer">
                    <button type="submit" class="btn btn-primary">Сохранить</button>
                </div>
            </div>
        </div>
        {!! Form::close() !!}
    </div>
    @endforeach

</div>


<script type="text/javascript">

    var questionInc   = 1;
    var answerInc     = 1;
    var activeQuestionDialog = 0;
    var summernoteHeight = 150;

    function createQuestionForm()
    {
        var dialog =
            '<div id="question-modal-new' + questionInc + '" class="modal modal-question fade" tabindex="-1" role="dialog" aria-labelledby="">\n' +
            '{!! Form::open(array('url' => $questionEditUrl,'class'=>'form-horizontal padding-15','name'=>'service_form','id'=>'service_form','role'=>'form', 'enctype' => 'multipart/form-data')) !!}    ' +
            '        <div class="modal-dialog modal-lg" role="document" style="min-width:950px;">\n' +
            '            <div class="modal-content">\n' +
            '                <div class="modal-header">\n' +
            '                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>\n' +
            '                    <h4 class="modal-title">Вопрос</h4>\n' +
            '                </div>\n' +
            '                <div class="modal-body">\n' +
            '                    <div class="form-group">\n' +
            '                        <label for="" class="col-sm-3 control-label">Вопрос</label>\n' +
            '                        <div class="col-sm-9">\n' +
            '                            <ul class="nav nav-tabs nav-tabs-question-' + questionInc + '">\n' +
            '                                <li role="presentation" onclick="changeQuestionLang(\'ru\', ' + questionInc + ')" class="active tab-ru"><a href="#">Русский</a></li>\n' +
            '                                <li role="presentation" onclick="changeQuestionLang(\'en\', ' + questionInc + ')" class="tab-en"><a href="#">Английский</a></li>\n' +
            '                                <li role="presentation" onclick="changeQuestionLang(\'kz\', ' + questionInc + ')" class="tab-kz"><a href="#">Казахский</a></li>\n' +
            '                                <li role="presentation" onclick="changeQuestionLang(\'fr\', ' + questionInc + ')" class="tab-fr"><a href="#">Французский</a></li>\n' +
            '                                <li role="presentation" onclick="changeQuestionLang(\'ar\', ' + questionInc + ')" class="tab-ar"><a href="#">Арабский</a></li>\n' +
            '                                <li role="presentation" onclick="changeQuestionLang(\'de\', ' + questionInc + ')" class="tab-de"><a href="#">Немецкий</a></li>\n' +
            '                            </ul>' +
            '                           <div class="question_lang_block_' + questionInc + '" id="question_lang_ru_' + questionInc + '">' +
            '                               <textarea type="text" name="questions[new][' + questionInc + '][question]" class="form-control question-text summernote" rows="2"></textarea>\n' +
            '                           </div><div class="question_lang_block_' + questionInc + '" id="question_lang_en_' + questionInc + '" style="display:none;">' +
            '                               <textarea type="text" name="questions[new][' + questionInc + '][question_en]" class="form-control question-text_en summernote" rows="2"></textarea>\n' +
            '                           </div><div class="question_lang_block_' + questionInc + '" id="question_lang_kz_' + questionInc + '" style="display:none;">' +
            '                               <textarea type="text" name="questions[new][' + questionInc + '][question_kz]" class="form-control question-text_kz summernote" rows="2"></textarea>\n' +
            '                           </div> ' +
            '                           <div class="question_lang_block_' + questionInc + '" id="question_lang_fr_' + questionInc + '" style="display:none;">' +
            '                               <textarea type="text" name="questions[new][' + questionInc + '][question_fr]" class="form-control question-text_fr summernote" rows="2"></textarea>\n' +
            '                           </div> ' +
            '                           <div class="question_lang_block_' + questionInc + '" id="question_lang_ar_' + questionInc + '" style="display:none;">' +
            '                               <textarea type="text" name="questions[new][' + questionInc + '][question_ar]" class="form-control question-text_ar summernote" rows="2"></textarea>\n' +
            '                           </div> ' +
            '                           <div class="question_lang_block_' + questionInc + '" id="question_lang_de_' + questionInc + '" style="display:none;">' +
            '                               <textarea type="text" name="questions[new][' + questionInc + '][question_de]" class="form-control question-text_de summernote" rows="2"></textarea>\n' +
            '                           </div> ' +
            '                        </div>\n' +
            '                    </div>\n' +
            '                    <div class="form-group">\n' +
            '                        <label for="" class="col-sm-3 control-label">Аудиофайл</label>\n' +
            '                        <div class="col-sm-9">\n' +
            '                            <input type="file" name="questions[new][' + questionInc + '][audio]" />\n' +
            '                        </div>\n' +
            '                    </div>\n' +
            '                    <hr>\n' +
            '                    <div class="answerBlock">\n' +
            '                        <div class="form-group"  id="answer-'+ answerInc +'">\n' +
            '                            <div class="col-sm-3 control-label">\n' +
            '                                <div class="answerTitle">Ответ <a onclick="removeAnswer('+answerInc+')" class="btn btn-default"><i class="fa fa-trash"></i></a></div><br />\n' +
            '                                <label>\n' +
            '                                    <input type="checkbox" onchange="changeCorrect(' + answerInc + ', this, ' + "'new'" + ')" value="true" name="questions[new][' + questionInc + '][answers][new][' + answerInc + '][correct]"> Правильный\n' +
            '                                </label>\n' +
            '\n' +
            '                                <div class="form-group" id="points-answer-new' + answerInc + '" style="display:none;">\n' +
            '                                    <label for="" class="col-sm-3 control-label">Баллов</label>\n' +
            '                                    <div class="col-sm-9">\n' +
            '                                        <input type="text" name="questions[new][' + questionInc + '][answers][new][' + answerInc + '][points]" value="" class="form-control">\n' +
            '                                    </div>\n' +
            '                                </div>\n' +
            '                            </div>\n' +
            '\n' +
            '                            <div class="col-sm-9">\n' +
            '                            <ul class="nav nav-tabs nav-tabs-answer-' + answerInc + '">\n' +
            '                                <li role="presentation" onclick="changeAnswerLang(\'ru\', ' + answerInc + ')" class="active tab-ru"><a href="#">Русский</a></li>\n' +
            '                                <li role="presentation" onclick="changeAnswerLang(\'en\', ' + answerInc + ')" class="tab-en"><a href="#">Английский</a></li>\n' +
            '                                <li role="presentation" onclick="changeAnswerLang(\'kz\', ' + answerInc + ')" class="tab-kz"><a href="#">Казахский</a></li>\n' +
            '                                <li role="presentation" onclick="changeAnswerLang(\'fr\', ' + answerInc + ')" class="tab-fr"><a href="#">Французский</a></li>\n' +
            '                                <li role="presentation" onclick="changeAnswerLang(\'ar\', ' + answerInc + ')" class="tab-ar"><a href="#">Арабский</a></li>\n' +
            '                                <li role="presentation" onclick="changeAnswerLang(\'de\', ' + answerInc + ')" class="tab-de"><a href="#">Немецкий</a></li>\n' +
            '                            </ul>' +
            '                            <div class="answer_lang_block_' + answerInc + '" id="answer_lang_ru_' + answerInc + '">' +
            '                                <textarea type="text" name="questions[new][' + questionInc + '][answers][new][' + answerInc + '][answer]" class="form-control summernote" rows="1"></textarea>\n' +
            '                           </div>' +
            '                           <div class="answer_lang_block_' + answerInc + '" id="answer_lang_en_' + answerInc + '" style="display:none;">' +
            '                                <textarea type="text" name="questions[new][' + questionInc + '][answers][new][' + answerInc + '][answer_en]" class="form-control summernote" rows="1"></textarea>\n' +
            '                           </div>' +
            '                           <div class="answer_lang_block_' + answerInc + '" id="answer_lang_kz_' + answerInc + '" style="display:none;">' +
            '                                <textarea type="text" name="questions[new][' + questionInc + '][answers][new][' + answerInc + '][answer_kz]" class="form-control summernote" rows="1"></textarea>\n' +
            '                            </div>\n' +
            '                           <div class="answer_lang_block_' + answerInc + '" id="answer_lang_fr_' + answerInc + '" style="display:none;">' +
            '                                <textarea type="text" name="questions[new][' + questionInc + '][answers][new][' + answerInc + '][answer_fr]" class="form-control summernote" rows="1"></textarea>\n' +
            '                            </div>\n' +
            '                           <div class="answer_lang_block_' + answerInc + '" id="answer_lang_ar_' + answerInc + '" style="display:none;">' +
            '                                <textarea type="text" name="questions[new][' + questionInc + '][answers][new][' + answerInc + '][answer_ar]" class="form-control summernote" rows="1"></textarea>\n' +
            '                            </div>\n' +
            '                           <div class="answer_lang_block_' + answerInc + '" id="answer_lang_de_' + answerInc + '" style="display:none;">' +
            '                                <textarea type="text" name="questions[new][' + questionInc + '][answers][new][' + answerInc + '][answer_de]" class="form-control summernote" rows="1"></textarea>\n' +
            '                            </div>\n' +
            '                        </div>\n' +
            '                    </div>\n' +
            '                    </div>\n' +
            '                    <a onclick="createAnswer('+questionInc+')" class="btn btn-default">Добавить ответ <i class="fa fa-plus"></i></a>\n' +
            '                </div>\n' +
            '                <div class="modal-footer">\n' +
            '                    <button type="submit" class="btn btn-primary">Сохранить</button>\n' +
            '                </div>\n' +
            '            </div>\n' +
            '        </div>\n' +
            '{!! Form::close() !!}' +
            '    </div>';

        $('#question-modal-list').append(dialog);
        activeQuestionDialog = questionInc;
        $('.summernote').summernote({ height: summernoteHeight});
        $('#question-modal-new' + activeQuestionDialog).on('hidden.bs.modal', function(e){
            if( $(this).find('.question-text').val() =='' && $(this).find('.question-text_en').val() =='' && $(this).find('.question-text_kz').val() =='' )
            {
                deleteQuestion('question-modal-new' + activeQuestionDialog);
            }
        });
        $('#question-modal-new' + activeQuestionDialog).modal('show');
        questionInc++;
        answerInc++;
    }

    function saveQuestion(dialogId)
    {
        $('textarea.summernote').each(function(index){
            $(this).val($(this).summernote().code());
        });
        updateQuestionTable();
        $('#' + dialogId).modal('hide');
    }

    function showQuestionDialog(dialogId)
    {
        $('#' + dialogId).modal('show');
    }

    function deleteQuestion(dialogId)
    {
        if(!confirm('Удалить вопрос?'))
        {
            return false;
        }
        $('#' + dialogId).remove();
        updateQuestionTable();
    }

    function createAnswer(id, update)
    {
        if(update)
        {
            $answer = '                   <div class="form-group" id="answer-' + answerInc + '">\n' +
                '                            <div class="col-sm-3 control-label">\n' +
                '                                <div class="answerTitle">Ответ <a onclick="removeAnswer(' + answerInc + ')" class="btn btn-default"><i class="fa fa-trash"></i></a></div><br />\n' +
                '                                <label>\n' +
                '                                    <input type="checkbox" onchange="changeCorrect(' + answerInc + ', this, ' + "'new'" + ')" id="points-answer-' + answerInc + '" value="true" name="questions[update][' + id + '][answers][new][' + answerInc + '][correct]"> Правильный\n' +
                '                                </label>\n' +
                '\n' +
                '                                <div class="form-group" id="points-answer-new' + answerInc + '" style="display:none;">\n' +
                '                                    <label for="" class="col-sm-3 control-label">Баллов</label>\n' +
                '                                    <div class="col-sm-9">\n' +
                '                                        <input type="text" name="questions[update][' + id + '][answers][new][' + answerInc + '][points]" value="" class="form-control">\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '\n' +
                '                            <div class="col-sm-9">\n' +
                '                               <ul class="nav nav-tabs nav-tabs-answer-' + answerInc + '">\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'ru\', ' + answerInc + ')" class="active tab-ru"><a href="#">Русский</a></li>\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'en\', ' + answerInc + ')" class="tab-en"><a href="#">Английский</a></li>\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'kz\', ' + answerInc + ')" class="tab-kz"><a href="#">Казахский</a></li>\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'fr\', ' + answerInc + ')" class="tab-fr"><a href="#">Французский</a></li>\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'ar\', ' + answerInc + ')" class="tab-ar"><a href="#">Арабский</a></li>\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'de\', ' + answerInc + ')" class="tab-de"><a href="#">Немецкий</a></li>\n' +
                '                               </ul>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_ru_' + answerInc + '">' +
                '                                   <textarea type="text" name="questions[update][' + id + '][answers][new][' + answerInc + '][answer]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_en_' + answerInc + '" style="display:none;">' +
                '                                   <textarea type="text" name="questions[update][' + id + '][answers][new][' + answerInc + '][answer_en]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_kz_' + answerInc + '" style="display:none;">' +
                '                                   <textarea type="text" name="questions[update][' + id + '][answers][new][' + answerInc + '][answer_kz]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_fr_' + answerInc + '" style="display:none;">' +
                '                                   <textarea type="text" name="questions[update][' + id + '][answers][new][' + answerInc + '][answer_fr]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_ar_' + answerInc + '" style="display:none;">' +
                '                                   <textarea type="text" name="questions[update][' + id + '][answers][new][' + answerInc + '][answer_ar]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_de_' + answerInc + '" style="display:none;">' +
                '                                   <textarea type="text" name="questions[update][' + id + '][answers][new][' + answerInc + '][answer_de]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '</div>\n' +
                '                        </div>';


            $('#question-modal-' + id).find('.answerBlock').append($answer);
        } else {
            $answer = '                   <div class="form-group" id="answer-' + answerInc + '">\n' +
                '                            <div class="col-sm-3 control-label">\n' +
                '                                <div class="answerTitle">Ответ <a onclick="removeAnswer(' + answerInc + ')" class="btn btn-default"><i class="fa fa-trash"></i></a></div><br />\n' +
                '                                <label>\n' +
                '                                    <input type="checkbox" onchange="changeCorrect(' + answerInc + ', this, ' + "'new'" + ')" id="points-answer-' + answerInc + '" value="true" name="questions[new][' + id + '][answers][new][' + answerInc + '][correct]"> Правильный\n' +
                '                                </label>\n' +
                '\n' +
                '                                <div class="form-group" id="points-answer-new' + answerInc + '" style="display:none;">\n' +
                '                                    <label for="" class="col-sm-3 control-label">Баллов</label>\n' +
                '                                    <div class="col-sm-9">\n' +
                '                                        <input type="text" name="questions[new][' + id + '][answers][new][' + answerInc + '][points]" value="" class="form-control">\n' +
                '                                    </div>\n' +
                '                                </div>\n' +
                '                            </div>\n' +
                '\n' +
                '                            <div class="col-sm-9">\n' +
                '                               <ul class="nav nav-tabs nav-tabs-answer-' + answerInc + '">\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'ru\', ' + answerInc + ')" class="active tab-ru"><a href="#">Русский</a></li>\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'en\', ' + answerInc + ')" class="tab-en"><a href="#">Английский</a></li>\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'kz\', ' + answerInc + ')" class="tab-kz"><a href="#">Казахский</a></li>\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'fr\', ' + answerInc + ')" class="tab-fr"><a href="#">Французкий</a></li>\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'ar\', ' + answerInc + ')" class="tab-ar"><a href="#">Арабский</a></li>\n' +
                '                                   <li role="presentation" onclick="changeAnswerLang(\'de\', ' + answerInc + ')" class="tab-de"><a href="#">Немецкий</a></li>\n' +
                '                               </ul>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_ru_' + answerInc + '">' +
                '                                   <textarea type="text" name="questions[new][' + id + '][answers][new][' + answerInc + '][answer]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_en_' + answerInc + '" style="display:none;">' +
                '                                   <textarea type="text" name="questions[new][' + id + '][answers][new][' + answerInc + '][answer_en]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_kz_' + answerInc + '" style="display:none;">' +
                '                                   <textarea type="text" name="questions[new][' + id + '][answers][new][' + answerInc + '][answer_kz]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_fr_' + answerInc + '" style="display:none;">' +
                '                                   <textarea type="text" name="questions[new][' + id + '][answers][new][' + answerInc + '][answer_fr]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_ar_' + answerInc + '" style="display:none;">' +
                '                                   <textarea type="text" name="questions[new][' + id + '][answers][new][' + answerInc + '][answer_ar]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '                               <div class="answer_lang_block_' + answerInc + '" id="answer_lang_de_' + answerInc + '" style="display:none;">' +
                '                                   <textarea type="text" name="questions[new][' + id + '][answers][new][' + answerInc + '][answer_de]" class="form-control summernote" rows="1"></textarea>\n' +
                '                               </div>' +
                '                           </div>\n' +
                '                        </div>';


            $('#question-modal-new' + id).find('.answerBlock').append($answer);
        }
        $('.summernote').summernote({ height: summernoteHeight});
        answerInc++;
    }

    function removeAnswer(id)
    {
        $('#answer-' + id).remove();
    }

    function changeCorrect(id, elem, type)
    {
        if($(elem).prop('checked'))
        {
            $('#points-answer-' + type + id).show();
        } else {
            $('#points-answer-' + type + id).hide();
            $('#points-answer-' + type + id + ' input').val(0);
        }
    }

    function changeQuestionLang(lang, id)
    {
        $('.question_lang_block_' + id).hide();
        $('#question_lang_' + lang + '_' + id).show();
        $('ul.nav-tabs-question-' + id + ' li').removeClass('active');
        $('ul.nav-tabs-question-' + id + ' li.tab-' + lang).addClass('active');
    }

    function changeAnswerLang(lang, id)
    {
        $('.answer_lang_block_' + id).hide();
        $('#answer_lang_' + lang + '_' + id).show();
        $('ul.nav-tabs-answer-' + id + ' li').removeClass('active');
        $('ul.nav-tabs-answer-' + id + ' li.tab-' + lang).addClass('active');
    }

</script>