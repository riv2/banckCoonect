@include('teacher.courses.lectures_tab.list')

@if(isset($lecture))
    <hr>
<form class="form-horizontal" method="POST" action="" enctype="multipart/form-data">
    {{ csrf_field() }}

    <div class="form-group{{ $errors->has('title') ? ' has-error' : '' }}">
        <label for="title" class="col-md-4 control-label">{{__('Theme')}}</label>

        <div class="col-md-6">
            <input id="title" type="text" class="form-control" name="title" value="{{ $lecture->title ?? '' }}" required autofocus>

            @if ($errors->has('title'))
                <span class="help-block">
                    <strong>{{ $errors->first('title') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('description') ? ' has-error' : '' }}">
        <label for="title" class="col-md-4 control-label">{{__('Description')}}</label>

        <div class="col-md-6">
            <textarea id="description" type="text" class="form-control" name="description" value="" required autofocus>{{ $lecture->description ?? '' }}</textarea>

            @if ($errors->has('description'))
                <span class="help-block">
                    <strong>{{ $errors->first('description') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('start') ? ' has-error' : '' }}">
        <label for="start" class="col-md-4 control-label">{{__('Start (date and time)')}}</label>

        <div class="col-md-6">
            @if(!$lecture->room_payed)
                <input id="start" type="text" class="form-control" name="start" value="{{ isset($lecture->start) && $lecture->start ? $lecture->start->format('d.m.Y H:i') : '' }}" required autofocus>

                @if ($errors->has('start'))
                    <span class="help-block">
                        <strong>{{ $errors->first('start') }}</strong>
                    </span>
                @endif
            @else
                {{ isset($lecture->start) && $lecture->start ? $lecture->start->format('d.m.Y H:i') : '' }}
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('duration') ? ' has-error' : '' }}">
        <label for="duration" class="col-md-4 control-label">{{__('Duration (academic hours)')}}</label>

        <div class="col-md-3">
            @if(!$lecture->room_payed)
                <input id="duration" type="number" max="5" min="1" class="form-control" name="duration" value="{{ $lecture->duration ?? '' }}" required autofocus>

                @if ($errors->has('duration'))
                    <span class="help-block">
                        <strong>{{ $errors->first('duration') }}</strong>
                    </span>
                @endif
            @else
                {{ $lecture->duration ?? '' }}
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('cost') ? ' has-error' : '' }}">
        <label for="cost" class="col-md-4 control-label">{{__('Cost tg')}}</label>

        <div class="col-md-3">
            <input id="cost" type="number" class="form-control" name="cost" value="{{ $lecture->cost ?? '' }}" required autofocus>

            @if ($errors->has('cost'))
                <span class="help-block">
                    <strong>{{ $errors->first('cost') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('type') ? ' has-error' : '' }}">
        <label for="type" class="col-md-4 control-label">{{__('Type of lecture')}}</label>
        <div class="col-md-6">
            @if(!$lecture->room_payed)
                <select required class="selectpicker" id="lecture-type" value="{{ $lecture->type ?? \App\Lecture::TYPE_ONLINE }}" name="type"
                        title="{{ __('Please select') }}">
                    <option value="online"
                        {{ !$lecture->type || $lecture->type ==  \App\Lecture::TYPE_ONLINE ? 'selected' : ''}}
                    >{{__('Online')}}</option>
                    <option value="offline"
                        {{ $lecture->type ==  \App\Lecture::TYPE_OFFLINE ? 'selected' : ''}}
                    >{{__('Offline')}}</option>
                    <option value="all"
                            {{ $lecture->type ==  \App\Lecture::TYPE_ALL ? 'selected' : ''}}
                    >{{__('Online/Offline')}}</option>
                </select>

                @if ($errors->has('type'))
                    <span class="help-block">
                    <strong>{{ $errors->first('type') }}</strong>
                </span>
                @endif
            @else
                @if($lecture->type ==  \App\Lecture::TYPE_ONLINE)
                    {{__('Online')}}
                @endif
                @if($lecture->type ==  \App\Lecture::TYPE_OFFLINE)
                    {{__('Offline')}}
                @endif
                @if($lecture->type ==  \App\Lecture::TYPE_ALL)
                    {{__('Online/Offline')}}
                @endif
            @endif
        </div>
    </div>

    <div class="form-group{{ $errors->has('tags') ? ' has-error' : '' }}">
        <label for="tags" class="col-md-4 control-label">{{__('Tags')}}</label>

        <div class="col-md-6">
            <input id="tags" type="text" class="form-control" name="tags" value="{{ $lecture->tags ?? '' }}" required autofocus>

            @if ($errors->has('tags'))
                <span class="help-block">
                    <strong>{{ $errors->first('tags') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div id="lecture-type-online-panel" class="form-group{{ $errors->has('url') ? ' has-error' : '' }}" @if(isset($lecture->type) && $lecture->type ==  \App\Lecture::TYPE_OFFLINE) style="display: none;"@endif>
        <label for="url" class="col-md-4 control-label">{{__('Link to resource')}}</label>

        <div class="col-md-6">
            <input id="url" type="url" class="form-control" name="url" value="{{ $lecture->url ?? '' }}">

            @if ($errors->has('url'))
                <span class="help-block">
                    <strong>{{ $errors->first('url') }}</strong>
                </span>
            @endif
        </div>
    </div>

    <div id="lecture-type-offline-panel" @if((isset($lecture->type) && ($lecture->type ==  \App\Lecture::TYPE_ONLINE)) || !isset($lecture->type))
        style="display: none;"
        @endif>
        <hr>

        @if(isset($lecture->type) && ($lecture->type ==  \App\Lecture::TYPE_OFFLINE || $lecture->type ==  \App\Lecture::TYPE_ALL) && $lecture->id>0 && !isset($lecture->room->number))
            <div class="alert alert-danger">
                {{ __('Room not found. Please change parameters') }}
            </div>
        @endif

        <div class="form-group">
            <label for="room_building_id" class="col-md-4 control-label">{{ __('Building name') }}</label>
            <div class="col-md-6">
                @if(!$lecture->room_payed)
                    <select class="selectpicker" id="room_building_id" value="{{ $lecture->room->building_id ?? 'no_matter' }}" name="room[building_id]"
                            title="{{ __('Please select') }}">
                        <option value="0" @if(!isset($lecture->room->building_id)) selected @endif>{{ __('No matter') }}</option>

                        @foreach($buildingList as $building)
                        <option value="{{ $building->id }}" @if(isset($lecture->room->building->id) && $lecture->room->building->id == $building->id) selected @endif>{{ $building->name }}</option>
                        @endforeach
                    </select>
                @else
                    {{ $lecture->room->building->name ?? '' }}
                @endif
            </div>
        </div>

        @if(isset($lecture->room->number))
        <div id="lecture-type-online-panel" class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
            <label for="url" class="col-md-4 control-label">{{__('Room number')}}</label>
            <div class="col-md-6"> {{ $lecture->room->number }} </div>
        </div>
        @endif

        @if(isset($lecture->room->floor))
            <div id="lecture-type-online-panel" class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                <label for="url" class="col-md-4 control-label">{{__('Floor')}}</label>
                <div class="col-md-6"> {{ $lecture->room->floor }} </div>
            </div>
        @endif

        <div class="form-group{{ $errors->has('seats_count') ? ' has-error' : '' }}">
            <label for="seats_count" class="col-md-4 control-label">{{__('Room type')}}</label>

            <div class="col-md-6">
                @if(!$lecture->room_payed)
                <select class="selectpicker" id="room_type" value="{{ $lecture->room->type ?? '' }}" name="room[type]"
                        title="{{ __('Please select') }}">
                    <option value="lecture" @if(isset($lecture->room->type) && $lecture->room->type == 'lecture') selected @endif>{{ __('Room lecture') }}</option>
                    <option value="computing" @if(isset($lecture->room->type) && $lecture->room->type == 'computing') selected @endif>{{ __('Room computing') }}</option>
                    <option value="laboratory_chemical" @if(isset($lecture->room->type) && $lecture->room->type == 'laboratory_chemical') selected @endif>{{ __('Room laboratory_chemical') }}</option>
                    <option value="laboratory_bio" @if(isset($lecture->room->type) && $lecture->room->type == 'laboratory_bio') selected @endif>{{ __('Room laboratory_bio') }}</option>
                    <option value="laboratory_physical" @if(isset($lecture->room->type) && $lecture->room->type == 'laboratory_physical') selected @endif>{{ __('Room laboratory_physical') }}</option>
                    <option value="sport" @if(isset($lecture->room->type) && $lecture->room->type == 'sport') selected @endif>{{ __('Room sport') }}</option>
                </select>
                @else
                    @if(isset($lecture->room->type) && $lecture->room->type == 'lecture')
                        {{ __('Room lecture') }}
                    @endif
                    @if(isset($lecture->room->type) && $lecture->room->type == 'computing')
                        {{ __('Room computing') }}
                    @endif
                    @if(isset($lecture->room->type) && $lecture->room->type == 'laboratory_chemical')
                        {{ __('Room laboratory_chemical') }}
                    @endif
                    @if(isset($lecture->room->type) && $lecture->room->type == 'laboratory_bio')
                        {{ __('Room laboratory_bio') }}
                    @endif
                    @if(isset($lecture->room->type) && $lecture->room->type == 'laboratory_physical')
                        {{ __('Room laboratory_physical') }}
                    @endif
                    @if(isset($lecture->room->type) && $lecture->room->type == 'sport')
                        {{ __('Room sport') }}
                    @endif
                @endif

                @if ($errors->has('room[type]'))
                    <span class="help-block">
                        <strong>{{ $errors->first('room[type]') }}</strong>
                    </span>
                @endif
            </div>
        </div>

        <div class="form-group{{ $errors->has('seats_count') ? ' has-error' : '' }}">
            <label for="seats_count" class="col-md-4 control-label">{{__('Seats count')}}</label>

            <div class="col-md-6">
                @if(!$lecture->room_payed)
                    <select class="selectpicker" id="room_seats_count" value="{{ $lecture->room->seats_count ?? '' }}" name="room[seats_count]"
                            title="{{ __('Please select') }}">
                        <option value="10" @if(isset($lecture->room->seats_count) && $lecture->room->seats_count == '10') selected @endif>10</option>
                        <option value="20" @if(isset($lecture->room->seats_count) && $lecture->room->seats_count == '20') selected @endif>20</option>
                        <option value="30" @if(isset($lecture->room->seats_count) && $lecture->room->seats_count == '30') selected @endif>30</option>
                        <option value="40" @if(isset($lecture->room->seats_count) && $lecture->room->seats_count == '40') selected @endif>40</option>
                        <option value="45" @if(isset($lecture->room->seats_count) && $lecture->room->seats_count == '45') selected @endif>45</option>
                    </select>

                    @if ($errors->has('room[seats_count]'))
                        <span class="help-block">
                            <strong>{{ $errors->first('room[seats_count]') }}</strong>s
                        </span>
                    @endif
                @else
                    {{ $lecture->room->seats_count ?? '' }}
                @endif
            </div>
        </div>

        <div class="form-group">
            <label for="conditioner" class="col-md-4 control-label">{{ __('Conditioner') }}</label>
            <div class="col-md-6">
                @if(!$lecture->room_payed)
                    <select class="selectpicker" id="room_conditioner" value="{{ $lecture->room->conditioner ?? 'no_matter' }}" name="room[conditioner]"
                            title="{{ __('Please select') }}">
                        <option value="no_matter" @if(isset($lecture->room->conditioner) && $lecture->room->conditioner == 'no_matter' || !isset($lecture->room->conditioner)) selected @endif>{{ __('No matter') }}</option>
                        <option value="yes" @if(isset($lecture->room->conditioner) && $lecture->room->conditioner == 'yes') selected @endif>{{ __('There is') }}</option>
                        <option value="no" @if(isset($lecture->room->conditioner) && $lecture->room->conditioner == 'no') selected @endif>{{ __('Has not') }}</option>

                    </select>
                @else
                    @if(isset($lecture->room->conditioner) && $lecture->room->conditioner == 'no_matter')
                        {{ __('No matter') }}
                    @endif
                    @if(isset($lecture->room->conditioner) && $lecture->room->conditioner == 'yes')
                        {{ __('There is') }}
                    @endif
                    @if(isset($lecture->room->conditioner) && $lecture->room->conditioner == 'no')
                        {{ __('Has not') }}
                    @endif
                @endif
            </div>
        </div>

        @foreach($stuffList as $i=>$stuff)
        <div class="field_all form-group">
            <label for="room[stuff][]" class="col-md-4 control-label">{{__('Room stuff ' . $stuff->name)}}</label>
            <div class="col-md-6">
                @if(!$lecture->room_payed)
                    <div class="checkbox">
                        <label>
                            <input class="education-kz_holder" type="radio" name="room[stuff][{{ $stuff->id }}]" @if(isset($lecture->room->stuff) && in_array($stuff->id, $lecture->room->stuffIds)) checked @endif value="1">
                            <label for="inkz">{{__('Yes')}}</label><br/>
                            <input class="education-kz_holder" type="radio" name="room[stuff][{{ $stuff->id }}]" @if((isset($lecture->room->stuff) && !in_array($stuff->id, $lecture->room->stuffIds)) || !isset($lecture->room->stuff) ) checked @endif value="0">
                            <label for="No">{{__('No')}}</label>
                        </label>
                    </div>
                @else
                    @if(isset($lecture->room->stuff) && in_array($stuff->id, $lecture->room->stuffIds))
                        {{__('Yes')}}
                    @else
                        {{__('No')}}
                    @endif
                @endif
            </div>
        </div>
        @endforeach

        @if(isset($lecture->room->description))
            <div id="lecture-type-online-panel" class="form-group{{ $errors->has('url') ? ' has-error' : '' }}">
                <label for="url" class="col-md-4 control-label">{{__('Room description')}}</label>
                <div class="col-md-6"> {{ $lecture->room->description }} </div>
            </div>
        @endif

        @if(isset($lecture->room->cost))
        <div class="form-group{{ $errors->has('room[cost]') ? ' has-error' : '' }}">
            <label for="room[cost]" class="col-md-4 control-label">{{__('Room cost')}}</label>

            <div class="col-md-3">
                {{ $lecture->room->cost }} ТГ
            </div>
        </div>
        @endif

    </div>

    <div class="form-group">
        <div class="col-md-8 col-md-offset-4">
            <button type="submit" class="btn btn-primary">
                {{__("Save")}}
            </button>
        </div>
    </div>
</form>

@section('scripts')
    <script type="text/javascript">
        $(function () {
            var date = new Date();
            date.setDate(date.getDate() + 5);

            $('#start').datetimepicker({
                minDate: date,
                format: 'DD.MM.YYYY HH:00'
            });

            $('#lecture-type').change(function(){

                var lectureType = $(this).val();

                if(lectureType == 'online')
                {
                    $('#lecture-type-online-panel').show();
                    $('#lecture-type-offline-panel').hide();

                    $('#url').attr('required', true);
                    $('#room_type').attr('required', false);
                    $('#room_seats_count').attr('required', false);
                }

                if(lectureType == 'offline')
                {
                    $('#lecture-type-online-panel').hide();
                    $('#lecture-type-offline-panel').show();

                    $('#url').attr('required', false);
                    $('#room_type').attr('required', true);
                    $('#room_seats_count').attr('required', true);
                }

                if(lectureType == 'all')
                {
                    $('#lecture-type-online-panel').show();
                    $('#lecture-type-offline-panel').show();

                    $('#url').attr('required', true);
                    $('#room_type').attr('required', true);
                    $('#room_seats_count').attr('required', true);
                }
            });
        });
    </script>
@endsection

@endif