<!-- Sidebar Left -->
<div class="sidebar left-side" id="sidebar-left">
    <div class="sidebar-user">
        <div class="media sidebar-padding">
            <div class="media-left media-middle">

                @if(Auth::user()->image_icon)

                    <img src="{{ URL::asset('upload/members/'.Auth::user()->image_icon.'-s.jpg') }}" width="60" alt="person" class="img-circle">
                @else

                    <img src="{{ URL::asset('admin_assets/images/guy.jpg') }}" alt="person" class="img-circle" width="60"/>

                @endif
            </div>
            <div class="media-body media-middle">

                <a href="{{ URL::to('/profile') }}" class="h4 margin-none">{{ Auth::user()->name }}</a>
                <ul class="list-unstyled list-inline margin-none">
                    <li><a href="{{ URL::to('/profile') }}"><i class="md-person-outline"></i></a></li>
                    @if(Auth::User()->usertype=="Admin")
                        <li><a href="{{ URL::to('/settings') }}"><i class="md-settings"></i></a></li>
                    @endif
                    <li><a href="{{ URL::to('/logout') }}"><i class="md-exit-to-app"></i></a></li>
                </ul>
            </div>
        </div>
    </div>
    <!-- Wrapper Reqired by Nicescroll (start scroll from here) -->
    <div class="nicescroll">
        <div class="wrapper" style="margin-bottom:90px">
            <div class="form-group has-feedback has-search">
                <span class="glyphicon glyphicon-search form-control-feedback" style="color: #fff;"></span>

                <input type="text" class="form-control" placeholder="Search" id="sidebar-search" style="background-color: transparent; color: #fff;">
            </div>

            <ul class="nav nav-sidebar" id="sidebar-menu">

                <li class="{{classActivePath('dashboard')}}"><a href="{{ URL::to('/dashboard') }}"><i class="fa fa-dashboard"></i> Обзор</a></li>
                <li class="{{classActivePath('profile')}}"><a href="{{ URL::to('/profile') }}"><i class="md md-person-outline"></i> Профиль</a></li>

                @if(\App\Services\Auth::user()->hasRight('roles', 'read'))
                    <li class="{{classActivePath('roles')}}"><a href="{{ route('adminRoleList') }}"><i class="fa fa-users"></i> Роли</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('users', 'read'))
                    <li class="{{classActivePath('users')}}"><a href="{{ URL::to('/users') }}"><i class="fa fa-users"></i>Пользователи</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('quiz', 'read'))
                    <li class="{{classActivePath('quiz')}}">
                        <a href="{{ route('admin.quiz.show') }}">
                            <i class="md md-settings"></i>Анкетирование
                        </a>
                    </li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('practice', 'read'))
                    <li class="{{classActivePath('practice')}}">
                        <a href="{{ route('admin.practice.show') }}">
                            <i class="md md-settings"></i>Практика
                        </a>
                    </li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('teachers', 'read'))
                    <li class="{{classActivePath('teachers')}}"><a href="{{ URL::to('/teachers') }}"><i class="fa fa-users"></i>Преподаватели</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('assign_teachers', 'read'))
                    <li class="{{classActivePath('assign/teachers')}}"><a href="{{ route('admin.assign.teachers.index')  }}"><i class="fa fa-users"></i>Назначить преподавателя</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('teacher_journal', 'read'))
                    <li class="{{classActivePath('teacher/journal')}}"><a href="{{ route('admin.teacher.journal.index')  }}"><i class="fa fa-users"></i>Журнал</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('guests', 'read'))
                    <li class="{{classActivePath('guests')}}"><a href="{{ URL::to('/guests') }}"><i class="fa fa-users"></i>Гости</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('students', 'read'))
                <!--<li class="{{classActivePath('students')}}"><a href="{{ URL::to('/students') }}"><i class="fa fa-users"></i>Студенты</a></li>-->
                @endif
                @if(\App\Services\Auth::user()->hasRight('courses', 'read'))
                    <li class="{{classActivePath('courses')}}"><a href="{{ URL::to('/courses') }}"><i class="fa fa-book"></i>Курсы</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('trends', 'read'))
                    <li class="{{classActivePath('trends')}}"><a href="{{ route('adminTrendList') }}"><i class="md md-settings"></i>Направления</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('specialities', 'read'))
                    <li class="{{classActivePath('specialities')}}"><a href="{{ route('adminSpecialityList') }}"><i class="md md-settings"></i>Образовательные программы</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('speciality_prices', 'read'))
                    <li class="{{classActivePath('speciality_prices')}}"><a href="{{ route('adminSpecialityPricesList') }}"><i class="md md-settings"></i>Спец. цены</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('speciality_semesters', 'read'))
                    <li class="{{classActivePath('speciality_semesters')}}"><a href="{{route('adminSpecialitySemesterList')}}"><i class="md md-settings"></i>Спец. семестры</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('modules', 'read'))
                    <li class="{{classActivePath('modules')}}"><a href="{{ route('adminModuleList') }}"><i class="md md-settings"></i>Модули</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('disciplines', 'read'))
                    <li class="{{classActivePath('disciplines')}}"><a href="{{ route('adminDisciplineList') }}"><i class="md md-settings"></i>Дисциплины</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('entrance_tests', 'read'))
                    <li class="{{classActivePath('entrance_tests')}}"><a href="{{ route('adminEntranceTestsList') }}"><i class="md md-settings"></i>Вступительные экзамены</a>
                    </li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('buildings', 'read'))
                    <li class="{{classActivePath('buildings')}}"><a href="{{ route('adminBuildingList') }}"><i class="md md-settings"></i>Здания</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('rooms', 'read'))
                    <li class="{{classActivePath('rooms')}}"><a href="{{ route('adminRoomList') }}"><i class="md md-settings"></i>Аудитории</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('helps', 'read'))
                    <li class="{{classActivePath('helps')}}"><a href="{{ route('adminHelpList') }}"><i class="md md-settings"></i>Помощь</a></li>
                @endif
                {{--
                @if(\App\Services\Auth::user()->hasRight('services', 'read'))
                    <li class="{{classActivePath('promotions')}}"><a href="{{ route('adminPromotionList') }}"><i class="md md-settings"></i>Акции</a></li>
                @endif
                --}}

                @if(\App\Services\Auth::user()->hasRight('discountrequests', 'read'))
                    <li class="{{classActivePath('discountrequests')}}"><a href="{{ route('adminDiscountRequestsList') }}"><i class="md md-settings"></i>Заявки на акции</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('discipline_pay_cancel', 'read'))
                    <li class="{{classActivePath('discipline_pay_cancel')}}"><a href="{{ route('adminDisciplinePayCancelList') }}"><i class="md md-settings"></i>Отмена покупки дисциплин</a></li>
                @endif


                @if(\App\Services\Auth::user()->hasRight('nobd_data', 'read'))
                    <li class="{{classActivePath('nobd')}}"><a href="{{ route('adminNobddataList') }}"><i class="md md-settings"></i>Данные НОБД</a></li>
                @endif


                @if(\App\Services\Auth::user()->hasRight('inspection', 'read'))


                    <li role="presentation" class="dropdown {{classActivePath('inspection')}}">
                        <a href="{{ route('adminInspectionMatriculantstList') }}"><i class="md md-settings"></i>Приемка</a>
                    <!--
			<a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
				<i class="md md-settings"></i>
				Приемка <span class="caret"></span>
			</a>
			<ul class="dropdown-menu">
				<li><a href="{{ route('adminInspectionMatriculantstList') }}">Абитуриенты</a></li>
				<li><a href="{{ route('adminInspectionList', ['tab' => 'bachelor']) }}">Настройки</a></li>
			</ul>-->
                    </li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('or_cabinet', 'read'))
                    <li class="{{classActivePath('or_cabinet')}}"><a href="{{ URL::to('/or_cabinet') }}"><i class="fa fa-users"></i>Кабинет ОР</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('test_pc_vi', 'read'))
                    <li class="{{classActivePath('entrance_exam')}}"><a href="{{ route('adminEntranceExamList') }}"><i class="fa fa-users"></i>ВИ</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('test_pc_pl', 'read'))
                    <li class="{{classActivePath('check_list')}}"><a href="{{ route('adminCheckListList') }}"><i class="fa fa-users"></i>ПЛ</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('study_plan', 'read'))
                    <li class="{{classActivePath('study_plan')}}"><a href="{{URL::to('/study_plan')}}"><i class="fa fa-users"></i>Учебный план</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('appeals', 'read'))
                    <li class="{{classActivePath('appeals')}}"><a href="{{route('adminAppealList')}}"><i class="fa fa-users"></i>Апелляции</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('quiz_results', 'read'))
                    <li class="{{classActivePath('quiz_results')}}"><a href="{{route('adminQuizResults')}}"><i class="fa fa-users"></i>Результаты тестов</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('news', 'read'))
                    <li class="{{classActivePath('news')}}"><a href="{{ URL::to('/news') }}"><i class="fa fa-users"></i>Объявления</a></li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('orders', 'read'))
                    <li class="{{classActivePath('orders')}}"><a href="{{ URL::to('/orders') }}"><i class="fa fa-users"></i>Приказы</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('nomenclature', 'read'))
                    <li class="{{classActivePath('nomenclature')}}">
                        <a href="{{ URL::to('/nomenclature') }}"><i class="md md-settings"></i>Номенклатура</a>
                    </li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('info_news', 'read'))
                    <li class="{{ classActivePath('info_news') }}">
                        <a href="{{ route('admin.news.get') }}"><i class="md md-settings"></i>Расписание</a>
                    </li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('info_table', 'read'))
                    <li class="{{ classActivePath('info_table') }}">
                        <a href="{{ route('admin.info.get') }}"><i class="md md-settings"></i>Infodesk</a>
                    </li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('library', 'read'))
                    <li class="{{classActivePath('library')}}">
                        <a href="{{ URL::to('/library') }}"><i class="md md-settings"></i>Библиотека</a>
                    </li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('manuals', 'read'))
                    <li class="{{classActivePath('manual')}}"><a href="{{ URL::to('/manual') }}"><i class="md md-settings"></i></i>Справочники</a></li>
                @endif

                @if(false && \App\Services\Auth::user()->hasRight('orders', 'read'))
                    <li class="{{classActivePath('orders')}}"><a href="{{ URL::to('/nobd') }}"><i class="fa fa-users"></i>НОБД\ЕСУВО</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('webcam', 'read'))
                    <li class="{{classActivePath('webcam')}}"><a href="{{route('admin.webcam.show')}}"><i class="fa fa-users"></i>Записи экзаменов</a></li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('etxt', 'read') or \App\Services\Auth::user()->hasRight('check_plagiarism_result', 'read') or 1 == 2)
                    <li role="presentation" class="dropdown {{classActivePath('check_plagiarism_result')}}">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="md md-settings"></i>
                            Плагиат <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @if(\App\Services\Auth::user()->hasRight('etxt', 'read'))
                                <li><a href="{{ route('etxtAntiPlagiat') }}">Проверка и результат</a></li>
                            @endif
                            @if(\App\Services\Auth::user()->hasRight('check_plagiarism_result', 'read'))
                                <li><a href="{{ route('admin.plagiarism.show') }}">Результаты проверки</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                <li class="{{classActivePath('forum')}}"><a href="{{route('chatter.home')}}"><i class="fa fa-users"></i>Форум</a></li>

                @if(\App\Services\Auth::user()->hasRight('activities', 'read') or \App\Services\Auth::user()->hasRight('visits', 'read'))
                    <li role="presentation" class="dropdown {{classActivePath('check_plagiarism_result')}}">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="md md-settings"></i>
                            Активность <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @if(\App\Services\Auth::user()->hasRight('visits', 'read'))
                                <li><a href="{{ URL::to('/visits') }}">Посещаемость</a></li>
                            @endif
                            @if(\App\Services\Auth::user()->hasRight('activities', 'read'))
                                <li><a href="{{ route('admin.activity.students') }}">Студенты</a></li>
                                <li><a href="{{ route('admin.activity.teachers') }}">Преподаватели</a></li>
                            @endif
                        </ul>
                    </li>
                @endif
                @if(\App\Services\Auth::user()->hasRight('employees', 'read'))
                    <li role="presentation" class="dropdown {{classActivePath('inspection')}}">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="md md-settings"></i>
                            Кадры <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            <li><a href="{{ route('employees.orders.page') }}">Приказы</a></li>
                            <li><a href="#" data-href="{{ route('employees.decree.all') }}">Аудит</a></li>
                            <li><a href="{{ route('employeesDepartment') }}">Отделы</a></li>
                            <li><a href="{{ route('employeesPosition') }}">Должности</a></li>
                            <li><a href="{{ route('employeesVacancy') }}">Вакансии</a></li>
                            <li><a href="{{ route('employeesUsers') }}">Сотрудники</a></li>
                            <li><a href="{{ route('employees.candidates') }}">Кандидаты</a></li>
                        </ul>
                    </li>
                @endif
                @if(
                    \App\Services\Auth::user()->hasRight('export_students', 'read') ||
                    \App\Services\Auth::user()->hasRight('discipline_practice_upload', 'read') ||
                    \App\Services\Auth::user()->hasRight('export_sro_courses', 'read') ||
                    \App\Services\Auth::user()->hasRight('export_diplomas', 'read') ||
                    \App\Services\Auth::user()->hasRight('export_exam_sheet', 'read')
                )
                    <li role="presentation" class="dropdown {{classActivePath('inspection')}}">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="md md-settings"></i>
                            Выгрузки <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @if(\App\Services\Auth::user()->hasRight('export_students', 'read'))
                                <li><a href="{{ route('adminExportStudents') }}">Студенты</a></li>
                            @endif

                            @if(\App\Services\Auth::user()->hasRight('export_student_result', 'read'))
                                <li><a href="{{ route('adminExportExamResults') }}">Рез. студентов</a></li>
                            @endif

                            @if(\App\Services\Auth::user()->hasRight('discipline_practice_upload', 'read'))
                                <li><a href="{{ route('adminExportPractice') }}">Практика</a></li>
                            @endif

                            @if(\App\Services\Auth::user()->hasRight('export_sro_courses', 'read'))
                                <li><a href="{{ route('adminExportSROPayCourses') }}">СРО курсовые</a></li>
                            @endif

                            @if(\App\Services\Auth::user()->hasRight('export_exam_sheet', 'read'))
                                <li><a href="{{route('adminExportExamSheets')}}">Экз. ведомость</a></li>
                            @endif

                            @if(\App\Services\Auth::user()->hasRight('export_diplomas', 'read'))
                                <li><a href="{{route('adminExportDiplomas')}}">Архив выдачи дипломов</a></li>
                            @endif

                            @if(\App\Services\Auth::user()->hasRight('export_activities', 'read'))
                                <li><a href="{{route('admin.activity.export')}}">Активности</a></li>
                            @endif
                        </ul>
                    </li>
                @endif

                @if(\App\Services\Auth::user()->hasRight('applications', 'read'))
                    <li role="presentation" class="dropdown {{classActivePath('applications')}}">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="md md-settings"></i>
                            Заявления <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @if(\App\Services\Auth::user()->hasRight('applications', 'edit'))
                                <li><a href="{{ route('adminApplicationTypeList') }}">Типы</a></li>
                            @endif
                            @if(\App\Services\Auth::user()->hasRight('applications', 'read'))
                                @inject('requestType', '\App\Models\StudentRequest\StudentRequestType')
                                @foreach( $requestType::get() as $type )
                                    <li><a href="{{ route('adminApplicationList', ['type' => $type->key]) }}">{{$type->name_ru}}</a></li>
                                @endforeach
                            @endif
                        </ul>
                    </li>
                @endif

                @if(
                \App\Services\Auth::user()->hasRight('agitator_transactions', 'read')
            )

                    <li role="presentation" class="dropdown {{classActivePath('agitator')}}">
                        <a class="dropdown-toggle" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
                            <i class="md md-settings"></i>
                            Агитаторы <span class="caret"></span>
                        </a>
                        <ul class="dropdown-menu">
                            @if(\App\Services\Auth::user()->hasRight('agitator_transactions', 'read'))
                                <li><a href="{{ route('adminAgitatorTransactions') }}">Транзакции</a></li>
                            @endif
                        </ul>
                    </li>
                        @endif

            </ul>
        </div>
    </div>
