<h4>Вопрос</h4>
<div class="form-group col-sm-12">
    <div class="col-sm-12">
        <summernote
            v-model="quizEdit.question"
        ></summernote>
    </div>
</div>

<div class="form-group col-sm-12">
    <label for="" class="col-sm-3 control-label">Язык</label>         
    <div class="col-sm-9">
        <div class="col-md-11">      
            <select v-model="quizEdit.lang">
              <option value = 'ru' selected >Русский</option>
              <option value = 'kz'>Казахский</option>
              <option value = 'en'>Английский</option>
            </select>
        </div>
    </div>
</div>

<div class="form-group col-sm-12">
    <label for="" class="col-sm-3 control-label">Аудиофайл</label>
    <div class="col-sm-9">
        <div class="col-md-11">
        <audio
                v-if="quizEdit.audiofiles.length > 0"
                style="width: 100%"
                v-bind:src="'/audio/' + quizEdit.audiofiles[0].filename"
                controls></audio>
        </div>
        <div class="col-md-1">
            <a class="btn btn-default" v-on:click="quizEdit.audiofiles = []" v-if="quizEdit.audiofiles.length > 0" style="cursor: pointer">
                <i class="fa fa-remove"></i>
            </a>
        </div>
            <input v-on:change="processFile($event)" type="file" />
    </div>
</div>
<h4>Ответы</h4>
<hr>
<div class="answerBlock col-sm-12">
    <div class="form-group col-sm-12 no-padding" v-for="(answer, key) in quizEdit.answers">
        <div class="col-sm-3 control-label no-padding">
            <div class="col-sm-12 no-padding">
                <span>Ответ @{{ key + 1 }}</span>
                <a class="btn btn-default pull-right" v-on:click="quizEdit.answers.splice(key, 1)"><i class="fa fa-trash"></i></a>
            </div>
            <div class="col-sm-12 no-padding">
                <div class="col-sm-12 no-padding">
                    <input type="checkbox" v-model="answer.correct" v-on:change="answer.points=(answer.correct ? 1 : 0)" /> Правильный
                </div>
                <div class="col-sm-12 no-padding" v-if="answer.correct">
                    <span style="margin-top: 5px;" class="col-sm-3 control-label">Баллов</span>
                    <div class="col-sm-9">
                        <input type="number" v-model="answer.points" class="form-control" min="0" max="5">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-sm-9">
            <summernote
                    v-model="answer.answer"
            ></summernote>
        </div>
    </div>
</div>
<a v-on:click="createAnswer()" class="btn btn-default">Добавить ответ <i class="fa fa-plus"></i></a>
