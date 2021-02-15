@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Курс {{ $course->title }}</h2>
            <a href="{{ route('adminCourseList') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>
        </div>
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body" id="app-courses">


                <div class="col-md-12 text-right" style="margin:20px 0px;">
                    <a class="btn btn-primary" href="{{ route('adminCourseEditTopic', ['id'=>0,'course_id'=>$course->id]) }}"> Добавить тему </a>
                </div>

                <ul class="nav nav-tabs">
                    <li role="presentation" class="tab-ru" :class="{'active': coursesLanguage=='ru'}" @click="coursesLanguage='ru'"><a href="#">Русский</a></li>
                    <li role="presentation" class="tab-kz" :class="{'active': coursesLanguage=='kz'}" @click="coursesLanguage='kz'"><a href="#">Казахский</a></li>
                    <li role="presentation" class="tab-en" :class="{'active': coursesLanguage=='en'}" @click="coursesLanguage='en'"><a href="#">Английский</a></li>
                </ul>

                <div class="panel panel-default panel-shadow" style="border-radius: 0px 5px 5px 5px;">
                    <div class="panel-body">

                        {{-- courses ru --}}
                        <div :class="{'hidden': coursesLanguage != 'ru'}">


                            <table id="data-table-ru" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Название</th>
                                    <th>Язык</th>
                                    <th class="text-center width-100">Действие</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($courseTopicListRu as $i => $topic1)
                                    <tr>
                                        <td>{{ $topic1->id }}</td>
                                        <td>{{ $topic1->title }}</td>
                                        <td>{{ __($topic1->language) }}</td>

                                        <td class="text-center">

                                            <div class="btn-group">
                                                @if(\App\Services\Auth::user()->hasRight('courses','edit'))
                                                    <a class="btn btn-default" href="{{ route('adminCourseEditTopic', ['id' => $topic1->id,'course_id' => $course->id]) }}"><i class="md md-edit"></i></a>
                                                @endif
                                                @if(\App\Services\Auth::user()->hasRight('courses','delete'))
                                                    <button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                        <li><a href="{{ route('adminCourseTopicDelete', ['id'=>$topic1->id]) }}"><i class="md md-delete"></i> Удалить</a></li>
                                                    </ul>
                                                @endif
                                            </div>

                                        </td>

                                    </tr>
                                @endforeach

                                </tbody>
                            </table>


                        </div>


                        {{-- courses kz --}}
                        <div :class="{'hidden': coursesLanguage != 'kz'}">


                            <table id="data-table-kz" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Название</th>
                                    <th>Язык</th>
                                    <th class="text-center width-100">Действие</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($courseTopicListKz as $i => $topic2)
                                    <tr>
                                        <td>{{ $topic2->id }}</td>
                                        <td>{{ $topic2->title }}</td>
                                        <td>{{ __($topic2->language) }}</td>

                                        <td class="text-center">

                                            <div class="btn-group">
                                                @if(\App\Services\Auth::user()->hasRight('courses','edit'))
                                                    <a class="btn btn-default" href="{{ route('adminCourseEditTopic', ['id' => $topic2->id,'course_id' => $course->id]) }}"><i class="md md-edit"></i></a>
                                                @endif
                                                @if(\App\Services\Auth::user()->hasRight('courses','delete'))
                                                    <button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                        <li><a href="{{ route('adminCourseTopicDelete', ['id' => $topic2->id]) }}"><i class="md md-delete"></i> Удалить</a></li>
                                                    </ul>
                                                @endif
                                            </div>

                                        </td>

                                    </tr>
                                @endforeach

                                </tbody>
                            </table>


                        </div>


                        {{-- courses en --}}
                        <div :class="{'hidden': coursesLanguage != 'en'}">


                            <table id="data-table-en" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                                <thead>
                                <tr>
                                    <th>Id</th>
                                    <th>Название</th>
                                    <th>Язык</th>
                                    <th class="text-center width-100">Действие</th>
                                </tr>
                                </thead>

                                <tbody>
                                @foreach($courseTopicListEn as $i => $topic3)
                                    <tr>
                                        <td>{{ $topic3->id }}</td>
                                        <td>{{ $topic3->title }}</td>
                                        <td>{{ __($topic3->language) }}</td>

                                        <td class="text-center">

                                            <div class="btn-group">
                                                @if(\App\Services\Auth::user()->hasRight('courses','edit'))
                                                    <a class="btn btn-default" href="{{ route('adminCourseEditTopic', ['id' => $topic3->id,'course_id' => $course->id]) }}"><i class="md md-edit"></i></a>
                                                @endif
                                                @if(\App\Services\Auth::user()->hasRight('courses','delete'))
                                                    <button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                                    <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                                        <li><a href="{{ route('adminCourseTopicDelete', ['id' => $topic3->id]) }}"><i class="md md-delete"></i> Удалить</a></li>
                                                    </ul>
                                                @endif
                                            </div>

                                        </td>

                                    </tr>
                                @endforeach

                                </tbody>
                            </table>


                        </div>


                    </div>
                </div>





            </div>
            <div class="clearfix"></div>
        </div>

    </div>

@endsection

@section('scripts')

    <script type="text/javascript">
        var app = new Vue({
            el: '#app-courses',
            data: {
                coursesLanguage: false

            },
            created: function(){

                this.coursesLanguage = '{{ \App\Profiles::EDUCATION_LANG_RU }}';

            }
        });

        $('#data-table-ru').dataTable();
        $('#data-table-kz').dataTable();
        $('#data-table-en').dataTable();

    </script>

@endsection

