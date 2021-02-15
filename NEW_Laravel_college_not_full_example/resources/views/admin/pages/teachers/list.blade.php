@extends("admin.admin_app")

@section('title', 'Преподаватели')

@section("content")
    <div id="main">
        <div class="page-header">
            <h2>Преподаватели</h2>
        </div>
        @if(Session::has('flash_message'))
            <div class="alert alert-success">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span></button>
                {{ Session::get('flash_message') }}
            </div>
        @endif

        <div class="panel panel-default panel-shadow">
            <div class="panel-body">
                <table id="data-table" class="table table-striped table-hover dt-responsive" cellspacing="0" width="100%">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Фото</th>
                        <th>ФИО</th>
                        <th>Email</th>
                        <th>Телефон</th>
                        <th>Статус</th>
                        <th class="text-center width-100">Действие</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($teacherList as $i => $teacher)
                        <tr>
                            <td>{{ $teacher->id }}</td>
                            <td>
                                @if(isset($teacher->teacherProfile->photo) && file_exists(public_path('avatars/' . $teacher->teacherProfile->photo)))
                                    <img src="/avatars/{{ $teacher->teacherProfile->photo }}" width="80" alt="{{ $teacher->teacherProfile->fio }}">
                                @endif
                            </td>
                            <td>{{ $teacher->teacherProfile->fio ?? ''}}</td>
                            <td>{{ $teacher->email ?? ''}}</td>
                            <td>{{ $teacher->phone ?? ''}}</td>
                            <td>
                            @if(isset($teacher->teacherProfile->status))
                                @if($teacher->teacherProfile->status == \App\Teacher\ProfileTeacher::STATUS_MODERATION)
                                    Не проверен
                                @elseif($teacher->teacherProfile->status == \App\Teacher\ProfileTeacher::STATUS_ACTIVE)
                                    Проверен
                                @elseif($teacher->teacherProfile->status == \App\Teacher\ProfileTeacher::STATUS_BLOCK)
                                    Заблокирован
                                @endif
                            @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group">
                                    @if(\App\Services\Auth::user()->hasRight('teachers','edit'))
                                        <a class="btn btn-default" href="{{ route('adminTeacherEdit', ['id' => $teacher->id]) }}"><i class="md md-edit"></i></a>
                                    @endif
                                    @if(\App\Services\Auth::user()->hasRight('teachers','delete'))
                                        <button type="button" class="btn btn-default-dark dropdown-toggle" data-toggle="dropdown" aria-expanded="false"><i class="md md-delete"></i><span class="caret"></span></button>
                                        <ul class="dropdown-menu dropdown-menu-right" role="menu">
                                            <li><a href="{{ route('adminTeacherDelete', ['id' => $teacher->id]) }}"><i class="md md-delete"></i> Удалить</a></li>
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