</div>
<!-- // Sidebar -->

<!-- Sidebar Right -->
<div class="sidebar right-side" id="sidebar-right">
    <!-- Wrapper Reqired by Nicescroll -->
    <div class="nicescroll">
        <div class="wrapper">
            <div class="block-primary">
                <div class="media">
                    <div class="media-left media-middle">
                        <a href="#">
                            @if(Auth::user()->image_icon)

                                <img src="{{ URL::asset('upload/members/'.Auth::user()->image_icon.'-s.jpg') }}" width="60" alt="person" class="img-circle border-white">

                            @else

                                <img src="{{ URL::asset('admin_assets/images/guy.jpg') }}" alt="person" class="img-circle border-white" width="60"/>

                            @endif
                        </a>
                    </div>
                    <div class="media-body media-middle">
                        <a href="{{ URL::to('/profile') }}" class="h4">{{ Auth::user()->name }}</a>
                        <a href="{{ URL::to('/logout') }}" class="logout pull-right"><i class="md md-exit-to-app"></i></a>
                    </div>
                </div>
            </div>
            <ul class="nav nav-sidebar" id="sidebar-menu">
                <li><a href="{{ URL::to('/profile') }}"><i class="md md-person-outline"></i> Профайл</a></li>

                @if(Auth::user()->usertype=='Admin')

                    <li><a href="{{ URL::to('/settings') }}"><i class="md md-settings"></i> Настройки</a></li>

                @endif

                <li><a href="{{ URL::to('/logout') }}"><i class="md md-exit-to-app"></i> Выйти</a></li>
            </ul>
        </div>
    </div>
</div>
<!-- // Sidebar -->

@section('scripts_sidebar')
    <script type="application/javascript">
        $('#sidebar-search').keyup(function () {
            var search_value = this.value.toLowerCase();

            $('#sidebar-menu li').removeClass('hide');

            $('#sidebar-menu li a').each(function (index, item) {
                var sidebar_text = item.innerText.toLowerCase();

                if (sidebar_text.indexOf(search_value) === -1) {
                    $(item).parent('li').addClass('hide');
                }
            });
        });
    </script>
@endsection
