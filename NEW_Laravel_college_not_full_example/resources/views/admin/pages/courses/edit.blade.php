@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> </h2>

            <a href="{{ URL::to('/courses') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

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
                {!! Form::open(array('url' => array( route('adminCourseEdit', ['id' => $course->id ?? 'add' ]) ),'class'=>'form-horizontal padding-15','name'=>'service_form','id'=>'service_form','role'=>'form','enctype' => 'multipart/form-data')) !!}


                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Внешние поля</h3>
                    </div>
                    <div class="panel-body">


                        {{-- Course title --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Наименование</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="title" value="{{ $course->title }}" required />
                            </div>
                        </div>

                        {{-- Course title kz --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Наименование KZ</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="title_kz" value="{{ $course->title_kz }}" required />
                            </div>
                        </div>

                        {{-- Course title en --}}
                        <div class="form-group">
                            <label class="col-sm-3 control-label">Наименование EN</label>
                            <div class="col-sm-9">
                                <input class="form-control" type="text" name="title_en" value="{{ $course->title_en }}" required />
                            </div>
                        </div>

                        <!-- COURSE AUTHOR RESUME FILE -->
                        <div class="form-group{{ $errors->has('author_resume_file') ? ' has-error' : '' }}">
                            <label for="author_resume_file" class="col-md-3 control-label">{{__('Разработчик/автор с кратким резюме (файл)')}}</label>
                            <div class="col-md-3">
                                @if($course->author_resume_file)
                                    <a href="/images/uploads/courses/{{ $course->author_resume_file }}" target="_blank">Посмотреть файл</a>
                                @endif
                                <input id="author_resume_file" type="file" class="form-control" name="author_resume_file" autofocus>
                                @if ($errors->has('author_resume_file'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('author_resume_file') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE AUTHOR RESUME LINK -->
                        <div class="form-group{{ $errors->has('author_resume_link') ? ' has-error' : '' }}">
                            <label for="author_resume_link" class="col-md-3 control-label">{{__('Разработчик/автор с кратким резюме (ссылка)')}}</label>
                            <div class="col-md-9">
                                <input id="author_resume_link" type="text" class="form-control" name="author_resume_link" value="{{ isset($course->author_resume_link) ? $course->author_resume_link : '' }}" autofocus>
                                @if ($errors->has('author_resume_link'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('author_resume_link') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE IMAGE -->
                        <div class="form-group{{ $errors->has('photo') ? ' has-error' : '' }}">
                            <label for="image" class="col-md-3 control-label">{{__('Фото')}}</label>
                            <div class="col-md-3">
                                @if($course->photo_file_name && file_exists( public_path('/images/uploads/courses/' . $course->photo_file_name)))
                                    <img src="/images/uploads/courses/{{ $course->photo_file_name }}" target="_blank" style="max-height: 200px;"/>
                                @endif
                                <input id="image" type="file" class="form-control" name="photo" autofocus>
                                @if ($errors->has('image'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('image') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE TITLE CARD -->
                        <div class="form-group{{ $errors->has('title_card') ? ' has-error' : '' }}">
                            <label for="title_card" class="col-md-3 control-label">{{__('Описание для визитки')}}</label>
                            <div class="col-md-9">
                                <textarea id="title_card" class="form-control" name="title_card" required autofocus>{{ isset($course->title_card) ? $course->title_card : '' }}</textarea>
                                @if ($errors->has('title_card'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('title_card') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE TITLE CARD KZ -->
                        <div class="form-group{{ $errors->has('title_card_kz') ? ' has-error' : '' }}">
                            <label for="title_card_kz" class="col-md-3 control-label">{{__('Описание для визитки')}} KZ</label>
                            <div class="col-md-9">
                                <textarea id="title_card_kz" class="form-control" name="title_card_kz" required autofocus>{{ isset($course->title_card_kz) ? $course->title_card_kz : '' }}</textarea>
                                @if ($errors->has('title_card_kz'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('title_card_kz') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE TITLE CARD EN -->
                        <div class="form-group{{ $errors->has('title_card_en') ? ' has-error' : '' }}">
                            <label for="title_card_en" class="col-md-3 control-label">{{__('Описание для визитки')}} EN</label>
                            <div class="col-md-9">
                                <textarea id="title_card_en" class="form-control" name="title_card_en" required autofocus>{{ isset($course->title_card_en) ? $course->title_card_en : '' }}</textarea>
                                @if ($errors->has('title_card_en'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('title_card_en') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                <div class="panel panel-success">
                    <div class="panel-heading">
                        <h3 class="panel-title">Внутренние поля</h3>
                    </div>
                    <div class="panel-body">


                        <!-- COURSE FORM HOLDING -->
                        <div class="form-group{{ $errors->has('form_holding') ? ' has-error' : '' }}">
                            <label for="form_holding" class="col-md-3 control-label"> {{ __('Form of the course') }} </label>
                            <div class="col-md-9">
                                <select v-model="coursesFormHolding" class="form-control" name="form_holding">
                                    <option value="{{ \App\Course::STATUS_FORM_HOLDING_FULLTIME }}" @if( !empty($course) && ($course->form_holding == \App\Course::STATUS_FORM_HOLDING_FULLTIME) ) selected @endif>{{ __(\App\Course::STATUS_FORM_HOLDING_FULLTIME) }}</option>
                                    <option value="{{ \App\Course::STATUS_FORM_HOLDING_ONLINE }}" @if( !empty($course) && ($course->form_holding == \App\Course::STATUS_FORM_HOLDING_ONLINE) ) selected @endif>{{ __(\App\Course::STATUS_FORM_HOLDING_ONLINE) }}</option>
                                    <option value="{{ \App\Course::STATUS_FORM_HOLDING_DISTANT }}" @if( !empty($course) && ($course->form_holding == \App\Course::STATUS_FORM_HOLDING_DISTANT) ) selected @endif>{{ __(\App\Course::STATUS_FORM_HOLDING_DISTANT) }}</option>
                                </select>
                            </div>
                        </div>


                        <!-- INNER PHOTO -->
                        <div class="form-group{{ $errors->has('inner_photo') ? ' has-error' : '' }}">
                            <label for="image" class="col-md-3 control-label">{{__('Фото')}}</label>
                            <div class="col-md-3">
                                @if($course->inner_photo && file_exists( public_path('/images/uploads/courses/' . $course->inner_photo)))
                                    <img src="/images/uploads/courses/{{ $course->inner_photo }}" target="_blank" style="max-height: 200px;"/>
                                @endif
                                <input id="image" type="file" class="form-control" name="inner_photo" autofocus>
                                @if ($errors->has('inner_photo'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('inner_photo') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE HOURSE -->
                        <div class="form-group{{ $errors->has('hours') ? ' has-error' : '' }}">
                            <label for="hours" class="col-md-3 control-label"> {{ __('Volume in hours') }} </label>
                            <div class="col-md-9">
                                <input id="hours" class="form-control" type="number" name="hours" value="{{ isset($course->hours) ? $course->hours : '' }}" required autofocus>
                                @if ($errors->has('hours'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('hours') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE COST -->
                        <div class="form-group{{ $errors->has('cost') ? ' has-error' : '' }}">
                            <label for="cost" class="col-md-3 control-label"> {{ __('Сourse price') }} </label>
                            <div class="col-md-9">

                                <input id="cost" class="form-control" type="number" min="1000" max="100000" step="1000" name="cost" value="{{ isset($course->cost) ? $course->cost : '' }}" required autofocus>
                                @if ($errors->has('cost'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('cost') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE DESCRIPTION -->
                        <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
                            <label for="description" class="col-md-3 control-label"> {{ __('Course description') }} </label>
                            <div class="col-md-9">
                                <textarea id="description" class="form-control" name="description" required autofocus>{{ isset($course->description) ? $course->description : '' }}</textarea>
                                @if ($errors->has('description'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE DESCRIPTION KZ -->
                        <div class="form-group{{ $errors->has('description_kz') ? ' has-error' : '' }}">
                            <label for="description_kz" class="col-md-3 control-label"> {{ __('Course description') }} KZ</label>
                            <div class="col-md-9">
                                <textarea id="description_kz" class="form-control" name="description_kz" required autofocus>{{ isset($course->description_kz) ? $course->description_kz : '' }}</textarea>
                                @if ($errors->has('description_kz'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description_kz') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE DESCRIPTION EN -->
                        <div class="form-group{{ $errors->has('description_en') ? ' has-error' : '' }}">
                            <label for="description_en" class="col-md-3 control-label"> {{ __('Course description') }} EN</label>
                            <div class="col-md-9">
                                <textarea id="description_en" class="form-control" name="description_en" required autofocus>{{ isset($course->description_en) ? $course->description_en : '' }}</textarea>
                                @if ($errors->has('description_en'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('description_en') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- TRIAL COURSE FILE -->
                        <div v-if="coursesFormHolding == '{{ \App\Course::STATUS_FORM_HOLDING_ONLINE }}' || coursesFormHolding == '{{ \App\Course::STATUS_FORM_HOLDING_DISTANT }}'"  class="form-group{{ $errors->has('trial_course_file') ? ' has-error' : '' }}">
                            <label for="trial_course_file" class="col-md-3 control-label"> {{__('Free trial lesson (file)')}} </label>
                            <div class="col-md-3">
                                @if($course->trial_course_file)
                                    <a href="/images/uploads/courses/{{ $course->trial_course_file }}" target="_blank">Посмотреть файл</a>
                                @endif
                                <input id="trial_course_file" type="file" class="form-control" name="trial_course_file" autofocus />
                                @if ($errors->has('trial_course_file'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('trial_course_file') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- TRIAL COURSE LINK -->
                        <div v-if="coursesFormHolding == '{{ \App\Course::STATUS_FORM_HOLDING_ONLINE }}' || coursesFormHolding == '{{ \App\Course::STATUS_FORM_HOLDING_DISTANT }}'" class="form-group{{ $errors->has('trial_course_link') ? ' has-error' : '' }}">
                            <label for="trial_course_link" class="col-md-3 control-label"> {{__('Free trial lesson (link)')}} </label>
                            <div class="col-md-9">
                                <input id="trial_course_link" type="text" class="form-control" name="trial_course_link" value="{{ isset($course->trial_course_link) ? $course->trial_course_link : '' }}" autofocus />
                                @if ($errors->has('trial_course_link'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('trial_course_link') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE TRAINING GROUP -->
                        {{--
                        <div v-if="coursesFormHolding == '{{ \App\Course::STATUS_FORM_HOLDING_FULLTIME }}'" class="form-group{{ $errors->has('training_group') ? ' has-error' : '' }}">
                            <label for="training_group" class="col-md-3 control-label">{{__('The size of the group – 12 participants')}}</label>
                            <div class="col-md-9">
                                <input id="training_group" type="number" min="1" max="12" step="1" class="form-control" name="training_group" value="{{ isset($course->training_group) ? $course->training_group : '' }}" autofocus />
                                @if ($errors->has('training_group'))
                                    <span class="help-block">
                                    <strong>{{ $errors->first('training_group') }}</strong>
                                </span>
                                @endif
                            </div>
                        </div>
                        --}}

                        <!-- IS CERTIFICATE -->
                        <div class="form-group{{ $errors->has('is_certificate') ? ' has-error' : '' }}">
                            <label for="is_certificate" class="col-md-3 control-label"> {{ __('Certificate availability') }} </label>
                            <div class="col-md-3">
                                <select class="form-control" name="is_certificate">
                                    <option value="{{ \App\Course::IS_CERTIFICATE_YES }}" @if( !empty($course) && ($course->is_certificate == \App\Course::IS_CERTIFICATE_YES) ) selected @endif>{{ __(\App\Course::IS_CERTIFICATE_YES) }}</option>
                                    <option value="{{ \App\Course::IS_CERTIFICATE_NO }}" @if( !empty($course) && ($course->is_certificate == \App\Course::IS_CERTIFICATE_NO) ) selected @endif>{{ __(\App\Course::IS_CERTIFICATE_NO) }}</option>
                                </select>
                            </div>
                        </div>
                        <!-- IS CERTIFICATE FILE -->
                        <div class="form-group">
                            <label for="coursesCertificateFile" class="col-md-3 control-label">Загрузить сертификат</label>

                            <div class="col-md-1">
                                <input v-model="coursesCertificateFile" id="coursesCertificateFile" type="checkbox" />
                            </div>
                        </div>
                        <!-- CERTIFICATE FILE -->
                        <div v-if="coursesCertificateFile" class="form-group{{ $errors->has('certificate_file') ? ' has-error' : '' }}">
                            <label for="certificate_file" class="col-md-3 control-label">{{__('Certificate')}}</label>

                            <div class="col-md-3">
                                @if($course->certificate_file_name)
                                    <a href="/images/uploads/certificates/{{ $course->certificate_file_name }}" target="_blank">Посмотреть сертификат</a>
                                @endif
                                <input id="certificate_file" type="file" class="form-control" name="certificate_file" autofocus>

                                    @if ($errors->has('certificate_file'))
                                        <span class="help-block">
                                        <strong>{{ $errors->first('certificate_file') }}</strong>
                                    @endif
                            </div>
                        </div>

                        <!-- SCHEME COURSE FILE -->
                        <div class="form-group{{ $errors->has('scheme_courses_file') ? ' has-error' : '' }}">
                            <label for="scheme_courses_file" class="col-md-3 control-label">{{__('Thematic plan of the course with a description of the results (file)')}}</label>
                            <div class="col-md-3">
                                @if($course->scheme_courses_file)
                                    <a href="/images/uploads/courses/{{ $course->scheme_courses_file }}" target="_blank">Посмотреть файл</a>
                                @endif
                                <input id="scheme_courses_file" type="file" class="form-control" name="scheme_courses_file" autofocus />
                                    @if ($errors->has('scheme_courses_file'))
                                        <span class="help-block">
                                            <strong>{{ $errors->first('scheme_courses_file') }}</strong>
                                        </span>
                                    @endif
                            </div>
                        </div>

                        <!-- SCHEME COURSE LINK -->
                        <div class="form-group{{ $errors->has('scheme_courses_link') ? ' has-error' : '' }}">
                            <label for="scheme_courses_link" class="col-md-3 control-label">{{__('Thematic plan of the course with a description of the results (link)')}}</label>
                            <div class="col-md-9">
                                <input id="scheme_courses_link" type="text" class="form-control" name="scheme_courses_link" value="{{ isset($course->scheme_courses_link) ? $course->scheme_courses_link : '' }}" autofocus />
                                @if ($errors->has('scheme_courses_link'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('scheme_courses_link') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- COURSE SCHEDULE -->
                        <div class="form-group{{ $errors->has('schedule') ? ' has-error' : '' }}">
                            <label for="schedule" class="col-md-3 control-label">{{__('Schedule')}}</label>
                            <div class="col-md-9">
                                <textarea id="schedule" class="form-control" name="schedule" required autofocus>{{ isset($course->schedule) ? $course->schedule : '' }}</textarea>
                                @if ($errors->has('schedule'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('schedule') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- LANGUAGE -->
                        <div class="form-group{{ $errors->has('language') ? ' has-error' : '' }}">
                            <label for="language" class="col-md-3 control-label">{{__('Язык')}}</label>

                            <div class="col-md-3">

                                @foreach( $language as $itemLang )

                                    <div class="col-md-10">
                                        <label>
                                            <input type="checkbox" value="{{ $itemLang['name'] }}"  name="language[]" {{ !empty($itemLang['data']) ? 'checked' : '' }} />
                                            {{ __($itemLang['name']) }}
                                        </label>
                                    </div>

                                @endforeach

                                {{--
                                <select id="language" class="form-control" name="language">
                                    <option value="{{ \App\Profiles::EDUCATION_LANG_RU }}" @if( !empty($topic) && ($topic->language == \App\Profiles::EDUCATION_LANG_RU) ) selected @endif> {{ __('russian') }} </option>
                                    <option value="{{ \App\Profiles::EDUCATION_LANG_KZ }}" @if( !empty($topic) && ($topic->language == \App\Profiles::EDUCATION_LANG_KZ) ) selected @endif> {{ __('kazakh') }} </option>
                                    <option value="{{ \App\Profiles::EDUCATION_LANG_EN }}" @if( !empty($topic) && ($topic->language == \App\Profiles::EDUCATION_LANG_EN) ) selected @endif> {{ __('english') }} </option>
                                </select>
                                --}}

                                @if ($errors->has('language'))
                                    <span class="help-block">
                                        <strong>{{ $errors->first('language') }}</strong>
                                    </span>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

                    <!-- TAGS -->
                    <div class="form-group{{ $errors->has('tags') ? ' has-error' : '' }}">
                        <label for="tags" class="col-md-3 control-label">{{__('Теги (разделитель - пробел)')}}</label>

                        <div class="col-md-9">
                            <input id="tags" type="text" class="form-control" name="tags" value="{{ isset($course->tags) ? $course->tags : '' }}" required autofocus>

                            @if ($errors->has('tags'))
                                <span class="help-block">
                                            <strong>{{ $errors->first('tags') }}</strong>
                                        </span>
                            @endif
                        </div>
                    </div>

                    <br>

                    @if( empty($course->user->educationDocumentList) || count($course->user->educationDocumentList) === 0)
                    <!-- Video link -->
                        <div class="form-group{{ $errors->has('video_link') ? ' has-error' : '' }}">
                            <label for="video_link" class="col-md-3 control-label">{{__('Видео (ссылка)')}}</label>
                            <div class="col-md-9">
                                <input id="video_link" type="text" class="form-control" name="video_link" value="{{ isset($course->video_link) ? $course->video_link : '' }}" autofocus>
                                @if ($errors->has('video_link'))
                                    <span class="help-block">
                                            <strong>{{ $errors->first('video_link') }}</strong>
                                        </span>
                                @endif
                            </div>
                        </div>
                    @endif


                    <div class="form-group">
                        <label for="tags" class="col-md-3 control-label">Проверено</label>

                        <div class="col-md-1">
                            <input id="status" type="checkbox" value="true"  name="status" {{ $course->status == \App\Course::STATUS_ACTIVE ? 'checked' : '' }}>
                        </div>
                    </div>

                    <hr>

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

                this.coursesFormHolding     = '{{ $course->form_holding ?? false }}';
                this.coursesCertificateFile = '{{ $course->certificate_file_name ?? false }}';
            }
        });

        $("#serviceType").change(function(){
            var placesList = $('#placesList');

            if( $(this).find('select').val() == 'Master' ) placesList.show(150);
            else placesList.hide(150);
        });

    </script>
@endsection