@extends("admin.admin_app")

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Курсы</h2>
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
                    <a class="btn btn-primary" href="{{ route('adminCourseEdit', ['id' => 0]) }}"> Добавить курс </a>
                </div>

                <table id="data-table-kz" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Название</th>
                        <th>Фото</th>
                        <th>Описание</th>
                        <th>Проверен</th>
                        <th class="text-center width-100">Действие</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($courseList as $key => $course)
                        <tr>

                            <td>{{ $course->id }}</td>
                            <td>{{ $course->getTitle() }}</td>
                            <td> @if($course->photo_file_name)
                                    <img src="{{ URL::asset('images/uploads/courses/'.$course->photo_file_name) }}" width="80" alt="">
                                @endif</td>
                            <td>{{ $course->title_card }}</td>
                            <td>{{ $course->status == \App\Course::STATUS_MODERATION ? 'Нет' : 'Да' }}</td>

                            <td class="text-center">

                                <div class="btn-group">
                                    @if(\App\Services\Auth::user()->hasRight('courses','edit'))
                                        <a class="btn btn-default" href="{{ route('adminCourseTopicsList',['id' => $course->id]) }}"><i class="md md-blur-circular"></i></a>
                                        <a class="btn btn-default" href="{{ route('adminCourseEdit', ['id' => $course->id]) }}"><i class="md md-edit"></i></a>
                                    @endif
                                    @if(\App\Services\Auth::user()->hasRight('courses','delete'))
                                        <button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li><a href="{{ route('adminCourseDelete', ['id' => $course->id]) }}"><i class="md md-delete"></i> Удалить</a></li>
                                        </ul>
                                    @endif
                                </div>

                            </td>

                        </tr>
                    @endforeach

                    </tbody>
                </table>


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


    </script>

@endsection

