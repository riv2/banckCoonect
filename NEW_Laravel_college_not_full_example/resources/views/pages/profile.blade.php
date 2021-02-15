@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid" id="main-container">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{ __("Profile") }} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div id="loader-layout" style="position: absolute;width: 100%;height: 100%;background: rgba(255, 255, 255, 0.5);text-align: center; z-index: 9;" v-if="loader"><img src="{{ URL::to('assets/img/load.gif') }}" style="opacity: 0.5; max-width: 100px;"></div>

                    <div class="row">
                        <div class="col-md-2"></div>
                        <div class="col-md-8">

                            @if(\App\Services\Auth::user()->hasRole('client'))
                            <ul class="nav nav-tabs col-md-10">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#personal" data-toggle="tab"> {{__("Personal data")}} </a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="#docs" data-toggle="tab"> {{__("Documents")}} </a>
                                </li>
{{--
                                <li class="nav-item">
                                    <a class="nav-link" href="#applications" data-toggle="tab"> {{__("Applications")}} </a>
                                </li>
--}}

                            </ul>
                            @endif

                            <div class="tab-content margin-t20">

                                <!-- personal -->
                                <div class="tab-pane active row" id="personal">

                                    <div class="row">
                                        @if(\App\Services\Auth::user()->hasRole('client'))
                                        <div class="col-md-3 text-center row">
                                            @if($profile->faceimg)
                                                <img
                                                        class="rounded"
                                                        src="{{ \App\Services\Avatar::getStudentFacePublicPath($profile->faceimg) ?? '' }}"
                                                        alt=" {{ $profile->fio ?? '' }}"
                                                        style="max-width: 100%; border: 1px solid #ddd"
                                                />
                                                <a class="btn alert-success margin-t5" onclick="getFile()">✓ {{__("Photo uploaded")}}</a>
                                            @else
                                                <img
                                                        src="/images/uploads/faces/default.png"
                                                        alt=" {{ $profile->fio ?? '' }}"
                                                        style="max-width: 100%; opacity: 0.4; padding: 3px;"
                                                />
                                                <a class="btn" onclick="getFile()" style="margin-top: 5px;">{{__("Select photo")}}</a>
                                            @endif
                                            <form method="post" id="faceimg-form" action="{{ route('studentProfileEditPhoto') }}" enctype="multipart/form-data">
                                                <input type="hidden" name="_token" value="{{ csrf_token() }}" />
                                                <input type="file" accept=".jpg, .jpeg, .png, .gif, .webp" onchange="uploadImage()" id="faceimg-file" name="faceimg" style="display: none;" />
                                            </form>
                                        </div>
                                        @endif

                                        <div class="col-md-9 padding-b10">
                                            @if(\App\Services\Auth::user()->hasRole('client'))
                                            <div class="row">
                                                <div class="col-4" style="margin-left: 15px">
                                                    <label> {{__("Full name")}} (id {{$profile->user_id}}) </label>
                                                </div>
                                                <div class="col">
                                                    {{ $profile->fio }}
                                                </div>
                                            </div>
                                            @endif

                                            <div class="row">
                                                <div class="col-4" style="margin-left: 15px">
                                                    <label> {{__("Phone number")}} </label>
                                                </div>
                                                <div class="col">
                                                    {{ $profile->mobile }}
                                                </div>
                                            </div>

                                            @if(\App\Services\Auth::user()->hasRole('client'))
                                            <div class="row">
                                                <div class="col-4" style="margin-left: 15px">
                                                    <label> {{__("Chosen schooling")}} </label>
                                                </div>
                                                <div class="col">
                                                    {{ $profile->speciality->name ?? '' }}
                                                </div>
                                            </div>
                                            <div class="row">
                                                <div class="col-4" style="margin-left: 15px">
                                                    <label> {{__("Group")}} </label>
                                                </div>
                                                <div class="col">
                                                    {{ $profile->studyGroup->name ?? '' }}
                                                </div>
                                            </div>
                                            @endif
                                        </div>

                                    </div>
                                    <div class="clearfix"></div>
                                    <br>

                                    <div class="row">

                                        <form class="col-12" method="post" action="{{ route('editProfile') }}" enctype="multipart/form-data">
                                            {{ csrf_field() }}

                                            @if(\App\Services\Auth::user()->hasRole('client'))
                                            <div class="col-12 form-group">
                                                <div class="col-12" style="margin-left: 15px">
                                                    <label class="pull-right text-right">{{__('Workplace')}}</label>
                                                </div>
                                                <div class="col-12">
                                                    <textarea cols="5" rows="3" class="form-control" name="workplace">{{ $profile->workplace ?? '' }}</textarea>
                                                </div>
                                            </div>
                                            @endif

                                            <div class="col-12 form-group">

                                                <div class="col-12">
                                                    <label class="pull-right text-right">{{__('Reset password')}}</label>
                                                </div>
                                                <div class="col-12">
                                                    <option-element v-bind:active="false">
                                                        <div class="col-12 subform">
                                                            <div class="col-12 form-group">
                                                                <label>{{__('Current password')}}</label>
                                                                <input type="password" name="current_password" class="form-control" />
                                                            </div>
                                                            <div class="col-12 form-group">
                                                                <label>{{__('New password')}}</label>
                                                                <input type="password" name="password" class="form-control" />
                                                            </div>
                                                            <div class="col-12 form-group">
                                                                <label class="pull-left">{{__('Repeat new password')}}</label>
                                                                <input type="password" name="password_confirmation" class="form-control" />
                                                            </div>
                                                        </div>
                                                    </option-element>
                                                </div>
                                            </div>

                                            <div class="col-12 form-group">
                                                <button class="btn btn-info" type="submit">{{__('Save')}}</button>
                                            </div>
                                        </form>

                                    </div>

                                </div>

                                <!-- docs -->
                                <div class="tab-pane padding-15" id="docs">

                                    <div v-html="message" v-bind:class="{'alert': message, 'alert-error': hasError, 'alert-success': !hasError}"></div>

                                    

                                    <input type="hidden" name="type" value="{{(isset($application->type))?$application->type:''}}">

                                    <div class="form-group row shadow-sm p-3 mb-5 bg-white rounded">
                                        <p>{{__('You can upload a document which will check later')}}</p>
                                        <form>
                                        <p>
                                        <div class="col-12 subform">
                                            <label class="col-12 control-label">{{__('Document type')}}</label>
                                            <select data-size="5" title="{{__('Please select')}}" class="form-control" v-model="docFilesType" v-on:select='docTypeSelected("docFiles")' id='docType'>
                                                <option disabled="disabled" selected="selected">{{__('Please select')}}</option>
                                                @foreach($docType as $type)
                                                    <option value="{{ $type->type }}">{{ __($type->type) }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        </p>

                                        <div class="col-12 subform">
                                                
                                            <input id="docFiles" ref="docFiles" type="file" accept=".jpg, .jpeg, .png, .gif, .webp" v-on:change="checkImageValid($event, 'docFiles')" class="form-control" multiple="true">
                                                
                                        </div>
                                        <p>
                                            <input class="btn btn-info" style="cursor: pointer;" v-on:click="uploadDocFiles()" value="{{ __('Upload files') }}" />
                                        </p>
                                        </form>

                                        <p>{{__('List of your documents')}}</p>

                                        <div class="card col-12" v-for="docItem in uploadedDocsList">
                                            <a v-bind:href="docItem.filepath" target="_blank">
                                                <b>@{{ docItem.doc_name }}</b>
                                            </a>
                                            <small class="text-muted">
                                                @{{ docItem.status }} 
                                            </small>    
                                        </div>


                                </div>

                            </div>

                                <div class="tab-pane padding-15" id="applications">
                                    @include('student.profile.applications_tab')
                                </div>



                        </div>
                        <div class="col-md-2"></div>
                    </div>

                </div> <!-- card-body end -->
            </div> <!-- card end -->

        </div>
    </section>

@endsection
@section('scripts')
    <script type="text/javascript">


    function getFile() {
        $('#faceimg-file').click();
    }

    function uploadImage() {
        $('#faceimg-form').submit();
    }

    function getExtension(filename) {
        var parts = filename.split('.');
        return parts[parts.length - 1];
    }

    function isImage(filename) {
        var ext = this.getExtension(filename);
        switch (ext.toLowerCase()) {
            case 'jpg':
            case 'png':
            case 'gif':
            case 'webp':
                return true;
        }
        return false;
    }

    

    window.onload = function(){

        //open specific tab on page
        var url = document.location.toString();
        if (url.match('#')) {
            $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').tab('show');
        }

        //Change hash for page-reload
        $('.nav-tabs a[href="#' + url.split('#')[1] + '"]').on('shown', function (e) {
            window.location.hash = e.target.hash;
        });
    };

        Vue.component('option-element', {
            props: {
                slotClass: '',
                active: false
            },
            data: function () {
                return {show: this.active || false};
            },
            methods: {},
            template: `
                <div class="col-md-12 no-padding">
                    <div class="col-md-5 no-padding">
                        <div class="switch-btn" v-bind:class="{'switch-on-color': show}" v-on:click="show = !show"></div>
                    </div>
                    <slot v-if="show"></slot>
                </div>
            `
        });
        Vue.component('student-education-block', {
            props: {
                name: '',
                slotClass: '',
                active: false,
                image: false
            },
            data: function(){
                return { show: this.active || false };
            },
            methods: {},
            watch: {
                show: function(val){
                    this.$root.$emit('showEducation', val);
                }
            },
            template: `
                    <div class="col-md-12 no-padding">
                        <slot v-if="show"></slot>
                    </div>
                `
        });
        Vue.component('student-document-option', {
            props: {
                name: '',
                slotClass: '',
                active: false,
                image: false
            },
            data: function () {
                return {show: this.active || false};
            },
            methods: {},
            watch: {
                show: function(val){
                    this.$root.$emit('changeEnt', val);
                }
            },
            template: `
                    <div class="col-md-12 no-padding">
                        <div class="col-md-5 no-padding">
                            <div class="switch-btn" v-bind:class="{'switch-on-color': show}" v-on:click="show = !show"></div>
                        </div>
                        <slot v-if="show"></slot>
                        <input type="hidden" v-bind:value="show" v-bind:name="'has_' + name" />
                    </div>
                `
        });

        Vue.component('student-document-option-nostrification', {
            props: {
                active: true
            },
            data: function () {
                return {show: this.active};
            },
            template: `
                    <div class="col-md-12 no-padding">
                        <div class="col-md-5 no-padding">
                            <div class="switch-btn" v-bind:class="{'switch-on-color': show}" v-on:click="show = !show"></div>
                            <select v-model="show" name="kzornot" style="display:none;">
                                <option v-bind:value="true">{{ __('Yes') }}</option>
                                <option v-bind:value="false">{{ __('No') }}</option>
                            </select>
                        </div>
                        <slot v-if="!show"></slot>
                    </div>
                `
        });

        var app = new Vue({
            el: "#main-container",
            data: {
                docFilesType: null,
                message: null,
                messageApp: null,
                hasError: false,
                hasErrorApp: false,
                formData: null,
                loader: false,
                uploadedDocsList: null,
                RequestsList: null,
                requestType: null,
                requestTypeTemplates: {
                    @foreach($requestTypes as $type)
                        {{$type->key}} : 
                            '{{ $type->template_doc ?? null }}',
                    @endforeach
                },
                requestDate: null,
                requestUrl: null,
                RequestFormData: null,
                disableNewRequests: [],

                {{--
                educationLevel: '{{ ( !empty($bcApplication) && !empty($bcApplication->bceducation) ) ? 'high_school' : 'high_school' }}',
                activePage: 'education',
                application: {},
                pageList: [
                    'education'
                ],
                isTransfer: false,
                showEducation: true,
                publicationList: [
                    {
                        id: 1
                    }
                ],
                publicationIdInc: 1
                --}}

                loaderApp: false,
                uploadedDocsList: null,
                sendDownAppStatus: '{{ $sendDownApp->status ?? '' }}'
            },
            methods: {
                docTypeSelected: function(elemId){
                    app.$refs.docFiles.value = '';
                },
                checkImageValid: function(event, elemId){
                    app.message = null;
                    var file = $('#' + elemId);

                    if(!file.val()) {
                        return false;
                    }

                    if(!isImage(file.val())) {
                        app.hasError = true;
                        app.message = "{{__("Invalid image format. File type require jpg, png, gif or webp")}}";
                        app.$refs.docFiles.value = '';
                        return false;
                    }

                    if (event.target.files.length > 10) {
                        app.hasError = true;
                        app.message = "{{__("You can upload up tp 10 files")}}";
                        app.$refs.docFiles.value = '';
                    }

                    app.formData = new FormData();

                    Array.from(Array(event.target.files.length).keys())
                      .map(x => {
                        //app.formData.append(event.target.files[x].name, event.target.files[x]);
                        app.formData.append('files[]', event.target.files[x]);
                    });

                    return true;
                },
                uploadDocFiles: function(){
                    app.loader = true;
                    app.formData.append('doc_type', app.docFilesType);
                    app.formData.append('type', '{{(isset($application->type))?$application->type:''}}');

                    axios.post('{{ route('docsNeedToUploadPost')}}', app.formData, {
                        headers: {
                          'Content-Type': 'multipart/form-data'
                        }
                    })
                        .then(function(response){
                            if(response.data.status === 'success') {
                                app.hasError = false;
                                app.getUserDocsList();
                                app.message = "{{__("Your files has beed uploaded")}}";
                            }
                            app.$refs.docFiles.value = '';
                            app.loader = false;
                        }).catch(function(error){
                            app.hasError = true;
                            app.message = "{{__("File upload error")}}";
                            app.$refs.docFiles.value = '';
                            app.loader = false;
                    });

                },
                getUserDocsList: function() {
                    axios.get('{{ route('getUserDocsList')}}')
                        .then(function(response){
                            app.uploadedDocsList = response.data;
                            app.loader = false;
                        }).catch(function(error){
                    });

                },
                requestLoadPhoto: function() {
                    $('#request_file').click();
                },
                onRequestFileChange(e) {
                  const file = e.target.files[0];
                  this.requestUrl = URL.createObjectURL(file);

                  app.RequestFormData = new FormData();

                    Array.from(Array(event.target.files.length).keys())
                      .map(x => {
                        //app.formData.append(event.target.files[x].name, event.target.files[x]);
                        app.RequestFormData.append('files[]', event.target.files[x]);
                    });
                },
                uploadRequestFiles: function(){
                    this.loader = true;

                    var self = this;

                    app.RequestFormData.append('date', app.requestDate);
                    app.RequestFormData.append('type', self.requestType);

                    axios.post('{{ route('docsApplicationUploadPost')}}', app.RequestFormData, 
                        {
                            headers: {
                            'Content-Type': 'multipart/form-data'
                            }
                        })
                        .then(function(response){
                            if(response.data.status === 'success') {
                                //self.sendDownAppStatus = '{{ \App\UserApplication::STATUS_MODERATION }}';
                            }
                            self.$refs.requestFile.value = '';
                            self.requestUrl = null;
                            self.loader = false;
                            self.requestType = null;
                            self.getUserRequestsList();
                        }).catch(function(error){
                            self.hasErrorApp = true;
                            self.messageApp = "{{__("File upload error")}}";
                            self.$refs.requestFile.value = '';
                            self.requestUrl = null;
                            self.loader = false;
                    });

                },
                getUserRequestsList: function() {
                    this.loader = true;
                    axios.get('{{ route('getUserRequestsList')}}')
                        .then(function(response){
                            app.RequestsList = response.data;
                            app.loader = false;
                            app.RequestsList.forEach(function(item){
                                if(item.status == '{{ \App\Models\StudentRequest\StudentRequest::STATUS_NEW }}') {
                                    app.disableNewRequests[item.key] = true;
                                }
                            });
                        }).catch(function(error){
                    });


                },
                {{--
                bcGetExtension: function (filename) {
                    var parts = filename.split('.');
                    return parts[parts.length - 1];
                },
                bcTestImage: function (filename) {
                    var ext = this.bcGetExtension(filename);
                    switch (ext.toLowerCase()) {
                        case 'jpg':
                        case 'png':
                        case 'gif':
                        case 'webp':
                            return true;
                    }
                    return false;
                },
                bcCheckImageValid: function(elemId){
                    return true;

                    var file = $('#' + elemId);

                    if(!file.val())
                    {
                        return false;
                    }

                    if(!this.bcTestImage(file.val())) {
                        alert('{{ __('Image file has invalid format. Need jpg, png,gif or webp') }}');
                        file.val('');
                        return false;
                    }

                    return true;
                },

                mgGetExtension: function (filename) {
                    var parts = filename.split('.');
                    return parts[parts.length - 1];
                },
                mgSsImage: function (filename) {
                    var ext = this.mgGetExtension(filename);
                    switch (ext.toLowerCase()) {
                        case 'jpg':
                        case 'png':
                        case 'gif':
                        case 'webp':
                            return true;
                    }
                    return false;
                },
                mgCheckImageValid: function(elemId){
                    return true;

                    var file = $('#' + elemId);

                    if(!file.val())
                    {
                        return false;
                    }

                    if(!this.mgSsImage(file.val())) {
                        alert('{{ __('Image file has invalid format. Need jpg, png,gif or webp') }}');
                        file.val('');
                        return false;
                    }

                    return true;

                }
--}}

                sendDownAppLoadPhoto: function() {
                    $('#send_down_app_file').click();
                }
            },
            mounted: function(){
                this.loader = true;
                this.getUserDocsList();
                this.getUserRequestsList();
            }

            {{--
            created: function(){
                this.$root.$on('showEducation', function(val){
                    this.showEducation = val;
                });
            }
            --}}

        });

        function readURL(input, imgId) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $('#' + imgId).attr('src', e.target.result);
                    $('#' + imgId).css('display', 'block');
                };

                reader.readAsDataURL(input.files[0]);
            }
        }

        $('#send_down_app_file').change(function () {
            readURL(this, 'send_down_app_img');
        });

    </script>
@endsection