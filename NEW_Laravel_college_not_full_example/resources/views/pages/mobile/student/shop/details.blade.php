@extends('student.shop.main')

@section('shop-content')

    <div class="row">
        @if($course->photo_file_name && file_exists(public_path('images/uploads/courses/' . $course->photo_file_name)))
            <div class="col-3">
                <img style="max-width: 100%;" src="/images/uploads/courses/{{ $course->photo_file_name }}" />
            </div>
        @endif

            <div class="col-9">
                <h3>{{ isset($course->discipline->name) ? $course->discipline->name : $course->title }}</h3>
                <div class="col--3">{{ __('Language') }}:</div><div class="col-9">{{ $course->language }}</div>
                <div class="col-3">{{ __('Teacher') }}:</div>
                <div class="col-9">
                    <span>{{ $course->user->teacherProfile->fio }}</span>
                    @include('component.rating')
                </div>

                <div class="col-12">
                    {{ $course->description }}
                </div>
            </div>

            <div class="col-12">
            <h3>{{ __('Timetable of classes') }}</h3>
        </div>

        @if(isset($course->lectures) && count($course->lectures) > 0)
            @foreach($course->lectures as $lecture)
                <div class="col-12">
            <hr>
            <h4>{{ $lecture->title }}</h4>
            <div class="col-12" style="margin-bottom: 10px;">{{ $lecture->description }}</div>
            <div class="col-4">
                {{ __('Start') }}: {{ $lecture->start->format('d.m.Y H:i') }}
            </div>
            <div class="col-4">
                {{ __('Duration') }}: {{ $lecture->duration }} {{__("a.h.")}}
            </div>
            <div class="col-4">
                Тип:
                @if($lecture->type == 'online')
                    {{__('Online')}}
                @elseif($lecture->type == 'offline')
                    {{__('Offline')}}
                @elseif ($lecture->type == 'all')
                    {{__('Online/Offline')}}
                @endif
            </div>

            <div class="col-12">
                @if((($lecture->type == \App\Lecture::TYPE_OFFLINE && $lecture->seats_count > count($lecture->offlineStudents)) || $lecture->type == \App\Lecture::TYPE_ONLINE || $lecture->type == \App\Lecture::TYPE_ALL) && !$course->discipline )
                    {{ __('Cost') }}: {{ $lecture->cost }} ТГ
                @endif
                @if(strtotime($lecture->start) > time() && !$course->discipline && count($lecture->students) == 0)
                    @if($lecture->type == \App\Lecture::TYPE_ONLINE)
                        <a class="btn btn-primary" href="{{ route('studentPayLecture', ['id' => $lecture->id, 'type' => \App\StudentLecture::TYPE_ONLINE]) }}" target="_blank">{{ __('Buy') }}</a>
                    @endif
                    @if($lecture->type == \App\Lecture::TYPE_OFFLINE)
                        @if($lecture->seats_count > count($lecture->offlineStudents) )
                            <a class="btn btn-primary" href="{{ route('studentPayLecture', ['id' => $lecture->id, 'type' => \App\StudentLecture::TYPE_OFFLINE]) }}" target="_blank">{{ __('Buy') }}</a>
                        @else
                            <span class="label label-danger">
                                {{ __('No free seats') }}
                            </span>
                        @endif
                    @endif
                    @if($lecture->type == \App\Lecture::TYPE_ALL)
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ __('Buy') }} <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a href="{{ route('studentPayLecture', ['id' => $lecture->id, 'type' => \App\StudentLecture::TYPE_ONLINE]) }}" target="_blank">{{ __('Online') }}</a></li>
                                @if($lecture->seats_count > count($lecture->offlineStudents))
                                <li><a href="{{ route('studentPayLecture', ['id' => $lecture->id, 'type' => \App\StudentLecture::TYPE_OFFLINE]) }}" target="_blank">{{ __('Offline') }}</a></li>
                                @else
                                    <li class="disabled"><a>{{ __('Offline') . ' (' . __('No free seats') . ')' }}</a></li>
                                @endif
                            </ul>
                        </div>
                    @endif
                @endif
            </div>

            @if($course->discipline || (!$course->discipline && count($lecture->students) ))
                @if($lecture->type ==\App\Lecture::TYPE_ONLINE || $lecture->type ==\App\Lecture::TYPE_ALL)
                <div class="col-12">
                    <a href="{{ $lecture->url }}" target="_blank">{{ __('Go to online viewing') }}</a>
                </div>
                @endif

                @if(isset($lecture->room->id) &&
                    (($lecture->type ==\App\Lecture::TYPE_OFFLINE || ($lecture->type ==\App\Lecture::TYPE_ALL)) &&
                    isset($lecture->students->first()->pivot->type) &&
                    $lecture->students->first()->pivot->type == \App\StudentLecture::TYPE_OFFLINE ||
                     (isset($course->disciplines) && count($course->disciplines) >0)) )
                 <div class="col-12">{{ __('Address') }}: {{ $lecture->room->building->name . ', ' . __('Floor') . ' ' . $lecture->room->floor . ', ' . __('Room') . ' №' . $lecture->room->number}}</div>
                @endif
            @endif

        </div>
            @endforeach
        @else
            <div class="col-12">
                {{ __('Classes have not been added yet') }}
            </div>
        @endif
    </div>
@endsection

@section('scripts')
    <script type="text/javascript">
        $(function() {
            $('#rating').barrating({
                theme: 'fontawesome-stars-o',
                readonly: true,
                initialRating: {{ $course->user->teacherProfile->rating }}
            });
        });
    </script>
@endsection