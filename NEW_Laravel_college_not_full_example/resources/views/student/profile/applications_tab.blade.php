<div>
    <h3>{{ __('Подать заявление') }}</h3>


    <div class="col-12 subform">
        <label class="col-12 control-label">{{__('Please select a type of request')}}</label>
        <select data-size="5" title="{{__('Please select')}}" class="form-control" v-model="requestType" id='requestType'>
            <option disabled="disabled" selected="selected">{{__('Please select')}}</option>
            @foreach($requestTypes as $type)
                <option v-if="!disableNewRequests['{{ $type->key }}']" value="{{ $type->key }}">{{ __($type->{'name_'.app()->getLocale()}) }}</option>
            @endforeach
        </select>
    </div>

    <div v-if="requestType">
        <div class="col-md-12">
            
            <ul>
                <li>
                    <a v-bind:href="'/images/uploads/{{\App\Models\StudentRequest\StudentRequestType::DOCS_TEMPLATE}}/'+ requestTypeTemplates[requestType]" target="_blank">{{ __('Скачайте бланк заявления')}}</a>
                </li>
                <li >
                    {{ __('Распечатайте, заполните')}}
                </li>
                <li>
                    <p>{{ __('Отправьте нам фото заполненого и подписаного заявления')}}. <a class="btn btn-default" v-on:click="requestLoadPhoto()">{{ __("Загрузить фото") }}</a></p>
                    <p><img id="request_img" style="max-width: 200px" v-if="requestUrl" :src="requestUrl" /></p>
                </li>
            </ul>
            <input id="request_file" ref="requestFile" type="file" style="display: none;" @change="onRequestFileChange" />
        </div>
        <div class="col-12">
            <label class="col-12 control-label">{{__('Please specify a date of request')}}</label>
            <input v-model="requestDate" id='requestDate' type="date" class="form-control" />
        </div>
        <div class="col-md-12">
            <p>&nbsp;</p>
                <button class="btn btn-primary" v-on:click="uploadRequestFiles()">{{ __('Отправить') }}</button>
            
        </div>

    {{--
    <div class="col-md-12">
        <span class="alert alert-success"
              v-show="sendDownAppStatus == '{{ \App\UserApplication::STATUS_MODERATION }}'">
            {{ __('Заявление на рассмотрении.') }}
        </span>
        @if($sendDownApp)
            <span class="alert alert-warning"
                  v-show="sendDownAppStatus == '{{ \App\UserApplication::STATUS_DECLINE }}'">
                {{ $sendDownApp->comment }}
            </span>

            <span class="alert alert-success"
                  v-show="sendDownAppStatus == '{{ \App\UserApplication::STATUS_CONFIRM }}'">
                {{ __('Заявление принято') }}
            </span>
        @endif
    </div>
    --}}

    </div>
</div>

<h3>{{__('Requests list')}}</h3>
<div v-for="item in RequestsList">
    <div class="card col-12" v-bind:class="{
        'alert-error': item.status == '{{ \App\Models\StudentRequest\StudentRequest::STATUS_DECLINED }}',
        'alert-success': item.status == '{{ \App\Models\StudentRequest\StudentRequest::STATUS_ACCEPTED }}',
        'alert-info': item.status == '{{ \App\Models\StudentRequest\StudentRequest::STATUS_NEW }}'
    }">
        <div class="row">
            <a v-bind:href="item.url" target="_blank" class="col-md-2">
                <img v-bind:src="item.url" style="max-height: 60px;">
            </a>
            <div class="col-md-8">
                <b>@{{ item.type }}</b>
                <small>
                    @{{ item.date }} 
                </small>
                <br />@{{ item.comment }}
            </div>
        </div>
    </div>
</div>
