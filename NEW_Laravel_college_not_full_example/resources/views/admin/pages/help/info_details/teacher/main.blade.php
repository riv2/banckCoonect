<ul class="nav nav-tabs">
    <li class="nav-item nav-item-teacher active" id="tab_info" style="cursor: pointer">
        <a onclick="selectTeacherTab('info')">Профиль</a>
    </li>
    <li class="nav-item nav-item-teacher" id="tab_education" style="cursor: pointer">
        <a onclick="selectTeacherTab('education')">Образование</a>
    </li>
    <li class="nav-item nav-item-teacher" id="tab_courses" style="cursor: pointer">
        <a onclick="selectTeacherTab('courses')">Курсы</a>
    </li>
</ul>

<div class="teacher_tab" id="info_teacher">
    @include('admin.pages.help.info_details.teacher.info')
</div>
<div class="teacher_tab" id="education_teacher" style="display: none">
    @include('admin.pages.help.info_details.teacher.education')
</div>
<div class="teacher_tab" id="courses_teacher" style="display: none">
    @include('admin.pages.help.info_details.teacher.courses')
</div>

@section('scripts')
    <script type="text/javascript">

        function selectTeacherTab(role)
        {
            $('.teacher_tab').hide();
            $('#' + role + '_teacher').show();
            $('.nav-item-teacher').removeClass('active');
            $('#tab_' + role).addClass('active');
        }

    </script>
@endsection