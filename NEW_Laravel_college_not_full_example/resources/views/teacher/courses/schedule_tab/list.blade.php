@foreach($dateList as $day => $lectureList)
    <div class="col-md-12">

    <h3>{{ $day }}</h3>
    @foreach($lectureList as $lecture)
        <div class="panel panel-default">
            <div class="panel-body">
                <h4>
                    {{ $lecture->start->format('H:i') . ' ' }}
                    <a href="{{ route('teacherLectureEdit', [
                                'courseId' => $lecture->course_id,
                                'lectureId' => $lecture->id
                            ]) }}">{{ $lecture->title }}
                    </a>
                </h4>
                <div class="col-md-5">
                    {{ __('Duration') }}: {{ $lecture->duration }} ак/ч.
                </div>
                <div class="col-md-5">
                    Тип:
                    @if($lecture->type == 'online')
                        {{__('Online')}}
                    @elseif($lecture->type == 'offline')
                        {{__('Offline')}}
                    @elseif ($lecture->type == 'all')
                        {{__('Online/Offline')}}
                    @endif
                </div>

                @if(isset($lecture->room->id) &&
                    ($lecture->type ==\App\Lecture::TYPE_OFFLINE || $lecture->type ==\App\Lecture::TYPE_ALL))
                    <div class="col-md-12">{{ __('Address') }}: {{ $lecture->room->building->name . ', ' . __('Floor') . ' ' . $lecture->room->floor . ', ' . __('Room') . ' №' . $lecture->room->number}}
                    @if($lecture->room_payed == false)
                    <a class="btn btn-primary" href="{{ route('teacherPayLectureRoom', ['id' => $lecture->id]) }}" target="_blank">{{ __('Pay room') . ' (' .$lecture->room->cost . ' ТГ)' }}</a>
                    @else
                    <span class="label label-success">{{ __('Audience paid') }}</span>
                    @endif
                    </div>
                @elseif($lecture->type ==\App\Lecture::TYPE_OFFLINE || $lecture->type ==\App\Lecture::TYPE_ALL)
                    <div class="col-md-12">
                        <span class="label label-danger">
                            {{ __('No audience selected') }}
                        </span>
                    </div>
                @endif
            </div>
        </div>
    @endforeach
    </div>
@endforeach