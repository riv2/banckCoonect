{!! Form::open(['url' => [ route('adminSyllabusEdit', ['themeId' => isset($syllabus->id) ? $syllabus->id : 'add', 'disciplineId' => $disciplineId]) ],'class'=>'form-horizontal padding-15','name'=>'service_form','id'=>'service_form','role'=>'form','enctype' => 'multipart/form-data']) !!}

@if($language)
    <input type="hidden" name="language" value="{{$language}}" />
@endif

<div class="form-group">
    <label for="theme_number" class="col-md-3 control-label">Номер темы</label>
    <div class="col-md-2">
        <input required type="text" name="theme_number" value="{{ $syllabus->theme_number ?? ''}}" maxlength="255" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="theme_name" class="col-md-3 control-label">Наименование темы</label>
    <div class="col-md-9">
        <textarea required name="theme_name" class="form-control">{{ $syllabus->theme_name ?? '' }}</textarea>
    </div>
</div>

<div class="form-group">
    <label for="module_id" class="col-md-3 control-label">Модуль</label>
    <div class="col-md-9">
        <select name="module_id" class="form-control">
            <option value="">Без модуля</option>
            @foreach($syllabusModules as $syllabusModule)
                <option value="{{ $syllabusModule->id }}"
                        {{ (request('module_id') == $syllabusModule->id || $syllabus->module_id == $syllabusModule->id) ? 'selected' : '' }}
                >
                    {{ $syllabusModule->name }}
                </option>
            @endforeach
        </select>
    </div>
</div>
<div  id="addLiteratureToSyllabusBlock">
<div class="form-group">
    <label for="literature" class="col-md-3 control-label">Основная литература</label>
    <div class="col-md-9">
        <div v-if="mainLiterature.length > 0" v-for="mainLiteratureVal in mainLiterature">
            <p>
                @{{ mainLiteratureVal.name }} 
                <span class="pull-right" @click="removeLiterature(mainLiteratureVal.id, 'main')">
                    <i class="fa fa-remove"></i>
                </span>
            </p>
            <input type="hidden" name="literature[]" :value="mainLiteratureVal.id">
        </div>
        <input 
            type="text" 
            class="form-control" 
            name="search" 
            placeholder="автоматический поиск от 5 символов" 
            @input="liveSearch($event)" 
            data-literature="main"
        >
        <div class="pos-relative livaSearchLiteratureSyllabusBlock">
            <div v-if="mainLiteratureList.length > 0" id="searchData" class="shadow padding-5">
                <p v-for="value in mainLiteratureList" @click="addLiteratureToList(value.id, value.id+' - '+value.name, 'main')">
                    <b>Наименование:</b> @{{ value.name }} <b>Автор:</b> @{{ value.author }}<b>Год:</b> @{{ value.publication_year }}
                </p>
            </div>
        </div>
        <!-- <textarea required name="literature" class="form-control">{{ $syllabus->literature ?? '' }}</textarea> -->
    </div>
</div>

<div class="form-group">
    <label for="literature" class="col-md-3 control-label">Дополнительная литература</label>
    <div class="col-md-9">
        <div v-if="secondaryLiterature.length > 0" v-for="secondaryLiteratureVal in secondaryLiterature">
            <p>
                @{{ secondaryLiteratureVal.name }} 
                <span class="pull-right" @click="removeLiterature(secondaryLiteratureVal.id, 'secondary')">
                    <i class="fa fa-remove"></i>
                </span>
            </p>
            <input type="hidden" name="literature_added[]" :value="secondaryLiteratureVal.id">
        </div>
        <input 
            type="text" 
            class="form-control" 
            name="search" 
            placeholder="автоматический поиск от 5 символов" 
            @input="liveSearch($event)" 
            data-literature="secondary"
        >
        <div class="pos-relative livaSearchLiteratureSyllabusBlock">
            <div v-if="secondaryLiteratureList.length > 0" id="searchData" class="shadow padding-5">
                <p v-for="value in secondaryLiteratureList" @click="addLiteratureToList(value.id, value.id+' - '+value.name, 'secondary')">
                    <b>Наименование:</b> @{{ value.name }} <b>Автор:</b> @{{ value.author }}<b>Год:</b> @{{ value.publication_year }}
                </p>
            </div>
        </div>
        <!-- <textarea required name="literature_added" class="form-control">{{ $syllabus->literature_added ?? '' }}</textarea> -->
    </div>
</div>
</div>
<hr>
@include('admin.pages.syllabus.form.teoretical_material', ['lang' => 'ru'])
<hr>
@include('admin.pages.syllabus.form.practical_material', ['lang' => 'ru'])
<hr>
@include('admin.pages.syllabus.form.sro_material', ['lang' => 'ru'])
<hr>
@include('admin.pages.syllabus.form.srop_material', ['lang' => 'ru'])
<hr>

<div class="form-group">
    <label for="contact_hours" class="col-md-3 control-label">Лекционные занятия</label>
    <div class="col-md-2">
        <input required type="text" name="contact_hours" value="{{ $syllabus->contact_hours ?? 0}}" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="self_hours" class="col-md-3 control-label">Практические (семинарские) занятия</label>
    <div class="col-md-2">
        <input required type="text" name="self_hours" value="{{ $syllabus->self_hours ?? 0}}" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="self_with_teacher_hours" class="col-md-3 control-label">Лабораторные занятия</label>
    <div class="col-md-2">
        <input required type="text" name="self_with_teacher_hours" value="{{ $syllabus->self_with_teacher_hours ?? 0}}" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="self_with_teacher_hours" class="col-md-3 control-label">СРОП</label>
    <div class="col-md-2">
        <input required type="text" name="srop_hours" value="{{ $syllabus->srop_hours ?? 0}}" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="self_with_teacher_hours" class="col-md-3 control-label">СРО</label>
    <div class="col-md-2">
        <input required type="text" name="sro_hours" value="{{ $syllabus->sro_hours ?? 0}}" class="form-control">
    </div>
</div>

<div class="form-group">
    <label for="" class="col-md-3 control-label">Всего часов</label>
    <div class="col-md-2">
        <p> {{ $allHours }} </p>
    </div>
</div>

<div class="form-group">
    <label for="" class="col-md-3 control-label">Участвует в Тестировании 1</label>
    <div class="col-md-2">
        <input type="checkbox" name="for_test1" class="form-control" @if($syllabus->for_test1) checked @endif>
    </div>
</div>

<hr>

<div class="form-group">
    <div  class="col-md-3 control-label"></div>
    <div class="col-md-9">
        <button type="submit" class="btn btn-primary">Сохранить</button>
    </div>
</div>

{!! Form::close() !!}
