@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2>Курс {{ $course->title }}</h2>

            <a href="{{ route('adminCourseTopicsList',['id' => $course->id]) }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

        </div>
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default">
            <div class="panel-body">
                {!! Form::open(array('url' => array( route('adminCourseEditTopic', ['id' => $topic->id ?? 0, 'course_id' => $course->id ]) ),'class'=>'form-horizontal padding-15','name'=>'service_form','id'=>'service_form','role'=>'form','enctype' => 'multipart/form-data')) !!}


                    <!-- COURSE TOPIC TITLE -->
                    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
                        <label for="title" class="col-md-3 control-label">{{__('Наименование')}}</label>
                        <div class="col-md-9">
                            <input id="title" type="text" class="form-control" name="title" value="{{ isset($topic->title) ? $topic->title : '' }}" required autofocus />
                            @if ($errors->has('description'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('title') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <!-- LANGUAGE -->
                    <div class="form-group{{ $errors->has('language') ? ' has-error' : '' }}">
                        <label for="language" class="col-md-3 control-label">{{__('Язык')}}</label>

                        <div class="col-md-3">

                            <select id="language" class="form-control" name="language">
                                <option value="{{ \App\Profiles::EDUCATION_LANG_RU }}" @if( !empty($topic) && ($topic->language == \App\Profiles::EDUCATION_LANG_RU) ) selected @endif> {{ __('russian') }} </option>
                                <option value="{{ \App\Profiles::EDUCATION_LANG_KZ }}" @if( !empty($topic) && ($topic->language == \App\Profiles::EDUCATION_LANG_KZ) ) selected @endif> {{ __('kazakh') }} </option>
                                <option value="{{ \App\Profiles::EDUCATION_LANG_EN }}" @if( !empty($topic) && ($topic->language == \App\Profiles::EDUCATION_LANG_EN) ) selected @endif> {{ __('english') }} </option>
                            </select>

                            @if ($errors->has('language'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('language') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>

                    <!--UPLOAD FILE -->
                    <div  class="form-group{{ $errors->has('resource_file') ? ' has-error' : '' }}">
                        <label for="resource_file" class="col-md-3 control-label">{{__('Прикрепить файл')}}</label>
                        <div class="col-md-3">
                            @if($topic->resource_file)
                                <a href="/images/uploads/courses/{{ $topic->resource_file }}" target="_blank">Посмотреть файл</a>
                            @endif
                            <input id="resource_file" type="file" class="form-control" name="resource_file" autofocus />
                            @if ($errors->has('resource_file'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('resource_file') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- ADD LINK -->
                    <div class="form-group{{ $errors->has('resource_link') ? ' has-error' : '' }}">
                        <label for="resource_link" class="col-md-3 control-label">{{__('Добавить ссылку')}}</label>
                        <div class="col-md-9">
                            <input id="resource_link" type="text" class="form-control" name="resource_link" value="{{ isset($topic->resource_link) ? $topic->resource_link : '' }}" autofocus />
                            @if ($errors->has('resource_link'))
                                <span class="help-block">
                                    <strong>{{ $errors->first('resource_link') }}</strong>
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- COURSE QUESTIONS -->
                    <div class="form-group{{ $errors->has('questions') ? ' has-error' : '' }}">
                        <label for="questions" class="col-md-3 control-label">{{__('Вопросы')}}</label>
                        <div class="col-md-9">
                            <textarea id="questions" class="form-control" name="questions" autofocus>{{ isset($topic->questions) ? $topic->questions : '' }}</textarea>
                            @if ($errors->has('questions'))
                                <span class="help-block">
                                        <strong>{{ $errors->first('questions') }}</strong>
                                    </span>
                            @endif
                        </div>
                    </div>


                <div class="form-group">
                    <div class="col-md-offset-3 col-sm-9 ">
                        <button type="submit" class="btn btn-primary">Сохранить</button>
                    </div>
                </div>

                {!! Form::close() !!}
            </div>
        </div>

    </div>

@endsection

@section('scripts')
    <script type="text/javascript">

        var app = new Vue({
            el: '#main',
            data: {
                coursesFormHolding: false,
                coursesCertificateFile: false

            },
            created: function(){

                this.coursesFormHolding     = '{{ $topic->form_holding ?? false }}';
                this.coursesCertificateFile = '{{ $topic->certificate_file_name ?? false }}';
            }
        });

    </script>
@endsection