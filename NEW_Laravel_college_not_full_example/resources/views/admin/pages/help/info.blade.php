@extends("admin.admin_app")

@section("content")

    <div id="main">
        <div class="page-header">
            <h2> </h2>

            <a href="{{ URL::to('/helps') }}" class="btn btn-default-light btn-xs"><i class="md md-backspace"></i> Назад</a>

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

                @if($help->user->studentProfile && $help->user->teacherProfile)
                <ul class="nav nav-tabs">
                    <li class="nav-item active" id="tab_student" style="cursor: pointer">
                        <a onclick="selectTab('student')">Профиль студента</a>
                    </li>
                    <li class="nav-item " id="tab_teacher" style="cursor: pointer">
                        <a onclick="selectTab('teacher')">Профиль преподавателя</a>
                    </li>
                </ul>
                <br>
                @endif

                @if($help->user->studentProfile)
                <div id="student_profile" class="profile_tab">
                    <div class="col-md-12">
                        <h4>Студент</h4>
                        <hr>
                    </div>

                    @include('admin.pages.help.info_details.student.main')
                    @if($help->user->bcApplication)
                        @include('admin.pages.help.info_details.student.bcApplication')
                    @endif
                    @if($help->user->mgApplication)
                        @include('admin.pages.help.info_details.student.mgApplication')
                    @endif
                </div>
                @endif

                @if($help->user->teacherProfile)
                <div id="teacher_profile" class="profile_tab">
                    <div class="col-md-12">
                        <h4>Преподаватель</h4>
                        <hr>
                    </div>
                    @include('admin.pages.help.info_details.teacher.main')
                </div>
                @endif

                <div class="col-md-12">
                    <hr>
                    <a class="btn btn-primary" href="{{ route('adminHelpDelete', ['id' => $help->id]) }}">Проблема решена</a>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
<script type="text/javascript">

    function selectTab(role)
    {
        $('.profile_tab').hide();
        $('#' + role + '_profile').show();
        $('.nav-item').removeClass('active');
        $('#tab_' + role).addClass('active');
    }

</script>
@endsection
