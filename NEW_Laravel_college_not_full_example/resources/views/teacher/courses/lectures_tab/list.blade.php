@if(isset($lectureList))
<ul>
    @foreach($lectureList as $lecture)

        <li><a href='{{route('teacherLectureEdit', ['courseId' => $course->id, 'lectureId' => $lecture->id])}}'>{{ $lecture->title }}</a></li>

    @endforeach
</ul>
@endif

@if($course->canCreateLecture())
<a style="margin-left: 40px;" href='{{route('teacherLectureEdit', ['courseId' => $course->id, 'lectureId' => 'add'])}}'>{{ __('Create new lecture') }}</a>
@endif
