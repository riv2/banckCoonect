<ul class="nav nav-tabs">
    <li class="nav-item {{ isset($courseTab) && $courseTab == true ? 'active' : '' }}">
        <a class="nav-link" href="{{route('teacherCourseEdit', ['id' => $course->id])}}"> {{ __('Course') }} </a>
    </li>
    <li class="nav-item {{ isset($lecturesTab) && $lecturesTab == true ? 'active' : '' }}">
        <a class="nav-link" href="{{route('teacherLectureList', ['courseId' => $course->id])}}"> {{ __('Classes') }} </a>
    </li>
    <li class="nav-item {{ isset($scheduleTab) && $scheduleTab == true ? 'active' : '' }}">
        <a class="nav-link" href="{{route('teacherScheduleList', ['courseId' => $course->id])}}"> {{ __('Timetable') }} </a>
    </li>
</ul>
<br>