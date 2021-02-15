<?php $docsRight = \App\Services\Auth::user()->hasRight('users', 'upload_student_docs'); ?>


        
<div class="row">
    <div v-html="docsMessage" v-bind:class="{'alert': docsMessage, 'alert-error': docsHasError, 'alert-success': !docsHasError}" class="col-md-8 col-md-offset-1"></div>
</div>

<div id="loader-layout" style="position: absolute;width: 100%;height: 100%;background: rgba(255, 255, 255, 0.5);text-align: center; z-index: 9;" v-if="docsloader"><img src="{{ URL::to('assets/img/load.gif') }}" style="opacity: 0.5; max-width: 100px;"></div>

@if($docsRight and $hasRightEdit)
<form>
    <p>
    <div class="row">
        <label class="col-md-3 control-label">Тип документа</label>
        <div class="col-md-6">
            <select data-size="5" title="{{__('Please select')}}" class="form-control" v-model="docFilesType" v-on:select='docTypeSelected("docFiles")' id='docType'>
                <option disabled="disabled" selected="selected">{{__('Please select')}}</option>
                @foreach($docType as $type)
                    <option value="{{ $type->type }}">{{ __($type->type) }}</option>
                @endforeach
                
            </select>
        </div>
    </div>
    </p>

    <div class="row">
        <label class="col-md-3 control-label">Файлы</label>
        <div class="col-md-6">
            <input id="docFiles" ref="docFiles" type="file" accept=".jpg, .jpeg, .png, .gif, .webp" v-on:change="checkImageValid($event, 'docFiles')" class="form-control" multiple="true">
        </div>
    </div>

    <p>
        <div class="col-md-offset-3">
            <input class="btn btn-info" style="cursor: pointer;" v-on:click="uploadDocFiles()" value="{{ __('Upload files') }}" />
        </div>
    </p>
</form>
@endif

<div class="row">
    <div class="panel panel-default col-md-8 col-md-offset-1" v-for="(docItem, docIndex) in uploadedDocsList" v-bind:class="{ 
    'alert alert-success': docsStatus[docIndex] === 'allow',
    'alert alert-danger': docsStatus[docIndex] === 'disallow'
    }">
        <a v-bind:href="docItem.filepath" target="_blank">
        <img v-bind:src="docItem.filepath" style="min-height: 100px; max-height: 100px; padding: 10px;">

            <b>@{{ docItem.doc_name }}</b>
        </a>

        @if($docsRight)
        <br />
        <div style="position: absolute;right: 10px;bottom: 10px;">
            <small>
                <select v-model="docsStatus[docIndex]" class="form-control" @if(!$hasRightEdit) disabled @endif>
                    <option value="moderation">проверяется</option>
                    <option value="allow">принят</option>
                    <option value="disallow">Отклонен</option>
                    <option value="hide">Скрыть</option>
                </select>
                <input :id="'delivered_' + docIndex" type="checkbox" v-model="docsDelivered[docIndex]" @if(!$hasRightEdit) disabled @endif>
                <label :for="'delivered_' + docIndex">Доставлено</label> 
                <div style="width: 50px; height: 36px; float: right; display: inline-block;">
                    <a class="btn btn-info" v-on:click=docUpdateStatus(docIndex) v-if="
                        docsDelivered[docIndex] != docsDeliveredOriginal[docIndex] || 
                        docsStatus[docIndex] != docsStatusOriginal[docIndex]">OK</a>
                </div>
            </small>
        </div>
        @endif

    </div>
</div>




