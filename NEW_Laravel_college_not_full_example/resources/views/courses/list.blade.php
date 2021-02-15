@extends('layouts.app')

@section('content')

    <section class="content">
        <div class="container-fluid">

            <div class="p-3 mb-2 bg-info"> <h2 class="text-white no-margin"> {{__('Courses')}} </h2> </div>

            <div class="card shadow-sm p-3 mb-5 bg-white rounded">
                <div class="card-body">

                    <div class="row">
                        <div class="col-md-12">


                            @if( !empty($courseStudent) && (count($courseStudent) > 0) )
                            <div class="row margin-b30">

                                <h3 class="col-12 text-center margin-b15"> {{ __('Purchased courses') }} </h3>

                                    @foreach( $courseStudent as $itemCS )
                                    @if( $itemCS->payed == \App\CourseStudent::STATUS_PAYED_YES )
                                        <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
                                            <div class="card courses-card discipline margin-b25">
                                                @if( !empty($itemCS->course->photo_file_name) && file_exists( public_path('/images/uploads/courses/' . $itemCS->course->photo_file_name)))
                                                    <a href="{{ route('getCoursePage',['course'=>$itemCS->course->id]) }}">
                                                        <img class="card-img-top" src="{{ '/images/uploads/courses/'. $itemCS->course->photo_file_name }}" />
                                                    </a>
                                                @endif
                                                <div class="card-body">
                                                    <h5 class="card-title margin-b10 clearfix"> {{ $itemCS->course->title ? substr($itemCS->course->getField('title'),0,60) : '' }} </h5><br>
                                                    <p class="card-text margin-tb10"> {{ $itemCS->course->title_card ? substr($itemCS->course->getField('title_card'),0,250) : '' }} </p>
                                                    <a class="btn btn-link btn-sm" href="{{ route('getCoursePage',['course'=>$itemCS->course->id]) }}"> {{ __('About courses') }} </a>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @endforeach

                            </div>
                            @endif


                            <div class="row">
                            @if( !empty($course) )

                                <h3 class="col-12 text-center margin-b15"> {{ __('Courses') }} </h3>

                                @foreach( $course as $item )

                                    <div class="col-xl-3 col-lg-3 col-md-3 col-sm-12 col-12">
                                        <div class="card courses-card discipline margin-b25">
                                            @if( !empty($item->photo_file_name) && file_exists( public_path('/images/uploads/courses/' . $item->photo_file_name)))
                                                <a href="{{ route('getCoursePage',['course'=>$item->id]) }}">
                                                    <img class="card-img-top" src="{{ '/images/uploads/courses/'. $item->photo_file_name }}" />
                                                </a>
                                            @endif
                                            <div class="card-body">
                                                <h5 class="card-title margin-b10 clearfix"> {{ $item->title ? substr($item->getField('title'),0,60) : '' }} </h5><br>
                                                <p class="card-text margin-tb10"> {{ $item->title_card ? substr($item->getField('title_card'),0,250) : '' }} </p>
                                                <a class="btn btn-link btn-sm" href="{{ route('getCoursePage',['course'=>$item->id]) }}"> {{ __('About courses') }} </a>
                                            </div>
                                        </div>
                                    </div>

                                @endforeach
                            @endif
                            </div>



                            {{--
                            @if( !empty($courseStudent) && (count($courseStudent) > 0) )

                                <h3 class="text-center margin-b15"> {{ __('Purchased courses') }} </h3>

                                <div class="card-group">
                                    @foreach( $courseStudent as $itemCS )
                                        <div class="card" style="width: 18rem;">
                                            @if( !empty($itemCS->photo_file_name) && file_exists( public_path('/images/uploads/courses/' . $itemCS->photo_file_name)))
                                                <img class="card-img-top" src="{{ '/images/uploads/courses/'. $itemCS->photo_file_name }}" />
                                            @endif
                                            <div class="card-body">
                                                <h5 class="card-title margin-b10 clearfix"> {{ $itemCS->title ? substr($itemCS->title,0,60) : '' }} </h5><br>
                                                <p class="card-text margin-tb10"> {{ $itemCS->title_card ? substr($itemCS->title_card,0,260) : '' }} </p>
                                                <a class="btn btn-link btn-sm" href="{{ route('getCoursePage',['course'=>$itemCS->id]) }}"> {{ __('About courses') }} </a>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif


                            @if( !empty($course) )
                            <div class="card-group">
                                @foreach( $course as $item )
                                    <div class="card">
                                        @if( !empty($item->photo_file_name) && file_exists( public_path('/images/uploads/courses/' . $item->photo_file_name)))
                                            <img class="card-img-top" src="{{ '/images/uploads/courses/'. $item->photo_file_name }}" />
                                        @endif
                                        <div class="card-body">
                                            <h5 class="card-title margin-b10 clearfix"> {{ $item->title ? substr($item->title,0,60) : '' }} </h5><br>
                                            <p class="card-text margin-tb10"> {{ $item->title_card ? substr($item->title_card,0,260) : '' }} </p>
                                            <a class="btn btn-link btn-sm" href="{{ route('getCoursePage',['course'=>$item->id]) }}"> {{ __('About courses') }} </a>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            @endif
                            --}}

                        </div>
                    </div>

                </div>
            </div>

        </div>
    </section>

@endsection