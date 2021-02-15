@extends('student.shop.main')

@section('shop-content')
    <div class="row">
        <div class="col-12" style="margin-bottom: 20px;">
            <a href="{{ route('studentShopDisciplines') }}"><div class="switch-btn"></div></a>
        </div>
        <h3>{{ __('Non-core courses') }}</h3>
        <hr>
            @foreach($courseList as $course)
                <div class="col-sm-4 col-md-4 col-xs-6">
                    <div class="thumbnail">
                        @if(isset($course->photo_file_name) && file_exists(public_path('images/uploads/courses/' . $course->photo_file_name)))
                        <img src="/images/uploads/courses/{{$course->photo_file_name}}" style="height:150px;">
                        @endif
                        <div class="caption">
                            <h3><a href="{{ route('studentShopDetails', ['id' => $course->id]) }}">{{ $course->title }}</a></h3>
                            <p>{{  $course->description }}</p>
                            <p> <small>{{ $course->user->teacherProfile->fio }}</small></p>
                            <p><a href="{{ route('studentShopDetails', ['id' => $course->id]) }}">{{ __('Read more') }}</a></p>
                        </div>
                    </div>
                </div>
            @endforeach
    </div>
@endsection
