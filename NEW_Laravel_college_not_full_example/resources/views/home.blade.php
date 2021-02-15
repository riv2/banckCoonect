@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-md-8 col-md-offset-2">
            <div class="panel panel-default">
                <div class="panel-heading">{{__('Dashboard')}}</div>
                @if(isset($isTeacher) && ($isTeacher === true))
                    <p>Привет, Учитель!</p>
                @else
                    <p>Привет, ученик!</p>
                @endif
                <div class="panel-body">
                    @if (session('status'))
                        <div class="alert alert-success">
                            {{ session('status') }}
                        </div>
                    @endif

                    <p>{{__('Welcome!')}} </p>

                    
                    <div id="instaFeed">


                        {{-- printing instagram data --}}
                        @foreach ($instaFeeds as $feed)

                            <div class='col-md-4 col-sm-6 col-xs-12 item_box'>   
                                <a href='{{$feed['link']}}' target='_blank'>
                                    <img class='img-responsive photo-thumb' src='{{$feed['pic_src']}}' />
                                </a>
                                <p>
                                    <p>
                                       <div style='color:#888;'>
                                           <a href='{{$feed['link']}}' target='_blank'>{{$feed['time']}}</a>
                                        </div>
                                    </p>
                                    <p>{{$feed['text']}}</p>
                                </p>
                            </div>

                        @endforeach
                    
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
