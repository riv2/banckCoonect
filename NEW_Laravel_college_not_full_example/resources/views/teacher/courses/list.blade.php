@if(count($courseList) > 0)
<ul>
    @foreach($courseList as $course)

        <li><a href='{{route('teacherCourseEdit', ['id' => $course->id])}}'>{{$course->getTitle()}}</a> ({{__($course->status)}})</li>

    @endforeach
    <li><a href='{{route('teacherCourseEdit', ['id' => 'add'])}}'>{{ __('Add course') }}</a></li>
</ul>
    <hr>
@endif